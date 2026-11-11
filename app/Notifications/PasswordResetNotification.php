<?php

namespace App\Notifications;

use App\Models\User;
use App\Services\GlobalMailConfigService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * PasswordResetNotification
 *
 * Custom password-reset notification that:
 * 1. Always sends via the **SaaS platform SMTP** (from super-admin settings),
 *    never the salon's own SMTP — so any user (owner, staff, customer) can
 *    receive a reset link even if the salon hasn't configured mail.
 * 2. Sends a branded HTML email including the user's name, salon name, and role.
 * 3. Falls back gracefully if the platform SMTP is not yet configured.
 *
 * Security:
 * - Token is passed directly by Laravel's Password broker (time-limited, one-use).
 * - We never store or log the raw token.
 * - SaaS SMTP credentials are loaded from DB at send time — not from salon config.
 */
class PasswordResetNotification extends Notification
{
    use Queueable;

    /**
     * The raw password reset token from Laravel's Password broker.
     */
    protected string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * This notification is delivered only via email.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail message.
     *
     * Applies the SaaS platform SMTP settings before sending so the email
     * goes through the platform's configured mail provider regardless of
     * whether the user belongs to a salon with its own SMTP or not.
     */
    public function toMail(object $notifiable): MailMessage
    {
        /** @var User $user */
        $user = $notifiable;

        // ── Apply SaaS platform SMTP (not salon-specific) ─────────────────
        try {
            app(GlobalMailConfigService::class)->apply();
        } catch (\Throwable $e) {
            Log::warning('PasswordResetNotification: could not apply global mail config: ' . $e->getMessage());
        }

        // ── Gather user context ────────────────────────────────────────────
        $salon    = $user->salon; // null for super_admin
        $userRole = $user->getRoleNames()->first() ?? 'user';
        $appName  = app(\App\Services\SettingsService::class)->get('app_name', config('app.name'));

        // Build the reset URL with the email pre-filled.
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $user->getEmailForPasswordReset(),
        ], false));

        // ── Build from address ─────────────────────────────────────────────
        // Use platform from address (SaaS admin configured), not salon's.
        $fromAddress = config('mail.from.address', 'noreply@example.com');
        $fromName    = $appName;

        // ── Determine subject ──────────────────────────────────────────────
        $subject = $salon
            ? "Reset your {$salon->name} password"
            : "Reset your {$appName} password";

        // ── Build mail via MailMessage (view) ──────────────────────────────
        return (new MailMessage)
            ->subject($subject)
            ->from($fromAddress, $fromName)
            ->view('emails.auth.password-reset', [
                'resetUrl'  => $resetUrl,
                'userName'  => $user->name,
                'userEmail' => $user->email,
                'userRole'  => $userRole,
                'salon'     => $salon,
                'appName'   => $appName,
                'token'     => null, // Never pass raw token to view
            ]);
    }

    /**
     * Array representation (unused, but required by contract).
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}

