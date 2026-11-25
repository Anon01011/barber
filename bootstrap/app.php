<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            // Subscription & Plan Related
            'subscription' => \App\Http\Middleware\SubscriptionMiddleware::class, // ✅ Better version with trial handling
            'plan_feature' => \App\Http\Middleware\CheckPlanFeature::class,
            'plan.limits' => \App\Http\Middleware\CheckPlanLimits::class,

            // Branch & Salon Context
            'check_branch' => \App\Http\Middleware\CheckBranch::class,
            'check.salon.slug' => \App\Http\Middleware\CheckSalonSlug::class,
            'set.branch.context' => \App\Http\Middleware\SetBranchContext::class,

            // Features & Modules
            'feature' => \App\Http\Middleware\FeatureEnabled::class, // Global system features
            'module' => \App\Http\Middleware\CheckModuleEnabled::class,

            // Mail Configuration
            'configure.salon.mail' => \App\Http\Middleware\ConfigureSalonMail::class,

            // Spatie Permissions
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'check.salon.approval' => \App\Http\Middleware\CheckSalonApproval::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            // Security: block direct platform-admin write mutations on tenant routes (Vuln 2)
            'restrict.admin.writes' => \App\Http\Middleware\RestrictPlatformAdminWrites::class,
        ]);

        // Exclude Stripe webhook from CSRF protection
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
            'saas/stripe/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Your session has expired. Please refresh the page.',
                    'session_expired' => true,
                    'redirect' => route('login'),
                ], 419);
            }
            return redirect()->route('login')->with('warning', 'Your session has expired. Please sign in again.');
        });

        // Always serve custom Blade error views for web requests — even when APP_DEBUG=true.
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return null;
            }
            if (view()->exists('errors.404')) {
                return response()->view('errors.404', [], 404);
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            $status = $e->getStatusCode();
            if ($status === 419) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'message' => 'Your session has expired. Please refresh the page.',
                        'session_expired' => true,
                        'redirect' => route('login'),
                    ], 419);
                }
                return redirect()->route('login')->with('warning', 'Your session has expired. Please sign in again.');
            }

            if ($request->expectsJson() || $request->ajax()) {
                return null;
            }

            if (in_array($status, [403, 500, 503]) && view()->exists("errors.{$status}")) {
                return response()->view("errors.{$status}", ['exception' => $e], $status);
            }

            return null;
        });
    })->create();
