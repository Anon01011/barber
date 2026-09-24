<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\SystemNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PosSale;
use App\Models\PosRefund;
use App\Models\Employee;
use App\Models\StaffCommission;
use App\Models\Branch;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            return redirect()->route('admin.super.dashboard');
        }

        if ($user->hasRole('employee')) {
            return $this->employeeDashboard($user);
        }

        if ($user->hasRole('customer')) {
            return $this->customerDashboard($user);
        }

        // Default to Admin/Manager Dashboard
        return $this->adminDashboard($user, $request);
    }

    /**
     * Employee Dashboard View
     */
    private function employeeDashboard($user)
    {
        $today = Carbon::today();
        $todayStart = $today->copy()->startOfDay();
        $todayEnd = $today->copy()->endOfDay();
        $tomorrowStart = $today->copy()->addDay()->startOfDay();
        $now = now();

        // Get employee-specific data
        $todayAppointmentsCount = Booking::where(function ($query) use ($user) {
            $query->where('staff_id', $user->id)
                ->orWhere(function ($q) {
                    $q->whereNull('staff_id')
                        ->where('staff_assignment_status', 'pending');
                });
        })
            ->where('salon_id', $user->salon_id)
            ->whereBetween('start_time', [$todayStart, $todayEnd])
            ->count();

        $completedAppointmentsCount = Booking::where('staff_id', $user->id)
            ->where('salon_id', $user->salon_id)
            ->whereBetween('start_time', [$todayStart, $todayEnd])
            ->where('status', 'completed')
            ->count();

        $pendingAppointmentsCount = Booking::where(function ($query) use ($user) {
            $query->where(function ($q) use ($user) {
                $q->where('staff_id', $user->id)
                    ->where('status', 'pending');
            })->orWhere(function ($q) {
                $q->whereNull('staff_id')
                    ->where('staff_assignment_status', 'pending')
                    ->where('status', 'pending');
            });
        })
            ->where('salon_id', $user->salon_id)
            ->where(function ($query) use ($todayStart, $todayEnd, $now) {
                $query->whereBetween('start_time', [$todayStart, $todayEnd])
                    ->where('start_time', '>=', $now);
            })
            ->count();

        $bookingTipsQuery = Booking::where('staff_id', $user->id)
            ->where('salon_id', $user->salon_id)
            ->where('status', 'completed')
            ->where('tip_amount', '>', 0);

        $bookingTips = $bookingTipsQuery->sum('tip_amount');
        $bookingTipsCount = $bookingTipsQuery->count();

        $posTipsQuery = StaffCommission::where('staff_id', $user->id)
            ->where('salon_id', $user->salon_id)
            ->where('item_type', 'tip');

        $posTips = $posTipsQuery->sum('commission_amount');
        $posTipsCount = $posTipsQuery->count();

        $totalTips = $bookingTips + $posTips;
        $totalTipsCount = $bookingTipsCount + $posTipsCount;

        $todayAppointments = Booking::where(function ($query) use ($user) {
            $query->where('staff_id', $user->id)
                ->orWhere(function ($q) {
                    $q->whereNull('staff_id')
                        ->whereIn('staff_assignment_status', ['pending', 'rejected']);
                });
        })
            ->where('salon_id', $user->salon_id)
            ->whereBetween('start_time', [$todayStart, $todayEnd])
            ->where(function ($q) use ($user) {
                $q->whereNull('rejected_by_staff_id')
                    ->orWhere('rejected_by_staff_id', '!=', $user->id);
            })
            ->with(['customer', 'service', 'package'])
            ->orderBy('start_time', 'asc')
            ->get();

        $upcomingAppointments = Booking::where(function ($query) use ($user) {
            $query->where(function ($q1) use ($user) {
                $q1->where('staff_id', $user->id)
                    ->whereIn('status', ['confirmed', 'arrived']);
            })->orWhere(function ($q2) {
                $q2->whereNull('staff_id')
                    ->where('staff_assignment_status', 'pending')
                    ->whereIn('status', ['confirmed', 'arrived']);
            });
        })
            ->where('salon_id', $user->salon_id)
            ->where('start_time', '>=', $tomorrowStart)
            ->where(function ($q) use ($user) {
                $q->whereNull('rejected_by_staff_id')
                    ->orWhere('rejected_by_staff_id', '!=', $user->id);
            })
            ->select('bookings.*')
            ->selectRaw('COALESCE((SELECT COUNT(*) FROM bookings b2 WHERE b2.booking_group_id = bookings.booking_group_id AND bookings.booking_group_id IS NOT NULL AND b2.salon_id = bookings.salon_id), 1) as group_count')
            ->selectRaw('COALESCE((SELECT SUM(amount) FROM bookings b3 WHERE b3.booking_group_id = bookings.booking_group_id AND bookings.booking_group_id IS NOT NULL AND b3.salon_id = bookings.salon_id), amount) as group_total')
            ->joinSub(
                DB::table('bookings')
                    ->select(DB::raw('MIN(id) as min_id'))
                    ->where('salon_id', $user->salon_id)
                    ->groupBy(DB::raw('COALESCE(booking_group_id, CAST(id AS CHAR))')),
                'grouped',
                'bookings.id',
                '=',
                'grouped.min_id'
            )
            ->with(['customer', 'service', 'package'])
            ->orderBy('start_time', 'asc')
            ->take(10)
            ->get();

        $services = Service::where('salon_id', $user->salon_id)
            ->where('status', 'active')
            ->get();

        return view('dashboard', compact(
            'todayAppointmentsCount',
            'completedAppointmentsCount',
            'pendingAppointmentsCount',
            'totalTips',
            'totalTipsCount',
            'todayAppointments',
            'upcomingAppointments',
            'services'
        ) + [
            'today_tasks' => 0,
            'completed_tasks' => 0,
            'pending_tasks' => 0,
            // Fix for shared view sections expecting these variables
            'today_appointments' => $todayAppointmentsCount,
            'total_services' => $services->count(),
            'total_customers' => 0, // Employees might not need this, but view expects it
            'subscription' => [ // View might check subscription limits
                'name' => $user->salon->plan ? $user->salon->plan->name : 'No Active Plan',
                'limits' => []
            ]
        ]);
    }

    /**
     * Customer Dashboard View
     */
    private function customerDashboard($user)
    {
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            return view('dashboard', [
                'upcoming_appointments' => collect([]),
                'services' => Service::where('status', 'active')->get(),
                'staff_members' => User::role('employee')->where('status', 'active')->get(),
                'recent_activities' => collect([]),
                'total_appointments' => 0,
                'completed_appointments' => 0,
                'upcoming_appointments_count' => 0,
                'total_spent' => 0,
                'salon_slug' => $user->salon->slug ?? request()->route('salon_slug'),// Pass slug for route generation
            ]);
        }

        $upcoming_appointments = Booking::where('customer_id', $customer->id)
            ->where('start_time', '>', now())
            ->whereIn('status', ['confirmed', 'arrived'])
            ->orderBy('start_time')
            ->paginate(10);

        $services = Service::where('status', 'active')->get();
        $staff_members = User::role('employee')->where('status', 'active')->get();

        $recent_activities = Booking::where('customer_id', $customer->id)
            ->with(['service', 'staff', 'package'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($booking) {
                return (object) [
                    'type' => 'appointment',
                    'description' => "Booked {$booking->service->name}" . ($booking->staff ? " with {$booking->staff->name}" : ""),
                    'created_at' => $booking->created_at
                ];
            });

        $total_appointments = Booking::where('customer_id', $customer->id)->count();
        $completed_appointments = Booking::where('customer_id', $customer->id)->where('status', 'completed')->count();
        $upcoming_appointments_count = Booking::where('customer_id', $customer->id)
            ->where('start_time', '>', now())
            ->whereIn('status', ['confirmed', 'arrived'])
            ->count();
        $total_spent = Booking::where('customer_id', $customer->id)->where('status', 'completed')->sum('amount');

        return view('dashboard', compact(
            'upcoming_appointments',
            'services',
            'staff_members',
            'recent_activities',
            'total_appointments',
            'completed_appointments',
            'upcoming_appointments_count',
            'total_spent'
        ));
    }

    /**
     * Admin/Manager Dashboard View
     */
    private function adminDashboard($user, Request $request)
    {
        $dateRange = $request->get('date_range', 'today');

        // Only use custom dates if date_range is explicitly 'custom' and dates are provided
        if ($dateRange === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->get('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->get('end_date'))->endOfDay();
        } else {
            switch ($dateRange) {
                case 'yesterday':
                    $startDate = Carbon::yesterday()->startOfDay();
                    $endDate = Carbon::yesterday()->endOfDay();
                    break;
                case 'last_7_days':
                    $startDate = Carbon::today()->subDays(6)->startOfDay();
                    $endDate = Carbon::today()->endOfDay();
                    break;
                case 'this_month':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;
                case 'today':
                default:
                    $startDate = Carbon::today()->startOfDay();
                    $endDate = Carbon::today()->endOfDay();
                    // Ensure date_range is reset to 'today' if it was invalid or empty
                    $dateRange = 'today';
                    break;
            }
        }

        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateRange' => $dateRange,
            'totalBookings' => Booking::where('salon_id', $user->salon_id)->whereBetween('created_at', [$startDate, $endDate])->count(),
            'todayBookings' => Booking::where('salon_id', $user->salon_id)->whereBetween('created_at', [$startDate, $endDate])->count(),
            'completedBookings' => Booking::where('salon_id', $user->salon_id)->where('status', 'completed')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'pendingBookings' => Booking::where('salon_id', $user->salon_id)->where('status', 'pending')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'recentBookings' => Booking::where('salon_id', $user->salon_id)
                ->with(['customer', 'service', 'staff'])
                ->latest()
                ->take(10)
                ->get(),
            'services' => Service::where('salon_id', $user->salon_id)->where('status', 'active')->get(),
            'today_tasks' => 0,
            'completed_tasks' => 0,
            'pending_tasks' => 0,
            'today_appointments' => 0,
            'total_services' => 0,
            'total_customers' => 0,
            'salon_slug' => $user->salon->slug ?? request()->route('salon_slug'), // For route generation
            'subscription' => [
                'name' => 'No Active Plan',
                'limits' => []
            ]
        ];

        if ($user->can('salon.view')) {
            $data = array_merge($data, $this->getSalonAdminData($user, $startDate, $endDate));
        }

        if ($user->can('bookings.view')) {
            $data = array_merge($data, $this->getManagerData($user, $startDate, $endDate));
        }

        if ($user->can('system.view_dashboard')) {
            $data = array_merge($data, $this->getAnalyticsData($user, $startDate, $endDate));
        }

        $data['system_notifications'] = SystemNotification::active()
            ->forSalon($user->salon_id)
            ->latest()
            ->take(5)
            ->get();

        // Add salon quick stats
        $data['salon_quick_stats'] = $this->getSalonQuickStats($user, $startDate, $endDate);

        return view('dashboard', $data);
    }

    private function getSuperAdminData()
    {
        return [
            'total_users' => User::count(),
            'active_employees' => User::role('employee')->where('status', 'active')->count(),
            'total_roles' => 4, // super_admin, salon_admin, manager, employee
            'recent_bookings' => Booking::with(['service', 'staff', 'customer'])
                ->latest()
                ->take(10)
                ->get()
        ];
    }

    private function getSalonAdminData($user, $startDate, $endDate)
    {
        return [
            'today_appointments' => Booking::whereBetween('start_time', [$startDate, $endDate])->count(),
            'total_services' => Service::where('status', 'active')->count(),
            'total_customers' => User::role('customer')->count(),
            'recent_bookings' => $this->getRecentSalesData($user->salon_id, $startDate, $endDate),
            'recent_bookings_all' => Booking::where('salon_id', $user->salon_id)
                ->select('bookings.*')
                ->selectRaw('COALESCE((SELECT COUNT(*) FROM bookings b2 WHERE b2.booking_group_id = bookings.booking_group_id AND bookings.booking_group_id IS NOT NULL AND b2.salon_id = bookings.salon_id), 1) as group_count')
                ->selectRaw('COALESCE((SELECT SUM(amount) FROM bookings b3 WHERE b3.booking_group_id = bookings.booking_group_id AND bookings.booking_group_id IS NOT NULL AND b3.salon_id = bookings.salon_id), amount) as group_total')
                ->joinSub(
                    DB::table('bookings')
                        ->select(DB::raw('MIN(id) as min_id'))
                        ->where('salon_id', $user->salon_id)
                        ->groupBy(DB::raw('COALESCE(booking_group_id, CAST(id AS CHAR))')),
                    'grouped',
                    'bookings.id',
                    '=',
                    'grouped.min_id'
                )
                ->with(['service', 'staff', 'customer'])
                ->latest('start_time')
                ->paginate(10, ['*'], 'bookings_page')
                ->through(function ($booking) use ($user) {
                    // Load all bookings in the group for tooltip
                    if ($booking->booking_group_id) {
                        $booking->groupBookings = Booking::where('salon_id', $user->salon_id)
                            ->where('booking_group_id', $booking->booking_group_id)
                            ->with(['service', 'staff'])
                            ->get();
                    } else {
                        $booking->groupBookings = collect([$booking]);
                    }
                    return $booking;
                }),
            'services' => Service::where('status', 'active')->get(),
            'subscription' => [
                'name' => $user->salon->plan ? $user->salon->plan->name : 'No Active Plan',
                'features' => $user->salon->plan ? (is_array($user->salon->plan->features) ? $user->salon->plan->features : json_decode($user->salon->plan->features, true) ?? []) : [],
                'limits' => [
                    'staff' => [
                        'current' => $user->salon->users()->role('employee')->count(),
                        'max' => $user->salon->plan ? $user->salon->plan->max_users : 0,
                        'is_unlimited' => $user->salon->plan ? ($user->salon->plan->max_users === null || $user->salon->plan->max_users === -1) : false
                    ],
                    'branches' => [
                        'current' => $user->salon->branches()->count(),
                        'max' => $user->salon->plan ? $user->salon->plan->max_branches : 0,
                        'is_unlimited' => $user->salon->plan ? ($user->salon->plan->max_branches === null || $user->salon->plan->max_branches === -1) : false
                    ],
                    'customers' => [
                        'current' => $user->salon->customers()->count(),
                        'max' => $user->salon->plan ? $user->salon->plan->getLimit('max_customers') : 0,
                        'is_unlimited' => $user->salon->plan ? $user->salon->plan->isUnlimited('max_customers') : false
                    ],
                    'services' => [
                        'current' => $user->salon->services()->count(),
                        'max' => $user->salon->plan ? $user->salon->plan->getLimit('max_services') : 0,
                        'is_unlimited' => $user->salon->plan ? $user->salon->plan->isUnlimited('max_services') : false
                    ],
                    'products' => [
                        'current' => $user->salon->products()->count(),
                        'max' => $user->salon->plan ? $user->salon->plan->getLimit('max_products') : 0,
                        'is_unlimited' => $user->salon->plan ? $user->salon->plan->isUnlimited('max_products') : false
                    ],
                    'bookings' => [
                        'current' => Booking::where('salon_id', $user->salon_id)
                            ->whereMonth('start_time', now()->month)
                            ->whereYear('start_time', now()->year)
                            ->count(),
                        'max' => $user->salon->plan ? $user->salon->plan->getLimit('max_bookings_per_month') : 0,
                        'is_unlimited' => $user->salon->plan ? $user->salon->plan->isUnlimited('max_bookings_per_month') : false
                    ],
                    'guest_bookings' => [
                        'current' => Booking::where('salon_id', $user->salon_id)
                            ->whereHas('customer', function ($query) {
                                $query->where('is_guest', true);
                            })
                            ->whereMonth('start_time', now()->month)
                            ->whereYear('start_time', now()->year)
                            ->count(),
                        'max' => $user->salon->plan ? $user->salon->plan->getLimit('max_guest_bookings_per_month') : 0,
                        'is_unlimited' => $user->salon->plan ? $user->salon->plan->isUnlimited('max_guest_bookings_per_month') : false
                    ],
                    'memberships' => [
                        'current' => $user->salon->memberships()->count(),
                        'max' => $user->salon->plan ? $user->salon->plan->getLimit('max_memberships') : 0,
                        'is_unlimited' => $user->salon->plan ? $user->salon->plan->isUnlimited('max_memberships') : false
                    ],
                    'packages' => [
                        'current' => $user->salon->packages()->count(),
                        'max' => $user->salon->plan ? $user->salon->plan->getLimit('max_packages') : 0,
                        'is_unlimited' => $user->salon->plan ? $user->salon->plan->isUnlimited('max_packages') : false
                    ]
                ]
            ]
        ];
    }

    private function getManagerData($user, $startDate, $endDate)
    {
        return [
            'today_tasks' => Booking::whereBetween('start_time', [$startDate, $endDate])->count(),
            'completed_tasks' => Booking::whereBetween('start_time', [$startDate, $endDate])
                ->where('status', 'completed')
                ->count(),
            'pending_tasks' => Booking::whereBetween('start_time', [$startDate, $endDate])
                ->where('status', 'pending')
                ->count(),
            'recent_bookings' => $this->getRecentSalesData($user->salon_id, $startDate, $endDate),
            'recent_bookings_all' => Booking::where('salon_id', $user->salon_id)
                ->select('bookings.*')
                ->selectRaw('COALESCE((SELECT COUNT(*) FROM bookings b2 WHERE b2.booking_group_id = bookings.booking_group_id AND bookings.booking_group_id IS NOT NULL AND b2.salon_id = bookings.salon_id), 1) as group_count')
                ->selectRaw('COALESCE((SELECT SUM(amount) FROM bookings b3 WHERE b3.booking_group_id = bookings.booking_group_id AND bookings.booking_group_id IS NOT NULL AND b3.salon_id = bookings.salon_id), amount) as group_total')
                ->joinSub(
                    DB::table('bookings')
                        ->select(DB::raw('MIN(id) as min_id'))
                        ->where('salon_id', $user->salon_id)
                        ->groupBy(DB::raw('COALESCE(booking_group_id, CAST(id AS CHAR))')),
                    'grouped',
                    'bookings.id',
                    '=',
                    'grouped.min_id'
                )
                ->with(['service', 'staff', 'customer', 'package'])
                ->latest('start_time')
                ->paginate(10, ['*'], 'bookings_page')
                ->through(function ($booking) use ($user) {
                    // Load all bookings in the group for tooltip
                    if ($booking->booking_group_id) {
                        $booking->groupBookings = Booking::where('salon_id', $user->salon_id)
                            ->where('booking_group_id', $booking->booking_group_id)
                            ->with(['service', 'staff', 'package'])
                            ->get();
                    } else {
                        $booking->groupBookings = collect([$booking]);
                    }
                    return $booking;
                }),
            'services' => Service::where('status', 'active')->get()
        ];
    }

    private function getEmployeeData($user)
    {
        $today = Carbon::today();

        return [
            'appointments' => Booking::where('staff_id', $user->id)
                ->where('salon_id', $user->salon_id) // SECURITY: Explicit salon scoping
                ->whereDate('start_time', $today)
                ->count(),
            'completed' => Booking::where('staff_id', $user->id)
                ->where('salon_id', $user->salon_id) // SECURITY: Explicit salon scoping
                ->whereDate('start_time', $today)
                ->where('status', 'completed')
                ->count(),
            'upcoming' => Booking::where('staff_id', $user->id)
                ->where('salon_id', $user->salon_id) // SECURITY: Explicit salon scoping
                ->whereDate('start_time', $today)
                ->where('status', 'pending')
                ->count(),
            'recent_bookings' => Booking::where('staff_id', $user->id)
                ->where('salon_id', $user->salon_id) // SECURITY: Explicit salon scoping
                ->with(['service', 'customer'])
                ->latest()
                ->take(10)
                ->get()
        ];
    }

    private function getCustomerData($user)
    {
        $customer = $user->customer;

        return [
            'upcoming_appointments' => $customer ? $customer->bookings()
                ->with(['service', 'staff'])
                ->upcoming()
                ->get() : collect(),
            'recent_bookings' => $customer ? $customer->bookings()
                ->with(['service', 'staff'])
                ->latest()
                ->take(10)
                ->get() : collect()
        ];
    }

    public function getAnalytics()
    {
        $user = Auth::user();
        $data = [];

        if ($user->hasRole('super_admin')) {
            // Get total users count
            $data['total_users'] = User::count();

            // Get active employees count (users with employee role)
            $data['active_employees'] = User::role('employee')->where('status', 'active')->count();

            // Get total roles count
            $data['total_roles'] = Role::count();

            // Get today's appointments
            $data['today_appointments'] = Booking::whereDate('start_time', Carbon::today())->count();

            // Get total services
            $data['total_services'] = Service::count();

            // Get upcoming bookings for the next 7 days
            $data['upcoming_bookings'] = Booking::with(['service', 'staff'])
                ->whereIn('status', ['confirmed', 'arrived'])
                ->whereBetween('start_time', [Carbon::now(), Carbon::now()->addDays(7)])
                ->orderBy('start_time')
                ->take(10)
                ->get()
                ->map(function ($booking) {
                    return [
                        'service' => $booking->service->name,
                        'time' => $booking->start_time->format('h:i A'),
                        'staff' => $booking->staff->name
                    ];
                });
        } elseif ($user->hasRole('salon_admin') || $user->hasRole('manager')) {
            // Get today's appointments for the salon
            $data['today_appointments'] = Booking::whereDate('start_time', Carbon::today())
                ->where('salon_id', $user->salon_id)
                ->count();

            // Get total services for the salon
            $data['total_services'] = Service::where('salon_id', $user->salon_id)->count();

            // Get upcoming bookings for the salon
            $data['upcoming_bookings'] = Booking::with(['service', 'staff'])
                ->whereIn('status', ['confirmed', 'arrived'])
                ->where('salon_id', $user->salon_id)
                ->whereBetween('start_time', [Carbon::now(), Carbon::now()->addDays(7)])
                ->orderBy('start_time')
                ->take(10)
                ->get()
                ->map(function ($booking) {
                    return [
                        'service' => $booking->service->name,
                        'time' => $booking->start_time->format('h:i A'),
                        'staff' => $booking->staff->name
                    ];
                });
        }

        return response()->json($data);
    }

    private function getAnalyticsData($user, $startDate, $endDate)
    {
        $data = [];
        $salonId = $user->salon_id;

        if ($salonId) {
            // --- 1. Financials (Bookings + POS) ---
            $bookingStats = Booking::where('salon_id', $salonId)
                ->where('status', 'completed')
                ->whereBetween('start_time', [$startDate, $endDate])
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('pos_sale_items')
                        ->join('pos_sales', 'pos_sale_items.sale_id', '=', 'pos_sales.id')
                        ->whereColumn('pos_sale_items.booking_id', 'bookings.id')
                        ->where('pos_sales.status', '!=', 'voided');
                })
                ->select(DB::raw('sum(amount) as revenue'), DB::raw('count(*) as count'), DB::raw('sum(tip_amount) as tips'))
                ->first();

            $posStats = PosSale::where('salon_id', $salonId)
                ->where('status', '!=', 'voided')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw('sum(total - tip) as revenue'), DB::raw('count(*) as count'))
                ->first();

            // Subtract refunds processed in this period (Revenue = Sales - NetRefunds)
            // Using DB::table() instead of model for backward compatibility
            try {
                $refundStats = DB::table('pos_refunds')
                    ->where('salon_id', $salonId)
                    ->whereBetween('processed_at', [$startDate, $endDate])
                    ->select(
                        DB::raw('COALESCE(SUM(amount), 0) as gross'),
                        DB::raw('COALESCE(SUM(fee_amount), 0) as fees'),
                        DB::raw('COALESCE(SUM(net_refund_amount), 0) as net')
                    )
                    ->first();

                $refundNetOutflow = $refundStats->net ?? 0;
            } catch (\Exception $e) {
                // Table or columns don't exist yet - migrations pending
                $refundStats = (object) ['gross' => 0, 'fees' => 0, 'net' => 0];
                $refundNetOutflow = 0;
            }

            if ($posStats) {
                $posStats->revenue -= $refundNetOutflow;
            }

            $data['total_bill_value'] = ($bookingStats->revenue ?? 0) + ($posStats->revenue ?? 0);
            $data['bill_count'] = ($bookingStats->count ?? 0) + ($posStats->count ?? 0);
            $data['average_bill_value'] = $data['bill_count'] > 0 ? $data['total_bill_value'] / $data['bill_count'] : 0;

            $data['refund_gross'] = $refundStats->gross ?? 0;
            $data['refund_fees'] = $refundStats->fees ?? 0;
            $data['refund_net'] = $refundStats->net ?? 0;

            $cancelledStats = Booking::where('salon_id', $salonId)
                ->where('status', 'cancelled')
                ->whereBetween('start_time', [$startDate, $endDate])
                ->select(DB::raw('count(*) as count'), DB::raw('sum(amount) as value'))
                ->first();

            $data['cancelled_bill_count'] = $cancelledStats->count ?? 0;
            $data['cancelled_bill_value'] = $cancelledStats->value ?? 0;

            $data['staff_tips_value'] = $bookingStats->tips ?? 0;

            $unpaidBookingValue = Booking::where('salon_id', $salonId)
                ->whereIn('status', ['completed', 'staff_completed'])
                ->whereIn('payment_status', ['pending', 'partial', 'unpaid', 'paylater'])
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('pos_sale_items')
                        ->join('pos_sales', 'pos_sale_items.sale_id', '=', 'pos_sales.id')
                        ->whereColumn('pos_sale_items.booking_id', 'bookings.id')
                        ->where('pos_sales.status', '!=', 'voided');
                })
                ->whereBetween('start_time', [$startDate, $endDate])
                ->sum('amount');
            $unpaidPosValue = PosSale::where('salon_id', $salonId)->where('status', '!=', 'voided')->whereBetween('created_at', [$startDate, $endDate])->sum('outstanding_amount');
            $data['unpaid_value'] = $unpaidBookingValue + $unpaidPosValue;

            $data['total_expense_value'] = Payment::where('salon_id', $salonId)->where('type', 'expense')->whereBetween('created_at', [$startDate, $endDate])->sum('amount');

            // --- 2. Payment Methods (Combined) ---
            $bookingPayments = Payment::where('salon_id', $salonId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select('payment_method as name', DB::raw('count(*) as count'), DB::raw('sum(amount) as value'))
                ->groupBy('payment_method')
                ->get();

            $posPayments = PosSale::where('salon_id', $salonId)
                ->where('status', '!=', 'voided')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(
                    DB::raw('"Cash" as name'),
                    DB::raw('COUNT(CASE WHEN cash_amount > 0 THEN 1 END) as count'),
                    DB::raw('SUM(cash_amount) as value')
                )->unionAll(function ($query) use ($salonId, $startDate, $endDate) {
                    $query->from('pos_sales')
                        ->where('salon_id', $salonId)
                        ->where('status', '!=', 'voided')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->select(DB::raw('"Card" as name'), DB::raw('COUNT(CASE WHEN card_amount > 0 THEN 1 END) as count'), DB::raw('SUM(card_amount) as value'));
                })->unionAll(function ($query) use ($salonId, $startDate, $endDate) {
                    $query->from('pos_sales')
                        ->where('salon_id', $salonId)
                        ->where('status', '!=', 'voided')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->select(DB::raw('"Online" as name'), DB::raw('COUNT(CASE WHEN online_amount > 0 THEN 1 END) as count'), DB::raw('SUM(online_amount) as value'));
                })->unionAll(function ($query) use ($salonId, $startDate, $endDate) {
                    $query->from('pos_sales')
                        ->where('salon_id', $salonId)
                        ->where('status', '!=', 'voided')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->select(DB::raw('"Other" as name'), DB::raw('COUNT(CASE WHEN other_amount > 0 THEN 1 END) as count'), DB::raw('SUM(other_amount) as value'));
                })->unionAll(function ($query) use ($salonId, $startDate, $endDate) {
                    $query->from('pos_sales')
                        ->where('salon_id', $salonId)
                        ->where('status', '!=', 'voided')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->where('payment_method', 'mixed')
                        ->select(DB::raw('"Mixed" as name'), DB::raw('COUNT(*) as count'), DB::raw('SUM(cash_amount + card_amount + online_amount + other_amount) as value'));
                })->unionAll(function ($query) use ($salonId, $startDate, $endDate) {
                    $query->from('pos_sales')
                        ->where('salon_id', $salonId)
                        ->where('status', '!=', 'voided')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->where('payment_method', 'none')
                        ->select(DB::raw('"Unpaid" as name'), DB::raw('COUNT(*) as count'), DB::raw('SUM(outstanding_amount) as value'));
                })
                ->get();

            $mergedPayments = collect();
            foreach ($bookingPayments as $bp) {
                $mergedPayments->push([
                    'name' => $bp->name ?: 'Unspecified',
                    'value' => (float) ($bp->value ?? 0),
                    'count' => (int) ($bp->count ?? 0)
                ]);
            }
            foreach ($posPayments as $pp) {
                $name = $pp->name ?: 'Unspecified';
                $existing = $mergedPayments->firstWhere('name', $name);
                if ($existing) {
                    $mergedPayments = $mergedPayments->map(function ($item) use ($name, $pp) {
                        if ($item['name'] === $name) {
                            $item['value'] += (float) ($pp->value ?? 0);
                            $item['count'] += (int) ($pp->count ?? 0);
                        }
                        return $item;
                    });
                } else {
                    $mergedPayments->push([
                        'name' => $name,
                        'value' => (float) ($pp->value ?? 0),
                        'count' => (int) ($pp->count ?? 0)
                    ]);
                }
            }

            // Filter out payment methods with zero values and sort by value descending
            $data['payment_methods'] = $mergedPayments
                ->filter(function ($item) {
                    return $item['value'] > 0;
                })
                ->sortByDesc('value')
                ->values();

            // --- 3. Top Services (Bookings + POS Services) ---
            $bookingServices = Booking::where('bookings.salon_id', $salonId)
                ->where('bookings.status', 'completed')
                ->whereBetween('bookings.start_time', [$startDate, $endDate])
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('pos_sale_items')
                        ->join('pos_sales', 'pos_sale_items.sale_id', '=', 'pos_sales.id')
                        ->whereColumn('pos_sale_items.booking_id', 'bookings.id')
                        ->where('pos_sales.status', '!=', 'voided');
                })
                ->join('services', 'bookings.service_id', '=', 'services.id')
                ->select('services.name', DB::raw('count(*) as count'), DB::raw('sum(bookings.amount) as value'))
                ->groupBy('services.name')
                ->get();

            $posServices = DB::table('pos_sale_items')
                ->join('pos_sales', 'pos_sale_items.sale_id', '=', 'pos_sales.id')
                ->where('pos_sales.salon_id', $salonId)
                ->where('pos_sales.status', '!=', 'voided')
                ->whereBetween('pos_sales.created_at', [$startDate, $endDate])
                ->where('pos_sale_items.item_type', 'App\Models\Service')
                ->select('pos_sale_items.item_name as name', DB::raw('sum(pos_sale_items.quantity) as count'), DB::raw('sum(pos_sale_items.total) as value'))
                ->groupBy('pos_sale_items.item_name')
                ->get();

            $mergedServices = collect();
            foreach ($bookingServices as $bs) {
                $mergedServices->push(['name' => $bs->name, 'value' => $bs->value, 'count' => $bs->count]);
            }
            foreach ($posServices as $ps) {
                $existing = $mergedServices->firstWhere('name', $ps->name);
                if ($existing) {
                    $mergedServices = $mergedServices->map(function ($item) use ($ps) {
                        if ($item['name'] === $ps->name) {
                            $item['value'] += $ps->value;
                            $item['count'] += $ps->count;
                        }
                        return $item;
                    });
                } else {
                    $mergedServices->push(['name' => $ps->name, 'value' => $ps->value, 'count' => $ps->count]);
                }
            }

            $data['top_services'] = $mergedServices->sortByDesc('value')->take(10)->values()->map(function ($item) use ($data) {
                $item['percentage'] = $data['total_bill_value'] > 0 ? ($item['value'] / $data['total_bill_value']) * 100 : 0;
                return (object) $item;
            });

            // --- 4. Staff Performance ---
            $staffBookingStats = Booking::where('salon_id', $salonId)
                ->where('status', 'completed')
                ->whereBetween('start_time', [$startDate, $endDate])
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('pos_sale_items')
                        ->join('pos_sales', 'pos_sale_items.sale_id', '=', 'pos_sales.id')
                        ->whereColumn('pos_sale_items.booking_id', 'bookings.id')
                        ->where('pos_sales.status', '!=', 'voided');
                })
                ->select('staff_id', DB::raw('sum(amount) as revenue'), DB::raw('count(*) as count'))
                ->groupBy('staff_id')
                ->get()
                ->keyBy('staff_id');

            $staffPosStats = PosSale::where('salon_id', $salonId)
                ->where('status', '!=', 'voided')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select('employee_id', DB::raw('sum(cash_amount + card_amount + online_amount + other_amount) as revenue'))
                ->groupBy('employee_id')
                ->get()
                ->keyBy('employee_id');

            $data['staff_performance'] = Employee::where('salon_id', $salonId)
                ->with('user')
                ->get()
                ->map(function ($employee) use ($staffBookingStats, $staffPosStats) {
                    $staff = $employee->user;
                    if (!$staff)
                        return null;

                    $bStat = $staffBookingStats->get($staff->id);
                    $pStat = $staffPosStats->get($staff->id);
                    $staff->revenue = ($bStat->revenue ?? 0) + ($pStat->revenue ?? 0);
                    $staff->appointments_count = $bStat->count ?? 0;
                    return $staff;
                })->filter()->sortByDesc('revenue')->values();

            // --- 5. Staff Schedule ---
            $today = Carbon::today();
            $staffTodayAppts = Booking::where('salon_id', $salonId)
                ->whereDate('start_time', $today)
                ->select('staff_id', DB::raw('count(*) as count'))
                ->groupBy('staff_id')
                ->get()
                ->keyBy('staff_id');

            $data['staff_schedule'] = Employee::where('salon_id', $salonId)
                ->where('status', 'active')
                ->with('user')
                ->get()
                ->map(function ($employee) use ($staffTodayAppts) {
                    $staff = $employee->user;
                    return (object) [
                        'staff' => $staff,
                        'appointments_count' => $staff ? ($staffTodayAppts->get($staff->id)->count ?? 0) : 0,
                        'next_available' => '09:00 AM', // Placeholder
                        'status' => 'available'
                    ];
                });

            // --- 6. Customer Stats ---
            $data['active_customers'] = Customer::where('salon_id', $salonId)->where('status', 'active')->count();
            $data['new_customers_month'] = Customer::where('salon_id', $salonId)->whereBetween('created_at', [$startDate, $endDate])->count();

            $totalCustomers = Customer::where('salon_id', $salonId)->count();
            $returningCustomers = Customer::where('salon_id', $salonId)->has('bookings', '>', 1)->count();
            $data['returning_rate'] = $totalCustomers > 0 ? ($returningCustomers / $totalCustomers) * 100 : 0;

            $customerBookingSpent = Booking::where('salon_id', $salonId)
                ->where('status', 'completed')
                ->whereBetween('start_time', [$startDate, $endDate])
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('pos_sale_items')
                        ->join('pos_sales', 'pos_sale_items.sale_id', '=', 'pos_sales.id')
                        ->whereColumn('pos_sale_items.booking_id', 'bookings.id')
                        ->where('pos_sales.status', '!=', 'voided');
                })
                ->select('customer_id', DB::raw('sum(amount) as spent'))
                ->groupBy('customer_id')
                ->get()
                ->keyBy('customer_id');

            $customerPosSpent = PosSale::where('salon_id', $salonId)
                ->where('status', '!=', 'voided')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select('customer_id', DB::raw('sum(cash_amount + card_amount + online_amount + other_amount) as spent'))
                ->groupBy('customer_id')
                ->get()
                ->keyBy('customer_id');

            $data['top_customers'] = Customer::where('salon_id', $salonId)
                ->withCount('bookings as visits_count')
                ->orderByDesc('visits_count')
                ->take(5)
                ->get()
                ->map(function ($customer) use ($customerBookingSpent, $customerPosSpent) {
                    $bSpent = $customerBookingSpent->get($customer->id)->spent ?? 0;
                    $pSpent = $customerPosSpent->get($customer->id)->spent ?? 0;
                    $customer->total_spent = $bSpent + $pSpent;
                    return $customer;
                });

            // --- 7. Monthly Revenue Trend (Last 6 Months) ---
            $monthlyRevenue = collect();
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthStart = $month->copy()->startOfMonth();
                $monthEnd = $month->copy()->endOfMonth();
                $monthLabel = $month->format('M Y');

                $bRev = Booking::where('salon_id', $salonId)
                    ->where('status', 'completed')
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('pos_sale_items')
                            ->whereColumn('pos_sale_items.booking_id', 'bookings.id');
                    })
                    ->whereBetween('start_time', [$monthStart, $monthEnd])
                    ->sum('amount');

                $pRev = PosSale::where('salon_id', $salonId)
                    ->where('status', '!=', 'voided')
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum(DB::raw('total - tip'));

                // Subtract refunds from POS revenue in monthly trend
                try {
                    $monthRefundOutflow = DB::table('pos_refunds')
                        ->where('salon_id', $salonId)
                        ->whereBetween('processed_at', [$monthStart, $monthEnd])
                        ->sum('net_refund_amount');
                } catch (\Exception $e) {
                    $monthRefundOutflow = 0;
                }

                $pRev = $pRev - $monthRefundOutflow;

                $monthlyRevenue->push([
                    'month' => $monthLabel,
                    'revenue' => (float) ($bRev + $pRev)
                ]);
            }
            $data['monthly_revenue'] = $monthlyRevenue;

            // --- 8. Sales Comparison ---
            $data['todays_sales'] = $this->getPeriodSalesComparison($salonId, $startDate, $endDate);

            // --- 9. Staff Commissions ---
            $data['staff_commissions'] = $this->getStaffCommissions($salonId, $startDate, $endDate);

            // --- 10. Customer Feedback ---
            $data['customer_feedback'] = $this->getCustomerFeedback($salonId);

            // --- 11. Low Stock Products ---
            $data['low_stock_products'] = $this->getLowStockProducts($salonId);

            // --- 12. Upcoming Birthdays ---
            $data['upcoming_birthdays'] = $this->getUpcomingBirthdays($salonId);

            // --- 13. No-Shows ---
            $data['todays_no_shows'] = $this->getPeriodNoShows($salonId, $startDate, $endDate);

            // --- 14. Outstanding Payments ---
            $data['outstanding_payments'] = $this->getOutstandingPayments($salonId);
        }

        return $data;
    }

    /**
     * Get recent sales data (completed bookings + POS sales) with database-level pagination
     */
    private function getRecentSalesData($salonId, $startDate = null, $endDate = null)
    {
        $perPage = 10;
        $pageName = 'sales_page';

        // 1. Get paginated IDs and Types using Union at DB level
        $bookingsSub = DB::table('bookings')
            ->select(
                DB::raw('MIN(id) as id'),
                DB::raw('MAX(start_time) as date'),
                DB::raw("'booking' as type")
            )
            ->where('salon_id', $salonId)
            ->where('status', 'completed');

        if ($startDate && $endDate) {
            $bookingsSub->whereBetween('start_time', [$startDate, $endDate]);
        }

        $bookingsSub->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('pos_sale_items')
                ->whereColumn('pos_sale_items.booking_id', 'bookings.id');
        })
            ->groupBy(DB::raw('COALESCE(booking_group_id, CAST(id AS CHAR))'));

        $posSub = DB::table('pos_sales')
            ->select('id', 'created_at as date', DB::raw("'pos' as type"))
            ->where('salon_id', $salonId)
            ->where('status', '!=', 'voided');

        if ($startDate && $endDate) {
            $posSub->whereBetween('created_at', [$startDate, $endDate]);
        }

        $paginatedResults = $bookingsSub->union($posSub)
            ->orderByDesc('date')
            ->paginate($perPage, ['*'], $pageName);

        // 2. Hydrate models for the current page to avoid N+1
        $bookingIds = [];
        $posIds = [];

        foreach ($paginatedResults as $result) {
            if ($result->type === 'booking') {
                $bookingIds[] = $result->id;
            } else {
                $posIds[] = $result->id;
            }
        }

        $hydratedBookings = Booking::whereIn('id', $bookingIds)
            ->select('bookings.*')
            ->selectRaw('COALESCE((SELECT COUNT(*) FROM bookings b2 WHERE b2.booking_group_id = bookings.booking_group_id AND bookings.booking_group_id IS NOT NULL AND b2.salon_id = bookings.salon_id), 1) as group_count')
            ->selectRaw('COALESCE((SELECT SUM(amount) FROM bookings b3 WHERE b3.booking_group_id = bookings.booking_group_id AND bookings.booking_group_id IS NOT NULL AND b3.salon_id = bookings.salon_id), amount) as group_total')
            ->with(['service', 'staff', 'customer'])
            ->get()
            ->map(function ($booking) use ($salonId) {
                // Load all bookings in the group for tooltip
                if ($booking->booking_group_id) {
                    $booking->groupBookings = Booking::where('salon_id', $salonId)
                        ->where('booking_group_id', $booking->booking_group_id)
                        ->with(['service', 'staff'])
                        ->get();
                } else {
                    $booking->groupBookings = collect([$booking]);
                }
                return $booking;
            })
            ->keyBy('id');

        $hydratedPosSales = PosSale::whereIn('id', $posIds)
            ->with(['customer', 'employee', 'items'])
            ->get()
            ->keyBy('id');

        // 3. Map back to the paginator items to maintain view compatibility
        $items = $paginatedResults->getCollection()->map(function ($result) use ($hydratedBookings, $hydratedPosSales) {
            if ($result->type === 'booking') {
                $model = $hydratedBookings->get($result->id);
                return (object) [
                    'id' => $result->id,
                    'type' => 'booking',
                    'date' => $result->date,
                    'created_at' => $model ? $model->created_at : null,
                    'amount' => $model ? $model->group_total : 0,
                    'status' => $model ? $model->payment_status : 'pending',
                    'model' => $model
                ];
            } else {
                $model = $hydratedPosSales->get($result->id);

                $totalRefunded = 0;
                $refundFees = 0;

                if ($model) {
                    try {
                        $refunds = DB::table('pos_refunds')
                            ->where('sale_id', $model->id)
                            ->select('amount', 'fee_amount')
                            ->get();
                        $totalRefunded = $refunds->sum('amount');
                        $refundFees = $refunds->sum('fee_amount');
                    } catch (\Exception $e) {
                        // Table pos_refunds likely doesn't exist
                        $totalRefunded = 0;
                        $refundFees = 0;
                    }
                }
                $originalTotal = $model ? ($model->cash_amount + $model->card_amount + $model->online_amount + $model->other_amount + $model->outstanding_amount) : 0;

                return (object) [
                    'id' => $result->id,
                    'type' => 'pos',
                    'date' => $result->date,
                    'created_at' => $model ? $model->created_at : null,
                    'amount' => $model ? ($originalTotal - $totalRefunded + $refundFees) : 0,
                    'status' => $model ? $model->status : 'unpaid',
                    'payment_status' => $model ? $model->payment_status : 'unpaid',
                    'refunded_amount' => $totalRefunded,
                    'model' => $model
                ];
            }
        });

        $paginatedResults->setCollection($items);

        return $paginatedResults;
    }

    /**
     * Get today's sales comparison with yesterday
     */
    private function getPeriodSalesComparison($salonId, $startDate, $endDate)
    {
        $duration = $startDate->diffInDays($endDate) + 1;
        $prevStartDate = $startDate->copy()->subDays($duration);
        $prevEndDate = $endDate->copy()->subDays($duration);

        // Current period sales
        $currentBookings = Booking::where('salon_id', $salonId)
            ->where('status', 'completed')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('pos_sale_items')
                    ->whereColumn('pos_sale_items.booking_id', 'bookings.id');
            })
            ->whereBetween('start_time', [$startDate, $endDate])
            ->sum('amount');

        $currentPos = PosSale::where('salon_id', $salonId)
            ->where('status', '!=', 'voided')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum(DB::raw('total - tip'));

        // Current period refunds outflow (net amount actually returned)
        try {
            $currentRefundsOutflow = DB::table('pos_refunds')
                ->where('salon_id', $salonId)
                ->whereBetween('processed_at', [$startDate, $endDate])
                ->sum('net_refund_amount');
        } catch (\Exception $e) {
            $currentRefundsOutflow = 0;
        }

        $currentRevenue = ($currentBookings + $currentPos) - $currentRefundsOutflow;

        $currentCount = Booking::where('salon_id', $salonId)
            ->where('status', 'completed')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('pos_sale_items')
                    ->whereColumn('pos_sale_items.booking_id', 'bookings.id');
            })
            ->whereBetween('start_time', [$startDate, $endDate])
            ->count() + PosSale::where('salon_id', $salonId)
                ->where('status', '!=', 'voided')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

        // Current period refunds (Gross amount for card display)
        try {
            $currentRefundsStats = DB::table('pos_refunds')
                ->where('salon_id', $salonId)
                ->whereBetween('processed_at', [$startDate, $endDate])
                ->select(
                    DB::raw('SUM(amount) as gross_amount'),
                    DB::raw('SUM(fee_amount) as fee_amount'),
                    DB::raw('SUM(net_refund_amount) as net_amount'),
                    DB::raw('COUNT(*) as count')
                )
                ->first();
        } catch (\Exception $e) {
            $currentRefundsStats = (object) ['gross_amount' => 0, 'fee_amount' => 0, 'net_amount' => 0, 'count' => 0];
        }

        $currentRefunds = $currentRefundsStats->gross_amount ?? 0;
        $currentRefundsCount = $currentRefundsStats->count ?? 0;
        $currentRefundsFees = $currentRefundsStats->fee_amount ?? 0;

        // Previous period sales
        $previousBookings = Booking::where('salon_id', $salonId)
            ->where('status', 'completed')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('pos_sale_items')
                    ->whereColumn('pos_sale_items.booking_id', 'bookings.id');
            })
            ->whereBetween('start_time', [$prevStartDate, $prevEndDate])
            ->sum('amount');

        $previousPos = PosSale::where('salon_id', $salonId)
            ->where('status', '!=', 'voided')
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->sum(DB::raw('total - tip'));

        // Previous period refunds outflow
        try {
            $previousRefundsOutflow = DB::table('pos_refunds')
                ->where('salon_id', $salonId)
                ->whereBetween('processed_at', [$prevStartDate, $prevEndDate])
                ->sum('net_refund_amount');
        } catch (\Exception $e) {
            $previousRefundsOutflow = 0;
        }

        $previousRevenue = ($previousBookings + $previousPos) - $previousRefundsOutflow;

        $previousCount = Booking::where('salon_id', $salonId)
            ->where('status', 'completed')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('pos_sale_items')
                    ->whereColumn('pos_sale_items.booking_id', 'bookings.id');
            })
            ->whereBetween('start_time', [$prevStartDate, $prevEndDate])
            ->count() + PosSale::where('salon_id', $salonId)
                ->where('status', '!=', 'voided')
                ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
                ->count();

        // Previous period refunds (Gross amount for card display)
        try {
            $previousRefundsStats = DB::table('pos_refunds')
                ->where('salon_id', $salonId)
                ->whereBetween('processed_at', [$prevStartDate, $prevEndDate])
                ->select(
                    DB::raw('SUM(amount) as gross_amount'),
                    DB::raw('COUNT(*) as count')
                )
                ->first();
        } catch (\Exception $e) {
            $previousRefundsStats = (object) ['gross_amount' => 0, 'count' => 0];
        }

        $previousRefunds = $previousRefundsStats->gross_amount ?? 0;
        $previousRefundsCount = $previousRefundsStats->count ?? 0;

        // Calculate growth
        $revenueGrowth = $previousRevenue > 0 ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;
        $countGrowth = $previousCount > 0 ? (($currentCount - $previousCount) / $previousCount) * 100 : 0;

        // Peak hour (Bookings + POS)
        $bookingHours = Booking::where('salon_id', $salonId)
            ->where('status', 'completed')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('pos_sale_items')
                    ->whereColumn('pos_sale_items.booking_id', 'bookings.id');
            })
            ->whereBetween('start_time', [$startDate, $endDate])
            ->select(DB::raw('HOUR(start_time) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        $posHours = PosSale::where('salon_id', $salonId)
            ->where('status', '!=', 'voided')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        // Merge counts
        $hourlyCounts = [];
        foreach ($bookingHours as $hour => $count) {
            $hourlyCounts[$hour] = ($hourlyCounts[$hour] ?? 0) + $count;
        }
        foreach ($posHours as $hour => $count) {
            $hourlyCounts[$hour] = ($hourlyCounts[$hour] ?? 0) + $count;
        }

        // Find peak hour
        $peakHour = 'N/A';
        if (!empty($hourlyCounts)) {
            $maxCount = 0;
            $bestHour = null;
            foreach ($hourlyCounts as $hour => $count) {
                if ($count >= $maxCount) {
                    $maxCount = $count;
                    $bestHour = $hour;
                }
            }

            if ($bestHour !== null) {
                $peakHour = Carbon::createFromTime($bestHour, 0, 0)->format('g:00 A');
            }
        }

        return [
            'today_revenue' => $currentRevenue,
            'today_count' => $currentCount,
            'yesterday_revenue' => $previousRevenue,
            'yesterday_count' => $previousCount,
            'revenue_growth' => round($revenueGrowth, 1),
            'count_growth' => round($countGrowth, 1),
            'peak_hour' => $peakHour,
            'today_refunds' => $currentRefunds,
            'today_net_refunds' => $currentRefundsStats->net_amount ?? 0,
            'today_refunds_count' => $currentRefundsCount,
            'today_refunds_fees' => $currentRefundsFees,
            'yesterday_refunds' => $previousRefunds,
            'yesterday_refunds_count' => $previousRefundsCount
        ];
    }

    /**
     * Get staff commissions summary
     */
    private function getStaffCommissions($salonId, $startDate, $endDate)
    {
        // Get commissions from StaffCommission model
        $todayCommissions = \App\Models\StaffCommission::where('salon_id', $salonId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('commission_amount');

        // Since the UI expects "today", "week", "month", but we want to show for the selected period,
        // we'll map the selected period to the "today" slot and leave the others or adjust.
        // Actually, better to just return the period total.

        // Get tips from bookings
        $todayTips = Booking::where('salon_id', $salonId)
            ->where('status', 'completed')
            ->whereBetween('start_time', [$startDate, $endDate])
            ->sum('tip_amount');

        // Let's also get week/month for comparison if period is just today
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();

        $weekCommissions = \App\Models\StaffCommission::where('salon_id', $salonId)
            ->where('created_at', '>=', $weekStart)
            ->where('created_at', '<=', now())
            ->sum('commission_amount');

        $monthCommissions = \App\Models\StaffCommission::where('salon_id', $salonId)
            ->where('created_at', '>=', $monthStart)
            ->where('created_at', '<=', now())
            ->sum('commission_amount');

        $weekTips = Booking::where('salon_id', $salonId)
            ->where('status', 'completed')
            ->where('start_time', '>=', $weekStart)
            ->where('start_time', '<=', now())
            ->sum('tip_amount');

        $monthTips = Booking::where('salon_id', $salonId)
            ->where('status', 'completed')
            ->where('start_time', '>=', $monthStart)
            ->where('start_time', '<=', now())
            ->sum('tip_amount');

        // Get top earners (this month)
        $topEarners = \App\Models\StaffCommission::where('salon_id', $salonId)
            ->where('created_at', '>=', $monthStart)
            ->select('staff_id', DB::raw('SUM(commission_amount) as total_commission'))
            ->groupBy('staff_id')
            ->orderByDesc('total_commission')
            ->take(5)
            ->get()
            ->map(function ($item) {
                $staff = User::find($item->staff_id);
                return [
                    'name' => $staff ? $staff->name : 'Unknown',
                    'commission' => $item->total_commission
                ];
            });

        return [
            'today' => $todayCommissions,
            'week' => $weekCommissions,
            'month' => $monthCommissions,
            'today_tips' => $todayTips,
            'week_tips' => $weekTips,
            'month_tips' => $monthTips,
            'top_earners' => $topEarners
        ];
    }

    /**
     * Get customer feedback and ratings
     */
    private function getCustomerFeedback($salonId)
    {
        // Check if Review or Rating model exists
        $hasReviews = class_exists('\App\Models\Review');
        $hasRatings = class_exists('\App\Models\Rating');

        $avgRating = 0;
        $totalReviews = 0;
        $recentReviews = collect();

        if ($hasReviews) {
            $avgRating = \App\Models\Review::where('salon_id', $salonId)->avg('rating') ?? 0;
            $totalReviews = \App\Models\Review::where('salon_id', $salonId)->count();
            $recentReviews = \App\Models\Review::where('salon_id', $salonId)
                ->with('customer')
                ->latest()
                ->take(5)
                ->get();
        } elseif ($hasRatings) {
            $avgRating = \App\Models\Rating::where('salon_id', $salonId)->avg('rating') ?? 0;
            $totalReviews = \App\Models\Rating::where('salon_id', $salonId)->count();
            $recentReviews = \App\Models\Rating::where('salon_id', $salonId)
                ->with('customer')
                ->latest()
                ->take(5)
                ->get();
        }

        return [
            'average_rating' => round($avgRating, 1),
            'total_reviews' => $totalReviews,
            'recent_reviews' => $recentReviews
        ];
    }

    /**
     * Get salon quick stats for main dashboard
     */
    private function getSalonQuickStats($user, $startDate, $endDate)
    {
        $salonId = $user->salon_id;

        // Count active staff from User model with relevant roles
        $activeStaffCount = User::where('salon_id', $salonId)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['employee', 'manager', 'salon_admin']);
            })
            ->where('status', 'active')
            ->count();

        // Fallback for legacy data using boolean 1
        if ($activeStaffCount === 0) {
            $activeStaffCount = User::where('salon_id', $salonId)
                ->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['employee', 'manager', 'salon_admin']);
                })
                ->where('status', 1)
                ->count();
        }

        return [
            'active_staff' => $activeStaffCount,
            'today_appointments' => Booking::where('salon_id', $salonId)
                ->whereBetween('start_time', [$startDate, $endDate])
                ->count(),
            'pending_appointments' => Booking::where('salon_id', $salonId)
                ->where('status', 'pending')
                ->whereBetween('start_time', [$startDate, $endDate])
                ->count(),
            'completed_today' => Booking::where('salon_id', $salonId)
                ->whereIn('status', ['completed', 'staff_completed'])
                ->whereBetween('start_time', [$startDate, $endDate])
                ->count()
        ];
    }

    /**
     * Get low stock products
     */
    private function getLowStockProducts($salonId)
    {
        return \App\Models\Product::where('salon_id', $salonId)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->orderBy('stock_quantity', 'asc')
            ->take(5)
            ->get(['id', 'name', 'stock_quantity', 'low_stock_threshold']);
    }

    /**
     * Get customers with upcoming birthdays (next 7 days)
     */
    private function getUpcomingBirthdays($salonId)
    {
        $today = Carbon::today();
        $nextWeek = Carbon::today()->addDays(7);

        return \App\Models\Customer::where('salon_id', $salonId)
            ->where('status', 'active')
            ->whereNotNull('dob')
            ->get()
            ->filter(function ($customer) use ($today, $nextWeek) {
                if (!$customer->dob)
                    return false;

                // Get this year's birthday
                $birthday = Carbon::parse($customer->dob)->year($today->year);

                // If birthday already passed this year, check next year
                if ($birthday->lt($today)) {
                    $birthday->addYear();
                }

                return $birthday->between($today, $nextWeek);
            })
            ->map(function ($customer) use ($today) {
                $birthday = Carbon::parse($customer->dob)->year($today->year);
                if ($birthday->lt($today)) {
                    $birthday->addYear();
                }

                return (object) [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'birthday' => $birthday,
                    'days_until' => $today->diffInDays($birthday)
                ];
            })
            ->sortBy('days_until')
            ->take(10)
            ->values();
    }

    /**
     * Get today's no-shows
     */
    private function getPeriodNoShows($salonId, $startDate, $endDate)
    {
        return Booking::where('salon_id', $salonId)
            ->where('status', 'no_show')
            ->whereBetween('start_time', [$startDate, $endDate])
            ->with(['customer', 'service'])
            ->orderBy('start_time', 'desc')
            ->take(10)
            ->get();
    }

    /**
     * Get outstanding payments
     */
    private function getOutstandingPayments($salonId)
    {
        // Get unpaid bookings
        $unpaidBookings = Booking::where('salon_id', $salonId)
            ->whereIn('status', ['completed', 'staff_completed'])
            ->whereIn('payment_status', ['pending', 'partial', 'unpaid', 'paylater'])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('pos_sale_items')
                    ->whereColumn('pos_sale_items.booking_id', 'bookings.id');
            })
            ->select('bookings.*')
            ->selectRaw('COALESCE((SELECT COUNT(*) FROM bookings b2 WHERE b2.booking_group_id = bookings.booking_group_id AND bookings.booking_group_id IS NOT NULL AND b2.salon_id = bookings.salon_id), 1) as group_count')
            ->selectRaw('COALESCE((SELECT SUM(amount) FROM bookings b3 WHERE b3.booking_group_id = bookings.booking_group_id AND bookings.booking_group_id IS NOT NULL AND b3.salon_id = bookings.salon_id), amount) as group_total')
            ->joinSub(
                DB::table('bookings')
                    ->select(DB::raw('MIN(id) as min_id'))
                    ->where('salon_id', $salonId)
                    ->groupBy(DB::raw('COALESCE(booking_group_id, CAST(id AS CHAR))')),
                'grouped',
                'bookings.id',
                '=',
                'grouped.min_id'
            )
            ->with('customer')
            ->get()
            ->map(function ($booking) use ($salonId) {
                // Load all bookings in the group for tooltip
                if ($booking->booking_group_id) {
                    $booking->groupBookings = Booking::where('salon_id', $salonId)
                        ->where('booking_group_id', $booking->booking_group_id)
                        ->with(['service', 'staff'])
                        ->get();
                } else {
                    $booking->groupBookings = collect([$booking]);
                }

                return (object) [
                    'type' => 'booking',
                    'id' => $booking->id,
                    'customer_name' => $booking->customer->name ?? 'Unknown',
                    'amount' => $booking->group_total,
                    'group_count' => $booking->group_count,
                    'groupBookings' => $booking->groupBookings,
                    'date' => $booking->start_time,
                    'days_overdue' => (int) Carbon::parse($booking->start_time)->diffInDays(Carbon::now()),
                    'status' => 'Unpaid'
                ];
            });

        // Get unpaid POS sales
        $unpaidPos = PosSale::where('salon_id', $salonId)
            ->where('status', '!=', 'voided')
            ->where('outstanding_amount', '>', 0)
            ->with('customer')
            ->get()
            ->map(function ($sale) {
                return (object) [
                    'type' => 'pos',
                    'id' => $sale->id,
                    'customer_name' => $sale->customer->name ?? 'Walk-in',
                    'amount' => $sale->outstanding_amount,
                    'group_count' => 1,
                    'date' => $sale->created_at,
                    'days_overdue' => (int) $sale->created_at->diffInDays(Carbon::now()),
                    'status' => 'Partial'
                ];
            });

        $allOutstanding = $unpaidBookings->concat($unpaidPos)
            ->sortByDesc('days_overdue')
            ->take(10);

        $totalOutstanding = $unpaidBookings->sum('amount') + $unpaidPos->sum('amount');

        return [
            'items' => $allOutstanding->values(),
            'total' => $totalOutstanding
        ];
    }
}
