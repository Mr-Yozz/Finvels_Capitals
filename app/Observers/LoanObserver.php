<?php

namespace App\Observers;

use App\Models\Loan;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LoanObserver
{
    public function created(Loan $loan): void
    {
        // Use DB transaction to keep consistency
        DB::transaction(function () use ($loan) {

            // Create Invoice
            $invoice = Invoice::create([
                'loan_id' => $loan->id,
                'invoice_no' => $this->generateInvoiceNo($loan),
                'invoice_date' => Carbon::now()->toDateString(),
                'loan_amount' => $loan->principal,
                'processing_fee' => $loan->processing_fee ?? 0,
                'insurance_amount' => $loan->insurance_amount ?? 0,
                'total_amount' => (float)$loan->principal + (float)($loan->processing_fee ?? 0) + (float)($loan->insurance_amount ?? 0),
                'notes' => null,
            ]);

            // If repayments exist (generateRepaymentSchedule called earlier), use them
            $repayments = $loan->repayments()->orderBy('due_date')->get();

            if ($repayments->isEmpty()) {
                // Fallback: recompute schedule in-memory (safe fallback)
                $loan->generateRepaymentSchedule();
                $repayments = $loan->repayments()->orderBy('due_date')->get();
            }

            // Build invoice lines using repayments; include principal/interest breakdown if you computed it when generating repayments
            // If your Repayment model only stores amount, we approximate principal/interest here using amortization logic
            $P = (float)$loan->principal;
            $frequency = $loan->repayment_frequency ?? 'monthly';
            $rAnnual = (float)$loan->interest_rate;
            $periodsPerYear = ($frequency === 'weekly') ? 52 : 12;
            $r = ($rAnnual / 100) / $periodsPerYear;

            $principalOS = $P;
            $instNo = 0;

            foreach ($repayments as $rep) {
                $instNo++;
                $amount = (float)$rep->amount;
                $interest = round($principalOS * $r, 2);
                $principalPart = round($amount - $interest, 2);

                // final installment guard
                if ($instNo === $repayments->count()) {
                    $principalPart = round($principalOS, 2);
                    $amount = round($principalPart + $interest, 2);
                }

                $principalOS = round($principalOS - $principalPart, 2);

                InvoiceLine::create([
                    'invoice_id' => $invoice->id,
                    'inst_no' => $instNo,
                    'due_date' => $rep->due_date,
                    'principal' => $principalPart,
                    'interest' => $interest,
                    'total' => $amount,
                    'prin_os' => $principalOS,
                    'km_signature' => null,
                ]);
            }
        });
    }

    protected function generateInvoiceNo(Loan $loan)
    {
        return 'INV-' . date('Y') . '-' . $loan->id . '-' . strtoupper(Str::random(4));
    }
}
