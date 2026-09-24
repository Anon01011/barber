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
    }

    public function index()
    {
        $categories = ServiceCategory::withCount('services')
            ->with([
                'services' => function ($query) {
                    $query->select('id', 'name', 'price', 'category_id', 'status', 'duration')
                        ->orderBy('name');
                }
            ])
            ->orderBy('name')
            ->get();

        $staff = User::role('employee')->where('salon_id', auth()->user()->salon_id)->orderBy('name')->get();
        return view('admin.services.categories.index', compact('categories', 'staff'));
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

        $salon = app()->bound('current_salon') ? app('current_salon') : null;

        if (!$salon) {
            abort(500, 'Salon context not available');
        }
        if (!$salon->canAddServiceCategory()) {
            return redirect()->route('admin.services.categories.index')
                ->with('error', 'You have reached the maximum number of service categories allowed for your plan.');
        }

        ServiceCategory::create($validated);

        return redirect()->route('admin.services.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(ServiceCategory $category)
    {
        return response()->json([
            'name' => $category->name,
            'name_ar' => $category->name_ar,
            'description' => $category->description,
            'status' => $category->status,
        ]);
    }

    public function update(Request $request, ServiceCategory $category)
    {
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
        $salon = app()->bound('current_salon') ? app('current_salon') : null;

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
            'available_for_online_booking' => 'boolean'
        ]);

        $service = $category->services()->create([
            'name' => $validated['name'],
            'name_ar' => $validated['name_ar'] ?? null,
            'description' => $validated['description'],
            'price' => $validated['price'],
            'duration' => $validated['duration'],
            'status' => $validated['is_active'] ? 'active' : 'inactive',
            'available_for_online_booking' => $validated['available_for_online_booking'] ?? true
        ]);

        if ($request->has('staff_ids')) {
            $service->staff()->sync($request->staff_ids);
        }

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully',
            'service' => $service
        ]);
    }

    public function showService(Service $service)
    {
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
        ]);
    }

    public function updateService(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
            'available_for_online_booking' => 'boolean'
        ]);

        $service->update($validated);

        if ($request->has('staff_ids')) {
            $service->staff()->sync($request->staff_ids);
        } else {
            $service->staff()->detach();
        }

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
        $validated = $request->validate([
            'staff_id' => 'required|exists:users,id',
            'action' => 'required|in:assign,remove'
        ]);

        $userId = $validated['staff_id'];
        $action = $validated['action'];
        $salonId = auth()->user()->salon_id;

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