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

        // Active trial bypasses feature restrictions to let user test capabilities
        if ($salon && method_exists($salon, 'isOnTrial') && $salon->isOnTrial()) {
            return $next($request);
        }

        $subscription = $salon?->activeSubscription;
        $plan = $subscription?->plan;

        $hasFeature = $plan && !$plan->isEmptyFeatures() && $plan->hasFeature($feature);

        if (!$hasFeature) {
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
                'currentPlan' => $plan,
            ], 403);
        }

        return $next($request);
    }
}
