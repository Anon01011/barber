<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get user's role and permissions with eager loading
        $user->load('roles.permissions');
        $roles = $user->getRoleNames();
        $permissions = $user->getAllPermissions()->pluck('name');
        
        // Log detailed permissions for debugging
        \Log::info('User permissions loaded:', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'roles' => $roles->toArray(),
            'permissions' => $permissions->toArray(),
            'role_details' => $user->roles->map(function($role) {
                return [
                    'name' => $role->name,
                    'status' => $role->status,
                    'permissions' => $role->permissions->pluck('name')->toArray()
                ];
            })->toArray(),
            'active_roles' => $user->roles->where('status', true)->pluck('name')->toArray(),
            'inactive_roles' => $user->roles->where('status', false)->pluck('name')->toArray()
        ]);
        
        // Prepare data based on role
        $data = [
            'user' => $user,
            'roles' => $roles,
            'permissions' => $permissions,
        ];

        // Add role-specific data
        if ($user->hasRole('super_admin')) {
            $data['dashboard_type'] = 'super_admin';
            // Add super admin specific data
        } elseif ($user->hasRole('salon_admin')) {
            $data['dashboard_type'] = 'salon_admin';
            // Add salon admin specific data
        } elseif ($user->hasRole('manager')) {
            $data['dashboard_type'] = 'manager';
            // Add manager specific data
        } elseif ($user->hasRole('employee')) {
            $data['dashboard_type'] = 'employee';
            // Add employee specific data
        } else {
            $data['dashboard_type'] = 'customer';
            // Add customer specific data
        }

        return view('dashboard', $data);
    }
}