<?php

namespace App\Services;

use App\Models\Plan;

class PermissionService
{
    /**
     * Get all available permissions grouped by module.
     *
     * @return array
     */
    public static function getAllPermissions(): array
    {
        return [
            'system' => [
                'system.view_dashboard',
                'system.manage_settings',
                'system.manage_roles',
                'system.view_logs',
                'system.manage_backups',
            ],
            'appointments' => [
                'bookings.view',
                'bookings.create',
                'bookings.edit',
                'bookings.delete',
                'bookings.view_stats',
                'bookings.manage_guest',
                'bookings.manage_calendar',
                'bookings.send_reminders',
                'bookings.manage_status',
                'bookings.view_analytics',
                'appointments.view',
                'appointments.create',
                'appointments.edit',
                'appointments.delete',
                'appointments.manage_calendar',
                'appointments.send_reminders',
                'appointments.manage_status',
                'appointments.view_analytics',
            ],
            'salon' => [
                'salon.view',
                'salon.create',
                'salon.edit',
                'salon.delete',
                'salon.manage_settings',
                'salon.manage_branches',
                'salon.manage_working_hours',
                'salon.manage_holidays',
                'salon.manage_email_templates',
                'salon.manage_email_settings',
            ],
            'staff' => [
                'staff.view',
                'staff.create',
                'staff.edit',
                'staff.delete',
                'staff.manage_schedule',
                'staff.view_performance',
                'staff.manage_commission',
                'staff.manage_attendance',
            ],
            'services' => [
                'services.view',
                'services.create',
                'services.edit',
                'services.delete',
                'services.manage_categories',
                'services.manage_pricing',
                'services.manage_duration',
                'services.assign_staff',
                'memberships.view',
                'memberships.create',
                'memberships.edit',
                'memberships.delete',
                'packages.view',
                'packages.create',
                'packages.edit',
                'packages.delete',
            ],
            'customers' => [
                'customers.view',
                'customers.create',
                'customers.edit',
                'customers.delete',
                'customers.view_history',
                'customers.manage_preferences',
                'customers.send_notifications',
                'customers.manage_loyalty',
                'customers.import',
                'customers.export',
            ],
            'pos' => [
                'pos.view',
                'pos.create_sale',
                'pos.edit_sale',
                'pos.delete_sale',
                'pos.process_refunds',
                'pos.view_reports',
                'pos.manage_payment',
                'pos.manage_discounts',
            ],
            'inventory' => [
                'inventory.view',
                'inventory.create',
                'inventory.edit',
                'inventory.manage_stock',
                'inventory.manage_suppliers',
                'inventory.view_reports',
                'inventory.manage_categories',
                'inventory.categories.view',
                'inventory.categories.create',
                'inventory.categories.edit',
                'inventory.categories.delete',
                'inventory.delete',
                'products.import',
                'products.export',
            ],
            'finance' => [
                'finance.view',
                'finance.manage_transactions',
                'finance.view_reports',
                'finance.manage_taxes',
                'finance.manage_discounts',
                'finance.manage_payments',
                'finance.view_analytics',
                'finance.export_reports',
            ],
            'marketing' => [
                'marketing.view',
                'marketing.create_campaigns',
                'marketing.send_notifications',
                'marketing.manage_promotions',
                'marketing.view_analytics',
                'marketing.manage_loyalty',
                'marketing.manage_reviews',
                'marketing.export_data',
            ],
            'reports' => [
                'reports.view',
                'reports.sales',
                'reports.appointments',
                'reports.staff',
                'reports.customers',
            ],
            'commissions' => [
                'commissions.view',
                'commissions.manage',
                'commissions.view_reports',
            ],
        ];
    }

    /**
     * Get flat list of all permissions.
     *
     * @return array
     */
    public static function getFlatPermissions(): array
    {
        $flat = [];
        foreach (self::getAllPermissions() as $module => $permissions) {
            $flat = array_merge($flat, $permissions);
        }
        return array_unique($flat);
    }

