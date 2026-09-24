<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class AssignSuperAdminPermissions extends Command
{
    protected $signature = 'admin:assign-super-admin {email?}';
    protected $description = 'Assign super admin role and all permissions to a user';

    public function handle()
    {
        // Get or create super admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);

        // Get all permissions
        $permissions = Permission::all();

        // Assign all permissions to super admin role
        $superAdminRole->syncPermissions($permissions);

        $this->info('All permissions have been assigned to super_admin role.');

        // If email is provided, assign role to user
        if ($email = $this->argument('email')) {
            $user = User::where('email', $email)->first();

            if (!$user) {
                $this->error("User with email {$email} not found!");
                return;
            }

            $user->syncRoles(['super_admin']);
            $this->info("Super admin role has been assigned to {$email}");
        }
    }
}