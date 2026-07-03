<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class unScheduledInsuranceCheck
{
    private const CACHE_KEY = 'insurances_notify_expiring_last_run';
    private const INTERVAL_HOURS = 24;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Runs after the response has already been sent, so it never slows down the page load.
     */
    public function terminate(Request $request, Response $response): void
    {
        $lastRun = Cache::get(self::CACHE_KEY);

        if ($lastRun && now()->diffInHours($lastRun) < self::INTERVAL_HOURS) {
            return;
        }

        Cache::put(self::CACHE_KEY, now(), now()->addDays(2));

        Artisan::call('insurances:notify-expiring');
    }
}