    /**
     * Get available permissions based on plan features and enabled modules.
     *
     * @param Plan|null $plan
     * @param int|null $salonId
     * @return array Array of permission names
     */
    public static function getAvailablePermissions(?Plan $plan = null, ?int $salonId = null): array
    {
        // Get enabled modules from SettingsService
        $settingsService = app(\App\Services\SettingsService::class);
        $enabledModules = $settingsService->get('enabled_modules', [], $salonId, false);

        // Default to all enabled if empty (first run)
        if (empty($enabledModules)) {
            $enabledModules = [
                'branches' => true,
                'inventory' => true,
                'pos' => true,
                'appointments' => true,
                'customers' => true,
                'services' => true,
                'staff' => true,
                'reports' => true,
                'marketing' => true,
            ];
        }

        $allPermissions = self::getAllPermissions();

        // Map permission modules to Plan Features (can be string or array of strings)
        $featureMapping = [
            'pos' => ['POS System', 'Basic POS System'],
            'inventory' => 'Inventory Management',
            'appointments' => 'Booking System',
            'bookings' => 'Booking System',
            'packages' => 'Packages',
            'memberships' => 'Memberships',
            'staff' => 'Staff Management',
            'services' => ['Service Management', 'Booking System'], // Services are core to bookings
            'reports' => ['Analytics & Reports', 'Basic Reports', 'Advanced Reports'],
            'finance' => ['Analytics & Reports', 'Advanced Reports'],
            'marketing' => ['Analytics & Reports', 'Email Marketing'],
            'branches' => 'Multi-Branch Support',
            'system' => 'Role Management',
            'commissions' => 'Commission Management',
            'email' => 'Email Marketing',
            'customers' => ['Customer Management', 'Booking System'], // Customers are core
            'salon' => null, // Base feature
        ];

        $activePermissions = [];

        foreach ($allPermissions as $module => $permissions) {
            // 1. Check Global Module Setting
            $isCoreModule = in_array($module, ['system', 'salon', 'commissions', 'finance']);
            $isModuleEnabled = $isCoreModule || (isset($enabledModules[$module]) && $enabledModules[$module]);

            if (!$isModuleEnabled) {
                continue;
            }

            // 2. Check Plan Feature
            if ($plan && isset($featureMapping[$module]) && $featureMapping[$module]) {
                $requiredFeatures = (array) $featureMapping[$module];
                $hasFeature = false;

                foreach ($requiredFeatures as $feature) {
                    if ($plan->hasFeature($feature)) {
                        $hasFeature = true;
                        break;
                    }
                }

                // Special case for 'system' module: only 'system.manage_roles' is restricted by 'Role Management'
                if ($module === 'system') {
                    foreach ($permissions as $permission) {
                        if ($permission === 'system.manage_roles') {
                            if ($plan->hasFeature('Role Management')) {
                                $activePermissions[] = $permission;
                            }
                        } else {
                            $activePermissions[] = $permission;
                        }
                    }
                    continue;
                }

                if (!$hasFeature) {
                    continue;
                }
            }

            $activePermissions = array_merge($activePermissions, $permissions);
        }

        return array_unique($activePermissions);
    }

