<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use App\Traits\EncryptsAttributes;
class Member extends Model
{
    //
    use EncryptsAttributes;
    use Notifiable;
    protected $fillable = ['group_id', 'name', 'mobile', 'aadhaar_encrypted', 'pan_encrypted'];
    protected $encrypts = ['aadhaar_encrypted', 'pan_encrypted']; // trait reads this
    
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
