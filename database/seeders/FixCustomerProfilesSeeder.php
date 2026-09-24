<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class FixCustomerProfilesSeeder extends Seeder
{
    public function run(): void
    {
        // Get all users with the 'customer' role
        $users = User::role('customer')->get();

        foreach ($users as $user) {
            // Check if the user already has a customer profile
            if (!$user->customer) {
                // Create a customer profile for this user
                Customer::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                    'preferred_contact' => 'email',
                    'status' => 'active',
                    'user_id' => $user->id
                ]);
            } else {
                // Ensure the user_id is set correctly
                if ($user->customer->user_id !== $user->id) {
                    $user->customer->user_id = $user->id;
                    $user->customer->save();
                }
            }
        }
    }
} 