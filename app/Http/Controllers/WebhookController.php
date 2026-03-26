<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\Channel;
use App\Services\ChannelService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    protected ChannelService $channelService;

    public function __construct(ChannelService $channelService)
    {
        $this->channelService = $channelService;
    }

    public function handle(Request $request, string $type)
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

        // Handle verification for Facebook and WhatsApp
        if ($request->isMethod('get')) {
            $challenge = $this->channelService->verifyWebhook($channel, $request->query());
            if ($challenge) {
                return response($challenge, 200);
            }
            return response()->json(['error' => 'Verification failed'], 403);
        }

        // Handle incoming messages
        $payload = $request->json()->all();
        $this->channelService->handleWebhook($bot, $channel, $payload);

        return response()->json(['status' => 'ok']);
    }

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
}
