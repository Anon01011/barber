<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ModuleEntitlementService;
use App\Models\Salon;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;

class ModuleEntitlementTest extends TestCase
{
    protected ModuleEntitlementService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ModuleEntitlementService();
    }

    /**
     * Test mapping of all 16 official modules to their feature strings
     */
    public function test_all_16_modules_have_feature_mappings(): void
    {
        $expectedModules = [
            'branches', 'inventory', 'pos', 'appointments', 'customers',
            'services', 'staff', 'reports', 'packages', 'memberships',
            'commissions', 'roles', 'marketing', 'sms', 'whatsapp', 'ai',
            'loyalty', 'feedback'
        ];

        foreach ($expectedModules as $mod) {
            $this->assertArrayHasKey($mod, ModuleEntitlementService::MODULE_FEATURE_MAP);
            $this->assertNotEmpty(ModuleEntitlementService::MODULE_FEATURE_MAP[$mod]);
        }
    }

    /**
     * Test getModuleKeyForFeature normalization
     */
    public function test_get_module_key_for_feature_normalization(): void
    {
        $this->assertEquals('pos', $this->service->getModuleKeyForFeature('Point of Sale (POS)'));
        $this->assertEquals('pos', $this->service->getModuleKeyForFeature('POS System'));
        $this->assertEquals('branches', $this->service->getModuleKeyForFeature('Multi-Branch Management'));
        $this->assertEquals('branches', $this->service->getModuleKeyForFeature('Multi-Branch Support'));
        $this->assertEquals('inventory', $this->service->getModuleKeyForFeature('Inventory Management'));
        $this->assertEquals('appointments', $this->service->getModuleKeyForFeature('Booking System'));
        $this->assertEquals('ai', $this->service->getModuleKeyForFeature('AI Insights & Automation'));
        $this->assertEquals('loyalty', $this->service->getModuleKeyForFeature('Loyalty Program'));
        $this->assertEquals('feedback', $this->service->getModuleKeyForFeature('Customer Feedback & Reviews'));
        $this->assertNull($this->service->getModuleKeyForFeature('Nonexistent Fake Module'));
    }

    /**
     * Test dependencies definition
     */
    public function test_module_dependencies_definition(): void
    {
        $this->assertEquals(['services'], ModuleEntitlementService::MODULE_DEPENDENCIES['pos']);
        $this->assertEquals(['services', 'staff', 'customers'], ModuleEntitlementService::MODULE_DEPENDENCIES['appointments']);
        $this->assertEquals(['staff'], ModuleEntitlementService::MODULE_DEPENDENCIES['commissions']);
        $this->assertEquals(['customers', 'pos'], ModuleEntitlementService::MODULE_DEPENDENCIES['loyalty']);
        $this->assertEquals(['appointments'], ModuleEntitlementService::MODULE_DEPENDENCIES['feedback']);
    }

    /**
     * Test canUseFeature denies access if salon is null
     */
    public function test_can_use_feature_denies_access_when_salon_is_null(): void
    {
        $this->assertFalse($this->service->canUseFeature(null, 'Point of Sale (POS)'));
    }

    /**
     * Test canAccess denies access when module is not globally enabled
     */
    public function test_can_access_denies_access_when_globally_disabled(): void
    {
        // Mock service with isGloballyEnabled = false
        $mock = $this->getMockBuilder(ModuleEntitlementService::class)
            ->onlyMethods(['isGloballyEnabled'])
            ->getMock();

        $mock->method('isGloballyEnabled')->willReturn(false);

        $salon = new Salon(['name' => 'Demo Salon']);
        $this->assertFalse($mock->canAccess($salon, 'pos'));
        $this->assertFalse($mock->canUseFeature($salon, 'Point of Sale (POS)'));
    }
}
