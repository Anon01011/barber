<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class ServiceImportService
{
    /**
     * Import services and categories from array data
     */
    public function import(array $data, int $salonId): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        DB::beginTransaction();

        try {
            foreach ($data as $index => $row) {
                $rowNumber = $index + 2; // +2 because index starts at 0 and row 1 is header

                try {
                    $this->importRow($row, $salonId, $rowNumber);
                    $results['success']++;
                } catch (Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'row' => $rowNumber,
                        'message' => $e->getMessage()
                    ];
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $results;
    }

    /**
     * Import a single row
     */
    protected function importRow(array $row, int $salonId, int $rowNumber): void
    {
        // Normalize keys (handle spaces, parentheses, and case)
        $normalizedRow = [];
        foreach ($row as $key => $value) {
            $cleanKey = strtolower(trim($key));
            $cleanKey = str_replace([' ', '(', ')', '-'], '_', $cleanKey);
            $cleanKey = preg_replace('/__+/', '_', $cleanKey);
            $cleanKey = trim($cleanKey, '_');
            $normalizedRow[$cleanKey] = $value;
        }

        // Validate required fields
        $validator = Validator::make($normalizedRow, [
            'category_name' => 'required|string|max:255',
            'service_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_mins' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            throw new Exception("Validation failed: " . implode(', ', $validator->errors()->all()));
        }

        // 1. Find or create category
        $category = ServiceCategory::where('salon_id', $salonId)
            ->where('name', $normalizedRow['category_name'])
            ->first();

        $salon = \App\Models\Salon::find($salonId);

        if (!$category) {
            if (!$salon->canAddServiceCategory()) {
                throw new Exception("Limit reached: You cannot add more service categories under your current plan.");
            }

            $category = ServiceCategory::create([
                'salon_id' => $salonId,
                'name' => $normalizedRow['category_name'],
                'description' => $normalizedRow['category_description'] ?? null,
                'status' => 'active',
            ]);
        }

        // 2. Check if service already exists in this category
        $existingService = Service::where('salon_id', $salonId)
            ->where('category_id', $category->id)
            ->where('name', $normalizedRow['service_name'])
            ->first();

        $serviceData = [
            'salon_id' => $salonId,
            'category_id' => $category->id,
            'name' => $normalizedRow['service_name'],
            'description' => $normalizedRow['service_description'] ?? null,
            'price' => $normalizedRow['price'],
            'duration' => $normalizedRow['duration_mins'],
            'status' => $this->parseStatus($normalizedRow['status'] ?? 'active'),
            'available_for_online_booking' => $this->parseBoolean($normalizedRow['online_booking'] ?? 'yes'),
        ];

        if ($existingService) {
            $existingService->update($serviceData);
            $service = $existingService;
        } else {
            if (!$salon->canAddService()) {
                throw new Exception("Limit reached: You cannot add more services under your current plan.");
            }
            $service = Service::create($serviceData);
        }

        // 3. Sync Assigned Products / Recipe Consumables if column is present
        $recipeKey = null;
        foreach ($normalizedRow as $k => $v) {
            if (str_contains($k, 'assigned_product') || str_contains($k, 'assigned_consumable') || str_contains($k, 'recipe') || str_contains($k, 'consumable')) {
                $recipeKey = $k;
                break;
            }
        }

        if ($recipeKey && !empty(trim($normalizedRow[$recipeKey] ?? ''))) {
            $rawRecipe = trim($normalizedRow[$recipeKey]);
            $recipeItems = array_filter(array_map('trim', preg_split('/[|;]+/', $rawRecipe)));

            foreach ($recipeItems as $itemStr) {
                if (empty($itemStr)) continue;
                $parts = array_map('trim', explode(':', $itemStr));
                $skuOrName = $parts[0] ?? '';
                if (empty($skuOrName)) continue;

                $qty = isset($parts[1]) && is_numeric($parts[1]) ? (float)$parts[1] : 1.0;
                $unit = !empty($parts[2]) ? $parts[2] : null;
                $chargeableStr = strtolower(trim($parts[3] ?? 'no'));
                $isBillable = in_array($chargeableStr, ['yes', 'true', '1', 'charge', 'chargeable']);
                $extraPrice = isset($parts[4]) && is_numeric($parts[4]) ? (float)$parts[4] : 0.00;

                $invItem = \App\Models\InventoryItem::withoutGlobalScope('branch')
                    ->where('salon_id', $salonId)
                    ->where(function ($q) use ($skuOrName) {
                        $q->where('sku', $skuOrName)
                          ->orWhere('name', $skuOrName);
                    })
                    ->first();

                if ($invItem) {
                    if (empty($unit)) {
                        $unit = $invItem->base_unit ?? $invItem->unit_type ?? 'pcs';
                    }

                    if ($isBillable && $extraPrice <= 0) {
                        $capacity = (float)($invItem->pack_capacity ?? 1);
                        if ($capacity <= 0) $capacity = 1;
                        $basePrice = (float)($invItem->selling_price > 0 ? $invItem->selling_price : $invItem->purchase_price);
                        $extraPrice = round($basePrice / $capacity, 4);
                    }

                    \App\Models\ServiceInventoryItem::updateOrCreate([
                        'salon_id' => $salonId,
                        'service_id' => $service->id,
                        'inventory_item_id' => $invItem->id,
                    ], [
                        'quantity' => $qty,
                        'unit' => $unit,
                        'is_optional' => false,
                        'is_billable_extra' => $isBillable,
                        'extra_price_per_unit' => $extraPrice,
                    ]);
                }
            }
        }
    }

    /**
     * Parse status value
     */
    protected function parseStatus($value): string
    {
        $value = strtolower(trim($value));
        return in_array($value, ['active', 'inactive']) ? $value : 'active';
    }

    /**
     * Parse boolean values
     */
    protected function parseBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim($value));
        return in_array($value, ['yes', 'true', '1', 'active']);
    }

    /**
     * Get template headers and sample rows demonstrating assigned products/recipe consumables.
     */
    public static function getTemplateHeaders(): array
    {
        return [
            [
                'Category Name',
                'Category Description',
                'Service Name',
                'Service Description',
                'Price',
                'Duration (mins)',
                'Status',
                'Online Booking',
                'Assigned Products (Recipe: SKU:Qty:Unit:Chargeable:ExtraPrice)'
            ],
            [
                'Hair Care',
                'Treatments and styling',
                'Deep Conditioning Treatment',
                'Hydrating hair mask and scalp massage',
                '35.00',
                '45',
                'active',
                'yes',
                'SERUM-100ML:15:ml:yes:5.00'
            ],
            [
                'Hair Coloring',
                'Professional coloring services',
                'Full Hair Color',
                'Permanent hair coloring with backbar developer',
                '80.00',
                '90',
                'active',
                'yes',
                'DEV-20V-1L:60:ml:no:0 | SERUM-100ML:10:ml:yes:5.00'
            ],
            [
                'Haircut',
                'All types of haircuts',
                'Men\'s Haircut',
                'Standard men\'s haircut and finish',
                '25.00',
                '30',
                'active',
                'yes',
                'CLAY-001:1:pcs:no:0'
            ],
        ];
    }
}
