<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Mail\BookingCreatedMail;
use App\Mail\BookingRescheduledMail;
use App\Notifications\BookingNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GuestBookingController extends Controller
{
    protected $settingsService;
    protected $notificationService;

    public function __construct(
        SettingsService $settingsService,
        \App\Services\NotificationService $notificationService
    ) {
        $this->settingsService = $settingsService;
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $salon = app()->bound('current_salon') ? app('current_salon') : null;

        if (!$salon) {
            abort(500, 'Salon context not available');
        }
        $settingsService = app(SettingsService::class);
        $guestBookingEnabled = $settingsService->get('guest_booking_enabled', true, $salon->id);

        // Override salon details with settings if available
        $salon->name = trim($settingsService->get('business_name', $salon->name, $salon->id), '"');
        $salon->address = $settingsService->get('business_address', $salon->address, $salon->id);
        $salon->phone = $settingsService->get('business_phone', $salon->phone, $salon->id);
        $salon->email = $settingsService->get('business_email', $salon->email, $salon->id);
        $salon->logo = $settingsService->get('business_logo', $salon->logo, $salon->id);

        if (!$guestBookingEnabled) {
            return view('booking.guest', [
                'guestBookingEnabled' => false,
                'salon' => $salon,
                'business_name' => $salon->name,
                'business_address' => $salon->address,
                'business_phone' => $salon->phone,
                'business_email' => $salon->email,
                'business_logo' => $salon->logo,
            ]);
        }

        // Get salon-specific services
        $services = Service::where('salon_id', $salon->id)
            ->where('status', 'active')
            ->availableForOnlineBooking()
            ->with('staff:id')
            ->get(['id', 'name', 'duration', 'price', 'category_id']);

        // Get salon-specific staff across employee, staff, stylist, barber roles
        $staffQuery = User::where('salon_id', $salon->id)
            ->where(function ($q) {
                $q->whereHas('roles', function ($r) {
                    $r->whereIn('name', ['employee', 'staff', 'stylist', 'barber']);
                })->orWhereNotNull('staff_id');
            })
            ->where(function ($q) {
                $q->where('status', 'active')
                  ->orWhere('status', '1')
                  ->orWhereNull('status');
            });

        if (app()->has('current_branch')) {
            $currentBranchId = app('current_branch')->id;
            $staffQuery->where(function ($q) use ($currentBranchId) {
                $q->where('branch_id', $currentBranchId)
                  ->orWhereNull('branch_id');
            });
        }

        $staffMembers = $staffQuery->orderBy('name')->get(['id', 'name']);

        if ($staffMembers->isEmpty()) {
            $staffMembers = User::where('salon_id', $salon->id)->orderBy('name')->get(['id', 'name']);
        }

        // Get salon-specific categories
        $serviceCategories = ServiceCategory::where('salon_id', $salon->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Format working hours
        $start = $settingsService->get('working_hours_start', '08:00', $salon->id);
        $end = $settingsService->get('working_hours_end', '20:00', $salon->id);

        try {
            $startTime = Carbon::createFromFormat('H:i', $start)->format('g:i A');
            $endTime = Carbon::createFromFormat('H:i', $end)->format('g:i A');
            $formatted_hours = $startTime . ' - ' . $endTime;
        } catch (\Exception $e) {
            $formatted_hours = '9:00 AM - 9:00 PM';
        }

        return view('booking.guest', compact(
            'services',
            'staffMembers',
            'serviceCategories',
            'guestBookingEnabled',
            'salon'
        ) + [
            'business_name' => $salon->name,
            'business_address' => $salon->address,
            'business_phone' => $salon->phone,
            'business_email' => $salon->email,
            'business_logo' => $salon->logo,
            'formatted_hours' => $formatted_hours,
            'currency_symbol' => $settingsService->get('currency_symbol', '$', $salon->id),
            'currency_code' => $settingsService->get('currency_code', 'USD', $salon->id),
            'advance_booking_days' => (int) $settingsService->get('advance_booking_days', 30, $salon->id),
            'slot_duration' => (int) $settingsService->get('slot_duration', 30, $salon->id),
        ]);
    }

    public function getServices(Request $request)
    {
        if (!$this->isGuestBookingEnabled()) {
            return response()->json([]);
        }

        $salon = app()->bound('current_salon') ? app('current_salon') : null;

        if (!$salon) {
            abort(500, 'Salon context not available');
        }
        $categoryId = $request->query('category');

        $query = Service::where('salon_id', $salon->id)
            ->where('status', 'active')
            ->availableForOnlineBooking()
            ->select('id', 'name', 'duration', 'price', 'category_id');

        if ($categoryId && $categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        $services = $query->with(['category:id,name', 'staff:id'])->get();

        return response()->json($services);
    }

    private function isGuestBookingEnabled()
    {
        $salon = app()->bound('current_salon') ? app('current_salon') : null;

        if (!$salon) {
            abort(500, 'Salon context not available');
        }
        return app(SettingsService::class)->get('guest_booking_enabled', true, $salon->id);
    }

    public function store(Request $request)
    {
        Log::info('Guest booking store initiated', ['request' => $request->all()]);

        try {
            if (!$this->isGuestBookingEnabled()) {
                Log::warning('Guest booking disabled');
                return response()->json(['success' => false, 'message' => 'Guest booking is currently disabled.'], 403);
            }

            $salon = app()->bound('current_salon') ? app('current_salon') : null;

        if (!$salon) {
            abort(500, 'Salon context not available');
        }
            Log::info('Current salon retrieved', ['salon_id' => $salon->id]);

            if (!$salon->canAddGuestBooking()) {
                Log::warning('Guest booking limit reached - BLOCKED', ['salon_id' => $salon->id]);
                return response()->json(['success' => false, 'message' => 'The salon has reached its limit for guest bookings this month.'], 403);
            }

            if (!$salon->canAddBooking()) {
                Log::warning('Total booking limit reached via guest booking - BLOCKED', ['salon_id' => $salon->id]);
                return response()->json(['success' => false, 'message' => 'The salon has reached its total booking limit for this month.'], 403);
            }

            if (!$request->filled('name') && $request->filled('first_name')) {
                $request->merge(['name' => trim($request->first_name . ' ' . ($request->last_name ?? ''))]);
            }

            if ($request->filled('services_json')) {
                try {
                    $services = json_decode($request->services_json, true);
                    if (is_array($services) && count($services) > 0) {
                        $request->merge(['services' => $services]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to decode services_json', ['error' => $e->getMessage()]);
                }
            }

            try {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'nullable|email|max:255',
                    'phone' => 'required|string|min:7|max:15',
                    'country_code' => 'required|string|max:5',
                    'services' => 'required|array|min:1',
                    'services.*.id' => [
                        'required',
                        Rule::exists('services', 'id')->where(function ($query) use ($salon) {
                            return $query->where('salon_id', $salon->id);
                        }),
                    ],
                    'services.*.quantity' => 'required|integer|min:1',
                    'staff_id' => [
                        'nullable',
                        Rule::exists('users', 'id')->where(function ($query) use ($salon) {
                            return $query->where('salon_id', $salon->id);
                        }),
                    ],
                    'date' => 'required|date|after:yesterday',
                    'time' => 'required',
                    'notes' => 'nullable|string|max:500'
                ]);
            } catch (\Throwable $e) {
                Log::error('Guest booking validation failed', [
                    'exception_class' => get_class($e),
                    'message' => $e->getMessage(),
                    'request' => $request->all()
                ]);

                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $e->errors()
                    ], 422);
                }

                throw $e; // Re-throw if not a validation exception to be caught by outer block
            }

            Log::info('Validation passed');

            // Check advance booking limit
            $advanceBookingDays = (int) $this->settingsService->get('advance_booking_days', 30, $salon->id);
            $bookingDate = Carbon::parse($validated['date']);
            $maxDate = Carbon::today()->addDays($advanceBookingDays);

            if ($bookingDate->gt($maxDate)) {
                return response()->json(['success' => false, 'message' => "Bookings can only be made up to {$advanceBookingDays} days in advance."], 422);
            }

            DB::beginTransaction();

            try {
                $salon = app()->bound('current_salon') ? app('current_salon') : null;

        if (!$salon) {
            abort(500, 'Salon context not available');
        }

                // Check if customer exists by phone or email
                $customer = Customer::where('salon_id', $salon->id)
                    ->where(function ($query) use ($validated) {
                        if (!empty($validated['email'])) {
                            $query->where('email', $validated['email']);
                        }
                        
                        $query->orWhere(function ($q) use ($validated) {
                            $q->where('phone', $validated['phone']);
                            if (!empty($validated['country_code'])) {
                                $q->orWhere(function ($subQ) use ($validated) {
                                    $subQ->where('phone', $validated['phone'])
                                        ->where('country_code', $validated['country_code']);
                                })
                                ->orWhere('phone', $validated['country_code'] . $validated['phone'])
                                ->orWhere('phone', str_replace('+', '', $validated['country_code']) . $validated['phone']);
                            }
                        });
                    })
                    ->first();

                if (!$customer) {
                    // Create new guest customer
                    $customer = Customer::create([
                        'salon_id' => $salon->id,
                        'name' => $validated['name'],
                        'email' => $validated['email'] ?? null,
                        'phone' => $validated['phone'],
                        'country_code' => $validated['country_code'],
                        'status' => 'active',
                        'preferred_contact' => 'phone',
                        'notes' => 'Guest booking - created via online form',
                        'is_guest' => true
                    ]);
                } else {
                    // Update existing customer info if needed
                    $customer->update([
                        'name' => $validated['name'],
                        'email' => $validated['email'] ?? $customer->email,
                        'country_code' => $validated['country_code'],
                        'phone' => $validated['phone'],
                        'is_guest' => $customer->is_guest ?? true,
                    ]);
                }

                $bookingNotes = $validated['notes'] ?? '';
                $taxEnabledServices = $this->settingsService->get('tax_enabled_services', false, $salon->id);
                $taxRate = $this->settingsService->get('tax_rate', 0, $salon->id);
                $allowOverlapping = $this->settingsService->get('allow_overlapping_bookings', false, $salon->id);
                $bufferTime = (int) $this->settingsService->get('appointment_buffer_time', 0, $salon->id);

                // Prepare services list (flatten quantity)
                $servicesList = [];
                foreach ($validated['services'] as $s) {
                    $serviceModel = Service::findOrFail($s['id']);
                    $qty = $s['quantity'] ?? 1;
                    for ($i = 0; $i < $qty; $i++) {
                        $servicesList[] = $serviceModel;
                    }
                }

                $startTime = Carbon::createFromFormat('Y-m-d H:i', $validated['date'] . ' ' . $validated['time'], salon_timezone())->setTimezone('UTC');

                // Fetch necessary data for staff availability check
                $dateStr = $validated['date'];
                $staffMembers = User::role('employee')->where('salon_id', $salon->id)->where('status', 'active')->get();
                $bookings = Booking::whereDate('start_time', $dateStr)->where('salon_id', $salon->id)->where('status', '!=', 'cancelled')->get();
                $absences = \App\Models\StaffAbsence::where('salon_id', $salon->id)
                    ->whereDate('start_at', '<=', $dateStr)
                    ->whereDate('end_at', '>=', $dateStr)
                    ->get();
                $dayOfWeek = Carbon::parse($dateStr)->dayOfWeek;
                $dailySchedules = \App\Models\StaffDailySchedule::whereIn('staff_id', $staffMembers->pluck('id'))->where('date', $dateStr)->get()->keyBy('staff_id');
                $weeklySchedules = \App\Models\StaffSchedule::whereIn('staff_id', $staffMembers->pluck('id'))->where('day_of_week', $dayOfWeek)->get()->keyBy('staff_id');
                $workingStart = $this->settingsService->get('working_hours_start', '08:00', $salon->id);
                $workingEnd = $this->settingsService->get('working_hours_end', '20:00', $salon->id);
                $salonTz = salon_timezone();
                $allowOverlapping = $this->settingsService->get('allow_overlapping_bookings', false, $salon->id);
                $bufferTime = (int) $this->settingsService->get('appointment_buffer_time', 0, $salon->id);

                $previousStaffId = $validated['staff_id'] ?? null; // Start with preferred staff if any
                $createdBookings = [];

                $currentStartTime = $startTime->copy();

                foreach ($servicesList as $index => $service) {
                    $serviceEndTime = $currentStartTime->copy()->addMinutes($service->duration);

                    // Find staff for this specific slot
                    $assignedStaffId = null;

                    // 1. Try preferred/previous staff
                    if ($previousStaffId) {
                        $staff = $staffMembers->find($previousStaffId);
                        if ($staff && $this->isStaffAvailable($staff, $currentStartTime, $serviceEndTime, $bookings, $dailySchedules, $weeklySchedules, $absences, $dateStr, $salonTz, $workingStart, $workingEnd, $allowOverlapping, $bufferTime)) {
                            $assignedStaffId = $staff->id;
                        }
                    }

                    // 2. If not available (and no strict preference enforced for all), find ANY available
                    if (!$assignedStaffId) {
                        foreach ($staffMembers as $staff) {
                            if ($staff->id === $previousStaffId)
                                continue;
                            if ($this->isStaffAvailable($staff, $currentStartTime, $serviceEndTime, $bookings, $dailySchedules, $weeklySchedules, $absences, $dateStr, $salonTz, $workingStart, $workingEnd, $allowOverlapping, $bufferTime)) {
                                $assignedStaffId = $staff->id;
                                break;
                            }
                        }
                    }

                    if (!$assignedStaffId) {
                        throw new \Exception("No staff available for service {$service->name} at " . $currentStartTime->format('H:i'));
                    }

                    $servicePrice = $service->price;
                    if ($taxEnabledServices) {
                        $servicePrice += $servicePrice * ($taxRate / 100);
                    }

                    // Generate group ID if multiple services
                    $bookingGroupId = (count($servicesList) > 1 && !isset($bookingGroupId)) ? (string) Str::uuid() : ($bookingGroupId ?? null);

                    $booking = Booking::create([
                        'salon_id' => $salon->id,
                        'booking_group_id' => $bookingGroupId,
                        'customer_id' => $customer->id,
                        'service_id' => $service->id,
                        'staff_id' => $assignedStaffId,
                        'start_time' => $currentStartTime,
                        'end_time' => $serviceEndTime,
                        'amount' => $servicePrice,
                        'notes' => $bookingNotes . ($index > 0 ? ' (Sequential service)' : ''),
                        'status' => Booking::STATUS_PENDING,
                        'source' => 'online'
                    ]);

                    $createdBookings[] = $booking;
                    $bookings->push($booking);
                    $previousStaffId = $assignedStaffId; // Try to keep same staff for next service
                    $currentStartTime = $serviceEndTime; // Next service starts when this one ends
                }

                DB::commit();

                // Send confirmation (using the first booking as reference, but listing all)
                try {
                    $notifyNewBooking = $this->settingsService->get('notify_new_booking', true, $salon->id);

                    if ($notifyNewBooking && count($createdBookings) > 0) {
                        $mainBooking = $createdBookings[0];
                        $businessEmail = $this->settingsService->get('business_email', 'admin@salon.com', $salon->id);

                        // Send to Customer
                        Mail::to($validated['email'] ?? $businessEmail)->send(new BookingCreatedMail($mainBooking));

                        // Send WhatsApp
                        $this->notificationService->sendGroupBookingConfirmation($createdBookings);

                        // Notify Staff/Admin
                        $admins = User::role(['super_admin', 'salon_admin', 'manager'])
                            ->where('salon_id', $salon->id)
                            ->get();

                        // Notify all assigned staff
                        $assignedStaffIds = collect($createdBookings)->pluck('staff_id')->unique();
                        $staffMembersToNotify = User::whereIn('id', $assignedStaffIds)->get();
                        $admins = $admins->merge($staffMembersToNotify)->unique('id');

                        Notification::send($admins, new BookingNotification($mainBooking, 'created'));
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to send booking confirmation email/notification', ['error' => $e->getMessage()]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Your booking has been submitted successfully! We will contact you soon to confirm.',
                    'booking_id' => $createdBookings[0]->id
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Guest booking creation failed', ['error' => $e->getMessage()]);
                return response()->json(['success' => false, 'message' => 'Failed to create booking. ' . $e->getMessage()], 500);
            }
        } catch (\Exception $e) {
            Log::error('Guest booking store outer exception', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function getAvailableSlots(Request $request)
    {
        if (!$this->isGuestBookingEnabled()) {
            return response()->json(['slots' => []]);
        }

        Log::info('getAvailableSlots called', ['input' => $request->all()]);

        try {
            $validated = $request->validate([
                'staff_id' => 'nullable|integer|exists:users,id',
                'date' => 'required|date',
                'services' => 'nullable|array',
                'services.*.id' => 'required|integer|exists:services,id',
                'services.*.quantity' => 'integer|min:1',
                'service_id' => 'nullable|integer|exists:services,id', // Legacy support
                'total_duration' => 'nullable|integer|min:1' // Legacy support
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Slots validation failed', ['errors' => $e->errors(), 'input' => $request->all()]);
            return response()->json(['error' => 'Invalid input parameters', 'messages' => $e->errors()], 422);
        }

        $date = $validated['date'];
        $staffId = $validated['staff_id'] ?? null;

        // Prepare services list
        $servicesList = [];
        if (!empty($validated['services'])) {
            foreach ($validated['services'] as $s) {
                $serviceModel = Service::find($s['id']);
                if ($serviceModel) {
                    $qty = $s['quantity'] ?? 1;
                    for ($i = 0; $i < $qty; $i++) {
                        $servicesList[] = $serviceModel;
                    }
                }
            }
        } elseif (!empty($validated['service_id'])) {
            $serviceModel = Service::find($validated['service_id']);
            if ($serviceModel) {
                $servicesList[] = $serviceModel;
            }
        }

        // If no services, return empty (or handle default duration if needed, but smart scheduling needs services)
        if (empty($servicesList)) {
            // Fallback for legacy simple duration check if no specific services
            if (!empty($validated['total_duration'])) {
                // Create a dummy service object for calculation
                $dummyService = new Service();
                $dummyService->id = 0;
                $dummyService->duration = $validated['total_duration'];
                $servicesList[] = $dummyService;
            } else {
                return response()->json(['slots' => []]);
            }
        }

        try {
            $salonTz = salon_timezone();
            $now = Carbon::now($salonTz);
            $requestedDate = Carbon::parse($date, $salonTz)->startOfDay();

            if ($requestedDate->lt($now->copy()->startOfDay())) {
                return response()->json(['slots' => []]);
            }

            $salon = app()->bound('current_salon') ? app('current_salon') : null;

        if (!$salon) {
            abort(500, 'Salon context not available');
        }
            $workingStart = $this->settingsService->get('working_hours_start', '08:00', $salon->id);
            $workingEnd = $this->settingsService->get('working_hours_end', '20:00', $salon->id);
            $allowOverlapping = $this->settingsService->get('allow_overlapping_bookings', false, $salon->id);
            $bufferTime = (int) $this->settingsService->get('appointment_buffer_time', 0, $salon->id);

            // Fetch all active staff
            $staffQuery = User::role('employee')
                ->where('salon_id', $salon->id)
                ->where('status', 'active');

            if ($staffId) {
                $staffQuery->where('id', $staffId);
            }

            $staffMembers = $staffQuery->get();
            if ($staffMembers->isEmpty()) {
                return response()->json(['slots' => []]);
            }

            $bookings = Booking::whereDate('start_time', $date)
                ->where('salon_id', $salon->id)
                ->where('status', '!=', 'cancelled')
                ->get();

            $absences = \App\Models\StaffAbsence::where('salon_id', $salon->id)
                ->whereDate('start_at', '<=', $date)
                ->whereDate('end_at', '>=', $date)
                ->get();

            $dayOfWeek = Carbon::parse($date)->dayOfWeek;
            $dailySchedules = \App\Models\StaffDailySchedule::whereIn('staff_id', $staffMembers->pluck('id'))
                ->where('date', $date)
                ->get()
                ->keyBy('staff_id');

            $weeklySchedules = \App\Models\StaffSchedule::whereIn('staff_id', $staffMembers->pluck('id'))
                ->where('day_of_week', $dayOfWeek)
                ->get()
                ->keyBy('staff_id');

            $startTime = Carbon::parse($date . ' ' . $workingStart, $salonTz);
            $endTime = Carbon::parse($date . ' ' . $workingEnd, $salonTz);

            $slotInterval = (int) $this->settingsService->get('slot_duration', 30, $salon->id);
            $slots = [];
            $currentTime = $startTime->copy();

            // Iterate through all potential start times
            while ($currentTime->lt($endTime)) {
                // Filter past times for today
                if ($requestedDate->isToday() && $currentTime->lt($now)) {
                    $currentTime->addMinutes($slotInterval);
                    continue;
                }

                // Check if the ENTIRE sequence of services is possible starting at $currentTime
                $sequencePossible = true;
                $serviceStartTime = $currentTime->copy();
                $previousStaffId = null; // To prioritize continuity

                foreach ($servicesList as $service) {
                    $serviceEndTime = $serviceStartTime->copy()->addMinutes($service->duration);

                    if ($serviceEndTime->gt($endTime)) {
                        $sequencePossible = false;
                        break;
                    }

                    // Find a staff member for THIS service at THIS time
                    $assignedStaffForStep = null;

                    // 1. Try previous staff first (continuity)
                    if ($previousStaffId) {
                        $staff = $staffMembers->find($previousStaffId);
                        if ($staff && $this->isStaffAvailable($staff, $serviceStartTime, $serviceEndTime, $bookings, $dailySchedules, $weeklySchedules, $absences, $date, $salonTz, $workingStart, $workingEnd, $allowOverlapping, $bufferTime)) {
                            $assignedStaffForStep = $staff;
                        }
                    }

                    // 2. If not, try any other staff
                    if (!$assignedStaffForStep) {
                        foreach ($staffMembers as $staff) {
                            if ($staff->id === $previousStaffId)
                                continue; // Already checked

                            if ($this->isStaffAvailable($staff, $serviceStartTime, $serviceEndTime, $bookings, $dailySchedules, $weeklySchedules, $absences, $date, $salonTz, $workingStart, $workingEnd, $allowOverlapping, $bufferTime)) {
                                $assignedStaffForStep = $staff;
                                break;
                            }
                        }
                    }

                    if ($assignedStaffForStep) {
                        $previousStaffId = $assignedStaffForStep->id;
                        $serviceStartTime = $serviceEndTime; // Move to next service start time
                    } else {
                        $sequencePossible = false;
                        break; // Cannot schedule this service, so sequence fails
                    }
                }

                if ($sequencePossible) {
                    $slots[] = $currentTime->format('H:i');
                }

                $currentTime->addMinutes($slotInterval);
            }

            return response()->json(['slots' => $slots]);

        } catch (\Exception $e) {
            Log::error('Error in getAvailableSlots', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper to check if a specific staff member is available for a specific time range
     */
    private function isStaffAvailable($staff, $start, $end, $bookings, $dailySchedules, $weeklySchedules, $absences, $date, $salonTz, $workingStart, $workingEnd, $allowOverlapping, $bufferTime)
    {
        // 1. Check Absences / Leave
        $checkStartUtc = $start->copy()->setTimezone('UTC');
        $checkEndUtc = $end->copy()->setTimezone('UTC');

        if (isset($absences) && count($absences) > 0) {
            foreach ($absences->where('staff_id', $staff->id) as $absence) {
                $absStartUtc = Carbon::parse($absence->start_at)->setTimezone('UTC');
                $absEndUtc = Carbon::parse($absence->end_at)->setTimezone('UTC');
                if ($absStartUtc->lt($checkEndUtc) && $absEndUtc->gt($checkStartUtc)) {
                    return false;
                }
            }
        }

        // 2. Check Schedule Shift
        $isWorking = false;
        $staffStart = $workingStart;
        $staffEnd = $workingEnd;

        if (isset($dailySchedules[$staff->id])) {
            if ($dailySchedules[$staff->id]->is_working) {
                $isWorking = true;
                $staffStart = $dailySchedules[$staff->id]->start_time;
                $staffEnd = $dailySchedules[$staff->id]->end_time;
            }
        } elseif (isset($weeklySchedules[$staff->id])) {
            if ($weeklySchedules[$staff->id]->is_working) {
                $isWorking = true;
                $staffStart = $weeklySchedules[$staff->id]->start_time;
                $staffEnd = $weeklySchedules[$staff->id]->end_time;
            }
        } else {
            $isWorking = true; // Default to salon hours
        }

        if (!$isWorking)
            return false;

        $startInSalonTz = $start->copy()->setTimezone($salonTz);
        $endInSalonTz = $end->copy()->setTimezone($salonTz);

        $staffStartTime = Carbon::parse($date . ' ' . $staffStart, $salonTz);
        $staffEndTime = Carbon::parse($date . ' ' . $staffEnd, $salonTz);

        if ($startInSalonTz->lt($staffStartTime) || $endInSalonTz->gt($staffEndTime)) {
            return false;
        }

        // 3. Check Booking Conflicts
        if (!$allowOverlapping) {
            foreach ($bookings->where('staff_id', $staff->id) as $booking) {
                $bStart = Carbon::parse($booking->start_time)->setTimezone('UTC');
                $bEnd = Carbon::parse($booking->end_time)->setTimezone('UTC')->addMinutes($bufferTime);

                // Check overlap
                if ($bStart->lt($checkEndUtc) && $bEnd->gt($checkStartUtc)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function checkExistingBookings(Request $request)
    {
        if (!$this->isGuestBookingEnabled()) {
            return response()->json(['error' => 'Guest booking system is disabled.'], 403);
        }

        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'country_code' => 'nullable|string|max:5',
            'email' => 'nullable|email|max:255',
        ]);

        // Ensure at least one field is provided
        if (empty($validated['phone']) && empty($validated['email'])) {
            return response()->json(['error' => 'Please provide at least a phone number or email address.'], 422);
        }

        $salon = app()->bound('current_salon') ? app('current_salon') : null;

        if (!$salon) {
            abort(500, 'Salon context not available');
        }
        $query = Customer::where('salon_id', $salon->id);

        if (!empty($validated['phone'])) {
            $query->where('phone', $validated['phone']);
            if (!empty($validated['country_code'])) {
                $query->where('country_code', $validated['country_code']);
            }
        }
        if (!empty($validated['email'])) {
            $query->orWhere('email', $validated['email']);
        }
        $customer = $query->first();

        if (!$customer) {
            return response()->json(['bookings' => [], 'message' => 'No existing appointments found.']);
        }

        $bookings = Booking::with(['service', 'staff'])
            ->where('customer_id', $customer->id)
            ->orderBy('start_time', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'service' => $booking->service ? $booking->service->name : 'Service not found',
                    'service_id' => $booking->service_id,
                    'date' => $booking->start_time->format('Y-m-d'),
                    'time' => $booking->start_time->format('H:i'),
                    'status' => $booking->status,
                    'staff' => $booking->staff ? $booking->staff->name : 'Not assigned',
                    'staff_id' => $booking->staff_id
                ];
            });

        return response()->json([
            'bookings' => $bookings,
            'customer' => $customer->only(['name', 'phone', 'email', 'country_code'])
        ]);
    }

    public function reschedule(Request $request, $id)
    {
        if (!$this->isGuestBookingEnabled()) {
            return response()->json(['success' => false, 'message' => 'Guest booking system is disabled.'], 403);
        }

        $validated = $request->validate([
            'phone' => 'required|string|min:7|max:15',
            'country_code' => 'required|string|max:5',
            'email' => 'nullable|email|max:255',
            'date' => 'required|date|after:yesterday',
            'time' => 'required|date_format:H:i',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'sometimes|nullable|exists:users,id'
        ]);

        DB::beginTransaction();

        try {
            $salon = app()->bound('current_salon') ? app('current_salon') : null;

        if (!$salon) {
            abort(500, 'Salon context not available');
        }

            // Find customer by contact
            $customer = Customer::where('salon_id', $salon->id)
                ->where(function ($query) use ($validated) {
                    $query->where(function ($q) use ($validated) {
                        $q->where('phone', $validated['phone'])
                            ->where('country_code', $validated['country_code']);
                    });
                    if (!empty($validated['email'])) {
                        $query->orWhere('email', $validated['email']);
                    }
                })
                ->first();

            if (!$customer) {
                return response()->json(['success' => false, 'message' => 'Customer not found.'], 404);
            }

            // Find booking
            $booking = Booking::where('id', $id)
                ->where('customer_id', $customer->id)
                ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
                ->first();

            if (!$booking) {
                return response()->json(['success' => false, 'message' => 'Booking not found or cannot be rescheduled.'], 404);
            }

            $service = Service::findOrFail($validated['service_id']);

            $startTime = Carbon::createFromFormat('Y-m-d H:i', $validated['date'] . ' ' . $validated['time'], salon_timezone())->setTimezone('UTC');
            $endTime = $startTime->copy()->addMinutes($service->duration);

            // Check for conflicts if staff selected
            $allowOverlapping = $this->settingsService->get('allow_overlapping_bookings', false, $salon->id);

            if (!empty($validated['staff_id'] ?? null) && !$allowOverlapping) {
                $bufferTime = (int) $this->settingsService->get('appointment_buffer_time', 0, $salon->id);
                $checkEndTime = $endTime->copy()->addMinutes($bufferTime);

                $conflict = Booking::where('staff_id', $validated['staff_id'])
                    ->where('id', '!=', $id)
                    ->where('status', '!=', 'cancelled')
                    ->where(function ($query) use ($startTime, $checkEndTime) {
                        $query->where('start_time', '<', $checkEndTime)
                            ->where('end_time', '>', $startTime);
                    })
                    ->exists();

                if ($conflict) {
                    return response()->json(['success' => false, 'message' => 'This time slot is already booked.'], 422);
                }
            }

            $servicePrice = $service->price;
            $taxEnabledServices = $this->settingsService->get('tax_enabled_services', false, $salon->id);
            if ($taxEnabledServices) {
                $taxRate = $this->settingsService->get('tax_rate', 0, $salon->id);
                $servicePrice += $servicePrice * ($taxRate / 100);
            }

            // Update booking
            $booking->update([
                'service_id' => $validated['service_id'],
                'staff_id' => $validated['staff_id'] ?? null,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'amount' => $servicePrice
            ]);

            DB::commit();

            // Send reschedule confirmation
            // Send reschedule confirmation
            try {
                // Mail handled by BookingObserver


                // Notify Staff/Admin
                $admins = User::role(['super_admin', 'salon_admin', 'manager'])
                    ->where('salon_id', $salon->id)
                    ->get();

                if ($booking->staff_id) {
                    $staff = User::find($booking->staff_id);
                    if ($staff) {
                        $admins->push($staff);
                    }
                }

                Notification::send($admins->unique('id'), new BookingNotification($booking, 'rescheduled'));

            } catch (\Exception $e) {
                Log::warning('Failed to send reschedule email', ['error' => $e->getMessage()]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Booking rescheduled successfully.',
                'booking' => $booking->load(['service', 'staff'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Guest booking reschedule failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to reschedule booking.'], 500);
        }
    }

    /**
     * Find an available staff member for the given time slot
     * 
     * @param int $salonId
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param int|null $serviceId
     * @return User|null
     */
    private function findAvailableStaff($salonId, $startTime, $endTime, $serviceId = null)
    {
        // Get all active staff members for this salon
        $staffMembers = User::role('employee')
            ->where('salon_id', $salonId)
            ->where('status', 'active')
            ->get();

        if ($staffMembers->isEmpty()) {
            return null;
        }

        // Get buffer time setting
        $bufferTime = (int) $this->settingsService->get('appointment_buffer_time', 0, $salonId);
        $checkEndTime = $endTime->copy()->addMinutes($bufferTime);

        $date = $startTime->format('Y-m-d');
        $dayOfWeek = $startTime->dayOfWeek;

        // Find first available staff member
        foreach ($staffMembers as $staff) {
            // 1. Check if staff is working at this time (Schedule check)
            $isWorking = false;

            // Check daily override
            $dailySchedule = \App\Models\StaffDailySchedule::where('staff_id', $staff->id)
                ->where('date', $date)
                ->first();

            if ($dailySchedule) {
                if ($dailySchedule->is_working) {
                    // Check if slot is within working hours
                    $staffStart = Carbon::parse($date . ' ' . $dailySchedule->start_time, salon_timezone());
                    $staffEnd = Carbon::parse($date . ' ' . $dailySchedule->end_time, salon_timezone());
                    if ($startTime->gte($staffStart) && $endTime->lte($staffEnd)) {
                        $isWorking = true;
                    }
                }
            } else {
                // Check weekly schedule
                $weeklySchedule = \App\Models\StaffSchedule::where('staff_id', $staff->id)
                    ->where('day_of_week', $dayOfWeek)
                    ->first();

                if ($weeklySchedule && $weeklySchedule->is_working) {
                    $staffStart = Carbon::parse($date . ' ' . $weeklySchedule->start_time, salon_timezone());
                    $staffEnd = Carbon::parse($date . ' ' . $weeklySchedule->end_time, salon_timezone());
                    if ($startTime->gte($staffStart) && $endTime->lte($staffEnd)) {
                        $isWorking = true;
                    }
                }
            }

            if (!$isWorking) {
                continue; // Staff not working, skip
            }

            // 2. Check if this staff member has any conflicting bookings
            $hasConflict = Booking::where('staff_id', $staff->id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($startTime, $checkEndTime) {
                    $query->where('start_time', '<', $checkEndTime)
                        ->where('end_time', '>', $startTime);
                })
                ->exists();

            // If no conflict, this staff is available
            if (!$hasConflict) {
                return $staff;
            }
        }

        // No available staff found
        return null;
    }

    /**
     * Get formatted working hours from settings
     */
    private function getFormattedWorkingHours($salonId)
    {
        $start = $this->settingsService->get('working_hours_start', '08:00', $salonId);
        $end = $this->settingsService->get('working_hours_end', '20:00', $salonId);

        try {
            $startTime = Carbon::createFromFormat('H:i', $start)->format('g:i A');
            $endTime = Carbon::createFromFormat('H:i', $end)->format('g:i A');
            return $startTime . ' - ' . $endTime;
        } catch (\Exception $e) {
            return '9:00 AM - 9:00 PM'; // Fallback
        }
    }
}
