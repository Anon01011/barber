<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\User;

class ServiceCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin|salon_admin|manager']);
        $this->middleware('permission:services.view')->only(['index', 'show']);
        $this->middleware('permission:services.create')->only(['create', 'store']);
        $this->middleware('permission:services.edit')->only(['edit', 'update', 'bulkStaffAssignment']);
        $this->middleware('permission:services.delete')->only(['destroy']);
    }

    /**
     * Resolve current salon ID safely.
     */
    protected function getSalonId(): ?int
    {
        if (app()->bound('current_salon') && app('current_salon')) {
            return app('current_salon')->id;
        }
        $salonId = auth()->user()?->salon_id ?? session('current_salon_id');
        if (!$salonId && request()->route('salon_slug')) {
            $salon = \App\Models\Salon::where('slug', request()->route('salon_slug'))->first();
            $salonId = $salon?->id;
        }
        return $salonId ? (int) $salonId : null;
    }

    /**
     * Resolve current salon model safely.
     */
    protected function getSalon(): ?\App\Models\Salon
    {
        if (app()->bound('current_salon') && app('current_salon')) {
            return app('current_salon');
        }
        $salon = auth()->user()?->salon;
        if ($salon) {
            return $salon;
        }
        $salonId = $this->getSalonId();
        if ($salonId) {
            return \App\Models\Salon::find($salonId);
        }
        return null;
    }

    public function index()
    {
        $salonId = $this->getSalonId();

        $categories = ServiceCategory::when($salonId, fn($q) => $q->where('salon_id', $salonId))
            ->withCount('services')
            ->with([
                'services' => function ($query) {
                    $query->select('id', 'name', 'price', 'category_id', 'status', 'duration')
                        ->orderBy('name');
                }
            ])
            ->orderBy('name')
            ->get();

        $staff = User::role('employee')
            ->when($salonId, fn($q) => $q->where('salon_id', $salonId))
            ->orderBy('name')
            ->get();

        $salon = $this->getSalon();
        $inventoryEnabled = \App\Helpers\ModuleHelper::inventoryEnabled()
            && (!$salon || $salon->canUseFeature('Inventory Management'));

        $consumableProducts = $inventoryEnabled
            ? \App\Models\InventoryItem::when($salonId, fn($q) => $q->where('salon_id', $salonId))
                ->active()
                ->orderBy('name')
                ->get(['id', 'name', 'unit_type', 'purchase_price', 'selling_price', 'pack_capacity', 'base_unit', 'product_type'])
            : collect();

        return view('admin.services.categories.index', compact('categories', 'staff', 'consumableProducts', 'inventoryEnabled'));
    }

    public function create()
    {
        return view('admin.services.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $salon = $this->getSalon();

        if (!$salon) {
            abort(500, 'Salon context not available');
        }
        if (!$salon->canAddServiceCategory()) {
            return redirect()->route('admin.services.categories.index')
                ->with('error', 'You have reached the maximum number of service categories allowed for your plan.');
        }

        ServiceCategory::create(array_merge($validated, ['salon_id' => $salon->id]));

        return redirect()->route('admin.services.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(ServiceCategory $category)
    {
        if (!auth()->user()->isSuperAdmin() && $category->salon_id !== auth()->user()->salon_id) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        return response()->json([
            'name' => $category->name,
            'name_ar' => $category->name_ar,
            'description' => $category->description,
            'status' => $category->status,
        ]);
    }

    public function update(Request $request, ServiceCategory $category)
    {
        if (!auth()->user()->isSuperAdmin() && $category->salon_id !== auth()->user()->salon_id) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'category' => $category->fresh()
        ]);
    }

    public function destroy(ServiceCategory $category)
    {
        if (!auth()->user()->isSuperAdmin() && $category->salon_id !== auth()->user()->salon_id) {
            abort(403, 'Unauthorized access to this category.');
        }

        if ($category->services()->exists()) {
            return redirect()->route('admin.services.categories.index')
                ->with('error', 'Cannot delete category with associated services.');
        }

        $category->delete();

        return redirect()->route('admin.services.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function storeService(Request $request, ServiceCategory $category)
    {
        $salon = $this->getSalon() ?? $category->salon;

        if (!$salon) {
            abort(500, 'Salon context not available');
        }
        if (!$salon->canAddService()) {
            return response()->json([
                'success' => false,
                'message' => 'You have reached the maximum number of services allowed for your plan.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'available_for_online_booking' => 'boolean',
            'consumables' => 'nullable|array',
            'consumables.*.inventory_item_id' => [
                'required_with:consumables',
                \Illuminate\Validation\Rule::exists('inventory_items', 'id')->where('salon_id', $salon->id)->whereNull('deleted_at'),
            ],
            'consumables.*.quantity' => 'required_with:consumables|numeric|gt:0',
            'consumables.*.unit' => 'nullable|string|max:20',
            'consumables.*.is_billable_extra' => 'nullable|boolean',
            'consumables.*.extra_price_per_unit' => 'nullable|numeric|min:0',
        ]);

        $service = \DB::transaction(function () use ($category, $validated, $request, $salon) {
            $service = $category->services()->create([
                'name' => $validated['name'],
                'name_ar' => $validated['name_ar'] ?? null,
                'description' => $validated['description'],
                'price' => $validated['price'],
                'duration' => $validated['duration'],
                'status' => ($validated['is_active'] ?? true) ? 'active' : 'inactive',
                'available_for_online_booking' => $validated['available_for_online_booking'] ?? true
            ]);

            if ($request->has('staff_ids')) {
                $service->staff()->sync($request->staff_ids);
            }

            // Sync Recipe Consumables (only if inventory module is enabled)
            $inventoryEnabled = \App\Helpers\ModuleHelper::inventoryEnabled()
                && (!$salon || $salon->canUseFeature('Inventory Management'));

            if ($inventoryEnabled && $request->has('consumables') && is_array($request->consumables)) {
                foreach ($request->consumables as $c) {
                    if (!empty($c['inventory_item_id']) && isset($c['quantity']) && (float)$c['quantity'] > 0) {
                        $service->recipeItems()->create([
                            'salon_id' => $service->salon_id,
                            'inventory_item_id' => $c['inventory_item_id'],
                            'quantity' => (float) $c['quantity'],
                            'unit' => $c['unit'] ?? 'pcs',
                            'is_optional' => !empty($c['is_optional']),
                            'is_billable_extra' => !empty($c['is_billable_extra']),
                            'extra_price_per_unit' => (float) ($c['extra_price_per_unit'] ?? 0),
                        ]);
                    }
                }
            }

            return $service;
        });

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully',
            'service' => $service
        ]);
    }

    public function showService(Service $service)
    {
        $salon = $this->getSalon() ?? $service->salon;
        $inventoryEnabled = \App\Helpers\ModuleHelper::inventoryEnabled()
            && (!$salon || $salon->canUseFeature('Inventory Management'));

        $service->load(['recipeItems.inventoryItem', 'staff']);

        return response()->json([
            'id' => $service->id,
            'name' => $service->name,
            'name_ar' => $service->name_ar,
            'description' => $service->description,
            'price' => $service->price,
            'duration' => $service->duration,
            'status' => $service->status,
            'available_for_online_booking' => (bool) $service->available_for_online_booking,
            'staff_ids' => $service->staff->pluck('id'),
            'inventory_enabled' => $inventoryEnabled,
            'consumables' => $inventoryEnabled ? $service->recipeItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'inventory_item_id' => $item->inventory_item_id,
                    'item_name' => $item->inventoryItem->name ?? 'Unknown',
                    'quantity' => (float) $item->quantity,
                    'unit' => $item->unit,
                    'is_optional' => (bool) $item->is_optional,
                    'is_billable_extra' => (bool) $item->is_billable_extra,
                    'extra_price_per_unit' => (float) $item->extra_price_per_unit,
                    'cost_per_unit' => (float) ($item->inventoryItem->cost_per_unit ?? 0),
                    'total_cost' => (float) $item->estimated_cost,
                ];
            }) : [],
            'estimated_cogs' => $inventoryEnabled ? $service->calculateEstimatedCogs() : 0,
        ]);
    }

    public function updateService(Request $request, Service $service)
    {
        $salon = $this->getSalon() ?? $service->salon;
        $salonId = $salon?->id ?? $service->salon_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
            'available_for_online_booking' => 'boolean',
            'consumables' => 'nullable|array',
            'consumables.*.inventory_item_id' => [
                'required_with:consumables',
                \Illuminate\Validation\Rule::exists('inventory_items', 'id')->where('salon_id', $salonId)->whereNull('deleted_at'),
            ],
            'consumables.*.quantity' => 'required_with:consumables|numeric|gt:0',
            'consumables.*.unit' => 'nullable|string|max:20',
            'consumables.*.is_billable_extra' => 'nullable|boolean',
            'consumables.*.extra_price_per_unit' => 'nullable|numeric|min:0',
        ]);

        \DB::transaction(function () use ($service, $validated, $request, $salon) {
            $service->update([
                'name' => $validated['name'],
                'name_ar' => $validated['name_ar'] ?? null,
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'duration' => $validated['duration'],
                'status' => $validated['status'],
                'available_for_online_booking' => $validated['available_for_online_booking'] ?? $service->available_for_online_booking,
            ]);

            if ($request->has('staff_ids')) {
                $service->staff()->sync($request->staff_ids);
            } else {
                $service->staff()->detach();
            }

            // Re-sync Recipe Consumables atomically (only if inventory module enabled)
            $inventoryEnabled = \App\Helpers\ModuleHelper::inventoryEnabled()
                && (!$salon || $salon->canUseFeature('Inventory Management'));

            if ($inventoryEnabled && $request->has('consumables')) {
                $service->recipeItems()->delete();
                if (is_array($request->consumables)) {
                    foreach ($request->consumables as $c) {
                        if (!empty($c['inventory_item_id']) && isset($c['quantity']) && (float)$c['quantity'] > 0) {
                            $service->recipeItems()->create([
                                'salon_id' => $service->salon_id,
                                'inventory_item_id' => $c['inventory_item_id'],
                                'quantity' => (float) $c['quantity'],
                                'unit' => $c['unit'] ?? 'pcs',
                                'is_optional' => !empty($c['is_optional']),
                                'is_billable_extra' => !empty($c['is_billable_extra']),
                                'extra_price_per_unit' => (float) ($c['extra_price_per_unit'] ?? 0),
                            ]);
                        }
                    }
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully'
        ]);
    }

    public function destroyService(Service $service)
    {
        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully'
        ]);
    }

    /**
     * Bulk assign or remove a staff member for all services in a category.
     */
    public function bulkStaffAssignment(Request $request, ServiceCategory $category)
    {
        $salonId = auth()->user()->salon_id;
        if (!auth()->user()->isSuperAdmin() && $category->salon_id !== $salonId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $validated = $request->validate([
            'staff_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('users', 'id')->where('salon_id', $salonId),
            ],
            'action' => 'required|in:assign,remove'
        ]);

        $userId = $validated['staff_id'];
        $action = $validated['action'];

        // Fetch all services belonging to this category and salon
        $services = Service::where('category_id', $category->id)
            ->where('salon_id', $salonId)
            ->get();

        if ($services->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No services found in this category.'
            ], 422);
        }

        // Find the Employee record for this User
        $employee = \App\Models\Employee::where('user_id', $userId)
            ->where('salon_id', $salonId)
            ->first();

        // Perform the bulk update
        \DB::beginTransaction();
        try {
            foreach ($services as $service) {
                if ($action === 'assign') {
                    // Update service_user table (User class)
                    if (!$service->staff()->where('user_id', $userId)->exists()) {
                        $service->staff()->attach($userId);
                    }
                    
                    // Update employee_service table (Employee class)
                    if ($employee && !$service->employees()->where('employee_id', $employee->id)->exists()) {
                        $service->employees()->attach($employee->id);
                    }
                } else {
                    // Update service_user table (User class)
                    $service->staff()->detach($userId);

                    // Update employee_service table (Employee class)
                    if ($employee) {
                        $service->employees()->detach($employee->id);
                    }
                }
            }

            \DB::commit();

            $message = $action === 'assign' 
                ? 'Staff successfully assigned to all services in this category.' 
                : 'Staff successfully removed from all services in this category.';

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update staff assignments: ' . $e->getMessage()
            ], 500);
        }
    }
}