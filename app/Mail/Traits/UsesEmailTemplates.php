<?php

namespace App\Mail\Traits;

use App\Models\EmailTemplate;
use App\Models\Booking;
use Illuminate\Mail\Mailables\Content;

trait UsesEmailTemplates
{
    /**
     * Check if a custom email template exists for this type.
     */
    protected function hasCustomTemplate(string $type): bool
    {
        if (!isset($this->booking)) {
            return false;
        }

        $salonId = $this->booking->salon_id ?? null;
        $template = EmailTemplate::forSalon($salonId, $type);

        return $template !== null;
    }

    /**
     * Get custom template content or null.
     */
    protected function getCustomTemplateContent(string $type): ?array
    {
        if (!isset($this->booking)) {
            return null;
        }

        $salonId = $this->booking->salon_id ?? null;
        $template = EmailTemplate::forSalon($salonId, $type);

        if (!$template) {
            return null;
        }

        $variables = $this->prepareTemplateVariables();
        $content = $template->render($variables);

        return [
            'subject' => $this->replaceVariables($template->subject, $variables),
            'content' => $content
        ];
    }

    /**
     * Prepare variables for template replacement.
     */
    protected function prepareTemplateVariables(): array
    {
        if (!isset($this->booking)) {
            return [];
        }

        $booking = $this->booking;
        $salon = $booking->salon;
        $customer = $booking->customer;
        $service = $booking->service;
        $staff = $booking->staff;

        return [
            'customer_name' => $customer->name ?? 'Valued Customer',
            'salon_name' => $salon->name ?? config('app.name'),
            'salon_phone' => $salon->phone ?? '',
            'salon_email' => $salon->email ?? '',
            'salon_address' => $salon->address ?? '',
            'service_name' => $service->name ?? 'Service',
            'booking_date' => $booking->start_time->format('F j, Y'),
            'booking_time' => $booking->start_time->format('g:i A'),
            'booking_status' => ucfirst($booking->status),
            'staff_name' => $staff->name ?? 'Our Team',
            'amount' => $booking->amount ? format_currency($booking->amount) : 'TBD',
            'old_date' => $booking->original_start_time ? $booking->original_start_time->format('F j, Y') : '',
            'old_time' => $booking->original_start_time ? $booking->original_start_time->format('g:i A') : '',
        ];
    }

    /**
     * Replace variables in a string.
     */
    protected function replaceVariables(string $text, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value, $text);
        }

        return $text;
    }

    /**
     * Get content for the email (custom template or default view).
     */
    protected function getEmailContent(string $templateType, string $defaultView): Content
    {
        $customTemplate = $this->getCustomTemplateContent($templateType);

        if ($customTemplate) {
            return new Content(
                html: $customTemplate['content']
            );
        }

        // Fall back to default Markdown view
        return new Content(
            markdown: $defaultView
        );
    }

    /**
     * Get subject for the email (custom template or default).
     */
    protected function getEmailSubject(string $templateType, string $defaultSubject): string
    {
        $customTemplate = $this->getCustomTemplateContent($templateType);

        if ($customTemplate) {
            return $customTemplate['subject'];
        }

        return $defaultSubject;
    }
}
