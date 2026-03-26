<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Configure session lifetime for longer sessions
        config(['session.lifetime' => 120]);

        // Set maximum execution time for long operations
        set_time_limit(120);
    }
}
