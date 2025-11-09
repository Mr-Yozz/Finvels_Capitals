<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LoansExport implements FromView
{
    protected $loans;

    public function __construct($loans)
    {
        $this->loans = $loans;
    }

    public function view(): View
    {
        return view('exports.loans_excel', [
            'loans' => $this->loans
        ]);
    }
}
