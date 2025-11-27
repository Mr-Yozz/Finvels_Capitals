<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomNotification extends Model
{
    protected $fillable = ['branch_id','loan_id', 'user_id', 'repayment_id', 'type', 'title', 'message', 'is_read', 'title'];

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