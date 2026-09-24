<?php

namespace App\Helpers;

use App\Services\SettingsService;
use Illuminate\Support\Facades\Auth;

class CustomerDataHelper
{
    /**
     * Check if data should be masked for the current user
     *
     * @return bool
     */
    public static function shouldMaskData(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return true;
        }

        // Salon admins always see unmasked data
        if ($user->hasRole('salon_admin')) {
            return false;
        }

        // Check if masking is enabled in settings
        $settingsService = app(SettingsService::class);
        $salonId = $user->salon_id;

        $maskingEnabled = $settingsService->get('customer_data_masking_enabled', false, $salonId);

        // Convert to boolean properly (handles string 'false', '0', empty array, etc.)
        $maskingEnabled = filter_var($maskingEnabled, FILTER_VALIDATE_BOOLEAN);

        // If masking is not enabled, don't mask
        if (!$maskingEnabled) {
            return false;
        }

        // ONLY check if user is in the allowed users list
        // This ensures masking works for ALL users (including managers) unless explicitly allowed
        $allowedUsersSetting = $settingsService->get('unmasked_data_users', '[]', $salonId);
        $allowedUsers = is_array($allowedUsersSetting) ? $allowedUsersSetting : (json_decode($allowedUsersSetting, true) ?: []);

        if (in_array($user->id, $allowedUsers)) {
            return false;
        }

        // User should see masked data
        return true;
    }

    /**
     * Get masked or unmasked phone based on settings and permissions
     * 
     * @param object $customer
     * @return string
     */
    public static function getMaskedPhone($customer): string
    {
        if (!$customer || !isset($customer->phone)) {
            return 'N/A';
        }

        // Check if phone masking is enabled
        $settingsService = app(SettingsService::class);
        $salonId = Auth::user()->salon_id ?? null;

        $phonesMaskingEnabled = $settingsService->get('mask_customer_phone', false, $salonId);

        // Convert to boolean properly
        $phonesMaskingEnabled = filter_var($phonesMaskingEnabled, FILTER_VALIDATE_BOOLEAN);

        // If phone masking is not enabled, return original
        if (!$phonesMaskingEnabled) {
            return $customer->phone ?? 'N/A';
        }

        // Check if we should mask data for this user
        if (self::shouldMaskData()) {
            return self::maskPhone($customer->phone);
        }

        return $customer->phone ?? 'N/A';
    }

    /**
     * Get masked or unmasked email based on settings and permissions
     * 
     * @param object $customer
     * @return string
     */
    public static function getMaskedEmail($customer): string
    {
        if (!$customer || !isset($customer->email) || $customer->email === null) {
            return 'N/A';
        }

        // Check if email masking is enabled
        $settingsService = app(SettingsService::class);
        $salonId = Auth::user()->salon_id ?? null;

        $emailMaskingEnabled = $settingsService->get('mask_customer_email', false, $salonId);

        // Convert to boolean properly
        $emailMaskingEnabled = filter_var($emailMaskingEnabled, FILTER_VALIDATE_BOOLEAN);

        // If email masking is not enabled, return original
        if (!$emailMaskingEnabled) {
            return $customer->email ?? 'N/A';
        }

        // Check if we should mask data for this user
        if (self::shouldMaskData()) {
            return self::maskEmail($customer->email);
        }

        return $customer->email ?? 'N/A';
    }

    /**
     * Mask phone number
     * 
     * @param string $phone
     * @return string
     */
    public static function maskPhone($phone)
    {
        if (empty($phone)) {
            return '';
        }

        // Keep last 4 digits
        $length = strlen($phone);
        if ($length <= 4) {
            return $phone;
        }

        $visible = substr($phone, -4);
        $masked = str_repeat('*', $length - 4);

        return $masked . $visible;
    }

    /**
     * Mask email address
     * 
     * @param string $email
     * @return string
     */
    public static function maskEmail($email)
    {
        if (empty($email) || !str_contains($email, '@')) {
            return $email;
        }

        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1];

        $nameLength = strlen($name);
        if ($nameLength <= 2) {
            return $name[0] . '***@' . $domain;
        }

        $visibleStart = substr($name, 0, 1);
        $visibleEnd = substr($name, -1);
        $masked = str_repeat('*', $nameLength - 2);

        return $visibleStart . $masked . $visibleEnd . '@' . $domain;
    }
    /**
     * Check if staff data should be masked for the current user
     *
     * @return bool
     */
    public static function shouldMaskStaffData(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return true;
        }

        // Salon admins and Managers always see unmasked staff data
        // (Managers need to contact staff)
        if ($user->hasRole('salon_admin') || $user->hasRole('manager')) {
            return false;
        }

        // Employees should not see other staff's personal contact info by default
        // unless explicitly allowed (reuse the same setting or a new one?)
        // For now, let's assume strict masking for employees viewing other staff.

        // If the user is viewing their OWN profile, they should see it unmasked.
        // But this method is static and doesn't know which profile is being viewed.
        // So we'll handle that in the getter or view.

        return true;
    }

    /**
     * Get masked or unmasked staff phone
     * 
     * @param object $staff
     * @return string
     */
    public static function getMaskedStaffPhone($staff): string
    {
        if (!$staff || !isset($staff->phone)) {
            return 'N/A';
        }

        // If user is viewing their own data, don't mask
        if (Auth::id() === $staff->id) {
            return $staff->phone;
        }

        if (self::shouldMaskStaffData()) {
            return self::maskPhone($staff->phone);
        }

        return $staff->phone;
    }

    /**
     * Get masked or unmasked staff email
     * 
     * @param object $staff
     * @return string
     */
    public static function getMaskedStaffEmail($staff): string
    {
        if (!$staff || !isset($staff->email)) {
            return 'N/A';
        }

        // If user is viewing their own data, don't mask
        if (Auth::id() === $staff->id) {
            return $staff->email;
        }

        if (self::shouldMaskStaffData()) {
            return self::maskEmail($staff->email);
        }

        return $staff->email;
    }
}
