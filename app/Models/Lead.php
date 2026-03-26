<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'bot_id', 'session_id', 'customer_name', 'customer_phone'
    ];

    public function bot()
    {
        return $this->belongsTo(Bot::class);
    }
}
