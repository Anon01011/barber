<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\Service;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MembershipController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:memberships.view|memberships.create|memberships.edit|memberships.delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:memberships.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:memberships.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:memberships.delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $memberships = Membership::withCount(['services', 'inventoryItems'])->paginate(15);

        return view('admin.memberships.index', compact('memberships'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $services = Service::active()->get(['id', 'name', 'price']);
        $inventoryItems = InventoryItem::where('is_active', true)->get(['id', 'name', 'selling_price']);
        $products = $inventoryItems;

        $settingsService = app(\App\Services\SettingsService::class);
        $globalSettings = [
            'currency_symbol' => $settingsService->get('currency_symbol', '$'),
            'currency_code' => $settingsService->get('currency_code', 'USD'),
        ];

        return view('admin.memberships.create', compact('services', 'inventoryItems', 'products', 'globalSettings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('Membership creation started', [
            'request_data' => $request->all(),
            'user_id' => auth()->id()
        ]);

        // Filter out null values from arrays
        $filteredData = $request->all();
        $filteredData['items'] = array_filter($request->items, fn($item) => !is_null($item));
        $filteredData['item_types'] = array_filter($request->item_types, fn($type) => !is_null($type));
        $filteredData['quantity'] = array_filter($request->quantity, fn($qty) => !is_null($qty));
        $filteredData['discount_type'] = array_filter($request->discount_type, fn($type) => !is_null($type));
        $filteredData['item_discount_value'] = array_filter($request->item_discount_value, fn($val) => !is_null($val));
        $filteredData['membership_price'] = array_filter($request->membership_price, fn($price) => !is_null($price));

        $validator = Validator::make($filteredData, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_value' => 'required|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'is_taxable' => 'nullable|boolean',
            'membership_type' => 'required|in:all,services,products',
            'validity_value' => 'required|integer|min:1|max:3650',
            'validity_unit' => 'required|in:days,weeks,months,years',
            'items' => 'required|array|min:1',
            'items.*' => 'nullable|numeric|min:1',
            'item_types' => 'required|array|min:1',
            'item_types.*' => 'nullable|in:service,product',
            'quantity.*' => 'nullable|integer|min:1',
            'discount_type.*' => 'nullable|in:amount,percent',
            'item_discount_value' => 'required|array|min:1',
            'item_discount_value.*' => 'nullable|numeric|min:0',
            'membership_price.*' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', [
                'errors' => $validator->errors()->toArray(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        Log::info('Validation passed', ['validated_data' => $validated]);

        // Enforce membership limit based on salon plan
        $salon = auth()->user()->salon;
        if ($salon && !$salon->canAddMembership()) {
            return redirect()->back()->withInput()->with('error', 'Membership limit reached for your current plan. Please upgrade to add more memberships.');
        }

        try {
            $servicesData = [];
            $inventoryData = [];

            foreach ($request->items as $index => $itemId) {
                if (is_null($itemId)) {
                    continue;
                }
                $itemType = $request->item_types[$index];
                $quantity = $request->input("quantity.{$index}");
                $discountType = $request->input("discount_type.{$index}");
                $discountValue = $request->input("item_discount_value.{$index}");
                $membershipPrice = $request->input("membership_price.{$index}");

                if ($itemType === 'service') {
                    $service = Service::find($itemId);
                    if (!$service) {
                        Log::error('Service not found', ['service_id' => $itemId]);
                        throw new \Exception("Service with ID {$itemId} not found");
                    }
                    $servicesData[$itemId] = [
                        'quantity' => $quantity,
                        'discount_type' => $discountType,
                        'discount_value' => $discountValue,
                        'membership_price' => $membershipPrice
                    ];
                } elseif ($itemType === 'product') {
                    $item = InventoryItem::find($itemId);
                    if (!$item) {
                        Log::error('Inventory item not found', ['item_id' => $itemId]);
                        throw new \Exception("Inventory item with ID {$itemId} not found");
                    }
                    $inventoryData[$itemId] = [
                        'quantity' => $quantity,
                        'discount_type' => $discountType,
                        'discount_value' => $discountValue,
                        'membership_price' => $membershipPrice
                    ];
                }
            }

            Log::info('Services data prepared', ['services_data' => $servicesData]);
            Log::info('Inventory data prepared', ['inventory_data' => $inventoryData]);

            $validated['is_active'] = $request->has('is_active');
            $membership = Membership::create($validated);

            Log::info('Membership created', ['membership_id' => $membership->id]);

            $membership->services()->attach($servicesData);
            if (!empty($inventoryData)) {
                $membership->inventoryItems()->attach($inventoryData);
            }

            Log::info('Services and inventory attached successfully');

            return redirect()->route('admin.memberships.index')
                ->with('success', 'Membership created successfully.');

        } catch (\Exception $e) {
            Log::error('Membership creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create membership: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Membership $membership)
    {
        $membership->load(['services', 'inventoryItems']);

        return view('admin.memberships.show', compact('membership'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Membership $membership)
    {
        $services = Service::active()->get(['id', 'name', 'price']);
        $inventoryItems = InventoryItem::where('is_active', true)->get(['id', 'name', 'selling_price']);
        $products = $inventoryItems;
        $membership->load(['services', 'inventoryItems']);

        $settingsService = app(\App\Services\SettingsService::class);
        $globalSettings = [
            'currency_symbol' => $settingsService->get('currency_symbol', '$'),
            'currency_code' => $settingsService->get('currency_code', 'USD'),
        ];

        return view('admin.memberships.edit', compact('membership', 'services', 'inventoryItems', 'products', 'globalSettings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Membership $membership)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_value' => 'required|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'is_taxable' => 'nullable|boolean',
            'membership_type' => 'required|in:all,services,products',
            'validity_value' => 'required|integer|min:1|max:3650',
            'validity_unit' => 'required|in:days,weeks,months,years',
            'items' => 'required|array|min:1',
            'items.*' => 'nullable|numeric|min:1',
            'item_types' => 'required|array|min:1',
            'item_types.*' => 'nullable|in:service,product',
            'quantity.*' => 'nullable|integer|min:1',
            'discount_type.*' => 'nullable|in:amount,percent',
            'item_discount_value' => 'required|array|min:1',
            'item_discount_value.*' => 'nullable|numeric|min:0',
            'membership_price.*' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $servicesData = [];
        $inventoryData = [];

        foreach ($request->items as $index => $itemId) {
            if (is_null($itemId)) {
                continue;
            }
            $itemType = $request->item_types[$index];
            $quantity = $request->input("quantity.{$index}");
            $discountType = $request->input("discount_type.{$index}");
            $discountValue = $request->input("item_discount_value.{$index}");
            $membershipPrice = $request->input("membership_price.{$index}");

            if ($itemType === 'service') {
                $servicesData[$itemId] = [
                    'quantity' => $quantity,
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                    'membership_price' => $membershipPrice
                ];
            } elseif ($itemType === 'product') {
                $inventoryData[$itemId] = [
                    'quantity' => $quantity,
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                    'membership_price' => $membershipPrice
                ];
            }
        }

        $validated['is_active'] = $request->has('is_active');
        $membership->update($validated);

        $membership->services()->sync($servicesData);
        if (!empty($inventoryData)) {
            $membership->inventoryItems()->sync($inventoryData);
        } else {
            $membership->inventoryItems()->detach();
        }

        return redirect()->route('admin.memberships.index')
            ->with('success', 'Membership updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Membership $membership)
    {
        $membership->delete();

        return redirect()->route('admin.memberships.index')
            ->with('success', 'Membership deleted successfully.');
    }
}
