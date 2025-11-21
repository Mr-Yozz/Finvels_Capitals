<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use App\Models\Invoice;

class InvoiceExport implements FromArray
{
    protected $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function array(): array
    {
        $data[] = [
            'Invoice No' => $this->invoice->invoice_no,
            'Loan ID' => $this->invoice->loan_id,
            'Member' => $this->invoice->loan->member->name,
            'Branch' => $this->invoice->loan->branch->name,
            'Issue Date' => $this->invoice->created_at->format('Y-m-d'),
        ];

        $data[] = []; // blank row

        $data[] = [
            'Installment No',
            'Due Date',
            'Principal',
            'Interest',
            'Total',
            'Status'
        ];

        foreach ($this->invoice->lines as $line) {
            $data[] = [
                $line->inst_no,
                \Carbon\Carbon::parse($line->due_date)->format('d M Y'),
                number_format($line->principal, 2),
                number_format($line->interest, 2),
                number_format($line->total, 2),
                $line->status ?? 'Pending',
            ];
        }

        return $data;
    }
}
