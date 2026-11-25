<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanFeature
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = auth()->user();

        // Super Admin bypasses all feature checks
        if ($user && $user->hasRole('super_admin')) {
            return $next($request);
        }

        // Resolve salon context safely
        $salon = app()->bound('current_salon') && app('current_salon')
            ? app('current_salon')
            : ($user?->salon ?? ($user?->salon_id ? \App\Models\Salon::find($user->salon_id) : null));

        if (!$salon && $request->route('salon_slug')) {
            $salon = \App\Models\Salon::where('slug', $request->route('salon_slug'))->first();
        }

        $entitlementService = app(\App\Services\ModuleEntitlementService::class);
        $moduleKey = $entitlementService->getModuleKeyForFeature($feature);

        // 1. HARD GLOBAL CHECK: If module is disabled globally, deny access immediately
        if ($moduleKey && !$entitlementService->isGloballyEnabled($moduleKey)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "The '{$feature}' module is currently disabled by the system administrator.",
                    'module_disabled' => true,
                ], 403);
            }

            $salonSlug = $request->route('salon_slug') ?? ($salon?->slug);
            return redirect()->route('admin.dashboard', ['salon_slug' => $salonSlug])
                ->with('error', "The '{$feature}' module is currently disabled by the system administrator.");
        }

        // 2. Authoritative feature entitlement check
        $canUse = $salon ? $entitlementService->canUseFeature($salon, $feature) : false;

        if (!$canUse) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "The '{$feature}' module is not available in your current plan. Please upgrade to unlock.",
                    'upgrade_required' => true,
                ], 403);
            }

            return response()->view('errors.feature-locked', [
                'feature' => $feature,
                'salon' => $salon,
                'currentPlan' => $salon?->currentSubscription()?->plan,
            ], 403);
        }

        return $next($request);
    }
}
