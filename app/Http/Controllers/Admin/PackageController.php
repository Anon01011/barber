<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Service;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:packages.view|packages.create|packages.edit|packages.delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:packages.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:packages.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:packages.delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages = Package::withCount('services')->paginate(15);

        return view('admin.packages.index', compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $services = Service::active()->get(['id', 'name', 'price']);
        $taxRates = [0, 5, 8.25, 10, 15]; // Common tax rates, can be from config
        $validityValue = 30; // Default validity value
        $validityUnit = 'days'; // Default validity unit

        return view('admin.packages.create', compact('services', 'taxRates', 'validityValue', 'validityUnit'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('Package creation started', [
            'request_data' => $request->all(),
            'user_id' => auth()->id()
        ]);

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:fixed,customizable',
            'price' => 'nullable|numeric|min:0',
            'special_price' => 'nullable|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'validity_value' => 'required|integer|min:1|max:3650',
            'validity_unit' => 'required|in:days,weeks,months,years',
            'validity_description' => 'nullable|string|max:500',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id',
            'quantity.*' => 'nullable|integer|min:1|max:10',
            'is_active' => 'nullable|boolean',
            'service_limit' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed', [
                'errors' => $validator->errors()->toArray(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        \Log::info('Validation passed', ['validated_data' => $validated]);

        // Enforce package limit based on salon plan
        $salon = auth()->user()->salon;
        if ($salon && !$salon->canAddPackage()) {
            return redirect()->back()->withInput()->with('error', 'Package limit reached for your current plan. Please upgrade to add more packages.');
        }

        try {
            // Calculate total from services
            $totalFromServices = 0;
            $servicesData = [];
            foreach ($request->services as $index => $serviceId) {
                $quantity = $request->input("quantity.{$index}", 1);
                $service = \App\Models\Service::find($serviceId);
                if (!$service) {
                    \Log::error('Service not found', ['service_id' => $serviceId]);
                    throw new \Exception("Service with ID {$serviceId} not found");
                }
                $totalFromServices += $service->price * $quantity;
                $servicesData[$serviceId] = ['quantity' => $quantity];
            }

            \Log::info('Services calculated', [
                'total_from_services' => $totalFromServices,
                'services_data' => $servicesData
            ]);

            // Validate special_price (selling price) against totalFromServices
            if ($request->filled('special_price') && $request->special_price > $totalFromServices) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['special_price' => 'The selling price cannot be higher than the total value of selected services (' . format_currency($totalFromServices) . ').']);
            }

            // Set price to calculated total unless special_price is provided
            $validated['price'] = $request->special_price ? $request->special_price : $totalFromServices;

            $validated['is_active'] = $request->has('is_active');
            $package = Package::create($validated);

            \Log::info('Package created', ['package_id' => $package->id]);

            // Attach services with quantities
            $package->services()->attach($servicesData);

            \Log::info('Services attached successfully');

            return redirect()->route('admin.packages.index')
                ->with('success', 'Package created successfully.');

        } catch (\Exception $e) {
            \Log::error('Package creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create package: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Package $package)
    {
        $package->load('services');

        return view('admin.packages.show', compact('package'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Package $package)
    {
        $services = Service::active()->get(['id', 'name', 'price']);
        $taxRates = [0, 5, 8.25, 10, 15];
        $package->load('services');

        return view('admin.packages.edit', compact('package', 'services', 'taxRates'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:fixed,customizable',
            'price' => 'nullable|numeric|min:0',
            'special_price' => 'nullable|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'validity_value' => 'required|integer|min:1|max:3650',
            'validity_unit' => 'required|in:days,weeks,months,years',
            'validity_description' => 'nullable|string|max:500',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id',
            'quantity.*' => 'nullable|integer|min:1|max:10',
            'is_active' => 'nullable|boolean',
            'service_limit' => 'nullable|integer|min:1',
        ]);

        // Calculate total from services
        $totalFromServices = 0;
        $servicesData = [];
        foreach ($request->services as $index => $serviceId) {
            $quantity = $request->input("quantity.{$index}", 1);
            $service = \App\Models\Service::find($serviceId);
            $totalFromServices += $service->price * $quantity;
            $servicesData[$serviceId] = ['quantity' => $quantity];
        }

        // Validate special_price (selling price) against totalFromServices
        if ($request->filled('special_price') && $request->special_price > $totalFromServices) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['special_price' => 'The selling price cannot be higher than the total value of selected services (' . format_currency($totalFromServices) . ').']);
        }

        // Set price to calculated total unless special_price is provided
        $validated['price'] = $request->special_price ? $request->special_price : $totalFromServices;
        $validated['is_active'] = $request->has('is_active');

        $package->update($validated);

        // Sync services with quantities
        $package->services()->sync($servicesData);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package deleted successfully.');
    }
}
