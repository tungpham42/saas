<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatLog extends Model
{
    protected $fillable = [
        'bot_id', 'session_id', 'role', 'content'
    ];

    public function bot()
    {
        return $this->belongsTo(Bot::class);
    }
}
