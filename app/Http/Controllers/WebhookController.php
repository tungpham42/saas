<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\Channel;
use App\Services\ChannelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected ChannelService $channelService;

    public function __construct(ChannelService $channelService)
    {
        $this->channelService = $channelService;
    }

    /**
     * Serve the embed JavaScript
     */
    public function serveEmbed(Request $request)
    {
        $apiKey = $request->query('api_key');
        $bot = Bot::where('api_key', $apiKey)->first();

        if (!$bot) {
            return response("console.error('SaaS AI: Invalid API Key');", 200)
                ->header('Content-Type', 'application/javascript');
        }

        $apiUrl = route('api.chat');
        $pollUrl = route('api.poll');
        $leadUrl = route('api.capture-lead');

        $color = json_encode($bot->ui_color);
        $bg = json_encode($bot->ui_bg_color);
        $textColor = json_encode($bot->ui_text_color ?: '#333333');
        $title = json_encode($bot->ui_title);
        $welcomeMsg = json_encode($bot->ui_welcome_msg ?: 'Hello! How can I help you today?');
        $placeholder = json_encode($bot->ui_placeholder ?: 'Type a message...');
        $btnText = json_encode($bot->ui_btn_text ?: 'Send');
        $posBottom = json_encode($bot->ui_pos_bottom ?: '20px');
        $posRight = json_encode($bot->ui_pos_right ?: '20px');
        $posLeft = json_encode($bot->ui_pos_left ?: 'auto');
        $triggerRadius = json_encode($bot->ui_trigger_border_radius ?: '50%');

        $triggerIconRaw = $bot->ui_trigger_icon ?: '💬';
        if (filter_var($triggerIconRaw, FILTER_VALIDATE_URL)) {
            $triggerIcon = "<img src='" . e($triggerIconRaw) . "' style='width:100%; height:100%; border-radius:{$triggerRadius}; object-fit:cover; pointer-events:none;' alt='Chat' />";
        } else {
            $triggerIcon = e($triggerIconRaw);
        }

        $triggerBgCss = empty($bot->ui_trigger_bg_transparent)
            ? "background: {$bot->ui_color}; box-shadow: 0 4px 15px rgba(0,0,0,0.2);"
            : "background: transparent; box-shadow: none;";

        $clearOnClose = $bot->ui_clear_on_close ? 'true' : 'false';
        $preChatEnabled = $bot->ui_pre_chat_form ? 'true' : 'false';

        $preChatMsg = json_encode($bot->ui_pre_chat_msg ?: 'Please enter your information to start support:');
        $preChatNameLabel = json_encode($bot->ui_pre_chat_name_label ?: 'Full Name *');
        $preChatPhoneLabel = json_encode($bot->ui_pre_chat_phone_label ?: 'Phone Number *');
        $preChatBtnText = json_encode($bot->ui_pre_chat_btn_text ?: 'Start Chat');
        $preChatErrorMsg = json_encode($bot->ui_pre_chat_error_msg ?: 'Please fill in all required information.');

        $js = view('embed.script', compact(
            'apiKey', 'apiUrl', 'pollUrl', 'leadUrl',
            'color', 'bg', 'textColor', 'title', 'welcomeMsg',
            'placeholder', 'btnText', 'posBottom', 'posRight', 'posLeft',
            'triggerIcon', 'triggerBgCss', 'triggerRadius', 'clearOnClose',
            'preChatEnabled', 'preChatMsg', 'preChatNameLabel', 'preChatPhoneLabel',
            'preChatBtnText', 'preChatErrorMsg'
        ))->render();

        return response($js, 200)
            ->header('Content-Type', 'application/javascript')
            ->header('Access-Control-Allow-Origin', '*');
    }

    /**
     * Handle chat request
     */
    public function chat(Request $request)
    {
        $apiKey = $request->input('api_key');
        $sessionId = $request->input('session_id');
        $message = $request->input('message');

        $bot = Bot::where('api_key', $apiKey)->first();

        if (!$bot) {
            return response()->json(['error' => 'Invalid API Key'], 401);
        }

        $response = app(\App\Services\LLMService::class)->generateResponse($bot, $sessionId, $message);

        return response()->json($response);
    }

    /**
     * Poll for new messages
     */
    public function poll(Request $request)
    {
        $apiKey = $request->query('api_key');
        $sessionId = $request->query('session_id');
        $lastId = (int) $request->query('last_id', 0);
        $role = $request->query('role');

        $bot = Bot::where('api_key', $apiKey)->first();

        if (!$bot) {
            return response()->json(['error' => 'Invalid API Key'], 401);
        }

        $query = $bot->chatLogs()
            ->where('session_id', $sessionId)
            ->where('id', '>', $lastId);

        if ($role === 'admin') {
            $query->where('role', 'admin');
        }

        $messages = $query->orderBy('id', 'asc')->get();

        return response()->json(['messages' => $messages]);
    }

    /**
     * Capture lead from pre-chat form
     */
    public function captureLead(Request $request)
    {
        $apiKey = $request->input('api_key');
        $sessionId = $request->input('session_id');
        $name = $request->input('name');
        $phone = $request->input('phone');

        $bot = Bot::where('api_key', $apiKey)->first();

        if (!$bot) {
            return response()->json(['error' => 'Invalid API Key'], 401);
        }

        if (!empty($name) && !empty($phone)) {
            $bot->leads()->create([
                'session_id' => $sessionId,
                'customer_name' => $name,
                'customer_phone' => $phone,
            ]);

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Missing data'], 400);
    }

    /**
     * Handle Facebook webhook
     */
    public function handleFacebook(Request $request)
    {
        $apiKey = $request->query('api_key');
        $channelId = (int) $request->query('channel_id');

        Log::info('Facebook webhook called', [
            'method' => $request->method(),
            'api_key' => $apiKey,
            'channel_id' => $channelId,
            'has_api_key' => !empty($apiKey),
            'has_channel_id' => !empty($channelId)
        ]);

        $bot = Bot::where('api_key', $apiKey)->first();

        if (!$bot) {
            Log::warning('Facebook webhook: Invalid API key', ['api_key' => $apiKey]);
            return response()->json(['error' => 'Invalid API Key'], 401);
        }

        $channel = $bot->channels()->find($channelId);

        if (!$channel) {
            Log::warning('Facebook webhook: Channel not found', ['channel_id' => $channelId]);
            return response()->json(['error' => 'Channel not found'], 404);
        }

        // Handle verification for GET requests FIRST (Always allow Meta to verify)
        if ($request->isMethod('get')) {
            return $this->verifyWebhook($request, $channel, 'facebook');
        }

        // Check if active ONLY for incoming POST messages
        if (!$channel->is_active) {
            Log::warning('Facebook webhook: Channel inactive', ['channel_id' => $channelId]);
            return response()->json(['status' => 'channel_disabled']);
        }

        $payload = $request->json()->all();
        Log::info('Facebook webhook: Processing message', ['payload_keys' => array_keys($payload)]);
        $this->channelService->handleFacebook($bot, $channel, $payload);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Handle Zalo webhook
     */
    public function handleZalo(Request $request)
    {
        $apiKey = $request->query('api_key');
        $channelId = (int) $request->query('channel_id');

        $bot = Bot::where('api_key', $apiKey)->first();

        if (!$bot) {
            return response()->json(['error' => 'Invalid API Key'], 401);
        }

        $channel = $bot->channels()->find($channelId);

        if (!$channel || !$channel->is_active) {
            return response()->json(['status' => 'channel_disabled']);
        }

        $payload = $request->json()->all();
        $this->channelService->handleZalo($bot, $channel, $payload);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Handle TikTok webhook
     */
    public function handleTikTok(Request $request)
    {
        $apiKey = $request->query('api_key');
        $channelId = (int) $request->query('channel_id');

        $bot = Bot::where('api_key', $apiKey)->first();

        if (!$bot) {
            return response()->json(['error' => 'Invalid API Key'], 401);
        }

        $channel = $bot->channels()->find($channelId);

        if (!$channel || !$channel->is_active) {
            return response()->json(['status' => 'channel_disabled']);
        }

        $payload = $request->json()->all();
        $this->channelService->handleTikTok($bot, $channel, $payload);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Handle Shopee webhook
     */
    public function handleShopee(Request $request)
    {
        $apiKey = $request->query('api_key');
        $channelId = (int) $request->query('channel_id');

        $bot = Bot::where('api_key', $apiKey)->first();

        if (!$bot) {
            return response()->json(['error' => 'Invalid API Key'], 401);
        }

        $channel = $bot->channels()->find($channelId);

        if (!$channel || !$channel->is_active) {
            return response()->json(['status' => 'channel_disabled']);
        }

        $payload = $request->json()->all();
        $this->channelService->handleShopee($bot, $channel, $payload);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Handle Zalo Personal webhook
     */
    public function handleZaloPersonal(Request $request)
    {
        $apiKey = $request->query('api_key');
        $channelId = (int) $request->query('channel_id');

        $bot = Bot::where('api_key', $apiKey)->first();

        if (!$bot) {
            return response()->json(['error' => 'Invalid API Key'], 401);
        }

        $channel = $bot->channels()->find($channelId);

        if (!$channel || !$channel->is_active) {
            return response()->json(['status' => 'channel_disabled']);
        }

        $payload = $request->json()->all();
        $this->channelService->handleZaloPersonal($bot, $channel, $payload);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Handle WhatsApp webhook
     */
    public function handleWhatsApp(Request $request)
    {
        $apiKey = $request->query('api_key');
        $channelId = (int) $request->query('channel_id');

        Log::info('WhatsApp webhook called', [
            'method' => $request->method(),
            'api_key' => $apiKey,
            'channel_id' => $channelId,
            'has_api_key' => !empty($apiKey),
            'has_channel_id' => !empty($channelId)
        ]);

        $bot = Bot::where('api_key', $apiKey)->first();

        if (!$bot) {
            Log::warning('WhatsApp webhook: Invalid API key', ['api_key' => $apiKey]);
            return response()->json(['error' => 'Invalid API Key'], 401);
        }

        $channel = $bot->channels()->find($channelId);

        if (!$channel) {
            Log::warning('WhatsApp webhook: Channel not found', ['channel_id' => $channelId]);
            return response()->json(['error' => 'Channel not found'], 404);
        }

        // Handle verification for GET requests FIRST
        if ($request->isMethod('get')) {
            return $this->verifyWebhook($request, $channel, 'whatsapp');
        }

        // Check if active ONLY for incoming POST messages
        if (!$channel->is_active) {
            Log::warning('WhatsApp webhook: Channel inactive', ['channel_id' => $channelId]);
            return response()->json(['status' => 'channel_disabled']);
        }

        $payload = $request->json()->all();
        Log::info('WhatsApp webhook: Processing message', ['payload_keys' => array_keys($payload)]);
        $this->channelService->handleWhatsApp($bot, $channel, $payload);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Verify webhook for Meta platforms (Facebook, WhatsApp)
     */
    private function verifyWebhook(Request $request, Channel $channel, string $platform)
    {
        $verifyTokenKey = $platform === 'facebook' ? 'fb_verify_token' : 'whatsapp_verify_token';
        $storedToken = $channel->config[$verifyTokenKey] ?? '';

        $hubMode = $request->query('hub_mode');
        $hubVerifyToken = $request->query('hub_verify_token');
        $hubChallenge = $request->query('hub_challenge');

        if (empty($hubMode)) {
            $hubMode = $request->query('hub.mode');
        }
        if (empty($hubVerifyToken)) {
            $hubVerifyToken = $request->query('hub.verify_token');
        }
        if (empty($hubChallenge)) {
            $hubChallenge = $request->query('hub.challenge');
        }

        // FIX: Cast to string before trimming to prevent PHP 8.1+ TypeError on null
        $storedToken = trim((string) $storedToken);
        $hubVerifyToken = trim((string) $hubVerifyToken);

        $isModeValid = $hubMode === 'subscribe';
        $isTokenValid = !empty($storedToken) && $hubVerifyToken === $storedToken;

        if ($isModeValid && $isTokenValid) {
            Log::info("{$platform} webhook verification successful", [
                'channel_id' => $channel->id,
                'challenge' => $hubChallenge
            ]);

            return response($hubChallenge, 200)
                ->header('Content-Type', 'text/plain');
        }

        $failureReasons = [];
        if (!$isModeValid) {
            $failureReasons[] = "hub_mode is '{$hubMode}', expected 'subscribe'";
        }
        if (!$isTokenValid) {
            if (empty($storedToken)) {
                $failureReasons[] = "No verification token configured for this channel";
            } else {
                $failureReasons[] = "Token mismatch: received '{$hubVerifyToken}', stored '{$storedToken}'";
            }
        }

        Log::warning("{$platform} webhook verification failed", [
            'channel_id' => $channel->id,
            'reasons' => $failureReasons,
            'full_query' => $request->getQueryString()
        ]);

        return response()->json([
            'error' => 'Verification failed',
            'message' => 'The verification token does not match or the mode is incorrect.',
            'details' => $failureReasons
        ], 403);
    }
}
