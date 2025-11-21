<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceLine extends Model
{
    protected $fillable = [
        'invoice_id',
        'inst_no',
        'due_date',
        'principal',
        'interest',
        'total',
        'prin_os',
        'km_signature'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
