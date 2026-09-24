<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Services\SettingsService;
use Illuminate\Support\Facades\View;
// use Illuminate\Support\Facades\URL; 
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingsService::class, function ($app) {
            return new SettingsService();
        });

        // Register Spatie Permission middleware
        $this->app->singleton('role', function ($app) {
            return new \Spatie\Permission\Middleware\RoleMiddleware();
        });

        $this->app->singleton('permission', function ($app) {
            return new \Spatie\Permission\Middleware\PermissionMiddleware();
        });

        $this->app->singleton('role_or_permission', function ($app) {
            return new \Spatie\Permission\Middleware\RoleOrPermissionMiddleware();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(SettingsService $settingsService): void
    {
        $this->app->singleton(\Spatie\Permission\PermissionRegistrar::class, function ($app) {
            return new \App\Permissions\TenantPermissionRegistrar($app['cache']);
        });

        Paginator::useBootstrapFive();

        // Register User Observer for staff_id auto-generation
        \App\Models\User::observe(\App\Observers\UserObserver::class);

        // Register Booking Observer for commission calculation
        \App\Models\Booking::observe(\App\Observers\BookingObserver::class);

        // Register PosSale Observer for commission calculation
        \App\Models\PosSale::observe(\App\Observers\PosSaleObserver::class);

        // Set default string length for MySQL
        Schema::defaultStringLength(191);

        // Override Mail Configuration from Database Settings
        /* Temporarily disabled to unblock bootstrap during DB lock
        try {
            // Only load if table exists to avoid issues during migration
            if (Schema::hasTable('settings')) {
                $settings = $settingsService->getAll();

                if (!empty($settings['mail_driver'])) {
                    $mailConfig = [
                        'mail.default' => $settings['mail_driver'],
                        'mail.from.address' => $settings['mail_from_address'] ?? config('mail.from.address'),
                        'mail.from.name' => $settings['mail_from_name'] ?? config('mail.from.name'),
                    ];

                    // Driver specific configurations
                    if ($settings['mail_driver'] === 'smtp') {
                        $mailConfig['mail.mailers.smtp.host'] = $settings['mail_host'] ?? config('mail.mailers.smtp.host');
                        $mailConfig['mail.mailers.smtp.port'] = isset($settings['mail_port']) ? (int) $settings['mail_port'] : config('mail.mailers.smtp.port');
                        $mailConfig['mail.mailers.smtp.encryption'] = $settings['mail_encryption'] ?? config('mail.mailers.smtp.encryption');
                        $mailConfig['mail.mailers.smtp.username'] = $settings['mail_username'] ?? config('mail.mailers.smtp.username');
                        $mailConfig['mail.mailers.smtp.password'] = $settings['mail_password'] ?? config('mail.mailers.smtp.password');
                    } elseif ($settings['mail_driver'] === 'mailgun') {
                        $mailConfig['services.mailgun.domain'] = $settings['mailgun_domain'] ?? config('services.mailgun.domain');
                        $mailConfig['services.mailgun.secret'] = $settings['mailgun_secret'] ?? config('services.mailgun.secret');
                        $mailConfig['services.mailgun.endpoint'] = $settings['mailgun_endpoint'] ?? config('services.mailgun.endpoint');
                    } elseif ($settings['mail_driver'] === 'ses') {
                        $mailConfig['services.ses.key'] = $settings['ses_key'] ?? config('services.ses.key');
                        $mailConfig['services.ses.secret'] = $settings['ses_secret'] ?? config('services.ses.secret');
                        $mailConfig['services.ses.region'] = $settings['ses_region'] ?? config('services.ses.region');
                    } elseif ($settings['mail_driver'] === 'postmark') {
                        $mailConfig['services.postmark.token'] = $settings['postmark_token'] ?? config('services.postmark.token');
                    } elseif ($settings['mail_driver'] === 'resend') {
                        $mailConfig['services.resend.key'] = $settings['resend_key'] ?? config('services.resend.key');
                    }

                    config($mailConfig);
                }
            }
        } catch (\Exception $e) {
            // specific handling if needed, or just ignore to fallback to env
            \Log::warning('Failed to load mail settings from database: ' . $e->getMessage());
        }
        */

        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Enable error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
        ini_set('log_errors', 1);

        // Set error handler
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            if (!(error_reporting() & $errno)) {
                return false;
            }

            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        });

        // Set exception handler
        set_exception_handler(function ($e) {
            \Log::error($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            if (app()->environment('production')) {
                return response()->view('errors.500', [], 500);
            }

            throw $e;
        });

        Schema::defaultStringLength(191);

        // Share settings with all views using a more efficient approach
        // Register Global Settings Composer for main layouts
        View::composer([
            'layouts.app',
            'layouts.dashboard',
            'layouts.guest',
            'dashboard',
            'welcome'
        ], \App\Http\View\Composers\GlobalSettingsComposer::class);

        // Register view composers for auth pages
        View::composer([
            'welcome',
            'auth.login',
            'auth.register',
            'saas.register',
        ], \App\Http\View\Composers\SettingsComposer::class);

        // Register Notification Composer for main layout
        View::composer('layouts.app', \App\Http\View\Composers\NotificationComposer::class);

        // Register Blade directive for customer data masking permission
        \Illuminate\Support\Facades\Blade::directive('canViewUnmaskedData', function () {
            return "<?php if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->can('view_unmasked_customer_data'))): ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('endcanViewUnmaskedData', function () {
            return "<?php endif; ?>";
        });
    }
}
