<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Repayment;
use App\Models\Loan;
use App\Models\Branch;
use App\Models\Member;
use App\Exports\RepaymentsExport;
use App\Exports\BranchReportExport;
use App\Exports\DailyRepaymentsExport;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RepaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $repayments = Repayment::with('loan.member')->latest()->paginate(10);
    //     return view('repayments.index', compact('repayments'));
    // }

    // public function index(Request $request)
    // {
    //     // If a specific member is selected, show their repayments
    //     if ($request->has('member_id')) {
    //         $member = Member::findOrFail($request->member_id);

    //         $repayments = Repayment::with('loan')
    //             ->whereHas('loan', function ($q) use ($member) {
    //                 $q->where('member_id', $member->id);
    //             })
    //             ->latest()
    //             ->paginate(10);

    //         return view('repayments.index', compact('repayments', 'member'));
    //     }

    //     // Otherwise, show the member list
    //     // $members = Member::orderBy('name')->get();
    //     $members = Member::orderBy('name', 'asc')->paginate(12);

    //     return view('repayments.members', compact('members'));
    // }

    public function index(Request $request)
    {
        // CASE 1: If a specific member is selected — show that member’s repayment table
        if ($request->has('loan_id')) {
            $loan = \App\Models\Loan::with(['member', 'repayments'])->findOrFail($request->loan_id);
            // $repayments = $loan->repayments()->latest()->paginate(10);

            // Paginated repayments
            // $repayments = $loan->repayments()->orderBy('due_date', 'asc')->paginate(10);
            if ($request->filter == '7days') {
                $repayments = $loan->repayments()
                    ->whereBetween('due_date', [
                        now()->toDateString(),
                        now()->addDays(7)->toDateString()
                    ])
                    ->orderBy('due_date', 'asc')
                    ->paginate(10);
            } else {
                // Default: All repayments
                $repayments = $loan->repayments()
                    ->orderBy('due_date', 'asc')
                    ->paginate(10);
            }

            // We need loan details to calculate EMI breakdown
            $monthlyRate = $loan->interest_rate / 100 / 12;
            $remaining = $loan->principal;

            // To compute correct split, we need *all* repayments temporarily
            $allRepayments = $loan->repayments()->orderBy('due_date', 'asc')->get();

            // Pre-calculate the full amortization schedule once
            $schedule = collect();
            foreach ($allRepayments as $r) {
                $interest = $remaining * $monthlyRate;
                $principal = $loan->monthly_emi - $interest;
                $remaining -= $principal;

                $schedule[$r->id] = [
                    'principal_part' => round($principal, 2),
                    'interest_part' => round($interest, 2),
                    'remaining_balance' => round(max($remaining, 0), 2),
                ];
            }

            // Attach only for paginated ones
            foreach ($repayments as $r) {
                if (isset($schedule[$r->id])) {
                    $r->principal_part = $schedule[$r->id]['principal_part'];
                    $r->interest_part = $schedule[$r->id]['interest_part'];
                    $r->remaining_balance = $schedule[$r->id]['remaining_balance'];
                }
            }

            $member = $loan->member;

            return view('repayments.index', compact('loan', 'repayments', 'member'));
        }

        // CASE 2: Loan List (specific member)
        if ($request->has('member_id')) {
            $member = \App\Models\Member::with(['loans.branch'])->findOrFail($request->member_id);
            $loans = $member->loans()->withCount(['repayments'])->get();

            return view('repayments.member_loans', compact('member', 'loans'));
        }

        // CASE 3: If a specific group is selected — show all members under that group
        if ($request->has('group_id')) {
            $group = \App\Models\Group::with(['members.loans.repayments'])->findOrFail($request->group_id);

            $repayments = collect();

            foreach ($group->members as $member) {
                foreach ($member->loans as $loan) {
                    foreach ($loan->repayments as $r) {
                        $repayments->push($r);
                    }
                }
            }

            $members = $group->members->map(function ($member) {
                $totalLoans = $member->loans->count();
                $totalDue = $member->loans->sum(fn($loan) => $loan->repayments->where('status', 'due')->sum('amount'));
                $totalPaid = $member->loans->sum(fn($loan) => $loan->repayments->where('status', 'paid')->sum('amount'));
                $totalDueCount = $member->loans->sum(fn($loan) => $loan->repayments->where('status', 'due')->count());
                $nextDueDate = $member->loans->flatMap->repayments->where('status', 'due')->sortBy('due_date')->first()->due_date ?? null;

                return [
                    'member' => $member,
                    'totalLoans' => $totalLoans,
                    'totalDue' => $totalDue,
                    'totalPaid' => $totalPaid,
                    'totalDueCount' => $totalDueCount,
                    'nextDueDate' => $nextDueDate,
                ];
            });

            return view('repayments.group_members', compact('group', 'members'));
        }

        // CASE: Search by member name
        if ($request->has('member_name') && $request->member_name != '') {
            $members = \App\Models\Member::where('name', 'LIKE', '%' . $request->member_name . '%')
                ->withCount('loans')
                ->paginate(12);

            return view('repayments.search_members', compact('members'));
        }


        // CASE 4: Default — show all groups first
        $groups = \App\Models\Group::orderBy('name', 'asc')->paginate(12);

        return view('repayments.groups', compact('groups'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $loans = Loan::where('status', 'active')->get();
        return view('repayments.create', compact('loans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'due_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'paid_at' => 'nullable|date',
            'status' => 'required|string|in:due,partial,paid',
        ]);

        Repayment::create($validated);
        return redirect()->route('repayments.index')->with('success', 'Repayment created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Repayment $repayment)
    {
        return view('repayments.show', compact('repayment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Repayment $repayment)
    {
        $loans = Loan::all();
        return view('repayments.edit', compact('repayment', 'loans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Repayment $repayment)
    {
        $validated = $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'due_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'paid_at' => 'nullable|date',
            'status' => 'required|string|in:due,partial,paid',
        ]);

        $repayment->update($validated);

        // ---- AUTO CLOSE LOGIC ----
        $loan = \App\Models\Loan::find($validated['loan_id']);

        $totalDueCount = $loan->repayments()->where('status', '!=', 'paid')->count();

        if ($totalDueCount == 0) {
            $loan->status = 'closed';
            $loan->save();
        }

        return redirect()
            ->route('repayments.index', ['loan_id' => $validated['loan_id']])
            ->with('success', 'Repayment updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Repayment $repayment)
    {
        $repayment->delete();
        return redirect()->route('repayments.index')->with('success', 'Repayment deleted successfully!');
    }

    public function dailyReport(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        $repayments = Repayment::with(['loan.member', 'loan.branch'])
            ->whereDate('due_date', $date)
            ->orWhereDate('paid_at', $date)
            ->get();

        $totalDue = $repayments->sum('amount');
        $totalPaid = $repayments->sum('paid_amount');
        $outstanding = $totalDue - $totalPaid;

        return view('reports.daily', compact('repayments', 'date', 'totalDue', 'totalPaid', 'outstanding'));
    }

    public function branchReport()
    {
        $branches = Branch::with(['loans.repayments'])->get();

        $reportData = $branches->map(function ($branch) {
            $totalDue = 0;
            $totalPaid = 0;
            $loanCount = $branch->loans->count();

            foreach ($branch->loans as $loan) {
                $totalDue += $loan->repayments->sum('amount');
                $totalPaid += $loan->repayments->sum('paid_amount');
            }

            return [
                'branch_name' => $branch->name,
                'total_loans' => $loanCount,
                'total_due' => $totalDue,
                'total_paid' => $totalPaid,
                'outstanding' => $totalDue - $totalPaid,
            ];
        });

        return view('reports.branch', ['reportData' => $reportData]);
    }

    public function exportPdfRepayments()
    {
        // Fetch all repayments with loan & member relation
        $repayments = Repayment::with('loan.member')->latest()->get();

        // Load PDF view
        $pdf = Pdf::loadView('exports.repayments_pdf', compact('repayments'));

        return $pdf->download('repayments_report.pdf');
    }

    public function exportExcelRepayments()
    {
        $repayments = Repayment::with('loan.member')->latest()->get();
        return Excel::download(new RepaymentsExport($repayments), 'repayments_report.xlsx');
    }

    public function exportPdfDailyReport(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        $repayments = Repayment::with(['loan.member', 'loan.branch'])
            ->whereDate('due_date', $date)
            ->orWhereDate('paid_at', $date)
            ->get();

        $totalDue = $repayments->sum('amount');
        $totalPaid = $repayments->sum('paid_amount');
        $outstanding = $totalDue - $totalPaid;

        $pdf = Pdf::loadView('exports.daily_repayments_pdf', compact('repayments', 'totalDue', 'totalPaid', 'outstanding', 'date'));
        return $pdf->download("daily_repayments_{$date}.pdf");
    }

    // Excel Export
    public function exportExcelDailyReport(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        $repayments = Repayment::with(['loan.member', 'loan.branch'])
            ->whereDate('due_date', $date)
            ->orWhereDate('paid_at', $date)
            ->get();

        return Excel::download(new DailyRepaymentsExport($repayments, $date), "daily_repayments_{$date}.xlsx");
    }

    // PDF Export
    public function exportPdfBranchReport()
    {
        $branches = Branch::with(['loans.repayments'])->get();

        $reportData = $branches->map(function ($branch) {
            $totalDue = 0;
            $totalPaid = 0;
            $loanCount = $branch->loans->count();

            foreach ($branch->loans as $loan) {
                $totalDue += $loan->repayments->sum('amount');
                $totalPaid += $loan->repayments->sum('paid_amount');
            }

            return [
                'branch_name' => $branch->name,
                'total_loans' => $loanCount,
                'total_due' => $totalDue,
                'total_paid' => $totalPaid,
                'outstanding' => $totalDue - $totalPaid,
            ];
        });

        $pdf = Pdf::loadView('exports.branch_report_pdf', compact('reportData'));
        return $pdf->download('branch_report.pdf');
    }

    // Excel Export
    public function exportExcelBranchReport()
    {
        $branches = Branch::with(['loans.repayments'])->get();

        $reportData = $branches->map(function ($branch) {
            $totalDue = 0;
            $totalPaid = 0;
            $loanCount = $branch->loans->count();

            foreach ($branch->loans as $loan) {
                $totalDue += $loan->repayments->sum('amount');
                $totalPaid += $loan->repayments->sum('paid_amount');
            }

            return [
                'branch_name' => $branch->name,
                'total_loans' => $loanCount,
                'total_due' => $totalDue,
                'total_paid' => $totalPaid,
                'outstanding' => $totalDue - $totalPaid,
            ];
        });

        return Excel::download(new BranchReportExport($reportData), 'branch_report.xlsx');
    }
}
