/**
* Get customer's package service limits (for customizable packages in booking modal).
* Calculates how many services can still be selected from each package.
*/
public function getCustomerPackageServiceLimits(Customer $customer)
{
if ($customer->salon_id !== auth()->user()->salon_id) {
return response()->json(['error' => 'Unauthorized'], 403);
}

Log::info('Fetching package service limits for customer', [
'customer_id' => $customer->id,
'customer_name' => $customer->name
]);

// Get all packages with balances for this customer
$packageBalances = CustomerPackageBalance::where('customer_id', $customer->id)
->where('payment_status', 'paid')
->with(['package.services', 'service'])
->get();

// Group by package
$packageLimits = $packageBalances->groupBy('package_id')
->map(function ($balances) {
$package = $balances->first()->package;

// Skip if package doesn't exist or has no service limit (standard package)
if (!$package || !$package->service_limit || $package->service_limit <= 0) { return null; } // Count how many services
    have been used (quantity_remaining=0 or partially used) $totalServicesInPackage=$balances->count();
    $servicesWithRemainingQty = $balances->filter(function ($b) {
    return $b->quantity_remaining > 0;
    })->count();

    // Services used = total services that have balance records - services with remaining qty
    // This works because balance records are created when package is purchased
    $servicesUsed = $totalServicesInPackage - $servicesWithRemainingQty;

    // Calculate remaining selections
    $servicesRemaining = max(0, $package->service_limit - $servicesUsed);

    // Get all available services from the package
    $availableServices = $package->services->map(function ($service) use ($balances) {
    // Check if this service has a balance record
    $balance = $balances->firstWhere('service_id', $service->id);

    return [
    'id' => $service->id,
    'name' => $service->name,
    'price' => $service->price,
    'duration' => $service->duration,
    'has_balance' => $balance !== null,
    'quantity_remaining' => $balance ? $balance->quantity_remaining : 0,
    'already_selected' => $balance !== null && $balance->quantity_remaining == 0
    ];
    });

    return [
    'package_id' => $package->id,
    'package_name' => $package->name,
    'service_limit' => $package->service_limit,
    'services_used' => $servicesUsed,
    'services_remaining' => $servicesRemaining,
    'total_services' => $package->services->count(),
    'available_services' => $availableServices
    ];
    })
    ->filter() // Remove nulls (standard packages)
    ->values();

    Log::info('Returning package service limits', [
    'packages_count' => $packageLimits->count(),
    'data' => $packageLimits
    ]);

    return response()->json($packageLimits);
    }
