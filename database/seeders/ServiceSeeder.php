<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $salon = \App\Models\Salon::where('slug', 'demo-salon')->first();
        if (!$salon) {
            Log::warning('Demo salon not found. Skipping service seeding.');
            return;
        }

        // Clear existing data for this salon
        Service::where('salon_id', $salon->id)->delete();
        ServiceCategory::where('salon_id', $salon->id)->delete();

        // Create service categories
        $categories = [
            [
                'name' => 'Hair Services',
                'description' => 'Professional hair cutting, styling, and treatment services',
                'status' => 'active',
                'salon_id' => $salon->id
            ],
            [
                'name' => 'Nail Services',
                'description' => 'Manicure, pedicure, and nail art services',
                'status' => 'active',
                'salon_id' => $salon->id
            ],
            [
                'name' => 'Skin Care',
                'description' => 'Facial treatments and skin care services',
                'status' => 'active',
                'salon_id' => $salon->id
            ],
            [
                'name' => 'Massage',
                'description' => 'Relaxation and therapeutic massage services',
                'status' => 'active',
                'salon_id' => $salon->id
            ]
        ];

        foreach ($categories as $category) {
            ServiceCategory::create($category);
        }

        // Create services for each category
        $services = [
            'Hair Services' => [
                [
                    'name' => 'Haircut',
                    'description' => 'Professional haircut and styling',
                    'duration' => 30,
                    'price' => 25.00,
                    'status' => 'active',
                    'salon_id' => $salon->id
                ],
                [
                    'name' => 'Hair Coloring',
                    'description' => 'Full hair coloring service',
                    'duration' => 120,
                    'price' => 85.00,
                    'status' => 'active',
                    'salon_id' => $salon->id
                ],
                [
                    'name' => 'Hair Treatment',
                    'description' => 'Deep conditioning and treatment',
                    'duration' => 45,
                    'price' => 45.00,
                    'status' => 'active',
                    'salon_id' => $salon->id
                ]
            ],
            'Nail Services' => [
                [
                    'name' => 'Manicure',
                    'description' => 'Basic manicure service',
                    'duration' => 45,
                    'price' => 35.00,
                    'status' => 'active',
                    'salon_id' => $salon->id
                ],
                [
                    'name' => 'Pedicure',
                    'description' => 'Basic pedicure service',
                    'duration' => 60,
                    'price' => 45.00,
                    'status' => 'active',
                    'salon_id' => $salon->id
                ],
                [
                    'name' => 'Nail Art',
                    'description' => 'Custom nail art design',
                    'duration' => 30,
                    'price' => 25.00,
                    'status' => 'active',
                    'salon_id' => $salon->id
                ]
            ],
            'Skin Care' => [
                [
                    'name' => 'Basic Facial',
                    'description' => 'Cleansing and basic facial treatment',
                    'duration' => 60,
                    'price' => 65.00,
                    'status' => 'active',
                    'salon_id' => $salon->id
                ],
                [
                    'name' => 'Deep Cleansing Facial',
                    'description' => 'Advanced facial with deep cleansing',
                    'duration' => 90,
                    'price' => 95.00,
                    'status' => 'active',
                    'salon_id' => $salon->id
                ]
            ],
            'Massage' => [
                [
                    'name' => 'Swedish Massage',
                    'description' => 'Relaxation massage',
                    'duration' => 60,
                    'price' => 75.00,
                    'status' => 'active',
                    'salon_id' => $salon->id
                ],
                [
                    'name' => 'Deep Tissue Massage',
                    'description' => 'Therapeutic deep tissue massage',
                    'duration' => 90,
                    'price' => 110.00,
                    'status' => 'active',
                    'salon_id' => $salon->id
                ]
            ]
        ];

        foreach ($services as $categoryName => $categoryServices) {
            $category = ServiceCategory::where('name', $categoryName)->where('salon_id', $salon->id)->first();
            if ($category) {
                foreach ($categoryServices as $service) {
                    $service['category_id'] = $category->id;
                    Service::create($service);
                }
            }
        }

        Log::info('Service categories and services seeded successfully for demo salon');
    }
}