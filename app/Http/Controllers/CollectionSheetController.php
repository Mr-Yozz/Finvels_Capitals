<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Repayment;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\CollectionSheetExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class CollectionSheetController extends Controller
{
    public function index(Request $request, $groupId)
    {
        $date = $request->query('date', Carbon::now()->toDateString());
        $dateCarbon = Carbon::parse($date);

        // Load group & branch
        $group = Group::with('branch')->findOrFail($groupId);

        // Manager (attempt safe lookup)
        $manager = null;
        if ($group->branch) {
            $manager = $group->branch->users()->where('role', 'manager')->first();
        }

        // Eager load members -> active loans -> repayments (ordered by due_date)
        $members = Member::where('group_id', $groupId)
            ->with(['loans' => function ($q) {
                $q->where('status', 'active')
                    ->with(['repayments' => function ($rq) {
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

            // Variables to store next due amount and date (earliest unpaid repayment)
            $nextDueAmount = 0.0;
            $nextDueDate = null;

            // aggregated per-member fields
            $memberAdvSum = 0.0;
            $dueDisbSum = 0.0;
            $prSum = 0.0;
            $sanchaySum = 0.0;
            $lpPalValue = null;
            $spouseCandidate = null;

            foreach ($member->loans as $loan) {
                // product / label
                $productLabel = strtoupper(trim($loan->product_name ?? 'LOAN'));

                // loan outstanding - prefer loan->outstanding() if present, else fallback
                try {
                    $loan_balance = (float) $loan->outstanding();
                } catch (\Throwable $e) {
                    // fallback: sum of principal remaining in repayments or amount - paid_amount
                    $loan_balance = (float) $loan->repayments()->sum(DB::raw('COALESCE(principal_component, amount)'))
                        - (float) $loan->repayments()->sum('paid_amount');
                    $loan_balance = max($loan_balance, 0.0);
                }
                $memberLoanBalances += $loan_balance;

                // Add loan instance display: include outstanding and next due (if any)
                $nextDue = $loan->repayments()
                    ->whereIn('status', ['due', 'partial'])
                    ->orderBy('due_date')
                    ->first();

                $nextDueText = '-';
                if ($nextDue) {
                    // Show due instance code and due amount (principal+interest or amount)
                    $amt = number_format((float)($nextDue->principal_component ?? $nextDue->amount ?? 0) + (float)($nextDue->interest_component ?? 0), 2);
                    $nextDueText = ($nextDue->loan_instance ?? ("INST-" . $nextDue->id)) . " due " . $nextDue->due_date . " ₹{$amt}";
                }

                $memberLoanInstances[] = "{$productLabel}: " . number_format($loan_balance, 2) . " ({$nextDueText})";

                $todayDue = $loan->repayments()
                    ->whereIn('status', ['due', 'partial'])
                    ->selectRaw('COALESCE(SUM(COALESCE(principal_component,0) + COALESCE(interest_component,0)),0) as due_sum')
                    ->first();

                $todayDueAmount = (float) ($todayDue->due_sum ?? 0.0);

                // always show a line for product/due (0.00 if none)
                $memberDueInstances[] = "{$productLabel}: " . number_format($todayDueAmount, 2);
                $memberDueTotal += $todayDueAmount;

                // Find repayments due on the selected date (not next due, but due on this specific date)
                $repaymentDueOnDate = $loan->repayments()
                    ->whereDate('due_date', $dateCarbon->toDateString())
                    ->whereIn('status', ['due', 'partial'])
                    ->first();

                // If we found a repayment due on the selected date, use it
                if ($repaymentDueOnDate) {
                    // Calculate total amount (principal + interest)
                    $repaymentAmount = (float)($repaymentDueOnDate->principal_component ?? $repaymentDueOnDate->amount ?? 0) +
                        (float)($repaymentDueOnDate->interest_component ?? 0);
                    $repaymentDate = $repaymentDueOnDate->due_date;

                    // Sum up amounts if multiple repayments exist for this date (across loans)
                    $nextDueAmount += $repaymentAmount;
                    // Set date (will be same for all repayments on this date)
                    if (!$nextDueDate) {
                        $nextDueDate = $repaymentDate;
                    }
                }

                // Aggregate per-repayment fields for this date for this loan
                $repaymentAggregates = $loan->repayments()
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

                // lp_pal pick any non-empty value (string)
                $lpPalCandidate = $loan->repayments()
                    ->whereDate('due_date', $dateCarbon->toDateString())
                    ->whereNotNull('lp_pal')
                    ->where('lp_pal', '!=', '')
                    ->pluck('lp_pal')
                    ->first();

                if (!$lpPalValue && $lpPalCandidate) {
                    $lpPalValue = $lpPalCandidate;
                }

                // spouse candidate (from loan or member)
                if (!$spouseCandidate) {
                    if (!empty($loan->spousename)) $spouseCandidate = $loan->spousename;
                    elseif (!empty($loan->spouse)) $spouseCandidate = $loan->spouse;
                }
            } // end loan loop

            // fallback spouse
            if (!$spouseCandidate) {
                $spouseCandidate = $member->spouse_name ?? $member->spouse ?? '-';
            }
            $spouseCandidate = $spouseCandidate ?: '-';
            $lpPalValue = $lpPalValue ?: '-';

            // Append row
            $rows[] = [
                'member_id' => $member->id,
                'member_name' => $member->name,
                'loan_instances' => $memberLoanInstances,
                'loan_total_balance' => round($memberLoanBalances, 2),
                'due_instances' => $memberDueInstances,
                'due_total' => round($memberDueTotal, 2),
                'next_due_amount' => round($nextDueAmount, 2),
                'next_due_date' => $nextDueDate ? $nextDueDate->format('Y-m-d') : null,
                'has_due_on_date' => $nextDueAmount > 0, // Flag to check if member has any due on selected date
                'member_adv' => round($memberAdvSum, 2),
                'due_disb' => round($dueDisbSum, 2),
                'spouse_kyc' => $spouseCandidate,
                'pr' => round($prSum, 2),
                'sanchay_due' => round($sanchaySum, 2),
                'lp_pa_l' => $lpPalValue,
            ];

            // accumulate summary only for members with dues on the selected date
            if ($nextDueAmount > 0) {
                $summary['due_collections'] += $nextDueAmount; // Use next_due_amount (due on selected date) instead of memberDueTotal
                $summary['due_disbursements'] += $dueDisbSum;
                $summary['other_collections'] += $memberAdvSum + $prSum + $sanchaySum;
                $summary['other_disbursements'] += 0; // update if you have other disbursement fields
            }
        }

        // finalize summary totals
        $summary['total_collections'] = round($summary['due_collections'] + $summary['other_collections'], 2);
        $summary['total_disbursements'] = round($summary['due_disbursements'] + $summary['other_disbursements'], 2);

        // Applications taken today
        $summary['applications_taken'] = Loan::where('is_approved', 'approved')
            // ->whereDate('updated_at', $dateCarbon->toDateString())
            ->whereHas('member', function ($q) use ($groupId) {
                $q->where('group_id', $groupId);
            })
            ->count();

        // Loans issued/disbursed today
        $summary['no_loans_issued'] = Loan::whereHas('member', function ($q) use ($groupId) {
            $q->where('group_id', $groupId);
        })
            ->count();

        // Absentees / defaults: we treat repayments with status 'absent' plus loans with status 'default'
        $absentRepayCount = DB::table('repayments')
            ->whereDate('due_date', $dateCarbon->toDateString())
            ->where('status', 'absent')
            ->whereIn('loan_id', function ($q) use ($groupId) {
                $q->select('loans.id')
                    ->from('loans')
                    ->join('members', 'members.id', '=', 'loans.member_id')
                    ->where('members.group_id', $groupId);
            })
            ->count();

        $loanDefaultsCount = Loan::where('status', 'default')
            ->whereHas('member', function ($q) use ($groupId) {
                $q->where('group_id', $groupId);
            })
            ->count();

        $summary['absentees_defaults'] = $absentRepayCount + $loanDefaultsCount;

        // Amount taken back to office: example field 'taken_back_amount' flagged on repayments
        $takenBack = DB::table('repayments')
            ->whereIn('loan_id', function ($q) use ($groupId) {
                $q->select('loans.id')
                    ->from('loans')
                    ->join('members', 'members.id', '=', 'loans.member_id')
                    ->where('members.group_id', $groupId);
            })
            ->sum('paid_amount');

        $summary['amount_taken_back_office'] = round($takenBack, 2);

        $sheetImagePath = asset('images/finvels.jpeg');

        return view('collection_sheet.index', compact(
            'group',
            'date',
            'rows',
            'summary',
            'sheetImagePath',
            'manager',
            'groupId',
        ));
    }

    // Helper function to calculate next due amount and date for a member
    private function calculateNextDue($member, $dateCarbon)
    {
        $nextDueAmount = 0.0;
        $nextDueDate = null;

        foreach ($member->loans as $loan) {
            // Find the earliest unpaid repayment for this loan
            $nextUnpaidRepayment = $loan->repayments()
                ->whereIn('status', ['due', 'partial'])
                ->orderBy('due_date', 'asc')
                ->first();

            if ($nextUnpaidRepayment) {
                // Calculate total amount (principal + interest)
                $repaymentAmount = (float)($nextUnpaidRepayment->principal_component ?? $nextUnpaidRepayment->amount ?? 0) +
                    (float)($nextUnpaidRepayment->interest_component ?? 0);
                $repaymentDate = $nextUnpaidRepayment->due_date;

                // Keep the earliest date
                if (!$nextDueDate || $repaymentDate < $nextDueDate) {
                    $nextDueAmount = $repaymentAmount;
                    $nextDueDate = $repaymentDate;
                }
            }
        }

        return [
            'amount' => round($nextDueAmount, 2),
            'date' => $nextDueDate ? $nextDueDate->format('Y-m-d') : null
        ];
    }

    private function prepareCollectionData($groupId, $date = null)
    {
        $date = $date ?: Carbon::now()->toDateString();
        $dateCarbon = Carbon::parse($date);

        $group = Group::with('branch')->findOrFail($groupId);
        $manager = null;
        if ($group->branch) {
            $manager = $group->branch->users()->where('role', 'manager')->first();
        }

        $members = Member::where('group_id', $groupId)
            ->with(['loans' => function ($q) {
                $q->where('status', 'active')
                    ->with(['repayments' => function ($rq) {
                        $rq->orderBy('due_date');
                    }]);
            }])
            ->orderBy('name')
            ->get();

        $rows = [];
        $summary = [
            'due_collections' => 0.0,
            'other_collections' => 0.0,
            'total_collections' => 0.0,
            'due_disbursements' => 0.0,
            'other_disbursements' => 0.0,
            'total_disbursements' => 0.0,
            'applications_taken' => 0,
            'no_loans_issued' => 0,
            'absentees_defaults' => 0,
            'amount_taken_back_office' => 0.0,
        ];

        foreach ($members as $member) {
            $memberLoanInstances = [];
            $memberLoanBalances = 0.0;

            $memberDueInstances = [];
            $memberDueTotal = 0.0;

            // Variables to store next due amount and date (earliest unpaid repayment)
            $nextDueAmount = 0.0;
            $nextDueDate = null;

            // aggregated per-member fields
            $memberAdvSum = 0.0;
            $dueDisbSum = 0.0;
            $prSum = 0.0;
            $sanchaySum = 0.0;
            $lpPalValue = null;
            $spouseCandidate = null;

            foreach ($member->loans as $loan) {
                // product / label
                $productLabel = strtoupper(trim($loan->product_name ?? 'LOAN'));

                // loan outstanding - prefer loan->outstanding() if present, else fallback
                try {
                    $loan_balance = (float) $loan->outstanding();
                } catch (\Throwable $e) {
                    // fallback: sum of principal remaining in repayments or amount - paid_amount
                    $loan_balance = (float) $loan->repayments()->sum(DB::raw('COALESCE(principal_component, amount)'))
                        - (float) $loan->repayments()->sum('paid_amount');
                    $loan_balance = max($loan_balance, 0.0);
                }
                $memberLoanBalances += $loan_balance;

                // Add loan instance display: include outstanding and next due (if any)
                $nextDue = $loan->repayments()
                    ->whereIn('status', ['due', 'partial'])
                    ->orderBy('due_date')
                    ->first();

                $nextDueText = '-';
                if ($nextDue) {
                    // Show due instance code and due amount (principal+interest or amount)
                    $amt = number_format((float)($nextDue->principal_component ?? $nextDue->amount ?? 0) + (float)($nextDue->interest_component ?? 0), 2);
                    $nextDueText = ($nextDue->loan_instance ?? ("INST-" . $nextDue->id)) . " due " . $nextDue->due_date . " ₹{$amt}";
                }

                $memberLoanInstances[] = "{$productLabel}: " . number_format($loan_balance, 2) . " ({$nextDueText})";

                $todayDue = $loan->repayments()
                    ->whereIn('status', ['due', 'partial'])
                    ->selectRaw('COALESCE(SUM(COALESCE(principal_component,0) + COALESCE(interest_component,0)),0) as due_sum')
                    ->first();

                $todayDueAmount = (float) ($todayDue->due_sum ?? 0.0);

                // always show a line for product/due (0.00 if none)
                $memberDueInstances[] = "{$productLabel}: " . number_format($todayDueAmount, 2);
                $memberDueTotal += $todayDueAmount;

                // Find repayments due on the selected date (not next due, but due on this specific date)
                $repaymentDueOnDate = $loan->repayments()
                    ->whereDate('due_date', $dateCarbon->toDateString())
                    ->whereIn('status', ['due', 'partial'])
                    ->first();

                // If we found a repayment due on the selected date, use it
                if ($repaymentDueOnDate) {
                    // Calculate total amount (principal + interest)
                    $repaymentAmount = (float)($repaymentDueOnDate->principal_component ?? $repaymentDueOnDate->amount ?? 0) +
                        (float)($repaymentDueOnDate->interest_component ?? 0);
                    $repaymentDate = $repaymentDueOnDate->due_date;

                    // Sum up amounts if multiple repayments exist for this date (across loans)
                    $nextDueAmount += $repaymentAmount;
                    // Set date (will be same for all repayments on this date)
                    if (!$nextDueDate) {
                        $nextDueDate = $repaymentDate;
                    }
                }

                // Aggregate per-repayment fields for this date for this loan
                $repaymentAggregates = $loan->repayments()
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

                // lp_pal pick any non-empty value (string)
                $lpPalCandidate = $loan->repayments()
                    ->whereDate('due_date', $dateCarbon->toDateString())
                    ->whereNotNull('lp_pal')
                    ->where('lp_pal', '!=', '')
                    ->pluck('lp_pal')
                    ->first();

                if (!$lpPalValue && $lpPalCandidate) {
                    $lpPalValue = $lpPalCandidate;
                }

                // spouse candidate (from loan or member)
                if (!$spouseCandidate) {
                    if (!empty($loan->spousename)) $spouseCandidate = $loan->spousename;
                    elseif (!empty($loan->spouse)) $spouseCandidate = $loan->spouse;
                }
            } // end loan loop

            // fallback spouse
            if (!$spouseCandidate) {
                $spouseCandidate = $member->spouse_name ?? $member->spouse ?? '-';
            }
            $spouseCandidate = $spouseCandidate ?: '-';
            $lpPalValue = $lpPalValue ?: '-';

            // Append row
            $rows[] = [
                'member_id' => $member->id,
                'member_name' => $member->name,
                'loan_instances' => $memberLoanInstances,
                'loan_total_balance' => round($memberLoanBalances, 2),
                'due_instances' => $memberDueInstances,
                'due_total' => round($memberDueTotal, 2),
                'next_due_amount' => round($nextDueAmount, 2),
                'next_due_date' => $nextDueDate ? $nextDueDate->format('Y-m-d') : null,
                'has_due_on_date' => $nextDueAmount > 0, // Flag to check if member has any due on selected date
                'member_adv' => round($memberAdvSum, 2),
                'due_disb' => round($dueDisbSum, 2),
                'spouse_kyc' => $spouseCandidate,
                'pr' => round($prSum, 2),
                'sanchay_due' => round($sanchaySum, 2),
                'lp_pa_l' => $lpPalValue,
            ];

            // accumulate summary only for members with dues on the selected date
            if ($nextDueAmount > 0) {
                $summary['due_collections'] += $nextDueAmount; // Use next_due_amount (due on selected date) instead of memberDueTotal
                $summary['due_disbursements'] += $dueDisbSum;
                $summary['other_collections'] += $memberAdvSum + $prSum + $sanchaySum;
                $summary['other_disbursements'] += 0; // update if you have other disbursement fields
            }
        }

        // finalize summary totals
        $summary['total_collections'] = round($summary['due_collections'] + $summary['other_collections'], 2);
        $summary['total_disbursements'] = round($summary['due_disbursements'] + $summary['other_disbursements'], 2);

        // Applications taken today
        $summary['applications_taken'] = Loan::where('is_approved', 'approved')
            // ->whereDate('updated_at', $dateCarbon->toDateString())
            ->whereHas('member', function ($q) use ($groupId) {
                $q->where('group_id', $groupId);
            })
            ->count();

        // Loans issued/disbursed today
        $summary['no_loans_issued'] = Loan::whereHas('member', function ($q) use ($groupId) {
            $q->where('group_id', $groupId);
        })
            ->count();

        // Absentees / defaults: we treat repayments with status 'absent' plus loans with status 'default'
        $absentRepayCount = DB::table('repayments')
            ->whereDate('due_date', $dateCarbon->toDateString())
            ->where('status', 'absent')
            ->whereIn('loan_id', function ($q) use ($groupId) {
                $q->select('loans.id')
                    ->from('loans')
                    ->join('members', 'members.id', '=', 'loans.member_id')
                    ->where('members.group_id', $groupId);
            })
            ->count();

        $loanDefaultsCount = Loan::where('status', 'default')
            ->whereHas('member', function ($q) use ($groupId) {
                $q->where('group_id', $groupId);
            })
            ->count();

        $summary['absentees_defaults'] = $absentRepayCount + $loanDefaultsCount;

        // Amount taken back to office: example field 'taken_back_amount' flagged on repayments
        $takenBack = DB::table('repayments')
            ->whereIn('loan_id', function ($q) use ($groupId) {
                $q->select('loans.id')
                    ->from('loans')
                    ->join('members', 'members.id', '=', 'loans.member_id')
                    ->where('members.group_id', $groupId);
            })
            ->sum('paid_amount');

        $summary['amount_taken_back_office'] = round($takenBack, 2);

        $sheetImagePath = asset('images/finvels.jpeg');

        return compact('group', 'date', 'rows', 'summary', 'sheetImagePath', 'manager', 'groupId');
    }

    public function exportPdf(Request $request, $groupId)
    {
        $date = $request->query('date', now()->toDateString());

        // Reuse your data
        $viewData = $this->prepareCollectionData($groupId, $date);

        // Filter rows here (so blade does NOT calculate)
        $filteredRows = collect($viewData['rows'])->filter(function ($r) {
            return isset($r['has_due_on_date']) &&
                $r['has_due_on_date'] &&
                ($r['next_due_amount'] ?? 0) > 0;
        });

        // Add logo path
        $logoFile = public_path('images/finvels.png');
        $logoBase64 = file_exists($logoFile)
            ? base64_encode(file_get_contents($logoFile))
            : null;

        // Add back into array
        $data = array_merge($viewData, [
            'filteredRows'     => $filteredRows,
            'logoBase64'   => $logoBase64,
            'date'             => $date
        ]);

        $pdf = PDF::loadView('collection_sheet.pdf', $data)
            ->setPaper('a4', 'landscape');

        $fileName = "Collection_Sheet_{$viewData['group']->name}_{$date}.pdf";

        return $pdf->download($fileName);
    }



    public function exportExcel(Request $request, $groupId)
    {
        $date = $request->query('date', Carbon::now()->toDateString());

        // reuse your index logic to get rows
        $viewData = $this->prepareCollectionData($groupId, $date);

        return Excel::download(new CollectionSheetExport($viewData), "Collection_Sheet_{$viewData['group']->name}_{$date}.xlsx");
    }
}
