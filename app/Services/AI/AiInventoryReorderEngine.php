<?php

namespace App\Services\AI;

use App\Models\Salon;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;

class AiInventoryReorderEngine
{
    public function generateDraftPurchaseOrder(Salon $salon): array
    {
        return $this->generateAutomatedPurchaseOrders($salon);
    }

    /**
     * Generate automated purchase order draft for low-stock inventory items.
     */
    public function generateAutomatedPurchaseOrders(Salon $salon): array
    {
        $lowStockItems = InventoryItem::where('salon_id', $salon->id)
            ->where('is_active', true)
            ->whereColumn('quantity_in_stock', '<=', 'reorder_level')
            ->get();

        if ($lowStockItems->isEmpty()) {
            return [
                'status' => 'info',
                'message' => 'Zero low-stock items detected. Stock levels are optimal.',
                'generated_orders_count' => 0,
                'items' => [],
            ];
        }

        $poDrafts = [];
        $totalEstimatedCost = 0;

        foreach ($lowStockItems as $item) {
            // Recommend quantity: (Reorder Level * 2) - current stock
            $targetStock = max(10, (int)$item->reorder_level * 2);
            $recommendedReorderQty = max(1, $targetStock - (int)$item->quantity_in_stock);
            $unitCost = (float) ($item->purchase_price ?? $item->cost_price ?? $item->selling_price ?? 10);
            $estimatedCost = $recommendedReorderQty * $unitCost;

            $totalEstimatedCost += $estimatedCost;

            $supplierName = 'Primary Vendor';
            if ($item->supplier) {
                $supplierName = is_object($item->supplier) ? ($item->supplier->name ?? 'Primary Vendor') : (string) $item->supplier;
            }

            $poDrafts[] = [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'sku' => $item->sku ?? 'N/A',
                'current_stock' => $item->quantity_in_stock,
                'reorder_level' => $item->reorder_level,
                'recommended_reorder_qty' => $recommendedReorderQty,
                'unit_cost' => $unitCost,
                'estimated_total_cost' => round($estimatedCost, 2),
                'supplier_name' => $supplierName,
            ];
        }

        return [
            'status' => 'success',
            'message' => "Generated AI Purchase Order Draft for " . count($poDrafts) . " low-stock inventory items.",
            'generated_orders_count' => count($poDrafts),
            'total_estimated_cost' => round($totalEstimatedCost, 2),
            'items' => $poDrafts,
        ];
    }
}
