<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\Channel;
use App\Models\RAGDocument;
use App\Models\ChatLog;
use App\Models\SessionStat;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BotController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $bots = Bot::with('user')->orderBy('id', 'desc')->get();
        } else {
            $bots = $user->bots()->orderBy('id', 'desc')->get();
        }

        $canCreate = $user->canCreateBot();
        $remainingSlots = $user->getRemainingBotSlots();

        return view('bots.index', compact('bots', 'canCreate', 'remainingSlots'));
    }

    public function create()
    {
        $user = auth()->user();

        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('error', 'Please verify your email address before creating a bot.');
        }

        if (!$user->canCreateBot()) {
            return redirect()->route('bots.index')
                ->with('error', 'You have reached your bot limit. Upgrade your plan to create more bots.');
        }

        return view('bots.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->canCreateBot()) {
            return redirect()->route('bots.index')
                ->with('error', 'You have reached your bot limit.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $bot = $user->bots()->create($validated);

        return redirect()->route('bots.show', $bot)
            ->with('success', 'Bot created successfully! API Key: ' . $bot->api_key);
    }

    public function show(Bot $bot)
    {
        $this->authorizeBot($bot);

        $tab = request('tab', 'settings');

        // Get statistics for dashboard (Sourced accurately from ChatLog)
        $totalSessions = ChatLog::where('bot_id', $bot->id)->distinct('session_id')->count('session_id');
        $totalLeads = Lead::where('bot_id', $bot->id)->count();
        $totalMessages = ChatLog::where('bot_id', $bot->id)->count();

        return view('bots.show', compact('bot', 'tab', 'totalSessions', 'totalLeads', 'totalMessages'));
    }

    public function update(Request $request, Bot $bot)
    {
        $this->authorizeBot($bot);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'provider' => 'required|in:openai,groq,gemini',
            'provider_api_key' => 'nullable|string',
            'model' => 'required|string',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'max_tokens' => 'nullable|integer|min:1|max:8192',
            'prompt_persona' => 'nullable|string',
            'prompt_task' => 'nullable|string',
            'prompt_context' => 'nullable|string',
            'prompt_format' => 'nullable|string',
            'ui_title' => 'nullable|string|max:100',
            'ui_welcome_msg' => 'nullable|string|max:255',
            'ui_placeholder' => 'nullable|string|max:100',
            'ui_btn_text' => 'nullable|string|max:50',
            'ui_color' => 'nullable|string|max:20',
            'ui_bg_color' => 'nullable|string|max:20',
            'ui_text_color' => 'nullable|string|max:20',
            'ui_pos_bottom' => 'nullable|string|max:20',
            'ui_pos_right' => 'nullable|string|max:20',
            'ui_pos_left' => 'nullable|string|max:20',
            'ui_trigger_icon' => 'nullable|string',
            'ui_trigger_bg_transparent' => 'boolean',
            'ui_trigger_border_radius' => 'nullable|string|max:20',
            'ui_clear_on_close' => 'boolean',
            'ui_pre_chat_form' => 'boolean',
            'ui_pre_chat_msg' => 'nullable|string',
            'ui_pre_chat_name_label' => 'nullable|string',
            'ui_pre_chat_phone_label' => 'nullable|string',
            'ui_pre_chat_btn_text' => 'nullable|string',
            'ui_pre_chat_error_msg' => 'nullable|string',
            'admin_timeout_mins' => 'nullable|integer|min:0',
            'history_limit' => 'nullable|integer|min:0',
            'email_notify_addresses' => 'nullable|string',
            'email_notify_timeout_mins' => 'nullable|integer|min:1',
        ]);

        $validated['ui_trigger_bg_transparent'] = $request->boolean('ui_trigger_bg_transparent');
        $validated['ui_clear_on_close'] = $request->boolean('ui_clear_on_close');
        $validated['ui_pre_chat_form'] = $request->boolean('ui_pre_chat_form');

        $bot->update($validated);

        $this->clearBotCache($bot);

        return redirect()->back()->with('success', 'Settings saved successfully!');
    }

    public function destroy(Bot $bot)
    {
        $this->authorizeBot($bot);

        // Delete all related data
        $bot->channels()->delete();
        $bot->chatLogs()->delete();
        $bot->ragDocuments()->delete();
        $bot->sessionStats()->delete();
        $bot->leads()->delete();
        $bot->delete();

        return redirect()->route('bots.index')
            ->with('success', 'Bot deleted successfully.');
    }

    public function regenerateApiKey(Bot $bot)
    {
        $this->authorizeBot($bot);

        $bot->update(['api_key' => 'sk_live_' . \Illuminate\Support\Str::random(24)]);

        return redirect()->back()->with('success', 'API Key regenerated successfully.');
    }

    private function authorizeBot(Bot $bot)
    {
        $user = auth()->user();

        if (!$user->isAdmin() && $bot->user_id !== $user->id) {
            abort(403, 'You do not own this bot.');
        }
    }

    private function clearBotCache(Bot $bot)
    {
        Cache::forget("bot_{$bot->api_key}");
        Cache::forget("bot_config_{$bot->id}");
    }
}
