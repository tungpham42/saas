<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\ChatLog;
use App\Models\SessionStat;
use App\Models\Channel;
use App\Services\LLMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ChatController extends Controller
{
    protected LLMService $llmService;

    public function __construct(LLMService $llmService)
    {
        $this->llmService = $llmService;
    }

    /**
     * Display live chat interface for a bot
     */
    public function liveChat(Bot $bot)
    {
        $this->authorizeBot($bot);

        $datePreset = request('date_preset');
        $filterDate = request('filter_date');

        // Get sessions with filters
        $sessions = $this->getSessions($bot, $datePreset, $filterDate, 20);

        // Get selected session messages
        $selectedSession = request('session_id');
        $messages = [];

        if ($selectedSession) {
            $messages = $bot->chatLogs()
                ->where('session_id', $selectedSession)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        // Get channels for display
        $channels = $bot->channels()->pluck('channel_name', 'id')->toArray();

        return view('chat.live', compact(
            'bot', 'sessions', 'messages', 'selectedSession',
            'datePreset', 'filterDate', 'channels'
        ));
    }

    /**
     * Display chat history for a bot
     */
    public function history(Bot $bot)
    {
        $this->authorizeBot($bot);

        $datePreset = request('date_preset');
        $filterDate = request('filter_date');

        // Get sessions with filters
        $sessions = $this->getSessions($bot, $datePreset, $filterDate, 50);

        // Get selected session messages
        $selectedSession = request('session_id');
        $messages = [];

        if ($selectedSession) {
            $messages = $bot->chatLogs()
                ->where('session_id', $selectedSession)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        // Get channels for display
        $channels = $bot->channels()->pluck('channel_name', 'id')->toArray();

        return view('chat.history', compact(
            'bot', 'sessions', 'messages', 'selectedSession',
            'datePreset', 'filterDate', 'channels'
        ));
    }

    /**
     * Get updated sessions list (for polling)
     */
    public function getSessionsList(Request $request, Bot $bot)
    {
        $this->authorizeBot($bot);

        $datePreset = $request->query('date_preset');
        $filterDate = $request->query('filter_date');

        $sessions = $this->getSessions($bot, $datePreset, $filterDate, 50);

        $formattedSessions = [];
        foreach ($sessions as $session) {
            $sessionInfo = $this->parseSessionId($session->session_id, $bot);
            $formattedSessions[] = [
                'session_id' => $session->session_id,
                'last_time' => $this->formatDateForJson($session->last_time),
                'msgs' => $session->msgs,
                'has_recent_admin' => $this->hasRecentAdminReply($bot, $session->session_id),
                'channel_name' => $sessionInfo['channel_name'] ?? null,
                'icon' => $sessionInfo['icon'] ?? '💬',
                'channel_type' => $sessionInfo['type'] ?? 'web'
            ];
        }

        return response()->json([
            'sessions' => $formattedSessions,
            'total' => count($formattedSessions)
        ]);
    }

    /**
     * Get sessions based on date filters
     */
    private function getSessions(Bot $bot, ?string $datePreset, ?string $filterDate, int $limit)
    {
        $query = $bot->chatLogs()
            ->select('session_id', DB::raw('MAX(created_at) as last_time'), DB::raw('COUNT(*) as msgs'))
            ->groupBy('session_id');

        // Apply date filters
        if ($datePreset === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($datePreset === 'yesterday') {
            $query->whereDate('created_at', today()->subDay());
        } elseif ($datePreset === 'last_7') {
            $query->where('created_at', '>=', today()->subDays(7));
        } elseif ($datePreset === 'this_month') {
            $query->whereYear('created_at', today()->year)
                  ->whereMonth('created_at', today()->month);
        } elseif ($datePreset === 'last_month') {
            $lastMonth = today()->subMonth();
            $query->whereYear('created_at', $lastMonth->year)
                  ->whereMonth('created_at', $lastMonth->month);
        } elseif ($datePreset === 'last_30') {
            $query->where('created_at', '>=', today()->subDays(30));
        } elseif ($datePreset === 'custom' && $filterDate) {
            $query->whereDate('created_at', $filterDate);
        }

        return $query->orderBy('last_time', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Send admin reply to a chat session
     */
    public function sendAdminReply(Request $request, Bot $bot)
    {
        $this->authorizeBot($bot);

        $validated = $request->validate([
            'session_id' => 'required|string',
            'message' => 'required|string',
            'content' => 'nullable|string',
        ]);

        $sessionId = $validated['session_id'];
        $message = $validated['message'] ?? $validated['content'] ?? '';

        if (empty($message)) {
            return response()->json(['error' => 'Message is required'], 400);
        }

        // Save admin message
        $adminMsg = $bot->chatLogs()->create([
            'session_id' => $sessionId,
            'role' => 'admin',
            'content' => $message,
        ]);

        // Update session statistics
        $this->updateSessionStats($bot, $sessionId);

        // Send to external channel if applicable
        $this->sendToExternalChannel($bot, $sessionId, $message);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $adminMsg->id,
                    'session_id' => $adminMsg->session_id,
                    'role' => $adminMsg->role,
                    'content' => $adminMsg->content,
                    'created_at' => $this->formatDateForJson($adminMsg->created_at),
                ],
                'message_id' => $adminMsg->id,
                'session_stats' => $this->getSessionStats($bot, $sessionId)
            ]);
        }

        return redirect()->back()->with('success', 'Reply sent successfully.');
    }

    /**
     * Get session statistics
     */
    private function getSessionStats(Bot $bot, string $sessionId): array
    {
        $messageCount = $bot->chatLogs()
            ->where('session_id', $sessionId)
            ->count();

        $lastMessage = $bot->chatLogs()
            ->where('session_id', $sessionId)
            ->latest('created_at')
            ->first();

        return [
            'msgs' => $messageCount,
            'last_time' => $lastMessage ? $this->formatDateForJson($lastMessage->created_at) : null,
            'session_id' => $sessionId,
        ];
    }

    /**
     * Update session statistics when admin replies
     */
    private function updateSessionStats(Bot $bot, string $sessionId): void
    {
        $stat = SessionStat::firstOrNew([
            'bot_id' => $bot->id,
            'session_id' => $sessionId
        ]);

        if (!$stat->exists) {
            $stat->start_time = now();
            $stat->first_admin_time = now();
            $stat->admin_msg_count = 1;
        } else {
            $stat->admin_msg_count += 1;
        }

        $stat->last_admin_time = now();
        $stat->save();
    }

    /**
     * Send admin reply to external channel (Facebook, Zalo, WhatsApp, etc.)
     */
    private function sendToExternalChannel(Bot $bot, string $sessionId, string $message): void
    {
        $parts = explode('__', $sessionId);

        if (count($parts) === 3) {
            $channelType = $parts[0];
            $channelId = (int) $parts[1];
            $recipientId = $parts[2];

            $channel = $bot->channels()->find($channelId);

            if ($channel && $channel->is_active) {
                $config = $channel->config ?? [];
                $cleanMessage = $this->llmService->cleanTextForChannels($message);

                switch ($channelType) {
                    case 'fb':
                        $this->sendFacebookMessage($config['fb_page_token'] ?? '', $recipientId, $cleanMessage);
                        break;
                    case 'zalo':
                        $this->sendZaloMessage($config['zalo_access_token'] ?? '', $recipientId, $cleanMessage);
                        break;
                    case 'tt':
                        $this->sendTikTokMessage($config['tiktok_access_token'] ?? '', $recipientId, $cleanMessage);
                        break;
                    case 'sp':
                        $this->sendShopeeMessage(
                            $config['shopee_access_token'] ?? '',
                            $config['shopee_shop_id'] ?? '',
                            $recipientId,
                            $cleanMessage
                        );
                        break;
                    case 'zlpn':
                        $this->sendZaloPersonalMessage($config['zalo_personal_token'] ?? '', $recipientId, $cleanMessage);
                        break;
                    case 'wa':
                        $this->sendWhatsAppMessage(
                            $config['whatsapp_token'] ?? '',
                            $config['whatsapp_phone_number_id'] ?? '',
                            $recipientId,
                            $cleanMessage
                        );
                        break;
                }
            }
        }
    }

    /**
     * Send message to Facebook Messenger
     */
    private function sendFacebookMessage(string $token, string $recipientId, string $message): void
    {
        if (empty($token)) return;

        $url = "https://graph.facebook.com/v18.0/me/messages?access_token={$token}";

        Http::withHeaders(['Content-Type' => 'application/json'])
            ->post($url, [
                'recipient' => ['id' => $recipientId],
                'message' => ['text' => $message]
            ]);
    }

    /**
     * Send message to Zalo Official Account
     */
    private function sendZaloMessage(string $token, string $recipientId, string $message): void
    {
        if (empty($token)) return;

        $url = "https://openapi.zalo.me/v3.0/oa/message/cs";

        Http::withHeaders([
            'Content-Type' => 'application/json',
            'access_token' => $token
        ])->post($url, [
            'recipient' => ['user_id' => $recipientId],
            'message' => ['text' => $message]
        ]);
    }

    /**
     * Send message to TikTok Shop
     */
    private function sendTikTokMessage(string $token, string $recipientId, string $message): void
    {
        if (empty($token)) return;

        $url = "https://open-api.tiktokglobalshop.com/customer_service/202309/conversations/{$recipientId}/messages";

        Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-tts-access-token' => $token
        ])->post($url, [
            'content' => ['text' => $message],
            'type' => 'TEXT'
        ]);
    }

    /**
     * Send message to Shopee
     */
    private function sendShopeeMessage(string $token, string $shopId, string $recipientId, string $message): void
    {
        if (empty($token) || empty($shopId)) return;

        $url = "https://partner.shopeemobile.com/api/v2/sellerchat/send_message";

        Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->post($url, [
            'to_id' => (int) $recipientId,
            'message_type' => 'text',
            'content' => ['text' => $message]
        ]);
    }

    /**
     * Send message to Zalo Personal
     */
    private function sendZaloPersonalMessage(string $token, string $recipientId, string $message): void
    {
        if (empty($token)) return;

        $parts = explode('|', $token);
        $apiUrl = $parts[0] ?? '';
        $apiToken = $parts[1] ?? $token;

        if (filter_var($apiUrl, FILTER_VALIDATE_URL)) {
            Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiToken
            ])->post($apiUrl, [
                'to' => $recipientId,
                'message' => $message
            ]);
        }
    }

    /**
     * Send message to WhatsApp Cloud API
     */
    private function sendWhatsAppMessage(string $token, string $phoneNumberId, string $recipientId, string $message): void
    {
        if (empty($token) || empty($phoneNumberId)) return;

        $url = "https://graph.facebook.com/v18.0/{$phoneNumberId}/messages";

        Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->post($url, [
            'messaging_product' => 'whatsapp',
            'to' => $recipientId,
            'type' => 'text',
            'text' => ['body' => $message]
        ]);
    }

    /**
     * Poll for new messages (AJAX endpoint for live chat)
     */
    public function pollMessages(Request $request, Bot $bot)
    {
        $this->authorizeBot($bot);

        $sessionId = $request->query('session_id');
        $lastId = (int) $request->query('last_id', 0);

        if (!$sessionId) {
            return response()->json(['error' => 'Session ID required'], 400);
        }

        // Get new messages with proper formatting
        $messages = $bot->chatLogs()
            ->where('session_id', $sessionId)
            ->where('id', '>', $lastId)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'session_id' => $message->session_id,
                    'role' => $message->role,
                    'content' => $message->content,
                    'created_at' => $this->formatDateForJson($message->created_at),
                ];
            });

        // Get updated session info
        $session = $bot->chatLogs()
            ->selectRaw('session_id, MAX(created_at) as last_time, COUNT(*) as msgs')
            ->where('session_id', $sessionId)
            ->groupBy('session_id')
            ->first();

        $lastMessageId = $messages->isNotEmpty() ? $messages->last()['id'] : $lastId;

        return response()->json([
            'messages' => $messages,
            'session' => $session ? [
                'session_id' => $session->session_id,
                'last_time' => $session->last_time ? $this->formatDateForJson($session->last_time) : null,
                'msgs' => $session->msgs,
            ] : null,
            'last_id' => $lastMessageId
        ]);
    }

    /**
     * Clear a specific chat session
     */
    public function clearSession(Bot $bot, Request $request)
    {
        $this->authorizeBot($bot);

        $sessionId = $request->input('session_id');

        if ($sessionId) {
            // Delete chat logs for this session
            $bot->chatLogs()->where('session_id', $sessionId)->delete();

            // Delete session statistics
            SessionStat::where('bot_id', $bot->id)
                ->where('session_id', $sessionId)
                ->delete();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'session_id' => $sessionId]);
            }

            return redirect()->back()->with('success', 'Session cleared successfully.');
        }

        return redirect()->back()->with('error', 'No session specified.');
    }

    /**
     * Clear all chat history for this bot
     */
    public function clearAllChats(Bot $bot, Request $request)
    {
        $this->authorizeBot($bot);

        // Delete all chat logs
        $bot->chatLogs()->delete();

        // Delete all session statistics
        SessionStat::where('bot_id', $bot->id)->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'All chat history cleared.');
    }

    /**
     * Export chat history for a session
     */
    public function exportSession(Bot $bot, Request $request)
    {
        $this->authorizeBot($bot);

        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->back()->with('error', 'No session specified.');
        }

        $messages = $bot->chatLogs()
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($messages->isEmpty()) {
            return redirect()->back()->with('error', 'No messages found for this session.');
        }

        $filename = "chat_session_{$sessionId}_" . now()->format('Y-m-d_H-i-s') . ".txt";

        $headers = [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $content = "Chat Session Export\n";
        $content .= "Bot: {$bot->name}\n";
        $content .= "Session ID: {$sessionId}\n";
        $content .= "Exported: " . now()->format('Y-m-d H:i:s') . "\n";
        $content .= str_repeat('=', 50) . "\n\n";

        foreach ($messages as $msg) {
            $role = strtoupper($msg->role);
            $time = $msg->created_at->format('Y-m-d H:i:s');
            $content .= "[{$time}] {$role}:\n";
            $content .= $msg->content . "\n\n";
            $content .= str_repeat('-', 50) . "\n\n";
        }

        return response($content, 200, $headers);
    }

    /**
     * Get unread sessions count (for notification badge)
     */
    public function unreadCount(Bot $bot)
    {
        $this->authorizeBot($bot);

        $timeoutMins = $bot->admin_timeout_mins ?? 15;

        // Get sessions that have user messages but no recent admin replies
        $unreadSessions = $bot->chatLogs()
            ->select('session_id')
            ->where('role', 'user')
            ->whereNotIn('session_id', function($query) use ($bot, $timeoutMins) {
                $query->select('session_id')
                    ->from('chat_logs')
                    ->where('bot_id', $bot->id)
                    ->where('role', 'admin')
                    ->where('created_at', '>=', DB::raw("DATE_SUB(NOW(), INTERVAL {$timeoutMins} MINUTE)"));
            })
            ->groupBy('session_id')
            ->get();

        return response()->json([
            'count' => $unreadSessions->count(),
            'sessions' => $unreadSessions->pluck('session_id')
        ]);
    }

    /**
     * Get session details with metadata
     */
    public function sessionDetails(Bot $bot, string $sessionId)
    {
        $this->authorizeBot($bot);

        $messages = $bot->chatLogs()
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'role' => $message->role,
                    'content' => $message->content,
                    'created_at' => $this->formatDateForJson($message->created_at),
                ];
            });

        $stats = SessionStat::where('bot_id', $bot->id)
            ->where('session_id', $sessionId)
            ->first();

        $lead = $bot->leads()
            ->where('session_id', $sessionId)
            ->first();

        // Parse session ID to get channel info
        $channelInfo = $this->parseSessionId($sessionId, $bot);

        return response()->json([
            'messages' => $messages,
            'stats' => $stats,
            'lead' => $lead,
            'channel' => $channelInfo,
            'message_count' => $messages->count(),
            'start_time' => $messages->first()['created_at'] ?? null,
            'last_activity' => $messages->last()['created_at'] ?? null,
        ]);
    }

    /**
     * Check if session has recent admin reply
     */
    private function hasRecentAdminReply(Bot $bot, string $sessionId): bool
    {
        return $bot->chatLogs()
            ->where('session_id', $sessionId)
            ->where('role', 'admin')
            ->where('created_at', '>=', now()->subMinutes(10))
            ->exists();
    }

    /**
     * Parse session ID to extract channel information
     */
    private function parseSessionId(string $sessionId, Bot $bot): ?array
    {
        $parts = explode('__', $sessionId);

        if (count($parts) === 3) {
            $channelType = $parts[0];
            $channelId = (int) $parts[1];
            $userId = $parts[2];

            $channel = $bot->channels()->find($channelId);

            $channelNames = [
                'fb' => 'Facebook Messenger',
                'zalo' => 'Zalo Official Account',
                'tt' => 'TikTok Shop',
                'sp' => 'Shopee',
                'zlpn' => 'Zalo Personal',
                'wa' => 'WhatsApp',
            ];

            $icons = [
                'fb' => '📘',
                'zalo' => '💬',
                'tt' => '🎵',
                'sp' => '🛍️',
                'zlpn' => '💚',
                'wa' => '💚',
            ];

            return [
                'type' => $channelType,
                'type_name' => $channelNames[$channelType] ?? ucfirst($channelType),
                'channel_id' => $channelId,
                'channel_name' => $channel?->channel_name,
                'user_id' => $userId,
                'is_external' => true,
                'icon' => $icons[$channelType] ?? '💬',
            ];
        }

        // Web chat session
        return [
            'type' => 'web',
            'type_name' => 'Website Chat',
            'is_external' => false,
            'icon' => '🌐',
        ];
    }

    /**
     * Format date for JSON response
     * Safely handles both Carbon instances and string dates
     */
    private function formatDateForJson($date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            if ($date instanceof \Carbon\Carbon) {
                return $date->toISOString();
            }

            if (is_string($date)) {
                return Carbon::parse($date)->toISOString();
            }

            if ($date instanceof \DateTime) {
                return Carbon::instance($date)->toISOString();
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Date formatting error: ' . $e->getMessage(), ['date' => $date]);
            return null;
        }
    }

    /**
     * Authorize bot access
     */
    private function authorizeBot(Bot $bot): void
    {
        $user = auth()->user();

        if (!$user->isAdmin() && $bot->user_id !== $user->id) {
            abort(403, 'You do not have permission to access this bot.');
        }
    }
}
