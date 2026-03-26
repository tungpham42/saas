<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class LeadController extends Controller
{
    public function index(Bot $bot)
    {
        $this->authorizeBot($bot);

        // Get leads with pagination
        $leads = $bot->leads()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('leads.index', compact('bot', 'leads'));
    }

    public function show(Bot $bot, Lead $lead)
    {
        $this->authorizeBot($bot);

        // Ensure lead belongs to this bot
        if ($lead->bot_id !== $bot->id) {
            abort(404);
        }

        return view('leads.show', compact('bot', 'lead'));
    }

    public function export(Bot $bot, Request $request)
    {
        $this->authorizeBot($bot);

        $query = $bot->leads();

        // Apply filters if present
        if ($request->has('session_id')) {
            $query->where('session_id', $request->session_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $leads = $query->orderBy('created_at', 'desc')->get();

        if ($leads->isEmpty()) {
            return redirect()->back()->with('error', 'No leads found to export.');
        }

        $filename = "leads_bot_{$bot->id}_" . now()->format('Y-m-d_H-i-s') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($leads) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            fputcsv($file, [
                'ID',
                'Name',
                'Phone',
                'Session ID',
                'Channel',
                'Bot Name',
                'Created At',
                'Created Date',
                'Created Time'
            ]);

            foreach ($leads as $lead) {
                // Parse channel info
                $channelInfo = $this->parseChannelInfo($lead->session_id, $lead->bot);

                fputcsv($file, [
                    $lead->id,
                    $lead->customer_name,
                    $lead->customer_phone,
                    $lead->session_id,
                    $channelInfo['name'],
                    $lead->bot->name,
                    $lead->created_at,
                    $lead->created_at->format('Y-m-d'),
                    $lead->created_at->format('H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function parseChannelInfo($sessionId, $bot)
    {
        $parts = explode('__', $sessionId);

        $names = [
            'fb' => 'Facebook Messenger',
            'zalo' => 'Zalo Official Account',
            'tt' => 'TikTok Shop',
            'sp' => 'Shopee',
            'zlpn' => 'Zalo Personal',
            'wa' => 'WhatsApp',
        ];

        if (count($parts) === 3) {
            $channelType = $parts[0];
            return [
                'name' => $names[$channelType] ?? ucfirst($channelType),
                'type' => $channelType,
            ];
        }

        return [
            'name' => 'Website Chat',
            'type' => 'web',
        ];
    }

    public function destroy(Bot $bot, Lead $lead)
    {
        $this->authorizeBot($bot);

        if ($lead->bot_id !== $bot->id) {
            abort(404);
        }

        $lead->delete();

        return redirect()->route('bots.leads', $bot)
            ->with('success', 'Lead deleted successfully.');
    }

    public function bulkDelete(Bot $bot, Request $request)
    {
        $this->authorizeBot($bot);

        $request->validate([
            'leads' => 'required|array',
            'leads.*' => 'exists:leads,id',
        ]);

        $deleted = $bot->leads()
            ->whereIn('id', $request->leads)
            ->delete();

        return redirect()->route('bots.leads', $bot)
            ->with('success', "{$deleted} lead(s) deleted successfully.");
    }

    private function authorizeBot(Bot $bot)
    {
        $user = auth()->user();

        if (!$user->isAdmin() && $bot->user_id !== $user->id) {
            abort(403, 'You do not own this bot.');
        }
    }
}
