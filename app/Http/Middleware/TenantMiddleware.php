<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Bot;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $bot = $request->route('bot');

        if ($bot instanceof Bot) {
            $user = $request->user();

            if (!$user->isAdmin() && $bot->user_id !== $user->id) {
                abort(403, 'You do not own this bot.');
            }
        }

        return $next($request);
    }
}
