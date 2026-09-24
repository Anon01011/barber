<?php

if (!function_exists('currency_symbol')) {
    /**
     * Get salon's currency symbol from settings
     * 
     * @return string
     */
    function currency_symbol(): string
    {
        try {
            $settings = app(\App\Services\SettingsService::class);
            $salon = app()->bound('current_salon') ? app('current_salon') : null;
            $salonId = $salon ? $salon->id : null;

            $symbol = $settings->get('currency_symbol', '$', $salonId);

            \Log::info('Currency symbol retrieved:', [
                'salon_id' => $salonId,
                'symbol' => $symbol,
                'current_salon_bound' => app()->bound('current_salon')
            ]);

            return $symbol;
        } catch (\Exception $e) {
            \Log::error('Error in currency_symbol helper: ' . $e->getMessage());
            return '$'; // Default fallback
        }
    }
}

if (!function_exists('currency_code')) {
    /**
     * Get salon's currency code from settings
     * 
     * @return string
     */
    function currency_code(): string
    {
        try {
            $settings = app(\App\Services\SettingsService::class);
            $salon = app()->bound('current_salon') ? app('current_salon') : null;
            $salonId = $salon ? $salon->id : null;

            $code = $settings->get('currency_code', 'USD', $salonId);

            // If custom currency, get the custom code
            if ($code === 'CUSTOM') {
                return $settings->get('custom_currency_code', 'USD', $salonId);
            }

            return $code;
        } catch (\Exception $e) {
            return 'USD'; // Default fallback
        }
    }
}

// System-wide currency functions for SaaS admin

if (!function_exists('system_currency')) {
    /**
     * Get the system-wide currency code for SaaS admin
     * This is independent of salon context
     * 
     * @return string
     */
    function system_currency(): string
    {
        try {
            $settings = app(\App\Services\SettingsService::class);
            return $settings->get('currency', 'USD', 0);
        } catch (\Exception $e) {
            return 'USD'; // Default fallback
        }
    }
}

if (!function_exists('system_currency_symbol')) {
    /**
     * Get the currency symbol for system currency
     * 
     * @return string
     */
    function system_currency_symbol(): string
    {
        try {
            $settings = app(\App\Services\SettingsService::class);
            // Get currency_symbol from settings (SaaS admin configured)
            $symbol = $settings->get('currency_symbol', null, 0);

            // If currency_symbol is set, use it
            if ($symbol) {
                return $symbol;
            }

            // Fallback: try to map from currency code
            $currency = system_currency();
            $symbols = [
                'USD' => '$',
                'EUR' => '€',
                'GBP' => '£',
                'INR' => '₹',
                'AED' => 'د.إ',
                'QAR' => 'QR',
            ];

            return $symbols[$currency] ?? '$';
        } catch (\Exception $e) {
            return '$'; // Default fallback
        }
    }
}



if (!function_exists('system_format_currency')) {
    /**
     * Format amount with system currency symbol
     *
     * @param float|int $amount
     * @return string
     */
    function system_format_currency($amount): string
    {
        $symbol = system_currency_symbol();
        return $symbol . ' ' . number_format((float) $amount, 2);
    }
}
