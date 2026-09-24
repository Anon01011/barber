<?php

namespace App\Observers;

use App\Models\PosSale;
use App\Services\CommissionService;

class PosSaleObserver
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    /**
     * Handle the PosSale "updated" event.
     */
    public function updated(PosSale $posSale): void
    {
        // Only calculate commission when sale is paid
        if ($posSale->payment_status === 'paid') {
            try {
                // Force reload items to ensure we have the latest data including those just added in transaction
                $posSale->unsetRelation('items');
                $posSale->load('items');

                // Handle Item Commissions
                foreach ($posSale->items as $item) {
                    // Skip if no staff assigned to item
                    if (!$item->staff_id) {
                        continue;
                    }

                    // Check if commission already exists for this item
                    $exists = \App\Models\StaffCommission::where('pos_sale_id', $posSale->id)
                        ->where('item_id', $item->item_id)
                        ->where('item_type', $this->mapItemType($item->item_type))
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    // Determine item type and ID based on polymorphic relation
                    $itemType = $this->mapItemType($item->item_type);
                    $itemId = $item->item_id;

                    if ($itemType) {
                        $this->commissionService->calculatePosSaleCommission(
                            $posSale,
                            $itemType,
                            $itemId,
                            $item->staff_id,
                            $item->subtotal
                        );
                    }
                }

                // Note: Tip commissions are now handled in PosController during sale creation
                // to ensure proper proportional distribution based on item count
            } catch (\Exception $e) {
                \Log::error('Error calculating POS commission: ' . $e->getMessage(), [
                    'pos_sale_id' => $posSale->id,
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    }

    private function mapItemType($modelClass)
    {
        if ($modelClass === 'App\Models\Service') return 'service';
        if ($modelClass === 'App\Models\InventoryItem') return 'product';
        if ($modelClass === 'App\Models\Membership') return 'membership';
        if ($modelClass === 'App\Models\Package') return 'package';
        return null;
    }
}
