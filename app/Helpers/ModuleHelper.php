<?php

namespace App\Helpers;

use App\Services\SettingsService;

class ModuleHelper
{
    /**
     * Check if a specific module is enabled
     *
     * @param string $moduleName
     * @return bool
     */
    public static function isEnabled(string $moduleName): bool
    {
        $settingsService = app(SettingsService::class);

        // IMPORTANT: Pass false (not null) to force global system settings
        // null would trigger auto-detection of salon_id in SettingsService
        $enabledModules = $settingsService->get('enabled_modules', [], false, false);

        // If module is not in the array, default to enabled
        // If module is in the array, return its boolean value
        return isset($enabledModules[$moduleName]) ? (bool) $enabledModules[$moduleName] : true;
    }

    /**
     * Check if branches module is enabled
     *
     * @return bool
     */
    public static function branchesEnabled(): bool
    {
        return self::isEnabled('branches');
    }

    /**
     * Check if inventory module is enabled
     *
     * @return bool
     */
    public static function inventoryEnabled(): bool
    {
        return self::isEnabled('inventory');
    }

    /**
     * Check if POS module is enabled
     *
     * @return bool
     */
    public static function posEnabled(): bool
    {
        return self::isEnabled('pos');
    }

    /**
     * Check if appointments module is enabled
     *
     * @return bool
     */
    public static function appointmentsEnabled(): bool
    {
        return self::isEnabled('appointments');
    }

    /**
     * Check if customers module is enabled
     *
     * @return bool
     */
    public static function customersEnabled(): bool
    {
        return self::isEnabled('customers');
    }

    /**
     * Check if services module is enabled
     *
     * @return bool
     */
    public static function servicesEnabled(): bool
    {
        return self::isEnabled('services');
    }

    /**
     * Check if staff module is enabled
     *
     * @return bool
     */
    public static function staffEnabled(): bool
    {
        return self::isEnabled('staff');
    }

    /**
     * Check if reports module is enabled
     *
     * @return bool
     */
    public static function reportsEnabled(): bool
    {
        return self::isEnabled('reports');
    }

    /**
     * Check if memberships module is enabled
     *
     * @return bool
     */
    public static function membershipsEnabled(): bool
    {
        return self::isEnabled('memberships');
    }

    /**
     * Check if packages module is enabled
     *
     * @return bool
     */
    public static function packagesEnabled(): bool
    {
        return self::isEnabled('packages');
    }

    /**
     * Check if email marketing module is enabled
     *
     * @return bool
     */
    public static function marketingEnabled(): bool
    {
        return self::isEnabled('marketing');
    }

    /**
     * Check if SMS notifications module is enabled
     *
     * @return bool
     */
    public static function smsEnabled(): bool
    {
        return self::isEnabled('sms');
    }

    /**
     * Check if WhatsApp notifications module is enabled
     *
     * @return bool
     */
    public static function whatsappEnabled(): bool
    {
        return self::isEnabled('whatsapp');
    }

    /**
     * Check if AI Insights & Automation module is enabled
     *
     * @return bool
     */
    public static function aiEnabled(): bool
    {
        return self::isEnabled('ai');
    }

    /**
     * Get all enabled modules
     *
     * @return array
     */
    public static function getEnabledModules(): array
    {
        $settingsService = app(SettingsService::class);
        // Force global system settings (false prevents auto-detection)
        return $settingsService->get('enabled_modules', [], false, false);
    }
}
