<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\Salon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailService
{
    /**
     * Send an email using a template.
     *
     * @param Salon $salon
     * @param string $recipientEmail
     * @param string $templateType
     * @param array $variables
     * @return bool
     */
    public function send(Salon $salon, string $recipientEmail, string $templateType, array $variables = [])
    {
        try {
            // Configure Mailer for this Salon
            $this->configureMailer($salon);

            // Fetch Template
            $template = EmailTemplate::forSalon($salon->id, $templateType);

            if (!$template || !$template->is_active) {
                Log::warning("Email template not found or inactive: {$templateType} for salon {$salon->id}");
                return false;
            }

            // Replace Variables
            $subject = $this->replaceVariables($template->subject, $variables);
            $content = $this->replaceVariables($template->content, $variables);

            // Send Email
            // We use Mail::html to send the content as HTML
            Mail::html($content, function ($message) use ($recipientEmail, $subject, $salon) {
                $message->to($recipientEmail)
                    ->subject($subject)
                    ->from($salon->mail_from_address ?? config('mail.from.address'), $salon->mail_from_name ?? config('mail.from.name'));
            });

            Log::info("Email sent successfully: {$templateType} to {$recipientEmail}");
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send email: {$templateType} to {$recipientEmail}. Error: " . $e->getMessage());
            return false;
        }
    }

    public function sendRaw(Salon $salon, string $recipientEmail, string $subject, string $content)
    {
        try {
            $this->configureMailer($salon);

            Mail::raw($content, function ($message) use ($recipientEmail, $subject, $salon) {
                $message->to($recipientEmail)
                    ->subject($subject)
                    ->from($salon->mail_from_address ?? config('mail.from.address'), $salon->mail_from_name ?? config('mail.from.name'));
            });

            Log::info("Raw email sent successfully to {$recipientEmail}");
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send raw email to {$recipientEmail}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    protected function configureMailer(Salon $salon)
    {
        // For high-scale multi-salon isolation, we ensure the mailer is re-resolved with current salon settings
        if ($salon->mail_driver && $salon->mail_driver !== 'log') {

            // Set the new configuration
            config([
                'mail.default' => $salon->mail_driver,
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host' => $salon->mail_host ?? '127.0.0.1',
                'mail.mailers.smtp.port' => $salon->mail_port ?? 587,
                'mail.mailers.smtp.encryption' => $salon->mail_encryption ?? 'tls',
                'mail.mailers.smtp.username' => $salon->mail_username,
                'mail.mailers.smtp.password' => $salon->mail_password,
                'mail.from.address' => $salon->mail_from_address ?? config('mail.from.address'),
                'mail.from.name' => $salon->mail_from_name ?? config('mail.from.name'),
            ]);

            // CRITICAL: Purge the MailManager singleton. 
            // This forces Laravel to re-instantiate the manager, which will then read the new config.
            app()->forgetInstance('mail.manager');

            // Also purge the specific mailer instance if it was resolved
            app()->forgetInstance('mailer');
            app()->forgetInstance(\Illuminate\Mail\Mailer::class);

            // Clear the facade resolution

            Mail::clearResolvedInstances();
        }
    }

    protected function replaceVariables($text, $variables)
    {
        foreach ($variables as $key => $value) {
            // Handle both {{variable}} and {{ variable }}
            $value = $value ?? '';
            $text = str_replace('{{' . $key . '}}', (string) $value, $text);
            $text = str_replace('{{ ' . $key . ' }}', (string) $value, $text);
        }
        return $text;
    }
}
