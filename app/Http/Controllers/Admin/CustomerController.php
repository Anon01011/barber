<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    protected $notificationService;

    public function __construct(\App\Services\NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of the customers.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Customer::where('salon_id', auth()->user()->salon_id)
                ->withCount('bookings')
                ->withMax('bookings as last_visit_at_from_bookings', 'start_time');

            // Search
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('customer_id', 'like', "%{$search}%");
                });
            }

            // Status Filter
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            // Segment Filter
            if ($request->has('segment') && !empty($request->segment)) {
                $now = \Carbon\Carbon::now();
                $sixtyDaysAgo = $now->copy()->subDays(60);
                $oneTwentyDaysAgo = $now->copy()->subDays(120);

                switch ($request->segment) {
                    case 'active':
                        $query->where('last_visit_at', '>=', $sixtyDaysAgo);
                        break;
                    case 'churn':
                        $query->where('last_visit_at', '<', $sixtyDaysAgo)
                            ->where('last_visit_at', '>=', $oneTwentyDaysAgo);
                        break;
                    case 'defected':
                        $query->where(function ($q) use ($oneTwentyDaysAgo) {
                            $q->where('last_visit_at', '<', $oneTwentyDaysAgo)
                                ->orWhere(function ($subQ) use ($oneTwentyDaysAgo) {
                                    $subQ->whereNull('last_visit_at')
                                        ->where('created_at', '<', $oneTwentyDaysAgo);
                                });
                        });
                        break;
                }
            }

            // Sorting
            $sort = $request->get('sort', 'name_asc');
            switch ($sort) {
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'recent':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'bookings_desc':
                    $query->orderBy('bookings_count', 'desc');
                    break;
                case 'bookings_asc':
                    $query->orderBy('bookings_count', 'asc');
                    break;
                case 'last_visit_desc':
                    $query->orderByRaw('COALESCE(last_visit_at, last_visit_at_from_bookings) DESC');
                    break;
                case 'last_visit_asc':
                    $query->orderByRaw('COALESCE(last_visit_at, last_visit_at_from_bookings) ASC');
                    break;
                case 'name_asc':
                default:
                    $query->orderBy('name', 'asc');
                    break;
            }

            $customers = $query->paginate(10);

            // Format the response data
            $formattedData = [
                'data' => $customers->getCollection()->map(function ($customer) {
                    $lastVisitDate = $customer->last_visit_at ?? $customer->last_visit_at_from_bookings;
                    $lastVisitHuman = $lastVisitDate ? \Carbon\Carbon::parse($lastVisitDate)->diffForHumans() : 'Never';

                    return [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'email' => $customer->display_email,
                        'phone' => $customer->display_phone,
                        'status' => $customer->status,
                        'created_at' => $customer->created_at,
                        'total_bookings' => $customer->bookings_count,
                        'last_visit' => $lastVisitHuman,
                        'is_guest' => $customer->is_guest
                    ];
                }),
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
                'per_page' => $customers->perPage(),
                'total' => $customers->total(),
                'from' => $customers->firstItem(),
                'to' => $customers->lastItem()
            ];

            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);
        }

        // Calculate counts for the dashboard cards
        $salonId = auth()->user()->salon_id;
        $now = \Carbon\Carbon::now();
        $sixtyDaysAgo = $now->copy()->subDays(60);
        $oneTwentyDaysAgo = $now->copy()->subDays(120);

        $counts = [
            'total' => Customer::where('salon_id', $salonId)->count(),
            'active' => Customer::where('salon_id', $salonId)
                ->where('last_visit_at', '>=', $sixtyDaysAgo)
                ->count(),
            'churn' => Customer::where('salon_id', $salonId)
                ->where('last_visit_at', '<', $sixtyDaysAgo)
                ->where('last_visit_at', '>=', $oneTwentyDaysAgo)
                ->count(),
            'defected' => Customer::where('salon_id', $salonId)
                ->where(function ($q) use ($oneTwentyDaysAgo) {
                    $q->where('last_visit_at', '<', $oneTwentyDaysAgo)
                        ->orWhere(function ($subQ) use ($oneTwentyDaysAgo) {
                            $subQ->whereNull('last_visit_at')
                                ->where('created_at', '<', $oneTwentyDaysAgo);
                        });
                })
                ->count(),
        ];

        return view('admin.customers.index', compact('counts'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required_without:name|string|max:255',
            'name' => 'required_without:first_name|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('customers')->where('salon_id', auth()->user()->salon_id)],
            'phone' => ['required', 'string', 'max:20', Rule::unique('customers')->where('salon_id', auth()->user()->salon_id)],
            'country_code' => 'nullable|string|max:10',
            'secondary_number' => 'nullable|string|max:20',
            'secondary_country_code' => 'nullable|string|max:10',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date|before:today',
            'anniversary' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'preferred_contact' => 'nullable|in:email,phone,sms',
            'send_promotional_sms' => 'nullable|boolean',
            'send_transactional_sms' => 'nullable|boolean',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'medical_notes' => 'nullable|string|max:1000',
            'custom_fields' => 'nullable|array',
        ], [
            'email.required_if' => 'The email field is required when email is selected as preferred contact method.',
            'first_name.required' => 'First name is required.',
            'dob.before' => 'Date of birth must be in the past.',
            'email.unique' => 'This email address is already registered to another customer.',
            'phone.unique' => 'This phone number is already registered to another customer.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Enforce customer limit based on salon plan
            $salon = auth()->user()->salon;
            if ($salon && !$salon->canAddCustomer()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Customer limit reached for your current plan. Please upgrade to add more customers.'
                ], 403);
            }

            $data = $request->all();
            $data['salon_id'] = auth()->user()->salon_id; // Force salon_id

            // Handle name/first_name/last_name logic
            if (empty($data['first_name']) && !empty($data['name'])) {
                $parts = explode(' ', trim($data['name']), 2);
                $data['first_name'] = $parts[0];
                $data['last_name'] = $parts[1] ?? '';
            } elseif (!empty($data['first_name']) && empty($data['name'])) {
                $data['name'] = trim($data['first_name'] . ' ' . ($data['last_name'] ?? ''));
            }

            // Set default SMS preferences if not provided
            $data['send_promotional_sms'] = $request->input('send_promotional_sms', true);
            $data['send_transactional_sms'] = $request->input('send_transactional_sms', true);

            // Set default preferred contact if not provided
            if (empty($data['preferred_contact'])) {
                $data['preferred_contact'] = !empty($data['email']) ? 'email' : 'phone';
            }

            $customer = Customer::create($data);

            // Check if new customer notifications are enabled
            $settingsService = app(\App\Services\SettingsService::class);
            $notifyNewCustomer = $settingsService->get('notify_new_customer', false, auth()->user()->salon_id);

            if ($notifyNewCustomer) {
                // Get notification email or use business email
                $notificationEmail = $settingsService->get('notification_email', null, auth()->user()->salon_id);
                if (!$notificationEmail) {
                    $notificationEmail = $settingsService->get('business_email', null, auth()->user()->salon_id);
                }

                if ($notificationEmail) {
                    try {
                        \Mail::raw(
                            "New Customer Registered!\n\n" .
                            "Name: {$customer->name}\n" .
                            "Email: {$customer->email}\n" .
                            "Phone: {$customer->phone}\n" .
                            "Registered: " . now()->format('Y-m-d H:i:s'),
                            function ($message) use ($notificationEmail, $customer) {
                                $message->to($notificationEmail)
                                    ->subject("New Customer: {$customer->name}");
                            }
                        );
                    } catch (\Exception $e) {
                        \Log::error("Failed to send new customer notification: " . $e->getMessage());
                    }
                }
            }

            // Send welcome email
            try {
                $this->notificationService->sendWelcomeEmailToCustomer($customer);
            } catch (\Exception $e) {
                Log::warning('Failed to send welcome email to customer', [
                    'customer_id' => $customer->id,
                    'error' => $e->getMessage()
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Customer created successfully',
                'customer' => $customer
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create customer: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Customer $customer)
    {
        // Verify salon scope
        if ($customer->salon_id !== auth()->user()->salon_id) {
            abort(403, 'Unauthorized access to customer');
        }

        try {
            // Fetch upcoming bookings
            $upcomingBookings = $customer->bookings()
                ->whereIn('status', ['confirmed', 'arrived'])
                ->with(['service', 'staff'])
                ->orderBy('start_time', 'asc')
                ->take(10)
                ->get();

            // Fetch past bookings
            $pastBookings = $customer->bookings()
                ->whereIn('status', ['completed', 'cancelled', 'no_show', 'rejected', 'paid'])
                ->with(['service', 'staff'])
                ->latest()
                ->take(10)
                ->get();

            // Calculate total visits (completed bookings)
            $totalVisits = $customer->bookings()->where('status', 'completed')->count();

            // Calculate last visit
            $lastVisit = 'Never';
            if ($customer->last_visit_at) {
                $lastVisit = $customer->last_visit_at->diffForHumans();
            } else {
                $lastBooking = $customer->bookings()
                    ->where('status', 'completed')
                    ->latest('start_time')
                    ->first();
                if ($lastBooking && $lastBooking->start_time) {
                    $lastVisit = \Carbon\Carbon::parse($lastBooking->start_time)->diffForHumans();
                }
            }

            // Calculate preferred service
            $preferredService = $customer->preferred_service;
            if (!$preferredService) {
                $preferredService = $customer->preferred_service_name;
            }

            // Map function for bookings
            $mapBooking = function ($booking) {
                return [
                    'id' => $booking->id,
                    'amount' => $booking->amount ?? 0.00,
                    'service' => [
                        'id' => $booking->service_id ?? null,
                        'name' => optional($booking->service)->name ?? 'Service not found',
                        'price' => $booking->amount ?? 0.00
                    ],
                    'staff' => [
                        'id' => $booking->staff_id,
                        'name' => optional($booking->staff)->name ?? 'Unassigned'
                    ],
                    'status' => $booking->status ?? 'unknown',
                    'start_time' => $booking->start_time,
                    'formatted_date' => $booking->start_time ?
                        \Carbon\Carbon::parse($booking->start_time)->format('M d, Y h:i A') :
                        ($booking->created_at ? $booking->created_at->format('M d, Y h:i A') : 'N/A')
                ];
            };

            // Format the customer data
            $formattedCustomer = [
                'id' => $customer->id,
                'name' => $customer->name ?? 'N/A',
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'full_name' => $customer->name,
                'customer_id' => $customer->customer_id,
                'email' => $customer->display_email ?? 'N/A',
                'phone' => $customer->display_phone ?? 'N/A',
                'secondary_number' => $customer->secondary_number,
                'address' => $customer->address ?? 'N/A',
                'location' => $customer->location,
                'notes' => $customer->notes ?? 'No notes available',
                'medical_notes' => $customer->medical_notes,
                'status' => $customer->status ?? 'inactive',
                'preferred_contact' => $customer->preferred_contact ?
                    ucfirst($customer->preferred_contact) : 'Not specified',
                'gender' => $customer->gender,
                'dob' => $customer->dob,
                'anniversary' => $customer->anniversary,
                'source' => $customer->source,
                'send_promotional_sms' => (bool) $customer->send_promotional_sms,
                'send_transactional_sms' => (bool) $customer->send_transactional_sms,
                'created_at' => $customer->created_at,
                'total_bookings' => $customer->bookings()->count(),
                'total_visits' => $totalVisits,
                'last_visit' => $lastVisit,
                'preferred_service' => $preferredService ?? 'None',
                'currency_symbol' => currency_symbol(),
                'upcoming_bookings' => $upcomingBookings->map($mapBooking),
                'past_bookings' => $pastBookings->map($mapBooking),
            ];

            return response()->json([
                'success' => true,
                'data' => $formattedCustomer
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error fetching customer data: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load customer data: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified customer.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function edit(Customer $customer)
    {
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $customer->id,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'name' => $customer->name,
                    'customer_id' => $customer->customer_id,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'country_code' => $customer->country_code ?? '+91',
                    'secondary_number' => $customer->secondary_number,
                    'secondary_country_code' => $customer->secondary_country_code,
                    'gender' => $customer->gender,
                    'dob' => optional($customer->dob)->format('Y-m-d'),
                    'anniversary' => optional($customer->anniversary)->format('Y-m-d'),
                    'location' => $customer->location,
                    'source' => $customer->source,
                    'preferred_contact' => $customer->preferred_contact,
                    'status' => $customer->status,
                    'address' => $customer->address,
                    'notes' => $customer->notes,
                    'medical_notes' => $customer->medical_notes,
                    'send_promotional_sms' => (bool) $customer->send_promotional_sms,
                    'send_transactional_sms' => (bool) $customer->send_transactional_sms,
                    'custom_fields' => $customer->custom_fields,
                ]
            ]);
        }

        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        // Verify salon scope
        if ($customer->salon_id !== auth()->user()->salon_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required_without:name|string|max:255',
            'name' => 'required_without:first_name|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('customers')->where('salon_id', auth()->user()->salon_id)->ignore($customer->id)],
            'phone' => ['required', 'string', 'max:20', Rule::unique('customers')->where('salon_id', auth()->user()->salon_id)->ignore($customer->id)],
            'country_code' => 'nullable|string|max:10',
            'secondary_number' => 'nullable|string|max:20',
            'secondary_country_code' => 'nullable|string|max:10',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date|before:today',
            'anniversary' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'preferred_contact' => 'nullable|in:email,phone,sms',
            'send_promotional_sms' => 'nullable|boolean',
            'send_transactional_sms' => 'nullable|boolean',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'medical_notes' => 'nullable|string|max:1000',
            'custom_fields' => 'nullable|array',
        ], [
            'email.required_if' => 'The email field is required when email is selected as preferred contact method.',
            'first_name.required' => 'First name is required.',
            'dob.before' => 'Date of birth must be in the past.',
            'email.unique' => 'This email address is already registered to another customer.',
            'phone.unique' => 'This phone number is already registered to another customer.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();

            // Handle name/first_name/last_name logic
            if (empty($data['first_name']) && !empty($data['name'])) {
                $parts = explode(' ', trim($data['name']), 2);
                $data['first_name'] = $parts[0];
                $data['last_name'] = $parts[1] ?? '';
            } elseif (!empty($data['first_name']) && empty($data['name'])) {
                $data['name'] = trim($data['first_name'] . ' ' . ($data['last_name'] ?? ''));
            }

            $customer->update($data);
            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {
        // Verify salon scope
        if ($customer->salon_id !== auth()->user()->salon_id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        // Check if customer has any bookings
        if ($customer->bookings()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete customer because they have existing bookings. You can mark them as inactive instead.'
            ], 422);
        }

        try {
            $customer->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Customer deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update customer notes
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateNotes(Request $request, Customer $customer)
    {
        // Verify salon scope
        if ($customer->salon_id !== auth()->user()->salon_id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'notes' => 'nullable|string',
            'medical_notes' => 'nullable|string',
        ]);

        try {
            $data = [];
            if ($request->has('notes')) {
                $data['notes'] = $request->input('notes');
            }
            if ($request->has('medical_notes')) {
                $data['medical_notes'] = $request->input('medical_notes');
            }

            $customer->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Customer notes updated successfully',
                'customer' => $customer->only(['id', 'name', 'notes', 'medical_notes'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update customer notes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customers for datatable.
     */
    /**
     * Get customers list with optional search and filters
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCustomers(Request $request)
    {
        $query = Customer::where('salon_id', auth()->user()->salon_id);

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        return response()->json($query->get());
    }

    /**
     * Export customers to CSV
     */
    public function export()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="customers_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Name',
                'Email',
                'Phone',
                'Preferred Contact',
                'Address',
                'Status',
                'Member Since',
                'Last Visit'
            ]);

            // Use cursor for memory efficiency
            $customers = Customer::where('salon_id', auth()->user()->salon_id)
                ->select([
                    'name',
                    'email',
                    'phone',
                    'preferred_contact',
                    'address',
                    'status',
                    'created_at',
                    'last_visit_at'
                ])
                ->cursor();

            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->name,
                    $customer->display_email,
                    $customer->display_phone,
                    $customer->preferred_contact,
                    $customer->address,
                    $customer->status,
                    $customer->created_at->format('Y-m-d'),
                    $customer->last_visit_at ? $customer->last_visit_at->format('Y-m-d') : 'Never'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import customers from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        try {
            $file = $request->file('file');
            $handle = fopen($file->getRealPath(), 'r');

            // Skip header row
            $header = fgetcsv($handle);

            $imported = 0;
            $errors = [];
            $row = 1;

            while (($data = fgetcsv($handle)) !== false) {
                $row++;

                try {
                    // Validate required fields
                    if (empty($data[0]) || empty($data[2])) {
                        $errors[] = "Row {$row}: Missing required fields (name, phone)";
                        continue;
                    }

                    $email = !empty($data[1]) ? trim($data[1]) : null;

                    if ($email) {
                        // Check for duplicate email
                        $exists = Customer::where('salon_id', auth()->user()->salon_id)
                            ->where('email', $email)
                            ->exists();

                        if ($exists) {
                            $errors[] = "Row {$row}: Customer with email {$email} already exists";
                            continue;
                        }

                        // Validate email format
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $errors[] = "Row {$row}: Invalid email format: {$email}";
                            continue;
                        }
                    }

                    $phone = !empty($data[2]) ? trim($data[2]) : null;

                    if ($phone) {
                        // Check for duplicate phone
                        $existsPhone = Customer::where('salon_id', auth()->user()->salon_id)
                            ->where('phone', $phone)
                            ->exists();

                        if ($existsPhone) {
                            $errors[] = "Row {$row}: Customer with phone {$phone} already exists";
                            continue;
                        }
                    }

                    Customer::create([
                        'salon_id' => auth()->user()->salon_id,
                        'name' => $data[0],
                        'email' => $email,
                        'phone' => $data[2],
                        'preferred_contact' => isset($data[3]) && in_array($data[3], ['email', 'phone', 'sms']) ? $data[3] : 'email',
                        'address' => $data[4] ?? null,
                        'status' => 'active',
                    ]);

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Row {$row}: " . $e->getMessage();
                }
            }

            fclose($handle);

            $message = "Successfully imported {$imported} customer(s).";
            if (count($errors) > 0) {
                $message .= " " . count($errors) . " error(s) occurred.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $imported,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Customer import failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download customer import template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="customer_import_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Name (Required)',
                'Email (Optional)',
                'Phone (Required)',
                'Preferred Contact (email/phone/sms)',
                'Address'
            ]);

            // Add example row
            fputcsv($file, [
                'John Doe',
                'john@example.com',
                '+1234567890',
                'email',
                '123 Main St, City'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display detailed view of customer with all related data
     */
    public function showDetails(Customer $customer)
    {
        \Log::info('DEBUG: showDetails hit for customer ' . $customer->id);

        // For AJAX requests (from booking modal), return JSON
        if (request()->wantsJson() || request()->ajax()) {

            // 1. Upcoming Appointments
            try {
                $upcomingAppointments = $customer->bookings()
                    ->where('start_time', '>', now())
                    ->whereIn('status', ['confirmed', 'arrived']) // Show confirmed and arrived
                    ->with(['service', 'staff'])
                    ->orderBy('start_time', 'asc')
                    ->limit(5)
                    ->get()
                    ->map(function ($booking) {
                        $startTime = \Carbon\Carbon::parse($booking->start_time);
                        return [
                            'service_name' => optional($booking->service)->name ?? 'N/A',
                            'date' => $startTime->format('d M Y'),
                            'time' => $startTime->format('h:i A'),
                            'staff_name' => optional($booking->staff)->name ?? 'Unassigned'
                        ];
                    });
            } catch (\Exception $e) {
                \Log::error('Error fetching upcoming appointments: ' . $e->getMessage());
                $upcomingAppointments = [
                    [
                        'service_name' => 'Error: ' . substr($e->getMessage(), 0, 20),
                        'date' => '-',
                        'time' => '-',
                        'staff_name' => '-'
                    ]
                ];
            }

            // 2. Last 5 Visits
            try {
                $lastVisits = $customer->bookings()
                    ->where(function ($query) {
                        $query->whereIn('status', ['completed', 'staff_completed'])
                            ->orWhere(function ($q) {
                                $q->where('start_time', '<=', now())
                                    ->where('status', '!=', 'cancelled');
                            });
                    })
                    ->with(['service', 'staff'])
                    ->orderBy('start_time', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function ($booking) {
                        return [
                            'service_name' => optional($booking->service)->name ?? 'N/A',
                            'date' => \Carbon\Carbon::parse($booking->start_time)->format('d M Y'),
                            'staff_name' => optional($booking->staff)->name ?? 'Unassigned',
                            'status' => $booking->status,
                            'price' => number_format(floatval($booking->amount ?? 0), 2)
                        ];
                    });
            } catch (\Exception $e) {
                \Log::error('Error fetching last visits: ' . $e->getMessage());
                $lastVisits = [
                    [
                        'service_name' => 'Error: ' . substr($e->getMessage(), 0, 20),
                        'date' => '-',
                        'staff_name' => '-',
                        'status' => 'error',
                        'price' => '0.00'
                    ]
                ];
            }

            // 3. Favorite Staff
            $favoriteStaffName = '-';
            $favoriteStaffCount = 0;
            try {
                $favoriteStaff = \DB::table('bookings')
                    ->join('users', 'bookings.staff_id', '=', 'users.id')
                    ->where('bookings.customer_id', $customer->id)
                    ->where('bookings.status', 'completed')
                    ->select('users.name', \DB::raw('COUNT(*) as booking_count'))
                    ->groupBy('users.id', 'users.name')
                    ->orderBy('booking_count', 'desc')
                    ->first();
                if ($favoriteStaff) {
                    $favoriteStaffName = $favoriteStaff->name;
                    $favoriteStaffCount = $favoriteStaff->booking_count;
                }
            } catch (\Exception $e) {
                \Log::warning('Could not fetch favorite staff: ' . $e->getMessage());
                $favoriteStaffName = 'Error';
            }

            // 4. Final Response Construction
            return response()->json([
                'id' => $customer->id,
                'name' => $customer->name ?? '-',
                'email' => $customer->display_email ?? $customer->email ?? '-',
                'phone' => $customer->display_phone ?? $customer->phone ?? '-',
                'country_code' => $customer->country_code ?? '+91',
                'secondary_country_code' => $customer->secondary_country_code,
                'status' => ucfirst($customer->status ?? 'active'),
                'dob' => $customer->dob ? $customer->dob->format('Y-m-d') : '-',
                'anniversary' => $customer->anniversary ? $customer->anniversary->format('Y-m-d') : '-',
                'favorite_staff' => $favoriteStaffName,
                'favorite_staff_count' => $favoriteStaffCount,

                'last_visit' => rescue(function () use ($customer) {
                    return ($lastVisitTime = $customer->bookings()
                        ->where('start_time', '<=', now()) // Only past or present
                        ->whereIn('status', ['confirmed', 'arrived', 'started', 'completed', 'staff_completed'])
                        ->latest('start_time')->value('start_time'))
                        ? \Carbon\Carbon::parse($lastVisitTime)->format('d M Y')
                        : 'Never';
                }, 'Error'),

                'total_visits' => rescue(function () use ($customer) {
                    return $customer->bookings()
                        ->where('start_time', '<=', now()) // Only past or present
                        ->whereIn('status', ['confirmed', 'arrived', 'started', 'completed', 'staff_completed'])
                        ->select(\DB::raw('count(distinct COALESCE(booking_group_id, id)) as visit_count'))
                        ->value('visit_count') ?? 0;
                }, 0),

                'total_revenue' => rescue(function () use ($customer) {
                    return number_format(
                        (float) ($customer->bookings()->where('status', 'completed')->whereDoesntHave('posSaleItem')->sum('amount') ?? 0) +
                        (float) ($customer->posSales()->where('status', '!=', 'voided')->sum(\DB::raw('cash_amount + card_amount + online_amount + other_amount')) ?? 0),
                        2
                    );
                }, '0.00'),

                'reward_points' => $customer->reward_points ?? 0,

                'unpaid_balance' => rescue(function () use ($customer) {
                    return number_format($customer->posSales()->whereIn('payment_status', ['pending', 'partial', 'unpaid'])->sum('outstanding_amount') ?? 0, 2);
                }, '0.00'),

                'prepaid_balance' => '0.00',
                'medical_notes' => $customer->medical_notes ?? null,
                'upcoming_appointments' => $upcomingAppointments,
                'recent_visits' => $lastVisits,
            ]);
        }



        // Initialize stats with default values
        $stats = [
            'total_bookings' => 0,
            'total_spent' => 0,
            'favorite_service' => 'N/A',
            'last_visit' => null,
            'avg_visit_value' => 0,
            'cancellation_rate' => 0,
            'bookings_change' => 0,
            'spent_change' => 0
        ];

        try {
            // Load memberships and ratings
            $customer->load([
                'memberships.membership' => function ($query) {
                    $query->select('id', 'name', 'membership_type', 'validity_value', 'validity_unit');
                },
                'ratings' => function ($query) {
                    $query->with(['service', 'staff', 'booking'])
                        ->orderBy('created_at', 'desc');
                }
            ]);

            // Manually fetch bookings to ensure we get all branches for this customer while strictly scoping to their salon
            $allBookings = \App\Models\Booking::withoutGlobalScopes()
                ->where('customer_id', $customer->id)
                ->where('salon_id', $customer->salon_id)
                ->with([
                    'service' => function ($q) {
                        $q->select('id', 'name', 'price', 'duration', 'category_id');
                    },
                    'service.category' => function ($q) {
                        $q->select('id', 'name');
                    },
                    'staff' => function ($q) {
                        $q->select('id', 'name', 'email');
                    },
                    'package' => function ($q) {
                        $q->select('id', 'name', 'type');
                    }
                ])
                ->orderBy('start_time', 'desc')
                ->get();

            \Log::info('Customer Details Debug', [
                'customer_id' => $customer->id,
                'salon_id' => $customer->salon_id,
                'bookings_count' => $allBookings->count(),
                'first_booking' => $allBookings->first()
            ]);

            // Set the relation on the customer object so the view can use it
            $customer->setRelation('bookings', $allBookings);

            // Calculate current month statistics
            $currentMonth = now()->startOfMonth();
            $lastMonth = now()->subMonth()->startOfMonth();
            $lastMonthEnd = now()->subMonth()->endOfMonth();

            // Current Month Stats
            $currentMonthBookings = $allBookings->filter(function ($booking) use ($currentMonth) {
                return \Carbon\Carbon::parse($booking->start_time)->greaterThanOrEqualTo($currentMonth);
            });

            $currentMonthCount = $currentMonthBookings->count();
            $currentMonthRevenue = $currentMonthBookings->sum('amount');

            // Last Month Stats
            $lastMonthBookings = $allBookings->filter(function ($booking) use ($lastMonth, $lastMonthEnd) {
                $startTime = \Carbon\Carbon::parse($booking->start_time);
                return $startTime->greaterThanOrEqualTo($lastMonth) && $startTime->lessThanOrEqualTo($lastMonthEnd);
            });

            $lastMonthCount = $lastMonthBookings->count();
            $lastMonthRevenue = $lastMonthBookings->sum('amount');

            // Calculate stats from the fetched bookings collection for accuracy
            $totalBookings = $allBookings->count();
            // Calculate total spent from completed bookings
            // Exclude bookings that were paid via POS to avoid double counting
            $completedBookings = $allBookings->where('status', 'completed');

            // We need to filter the collection to exclude bookings that have a POS sale item
            $bookingSpent = $completedBookings->filter(function ($booking) {
                return !$booking->posSaleItem()->exists();
            })->sum(function ($booking) {
                return floatval($booking->amount ?? 0);
            });

            // Calculate total spent from POS sales (paid sales only)
            // Sum all payment amounts (cash, card, online, other) for non-voided sales
            $posSpent = $customer->posSales()
                ->where('status', '!=', 'voided')
                ->sum(\DB::raw('cash_amount + card_amount + online_amount + other_amount'));

            // Total spent is the sum of booking spent and POS spent
            $totalSpent = $bookingSpent + $posSpent;

            $completedCount = $completedBookings->count();
            $cancelledCount = $allBookings->where('status', 'cancelled')->count();

            // Get last visit from completed bookings, or from any booking if no completed ones
            $lastVisit = $completedBookings->sortByDesc('start_time')->first();
            if (!$lastVisit && $allBookings->count() > 0) {
                // If no completed bookings, use the most recent booking
                $lastVisit = $allBookings->sortByDesc('start_time')->first();
            }
            $lastVisitDate = $lastVisit ? $lastVisit->start_time : null;

            // Calculate unpaid balance from POS sales (invoices) - check multiple payment statuses
            $unpaidBalance = $customer->posSales()
                ->whereIn('payment_status', ['pending', 'partial', 'unpaid'])
                ->sum('outstanding_amount') ?: $customer->posSales()
                    ->whereIn('payment_status', ['pending', 'partial', 'unpaid'])
                    ->sum('total');

            $avgVisitValue = $completedCount > 0 ? ($totalSpent / $completedCount) : 0;

            // Calculate percentage changes - handle edge cases better
            $bookingsChange = 0;
            if ($lastMonthCount > 0) {
                // Normal case: both months have data
                $bookingsChange = (($currentMonthCount - $lastMonthCount) / $lastMonthCount) * 100;
            } elseif ($currentMonthCount > 0 && $lastMonthCount == 0) {
                // New bookings this month, none last month
                $bookingsChange = 100; // 100% increase (from 0 to current)
            } elseif ($currentMonthCount == 0 && $lastMonthCount > 0) {
                // No bookings this month, had bookings last month
                $bookingsChange = -100; // 100% decrease
            }
            // else both are 0, change stays 0

            $spentChange = 0;
            if ($lastMonthRevenue > 0) {
                // Normal case: both months have revenue
                $spentChange = (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
            } elseif ($currentMonthRevenue > 0 && $lastMonthRevenue == 0) {
                // New revenue this month, none last month
                $spentChange = 100; // 100% increase (from 0 to current)
            } elseif ($currentMonthRevenue == 0 && $lastMonthRevenue > 0) {
                // No revenue this month, had revenue last month
                $spentChange = -100; // 100% decrease
            }
            // else both are 0, change stays 0

            // Get favorite service using the accessor
            $favoriteServiceName = $customer->preferred_service_name ?? 'N/A';

            $stats = [
                'total_bookings' => $totalBookings ?? 0,
                'total_spent' => $totalSpent ?? 0,
                'last_visit' => $lastVisitDate,
                'avg_visit_value' => $avgVisitValue ?? 0,
                'cancelled_count' => $cancelledCount ?? 0,
                'bookings_change' => $bookingsChange ?? 0,
                'spent_change' => $spentChange ?? 0,
                'favorite_service' => $favoriteServiceName,
                'unpaid_balance' => $unpaidBalance ?? 0
            ];

        } catch (\Exception $e) {
            // Log the error and continue with default stats
            \Log::error('Error loading customer details: ' . $e->getMessage());
        }

        // Load package data
        $packageData = [];
        try {
            // Get active package balances (remaining services)
            $activePackageBalances = $customer->activePackageBalances()
                ->with(['package', 'service'])
                ->get()
                ->groupBy('package_id');

            // Get package purchase history from POS sales
            $packagePurchases = $customer->packagePurchases()
                ->with(['package', 'sale'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('package_id');

            // Get bookings that used packages
            $packageBookings = $customer->packageBookings()
                ->with(['service', 'staff', 'package'])
                ->orderBy('start_time', 'desc')
                ->get();

            // Calculate package statistics
            $packageStats = [];
            foreach ($activePackageBalances as $packageId => $balances) {
                try {
                    $firstBalance = $balances->first();
                    if (!$firstBalance || !$firstBalance->package) {
                        continue;
                    }

                    $package = $firstBalance->package;
                    $totalServices = $balances->sum(function ($balance) {
                        return $balance->quantity_remaining ?? 0;
                    });
                    $servicesCount = $balances->count();

                    $packageStats[$packageId] = [
                        'package' => $package,
                        'total_remaining' => $totalServices,
                        'services_count' => $servicesCount,
                        'balances' => $balances
                    ];
                } catch (\Exception $e) {
                    \Log::warning('Error processing package balance: ' . $e->getMessage());
                    continue;
                }
            }

            $packageData = [
                'active_balances' => $activePackageBalances,
                'purchases' => $packagePurchases,
                'bookings' => $packageBookings,
                'stats' => $packageStats
            ];
        } catch (\Exception $e) {
            \Log::error('Error loading package data: ' . $e->getMessage());
            $packageData = [
                'active_balances' => collect(),
                'purchases' => collect(),
                'bookings' => collect(),
                'stats' => []
            ];
        }

        return view('admin.customers.details', compact('customer', 'stats', 'packageData'));
    }

    /**
     * Get customer's bill activity (bookings and POS sales)
     * 
     * @param  string $salonSlug
     * @param  int  $customerId
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Get customer's bill activity (bookings and POS sales)
     * 
     * @param  string $salonSlug
     * @param  int  $customerId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBillActivity($customerId)
    {
        try {
            $salonId = auth()->user()->salon_id;
            $customer = Customer::where('salon_id', $salonId)->findOrFail($customerId);

            // Get timezone settings
            $settings = app(\App\Services\SettingsService::class);
            $timezone = $settings->get('timezone', config('app.timezone'), $salonId);

            // Get bookings with services and staff
            // Exclude bookings that are linked to a POS sale to avoid duplicates
            $bookings = \App\Models\Booking::where('customer_id', $customerId)
                ->where('salon_id', $salonId)
                ->doesntHave('posSaleItem')
                ->with(['service', 'staff', 'branch'])
                ->whereIn('status', ['completed', 'staff_completed'])
                ->get()
                ->map(function ($booking) use ($timezone) {
                    $date = $booking->start_time ? \Carbon\Carbon::parse($booking->start_time)->setTimezone($timezone) : null;

                    return [
                        'id' => $booking->id,
                        'type' => 'booking',
                        'bill_no' => 'BKG-' . str_pad($booking->id, 3, '0', STR_PAD_LEFT),
                        'branch' => optional($booking->branch)->name ?? 'N/A',
                        'date' => $date ? $date->format('d M Y') : 'N/A',
                        'datetime' => $date ? $date->format('l, d M Y h:i A') : 'N/A',
                        'grand_total' => $booking->amount ?? 0,
                        'payment_status' => ucfirst($booking->payment_status ?? 'pending'),
                        'payment_method' => ucfirst($booking->payment_method ?? '-'),
                        'feedback' => $booking->rating ? 'Yes' : '-',
                        'services' => [
                            [
                                'name' => optional($booking->service)->name ?? 'Unknown',
                                'staff' => optional($booking->staff)->name ?? 'Unassigned',
                                'total' => $booking->amount ?? 0
                            ]
                        ],
                        'paid_amount' => $booking->payment_status === 'paid' ? ($booking->amount ?? 0) : (($booking->payment_status === 'partial' && $booking->posSaleItem && $booking->posSaleItem->sale) ? round($booking->posSaleItem->sale->total - $booking->posSaleItem->sale->outstanding_amount, 2) : 0),
                        'outstanding_amount' => $booking->payment_status === 'paid' ? 0 : (($booking->payment_status === 'partial' && $booking->posSaleItem && $booking->posSaleItem->sale) ? $booking->posSaleItem->sale->outstanding_amount : ($booking->amount ?? 0)),
                        'feedback_details' => $booking->rating ? [
                            'rating' => $booking->rating,
                            'review' => $booking->review
                        ] : null
                    ];
                });

            // Get POS sales with items and staff
            $posSales = \App\Models\PosSale::where('customer_id', $customerId)
                ->where('salon_id', $salonId)
                ->with(['items.item', 'items.staff', 'employee', 'branch'])
                ->get()
                ->map(function ($sale) use ($timezone) {
                    $date = $sale->created_at ? $sale->created_at->setTimezone($timezone) : null;

                    $services = $sale->items->map(function ($item) use ($sale) {
                        // Determine Type (logic from PosController)
                        $type = 'Product';
                        if ($item->item_type) {
                            $type = class_basename($item->item_type);
                        } elseif (str_contains(strtolower($item->item_name ?? ''), 'service') || $item->service_id) {
                            $type = 'Service';
                        } elseif (str_contains(strtolower($item->item_name ?? ''), 'package') || $item->package_id) {
                            $type = 'Package';
                        }

                        return [
                            'name' => $item->item_name ?? optional($item->item)->name ?? 'Unknown',
                            'staff' => optional($item->staff)->name ?? optional($sale->employee)->name ?? 'N/A',
                            'total' => $item->total,
                            'type' => $type
                        ];
                    })->toArray();

                    return [
                        'id' => $sale->id,
                        'type' => 'pos',
                        'bill_no' => $sale->invoice_number ?? ('FST-' . str_pad($sale->id, 3, '0', STR_PAD_LEFT)),
                        'branch' => optional($sale->branch)->name ?? 'N/A',
                        'date' => $date ? $date->format('d M Y') : 'N/A',
                        'datetime' => $date ? $date->format('l, d M Y h:i A') : 'N/A',
                        'grand_total' => $sale->total,
                        'payment_status' => ucfirst(str_replace('_', ' ', $sale->payment_status ?? 'pending')),
                        'payment_method' => ucfirst(str_replace('_', ' ', $sale->payment_method ?? '-')),
                        'feedback' => $sale->feedback ? 'Yes' : '-',
                        'services' => $services,
                        'feedback_details' => $sale->feedback ? json_decode($sale->feedback, true) : null,
                        'paid_amount' => round($sale->total - $sale->outstanding_amount, 2),
                        'outstanding_amount' => $sale->outstanding_amount ?? 0,
                    ];
                });


            // Merge and sort by date (most recent first)
            $bills = $bookings->concat($posSales)->sortByDesc('date')->values();

            return response()->json([
                'success' => true,
                'bills' => $bills,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->display_phone
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching bill activity: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching bill activity'
            ], 500);
        }
    }
    /**
     * Toggle customer status
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Customer $customer)
    {
        // Verify salon scope
        if ($customer->salon_id !== auth()->user()->salon_id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        try {
            $newStatus = $customer->status === 'active' ? 'inactive' : 'active';
            $customer->update(['status' => $newStatus]);

            $message = $newStatus === 'active'
                ? 'Customer activated successfully.'
                : 'Customer deactivated successfully.';

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}