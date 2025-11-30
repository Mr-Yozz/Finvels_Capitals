<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Loan extends Model
{
    protected $fillable = [
        'member_id',
        'branch_id',
        'principal',
        'interest_rate',
        'tenure_months', // Stores total periods (weeks or months)
        'monthly_emi',
        'weekly_emi',
        'disbursed_at',
        'status',
        'repayment_frequency',
        'processing_fee',
        'insurance_amount',
        'product_name',
        'purpose',
        'spousename',
        'moratorium',
        'paid_amount',
        'created_by',
        'is_approved',
    ];

    protected $casts = [
        'disbursed_at' => 'date',
    ];

    // --- Relationships ---
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

    public function loanRequest()
    {
        return $this->belongsTo(LoanRequest::class, 'loan_request_id');
    }

    // --- Booting and Scopes ---
    public static function booted()
    {
        // Automatically set created_by if not provided
        static::creating(function ($loan) {
            if (empty($loan->created_by) && Auth::check()) {
                $loan->created_by = Auth::id();
            }
        });
    }
    // public static function booted()
    // {
    //     static::created(function ($loan) {
    //         $loan->generateRepaymentsAndInvoice();

    //         \App\Models\AuditLog::log(Auth::id() ?? 0, 'loan.created', $loan->toArray());
    //     });
    //     static::updated(function ($loan) {
    //         \App\Models\AuditLog::log(Auth::id() ?? 0, 'loan.updated', $loan->getChanges());
    //     });
    // }

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

    public function buildSchedule($firstInterestOnly = true)
    {
        $schedule = [];

        $P = floatval($this->principal);
        $annualRate = floatval($this->interest_rate);
        $N = max(1, intval($this->tenure_months));
        $moratorium = intval($this->moratorium ?? 0);

        $disbursed = $this->disbursed_at
            ? \Carbon\Carbon::parse($this->disbursed_at)
            : \Carbon\Carbon::now();

        // Monthly frequency
        if ($this->repayment_frequency === 'monthly') {
            $R = ($annualRate / 100) / 12;
            $prefix = 'MONTH';
            // Set first due date to 2nd of next month
            $nextDate = $disbursed->copy()->addMonth()->day(2);
            $interval = 'month';
        }
        // Weekly frequency
        else {
            $R = ($annualRate / 100) / 52;
            $prefix = 'WEEK';
            $nextDate = $disbursed->copy()->addWeek();
            $interval = 'week';
        }

        $balance = $P;
        $firstPayment = true;

        // Number of interest-only periods
        $interestOnlyPeriods = $moratorium + ($firstInterestOnly ? 1 : 0);

        // Regular EMI periods = N - interest-only periods
        $regularPeriods = $N - $interestOnlyPeriods;

        // EMI is calculated ONLY on remaining periods (important!)
        $EMI = 0;
        if ($regularPeriods > 0 && $R > 0) {
            $EMI = round(
                $P * $R * pow(1 + $R, $regularPeriods) /
                    (pow(1 + $R, $regularPeriods) - 1),
                2
            );
        } else if ($regularPeriods > 0) {
            $EMI = round($P / $regularPeriods, 2);
        }

        for ($i = 1; $i <= $N; $i++) {

            // Month-1 OR moratorium = interest-only
            $interestOnly = ($firstPayment && $firstInterestOnly) || ($i <= $moratorium);

            if ($interestOnly) {
                // Only interest
                $interest = round($balance * $R, 2);
                $principal = 0;
                $payment = $interest;
            } else {
                // Standard EMI amortization
                $interest = round($balance * $R, 2);
                $principal = round($EMI - $interest, 2);
                if ($principal < 0) $principal = 0;
                $payment = $EMI;
            }

            // FINAL MONTH → clear remaining balance exactly
            if ($i == $N) {
                $interest = round($balance * $R, 2);
                $principal = round($balance, 2);
                $payment = round($principal + $interest, 2);
            }

            // Update remaining principal
            $balance = round($balance - $principal, 2);
            if ($balance < 0) $balance = 0;

            $schedule[] = [
                'inst_no' => $i,
                'date' => $nextDate->format('Y-m-d'),
                'principal' => $principal,
                'interest' => $interest,
                'total' => $payment,
                'prin_os' => $balance,
                'loan_instance' => $prefix . "-$i",
            ];

            $firstPayment = false;

            // Calculate next due date
            if ($interval === 'month') {
                // For monthly: always set to 2nd of next month
                $nextDate = $nextDate->copy()->addMonth()->day(2);
            } else {
                // For weekly: add week 
                $nextDate = $nextDate->copy()->addWeek();
            }
        }

        // Save EMI value for reference
        if ($this->repayment_frequency === 'monthly') {
            $this->monthly_emi = $EMI;
        } else {
            $this->weekly_emi = $EMI;
        }

        $this->saveQuietly();
        return $schedule;
    }



    /**
     * Generate repayments from a schedule
     */
    public function generateRepaymentsFromSchedule(array $schedule)
    {
        $this->repayments()->delete(); // clear old

        foreach ($schedule as $row) {
            \App\Models\Repayment::create([
                'loan_id' => $this->id,
                'loan_instance' => $row['loan_instance'],
                'due_date' => $row['date'],
                'principal_component' => $row['principal'],
                'interest_component' => $row['interest'],
                'amount' => $row['total'],
                'balance' => $row['prin_os'],
                'status' => 'due',
                'due_total' => $row['total'],
            ]);
        }
    }

    /**
     * Generate Invoice from a schedule
     */
    public function generateInvoiceFromSchedule(array $schedule)
    {
        $this->invoice()->delete(); // clear existing

        $totalPrincipal = array_sum(array_column($schedule, 'principal'));
        $totalInterest  = array_sum(array_column($schedule, 'interest'));

        $invoice = \App\Models\Invoice::create([
            'loan_id' => $this->id,
            'invoice_no' => 'INV-' . now()->format('Ymd') . '-' . $this->id,
            'invoice_date' => now()->toDateString(),
            'loan_amount' => $this->principal,
            'processing_fee' => $this->processing_fee ?? 0,
            'insurance_amount' => $this->insurance_amount ?? 0,
            'principal_total' => $totalPrincipal,
            'interest_total' => $totalInterest,
            'total_amount' => $totalPrincipal + $totalInterest + ($this->processing_fee ?? 0) + ($this->insurance_amount ?? 0),
            'notes' => 'Loan disbursement invoice',
        ]);

        foreach ($schedule as $row) {
            \App\Models\InvoiceLine::create([
                'invoice_id' => $invoice->id,
                'inst_no' => $row['inst_no'],
                'due_date' => $row['date'],
                'principal' => $row['principal'],
                'interest' => $row['interest'],
                'total' => $row['total'],
                'prin_os' => $row['prin_os'],
                'km_signature' => null,
            ]);
        }

        return $invoice;
    }

    /**
     * Wrapper: generate both repayments & invoice consistently
     */
    public function generateRepaymentsAndInvoice()
    {
        $this->repayments()->delete();
        $this->invoice()->delete();

        $schedule = $this->buildSchedule(true);

        $totalPrincipal = 0;
        $totalInterest = 0;

        // Repayments
        foreach ($schedule as $row) {
            $this->repayments()->create([
                'loan_instance' => $row['loan_instance'],
                'due_date' => $row['date'],
                'principal_component' => $row['principal'],
                'interest_component' => $row['interest'],
                'amount' => $row['total'],
                'balance' => $row['prin_os'],
                'status' => 'due',
                'due_total' => $row['total'],
            ]);

            $totalPrincipal += $row['principal'];
            $totalInterest += $row['interest'];
        }

        // Invoice
        $invoice = $this->invoice()->create([
            'invoice_no' => 'INV-' . now()->format('Ymd') . '-' . $this->id,
            'invoice_date' => now()->toDateString(),
            'loan_amount' => $this->principal,
            'processing_fee' => $this->processing_fee ?? 0,
            'insurance_amount' => $this->insurance_amount ?? 0,
            'principal_total' => $totalPrincipal,
            'interest_total' => $totalInterest,
            'total_amount' => $totalPrincipal + $totalInterest + ($this->processing_fee ?? 0) + ($this->insurance_amount ?? 0),
            'notes' => 'Loan Disbursement Invoice',
        ]);

        foreach ($schedule as $row) {
            $invoice->lines()->create([
                'inst_no' => $row['inst_no'],
                'due_date' => $row['date'],
                'principal' => $row['principal'],
                'interest' => $row['interest'],
                'total' => $row['total'],
                'prin_os' => $row['prin_os'],
                'km_signature' => null,
            ]);
        }

        return true;
    }
}
