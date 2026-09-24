<?php

if (!function_exists('normalize_phone')) {
    function normalize_phone(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $normalized = preg_replace('/\s+/', '', trim($phone));

        return $normalized === '' ? null : $normalized;
    }
}

if (!function_exists('safe_http_url')) {
    function safe_http_url(?string $url): ?string
    {
        if ($url === null || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if (! preg_match('/^https?:\/\//i', $url)) {
            return null;
        }

        return filter_var($url, FILTER_VALIDATE_URL) ?: null;
    }
}
