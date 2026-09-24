<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        // $this->middleware('role:super_admin|salon_admin'); // Removed to allow custom roles with permission

        // Check for subscription feature access
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user->isSuperAdmin()) {
                if (!$user->salon || !$user->salon->canUseFeature('Role Management')) {
                    abort(403, 'Upgrade your plan to manage roles.');
                }
            }
            return $next($request);
        });
    }

    public function index()
    {
        $user = auth()->user();

        $query = Role::with(['permissions'])
            ->withCount([
                'users' => function ($query) use ($user) {
                    if (!$user->isSuperAdmin()) {
                        $query->where('users.salon_id', $user->salon_id);
                    }
                }
            ]);

        if (!$user->isSuperAdmin() || app()->bound('current_salon')) {
            $salonId = app()->bound('current_salon') ? app('current_salon')->id : $user->salon_id;
            $query->where('salon_id', $salonId)
                ->where('name', '!=', 'super_admin');
        }

        $roles = $query->orderBy('created_at', 'desc')->get();

        // Get permissions based on plan features for salon admins
        if ($user->isSuperAdmin()) {
            $permissions = Permission::all();
        } else {
            // Get salon's plan
            $plan = $user->salon?->activeSubscription?->plan;

            // Get available permission names based on plan
            $availablePermissionNames = \App\Services\PermissionService::getAvailablePermissions($plan);

            // Fetch actual Permission models
            $permissions = Permission::whereIn('name', $availablePermissionNames)->get();
        }

        // For non-super admins, mark which permissions are available in their plan
        if (!$user->isSuperAdmin()) {
            $settingsService = app(\App\Services\SettingsService::class);
            $enabledModules = $settingsService->get('enabled_modules', [], false, false);
            $subscription = $user->salon?->activeSubscription;
            $plan = $subscription?->plan;

            // If salon has an active subscription, default to showing all as available
            $hasActiveSubscription = $subscription && $subscription->isActive();

            // Map permission prefixes to Modules and Plan Features
            $prefixMap = [
                'bookings' => ['module' => 'appointments', 'feature' => 'Booking System'],
                'appointments' => ['module' => 'appointments', 'feature' => 'Booking System'],
                'packages' => ['module' => 'services', 'feature' => 'Packages'],
                'memberships' => ['module' => 'services', 'feature' => 'Memberships'],
                'services' => ['module' => 'services', 'feature' => null], // Base feature
                'staff' => ['module' => 'staff', 'feature' => 'Staff Management'],
                'reports' => ['module' => 'reports', 'feature' => 'Analytics & Reports'],
                'finance' => ['module' => 'reports', 'feature' => 'Analytics & Reports'], // Finance is part of reports
                'marketing' => ['module' => 'reports', 'feature' => 'Analytics & Reports'], // Marketing is part of reports
                'customers' => ['module' => 'customers', 'feature' => 'Customer Management'],
                'pos' => ['module' => 'pos', 'feature' => 'POS System'],
                'inventory' => ['module' => 'inventory', 'feature' => 'Inventory Management'],
                'products' => ['module' => 'inventory', 'feature' => 'Inventory Management'], // Products are part of inventory
                'commissions' => ['module' => 'commissions', 'feature' => 'Commission Management'],
                'system' => ['module' => 'system', 'feature' => 'Role Management'],
                'salon' => ['module' => 'salon', 'feature' => null],
            ];

            // Mark each permission as available or not (but don't filter them out)
            $permissions = $permissions->map(function ($permission) use ($enabledModules, $plan, $prefixMap, $hasActiveSubscription) {
                $prefix = explode('.', $permission->name)[0];
                $map = $prefixMap[$prefix] ?? ['module' => $prefix, 'feature' => null];
                $module = $map['module'];
                $feature = $map['feature'];

                // Special handling for 'salon' prefix
                if ($prefix === 'salon') {
                    if ($permission->name === 'salon.manage_branches') {
                        $module = 'branches';
                        $feature = 'Multi-Branch Support';
                    } elseif (str_starts_with($permission->name, 'salon.manage_email')) {
                        $module = 'email';
                        $feature = 'Email Marketing';
                    }
                }

                // Default to available if has active subscription
                $isAvailable = $hasActiveSubscription;

                // Only check restrictions if we have plan/module data AND subscription is active
                if ($hasActiveSubscription && (!empty($enabledModules) || $plan)) {
                    // Check if module is enabled (only if modules are configured)
                    if (!empty($enabledModules)) {
                        $isModuleEnabled = in_array($module, ['system', 'salon', 'commissions', 'email']) ||
                            (isset($enabledModules[$module]) && $enabledModules[$module]);

                        if (!$isModuleEnabled) {
                            $isAvailable = false;
                        }
                    }

                    // Check if plan has the feature (only if plan exists and has features configured)
                    if ($isAvailable && $plan && $feature) {
                        $planFeatures = $plan->features;

                        // Only check if features are actually configured (non-empty array)
                        if (is_array($planFeatures) && !empty($planFeatures)) {
                            if ($prefix === 'system' && $permission->name === 'system.manage_roles') {
                                $isAvailable = $plan->hasFeature('Role Management');
                            } elseif ($feature) {
                                $isAvailable = $plan->hasFeature($feature);
                            }
                        }
                        // If features array is empty or null, assume all features are available
                    }
                }

                // Mark permission availability
                $permission->is_available = $isAvailable;
                $permission->requires_feature = $feature;
                $permission->requires_module = $module;

                return $permission;
            });
        } else {
            // Super admin sees all permissions as available
            $permissions = $permissions->map(function ($permission) {
                $permission->is_available = true;
                $permission->requires_feature = null;
                $permission->requires_module = null;
                return $permission;
            });
        }

        // Group permissions by module for better organization
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            $parts = explode('.', $permission->name);
            return ucfirst($parts[0] ?? 'other');
        });

        // Add module metadata for display
        $moduleMetadata = [
            'system' => ['name' => 'System', 'icon' => 'cog'],
            'salon' => ['name' => 'Salon', 'icon' => 'store'],
            'bookings' => ['name' => 'Bookings', 'icon' => 'calendar-check'],
            'appointments' => ['name' => 'Appointments', 'icon' => 'calendar-alt'],
            'staff' => ['name' => 'Staff', 'icon' => 'users'],
            'services' => ['name' => 'Services', 'icon' => 'cut'],
            'customers' => ['name' => 'Customers', 'icon' => 'user-friends'],
            'pos' => ['name' => 'POS', 'icon' => 'cash-register'],
            'inventory' => ['name' => 'Inventory', 'icon' => 'boxes'],
            'products' => ['name' => 'Products', 'icon' => 'box'],
            'packages' => ['name' => 'Packages', 'icon' => 'gift'],
            'memberships' => ['name' => 'Memberships', 'icon' => 'id-card'],
            'reports' => ['name' => 'Reports & Analytics', 'icon' => 'chart-line'],
            'finance' => ['name' => 'Finance (Reports)', 'icon' => 'dollar-sign'],
            'marketing' => ['name' => 'Marketing (Reports)', 'icon' => 'bullhorn'],
            'commissions' => ['name' => 'Commissions', 'icon' => 'percentage'],
        ];

        $routePrefix = $user->isSuperAdmin() ? 'admin.super.roles' : 'admin.roles';

        return view('admin.roles.index', compact('roles', 'permissions', 'groupedPermissions', 'routePrefix', 'moduleMetadata'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles')->where(function ($query) {
                    $user = auth()->user();
                    if ($user->isSuperAdmin()) {
                        return $query->whereNull('salon_id');
                    }
                    return $query->where(function ($q) use ($user) {
                        $q->where('salon_id', $user->salon_id)
                            ->orWhereNull('salon_id');
                    });
                }),
                'regex:/^[a-z0-9_]+$/'
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'in:0,1'],
            'permissions' => ['required', 'array'],
            'permissions.*' => [
                'exists:permissions,id',
                function ($attribute, $value, $fail) {
                    $user = auth()->user();
                    if (!$user->isSuperAdmin()) {
                        $permission = Permission::find($value);
                        if (!$permission) {
                            $fail('Invalid permission.');
                            return;
                        }

                        // Check if permission is available in the salon's plan
                        $plan = $user->salon?->activeSubscription?->plan;
                        $availablePermissionNames = \App\Services\PermissionService::getAvailablePermissions($plan);

                        if (!in_array($permission->name, $availablePermissionNames)) {
                            $fail('This permission is not available in your current plan.');
                        }
                    }
                }
            ]
        ]);

        try {
            DB::beginTransaction();

            $roleData = [
                'name' => strtolower($validated['name']),
                'guard_name' => 'web',
                'description' => $request->description,
                'status' => $request->has('status') ? ($request->status == '1') : true
            ];

            if (!auth()->user()->isSuperAdmin() || app()->bound('current_salon')) {
                $roleData['salon_id'] = app()->bound('current_salon') ? app('current_salon')->id : auth()->user()->salon_id;
            }

            $role = Role::create($roleData);

            // Convert permission IDs to permission names
            $permissionNames = Permission::whereIn('id', $validated['permissions'])
                ->pluck('name')
                ->toArray();

            $role->syncPermissions($permissionNames);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Role created successfully',
                'role' => $role->load(['permissions', 'users'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Role $role)
    {
        // Only super_admin role is protected from modification
        if ($role->name === 'super_admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot modify the super_admin role.'
            ], 403);
        }

        if (!auth()->user()->isSuperAdmin() && $role->salon_id !== auth()->user()->salon_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles')->ignore($role->id)->where(function ($query) use ($role) {
                    $user = auth()->user();
                    if ($user->isSuperAdmin()) {
                        return $query->whereNull('salon_id');
                    }
                    return $query->where(function ($q) use ($role) {
                        $q->where('salon_id', $role->salon_id)
                            ->orWhereNull('salon_id');
                    });
                }),
                'regex:/^[a-z0-9_]+$/'
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'in:0,1'],
            'permissions' => ['required', 'array'],
            'permissions.*' => [
                'exists:permissions,id',
                function ($attribute, $value, $fail) {
                    $user = auth()->user();
                    if (!$user->isSuperAdmin()) {
                        $permission = Permission::find($value);
                        if (!$permission) {
                            $fail('Invalid permission.');
                            return;
                        }

                        // Check if permission is available in the salon's plan
                        $plan = $user->salon?->activeSubscription?->plan;
                        $availablePermissionNames = \App\Services\PermissionService::getAvailablePermissions($plan);

                        if (!in_array($permission->name, $availablePermissionNames)) {
                            $fail('This permission is not available in your current plan.');
                        }
                    }
                }
            ]
        ]);

        try {
            DB::beginTransaction();

            $updateData = [
                'name' => $validated['name'],
                'description' => $request->description,
            ];

            if ($request->has('status')) {
                $updateData['status'] = $request->status == '1';
            }

            $role->update($updateData);

            // Convert permission IDs to permission names
            $permissionNames = Permission::whereIn('id', $validated['permissions'])
                ->pluck('name')
                ->toArray();

            $role->syncPermissions($permissionNames);

            // Clear permission cache for all users with this role
            foreach ($role->users as $user) {
                $user->forgetCachedPermissions();
            }
            cache()->forget('spatie.permission.cache');

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Role updated successfully',
                'role' => $role->fresh(['permissions', 'users'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'super_admin' || $role->name === 'salon_admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete ' . $role->name . ' role'
            ], 403);
        }

        if (!auth()->user()->isSuperAdmin() && $role->salon_id !== auth()->user()->salon_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action.'
            ], 403);
        }

        if ($role->users()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete role with assigned users'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $role->permissions()->detach();
            $role->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Role deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Role $role)
    {
        try {
            // Check if the role is super admin or salon admin
            if ($role->name === 'super_admin' || $role->name === 'salon_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot modify ' . $role->name . ' role status'
                ], 403);
            }

            DB::beginTransaction();

            // Toggle the status
            $role->status = !$role->status;
            $role->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role status updated successfully',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'status' => $role->status
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error toggling role status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update role status',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    public function getTemplates()
    {
        try {
            $user = auth()->user();
            $plan = $user->salon?->activeSubscription?->plan;

            // Get templates from service
            $templates = \App\Services\PermissionService::getRoleTemplates($plan);

            // We need to convert permission names to IDs for the frontend
            // First get all relevant permissions
            $permissionNames = [];
            foreach ($templates as $template) {
                $permissionNames = array_merge($permissionNames, $template['permissions']);
            }
            $permissionNames = array_unique($permissionNames);

            $permissions = Permission::whereIn('name', $permissionNames)->pluck('id', 'name');

            // Map permission names to IDs in templates
            foreach ($templates as $key => &$template) {
                $template['permissions'] = collect($template['permissions'])->map(function ($name) use ($permissions) {
                    return $permissions[$name] ?? null;
                })->filter()->values()->toArray();
            }

            return response()->json([
                'status' => 'success',
                'templates' => $templates
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch role templates',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(Role $role)
    {
        try {
            // Check if user has permission to edit this role
            // Check if user has permission to edit this role
            // Allow if: user is super admin, role belongs to user's salon, or role is global
            if (!auth()->user()->isSuperAdmin() && $role->salon_id !== null && $role->salon_id !== auth()->user()->salon_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized action.'
                ], 403);
            }

            // Eager load relationships
            $role->load(['permissions']);

            // Get users count and users filtered by salon for non-super admins
            $user = auth()->user();
            $usersQuery = $role->users();

            if (!$user->isSuperAdmin()) {
                $usersQuery->where('users.salon_id', $user->salon_id);
            }

            $usersCount = $usersQuery->count();

            // Get all permissions with their IDs and names
            $roleData = [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'status' => (bool) $role->status,
                'users_count' => $usersCount,
                'permissions' => $role->permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'module' => explode('.', $permission->name)[0]
                    ];
                })->toArray(),
                'users' => [] // Removed full users list to prevent memory exhaustion
            ];

            // Determine if the role is editable by the current user
            $isEditable = true;

            // Only super_admin role is non-editable
            if ($role->name === 'super_admin') {
                $isEditable = false;
            }

            // Non-super admins can only edit roles from their own salon
            if (!auth()->user()->isSuperAdmin()) {
                if ($role->salon_id !== auth()->user()->salon_id) {
                    $isEditable = false;
                }
            }

            return response()->json([
                'status' => 'success',
                'role' => $roleData,
                'is_editable' => $isEditable
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in edit role:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch role data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUsers(Role $role)
    {
        try {
            $user = auth()->user();

            // Check if user has permission to access this role
            // Allow if: user is super admin, role belongs to user's salon, or role is global (salon_id is null)
            if (!$user->isSuperAdmin() && $role->salon_id !== null && $role->salon_id !== $user->salon_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access to this role'
                ], 403);
            }

            $query = $role->users()->select('users.id', 'users.name', 'users.email', 'users.status');

            // Filter by salon for non-super admins
            if (!$user->isSuperAdmin()) {
                $query->where('users.salon_id', $user->salon_id);
            }

            $users = $query->orderBy('users.name')->limit(100)->get();

            return response()->json([
                'status' => 'success',
                'users' => $users
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching role users:', [
                'role_id' => $role->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch users: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAvailableUsers(Role $role)
    {
        try {
            $user = auth()->user();

            // Check if user has permission to access this role
            // Allow if: user is super admin, role belongs to user's salon, or role is global (salon_id is null)
            if (!$user->isSuperAdmin() && $role->salon_id !== null && $role->salon_id !== $user->salon_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access to this role'
                ], 403);
            }

            $query = User::whereDoesntHave('roles', function ($query) use ($role) {
                $query->where('roles.id', $role->id);
            })
                ->select('users.id', 'users.name', 'users.email');

            // Filter by salon for non-super admins
            if (!$user->isSuperAdmin()) {
                $query->where('users.salon_id', $user->salon_id);
            }

            $users = $query->orderBy('users.name')->limit(100)->get();

            return response()->json([
                'status' => 'success',
                'users' => $users
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching available users:', [
                'role_id' => $role->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch available users: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addUser(Request $request, Role $role)
    {
        try {
            $currentUser = auth()->user();

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::findOrFail($request->user_id);

            // Verify user belongs to the same salon (for non-super admins)
            if (!$currentUser->isSuperAdmin()) {
                if ($user->salon_id !== $currentUser->salon_id) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Cannot assign role to user from another salon'
                    ], 403);
                }

                // Verify role belongs to the same salon OR is a global role
                if ($role->salon_id !== null && $role->salon_id !== $currentUser->salon_id) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Cannot assign role from another salon'
                    ], 403);
                }
            }

            if ($user->hasRole($role)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User already has this role'
                ], 422);
            }

            $user->assignRole($role);

            return response()->json([
                'status' => 'success',
                'message' => 'User added to role successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add user to role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeUser(Role $role, User $user)
    {
        try {
            $currentUser = auth()->user();

            // Verify user belongs to the same salon (for non-super admins)
            if (!$currentUser->isSuperAdmin()) {
                if ($user->salon_id !== $currentUser->salon_id) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Cannot remove role from user in another salon'
                    ], 403);
                }
            }

            if (!$user->hasRole($role)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User does not have this role'
                ], 422);
            }

            if ($role->name === 'super_admin' && $role->users()->count() <= 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot remove the last super admin'
                ], 422);
            }

            // Prevent salon admin from removing their own salon_admin role if they're the last one
            if ($role->name === 'salon_admin' && $user->id === $currentUser->id) {
                $salonAdminCount = $role->users()
                    ->where('users.salon_id', $currentUser->salon_id)
                    ->count();

                if ($salonAdminCount <= 1) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Cannot remove the last salon admin from your salon'
                    ], 422);
                }
            }

            $user->removeRole($role);

            return response()->json([
                'status' => 'success',
                'message' => 'User removed from role successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to remove user from role: ' . $e->getMessage()
            ], 500);
        }
    }
}