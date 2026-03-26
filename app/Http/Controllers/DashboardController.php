<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bot;
use App\Models\Lead;
use App\Models\SessionStat;
use App\Models\ChatLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->userDashboard($user);
    }

    public function adminDashboard()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalBots = Bot::count();
        $totalMessages = ChatLog::count();
        $totalLeads = Lead::count();

        // Revenue calculation
        $usersWithAddons = User::where('bot_limit', '>', 1)->get();
        $totalMonthlyRevenue = 0;
        foreach ($usersWithAddons as $user) {
            $additionalBots = max(0, $user->bot_limit - 1);
            $totalMonthlyRevenue += $additionalBots * 2;
        }

        $recentUsers = User::where('role', 'user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentBots = Bot::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Charts data - last 30 days
        $last30Days = collect(range(29, 0))->map(function($days) {
            return now()->subDays($days)->format('Y-m-d');
        });

        $messagesByDay = ChatLog::where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $chartData = $last30Days->map(function($date) use ($messagesByDay) {
            return [
                'date' => $date,
                'messages' => $messagesByDay[$date] ?? 0
            ];
        });

        return view('dashboard.admin', compact(
            'totalUsers', 'totalBots', 'totalMessages', 'totalLeads',
            'totalMonthlyRevenue', 'recentUsers', 'recentBots', 'chartData'
        ));
    }

    private function userDashboard($user)
    {
        $botCount = $user->bots()->count();
        $botLimit = $user->bot_limit;
        $remainingSlots = $user->getRemainingBotSlots();

        // Get recent leads from user's bots
        $recentLeads = Lead::whereIn('bot_id', $user->bots()->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent sessions
        $recentSessions = SessionStat::whereIn('bot_id', $user->bots()->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $totalMessages = ChatLog::whereIn('bot_id', $user->bots()->pluck('id'))->count();
        $totalLeads = Lead::whereIn('bot_id', $user->bots()->pluck('id'))->count();

        // Calculate usage per bot
        $botsUsage = [];
        foreach ($user->bots as $bot) {
            $botsUsage[] = [
                'bot' => $bot,
                'messages' => $bot->chatLogs()->count(),
                'leads' => $bot->leads()->count(),
                'sessions' => $bot->sessionStats()->count(),
            ];
        }

        return view('dashboard.user', compact(
            'user', 'botCount', 'botLimit', 'remainingSlots',
            'recentLeads', 'recentSessions', 'totalMessages', 'totalLeads', 'botsUsage'
        ));
    }
}
