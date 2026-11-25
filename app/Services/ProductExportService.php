<?php

namespace App\Services;

use App\Models\Product;
use App\Models\InventoryItem;
use Illuminate\Support\Collection;

class ProductExportService
{
    /**
     * Export products/inventory items to array format.
     */
    public function export(int $salonId, ?int $branchId = null): array
    {
        // Check InventoryItem first as it is the comprehensive catalog
        $inventoryQuery = InventoryItem::where('salon_id', $salonId)->with(['category', 'branches']);
        if ($branchId) {
            $inventoryQuery->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhereHas('branches', function ($b) use ($branchId) {
                      $b->where('branches.id', $branchId);
                  });
            });
        }
        $inventoryItems = $inventoryQuery->get();

        // Check Product table
        $productsQuery = Product::where('salon_id', $salonId)->with(['category']);
        if ($branchId) {
            $productsQuery->where('branch_id', $branchId);
        }
        $products = $productsQuery->get();

        // If no products exist yet in the salon catalog, provide the template with sample items
        if ($inventoryItems->isEmpty() && $products->isEmpty()) {
            return self::getTemplate();
        }

        // Deduplicate: merge unmatched Product rows that are not already in InventoryItems (matched by SKU)
        $existingSkus = $inventoryItems->pluck('sku')->filter()->all();
        $unmatchedProducts = $products->filter(function ($p) use ($existingSkus) {
            return !in_array($p->sku, $existingSkus);
        });

        return $this->formatCombinedForExport($inventoryItems, $unmatchedProducts, $branchId);
    }

    /**
     * Format combined InventoryItem and unmatched Product collections for export
     */
    protected function formatCombinedForExport(Collection $items, Collection $unmatchedProducts, ?int $branchId = null): array
    {
        $data = [
            $this->getHeaders()
        ];

        foreach ($items as $item) {
            $stockQty = $item->quantity_in_stock;
            if ($branchId) {
                $branch = $item->branches->firstWhere('id', $branchId);
                if ($branch && isset($branch->pivot->quantity)) {
                    $stockQty = $branch->pivot->quantity;
                }
            }

            $data[] = [
                $item->name,
                $item->description ?? '',
                $item->sku,
                $item->barcode ?? '',
                $item->product_type ?? 'retail',
                $item->base_unit ?? $item->unit_type ?? 'pcs',
                $item->pack_capacity ?? 1,
                $item->selling_price,
                $item->purchase_price,
                $item->category->name ?? '',
                $item->brand ?? '',
                $stockQty,
                $item->minimum_quantity ?? $item->reorder_level ?? 10,
                $item->is_active ? 'yes' : 'no',
                $item->tax_rate ?? 0,
                $item->is_taxable ? 'yes' : 'no'
            ];
        }

        foreach ($unmatchedProducts as $product) {
            $data[] = [
                $product->name,
                $product->description ?? '',
                $product->sku,
                $product->barcode ?? '',
                'retail',
                'pcs',
                1,
                $product->price,
                $product->cost_price,
                $product->category->name ?? '',
                $product->brand ?? '',
                $product->stock_quantity,
                $product->low_stock_threshold,
                $product->is_active ? 'yes' : 'no',
                $product->tax_rate,
                $product->is_taxable ? 'yes' : 'no'
            ];
        }

        return $data;
    }

    /**
     * Format Product collection for export
     */
    protected function formatProductsForExport(Collection $products): array
    {
        $data = [
            $this->getHeaders()
        ];

        foreach ($products as $product) {
            $data[] = [
                $product->name,
                $product->description ?? '',
                $product->sku,
                $product->barcode ?? '',
                'retail',
                'pcs',
                1,
                $product->price,
                $product->cost_price,
                $product->category->name ?? '',
                $product->brand ?? '',
                $product->stock_quantity,
                $product->low_stock_threshold,
                $product->is_active ? 'yes' : 'no',
                $product->tax_rate,
                $product->is_taxable ? 'yes' : 'no'
            ];
        }

        return $data;
    }

    /**
     * Get standardized CSV headers
     */
    public static function getHeaders(): array
    {
        return [
            'Name',
            'Description',
            'SKU',
            'Barcode',
            'Product Type',
            'Base Unit',
            'Pack Capacity',
            'Price',
            'Cost Price',
            'Category',
            'Brand',
            'Stock Quantity',
            'Low Stock Threshold',
            'Is Active',
            'Tax Rate',
            'Is Taxable'
        ];
    }

    /**
     * Get template structure with diverse examples (Retail, Consumable Backbar, Dual Use)
     */
    public static function getTemplate(): array
    {
        return [
            self::getHeaders(),
            [
                'Styling Clay Matte (Retail Example)',
                'High hold matte styling pomade for retail shelf sale',
                'CLAY-001',
                '123456789012',
                'retail',
                'pcs',
                '1',
                '25.00',
                '12.00',
                'Styling',
                'Crown & Blade',
                '50',
                '10',
                'yes',
                '0',
                'no'
            ],
            [
                'Developer 20 Vol 1000ml (Backbar Consumable Example)',
                'Salon backbar oxidant developer for hair coloring recipes',
                'DEV-20V-1L',
                '123456789013',
                'consumable',
                'ml',
                '1000',
                '0.00',
                '18.00',
                'Coloring',
                'L\'Oreal',
                '5000',
                '500',
                'yes',
                '0',
                'no'
            ],
            [
                'Argan Hair Serum 100ml (Dual-Use Example)',
                'Sold at retail counter AND dispensed as service finishing oil',
                'SERUM-100ML',
                '123456789014',
                'dual',
                'ml',
                '100',
                '35.00',
                '14.00',
                'Hair Care',
                'Moroccanoil',
                '400',
                '50',
                'yes',
                '0',
                'no'
            ]
        ];
    }
}
