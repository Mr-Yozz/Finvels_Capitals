<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Group;
use App\Models\Branch;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\MembersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = Member::with('group.branch')->paginate(15);
        return view('members.index', compact('members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::all();
        $groups = Group::all();
        return view('members.create', compact('branches', 'groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'aadhaar_encrypted' => 'required|string',
            'pan_encrypted' => 'required|string',
            'group_id' => 'required|exists:groups,id',
        ]);

        Member::create($request->all());

        return redirect()->route('members.index')->with('success', 'Member created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        $member->load('group.branch', 'loans'); // eager load group->branch & loans
        return view('members.show', compact('member'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        $branches = Branch::all();
        $groups = Group::all();
        return view('members.edit', compact('member', 'branches', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'aadhaar_encrypted' => 'required|string',
            'pan_encrypted' => 'required|string',
            'group_id' => 'required|exists:groups,id',
        ]);

        $member->update($request->all());

        // Also update related user name
        if ($member->user) {
            $member->user->update([
                'name' => $request->name,
            ]);
        }

        return redirect()->route('members.index')->with('success', 'Member updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        $member->delete();
        return redirect()->route('members.index')->with('success', 'Member deleted successfully!');
    }

    // PDF Export
    public function exportPdf()
    {
        $members = Member::with(['group.branch'])->get();
        $pdf = Pdf::loadView('exports.members_pdf', compact('members'));
        return $pdf->download('members_report.pdf');
    }

    // Excel Export
    public function exportExcel()
    {
        $members = Member::with(['group.branch'])->get();
        return Excel::download(new MembersExport($members), 'members_report.xlsx');
    }
}
