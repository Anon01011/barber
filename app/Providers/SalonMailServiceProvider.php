<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class SalonMailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configure mail settings based on current salon
        $this->app->booted(function () {
            try {
                if (app()->bound('current_salon')) {
                    $salon = app()->bound('current_salon') ? app('current_salon') : null;

                    if ($salon && $salon->mail_host) {
                        // Override mail configuration with salon-specific settings
                        Config::set('mail.mailers.smtp', [
                            'transport' => 'smtp',
                            'host' => $salon->mail_host,
                            'port' => $salon->mail_port ?? 587,
                            'encryption' => $salon->mail_encryption ?? 'tls',
                            'username' => $salon->mail_username,
                            'password' => $salon->mail_password,
                            'timeout' => null,
                        ]);

                        Config::set('mail.from', [
                            'address' => $salon->mail_from_address ?? $salon->email,
                            'name' => $salon->mail_from_name ?? $salon->name,
                        ]);

                        // Set default mailer
                        Config::set('mail.default', $salon->mail_driver ?? 'smtp');

                        // CRITICAL FIX: Purge and rebuild mail manager to apply new config
                        app()->forgetInstance('mail.manager');
                        app()->forgetInstance(\Illuminate\Mail\Mailer::class);
                    }
                }
            } catch (\Exception $e) {
                \Log::error("SalonMailServiceProvider Error (DB Lock?): " . $e->getMessage());
            }
        });
    }
}