    /**
     * Get role templates with permissions.
     *
     * @param Plan|null $plan
     * @param int|null $salonId
     * @return array
     */
    public static function getRoleTemplates(?Plan $plan = null, ?int $salonId = null): array
    {
        $activePermissions = self::getAvailablePermissions($plan, $salonId);

        // Helper to filter permissions
        $filter = function (callable $callback) use ($activePermissions) {
            return array_filter($activePermissions, $callback);
        };

        return [
            'salon_admin' => [
                'display_name' => 'Salon Admin',
                'description' => 'Full access to all salon features.',
                'permissions' => $activePermissions
            ],
            'manager' => [
                'display_name' => 'Manager',
                'description' => 'Salon Manager with limited administrative access',
                'level' => 80,
                'permissions' => $filter(function ($permission) {
                    // Managers should NOT have system-wide settings or branch management
                    // They also should NOT have delete permissions for core entities
                    $excluded = [
                        'system.manage_settings',
                        'system.manage_roles',
                        'system.view_logs',
                        'system.manage_backups',
                        'salon.delete',
                        'salon.manage_settings',
                        'salon.manage_branches',
                        'salon.manage_email_settings',
                        'staff.manage_commission',
                        'finance.manage_taxes',
                        'finance.manage_payments',
                    ];

                    return !in_array($permission, $excluded) && !str_contains($permission, 'delete');
                })
            ],
            'staff' => [
                'display_name' => 'Staff',
                'description' => 'Regular staff member',
                'level' => 50,
                'permissions' => $filter(function ($permission) {
                    // Staff should only have view and basic create/edit permissions
                    // No management, settings, or delete permissions
                    $allowed = [
                        'system.view_dashboard',
                        'bookings.view',
                        'bookings.create',
                        'bookings.edit',
                        'bookings.view_stats',
                        'bookings.manage_calendar',
                        'bookings.manage_status',
                        'appointments.view',
                        'appointments.create',
                        'appointments.edit',
                        'appointments.manage_calendar',
                        'appointments.manage_status',
                        'staff.view',
                        'staff.view_performance',
                        'services.view',
                        'customers.view',
                        'customers.create',
                        'customers.edit',
                        'customers.view_history',
                        'pos.view',
                        'pos.create_sale',
                        'inventory.view',
                        'reports.view',
                    ];

                    return in_array($permission, $allowed);
                })
            ],
            'employee' => [
                'display_name' => 'Employee',
                'description' => 'Employee with access to their own schedule',
                'level' => 30,
                'permissions' => $filter(function ($permission) {
                    $allowed = [
                        'system.view_dashboard',
                        'bookings.view',
                        'appointments.view',
                        'staff.view',
                        'staff.view_performance',
                        'services.view',
                    ];
                    return in_array($permission, $allowed);
                })
            ],
            'pos_staff' => [
                'display_name' => 'POS Staff',
                'description' => 'Staff focused on Point of Sale',
                'level' => 40,
                'permissions' => $filter(function ($permission) {
                    $allowed = [
                        'system.view_dashboard',
                        'pos.view',
                        'pos.create_sale',
                        'pos.edit_sale',
                        'pos.manage_discounts',
                        'customers.view',
                        'customers.create',
                        'inventory.view',
                    ];
                    return in_array($permission, $allowed);
                })
            ],
            'inventory_staff' => [
                'display_name' => 'Inventory Staff',
                'description' => 'Full access to inventory management and stock control.',
                'level' => 40,
                'permissions' => $filter(function ($permission) {
                    $module = explode('.', $permission)[0];
                    return in_array($module, ['inventory']) || $permission === 'system.view_dashboard';
                })
            ],
            'customer' => [
                'display_name' => 'Customer',
                'description' => 'Basic access for customers.',
                'level' => 10,
                'permissions' => $filter(function ($permission) {
                    return in_array($permission, [
                        'bookings.create',
                        'bookings.view',
                        'services.view',
                        'packages.view',
                        'memberships.view'
                    ]);
                })
            ]
        ];
    }

