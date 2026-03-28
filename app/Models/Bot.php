<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Bot extends Model
{
    protected $table = 'bots';

    protected $fillable = [
        'user_id', 'name', 'api_key', 'provider', 'provider_api_key', 'model',
        'temperature', 'max_tokens', 'prompt_persona', 'prompt_task', 'prompt_context',
        'prompt_format', 'ui_title', 'ui_welcome_msg', 'ui_placeholder', 'ui_btn_text',
        'ui_color', 'ui_bg_color', 'ui_text_color', 'ui_pos_bottom', 'ui_pos_right',
        'ui_pos_left', 'ui_trigger_icon', 'ui_trigger_bg_transparent', 'ui_trigger_border_radius',
        'ui_clear_on_close', 'ui_pre_chat_form', 'ui_pre_chat_msg', 'ui_pre_chat_name_label',
        'ui_pre_chat_phone_label', 'ui_pre_chat_btn_text', 'ui_pre_chat_error_msg',
        'admin_timeout_mins', 'history_limit', 'email_notify_addresses', 'email_notify_timeout_mins',
        'ui_trigger_custom_icon', 'icon_type'
    ];

    protected $casts = [
        'temperature' => 'float',
        'ui_trigger_bg_transparent' => 'boolean',
        'ui_clear_on_close' => 'boolean',
        'ui_pre_chat_form' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'icon_type' => 'string', // Add this cast
    ];

    protected static function booted()
    {
        static::creating(function ($bot) {
            if (empty($bot->api_key)) {
                $bot->api_key = 'sk_live_' . Str::random(24);
            }
            // Set default icon_type if not provided
            if (empty($bot->icon_type)) {
                $bot->icon_type = 'emoji';
            }
            if (empty($bot->ui_trigger_icon)) {
                $bot->ui_trigger_icon = '💬';
            }
        });

        static::deleting(function ($bot) {
            // Xóa file icon custom khi xóa bot
            if ($bot->ui_trigger_custom_icon && file_exists(public_path($bot->ui_trigger_custom_icon))) {
                unlink(public_path($bot->ui_trigger_custom_icon));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function channels()
    {
        return $this->hasMany(Channel::class);
    }

    public function chatLogs()
    {
        return $this->hasMany(ChatLog::class);
    }

    public function ragDocuments()
    {
        // Explicitly specify the foreign key and local key
        return $this->hasMany(RAGDocument::class, 'bot_id', 'id');
    }

    public function sessionStats()
    {
        return $this->hasMany(SessionStat::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function getEmbedCode(): string
    {
        return '<script src="' . route('embed.js', ['api_key' => $this->api_key]) . '" defer></script>';
    }

    public function getActiveChannels()
    {
        return $this->channels()->where('is_active', true)->get();
    }

    // Helper method để lấy trigger icon (custom hoặc emoji)
    public function getTriggerIcon()
    {
        if ($this->icon_type === 'custom' && $this->ui_trigger_custom_icon && file_exists(public_path($this->ui_trigger_custom_icon))) {
            return '<img src="' . asset($this->ui_trigger_custom_icon) . '" alt="icon" style="width: 24px; height: 24px;">';
        }
        return $this->ui_trigger_icon ?? '💬';
    }

    // Helper method to get the actual icon path or emoji for widget
    public function getTriggerIconForWidget()
    {
        if ($this->icon_type === 'custom' && $this->ui_trigger_custom_icon && file_exists(public_path($this->ui_trigger_custom_icon))) {
            return asset($this->ui_trigger_custom_icon);
        }
        return null;
    }
}
