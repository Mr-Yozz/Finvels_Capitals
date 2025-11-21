<?php

namespace App\Http\Controllers;

use App\Services\RepaymentScheduleService;
use App\Models\Loan;
use App\Models\AuditLog;
use App\Models\Member;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LoansExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $scheduleService;

    public function __construct(RepaymentScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public function index()
    {
        $loans = Loan::with('member.group.branch')->paginate(15);
        return view('loans.index', compact('loans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $members = Member::with('group.branch')->get();
        $branches = Branch::all();
        return view('loans.create', compact('members', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id' => 'required|exists:members,id',
            'branch_id' => 'required|exists:branches,id',
            'product_name' => 'required|max:225',
            'purpose' => 'required|max:225',
            'repayment_frequency' => 'required',
            'insurance_amount' => 'required|numeric',
            'principal' => 'required|numeric|min:1',
            'interest_rate' => 'required|numeric|min:0',
            'tenure_months' => 'required|integer|min:1',
            'disbursed_at' => 'required|date',
            'status' => 'required|in:pending,active,closed',
        ]);

        $data['processing_fee'] = $data['processing_fee'] ?? 0;

        $loan = Loan::create($data);

        $this->scheduleService->generate($loan);



        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'loan.created',
            'meta' => json_encode($loan),
        ]);

        return redirect()->route('loans.index', $loan)->with('success', 'Loan created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Loan $loan)
    {
        $loan->load('member.group.branch', 'repayments');
        return view('loans.show', compact('loan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Loan $loan)
    {
        $members = Member::with('group.branch')->get();
        $branches = Branch::all();
        return view('loans.edit', compact('loan', 'members', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Loan $loan)
    {
        $data = $request->validate([
            'member_id' => 'required|exists:members,id',
            'branch_id' => 'required|exists:branches,id',
            'principal' => 'required|numeric|min:1',
            'product_name' => 'required|max:225',
            'purpose' => 'required|max:225',
            'repayment_frequency' => 'required',
            'insurance_amount' => 'required|numeric',
            'interest_rate' => 'required|numeric|min:0',
            'tenure_months' => 'required|integer|min:1',
            'disbursed_at' => 'required|date',
            'status' => 'required|in:pending,active,closed',
        ]);

        $data['processing_fee'] = $data['processing_fee'] ?? 0;

        $loan->update($data);

        return redirect()->route('loans.index')->with('success', 'Loan updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loan $loan)
    {
        $loan->delete();
        return redirect()->route('loans.index')->with('success', 'Loan deleted successfully!');
    }

    // PDF Export
    public function exportPdf()
    {
        $loans = Loan::with(['member', 'branch'])->get();
        $pdf = Pdf::loadView('exports.loans_pdf', compact('loans'));
        return $pdf->download('loans_report.pdf');
    }

    // Excel Export
    public function exportExcel()
    {
        $loans = Loan::with(['member', 'branch'])->get();
        return Excel::download(new LoansExport($loans), 'loans_report.xlsx');
    }
}
