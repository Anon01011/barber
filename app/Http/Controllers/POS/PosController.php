<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerMembership;
use App\Models\CustomerPackageBalance;
use App\Models\InventoryItem;
use App\Models\Membership;
use App\Models\Package;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\Salon;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Helpers\CustomerDataHelper;
use Illuminate\Support\Facades\Mail;
use App\Mail\PosSaleReceiptMail;

class PosController extends Controller
{
    /**
     * Display the POS interface.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // 1. Get salon ID first to scope all queries
        $salonId = auth()->user()->salon_id;
        if (!$salonId) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Optimize Services: Select only needed columns
        $services = Service::active()
            ->where('salon_id', $salonId)
            ->select('id', 'name', 'price', 'salon_id')
            ->get();

        // 3. Optimize Products: Select only needed columns and eager load category with limit
        $products = InventoryItem::with(['category:id,name'])
            ->where('salon_id', $salonId)
            ->where('is_active', true)
            ->where('quantity_in_stock', '>', 0)
            ->select('id', 'name', 'selling_price', 'quantity_in_stock', 'sku', 'category_id', 'salon_id')
            ->get();

        // 4. Optimize Packages: Select columns and AVOID N+1 in calculation
        $packages = Package::active()
            ->where('salon_id', $salonId)
            ->with([
                'services' => function ($q) {
                    // Select only specific columns from services + pivot fields are auto-included
                    $q->select('services.id', 'services.name', 'services.price');
                }
            ])
            ->select('id', 'name', 'price', 'special_price', 'salon_id')
            ->get()
            ->map(function ($package) {
                // Manually calculate using eager loaded collection to avoid N+1 query from model method
                $totalValue = $package->services->sum(function ($service) {
                    return $service->price * $service->pivot->quantity;
                });
                $package->total_value = $totalValue;
                $package->savings = max(0, $package->total_value - $package->price);
                return $package;
            });

        // 5. Optimize Memberships
        $memberships = Membership::with([
            'services' => function ($q) {
                $q->select('services.id', 'services.name', 'services.price');
            },
            'inventoryItems' => function ($q) {
                $q->select('inventory_items.id', 'inventory_items.name', 'inventory_items.selling_price');
            }
        ])
            ->where('salon_id', $salonId)
            ->active()
            ->select('id', 'name', 'salon_id', 'description')
            ->get()
            ->map(function ($membership) {
                $membership->price = $membership->calculateTotalMembershipPrice();
                $membership->total_value = $membership->calculateTotalValue();
                $membership->savings = max(0, $membership->total_value - $membership->price);
                return $membership;
            });

        $staffQuery = User::where('salon_id', $salonId)
            ->where('status', 'active')
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['employee', 'barber', 'stylist', 'staff']);
            });

        if (app()->has('current_branch')) {
            $currentBranchId = app('current_branch')->id;
            $staffQuery->where(function ($q) use ($currentBranchId) {
                $q->where('branch_id', $currentBranchId)
                  ->orWhereNull('branch_id');
            });
        }

        $staff = $staffQuery->with([
                'services' => function ($query) {
                    $query->select('services.id');
                }
            ])
            ->select('id', 'name', 'salon_id', 'branch_id')
            ->get();

        // 6. Optimized Customer Query
        $customersQuery = Customer::with('user')->where('salon_id', $salonId)->where('status', 'active');

        $preselectedCustomerId = null;
        if (request()->has('booking_id')) {
            $booking = \App\Models\Booking::where('salon_id', $salonId)->find(request('booking_id'));
            if ($booking) {
                $preselectedCustomerId = $booking->customer_id;
            }
        }

        if ($preselectedCustomerId) {
            $customers = $customersQuery->where(function ($q) use ($preselectedCustomerId) {
                $q->where('id', $preselectedCustomerId)
                    ->orWhere('id', '!=', $preselectedCustomerId);
            })->orderByRaw("FIELD(id, ?) DESC", [$preselectedCustomerId])
                ->limit(50)
                ->get();
        } else {
            $customers = $customersQuery->limit(50)->get();
        }

        $customers = $customers->map(function ($customer) {
            $maskedPhone = \App\Helpers\CustomerDataHelper::getMaskedPhone($customer);
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $maskedPhone,
                'email' => \App\Helpers\CustomerDataHelper::getMaskedEmail($customer),
                'last_visit' => $customer->last_purchase_at ? $customer->last_purchase_at->format('M d, Y') : 'Never',
                'total_spent' => format_currency($customer->total_spent ?? 0),
                'text' => "{$customer->name} ({$maskedPhone})"
            ];
        });

        $customersJson = $customers->map(function ($customer) {
            return [
                'id' => $customer['id'],
                'text' => $customer['text']
            ];
        });

        $salon = auth()->user()->salon;
        $salonData = [
            'name' => $salon->name,
            'address' => $salon->address,
            'phone' => $salon->phone,
            'email' => $salon->email,
            'website' => $salon->website,
            'logo' => $salon->logo ? asset('storage/' . $salon->logo) : null,
        ];

        // Settings retrieval (uses salonId which we now have at top)
        $settings = app(\App\Services\SettingsService::class);
        $taxEnabled = $settings->get('tax_enabled', false, $salonId);
        $taxEnabledPos = $settings->get('tax_enabled_pos', $taxEnabled, $salonId);
        $taxEnabledServices = $settings->get('tax_enabled_services', $taxEnabled, $salonId);
        $taxRate = $taxEnabled ? $settings->get('tax_rate', 0, $salonId) : 0;
        $taxName = $settings->get('tax_name', 'Tax', $salonId);
        $currencySymbol = $settings->get('currency_symbol', '$', $salonId);
        $acceptCash = $settings->get('accept_cash', true, $salonId);
        $acceptCard = $settings->get('accept_card', true, $salonId);
        $acceptOnline = $settings->get('accept_online', true, $salonId);
        $tipEnabled = $settings->get('tip_enabled', true, $salonId);
        $posReceiptArabic = $settings->get('pos_receipt_arabic', true, $salonId);
        $posReceiptArabicButton = $settings->get('pos_receipt_arabic_button', true, $salonId);

        $viewParam = request()->query('view');
        if ($salon && $salon->business_type === 'both') {
            $viewName = ($viewParam === 'barber') ? 'pos.barber' : 'pos.index';
        } else {
            $viewName = ($salon && (method_exists($salon, 'isBarber') ? $salon->isBarber() : $salon->business_type === 'barber')) ? 'pos.barber' : 'pos.index';
        }

        return view($viewName, compact('services', 'products', 'packages', 'memberships', 'customers', 'customersJson', 'staff', 'salonId', 'taxEnabled', 'taxEnabledPos', 'taxEnabledServices', 'taxRate', 'taxName', 'salonData', 'currencySymbol', 'acceptCash', 'acceptCard', 'acceptOnline', 'tipEnabled', 'posReceiptArabic', 'posReceiptArabicButton'));
    }

    /**
     * Get booking details for POS integration.
     */
    public function getBookingDetailsForPos(Request $request)
    {
        $bookingId = $request->input('booking_id');
        $saleId = $request->input('sale_id');
        $salonId = auth()->user()->salon_id;

        if ($saleId && !$bookingId) {
            // If sale_id is provided, find bookings linked to this sale
            $bookingIds = \App\Models\PosSaleItem::where('sale_id', $saleId)
                ->whereNotNull('booking_id')
                ->pluck('booking_id');

            if ($bookingIds->isEmpty()) {
                // If it's a sale with no bookings, we just return the sale info
                // The POS JS should handle loading sale items if needed, but for now
                // we'll try to find any booking that might match or just return empty
                $sale = \App\Models\PosSale::with(['items.item', 'items.staff'])->find($saleId);
                $items = [];
                if ($sale) {
                    foreach ($sale->items as $item) {
                        $type = 'product';
                        if ($item->item_type === \App\Models\Service::class) {
                            $type = 'service';
                        } elseif ($item->item_type === \App\Models\Package::class) {
                            $type = 'package';
                        } elseif ($item->item_type === \App\Models\Membership::class) {
                            $type = 'membership';
                        }

                        $items[] = [
                            'type' => $type,
                            'id' => $item->item_id,
                            'name' => $item->item_name,
                            'price' => (float) $item->unit_price,
                            'quantity' => (float) $item->quantity,
                            'staff_id' => $item->staff_id,
                            'discount_amount' => (float) $item->discount,
                            'notes' => $item->notes,
                            'booking_id' => $item->booking_id,
                            'package_id' => $item->package_id,
                            'editable' => true
                        ];
                    }
                }

                return response()->json([
                    'customer_id' => $sale?->customer_id,
                    'customer_name' => $sale?->customer?->name,
                    'customer_phone' => $sale?->customer?->phone,
                    'customer_email' => $sale?->customer?->email,
                    'sale_id' => $saleId,
                    'sale_data' => $sale,
                    'items' => $items,
                    'bookings' => []
                ]);
            }
            $bookingId = $bookingIds->first();
        }

        $booking = \App\Models\Booking::where('id', $bookingId)
            ->where('salon_id', $salonId)
            ->with('customer')
            ->firstOrFail();

        // Check if this booking is part of a group
        if ($booking->booking_group_id) {
            // Fetch all bookings in the same group
            $bookings = \App\Models\Booking::where('salon_id', $salonId)
                ->where('booking_group_id', $booking->booking_group_id)
                ->where('status', '!=', 'cancelled')
                ->where('status', '!=', 'cancelled')
                ->with(['service', 'staff', 'package'])
                ->get();
        } else {
            // Single booking - return only this booking
            $bookings = collect([$booking->load(['service', 'staff', 'package'])]);
        }

        $allCompleted = $bookings->every(function ($b) {
            return in_array($b->status, ['completed', 'staff_completed']);
        });

        $allPaid = $bookings->every(function ($b) {
            return strtolower((string) $b->payment_status) === 'paid';
        });

        // Check for any existing non-voided sale associated with these bookings if not already provided
        if (!$saleId) {
            $saleItem = \App\Models\PosSaleItem::whereIn('booking_id', $bookings->pluck('id'))
                ->whereHas('sale', function ($q) {
                    $q->where('status', '!=', 'voided');
                })
                ->with('sale')
                ->get()
                ->sortBy(function ($item) {
                    // Prefer unpaid/pending sales if multiple exist
                    return $item->sale->payment_status === 'paid' ? 1 : 0;
                })
                ->first();

            if ($saleItem) {
                $saleId = $saleItem->sale_id;
            }
        }

        $sale = $saleId ? \App\Models\PosSale::with(['items.item', 'items.staff'])->find($saleId) : null;

        // Map sale items to a unified format for POS cart
        $items = [];
        if ($sale) {
            foreach ($sale->items as $item) {
                $type = 'product';
                if ($item->item_type === \App\Models\Service::class) {
                    $type = 'service';
                } elseif ($item->item_type === \App\Models\Package::class) {
                    $type = 'package';
                } elseif ($item->item_type === \App\Models\Membership::class) {
                    $type = 'membership';
                }

                $items[] = [
                    'type' => $type,
                    'id' => $item->item_id,
                    'name' => $item->item_name,
                    'price' => (float) $item->unit_price,
                    'quantity' => (float) $item->quantity,
                    'staff_id' => $item->staff_id,
                    'discount_amount' => (float) $item->discount,
                    'notes' => $item->notes,
                    'booking_id' => $item->booking_id,
                    'package_id' => $item->package_id,
                    'editable' => true
                ];
            }
        }

        return response()->json([
            'customer_id' => $booking->customer_id,
            'customer_name' => $booking->customer->name,
            'customer_phone' => $booking->customer->phone,
            'customer_email' => $booking->customer->email,
            'all_completed' => $allCompleted,
            'all_paid' => $allPaid,
            'sale_id' => $saleId,
            'sale_data' => $sale,
            'items' => $items,
            'bookings' => $bookings->map(function ($b) {
                $hasBalance = false;
                $hasAnyPackageBalance = false;

                if ($b->package_id) {
                    // (Keep existing package balance check logic...)
                    $hasBalance = \App\Models\CustomerPackageBalance::where('customer_id', $b->customer_id)
                        ->where('package_id', $b->package_id)
                        ->where('service_id', $b->service_id)
                        ->where('quantity_remaining', '>', 0)
                        ->where('payment_status', 'paid')
                        ->where(function ($query) {
                        $query->whereNull('expiry_date')
                            ->orWhere('expiry_date', '>=', now()->toDateString());
                    })
                        ->exists();

                    $hasAnyPackageBalance = \App\Models\CustomerPackageBalance::where('customer_id', $b->customer_id)
                        ->where('package_id', $b->package_id)
                        ->where('payment_status', 'paid')
                        ->exists();
                }

                return [
                    'id' => $b->id,
                    'service_id' => $b->service_id,
                    'service_name' => $b->service->name,
                    'service_price' => $b->service->price,
                    'price' => $hasBalance ? 0 : ($b->amount ?? $b->service->price),
                    'staff_id' => $b->staff_id,
                    'staff_name' => $b->staff->name,
                    'status' => $b->status,
                    'payment_status' => $b->payment_status,
                    'package_id' => $b->package_id,
                    'package_name' => $b->package ? $b->package->name : null,
                    'package_price' => $b->package ? $b->package->price : 0,
                    'has_package_balance' => $hasBalance,
                    'has_any_package_balance' => $hasAnyPackageBalance ?? false,
                    'package_service_status' => $b->package_service_status,
                    'selected_services' => $b->selected_services,
                    'start_time' => $b->start_time->toIso8601String(),
                ];
            })
        ]);
    }

