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
        ]);

        // Exclude Stripe webhook from CSRF protection
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
            'saas/stripe/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Use Laravel's default exception handling
    })->create();
