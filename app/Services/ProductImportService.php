<?php

namespace App\Services;

use App\Models\Product;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class ProductImportService
{
    protected $results = [
        'success' => 0,
        'failed' => 0,
        'errors' => []
    ];

    /**
     * Import products from array data
     */
    public function import(array $data, int $salonId, ?int $branchId = null): array
    {
        DB::beginTransaction();
        
        try {
            foreach ($data as $index => $row) {
                $rowNumber = $index + 2; // +2 because index starts at 0 and row 1 is header
                
                try {
                    $this->importRow($row, $salonId, $branchId, $rowNumber);
                    $this->results['success']++;
                } catch (Exception $e) {
                    $this->results['failed']++;
                    $this->results['errors'][] = [
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
        
        return $this->results;
    }

    /**
     * Import a single row
     */
    protected function importRow(array $row, int $salonId, ?int $branchId, int $rowNumber): void
    {
        // Validate required fields
        $validator = Validator::make($row, [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            throw new Exception("Validation failed: " . implode(', ', $validator->errors()->all()));
        }

        // Check for duplicate SKU in Product and InventoryItem tables
        $existingProduct = Product::withoutGlobalScope('branch')
            ->where('salon_id', $salonId)
            ->where('sku', $row['sku'])
            ->first();

        $inventoryItem = InventoryItem::withoutGlobalScope('branch')
            ->where('salon_id', $salonId)
            ->where('sku', $row['sku'])
            ->first();

        $isUpdate = ($existingProduct !== null || $inventoryItem !== null);

        // Parse Column Presences
        $hasProductTypeCol = array_key_exists('product_type', $row) || array_key_exists('product type', $row);
        $hasBaseUnitCol = array_key_exists('base_unit', $row) || array_key_exists('base unit', $row) || array_key_exists('unit', $row);
        $hasUnitTypeCol = array_key_exists('unit_type', $row) || array_key_exists('unit type', $row);
        $hasPackCapacityCol = array_key_exists('pack_capacity', $row) || array_key_exists('pack capacity', $row);
        $hasPriceCol = array_key_exists('price', $row) && $row['price'] !== '' && $row['price'] !== null;

        // Parse Product Classification
        if ($isUpdate && !$hasProductTypeCol) {
            $productType = $inventoryItem?->product_type ?? 'retail';
        } else {
            $rawType = strtolower(trim($row['product_type'] ?? $row['product type'] ?? 'retail'));
            $productType = in_array($rawType, ['retail', 'consumable', 'dual', 'backbar'])
                ? ($rawType === 'backbar' ? 'consumable' : $rawType)
                : 'retail';
        }

        // Parse Base Unit & Unit Type
        if ($isUpdate && !$hasBaseUnitCol) {
            $baseUnit = $inventoryItem?->base_unit ?? 'pcs';
        } else {
            $baseUnit = strtolower(trim($row['base_unit'] ?? $row['base unit'] ?? $row['unit'] ?? 'pcs'));
            if (empty($baseUnit)) $baseUnit = 'pcs';
        }

        if ($hasUnitTypeCol && !empty(trim($row['unit_type'] ?? $row['unit type'] ?? ''))) {
            $unitType = strtolower(trim($row['unit_type'] ?? $row['unit type']));
        } elseif ($isUpdate && $inventoryItem?->unit_type) {
            $unitType = $inventoryItem->unit_type;
        } else {
            $unitType = $baseUnit;
        }

        // Parse Pack Capacity
        if ($isUpdate && (!$hasPackCapacityCol || $row['pack_capacity'] === '' || $row['pack_capacity'] === null)) {
            $packCapacity = (float) ($inventoryItem?->pack_capacity ?? 1);
        } else {
            $packCapacity = (float) ($row['pack_capacity'] ?? $row['pack capacity'] ?? 1);
            if ($packCapacity <= 0) $packCapacity = 1;
        }

        // Validate Price Requirements
        if (!$isUpdate && in_array($productType, ['retail', 'dual']) && !$hasPriceCol) {
            throw new Exception("Validation failed: Price is required for new {$productType} products.");
        }

        // Determine price
        if ($hasPriceCol && is_numeric($row['price'])) {
            $sellingPrice = (float) $row['price'];
        } elseif ($isUpdate) {
            $sellingPrice = $inventoryItem?->selling_price ?? $existingProduct?->price ?? 0.00;
        } else {
            $sellingPrice = 0.00;
        }

        // Get or create category if provided
        $categoryId = null;
        if (!empty($row['category'])) {
            $category = InventoryCategory::where('salon_id', $salonId)
                ->where('name', $row['category'])
                ->first();
            
            if ($category) {
                $categoryId = $category->id;
            }
        }

        // Prepare product data
        $productData = [
            'salon_id' => $salonId,
            'branch_id' => $branchId,
            'name' => $row['name'],
            'description' => $row['description'] ?? null,
            'sku' => $row['sku'],
            'barcode' => $row['barcode'] ?? null,
            'price' => $sellingPrice,
            'cost_price' => $row['cost_price'] ?? 0,
            'category_id' => $categoryId,
            'brand' => $row['brand'] ?? null,
            'stock_quantity' => $row['stock_quantity'] ?? 0,
            'low_stock_threshold' => $row['low_stock_threshold'] ?? 10,
            'is_active' => $this->parseBooleanValue($row['is_active'] ?? 'yes'),
            'tax_rate' => $row['tax_rate'] ?? 0,
            'is_taxable' => $this->parseBooleanValue($row['is_taxable'] ?? 'no'),
        ];

        try {
            if ($existingProduct) {
                // Update existing product
                $existingProduct->update($productData);
            } else {
                // Create new product
                Product::create($productData);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database errors with user-friendly messages
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                throw new Exception("Duplicate SKU '{$row['sku']}' - Product already exists in the system");
            }
            throw new Exception("Database error: " . $e->getMessage());
        }

        // Handle InventoryItem (for Inventory List)
        $inventoryData = [
            'salon_id' => $salonId,
            'branch_id' => $branchId,
            'name' => $row['name'],
            'description' => $row['description'] ?? null,
            'sku' => $row['sku'],
            'barcode' => $row['barcode'] ?? null,
            'selling_price' => $sellingPrice,
            'purchase_price' => $row['cost_price'] ?? 0,
            'category_id' => $categoryId,
            'brand' => $row['brand'] ?? null,
            'quantity_in_stock' => $row['stock_quantity'] ?? 0,
            'minimum_quantity' => $row['low_stock_threshold'] ?? 10,
            'reorder_level' => $row['low_stock_threshold'] ?? 10, // Sync reorder level with low stock threshold
            'is_active' => $this->parseBooleanValue($row['is_active'] ?? 'yes'),
            'tax_rate' => $row['tax_rate'] ?? 0,
            'is_taxable' => $this->parseBooleanValue($row['is_taxable'] ?? 'no'),
            'unit_type' => $unitType,
            'base_unit' => $baseUnit,
            'product_type' => $productType,
            'pack_capacity' => $packCapacity,
        ];

        try {
            if ($inventoryItem) {
                $inventoryItem->update($inventoryData);
            } else {
                $inventoryItem = InventoryItem::create($inventoryData);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error("Failed to sync inventory item for SKU {$row['sku']}: " . $e->getMessage());
            throw new \Exception("Failed to sync inventory item for SKU {$row['sku']}: " . $e->getMessage());
        }

        // Handle Branch Stock if branch_id is provided
        if ($branchId && $inventoryItem) {
            try {
                $inventoryItem->branches()->syncWithoutDetaching([
                    $branchId => [
                        'quantity' => $row['stock_quantity'] ?? 0,
                        'minimum_quantity' => $row['low_stock_threshold'] ?? 0,
                        'is_active' => true
                    ]
                ]);
            } catch (\Exception $e) {
                \Log::error("Failed to sync branch stock for SKU {$row['sku']}: " . $e->getMessage());
            }
        }
    }

    /**
     * Parse boolean values from CSV
     */
    protected function parseBooleanValue($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        
        $value = strtolower(trim($value));
        return in_array($value, ['yes', 'true', '1', 'active']);
    }

    /**
     * Get import results
     */
    public function getResults(): array
    {
        return $this->results;
    }
}
