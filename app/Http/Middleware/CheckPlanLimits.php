<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanLimits
{
    /**
     * Handle an incoming request.
     *
     * Check if the salon has reached the limit for a specific resource
     * based on their subscription plan.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $resource  The resource to check (e.g., 'staff', 'branches', 'services')
     */
    public function handle(Request $request, Closure $next, string $resource): Response
    {
        $user = $request->user();

        // Skip for super admin
        if (!$user || $user->isSuperAdmin()) {
            return $next($request);
        }

        $salon = $user->salon;

        if (!$salon) {
            return redirect()->route('login')->with('error', 'No salon associated with this account.');
        }

        // Check if salon can add more of this resource
        $canAdd = match ($resource) {
            'staff', 'users' => $salon->canAddStaff(),
            'branches' => $salon->canAddBranch(),
            'services' => $salon->canAddService(),
            'products' => $salon->canAddProduct(),
            'customers' => $salon->canAddCustomer(),
            'memberships' => $salon->canAddMembership(),
            'packages' => $salon->canAddPackage(),
            'bookings' => $salon->canAddBooking(),
            'guest_bookings' => $salon->canAddGuestBooking(),
            default => true, // Allow if resource not recognized
        };

        if (!$canAdd) {
            $featureName = ucfirst($resource);
            $limit = $salon->getFeatureLimit("max_{$resource}");

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "You have reached the maximum number of {$featureName} allowed on your plan.",
                    'limit' => $limit,
                    'upgrade_url' => route('admin.saas.subscription.index', ['salon_slug' => $salon->slug]),
                ], 403);
            }

            return redirect()->back()->with('error', "You have reached the maximum number of {$featureName} ({$limit}) allowed on your current plan. Please upgrade to add more.");
        }

        return $next($request);
    }
}
