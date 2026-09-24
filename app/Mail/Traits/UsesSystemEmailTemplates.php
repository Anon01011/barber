<?php

namespace App\Mail\Traits;

use App\Models\EmailTemplate;
use Illuminate\Mail\Mailables\Content;

trait UsesSystemEmailTemplates
{
    /**
     * Get system template content or null.
     */
    protected function getSystemTemplateContent(string $type, array $variables): ?array
    {
        // Fetch system template (salon_id = null)
        $template = EmailTemplate::whereNull('salon_id')
            ->where('type', $type)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            return null;
        }

        // Use Blade::render to support Blade directives like @if, @component
        $renderedBody = \Illuminate\Support\Facades\Blade::render($template->content, $variables);
        $renderedSubject = \Illuminate\Support\Facades\Blade::render($template->subject, $variables);

        // Wrap in master layout
        $finalHtml = view('emails.layouts.system', [
            'content' => $renderedBody,
            'title' => $renderedSubject
        ])->render();

        return [
            'subject' => $renderedSubject,
            'content' => $finalHtml
        ];
    }

    /**
     * Replace variables in a string.
     */
    protected function replaceVariables(string $text, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $value = $value ?? '';
            $text = str_replace('{{' . $key . '}}', (string) $value, $text);
            $text = str_replace('{{ ' . $key . ' }}', (string) $value, $text); // Handle spaces
        }

        return $text;
    }

    /**
     * Get content for the email (system template or default view).
     */
    protected function getEmailContent(string $templateType, string $defaultView, array $variables): Content
    {
        $customTemplate = $this->getSystemTemplateContent($templateType, $variables);

        if ($customTemplate) {
            return new Content(
                htmlString: $customTemplate['content']
            );
        }

        // Fall back to default Markdown view
        return new Content(
            markdown: $defaultView,
            with: $variables
        );
    }

    /**
     * Get subject for the email (system template or default).
     */
    protected function getEmailSubject(string $templateType, string $defaultSubject, array $variables): string
    {
        $customTemplate = $this->getSystemTemplateContent($templateType, $variables);

        if ($customTemplate) {
            return $customTemplate['subject'];
        }

        return $defaultSubject;
    }
}
