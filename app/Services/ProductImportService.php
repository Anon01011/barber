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
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            throw new Exception("Validation failed: " . implode(', ', $validator->errors()->all()));
        }

        // Check for duplicate SKU in Product table
        $existingProduct = Product::withoutGlobalScope('branch')
            ->where('salon_id', $salonId)
            ->where('sku', $row['sku'])
            ->first();

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
            'price' => $row['price'],
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
        $inventoryItem = InventoryItem::withoutGlobalScope('branch')
            ->where('salon_id', $salonId)
            ->where('sku', $row['sku'])
            ->first();

        $inventoryData = [
            'salon_id' => $salonId,
            'branch_id' => $branchId,
            'name' => $row['name'],
            'description' => $row['description'] ?? null,
            'sku' => $row['sku'],
            'barcode' => $row['barcode'] ?? null,
            'selling_price' => $row['price'],
            'purchase_price' => $row['cost_price'] ?? 0,
            'category_id' => $categoryId,
            'brand' => $row['brand'] ?? null,
            'quantity_in_stock' => $row['stock_quantity'] ?? 0,
            'minimum_quantity' => $row['low_stock_threshold'] ?? 10,
            'reorder_level' => $row['low_stock_threshold'] ?? 10, // Sync reorder level with low stock threshold
            'is_active' => $this->parseBooleanValue($row['is_active'] ?? 'yes'),
            'tax_rate' => $row['tax_rate'] ?? 0,
            'is_taxable' => $this->parseBooleanValue($row['is_taxable'] ?? 'no'),
            'unit_type' => 'pcs', // Default unit type
        ];

        try {
            if ($inventoryItem) {
                $inventoryItem->update($inventoryData);
            } else {
                InventoryItem::create($inventoryData);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // Report inventory item errors
            $this->results['failed']++;
            $this->results['errors'][] = [
                'row' => $rowNumber,
                'message' => "Failed to sync inventory item for SKU {$row['sku']}: " . $e->getMessage()
            ];
            \Log::error("Failed to sync inventory item for SKU {$row['sku']}: " . $e->getMessage());
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
