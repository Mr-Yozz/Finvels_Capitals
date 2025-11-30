<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\GroupsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Branch;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $branch_id = $request->query('branch_id'); // get branch_id from query string

        $query = Group::with('branch');

        if ($branch_id) {
            $query->where('branch_id', $branch_id);
        }

        $groups = $query->paginate(15)->withQueryString();

        return view('groups.index', compact('groups', 'branch_id'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //
        $branches = Branch::all();
        $selectedBranchId = $request->query('branch_id');
        return view('groups.create', compact('branches', 'selectedBranchId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required|string|max:255|unique:groups,name',
            'branch_id' => 'required|exists:branches,id',
        ]);

        Group::create($request->all());
        return redirect()->route('groups.index')->with('success', 'Group created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        //
        $group->load('branch', 'members'); // eager load branch & members
        return view('groups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        //
        $branches = Branch::all();
        return view('groups.edit', compact('group', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        //
        $request->validate([
            'name' => 'required|string|max:255|unique:groups,name,' . $group->id,
            'branch_id' => 'required|exists:branches,id',
        ]);

        $group->update($request->all());
        return redirect()->route('groups.index')->with('success', 'Group updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        //
        $group->delete();
        return redirect()->route('groups.index')->with('success', 'Group deleted successfully!');
    }

    // PDF Export
    public function exportPdf()
    {
        $groups = Group::with('branch')->get();
        // Logo path
        $logoPath = public_path('images/finvels.jpeg');

        // Convert logo to Base64
        $logo = null;
        if (file_exists($logoPath)) {
            $logo = base64_encode(file_get_contents($logoPath));
        }
        $pdf = Pdf::loadView('exports.groups_pdf', compact('groups', 'logo'));
        return $pdf->download('groups_report.pdf');
    }

    // Excel Export
    public function exportExcel()
    {
        $groups = Group::with('branch')->get();
        return Excel::download(new GroupsExport($groups), 'groups_report.xlsx');
    }
}
