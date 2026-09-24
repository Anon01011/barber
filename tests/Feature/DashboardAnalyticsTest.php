<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\Salon;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class DashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected $salon;
    protected $admin;
    protected $customer;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->salon = Salon::factory()->create(['slug' => 'test-salon']);

        // Ensure roles exist
        if (!Role::where('name', 'salon_admin')->exists()) {
            Role::create(['name' => 'salon_admin']);
        }

        $this->admin = User::factory()->create(['salon_id' => $this->salon->id]);
        $this->admin->assignRole('salon_admin');

        $this->customer = Customer::factory()->create(['salon_id' => $this->salon->id]);
        $this->service = Service::factory()->create(['salon_id' => $this->salon->id, 'price' => 100]);
    }

    /** @test */
    public function it_does_not_double_count_revenue_when_booking_is_linked_to_pos()
    {
        // 1. Create a booking
        $booking = Booking::create([
            'salon_id' => $this->salon->id,
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'start_time' => Carbon::now(),
            'end_time' => Carbon::now()->addHour(),
            'amount' => 100,
            'status' => 'completed',
            'payment_status' => 'paid'
        ]);

        // 2. Create a POS sale linked to this booking
        $posSale = PosSale::create([
            'salon_id' => $this->salon->id,
            'customer_id' => $this->customer->id,
            'status' => 'paid',
            'subtotal' => 100,
            'total' => 100,
            'cash_amount' => 100
        ]);

        PosSaleItem::create([
            'pos_sale_id' => $posSale->id,
            'booking_id' => $booking->id,
            'item_type' => Service::class,
            'item_id' => $this->service->id,
            'quantity' => 1,
            'unit_price' => 100,
            'subtotal' => 100,
            'total' => 100
        ]);

        // 3. Access dashboard data
        $response = $this->actingAs($this->admin)->get(route('dashboard', ['salon_slug' => $this->salon->slug]));

        $response->assertStatus(200);

        // Verify revenue in the view data
        $totalRevenue = $response->viewData('total_bill_value');
        $billCount = $response->viewData('bill_count');

        // Revenue should be 100 (from POS), not 200 (Booking + POS)
        $this->assertEquals(100, $totalRevenue, "Revenue is double counted!");
        $this->assertEquals(1, $billCount, "Bill count is double counted!");
    }

    /** @test */
    public function it_shows_only_one_entry_in_recent_sales_for_pos_linked_booking()
    {
        // 1. Create a booking
        $booking = Booking::create([
            'salon_id' => $this->salon->id,
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'start_time' => Carbon::now(),
            'end_time' => Carbon::now()->addHour(),
            'amount' => 100,
            'status' => 'completed',
            'payment_status' => 'paid'
        ]);

        // 2. Create a POS sale linked to this booking
        $posSale = PosSale::create([
            'salon_id' => $this->salon->id,
            'customer_id' => $this->customer->id,
            'status' => 'paid',
            'subtotal' => 100,
            'total' => 100,
            'cash_amount' => 100,
            'invoice_number' => 'INV-001'
        ]);

        PosSaleItem::create([
            'pos_sale_id' => $posSale->id,
            'booking_id' => $booking->id,
            'item_type' => Service::class,
            'item_id' => $this->service->id,
            'quantity' => 1,
            'unit_price' => 100,
            'subtotal' => 100,
            'total' => 100
        ]);

        $response = $this->actingAs($this->admin)->get(route('dashboard', ['salon_slug' => $this->salon->slug]));

        $recentSales = $response->viewData('recent_bookings'); // This is the variable for recent sales

        // Should only have 1 entry (the POS sale)
        $this->assertEquals(1, $recentSales->count(), "Recent sales shows duplicate entries for POS-linked booking!");
        $this->assertEquals('pos', $recentSales->first()->type);
    }
}
