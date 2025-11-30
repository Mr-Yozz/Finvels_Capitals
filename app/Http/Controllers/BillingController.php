<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Repayment;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function groups()
    {
        $groups = Group::paginate(15);

        return view('reports.groups', compact('groups'));
    }

    public function members(Group $group)
    {
        $members = Member::where('group_id', $group->id)
            ->with('latestLoan')
            ->paginate(15);

        return view('reports.members', compact('group', 'members'));
    }

    public function memberBillings($memberId, Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        // Fetch all repayments for this member
        $repayments = Repayment::with(['loan'])
            ->whereHas('loan', function ($q) use ($memberId) {
                $q->where('member_id', $memberId);
            })
            ->whereDate('due_date', $date)
            ->get();

        $totalDue = $repayments->sum('amount');
        $totalPaid = $repayments->sum('paid_amount');
        $outstanding = $totalDue - $totalPaid;

        return view('reports.members_billing', compact('repayments', 'date', 'totalDue', 'totalPaid', 'outstanding', 'memberId'));
    }

    public function groupDailyBillings(Group $group, Request $request)
    {
        // The date defaults to today, but can be overridden by a request input 'date'
        $date = $request->input('date', Carbon::today()->toDateString());

        // Fetch Repayments due on the specified date for members in this group
        $repayments = Repayment::with(['loan.member'])
            ->whereDate('due_date', $date)
            // Filter by group_id via the loan and member relationships
            ->whereHas('loan.member', function ($q) use ($group) {
                $q->where('group_id', $group->id);
            })
            ->get(); // Use get() to collect all for the daily list

        // You can optionally calculate totals here if needed for the view footer
        // $totalDue = $repayments->sum('amount');

        // Pass the repayments and the group model to the view
        return view('reports.group_daily_billings', compact('repayments', 'group', 'date'));
    }

    public function markPaid(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $rep = Repayment::findOrFail($id);
        $loan = $rep->loan;

        $payAmount = $request->amount;
        $dueAmount = $rep->amount;
        $interestRate = $loan->interest_rate / 100; // 30% → 0.30

        // FULL PAYMENT
        if ($payAmount >= $dueAmount) {

            $rep->paid_amount = $dueAmount;
            $rep->outstanding = 0;
            $rep->status = 'paid';
            $rep->paid_at = now();
            $rep->save();

            return back()->with('success', 'Fully Paid');
        }

        // ----------------------------------
        // PARTIAL PAYMENT LOGIC
        // ----------------------------------

        $outstanding = $dueAmount - $payAmount;  // 1000 - 900 = 100

        $rep->paid_amount = $payAmount;
        $rep->outstanding = $outstanding;
        $rep->status = 'partial';
        $rep->paid_at = now();
        $rep->save();

        // FIND NEXT INSTALLMENT
        $nextDue = Repayment::where('loan_id', $loan->id)
            ->where('due_date', '>', $rep->due_date)
            ->orderBy('due_date', 'asc')
            ->first();

        if ($nextDue) {

            // NEW PRINCIPAL = old principal + outstanding
            $newPrincipal = $nextDue->principal_component + $outstanding;

            // CALCULATE INTEREST WITH SAME RATE
            $newInterest = $newPrincipal * $interestRate;

            // NEW TOTAL EMI
            $newTotal = $newPrincipal + $newInterest;

            $nextDue->principal_component = $newPrincipal;
            $nextDue->interest_component = $newInterest;
            $nextDue->amount = $newTotal; // updated EMI
            $nextDue->save();
        }

        // Update total paid for loan
        // $loan->paid_amount = Repayment::where('loan_id', $loan->id)->sum('paid_amount');
        // $loan->save();

        return back()->with('success', 'Partial payment applied');
    }
}
