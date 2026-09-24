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

        // Check if user has salon and active subscription with a plan
        if (!$user || !$user->salon || !$user->salon->activeSubscription || !$user->salon->activeSubscription->plan) {
            abort(403, 'No active subscription or plan found. Please subscribe to access this feature.');
        }

        $plan = $user->salon->activeSubscription->plan;

        // Check if plan features configuration is empty
        if ($plan->isEmptyFeatures()) {
            abort(403, 'Your current plan has no features assigned. Please contact administrator or choose a valid plan.');
        }

        // Check if plan has the required feature
        if (!$plan->hasFeature($feature)) {
            abort(403, 'This feature is not available in your current plan. Please upgrade to access this feature.');
        }

        return $next($request);
    }
}
