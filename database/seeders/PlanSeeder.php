<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run()
    {
        // Free Trial Plan
        Plan::updateOrCreate(
            ['slug' => 'free-trial'],
            [
                'name' => 'Free Trial',
                'business_type' => 'both',
                'price' => 0.00,
                'duration_in_days' => 14,
                'trial_days' => 14,
                'description' => 'Try our platform with basic features for 14 days',
                'features' => [
                    'Booking System',
                    'Customer Management',
                    'Basic Reports'
                ],
                'limits' => [
                    'max_users' => -1, // Unlimited for trial
                    'max_branches' => 1,
                    'max_customers' => 50,
                    'max_services' => 10,
                    'max_service_categories' => 5,
                    'max_products' => 10,
                    'max_bookings_per_month' => 100,
                    'max_guest_bookings_per_month' => 20
                ],
                'max_users' => -1, // Unlimited for trial
                'max_branches' => 1,
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 1
            ]
        );

        // Basic Plan
        Plan::updateOrCreate(
            ['slug' => 'basic-plan'],
            [
                'name' => 'Basic',
                'business_type' => 'both',
                'price' => 29.00,
                'duration_in_days' => 30,
                'trial_days' => 14,
                'description' => 'Essential features for small salons',
                'features' => [
                    'Booking System',
                    'Customer Management',
                    'Staff Management',
                    'Basic POS System',
                    'Email Notifications',
                    'Basic Reports'
                ],
                'limits' => [
                    'max_users' => 5,
                    'max_branches' => 1,
                    'max_customers' => 500,
                    'max_services' => 25,
                    'max_service_categories' => 10,
                    'max_products' => 50,
                    'max_bookings_per_month' => -1, // Unlimited
                    'max_guest_bookings_per_month' => 100
                ],
                'max_users' => 5,
                'max_branches' => 1,
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 2
            ]
        );

        // Standard Plan (Most Popular)
        Plan::updateOrCreate(
            ['slug' => 'standard-plan'],
            [
                'name' => 'Standard',
                'business_type' => 'both',
                'price' => 79.00,
                'duration_in_days' => 30,
                'trial_days' => 14,
                'description' => 'Perfect for growing salons',
                'features' => [
                    'Booking System',
                    'Customer Management',
                    'Staff Management',
                    'POS System',
                    'Inventory Management',
                    'Memberships',
                    'Packages',
                    'SMS Notifications',
                    'Email Marketing',
                    'Advanced Reports'
                ],
                'limits' => [
                    'max_users' => -1, // Unlimited
                    'max_branches' => 3,
                    'max_customers' => -1, // Unlimited
                    'max_services' => -1,
                    'max_service_categories' => -1,
                    'max_products' => -1,
                    'max_packages' => 50,
                    'max_memberships' => 50,
                    'max_bookings_per_month' => -1,
                    'max_guest_bookings_per_month' => -1
                ],
                'max_users' => -1, // Unlimited
                'max_branches' => 3,
                'is_active' => true,
                'is_popular' => true, // Recommended plan
                'sort_order' => 3
            ]
        );

        // Premium Plan
        Plan::updateOrCreate(
            ['slug' => 'premium-plan'],
            [
                'name' => 'Premium',
                'business_type' => 'both',
                'price' => 149.00,
                'duration_in_days' => 30,
                'trial_days' => 14,
                'description' => 'All features for established salons',
                'features' => [
                    'Booking System',
                    'Customer Management',
                    'Staff Management',
                    'POS System',
                    'Inventory Management',
                    'Role Management',
                    'Multi-Branch Support',
                    'Memberships',
                    'Packages',
                    'Commission Management',
                    'SMS Notifications',
                    'Email Marketing',
                    'Analytics & Reports',
                    'Customer Data Masking',
                    'AI Insights & Automation',
                    'Priority Support'
                ],
                'limits' => [
                    'max_users' => 25,
                    'max_branches' => 5,
                    'max_customers' => -1,
                    'max_services' => -1,
                    'max_service_categories' => -1,
                    'max_products' => -1,
                    'max_packages' => -1,
                    'max_memberships' => -1,
                    'max_bookings_per_month' => -1,
                    'max_guest_bookings_per_month' => -1
                ],
                'max_users' => 25,
                'max_branches' => 5,
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 4
            ]
        );

        // Enterprise Plan
        Plan::updateOrCreate(
            ['slug' => 'enterprise-plan'],
            [
                'name' => 'Enterprise',
                'business_type' => 'both',
                'price' => 299.00,
                'duration_in_days' => 30,
                'trial_days' => 30,
                'description' => 'Unlimited everything for large businesses',
                'features' => [
                    'All Premium Features',
                    'Unlimited Users',
                    'Unlimited Branches',
                    'Custom Integrations',
                    'API Access',
                    'Dedicated Account Manager',
                    'Priority Support',
                    'Custom Reports',
                    'White Label Options',
                    'Advanced Security'
                ],
                'limits' => [
                    'max_users' => -1, // Unlimited
                    'max_branches' => -1,
                    'max_customers' => -1,
                    'max_services' => -1,
                    'max_service_categories' => -1,
                    'max_products' => -1,
                    'max_packages' => -1,
                    'max_memberships' => -1,
                    'max_bookings_per_month' => -1,
                    'max_guest_bookings_per_month' => -1
                ],
                'max_users' => -1,
                'max_branches' => -1,
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 5
            ]
        );
    }
}