    /**
     * Process a new sale with proper transaction handling and stock management.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate request data
        $salonId = auth()->user()->salon_id;
        $validated = $request->validate([
            'customer_id' => [
                'nullable',
                \Illuminate\Validation\Rule::exists('customers', 'id')->where('salon_id', $salonId)
            ],
            'booking_ids' => 'nullable|array',
            'booking_ids.*' => [
                \Illuminate\Validation\Rule::exists('bookings', 'id')->where('salon_id', $salonId)
            ],
            'sale_id' => [
                'nullable',
                'integer',
                \Illuminate\Validation\Rule::exists('pos_sales', 'id')->where('salon_id', $salonId)
            ],
            'items' => 'required|array|min:1',
            'items.*.id' => 'required',
            'items.*.type' => 'required|in:product,service,package,membership',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.staff_id' => [
                'nullable',
                \Illuminate\Validation\Rule::exists('users', 'id')->where('salon_id', $salonId)
            ],
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:1000',
            'items.*.package_id' => [
                'nullable',
                \Illuminate\Validation\Rule::exists('packages', 'id')->where('salon_id', $salonId)
            ],
            'items.*.booking_ids' => 'nullable|array',
            'items.*.selected_service_ids' => 'nullable|array',
            'items.*.selected_service_ids.*' => [
                \Illuminate\Validation\Rule::exists('services', 'id')->where('salon_id', $salonId)
            ],
            'tip' => 'nullable|numeric|min:0',
            'cash_amount' => 'nullable|numeric|min:0',
            'card_amount' => 'nullable|numeric|min:0',
            'online_amount' => 'nullable|numeric|min:0',
            'other_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percent',
            'payment_method' => 'nullable|string|in:cash,card,online,other,mixed,none,package',
            'tendered_amount' => 'nullable|numeric|min:0',
            'change_amount' => 'nullable|numeric|min:0',
        ]);

        // Initialize array to track low stock products for notification
        // Initialize array to track low stock products for notification
        $lowStockProducts = [];

        $maxRetries = 3;
        $attempt = 0;
        $result = null;

        while ($attempt < $maxRetries) {
            // Reset low stock products for each attempt to avoid duplicates from failed transactions
            $lowStockProducts = [];

            try {
                $result = DB::transaction(function () use ($validated, &$lowStockProducts, $attempt) {
                    try {
                        // Get the current salon ID from the authenticated user
                        $salonId = auth()->user()->salon_id;
                        if (!$salonId) {
                            throw new \Exception('Unauthorized action.');
                        }
                        $settings = app(\App\Services\SettingsService::class);
                        $taxEnabled = $settings->get('tax_enabled', false, $salonId);
                        // Force POS tax enabled if tax is enabled globally to ensure tax is calculated
                        $taxEnabledPos = $settings->get('tax_enabled_pos', $taxEnabled, $salonId);

                        // Get tax_enabled_services, but handle the case where it's not explicitly set
                        // The SettingsService defaults it to false, which overrides our desire to fallback to $taxEnabled
                        $taxEnabledServices = $settings->get('tax_enabled_services', null, $salonId);

                        // If global tax is enabled, but services tax is false, check if it's explicitly disabled
                        if ($taxEnabled && !$taxEnabledServices) {
                            $explicitSetting = \App\Models\Setting::where('key', 'tax_enabled_services')
                                ->where('salon_id', $salonId)
                                ->exists();

                            // If not explicitly set in DB, default to global tax setting
                            if (!$explicitSetting) {
                                $taxEnabledServices = true;
                            }
                        }

                        $taxRate = $taxEnabled ? $settings->get('tax_rate', 0, $salonId) : 0;
                        $baseTaxMultiplier = $taxRate / 100;

                        // Determine if tax should be applied based on POS-specific settings
                        $applyTaxPos = $taxEnabled && $taxEnabledPos;
                        $applyTaxServices = $taxEnabled && $taxEnabledServices;

                        $sale = null;

                        // Check if any associated booking is in the future and payment is 'none'
                        if (($validated['payment_method'] ?? null) === 'none' && !empty($validated['booking_ids'])) {
                            $hasFutureBooking = \App\Models\Booking::whereIn('id', $validated['booking_ids'])
                                ->where('start_time', '>', now())
                                ->exists();
                            if ($hasFutureBooking) {
                                throw new \Exception('Unpaid payment option is not allowed for future appointments.');
                            }
                        }

                        // Priority 1: Check if sale_id is explicitly provided
                        if (!empty($validated['sale_id'])) {
                            $sale = PosSale::where('id', $validated['sale_id'])
                                ->where('salon_id', $salonId)
                                ->where('status', '!=', 'voided')
                                // Removed payment_status != paid check to allow editing paid sales
                                ->where('payment_status', '!=', 'refunded')
                                ->first();
                        }

                        // Priority 2: Check if we have booking IDs and if there's an existing unpaid sale for them
                        if (!$sale && !empty($validated['booking_ids'])) {
                            $existingSaleItem = \App\Models\PosSaleItem::whereIn('booking_id', $validated['booking_ids'])
                                ->whereHas('sale', function ($q) {
                                    $q
                                        // Removed payment_status != paid check
                                        ->where('payment_status', '!=', 'refunded')
                                        ->where('status', '!=', 'voided');
                                })
                                ->with('sale.items')
                                ->first();

                            if ($existingSaleItem && $existingSaleItem->sale) {
                                $sale = $existingSaleItem->sale;
                            }
                        }

                        // If we found an existing sale (via either method), prepare it for update
                        $existingBookingIds = [];
                        // Track previous payments to accumulate them
                        $previousPayments = ['cash' => 0, 'card' => 0, 'online' => 0, 'other' => 0];

                        \Illuminate\Support\Facades\Log::info('POS Store Debug: Attempting to find sale', [
                            'provided_sale_id' => $validated['sale_id'] ?? 'null',
                            'booking_ids' => $validated['booking_ids'] ?? [],
                        ]);


                        if ($sale) {
                            $previousPayments['cash'] = $sale->cash_amount;
                            $previousPayments['card'] = $sale->card_amount;
                            $previousPayments['online'] = $sale->online_amount;
                            $previousPayments['other'] = $sale->other_amount;

                            \Illuminate\Support\Facades\Log::info('POS Store Debug: Found existing sale', [
                                'sale_id' => $sale->id,
                                'previous_payments' => $previousPayments
                            ]);

                            // Capture key information before deleting items
                            $existingBookingIds = $sale->items()
                                ->whereNotNull('booking_id')
                                ->pluck('booking_id')
                                ->unique()
                                ->toArray();

                            // Reverse inventory for existing items before clearing them
                            foreach ($sale->items as $existingItem) {
                                if ($existingItem->item_type === 'App\Models\InventoryItem') {
                                    $inventoryItem = \App\Models\InventoryItem::find($existingItem->item_id);
                                    if ($inventoryItem) {
                                        $inventoryItem->increment('quantity_in_stock', $existingItem->quantity);

                                        // Record reversal transaction
                                        $this->recordInventoryTransaction(
                                            $inventoryItem->id,
                                            $existingItem->quantity,
                                            'adjustment',
                                            $sale->id,
                                            \App\Models\PosSale::class,
                                            'Stock returned from updated sale #' . $sale->invoice_number,
                                            $existingItem->unit_price
                                        );
                                    }
                                }
                            }

                            // Delete existing items
                            $sale->items()->delete();

                            // Update existing sale details
                            $sale->update([
                                'customer_id' => $validated['customer_id'] ?? $sale->customer_id,
                                'employee_id' => auth()->id(),
                                'subtotal' => 0,
                                'tax' => 0,
                                'discount' => 0,
                                'tip' => 0,
                                // accumulated payments will be set at the end
                                'outstanding_amount' => 0,
                                'total' => 0,
                                'payment_method' => '',
                                'payment_status' => 'pending',
                                'notes' => $validated['notes'] ?? $sale->notes,
                                'updated_at' => now(),
                            ]);
                        }

                        // Create new sale record if no existing one found
                        if (!$sale) {
                            $sale = new PosSale([
                                'salon_id' => $salonId,
                                'customer_id' => $validated['customer_id'] ?? null,
                                'employee_id' => auth()->id(),
                                'invoice_number' => $this->generateInvoiceNumber(),
                                'subtotal' => 0,
                                'tax' => 0,
                                'discount' => 0,
                                'tip' => 0,
                                'cash_amount' => 0,
                                'card_amount' => 0,
                                'outstanding_amount' => 0,
                                'total' => 0,
                                'payment_method' => '',
                                'payment_status' => 'pending',
                                'notes' => $validated['notes'] ?? null,
                                'sale_date' => now(),
                                'refunded_amount' => 0,
                            ]);
                            $sale->save();
                        }

                        $grossSubtotal = 0;
                        $totalItemDiscount = 0;
                        $totalTax = 0;
                        $items = [];

                        // Get customer's active membership for discount application
                        $customerMembership = null;
                        if ($validated['customer_id']) {
                            $customerMembership = CustomerMembership::active()
                                ->where('customer_id', $validated['customer_id'])
                                ->with('membership.services', 'membership.inventoryItems')
                                ->first();
                        }

                        // Process each item in the sale
                        foreach ($validated['items'] as $item) {
                            $lineGross = 0;
                            $lineTax = 0;
                            $lineDiscount = $item['discount_amount'] ?? 0;
                            $staffId = $item['staff_id'] ?? null;
                            $packageId = $item['package_id'] ?? null;

                            // Apply membership discount if applicable
                            if ($customerMembership && $customerMembership->membership) {
                                $membershipDiscount = $this->calculateMembershipDiscount($item, $customerMembership->membership);
                                $lineDiscount += $membershipDiscount;
                            }

                            $itemBookingIds = $item['booking_ids'] ?? [];
                            $quantityPerBooking = count($itemBookingIds) > 0 ? $item['quantity'] / count($itemBookingIds) : $item['quantity'];

                            if ($item['type'] === 'product') {
                                // Lock the product row for update to prevent race conditions
                                $product = InventoryItem::lockForUpdate()->findOrFail($item['id']);

                                // Validate stock availability
                                if ($product->quantity_in_stock < $item['quantity']) {
                                    throw new \Exception("Insufficient stock for {$product->name}. Available: {$product->quantity_in_stock}");
                                }

                                $unitPrice = $product->selling_price;
                                $lineGross = round($unitPrice * $item['quantity'], 2);
                                $lineNet = round($lineGross - $lineDiscount, 2);
                                // Calculate tax if tax rate is > 0 and POS tax is enabled
                                $lineTax = ($applyTaxPos && $taxRate > 0) ? round($lineNet * $baseTaxMultiplier, 2) : 0;

                                // Create sale item(s) - Split if multiple booking IDs
                                $bookingsToLink = count($itemBookingIds) > 0 ? $itemBookingIds : [null];
                                foreach ($bookingsToLink as $index => $bId) {
                                    $itemQty = count($bookingsToLink) > 1 ? $quantityPerBooking : $item['quantity'];
                                    $itemGross = round($unitPrice * $itemQty, 2);
                                    $itemDiscount = count($bookingsToLink) > 1 ? round($lineDiscount / count($bookingsToLink), 2) : $lineDiscount;
                                    $itemNet = round($itemGross - $itemDiscount, 2);
                                    $itemTax = ($applyTaxPos && $taxRate > 0) ? round($itemNet * $baseTaxMultiplier, 2) : 0;

                                    $saleItem = new PosSaleItem([
                                        'sale_id' => $sale->id,
                                        'item_id' => $product->id,
                                        'item_name' => $product->name,
                                        'item_type' => get_class($product),
                                        'quantity' => $itemQty,
                                        'unit_price' => $unitPrice,
                                        'tax_rate' => $taxRate,
                                        'tax_amount' => $itemTax,
                                        'subtotal' => $itemGross,
                                        'discount_amount' => $itemDiscount,
                                        'total' => $itemNet + $itemTax,
                                        'notes' => $item['notes'] ?? null,
                                        'staff_id' => $staffId,
                                        'package_id' => $packageId,
                                        'booking_id' => $bId,
                                    ]);

                                    $saleItem->save();
                                    $items[] = $saleItem;
                                }

                                // Update inventory
                                $product->decrement('quantity_in_stock', $item['quantity']);

                                $product->refresh();
                                if ($product->isLowStock()) {
                                    $notifyLowStock = $settings->get('notify_low_stock', true, $salonId);
                                    if ($notifyLowStock) {
                                        $lowStockProducts[] = $product;
                                    }
                                }

                                $this->recordInventoryTransaction(
                                    $product->id,
                                    -$item['quantity'],
                                    'sale',
                                    $sale->id,
                                    \App\Models\PosSale::class,
                                    'Sold via POS',
                                    (float) $unitPrice
                                );

                                $grossSubtotal += $lineGross;
                                $totalItemDiscount += $lineDiscount;
                                $totalTax += $lineTax;
                            } elseif ($item['type'] === 'package') {
                                // Handle package items
                                $package = Package::with('services')->findOrFail($item['id']);

                                $unitPrice = $package->special_price ?? $package->price;
                                $lineGross = round($unitPrice * $item['quantity'], 2);
                                $lineNet = round($lineGross - $lineDiscount, 2);
                                // Calculate tax if tax rate is > 0 and POS tax is enabled
                                $lineTax = ($applyTaxPos && $taxRate > 0) ? round($lineNet * $baseTaxMultiplier, 2) : 0;

                                // Create sale item(s) - Split if multiple booking IDs
                                $bookingsToLink = count($itemBookingIds) > 0 ? $itemBookingIds : [null];
                                foreach ($bookingsToLink as $index => $bId) {
                                    $itemQty = count($bookingsToLink) > 1 ? $quantityPerBooking : $item['quantity'];
                                    $itemGross = round($unitPrice * $itemQty, 2);
                                    $itemDiscount = count($bookingsToLink) > 1 ? round($lineDiscount / count($bookingsToLink), 2) : $lineDiscount;
                                    $itemNet = round($itemGross - $itemDiscount, 2);
                                    $itemTax = ($applyTaxPos && $taxRate > 0) ? round($itemNet * $baseTaxMultiplier, 2) : 0;

                                    $saleItem = new PosSaleItem([
                                        'sale_id' => $sale->id,
                                        'item_id' => $package->id,
                                        'item_name' => $package->name,
                                        'item_type' => get_class($package),
                                        'quantity' => $itemQty,
                                        'unit_price' => $unitPrice,
                                        'tax_rate' => $taxRate,
                                        'tax_amount' => $itemTax,
                                        'subtotal' => $itemGross,
                                        'discount_amount' => $itemDiscount,
                                        'total' => $itemNet + $itemTax,
                                        'notes' => $item['notes'] ?? null,
                                        'staff_id' => $staffId,
                                        'package_id' => $package->id,
                                        'booking_id' => $bId,
                                    ]);

                                    $saleItem->save();
                                    $items[] = $saleItem;
                                }

                                $grossSubtotal += $lineGross;
                                $totalItemDiscount += $lineDiscount;
                                $totalTax += $lineTax;
                            } elseif ($item['type'] === 'membership') {
                                // Handle membership purchase
                                $membership = Membership::active()->findOrFail($item['id']);

                                $unitPrice = $membership->calculateTotalMembershipPrice();
                                $lineGross = round($unitPrice * $item['quantity'], 2);
                                $lineNet = round($lineGross - $lineDiscount, 2);
                                $lineTax = ($membership->is_taxable && $applyTaxPos && $taxRate > 0) ? round($lineNet * $baseTaxMultiplier, 2) : 0;

                                // Create sale item(s) - Split if multiple booking IDs
                                $bookingsToLink = count($itemBookingIds) > 0 ? $itemBookingIds : [null];
                                foreach ($bookingsToLink as $index => $bId) {
                                    $itemQty = count($bookingsToLink) > 1 ? $quantityPerBooking : $item['quantity'];
                                    $itemGross = round($unitPrice * $itemQty, 2);
                                    $itemDiscount = count($bookingsToLink) > 1 ? round($lineDiscount / count($bookingsToLink), 2) : $lineDiscount;
                                    $itemNet = round($itemGross - $itemDiscount, 2);
                                    $itemTax = ($membership->is_taxable && $applyTaxPos && $taxRate > 0) ? round($itemNet * $baseTaxMultiplier, 2) : 0;

                                    $saleItem = new PosSaleItem([
                                        'sale_id' => $sale->id,
                                        'item_id' => $membership->id,
                                        'item_name' => $membership->name,
                                        'item_type' => get_class($membership),
                                        'quantity' => $itemQty,
                                        'unit_price' => $unitPrice,
                                        'tax_rate' => $membership->is_taxable ? $taxRate : 0,
                                        'tax_amount' => $itemTax,
                                        'subtotal' => $itemGross,
                                        'discount_amount' => $itemDiscount,
                                        'total' => $itemNet + $itemTax,
                                        'notes' => $item['notes'] ?? null,
                                        'staff_id' => $staffId,
                                        'package_id' => $packageId,
                                        'booking_id' => $bId,
                                    ]);

                                    $saleItem->save();
                                    $items[] = $saleItem;
                                }

                                // Assign membership to customer if customer is selected
                                if ($validated['customer_id']) {
                                    $customer = Customer::where('salon_id', $salonId)->findOrFail($validated['customer_id']);

                                    // Deactivate any existing active memberships for this customer
                                    CustomerMembership::where('customer_id', $customer->id)
                                        ->where('is_active', true)
                                        ->update(['is_active' => false, 'end_date' => now()]);

                                    // Delete any existing record for this specific membership to avoid unique constraint violation
                                    CustomerMembership::where('customer_id', $customer->id)
                                        ->where('membership_id', $membership->id)
                                        ->delete();

                                    // Calculate end date based on membership validity
                                    $startDate = now();
                                    $endDate = $startDate->copy();

                                    if ($membership->validity_unit === 'days') {
                                        $endDate->addDays($membership->validity_value);
                                    } elseif ($membership->validity_unit === 'weeks') {
                                        $endDate->addWeeks($membership->validity_value);
                                    } elseif ($membership->validity_unit === 'months') {
                                        $endDate->addMonths($membership->validity_value);
                                    } elseif ($membership->validity_unit === 'years') {
                                        $endDate->addYears($membership->validity_value);
                                    }

                                    // Create new membership assignment
                                    CustomerMembership::create([
                                        'customer_id' => $customer->id,
                                        'membership_id' => $membership->id,
                                        'start_date' => $startDate,
                                        'end_date' => $endDate,
                                        'is_active' => true,
                                        'assigned_by' => auth()->id(),
                                    ]);
                                }

                                $grossSubtotal += $lineGross;
                                $totalItemDiscount += $lineDiscount;
                                $totalTax += $lineTax;
                            } else {
                                // Handle service items
                                $service = Service::findOrFail($item['id']);

                                // If this service is part of a package, use price 0 for individual service items
                                $unitPrice = $packageId ? 0 : $service->price;
                                $lineGross = round($unitPrice * $item['quantity'], 2);
                                $lineNet = round($lineGross - $lineDiscount, 2);
                                // Calculate tax if tax rate is > 0 and services tax is enabled
                                $lineTax = ($applyTaxServices && $taxRate > 0) ? round($lineNet * $baseTaxMultiplier, 2) : 0;

                                // Create sale item(s) - Split if multiple booking IDs
                                $bookingsToLink = count($itemBookingIds) > 0 ? $itemBookingIds : [null];
                                foreach ($bookingsToLink as $index => $bId) {
                                    $itemQty = count($bookingsToLink) > 1 ? $quantityPerBooking : $item['quantity'];
                                    $itemGross = round($unitPrice * $itemQty, 2);
                                    $itemDiscount = count($bookingsToLink) > 1 ? round($lineDiscount / count($bookingsToLink), 2) : $lineDiscount;
                                    $itemNet = round($itemGross - $itemDiscount, 2);
                                    $itemTax = ($applyTaxServices && $taxRate > 0) ? round($itemNet * $baseTaxMultiplier, 2) : 0;

                                    $saleItem = new PosSaleItem([
                                        'sale_id' => $sale->id,
                                        'item_id' => $service->id,
                                        'item_name' => $service->name,
                                        'item_type' => get_class($service),
                                        'quantity' => $itemQty,
                                        'unit_price' => $unitPrice,
                                        'tax_rate' => $taxRate,
                                        'tax_amount' => $itemTax,
                                        'subtotal' => $itemGross,
                                        'discount_amount' => $itemDiscount,
                                        'total' => $itemNet + $itemTax,
                                        'notes' => $item['notes'] ?? null,
                                        'staff_id' => $staffId,
                                        'package_id' => $packageId,
                                        'booking_id' => $bId,
                                    ]);

                                    $saleItem->save();
                                    $items[] = $saleItem;
                                }

                                $grossSubtotal += $lineGross;
                                $totalItemDiscount += $lineDiscount;
                                $totalTax += $lineTax;
                            }
                        }

                        // Calculate overall discount
                        $overallDiscount = 0;
                        $netBeforeGlobal = $grossSubtotal - $totalItemDiscount;

                        if (!empty($validated['discount_amount'])) {
                            if (($validated['discount_type'] ?? 'fixed') === 'percent') {
                                $overallDiscount = ($netBeforeGlobal * $validated['discount_amount']) / 100;
                            } else {
                                $overallDiscount = min($validated['discount_amount'], $netBeforeGlobal);
                            }
                        }

                        // Adjust tax if global discount exists
                        if ($overallDiscount > 0 && $netBeforeGlobal > 0) {
                            $ratio = ($netBeforeGlobal - $overallDiscount) / $netBeforeGlobal;
                            $totalTax = round($totalTax * $ratio, 2);
                        }

                        $tip = round($validated['tip'] ?? 0, 2);
                        $paymentMethod = $validated['payment_method'] ?? null;



                        $newCash = round($validated['cash_amount'] ?? 0, 2);
                        $newCard = round($validated['card_amount'] ?? 0, 2);
                        $newOnline = round($validated['online_amount'] ?? 0, 2);
                        $newOther = round($validated['other_amount'] ?? 0, 2);


                        $prevTotal = array_sum($previousPayments);
                        $billTotal = round($grossSubtotal - $totalItemDiscount + $totalTax + $tip, 2);

                        if ($prevTotal >= $billTotal && ($newCash + $newCard + $newOnline + $newOther) > 0) {
                            \Log::info("POS Idempotency: Bill already paid. Ignoring redundant payment amounts.", [
                                'prev_total' => $prevTotal,
                                'bill_total' => $billTotal,
                                'new_cash' => $newCash
                            ]);
                            $cash = $previousPayments['cash'];
                            $card = $previousPayments['card'];
                            $online = $previousPayments['online'];
                            $other = $previousPayments['other'];
                        } else {
                            $cash = round($newCash + $previousPayments['cash'], 2);
                            $card = round($newCard + $previousPayments['card'], 2);
                            $online = round($newOnline + $previousPayments['online'], 2);
                            $other = round($newOther + $previousPayments['other'], 2);
                        }

                        \Illuminate\Support\Facades\Log::info('POS Store Debug: Final accumulated payments', [
                            'attempt' => $attempt,
                            'new_cash' => $newCash,
                            'prev_cash' => $previousPayments['cash'],
                            'final_cash' => $cash,
                            'final_total_paid' => $cash + $card + $online + $other
                        ]);

                        // Final totals
                        $totalDiscount = round($totalItemDiscount + $overallDiscount, 2);
                        $grand = round(($grossSubtotal - $totalDiscount) + $totalTax, 2);
                        $payable = round($grand + $tip, 2);

                        // For cash/mixed payments, if tendered_amount is provided, calculate actual cash paid
                        if (in_array($paymentMethod, ['cash', 'mixed']) && isset($validated['tendered_amount']) && $validated['tendered_amount'] > 0) {
                            $tenderedAmount = round($validated['tendered_amount'], 2);
                            $changeAmount = round($validated['change_amount'] ?? 0, 2);
                            // Actual cash paid = tendered - change
                            $currentCashTx = round($tenderedAmount - $changeAmount, 2);

                            // Only add if not already redundant
                            if ($prevTotal < $billTotal || $currentCashTx > $newCash) {
                                $cash = round($currentCashTx + $previousPayments['cash'], 2);
                            }
                        }

                        $paid = round($cash + $card + $online + $other, 2);

                        // Prevent overpayment
                        if ($paid > $payable + 0.10) { // Slightly larger buffer for rounding
                            throw new \Exception("Payment amount (" . number_format($paid, 2) . ") cannot be more than the bill amount (" . number_format($payable, 2) . "). Check if payment was already recorded.");
                        }

                        $outstanding = round($payable - $paid, 2);


                        // Determine payment method early (needed for discount inference logging)

                        // FALLBACK: Inferred discount logic disabled to allow for outstanding balances (partial payments)
                        /*
                        if (empty($validated['discount_amount']) && $outstanding > 0 && $outstanding < $payable) {
                            // Check if this looks like an intentional discount (not just underpayment)
                            // Criteria: Cash payment with proper change calculation OR card/online payment
                            $looksLikeDiscount = false;

                            if ($cash > 0 && isset($validated['tendered_amount']) && $validated['change_amount'] > 0) {
                                // Cash payment with change - likely intentional discount
                                $looksLikeDiscount = true;
                            } elseif ($card > 0 || $online > 0 || $other > 0) {
                                // Card/online/other payments are typically exact - if short, likely discount
                                $looksLikeDiscount = true;
                            }

                            if ($looksLikeDiscount) {
                                $inferredDiscount = $outstanding;
                                \Log::info('Inferred cart discount from payment shortfall', [
                                    'subtotal' => $grossSubtotal,
                                    'payable_before' => $payable,
                                    'paid' => $paid,
                                    'inferred_discount' => $inferredDiscount,
                                    'payment_method' => $paymentMethod
                                ]);

                                // Apply the inferred discount
                                $totalDiscount = round($totalDiscount + $inferredDiscount, 2);
                                $payable = round($payable - $inferredDiscount, 2);
                                $outstanding = round($payable - $paid, 2);
                            }
                        }
                        */

