<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    //
    protected $fillable = ['user_id', 'action', 'meta'];
    protected $casts = ['meta' => 'array'];
    public static function log($userId, $action, $meta = null)
    {
        static::create(['user_id' => $userId, 'action' => $action, 'meta' => $meta]);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
