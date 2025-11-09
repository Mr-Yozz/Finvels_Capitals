<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\Repayment;
use Carbon\Carbon;

class RepaymentScheduleService
{
    // public function generate(Loan $loan)
    // {
    //     $principal = $loan->principal;
    //     $rate = $loan->interest_rate / 100 / 12; // monthly interest
    //     $tenure = $loan->tenure_months;

    //     // EMI formula
    //     $emi = $principal * $rate * pow(1 + $rate, $tenure) / (pow(1 + $rate, $tenure) - 1);
    //     $emi = round($emi, 2);

    //     // Store EMI in loan record
    //     $loan->update(['monthly_emi' => $emi]);

    //     $startDate = Carbon::parse($loan->disbursed_at ?? now());
    //     // $dueDate = $startDate->copy();

    //     // Generate all installments
    //     for ($i = 1; $i <= $tenure; $i++) {
    //         $dueDate = $startDate->copy()->addMonths($i);

    //         Repayment::create([
    //             'loan_id' => $loan->id,
    //             'due_date' => $dueDate->copy(),
    //             'amount' => $emi,
    //         ]);
    //     }
    // }

    public function generate(Loan $loan)
    {
        // Delete existing repayments first
        $loan->repayments()->delete();

        $principal = $loan->principal;
        $rate = $loan->interest_rate / 100 / 12;
        $tenure = $loan->tenure_months;

        $emi = $principal * $rate * pow(1 + $rate, $tenure) / (pow(1 + $rate, $tenure) - 1);
        $emi = round($emi, 2);

        $loan->update(['monthly_emi' => $emi]);

        $startDate = Carbon::parse($loan->disbursed_at ?? now());

        for ($i = 1; $i <= $tenure; $i++) {
            $dueDate = $startDate->copy()->addMonths($i);

            Repayment::create([
                'loan_id' => $loan->id,
                'due_date' => $dueDate,
                'amount' => $emi,
            ]);
        }
    }

    /**
     * Calculate outstanding balance for a loan
     */
    public function calculateOutstanding(Loan $loan)
    {
        $totalDue = $loan->repayments()->sum('amount');
        $totalPaid = $loan->repayments()->sum('paid_amount');

        return round($totalDue - $totalPaid, 2);
    }
}
