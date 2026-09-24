<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RefreshPermissionCache
{
    /**
     * Handle an incoming request.
     *
     * This middleware ensures that permission cache is refreshed periodically
     * to prevent stale permissions from affecting users when salons are created/updated.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Check if we should refresh the cache (every 5 minutes per user session)
            $lastRefresh = session('permission_cache_refreshed_at');
            $shouldRefresh = !$lastRefresh || now()->diffInMinutes($lastRefresh) >= 5;

            if ($shouldRefresh && auth()->check()) {
                // Clear the user's cached permissions
                auth()->user()->forgetCachedPermissions();
                session(['permission_cache_refreshed_at' => now()]);
            }
        } catch (\Exception $e) {
            \Log::error("RefreshPermissionCache Error (DB Lock?): " . $e->getMessage());
        }

        return $next($request);
    }
}
