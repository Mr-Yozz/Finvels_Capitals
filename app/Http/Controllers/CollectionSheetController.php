<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Repayment;
use Carbon\Carbon;
use PDF;

class CollectionSheetController extends Controller
{
    //
    public function index(Request $request, $groupId)
    {
        $date = $request->query('date', Carbon::now()->toDateString());
        $dateCarbon = Carbon::parse($date);

        // Load group and branch (may be null)
        $group = Group::with('branch')->findOrFail($groupId);

        // Find manager (safe): branch may have users; pick first user with role = 'manager'
        $manager = null;
        if ($group->branch) {
            // Branch->users() exists in your Branch model
            $manager = $group->branch->users()->where('role', 'manager')->first();
        }

        // Load members -> active loans -> repaymnets (only those needed)
        // We'll eager load loans and repayments to avoid N+1
        $members = Member::where('group_id', $groupId)
            ->with(['loans' => function ($q) {
                $q->where('status', 'active')->with(['repayments' => function ($rq) {
                    $rq->orderBy('due_date');
                }]);
            }])
            ->orderBy('name')
            ->get();

        $rows = [];
        $summary = [
            'due_collections'      => 0.0,
            'other_collections'    => 0.0,
            'total_collections'    => 0.0,
            'due_disbursements'    => 0.0,
            'other_disbursements'  => 0.0,
            'total_disbursements'  => 0.0,
            'applications_taken'   => 0,
            'no_loans_issued'      => 0,
            'absentees_defaults'   => 0,
            'amount_taken_back_office' => 0.0,
        ];

        foreach ($members as $member) {
            $memberLoanInstances = [];
            $memberLoanBalances = 0.0;

            $memberDueInstances = [];
            $memberDueTotal = 0.0;

            // per-member aggregated amounts from repayments on the selected date
            $memberAdvSum = 0.0;
            $dueDisbSum = 0.0;
            $prSum = 0.0;
            $sanchaySum = 0.0;
            $lpPalValue = null;

            // spouse candidate
            $spouseCandidate = null;

            foreach ($member->loans as $loan) {
                // safe product label
                $productLabel = strtoupper(trim($loan->product_name ?? 'LOAN'));

                // loan outstanding - using your Loan::outstanding()
                try {
                    $loan_balance = (float) $loan->outstanding();
                } catch (\Throwable $e) {
                    // fallback if outstanding() throws
                    $loan_balance = (float) $loan->repayments()->sum('amount') - (float) $loan->repayments()->sum('paid_amount');
                }
                $memberLoanBalances += $loan_balance;

                // add loan instance display string
                $memberLoanInstances[] = "{$productLabel}: " . number_format($loan_balance, 2);

                // due for the given date for this loan
                $todayDueAmount = (float) $loan->repayments()
                    ->whereDate('due_date', $dateCarbon->toDateString())
                    ->whereIn('status', ['due', 'partial'])
                    ->sum('amount');

                // always show each product in due instances (0.00 if none) to match the sheet
                $memberDueInstances[] = "{$productLabel}: " . number_format($todayDueAmount, 2);
                $memberDueTotal += $todayDueAmount;

                // aggregate per-loan repayment-linked fields for this date (member-adv, due_disb, pr, sanchay, lp_pal)
                $repaymentAggregates = $loan->repayments()
                    ->whereDate('due_date', $dateCarbon->toDateString())
                    ->whereIn('status', ['due', 'partial', 'paid'])
                    ->selectRaw('
                        COALESCE(SUM(member_adv),0) as member_adv_sum,
                        COALESCE(SUM(due_disb),0) as due_disb_sum,
                        COALESCE(SUM(pr),0) as pr_sum,
                        COALESCE(SUM(sanchay_due),0) as sanchay_sum
                    ')
                    ->first();

                if ($repaymentAggregates) {
                    $memberAdvSum += (float) ($repaymentAggregates->member_adv_sum ?? 0);
                    $dueDisbSum += (float) ($repaymentAggregates->due_disb_sum ?? 0);
                    $prSum += (float) ($repaymentAggregates->pr_sum ?? 0);
                    $sanchaySum += (float) ($repaymentAggregates->sanchay_sum ?? 0);
                }

                // lp_pal - try to pick any non-empty value from repayments (if stored as string/code)
                $lpPalCandidate = $loan->repayments()
                    ->whereDate('due_date', $dateCarbon->toDateString())
                    ->whereNotNull('lp_pal')
                    ->where('lp_pal', '!=', '')
                    ->pluck('lp_pal')
                    ->first();

                if (!$lpPalValue && $lpPalCandidate) {
                    $lpPalValue = $lpPalCandidate;
                }

                // spouse - try loan->spousename or loan->spouse (whichever used)
                if (!$spouseCandidate) {
                    if (!empty($loan->spousename)) {
                        $spouseCandidate = $loan->spousename;
                    } elseif (!empty($loan->spouse)) {
                        $spouseCandidate = $loan->spouse;
                    }
                }
            } // end loans loop

            // If no spouse found via loans, check member fields (if any)
            if (!$spouseCandidate) {
                $spouseCandidate = $member->spouse_name ?? $member->spouse ?? '-';
            }

            // If still null use dash
            $spouseCandidate = $spouseCandidate ?: '-';
            $lpPalValue = $lpPalValue ?: '-';

            // Add row (using numeric values for summary math; formatting handled in blade)
            $rows[] = [
                'member_id' => $member->id,
                'member_name' => $member->name,
                'loan_instances' => $memberLoanInstances,
                'loan_total_balance' => $memberLoanBalances,
                'due_instances' => $memberDueInstances,
                'due_total' => $memberDueTotal,
                'member_adv' => $memberAdvSum,
                'due_disb' => $dueDisbSum,
                'spouse_kyc' => $spouseCandidate,
                'pr' => $prSum,
                'sanchay_due' => $sanchaySum,
                'lp_pa_l' => $lpPalValue,
            ];

            // accumulate summary totals
            $summary['due_collections'] += $memberDueTotal;
            $summary['due_disbursements'] += $dueDisbSum;
            $summary['other_collections'] += $memberAdvSum + $prSum + $sanchaySum;
            // you may want a different meaning for "other_collections"; adjust as desired
        }

        // finalize summary totals
        $summary['total_collections'] = $summary['due_collections'] + $summary['other_collections'];
        $summary['total_disbursements'] = $summary['due_disbursements'] + $summary['other_disbursements'];

        // header image path (uploaded file). We'll use this path as the "url" for Blade/PDF.
        $sheetImagePath = asset('images/finvels.jpeg');

        return view('collection_sheet.index', compact(
            'group',
            'date',
            'rows',
            'summary',
            'sheetImagePath',
            'manager'
        ));
    }
}
