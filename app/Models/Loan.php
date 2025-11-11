<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Loan extends Model
{
    //
    protected $fillable = ['member_id', 'branch_id', 'principal', 'interest_rate', 'tenure_months', 'monthly_emi', 'disbursed_at', 'status'];

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

    public static function booted()
    {
        static::created(function ($loan) {
            // generate repayment schedule when created
            $loan->generateRepaymentSchedule();
            \App\Models\AuditLog::log(Auth::id() ?? 0, 'loan.created', $loan->toArray());
        });
        static::updated(function ($loan) {
            \App\Models\AuditLog::log(Auth::id() ?? 0, 'loan.updated', $loan->getChanges());
        });
    }

    // generate equal monthly installments (EMI) schedule
    public function generateRepaymentSchedule()
    {
        $P = $this->principal;
        $r = ($this->interest_rate / 100) / 12;
        $n = $this->tenure_months;

        if ($r == 0) {
            $emi = round($P / $n, 2);
        } else {
            $emi = round($P * ($r * pow(1 + $r, $n)) / (pow(1 + $r, $n) - 1), 2);
        }
        $this->monthly_emi = $emi;
        $this->save();

        $start = \Carbon\Carbon::parse($this->disbursed_at ?: now());
        for ($i = 1; $i <= $n; $i++) {
            $due = $start->copy()->addMonths($i);
            Repayment::create([
                'loan_id' => $this->id,
                'due_date' => $due->toDateString(),
                'amount' => $emi,
                'status' => 'due'
            ]);
        }
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
}
