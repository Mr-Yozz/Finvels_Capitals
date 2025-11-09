<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\Repayment;
use Maatwebsite\Excel\Concerns\FromCollection;

class RepaymentsExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $repayments;

    public function __construct($repayments)
    {
        $this->repayments = $repayments;
    }

    public function view(): View
    {
        return view('exports.repayments_excel', [
            'repayments' => $this->repayments
        ]);
    }
}
