<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();

                // Ensure salon relationship is loaded if it exists
                if (method_exists($user, 'salon')) {
                    $user->load('salon');
                }

                if ($user->hasRole('super_admin')) {
                    \Illuminate\Support\Facades\Log::info('RedirectIfAuthenticated: Redirecting super admin');
                    return redirect()->route('admin.super.dashboard');
                }

                // Use home.dashboard route which handles salon slug routing
                \Illuminate\Support\Facades\Log::info('RedirectIfAuthenticated: Redirecting to home.dashboard');
                return redirect()->route('home.dashboard');
            }
        }

        return $next($request);
    }
}
