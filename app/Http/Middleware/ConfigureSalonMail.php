<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ConfigureSalonMail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->bound('current_salon')) {
            $salon = app('current_salon');

            // ALWAYS override mail configuration to prevent fallback to system .env credentials
            if ($salon->mail_host && ($salon->mail_driver === 'smtp' || !$salon->mail_driver)) {
                // Override mail configuration with salon-specific settings
                Config::set('mail.mailers.smtp', [
                    'transport' => 'smtp',
                    'host' => $salon->mail_host,
                    'port' => $salon->mail_port ?? 587,
                    'encryption' => $salon->mail_encryption ?? 'tls',
                    'username' => $salon->mail_username,
                    'password' => $salon->mail_password,
                    'timeout' => null,
                    'local_domain' => env('MAIL_EHLO_DOMAIN'),
                ]);

                // Set default mailer
                Config::set('mail.default', 'smtp');
                Log::info("Mail configured for salon: {$salon->name} ({$salon->id}) using SMTP");
            } elseif ($salon->mail_driver === 'log') {
                Config::set('mail.default', 'log');
                Log::info("Mail configured for salon: {$salon->name} ({$salon->id}) using LOG driver");
            } else {
                // Salon has NO mail config or invalid config.
                // explicitly DISABLE mail sending by using 'array' driver.
                // This prevents falling back to the SaaS platform's .env SMTP credentials.
                Config::set('mail.default', 'array');
                Config::set('mail.mailers.smtp', ['transport' => 'array']);
                Log::info("Mail disabled for salon: {$salon->name} ({$salon->id}) - No SMTP configured");
            }

            // Always override 'from' address to maintain salon identity
            Config::set('mail.from', [
                'address' => $salon->mail_from_address ?? $salon->email ?? 'noreply@example.com',
                'name' => $salon->mail_from_name ?? $salon->name ?? 'Salon',
            ]);

            // CRITICAL: Purge mail manager to ensure new config is used
            app()->forgetInstance('mail.manager');
            app()->forgetInstance('mailer');
            app()->forgetInstance(\Illuminate\Mail\Mailer::class);

            try {
                \Illuminate\Support\Facades\Mail::clearResolvedInstances();
            } catch (\Exception $e) {
                // Ignore if method doesn't exist in older Laravel versions
            }
        }

        return $next($request);
    }
}
