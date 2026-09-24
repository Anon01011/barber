<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;
use App\Models\Salon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VerifyRoles extends Command
{
    protected $signature = 'verify:roles';
    protected $description = 'Verify role scoping and assignment safety';

    public function handle()
    {
        $this->info('Starting verification...');

        DB::beginTransaction();

        try {
            // Create owner
            $owner = User::factory()->create(['name' => 'Owner', 'email' => 'owner@example.com']);

            // Create two salons with required fields
            $salon1 = Salon::create([
                'name' => 'Salon A', 
                'slug' => 'salon-a', 
                'owner_id' => $owner->id,
                'email' => 'salon1@example.com',
                'phone' => '1234567890',
                'address' => '123 Main St'
            ]);
            $salon2 = Salon::create([
                'name' => 'Salon B', 
                'slug' => 'salon-b', 
                'owner_id' => $owner->id,
                'email' => 'salon2@example.com',
                'phone' => '0987654321',
                'address' => '456 High St'
            ]);

            // Create users for each salon
            $user1 = User::factory()->create(['salon_id' => $salon1->id, 'name' => 'User A', 'email' => 'usera@example.com']);
            $user2 = User::factory()->create(['salon_id' => $salon2->id, 'name' => 'User B', 'email' => 'userb@example.com']);

            // Create roles for each salon
            $role1 = Role::create(['name' => 'Role A', 'salon_id' => $salon1->id, 'guard_name' => 'web']);
            $role2 = Role::create(['name' => 'Role B', 'salon_id' => $salon2->id, 'guard_name' => 'web']);

            // 1. Verify Scoping
            $this->info('1. Verifying Role Scoping...');
            
            Auth::login($user1);
            $roles1 = Role::salon($salon1->id)->get();
            
            if ($roles1->contains('name', 'Role B')) {
                $this->error("FAIL: User A sees Role B");
            } else {
                $this->info("PASS: User A does not see Role B");
            }

            if (!$roles1->contains('name', 'Role A')) {
                $this->error("FAIL: User A does not see Role A");
            } else {
                $this->info("PASS: User A sees Role A");
            }

            // 2. Verify Assignment Safety
            $this->info('2. Verifying Assignment Safety...');

            // Try to assign Role B (Salon B) to User A (Salon A)
            try {
                $user1->assignRole($role2);
                $this->error("FAIL: User A was assigned Role B (Cross-salon assignment succeeded)");
            } catch (\Exception $e) {
                if (str_contains($e->getMessage(), 'another salon')) {
                    $this->info("PASS: Cross-salon assignment blocked: " . $e->getMessage());
                } else {
                    $this->error("FAIL: Exception thrown but unexpected message: " . $e->getMessage());
                }
            }

            // Try to assign Role A (Salon A) to User A (Salon A)
            try {
                $user1->assignRole($role1);
                if ($user1->hasRole($role1)) {
                    $this->info("PASS: Same-salon assignment succeeded");
                } else {
                    $this->error("FAIL: Same-salon assignment failed silently");
                }
            } catch (\Exception $e) {
                $this->error("FAIL: Same-salon assignment threw exception: " . $e->getMessage());
            }

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error($e->getTraceAsString());
        } finally {
            DB::rollBack();
            $this->info('Rolled back database changes.');
        }
    }
}
