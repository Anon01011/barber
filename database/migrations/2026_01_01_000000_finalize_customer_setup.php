<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Customer;

class FinalizeCustomerSetup extends Migration
{
    public function up()
    {
        // Check if the users table exists
        if (!Schema::hasTable('users')) {
            return;
        }

        // Check if the customers table exists, if not create it
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function ($table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('preferred_contact')->default('email');
                $table->string('status')->default('active');
                $table->timestamps();
            });
        }

        // Get all users who don't have a customer record yet
        $users = User::whereDoesntHave('customer')
            ->where(function($query) {
                // Only create customer records for users with customer role if roles table exists
                if (Schema::hasTable('model_has_roles')) {
                    $query->whereHas('roles', function($q) {
                        $q->where('name', 'customer');
                    });
                } else {
                    // If roles table doesn't exist, create customer records for all users
                    $query->whereRaw('1=1');
                }
            })
            ->get();

        foreach ($users as $user) {
            try {
                Customer::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone ?? null,
                        'preferred_contact' => 'email',
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            } catch (\Exception $e) {
                // Log the error but don't stop the migration
                \Log::error("Failed to create customer record for user {$user->id}: " . $e->getMessage());
            }
        }
    }

    public function down()
    {
        // Don't drop the customers table as it might contain important data
        // Just remove the foreign key constraint if it exists
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function ($table) {
                if (Schema::hasColumn('customers', 'user_id')) {
                    $table->dropForeign(['user_id']);
                }
            });
        }
    }
}
