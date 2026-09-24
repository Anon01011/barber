<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class ProductExportService
{
    /**
     * Export products to array format
     */
    public function export(int $salonId, ?int $branchId = null): array
    {
        $query = Product::where('salon_id', $salonId)
            ->with(['category']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $products = $query->get();

        return $this->formatForExport($products);
    }

    /**
     * Format products for export
     */
    protected function formatForExport(Collection $products): array
    {
        $data = [];

        // Add header row
        $data[] = [
            'Name',
            'Description',
            'SKU',
            'Barcode',
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

        // Add product rows
        foreach ($products as $product) {
            $data[] = [
                $product->name,
                $product->description ?? '',
                $product->sku,
                $product->barcode ?? '',
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
     * Get template structure
     */
    public static function getTemplate(): array
    {
        return [
            [
                'Name',
                'Description',
                'SKU',
                'Barcode',
                'Price',
                'Cost Price',
                'Category',
                'Brand',
                'Stock Quantity',
                'Low Stock Threshold',
                'Is Active',
                'Tax Rate',
                'Is Taxable'
            ],
            [
                'Sample Product',
                'This is a sample product description',
                'PROD-001',
                '1234567890',
                '99.99',
                '50.00',
                'Hair Care',
                'Sample Brand',
                '100',
                '10',
                'yes',
                '0',
                'no'
            ]
        ];
    }
}
