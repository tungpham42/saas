<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\Channel;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function index(Bot $bot)
    {
        $this->authorizeBot($bot);

        $channels = $bot->channels()->orderBy('channel_type')->orderBy('id', 'desc')->get();

        return view('channels.index', compact('bot', 'channels'));
    }

    public function store(Request $request, Bot $bot)
    {
        $this->authorizeBot($bot);

        $validated = $request->validate([
            'channel_type' => 'required|in:fb,zalo,tt,sp,zlpn,wa',
            'channel_name' => 'required|string|max:255',
        ]);

        $bot->channels()->create([
            'channel_type' => $validated['channel_type'],
            'channel_name' => $validated['channel_name'],
            'is_active' => true,
            'config' => [],
        ]);

        return redirect()->back()->with('success', 'Channel added successfully.');
    }

    public function update(Request $request, Bot $bot, Channel $channel)
    {
        $this->authorizeBot($bot);

        if ($channel->bot_id !== $bot->id) {
            abort(404);
        }

        $validated = $request->validate([
            'channel_name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $config = $channel->config ?? [];

        // Update channel-specific config
        switch ($channel->channel_type) {
            case 'fb':
                $config['fb_verify_token'] = $request->fb_verify_token;
                $config['fb_page_token'] = $request->fb_page_token;
                break;
            case 'zalo':
                $config['zalo_access_token'] = $request->zalo_access_token;
                break;
            case 'tt':
                $config['tiktok_access_token'] = $request->tiktok_access_token;
                break;
            case 'sp':
                $config['shopee_shop_id'] = $request->shopee_shop_id;
                $config['shopee_access_token'] = $request->shopee_access_token;
                break;
            case 'zlpn':
                $config['zalo_personal_token'] = $request->zalo_personal_token;
                break;
            case 'wa':
                $config['whatsapp_verify_token'] = $request->whatsapp_verify_token;
                $config['whatsapp_phone_number_id'] = $request->whatsapp_phone_number_id;
                $config['whatsapp_token'] = $request->whatsapp_token;
                break;
        }

        $channel->update([
            'channel_name' => $validated['channel_name'],
            'is_active' => $request->boolean('is_active'),
            'config' => $config,
        ]);

        return redirect()->back()->with('success', 'Channel updated successfully.');
    }

    public function destroy(Bot $bot, Channel $channel)
    {
        $this->authorizeBot($bot);

        if ($channel->bot_id !== $bot->id) {
            abort(404);
        }

        $channel->delete();

        return redirect()->back()->with('success', 'Channel deleted successfully.');
    }

    private function authorizeBot(Bot $bot)
    {
        $user = auth()->user();

        if (!$user->isAdmin() && $bot->user_id !== $user->id) {
            abort(403, 'You do not own this bot.');
        }
    }
}
