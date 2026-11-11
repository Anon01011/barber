<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * RestrictPlatformAdminWrites Middleware
 *
 * SECURITY FIX – Vulnerability 2 (CWE-862, CWE-269):
 * Prevents a Super Admin with a DIRECT platform session (not impersonating a
 * tenant owner) from performing write mutations (POST / PUT / PATCH / DELETE)
 * against salon-scoped resources.
 *
 * Allowed scenarios:
 *   1. The user is NOT a super_admin  → pass through as normal.
 *   2. The user is a super_admin who IS impersonating (has a salon_id because
 *      they logged in as a tenant owner via the impersonate route)  → pass through.
 *   3. The request is a read-only method (GET / HEAD / OPTIONS)  → pass through.
 *
 * Blocked scenario:
 *   Super Admin + direct session + mutating method  → 403.
 *
 * Apply this middleware to all {salon_slug}/admin/* route groups.
 */
class RestrictPlatformAdminWrites
{
    /**
     * HTTP verbs that mutate data.
     */
    private const WRITE_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Only applies to super_admin users
        if (!$user->hasRole('super_admin')) {
            return $next($request);
        }

        // If super_admin has a salon_id they are impersonating → allow
        if ($user->salon_id) {
            return $next($request);
        }

        // Check for impersonator session key set by SalonManagementController::impersonate()
        // This is a belt-and-suspenders check; having salon_id should already indicate impersonation.
        if (session()->has('impersonator_id')) {
            return $next($request);
        }

        // Direct platform-admin session attempting a write on a tenant route
        if (in_array(strtoupper($request->method()), self::WRITE_METHODS)) {
            $currentSalon = app()->bound('current_salon') ? app('current_salon') : null;

            Log::warning('[SECURITY] Platform admin attempted direct write on tenant route', [
                'admin_id'    => $user->id,
                'admin_name'  => $user->name,
                'method'      => $request->method(),
                'url'         => $request->fullUrl(),
                'salon_slug'  => $currentSalon?->slug,
                'ip'          => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);

            // Log to audit table
            \App\Models\SuperAdminAuditLog::logAction(
                'BLOCKED_DIRECT_WRITE',
                $currentSalon?->id,
                null,
                null,
                [
                    'method'  => $request->method(),
                    'url'     => $request->fullUrl(),
                    'blocked' => true,
                ]
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Platform administrators cannot perform write actions on tenant resources directly. Please use impersonation.',
                ], 403);
            }

            return redirect()->back()->with(
                'error',
                'Platform administrators cannot perform write actions directly on a salon. Please use the Impersonate button to act as the salon owner.'
            );
        }

        return $next($request);
    }
}
