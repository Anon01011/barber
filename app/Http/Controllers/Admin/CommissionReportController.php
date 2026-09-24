<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StaffCommission;
use App\Models\User;
use App\Services\CommissionService;
use Carbon\Carbon;

class CommissionReportController extends Controller
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
        // Add middleware if needed, e.g., permission checks
        // $this->middleware('permission:reports.view');
    }

    /**
     * Commission Overview Dashboard
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now()->endOfDay();
        $salonId = auth()->user()->salon_id;
        $branch = app()->has('current_branch') ? app('current_branch') : null;

        // Overall Summary
        $totalCommissions = StaffCommission::where('salon_id', $salonId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('commission_amount');

        $pendingAmount = StaffCommission::where('salon_id', $salonId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'pending')
            ->sum('commission_amount');

        $paidAmount = StaffCommission::where('salon_id', $salonId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('commission_amount');

        // Top Staff by Commission
        $topStaff = StaffCommission::where('salon_id', $salonId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('staff_id, SUM(commission_amount) as total_commission')
            ->groupBy('staff_id')
            ->with('staff')
            ->orderByDesc('total_commission')
            ->take(5)
            ->get()
            ->map(function ($item) {
                $item->staff_name = $item->staff ? $item->staff->name : 'Unknown';
                return $item;
            });

        // Recent Commissions
        $recentCommissions = StaffCommission::where('salon_id', $salonId)
            ->with(['staff', 'booking.service'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.reports.commissions.index', compact(
            'startDate',
            'endDate',
            'totalCommissions',
            'pendingAmount',
            'paidAmount',
            'topStaff',
            'topStaff',
            'recentCommissions',
            'branch'
        ));
    }

    /**
     * Staff-wise Commission Report
     */
    public function byStaff(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now()->endOfDay();
        $salonId = auth()->user()->salon_id;
        $staffId = $request->input('staff_id');
        $branch = app()->has('current_branch') ? app('current_branch') : null;

        $staffMembers = User::where('salon_id', $salonId)
            ->role('employee')
            ->get();

        $commissions = StaffCommission::where('salon_id', $salonId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($staffId, function ($query) use ($staffId) {
                return $query->where('staff_id', $staffId);
            })
            ->with(['staff', 'booking.service', 'booking.customer'])
            ->orderByDesc('created_at')
            ->paginate(20);

        // Calculate totals for the filtered view
        $totalAmount = $commissions->sum('commission_amount'); // Note: this sums only current page if using simple paginate, but for report usually we want total of query. 
        // For accurate totals with pagination, we should run a separate aggregate query.
        $summaryTotal = StaffCommission::where('salon_id', $salonId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($staffId, function ($query) use ($staffId) {
                return $query->where('staff_id', $staffId);
            })
            ->sum('commission_amount');

        return view('admin.reports.commissions.by-staff', compact(
            'startDate',
            'endDate',
            'staffMembers',
            'commissions',
            'staffId',
            'staffId',
            'summaryTotal',
            'branch'
        ));
    }

    /**
     * Pending Approvals
     */
    /**
     * Pending Approvals
     */
    public function pending(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $staffId = $request->input('staff_id');

        $query = StaffCommission::where('salon_id', $salonId)
            ->where('status', 'pending')
            ->with(['staff', 'booking.service']);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        if ($staffId) {
            $query->where('staff_id', $staffId);
        }

        $pendingCommissions = $query->orderBy('created_at')
            ->paginate(20)
            ->withQueryString();

        $staffMembers = User::where('salon_id', $salonId)->role('employee')->get();

        $branch = app()->has('current_branch') ? app('current_branch') : null;

        return view('admin.reports.commissions.pending', compact('pendingCommissions', 'staffMembers', 'startDate', 'endDate', 'staffId', 'branch'));
    }

    /**
     * Payouts (Approved Commissions)
     */
    public function payouts(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $staffId = $request->input('staff_id');

        $query = StaffCommission::where('salon_id', $salonId)
            ->where('status', 'approved')
            ->with(['staff', 'booking.service']);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        if ($staffId) {
            $query->where('staff_id', $staffId);
        }

        // Calculate totals
        $totalTransactions = $query->count();
        $totalAmount = $query->sum('commission_amount');

        $approvedCommissions = $query->orderBy('created_at')
            ->paginate(20)
            ->withQueryString();

        $staffMembers = User::where('salon_id', $salonId)->role('employee')->get();

        $branch = app()->has('current_branch') ? app('current_branch') : null;

        return view('admin.reports.commissions.payouts', compact('approvedCommissions', 'staffMembers', 'startDate', 'endDate', 'staffId', 'branch', 'totalTransactions', 'totalAmount'));
    }

    /**
     * Revert Commission Status
     */
    public function revert(Request $request, StaffCommission $commission)
    {
        if ($commission->salon_id !== auth()->user()->salon_id) {
            abort(403);
        }

        if ($commission->status === 'approved') {
            $commission->update(['status' => 'pending']);
            return back()->with('success', 'Commission reverted to pending status.');
        }

        return back()->with('error', 'Only approved commissions can be reverted.');
    }

    /**
     * Bulk Approve
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'commission_ids' => 'required|array',
            'commission_ids.*' => [
                'exists:staff_commissions,id',
                function ($attribute, $value, $fail) {
                    $exists = StaffCommission::where('id', $value)
                        ->where('salon_id', auth()->user()->salon_id)
                        ->exists();
                    if (!$exists) {
                        $fail('The selected commission is invalid.');
                    }
                }
            ]
        ]);

        $this->commissionService->approveCommissions($request->commission_ids);

        return back()->with('success', 'Selected commissions approved successfully.');
    }

    /**
     * Bulk Pay
     */
    public function bulkPay(Request $request)
    {
        $request->validate([
            'commission_ids' => 'required|array',
            'commission_ids.*' => [
                'exists:staff_commissions,id',
                function ($attribute, $value, $fail) {
                    $exists = StaffCommission::where('id', $value)
                        ->where('salon_id', auth()->user()->salon_id)
                        ->exists();
                    if (!$exists) {
                        $fail('The selected commission is invalid.');
                    }
                }
            ]
        ]);

        $this->commissionService->markCommissionsAsPaid($request->commission_ids);

        return back()->with('success', 'Selected commissions marked as paid.');
    }
}
