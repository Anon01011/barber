<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionProfile;
use App\Models\CommissionRule;
use App\Models\Service;
use App\Models\Product;
use App\Models\Membership;
use App\Models\Package;
use App\Models\StaffCommission;
use App\Services\CommissionService;
use Illuminate\Http\Request;

class CommissionProfileController extends Controller
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    /**
     * Display a listing of commission profiles.
     */
    public function index()
    {
        $salonId = auth()->user()->salon_id;
        
        $profiles = CommissionProfile::where('salon_id', $salonId)
            ->withCount(['rules', 'staff'])
            ->latest()
            ->get();

        return view('admin.commissions.index', compact('profiles'));
    }

    /**
     * Show the form for creating a new commission profile.
     */
    public function create()
    {
        $salonId = auth()->user()->salon_id;
        
        $services = Service::where('salon_id', $salonId)->get();
        $products = Product::where('salon_id', $salonId)->get();
        $memberships = Membership::where('salon_id', $salonId)->get();
        $packages = Package::where('salon_id', $salonId)->get();

        return view('admin.commissions.create', compact('services', 'products', 'memberships', 'packages'));
    }

    /**
     * Store a newly created commission profile.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|in:by_item,by_target',
            'include_tax' => 'boolean',
            'is_active' => 'boolean',
            'calculation_interval' => 'required_if:type,by_target|nullable|in:daily,weekly,monthly',
            'qualifying_item' => 'required_if:type,by_target|nullable|in:all,service,product,membership,package',
            'is_cascade' => 'boolean',
            'rules' => 'required|array|min:1',
        ];

        if ($request->input('type') === 'by_target') {
            $rules['rules.*.target_from'] = 'required|numeric|min:0';
            $rules['rules.*.target_to'] = 'nullable|numeric|min:0';
            $rules['rules.*.commission_type'] = 'required|in:percentage,fixed';
            $rules['rules.*.commission_value'] = 'required|numeric|min:0';
        } else {
            $rules['rules.*.item_type'] = 'required|in:service,product,membership,package';
            $rules['rules.*.item_id'] = 'nullable|integer';
            $rules['rules.*.commission_type'] = 'required|in:percentage,fixed';
            $rules['rules.*.commission_value'] = 'required|numeric|min:0';
            $rules['rules.*.target_amount'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        $salonId = auth()->user()->salon_id;

        // Create profile
        $profile = CommissionProfile::create([
            'salon_id' => $salonId,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'include_tax' => $request->boolean('include_tax'),
            'is_active' => $request->boolean('is_active', true),
            'calculation_interval' => $validated['type'] === 'by_target' ? $validated['calculation_interval'] : null,
            'qualifying_item' => $validated['type'] === 'by_target' ? $validated['qualifying_item'] : null,
            'is_cascade' => $validated['type'] === 'by_target' ? $request->boolean('is_cascade') : false,
        ]);

        // Create rules
        foreach ($validated['rules'] as $ruleData) {
            CommissionRule::create([
                'salon_id' => $salonId,
                'commission_profile_id' => $profile->id,
                'item_type' => $validated['type'] === 'by_item' ? $ruleData['item_type'] : null,
                'item_id' => $validated['type'] === 'by_item' ? ($ruleData['item_id'] ?? null) : null,
                'commission_type' => $ruleData['commission_type'],
                'commission_value' => $ruleData['commission_value'],
                'target_amount' => $validated['type'] === 'by_item' ? ($ruleData['target_amount'] ?? null) : null,
                'target_from' => $validated['type'] === 'by_target' ? $ruleData['target_from'] : 0.00,
                'target_to' => $validated['type'] === 'by_target' ? ($ruleData['target_to'] ?? null) : null,
            ]);
        }

        return redirect()->route('admin.commissions.index')
            ->with('success', 'Commission profile created successfully.');
    }

    /**
     * Show the form for editing the specified commission profile.
     */
    public function edit(CommissionProfile $profile)
    {
        // Ensure profile belongs to current salon
        if ($profile->salon_id !== auth()->user()->salon_id) {
            abort(403);
        }

        $salonId = auth()->user()->salon_id;
        
        $services = Service::where('salon_id', $salonId)->get();
        $products = Product::where('salon_id', $salonId)->get();
        $memberships = Membership::where('salon_id', $salonId)->get();
        $packages = Package::where('salon_id', $salonId)->get();

        $profile->load('rules');

        return view('admin.commissions.edit', compact('profile', 'services', 'products', 'memberships', 'packages'));
    }

    /**
     * Update the specified commission profile.
     */
    public function update(Request $request, CommissionProfile $profile)
    {
        // Ensure profile belongs to current salon
        if ($profile->salon_id !== auth()->user()->salon_id) {
            abort(403);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|in:by_item,by_target',
            'include_tax' => 'boolean',
            'is_active' => 'boolean',
            'calculation_interval' => 'required_if:type,by_target|nullable|in:daily,weekly,monthly',
            'qualifying_item' => 'required_if:type,by_target|nullable|in:all,service,product,membership,package',
            'is_cascade' => 'boolean',
            'rules' => 'required|array|min:1',
        ];

        if ($request->input('type') === 'by_target') {
            $rules['rules.*.target_from'] = 'required|numeric|min:0';
            $rules['rules.*.target_to'] = 'nullable|numeric|min:0';
            $rules['rules.*.commission_type'] = 'required|in:percentage,fixed';
            $rules['rules.*.commission_value'] = 'required|numeric|min:0';
        } else {
            $rules['rules.*.item_type'] = 'required|in:service,product,membership,package';
            $rules['rules.*.item_id'] = 'nullable|integer';
            $rules['rules.*.commission_type'] = 'required|in:percentage,fixed';
            $rules['rules.*.commission_value'] = 'required|numeric|min:0';
            $rules['rules.*.target_amount'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        // Update profile
        $profile->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'include_tax' => $request->boolean('include_tax'),
            'is_active' => $request->boolean('is_active', true),
            'calculation_interval' => $validated['type'] === 'by_target' ? $validated['calculation_interval'] : null,
            'qualifying_item' => $validated['type'] === 'by_target' ? $validated['qualifying_item'] : null,
            'is_cascade' => $validated['type'] === 'by_target' ? $request->boolean('is_cascade') : false,
        ]);

        // Delete old rules and create new ones
        $profile->rules()->delete();
        
        foreach ($validated['rules'] as $ruleData) {
            CommissionRule::create([
                'salon_id' => $profile->salon_id,
                'commission_profile_id' => $profile->id,
                'item_type' => $validated['type'] === 'by_item' ? $ruleData['item_type'] : null,
                'item_id' => $validated['type'] === 'by_item' ? ($ruleData['item_id'] ?? null) : null,
                'commission_type' => $ruleData['commission_type'],
                'commission_value' => $ruleData['commission_value'],
                'target_amount' => $validated['type'] === 'by_item' ? ($ruleData['target_amount'] ?? null) : null,
                'target_from' => $validated['type'] === 'by_target' ? $ruleData['target_from'] : 0.00,
                'target_to' => $validated['type'] === 'by_target' ? ($ruleData['target_to'] ?? null) : null,
            ]);
        }

        return redirect()->route('admin.commissions.index')
            ->with('success', 'Commission profile updated successfully.');
    }

    /**
     * Remove the specified commission profile.
     */
    public function destroy(CommissionProfile $profile)
    {
        // Ensure profile belongs to current salon
        if ($profile->salon_id !== auth()->user()->salon_id) {
            abort(403);
        }

        // Check if profile is assigned to any staff
        if ($profile->staff()->count() > 0) {
            return back()->with('error', 'Cannot delete profile that is assigned to staff members.');
        }

        $profile->delete();

        return redirect()->route('admin.commissions.index')
            ->with('success', 'Commission profile deleted successfully.');
    }

    /**
     * Show commission reports.
     */
    public function reports(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $status = $request->get('status', 'all');

        $query = StaffCommission::where('salon_id', $salonId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['staff', 'profile']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $commissions = $query->latest()->paginate(50);

        $summary = [
            'total_pending' => StaffCommission::where('salon_id', $salonId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'pending')
                ->sum('commission_amount'),
            'total_approved' => StaffCommission::where('salon_id', $salonId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'approved')
                ->sum('commission_amount'),
            'total_paid' => StaffCommission::where('salon_id', $salonId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'paid')
                ->sum('commission_amount'),
        ];

        return view('admin.commissions.reports', compact('commissions', 'summary', 'startDate', 'endDate', 'status'));
    }

    /**
     * Approve selected commissions.
     */
    public function approveCommissions(Request $request)
    {
        $validated = $request->validate([
            'commission_ids' => 'required|array',
            'commission_ids.*' => 'exists:staff_commissions,id',
        ]);

        $count = $this->commissionService->approveCommissions($validated['commission_ids']);

        return back()->with('success', "{$count} commission(s) approved successfully.");
    }

    /**
     * Mark selected commissions as paid.
     */
    public function markAsPaid(Request $request)
    {
        $validated = $request->validate([
            'commission_ids' => 'required|array',
            'commission_ids.*' => 'exists:staff_commissions,id',
        ]);

        $count = $this->commissionService->markCommissionsAsPaid($validated['commission_ids']);

        return back()->with('success', "{$count} commission(s) marked as paid successfully.");
    }
}
