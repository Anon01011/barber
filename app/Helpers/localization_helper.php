<?php

if (!function_exists('format_date')) {
    /**
     * Format a date using salon's configured date format
     * 
     * @param \Carbon\Carbon|string|null $date
     * @param string|null $format Override format (optional)
     * @return string
     */
    function format_date($date, $format = null): string
    {
        if (!$date) {
            return '';
        }

        try {
            $carbon = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);

            if ($format) {
                return $carbon->format($format);
            }

            $settings = app(\App\Services\SettingsService::class);
            $salon = app()->bound('current_salon') ? app('current_salon') : null;
            $salonId = $salon ? $salon->id : null;

            $dateFormat = $settings->get('date_format', 'Y-m-d', $salonId);

            return $carbon->format($dateFormat);
        } catch (\Exception $e) {
            return '';
        }
    }
}

if (!function_exists('format_time')) {
    /**
     * Format a time using salon's configured time format
     * 
     * @param \Carbon\Carbon|string|null $time
     * @param string|null $format Override format (optional)
     * @return string
     */
    function format_time($time, $format = null): string
    {
        if (!$time) {
            return '';
        }

        try {
            $carbon = $time instanceof \Carbon\Carbon ? $time : \Carbon\Carbon::parse($time);

            if ($format) {
                return $carbon->format($format);
            }

            $settings = app(\App\Services\SettingsService::class);
            $salon = app()->bound('current_salon') ? app('current_salon') : null;
            $salonId = $salon ? $salon->id : null;

            $timeFormat = $settings->get('time_format', '24h', $salonId);

            // Convert setting to PHP format
            $phpFormat = $timeFormat === '12h' ? 'h:i A' : 'H:i';

            return $carbon->format($phpFormat);
        } catch (\Exception $e) {
            return '';
        }
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Format a datetime using salon's configured date and time formats
     * 
     * @param \Carbon\Carbon|string|null $datetime
     * @param string|null $format Override format (optional)
     * @return string
     */
    function format_datetime($datetime, $format = null): string
    {
        if (!$datetime) {
            return '';
        }

        try {
            $carbon = $datetime instanceof \Carbon\Carbon ? $datetime : \Carbon\Carbon::parse($datetime);

            if ($format) {
                return $carbon->format($format);
            }

            $settings = app(\App\Services\SettingsService::class);
            $salon = app()->bound('current_salon') ? app('current_salon') : null;
            $salonId = $salon ? $salon->id : null;

            $dateFormat = $settings->get('date_format', 'Y-m-d', $salonId);
            $timeFormat = $settings->get('time_format', '24h', $salonId);

            // Convert time setting to PHP format
            $phpTimeFormat = $timeFormat === '12h' ? 'h:i A' : 'H:i';

            return $carbon->format($dateFormat . ' ' . $phpTimeFormat);
        } catch (\Exception $e) {
            return '';
        }
    }
}

if (!function_exists('salon_timezone')) {
    /**
     * Get salon's configured timezone
     * 
     * @return string
     */
    function salon_timezone(): string
    {
        try {
            $salon = null;
            try {
                $salon = app('current_salon');
            } catch (\Exception $e) {
                // Ignore binding error
            }

            // Fallback to auth user if current_salon binding missing or null
            if (!$salon && auth()->check()) {
                $salon = auth()->user()->salon;
            }

            if ($salon && $salon->timezone) {
                return $salon->timezone;
            }

            $settings = app(\App\Services\SettingsService::class);
            $salonId = $salon ? $salon->id : (auth()->check() ? auth()->user()->salon_id : null);

            $tz = $settings->get('timezone', config('app.timezone', 'UTC'), $salonId);

            return $tz;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("salon_timezone ERROR: " . $e->getMessage());
            return config('app.timezone', 'UTC');
        }
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format a number as currency
     * 
     * @param float|int $amount
     * @param string|null $currency Override currency code (optional)
     * @return string
     */
    function format_currency($amount, $currency = null): string
    {
        try {
            $salon = null;
            try {
                $salon = app('current_salon');
            } catch (\Exception $e) {
            }

            if (!$salon && auth()->check()) {
                $salon = auth()->user()->salon;
            }

            $settings = app(\App\Services\SettingsService::class);
            $salonId = $salon ? $salon->id : (auth()->check() ? auth()->user()->salon_id : null);

            $currencyCode = $currency ?? ($salon->currency ?? $settings->get('currency_code', 'USD', $salonId));

            // Map currency code to symbol if not provided in settings
            $currencySymbols = [
                'USD' => '$',
                'EUR' => '€',
                'GBP' => '£',
                'INR' => '₹',
                'AED' => 'د.إ',
                'SAR' => '﷼',
                'QAR' => 'QR',
                'CAD' => '$',
                'AUD' => '$',
            ];

            $currencySymbol = $settings->get('currency_symbol', null, $salonId) ?? ($currencySymbols[$currencyCode] ?? '$');

            // Format number with 2 decimal places
            $formattedNumber = number_format((float) $amount, 2);

            // Return with symbol
            return $currencySymbol . ' ' . $formattedNumber;
        } catch (\Exception $e) {
            return '$ ' . number_format((float) $amount, 2);
        }
    }
}
