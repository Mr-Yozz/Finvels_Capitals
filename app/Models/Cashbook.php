<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cashbook extends Model
{
    //
    protected $fillable = [
        'group_id',
        'date',
        'opening_balance',
        'total_collection',
        'deposit',
        'expenses',
        'closing_balance',
    ];
}
