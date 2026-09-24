<?php

namespace Tests\Feature;

use App\Models\Salon;
use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StaffScheduleDefaultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_schedule_uses_salon_working_hours()
    {
        // 1. Setup Salon and Owner
        $salon = Salon::create([
            'name' => 'Test Salon',
            'slug' => 'test-salon',
            'email' => 'test@salon.com',
            'status' => true,
            'is_active' => true,
        ]);

        $owner = User::factory()->create([
            'salon_id' => $salon->id,
            'email_verified_at' => now(),
        ]);

        $this->withoutMiddleware([
            \App\Http\Middleware\CheckSalonSlug::class,
            \App\Http\Middleware\ConfigureSalonMail::class,
            \App\Http\Middleware\SetBranchContext::class,
            \App\Http\Middleware\CheckSalonApproval::class,
            \Spatie\Permission\Middleware\PermissionMiddleware::class,
            \App\Http\Middleware\SubscriptionMiddleware::class,
            \App\Http\Middleware\CheckPlanFeature::class,
            \App\Http\Middleware\CheckModuleEnabled::class,
        ]);

        // 2. Set Salon Working Hours
        $settingsService = app(SettingsService::class);
        $settingsService->set('working_hours_start', '10:00', $salon->id);
        $settingsService->set('working_hours_end', '18:00', $salon->id);

        // 3. Create Staff Member (without schedule)
        $staff = User::factory()->create([
            'salon_id' => $salon->id,
            'name' => 'Test Staff'
        ]);

        // 4. Authenticate as Owner
        $this->actingAs($owner);

        // 5. Call getSchedule endpoint
        $response = $this->get(route('admin.staff.schedule.index', ['salon_slug' => $salon->slug, 'staff' => $staff->id]));

        if ($response->status() !== 200) {
            $response->dump();
        }

        $response->assertStatus(200);

        $schedule = $response->json();

        // 6. Verify Defaults
        // Check that at least one day has the correct start/end time
        $this->assertEquals('10:00', $schedule[0]['start_time']);
        $this->assertEquals('18:00', $schedule[0]['end_time']);
    }

    public function test_staff_creation_uses_salon_defaults()
    {
        // 1. Setup Salon and Owner
        $salon = Salon::create([
            'name' => 'Test Salon 2',
            'slug' => 'test-salon-2',
            'email' => 'test2@salon.com',
            'status' => true,
            'is_active' => true,
        ]);

        $owner = User::factory()->create([
            'salon_id' => $salon->id,
            'email_verified_at' => now(),
        ]);

        $this->withoutMiddleware([
            \App\Http\Middleware\CheckSalonSlug::class,
            \App\Http\Middleware\ConfigureSalonMail::class,
            \App\Http\Middleware\SetBranchContext::class,
            \App\Http\Middleware\CheckSalonApproval::class,
            \Spatie\Permission\Middleware\PermissionMiddleware::class,
            \App\Http\Middleware\SubscriptionMiddleware::class,
            \App\Http\Middleware\CheckPlanFeature::class,
            \App\Http\Middleware\CheckModuleEnabled::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        ]);

        // 2. Set Salon Working Hours
        $settingsService = app(SettingsService::class);
        $settingsService->set('working_hours_start', '10:00', $salon->id);
        $settingsService->set('working_hours_end', '18:00', $salon->id);

        // Create employee role
        \Spatie\Permission\Models\Role::create(['name' => 'employee', 'guard_name' => 'web']);

        // Bind current_salon since middleware is disabled
        app()->instance('current_salon', $salon);

        // 3. Authenticate as Owner
        $this->actingAs($owner);

        // 4. Create Staff with partial schedule (no times)
        $response = $this->post(route('admin.staff.store', ['salon_slug' => $salon->slug]), [
            'name' => 'New Staff',
            'email' => 'newstaff@example.com',
            'status' => 'active',
            'role' => 'employee',
            'allow_login' => true,
            'password' => 'password',
            'password_confirmation' => 'password',
            'schedule' => [
                [
                    'day_of_week' => 1, // Monday
                    'is_working' => true,
                    // start_time and end_time missing, should use defaults
                ]
            ]
        ]);

        $response->assertRedirect();

        $staff = User::where('email', 'newstaff@example.com')->first();
        $this->assertNotNull($staff);

        $schedule = \App\Models\StaffSchedule::where('staff_id', $staff->id)
            ->where('day_of_week', 1)
            ->first();

        $this->assertNotNull($schedule);
        $this->assertEquals('10:00:00', $schedule->start_time);
        $this->assertEquals('18:00:00', $schedule->end_time);
    }
}
