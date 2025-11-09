<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;

class BranchesExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $branches;

    public function __construct($branches)
    {
        $this->branches = $branches;
    }

    public function view(): View
    {
        return view('exports.branches_excel', [
            'branches' => $this->branches
        ]);
    }
}
