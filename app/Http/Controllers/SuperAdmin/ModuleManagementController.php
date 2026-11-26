<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class ModuleManagementController extends Controller
{
    protected SettingsService $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Get the authoritative list of all platform modules.
     */
    public function getAvailableModules(): array
    {
        return [
            'branches' => [
                'name' => 'Multi-Branch Management',
                'icon' => 'fa-code-branch',
                'description' => 'Manage multiple salon branches and locations',
            ],
            'inventory' => [
                'name' => 'Inventory Management',
                'icon' => 'fa-boxes',
                'description' => 'Track stock, suppliers, and product usage',
            ],
            'pos' => [
                'name' => 'Point of Sale (POS)',
                'icon' => 'fa-cash-register',
                'description' => 'Process sales, manage cart, and receipts',
            ],
            'appointments' => [
                'name' => 'Booking System',
                'icon' => 'fa-calendar-alt',
                'description' => 'Manage appointments, scheduling, and calendar',
            ],
            'customers' => [
                'name' => 'Customer Management',
                'icon' => 'fa-users',
                'description' => 'Manage customer profiles and history',
            ],
            'services' => [
                'name' => 'Service Management',
                'icon' => 'fa-cut',
                'description' => 'Manage services, categories, and pricing',
            ],
            'staff' => [
                'name' => 'Staff Management',
                'icon' => 'fa-user-tie',
                'description' => 'Manage employees, schedules, and commissions',
            ],
            'reports' => [
                'name' => 'Analytics & Reports',
                'icon' => 'fa-chart-line',
                'description' => 'View sales, appointments, and performance reports',
            ],
            'packages' => [
                'name' => 'Packages',
                'icon' => 'fa-gift',
                'description' => 'Create and manage service packages',
            ],
            'memberships' => [
                'name' => 'Memberships',
                'icon' => 'fa-id-card',
                'description' => 'Manage customer memberships and recurring plans',
            ],
            'commissions' => [
                'name' => 'Commission Management',
                'icon' => 'fa-percent',
                'description' => 'Track and manage staff commissions',
            ],
            'roles' => [
                'name' => 'Role Management',
                'icon' => 'fa-user-shield',
                'description' => 'Custom roles and permissions for salon staff',
            ],
            'marketing' => [
                'name' => 'Email Notifications',
                'icon' => 'fa-envelope',
                'description' => 'Transactional and automated email notifications',
            ],
            'sms' => [
                'name' => 'SMS Notifications',
                'icon' => 'fa-sms',
                'description' => 'Automated SMS alerts using various gateways',
            ],
            'whatsapp' => [
                'name' => 'WhatsApp Notifications',
                'icon' => 'fa-whatsapp',
                'description' => 'Direct WhatsApp customer alerts and updates',
            ],
            'ai' => [
                'name' => 'AI Insights & Automation',
                'icon' => 'fa-brain',
                'description' => 'Autonomous ML churn campaigns, revenue forecasting & AI copilot',
            ],
            'loyalty' => [
                'name' => 'Loyalty Program',
                'icon' => 'fa-star',
                'description' => 'Customer loyalty reward points and POS redemption',
            ],
            'feedback' => [
                'name' => 'Customer Feedback & Reviews',
                'icon' => 'fa-comments',
                'description' => 'Customer post-service star ratings and reviews',
            ],
            'data_backup' => [
                'name' => 'Data Backup & Safety',
                'icon' => 'fa-database',
                'description' => 'Automated snapshot replicas, selective salon data exports and restore',
            ],
        ];
    }

    public function index()
    {
        $availableModules = $this->getAvailableModules();

        // Get current enabled modules from settings (global)
        $enabledModules = $this->settingsService->get('enabled_modules', [], false, false);

        // Get module statuses from settings (global)
        $moduleStatuses = $this->settingsService->get('module_statuses', [], false, false);

        // Default missing module keys to enabled (matching ModuleHelper::isEnabled())
        foreach (array_keys($availableModules) as $key) {
            if (!array_key_exists($key, $enabledModules)) {
                $enabledModules[$key] = true;
            }
        }

        // Set default statuses if not set
        foreach (array_keys($availableModules) as $key) {
            if (!isset($moduleStatuses[$key])) {
                $moduleStatuses[$key] = 'stable'; // Default to stable for core features
            }
        }

        return view('super-admin.modules.index', compact('availableModules', 'enabledModules', 'moduleStatuses'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'enabled_modules' => 'nullable|array',
            'enabled_modules.*' => 'boolean',
            'module_statuses' => 'nullable|array',
            'module_statuses.*' => 'in:stable,beta,alpha,development',
        ]);

        $availableModules = $this->getAvailableModules();

        // Build the enabled modules array dynamically from all available modules
        $enabledModules = [];
        $submitted = $request->input('enabled_modules', []);
        foreach (array_keys($availableModules) as $module) {
            $enabledModules[$module] = !empty($submitted[$module]);
        }

        // Save enabled modules to global settings
        $this->settingsService->set('enabled_modules', $enabledModules, false, false);

        // Save module statuses to global settings
        if ($request->has('module_statuses')) {
            $this->settingsService->set('module_statuses', $request->input('module_statuses'), false, false);
        }

        // Clear cache across all layers
        $this->settingsService->clearCache('enabled_modules', false, null);
        $this->settingsService->clearCache('enabled_modules', null, null);
        $this->settingsService->clearCache('module_statuses', false, null);
        $this->settingsService->clearCache('module_statuses', null, null);
        \Illuminate\Support\Facades\Cache::forget('settings_enabled_modules');
        \Illuminate\Support\Facades\Cache::forget('settings_module_statuses');

        return redirect()->route('admin.modules.index')
            ->with('success', 'Module settings updated successfully.');
    }
}
