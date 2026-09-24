<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Setting;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected $settingsService;

    protected $salon;
    protected $branch;

    protected function setUp(): void
    {
        parent::setUp();
        $this->settingsService = app(SettingsService::class);
        $this->salon = \App\Models\Salon::create([
            'name' => 'Test Salon',
            'slug' => 'test-salon-' . uniqid(),
            'email' => 'test' . uniqid() . '@salon.com',
            'phone' => '1234567890',
            'is_active' => true
        ]);
        app()->instance('current_salon', $this->salon);
        $this->branch = \App\Models\Branch::create([
            'salon_id' => $this->salon->id,
            'name' => 'Test Branch',
            'slug' => 'test-branch-' . uniqid(),
            'address' => '123 Test Street',
            'phone' => '1234567890',
            'email' => 'branch' . uniqid() . '@test.com',
            'is_active' => true
        ]);
    }

    public function test_get_global_setting()
    {
        $this->settingsService->set('test_key', 'global_value');

        $this->assertEquals('global_value', $this->settingsService->get('test_key'));
    }

    public function test_get_salon_setting_overrides_global()
    {
        $salonId = $this->salon->id;
        $this->settingsService->set('test_key', 'global_value');
        $this->settingsService->set('test_key', 'salon_value', $salonId);

        $this->assertEquals('global_value', $this->settingsService->get('test_key'));
        $this->assertEquals('salon_value', $this->settingsService->get('test_key', null, $salonId));
    }

    public function test_get_branch_setting_overrides_salon_and_global()
    {
        $salonId = $this->salon->id;
        $branchId = $this->branch->id;
        
        $this->settingsService->set('test_key', 'global_value');
        $this->settingsService->set('test_key', 'salon_value', $salonId);
        $this->settingsService->set('test_key', 'branch_value', $salonId, $branchId);

        $this->assertEquals('global_value', $this->settingsService->get('test_key'));
        $this->assertEquals('salon_value', $this->settingsService->get('test_key', null, $salonId));
        $this->assertEquals('branch_value', $this->settingsService->get('test_key', null, $salonId, $branchId));
    }

    public function test_branch_setting_fallback_to_salon_then_global()
    {
        $salonId = $this->salon->id;
        $branchId = $this->branch->id;
        
        // 1. Fallback to Salon
        $this->settingsService->set('test_key', 'salon_value', $salonId);
        $this->assertEquals('salon_value', $this->settingsService->get('test_key', null, $salonId, $branchId));
        
        // 2. Fallback to Global
        $this->settingsService->set('test_key_2', 'global_value');
        $this->assertEquals('global_value', $this->settingsService->get('test_key_2', null, $salonId, $branchId));
    }

    public function test_set_saas_settings()
    {
        $salonId = $this->salon->id;
        $branchId = $this->branch->id;
        
        $this->settingsService->set('test_key', 'salon_value', $salonId);
        $this->settingsService->set('test_key', 'branch_value', $salonId, $branchId);

        $this->assertDatabaseHas('settings', [
            'key' => 'test_key',
            'value' => json_encode('salon_value'),
            'salon_id' => $salonId,
            'branch_id' => null
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'test_key',
            'value' => json_encode('branch_value'),
            'salon_id' => $salonId,
            'branch_id' => $branchId
        ]);
    }
    
    public function test_get_all_merges_layers()
    {
        $salonId = $this->salon->id;
        $branchId = $this->branch->id;
        
        $this->settingsService->set('global_only', 'global');
        $this->settingsService->set('salon_override', 'global');
        $this->settingsService->set('branch_override', 'global');
        
        $this->settingsService->set('salon_override', 'salon', $salonId);
        $this->settingsService->set('branch_override', 'salon', $salonId);
        $this->settingsService->set('salon_only', 'salon', $salonId);
        
        $this->settingsService->set('branch_override', 'branch', $salonId, $branchId);
        $this->settingsService->set('branch_only', 'branch', $salonId, $branchId);
        
        $all = $this->settingsService->getAll($salonId, $branchId);
        
        $this->assertEquals('global', $all['global_only']);
        $this->assertEquals('salon', $all['salon_override']);
        $this->assertEquals('salon', $all['salon_only']);
        $this->assertEquals('branch', $all['branch_override']);
        $this->assertEquals('branch', $all['branch_only']);
    }
}
