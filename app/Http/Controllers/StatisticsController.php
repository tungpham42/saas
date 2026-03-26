<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\SessionStat;
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
        $query = SessionStat::where('bot_id', $bot->id);

        // Apply date filters
        if ($preset === 'today') {
            $query->whereDate('start_time', today());
        } elseif ($preset === 'yesterday') {
            $query->whereDate('start_time', today()->subDay());
        } elseif ($preset === 'last_7') {
            $query->where('start_time', '>=', today()->subDays(7));
        } elseif ($preset === 'this_month') {
            $query->whereYear('start_time', today()->year)
                  ->whereMonth('start_time', today()->month);
        } elseif ($preset === 'last_month') {
            $lastMonth = today()->subMonth();
            $query->whereYear('start_time', $lastMonth->year)
                  ->whereMonth('start_time', $lastMonth->month);
        } elseif ($preset === 'last_30') {
            $query->where('start_time', '>=', today()->subDays(30));
        } elseif ($preset === 'custom' && $date) {
            $query->whereDate('start_time', $date);
        }

        $totalSessions = (clone $query)->count();
        $takenOver = (clone $query)->whereNotNull('first_admin_time')->count();
        $totalAdminMsgs = (clone $query)->sum('admin_msg_count');

        $avgFirstResponse = (clone $query)
            ->whereNotNull('first_admin_time')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, start_time, first_admin_time)) as avg'))
            ->value('avg') ?: 0;

        $avgHandlingTime = (clone $query)
            ->whereNotNull('last_admin_time')
            ->whereNotNull('first_admin_time')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, first_admin_time, last_admin_time)) as avg'))
            ->value('avg') ?: 0;

        $totalOnlineTime = (clone $query)
            ->whereNotNull('last_admin_time')
            ->whereNotNull('first_admin_time')
            ->select(DB::raw('SUM(TIMESTAMPDIFF(SECOND, first_admin_time, last_admin_time)) as total'))
            ->value('total') ?: 0;

        // Get sessions per day for chart
        $days = collect(range(6, 0))->map(function($days) {
            return today()->subDays($days)->format('Y-m-d');
        });

        $sessionsByDay = SessionStat::where('bot_id', $bot->id)
            ->where('start_time', '>=', today()->subDays(7))
            ->select(DB::raw('DATE(start_time) as date'), DB::raw('count(*) as count'))
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
