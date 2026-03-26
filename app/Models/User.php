<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'bot_limit',
        'paypal_sub_id',
        'paypal_sub_status',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function bots()
    {
        return $this->hasMany(Bot::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function canCreateBot(): bool
    {
        return $this->isAdmin() || $this->bots()->count() < $this->bot_limit;
    }

    public function getRemainingBotSlots(): int
    {
        if ($this->isAdmin()) return PHP_INT_MAX;
        return max(0, $this->bot_limit - $this->bots()->count());
    }

    /**
     * Check if email is verified
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Mark email as verified
     */
    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Send email verification notification
     */
    public function sendEmailVerificationNotification()
    {
        // Handled by controller
    }
}
