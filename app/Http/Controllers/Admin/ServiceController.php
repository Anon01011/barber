<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|manager']);
    }

    public function index()
    {
        $services = Service::with('category', 'staff')->orderBy('name')->get();
        $staff = User::role('employee')->where('salon_id', auth()->user()->salon_id)->orderBy('name')->get();
        return view('admin.services.index', compact('services', 'staff'));
    }

    public function create()
    {
        $categories = ServiceCategory::where('salon_id', auth()->user()->salon_id)->orderBy('name')->where('status', 'active')->get();
        return view('admin.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'category_id' => [
                'required',
                Rule::exists('service_categories', 'id')->where('salon_id', auth()->user()->salon_id),
            ],
            'status' => 'required|in:active,inactive',
        ]);

        // Enforce service limit based on salon plan
        $salon = auth()->user()->salon;
        if ($salon && !$salon->canAddService()) {
            return redirect()->back()->withInput()->with('error', 'Service limit reached for your current plan. Please upgrade to add more services.');
        }

        $service = Service::create($validated);

        if ($request->has('staff_ids')) {
            $service->staff()->sync($request->staff_ids);
        }

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && $service->salon_id !== $user->salon_id) {
            abort(403, 'Unauthorized access to this service.');
        }

        if (request()->ajax()) {
            $service->load('staff');
            return response()->json([
                'id' => $service->id,
                'name' => $service->name,
                'name_ar' => $service->name_ar,
                'category_id' => $service->category_id,
                'price' => $service->price,
                'duration' => $service->duration,
                'status' => $service->status,
                'description' => $service->description,
                'staff_ids' => $service->staff->pluck('id'),
            ]);
        }
        $salonId = $user->salon_id;
        $categories = ServiceCategory::where('salon_id', $salonId)->orderBy('name')->where('status', 'active')->get();
        return view('admin.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && $service->salon_id !== $user->salon_id) {
            abort(403, 'Unauthorized access to this service.');
        }

        $salonId = $user->salon_id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'category_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('service_categories', 'id')->where('salon_id', $salonId),
            ],
            'status' => 'required|in:active,inactive',
        ]);
        $service->update($validated);

        if ($request->has('staff_ids')) {
            $validStaffIds = \App\Models\User::where('salon_id', $salonId)
                ->whereIn('id', (array) $request->staff_ids)
                ->pluck('id');
            $service->staff()->sync($validStaffIds);
        } else {
            $service->staff()->detach();
        }

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && $service->salon_id !== $user->salon_id) {
            abort(403, 'Unauthorized access to this service.');
        }

        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
    }
}