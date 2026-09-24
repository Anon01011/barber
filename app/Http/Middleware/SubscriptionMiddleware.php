<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $return
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login');
            }

            // Allow super admin to access everything
            if ($user->roles->pluck('name')->contains('super_admin')) {
                return $next($request);
            }

            // Allow access to subscription/payment pages even if subscription is not active
            // NOTE: Routes are under prefix('admin')->name('admin.') so full names are admin.saas.*
            if ($request->is('*/subscription*', '*/payment*') || $request->routeIs('admin.saas.subscription.*', 'admin.saas.payment.*')) {
                return $next($request);
            }

            // Check if user has a salon
            if (!$user->salon) {
                return redirect()->route('saas.errors.no-active-plan')->with('error', 'You are not associated with any salon. Please contact support.');
            }

            $salon = $user->salon;

            // Check if on active trial
            if ($salon->isOnTrial()) {
                return $next($request);
            }

            // Check if salon has an active subscription
            $subscription = $salon->activeSubscription;

            // Explicit check: if subscription exists but is expired, block access
            if ($subscription && $subscription->ends_at && $subscription->ends_at->isPast()) {
                $message = 'Your subscription expired on ' . $subscription->ends_at->format('M d, Y') . '. Please renew your subscription to continue using the service.';
                return redirect()->route('admin.saas.subscription.index', ['salon_slug' => $salon->slug])
                    ->with('error', $message);
            }

            // Check if has active subscription
            if ($subscription && $subscription->isActive()) {
                return $next($request);
            }

            // Determine error message
            $message = 'Your subscription has expired or has not been activated yet. Please renew your subscription to continue using the service.';

            if ($salon->trial_ends_at && $salon->trial_ends_at->isPast() && !$subscription) {
                $message = 'Your free trial has expired. Please upgrade to a paid plan to continue using the service.';
            } elseif ($subscription && !$subscription->isActive()) {
                $message = 'Your subscription has expired. Please renew your subscription to continue using the service.';
            }

            // Redirect to subscription index for renewal instead of a static error page
            return redirect()->route('admin.saas.subscription.index', ['salon_slug' => $salon->slug])
                ->with('error', $message);

        } catch (\Exception $e) {
            \Log::error("SubscriptionMiddleware Error: " . $e->getMessage());
            // SECURITY FIX: Don't allow access on exceptions - redirect to subscription page
            if (auth()->check() && auth()->user()->salon) {
                return redirect()->route('admin.saas.subscription.index', ['salon_slug' => auth()->user()->salon->slug])
                    ->with('error', 'Unable to verify subscription status. Please try again or contact support.');
            }
            return redirect()->route('login');
        }
    }
}