                        // Payment method and status determination

                        // If payment method is online/other and no amount was sent, assume full payment
                        if ($paid <= 0 && $payable > 0) {
                            if ($paymentMethod === 'online') {
                                $online = $payable;
                                $paid = $payable;
                            } elseif ($paymentMethod === 'other') {
                                $other = $payable;
                                $paid = $payable;
                            }
                            $outstanding = round($payable - $paid, 2);
                        }

                        if ($paid > 0) {
                            // Payment entered
                            \Illuminate\Support\Facades\Log::info('POS Payment Data:', [
                                'method' => $paymentMethod,
                                'tendered' => $validated['tendered_amount'] ?? 'null',
                                'change' => $validated['change_amount'] ?? 'null',
                                'cash_amount' => $cash,
                                'gross_subtotal' => $grossSubtotal,
                                'total_discount' => $totalDiscount,
                                'total_tax' => $totalTax,
                                'tip' => $tip,
                                'grand_total' => $grand,
                                'payable' => $payable,
                                'paid' => $paid,
                                'outstanding' => $outstanding
                            ]);

                            if (!$paymentMethod || $paymentMethod === 'mixed') {
                                $methodsUsed = 0;
                                if ($cash > 0)
                                    $methodsUsed++;
                                if ($card > 0)
                                    $methodsUsed++;
                                if ($online > 0)
                                    $methodsUsed++;
                                if ($other > 0)
                                    $methodsUsed++;
                                if ($methodsUsed > 1) {
                                    $paymentMethod = 'mixed';
                                } elseif ($cash > 0) {
                                    $paymentMethod = 'cash';
                                } elseif ($card > 0) {
                                    $paymentMethod = 'card';
                                } elseif ($online > 0) {
                                    $paymentMethod = 'online';
                                } elseif ($other > 0) {
                                    $paymentMethod = 'other';
                                }
                            }
                            // Fix: Use absolute value comparison with tolerance for rounding errors (increased to 0.10 for split payments)
                            if (abs($outstanding) <= 0.10) {
                                $outstanding = 0;
                                $paymentStatus = 'paid';
                            } else {
                                $paymentStatus = 'partial';
                            }
                        } else {
                            // No payment entered
                            if ($payable > 0) {
                                $paymentStatus = 'pending';
                                if (!$paymentMethod) {
                                    $paymentMethod = 'other';
                                }
                            } else {
                                $paymentStatus = 'paid';
                                if (!$paymentMethod) {
                                    $paymentMethod = 'other';
                                }
                            }
                        }

