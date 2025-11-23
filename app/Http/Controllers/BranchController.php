<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\BranchesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BranchReportExport;

use App\Models\Branch;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $search = $request->input('search');

        $branches = Branch::when($search, function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('address', 'LIKE', "%{$search}%");
        })
            ->orderBy('id', 'desc')
            ->paginate(10);
        return view('branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('branches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate(['name' => 'required|string|max:255']);
        Branch::create($request->all());
        return redirect()->route('branches.index')->with('success', 'Branch created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $branch = Branch::findOrFail($id);
        return view('branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $branch = Branch::findOrFail($id);
        return view('branches.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate(['name' => 'required|string|max:255']);

        $branch = Branch::findOrFail($id);

        $branch->update($request->all());
        return redirect()->route('branches.index')->with('success', 'Branch updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $branch = Branch::findORFail($id);
        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Branch deleted!');
    }

    // PDF Export
    public function exportPdf()
    {
        $branches = Branch::withCount(['groups', 'loans', 'users'])->get();
        $pdf = Pdf::loadView('exports.branches_pdf', compact('branches'));
        return $pdf->download('branches_report.pdf');
    }

    // Excel Export
    public function exportExcel()
    {
        $branches = Branch::withCount(['groups', 'loans', 'users'])->get();
        return Excel::download(new BranchesExport($branches), 'branches_report.xlsx');
    }
}
