<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Repayment;
use App\Models\Loan;
use App\Models\Branch;
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
    public function index()
    {
        $repayments = Repayment::with('loan.member')->latest()->paginate(10);
        return view('repayments.index', compact('repayments'));
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
        return redirect()->route('repayments.index')->with('success', 'Repayment updated successfully!');
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
