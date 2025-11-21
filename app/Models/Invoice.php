<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'loan_id',
        'invoice_no',
        'invoice_date',
        'loan_amount',
        'processing_fee',
        'insurance_amount',
        'total_amount',
        'notes'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function lines()
    {
        return $this->hasMany(InvoiceLine::class);
    }
}
