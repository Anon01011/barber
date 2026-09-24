<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);

        // Simplified role check middleware
        $this->middleware(function ($request, $next) {
            $user = auth()->user();

            try {
                if (!$user->hasRole('employee')) {
                    return redirect()->route('dashboard')
                        ->with('error', 'You do not have permission to access this area.');
                }

                return $next($request);

            } catch (\Exception $e) {
                \Log::error('Role check failed', [
                    'user_id' => $user->id ?? null,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return redirect()->route('dashboard')
                    ->with('error', 'An error occurred while checking your permissions.');
            }
        });
    }

    /**
     * Display the employee dashboard
     */
    public function index()
    {
        $user = auth()->user();
        $today = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        // Get today's appointments (assigned to me OR unassigned pending)
        $todayAppointments = Booking::where(function ($query) use ($user) {
            $query->where('staff_id', $user->id)
                ->orWhere(function ($q) {
                    $q->whereNull('staff_id')
                        ->whereIn('staff_assignment_status', ['pending', 'rejected']);
                });
        })
            ->where('salon_id', $user->salon_id) // SECURITY: Explicit salon scoping
            ->whereBetween('start_time', [$today, $todayEnd])
            ->where(function ($q) use ($user) {
                $q->whereNull('rejected_by_staff_id')
                    ->orWhere('rejected_by_staff_id', '!=', $user->id);
            })
            ->with(['customer', 'service', 'package'])
            ->orderBy('start_time')
            ->get();

        // Get upcoming appointments (assigned to me AND pending, OR unassigned AND pending)
        $upcomingAppointments = Booking::where(function ($query) use ($user) {
            $query->where(function ($q1) use ($user) {
                $q1->where('staff_id', $user->id)
                    ->where('status', 'pending');
            })->orWhere(function ($q2) {
                $q2->whereNull('staff_id')
                    ->where('staff_assignment_status', 'pending')
                    ->where('status', 'pending');
            });
        })
            ->where('salon_id', $user->salon_id) // SECURITY: Explicit salon scoping
            ->where('start_time', '>', $todayEnd)
            ->where(function ($q) use ($user) {
                $q->whereNull('rejected_by_staff_id')
                    ->orWhere('rejected_by_staff_id', '!=', $user->id);
            })
            ->with(['customer', 'service', 'package'])
            ->orderBy('start_time')
            ->take(5)
            ->get();

        // Get new bookings
        $newBookings = Booking::where('created_at', '>', $user->last_login_at ?? now()->subDay())
            ->where('staff_id', $user->id)
            ->where('salon_id', $user->salon_id) // SECURITY: Explicit salon scoping
            ->where('status', 'pending')
            ->with(['customer', 'service', 'package'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('staff.dashboard', compact('todayAppointments', 'upcomingAppointments', 'newBookings'));
    }

    /**
     * Display all appointments for the employee
     */
    /**
     * Display all appointments for the employee with filters
     */
    public function appointments(Request $request)
    {
        $user = auth()->user();
        $status = $request->query('status');

        // Build query to show assigned appointments OR unassigned pending appointments
        $query = Booking::where(function ($q) use ($user) {
            $q->where('staff_id', $user->id)
                ->orWhere(function ($subQ) {
                    $subQ->whereNull('staff_id')
                        ->whereIn('staff_assignment_status', ['pending', 'rejected']);
                });
        })
            ->where('salon_id', $user->salon_id) // SECURITY: Explicit salon scoping
            ->where(function ($q) use ($user) {
                $q->whereNull('rejected_by_staff_id')
                    ->orWhere('rejected_by_staff_id', '!=', $user->id);
            })
            ->with(['customer', 'service', 'package']);

        // Apply status filter if provided
        if ($status && in_array($status, ['pending', 'confirmed', 'completed', 'cancelled', 'rejected'])) {
            $query->where('status', $status);
        }

        $appointments = $query->orderBy('start_time', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return view('staff.appointments.index', compact('appointments', 'status'));
    }

    /**
     * Accept a booking
     */
    public function acceptBooking(Booking $booking)
    {
        $user = auth()->user();

        // SECURITY: Verify booking belongs to employee's salon
        if ($booking->salon_id !== $user->salon_id) {
            Log::warning('Employee attempted to accept booking from different salon via ScheduleController', [
                'user_id' => $user->id,
                'user_salon_id' => $user->salon_id,
                'booking_id' => $booking->id,
                'booking_salon_id' => $booking->salon_id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this booking.'
            ], 403);
        }

        $this->authorize('update', $booking);

        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending bookings can be accepted.'
            ], 400);
        }

        $booking->update([
            'status' => 'confirmed',
            'confirmed_at' => now()
        ]);

        // Send notification to customer
        try {
            app(\App\Services\NotificationService::class)->sendBookingConfirmation($booking);
        } catch (\Exception $e) {
            Log::error('Failed to send booking acceptance notification: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking accepted successfully.',
            'status' => $booking->status,
            'status_label' => ucfirst($booking->status)
        ]);
    }

    /**
     * Reject a booking
     */
    public function rejectBooking(Booking $booking)
    {
        $user = auth()->user();

        // SECURITY: Verify booking belongs to employee's salon
        if ($booking->salon_id !== $user->salon_id) {
            Log::warning('Employee attempted to reject booking from different salon via ScheduleController', [
                'user_id' => $user->id,
                'user_salon_id' => $user->salon_id,
                'booking_id' => $booking->id,
                'booking_salon_id' => $booking->salon_id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this booking.'
            ], 403);
        }

        $this->authorize('update', $booking);

        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending bookings can be rejected.'
            ], 400);
        }

        $booking->update([
            'status' => 'rejected',
            'rejected_at' => now()
        ]);

        // Send notification to customer
        try {
            app(\App\Services\NotificationService::class)->sendBookingRejected($booking);
        } catch (\Exception $e) {
            Log::error('Failed to send booking rejection notification: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking rejected successfully.',
            'status' => $booking->status,
            'status_label' => ucfirst($booking->status)
        ]);
    }

    /**
     * Display today's appointments
     */
    public function today()
    {
        $user = auth()->user();
        $today = now()->startOfDay();

        $appointments = Booking::where('staff_id', $user->id)
            ->where('salon_id', $user->salon_id) // SECURITY: Explicit salon scoping
            ->whereDate('start_time', $today)
            ->with(['customer', 'service', 'package'])
            ->orderBy('start_time')
            ->get();

        return view('staff.appointments.today', compact('appointments'));
    }

    /**
     * Display upcoming appointments
     */
    public function upcoming()
    {
        $user = auth()->user();
        $today = now()->startOfDay();

        $appointments = Booking::where('staff_id', $user->id)
            ->where('salon_id', $user->salon_id) // SECURITY: Explicit salon scoping
            ->where('start_time', '>', $today)
            ->where('status', '!=', 'completed')
            ->with(['customer', 'service', 'package'])
            ->orderBy('start_time')
            ->paginate(10);

        return view('staff.appointments.upcoming', compact('appointments'));
    }

    /**
     * Display completed appointments
     */
    public function completed()
    {
        $user = auth()->user();

        $appointments = Booking::where('staff_id', $user->id)
            ->where('salon_id', $user->salon_id) // SECURITY: Explicit salon scoping
            ->where('status', 'completed')
            ->with(['customer', 'service', 'package'])
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        return view('staff.appointments.completed', compact('appointments'));
    }

    /**
     * Display a specific appointment
     */
    public function show($id)
    {
        $booking = Booking::with(['customer', 'service', 'package'])->findOrFail($id);
        $this->authorize('view', $booking);

        return view('staff.appointments.show', compact('booking'));
    }

    /**
     * Display staff earnings and commissions
     */
    public function earnings(Request $request)
    {
        $user = auth()->user();

        // Get date range from request or default to current month
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now()->endOfMonth();

        // Get commission service
        $commissionService = app(\App\Services\CommissionService::class);

        // Get commission summary for selected period
        $summary = $commissionService->getStaffCommissionSummary(
            $user->id,
            $startDate,
            $endDate
        );

        // Get detailed commissions with pagination
        $commissionsQuery = \App\Models\StaffCommission::forStaff($user->id)
            ->where('salon_id', $user->salon_id)
            ->with(['booking.service', 'booking.customer', 'booking.package', 'profile'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Apply status filter if provided
        if ($request->has('status') && $request->status !== 'all') {
            $commissionsQuery->where('status', $request->status);
        }

        $commissions = $commissionsQuery->orderBy('created_at', 'desc')->paginate(15);

        // Get monthly earnings data for chart (last 6 months)
        $monthlyEarnings = \App\Models\StaffCommission::forStaff($user->id)
            ->where('salon_id', $user->salon_id)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(commission_amount) as total, status')
            ->groupBy('month', 'status')
            ->orderBy('month')
            ->get()
            ->groupBy('month');

        // Get commission breakdown by type
        $breakdown = \App\Models\StaffCommission::forStaff($user->id)
            ->where('salon_id', $user->salon_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('item_type, SUM(commission_amount) as total, COUNT(*) as count')
            ->groupBy('item_type')
            ->get();

        return view('staff.earnings', compact(
            'summary',
            'commissions',
            'monthlyEarnings',
            'breakdown',
            'startDate',
            'endDate'
        ));
    }
}
