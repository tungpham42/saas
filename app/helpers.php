<?php

use App\Models\Bot;
use App\Models\ChatLog;
use Carbon\Carbon;

if (!function_exists('parseSessionId')) {
    /**
     * Parse the session ID to determine the platform icon and channel name.
     */
    function parseSessionId($sessionId, Bot $bot)
    {
        $icon = '💬';
        $channelName = 'Web Widget';

        // Assuming your session IDs are prefixed with the platform (e.g., 'fb_12345', 'wa_67890')
        $prefix = explode('_', $sessionId)[0] ?? 'web';

        switch ($prefix) {
            case 'fb':
                $icon = '📘';
                $channelName = 'Facebook';
                break;
            case 'zalo':
            case 'zl':
                $icon = '🔵';
                $channelName = 'Zalo';
                break;
            case 'zlpn':
                $icon = '👤';
                $channelName = 'Zalo Personal';
                break;
            case 'tt':
                $icon = '🎵';
                $channelName = 'TikTok';
                break;
            case 'sp':
                $icon = '🟠';
                $channelName = 'Shopee';
                break;
            case 'wa':
                $icon = '🟩';
                $channelName = 'WhatsApp';
                break;
        }

        // Optional: If you encode the actual channel ID in the session (e.g., 'fb_channelID_userID')
        // you could look up the exact channel name here:
        // $parts = explode('_', $sessionId);
        // if (isset($parts[1])) {
        //     $channel = $bot->channels()->find($parts[1]);
        //     if ($channel) $channelName = $channel->channel_name;
        // }

        return [
            'icon' => $icon,
            'channel_name' => $channelName
        ];
    }
}

if (!function_exists('hasRecentAdminReply')) {
    /**
     * Check if an admin has replied recently to stop the "unread" pulsing animation.
     */
    function hasRecentAdminReply(Bot $bot, $sessionId)
    {
        $lastMessage = ChatLog::where('bot_id', $bot->id)
            ->where('session_id', $sessionId)
            ->orderBy('id', 'desc')
            ->first();

        // If the last message was from an admin, return true
        return $lastMessage && $lastMessage->role === 'admin';
    }
}
