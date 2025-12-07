<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Branch;
use App\Models\Member;

class Group extends Model
{
    //
    protected $fillable = ['branch_id', 'name', 'day'];
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function leader()
    {
        return $this->hasOne(Member::class)->where('role', 'leader');
    }

    public function subLeader()
    {
        return $this->hasOne(Member::class)->where('role', 'sub_leader');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
