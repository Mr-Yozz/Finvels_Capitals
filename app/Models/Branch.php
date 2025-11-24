<?php

namespace App\Models;

use App\Models\Group;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    //
    protected $fillable = ['name', 'address', 'user_id'];
    public function groups()
    {
        return $this->hasMany(Group::class);
    }
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }

    public function manager()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class); // Expense model
    }
}
