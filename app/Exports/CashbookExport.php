<?php

namespace App\Exports;

use App\Models\Cashbook;
use App\Models\Loan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CashbookExport implements FromView
{
    protected $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function view(): View
    {
        // Fetch cashbook without group filter
        $cashbook = Cashbook::where('date', $this->date)->first();

        // Fetch all loans disbursed on the date (no group filter)
        $loans = Loan::whereDate('disbursed_at', $this->date)->get();

        return view('exports.cashbook_excel', [
            'cashbook' => $cashbook,
            'loans'    => $loans,
            'date'     => $this->date,
        ]);
    }
}