    /**
     * Get a human-readable label for a permission.
     *
     * @param string $permission
     * @return string
     */
    public static function getPermissionLabel(string $permission): string
    {
        $labels = [
            // System
            'system.view_dashboard' => 'View Dashboard',
            'system.manage_settings' => 'Manage System Settings',
            'system.manage_roles' => 'Manage Roles & Permissions',
            'system.view_logs' => 'View System Logs',
            'system.manage_backups' => 'Manage Backups',

            // Appointments / Bookings
            'bookings.view' => 'View Bookings',
            'bookings.create' => 'Create Bookings',
            'bookings.edit' => 'Edit Bookings',
            'bookings.delete' => 'Delete Bookings',
            'bookings.view_stats' => 'View Booking Statistics',
            'bookings.manage_guest' => 'Manage Guest Bookings',
            'bookings.manage_calendar' => 'Manage Booking Calendar',
            'bookings.send_reminders' => 'Send Booking Reminders',
            'bookings.manage_status' => 'Manage Booking Status',
            'bookings.view_analytics' => 'View Booking Analytics',

            'appointments.view' => 'View Appointments',
            'appointments.create' => 'Create Appointments',
            'appointments.edit' => 'Edit Appointments',
            'appointments.delete' => 'Delete Appointments',
            'appointments.manage_calendar' => 'Manage Appointment Calendar',
            'appointments.send_reminders' => 'Send Appointment Reminders',
            'appointments.manage_status' => 'Manage Appointment Status',
            'appointments.view_analytics' => 'View Appointment Analytics',

            // Salon
            'salon.view' => 'View Salon Information',
            'salon.create' => 'Create New Salon',
            'salon.edit' => 'Edit Salon Details',
            'salon.delete' => 'Delete Salon',
            'salon.manage_settings' => 'Manage Salon Settings',
            'salon.manage_branches' => 'Manage Branches',
            'salon.manage_working_hours' => 'Manage Working Hours',
            'salon.manage_holidays' => 'Manage Holidays',
            'salon.manage_email_templates' => 'Manage Email Templates',
            'salon.manage_email_settings' => 'Manage Email Settings',

            // Staff
            'staff.view' => 'View Staff Members',
            'staff.create' => 'Add New Staff',
            'staff.edit' => 'Edit Staff Member',
            'staff.delete' => 'Remove Staff Member',
            'staff.manage_schedule' => 'Manage Staff Schedules',
            'staff.view_performance' => 'View Staff Performance',
            'staff.manage_commission' => 'Manage Staff Commissions',
            'staff.manage_attendance' => 'Manage Staff Attendance',

            // Services / Memberships / Packages
            'services.view' => 'View Services',
            'services.create' => 'Add New Service',
            'services.edit' => 'Edit Service',
            'services.delete' => 'Delete Service',
            'services.manage_categories' => 'Manage Service Categories',
            'services.manage_pricing' => 'Manage Service Pricing',
            'services.manage_duration' => 'Manage Service Durations',
            'services.assign_staff' => 'Assign Staff to Services',

            'memberships.view' => 'View Memberships',
            'memberships.create' => 'Create Memberships',
            'memberships.edit' => 'Edit Memberships',
            'memberships.delete' => 'Delete Memberships',

            'packages.view' => 'View Packages',
            'packages.create' => 'Create Packages',
            'packages.edit' => 'Edit Packages',
            'packages.delete' => 'Delete Packages',

            // Customers
            'customers.view' => 'View Customers',
            'customers.create' => 'Add New Customer',
            'customers.edit' => 'Edit Customer Profile',
            'customers.delete' => 'Delete Customer',
            'customers.view_history' => 'View Customer History',
            'customers.manage_preferences' => 'Manage Customer Preferences',
            'customers.send_notifications' => 'Send Customer Notifications',
            'customers.manage_loyalty' => 'Manage Loyalty Program',
            'customers.import' => 'Import Customers',
            'customers.export' => 'Export Customers',

            // POS
            'pos.view' => 'Access POS System',
            'pos.create_sale' => 'Create POS Sale',
            'pos.edit_sale' => 'Edit POS Sale',
            'pos.delete_sale' => 'Delete POS Sale',
            'pos.process_refunds' => 'Process Refunds',
            'pos.view_reports' => 'View POS Reports',
            'pos.manage_payment' => 'Manage Payment Methods',
            'pos.manage_discounts' => 'Manage POS Discounts',

            // Inventory
            'inventory.view' => 'View Inventory',
            'inventory.create' => 'Add Inventory Item',
            'inventory.edit' => 'Edit Inventory Item',
            'inventory.delete' => 'Delete Inventory Item',
            'inventory.manage_stock' => 'Manage Stock Levels',
            'inventory.manage_suppliers' => 'Manage Suppliers',
            'inventory.view_reports' => 'View Inventory Reports',
            'inventory.manage_categories' => 'Manage Product Categories',
            'inventory.categories.view' => 'View Product Categories',
            'inventory.categories.create' => 'Create Product Category',
            'inventory.categories.edit' => 'Edit Product Category',
            'inventory.categories.delete' => 'Delete Product Category',
            'products.import' => 'Import Products',
            'products.export' => 'Export Products',

            // Finance
            // 'finance.view' => 'View Finance Module',
            // 'finance.manage_transactions' => 'Manage Transactions',
            // 'finance.view_reports' => 'View Financial Reports',
            // 'finance.manage_taxes' => 'Manage Tax Settings',
            // 'finance.manage_discounts' => 'Manage Financial Discounts',
            // 'finance.manage_payments' => 'Manage Financial Payments',
            // 'finance.view_analytics' => 'View Financial Analytics',
            // 'finance.export_reports' => 'Export Financial Reports',

            // Marketing
            // 'marketing.view' => 'View Marketing Module',
            // 'marketing.create_campaigns' => 'Create Marketing Campaigns',
            // 'marketing.send_notifications' => 'Send Marketing Notifications',
            // 'marketing.manage_promotions' => 'Manage Promotions',
            // 'marketing.view_analytics' => 'View Marketing Analytics',
            // 'marketing.manage_loyalty' => 'Manage Marketing Loyalty',
            // 'marketing.manage_reviews' => 'Manage Customer Reviews',
            // 'marketing.export_data' => 'Export Marketing Data',

            // Reports
            'reports.view' => 'View Reports Dashboard',
            'reports.sales' => 'View Sales Reports',
            'reports.appointments' => 'View Appointment Reports',
            'reports.staff' => 'View Staff Reports',
            'reports.customers' => 'View Customer Reports',

            // Commissions
            'commissions.view' => 'View Staff Commissions',
            'commissions.manage' => 'Manage Commission Rates',
            'commissions.view_reports' => 'View Commission Reports',
        ];

        return $labels[$permission] ?? ucwords(str_replace(['.', '_'], ' ', $permission));
    }
}
