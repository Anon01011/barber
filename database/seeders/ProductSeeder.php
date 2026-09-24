<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Salon;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Find the demo salon
            $salon = Salon::where('slug', 'demo-salon')->first();
            if (!$salon) {
                Log::warning('Demo salon not found. Skipping product seeding.');
                return;
            }

            // Find or create the category "NEW TEST"
            $category = InventoryCategory::firstOrCreate(
                ['name' => 'NEW TEST', 'salon_id' => $salon->id],
                ['description' => 'Test Category', 'status' => 'active']
            );

            Log::info('Seeding inventory items and products for demo salon: ' . $salon->name);

            // Sample salon inventory items data
            $inventoryItems = [
                [
                    'name' => 'Professional Shampoo',
                    'description' => 'High-quality shampoo for all hair types',
                    'sku' => 'SHAM-001',
                    'barcode' => '1234567890123',
                    'purchase_price' => 15.00,
                    'selling_price' => 25.99,
                    'quantity_in_stock' => 50,
                    'minimum_quantity' => 10,
                    'reorder_level' => 10,
                    'unit_type' => 'pcs',
                    'brand' => 'SalonPro',
                    'is_active' => true,
                    'is_taxable' => true,
                    'tax_rate' => 5.00,
                ],
                [
                    'name' => 'Conditioner Deep Repair',
                    'description' => 'Intensive conditioner for damaged hair',
                    'sku' => 'COND-001',
                    'barcode' => '1234567890124',
                    'purchase_price' => 17.00,
                    'selling_price' => 28.50,
                    'quantity_in_stock' => 40,
                    'minimum_quantity' => 8,
                    'reorder_level' => 8,
                    'unit_type' => 'pcs',
                    'brand' => 'SalonPro',
                    'is_active' => true,
                    'is_taxable' => true,
                    'tax_rate' => 5.00,
                ],
                [
                    'name' => 'Hair Color Natural Black',
                    'description' => 'Permanent hair color in natural black shade',
                    'sku' => 'HCOL-001',
                    'barcode' => '1234567890125',
                    'purchase_price' => 25.00,
                    'selling_price' => 45.00,
                    'quantity_in_stock' => 30,
                    'minimum_quantity' => 5,
                    'reorder_level' => 5,
                    'unit_type' => 'pcs',
                    'brand' => 'ColorTech',
                    'is_active' => true,
                    'is_taxable' => true,
                    'tax_rate' => 5.00,
                ],
                [
                    'name' => 'Styling Gel Strong Hold',
                    'description' => 'Strong hold gel for all-day styling',
                    'sku' => 'GEL-001',
                    'barcode' => '1234567890126',
                    'purchase_price' => 10.50,
                    'selling_price' => 18.99,
                    'quantity_in_stock' => 60,
                    'minimum_quantity' => 15,
                    'reorder_level' => 15,
                    'unit_type' => 'pcs',
                    'brand' => 'StyleMaster',
                    'is_active' => true,
                    'is_taxable' => true,
                    'tax_rate' => 5.00,
                ],
                [
                    'name' => 'Scalp Treatment Serum',
                    'description' => 'Therapeutic serum for scalp health',
                    'sku' => 'SERM-001',
                    'barcode' => '1234567890127',
                    'purchase_price' => 20.00,
                    'selling_price' => 35.00,
                    'quantity_in_stock' => 25,
                    'minimum_quantity' => 5,
                    'reorder_level' => 5,
                    'unit_type' => 'pcs',
                    'brand' => 'HealthHair',
                    'is_active' => true,
                    'is_taxable' => true,
                    'tax_rate' => 5.00,
                ],
            ];

            // Create inventory items and sync with products
            foreach ($inventoryItems as $itemData) {
                $inventoryItem = InventoryItem::create([
                    'salon_id' => $salon->id,
                    'branch_id' => null, // Default to main branch or null
                    'category_id' => $category->id,
                    'supplier_id' => null, // No supplier for demo
                    'name' => $itemData['name'],
                    'slug' => \Illuminate\Support\Str::slug($itemData['name']),
                    'sku' => $itemData['sku'],
                    'barcode' => $itemData['barcode'],
                    'description' => $itemData['description'],
                    'purchase_price' => $itemData['purchase_price'],
                    'selling_price' => $itemData['selling_price'],
                    'quantity_in_stock' => $itemData['quantity_in_stock'],
                    'minimum_quantity' => $itemData['minimum_quantity'],
                    'reorder_level' => $itemData['reorder_level'],
                    'unit_type' => $itemData['unit_type'],
                    'location' => null,
                    'image_path' => null,
                    'notes' => null,
                    'tax_rate' => $itemData['tax_rate'],
                    'is_taxable' => $itemData['is_taxable'],
                    'weight' => null,
                    'dimensions' => null,
                    'expiry_date' => null,
                    'manufacturer' => null,
                    'brand' => $itemData['brand'],
                    'is_active' => $itemData['is_active'],
                ]);

                // Sync with Product model (for POS) - same as controller logic
                Product::create([
                    'salon_id' => $inventoryItem->salon_id,
                    'branch_id' => $inventoryItem->branch_id,
                    'name' => $inventoryItem->name,
                    'description' => $inventoryItem->description,
                    'sku' => $inventoryItem->sku,
                    'barcode' => $inventoryItem->barcode,
                    'price' => $inventoryItem->selling_price,
                    'cost_price' => $inventoryItem->purchase_price,
                    'category_id' => $inventoryItem->category_id,
                    'brand' => $inventoryItem->brand,
                    'stock_quantity' => $inventoryItem->quantity_in_stock,
                    'low_stock_threshold' => $inventoryItem->minimum_quantity,
                    'is_active' => $inventoryItem->is_active,
                    'image_path' => $inventoryItem->image_path,
                    'tax_rate' => $inventoryItem->tax_rate,
                    'is_taxable' => $inventoryItem->is_taxable,
                ]);
            }

            Log::info('Successfully seeded 5 inventory items and products for demo salon using category "NEW TEST".');

        } catch (\Exception $e) {
            Log::error('Product seeding failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
