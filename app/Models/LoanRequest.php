<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LoanRequest extends Model
{

    protected $table = 'loanrequests';

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
        'created_by',
        'is_approved'
    ];
}
