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
        // delete old rows
        $loan->repayments()->delete();

        $principal = $loan->principal;
        $rate = $loan->interest_rate / 100 / 12;
        $tenure = $loan->tenure_months;

        $emi = round($principal * $rate * pow(1 + $rate, $tenure) / (pow(1 + $rate, $tenure) - 1), 2);

        $loan->update(['monthly_emi' => $emi]);

        $balance = $principal;
        $startDate = Carbon::parse($loan->disbursed_at ?? now());

        for ($i = 1; $i <= $tenure; $i++) {
            $interest = round($balance * $rate, 2);
            $principalComponent = round($emi - $interest, 2);

            $balance = round($balance - $principalComponent, 2);

            Repayment::create([
                'loan_id' => $loan->id,

                // instances
                'loan_instance' => "INST-$i",
                'due_instance'  => "INST-$i",

                // amounts
                'amount'     => $emi,
                'due_total'  => $emi,

                // breakup
                'principal_component' => $principalComponent,
                'interest_component'  => $interest,
                'pr' => $principalComponent,

                // balance
                'balance'    => $balance,

                // other loan columns
                'sanchay_due' => 0,
                'lp_pal'      => null,
                'member_adv'  => 0,
                'due_disb'    => 0,
                'spouse_kyc'  => null,

                // dates
                'due_date' => $startDate->copy()->addMonths($i),

                // status
                'status' => 'pending',
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
