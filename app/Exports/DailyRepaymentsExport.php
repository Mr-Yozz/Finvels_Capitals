<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DailyRepaymentsExport implements FromView
{
    protected $repayments;
    protected $date;

    public function __construct($repayments, $date)
    {
        $this->repayments = $repayments;
        $this->date = $date;
    }

    public function view(): View
    {
        return view('exports.daily_repayments_excel', [
            'repayments' => $this->repayments,
            'date' => $this->date
        ]);
    }
}
