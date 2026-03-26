<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionStat extends Model
{
    protected $fillable = [
        'bot_id', 'session_id', 'start_time', 'first_admin_time',
        'last_admin_time', 'admin_msg_count', 'is_emailed'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'first_admin_time' => 'datetime',
        'last_admin_time' => 'datetime',
        'is_emailed' => 'boolean',
    ];

    public function bot()
    {
        return $this->belongsTo(Bot::class);
    }

    public function getDuration(): ?int
    {
        if (!$this->first_admin_time || !$this->last_admin_time) {
            return null;
        }
        return $this->first_admin_time->diffInSeconds($this->last_admin_time);
    }

    public function getFormattedDuration(): string
    {
        $seconds = $this->getDuration();
        if (!$seconds) return '0s';

        if ($seconds < 60) return $seconds . 's';
        $minutes = floor($seconds / 60);
        $secs = $seconds % 60;
        if ($minutes < 60) return $minutes . 'm ' . $secs . 's';
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        return $hours . 'h ' . $minutes . 'm';
    }

    public function getFirstResponseTime(): ?int
    {
        if (!$this->first_admin_time) return null;
        return $this->start_time->diffInSeconds($this->first_admin_time);
    }

    public function getFormattedFirstResponseTime(): string
    {
        $seconds = $this->getFirstResponseTime();
        if (!$seconds) return 'N/A';

        if ($seconds < 60) return $seconds . 's';
        $minutes = floor($seconds / 60);
        $secs = $seconds % 60;
        return $minutes . 'm ' . $secs . 's';
    }
}
