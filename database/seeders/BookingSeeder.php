<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all customers
        $customers = User::role('customer')->with('customer')->get();
        if ($customers->isEmpty()) {
            $this->command->warn('No customers found. Please run UserSeeder first.');
            return;
        }

        // Get all active services
        $services = Service::where('status', 'active')->get();
        if ($services->isEmpty()) {
            $this->command->warn('No active services found. Please add some services first.');
            return;
        }

        // Get all staff members
        $staff = User::role('employee')->where('status', 'active')->get();
        if ($staff->isEmpty()) {
            $this->command->warn('No active staff found. Please run EmployeeSeeder first.');
            return;
        }

        try {
            DB::beginTransaction();

            // Clear existing bookings
            DB::table('bookings')->truncate();

            // Generate bookings for the next 30 days
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->addDays(30)->endOfDay();

            $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
            $statusWeights = [
                'pending' => 20,    // 20% pending
                'confirmed' => 40,  // 40% confirmed
                'completed' => 30,  // 30% completed
                'cancelled' => 10   // 10% cancelled
            ];

            $bookings = [];
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                // Generate 2-5 bookings per day
                $bookingsPerDay = rand(2, 5);
                
                for ($i = 0; $i < $bookingsPerDay; $i++) {
                    // Skip weekends (Saturday and Sunday)
                    if ($currentDate->isWeekend()) {
                        continue;
                    }

                    // Generate time between 9 AM and 5 PM
                    $hour = rand(9, 16);
                    $minute = rand(0, 3) * 15; // 15-minute intervals
                    $bookingTime = $currentDate->copy()->setHour($hour)->setMinute($minute);

                    // Skip if booking time is in the past
                    if ($bookingTime->isPast()) {
                        continue;
                    }

                    // Randomly select customer, service, and staff
                    $customer = $customers->random();
                    $service = $services->random();
                    $staffMember = $staff->random();

                    // Skip if the user does not have a related Customer record
                    if (!$customer->customer) {
                        continue;
                    }

                    // Calculate end time based on service duration
                    $endTime = $bookingTime->copy()->addMinutes($service->duration);

                    // Determine status based on weights
                    $status = $this->getWeightedRandomStatus($statusWeights);

                    // For past dates, adjust status probabilities
                    if ($bookingTime->isPast()) {
                        $status = $this->getPastDateStatus($bookingTime);
                    }

                    $bookings[] = [
                        'customer_id' => $customer->customer->id,
                        'service_id' => $service->id,
                        'staff_id' => $staffMember->id,
                        'start_time' => $bookingTime,
                        'end_time' => $endTime,
                        'amount' => $service->price,
                        'status' => $status,
                        'notes' => $this->getRandomNotes($status),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $currentDate->addDay();
            }

            // Insert bookings in chunks to avoid memory issues
            foreach (array_chunk($bookings, 100) as $chunk) {
                Booking::insert($chunk);
            }

            DB::commit();
            $this->command->info('Successfully seeded ' . count($bookings) . ' bookings.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Failed to seed bookings: ' . $e->getMessage());
        }
    }

    /**
     * Get a random status based on weights
     */
    private function getWeightedRandomStatus(array $weights): string
    {
        $total = array_sum($weights);
        $rand = rand(1, $total);
        $current = 0;

        foreach ($weights as $status => $weight) {
            $current += $weight;
            if ($rand <= $current) {
                return $status;
            }
        }

        return 'pending'; // fallback
    }

    /**
     * Get appropriate status for past dates
     */
    private function getPastDateStatus(Carbon $date): string
    {
        if ($date->isPast()) {
            // For dates more than a day in the past
            if ($date->diffInDays(now()) > 1) {
                return rand(0, 1) ? 'completed' : 'cancelled';
            }
            // For dates within the last day
            return rand(0, 2) ? 'completed' : 'cancelled';
        }
        return 'pending';
    }

    /**
     * Get random notes based on status
     */
    private function getRandomNotes(string $status): ?string
    {
        $notes = [
            'pending' => [
                'Awaiting confirmation',
                'Please confirm appointment',
                'New booking request'
            ],
            'confirmed' => [
                'Appointment confirmed',
                'Looking forward to seeing you',
                'Confirmed booking'
            ],
            'completed' => [
                'Service completed successfully',
                'Thank you for your business',
                'Appointment completed'
            ],
            'cancelled' => [
                'Cancelled by customer',
                'Unable to attend',
                'Rescheduling requested'
            ]
        ];

        return rand(0, 1) ? $notes[$status][array_rand($notes[$status])] : null;
    }
} 