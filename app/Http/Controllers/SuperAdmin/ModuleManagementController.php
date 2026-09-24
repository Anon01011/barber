<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SettingsService;

class ModuleManagementController extends Controller
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->middleware(['auth', 'verified', 'role:super_admin']);
        $this->settingsService = $settingsService;
    }

    public function index()
    {
        $availableModules = [
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
        ];

        // Get current enabled modules from settings
        $enabledModules = $this->settingsService->get('enabled_modules', [], null, null);

        // Get module statuses from settings
        $moduleStatuses = $this->settingsService->get('module_statuses', [], null, null);

        // If empty, default all to enabled
        if (empty($enabledModules)) {
            foreach (array_keys($availableModules) as $key) {
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
        $validated = $request->validate([
            'enabled_modules' => 'nullable|array',
            'enabled_modules.*' => 'boolean',
            'module_statuses' => 'nullable|array',
            'module_statuses.*' => 'in:stable,beta,alpha,development',
        ]);

        // Get all module keys dynamically from available modules list
        $allModules = [
            'branches',
            'inventory',
            'pos',
            'appointments',
            'customers',
            'services',
            'staff',
            'reports',
            'packages',
            'memberships',
            'commissions',
            'roles',
            'marketing',
            'sms',
            'whatsapp',
            'ai'
        ];

        // Build the enabled modules array
        $enabledModules = [];
        foreach ($allModules as $module) {
            $enabledModules[$module] = $request->has("enabled_modules.{$module}")
                ? (bool) $request->input("enabled_modules.{$module}")
                : false;
        }

        // Save enabled modules to settings
        $this->settingsService->set('enabled_modules', $enabledModules, null, null);

        // Save module statuses to settings
        if ($request->has('module_statuses')) {
            $this->settingsService->set('module_statuses', $request->input('module_statuses'), null, null);
        }

        // Clear cache
        $this->settingsService->clearCache('enabled_modules', null, null);
        $this->settingsService->clearCache('module_statuses', null, null);

        return redirect()->route('admin.modules.index')
            ->with('success', 'Module settings updated successfully.');
    }
}
