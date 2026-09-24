<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            'auth' => true,
            'role_management' => true,
            'employee_management' => true,
            'service_management' => true,
            'appointment_booking' => true,
            'calendar_views' => true,
            'pos' => true,
            'customer_management' => true,
            'dashboards' => true,
            'multi_currency' => true,
            'saas_module' => true,
            'license_validation' => true,
            'integrations' => true,
            'kiosk_mode' => true,
        ];
        Setting::updateOrCreate([
            'key' => 'features',
        ], [
            'value' => $features,
            'type' => 'feature',
        ]);
    }
} 