<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['branch_id', 'user_id', 'repayment_id', 'type', 'title', 'message', 'is_read'];

    public function repayment()
    {
        return $this->belongsTo(Repayment::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}