                        // Update sale with final amounts
                        // Ensure outstanding is exactly 0 if payment is complete
                        // Handle rounding errors - if outstanding is within 10 cents or if paid >= payable, mark as fully paid
                        if (abs($outstanding) <= 0.10 || $paid >= $payable) {
                            $outstanding = 0;
                        }

                        $sale->update([
                            'subtotal' => $grossSubtotal,
                            'tax' => $totalTax,
                            'discount' => $totalDiscount,
                            'tip' => $tip,
                            'cash_amount' => $cash,
                            'card_amount' => $card,
                            'online_amount' => $online,
                            'other_amount' => $other,
                            'outstanding_amount' => $outstanding,
                            'total' => $payable,
                            'payment_method' => $paymentMethod,
                            'payment_status' => $paymentStatus,
                            'tendered_amount' => $validated['tendered_amount'] ?? 0,
                            'change_amount' => $validated['change_amount'] ?? 0,
                        ]);

                        // Update booking statuses to completed if booking_ids provided and payment is complete
                        // OR if customer has staff_completed bookings from today
                        if ($paymentStatus === 'paid') {
                            $bookingIdsToUpdate = [];

                            // If booking_ids explicitly provided, use those
                            if (!empty($validated['booking_ids'])) {
                                $bookingIdsToUpdate = $validated['booking_ids'];
                            }
                            // Otherwise, find all staff_completed bookings for this customer from today
                            elseif (!empty($validated['customer_id'])) {
                                $bookingIdsToUpdate = \App\Models\Booking::where('salon_id', $salonId)
                                    ->where('customer_id', $validated['customer_id'])
                                    ->whereDate('start_time', now()->toDateString())
                                    ->where('status', 'staff_completed')
                                    ->pluck('id')
                                    ->toArray();
                            }

                            // Unified Package Balance Logic: Handle both new purchases and consumption
                            if (!empty($validated['customer_id'])) {
                                $packageBalanceIncrements = []; // [package_id][service_id] => qty
                                $packageBalanceDecrements = []; // [package_id][service_id] => qty
                                $priorPackageBalances = []; // Package IDs that had balance BEFORE this transaction

                                // Identify packages being purchased in this transaction
                                $purchasedPackageIds = collect($validated['items'])
                                    ->where('type', 'package')
                                    ->pluck('id')
                                    ->unique()
                                    ->toArray();

                                if (!empty($purchasedPackageIds)) {
                                    // Check if customer ALREADY has balance > 0 for these packages
                                    // Fetch QUANTITIES to track consumption across multiple items
                                    $priorBalanceRows = CustomerPackageBalance::where('customer_id', $validated['customer_id'])
                                        ->whereIn('package_id', $purchasedPackageIds)
                                        ->where('quantity_remaining', '>', 0)
                                        ->where('salon_id', $salonId)
                                        ->select('package_id', 'service_id', 'quantity_remaining')
                                        ->get();

                                    foreach ($priorBalanceRows as $row) {
                                        if (!isset($priorPackageBalances[$row->package_id][$row->service_id])) {
                                            $priorPackageBalances[$row->package_id][$row->service_id] = 0;
                                        }
                                        $priorPackageBalances[$row->package_id][$row->service_id] += $row->quantity_remaining;
                                    }
                                }

                                // 1. First Pass: Identify Package Purchases (Increments)
                                foreach ($validated['items'] as $item) {
                                    if ($item['type'] === 'package') {
                                        $package = Package::with('services')->find($item['id']);
                                        if ($package) {
                                            // Check if this is a customizable package with service selection
                                            if (isset($item['services']) && is_array($item['services'])) {
                                                // CUSTOMIZABLE PACKAGE: User selected which services to use now vs save
                                                \Log::info('Processing customizable package', [
                                                    'package_id' => $package->id,
                                                    'services' => $item['services']
                                                ]);

                                                // Validate service limit
                                                if ($package->service_limit && count($item['services']) > $package->service_limit) {
                                                    throw new \Exception("Cannot select more than {$package->service_limit} services from package {$package->name}");
                                                }

                                                foreach ($item['services'] as $svcData) {
                                                    $serviceId = $svcData['service_id'] ?? null;
                                                    $saveForFuture = intval($svcData['save_future'] ?? 0);

                                                    if (!$serviceId)
                                                        continue;

                                                    // Only add to balance if saving for future
                                                    if ($saveForFuture > 0) {
                                                        $packageBalanceIncrements[$package->id][$serviceId] =
                                                            ($packageBalanceIncrements[$package->id][$serviceId] ?? 0) + $saveForFuture;

                                                        \Log::info('Saving service for future', [
                                                            'service_id' => $serviceId,
                                                            'quantity' => $saveForFuture
                                                        ]);
                                                    }
                                                }
                                            } else {
                                                // Check if this should be a customizable package
                                                // Skip validation if package comes from a booking (services already selected)
                                                $isFromBooking = !empty($item['booking_id']) || !empty($item['booking_ids']);
                                                $hasSelectedServices = isset($item['selected_services']) && is_array($item['selected_services']);

                                                if ($package->service_limit && $package->service_limit > 0 && !$isFromBooking && !$hasSelectedServices) {
                                                    throw new \Exception("Package {$package->name} requires service selection. Please select which services to use.");
                                                }

                                                // Check if we have selected_services from booking
                                                if ($hasSelectedServices) {
                                                    // CUSTOMIZABLE PACKAGE FROM BOOKING: Use selected services
                                                    \Log::info('Processing customizable package from booking with selected services', [
                                                        'package_id' => $package->id,
                                                        'selected_services' => $item['selected_services']
                                                    ]);

                                                    foreach ($item['selected_services'] as $svcData) {
                                                        $serviceId = $svcData['service_id'] ?? null;
                                                        $saveForFuture = intval($svcData['save_future'] ?? 0);

                                                        if (!$serviceId)
                                                            continue;

                                                        // Only add to balance if saving for future
                                                        if ($saveForFuture > 0) {
                                                            $packageBalanceIncrements[$package->id][$serviceId] =
                                                                ($packageBalanceIncrements[$package->id][$serviceId] ?? 0) + $saveForFuture;

                                                            \Log::info('Adding selected service to balance', [
                                                                'service_id' => $serviceId,
                                                                'quantity' => $saveForFuture
                                                            ]);
                                                        }
                                                    }
                                                } else {
                                                    // STANDARD PACKAGE or LEGACY BOOKING: Add all services to balance
                                                    \Log::info('Processing standard package or legacy booking', [
                                                        'package_id' => $package->id,
                                                        'from_booking' => $isFromBooking
                                                    ]);

                                                    foreach ($package->services as $service) {
                                                        $qtyInPackage = $service->pivot->quantity ?? 1;
                                                        $totalQty = $qtyInPackage * $item['quantity'];
                                                        $packageBalanceIncrements[$package->id][$service->id] =
                                                            ($packageBalanceIncrements[$package->id][$service->id] ?? 0) + $totalQty;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                                // 2. Second Pass: Identify Service Consumption (Decrements)
                                foreach ($validated['items'] as $item) {
                                    if ($item['type'] === 'service' && !empty($item['package_id']) && floatval($item['price'] ?? 0) == 0) {
                                        // CONSUMPTION: Decrement Balance
                                        $packageId = $item['package_id'];
                                        $serviceId = $item['id'];
                                        $consumeQty = $item['quantity'];

                                        // Check if this was already decremented by a booking (to avoid double decrement)
                                        $alreadyDecremented = false;
                                        $isNewPurchase = isset($packageBalanceIncrements[$packageId]);

                                        // Check if customer had prior balance for this SPECIFIC service
                                        // Use running balance to handle multiple items consuming same service
                                        $availablePrior = $priorPackageBalances[$packageId][$serviceId] ?? 0;
                                        $hadPriorBalance = $availablePrior >= $consumeQty;

                                        // Update running balance if we assume booking handled it
                                        if ($hadPriorBalance) {
                                            $priorPackageBalances[$packageId][$serviceId] -= $consumeQty;
                                        }

                                        // Check single booking_id
                                        if (!empty($item['booking_id'])) {
                                            $booking = \App\Models\Booking::find($item['booking_id']);
                                            if ($booking && $booking->package_id == $packageId) {
                                                if ($isNewPurchase) {
                                                    if (!$hadPriorBalance) {
                                                        // New Purchase AND Insufficient Prior Balance:
                                                        // BookingController failed to decrement full amount.
                                                        // So we MUST decrement here.
                                                        $alreadyDecremented = false;
                                                        \Log::info('Force decrement: New purchase with insufficient prior balance', [
                                                            'package_id' => $packageId,
                                                            'service_id' => $serviceId,
                                                            'prior' => $availablePrior,
                                                            'consume' => $consumeQty
                                                        ]);
                                                    } else {
                                                        // Prior balance existed, normal check
                                                        $wasDecremented = CustomerPackageBalance::where('customer_id', $booking->customer_id)
                                                            ->where('package_id', $packageId)
                                                            ->where('service_id', $serviceId)
                                                            ->where('updated_at', '>=', $booking->created_at->copy()->subSeconds(5))
                                                            ->exists();

                                                        if ($wasDecremented) {
                                                            $alreadyDecremented = true;
                                                        } else {
                                                            // Even if had prior, if not decremented, force it (Booking failed for other reasons?)
                                                            // BUT be careful of timestamp logic. Assuming timestamp logic holds for prior.
                                                            // If Booking failed, wasDecremented is false. So we decrement. Correct.
                                                            $alreadyDecremented = false;
                                                        }
                                                    }
                                                } else {
                                                    // If not buying new, assume standard redemption consumed the balance
                                                    // Or better, check wasDecremented to be safe?
                                                    // Existing logic assumed true. Let's keep it but ideally we should verify.
                                                    // For now, keep existing behavior for non-new purchase to avoid regression.
                                                    $alreadyDecremented = true;
                                                }
                                            }
                                        }
                                        // Check array of booking_ids
                                        elseif (!empty($item['booking_ids']) && is_array($item['booking_ids'])) {
                                            foreach ($item['booking_ids'] as $bId) {
                                                $booking = \App\Models\Booking::find($bId);
                                                if ($booking && $booking->package_id == $packageId) {
                                                    if ($isNewPurchase) {
                                                        if (!$hadPriorBalance) {
                                                            // New Purchase AND Insufficient Prior Balance: Force decrement
                                                            $alreadyDecremented = false;
                                                            \Log::info('Force decrement (multi): New purchase with insufficient prior balance', ['package_id' => $packageId]);
                                                        } else {
                                                            // Prior balance existed, normal check
                                                            $wasDecremented = CustomerPackageBalance::where('customer_id', $booking->customer_id)
                                                                ->where('package_id', $packageId)
                                                                ->where('service_id', $serviceId)
                                                                ->where('updated_at', '>=', $booking->created_at->copy()->subSeconds(5))
                                                                ->exists();

                                                            if ($wasDecremented) {
                                                                $alreadyDecremented = true;
                                                                break;
                                                            }
                                                        }
                                                    } else {
                                                        $alreadyDecremented = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        }

                                        if (!$alreadyDecremented) {
                                            $packageBalanceDecrements[$packageId][$serviceId] = ($packageBalanceDecrements[$packageId][$serviceId] ?? 0) + $consumeQty;
                                        }
                                    }
                                }

                                // 1. Process Increments (Purchases) - DO THIS FIRST so balance is available for consumption
                                foreach ($packageBalanceIncrements as $packageId => $services) {
                                    \Log::info('Processing increments for package', ['package_id' => $packageId, 'services' => $services]);
                                    foreach ($services as $serviceId => $qtyToAdd) {
                                        if ($qtyToAdd <= 0)
                                            continue;

                                        // Calculate expiry date for the package
                                        $expiryDate = null;
                                        $package = Package::find($packageId);
                                        if ($package && $package->validity_value && $package->validity_unit) {
                                            $expiryDate = now();
                                            switch ($package->validity_unit) {
                                                case 'days':
                                                    $expiryDate->addDays($package->validity_value);
                                                    break;
                                                case 'weeks':
                                                    $expiryDate->addWeeks($package->validity_value);
                                                    break;
                                                case 'months':
                                                    $expiryDate->addMonths($package->validity_value);
                                                    break;
                                                case 'years':
                                                    $expiryDate->addYears($package->validity_value);
                                                    break;
                                            }
                                        }

                                        // ALWAYS create new balance record for new purchases
                                        // Linked to the specific sale via pos_sale_id
                                        $balance = \App\Models\CustomerPackageBalance::create([
                                            'salon_id' => $salonId,
                                            'customer_id' => $validated['customer_id'],
                                            'pos_sale_id' => $sale->id, // Link to this specific sale
                                            'package_id' => $packageId,
                                            'service_id' => $serviceId,
                                            'quantity_remaining' => $qtyToAdd,
                                            'payment_status' => $paymentStatus, // Use actual sale status
                                            'expiry_date' => $expiryDate
                                        ]);

                                        \Log::info('Created new package balance', [
                                            'balance_id' => $balance->id,
                                            'sale_id' => $sale->id,
                                            'quantity' => $qtyToAdd
                                        ]);

                                        // Explicitly refresh to ensure latest state
                                        $balance->refresh();
                                        \Log::info('Incremented balance', ['id' => $balance->id, 'new_qty' => $balance->quantity_remaining, 'expiry' => $balance->expiry_date]);
                                    }
                                }

                                // 2. Process Decrements (Consumption) - FIFO Logic
                                foreach ($packageBalanceDecrements as $packageId => $services) {
                                    foreach ($services as $serviceId => $qtyToConsume) {
                                        if ($qtyToConsume <= 0)
                                            continue;

                                        // Fetch all valid balance rows for this package/service
                                        $balances = CustomerPackageBalance::where('salon_id', $salonId)
                                            ->where('customer_id', $validated['customer_id'])
                                            ->where('package_id', $packageId)
                                            ->where('service_id', $serviceId)
                                            ->where('quantity_remaining', '>', 0)
                                            ->where(function ($query) {
                                                $query->whereNull('expiry_date')
                                                    ->orWhere('expiry_date', '>=', now()->toDateString());
                                            })
                                            ->orderBy('created_at', 'asc') // FIFO: Consume oldest first
                                            ->get();

                                        \Log::info('Balances found for decrement', ['count' => $balances->count(), 'total' => $balances->sum('quantity_remaining'), 'rows' => $balances->toArray()]);

                                        $totalAvailable = $balances->sum('quantity_remaining');

                                        $totalAvailable = $balances->sum('quantity_remaining');

                                        if ($totalAvailable < $qtyToConsume) {
                                            $serviceName = Service::find($serviceId)->name ?? 'Service';
                                            // DEBUG: Get all balances for this package/service to see what's there
                                            $allBalances = CustomerPackageBalance::where('salon_id', $salonId)
                                                ->where('customer_id', $validated['customer_id'])
                                                ->where('package_id', $packageId)
                                                ->where('service_id', $serviceId)
                                                ->get();

                                            $debugInfo = $allBalances->map(function ($b) {
                                                return "ID:{$b->id}, Qty:{$b->quantity_remaining}, Exp:{$b->expiry_date}";
                                            })->implode(' | ');

                                            throw new \Exception("Insufficient package balance for {$serviceName}. Required: {$qtyToConsume}, Available: {$totalAvailable}. Debug: {$debugInfo}");
                                        }

                                        $remainingToConsume = $qtyToConsume;

                                        foreach ($balances as $balance) {
                                            if ($remainingToConsume <= 0)
                                                break;

                                            $availableInRow = $balance->quantity_remaining;
                                            $consumeFromRow = min($availableInRow, $remainingToConsume);

                                            $balance->decrement('quantity_remaining', $consumeFromRow);
                                            $remainingToConsume -= $consumeFromRow;

                                            \Log::info('Package balance decremented', [
                                                'balance_id' => $balance->id,
                                                'consumed' => $consumeFromRow,
                                                'remaining_in_row' => $balance->quantity_remaining
                                            ]);
                                        }
                                    }
                                }
                            }

                            if (!empty($bookingIdsToUpdate)) {
                                // Get all booking_group_ids from the bookings to update
                                $bookingGroupIds = \App\Models\Booking::whereIn('id', $bookingIdsToUpdate)
                                    ->where('salon_id', $salonId)
                                    ->whereNotNull('booking_group_id')
                                    ->pluck('booking_group_id')
                                    ->unique();

                                // Collect all bookings to update (individual + group members)
                                $query = \App\Models\Booking::where('salon_id', $salonId)
                                    ->whereIn('status', ['staff_completed', 'confirmed', 'pending', 'arrived', 'started']);

                                if ($bookingGroupIds->isNotEmpty()) {
                                    $query->where(function ($q) use ($bookingIdsToUpdate, $bookingGroupIds) {
                                        $q->whereIn('id', $bookingIdsToUpdate)
                                            ->orWhereIn('booking_group_id', $bookingGroupIds);
                                    });
                                } else {
                                    $query->whereIn('id', $bookingIdsToUpdate);
                                }

                                $bookingsToUpdate = $query->get();
                                $updatedCount = 0;

                                // Calculate tip per booking if multiple bookings exist
                                $tipPerBooking = 0;
                                if ($tip > 0 && $bookingsToUpdate->count() > 0) {
                                    $tipPerBooking = round($tip / $bookingsToUpdate->count(), 2);
                                }

                                // Track which bookings are already linked to a PosSaleItem
                                $linkedBookingIds = collect($items)->pluck('booking_id')->filter()->unique()->toArray();

                                foreach ($bookingsToUpdate as $index => $booking) {
                                    try {
                                        // If pending, move to confirmed first to satisfy completeWithPayment requirements
                                        if ($booking->status === 'pending') {
                                            $booking->status = 'confirmed';
                                        }

                                        // Adjust last booking's tip to handle rounding differences
                                        $currentBookingTip = $tipPerBooking;
                                        if ($index === $bookingsToUpdate->count() - 1) {
                                            $currentBookingTip = round($tip - ($tipPerBooking * ($bookingsToUpdate->count() - 1)), 2);
                                        }

                                        // Use completeWithPayment to trigger rating tokens, emails, etc.
                                        $salonTimezone = salon_timezone();
                                        $isFutureBooking = $booking->start_time->isFuture() && !$booking->start_time->copy()->setTimezone($salonTimezone)->isToday();

                                        if ($isFutureBooking) {
                                            // For future bookings, only mark as paid but keep status as confirmed (or whatever it was, unless pending)
                                            if ($booking->status === 'pending') {
                                                $booking->status = 'confirmed';
                                            }

                                            // Mark as paid
                                            $booking->markAsPaid($paymentMethod, $currentBookingTip);

                                            // Manually increment customer total spent since completeWithPayment is not used
                                            if ($booking->customer && !$booking->package_id) {
                                                // If it's a package booking, amount is covered by package. Only increment if NOT package booking.
                                                // But wait, markAsPaid doesn't handle this logic.
                                                // We need to mirror completeWithPayment's logic for total_spent
                                                $amountToIncrement = ($booking->amount ?? 0) + ($currentBookingTip ?? 0);
                                                $booking->customer->increment('total_spent', $amountToIncrement);
                                                $booking->customer->update(['last_visit_at' => now()]);
                                            } elseif ($booking->customer && $currentBookingTip > 0) {
                                                $booking->customer->increment('total_spent', $currentBookingTip);
                                            }

                                            $updatedCount++;
                                            \Log::info("Future booking {$booking->id} marked as PAID but not completed via POS");
                                        } else {
                                            // Today or past: Complete normally
                                            if ($booking->completeWithPayment($paymentMethod, $currentBookingTip, false)) {
                                                $updatedCount++;
                                            }
                                        }

                                        // AUTO-LINKING: If this booking is not yet linked to any PosSaleItem, try to link it now
                                        if (!in_array($booking->id, $linkedBookingIds)) {
                                            // Try to find an unlinked PosSaleItem that matches this booking's service
                                            $matchingItem = collect($items)->first(function ($si) use ($booking) {
                                                return $si->item_type === \App\Models\Service::class && $si->item_id === $booking->service_id && empty($si->booking_id);
                                            });

                                            if ($matchingItem) {
                                                $matchingItem->update(['booking_id' => $booking->id]);
                                                $linkedBookingIds[] = $booking->id;
                                            } else {
                                                // If no exact service match, try to link to ANY unlinked item (e.g. package or other service)
                                                $anyUnlinkedItem = collect($items)->first(function ($si) {
                                                    return empty($si->booking_id);
                                                });
                                                if ($anyUnlinkedItem) {
                                                    $anyUnlinkedItem->update(['booking_id' => $booking->id]);
                                                    $linkedBookingIds[] = $booking->id;
                                                }
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        \Log::error("Failed to complete booking {$booking->id} via POS: " . $e->getMessage());

                                        // Fallback to direct update if completeWithPayment fails
                                        $booking->update([
                                            'status' => 'completed',
                                            'payment_status' => $paymentStatus ?? 'paid',
                                            'payment_method' => $paymentMethod,
                                            'paid_at' => ($paymentStatus ?? 'paid') === 'paid' ? now() : null,
                                            'tip_amount' => $currentBookingTip ?? 0,
                                            'updated_at' => now()
                                        ]);
                                        $updatedCount++;
                                    }
                                }

                                \Log::info("Updated booking statuses to completed for POS sale", [
                                    'sale_id' => $sale->id,
                                    'invoice_number' => $sale->invoice_number,
                                    'customer_id' => $validated['customer_id'] ?? null,
                                    'booking_ids' => $bookingIdsToUpdate,
                                    'updated_count' => $updatedCount,
                                    'booking_group_ids' => $bookingGroupIds->toArray()
                                ]);
                            }
                        } else {
                            // Logic for Unpaid / Partial / Pending Sales
                            // We need to ensure linked bookings reflect this status (e.g., revert from 'completed' to 'staff_completed')

                            $bookingIdsToUpdate = [];

                            // If booking_ids explicitly provided, use those
                            if (!empty($validated['booking_ids'])) {
                                $bookingIdsToUpdate = $validated['booking_ids'];
                            }
                            // Otherwise, find bookings linked to this sale (via newly created items OR old recovered items)
                            elseif ($sale->exists) {
                                $bookingIdsToUpdate = \App\Models\PosSaleItem::where('sale_id', $sale->id)
                                    ->whereNotNull('booking_id')
                                    ->pluck('booking_id')
                                    ->toArray();

                                // Merge with recovered IDs if we didn't find them in new items (lost link scenario)
                                if (empty($bookingIdsToUpdate) && !empty($existingBookingIds)) {
                                    $bookingIdsToUpdate = $existingBookingIds;
                                    \Log::info('Recovered lost booking links from pre-delete state', ['count' => count($bookingIdsToUpdate)]);
                                }

                                // Also check staff_completed status for this customer today if no direct links found yet
                                if (empty($bookingIdsToUpdate) && !empty($validated['customer_id'])) {
                                    $bookingIdsToUpdate = \App\Models\Booking::where('salon_id', $salonId)
                                        ->where('customer_id', $validated['customer_id'])
                                        ->whereDate('start_time', now()->toDateString())
                                        ->whereIn('status', ['staff_completed', 'completed'])
                                        ->pluck('id')
                                        ->toArray();
                                }
                            }

                            if (!empty($bookingIdsToUpdate)) {
                                // Get all booking_group_ids
                                $bookingGroupIds = \App\Models\Booking::whereIn('id', $bookingIdsToUpdate)
                                    ->where('salon_id', $salonId)
                                    ->whereNotNull('booking_group_id')
                                    ->pluck('booking_group_id')
                                    ->unique();

                                // Collect all bookings
                                $query = \App\Models\Booking::where('salon_id', $salonId);

                                if ($bookingGroupIds->isNotEmpty()) {
                                    $query->where(function ($q) use ($bookingIdsToUpdate, $bookingGroupIds) {
                                        $q->whereIn('id', $bookingIdsToUpdate)
                                            ->orWhereIn('booking_group_id', $bookingGroupIds);
                                    });
                                } else {
                                    $query->whereIn('id', $bookingIdsToUpdate);
                                }

                                $bookingsToUpdate = $query->get();
                                $updatedCount = 0;

                                foreach ($bookingsToUpdate as $booking) {
                                    $updates = [
                                        'payment_status' => $paymentStatus, // pending, partial, etc.
                                        'payment_method' => $paymentMethod,
                                        'updated_at' => now()
                                    ];

                                    // If status was 'completed' (implies paid), revert to 'staff_completed'
                                    // This keeps the appointment "done" but marks it as needing payment attention
                                    if ($booking->status === 'completed') {
                                        $updates['status'] = 'staff_completed';

                                        // Also clear paid_at if it's no longer paid
                                        if ($paymentStatus !== 'paid') {
                                            $updates['paid_at'] = null;
                                        }
                                    } elseif ($booking->status === 'confirmed' && $paymentStatus !== 'pending') {
                                        // unchanged
                                    }

                                    $booking->update($updates);
                                    $updatedCount++;
                                }

                                \Log::info("Reverted/Updated booking statuses for Unpaid/Partial POS sale", [
                                    'sale_id' => $sale->id,
                                    'new_payment_status' => $paymentStatus,
                                    'updated_count' => $updatedCount
                                ]);
                            }
                        }

                        // Update customer's total spent and last purchase date if customer exists
                        if ($sale->customer) {
                            $sale->customer->increment('total_spent', $paid);
                            $sale->customer->update(['last_visit_at' => now()]);
                        }

                        // Create commission records for tips distributed among staff
                        if ($tip > 0) {
                            // Collect all staff members who worked on items in this sale
                            $staffContributions = [];
                            foreach ($items as $saleItem) {
                                if ($saleItem->staff_id) {
                                    if (!isset($staffContributions[$saleItem->staff_id])) {
                                        $staffContributions[$saleItem->staff_id] = 0;
                                    }
                                    // Track contribution (could be based on item total, but using count for simplicity)
                                    $staffContributions[$saleItem->staff_id] += 1;
                                }
                            }

                            // Distribute tip among staff members proportionally
                            if (!empty($staffContributions)) {
                                $totalContributions = array_sum($staffContributions);
                                foreach ($staffContributions as $staffId => $contribution) {
                                    $staffTipAmount = ($contribution / $totalContributions) * $tip;
                                    \App\Models\StaffCommission::create([
                                        'salon_id' => $salonId,
                                        'staff_id' => $staffId,
                                        'pos_sale_id' => $sale->id,
                                        'item_type' => 'tip',
                                        'item_id' => null,
                                        'sale_amount' => $staffTipAmount,
                                        'commission_amount' => $staffTipAmount, // Tips go 100% to staff
                                        'commission_profile_id' => null,
                                        'status' => 'approved',
                                    ]);
                                }
                            }
                        }

                        // Log the successful sale
                        \Log::info("POS Sale #{$sale->invoice_number} completed successfully", [
                            'sale_id' => $sale->id,
                            'amount' => $payable,
                            'items_count' => count($items),
                            'user_id' => auth()->id(),
                        ]);


                        // Send Receipt Email if customer has email
                        if ($sale->customer && $sale->customer->email) {
                            try {
                                $salon = Salon::find($salonId);
                                $salonData = [
                                    'name' => $salon->name,
                                    'slug' => $salon->slug,
                                    'address' => $salon->address,
                                    'phone' => $salon->phone,
                                    'email' => $salon->email,
                                    'website' => $salon->website,
                                    'logo' => $salon->logo ? asset('storage/' . $salon->logo) : null,
                                ];

                                $settings = app(\App\Services\SettingsService::class);

                                Mail::to($sale->customer->email)->send(new PosSaleReceiptMail($sale, $salonData, $settings));
                                \Log::info('Receipt email sent to ' . $sale->customer->email);
                            } catch (\Exception $e) {
                                \Log::error('Failed to send receipt email: ' . $e->getMessage());
                            }
                        }

                        // Send WhatsApp Receipt
                        if ($sale->customer && $sale->customer->phone) {
                            try {
                                app(\App\Services\NotificationService::class)->sendSaleReceiptWhatsapp($sale);
                            } catch (\Exception $e) {
                                \Log::error('Failed to send WhatsApp receipt: ' . $e->getMessage());
                            }
                        }

                        // Return success response with sale details
                        return response()->json([
                            'success' => true,
                            'message' => 'Sale completed successfully',
                            'sale' => $sale->load(['items.item', 'customer', 'employee']),
                            'receipt_url' => route('admin.pos.receipt', $sale->id),
                        ]);

                    } catch (\Exception $e) {
                        // Log the error
                        \Log::error('POS Sale Error: ' . $e->getMessage(), [
                            'trace' => $e->getTraceAsString(),
                            'request' => $validated,
                        ]);

                        throw $e; // Re-throw to trigger rollback
                    }
                });

                break; // Success, exit loop

            } catch (\Illuminate\Database\QueryException $e) {
                // Check for duplicate entry error (1062)
                if ($e->errorInfo[1] == 1062) {
                    $attempt++;
                    if ($attempt >= $maxRetries) {
                        throw $e;
                    }
                    // Small delay before retry
                    usleep(100000); // 100ms
                    continue;
                }
                throw $e;
            }
        }

        // Send low stock notifications after transaction commits
        if (!empty($lowStockProducts)) {
            $salonId = auth()->user()->salon_id;
            $settings = app(\App\Services\SettingsService::class);

            foreach ($lowStockProducts as $product) {
                try {
                    // Use notification_email if set, otherwise fall back to business_email
                    $notificationEmail = $settings->get('notification_email', null, $salonId);
                    if (!$notificationEmail) {
                        $notificationEmail = $settings->get('business_email', null, $salonId);
                    }

                    if ($notificationEmail) {
                        // Use a simple cache lock to prevent spamming emails for the same product every transaction
                        $cacheKey = "low_stock_email_sent_{$product->id}";
                        if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                            \Illuminate\Support\Facades\Mail::raw("Low Stock Alert!\n\nProduct: {$product->name}\nSKU: {$product->sku}\nCurrent Stock: {$product->quantity_in_stock}\nReorder Level: {$product->reorder_level}", function ($message) use ($notificationEmail, $product) {
                                $message->to($notificationEmail)
                                    ->subject("Low Stock Alert: {$product->name}");
                            });
                            // Don't send another email for this product for 24 hours
                            \Illuminate\Support\Facades\Cache::put($cacheKey, true, 60 * 24);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error("Failed to send low stock email: " . $e->getMessage());
                }
            }
        }

        return $result;
    }

    /**
     * Record inventory transaction.
     *
     * @param int $itemId
     * @param float $quantity
     * @param string $type
     * @param int $referenceId
     * @param string $referenceNumber
     * @param string $notes
     * @param float $unitPrice
     * @return \App\Models\InventoryTransaction
     */
    protected function recordInventoryTransaction($itemId, $quantity, $type, $referenceId, $referenceType, $notes = '', $unitPrice = 0)
    {
        return \App\Models\InventoryTransaction::create([
            'item_id' => $itemId,
            'quantity' => $quantity,
            'transaction_type' => $type,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'unit_cost' => $unitPrice,
            'total_cost' => $quantity * $unitPrice,
            'notes' => $notes,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Calculate membership discount for an item.
     *
     * @param array $item
     * @param Membership $membership
     * @return float
     */
    protected function calculateMembershipDiscount($item, Membership $membership)
    {
        $discount = 0;

        if ($item['type'] === 'service') {
            // Check if service is included in membership
            $membershipService = $membership->services()
                ->where('service_id', $item['id'])
                ->first();

            if ($membershipService) {
                $service = Service::find($item['id']);
                if ($service) {
                    $unitPrice = $service->price;
                    $lineTotal = $unitPrice * $item['quantity'];

                    if (in_array($membershipService->pivot->discount_type, ['percentage', 'percent'])) {
                        $discount = $lineTotal * ($membershipService->pivot->discount_value / 100);
                    } elseif (in_array($membershipService->pivot->discount_type, ['fixed', 'amount'])) {
                        $discount = min($membershipService->pivot->discount_value * $item['quantity'], $lineTotal);
                    } elseif ($membershipService->pivot->discount_type === 'membership_price') {
                        $membershipPrice = $membershipService->pivot->membership_price ?? 0;
                        $discount = max(0, $lineTotal - ($membershipPrice * $item['quantity']));
                    }
                }
            } else {
                // Apply general membership discount if no specific discount
                $service = Service::find($item['id']);
                if ($service) {
                    $unitPrice = $service->price;
                    $lineTotal = $unitPrice * $item['quantity'];
                    $discount = $lineTotal * ($membership->discount_value / 100);
                }
            }
        } elseif ($item['type'] === 'product') {
            // Check if product is included in membership
            $membershipProduct = $membership->inventoryItems()
                ->where('inventory_item_id', $item['id'])
                ->first();

            if ($membershipProduct) {
                $product = InventoryItem::find($item['id']);
                if ($product) {
                    $unitPrice = $product->selling_price;
                    $lineTotal = $unitPrice * $item['quantity'];

                    if (in_array($membershipProduct->pivot->discount_type, ['percentage', 'percent'])) {
                        $discount = $lineTotal * ($membershipProduct->pivot->discount_value / 100);
                    } elseif (in_array($membershipProduct->pivot->discount_type, ['fixed', 'amount'])) {
                        $discount = min($membershipProduct->pivot->discount_value * $item['quantity'], $lineTotal);
                    } elseif ($membershipProduct->pivot->discount_type === 'membership_price') {
                        $membershipPrice = $membershipProduct->pivot->membership_price ?? 0;
                        $discount = max(0, $lineTotal - ($membershipPrice * $item['quantity']));
                    }
                }
            } else {
                // Apply general membership discount if no specific discount
                $product = InventoryItem::find($item['id']);
                if ($product) {
                    $unitPrice = $product->selling_price;
                    $lineTotal = $unitPrice * $item['quantity'];
                    $discount = $lineTotal * ($membership->discount_value / 100);
                }
            }
        } elseif ($item['type'] === 'package') {
            // For packages, apply general membership discount to the package price
            $package = Package::find($item['id']);
            if ($package) {
                $unitPrice = $package->price;
                $lineTotal = $unitPrice * $item['quantity'];
                $discount = $lineTotal * ($membership->discount_value / 100);
            }
        } elseif ($item['type'] === 'membership') {
            // Membership purchases don't get membership discounts
            $discount = 0;
        }

        return $discount;
    }

    /**
     * Generate a unique invoice number.
     *
     * @return string
     */
    protected function generateInvoiceNumber()
    {
        $prefix = 'INV-';

        // Find the last invoice number that matches the pattern INV-XXXXXX
        // We use a regex or just simple ordering. Since we want to be safe, let's look for the max ID first
        // or order by id desc which is safer for sequential generation usually.

        $lastSale = PosSale::where('salon_id', auth()->user()->salon_id)
            ->where('invoice_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastSale) {
            // Extract the number part. 
            // If the previous format was INV-Ymd-XXXX, this might fail if we don't handle it.
            // Let's try to parse it.
            $parts = explode('-', $lastSale->invoice_number);

            // If it's the old format (3 parts: INV, Date, Num), we might want to start fresh or continue?
            // User said "dont use date kee sequentinal invoice number".
            // If we have mixed formats, it's tricky. 
            // Let's assume we want to continue from the highest number found, OR start from 1 if only old format exists?
            // Actually, if we want strict sequential INV-000001, INV-000002...
            // If the DB has INV-20230101-0001, that's not compatible with INV-000001 sorting alphabetically if we mix them.
            // But we are ordering by ID desc.

            if (count($parts) === 2 && is_numeric($parts[1])) {
                // It's already in new format INV-XXXXXX
                $number = (int) $parts[1] + 1;
            } else {
                // It's likely old format or something else. 
                // Let's check if there are ANY new format invoices.
                // If not, start from 1.
                // To be safe, let's try to find the max number of the NEW format specifically.

                $lastSequentialSale = PosSale::where('salon_id', auth()->user()->salon_id)
                    ->where('invoice_number', 'REGEXP', '^INV-[0-9]+$')
                    ->orderByRaw('CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED) DESC')
                    ->first();

                if ($lastSequentialSale) {
                    $parts = explode('-', $lastSequentialSale->invoice_number);
                    $number = (int) $parts[1] + 1;
                } else {
                    $number = 1;
                }
            }
        } else {
            $number = 1;
        }

        return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Search for products, services, and packages.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        $services = [];
        $products = [];
        $packages = [];

        if (!empty($query)) {
            // Search services
            $services = Service::active()
                ->where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->get()
                ->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'price' => $service->price,
                        'duration' => $service->duration,
                        'type' => 'service',
                        'image' => $service->image_url ?? asset('images/default-service.png'),
                        'formatted_price' => format_currency((float) $service->price)
                    ];
                });

            // Search products
            $products = InventoryItem::with('category')
                ->where('is_active', true)
                ->where('quantity_in_stock', '>', 0)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhere('sku', 'like', "%{$query}%");
                })
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->selling_price,
                        'quantity' => $product->quantity_in_stock,
                        'type' => 'product',
                        'image' => $product->image_url ?? asset('images/default-product.png'),
                        'formatted_price' => format_currency((float) $product->selling_price),
                        'sku' => $product->sku,
                        'description' => $product->description
                    ];
                });

            // Search packages
            $packages = Package::active()
                ->where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->get()
                ->map(function ($package) {
                    return [
                        'id' => $package->id,
                        'name' => $package->name,
                        'price' => $package->price,
                        'services_count' => $package->services->count(),
                        'type' => 'package',
                        'image' => asset('images/default-package.png'),
                        'formatted_price' => format_currency((float) $package->price),
                        'description' => $package->description
                    ];
                });

            // Search memberships
            $memberships = Membership::active()
                ->where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->get()
                ->map(function ($membership) {
                    return [
                        'id' => $membership->id,
                        'name' => $membership->name,
                        'price' => $membership->calculateTotalMembershipPrice(),
                        'validity' => $membership->getFormattedValidityAttribute(),
                        'type' => 'membership',
                        'image' => asset('images/default-membership.png'),
                        'formatted_price' => format_currency($membership->calculateTotalMembershipPrice()),
                        'description' => $membership->description
                    ];
                });
        }

        return response()->json([
            'services' => $services,
            'products' => $products,
            'packages' => $packages,
            'memberships' => $memberships ?? []
        ]);
    }

    /**
     * Search customers by name or phone.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchCustomerByMobile(Request $request)
    {
        $query = $request->input('q', '');
        $salonId = auth()->user()->salon_id;

        $customers = Customer::with('user')
            ->where('salon_id', $salonId)
            ->where('status', 'active')
            ->when(!empty($query), function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%")
                        ->orWhere('phone', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->orderBy('name')
            ->limit(50)
            ->get()
            ->map(function ($customer) {
                $maskedPhone = \App\Helpers\CustomerDataHelper::getMaskedPhone($customer);
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $maskedPhone,
                    'email' => \App\Helpers\CustomerDataHelper::getMaskedEmail($customer),
                    'text' => "{$customer->name} ({$maskedPhone})"
                ];
            });

        return response()->json([
            'customers' => $customers
        ]);
    }

    /**
     * Get package services for expansion in cart.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPackageServices($id)
    {
        $package = Package::with([
            'services' => function ($query) {
                $query->select('services.id', 'services.name', 'services.price', 'services.duration');
            }
        ])->findOrFail($id);

        $services = $package->services->map(function ($service) use ($package) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'price' => 0,
                'duration' => $service->duration,
                'type' => 'service',
                'package_id' => $package->id,
                'package_name' => $package->name,
                'formatted_price' => format_currency(0)
            ];
        });

        return response()->json([
            'services' => $services,
            'package_total' => $package->price
        ]);
    }

    public function receipt($id)
    {
        $salonId = auth()->user()->salon_id;
        $sale = PosSale::with(['customer', 'employee', 'items.item', 'items.staff'])->where('salon_id', $salonId)->findOrFail($id);

        $salon = auth()->user()->salon;
        $salonData = [
            'name' => $salon->name,
            'slug' => $salon->slug,
            'address' => $salon->address,
            'phone' => $salon->phone,
            'email' => $salon->email,
            'website' => $salon->website,
            'logo' => $salon->logo ? asset('storage/' . $salon->logo) : null,
        ];

        $settings = app(\App\Services\SettingsService::class);

        // Fix timezone
        $timezone = $settings->get('timezone', config('app.timezone'), $salon->id);
        if ($sale->created_at) {
            $sale->created_at = $sale->created_at->setTimezone($timezone);
        }
        if ($sale->sale_date) {
            $sale->sale_date = $sale->sale_date->setTimezone($timezone);
        }

        return view('pos.receipt', compact('sale', 'salonData', 'settings'));
    }

    public function arabicReceipt($id)
    {
        $salonId = auth()->user()->salon_id;
        $sale = PosSale::with(['customer', 'employee', 'items.item', 'items.staff'])->where('salon_id', $salonId)->findOrFail($id);

        $salon = auth()->user()->salon;
        $salonData = [
            'name' => $salon->name,
            'slug' => $salon->slug,
            'address' => $salon->address,
            'phone' => $salon->phone,
            'email' => $salon->email,
            'website' => $salon->website,
            'logo' => $salon->logo ? asset('storage/' . $salon->logo) : null,
        ];

        $settings = app(\App\Services\SettingsService::class);

        // Fix timezone
        $timezone = $settings->get('timezone', config('app.timezone'), $salon->id);
        if ($sale->created_at) {
            $sale->created_at = $sale->created_at->setTimezone($timezone);
        }
        if ($sale->sale_date) {
            $sale->sale_date = $sale->sale_date->setTimezone($timezone);
        }

        return view('pos.arabic_receipt', compact('sale', 'salonData', 'settings'));
    }

    /**
     * Display a listing of sales.
     *
     * @return \Illuminate\View\View
     */
    public function sales(Request $request)
    {
        $query = PosSale::with(['customer', 'employee'])
            ->withCount('items');

        // Date Filter
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Customer Filter
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Search Filter (Invoice or Customer Name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $sales = $query->latest()
            ->paginate(20)
            ->withQueryString();

        $salonId = auth()->user()->salon_id;
        $customers = Customer::where('salon_id', $salonId)->orderBy('name')->get();
        $settings = app(\App\Services\SettingsService::class);
        $posReceiptArabicButton = $settings->get('pos_receipt_arabic_button', true, auth()->user()->salon_id);

        return view('pos.sales.index', compact('sales', 'customers', 'posReceiptArabicButton'));
    }

    /**
     * Display the specified sale.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function showSale($id)
    {
        $salonId = auth()->user()->salon_id;
        $sale = PosSale::with(['customer', 'employee', 'items.item', 'items.staff'])->where('salon_id', $salonId)->findOrFail($id);

        $salon = auth()->user()->salon;
        $settings = app(\App\Services\SettingsService::class);

        // Fix timezone
        $timezone = $settings->get('timezone', config('app.timezone'), $salon->id);
        if ($sale->created_at) {
            $sale->created_at = $sale->created_at->setTimezone($timezone);
        }
        if ($sale->sale_date) {
            $sale->sale_date = $sale->sale_date->setTimezone($timezone);
        }

        $posReceiptArabicButton = $settings->get('pos_receipt_arabic_button', true, $salon->id);

        return view('pos.sales.show', compact('sale', 'posReceiptArabicButton'));
    }

    /**
     * Show the form for editing the specified sale.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function editSale($id)
    {
        $salonId = auth()->user()->salon_id;
        $sale = PosSale::with(['customer', 'items.item', 'items.staff', 'items.booking'])->where('salon_id', $salonId)->findOrFail($id);

        $hasFutureBooking = $sale->items->whereNotNull('booking_id')->some(function ($item) {
            return $item->booking && \Carbon\Carbon::parse($item->booking->start_time)->isFuture();
        });

        $customers = Customer::where('salon_id', $salonId)->active()->get();
        $employees = User::where('salon_id', $salonId)->role('employee')->get();

        return view('pos.sales.edit', compact('sale', 'customers', 'employees', 'hasFutureBooking'));
    }

    /**
     * Update the specified sale in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSale(Request $request, $id)
    {
        $salonId = auth()->user()->salon_id;
        $sale = PosSale::where('salon_id', $salonId)->findOrFail($id);

        // Capture old state for total_spent adjustment
        $oldCustomerId = $sale->customer_id;
        $oldPaidAmount = $sale->total - $sale->outstanding_amount;

        $validated = $request->validate([
            'customer_id' => [
                'nullable',
                \Illuminate\Validation\Rule::exists('customers', 'id')->where(function ($query) use ($salonId) {
                    return $query->where('salon_id', $salonId);
                }),
            ],
            'employee_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('users', 'id')->where(function ($query) use ($salonId) {
                    return $query->where('salon_id', $salonId);
                }),
            ],
            'payment_method' => 'required|string',
            'payment_status' => 'required|string',
            'notes' => 'nullable|string',
            'tendered_amount' => 'nullable|numeric|min:0',
            'change_amount' => 'nullable|numeric|min:0',
            'sale_date' => 'required|date',
            'invoice_number' => [
                'required',
                'string',
                \Illuminate\Validation\Rule::unique('pos_sales')->where(function ($query) {
                    return $query->where('salon_id', auth()->user()->salon_id);
                })->ignore($id),
            ],
            'discount' => 'nullable|numeric|min:0',
            'tip' => 'nullable|numeric|min:0',
            'cash_amount' => 'nullable|numeric|min:0',
            'card_amount' => 'nullable|numeric|min:0',
            'online_amount' => 'nullable|numeric|min:0',
            'other_amount' => 'nullable|numeric|min:0',
        ]);

        // Recalculate total components
        $subtotal = $sale->subtotal;
        $tax = $sale->tax;
        $discount = $validated['discount'] ?? 0;
        $tip = $validated['tip'] ?? 0;
        $total = round($subtotal + $tax - $discount + $tip, 2);

        $validated['total'] = $total;

        // Handle payment amounts based on method and status
        $paymentMethod = $validated['payment_method'];
        $paymentStatus = $validated['payment_status'];

        // If payment method is 'none' (Pay Later), force status to pending
        if ($paymentMethod === 'none') {
            // Check for future bookings linked to this sale
            $hasFutureBooking = \App\Models\PosSaleItem::where('sale_id', $sale->id)
                ->whereNotNull('booking_id')
                ->whereHas('booking', function ($query) {
                    $query->where('start_time', '>', now());
                })
                ->exists();

            if ($hasFutureBooking) {
                return back()->with('error', 'Unpaid / Pay Later option is not allowed for sales linked to future appointments.');
            }

            $paymentStatus = 'pending';
            $validated['payment_status'] = 'pending';
        }

        $cash = round($validated['cash_amount'] ?? 0, 2);
        $card = round($validated['card_amount'] ?? 0, 2);
        $online = round($validated['online_amount'] ?? 0, 2);
        $other = round($validated['other_amount'] ?? 0, 2);

        $paid = $cash + $card + $online + $other;

        $paid = $cash + $card + $online + $other;

        // STRICT VALIDATION: Ensure amounts match total if status is paid
        if ($paymentStatus === 'paid') {
            // Allow a small epsilon for float comparison errors (0.01)
            if ($paid < ($total - 0.01)) {
                return back()->withErrors(['payment_status' => "Payment status cannot be 'Paid' because the total paid amount (" . format_currency($paid) . ") is less than the total amount (" . format_currency($total) . "). Please update the payment amounts or change status to 'Partial'."])->withInput();
            }
        }

        // If it's marked as paid, ensure outstanding is 0 and amounts match total
        if ($paymentStatus === 'paid') {
            // If all amounts are 0, fill the primary method
            if ($paid == 0) {
                if ($paymentMethod === 'cash')
                    $cash = $total;
                elseif ($paymentMethod === 'card')
                    $card = $total;
                elseif ($paymentMethod === 'online')
                    $online = $total;
                elseif ($paymentMethod !== 'none' && $paymentMethod !== 'mixed')
                    $other = $total;
            }
            $validated['outstanding_amount'] = 0;
        } elseif ($paymentStatus === 'pending' && $paid == 0) {
            // Only clear if no amounts were provided
            $validated['outstanding_amount'] = $total;
        } else {
            // Partial or other (or pending with some amounts)
            $validated['outstanding_amount'] = max(0, $total - $paid);

            // If it's actually fully paid now, update status
            if ($validated['outstanding_amount'] <= 0) {
                $validated['payment_status'] = 'paid';
            } elseif ($paid > 0) {
                $validated['payment_status'] = 'partial';
            }
        }

        $validated['cash_amount'] = $cash;
        $validated['card_amount'] = $card;
        $validated['online_amount'] = $online;
        $validated['other_amount'] = $other;

        // Ensure tendered and change are handled
        if ($paymentMethod === 'cash') {
            $validated['tendered_amount'] = $validated['tendered_amount'] ?? $cash;
            $validated['change_amount'] = max(0, ($validated['tendered_amount'] ?? 0) - $cash);
        }

        $sale->update($validated);

        // SYNC BOOKING STATUS: Update associated bookings based on new sale payment status
        // This mirrors the logic in the store() method to ensure consistency when editing sales
        try {
            $sale->refresh();
            $salonId = auth()->user()->salon_id;
            $newPaymentStatus = $sale->payment_status;
            $newPaymentMethod = $sale->payment_method;

            // Find associated booking IDs from sale items
            $bookingIdsToUpdate = \App\Models\PosSaleItem::where('sale_id', $sale->id)
                ->whereNotNull('booking_id')
                ->pluck('booking_id')
                ->toArray();

            // SYNC CUSTOMER TOTAL SPENT
            // Calculate new paid amount
            $newPaidAmount = $sale->total - $sale->outstanding_amount;
            $newCustomerId = $sale->customer_id;

            // 1. Revert old amount from old customer
            if ($oldCustomerId && $oldPaidAmount > 0) {
                $oldCustomer = \App\Models\Customer::find($oldCustomerId);
                if ($oldCustomer) {
                    $oldCustomer->decrement('total_spent', $oldPaidAmount);
                }
            }

            // 2. Add new amount to new customer (even if same customer)
            if ($newCustomerId && $newPaidAmount > 0) {
                $newCustomer = $newCustomerId == $oldCustomerId && isset($oldCustomer) ? $oldCustomer : \App\Models\Customer::find($newCustomerId);
                // Refresh if it's the same object to be safe, though decrement happened in DB
                if ($newCustomerId == $oldCustomerId && isset($oldCustomer)) {
                    $newCustomer->refresh();
                }

                if ($newCustomer) {
                    $newCustomer->increment('total_spent', $newPaidAmount);
                    // Update last visit if this is a valid paid sale
                    if ($newPaidAmount > 0) {
                        $newCustomer->update(['last_visit_at' => now()]);
                    }
                }
            }
            $bookingIdsToUpdate = \App\Models\PosSaleItem::where('sale_id', $sale->id)
                ->whereNotNull('booking_id')
                ->pluck('booking_id')
                ->toArray();

            if (!empty($bookingIdsToUpdate)) {
                // Get all booking_group_ids to ensure we update entire groups
                $bookingGroupIds = \App\Models\Booking::whereIn('id', $bookingIdsToUpdate)
                    ->where('salon_id', $salonId)
                    ->whereNotNull('booking_group_id')
                    ->pluck('booking_group_id')
                    ->unique();

                // Build query to get all affected bookings
                $query = \App\Models\Booking::where('salon_id', $salonId);
                if ($bookingGroupIds->isNotEmpty()) {
                    $query->where(function ($q) use ($bookingIdsToUpdate, $bookingGroupIds) {
                        $q->whereIn('id', $bookingIdsToUpdate)
                            ->orWhereIn('booking_group_id', $bookingGroupIds);
                    });
                } else {
                    $query->whereIn('id', $bookingIdsToUpdate);
                }

                $bookingsToUpdate = $query->get();
                $updatedCount = 0;

                foreach ($bookingsToUpdate as $booking) {
                    $updates = [
                        'payment_status' => $newPaymentStatus,
                        'payment_method' => $newPaymentMethod,
                        'updated_at' => now()
                    ];

                    if ($newPaymentStatus === 'paid') {
                        // If paid, mark as completed (if not future) and set paid_at

                        $salonTimezone = salon_timezone();
                        $isFutureBooking = $booking->start_time->isFuture() && !$booking->start_time->copy()->setTimezone($salonTimezone)->isToday();

                        if ($isFutureBooking) {
                            // FUTURE: Mark paid, Confirm if pending, DO NOT COMPLETE
                            if ($booking->status === 'pending') {
                                $updates['status'] = 'confirmed';
                            }
                            $updates['payment_status'] = 'paid'; // Already set in $updates but explicit here for clarity
                            $updates['paid_at'] = now();

                            // Note: updateSale manages customer total_spent via the sale total diff logic above, 
                            // so we don't need to manually increment total_spent for the booking here 
                            // because the Sale update logic (lines 2378-2406) handles the financial aggregation.

                        } else {
                            // TODAY/PAST: Mark completed
                            if ($booking->status !== 'completed') {
                                $updates['status'] = 'completed';
                            }
                            $updates['paid_at'] = now();
                        }

                        // Re-calculate tip if needed? (Skipping complex tip logic here for simplicity, focusing on status)
                    } else {
                        // If NOT paid (pending, partial, unpaid)
                        // Revert 'completed' to 'staff_completed' to indicate service done but not paid
                        if ($booking->status === 'completed') {
                            $updates['status'] = 'staff_completed';
                        }
                        // Clear paid_at
                        $updates['paid_at'] = null;
                    }

                    $booking->update($updates);
                    $updatedCount++;
                }

                \Log::info("Synced bookings for updated POS sale", [
                    'sale_id' => $sale->id,
                    'status' => $newPaymentStatus,
                    'updated_count' => $updatedCount
                ]);
            }

            // UPDATE TIP COMMISSIONS
            // Remove existing tip commissions for this sale and redistribute based on new tip amount
            \App\Models\StaffCommission::where('pos_sale_id', $sale->id)
                ->where('item_type', 'tip')
                ->delete();

            if ($sale->tip > 0) {
                $items = $sale->items;
                $staffContributions = [];
                foreach ($items as $saleItem) {
                    if ($saleItem->staff_id) {
                        if (!isset($staffContributions[$saleItem->staff_id])) {
                            $staffContributions[$saleItem->staff_id] = 0;
                        }
                        $staffContributions[$saleItem->staff_id] += 1;
                    }
                }

                if (!empty($staffContributions)) {
                    $totalContributions = array_sum($staffContributions);
                    foreach ($staffContributions as $staffId => $contribution) {
                        $staffTipAmount = ($contribution / $totalContributions) * $sale->tip;
                        \App\Models\StaffCommission::create([
                            'salon_id' => $salonId,
                            'staff_id' => $staffId,
                            'pos_sale_id' => $sale->id,
                            'item_type' => 'tip',
                            'item_id' => null,
                            'sale_amount' => $staffTipAmount,
                            'commission_amount' => $staffTipAmount,
                            'commission_profile_id' => null,
                            'status' => 'approved',
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to sync bookings after sale update', ['sale_id' => $sale->id, 'error' => $e->getMessage()]);
        }

        return redirect()->route('admin.pos.sales.show', $id)
            ->with('success', 'Sale updated successfully.');
    }

    /**
     * Process a refund for the specified sale.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function processRefund(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:1000',
        ]);

        $salonId = auth()->user()->salon_id;
        $sale = PosSale::with(['items', 'customer'])->where('salon_id', $salonId)->findOrFail($id);

        DB::beginTransaction();

        try {
            // Create refund record
            // Ensure salon_id is passed as it's required by the model/migration
            $refund = $sale->refunds()->create([
                'salon_id' => $sale->salon_id,
                'amount' => $request->amount,
                'reason' => $request->reason,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // Calculate Refund Fees
            $salon = \App\Models\Salon::find($sale->salon_id);
            $feeAmount = 0;
            if ($salon && $salon->refund_fee > 0) {
                if ($salon->refund_fee_type === 'percentage') {
                    $feeAmount = ($request->amount * $salon->refund_fee) / 100;
                } else {
                    $feeAmount = $salon->refund_fee;
                }
            }

            // Ensure fee doesn't exceed refund amount
            if ($feeAmount > $request->amount) {
                $feeAmount = $request->amount;
            }

            $netRefundAmount = $request->amount - $feeAmount;

            $refund->update([
                'fee_amount' => $feeAmount,
                'net_refund_amount' => $netRefundAmount
            ]);

            // Refresh sale to get updated total_refunded via relationship
            $sale->refresh();
            $totalRefunded = $sale->total_refunded;
            $isFullRefund = ($totalRefunded >= $sale->total);

            // Update sale status and refunded_amount column
            $sale->refunded_amount = $totalRefunded;

            if ($isFullRefund) {
                $sale->update([
                    'status' => 'refunded',
                    'refunded_amount' => $totalRefunded
                ]);

                // Void commissions on full refund
                \App\Models\StaffCommission::where('pos_sale_id', $sale->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->update(['status' => 'voided']);

                \Log::info("Voided commissions for refunded sale #{$sale->invoice_number}");
            } else {
                $sale->update([
                    'status' => 'partially_refunded',
                    'refunded_amount' => $totalRefunded
                ]);
            }

            // Update inventory for product items
            if ($request->has('restock_items') && $request->restock_items) {
                foreach ($sale->items as $item) {
                    if ($item->saleable_type === InventoryItem::class) {
                        $inventoryItem = InventoryItem::find($item->saleable_id);
                        if ($inventoryItem) {
                            $inventoryItem->increment('quantity_in_stock', $item->quantity);

                            // Record inventory transaction for the return
                            $this->recordInventoryTransaction(
                                $inventoryItem->id,
                                $item->quantity, // Positive quantity adds to stock
                                'return', // New type 'return' or use 'adjustment'
                                $sale->id,
                                \App\Models\PosSale::class,
                                'Restocked from Refund #' . $sale->id . ' Reason: ' . $request->reason,
                                (float) $item->unit_price
                            );
                        }
                    }
                }
            }

            // Update associated bookings
            $bookingIds = $sale->items->pluck('booking_id')->filter()->unique();
            if ($bookingIds->isNotEmpty()) {
                $bookings = \App\Models\Booking::whereIn('id', $bookingIds)->get();
                foreach ($bookings as $booking) {
                    if ($isFullRefund) {
                        // Full refund -> Cancel booking
                        $booking->update([
                            'status' => \App\Models\Booking::STATUS_CANCELLED,
                            'payment_status' => \App\Models\Booking::PAYMENT_STATUS_REFUNDED,
                            'cancellation_reason' => $request->reason . ' (Refunded via POS)',
                            // Clear paid_at to reflect it's no longer a paid/valid transaction for statistics
                            'paid_at' => null
                        ]);

                        \Log::info("Booking {$booking->id} cancelled via POS Refund", ['sale_id' => $sale->id]);
                    } else {
                        // Partial refund
                        $booking->update([
                            'payment_status' => \App\Models\Booking::PAYMENT_STATUS_PARTIAL
                        ]);
                    }
                }
            }

            // Update customer stats
            if ($sale->customer) {
                // Decrement total spent by the net refunded amount (actual money returned)
                // Any refund fees kept by the salon are still considered 'spent' by the customer
                $sale->customer->decrement('total_spent', $netRefundAmount);
            }

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Refund processed successfully',
                    'redirect' => route('admin.pos.sales.show', $sale->id)
                ]);
            }

            return redirect()->route('admin.pos.sales.show', $sale->id)
                ->with('success', 'Refund processed successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error processing refund: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error processing refund: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error processing refund: ' . $e->getMessage());
        }
    }

    /**
     * Void the specified sale.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function voidSale($id, Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $sale = PosSale::where('salon_id', $salonId)->findOrFail($id);

        // Check if sale can be voided
        if ($sale->status === 'voided') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sale has already been voided.'
                ], 400);
            }
            return redirect()->back()->with('error', 'Sale has already been voided.');
        }

        if ($sale->status === 'refunded' || $sale->status === 'partially_refunded') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot void a sale that has been refunded.'
                ], 400);
            }
            return redirect()->back()->with('error', 'Cannot void a sale that has been refunded.');
        }

        DB::beginTransaction();

        try {
            // Update sale status
            $sale->update([
                'status' => 'voided',
                'voided_by' => auth()->id(),
                'voided_at' => now(),
                'void_reason' => $request->void_reason,
            ]);

            // Restore inventory for product items
            foreach ($sale->items as $item) {
                if ($item->saleable_type === InventoryItem::class) {
                    $inventoryItem = InventoryItem::find($item->saleable_id);
                    if ($inventoryItem) {
                        $inventoryItem->increment('quantity_in_stock', $item->quantity);
                    }
                }
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sale has been voided successfully',
                    'redirect' => route('admin.pos.sales.show', $sale->id)
                ]);
            }

            return redirect()->route('admin.pos.sales.show', $sale->id)
                ->with('success', 'Sale has been voided successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error voiding sale: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error voiding sale: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error voiding sale: ' . $e->getMessage());
        }
    }

    /**
     * Display daily sales report.
     *
     * @return \Illuminate\View\View
     */
    public function dailySalesReport()
    {
        $startDate = request('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));

        $sales = PosSale::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total_sales'),
            DB::raw('SUM(total) as total_amount')
        )
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return view('pos.reports.daily-sales', compact('sales', 'startDate', 'endDate'));
    }

    /**
     * Display product sales report.
     *
     * @return \Illuminate\View\View
     */
    public function productSalesReport()
    {
        $startDate = request('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));

        $products = PosSaleItem::join('pos_sales', 'pos_sale_items.sale_id', '=', 'pos_sales.id')
            ->join('inventory_items', 'pos_sale_items.saleable_id', '=', 'inventory_items.id')
            ->where('pos_sale_items.saleable_type', InventoryItem::class)
            ->whereBetween('pos_sales.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select(
                'inventory_items.name',
                'inventory_items.sku',
                DB::raw('SUM(pos_sale_items.quantity) as total_quantity'),
                DB::raw('SUM(pos_sale_items.quantity * pos_sale_items.unit_price) as total_amount')
            )
            ->groupBy('inventory_items.id', 'inventory_items.name', 'inventory_items.sku')
            ->orderBy('total_quantity', 'desc')
            ->get();

        return view('pos.reports.product-sales', compact('products', 'startDate', 'endDate'));
    }

    /**
     * Display service sales report.
     *
     * @return \Illuminate\View\View
     */
    public function serviceSalesReport()
    {
        $startDate = request('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));

        $services = PosSaleItem::join('pos_sales', 'pos_sale_items.sale_id', '=', 'pos_sales.id')
            ->join('services', 'pos_sale_items.saleable_id', '=', 'services.id')
            ->where('pos_sale_items.saleable_type', Service::class)
            ->whereBetween('pos_sales.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select(
                'services.name',
                DB::raw('COUNT(*) as total_services'),
                DB::raw('SUM(pos_sale_items.quantity * pos_sale_items.unit_price) as total_amount')
            )
            ->groupBy('services.id', 'services.name')
            ->orderBy('total_services', 'desc')
            ->get();

        return view('pos.reports.service-sales', compact('services', 'startDate', 'endDate'));
    }

    /**
     * Display POS settings.
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        return view('pos.settings');
    }

    public function getCustomer($id)
    {
        $salonId = auth()->user()->salon_id;
        $customer = Customer::with('user')->where('salon_id', $salonId)->findOrFail($id);
        return response()->json([
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => \App\Helpers\CustomerDataHelper::getMaskedEmail($customer),
            'phone' => \App\Helpers\CustomerDataHelper::getMaskedPhone($customer),
            'address' => $customer->address,
        ]);
    }

    /**
     * Get customer's active membership information.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCustomerMembership($id)
    {
        $salonId = auth()->user()->salon_id;
        $customer = Customer::where('salon_id', $salonId)->findOrFail($id);
        $activeMembership = $customer->getActiveMembership();

        if ($activeMembership) {
            return response()->json([
                'has_membership' => true,
                'membership' => [
                    'id' => $activeMembership->membership->id,
                    'name' => $activeMembership->membership->name,
                    'start_date' => $activeMembership->start_date->format('Y-m-d'),
                    'end_date' => $activeMembership->end_date->format('Y-m-d'),
                ]
            ]);
        }

        return response()->json([
            'has_membership' => false,
            'membership' => null
        ]);
    }

    /**
     * Get membership discounts for cart items.
     *
     * @param  int  $customerId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMembershipDiscountsForItems(Request $request, $customerId)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required',
            'items.*.type' => 'required|in:product,service,package,membership',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.package_id' => 'nullable|exists:packages,id',
        ]);

        $salonId = auth()->user()->salon_id;
        $customer = Customer::where('salon_id', $salonId)->findOrFail($customerId);
        $activeMembership = $customer->getActiveMembership();

        $discounts = [];

        if ($activeMembership && $activeMembership->membership) {
            $membership = $activeMembership->membership;

            foreach ($validated['items'] as $item) {
                $discountAmount = $this->calculateMembershipDiscount($item, $membership);
                $discounts[] = [
                    'item_id' => $item['id'],
                    'type' => $item['type'],
                    'package_id' => $item['package_id'] ?? null,
                    'discount_amount' => $discountAmount,
                    'discount_type' => $this->getDiscountType($item, $membership),
                ];
            }
        }

        return response()->json([
            'discounts' => $discounts,
            'has_membership' => $activeMembership ? true : false,
        ]);
    }

    /**
     * Get discount type for an item (for frontend display).
     *
     * @param array $item
     * @param Membership $membership
     * @return string
     */
    protected function getDiscountType($item, Membership $membership)
    {
        if ($item['type'] === 'service') {
            $membershipService = $membership->services()
                ->where('service_id', $item['id'])
                ->first();

            if ($membershipService) {
                return $membershipService->pivot->discount_type;
            }
        } elseif ($item['type'] === 'product') {
            $membershipProduct = $membership->inventoryItems()
                ->where('inventory_item_id', $item['id'])
                ->first();

            if ($membershipProduct) {
                return $membershipProduct->pivot->discount_type;
            }
        }

        // General discount
        return 'percentage';
    }

    /**
     * Assign a membership to a customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $customerId
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignMembership(Request $request, $customerId)
    {
        $salonId = auth()->user()->salon_id;
        $validated = $request->validate([
            'membership_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('memberships', 'id')->where(function ($query) use ($salonId) {
                    return $query->where('salon_id', $salonId);
                }),
            ],
            'start_date' => 'nullable|date|after_or_equal:today',
        ]);

        $customer = Customer::where('salon_id', $salonId)->findOrFail($customerId);
        $membership = Membership::where('salon_id', $salonId)->active()->findOrFail($validated['membership_id']);

        // Calculate end date based on membership validity
        $startDate = $validated['start_date'] ? \Carbon\Carbon::parse($validated['start_date']) : now();
        $endDate = $startDate->copy();

        if ($membership->validity_unit === 'days') {
            $endDate->addDays($membership->validity_value);
        } elseif ($membership->validity_unit === 'weeks') {
            $endDate->addWeeks($membership->validity_value);
        } elseif ($membership->validity_unit === 'months') {
            $endDate->addMonths($membership->validity_value);
        } elseif ($membership->validity_unit === 'years') {
            $endDate->addYears($membership->validity_value);
        }

        DB::transaction(function () use ($customer, $membership, $startDate, $endDate) {
            // Deactivate any existing active memberships for this customer
            CustomerMembership::where('customer_id', $customer->id)
                ->where('is_active', true)
                ->update(['is_active' => false, 'end_date' => now()]);

            // Delete any existing record for this specific membership to avoid unique constraint violation
            CustomerMembership::where('customer_id', $customer->id)
                ->where('membership_id', $membership->id)
                ->delete();

            // Create new membership assignment
            CustomerMembership::create([
                'customer_id' => $customer->id,
                'membership_id' => $membership->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => true,
                'assigned_by' => auth()->id(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Membership assigned successfully',
            'membership' => [
                'name' => $membership->name,
                'end_date' => $endDate->format('Y-m-d'),
            ]
        ]);
    }

    public function salesHistory()
    {
        $sales = PosSale::with(['customer', 'user'])
            ->latest()
            ->paginate(20);

        return view('pos.history', compact('sales'));
    }

}
