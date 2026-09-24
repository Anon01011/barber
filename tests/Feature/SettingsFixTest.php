<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\SettingsService;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingsFixTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_service_strips_quotes_from_strings()
    {
        // Arrange
        $service = new SettingsService();
        
        // Create settings with quoted values directly in DB to simulate the issue
        Setting::create(['key' => 'business_email', 'value' => '"test@example.com"']);
        Setting::create(['key' => 'business_name', 'value' => "'My Salon'"]);
        Setting::create(['key' => 'normal_setting', 'value' => 'Normal Value']);
        
        // Act
        $settings = $service->getAll();
        
        // Assert
        $this->assertEquals('test@example.com', $settings['business_email']);
        $this->assertEquals('My Salon', $settings['business_name']);
        $this->assertEquals('Normal Value', $settings['normal_setting']);
    }
}
