<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Loan extends Model
{
    //
    protected $fillable = [
        'member_id',
        'branch_id',
        'principal',
        'interest_rate',
        'tenure_months',
        'monthly_emi',
        'disbursed_at',
        'status',
        'repayment_frequency',
        'processing_fee',
        'insurance_amount',
        'product_name',
        'purpose',
        'spousename',
        'moratorium',
    ];

    protected $casts = [
        'disbursed_at' => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public static function booted()
    {
        static::created(function ($loan) {
            // generate repayment schedule when created
            $loan->generateRepaymentSchedule();

            $loan->generateInvoice();

            \App\Models\AuditLog::log(Auth::id() ?? 0, 'loan.created', $loan->toArray());
        });
        static::updated(function ($loan) {
            \App\Models\AuditLog::log(Auth::id() ?? 0, 'loan.updated', $loan->getChanges());
        });
    }

    // generate equal monthly installments (EMI) schedule
    public function generateRepaymentSchedule()
    {
        // Remove any existing schedule for a clean re-generation
        $this->repayments()->delete();

        $P = floatval($this->principal);
        $annualRate = floatval($this->interest_rate);
        $freq = $this->repayment_frequency ?? 'monthly';
        $total_periods = max(1, intval($this->tenure_months)); // number of installments

        // Select period rate
        $period_rate = ($freq === 'weekly') ? ($annualRate / 100) / 52 : ($annualRate / 100) / 12;

        // calculate installment using standard annuity formula for reducing balance
        if ($period_rate == 0) {
            $installment = round($P / $total_periods, 2);
        } else {
            $r = $period_rate;
            $n = $total_periods;
            $installment = round($P * ($r * pow(1 + $r, $n)) / (pow(1 + $r, $n) - 1), 2);
        }

        // Save the computed installment into monthly_emi for compatibility (field name kept)
        $this->monthly_emi = $installment;
        $this->saveQuietly();

        $start = $this->disbursed_at ? Carbon::parse($this->disbursed_at) : Carbon::now();
        $balance = $P;

        for ($i = 1; $i <= $total_periods; $i++) {
            // interest on current balance
            $interest = round($balance * $period_rate, 2);
            $principal = round($installment - $interest, 2);

            // if last payment, adjust to zero out remaining rounding differences
            if ($i === $total_periods) {
                $principal = round($balance, 2);
                $installment = round($principal + $interest, 2);
            }

            $balance = round($balance - $principal, 2);
            if ($balance < 0) $balance = 0.00;

            // compute due date increment: weeks or months
            if ($freq === 'weekly') {
                $due = $start->copy()->addWeeks($i);
            } else {
                $due = $start->copy()->addMonths($i);
            }

            // Create repayment row
            $this->repayments()->create([
                'due_date' => $due->toDateString(),
                'amount' => $installment,
                'paid_amount' => 0,
                'paid_at' => null,
                'status' => 'due',
                'principal_component' => $principal,
                'interest_component' => $interest,
                'balance' => $balance,
                'loan_instance' => "INST-{$i}",
                'due_total' => $installment,
                'pr' => 0,
                'sanchay_due' => 0,
                'lp_pal' => null,
                'due_instance' => null,
                'member_adv' => 0,
                'due_disb' => 0,
                'spouse_kyc' => null,
            ]);
        }

        return true;
    }

    public function outstanding()
    {
        $total_due = $this->repayments()->sum('amount');
        $paid = $this->repayments()->sum('paid_amount');
        return $total_due - $paid;
    }

    public function scopeAccessibleBy($query, $user)
    {
        if ($user->role === 'admin') {
            return $query;
        } elseif ($user->role === 'manager') {
            return $query->where('branch_id', $user->branch_id);
        }

        return $query->whereHas('member.user', fn($q) => $q->where('id', $user->id));
    }

    public function generateInvoice()
    {

        if ($this->invoice()->exists()) {
            return $this->invoice;
        }

        // Generate invoice number
        $invoiceNo = 'INV-' . now()->format('Ymd') . '-' . $this->id;

        // Create invoice header
        $invoice = \App\Models\Invoice::create([
            'loan_id'        => $this->id,
            'invoice_no'     => $invoiceNo,
            'invoice_date'   => now(),
            'loan_amount'    => $this->principal,
            'processing_fee' => $this->processing_fee ?? 0,
            'insurance_amount' => $this->insurance_amount ?? 0,
            'total_amount'   => $this->principal
                + ($this->processing_fee ?? 0)
                + ($this->insurance_amount ?? 0),
            'notes'          => null,
        ]);

        // Create invoice lines from repayments
        foreach ($this->repayments as $i => $repay) {
            \App\Models\InvoiceLine::create([
                'invoice_id'   => $invoice->id,
                'inst_no'      => "INST-" . ($i + 1),
                'due_date'     => $repay->due_date,
                'principal'    => $repay->principal_component ?? 0,
                'interest'     => $repay->interest_component ?? 0,
                'total'        => $repay->amount,
                'prin_os'      => $repay->balance ?? 0,
                'km_signature' => null,
            ]);
        }

        return $invoice;
    }
}
