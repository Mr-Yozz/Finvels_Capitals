<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CollectionSheetExport implements FromArray, WithHeadings
{
    protected $data;

    public function __construct($viewData)
    {
        $this->data = $viewData['rows'];
    }

    public function array(): array
    {
        $export = [];
        foreach ($this->data as $r) {
            $export[] = [
                'Member ID' => $r['member_id'],
                'Member Name' => $r['member_name'],
                'Loan Balances' => implode(", ", $r['loan_instances']),
                'Loan Total' => $r['loan_total_balance'],
                'Dues' => implode(", ", $r['due_instances']),
                'Due Total' => $r['due_total'],
                'Member Adv' => $r['member_adv'],
                'Due Disb' => $r['due_disb'],
                'Spouse KYC' => $r['spouse_kyc'],
                'PR' => $r['pr'],
                'Sanchay' => $r['sanchay_due'],
                'LP/PA/L' => $r['lp_pa_l'],
            ];
        }
        return $export;
    }

    public function headings(): array
    {
        return [
            'Member ID', 'Member Name', 'Loan Balances', 'Loan Total', 'Dues', 'Due Total',
            'Member Adv', 'Due Disb', 'Spouse KYC', 'PR', 'Sanchay', 'LP/PA/L'
        ];
    }
}
