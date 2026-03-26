<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = [
        'bot_id',
        'type',
        'name',
        'config',
        'is_active'
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    // Helper method to get config values safely
    public function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    // Helper method to set config values
    public function setConfig(string $key, $value): self
    {
        $config = $this->config ?? [];
        $config[$key] = $value;
        $this->config = $config;
        return $this;
    }

    public function bot()
    {
        return $this->belongsTo(Bot::class);
    }

    public function getWebhookUrl(): string
    {
        $webhookPath = match($this->channel_type) {
            'fb' => 'fb-webhook',
            'zalo' => 'zalo-webhook',
            'tt' => 'tiktok-webhook',
            'sp' => 'shopee-webhook',
            'zlpn' => 'zalo-personal-webhook',
            'wa' => 'whatsapp-webhook',
            default => 'webhook',
        };

        $routeName = match($this->channel_type) {
            'fb' => 'api.webhook.fb',
            'zalo' => 'api.webhook.zalo',
            'tt' => 'api.webhook.tiktok',
            'sp' => 'api.webhook.shopee',
            'zlpn' => 'api.webhook.zalo-personal',
            'wa' => 'api.webhook.whatsapp',
            default => 'api.webhook.fb',
        };

        return route($routeName, ['api_key' => $this->bot->api_key, 'channel_id' => $this->id]);
    }

    public function getChannelIcon(): string
    {
        return match($this->channel_type) {
            'fb' => '📘',
            'zalo' => '🔵',
            'tt' => '🎵',
            'sp' => '🟠',
            'zlpn' => '👤',
            'wa' => '🟩',
            default => '💬',
        };
    }

    public function getChannelColor(): string
    {
        return match($this->channel_type) {
            'fb' => '#0866FF',
            'zalo' => '#0068FF',
            'tt' => '#000000',
            'sp' => '#EE4D2D',
            'wa' => '#25D366',
            default => '#64748b',
        };
    }
}
