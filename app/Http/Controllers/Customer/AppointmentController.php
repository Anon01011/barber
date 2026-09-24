<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Rating;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\SettingsService;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $customer = auth()->user()->customer;
            if (!$customer) {
                return redirect()->route('customer.profile.create')
                    ->with('error', 'Please complete your profile before booking appointments.');
            }

            // Get appointment statistics
            $stats = [
                'total' => $customer->bookings()->count(),
                'upcoming' => $customer->bookings()->upcoming()->count(),
                'completed' => $customer->bookings()->completed()->count(),
                'cancelled' => $customer->bookings()->cancelled()->count(),
            ];

            // Get filtered appointments for the list
            $status = $request->get('status', 'upcoming');
            $query = $customer->bookings()->with(['service', 'staff']);

            switch ($status) {
                case 'upcoming':
                    $query->upcoming();
                    break;
                case 'completed':
                    $query->completed();
                    break;
                case 'cancelled':
                    $query->cancelled();
                    break;
            }

            $appointments = $query->latest()->paginate(10);

            return view('customer.appointments.index', compact('stats', 'appointments', 'status'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load appointments. Please try again.');
        }
    }

    /**
     * API endpoint to fetch appointments for calendar
     */
    public function apiAppointments(Request $request)
    {
        try {
            $customer = auth()->user()->customer;
            if (!$customer) {
                return response()->json(['error' => 'Customer profile incomplete'], 403);
            }

            $bookings = $customer->bookings()
                ->with(['service', 'staff'])
                ->get()
                ->map(function ($booking) {
                    // Color code by status
                    $color = match ($booking->status) {
                        'confirmed' => '#28a745',
                        'pending' => '#ffc107',
                        'completed' => '#17a2b8',
                        'cancelled' => '#dc3545',
                        default => '#6c757d'
                    };

                    return [
                        'id' => $booking->id,
                        'title' => $booking->service ? $booking->service->name : 'Unknown Service',
                        'start' => $booking->start_time->setTimezone(salon_timezone())->toIso8601String(),
                        'end' => $booking->end_time->setTimezone(salon_timezone())->toIso8601String(),
                        'backgroundColor' => $color,
                        'borderColor' => $color,
                        'extendedProps' => [
                            'service' => $booking->service ? $booking->service->name : 'Unknown Service',
                            'staff' => $booking->staff ? $booking->staff->name : 'Unassigned',
                            'status' => $booking->status
                        ]
                    ];
                });

            return response()->json($bookings);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load appointments'], 500);
        }
    }

    /**
     * API endpoint to get single appointment details
     */
    public function apiAppointmentDetails($id)
    {
        try {
            $customer = auth()->user()->customer;
            if (!$customer) {
                return response()->json(['error' => 'Customer profile incomplete'], 403);
            }

            $booking = $customer->bookings()
                ->with(['service', 'staff'])
                ->findOrFail($id);

            return response()->json([
                'id' => $booking->id,
                'service' => $booking->service,
                'staff' => $booking->staff,
                'start_time' => $booking->start_time->setTimezone(salon_timezone())->toIso8601String(),
                'end_time' => $booking->end_time->setTimezone(salon_timezone())->toIso8601String(),
                'status' => $booking->status,
                'amount' => $booking->amount,
                'notes' => $booking->notes
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }
    }

    /**
     * Get the color for appointment status
     */
    private function getStatusColor($status)
    {
        return match ($status) {
            'pending' => '#ffc107', // yellow
            'confirmed' => '#28a745', // green
            'completed' => '#17a2b8', // blue
            'cancelled' => '#dc3545', // red
            default => '#6c757d' // gray
        };
    }

    public function create()
    {
        $salonId = auth()->user()->customer->salon_id;
        $services = Service::where('salon_id', $salonId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('customer.appointments.create', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => [
                'required',
                'exists:services,id',
                function ($attribute, $value, $fail) {
                    $service = Service::find($value);
                    if ($service && $service->salon_id !== auth()->user()->customer->salon_id) {
                        $fail('The selected service is not available.');
                    }
                },
            ],
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'notes' => 'nullable|string'
        ]);

        // Defensive check for customer record
        $customer = Auth::user()->customer;
        if (!$customer) {
            return response()->json([
                'message' => 'No customer profile found. Please complete your profile before booking an appointment.'
            ], 422);
        }

        // Check if the combined date and time is in the past
        // We need to check this using the salon's timezone
        $timezone = app(SettingsService::class)->get('timezone', 'UTC', $customer->salon_id);

        // Parse date and time in the salon's timezone to avoid conversion issues
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $validated['date'] . ' ' . $validated['time'], $timezone);

        if ($startDateTime->isPast()) {
            return response()->json([
                'message' => 'Cannot book an appointment in the past.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $service = Service::findOrFail($validated['service_id']);

            // Get salon timezone
            $timezone = app(SettingsService::class)->get('timezone', 'UTC', $customer->salon_id);

            // IMPORTANT: Parse the date and time separately to avoid timezone conversion issues
            // The date input from the browser is in YYYY-MM-DD format (user's local date)
            // We need to treat it as a date in the salon's timezone, not convert it
            $dateString = $validated['date']; // e.g., "2026-02-05"
            $timeString = $validated['time']; // e.g., "14:30"

            // Create datetime string and parse it directly in the salon's timezone
            // This ensures "2026-02-05 14:30" is interpreted as Feb 5 in the salon's timezone
            $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $dateString . ' ' . $timeString, $timezone);

            // Store directly in salon's timezone as requested
            $startTime = $startDateTime;
            $endTime = $startTime->copy()->addMinutes((int) $service->duration);

            // Create booking with pending status
            $booking = Booking::create([
                'salon_id' => $customer->salon_id, // Explicitly set salon_id
                'customer_id' => $customer->id,
                'created_by' => auth()->id(),
                'service_id' => $validated['service_id'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'notes' => $validated['notes'],
                'amount' => $service->price,
                'status' => 'pending',
                'source' => 'online',
                'staff_assignment_status' => 'pending',
                'time_confirmation_status' => 'pending' // New field to track time confirmation
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Appointment request submitted successfully. Our staff will review and confirm your booking time.',
                'booking' => $booking
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to book appointment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // updateTime method removed as it was unused and potentially insecure (IDOR risk).


    public function show(Booking $booking)
    {
        if ($booking->customer_id !== Auth::user()->customer->id) {
            abort(403);
        }

        $booking->load(['service', 'staff']);
        if (request()->ajax()) {
            return response()->json(['success' => true, 'appointment' => $booking]);
        }
        return view('customer.appointments.show', compact('booking'));
    }

    public function destroy(Booking $booking)
    {
        if ($booking->customer_id !== auth()->user()->customer->id) {
            abort(403, 'Unauthorized action.');
        }

        $booking->delete();

        return redirect()->route('customer.appointments.index')
            ->with('success', 'Appointment cancelled successfully.');
    }

    /**
     * Get available time slots for a service
     */
    public function getAvailableSlots(Request $request)
    {
        $date = $request->get('date');
        $serviceId = $request->get('service_id');

        if (!$date || !$serviceId) {
            return response()->json(['error' => 'Date and service are required'], 400);
        }

        $service = Service::findOrFail($serviceId);

        // Verify service belongs to customer's salon
        if ($service->salon_id !== auth()->user()->customer->salon_id) {
            return response()->json(['error' => 'Service not available'], 403);
        }

        $duration = $service->duration;

        // Get salon timezone and settings
        $salonId = auth()->user()->customer->salon_id;
        $settings = app(\App\Services\SettingsService::class);
        $timezone = $settings->get('timezone', config('app.timezone', 'UTC'), $salonId);
        $workingStart = $settings->get('working_hours_start', '09:00', $salonId);
        $workingEnd = $settings->get('working_hours_end', '20:00', $salonId);
        $slotInterval = (int) $settings->get('slot_duration', 30, $salonId);
        $bufferTime = (int) $settings->get('appointment_buffer_time', 0, $salonId);

        // Get all active staff members
        $staffMembers = User::role('employee')
            ->where('salon_id', $salonId)
            ->where('status', 'active')
            ->get();

        if ($staffMembers->isEmpty()) {
            return response()->json(['error' => 'No staff available'], 404);
        }

        // Parse slot boundaries in salon timezone
        $currentSlot = Carbon::parse($date . ' ' . $workingStart, $timezone);
        $endOfDay = Carbon::parse($date . ' ' . $workingEnd, $timezone);
        $dayOfWeek = Carbon::parse($date, $timezone)->dayOfWeek;

        // Get all bookings for the date
        $dayStart = Carbon::parse($date, $timezone)->startOfDay()->setTimezone('UTC');
        $dayEnd = Carbon::parse($date, $timezone)->endOfDay()->setTimezone('UTC');

        $bookings = Booking::where('salon_id', $salonId)
            ->whereBetween('start_time', [$dayStart, $dayEnd])
            ->where('status', '!=', 'cancelled')
            ->get()
            ->groupBy('staff_id');

        // Get staff schedules for this day
        $staffSchedules = \App\Models\StaffSchedule::whereIn('staff_id', $staffMembers->pluck('id'))
            ->where('day_of_week', $dayOfWeek)
            ->get()
            ->keyBy('staff_id');

        // Get staff absences for this date
        $dateCarbon = Carbon::parse($date, $timezone);
        $staffAbsences = \App\Models\StaffAbsence::whereIn('staff_id', $staffMembers->pluck('id'))
            ->whereDate('start_at', '<=', $dateCarbon)
            ->whereDate('end_at', '>=', $dateCarbon)
            ->pluck('staff_id')
            ->toArray();

        $slots = [];

        // Generate slots
        while ($currentSlot->lt($endOfDay)) {
            // Stop if the slot + duration exceeds end of day
            if ($currentSlot->copy()->addMinutes((int) $duration)->gt($endOfDay)) {
                break;
            }

            $time = $currentSlot->format('H:i');
            $slotStart = $currentSlot->copy();
            $slotEnd = $slotStart->copy()->addMinutes((int) $duration);

            // Check if at least one staff member is available
            $isAvailable = false;

            foreach ($staffMembers as $staff) {
                // Skip if staff is absent
                if (in_array($staff->id, $staffAbsences)) {
                    continue;
                }

                // Check staff schedule
                $schedule = $staffSchedules->get($staff->id);
                if (!$schedule || !$schedule->is_working) {
                    continue;
                }

                // Check if slot is within staff working hours (intersection with salon hours)
                $staffStart = Carbon::createFromFormat('H:i:s', $schedule->start_time, $timezone)->setDateFrom($slotStart);
                $staffEnd = Carbon::createFromFormat('H:i:s', $schedule->end_time, $timezone)->setDateFrom($slotStart);

                // Use intersection: slot must be within both salon hours and staff hours
                $effectiveStart = $slotStart->lt($staffStart) ? $staffStart : $slotStart;
                $effectiveEnd = $slotEnd->gt($staffEnd) ? $staffEnd : $slotEnd;

                // If slot doesn't fit within staff hours, skip
                if ($slotStart->lt($staffStart) || $slotEnd->gt($staffEnd)) {
                    continue;
                }

                // Check for booking conflicts for this staff
                $hasConflict = false;
                $staffBookings = $bookings->get($staff->id, collect());

                foreach ($staffBookings as $booking) {
                    $bookingStart = $booking->start_time->copy()->setTimezone($timezone);
                    $bookingEnd = $booking->end_time->copy()->setTimezone($timezone)->addMinutes($bufferTime);

                    // Check overlap
                    if ($slotStart->lt($bookingEnd) && $slotEnd->gt($bookingStart)) {
                        $hasConflict = true;
                        break;
                    }
                }

                // If this staff is available, mark slot as available
                if (!$hasConflict) {
                    $isAvailable = true;
                    break; // At least one staff is available, no need to check others
                }
            }

            $slots[] = [
                'time' => $time,
                'available' => $isAvailable
            ];

            $currentSlot->addMinutes($slotInterval);
        }

        return response()->json($slots);
    }

    public function getServices()
    {
        $salonId = auth()->user()->customer->salon_id;
        $services = Service::where('salon_id', $salonId)
            ->where('status', 'active')
            ->availableForOnlineBooking()
            ->orderBy('name')
            ->get(['id', 'name', 'duration']);

        return response()->json($services);
    }

    public function getStaff()
    {
        $salonId = auth()->user()->customer->salon_id;
        $staff = User::role('employee')
            ->where('salon_id', $salonId)
            ->where('status', 'active')
            ->select('id', 'name', 'email')
            ->get();
        return response()->json($staff);
    }

    /**
     * Get the customer's favorite services
     */
    public function getFavoriteServices()
    {
        try {
            $user = auth()->user();
            $favorites = $user->customer->favoriteServices()
                ->with(['category', 'staff'])
                ->get();

            return response()->json([
                'status' => 'success',
                'favorites' => $favorites
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching favorite services', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch favorite services'
            ], 500);
        }
    }

    /**
     * Reschedule an appointment
     */
    public function reschedule(Request $request, Booking $booking)
    {
        // Check ownership
        if ($booking->customer_id !== auth()->user()->customer->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check status
        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return response()->json(['message' => 'Cannot reschedule completed or cancelled appointments'], 422);
        }

        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
        ]);

        // Check if the combined date and time is in the past
        $timezone = app(SettingsService::class)->get('timezone', 'UTC', $booking->salon_id);

        // Parse date and time in the salon's timezone to avoid conversion issues
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $validated['date'] . ' ' . $validated['time'], $timezone);

        if ($startDateTime->isPast()) {
            return response()->json([
                'message' => 'Cannot reschedule to a time in the past.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $service = $booking->service;

            // Store directly in salon's timezone (no conversion to UTC)
            $startTime = $startDateTime;
            $endTime = $startTime->copy()->addMinutes((int) $service->duration);

            // Update booking
            // We reset status to pending because the new time needs confirmation
            $booking->update([
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'pending',
                'staff_assignment_status' => 'pending',
                'time_confirmation_status' => 'pending'
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Appointment rescheduled successfully.',
                'booking' => $booking
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Appointment reschedule failed', ['error' => $e->getMessage(), 'user_id' => auth()->id()]);
            return response()->json([
                'message' => 'Failed to reschedule appointment. Please try again.'
            ], 500);
        }
    }

    /**
     * Rate a completed appointment
     */
    public function rate(Request $request, Booking $booking)
    {
        // Validate the request
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        try {
            // Check if user has a customer profile
            $customer = auth()->user()->customer;

            // Debug logging
            \Log::info('Rating submission attempt', [
                'user_id' => auth()->id(),
                'customer' => $customer ? $customer->id : 'null',
                'appointment_customer_id' => $booking->customer_id,
                'appointment_id' => $booking->id
            ]);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer profile not found.'
                ], 403);
            }

            // Check if the booking belongs to the authenticated customer
            if ($booking->customer_id !== $customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }

            // Check if the booking is completed
            if ($booking->status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only rate completed appointments.'
                ], 422);
            }

            // Check if already rated
            if ($booking->is_rated) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already rated this appointment.'
                ], 422);
            }

            // Start database transaction
            DB::beginTransaction();

            // Create the rating
            $rating = new Rating([
                'booking_id' => $booking->id,
                'customer_id' => $customer->id, // Use Customer record ID, not auth User ID
                'service_id' => $booking->service_id,
                'employee_id' => $booking->staff_id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]);

            $rating->save();

            // Commit the transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your rating!'
            ]);

        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            \Log::error('Error submitting rating', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->id ?? null,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit rating. Please try again.'
            ], 500);
        }
    }
}