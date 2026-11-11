<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

/**
 * GlobalMailConfigService
 *
 * Applies the super-admin configured SMTP settings (stored in the `settings` table)
 * to Laravel's runtime mail configuration. This is used for system-level emails
 * (e.g., password reset) that are NOT tied to a specific salon context.
 *
 * Usage: app(GlobalMailConfigService::class)->apply();
 */
class GlobalMailConfigService
{
    /**
     * Apply global mail settings from the database to Laravel's runtime config.
     *
     * @return bool  true if SMTP settings were applied, false if not configured.
     */
    public function apply(): bool
    {
        try {
            if (!Schema::hasTable('settings')) {
                return false;
            }

            // Load only mail-related settings — avoid loading all settings for performance.
            $rows = DB::table('settings')
                ->where('key', 'like', 'mail_%')
                ->get(['key', 'value'])
                ->pluck('value', 'key')
                ->toArray();

            if (empty($rows)) {
                Log::debug('GlobalMailConfigService: No mail settings found in DB, using .env defaults.');
                return false;
            }

            $driver = $rows['mail_driver'] ?? null;

            if (!$driver || in_array($driver, ['log', 'array', 'null'])) {
                // Intentionally set to log/array — respect that choice.
                Config::set('mail.default', $driver ?? config('mail.default'));
                Log::debug("GlobalMailConfigService: Mail driver set to [{$driver}] from DB settings.");
                return false;
            }

            // Apply from address
            Config::set('mail.from', [
                'address' => $rows['mail_from_address'] ?? config('mail.from.address'),
                'name'    => $rows['mail_from_name']    ?? config('mail.from.name'),
            ]);

            if ($driver === 'smtp') {
                Config::set('mail.default', 'smtp');
                Config::set('mail.mailers.smtp', [
                    'transport'  => 'smtp',
                    'host'       => $rows['mail_host']       ?? config('mail.mailers.smtp.host'),
                    'port'       => isset($rows['mail_port']) ? (int) $rows['mail_port'] : config('mail.mailers.smtp.port'),
                    'encryption' => $rows['mail_encryption'] ?? config('mail.mailers.smtp.encryption'),
                    'username'   => $rows['mail_username']   ?? config('mail.mailers.smtp.username'),
                    'password'   => $rows['mail_password']   ?? config('mail.mailers.smtp.password'),
                    'timeout'    => null,
                ]);
                Log::info('GlobalMailConfigService: SMTP configured from DB settings.');
            } elseif ($driver === 'mailgun') {
                Config::set('mail.default', 'mailgun');
                Config::set('services.mailgun.domain',   $rows['mailgun_domain']   ?? config('services.mailgun.domain'));
                Config::set('services.mailgun.secret',   $rows['mailgun_secret']   ?? config('services.mailgun.secret'));
                Config::set('services.mailgun.endpoint', $rows['mailgun_endpoint'] ?? config('services.mailgun.endpoint'));
                Log::info('GlobalMailConfigService: Mailgun configured from DB settings.');
            } elseif ($driver === 'ses') {
                Config::set('mail.default', 'ses');
                Config::set('services.ses.key',    $rows['ses_key']    ?? config('services.ses.key'));
                Config::set('services.ses.secret', $rows['ses_secret'] ?? config('services.ses.secret'));
                Config::set('services.ses.region', $rows['ses_region'] ?? config('services.ses.region'));
                Log::info('GlobalMailConfigService: SES configured from DB settings.');
            } elseif ($driver === 'postmark') {
                Config::set('mail.default', 'postmark');
                Config::set('services.postmark.token', $rows['postmark_token'] ?? config('services.postmark.token'));
                Log::info('GlobalMailConfigService: Postmark configured from DB settings.');
            } elseif ($driver === 'resend') {
                Config::set('mail.default', 'resend');
                Config::set('services.resend.key', $rows['resend_key'] ?? config('services.resend.key'));
                Log::info('GlobalMailConfigService: Resend configured from DB settings.');
            } else {
                Config::set('mail.default', $driver);
                Log::info("GlobalMailConfigService: Mail driver [{$driver}] configured from DB settings.");
            }

            // Purge any cached mailer instances so the new config takes effect immediately.
            app()->forgetInstance('mail.manager');
            app()->forgetInstance('mailer');
            app()->forgetInstance(\Illuminate\Mail\Mailer::class);

            try {
                Mail::clearResolvedInstances();
            } catch (\Throwable $e) {
                // Ignore — older versions may not have this method.
            }

            return true;

        } catch (\Throwable $e) {
            Log::warning('GlobalMailConfigService: Failed to apply DB mail settings: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if real (non-log/array) mail settings are configured in the DB.
     */
    public function isConfigured(): bool
    {
        try {
            if (!Schema::hasTable('settings')) {
                return false;
            }
            $driver = DB::table('settings')->where('key', 'mail_driver')->value('value');
            return $driver && !in_array($driver, ['log', 'array', 'null']);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Return the currently active driver name from DB, or null if not set.
     */
    public function getDriverFromDb(): ?string
    {
        try {
            if (!Schema::hasTable('settings')) {
                return null;
            }
            return DB::table('settings')->where('key', 'mail_driver')->value('value');
        } catch (\Throwable $e) {
            return null;
        }
    }
}

