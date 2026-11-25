<?php

namespace App\Services;

use App\Helpers\ModuleHelper;
use App\Models\Salon;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Class ModuleEntitlementService
 *
 * Centralized, authoritative entitlement service for the Salon & Barber SaaS platform.
 * Enforces the core rule:
 * module_access = global_enabled AND plan_included AND subscription_eligible AND tenant_module_active AND dependencies_available AND user_authorized
 */
class ModuleEntitlementService
{
    /**
     * Map of module keys to plan feature identifiers
     */
    public const MODULE_FEATURE_MAP = [
        'branches' => ['Multi-Branch Management', 'Multi-Branch Support', 'multi_branch', 'branches'],
        'inventory' => ['Inventory Management', 'inventory', 'Products'],
        'pos' => ['Point of Sale (POS)', 'POS System', 'pos'],
        'appointments' => ['Booking System', 'appointments', 'Online Booking'],
        'customers' => ['Customer Management', 'customers'],
        'services' => ['Service Management', 'services'],
        'staff' => ['Staff Management', 'staff'],
        'reports' => ['Analytics & Reports', 'reports', 'Advanced Reports'],
        'packages' => ['Packages', 'packages'],
        'memberships' => ['Memberships', 'memberships'],
        'commissions' => ['Commission Management', 'commissions'],
        'roles' => ['Role Management', 'roles'],
        'marketing' => ['Email Notifications', 'marketing', 'Email Marketing'],
        'sms' => ['SMS Notifications', 'sms'],
        'whatsapp' => ['WhatsApp Notifications', 'whatsapp'],
        'ai' => ['AI Insights & Automation', 'ai', 'AI Copilot'],
        'loyalty' => ['Loyalty Program', 'loyalty_program', 'loyalty'],
        'feedback' => ['Customer Feedback & Reviews', 'feedback_reviews', 'feedback'],
    ];

    /**
     * Direct dependencies between modules.
     * Independent operations must continue if an optional module is disabled.
     */
    public const MODULE_DEPENDENCIES = [
        'branches' => [],
        'inventory' => [],
        'pos' => ['services'],
        'appointments' => ['services', 'staff', 'customers'],
        'customers' => [],
        'services' => [],
        'staff' => [],
        'reports' => [],
        'packages' => ['services'],
        'memberships' => ['customers'],
        'commissions' => ['staff'],
        'roles' => [],
        'marketing' => ['customers'],
        'sms' => ['customers'],
        'whatsapp' => ['customers'],
        'ai' => [],
        'loyalty' => ['customers', 'pos'],
        'feedback' => ['appointments'],
    ];

    /**
     * Map a plan feature string to its canonical module key.
     */
    public function getModuleKeyForFeature(string $feature): ?string
    {
        $normalizedFeature = trim($feature);

        foreach (self::MODULE_FEATURE_MAP as $moduleKey => $features) {
            foreach ($features as $f) {
                if (strcasecmp($f, $normalizedFeature) === 0) {
                    return $moduleKey;
                }
            }
        }

        // Direct key lookup
        $lower = strtolower($normalizedFeature);
        if (array_key_exists($lower, self::MODULE_FEATURE_MAP)) {
            return $lower;
        }

        return null;
    }

    /**
     * Check if a module is globally enabled by the SaaS Super Admin.
     */
    public function isGloballyEnabled(string $module): bool
    {
        return ModuleHelper::isEnabled($module);
    }

