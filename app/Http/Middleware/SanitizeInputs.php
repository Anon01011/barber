<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInputs
{
    /**
     * Fields that should NOT be sanitized (e.g. passwords).
     *
     * @var string[]
     */
    protected array $except = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'mail_password',
        '_token',
    ];

    /**
     * Fields that allow safe HTML formatting (rich text/email templates).
     *
     * @var string[]
     */
    protected array $htmlFields = [
        'email_template_content',
        'template_content',
        'content_html',
        'body_html',
        'custom_html',
        'terms_and_conditions',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Sanitize all query string parameters
        if ($request->query->count() > 0) {
            $cleanedQuery = $this->sanitizeArray($request->query->all());
            $request->query->replace($cleanedQuery);
        }

        // 2. Sanitize all request body parameters (POST/PUT/PATCH/DELETE)
        if ($request->request->count() > 0) {
            $cleanedBody = $this->sanitizeArray($request->request->all());
            $request->request->replace($cleanedBody);
        }

        // 3. Sanitize JSON payload if present
        if ($request->isJson() && is_array($request->json()->all())) {
            $cleanedJson = $this->sanitizeArray($request->json()->all());
            $request->json()->replace($cleanedJson);
        }

        return $next($request);
    }

    /**
     * Recursively sanitize array values.
     */
    protected function sanitizeArray(array $data, string $parentKey = ''): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            $fullKey = $parentKey ? "{$parentKey}.{$key}" : (string)$key;

            if (in_array($key, $this->except, true) || in_array($fullKey, $this->except, true)) {
                $sanitized[$key] = $value;
                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeArray($value, $fullKey);
            } elseif (is_string($value)) {
                if (in_array($key, $this->htmlFields, true) || in_array($fullKey, $this->htmlFields, true)) {
                    $sanitized[$key] = $this->cleanHtml($value);
                } else {
                    $sanitized[$key] = $this->cleanString($value);
                }
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Sanitize a standard string value by stripping dangerous scripts and tags.
     */
    protected function cleanString(string $value): string
    {
        // Strip null bytes
        $value = str_replace(chr(0), '', $value);

        // Remove dangerous URI schemes like javascript:, data:text/html, vbscript:
        $value = preg_replace('/javascript\s*:/i', '', $value);
        $value = preg_replace('/vbscript\s*:/i', '', $value);
        $value = preg_replace('/data\s*:\s*text\/html/i', '', $value);

        // Strip dangerous inline event handlers (onerror=, onload=, onclick=, etc.)
        $value = preg_replace('/on[a-z]+\s*=\s*(["\'][^"\']*["\']|[^\s>]+)/i', '', $value);

        // Strip HTML script, iframe, embed, object, frame tags completely
        $value = preg_replace('/<\s*(script|iframe|embed|object|applet|meta|link|style)[^>]*>.*?<\s*\/\s*\1\s*>/is', '', $value);
        $value = preg_replace('/<\s*(script|iframe|embed|object|applet|meta|link|style)[^>]*\/?>/is', '', $value);

        // Strip remaining HTML tags for standard input fields to prevent stored XSS
        $value = strip_tags($value);

        return $value;
    }

    /**
     * Clean HTML content to remove XSS vectors while preserving safe markup.
     */
    protected function cleanHtml(string $html): string
    {
        // Strip null bytes
        $html = str_replace(chr(0), '', $html);

        // Strip dangerous tags completely
        $html = preg_replace('/<\s*(script|iframe|embed|object|applet|meta|link|form|svg|base)[^>]*>.*?<\s*\/\s*\1\s*>/is', '', $html);
        $html = preg_replace('/<\s*(script|iframe|embed|object|applet|meta|link|form|svg|base)[^>]*\/?>/is', '', $html);

        // Strip dangerous attributes (all on* event handlers, javascript: links)
        $html = preg_replace('/on[a-z]+\s*=\s*(["\'][^"\']*["\']|[^\s>]+)/i', '', $html);
        $html = preg_replace('/href\s*=\s*["\']\s*javascript:[^"\']*["\']/i', 'href="#"', $html);
        $html = preg_replace('/src\s*=\s*["\']\s*javascript:[^"\']*["\']/i', 'src=""', $html);

        return $html;
    }
}
