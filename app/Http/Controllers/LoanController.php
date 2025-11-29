<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Exports\InvoiceExport;
use App\Services\RepaymentScheduleService;
use App\Models\Loan;
use App\Models\AuditLog;
use App\Models\Member;
use App\Models\Group;
use App\Models\Branch;
use App\Models\User;
use App\Models\LoanRequest;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LoansExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LoanRequestNotification;
use Illuminate\Notifications\DatabaseNotification;

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

    public function groups()
    {
        $groups = Group::paginate(15);

        return view('loans.groups', compact('groups'));
    }

    // LEVEL 2 → Show members of selected group
    public function members(Group $group)
    {
        $members = Member::where('group_id', $group->id)->paginate(15);

        return view('loans.members', compact('group', 'members'));
    }

    // LEVEL 3 → Show loans of selected member
    public function memberLoans(Member $member)
    {
        $loans = Loan::where('member_id', $member->id)->paginate(15);
        return view('loans.loans', compact('member', 'loans'));
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
            'spousename' => 'required|max:225',
            'moratorium' => 'nullable|max:225',
            'purpose' => 'required|max:225',
            'repayment_frequency' => 'required',
            'insurance_amount' => 'required|numeric',
            'principal' => 'required|numeric|min:1',
            'interest_rate' => 'required|numeric',
            'tenure_months' => 'required|integer',
            'disbursed_at' => 'nullable|date',
            'status' => 'required|in:pending,active,closed',

        ]);

        $data['processing_fee'] = $data['processing_fee'] ?? 3;
        $data['created_by'] = Auth::id();
        $data['is_approved'] = 'pending';

        $loanRequest = LoanRequest::create($data);

        // $loan = Loan::create($data);

        // $this->scheduleService->generate($loan);

        $admins = User::where('role', 'admin')->get();
        // dd($admins);
        $note = Notification::send($admins, new LoanRequestNotification($loanRequest));

        // dd($note);

        // Notification::send(User::role('admin')->get(), new LoanRequestNotification($loanRequest));

        // AuditLog::create([
        //     'user_id' => Auth::id(),
        //     'action' => 'loan.created',
        //     'meta' => json_encode($loan),
        // ]);

        return back()->with('success', 'Loan request submitted. Waiting for admin approval.');
        // return redirect()->route('loans.index', $loan)->with('success', 'Loan created successfully!');
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
            'spousename' => 'required|max:225',
            'moratorium' => 'nullable|max:225',
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


    public function exportPdf_in($id)
    {
        $invoice = Invoice::with(['loan.member', 'lines'])->findOrFail($id);

        $pdf = Pdf::loadView('exports.invoice_pdf', compact('invoice'));
        return $pdf->download('invoice_' . $invoice->invoice_no . '.pdf');
    }

    public function exportExcel_in($id)
    {
        $invoice = Invoice::with(['loan.member', 'lines'])->findOrFail($id);
        return Excel::download(new InvoiceExport($invoice), 'invoice_' . $invoice->invoice_no . '.xlsx');
    }

    public function show_admin($id)
    {
        $notifications = [];

        // if (Auth::check()) {
        //     $notifications = DatabaseNotification::where('notifiable_id', Auth::id())
        //         ->where('notifiable_type', get_class(Auth::user()))
        //         ->whereNull('read_at')
        //         ->latest()
        //         ->get();
        // }
        $loanRequest = LoanRequest::findOrFail($id);
        return view('admin.loan_request.view', compact('loanRequest'));
    }

    public function approve($id)
    {
        $loanRequest = LoanRequest::findOrFail($id);

        if ($loanRequest->is_approved === 'approved') {
            return back()->with('error', 'This loan request is already approved.');
        }

        // create actual loan
        $loan = Loan::create([
            'member_id' => $loanRequest->member_id,
            'branch_id' => $loanRequest->branch_id,
            'product_name' => $loanRequest->product_name,
            'spousename' => $loanRequest->spousename,
            'moratorium' => $loanRequest->moratorium,
            'purpose' => $loanRequest->purpose,
            'repayment_frequency' => $loanRequest->repayment_frequency,
            'insurance_amount' => $loanRequest->insurance_amount,
            'principal' => $loanRequest->principal,
            'interest_rate' => $loanRequest->interest_rate,
            'tenure_months' => $loanRequest->tenure_months,
            'disbursed_at' => $loanRequest->disbursed_at,
            'status' => 'active',
            'is_approved' => 'approved',
        ]);

        // generate schedule
        $this->scheduleService->generate($loan);

        // update loan request status
        $loanRequest->is_approved = 'approved';
        $loanRequest->save();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'loan.created',
            'meta' => json_encode($loan),
        ]);

        return redirect()
            ->route('loan-requests.show', $loanRequest->id)
            ->with('success', 'Loan approved and created successfully!');
    }


    public function reject($id)
    {
        $loanRequest = LoanRequest::findOrFail($id);

        if ($loanRequest->is_approved === 'rejected') {
            return back()->with('error', 'This loan request is already rejected.');
        }

        $loanRequest->is_approved = 'rejected';
        $loanRequest->save();

        return back()->with('error', 'Loan request rejected.');
    }
}
