<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Console\Scheduling\Schedule as SchedulingSchedule;

// Check for ended chat sessions every 5 minutes
Schedule::command('saas:check-ended-chats')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// Clear cache daily
Schedule::command('saas:clear-cache')
    ->daily()
    ->at('02:00')
    ->withoutOverlapping()
    ->runInBackground();

// Optional: Send a summary email daily
Schedule::call(function () {
    $pendingSessions = \App\Models\SessionStat::where('is_emailed', false)
        ->where('created_at', '>=', now()->subDay())
        ->count();

    if ($pendingSessions > 0) {
        \Illuminate\Support\Facades\Log::info("Pending sessions to process: {$pendingSessions}");
    }
})->daily()->at('09:00');
