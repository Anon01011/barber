<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Customer;
use App\Models\User;

class CreateCustomerRecords extends Migration
{
    public function up()
    {
        // Check if the model_has_roles table exists
        if (!Schema::hasTable('model_has_roles')) {
            return;
        }

        // Get all users with customer role who don't have a customer record yet
        $customerUsers = User::whereHas('roles', function($query) {
                $query->where('name', 'customer');
            })
            ->whereDoesntHave('customer')
            ->get();

        // Create customer records for each user
        foreach ($customerUsers as $user) {
            Customer::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'preferred_contact' => 'email',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        // This is a data migration, so we don't need to do anything in the down method
        // as we don't want to delete customer records that might have been modified
    }
}
