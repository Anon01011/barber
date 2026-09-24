<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSalonApproval
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = $request->user();

            // Check if user is authenticated and has a salon
            if ($user && $user->salon) {
                // Get the latest active or pending subscription
                $subscription = $user->salon->subscriptions()->latest()->first();

                // Check if subscription exists and is pending approval
                if ($subscription && $subscription->status === 'pending') {
                    // Allow logout and payment routes
                    if (
                        $request->routeIs('logout') ||
                        $request->routeIs('saas.payment.*') ||
                        $request->routeIs('admin.saas.payment.*')
                    ) {
                        return $next($request);
                    }

                    // Redirect to pending approval page
                    return redirect()->route('saas.pending-approval');
                }
            }
        } catch (\Exception $e) {
            \Log::error("CheckSalonApproval Error (DB Lock?): " . $e->getMessage());
        }

        return $next($request);
    }
}
