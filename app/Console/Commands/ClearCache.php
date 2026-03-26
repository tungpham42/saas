<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class ClearCache extends Command
{
    protected $signature = 'saas:clear-cache';
    protected $description = 'Clear all application caches';

    public function handle()
    {
        $this->info('Clearing application caches...');

        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        Cache::flush();

        $this->info('All caches cleared successfully.');

        return Command::SUCCESS;
    }
}
