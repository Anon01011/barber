<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    /**
     * Handle successful responses consistently
     */
    protected function handleResponse($request, $data, $status = 200)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($data, $status);
        }

        return redirect($data['redirect'] ?? route('admin.inventory.index'))
            ->with('success', $data['message']);
    }

    /**
     * Handle error responses consistently
     */
    protected function handleErrorResponse($request, $exception, $status = 500)
    {
        $message = $exception->getMessage();

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $message = 'Validation failed';
            $errors = $exception->errors();
        } else {
            $errors = $exception->getMessage();
        }

        if ($request->ajax() || $request->wantsJson()) {
            $response = [
                'success' => false,
                'message' => $message,
                'errors' => $errors ?? null,
            ];

            if (config('app.debug')) {
                $response['debug'] = [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTraceAsString()
                ];
            }

            return response()->json($response, $status);
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return back()->withErrors($errors)->withInput();
        }

        return back()
            ->withInput()
            ->with('error', $message);
    }

    /**
     * Display a listing of the inventory items.
     */
    public function index(Request $request)
    {
        try {
            $salonId = auth()->user()->salon_id;

            $query = InventoryItem::with('category', 'supplier', 'variants', 'branches')
                ->when($request->search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%")
                            ->orWhere('barcode', 'like', "%{$search}%");
                    });
                })
                ->when($request->category, function ($query, $categoryId) {
                    $query->where('category_id', $categoryId);
                })
                ->when($request->branch, function ($query, $branchId) {
                    // Filter by branch - check both direct branch_id and pivot table
                    $query->where(function ($q) use ($branchId) {
                        $q->where('branch_id', $branchId)
                            ->orWhereHas('branches', function ($q) use ($branchId) {
                                $q->where('branches.id', $branchId);
                            });
                    });
                })
                ->when($request->status === 'low', function ($query) use ($request) {
                    if ($request->branch) {
                        $branchId = $request->branch;
                        $query->whereHas('branches', function ($q) use ($branchId) {
                            $q->where('branches.id', $branchId)
                              ->whereColumn('branch_inventory_item.quantity', '<=', 'branch_inventory_item.minimum_quantity')
                              ->where('branch_inventory_item.quantity', '>', 0);
                        });
                    } else {
                        $query->where('quantity_in_stock', '>', 0)
                            ->where(function ($q) {
                                $q->where(function ($sub) {
                                    $sub->where('minimum_quantity', '>', 0)
                                        ->whereColumn('quantity_in_stock', '<=', 'minimum_quantity');
                                })
                                ->orWhere(function ($sub) {
                                    $sub->where('reorder_level', '>', 0)
                                        ->whereColumn('quantity_in_stock', '<=', 'reorder_level');
                                });
                            });
                    }
                })
                ->when($request->status === 'out', function ($query) use ($request) {
                    if ($request->branch) {
                        $branchId = $request->branch;
                        $query->whereHas('branches', function ($q) use ($branchId) {
                            $q->where('branches.id', $branchId)
                              ->where('branch_inventory_item.quantity', '<=', 0);
                        });
                    } else {
                        $query->where('quantity_in_stock', '<=', 0);
                    }
                });

            $items = $query->latest()->paginate(15);

            $categories = InventoryCategory::active()
                ->orderBy('name')
                ->get(['id', 'name']);

            $suppliers = \App\Models\Supplier::orderBy('name')
                ->get(['id', 'name']);

            // Get branches for the current salon
            $branches = \App\Models\Branch::where('salon_id', $salonId)
                ->active()
                ->orderBy('name')
                ->get(['id', 'name']);

            return view('inventory.index', [
                'items' => $items,
                'categories' => $categories,
                'suppliers' => $suppliers,
                'branches' => $branches,
                'filters' => $request->only(['search', 'category', 'status', 'branch']),
                'unitTypes' => InventoryItem::$unitTypes,
            ]);

        } catch (\Exception $e) {
            \Log::error('Inventory index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load inventory. Please try again.');
        }
    }

    /**
     * Show the form for creating a new inventory item.
     */
    public function create()
    {
        try {
            $salonId = auth()->user()->salon_id;

            $categories = InventoryCategory::active()->orderBy('name')->get(['id', 'name']);
            $suppliers = \App\Models\Supplier::active()->orderBy('name')->get(['id', 'name']);

            // Get branches for the current salon
            $branches = \App\Models\Branch::where('salon_id', $salonId)
                ->active()
                ->orderBy('name')
                ->get(['id', 'name']);

            return view('inventory.create', [
                'categories' => $categories,
                'suppliers' => $suppliers,
                'branches' => $branches,
                'unitTypes' => InventoryItem::$unitTypes,
            ]);
        } catch (\Exception $e) {
            \Log::error('Create inventory form error: ' . $e->getMessage());
            return redirect()->route('admin.inventory.index')
                ->with('error', 'Failed to load the create form. Please try again.');
        }
    }

    /**
     * Store a newly created inventory category.
     */
    public function storeCategory(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('inventory_categories', 'name')->where(function ($query) use ($salonId) {
                    return $query->where('salon_id', $salonId);
                })
            ],
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            $category = InventoryCategory::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'icon' => $validated['icon'] ?? null,
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.inventory.index')
                ->with('success', 'Category created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating category: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create category. Please try again.');
        }
    }

    /**
     * Store a newly created inventory item.
     */
    public function store(Request $request)
    {
        $salonId = auth()->user()->salon_id;
        $userBranchId = auth()->user()->branch_id;

        // If user is assigned to a branch, force that branch
        if ($userBranchId) {
            $request->merge(['branch_ids' => [$userBranchId]]);
        } else {
            // If salon has only 1 active branch, automatically assign it
            $salonBranchCount = \App\Models\Branch::where('salon_id', $salonId)->active()->count();
            if ($salonBranchCount === 1) {
                $onlyBranch = \App\Models\Branch::where('salon_id', $salonId)->active()->first();
                if ($onlyBranch) {
                    $request->merge(['branch_ids' => [$onlyBranch->id]]);
                }
            }
        }

        $salon = app()->bound('current_salon') ? app('current_salon') : null;

        if (!$salon) {
            abort(500, 'Salon context not available');
        }
        if (!$salon->canAddProduct()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have reached the maximum number of products allowed for your plan.'
                ], 403);
            }
            return back()->with('error', 'You have reached the maximum number of products allowed for your plan.');
        }

        try {
            \Log::info('Inventory store request data:', $request->all());

            // Start database transaction
            DB::beginTransaction();

            // Validate the request data
            $validated = $request->validate([
                'category_id' => [
                    'required',
                    Rule::exists('inventory_categories', 'id')->where('salon_id', $salonId)
                ],
                'name' => 'required|string|max:255',
                'sku' => [
                    'nullable',
                    'string',
                    'max:100',
                    Rule::unique('inventory_items')->where('salon_id', $salonId),
                    'regex:/^[A-Z0-9-]+$/',
                ],
                'description' => 'nullable|string|max:2000',
                'purchase_price' => 'nullable|numeric|min:0|max:999999.99',
                'selling_price' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:999999.99',
                ],
                'quantity_in_stock' => 'nullable|numeric|min:0|max:999999.9999',
                'minimum_quantity' => 'nullable|numeric|min:0|max:999999.9999',
                'unit_type' => 'nullable|string|max:50',
                'barcode' => [
                    'nullable',
                    'string',
                    'max:100',
                    Rule::unique('inventory_items')->where('salon_id', $salonId),
                ],
                'location' => 'nullable|string|max:100',
                'is_active' => 'sometimes|boolean',
                'is_taxable' => 'sometimes|boolean',
                'notes' => 'nullable|string|max:1000',
                'reorder_level' => 'nullable|numeric|min:0|max:999999.9999',
                'supplier_id' => [
                    'nullable',
                    Rule::exists('suppliers', 'id')->where('salon_id', $salonId)
                ],
                'tax_rate' => 'nullable|numeric|min:0|max:100',
                'weight' => 'nullable|numeric|min:0|max:999999.999',
                'dimensions' => 'nullable|string|max:100',
                'expiry_date' => 'nullable|date|after_or_equal:today',
                'manufacturer' => 'nullable|string|max:255',
                'brand' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'branch_ids' => 'nullable|array',
                'branch_ids.*' => 'exists:branches,id',
            ], [
                'sku.regex' => 'SKU can only contain uppercase letters, numbers, and hyphens.',
                'selling_price.min' => 'Selling price must be greater than or equal to purchase price.',
                'image.max' => 'The image must not be larger than 5MB.',
                'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, or webp.',
                'variants.*.name' => 'Variant name is required.',
                'variants.*.value' => 'Variant value is required.',
                'branch_ids.*.exists' => 'Invalid branch selection.',
            ]);

            // Validate branch assignments if provided
            // Only require branch selection if salon has multiple branches
            $salonBranchCount = \App\Models\Branch::where('salon_id', $salonId)->active()->count();

            if ($request->has('branch_ids') && is_array($request->branch_ids)) {
                // Ensure all selected branches belong to the salon
                $validBranches = \App\Models\Branch::where('salon_id', $salonId)
                    ->whereIn('id', $request->branch_ids)
                    ->pluck('id')
                    ->toArray();

                if (count($validBranches) !== count($request->branch_ids)) {
                    throw new \Exception('One or more selected branches do not belong to your salon.');
                }

                // Only require at least one branch if salon has multiple branches
                if ($salonBranchCount > 1 && count($validBranches) === 0) {
                    throw new \Exception('Please select at least one branch.');
                }
            } elseif ($salonBranchCount > 1 && !$request->has('branch_ids')) {
                // If salon has multiple branches but no branch_ids provided, require selection
                throw new \Exception('Please select at least one branch for this product.');
            }

            // Set default values
            $validated['salon_id'] = $salonId;
            $validated['branch_id'] = $userBranchId;
            $validated['is_active'] = $request->boolean('is_active', true);
            $validated['purchase_price'] = $validated['purchase_price'] ?? 0;
            $validated['quantity_in_stock'] = $validated['quantity_in_stock'] ?? 9999; // Default to high stock for basic use
            $validated['minimum_quantity'] = $validated['minimum_quantity'] ?? 0;
            $validated['unit_type'] = $validated['unit_type'] ?? 'pcs';

            // Auto-generate SKU if not provided
            if (empty($validated['sku'])) {
                $validated['sku'] = InventoryItem::generateUniqueSku($validated['name']);
            }

            // Create the inventory item first to get an ID
            $inventoryItem = InventoryItem::create($validated);

            // Handle variants if present
            if ($request->has('variants') && is_array($request->variants)) {
                \Log::info('Processing variants', ['variants' => $request->variants]);
                foreach ($request->variants as $variantData) {
                    if (!empty($variantData['name']) && !empty($variantData['value'])) {
                        $inventoryItem->variants()->create([
                            'salon_id' => $inventoryItem->salon_id,
                            'name' => $variantData['name'],
                            'value' => $variantData['value'],
                            'sku' => $variantData['sku'] ?? $inventoryItem->sku . '-' . Str::random(4),
                            'price_adjustment' => $variantData['price_adjustment'] ?? 0,
                            'stock_quantity' => $variantData['stock_quantity'] ?? 9999, // Default to high stock
                            'is_active' => isset($variantData['is_active']) ? (bool) $variantData['is_active'] : true,
                        ]);
                    }
                }
            }

            // Handle file upload if present
            if ($request->hasFile('image')) {
                try {
                    $path = $inventoryItem->uploadImage($request->file('image'));
                    $inventoryItem->update(['image_path' => $path]);
                } catch (\Exception $e) {
                    // If image upload fails, delete the created item and rethrow
                    $inventoryItem->delete();
                    throw new \Exception('Failed to upload image: ' . $e->getMessage());
                }
            }

            // Handle multi-branch assignment
            if ($request->has('branch_ids') && is_array($request->branch_ids) && count($request->branch_ids) > 0) {
                $syncData = [];
                $initialStock = $validated['quantity_in_stock'] ?? 0;
                $branchCount = count($request->branch_ids);

                foreach ($request->branch_ids as $bid) {
                    // If only 1 branch is selected (e.g. branch user or admin selecting one),
                    // assign the initial stock to that branch.
                    // If multiple branches, we initialize with 0 to avoid duplication/confusion,
                    // and expect stock adjustments later.
                    $qty = ($branchCount === 1) ? $initialStock : 0;

                    $syncData[$bid] = [
                        'quantity' => $qty,
                        'minimum_quantity' => $validated['minimum_quantity'] ?? 0,
                        'is_active' => true
                    ];
                }

                // Sync branches to pivot table
                $inventoryItem->branches()->sync($syncData);

                \Log::info('Synced branches for inventory item', [
                    'item_id' => $inventoryItem->id,
                    'branch_ids' => $request->branch_ids,
                    'sync_data' => $syncData
                ]);
            }

            // Sync with Product model (for POS) - create a product for each assigned branch
            $assignedBranches = $inventoryItem->branches()->pluck('branch_id')->toArray();

            if (empty($assignedBranches)) {
                // Fallback: use the item's branch_id if no branches assigned via pivot
                $assignedBranches = [$inventoryItem->branch_id];
            }

            foreach ($assignedBranches as $branchId) {
                \App\Models\Product::updateOrCreate(
                    [
                        'salon_id' => $inventoryItem->salon_id,
                        'branch_id' => $branchId,
                        'sku' => $inventoryItem->sku,
                    ],
                    [
                        'name' => $inventoryItem->name,
                        'description' => $inventoryItem->description,
                        'barcode' => $inventoryItem->barcode,
                        'price' => $inventoryItem->selling_price,
                        'cost_price' => $inventoryItem->purchase_price,
                        'category_id' => $inventoryItem->category_id,
                        'brand' => $inventoryItem->brand,
                        'stock_quantity' => $inventoryItem->quantity_in_stock,
                        'low_stock_threshold' => $inventoryItem->minimum_quantity,
                        'is_active' => $inventoryItem->is_active,
                        'tax_rate' => $inventoryItem->tax_rate,
                        'is_taxable' => $inventoryItem->is_taxable,
                        'image_path' => $inventoryItem->image_path,
                    ]
                );
            }

            // Commit the transaction
            DB::commit();

            // Prepare success response
            $response = [
                'success' => true,
                'message' => 'Inventory item created successfully',
                'redirect' => route('admin.inventory.index'),
                'item' => $inventoryItem->fresh()->load('category', 'supplier')
            ];

            return $this->handleResponse($request, $response);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::warning('Inventory validation failed', ['errors' => $e->errors(), 'data' => $request->all()]);
            return $this->handleErrorResponse($request, $e, 422);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating inventory item: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return $this->handleErrorResponse($request, $e, 500);
        }
    }

    /**
     * Show the form for editing the specified inventory item.
     */
    public function edit(InventoryItem $inventory)
    {
        try {
            $categories = InventoryCategory::active()->orderBy('name')->get(['id', 'name']);
            $suppliers = \App\Models\Supplier::active()->orderBy('name')->get(['id', 'name']);

            return view('inventory.edit', [
                'item' => $inventory,
                'categories' => $categories,
                'suppliers' => $suppliers,
                'unitTypes' => InventoryItem::$unitTypes,
            ]);
        } catch (\Exception $e) {
            \Log::error('Edit inventory item error: ' . $e->getMessage());
            return redirect()->route('admin.inventory.index')
                ->with('error', 'Failed to load the edit form. Please try again.');
        }
    }

    /**
     * Update the specified inventory item.
     */
    public function update(Request $request, InventoryItem $inventory)
    {
        $userBranchId = auth()->user()->branch_id;
        $salonId = auth()->user()->salon_id;

        // If user is assigned to a branch, force that branch
        if ($userBranchId) {
            $request->merge(['branch_ids' => [$userBranchId]]);
        } else {
            // If salon has only 1 active branch, automatically assign it
            $salonBranchCount = \App\Models\Branch::where('salon_id', $salonId)->active()->count();
            if ($salonBranchCount === 1) {
                $onlyBranch = \App\Models\Branch::where('salon_id', $salonId)->active()->first();
                if ($onlyBranch) {
                    $request->merge(['branch_ids' => [$onlyBranch->id]]);
                }
            }
        }

        try {
            DB::beginTransaction();

            // Validate the request data
            $validated = $request->validate([
                'category_id' => [
                    'required',
                    Rule::exists('inventory_categories', 'id')->where('salon_id', auth()->user()->salon_id)
                ],
                'name' => 'required|string|max:255',
                'sku' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('inventory_items', 'sku')
                        ->where('salon_id', auth()->user()->salon_id)
                        ->ignore($inventory->id),
                ],
                'description' => 'nullable|string|max:2000',
                'purchase_price' => 'nullable|numeric|min:0|max:999999.99',
                'selling_price' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:999999.99',
                ],
                'quantity_in_stock' => 'nullable|numeric|min:0|max:999999.9999',
                'minimum_quantity' => 'nullable|numeric|min:0|max:999999.9999',
                'unit_type' => 'nullable|string|max:50',
                'barcode' => [
                    'nullable',
                    'string',
                    'max:100',
                    Rule::unique('inventory_items', 'barcode')
                        ->where('salon_id', auth()->user()->salon_id)
                        ->ignore($inventory->id),
                ],
                'location' => 'nullable|string|max:100',
                'is_active' => 'sometimes|boolean',
                'is_taxable' => 'sometimes|boolean',
                'notes' => 'nullable|string|max:1000',
                'reorder_level' => 'nullable|numeric|min:0|max:999999.9999',

                'supplier_id' => [
                    'nullable',
                    Rule::exists('suppliers', 'id')->where('salon_id', auth()->user()->salon_id)
                ],
                'tax_rate' => 'nullable|numeric|min:0|max:100',
                'weight' => 'nullable|numeric|min:0|max:999999.999',
                'dimensions' => 'nullable|string|max:100',
                'expiry_date' => 'nullable|date|after_or_equal:today',
                'manufacturer' => 'nullable|string|max:255',
                'brand' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'remove_image' => 'sometimes|boolean',
                'branch_ids' => 'nullable|array',
                'branch_ids.*' => 'exists:branches,id',
            ], [
                'sku.unique' => 'This SKU is already in use.',
                'barcode.unique' => 'This barcode is already in use.',
                'selling_price.min' => 'Selling price must be greater than or equal to purchase price.',
                'image.max' => 'The image must not be larger than 5MB.',
                'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, or webp.',
                'variants.*.name' => 'Variant name is required.',
                'variants.*.value' => 'Variant value is required.',
                'branch_ids.*.exists' => 'Invalid branch selection.',
            ]);

            // Validate branch assignments if provided
            // Only require branch selection if salon has multiple branches
            $salonId = auth()->user()->salon_id;
            $salonBranchCount = \App\Models\Branch::where('salon_id', $salonId)->active()->count();

            if ($request->has('branch_ids') && is_array($request->branch_ids)) {
                // Ensure all selected branches belong to the salon
                $validBranches = \App\Models\Branch::where('salon_id', $salonId)
                    ->whereIn('id', $request->branch_ids)
                    ->pluck('id')
                    ->toArray();

                if (count($validBranches) !== count($request->branch_ids)) {
                    throw new \Exception('One or more selected branches do not belong to your salon.');
                }

                // Only require at least one branch if salon has multiple branches
                if ($salonBranchCount > 1 && count($validBranches) === 0) {
                    throw new \Exception('Please select at least one branch.');
                }
            } elseif ($salonBranchCount > 1 && !$request->has('branch_ids')) {
                // If salon has multiple branches but no branch_ids provided, require selection
                throw new \Exception('Please select at least one branch for this product.');
            }


            // Set default values for update if not present
            $validated['purchase_price'] = $validated['purchase_price'] ?? 0;

            if (!isset($validated['quantity_in_stock']) && $request->has('quantity_in_stock')) {
                $validated['quantity_in_stock'] = 9999;
            }
            if (!isset($validated['minimum_quantity']) && $request->has('minimum_quantity')) {
                $validated['minimum_quantity'] = 0;
            }
            if (!isset($validated['unit_type']) && $request->has('unit_type')) {
                $validated['unit_type'] = 'pcs';
            }

            // Handle file upload if present
            if ($request->hasFile('image')) {
                try {
                    $path = $inventory->uploadImage($request->file('image'));
                    $validated['image_path'] = $path;
                } catch (\Exception $e) {
                    throw new \Exception('Failed to upload image: ' . $e->getMessage());
                }
            }

            // Handle image removal if requested
            if ($request->has('remove_image') && $request->boolean('remove_image')) {
                $inventory->deleteImage();
                $validated['image_path'] = null;
            }

            // Update the inventory item
            $inventory->update($validated);

            // Sync with Product model (for POS) - update/create products for each assigned branch
            $assignedBranches = $inventory->branches()->pluck('branch_id')->toArray();

            if (empty($assignedBranches)) {
                // Fallback: use the item's branch_id if no branches assigned via pivot
                $assignedBranches = [$inventory->branch_id];
            }

            $oldSku = $inventory->getOriginal('sku');
            $skuChanged = $oldSku !== $inventory->sku;

            // Step 1: Handle SKU changes - update existing products with new SKU
            if ($skuChanged) {
                \App\Models\Product::where('salon_id', $inventory->salon_id)
                    ->where('sku', $oldSku)
                    ->whereIn('branch_id', $assignedBranches)
                    ->update(['sku' => $inventory->sku]);

                \Log::info('Updated product SKUs', [
                    'old_sku' => $oldSku,
                    'new_sku' => $inventory->sku,
                    'branches' => $assignedBranches
                ]);
            }

            // Step 2: Update or create products for each assigned branch
            $productData = [
                'name' => $inventory->name,
                'description' => $inventory->description,
                'barcode' => $inventory->barcode,
                'price' => $inventory->selling_price,
                'cost_price' => $inventory->purchase_price,
                'category_id' => $inventory->category_id,
                'brand' => $inventory->brand,
                'stock_quantity' => $inventory->quantity_in_stock,
                'low_stock_threshold' => $inventory->minimum_quantity,
                'is_active' => $inventory->is_active,
                'tax_rate' => $inventory->tax_rate,
                'is_taxable' => $inventory->is_taxable,
                'image_path' => $inventory->image_path,
            ];

            foreach ($assignedBranches as $branchId) {
                $existingProduct = \App\Models\Product::where('salon_id', $inventory->salon_id)
                    ->where('branch_id', $branchId)
                    ->where('sku', $inventory->sku)
                    ->first();

                if ($existingProduct) {
                    $existingProduct->update($productData);
                } else {
                    \App\Models\Product::create(array_merge([
                        'salon_id' => $inventory->salon_id,
                        'branch_id' => $branchId,
                        'sku' => $inventory->sku,
                    ], $productData));
                }
            }

            // Step 3: Remove products for branches that are no longer assigned
            \App\Models\Product::where('salon_id', $inventory->salon_id)
                ->where('sku', $inventory->sku)
                ->whereNotIn('branch_id', $assignedBranches)
                ->delete();

            // Step 4: Clean up old SKU products if SKU changed
            if ($skuChanged) {
                \App\Models\Product::where('salon_id', $inventory->salon_id)
                    ->where('sku', $oldSku)
                    ->delete();
            }

            // Handle variants update
            if ($request->has('variants') && is_array($request->variants)) {
                // Get existing variant IDs to identify which ones to keep/update
                $existingVariantIds = $inventory->variants()->pluck('id')->toArray();
                $processedVariantIds = [];

                foreach ($request->variants as $variantData) {
                    if (!empty($variantData['name']) && !empty($variantData['value'])) {
                        $variantData['is_active'] = isset($variantData['is_active']) ? (bool) $variantData['is_active'] : true;

                        if (isset($variantData['id']) && in_array($variantData['id'], $existingVariantIds)) {
                            // Update existing variant
                            $inventory->variants()->where('id', $variantData['id'])->update([
                                'name' => $variantData['name'],
                                'value' => $variantData['value'],
                                'sku' => $variantData['sku'] ?? null,
                                'price_adjustment' => $variantData['price_adjustment'] ?? 0,
                                'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                                'is_active' => $variantData['is_active'],
                            ]);
                            $processedVariantIds[] = $variantData['id'];
                        } else {
                            // Create new variant
                            $newVariant = $inventory->variants()->create([
                                'salon_id' => $inventory->salon_id,
                                'name' => $variantData['name'],
                                'value' => $variantData['value'],
                                'sku' => $variantData['sku'] ?? $inventory->sku . '-' . Str::random(4),
                                'price_adjustment' => $variantData['price_adjustment'] ?? 0,
                                'stock_quantity' => $variantData['stock_quantity'] ?? 9999, // Default to high stock
                                'is_active' => $variantData['is_active'],
                            ]);
                            $processedVariantIds[] = $newVariant->id;
                        }
                    }
                }

                // Delete variants that were not included in the request (if any logic requires it, 
                // but usually we might want to keep them or handle deletion explicitly. 
                // For now, let's assume explicit deletion isn't requested via this array, 
                // or we can implement a sync logic if the frontend sends ALL current variants).
                // If the frontend sends the full list of desired variants, we should delete the missing ones.
                // Let's implement sync logic: delete variants not in processedVariantIds
                $inventory->variants()->whereNotIn('id', $processedVariantIds)->delete();
            }

            // Handle multi-branch assignment
            if ($request->has('branch_ids') && is_array($request->branch_ids) && count($request->branch_ids) > 0) {
                $syncData = [];
                foreach ($request->branch_ids as $bid) {
                    // Check if already exists to preserve quantity
                    $existing = $inventory->branches()->where('branch_id', $bid)->first();
                    if ($existing) {
                        $syncData[$bid] = [
                            'quantity' => $existing->pivot->quantity,
                            'minimum_quantity' => $validated['minimum_quantity'] ?? 0,
                            'is_active' => $existing->pivot->is_active,
                        ];
                    } else {
                        $syncData[$bid] = [
                            'quantity' => 0,
                            'minimum_quantity' => $validated['minimum_quantity'] ?? 0,
                            'is_active' => true,
                        ];
                    }
                }

                // Sync branches to pivot table
                $inventory->branches()->sync($syncData);

                \Log::info('Synced branches for inventory item', [
                    'item_id' => $inventory->id,
                    'branch_ids' => $request->branch_ids
                ]);
            } elseif ($request->has('branch_ids') && is_array($request->branch_ids) && count($request->branch_ids) === 0) {
                // If empty array is sent, detach all branches
                $inventory->branches()->detach();
            }

            // Commit the transaction
            DB::commit();

            // Prepare response
            $response = [
                'success' => true,
                'message' => 'Inventory item updated successfully',
                'redirect' => route('admin.inventory.index'),
                'item' => $inventory->fresh()->load('category', 'supplier')
            ];

            return $this->handleResponse($request, $response);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::warning('Validation failed', ['errors' => $e->errors()]);
            return $this->handleErrorResponse($request, $e, 422);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating inventory item: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return $this->handleErrorResponse($request, $e, 500);
        }
    }

    /**
     * Remove the specified inventory item.
     */
    public function destroy(InventoryItem $inventory)
    {
        if ($inventory->quantity_in_stock > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete item with remaining stock. Please adjust stock to zero first.');
        }

        $inventory->delete();

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item deleted successfully.');
    }

    /**
     * Update stock for an inventory item.
     */
    /**
     * Update stock for an inventory item.
     */
    public function updateStock(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'type' => 'required|in:purchase,adjustment,return,damaged,lost',
            'quantity' => 'required|numeric|min:0.0001',
            'unit_cost' => 'required_if:type,purchase,adjustment|numeric|min:0|nullable',
            'notes' => 'nullable|string|max:1000',
            'branch_id' => [
                'nullable',
                Rule::exists('branches', 'id')->where('salon_id', auth()->user()->salon_id)
            ],
        ]);

        $quantity = (float) $validated['quantity'];
        $type = $validated['type'];
        $unitCost = $validated['unit_cost'] ?? $inventory->purchase_price;

        // Determine branch ID
        $branchId = auth()->user()->branch_id ?? ($validated['branch_id'] ?? null);

        // If no branch ID found (e.g. admin didn't select one), and item has only one branch, use that
        if (!$branchId && $inventory->branches()->count() === 1) {
            $branchId = $inventory->branches()->first()->id;
        }

        // Fallback: If still no branch ID, try to get the salon's default (first active) branch
        if (!$branchId) {
            $defaultBranch = \App\Models\Branch::where('salon_id', auth()->user()->salon_id)
                ->where('is_active', true)
                ->orderBy('id')
                ->first();

            if ($defaultBranch) {
                $branchId = $defaultBranch->id;
            }
        }

        // If still no branch ID and we have multiple branches assigned to this item, we need to know which one
        // (But if we found a default branch above, we are good to go even if multiple exist generally, 
        // though strictly speaking for accurate stock tracking per branch we should ask. 
        // However, to prevent the crash and "blocker", we'll use the fallback or error out gracefully if absolutely nothing found).
        if (!$branchId && $inventory->branches()->count() > 1) {
            return redirect()->back()
                ->with('error', 'Please select a branch to update stock for.');
        }

        // If absolutely no branch found (no default, no assignment), we can't track stock per branch.
        // But we should probably allow updating the global stock if that's the intention?
        // For now, let's ensure we don't crash.


        try {
            // Calculate the direction of the transaction (in/out)
            $direction = in_array($type, ['purchase', 'return']) ? 'in' : 'out';

            // For damaged/lost, we reduce stock but don't record a cost
            $totalCost = in_array($type, ['purchase', 'adjustment'])
                ? $unitCost * $quantity
                : null;

            DB::beginTransaction();

            // 1. Update Branch Stock (Pivot Table)
            if ($branchId) {
                $pivotRow = $inventory->branches()->where('branch_id', $branchId)->first();

                if (!$pivotRow) {
                    // If not assigned to this branch yet, attach it with 0 stock first
                    $inventory->branches()->attach($branchId, [
                        'quantity' => 0,
                        'minimum_quantity' => 0,
                        'is_active' => true
                    ]);
                    $pivotRow = $inventory->branches()->where('branch_id', $branchId)->first();
                }

                if ($pivotRow) {
                    $currentQty = $pivotRow->pivot->quantity;
                    $newQty = $direction === 'in' ? $currentQty + $quantity : $currentQty - $quantity;

                    $inventory->branches()->updateExistingPivot($branchId, [
                        'quantity' => $newQty
                    ]);
                } else {
                    // If branch not assigned, assign it with initial stock
                    // Only if direction is 'in' (can't remove from non-existent)
                    if ($direction === 'in') {
                        $inventory->branches()->attach($branchId, [
                            'quantity' => $quantity,
                            'minimum_quantity' => 0,
                            'is_active' => true
                        ]);
                    } else {
                        throw new \Exception('Cannot reduce stock for a branch that is not assigned to this item.');
                    }
                }
            }

            // 2. Update Global Stock (Cache)
            // Recalculate total from all branches to ensure consistency
            $totalStock = DB::table('branch_inventory_item')
                ->where('inventory_item_id', $inventory->id)
                ->sum('quantity');

            $inventory->update(['quantity_in_stock' => $totalStock]);

            // 3. Record transaction
            $inventory->transactions()->create([
                'branch_id' => $branchId,
                'transaction_type' => $type,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'notes' => $validated['notes'],
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Stock updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating stock', [
                'item_id' => $inventory->id,
                'type' => $type,
                'quantity' => $quantity,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to update stock: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified inventory item's transactions.
     */
    public function transactions(InventoryItem $inventory)
    {
        return redirect()->route('admin.inventory.index', ['search' => $inventory->sku]);
    }

    /**
     * Display low stock items.
     */
    public function lowStock()
    {
        return redirect()->route('admin.inventory.index', ['status' => 'low']);
    }

    /**
     * Display out of stock items.
     */
    public function outOfStock()
    {
        return redirect()->route('admin.inventory.index', ['status' => 'out']);
    }

    /**
     * Generate a unique SKU for a new item.
     */
    public function generateSku()
    {
        do {
            $sku = 'ITM-' . strtoupper(Str::random(8));
        } while (InventoryItem::where('sku', $sku)->exists());

        return response()->json(['sku' => $sku]);
    }

    /**
     * Generate a name-based SKU for a new item.
     */
    public function generateNameBasedSku(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $sku = InventoryItem::generateUniqueSku($request->name);

            return response()->json([
                'success' => true,
                'sku' => $sku
            ]);
        } catch (\Exception $e) {
            \Log::error('Error generating SKU', [
                'name' => $request->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate SKU: ' . $e->getMessage()
            ], 500);
        }
    }
}
