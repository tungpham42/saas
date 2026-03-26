<?php

use Illuminate\Support\Facades\Schedule;

// Check for ended chat sessions every 5 minutes
Schedule::command('saas:check-ended-chats')->everyFiveMinutes();

// Clear cache daily
Schedule::command('saas:clear-cache')->daily();
