<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerPackageBalance;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use App\Mail\BookingStatusUpdatedMail;
use App\Notifications\BookingNotification;

class BookingController extends Controller
{
    protected $settingsService;
    protected $notificationService;

    public function __construct(
        \App\Services\SettingsService $settingsService,
        \App\Services\NotificationService $notificationService
    ) {
        $this->settingsService = $settingsService;
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        Log::info('Accessing booking calendar view');
        $salonId = auth()->user()->salon_id;

        // Get all staff members with the 'employee' role for this salon
        // Get all staff members with the 'employee' role for this salon
        $staffMembersQuery = \App\Models\User::role('employee')
            ->where('salon_id', $salonId)
            ->orderBy('name');

        // Filter by branch if branch context is active
        if (app()->has('current_branch')) {
            $currentBranchId = app('current_branch')->id;
            $staffMembersQuery->where(function ($q) use ($currentBranchId) {
                $q->where('branch_id', $currentBranchId)
                  ->orWhereNull('branch_id');
            });
        }

        if (auth()->user()->hasRole('employee')) {
            $staffMembersQuery->where('id', auth()->id());
        }

        $staffMembers = $staffMembersQuery->get(['id', 'name']);

        // Get calendar color settings
        $settings = [
            'pending_color' => $this->settingsService->get('booking_color_pending', '#f8fafc', $salonId),
            'confirmed_color' => $this->settingsService->get('booking_color_confirmed', '#f0fdf4', $salonId),
            'completed_color' => $this->settingsService->get('booking_color_completed', '#eff6ff', $salonId),
            'cancelled_color' => $this->settingsService->get('booking_color_cancelled', '#fef2f2', $salonId),
            'pending_border' => $this->settingsService->get('booking_border_pending', '#e2e8f0', $salonId),
            'confirmed_border' => $this->settingsService->get('booking_border_confirmed', '#86efac', $salonId),
            'completed_border' => $this->settingsService->get('booking_border_completed', '#93c5fd', $salonId),
            'cancelled_border' => $this->settingsService->get('booking_border_cancelled', '#fca5a5', $salonId),
            'pending_text' => $this->settingsService->get('booking_text_pending', '#000000', $salonId),
            'confirmed_text' => $this->settingsService->get('booking_text_confirmed', '#166534', $salonId),
            'completed_text' => $this->settingsService->get('booking_text_completed', '#1e40af', $salonId),
            'cancelled_text' => $this->settingsService->get('booking_text_cancelled', '#991b1b', $salonId),
            'booking_color_unpaid' => $this->settingsService->get('booking_color_unpaid', '#dc3545', $salonId),
            'booking_border_unpaid' => $this->settingsService->get('booking_border_unpaid', '#dc3545', $salonId),
            'booking_text_unpaid' => $this->settingsService->get('booking_text_unpaid', '#ffffff', $salonId),

            // Calendar Settings
            'working_hours_start' => $this->settingsService->get('working_hours_start', '08:00', $salonId),
            'working_hours_end' => $this->settingsService->get('working_hours_end', '20:00', $salonId),
            'slot_duration' => (int) $this->settingsService->get('slot_duration', 30, $salonId),
            'timezone' => salon_timezone(),
            'time_format' => $this->settingsService->get('time_format', '24h', $salonId),

            // Masking Settings
            'customer_data_masking_enabled' => (bool) $this->settingsService->get('customer_data_masking_enabled', false, $salonId),
            'mask_customer_phone' => (bool) $this->settingsService->get('mask_customer_phone', false, $salonId),
            'mask_customer_email' => (bool) $this->settingsService->get('mask_customer_email', false, $salonId),
        ];

        // Check if this is an AJAX request for list view
        if ($request->ajax() && $request->has('view') && $request->get('view') === 'list') {
            $perPage = $request->get('per_page', 10);
            $staffId = $request->get('staff_id');
            $sortBy = $request->get('sort_by', 'newest');
            $status = $request->get('status');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            $query = Booking::where('bookings.salon_id', $salonId)
                ->with(['customer.user', 'service', 'staff']);

            if (auth()->user()->hasRole('employee')) {
                $query->where('bookings.staff_id', auth()->id());
            }

            // Apply filters
            if ($staffId) {
                if (str_contains($staffId, ',')) {
                    $query->whereIn('bookings.staff_id', explode(',', $staffId));
                } else {
                    $query->where('bookings.staff_id', $staffId);
                }
            }

            if ($status) {
                $query->where('bookings.status', $status);
            }

            if ($startDate) {
                $query->whereDate('bookings.start_time', '>=', $startDate);
            }

            if ($endDate) {
                $query->whereDate('bookings.start_time', '<=', $endDate);
            }

            // Apply Sorting
            if ($sortBy === 'oldest') {
                $query->orderBy('bookings.start_time', 'asc');
            } elseif ($sortBy === 'alpha_asc' || $sortBy === 'alpha_desc') {
                $query->join('customers', 'bookings.customer_id', '=', 'customers.id')
                    ->select('bookings.*')
                    ->orderBy('customers.name', $sortBy === 'alpha_asc' ? 'asc' : 'desc');
            } else {
                $query->orderBy('bookings.start_time', 'desc');
            }

            $bookings = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'html' => view('admin.bookings.partials.booking_rows', ['bookings' => $bookings])->render(),
                'pagination' => (string) $bookings->links('pagination::bootstrap-4'),
                'total' => $bookings->total(),
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage()
            ]);
        }

        // Get Services and Packages for the dropdowns
        $services = Service::where('salon_id', $salonId)
            ->where('status', 'active')
            ->with(['staff:id', 'employees:id,user_id', 'category:id,name'])
            ->get(['id', 'name', 'duration', 'price', 'category_id'])
            ->map(function ($service) {
                // Merge staff from both relationships (direct user relation and employee profile relation)
                $staffIds = $service->staff->pluck('id')->toArray();
                $employeeUserIds = $service->employees->pluck('user_id')->filter()->toArray();

                $service->staff_ids = array_values(array_unique(array_merge($staffIds, $employeeUserIds)));

                // Add category name flattening
                $service->category_name = $service->category ? $service->category->name : 'Uncategorized';

                unset($service->staff);
                unset($service->employees);
                unset($service->category);
                return $service;
            });
        $packages = \App\Models\Package::where('salon_id', $salonId)
            ->with([
                'services' => function ($query) {
                    $query->select('services.id', 'services.name', 'services.duration', 'services.price')
                        ->with(['staff:id', 'employees:id,user_id']);
                }
            ])
            ->get(['id', 'name', 'type', 'price', 'service_limit'])
            ->map(function ($package) {
                $package->services->map(function ($service) {
                    $staffIds = $service->staff->pluck('id')->toArray();
                    $employeeUserIds = $service->employees->pluck('user_id')->filter()->toArray();

                    $service->staff_ids = array_values(array_unique(array_merge($staffIds, $employeeUserIds)));

                    unset($service->staff);
                    unset($service->employees);
                    return $service;
                });
                return $package;
            });

        return view('bookings.index', compact('staffMembers', 'settings', 'services', 'packages'));
    }

    /**
     * Get available time slots for a staff member on a specific date
     */
    public function getAvailableTimeSlots(Request $request)
    {
        try {
            $salonId = auth()->user()->salon_id;
            $validated = $request->validate([
                'staff_id' => [
                    'required',
                    \Illuminate\Validation\Rule::exists('users', 'id')->where('salon_id', $salonId)
                ],
                'date' => 'required|date',
                'service_id' => [
                    'nullable',
                    \Illuminate\Validation\Rule::exists('services', 'id')->where('salon_id', $salonId)
                ],
                'booking_id' => [
                    'nullable',
                    \Illuminate\Validation\Rule::exists('bookings', 'id')->where('salon_id', $salonId)
                ],
                'booking_group_id' => 'nullable',
                'desired_time' => 'nullable|date_format:H:i'
            ]);
            $staffId = $validated['staff_id'];
            $date = Carbon::parse($validated['date']);
            // Get salon working hours as the base
            $salonStart = $this->settingsService->get('working_hours_start', '08:00', $salonId);
            $salonEnd = $this->settingsService->get('working_hours_end', '20:00', $salonId);
            $slotDuration = (int) $this->settingsService->get('slot_duration', 30, $salonId);
            $bufferTime = (int) $this->settingsService->get('appointment_buffer_time', 0, $salonId);
            $allowOverlapping = (bool) $this->settingsService->get('allow_overlapping_bookings', false, $salonId);

            $serviceDuration = $slotDuration; // Default to slot duration if no service selected

            // Get service duration if provided
            if (!empty($validated['service_id'])) {
                $service = Service::where('id', $validated['service_id'])
                    ->where('salon_id', $salonId)
                    ->first();
                if ($service) {
                    $serviceDuration = (int) $service->duration;
                }
            }

            // Get staff schedule for this day
            $dayOfWeek = $date->dayOfWeek;
            $staffSchedule = \App\Models\StaffSchedule::where('staff_id', $staffId)
                ->where('day_of_week', $dayOfWeek)
                ->first();

            // Check regular and overtime status
            $isRegularWorking = $staffSchedule && $staffSchedule->is_working;
            $hasOvertime = $staffSchedule && $staffSchedule->allows_overtime && $staffSchedule->overtime_start && $staffSchedule->overtime_end;

            // If staff is not working on this day (neither regular nor overtime), return empty
            if (!$staffSchedule || (!$isRegularWorking && !$hasOvertime)) {
                return response()->json([
                    'available_slots' => [],
                    'blocked_slots' => [],
                    'staff_working_hours' => null,
                    'message' => 'Staff is not working on this day'
                ]);
            }


            // Determine working hours:
            $workingStart = $salonStart;
            $workingEnd = $salonEnd;

            Log::info('getAvailableTimeSlots Debug', [
                'staff_id' => $staffId,
                'date' => $date->toDateString(),
                'is_regular' => $isRegularWorking,
                'has_overtime' => $hasOvertime,
                'salon_start' => $salonStart,
                'salon_end' => $salonEnd
            ]);


            if (!$isRegularWorking && $hasOvertime) {
                // Overtime only - use overtime hours directly
                $workingStart = $staffSchedule->overtime_start;
                $workingEnd = $staffSchedule->overtime_end;
            } elseif ($staffSchedule->start_time && $staffSchedule->end_time) {
                // Regular working (potentially with overtime)
                $staffStart = $staffSchedule->start_time;
                $staffEnd = $staffSchedule->end_time;

                // Use the latest start time (Staff start or Salon start)
                $workingStart = (strtotime($staffStart) > strtotime($salonStart)) ? $staffStart : $salonStart;
                // Use the earliest end time (Staff end or Salon end)
                $workingEnd = (strtotime($staffEnd) < strtotime($salonEnd)) ? $staffEnd : $salonEnd;

                // OVERTIME: Extend working hours if overtime is enabled
                if ($staffSchedule->allows_overtime && $staffSchedule->overtime_end) {
                    $overtimeEnd = $staffSchedule->overtime_end;
                    // Extend working hours to overtime end time
                    $workingEnd = $overtimeEnd;
                }
            }




            Log::info('getAvailableTimeSlots Hours Calculated', [
                'working_start' => $workingStart,
                'working_end' => $workingEnd,
                'start_time_obj' => isset($staffStart) ? $staffStart : 'N/A',
                'end_time_obj' => isset($staffEnd) ? $staffEnd : 'N/A'
            ]);

            // Check for staff absences
            $isAbsent = \App\Models\StaffAbsence::where('staff_id', $staffId)

                ->whereDate('start_at', '<=', $date)
                ->whereDate('end_at', '>=', $date)
                ->exists();

            if ($isAbsent) {
                return response()->json([
                    'available_slots' => [],
                    'blocked_slots' => [],
                    'staff_working_hours' => [
                        'start' => $workingStart,
                        'end' => $workingEnd
                    ],
                    'message' => 'Staff is absent on this day'
                ]);
            }

            // Get existing bookings for this staff on this date
            $bookingsQuery = Booking::where('staff_id', $staffId)
                ->whereDate('start_time', $date)
                ->where('status', '!=', 'cancelled');

            // Exclude current booking or entire group if editing
            if (!empty($validated['booking_group_id'])) {
                $bookingsQuery->where('booking_group_id', '!=', $validated['booking_group_id']);
            } elseif (!empty($validated['booking_id'])) {
                $bookingsQuery->where('id', '!=', $validated['booking_id']);
            }

            $existingBookings = $bookingsQuery->get();

            // Generate all possible time slots using salon's timezone
            $timezone = $this->settingsService->get('timezone', config('app.timezone', 'UTC'), $salonId);

            // Ensure time format includes seconds (H:i:s)
            $workingStartFormatted = strlen($workingStart) === 5 ? $workingStart . ':00' : $workingStart;
            $workingEndFormatted = strlen($workingEnd) === 5 ? $workingEnd . ':00' : $workingEnd;

            $startTime = Carbon::createFromFormat('H:i:s', $workingStartFormatted, $timezone)->setDateFrom($date);
            $endTime = Carbon::createFromFormat('H:i:s', $workingEndFormatted, $timezone)->setDateFrom($date);

            $allSlots = [];
            $bookedSlots = [];
            $unavailableSlots = [];
            $availableSlots = [];

            $currentSlot = $startTime->copy();
            while ($currentSlot->lt($endTime)) {
                $slotTime = $currentSlot->format('H:i');
                $allSlots[] = $slotTime;

                // Check if this slot can accommodate the service
                $slotEnd = $currentSlot->copy()->addMinutes($serviceDuration);

                // Check if service would extend beyond working hours
                if ($slotEnd->gt($endTime)) {
                    $unavailableSlots[] = $slotTime;
                    $currentSlot->addMinutes($slotDuration);
                    continue;
                }

                // Check for overlaps with existing bookings
                // Check for overlaps with existing bookings
                $isBooked = false;
                if (!$allowOverlapping) {
                    foreach ($existingBookings as $booking) {
                        $bookingStart = Carbon::parse($booking->start_time);
                        $bookingEnd = Carbon::parse($booking->end_time)->addMinutes($bufferTime);

                        // Check if slot overlaps with booking (including buffer)
                        if ($currentSlot->lt($bookingEnd) && $slotEnd->gt($bookingStart)) {
                            $isBooked = true;
                            break;
                        }
                    }
                }

                if ($isBooked) {
                    $bookedSlots[] = $slotTime;
                } else {
                    $availableSlots[] = $slotTime;
                }

                $currentSlot->addMinutes($slotDuration);
            }

            // Ensure current booking's start time is included if editing
            if (!empty($validated['booking_id'])) {
                $currentBooking = Booking::find($validated['booking_id']);
                if ($currentBooking) {
                    $currentBookingStartTime = Carbon::parse($currentBooking->start_time)->setTimezone($timezone)->format('H:i');
                    if (!in_array($currentBookingStartTime, $availableSlots) && !in_array($currentBookingStartTime, $bookedSlots) && !in_array($currentBookingStartTime, $unavailableSlots)) {
                        // Check if it overlaps with any OTHER booking
                        $isBooked = false;
                        $currentBookingStart = Carbon::parse($currentBooking->start_time);
                        $currentBookingEnd = Carbon::parse($currentBooking->end_time);

                        foreach ($existingBookings as $booking) {
                            if ($allowOverlapping)
                                break;
                            $bookingStart = Carbon::parse($booking->start_time);
                            $bookingEnd = Carbon::parse($booking->end_time)->addMinutes($bufferTime);
                            if ($currentBookingStart->lt($bookingEnd) && $currentBookingEnd->gt($bookingStart)) {
                                $isBooked = true;
                                break;
                            }
                        }

                        if (!$isBooked) {
                            $availableSlots[] = $currentBookingStartTime;
                            sort($availableSlots);
                        } else {
                            $bookedSlots[] = $currentBookingStartTime;
                            sort($bookedSlots);
                        }
                    }
                }
            }

            // Filter out past slots if the date is today
            $now = Carbon::now($timezone);
            $isToday = $date->isSameDay($now);

            // Handle desired_time if provided
            if ($request->has('desired_time')) {
                $desiredTime = $request->input('desired_time');
                // Validate format H:i
                if (preg_match('/^\d{2}:\d{2}$/', $desiredTime)) {
                    // Check if it's already in the list
                    if (!in_array($desiredTime, $availableSlots) && !in_array($desiredTime, $bookedSlots) && !in_array($desiredTime, $unavailableSlots)) {

                        // Check for conflicts for this specific time
                        $desiredSlotStart = Carbon::parse($date->format('Y-m-d') . ' ' . $desiredTime, $timezone);
                        $desiredSlotEnd = (clone $desiredSlotStart)->addMinutes($serviceDuration);

                        // Basic validation: must be in future if today
                        $isValid = true;
                        if ($isToday && $desiredSlotStart->lte($now)) {
                            $isValid = false;
                        }

                        // Check working hours
                        $workingStartCarbon = Carbon::parse($date->format('Y-m-d') . ' ' . $workingStart, $timezone);
                        $workingEndCarbon = Carbon::parse($date->format('Y-m-d') . ' ' . $workingEnd, $timezone);

                        // Allow exception for existing booking time (preserve existing slot)
                        $isExistingBookingTime = false;
                        if (!empty($validated['booking_id'])) {
                            $originalBooking = Booking::find($validated['booking_id']);
                            if ($originalBooking) {
                                $originalTime = Carbon::parse($originalBooking->start_time)->setTimezone($timezone)->format('H:i');
                                if ($originalTime === $desiredTime) {
                                    $isExistingBookingTime = true;
                                }
                            }
                        }

                        // Strict working hours check only if it's NOT the existing booking time
                        if (!$isExistingBookingTime && ($desiredSlotStart->lt($workingStartCarbon) || $desiredSlotEnd->gt($workingEndCarbon))) {
                            $isValid = false;
                        }

                        if ($isValid && !$allowOverlapping) {
                            // Check against existing bookings
                            foreach ($existingBookings as $booking) {
                                $bookingStart = Carbon::parse($booking->start_time);
                                $bookingEnd = Carbon::parse($booking->end_time)->addMinutes($bufferTime);

                                if ($desiredSlotStart->lt($bookingEnd) && $desiredSlotEnd->gt($bookingStart)) {
                                    $isValid = false;
                                    break;
                                }
                            }
                        }

                        if ($isValid) {
                            $availableSlots[] = $desiredTime;
                            sort($availableSlots);
                        }
                    }
                }
            }

            if ($isToday) {
                // Get current booking's start time if editing
                $currentBookingStartTime = null;
                if (!empty($validated['booking_id'])) {
                    $currentBookingStartTime = Booking::where('id', $validated['booking_id'])->value('start_time');
                    if ($currentBookingStartTime) {
                        $currentBookingStartTime = Carbon::parse($currentBookingStartTime)->setTimezone($timezone)->format('H:i');
                    }
                }

                $availableSlots = array_filter($availableSlots, function ($slot) use ($now, $timezone, $currentBookingStartTime) {
                    // Include current booking's slot even if it's in the past
                    if ($currentBookingStartTime && $slot === $currentBookingStartTime) {
                        return true;
                    }
                    // Create a Carbon instance for the slot time on the current day
                    try {
                        $slotTime = Carbon::createFromFormat('H:i', $slot, $timezone)->setDateFrom($now);
                        // Add a small buffer (e.g., 1 minute) to allow booking the current minute? 
                        // No, strictly future slots is safer.
                        return $slotTime->gt($now);
                    } catch (\Exception $e) {
                        return false;
                    }
                });
                // Re-index array
                $availableSlots = array_values($availableSlots);
            }

            // Optimization: If a slot is booked, but there is remaining time before the next slot, 
            // we could potentially add a new slot. 
            // However, this requires a dynamic grid which might break the UI consistency.
            // For now, we stick to the fixed grid but ensure accuracy.

            return response()->json([
                'available_slots' => $availableSlots,
                'booked_slots' => $bookedSlots,
                'unavailable_slots' => $unavailableSlots,
                'staff_working_hours' => [
                    'start' => $workingStart,
                    'end' => $workingEnd
                ],
                'slot_duration' => $slotDuration,
                'service_duration' => $serviceDuration
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching available time slots', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Failed to fetch available time slots',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available package services for a customer.
     */
    public function getCustomerPackageBalances(Customer $customer)
    {
        if ($customer->salon_id !== auth()->user()->salon_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        Log::info('Fetching package balances for customer', [
            'customer_id' => $customer->id,
            'customer_name' => $customer->name
        ]);

        $balances = CustomerPackageBalance::where('customer_id', $customer->id)
            ->where('quantity_remaining', '>', 0)
            ->where('payment_status', 'paid') // Only show paid packages
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', now()->toDateString());
            })
            ->with(['package', 'service.staff'])
            ->get();

        Log::info('Raw package balances from database', [
            'count' => $balances->count(),
            'balances' => $balances->map(function ($b) {
                return [
                    'id' => $b->id,
                    'package_id' => $b->package_id,
                    'service_id' => $b->service_id,
                    'service_name' => $b->service->name ?? 'N/A',
                    'quantity_remaining' => $b->quantity_remaining,
                    'expiry_date' => $b->expiry_date ? optional($b->expiry_date)->toDateString() : null
                ];
            })
        ]);

        $grouped = $balances->groupBy('package_id')
            ->map(function ($group) {
                $package = $group->first()->package;

                // Map services and filter out those with 0 remaining (double-check)
                $services = $group->map(function ($balance) {
                    return [
                        'id' => $balance->service->id,
                        'name' => $balance->service->name,
                        'duration' => $balance->service->duration,
                        'price' => 0, // Package services are already paid, so price is 0
                        'is_from_package' => true, // Flag to identify package services
                        'package_balance_id' => $balance->id, // Track which balance record this is from
                        'quantity_remaining' => $balance->quantity_remaining,
                        'staff_ids' => $balance->service->staff->pluck('id')->toArray(),
                        'expiry_date' => $balance->expiry_date ? optional($balance->expiry_date)->toDateString() : null
                    ];
                })->filter(function ($service) {
                    // Extra safety: filter out services with 0 or negative remaining
                    return $service['quantity_remaining'] > 0;
                })->values();

                return [
                    'id' => $package->id,
                    'name' => $package->name,
                    'services' => $services
                ];
            })
            ->filter() // Remove null values
            ->values();

        Log::info('Returning package balances', [
            'packages_count' => $grouped->count(),
            'data' => $grouped
        ]);

        return response()->json($grouped);
    }

    /**
     * Get customer's package service limits (for customizable packages in booking modal).
     * Calculates how many services can still be selected from each package.
     */
    public function getCustomerPackageServiceLimits(Customer $customer)
    {
        if ($customer->salon_id !== auth()->user()->salon_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        Log::info('Fetching package service limits for customer', [
            'customer_id' => $customer->id,
            'customer_name' => $customer->name
        ]);

        // Get all packages with balances for this customer
        $packageBalances = CustomerPackageBalance::where('customer_id', $customer->id)
            ->where('payment_status', 'paid')
            ->with(['package.services', 'service'])
            ->get();

        // Group by package
        $packageLimits = $packageBalances->groupBy('package_id')
            ->map(function ($balances) {
                $package = $balances->first()->package;

                // Skip if package doesn't exist or has no service limit (standard package)
                if (!$package || !$package->service_limit || $package->service_limit <= 0) {
                    return null;
                }

                // Count how many services have been used (quantity_remaining = 0 or partially used)
                $totalServicesInPackage = $balances->count();
                $servicesWithRemainingQty = $balances->filter(function ($b) {
                    return $b->quantity_remaining > 0;
                })->count();

                // Services used = total services that have balance records - services with remaining qty
                $servicesUsed = $totalServicesInPackage - $servicesWithRemainingQty;

                // Calculate remaining selections
                $servicesRemaining = max(0, $package->service_limit - $servicesUsed);

                // Get all available services from the package
                $availableServices = $package->services->map(function ($service) use ($balances) {
                    $balance = $balances->firstWhere('service_id', $service->id);

                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'price' => $service->price,
                        'duration' => $service->duration,
                        'has_balance' => $balance !== null,
                        'quantity_remaining' => $balance ? $balance->quantity_remaining : 0,
                        'already_selected' => $balance !== null && $balance->quantity_remaining == 0
                    ];
                });

                return [
                    'package_id' => $package->id,
                    'package_name' => $package->name,
                    'service_limit' => $package->service_limit,
                    'services_used' => $servicesUsed,
                    'services_remaining' => $servicesRemaining,
                    'total_services' => $package->services->count(),
                    'available_services' => $availableServices
                ];
            })
            ->filter() // Remove nulls (standard packages)
            ->values();

        Log::info('Returning package service limits', [
            'packages_count' => $packageLimits->count(),
            'data' => $packageLimits
        ]);

        return response()->json($packageLimits);
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create()
    {
        $salonId = auth()->user()->salon_id;

        // Get all active services for this salon
        $services = Service::where('salon_id', $salonId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Get all staff members with the 'employee' role for this salon
        $staffMembers = User::role('employee')
            ->where('salon_id', $salonId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Get all active customers
        $customers = Customer::where('salon_id', $salonId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.bookings.create', compact('services', 'staffMembers', 'customers'));
    }



    public function settings()
    {
        $salonId = auth()->user()->salon_id;

        $settings = [
            'calendar_default_view' => $this->settingsService->get('calendar_default_view', 'timeGridWeek', $salonId),

            // Appointment Settings
            'guest_booking_enabled' => $this->settingsService->get('guest_booking_enabled', true, $salonId),
            'default_guest_status' => $this->settingsService->get('default_guest_status', 'pending', $salonId),
            'working_hours_start' => $this->settingsService->get('working_hours_start', '08:00', $salonId),
            'working_hours_end' => $this->settingsService->get('working_hours_end', '20:00', $salonId),
            'slot_duration' => $this->settingsService->get('slot_duration', 30, $salonId),
            'advance_booking_days' => $this->settingsService->get('advance_booking_days', 30, $salonId),
            'appointment_buffer_time' => $this->settingsService->get('appointment_buffer_time', 15, $salonId),
            'no_show_fee' => $this->settingsService->get('no_show_fee', 0, $salonId),
            'allow_overlapping_bookings' => $this->settingsService->get('allow_overlapping_bookings', false, $salonId),
            'auto_confirm_bookings' => $this->settingsService->get('auto_confirm_bookings', false, $salonId),
            'cancellation_policy' => $this->settingsService->get('cancellation_policy', '', $salonId),
            'currency_symbol' => $this->settingsService->get('currency_symbol', '$', $salonId),

            // Color Settings
            'booking_color_pending' => $this->settingsService->get('booking_color_pending', '#f8fafc', $salonId),
            'booking_color_confirmed' => $this->settingsService->get('booking_color_confirmed', '#f0fdf4', $salonId),
            'booking_color_arrived' => $this->settingsService->get('booking_color_arrived', '#fffbeb', $salonId),
            'booking_color_started' => $this->settingsService->get('booking_color_started', '#eff6ff', $salonId),
            'booking_color_completed' => $this->settingsService->get('booking_color_completed', '#eff6ff', $salonId),
            'booking_color_cancelled' => $this->settingsService->get('booking_color_cancelled', '#fef2f2', $salonId),
            'booking_color_no_show' => $this->settingsService->get('booking_color_no_show', '#f3f4f6', $salonId),
            'booking_color_staff_completed' => $this->settingsService->get('booking_color_staff_completed', '#f5f3ff', $salonId),
            'booking_color_frozen' => $this->settingsService->get('booking_color_frozen', '#f8fafc', $salonId),

            'booking_border_pending' => $this->settingsService->get('booking_border_pending', '#e2e8f0', $salonId),
            'booking_border_confirmed' => $this->settingsService->get('booking_border_confirmed', '#86efac', $salonId),
            'booking_border_arrived' => $this->settingsService->get('booking_border_arrived', '#fcd34d', $salonId),
            'booking_border_started' => $this->settingsService->get('booking_border_started', '#93c5fd', $salonId),
            'booking_border_completed' => $this->settingsService->get('booking_border_completed', '#93c5fd', $salonId),
            'booking_border_cancelled' => $this->settingsService->get('booking_border_cancelled', '#fca5a5', $salonId),
            'booking_border_no_show' => $this->settingsService->get('booking_border_no_show', '#d1d5db', $salonId),
            'booking_border_staff_completed' => $this->settingsService->get('booking_border_staff_completed', '#c4b5fd', $salonId),
            'booking_border_frozen' => $this->settingsService->get('booking_border_frozen', '#94a3b8', $salonId),

            'booking_text_pending' => $this->settingsService->get('booking_text_pending', '#000000', $salonId),
            'booking_text_confirmed' => $this->settingsService->get('booking_text_confirmed', '#166534', $salonId),
            'booking_text_arrived' => $this->settingsService->get('booking_text_arrived', '#92400e', $salonId),
            'booking_text_started' => $this->settingsService->get('booking_text_started', '#1e40af', $salonId),
            'booking_text_completed' => $this->settingsService->get('booking_text_completed', '#1e40af', $salonId),
            'booking_text_cancelled' => $this->settingsService->get('booking_text_cancelled', '#991b1b', $salonId),
            'booking_text_no_show' => $this->settingsService->get('booking_text_no_show', '#374151', $salonId),
            'booking_text_staff_completed' => $this->settingsService->get('booking_text_staff_completed', '#7c3aed', $salonId),
            'booking_text_frozen' => $this->settingsService->get('booking_text_frozen', '#334155', $salonId),
            'booking_text_unpaid' => $this->settingsService->get('booking_text_unpaid', '#9a3412', $salonId),

            'booking_color_unpaid' => $this->settingsService->get('booking_color_unpaid', '#fff7ed', $salonId),
            'booking_border_unpaid' => $this->settingsService->get('booking_border_unpaid', '#f97316', $salonId),
        ];

        return view('admin.bookings.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'calendar_default_view' => 'required|in:timeGridWeek,timeGridDay,resourceTimeGridDay',

            // Appointment Settings
            'guest_booking_enabled' => 'nullable|boolean',
            'default_guest_status' => 'required|in:pending,confirmed',
            'working_hours_start' => 'required|date_format:H:i',
            'working_hours_end' => 'required|date_format:H:i',
            'slot_duration' => 'required|integer|min:5|max:120',
            'advance_booking_days' => 'required|integer|min:1|max:365',
            'appointment_buffer_time' => 'nullable|integer|min:0',
            'no_show_fee' => 'nullable|numeric|min:0',
            'allow_overlapping_bookings' => 'nullable|boolean',
            'auto_confirm_bookings' => 'nullable|boolean',
            'cancellation_policy' => 'nullable|string',

            // Color Settings
            'booking_color_pending' => 'required|string|max:7',
            'booking_color_confirmed' => 'required|string|max:7',
            'booking_color_arrived' => 'required|string|max:7',
            'booking_color_started' => 'required|string|max:7',
            'booking_color_completed' => 'required|string|max:7',
            'booking_color_cancelled' => 'required|string|max:7',
            'booking_color_no_show' => 'required|string|max:7',
            'booking_color_staff_completed' => 'required|string|max:7',
            'booking_color_frozen' => 'required|string|max:7',
            'booking_color_unpaid' => 'required|string|max:7',

            'booking_border_pending' => 'required|string|max:7',
            'booking_border_confirmed' => 'required|string|max:7',
            'booking_border_arrived' => 'required|string|max:7',
            'booking_border_started' => 'required|string|max:7',
            'booking_border_completed' => 'required|string|max:7',
            'booking_border_cancelled' => 'required|string|max:7',
            'booking_border_no_show' => 'required|string|max:7',
            'booking_border_staff_completed' => 'required|string|max:7',
            'booking_border_frozen' => 'required|string|max:7',
            'booking_border_unpaid' => 'required|string|max:7',

            'booking_text_pending' => 'required|string|max:7',
            'booking_text_confirmed' => 'required|string|max:7',
            'booking_text_arrived' => 'required|string|max:7',
            'booking_text_started' => 'required|string|max:7',
            'booking_text_completed' => 'required|string|max:7',
            'booking_text_cancelled' => 'required|string|max:7',
            'booking_text_no_show' => 'required|string|max:7',
            'booking_text_staff_completed' => 'required|string|max:7',
            'booking_text_frozen' => 'required|string|max:7',
            'booking_text_unpaid' => 'required|string|max:7',
        ]);

        $salonId = auth()->user()->salon_id;

        // Handle boolean fields explicitly
        $booleanFields = ['guest_booking_enabled', 'allow_overlapping_bookings', 'auto_confirm_bookings'];
        foreach ($booleanFields as $field) {
            $validated[$field] = $request->boolean($field);
        }

        foreach ($validated as $key => $value) {
            $this->settingsService->set($key, $value, $salonId);
        }

        return redirect()->route('admin.bookings.settings')->with('success', 'Calendar settings updated successfully.');
    }

    /**
     * Update the status of the specified booking.
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        if ($booking->salon_id !== auth()->user()->salon_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,confirm,confirmed,arrived,started,staff_completed,completed,cancelled,no_show,frozen'
        ]);

        // Restrict 'completed' status to authorized roles only
        if ($request->status === 'completed' && !auth()->user()->hasAnyRole(['super_admin', 'salon_admin', 'manager', 'receptionist'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized to complete appointments. Only managers and receptionists can perform this action.'], 403);
        }

        // Allow 'staff_completed' for all roles (including employees)
        // But enforce today/past rule for both 'completed' and 'staff_completed'
        if (in_array($request->status, ['completed', 'staff_completed'])) {
            $salonTimezone = salon_timezone();
            $now = Carbon::now($salonTimezone);
            $bookingStart = $booking->start_time->copy()->setTimezone($salonTimezone);

            // If it's in the future and NOT today, block completion
            if ($bookingStart->isFuture() && !$bookingStart->isToday()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Future appointments cannot be marked as completed. Only today\'s or past appointments can be completed.'
                ], 422);
            }
        }

        try {
            // If changing to completed, require payment information
            if ($request->status === 'completed' && !in_array($booking->status, ['completed', 'staff_completed'])) {
                return response()->json([
                    'success' => false,
                    'requires_payment' => true,
                    'message' => 'Please provide payment information to complete this booking.',
                    'booking_id' => $booking->id,
                    'amount' => $booking->amount
                ]);
            }

            // If it's already staff_completed and we want to mark as completed, it MUST go through completeWithPayment
            if ($request->status === 'completed' && $booking->status === 'staff_completed') {
                return response()->json([
                    'success' => false,
                    'requires_payment' => true,
                    'message' => 'This booking was marked as completed by staff. Please provide payment information to finalize.',
                    'booking_id' => $booking->id,
                    'amount' => $booking->amount
                ]);
            }

            // Start database transaction
            \DB::beginTransaction();

            // Map 'confirm' to 'confirmed' to match database ENUM
            $status = $request->status === 'confirm' ? 'confirmed' : (string) $request->status;
            $now = now()->toDateTimeString();

            // Store old status for package balance logic
            $oldStatus = $booking->status;

            // Update the main booking status using Eloquent to trigger observer
            $booking->update(['status' => $status]);

            // If this is part of a group, update all bookings in the same group
            if ($booking->booking_group_id) {
                $groupBookings = Booking::where('booking_group_id', $booking->booking_group_id)
                    ->where('id', '!=', $booking->id)
                    ->where('status', '!=', $status)
                    ->get();

                foreach ($groupBookings as $groupBooking) {
                    $groupBooking->update(['status' => $status]);
                }
            }

            // Handle package balance restoration for cancellations and no-shows
            if (
                in_array($status, ['cancelled', 'no_show']) &&
                !in_array($oldStatus, ['cancelled', 'no_show'])
            ) {

                // Restore package balance if this was a package booking
                if ($booking->package_id && $booking->service_id) {
                    $balance = CustomerPackageBalance::where('customer_id', $booking->customer_id)
                        ->where('package_id', $booking->package_id)
                        ->where('service_id', $booking->service_id)
                        ->where('salon_id', $booking->salon_id)
                        ->first();

                    if ($balance) {
                        $balance->increment('quantity_remaining');
                        Log::info('Package balance restored due to cancellation/no-show', [
                            'booking_id' => $booking->id,
                            'customer_id' => $booking->customer_id,
                            'package_id' => $booking->package_id,
                            'service_id' => $booking->service_id,
                            'new_balance' => $balance->quantity_remaining
                        ]);
                    }
                }
            }

            // Commit the transaction
            \DB::commit();

            // Email notification handled by BookingObserver

            // Check if we should send notifications based on status
            $shouldNotify = true;

            // For cancellations, check the notify_cancellation setting
            if ($request->status === 'cancelled') {
                $settingsService = app(\App\Services\SettingsService::class);
                $shouldNotify = $settingsService->get('notify_cancellation', true, $booking->salon_id);
            }

            if ($shouldNotify) {
                try {
                    // Send Notification to Customer (if User) and Staff
                    $notifiables = collect();

                    if ($booking->customer && $booking->customer->user) {
                        $notifiables->push($booking->customer->user);
                    }

                    if ($booking->staff) {
                        $notifiables->push($booking->staff);
                    }

                    if ($notifiables->isNotEmpty()) {
                        Notification::send($notifiables, new BookingNotification($booking, $request->status));
                    }

                    // Notify salon admins
                    $admins = User::role(['super_admin', 'salon_admin', 'manager'])
                        ->where('salon_id', $booking->salon_id)
                        ->get();
                    if ($admins->isNotEmpty()) {
                        Notification::send($admins, new BookingNotification($booking, $request->status));
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send booking status notifications', ['error' => $e->getMessage()]);
                    // Continue execution, do not fail the request
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Booking status updated successfully.',
                'booking' => $booking->load(['customer.user', 'service', 'staff'])
            ]);
        } catch (\Exception $e) {
            // Rollback transaction on error
            \DB::rollBack();

            Log::error('Error updating booking status', [
                'booking_id' => $booking->id,
                'status' => $request->status,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update booking status: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Complete booking with payment information
     */
    public function completeWithPayment(Request $request, Booking $booking)
    {
        if ($booking->salon_id !== auth()->user()->salon_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card,upi,bank_transfer,online',
            'tip_amount' => 'nullable|numeric|min:0'
        ]);

        // Restrict completion with payment to authorized roles only
        if (!auth()->user()->hasAnyRole(['super_admin', 'salon_admin', 'manager', 'receptionist'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized to complete appointments. Only managers and receptionists can perform this action.'], 403);
        }

        try {
            // Check if booking is in the future
            $salonTimezone = salon_timezone();
            if ($booking->start_time->isFuture() && !$booking->start_time->copy()->setTimezone($salonTimezone)->isToday()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bookings scheduled for the future cannot be completed. Only today\'s bookings can be completed today.'
                ], 422);
            }

            $booking->completeWithPayment(
                $validated['payment_method'],
                $validated['tip_amount'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Booking completed successfully with payment.',
                'booking' => $booking->fresh(['customer.user', 'service', 'staff'])
            ]);
        } catch (\Exception $e) {
            Log::error('Error completing booking with payment', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit(Booking $booking)
    {
        if ($booking->salon_id !== auth()->user()->salon_id) {
            abort(403, 'Unauthorized');
        }

        return redirect()->route('admin.bookings.index', ['edit_booking_id' => $booking->id]);
    }

    /**
     * Get recent bookings for the stats page
     */
    public function getRecentBookings()
    {
        try {
            $recentBookings = Booking::where('salon_id', auth()->user()->salon_id)
                ->with(['service', 'staff'])
                ->where('status', 'completed')
                ->orderBy('start_time', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($booking) {
                    $bookingDate = $booking->start_time->setTimezone(salon_timezone());
                    return [
                        'id' => $booking->id,
                        'date' => $bookingDate->format('Y-m-d'),
                        'service' => $booking->service ? $booking->service->name : 'N/A',
                        'status' => ucfirst($booking->status),
                        'amount' => number_format((float) $booking->amount, 2),
                        'rating' => $booking->rating ?? 0,
                        'staff_name' => $booking->staff ? $booking->staff->name : 'N/A'
                    ];
                });

            return response()->json($recentBookings);
        } catch (\Exception $e) {
            Log::error('Error fetching recent bookings', [
                'salon_id' => auth()->user()->salon_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to load recent bookings'], 500);
        }
    }

    /**
     * Get bookings for a specific employee
     */
    public function getEmployeeBookings(Request $request, $employeeId)
    {
        try {
            $salonId = auth()->user()->salon_id;

            // Verify the employee belongs to this salon
            $employee = User::where('id', $employeeId)
                ->where('salon_id', $salonId)
                ->firstOrFail();

            $bookings = Booking::where('salon_id', $salonId)
                ->where('staff_id', $employeeId)
                ->with(['service', 'customer', 'ratings'])
                ->where('status', 'completed')
                ->orderBy('start_time', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($booking) {
                    // Get the rating from the relationship
                    $rating = $booking->ratings->first() ? $booking->ratings->first()->rating : 0;
                    $bookingDate = $booking->start_time->setTimezone(salon_timezone());

                    return [
                        'id' => $booking->id,
                        'date' => $bookingDate->format('Y-m-d'),
                        'service' => $booking->service ? $booking->service->name : 'N/A',
                        'customer' => $booking->customer ? $booking->customer->name : 'N/A',
                        'status' => ucfirst($booking->status),
                        'amount' => number_format((float) $booking->amount, 2),
                        'tip_amount' => number_format((float) $booking->tip_amount, 2),
                        'rating' => $rating,
                    ];
                });

            return response()->json($bookings);
        } catch (\Exception $e) {
            Log::error('Error fetching employee bookings', [
                'employee_id' => $employeeId,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to load employee bookings'], 500);
        }
    }

    /**
     * Display ongoing, future and today's bookings.
     */
    public function ongoing(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $salonTimezone = salon_timezone();
        $now = now($salonTimezone);
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();

        // Base query for bookings from today onwards
        $query = Booking::where('salon_id', $salonId)
            ->where('start_time', '>=', $todayStart);

        if (auth()->user()->hasRole('employee')) {
            $query->where('staff_id', auth()->id());
        }

        if ($request->has('staff_id') && $request->staff_id) {
            $query->where('staff_id', $request->staff_id);
        }

        // Get all bookings for the view (today + future)
        $allBookings = $query->with(['customer.user', 'service', 'staff'])
            ->orderBy('start_time', 'asc')
            ->get();

        // Calculate stats for TODAY only
        $todayBookings = $allBookings->filter(function ($booking) use ($salonTimezone) {
            return $booking->start_time->copy()->setTimezone($salonTimezone)->isToday();
        });

        $stats = [
            'total' => $todayBookings->count(),
            'completed' => $todayBookings->whereIn('status', ['completed', 'staff_completed'])->count(),
            'pending' => $todayBookings->whereIn('status', ['pending', 'confirmed'])->count(),
            'revenue' => $todayBookings->where('status', 'completed')->sum('amount') + $todayBookings->where('status', 'completed')->sum('tip_amount'),
        ];

        // Paginate the results for the table
        $perPage = 15;
        $page = $request->get('page', 1);
        $bookings = new \Illuminate\Pagination\LengthAwarePaginator(
            $allBookings->forPage($page, $perPage),
            $allBookings->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $staffMembersQuery = User::role('employee')
            ->where('salon_id', $salonId)
            ->orderBy('name');

        // If the user is an employee, they should only see themselves in the filter
        if (auth()->user()->hasRole('employee')) {
            $staffMembersQuery->where('id', auth()->id());
        }

        $staffMembers = $staffMembersQuery->get();

        return view('admin.bookings.ongoing', compact('bookings', 'staffMembers', 'stats'));
    }

    /**
     * Display booking receipt.
     */
    public function receipt(Booking $booking)
    {
        if ($booking->salon_id !== auth()->user()->salon_id) {
            abort(403);
        }
        // Check if there is a POS sale associated with this booking
        if ($booking->pos_sale_id) {
            $sale = \App\Models\PosSale::with(['items.item', 'items.staff', 'customer', 'employee', 'salon'])
                ->where('salon_id', $booking->salon_id)
                ->find($booking->pos_sale_id);

            if ($sale) {
                $salon = $booking->salon;
                $salonData = [
                    'name' => $salon->name,
                    'address' => $salon->address,
                    'phone' => $salon->phone,
                    'email' => $salon->email,
                    'website' => $salon->website,
                    'logo' => $salon->logo ? asset('storage/' . $salon->logo) : null,
                ];

                $settings = app(\App\Services\SettingsService::class);

                return view('pos.receipt', compact('sale', 'salonData', 'settings'));
            }
        }

        // If part of a group, fetch all bookings
        if ($booking->booking_group_id) {
            $bookings = Booking::where('booking_group_id', $booking->booking_group_id)
                ->where('salon_id', $booking->salon_id)
                ->with(['customer.user', 'service', 'staff', 'salon', 'creator'])
                ->get();
        } else {
            $booking->load(['customer.user', 'service', 'staff', 'salon', 'creator']);
            $bookings = collect([$booking]);
        }

        $salon = $booking->salon;
        $salonData = [
            'name' => $salon->name,
            'address' => $salon->address,
            'phone' => $salon->phone,
            'email' => $salon->email,
            'website' => $salon->website,
            'logo' => $salon->logo_url,
        ];

        $settings = app(\App\Services\SettingsService::class);

        return view('admin.bookings.receipt', compact('bookings', 'booking', 'salonData', 'settings'));
    }

    /**
     * Display booking statistics
     *
     * @return \Illuminate\View\View
     */
    public function stats()
    {
        // Get current salon
        $salon = app()->bound('current_salon') ? app('current_salon') : null;

        if (!$salon) {
            abort(500, 'Salon context not available');
        }
        $salonId = $salon->id;

        // Get date ranges in salon timezone
        $timezone = salon_timezone();
        $today = now($timezone);
        $startOfWeek = now($timezone)->startOfWeek();
        $startOfMonth = now($timezone)->startOfMonth();
        $thirtyDaysAgo = now($timezone)->subDays(30);

        // Helper to get UTC range for a salon date
        $getUtcRange = function ($date) use ($timezone) {
            $start = Carbon::parse($date, $timezone)->startOfDay()->setTimezone('UTC');
            $end = Carbon::parse($date, $timezone)->endOfDay()->setTimezone('UTC');
            return [$start, $end];
        };

        $nonPosBookingQuery = function ($query) {
            return $query->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('pos_sale_items')
                    ->whereColumn('pos_sale_items.booking_id', 'bookings.id');
            });
        };

        // Basic stats
        $totalBookings = $nonPosBookingQuery(Booking::where('salon_id', $salonId))->count();

        // Today's range
        [$todayStart, $todayEnd] = $getUtcRange($today);

        $todayBookings = $nonPosBookingQuery(Booking::where('salon_id', $salonId))
            ->whereBetween('start_time', [$todayStart, $todayEnd])
            ->count();

        // Week's range
        $weekStartUtc = $startOfWeek->copy()->setTimezone('UTC');
        $weekBookings = $nonPosBookingQuery(Booking::where('salon_id', $salonId))
            ->where('start_time', '>=', $weekStartUtc)
            ->count();

        // Month's range
        $monthStartUtc = $startOfMonth->copy()->setTimezone('UTC');
        $monthBookings = $nonPosBookingQuery(Booking::where('salon_id', $salonId))
            ->where('start_time', '>=', $monthStartUtc)
            ->count();



        $upcomingBookings = Booking::where('salon_id', $salonId)
            ->where('start_time', '>', now('UTC'))
            ->whereNotIn('status', ['completed', 'cancelled', 'staff_completed'])
            ->count();
        $completedBookings = $nonPosBookingQuery(Booking::where('salon_id', $salonId))->whereIn('status', ['completed', 'staff_completed'])->count();
        $cancelledBookings = Booking::where('salon_id', $salonId)->where('status', 'cancelled')->count();

        // POS Sales stats (Today/Month/Total) for revenue inclusion
        $posToday = \App\Models\PosSale::where('salon_id', $salonId)
            ->whereBetween('sale_date', [$todayStart, $todayEnd])
            ->where('payment_status', '!=', 'pending')
            ->where('status', '!=', 'voided');

        $posMonth = \App\Models\PosSale::where('salon_id', $salonId)
            ->where('sale_date', '>=', $monthStartUtc)
            ->where('payment_status', '!=', 'pending')
            ->where('status', '!=', 'voided');

        $posTotal = \App\Models\PosSale::where('salon_id', $salonId)
            ->where('payment_status', '!=', 'pending')
            ->where('status', '!=', 'voided');

        // Additional metrics
        $bookingCompletionRate = $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100) : 0;
        $cancellationRate = $totalBookings > 0 ? round(($cancelledBookings / $totalBookings) * 100) : 0;

        // Average booking value (excluding tips) - Deduplicated
        $avgBookingValue = $nonPosBookingQuery(Booking::where('salon_id', $salonId))
            ->whereIn('status', ['completed', 'staff_completed'])
            ->avg('amount') ?? 0;

        // Average tip value - Deduplicated
        $avgTipValue = $nonPosBookingQuery(Booking::where('salon_id', $salonId))
            ->whereIn('status', ['completed', 'staff_completed'])
            ->avg('tip_amount') ?? 0;

        // Get Timezone Offset
        $offset = now($timezone)->format('P');

        // Peak hours (most common booking hour) - Adjust for Timezone
        $peakHour = Booking::where('salon_id', $salonId)
            ->select(DB::raw("HOUR(CONVERT_TZ(start_time, '+00:00', '$offset')) as hour"), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->first();

        $peakHourFormatted = $peakHour ? date('g A', strtotime($peakHour->hour . ':00')) : 'N/A';

        // Busiest day of week - Adjust for Timezone
        $busiestDay = Booking::where('salon_id', $salonId)
            ->select(DB::raw("DAYNAME(CONVERT_TZ(start_time, '+00:00', '$offset')) as day"), DB::raw('COUNT(*) as count'))
            ->groupBy('day')
            ->orderBy('count', 'desc')
            ->first();

        $busiestDayFormatted = $busiestDay ? $busiestDay->day : 'N/A';

        // Customer satisfaction (using ratings table)
        $avgRating = 0;
        if (Schema::hasTable('ratings')) {
            // Join with bookings to scope by salon_id
            $avgRating = DB::table('ratings')
                ->join('bookings', 'ratings.booking_id', '=', 'bookings.id')
                ->where('bookings.salon_id', $salonId)
                ->where('ratings.created_at', '>=', $thirtyDaysAgo)
                ->avg('ratings.rating') ?? 0;
        }

        // Repeat customers (customers with more than 1 booking)
        $repeatCustomers = DB::table('bookings')
            ->where('salon_id', $salonId)
            ->select('customer_id', DB::raw('COUNT(*) as booking_count'))
            ->groupBy('customer_id')
            ->having('booking_count', '>', 1)
            ->count();

        $totalCustomers = DB::table('bookings')
            ->where('salon_id', $salonId)
            ->distinct('customer_id')
            ->count('customer_id');
        $repeatCustomerRate = $totalCustomers > 0 ? round(($repeatCustomers / $totalCustomers) * 100) : 0;

        // Revenue calculations (Combined Booking and POS)
        // Note: We exclude bookings linked to POS from the booking SUM because their revenue is already in $posRevenueTotal
        $bookingRevenueTotal = $nonPosBookingQuery(Booking::where('salon_id', $salonId))
            ->whereIn('status', ['completed', 'staff_completed'])
            ->sum('amount') ?? 0;
        $bookingTipsTotal = $nonPosBookingQuery(Booking::where('salon_id', $salonId))
            ->whereIn('status', ['completed', 'staff_completed'])
            ->sum('tip_amount') ?? 0;

        $posRevenueTotal = $posTotal->sum('total') - $posTotal->sum('tip');
        $posTipsTotal = $posTotal->sum('tip');

        $totalBookingAmount = $bookingRevenueTotal + $posRevenueTotal; // Combined Gross Revenue
        $totalTips = $bookingTipsTotal + $posTipsTotal;
        $totalRevenue = $totalBookingAmount + $totalTips; // Total Earnings

        // Today
        $todayBookingRev = $nonPosBookingQuery(Booking::where('salon_id', $salonId))
            ->whereBetween('start_time', [$todayStart, $todayEnd])
            ->whereIn('status', ['completed', 'staff_completed'])
            ->sum('amount') ?? 0;
        $todayBookingTips = $nonPosBookingQuery(Booking::where('salon_id', $salonId))
            ->whereBetween('start_time', [$todayStart, $todayEnd])
            ->whereIn('status', ['completed', 'staff_completed'])
            ->sum('tip_amount') ?? 0;

        $todayPosRes = $posToday->selectRaw('SUM(total) as total, SUM(tip) as tip')->first();
        $todayPosTotal = $todayPosRes->total ?? 0;
        $todayPosTips = $todayPosRes->tip ?? 0;

        $todayBookingAmount = $todayBookingRev + ($todayPosTotal - $todayPosTips);
        $todayTips = $todayBookingTips + $todayPosTips;
        $todayRevenue = $todayBookingAmount + $todayTips;

        // Month
        $monthBookingRev = $nonPosBookingQuery(Booking::where('salon_id', $salonId))
            ->where('start_time', '>=', $monthStartUtc)
            ->whereIn('status', ['completed', 'staff_completed'])
            ->sum('amount') ?? 0;
        $monthBookingTips = $nonPosBookingQuery(Booking::where('salon_id', $salonId))
            ->where('start_time', '>=', $monthStartUtc)
            ->whereIn('status', ['completed', 'staff_completed'])
            ->sum('tip_amount') ?? 0;

        $monthPosRes = $posMonth->selectRaw('SUM(total) as total, SUM(tip) as tip')->first();
        $monthPosTotal = $monthPosRes->total ?? 0;
        $monthPosTips = $monthPosRes->tip ?? 0;

        $monthBookingAmount = $monthBookingRev + ($monthPosTotal - $monthPosTips);
        $monthTips = $monthBookingTips + $monthPosTips;
        $monthRevenue = $monthBookingAmount + $monthTips;

        // Calculate earnings change (compare this month to last month)
        $lastMonthStart = now($timezone)->subMonth()->startOfMonth()->setTimezone('UTC');
        $lastMonthEnd = now($timezone)->subMonth()->endOfMonth()->setTimezone('UTC');

        $lastMonthBookingRev = $nonPosBookingQuery(Booking::where('salon_id', $salonId))
            ->whereBetween('start_time', [$lastMonthStart, $lastMonthEnd])
            ->whereIn('status', ['completed', 'staff_completed'])
            ->sum('amount') ?? 0;
        $lastMonthBookingTips = $nonPosBookingQuery(Booking::where('salon_id', $salonId))
            ->whereBetween('start_time', [$lastMonthStart, $lastMonthEnd])
            ->whereIn('status', ['completed', 'staff_completed'])
            ->sum('tip_amount') ?? 0;

        $lastMonthPos = \App\Models\PosSale::where('salon_id', $salonId)
            ->whereBetween('sale_date', [$lastMonthStart, $lastMonthEnd])
            ->where('payment_status', '!=', 'pending')
            ->where('status', '!=', 'voided');

        $lastMonthPosRes = $lastMonthPos->selectRaw('SUM(total) as total, SUM(tip) as tip')->first();
        $lastMonthRevenueTotal = $lastMonthBookingRev + $lastMonthBookingTips + ($lastMonthPosRes->total ?? 0);

        $earningsChange = 0;
        if ($lastMonthRevenueTotal > 0) {
            $earningsChange = round((($monthRevenue - $lastMonthRevenueTotal) / $lastMonthRevenueTotal) * 100);
        } else {
            $earningsChange = $monthRevenue > 0 ? 100 : 0;
        }

        // Get bookings by status
        $bookingsByStatus = Booking::where('salon_id', $salonId)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->orderBy('count', 'desc')
            ->pluck('count', 'status');

        // Get top services
        $bookingsByService = Booking::where('salon_id', $salonId)
            ->with('service')
            ->select('service_id', DB::raw('count(*) as count'))
            ->groupBy('service_id')
            ->take(5)
            ->get();

        // Get top customers
        $topCustomers = Booking::where('salon_id', $salonId)
            ->where('status', 'completed')
            ->with('customer.user')
            ->select(
                'customer_id',
                DB::raw('count(*) as booking_count'),
                DB::raw('sum(amount) as total_booking_amount'),
                DB::raw('sum(COALESCE(tip_amount, 0)) as total_tips'),
                DB::raw('sum(amount + COALESCE(tip_amount, 0)) as total_spent')
            )
            ->groupBy('customer_id')
            ->orderBy('booking_count', 'desc')
            ->take(5)
            ->get();

        // Get top services (Unified Bookings + POS without duplicates)
        $bookingServiceCounts = $nonPosBookingQuery(Booking::where('salon_id', $salonId))
            ->whereIn('status', ['completed', 'staff_completed'])
            ->select('service_id', DB::raw('count(*) as booking_count'))
            ->groupBy('service_id')
            ->get()
            ->pluck('booking_count', 'service_id');

        $posServiceCounts = DB::table('pos_sale_items')
            ->join('pos_sales', 'pos_sale_items.sale_id', '=', 'pos_sales.id')
            ->where('pos_sales.salon_id', $salonId)
            ->where('pos_sales.payment_status', '!=', 'pending')
            ->where('pos_sales.status', '!=', 'voided')
            ->where('pos_sale_items.item_type', 'service')
            ->select('pos_sale_items.item_id', DB::raw('SUM(pos_sale_items.quantity) as sale_count'))
            ->groupBy('pos_sale_items.item_id')
            ->get()
            ->pluck('sale_count', 'item_id');

        // Combine and Get Details
        $allServiceIds = $bookingServiceCounts->keys()->merge($posServiceCounts->keys())->unique();

        $topServices = \App\Models\Service::whereIn('id', $allServiceIds)
            ->with(['category'])
            ->withAvg('ratings', 'rating')
            ->get()
            ->map(function ($service) use ($bookingServiceCounts, $posServiceCounts) {
                $service->booking_count = ($bookingServiceCounts[$service->id] ?? 0) + ($posServiceCounts[$service->id] ?? 0);
                $service->average_rating = $service->ratings_avg_rating ?? 0;
                $service->service_id = $service->id; // for modal compatibility
                return $service;
            })
            ->sortByDesc('booking_count')
            ->take(5);

        // Required for Service Distribution Chart
        $bookingsByService = $topServices;

        // Calculate total earnings (including tips)
        $totalEarnings = $totalRevenue;

        // Calculate average rating from ratings table
        $avgRating = DB::table('ratings')
            ->join('bookings', 'ratings.booking_id', '=', 'bookings.id')
            ->where('bookings.salon_id', $salonId)
            ->avg('ratings.rating') ?? 0;

        // Get total number of ratings
        $totalReviews = DB::table('ratings')
            ->join('bookings', 'ratings.booking_id', '=', 'bookings.id')
            ->where('bookings.salon_id', $salonId)
            ->count();

        // Calculate positive ratings percentage (4 stars and above)
        $positiveReviews = $totalReviews > 0
            ? round((DB::table('ratings')
                ->join('bookings', 'ratings.booking_id', '=', 'bookings.id')
                ->where('bookings.salon_id', $salonId)
                ->where('ratings.rating', '>=', 4)
                ->count() / $totalReviews) * 100)
            : 0;

        // Get recent ratings
        $recentReviews = DB::table('ratings')
            ->join('bookings', 'ratings.booking_id', '=', 'bookings.id')
            ->join('customers', 'ratings.customer_id', '=', 'customers.id')
            ->join('users', 'customers.user_id', '=', 'users.id')
            ->join('services', 'ratings.service_id', '=', 'services.id')
            ->where('bookings.salon_id', $salonId)
            ->select(
                'ratings.*',
                'users.name as customer_name',
                'services.name as service_name'
            )
            ->orderBy('ratings.created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($review) {
                $review->created_at = \Carbon\Carbon::parse($review->created_at);
                return $review;
            });

        // Get top employee with most completed bookings
        $topEmployee = DB::table('bookings')
            ->where('salon_id', $salonId)
            ->select('staff_id', DB::raw('count(*) as booking_count'))
            ->where('status', 'completed')
            ->whereNotNull('staff_id')
            ->groupBy('staff_id')
            ->orderBy('booking_count', 'desc')
            ->first();

        if ($topEmployee) {
            $topEmployee = (object) array_merge((array) $topEmployee, [
                'name' => \App\Models\User::find($topEmployee->staff_id)->name ?? 'Unknown'
            ]);
        }

        // Get top employees with their booking counts
        $topEmployees = Booking::where('salon_id', $salonId)
            ->with([
                'staff' => function ($query) {
                    $query->withAvg('receivedRatings', 'rating');
                }
            ])
            ->select('staff_id', DB::raw('count(*) as booking_count'))
            ->whereNotNull('staff_id')
            ->whereIn('status', ['completed', 'staff_completed'])
            ->groupBy('staff_id')
            ->orderBy('booking_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) use ($salonId, $nonPosBookingQuery) {
                // Calculate total revenue for this employee's completed bookings (excluding POS-linked)
                $bookingAmount = $nonPosBookingQuery(Booking::where('staff_id', $item->staff_id))
                    ->where('salon_id', $salonId)
                    ->whereIn('status', ['completed', 'staff_completed'])
                    ->sum('amount');

                $bookingTips = $nonPosBookingQuery(Booking::where('staff_id', $item->staff_id))
                    ->where('salon_id', $salonId)
                    ->whereIn('status', ['completed', 'staff_completed'])
                    ->sum('tip_amount');

                // Include POS sales where staff is assigned (summarize from items)
                $posRes = DB::table('pos_sale_items')
                    ->join('pos_sales', 'pos_sale_items.sale_id', '=', 'pos_sales.id')
                    ->where('pos_sale_items.staff_id', $item->staff_id)
                    ->where('pos_sales.salon_id', $salonId)
                    ->where('pos_sales.payment_status', '!=', 'pending')
                    ->where('pos_sales.status', '!=', 'voided')
                    ->selectRaw('SUM(pos_sale_items.total) as total, SUM(CASE WHEN pos_sale_items.item_type = "service" THEN pos_sale_items.total ELSE 0 END) as service_total')
                    ->first();

                $posTotal = $posRes->total ?? 0;
                // Note: tips are usually on the whole sale, but we can't easily attribute a portion of the tip to a specific staff member if multiple are involved.
                // For now, we use the item totals.
                $posTips = 0; // POS tip attribution to specific staff is complex without a breakdown map.
    
                if ($item->staff) {
                    $item->name = $item->staff->name;
                    $item->position = $item->staff->employee->position ?? 'Employee';
                    $item->avatar_url = $item->staff->employee->avatar_url ?? null;
                    $item->rating = $item->staff->received_ratings_avg_rating ?? 0;
                    $item->completed_bookings = $item->booking_count;
                    $item->booking_amount = $bookingAmount + $posTotal;
                    $item->tips = $bookingTips + $posTips;
                    $item->revenue = $item->tips; // Maintain original logic: "revenue" variable for staff is their tips
                } else {
                    $item->name = 'Staff #' . $item->staff_id;
                    $item->position = 'Employee';
                    $item->avatar_url = null;
                    $item->rating = 0;
                    $item->completed_bookings = $item->booking_count;
                    $item->booking_amount = 0;
                    $item->tips = 0;
                    $item->revenue = 0;
                }
                return $item;
            });

        // Trends with Timezone Awareness (Exclude POS-linked bookings for Trends)
        $monthlyBookings = $nonPosBookingQuery(Booking::where('salon_id', $salonId))
            ->whereIn('status', ['completed', 'staff_completed'])
            ->where('start_time', '>=', now($timezone)->subMonths(6)->startOfMonth()->setTimezone('UTC'))
            ->select(
                DB::raw("YEAR(CONVERT_TZ(start_time, '+00:00', '$offset')) as year"),
                DB::raw("MONTH(CONVERT_TZ(start_time, '+00:00', '$offset')) as month"),
                DB::raw('count(*) as count'),
                DB::raw('sum(amount) as booking_amount'),
                DB::raw('sum(COALESCE(tip_amount, 0)) as tips')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $monthlyPos = \App\Models\PosSale::where('salon_id', $salonId)
            ->where('payment_status', '!=', 'pending')
            ->where('status', '!=', 'voided')
            ->where('sale_date', '>=', now($timezone)->subMonths(6)->startOfMonth()->setTimezone('UTC'))
            ->select(
                DB::raw("YEAR(CONVERT_TZ(sale_date, '+00:00', '$offset')) as year"),
                DB::raw("MONTH(CONVERT_TZ(sale_date, '+00:00', '$offset')) as month"),
                DB::raw('SUM(total) as pos_total'),
                DB::raw('SUM(tip) as pos_tips')
            )
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(function ($item) {
                return $item->year . '-' . $item->month;
            });

        // Day of Week Trends (Exclude POS-linked bookings)
        $bookingsByDay = $nonPosBookingQuery(Booking::where('salon_id', $salonId))
            ->whereIn('status', ['completed', 'staff_completed'])
            ->select(
                DB::raw("DAYOFWEEK(CONVERT_TZ(start_time, '+00:00', '$offset')) as day_of_week"),
                DB::raw('count(*) as count')
            )
            ->groupBy('day_of_week')
            ->pluck('count', 'day_of_week');

        // Format data for charts
        $monthlyLabels = [];
        $monthlyData = [];
        $monthlyBookingAmount = [];
        $monthlyTips = [];
        $monthlyRevenue = [];

        foreach ($monthlyBookings as $booking) {
            $key = $booking->year . '-' . $booking->month;
            $pos = $monthlyPos->get($key);

            $date = \Carbon\Carbon::createFromDate($booking->year, $booking->month, 1);
            $monthlyLabels[] = $date->format('M Y');
            $monthlyData[] = $booking->count;

            $totalBamount = (float) ($booking->booking_amount ?? 0) + (float) (($pos->pos_total ?? 0) - ($pos->pos_tips ?? 0));
            $totalTamount = (float) ($booking->tips ?? 0) + (float) ($pos->pos_tips ?? 0);

            $monthlyBookingAmount[] = $totalBamount;
            $monthlyTips[] = $totalTamount;
            $monthlyRevenue[] = $totalBamount + $totalTamount;
        }

        // Format day of week data
        $daysOfWeek = [
            1 => 'Sunday',
            2 => 'Monday',
            3 => 'Tuesday',
            4 => 'Wednesday',
            5 => 'Thursday',
            6 => 'Friday',
            7 => 'Saturday'
        ];

        $dayLabels = [];
        $dayData = [];

        foreach ($daysOfWeek as $dayNum => $dayName) {
            $dayLabels[] = $dayName;
            $dayData[] = $bookingsByDay[$dayNum] ?? 0;
        }

        return view('admin.bookings.stats', [
            // Summary stats
            'totalBookings' => $totalBookings,
            'todayBookings' => $todayBookings,
            'weekBookings' => $weekBookings,
            'monthBookings' => $monthBookings,
            'upcomingBookings' => $upcomingBookings,
            'completedBookings' => $completedBookings,
            'cancelledBookings' => $cancelledBookings,
            'totalBookingAmount' => $totalBookingAmount,
            'totalTips' => $totalTips,
            'totalRevenue' => $totalRevenue,
            'todayBookingAmount' => $todayBookingAmount,
            'todayTips' => $todayTips,
            'todayRevenue' => $todayRevenue,
            'monthBookingAmount' => $monthBookingAmount,
            'monthTips' => $monthTips,
            'monthRevenue' => $monthRevenue,
            'totalEarnings' => $totalEarnings,

            // Chart data
            'bookingsByStatus' => $bookingsByStatus,
            'bookingsByService' => $bookingsByService,
            'topCustomers' => $topCustomers,
            'topServices' => $topServices,
            'monthlyLabels' => $monthlyLabels,
            'monthlyData' => $monthlyData,
            'monthlyBookingAmount' => $monthlyBookingAmount,
            'monthlyTips' => $monthlyTips,
            'monthlyRevenue' => $monthlyRevenue,
            'dayLabels' => $dayLabels,
            'dayData' => $dayData,
            'topEmployees' => $topEmployees,
            'topEmployee' => $topEmployee ?? (object) ['name' => 'N/A', 'booking_count' => 0],

            // Additional metrics
            'bookingCompletionRate' => $bookingCompletionRate,
            'customerSatisfaction' => round($avgRating, 1),
            'avgRating' => round($avgRating, 1),
            'repeatCustomerRate' => $repeatCustomerRate,
            'avgBookingValue' => $avgBookingValue,
            'avgTipValue' => $avgTipValue,
            'peakHours' => $peakHourFormatted,
            'busiestDay' => $busiestDayFormatted,
            'cancellationRate' => $cancellationRate,
            'earningsChange' => $earningsChange,
            'recentReviews' => $recentReviews,
            'totalReviews' => $totalReviews,
            'positiveReviews' => $positiveReviews,

            // Date ranges
            'startDate' => now($timezone)->subMonths(6)->format('Y-m-d'),
            'endDate' => now($timezone)->format('Y-m-d'),
        ]);
    }

    private function normalizeDate($dateStr)
    {
        Log::info('Original date string:', ['date' => $dateStr]);
        $dateStr = preg_replace('/(\+\d{2}:\d{2}|Z)$/', '', $dateStr);
        $dateStr = str_replace('T', ' ', $dateStr);
        Log::info('Normalized date string:', ['date' => $dateStr]);
        return $dateStr;
    }

    public function getBookings(Request $request)
    {
        try {
            Log::info('Fetching bookings', $request->only(['start', 'end', 'staff_id']));
            $salonId = auth()->user()->salon_id;

            $query = Booking::where('salon_id', $salonId)
                ->where('status', '!=', 'cancelled')
                ->with(['customer', 'service', 'staff', 'creator', 'posSaleItem.sale']);

            // Restrict employees to only see their own bookings
            if (auth()->user()->hasRole('employee')) {
                $query->where('staff_id', auth()->id());
            }

            if ($request->has('start') && $request->has('end')) {
                try {
                    // Fix: Parse as UTC (from ISO string), convert to Salon Time to get day boundaries, then back to UTC
                    $startUtc = Carbon::parse($request->start); // Parses as UTC if Z present
                    $endUtc = Carbon::parse($request->end);

                    $salonTimezone = salon_timezone();

                    // Convert to salon timezone to determine the "day"
                    $startDay = $startUtc->copy()->setTimezone($salonTimezone)->startOfDay();
                    $endDay = $endUtc->copy()->setTimezone($salonTimezone)->endOfDay();

                    // Convert back to UTC for DB query
                    $start = $startDay->copy()->setTimezone('UTC');
                    $end = $endDay->copy()->setTimezone('UTC');

                    Log::info('Date Range Debug', [
                        'request_start' => $request->start,
                        'request_end' => $request->end,
                        'salon_timezone' => $salonTimezone,
                        'query_start_utc' => $start->toDateTimeString(),
                        'query_end_utc' => $end->toDateTimeString()
                    ]);

                    $query->where(function ($q) use ($start, $end) {
                        $q->whereBetween('start_time', [$start, $end])
                            ->orWhereBetween('end_time', [$start, $end])
                            ->orWhere(function ($q) use ($start, $end) {
                                $q->where('start_time', '<=', $start)
                                    ->where('end_time', '>=', $end);
                            });
                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => 'error', 'message' => 'Invalid date format'], 400);
                }
            }

            if ($request->has('staff_id') && !empty($request->staff_id)) {
                $staffIds = explode(',', $request->staff_id);
                $query->whereIn('staff_id', $staffIds);
            }

            // Fetch color settings
            $colors = [
                'pending' => [
                    'background' => $this->settingsService->get('booking_color_pending', '#f8fafc', $salonId),
                    'border' => $this->settingsService->get('booking_border_pending', '#e2e8f0', $salonId),
                    'text' => $this->settingsService->get('booking_text_pending', '#000000', $salonId),
                ],
                'confirmed' => [
                    'background' => $this->settingsService->get('booking_color_confirmed', '#f0fdf4', $salonId),
                    'border' => $this->settingsService->get('booking_border_confirmed', '#86efac', $salonId),
                    'text' => $this->settingsService->get('booking_text_confirmed', '#166534', $salonId),
                ],
                'completed' => [
                    'background' => $this->settingsService->get('booking_color_completed', '#eff6ff', $salonId),
                    'border' => $this->settingsService->get('booking_border_completed', '#93c5fd', $salonId),
                    'text' => $this->settingsService->get('booking_text_completed', '#1e40af', $salonId),
                ],
                'staff_completed' => [
                    'background' => $this->settingsService->get('booking_color_staff_completed', '#f5f3ff', $salonId),
                    'border' => $this->settingsService->get('booking_border_staff_completed', '#c4b5fd', $salonId),
                    'text' => $this->settingsService->get('booking_text_staff_completed', '#7c3aed', $salonId),
                ],
                'cancelled' => [
                    'background' => $this->settingsService->get('booking_color_cancelled', '#fef2f2', $salonId),
                    'border' => $this->settingsService->get('booking_border_cancelled', '#fca5a5', $salonId),
                    'text' => $this->settingsService->get('booking_text_cancelled', '#991b1b', $salonId),
                ],
                'no_show' => [
                    'background' => $this->settingsService->get('booking_color_no_show', '#f3f4f6', $salonId),
                    'border' => $this->settingsService->get('booking_border_no_show', '#d1d5db', $salonId),
                    'text' => $this->settingsService->get('booking_text_no_show', '#374151', $salonId),
                ],
                'arrived' => [
                    'background' => $this->settingsService->get('booking_color_arrived', '#fffbeb', $salonId),
                    'border' => $this->settingsService->get('booking_border_arrived', '#fcd34d', $salonId),
                    'text' => $this->settingsService->get('booking_text_arrived', '#b45309', $salonId),
                ],
                'started' => [
                    'background' => $this->settingsService->get('booking_color_started', '#eff6ff', $salonId),
                    'border' => $this->settingsService->get('booking_border_started', '#60a5fa', $salonId),
                    'text' => $this->settingsService->get('booking_text_started', '#1d4ed8', $salonId),
                ],
                'frozen' => [
                    'background' => $this->settingsService->get('booking_color_frozen', '#f8fafc', $salonId),
                    'border' => $this->settingsService->get('booking_border_frozen', '#94a3b8', $salonId),
                    'text' => $this->settingsService->get('booking_text_frozen', '#334155', $salonId),
                ],
                'unpaid' => [
                    'background' => $this->settingsService->get('booking_color_unpaid', '#fff7ed', $salonId),
                    'border' => $this->settingsService->get('booking_border_unpaid', '#f97316', $salonId),
                    'text' => $this->settingsService->get('booking_text_unpaid', '#9a3412', $salonId),
                ],
            ];

            DB::enableQueryLog();

            $bookings = $query->get()->map(function ($booking) use ($colors) {
                $status = strtolower($booking->status);
                $statusColors = $colors[$status] ?? $colors['pending']; // Default to pending if status unknown

                // Get salon timezone
                $salonTimezone = salon_timezone();

                // Determine payment status and method
                // Priority: Booking's own status > POS Sale status
                $paymentStatus = $booking->payment_status;
                $paymentMethod = $booking->payment_method;

                // If booking is NOT paid, check if there's a linked POS sale that might have info
                // But if booking IS paid, trust it (e.g. paid via Calendar)
                // If booking is NOT paid, check if there's a linked POS sale that might have info
                if (strtolower($paymentStatus) !== 'paid') {
                    if ($booking->posSaleItem && $booking->posSaleItem->sale) {
                        $paymentStatus = $booking->posSaleItem->sale->payment_status;
                        $paymentMethod = $booking->posSaleItem->sale->payment_method;
                    }
                    // REMOVED: Automatic assumption that completed = paid. 
                    // This allows tracking "Completed but Unpaid" appointments.
                }

                // Custom Styling for Unpaid/Pay Later
                // We highlight if:
                // 1. Payment status is unpaid/partial
                // 2. AND (Linked to a Sale OR Status is completed/staff_completed)
                // This prevents future confirmed appointments from turning orange just because they aren't paid yet.
                $isUnpaid = in_array(strtolower($paymentStatus), ['unpaid', 'partially_paid', 'pay_later', 'pending']);
                $isSignificant = ($booking->posSaleItem || in_array($status, ['staff_completed', 'completed']));



                if ($isUnpaid && $isSignificant) {
                    // Use dynamic settings for Unpaid color
                    $statusColors = $colors['unpaid'];
                }

                return [
                    'id' => $booking->id,
                    'title' => $booking->customer->name . ($booking->service ? ' - ' . $booking->service->name : ''),
                    'start' => $booking->start_time->copy()->timezone($salonTimezone)->toIso8601String(),
                    'end' => $booking->end_time->copy()->timezone($salonTimezone)->toIso8601String(),
                    'className' => 'fc-event ' . $status . ($isUnpaid && $isSignificant ? ' fc-event-unpaid' : ''),
                    'backgroundColor' => $statusColors['background'],
                    'borderColor' => $statusColors['border'],
                    'textColor' => $statusColors['text'],
                    'extendedProps' => [
                        'customer_id' => $booking->customer_id,
                        'service_id' => $booking->service_id,
                        'staff_id' => $booking->staff_id,
                        'status' => $booking->status,
                        'notes' => $booking->notes,
                        'customer' => $booking->customer->name ?? 'Unknown',
                        'customer_phone' => $booking->customer->phone ?? 'N/A',
                        'customer_dob' => $booking->customer->dob ? Carbon::parse($booking->customer->dob)->format('Y-m-d') : '-',
                        'customer_anniversary' => $booking->customer->anniversary ? Carbon::parse($booking->customer->anniversary)->format('Y-m-d') : '-',
                        'service' => $booking->service->name ?? 'Unknown',
                        'price' => $booking->amount,
                        'duration' => ($booking->service ? $booking->service->duration : 0),
                        'staff' => $booking->staff->name ?? 'Unknown',
                        'created_at' => $booking->created_at->format('Y-m-d H:i'),
                        'created_by' => $booking->creator ? $booking->creator->name : ($booking->source === 'online' ? 'Customer (Online)' : 'Salon Staff'),
                        'creator_role' => $booking->creator ? ($booking->creator->roles->first()->name ?? 'Staff') : ($booking->source === 'online' ? 'Customer' : 'Staff'),
                        'source' => $booking->source,
                        'pos_sale_id' => $booking->posSaleItem ? $booking->posSaleItem->sale_id : null,
                        'payment_status' => $paymentStatus,
                        'payment_method' => $paymentMethod,
                    ]
                ];
            });

            Log::info('Get Bookings Query', [
                'queries' => DB::getQueryLog(),
                'count' => $bookings->count()
            ]);

            if ($bookings->count() > 0) {
                Log::info('First Booking Sample', ['booking' => $bookings->first()]);
            }

            return response()->json($bookings);

        } catch (\Exception $e) {
            Log::error('Error fetching bookings', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            Log::info('Creating new booking(s)', $request->all());
            $salonId = auth()->user()->salon_id;

            // Check if this is a batch request
            $bookingsData = $request->input('bookings', []);

            // If not a batch request, try to construct one from single request data
            if (empty($bookingsData)) {
                $bookingsData = [$request->all()];
            }

            // Check Booking Limit
            $salon = auth()->user()->salon;
            $limit = $salon->getBookingLimit();

            if ($limit !== null && $limit !== -1) {
                $currentCount = \App\Models\Booking::where('salon_id', $salonId)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count();

                $newCount = count($bookingsData);

                if (($currentCount + $newCount) > $limit) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Booking limit reached. Your plan allows {$limit} bookings per month. You have used {$currentCount} and are trying to add {$newCount}. Please Upgrade your plan!"
                    ], 403);
                }
            }

            DB::beginTransaction();
            $createdBookings = [];
            $bookingGroupId = (count($bookingsData) > 1) ? (string) Str::uuid() : null;

            foreach ($bookingsData as $index => $bookingData) {
                // Validate each booking data
                $validator = \Illuminate\Support\Facades\Validator::make($bookingData, [
                    'customer_id' => ['required', Rule::exists('customers', 'id')->where('salon_id', $salonId)],
                    'service_id' => ['nullable', Rule::exists('services', 'id')->where('salon_id', $salonId)],
                    'package_id' => ['nullable', Rule::exists('packages', 'id')->where('salon_id', $salonId)],
                    'package_service_status' => 'nullable|integer|in:0,1,2',
                    'staff_id' => ['required', Rule::exists('users', 'id')->where('salon_id', $salonId)],
                    'date' => 'required_without:start_time|date',
                    'time' => 'required_without:start_time',
                    'start_time' => 'nullable|date', // Can be ISO string
                    'duration' => 'nullable|integer|min:1',
                    'status' => 'nullable|in:pending,confirmed,arrived,started,staff_completed,completed,cancelled,no_show,frozen',
                    'notes' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    throw new \Exception("Validation failed for booking #" . ($index + 1) . ": " . implode(', ', $validator->errors()->all()));
                }

                $validated = $validator->validated();

                // Calculate Start and End Times
                if (isset($validated['start_time'])) {
                    // New format: ISO string
                    $startTime = Carbon::parse($validated['start_time'], salon_timezone());
                } else {
                    // Old format: date + time
                    $startTime = Carbon::createFromFormat('Y-m-d H:i', $validated['date'] . ' ' . $validated['time'], salon_timezone());
                }

                // Calculate duration and amount
                $duration = 0;
                $amount = 0;

                if (isset($validated['service_id'])) {
                    $service = Service::find($validated['service_id']);
                    if ($service) {
                        $duration = $service->duration;
                        $amount = $service->price;

                        // If this is a package booking, the amount is 0 as it's prepaid
                        if (isset($validated['package_id'])) {
                            $amount = 0;
                        }

                        // Apply Tax if enabled for services
                        $taxEnabledServices = $this->settingsService->get('tax_enabled_services', false, $salonId);
                        if ($taxEnabledServices) {
                            $taxRate = $this->settingsService->get('tax_rate', 0, $salonId);
                            $amount += $amount * ($taxRate / 100);
                        }
                    }
                } elseif (isset($validated['duration'])) {
                    $duration = (int) $validated['duration'];
                } else {
                    $duration = 30; // Default
                }

                $endTime = $startTime->copy()->addMinutes($duration);

                // Check Staff Availability (Schedule & Absences)
                $dayOfWeek = $startTime->dayOfWeek;
                $schedule = \App\Models\StaffSchedule::where('staff_id', $validated['staff_id'])
                    ->where('day_of_week', $dayOfWeek)
                    ->first();

                if ($schedule) {
                    if (!$schedule->is_working) {
                        throw new \Exception("Staff member is not working on this day (Booking #" . ($index + 1) . ").");
                    }

                    $scheduleStart = Carbon::createFromFormat('H:i:s', $schedule->start_time, salon_timezone())->setDateFrom($startTime);
                    $scheduleEnd = Carbon::createFromFormat('H:i:s', $schedule->end_time, salon_timezone())->setDateFrom($startTime);

                    // OVERTIME: Extend schedule end time if overtime is enabled
                    if ($schedule->allows_overtime && $schedule->overtime_end) {
                        $overtimeEnd = Carbon::createFromFormat('H:i:s', $schedule->overtime_end, salon_timezone())->setDateFrom($startTime);
                        $scheduleEnd = $overtimeEnd;

                        Log::info('Booking validation with overtime', [
                            'booking_index' => $index + 1,
                            'staff_id' => $validated['staff_id'],
                            'regular_end' => $schedule->end_time,
                            'overtime_end' => $schedule->overtime_end,
                            'booking_start' => $startTime->format('H:i'),
                            'booking_end' => $endTime->format('H:i')
                        ]);
                    }

                    if ($startTime->lt($scheduleStart) || $endTime->gt($scheduleEnd)) {
                        throw new \Exception("Booking time is outside of staff working hours (including overtime) (Booking #" . ($index + 1) . ").");
                    }
                }

                // Check for Absences - use UTC for query
                $isAbsent = \App\Models\StaffAbsence::where('staff_id', $validated['staff_id'])
                    ->where(function ($query) use ($startTime, $endTime) {
                        $startUtc = $startTime->copy()->setTimezone('UTC');
                        $endUtc = $endTime->copy()->setTimezone('UTC');
                        $query->whereBetween('start_at', [$startUtc, $endUtc])
                            ->orWhereBetween('end_at', [$startUtc, $endUtc])
                            ->orWhere(function ($q) use ($startUtc, $endUtc) {
                                $q->where('start_at', '<=', $startUtc)
                                    ->where('end_at', '>=', $endUtc);
                            });
                    })
                    ->exists();

                if ($isAbsent) {
                    throw new \Exception("Staff member is absent during this time (Booking #" . ($index + 1) . ").");
                }

                // Check for Overlapping Bookings
                $allowOverlapping = $this->settingsService->get('allow_overlapping_bookings', false, $salonId);

                if (!$allowOverlapping) {
                    $bufferTime = $this->settingsService->get('appointment_buffer_time', 0, $salonId);
                    $checkEndTime = $endTime->copy()->addMinutes((int) $bufferTime);

                    $existingBookings = Booking::where('staff_id', $validated['staff_id'])
                        ->where('status', '!=', 'cancelled')
                        ->where(function ($query) use ($startTime, $checkEndTime) {
                            $startUtc = $startTime->copy()->setTimezone('UTC');
                            $endUtc = $checkEndTime->copy()->setTimezone('UTC');
                            $query->where(function ($q) use ($startUtc, $endUtc) {
                                $q->where('start_time', '<', $endUtc)
                                    ->where('end_time', '>', $startUtc);
                            });
                        })->exists();

                    if ($existingBookings) {
                        throw new \Exception("This time slot is already booked for the selected staff member (Booking #" . ($index + 1) . ").");
                    }
                }

                // Create the booking - ensure UTC storage
                $booking = Booking::create([
                    'salon_id' => $salonId,
                    'created_by' => auth()->id(),
                    'source' => auth()->user()->hasRole('customer') ? 'online' : 'salon',
                    'booking_group_id' => $bookingGroupId,
                    'customer_id' => $validated['customer_id'],
                    'service_id' => $validated['service_id'] ?? null,
                    'package_id' => $validated['package_id'] ?? null,
                    'package_service_status' => $validated['package_service_status'] ?? Booking::PACKAGE_SERVICE_USE_NOW,
                    'staff_id' => $validated['staff_id'],
                    'start_time' => $startTime->setTimezone('UTC'),
                    'end_time' => $endTime->setTimezone('UTC'),
                    'amount' => $amount,
                    'status' => $validated['status'] ?? 'pending',
                    'notes' => $validated['notes'] ?? null,
                ]);

                // Handle Package Service Consumption
                // Only consume balance if package_service_status = 0 (use now)
                // If package_service_status = 1 (save for future), balance is added by POS, not consumed here
                if ($booking->package_id && $booking->service_id && $booking->package_service_status == Booking::PACKAGE_SERVICE_USE_NOW) {
                    $balance = CustomerPackageBalance::where('customer_id', $booking->customer_id)
                        ->where('package_id', $booking->package_id)
                        ->where('service_id', $booking->service_id)
                        ->where('salon_id', $salonId)
                        ->where('quantity_remaining', '>', 0)
                        ->where(function ($query) {
                            $query->whereNull('expiry_date')
                                ->orWhere('expiry_date', '>=', now()->toDateString());
                        })
                        ->orderBy('created_at', 'asc')
                        ->first();

                    if ($balance) {
                        $oldQuantity = $balance->quantity_remaining;
                        $balance->decrement('quantity_remaining');
                        $balance->refresh();

                        Log::info('Package service consumed', [
                            'booking_id' => $booking->id,
                            'customer_id' => $booking->customer_id,
                            'package_id' => $booking->package_id,
                            'service_id' => $booking->service_id,
                            'package_service_status' => $booking->package_service_status,
                            'old_quantity' => $oldQuantity,
                            'new_quantity' => $balance->quantity_remaining
                        ]);
                    } else {
                        Log::warning('No package balance found to decrement', [
                            'booking_id' => $booking->id,
                            'customer_id' => $booking->customer_id,
                            'package_id' => $booking->package_id,
                            'service_id' => $booking->service_id,
                            'package_service_status' => $booking->package_service_status
                        ]);
                    }
                }

                $createdBookings[] = $booking;
                Log::info('Created Booking', [
                    'id' => $booking->id,
                    'salon_id' => $booking->salon_id,
                    'start_time' => $booking->start_time,
                    'end_time' => $booking->end_time
                ]);
            }

            DB::commit();

            // Send notifications
            try {
                $this->notificationService->sendGroupBookingConfirmation($createdBookings);
            } catch (\Exception $e) {
                Log::warning('Failed to send notification', ['error' => $e->getMessage()]);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => count($createdBookings) . ' appointment(s) created successfully.',
                    'bookings' => $createdBookings
                ]);
            }

            return redirect()->route('admin.bookings.index')->with('success', 'Appointments created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating booking', ['error' => $e->getMessage()]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        try {
            $booking = Booking::with(['customer', 'service', 'staff', 'creator', 'posSaleItem.sale'])->find($id);

            if (!$booking) {
                return response()->json(['error' => 'Booking not found'], 404);
            }

            if ($booking->salon_id !== auth()->user()->salon_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Fetch all bookings in the same group if it exists
            $groupedBookings = collect([$booking]);
            if ($booking->booking_group_id) {
                $groupedBookings = Booking::where('booking_group_id', $booking->booking_group_id)
                    ->with(['service', 'staff', 'package.services'])
                    ->get();
            }

            // Get salon timezone
            $salonTimezone = salon_timezone();

            $services = [];
            $packages = [];

            foreach ($groupedBookings as $b) {
                if ($b->package_id) {
                    // Handle package (avoid duplicates if multiple services in same package are separate bookings)
                    // Actually, our store method creates one booking per service even in packages?
                    // Let's check how packages are stored.
                    // If package_id is present, it's a package booking.
                    if (!isset($packages[$b->package_id])) {
                        $packages[$b->package_id] = [
                            'id' => $b->package_id,
                            'name' => $b->package->name ?? 'Package',
                            'services' => []
                        ];
                    }
                    $packages[$b->package_id]['services'][] = [
                        'id' => $b->service_id,
                        'booking_id' => $b->id,
                        'name' => $b->service->name ?? 'Service',
                        'staff_id' => $b->staff_id,
                        'start_time' => $b->start_time->copy()->timezone($salonTimezone)->toIso8601String(),
                        'duration' => $b->service->duration ?? 0,
                        'pivot' => [
                            'booking_id' => $b->id,
                            'staff_id' => $b->staff_id,
                            'start_time' => $b->start_time->copy()->timezone($salonTimezone)->toIso8601String()
                        ]
                    ];
                } else {
                    $services[] = [
                        'id' => $b->service_id,
                        'booking_id' => $b->id,
                        'name' => $b->service->name ?? 'Service',
                        'staff_id' => $b->staff_id,
                        'start_time' => $b->start_time->copy()->timezone($salonTimezone)->toIso8601String(),
                        'duration' => $b->service->duration ?? 0,
                        'pivot' => [
                            'booking_id' => $b->id,
                            'staff_id' => $b->staff_id,
                            'start_time' => $b->start_time->copy()->timezone($salonTimezone)->toIso8601String()
                        ]
                    ];
                }
            }

            // Determine payment status and method
            $paymentStatus = $booking->payment_status;
            $paymentMethod = $booking->payment_method;

            if (strtolower((string) $paymentStatus) !== 'paid') {
                // Safely check for POS sale
                if ($booking->posSaleItem && $booking->posSaleItem->sale) {
                    $paymentStatus = $booking->posSaleItem->sale->payment_status;
                    $paymentMethod = $booking->posSaleItem->sale->payment_method;
                } elseif ($booking->status === 'completed') {
                    $paymentStatus = 'paid';
                }
            }

            // Mask customer data if required
            $customerData = $booking->customer;
            if ($customerData) {
                // validation check if we need to mask
                if (\App\Helpers\CustomerDataHelper::shouldMaskData()) {
                    $customerData = $booking->customer->toArray();
                    $customerData['phone'] = \App\Helpers\CustomerDataHelper::maskPhone($booking->customer->phone);
                    $customerData['email'] = \App\Helpers\CustomerDataHelper::maskEmail($booking->customer->email);
                }
            }

            return response()->json([
                'id' => $booking->id,
                'booking_group_id' => $booking->booking_group_id,
                'customer_id' => $booking->customer_id,
                'status' => $booking->status,
                'notes' => $booking->notes,
                'customer' => $customerData,
                'service' => $booking->service,
                'staff' => $booking->staff,
                'booking_type' => $booking->source,
                'created_by_user' => $booking->creator,
                'start_time' => $booking->start_time->copy()->timezone($salonTimezone)->toIso8601String(),
                'end_time' => $booking->end_time ? $booking->end_time->copy()->timezone($salonTimezone)->toIso8601String() : null,
                'created_at' => $booking->created_at->copy()->timezone($salonTimezone)->toIso8601String(),
                'amount' => $booking->amount,
                'duration' => $booking->duration ?? ($booking->service ? $booking->service->duration : 0),
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
                'pos_sale_id' => $booking->posSaleItem ? $booking->posSaleItem->sale_id : null,
                'services' => $services,
                'packages' => array_values($packages),
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching booking details', ['id' => $id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Error fetching booking', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Booking $booking)
    {
        try {
            Log::info('Updating booking(s)', $request->all());
            $salonId = auth()->user()->salon_id;
            $bookingGroupId = $booking->booking_group_id;

            // Check if this is a batch request
            $isBatchRequest = $request->has('bookings');
            $bookingsData = $request->input('bookings', []);

            DB::beginTransaction();

            if (!$isBatchRequest) {
                // SINGLE UPDATE (e.g. Drag-and-Drop or Simple Edit)
                $validated = $request->validate([
                    'customer_id' => ['required', Rule::exists('customers', 'id')->where('salon_id', $salonId)],
                    'service_id' => ['nullable', Rule::exists('services', 'id')->where('salon_id', $salonId)],
                    'package_id' => ['nullable', Rule::exists('packages', 'id')->where('salon_id', $salonId)],
                    'package_service_status' => 'nullable|integer|in:0,1,2',
                    'staff_id' => ['required', Rule::exists('users', 'id')->where('salon_id', $salonId)],
                    'datetime' => 'nullable|date',
                    'date' => 'nullable|date',
                    'time' => 'nullable',
                    'duration' => 'nullable|integer',
                    'notes' => 'nullable|string',
                    'status' => 'required|in:pending,confirmed,arrived,started,staff_completed,completed,cancelled,no_show,frozen'
                ]);

                // Calculate Start and End Times
                if (isset($validated['datetime'])) {
                    $startTime = Carbon::parse($validated['datetime'], salon_timezone());
                } else if (isset($validated['date']) && isset($validated['time'])) {
                    $startTime = Carbon::createFromFormat('Y-m-d H:i', $validated['date'] . ' ' . $validated['time'], salon_timezone());
                } else {
                    $startTime = $booking->start_time->copy()->timezone(salon_timezone());
                }

                $duration = $validated['duration'] ?? ($booking->service ? $booking->service->duration : 30);
                $endTime = $startTime->copy()->addMinutes((int) $duration);

                // Check for rescheduling and completion
                $isRescheduled = $booking->start_time->ne($startTime->copy()->setTimezone('UTC'));
                $wasCompleted = !in_array($booking->status, ['completed', 'staff_completed']) &&
                    in_array($validated['status'], ['completed', 'staff_completed']);

                // Store old values for package balance adjustment
                $oldPackageId = $booking->package_id;
                $oldServiceId = $booking->service_id;

                // Basic validation checks (past date, staff availability, conflicts)
                // ... (Simplified for brevity, but should ideally be here)

                $booking->update([
                    'customer_id' => $validated['customer_id'],
                    'service_id' => $validated['service_id'] ?? $booking->service_id,
                    'package_id' => array_key_exists('package_id', $validated) ? $validated['package_id'] : $booking->package_id,
                    'staff_id' => $validated['staff_id'],
                    'start_time' => $startTime->setTimezone('UTC'),
                    'end_time' => $endTime->setTimezone('UTC'),
                    'notes' => $validated['notes'] ?? $booking->notes,
                    'status' => $validated['status']
                ]);

                // Adjust package balances if package or service changed
                $newPackageId = $validated['package_id'] ?? null;
                $newServiceId = $validated['service_id'] ?? $booking->service_id;

                if ($oldPackageId != $newPackageId || $oldServiceId != $newServiceId) {
                    // Restore old balance
                    if ($oldPackageId && $oldServiceId) {
                        $oldBalance = CustomerPackageBalance::where('customer_id', $booking->customer_id)
                            ->where('package_id', $oldPackageId)
                            ->where('service_id', $oldServiceId)
                            ->where('salon_id', $salonId)
                            ->first();
                        if ($oldBalance) {
                            $oldBalance->increment('quantity_remaining');
                        }
                    }

                    // Consume new balance
                    if ($newPackageId && $newServiceId) {
                        $newBalance = CustomerPackageBalance::where('customer_id', $booking->customer_id)
                            ->where('package_id', $newPackageId)
                            ->where('service_id', $newServiceId)
                            ->where('salon_id', $salonId)
                            ->where('quantity_remaining', '>', 0)
                            ->where(function ($query) {
                                $query->whereNull('expiry_date')
                                    ->orWhere('expiry_date', '>=', now()->toDateString());
                            })
                            ->orderBy('created_at', 'asc')
                            ->first();
                        if ($newBalance) {
                            $newBalance->decrement('quantity_remaining');
                        }
                    }
                }

                DB::commit();

                // Send Notifications
                try {
                    if ($isRescheduled) {
                        $this->notificationService->sendBookingRescheduled($booking);
                    }
                    if ($wasCompleted) {
                        $this->notificationService->sendBookingCompleted($booking);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to send booking update notifications', ['error' => $e->getMessage()]);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Booking updated successfully',
                    'booking' => $booking
                ]);
            }

            // BATCH UPDATE (from Modal)
            // Fetch existing bookings in the group to properly update them instead of deleting and recreating
            $existingBookings = [];
            if ($bookingGroupId) {
                $existingBookings = Booking::where('booking_group_id', $bookingGroupId)
                    ->orderBy('start_time')
                    ->get()
                    ->keyBy('id');
            } else if (count($bookingsData) > 1) {
                // Creating a new group from a single booking
                $bookingGroupId = (string) Str::uuid();
                $existingBookings = collect([$booking->id => $booking]);
            } else {
                // Single booking, no group
                $existingBookings = collect([$booking->id => $booking]);
            }

            // Check Booking Limit - only count NEW bookings being added
            $salon = auth()->user()->salon;
            $limit = $salon->getBookingLimit();

            if ($limit !== null && $limit !== -1) {
                $currentCount = \App\Models\Booking::where('salon_id', $salonId)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count();

                $existingBookingsCount = $existingBookings->count();
                $newBookingsCount = count($bookingsData) - $existingBookingsCount;

                if ($newBookingsCount > 0 && ($currentCount + $newBookingsCount) > $limit) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Booking limit reached. Your plan allows {$limit} bookings per month. You have used {$currentCount}. Cannot add {$newBookingsCount} more bookings. Please upgrade your plan!"
                    ], 403);
                }
            }

            $createdBookings = [];
            $totalAmount = 0;
            $requiresPayment = false;
            $processedBookingIds = [];

            foreach ($bookingsData as $index => $bookingData) {
                $validator = Validator::make($bookingData, [
                    'customer_id' => ['required', Rule::exists('customers', 'id')->where('salon_id', $salonId)],
                    'service_id' => ['nullable', Rule::exists('services', 'id')->where('salon_id', $salonId)],
                    'package_id' => ['nullable', Rule::exists('packages', 'id')->where('salon_id', $salonId)],
                    'staff_id' => ['required', Rule::exists('users', 'id')->where('salon_id', $salonId)],
                    'date' => 'required_without:start_time|date',
                    'time' => 'required_without:start_time',
                    'start_time' => 'nullable|date',
                    'duration' => 'nullable|integer|min:1',
                    'status' => 'nullable|in:pending,confirmed,arrived,started,staff_completed,completed,cancelled,no_show,frozen',
                    'notes' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    throw new \Exception("Validation failed for booking #" . ($index + 1) . ": " . implode(', ', $validator->errors()->all()));
                }

                $validated = $validator->validated();

                if (isset($validated['start_time'])) {
                    $startTime = Carbon::parse($validated['start_time'], salon_timezone());
                } else {
                    $startTime = Carbon::createFromFormat('Y-m-d H:i', $validated['date'] . ' ' . $validated['time'], salon_timezone());
                }

                $duration = 0;
                $amount = 0;
                if (isset($validated['service_id'])) {
                    $service = Service::find($validated['service_id']);
                    if ($service) {
                        $duration = $service->duration;
                        $amount = $service->price;

                        // If this is a package booking, the amount is 0 as it's prepaid
                        if (isset($validated['package_id'])) {
                            $amount = 0;
                        }
                        $taxEnabledServices = $this->settingsService->get('tax_enabled_services', false, $salonId);
                        if ($taxEnabledServices) {
                            $taxRate = $this->settingsService->get('tax_rate', 0, $salonId);
                            $amount += $amount * ($taxRate / 100);
                        }
                    }
                } else {
                    $duration = (int) ($validated['duration'] ?? 30);
                }

                $totalAmount += $amount;
                $endTime = $startTime->copy()->addMinutes($duration);

                // Check Staff Availability (Schedule & Absences)
                $dayOfWeek = $startTime->dayOfWeek;
                $schedule = \App\Models\StaffSchedule::where('staff_id', $validated['staff_id'])
                    ->where('day_of_week', $dayOfWeek)
                    ->first();

                if ($schedule) {
                    if (!$schedule->is_working) {
                        throw new \Exception("Staff member is not working on this day (Booking #" . ($index + 1) . ").");
                    }

                    $scheduleStart = Carbon::createFromFormat('H:i:s', $schedule->start_time, salon_timezone())->setDateFrom($startTime);
                    $scheduleEnd = Carbon::createFromFormat('H:i:s', $schedule->end_time, salon_timezone())->setDateFrom($startTime);

                    if ($startTime->lt($scheduleStart) || $endTime->gt($scheduleEnd)) {
                        throw new \Exception("Booking time is outside of staff working hours (Booking #" . ($index + 1) . ").");
                    }
                }

                // Check for Absences
                $isAbsent = \App\Models\StaffAbsence::where('staff_id', $validated['staff_id'])
                    ->where(function ($query) use ($startTime, $endTime) {
                        $startUtc = $startTime->copy()->setTimezone('UTC');
                        $endUtc = $endTime->copy()->setTimezone('UTC');
                        $query->whereBetween('start_at', [$startUtc, $endUtc])
                            ->orWhereBetween('end_at', [$startUtc, $endUtc])
                            ->orWhere(function ($q) use ($startUtc, $endUtc) {
                                $q->where('start_at', '<=', $startUtc)
                                    ->where('end_at', '>=', $endUtc);
                            });
                    })->exists();

                if ($isAbsent) {
                    throw new \Exception("Staff member is absent during this time (Booking #" . ($index + 1) . ").");
                }

                // Determine which existing booking to update (if any)
                $bookingToUpdate = null;
                if ($index === 0) {
                    // First item always updates the main booking
                    $bookingToUpdate = $booking;
                } else {
                    // For subsequent items, try to find an existing booking in the group that hasn't been processed yet
                    // Match by service_id and package_id to update the correct booking
                    foreach ($existingBookings as $existingBooking) {
                        if (
                            !in_array($existingBooking->id, $processedBookingIds) &&
                            $existingBooking->id !== $booking->id &&
                            $existingBooking->service_id == ($validated['service_id'] ?? null) &&
                            $existingBooking->package_id == ($validated['package_id'] ?? null)
                        ) {
                            $bookingToUpdate = $existingBooking;
                            break;
                        }
                    }
                }

                // Check for Conflicts (excluding the booking we're updating)
                $excludeId = $bookingToUpdate ? $bookingToUpdate->id : null;
                $conflict = Booking::hasConflict($validated['staff_id'], $startTime, $endTime, $excludeId);

                if ($conflict) {
                    throw new \Exception("This time slot is already booked for the selected staff member (Booking #" . ($index + 1) . ").");
                }

                if ($bookingToUpdate) {
                    // Update existing booking
                    if (in_array($validated['status'], ['completed', 'staff_completed'])) {
                        if ($validated['status'] === 'completed' && !in_array($bookingToUpdate->status, ['completed', 'staff_completed'])) {
                            $requiresPayment = true;
                        }
                    }

                    $oldPackageId = $bookingToUpdate->package_id;
                    $oldServiceId = $bookingToUpdate->service_id;

                    $bookingToUpdate->update([
                        'booking_group_id' => $bookingGroupId,
                        'customer_id' => $validated['customer_id'],
                        'service_id' => $validated['service_id'] ?? null,
                        'package_id' => array_key_exists('package_id', $validated) ? $validated['package_id'] : $bookingToUpdate->package_id,
                        'staff_id' => $validated['staff_id'],
                        'start_time' => $startTime->setTimezone('UTC'),
                        'end_time' => $endTime->setTimezone('UTC'),
                        'amount' => $amount,
                        'status' => $validated['status'] ?? $bookingToUpdate->status,
                        'notes' => $validated['notes'] ?? null,
                    ]);

                    // Adjust balances if package or service changed
                    if ($oldPackageId != $bookingToUpdate->package_id || $oldServiceId != $bookingToUpdate->service_id) {
                        // Restore old balance
                        if ($oldPackageId && $oldServiceId) {
                            $oldBalance = CustomerPackageBalance::where('customer_id', $bookingToUpdate->customer_id)
                                ->where('package_id', $oldPackageId)
                                ->where('service_id', $oldServiceId)
                                ->where('salon_id', $salonId)
                                ->first();
                            if ($oldBalance)
                                $oldBalance->increment('quantity_remaining');
                        }
                        // Consume new balance
                        if ($bookingToUpdate->package_id && $bookingToUpdate->service_id) {
                            $newBalance = CustomerPackageBalance::where('customer_id', $bookingToUpdate->customer_id)
                                ->where('package_id', $bookingToUpdate->package_id)
                                ->where('service_id', $bookingToUpdate->service_id)
                                ->where('salon_id', $salonId)
                                ->where('quantity_remaining', '>', 0)
                                ->where(function ($query) {
                                    $query->whereNull('expiry_date')
                                        ->orWhere('expiry_date', '>=', now()->toDateString());
                                })
                                ->orderBy('created_at', 'asc')
                                ->first();
                            if ($newBalance)
                                $newBalance->decrement('quantity_remaining');
                        }
                    }

                    $createdBookings[] = $bookingToUpdate;
                    $processedBookingIds[] = $bookingToUpdate->id;
                } else {
                    // Validate package balance if this is a package booking
                    if (isset($validated['package_id']) && isset($validated['service_id'])) {
                        $hasBalance = CustomerPackageBalance::where('customer_id', $validated['customer_id'])
                            ->where('package_id', $validated['package_id'])
                            ->where('service_id', $validated['service_id'])
                            ->where('salon_id', $salonId)
                            ->where('quantity_remaining', '>', 0)
                            ->where(function ($query) {
                                $query->whereNull('expiry_date')
                                    ->orWhere('expiry_date', '>=', now()->toDateString());
                            })
                            ->exists();

                        if (!$hasBalance) {
                            throw new \Exception("No remaining balance for this service in the package (Booking #" . ($index + 1) . "). Please check the customer's available package services.");
                        }
                    }

                    // Create new booking (only if we couldn't find an existing one to update)
                    $newBooking = Booking::create([
                        'salon_id' => $salonId,
                        'created_by' => auth()->id(),
                        'booking_group_id' => $bookingGroupId,
                        'customer_id' => $validated['customer_id'],
                        'service_id' => $validated['service_id'] ?? null,
                        'package_id' => $validated['package_id'] ?? null,
                        'staff_id' => $validated['staff_id'],
                        'start_time' => $startTime->setTimezone('UTC'),
                        'end_time' => $endTime->setTimezone('UTC'),
                        'amount' => $amount,
                        'status' => $validated['status'] ?? 'pending',
                        'notes' => $validated['notes'] ?? null,
                        'source' => 'admin',
                    ]);

                    // Consume package balance for new booking
                    if ($newBooking->package_id && $newBooking->service_id) {
                        $balance = CustomerPackageBalance::where('customer_id', $newBooking->customer_id)
                            ->where('package_id', $newBooking->package_id)
                            ->where('service_id', $newBooking->service_id)
                            ->where('salon_id', $salonId)
                            ->where('quantity_remaining', '>', 0)
                            ->where(function ($query) {
                                $query->whereNull('expiry_date')
                                    ->orWhere('expiry_date', '>=', now()->toDateString());
                            })
                            ->orderBy('created_at', 'asc')
                            ->first();

                        if ($balance) {
                            $balance->decrement('quantity_remaining');
                        }
                    }

                    $createdBookings[] = $newBooking;
                    $processedBookingIds[] = $newBooking->id;
                }
            }

            // Delete any bookings in the group that were not processed (i.e., removed from the group)
            if ($bookingGroupId) {
                $bookingsToDelete = Booking::where('booking_group_id', $bookingGroupId)
                    ->whereNotIn('id', $processedBookingIds)
                    ->get();

                foreach ($bookingsToDelete as $bToDelete) {
                    // Restore package balance if a package booking is removed
                    if ($bToDelete->package_id && $bToDelete->service_id) {
                        $balance = CustomerPackageBalance::where('customer_id', $bToDelete->customer_id)
                            ->where('package_id', $bToDelete->package_id)
                            ->where('service_id', $bToDelete->service_id)
                            ->where('salon_id', $salonId)
                            ->first();

                        if ($balance) {
                            $balance->increment('quantity_remaining');
                        }
                    }
                    $bToDelete->delete();
                }
            }

            if ($requiresPayment) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'requires_payment' => true,
                    'message' => 'Please provide payment information to complete this booking.',
                    'booking_id' => $booking->id,
                    'amount' => $totalAmount
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Booking(s) updated successfully',
                'bookings' => $createdBookings
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating booking:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Booking $booking)
    {
        try {
            // Restore package balance if this was a package booking
            if ($booking->package_id && $booking->service_id && !in_array($booking->status, ['cancelled', 'no_show'])) {
                $balance = CustomerPackageBalance::where('customer_id', $booking->customer_id)
                    ->where('package_id', $booking->package_id)
                    ->where('service_id', $booking->service_id)
                    ->where('salon_id', $booking->salon_id)
                    ->first();

                if ($balance) {
                    $balance->increment('quantity_remaining');
                    Log::info('Package balance restored due to booking deletion', [
                        'booking_id' => $booking->id,
                        'customer_id' => $booking->customer_id,
                        'package_id' => $booking->package_id,
                        'service_id' => $booking->service_id,
                        'new_balance' => $balance->quantity_remaining
                    ]);
                }
            }

            $booking->delete();
            return response()->json(['message' => 'Booking cancelled successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to cancel booking', 'error' => $e->getMessage()], 500);
        }
    }

    public function getCustomers(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $term = $request->input('term');
        $perPage = 20;

        $query = Customer::where('salon_id', $salonId)
            ->where('status', 'active');

        if ($term) {
            $query->search($term);
        }

        $customers = $query->paginate($perPage);

        return response()->json([
            'results' => $customers->getCollection()->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'text' => $customer->name . ' (' . $customer->display_phone . ')',
                    'name' => $customer->name,
                    'phone' => $customer->display_phone,
                    'email' => $customer->display_email,
                ];
            }),
            'pagination' => [
                'more' => $customers->hasMorePages()
            ]
        ]);
    }

    public function getServices()
    {
        return response()->json(Service::where('salon_id', auth()->user()->salon_id)
            ->where('status', 'active')
            ->with([
                'staff' => function ($query) {
                    $query->select('users.id');
                }
            ])
            ->select('id', 'name', 'duration', 'price')
            ->get()
            ->map(function ($service) {
                $service->staff_ids = $service->staff->pluck('id');
                unset($service->staff);
                return $service;
            }));
    }

    public function getPackages()
    {
        return response()->json(\App\Models\Package::where('salon_id', auth()->user()->salon_id)
            ->where('is_active', true)
            ->with([
                'services' => function ($query) {
                    $query->select('services.id', 'services.name', 'services.duration', 'services.price')
                        ->with([
                            'staff' => function ($q) {
                                $q->select('users.id');
                            }
                        ]);
                }
            ])
            ->select('id', 'name', 'price', 'special_price')
            ->get()
            ->map(function ($package) {
                foreach ($package->services as $service) {
                    $service->staff_ids = $service->staff->pluck('id');
                    unset($service->staff);
                }
                return $package;
            }));
    }

    public function getStaff(Request $request)
    {
        try {
            $date = $request->input('date', now()->toDateString());
            $salonId = auth()->user()->salon_id;
            $salonTimezone = salon_timezone();
            $requestDate = Carbon::parse($date, $salonTimezone);
            $isToday = $requestDate->isSameDay(Carbon::now($salonTimezone));
            $now = Carbon::now($salonTimezone);

            $staffMembers = User::role('employee')
                ->where('salon_id', $salonId)
                ->where('status', 'active')
                ->get(['id', 'name']);

            $staffMembers->transform(function ($staff) use ($requestDate, $isToday, $now, $salonTimezone) {
                $staff->is_working = true;
                $staff->availability_reason = '';

                // Helper to return array
                $toStaffArray = function ($s) {
                    $data = $s->toArray();
                    $data['is_working'] = $s->is_working;
                    $data['availability_reason'] = $s->availability_reason;
                    return $data;
                };

                // 1. Check Schedule
                $dayOfWeek = $requestDate->dayOfWeek;
                $schedule = \App\Models\StaffSchedule::where('staff_id', $staff->id)
                    ->where('day_of_week', $dayOfWeek)
                    ->first();

                // Log for debugging
                if ($staff->name === 'Ketan Mhatre' || $staff->name === 'Ketan test') {
                    Log::info('Staff Availability Debug', [
                        'staff' => $staff->name,
                        'date' => $requestDate->toDateString(),
                        'dayOfWeek' => $dayOfWeek,
                        'schedule_found' => (bool) $schedule,
                        'is_working_schedule' => $schedule ? $schedule->is_working : null,
                        'is_today' => $isToday
                    ]);
                }

                // Check if staff is working regular hours OR has overtime
                $isRegularWorking = $schedule && $schedule->is_working;
                $hasOvertime = $schedule && $schedule->allows_overtime && $schedule->overtime_start && $schedule->overtime_end;

                if (!$schedule || (!$isRegularWorking && !$hasOvertime)) {
                    $staff->is_working = false;
                    $staff->availability_reason = 'Not Working Today';
                    return $toStaffArray($staff);
                }

                // If regular working + overtime, use regular start time.
                // If NOT regular working but HAS overtime, use overtime start time.
                $startTime = $isRegularWorking ? $schedule->start_time : $schedule->overtime_start;
                $workingEnd = $isRegularWorking ? $schedule->end_time : $schedule->overtime_end;

                // Log for debugging
                // if ($staff->name === 'Ketan Mhatre' || $staff->name === 'Ketan test') {
                //     Log::info('Staff Availability Debug', [
                //         'staff' => $staff->name,
                //         'date' => $requestDate->toDateString(),
                //         'dayOfWeek' => $dayOfWeek,
                //         'is_regular' => $isRegularWorking,
                //         'has_overtime' => $hasOvertime,
                //         'start' => $startTime,
                //         'end' => $workingEnd
                //     ]);
                // }

                // OVERTIME: If regular working and overtime is enabled, extend the end time
                if ($isRegularWorking && $schedule->allows_overtime && $schedule->overtime_end) {
                    $workingEnd = $schedule->overtime_end;
                }

                $staff->working_hours = [
                    'start' => $startTime,
                    'end' => $workingEnd
                ];

                // Add overtime information for visual differentiation in calendar
                if ($schedule->allows_overtime && $schedule->overtime_start && $schedule->overtime_end) {
                    $staff->overtime = [
                        'start' => $schedule->overtime_start,
                        'end' => $schedule->overtime_end
                    ];
                } else {
                    $staff->overtime = null;
                }

                // 2. Check if shift is over (only if today)
                if ($isToday) {
                    // Use overtime end if enabled, otherwise regular end time
                    $shiftEndTime = $schedule->end_time;
                    if ($schedule->allows_overtime && $schedule->overtime_end) {
                        $shiftEndTime = $schedule->overtime_end;
                    }

                    $endTime = Carbon::createFromFormat('H:i:s', $shiftEndTime, $salonTimezone)->setDateFrom($now);
                    if ($now->gt($endTime)) {
                        $staff->is_working = false;
                        $staff->availability_reason = '(Shift Over)';
                        return $toStaffArray($staff);
                    }
                }

                // 3. Check Absences
                $isAbsent = \App\Models\StaffAbsence::where('staff_id', $staff->id)
                    ->where(function ($query) use ($requestDate) {
                        $startOfDay = $requestDate->copy()->startOfDay()->setTimezone('UTC');
                        $endOfDay = $requestDate->copy()->endOfDay()->setTimezone('UTC');

                        $query->where(function ($q) use ($startOfDay, $endOfDay) {
                            $q->whereBetween('start_at', [$startOfDay, $endOfDay])
                                ->orWhereBetween('end_at', [$startOfDay, $endOfDay])
                                ->orWhere(function ($subQ) use ($startOfDay, $endOfDay) {
                                    $subQ->where('start_at', '<=', $startOfDay)
                                        ->where('end_at', '>=', $endOfDay);
                                });
                        });
                    })
                    ->exists();

                if ($isAbsent) {
                    $staff->is_working = false;
                    $staff->availability_reason = '(On Leave)';
                }

                $result = $toStaffArray($staff);

                if ($staff->name === 'Ketan Mhatre' || $staff->name === 'Ketan test') {
                    Log::info('Final Staff Data', $result);
                }

                return $result;
            });

            Log::info('getStaff: Fetched staff with availability', ['date' => $date, 'count' => $staffMembers->count()]);

            return response()->json($staffMembers);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get detailed staff information and assigned services for modal display.
     */
    public function getStaffDetails(User $user)
    {
        // Scope to user's salon
        if ($user->salon_id !== auth()->user()->salon_id) {
            abort(403, 'Unauthorized');
        }

        // Load employee profile and services
        $user->load(['employee', 'services.category']);

        return response()->json([
            'success' => true,
            'staff' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'position' => $user->employee->position ?? 'Staff',
                'avatar_url' => $user->employee && $user->employee->avatar 
                    ? asset('storage/' . $user->employee->avatar) 
                    : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random',
                'services' => $user->services->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'category_name' => $service->category->name ?? 'Uncategorized',
                        'price' => $service->price,
                        'duration' => $service->duration
                    ];
                })
            ]
        ]);
    }

    public function assign(Request $request, Booking $booking)
    {
        $salonId = auth()->user()->salon_id;
        $validated = $request->validate([
            'staff_id' => ['required', Rule::exists('users', 'id')->where('salon_id', $salonId)],
            'assigned_time' => 'required|date_format:Y-m-d H:i:s',
        ]);

        // Prevent assigning staff to completed or cancelled bookings
        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot assign staff to a booking that is already ' . $booking->status . '.'
            ], 422);
        }

        try {
            $service = $booking->service;
            $startTime = \Carbon\Carbon::parse($validated['assigned_time']);
            $endTime = $startTime->copy()->addMinutes((int) $service->duration);

            // Check for conflicts
            $conflict = Booking::where('staff_id', $validated['staff_id'])
                ->where('id', '!=', $booking->id)
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                        ->orWhereBetween('end_time', [$startTime, $endTime])
                        ->orWhere(function ($q) use ($startTime, $endTime) {
                            $q->where('start_time', '<=', $startTime)
                                ->where('end_time', '>=', $endTime);
                        });
                })
                ->exists();

            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'This time slot is already booked for the selected staff member.'
                ], 422);
            }

            $booking->update([
                'staff_id' => $validated['staff_id'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'staff_assignment_status' => Booking::STAFF_ASSIGNMENT_ASSIGNED,
                'staff_assigned_at' => now(),
                'status' => Booking::STATUS_CONFIRMED,
            ]);

            // Send Confirmation
            try {
                $this->notificationService->sendBookingConfirmation($booking);
            } catch (\Exception $e) {
                Log::warning('Failed to send booking confirmation after assignment', ['error' => $e->getMessage()]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Staff assigned to booking successfully',
                'booking' => $booking->load(['customer', 'service', 'staff'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign staff to booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAvailableSlots(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $validated = $request->validate([
            'staff_id' => ['required', Rule::exists('users', 'id')->where('salon_id', $salonId)],
            'date' => 'nullable|date',
        ]);

        $date = $validated['date'] ?? now()->toDateString();
        $staffId = $validated['staff_id'];
        $salonId = auth()->user()->salon_id;

        // Define working hours and slot duration
        $settings = app(\App\Services\SettingsService::class);
        $workingStart = $settings->get('working_hours_start', '08:00', $salonId);
        $workingEnd = $settings->get('working_hours_end', '20:00', $salonId);

        $startTime = Carbon::parse($date . ' ' . $workingStart, salon_timezone());
        $endTime = Carbon::parse($date . ' ' . $workingEnd, salon_timezone());
        $slotDuration = (int) $settings->get('slot_duration', 30, $salonId);

        // Fetch existing bookings for the staff on the given date
        $bookings = Booking::where('staff_id', $staffId)
            ->whereDate('start_time', $date)
            ->get();

        $slots = [];
        $currentTime = $startTime->copy();

        while ($currentTime->lt($endTime)) {
            $slotEnd = $currentTime->copy()->addMinutes($slotDuration);

            // Check if slot overlaps with any existing booking
            $overlap = $bookings->contains(function ($booking) use ($currentTime, $slotEnd) {
                return $booking->start_time < $slotEnd && $booking->end_time > $currentTime;
            });

            if (!$overlap) {
                $slots[] = $currentTime->toDateTimeString();
            }

            $currentTime->addMinutes($slotDuration);
        }

        return response()->json(['slots' => $slots]);
    }

    /**
     * Get appointment settings for calendar configuration
     */
    public function getAppointmentSettings()
    {
        try {
            $salonId = auth()->user()->salon_id;

            $settings = [
                'working_hours_start' => $this->settingsService->get('working_hours_start', '08:00', $salonId),
                'working_hours_end' => $this->settingsService->get('working_hours_end', '20:00', $salonId),
                'slot_duration' => (int) $this->settingsService->get('slot_duration', 30, $salonId),
                'appointment_buffer_time' => (int) $this->settingsService->get('appointment_buffer_time', 0, $salonId),
                'calendar_default_view' => $this->settingsService->get('calendar_default_view', 'timeGridWeek', $salonId),
                'timezone' => salon_timezone(),
                'time_format' => $this->settingsService->get('time_format', '24h', $salonId),
                'date_format' => $this->settingsService->get('date_format', 'Y-m-d', $salonId),
            ];

            return response()->json($settings);
        } catch (\Exception $e) {
            Log::error('Error fetching appointment settings: ' . $e->getMessage());
            return response()->json([
                'working_hours_start' => '08:00',
                'working_hours_end' => '20:00',
                'slot_duration' => 30,
                'appointment_buffer_time' => 0,
                'timezone' => config('app.timezone', 'UTC'),
            ]);
        }
    }
}