    /**
     * Check if the salon's current subscription plan includes the module.
     * Note: Active trial grants plan inclusion for all modules.
     */
    public function isPlanIncluded(?Salon $salon, string $module): bool
    {
        if (!$salon) {
            return false;
        }

        // Active trial grants entitlement to all features in the catalog
        if (method_exists($salon, 'isOnTrial') && $salon->isOnTrial()) {
            return true;
        }

        $subscription = $salon->currentSubscription();
        if (!$subscription || !$subscription->plan) {
            return false;
        }

        $plan = $subscription->plan;
        if (method_exists($plan, 'isEmptyFeatures') && $plan->isEmptyFeatures()) {
            return false;
        }

        $featureNames = self::MODULE_FEATURE_MAP[$module] ?? [$module];
        foreach ($featureNames as $featureName) {
            if ($plan->hasFeature($featureName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the salon's subscription is eligible to consume services.
     */
    public function isSubscriptionEligible(?Salon $salon): bool
    {
        if (!$salon) {
            return false;
        }

        // Active trial is always eligible
        if (method_exists($salon, 'isOnTrial') && $salon->isOnTrial()) {
            return true;
        }

        $subscription = $salon->currentSubscription();
        if (!$subscription) {
            return false;
        }

        if ($subscription->ends_at && $subscription->ends_at->isPast()) {
            return false;
        }

        $status = strtolower($subscription->status ?? '');
        return in_array($status, ['active', 'trialing', 'past_due']);
    }

    /**
     * Check if tenant-level module setting is active (if supported).
     */
    public function isTenantModuleActive(?Salon $salon, string $module): bool
    {
        if (!$salon) {
            return false;
        }

        // Check salon settings if a specific toggle exists for this module
        $settingsService = app(SettingsService::class);
        $settings = $settingsService->getAll($salon->id);

        if ($module === 'loyalty' && array_key_exists('loyalty_enabled', $settings)) {
            $val = $settings['loyalty_enabled'];
            return ($val === '' || $val === null) ? true : filter_var($val, FILTER_VALIDATE_BOOLEAN);
        }

        if ($module === 'feedback' && array_key_exists('feedback_enabled', $settings)) {
            $val = $settings['feedback_enabled'];
            return ($val === '' || $val === null) ? true : filter_var($val, FILTER_VALIDATE_BOOLEAN);
        }

        if ($module === 'sms' && isset($settings['enable_sms'])) {
            return filter_var($settings['enable_sms'], FILTER_VALIDATE_BOOLEAN);
        }

        if ($module === 'whatsapp' && isset($settings['enable_whatsapp'])) {
            return filter_var($settings['enable_whatsapp'], FILTER_VALIDATE_BOOLEAN);
        }

        return true;
    }

    /**
     * Check if all required parent dependencies of a module are available (recursively).
     */
    public function areDependenciesAvailable(?Salon $salon, string $module, array $visited = []): bool
    {
        if (in_array($module, $visited, true)) {
            return true;
        }
        $visited[] = $module;

        $deps = self::MODULE_DEPENDENCIES[$module] ?? [];

        foreach ($deps as $dep) {
            // Each required dependency must be globally enabled, plan included, and have its own parents available
            if (!$this->isGloballyEnabled($dep)) {
                return false;
            }

            if ($salon && !$this->isPlanIncluded($salon, $dep)) {
                return false;
            }

            if (!$this->areDependenciesAvailable($salon, $dep, $visited)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a specific user has the required permission/role for a module.
     */
    public function isUserAuthorized(?User $user, string $module): bool
    {
        if (!$user) {
            return false;
        }

        // Super Admin and Salon Admin bypass granular module permission checks
        if ($user->hasAnyRole(['super_admin', 'salon_admin'])) {
            return true;
        }

        $modulePermissions = [
            'branches'     => 'branches.view',
            'inventory'    => 'inventory.view',
            'pos'          => 'pos.view',
            'appointments' => 'bookings.view',
            'customers'    => 'customers.view',
            'services'     => 'services.view',
            'staff'        => 'staff.view',
            'reports'      => 'reports.view',
            'packages'     => 'packages.view',
            'memberships'  => 'memberships.view',
            'commissions'  => 'commissions.view',
            'roles'        => 'roles.view',
            'feedback'     => 'marketing.manage_reviews',
        ];

        if (isset($modulePermissions[$module])) {
            return $user->can($modulePermissions[$module]);
        }

        return true;
    }

    /**
     * Check if a salon is entitled to a module by global, subscription, plan, and dependency rules
     * (excluding the tenant-level on/off toggle, used by Settings to allow enabling/disabling).
     */
    public function isEntitledByPlan(?Salon $salon, string $module): bool
    {
        if (!$this->isGloballyEnabled($module)) {
            return false;
        }

        if (!$salon) {
            return true;
        }

        if (!$this->isSubscriptionEligible($salon)) {
            return false;
        }

        if (!$this->isPlanIncluded($salon, $module)) {
            return false;
        }

        return $this->areDependenciesAvailable($salon, $module);
    }

    /**
     * Authoritative check whether a salon can access a given module.
     * Enforces: global_enabled AND subscription_eligible AND plan_included AND dependencies_available AND tenant_active
     */
    public function canAccess(?Salon $salon, string $module, ?User $user = null): bool
    {
        // 1. Must be globally enabled by Super Admin (Hard Boundary)
        if (!$this->isGloballyEnabled($module)) {
            return false;
        }

        // If no salon context is provided (e.g. system route), rely on global enable
        if (!$salon) {
            return true;
        }

        // 2. Subscription eligibility
        if (!$this->isSubscriptionEligible($salon)) {
            return false;
        }

        // 3. Plan feature inclusion
        if (!$this->isPlanIncluded($salon, $module)) {
            return false;
        }

        // 4. Dependencies availability
        if (!$this->areDependenciesAvailable($salon, $module)) {
            return false;
        }

        // 5. Tenant level activation
        if (!$this->isTenantModuleActive($salon, $module)) {
            return false;
        }

        // 6. User authorization
        if ($user && !$this->isUserAuthorized($user, $module)) {
            return false;
        }

        return true;
    }

    /**
     * Authoritative check whether a salon can use a specific named plan feature.
     * Guarantees that if the parent module is disabled globally, access is denied
     * even if included in the plan or on trial.
     */
    public function canUseFeature(?Salon $salon, string $feature): bool
    {
        if (!$salon) {
            return false;
        }

        // Check if feature maps to a known module
        $moduleKey = $this->getModuleKeyForFeature($feature);

        if ($moduleKey) {
            // HARD CHECK: If module is disabled globally, NEVER permit access
            if (!$this->isGloballyEnabled($moduleKey)) {
                return false;
            }

            // Verify subscription eligibility
            if (!$this->isSubscriptionEligible($salon)) {
                return false;
            }

            // Verify plan inclusion
            if (!$this->isPlanIncluded($salon, $moduleKey)) {
                return false;
            }

            // Verify required dependencies
            if (!$this->areDependenciesAvailable($salon, $moduleKey)) {
                return false;
            }

            // Verify tenant-level activation
            if (!$this->isTenantModuleActive($salon, $moduleKey)) {
                return false;
            }

            return true;
        }

        // Unmapped feature fallback
        if (method_exists($salon, 'isOnTrial') && $salon->isOnTrial()) {
            return true;
        }

        $subscription = $salon->currentSubscription();
        if (!$subscription || !$subscription->plan) {
            return false;
        }

        return $subscription->plan->hasFeature($feature);
    }
}
