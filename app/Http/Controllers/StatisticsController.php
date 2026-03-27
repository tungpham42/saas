<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\SessionStat;
use App\Models\ChatLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index(Bot $bot)
    {
        $this->authorizeBot($bot);

        $statPreset = request('stat_preset');
        $statDate = request('stat_date');

        $stats = $this->getStatistics($bot, $statPreset, $statDate);

        return view('statistics.index', array_merge($stats, [
            'bot' => $bot,
            'statPreset' => $statPreset,
            'statDate' => $statDate,
        ]));
    }

    private function getStatistics(Bot $bot, ?string $preset, ?string $date)
    {
        $chatLogQuery = ChatLog::where('bot_id', $bot->id);
        $statQuery = SessionStat::where('bot_id', $bot->id);

        // Apply date filters
        if ($preset === 'today') {
            $chatLogQuery->whereDate('created_at', today());
            $statQuery->whereDate('start_time', today());
        } elseif ($preset === 'yesterday') {
            $chatLogQuery->whereDate('created_at', today()->subDay());
            $statQuery->whereDate('start_time', today()->subDay());
        } elseif ($preset === 'last_7') {
            $chatLogQuery->where('created_at', '>=', today()->subDays(7));
            $statQuery->where('start_time', '>=', today()->subDays(7));
        } elseif ($preset === 'this_month') {
            $chatLogQuery->whereYear('created_at', today()->year)
                         ->whereMonth('created_at', today()->month);
            $statQuery->whereYear('start_time', today()->year)
                      ->whereMonth('start_time', today()->month);
        } elseif ($preset === 'last_month') {
            $lastMonth = today()->subMonth();
            $chatLogQuery->whereYear('created_at', $lastMonth->year)
                         ->whereMonth('created_at', $lastMonth->month);
            $statQuery->whereYear('start_time', $lastMonth->year)
                      ->whereMonth('start_time', $lastMonth->month);
        } elseif ($preset === 'last_30') {
            $chatLogQuery->where('created_at', '>=', today()->subDays(30));
            $statQuery->where('start_time', '>=', today()->subDays(30));
        } elseif ($preset === 'custom' && $date) {
            $chatLogQuery->whereDate('created_at', $date);
            $statQuery->whereDate('start_time', $date);
        }

        // Base metrics sourced from ChatLog so that Analytics updates correctly for every interaction
        $totalSessions = (clone $chatLogQuery)->distinct('session_id')->count('session_id');
        $totalAdminMsgs = (clone $chatLogQuery)->where('role', 'admin')->count();

        // Admin intervention specifics sourced from SessionStat
        $takenOver = (clone $statQuery)->whereNotNull('first_admin_time')->count();

        $avgFirstResponse = (clone $statQuery)
            ->whereNotNull('first_admin_time')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, start_time, first_admin_time)) as avg'))
            ->value('avg') ?: 0;

        $avgHandlingTime = (clone $statQuery)
            ->whereNotNull('last_admin_time')
            ->whereNotNull('first_admin_time')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, first_admin_time, last_admin_time)) as avg'))
            ->value('avg') ?: 0;

        $totalOnlineTime = (clone $statQuery)
            ->whereNotNull('last_admin_time')
            ->whereNotNull('first_admin_time')
            ->select(DB::raw('SUM(TIMESTAMPDIFF(SECOND, first_admin_time, last_admin_time)) as total'))
            ->value('total') ?: 0;

        // Get sessions per day for chart - using ChatLog for consistency
        $days = collect(range(6, 0))->map(function($days) {
            return today()->subDays($days)->format('Y-m-d');
        });

        $sessionsByDay = ChatLog::where('bot_id', $bot->id)
            ->where('created_at', '>=', today()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(DISTINCT session_id) as count'))
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $chartData = $days->map(function($date) use ($sessionsByDay) {
            return [
                'date' => $date,
                'sessions' => $sessionsByDay[$date] ?? 0
            ];
        });

        return [
            'totalSessions' => $totalSessions,
            'takenOver' => $takenOver,
            'totalAdminMsgs' => $totalAdminMsgs,
            'avgFirstResponse' => $this->formatDuration($avgFirstResponse),
            'avgHandlingTime' => $this->formatDuration($avgHandlingTime),
            'totalOnlineTime' => $this->formatDuration($totalOnlineTime),
            'chartData' => $chartData,
        ];
    }

    private function formatDuration(float $seconds): string
    {
        return self::formatDurationStatic($seconds);
    }

    public static function formatDurationStatic(float $seconds): string
    {
        if ($seconds < 60) return round($seconds) . 's';
        $minutes = floor($seconds / 60);
        $secs = round($seconds % 60);
        if ($minutes < 60) return $minutes . 'm ' . $secs . 's';
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        return $hours . 'h ' . $minutes . 'm';
    }

    private function authorizeBot(Bot $bot)
    {
        $user = auth()->user();

        if (!$user->isAdmin() && $bot->user_id !== $user->id) {
            abort(403, 'You do not own this bot.');
        }
    }
}
