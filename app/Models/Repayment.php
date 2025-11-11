<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Repayment extends Model
{
    //
    protected $fillable = ['loan_id', 'due_date', 'amount', 'paid_amount', 'paid_at', 'status'];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'date',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function markPaid($amount)
    {
        $this->paid_amount += $amount;
        $this->paid_at = now();
        $this->status = $this->paid_amount >= $this->amount ? 'paid' : 'partial';
        $this->save();
        \App\Models\AuditLog::log(Auth::id() ?? 0, 'repayment.paid', ['repayment_id' => $this->id, 'amount' => $amount]);
    }

    public function scopeAccessibleBy($query, $user)
    {
        if ($user->role === 'admin') return $query;
        if ($user->role === 'manager') {
            return $query->whereHas('loan', fn($q) => $q->where('branch_id', $user->branch_id));
        }
        return $query->whereHas('loan.member.user', fn($q) => $q->where('id', $user->id));
    }
}
