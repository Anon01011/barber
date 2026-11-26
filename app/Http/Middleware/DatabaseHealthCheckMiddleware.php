<?php

namespace App\Http\Middleware;

use App\Services\DatabaseFailoverService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DatabaseHealthCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Ensures application routes to the active database (primary or failover replica).
     * Periodically checks database connectivity if not checked recently.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. If failover is currently flagged active, ensure runtime connection points to replica
        if (Cache::get('db_failover_active', false)) {
            Config::set('database.default', 'mysql_replica');
        }

        // 2. Throttle passive health check (once every 30 seconds per worker/thread)
        // If cooldown expired, run a fast probe of the primary database
        if (!Cache::has('db_health_check_cooldown')) {
            Cache::put('db_health_check_cooldown', true, 30);

            try {
                /** @var DatabaseFailoverService $failoverService */
                $failoverService = app(DatabaseFailoverService::class);
                $failoverService->runHealthCheck();
            } catch (\Throwable $e) {
                Log::warning('DatabaseHealthCheckMiddleware passive probe error: ' . $e->getMessage());
            }
        }

        return $next($request);
    }
}
