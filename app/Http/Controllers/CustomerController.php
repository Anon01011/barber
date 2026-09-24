<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerWelcomeMail;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Customer::query();

            // Search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            // Status filter
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'name_asc');
            switch ($sortBy) {
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'recent':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                default:
                    $query->orderBy('name', 'asc');
            }

            $customers = $query->paginate(10);

            // If it's an AJAX request, return JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $customers
                ]);
            }

            // For regular requests, return the view
            return view('customer.index', [
                'customers' => $customers
            ]);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load customers: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to load customers: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'required_if:preferred_contact,email',
                \Illuminate\Validation\Rule::unique('customers')->where(function ($query) {
                    return $query->where('salon_id', auth()->user()->salon_id);
                })
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                \Illuminate\Validation\Rule::unique('customers')->where(function ($query) {
                    return $query->where('salon_id', auth()->user()->salon_id);
                })
            ],
            'preferred_contact' => 'required|in:email,phone,sms',
            'address' => 'nullable|string',
            'notes' => 'nullable|string'
        ], [
            'email.required_if' => 'The email field is required when email is selected as preferred contact method.',
            'email.unique' => 'This email address is already registered to another customer.',
            'phone.unique' => 'This phone number is already registered to another customer.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'preferred_contact' => $request->preferred_contact,
                'address' => $request->address,
                'notes' => $request->notes,
                'status' => 'active'
            ]);

            // Send Welcome Email
            try {
                if ($customer->email) {
                    Mail::to($customer->email)->send(new CustomerWelcomeMail($customer));
                    Log::info('Customer welcome email sent', ['customer_id' => $customer->id]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send customer welcome email', ['error' => $e->getMessage()]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create customer'
            ], 500);
        }
    }

    public function show(Customer $customer)
    {
        $customer->load([
            'bookings' => function ($query) {
                $query->with([
                    'service' => function ($q) {
                        $q->select('id', 'name', 'price'); // Make sure price is selected
                    },
                    'staff'
                ])
                    ->orderBy('created_at', 'desc')
                    ->limit(5) // Get the 5 most recent bookings
                    ->select([
                        'id',
                        'customer_id',
                        'service_id',
                        'staff_id',
                        'start_time',
                        'end_time',
                        'amount',
                        'status',
                        'created_at',
                        'updated_at'
                    ]);
            }
        ]);

        // Transform the data to ensure all required fields are included
        $customerData = $customer->toArray();

        // Ensure bookings have the required fields
        if (isset($customerData['bookings'])) {
            $customerData['bookings'] = array_map(function ($booking) {
                // If amount is not set or 0, try to get it from service price
                if (
                    (!isset($booking['amount']) || $booking['amount'] == 0) &&
                    isset($booking['service']['price'])
                ) {
                    $booking['amount'] = $booking['service']['price'];
                }
                return $booking;
            }, $customerData['bookings']);
        }

        return response()->json([
            'success' => true,
            'data' => $customerData
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'required_if:preferred_contact,email',
                \Illuminate\Validation\Rule::unique('customers')->where(function ($query) {
                    return $query->where('salon_id', auth()->user()->salon_id);
                })->ignore($customer->id)
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                \Illuminate\Validation\Rule::unique('customers')->where(function ($query) {
                    return $query->where('salon_id', auth()->user()->salon_id);
                })->ignore($customer->id)
            ],
            'preferred_contact' => 'required|in:email,phone,sms',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ], [
            'email.required_if' => 'The email field is required when email is selected as preferred contact method.',
            'email.unique' => 'This email address is already registered to another customer.',
            'phone.unique' => 'This phone number is already registered to another customer.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $customer->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update customer'
            ], 500);
        }
    }

    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete customer'
            ], 500);
        }
    }

    public function export()
    {
        $customers = Customer::select([
            'name',
            'email',
            'phone',
            'address',
            'preferred_contact',
            'status',
            'created_at',
            'last_visit_at'
        ])->get();

        $headers = [
            'Name',
            'Email',
            'Phone',
            'Address',
            'Preferred Contact',
            'Status',
            'Member Since',
            'Last Visit'
        ];

        $data = $customers->map(function ($customer) {
            return [
                $customer->name,
                $customer->email,
                $customer->phone,
                $customer->address,
                ucfirst($customer->preferred_contact),
                ucfirst($customer->status),
                $customer->created_at->format('M d, Y'),
                $customer->last_visit_at ? $customer->last_visit_at->format('M d, Y') : 'Never'
            ];
        });

        $filename = 'customers_' . Carbon::now()->format('Y-m-d_His') . '.csv';

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);

        foreach ($data as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}