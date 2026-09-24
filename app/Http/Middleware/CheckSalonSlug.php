<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Salon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class CheckSalonSlug
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('salon_slug');

        // Set default parameter for URL generation early to ensure it's available even if exceptions occur later
        if ($slug && is_string($slug)) {
            \Illuminate\Support\Facades\URL::defaults(['salon_slug' => $slug]);
        }

        try {
            if (env('SINGLE_SALON_MODE', false)) {
                $singleSlug = env('SINGLE_SALON_SLUG', 'intranet');
                if ($slug && $slug !== $singleSlug) {
                    return redirect()->route($request->route()->getName(), array_merge($request->route()->parameters(), ['salon_slug' => $singleSlug]));
                }
            }

            if (!$slug) {
                return $next($request);
            }

            $salon = Salon::where('slug', $slug)->first();

            if (!$salon) {
                Log::warning('Salon not found for slug', ['slug' => $slug, 'ip' => $request->ip()]);
                abort(404, 'Salon not found');
            }

            // ALWAYS bind current_salon before any redirect so redirect targets can access it
            app()->instance('current_salon', $salon);
            $request->merge(['current_salon' => $salon]);

            // Allow access to subscription and payment routes even if inactive
            // NOTE: These routes are under prefix('admin')->name('admin.') so full names are admin.saas.*
            $whiteListedRoutes = [
                'admin.saas.subscription.index',
                'admin.saas.subscription.renew',
                'admin.saas.subscription.upgrade',
                'admin.saas.subscription.history',
                'admin.saas.subscription.invoice',
                'admin.saas.subscription.cancel',
                'admin.saas.payment.confirm',
                'admin.saas.payment.create-intent',
                'admin.saas.payment.history',
                'admin.saas.payment.show',
                'logout'
            ];

            if ($request->routeIs($whiteListedRoutes) || $request->is('*/subscription*', '*/payment*')) {
                return $next($request);
            }

            // Check if salon is active (including subscription status)
            if (!$salon->isActive()) {
                // Allow super admin to access inactive salons
                $user = $request->user();
                if (!$user || !$user->hasRole('super_admin')) {
                    Log::warning('Attempt to access inactive/paused salon', [
                        'salon_id' => $salon->id,
                        'salon_slug' => $slug,
                        'user_id' => $user?->id,
                        'is_active' => $salon->is_active,
                        'subscription_status' => $salon->subscription_status,
                        'ip' => $request->ip()
                    ]);

                    // If salon itself is disabled by admin
                    if (!$salon->is_active) {
                        return redirect()->route('saas.errors.salon-inactive');
                    }

                    // If subscription is paused
                    if ($salon->subscription_status === 'paused') {
                        return redirect()->route('saas.errors.subscription-paused');
                    }

                    // Subscription expired — redirect to renewal page
                    return redirect()->route('admin.saas.subscription.index', ['salon_slug' => $slug])
                        ->with('error', 'Your subscription has expired. Please renew to continue.');
                }
            }

            // Set default parameter for URL generation again just in case (redundant but safe)
            \Illuminate\Support\Facades\URL::defaults(['salon_slug' => $slug]);

            // If user is logged in, verify they belong to this salon
            $user = $request->user();
            if ($user) {
                if (!$user->hasRole('super_admin') && $user->salon_id !== $salon->id) {
                    if ($user->salon && $user->salon->slug) {
                        return redirect()->route('dashboard', ['salon_slug' => $user->salon->slug])
                            ->with('error', 'You can only access your own salon.');
                    } else {
                        abort(403, 'Unauthorized access to this salon.');
                    }
                }
            }

            // current_salon is already bound above — skip rebinding.

            // OPTIMIZATION: Do NOT clear the entire permission cache on every request.
            // instead, we just ensure the user's relations are reloaded if needed.
            if ($user && $user->relationLoaded('roles')) {
                $user->unsetRelation('roles');
                $user->unsetRelation('permissions');
            }

            // Forget the salon_slug parameter
            $request->route()->forgetParameter('salon_slug');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("CheckSalonSlug Error: " . $e->getMessage(), [
                'slug' => $slug,
                'url'  => $request->fullUrl(),
            ]);
            // Do NOT pass through silently — redirect to login to avoid broken page renders
            // where current_salon is unbound and views throw BindingResolutionException.
            if (auth()->check() && auth()->user()->salon) {
                $userSalonSlug = auth()->user()->salon->slug;
                return redirect()->route('admin.saas.subscription.index', ['salon_slug' => $userSalonSlug])
                    ->with('error', 'Unable to load salon. Please try again.');
            }
            return redirect()->route('login')->with('error', 'Unable to load salon. Please log in again.');
        }

        // Always forget the parameter before passing to controller to match controller signatures
        if ($request->route() && $request->route()->hasParameter('salon_slug')) {
            $request->route()->forgetParameter('salon_slug');
        }

        return $next($request);
    }
}
