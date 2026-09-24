<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockAlert;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAlertController extends Controller
{
    /**
     * Display a listing of stock alerts.
     */
    public function index(Request $request)
    {
        $query = StockAlert::with(['inventoryItem.category', 'resolver'])
            ->when($request->status === 'resolved', fn($q) => $q->resolved())
            ->when($request->status === 'unresolved', fn($q) => $q->unresolved())
            ->when($request->type, fn($q, $type) => $q->ofType($type))
            ->when($request->severity, fn($q, $severity) => $q->bySeverity($severity))
            ->latest();

        $alerts = $query->paginate(20);

        $stats = [
            'total' => StockAlert::count(),
            'unresolved' => StockAlert::unresolved()->count(),
            'critical' => StockAlert::unresolved()->bySeverity('critical')->count(),
            'low_stock' => StockAlert::unresolved()->ofType('low_stock')->count(),
            'out_of_stock' => StockAlert::unresolved()->ofType('out_of_stock')->count(),
            'expiring_soon' => StockAlert::unresolved()->ofType('expiring_soon')->count(),
        ];

        return view('inventory.alerts.index', compact('alerts', 'stats'));
    }

    /**
     * Resolve a stock alert.
     */
    public function resolve(Request $request, StockAlert $alert)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000'
        ]);

        $alert->resolve($validated['notes'] ?? null);

        return back()->with('success', 'Alert resolved successfully.');
    }

    /**
     * Generate stock alerts for all items.
     */
    public function generate()
    {
        DB::beginTransaction();
        
        try {
            $generated = 0;
            
            // Get all active inventory items
            $items = InventoryItem::active()->with('variants')->get();
            
            foreach ($items as $item) {
                // Check for out of stock
                if ($item->quantity_in_stock <= 0) {
                    $this->createAlertIfNotExists($item, StockAlert::TYPE_OUT_OF_STOCK, StockAlert::SEVERITY_CRITICAL);
                    $generated++;
                }
                // Check for low stock
                elseif (($item->reorder_level > 0 && $item->quantity_in_stock <= $item->reorder_level) || 
                        ($item->minimum_quantity > 0 && $item->quantity_in_stock <= $item->minimum_quantity)) {
                    $this->createAlertIfNotExists($item, StockAlert::TYPE_LOW_STOCK, StockAlert::SEVERITY_WARNING);
                    $generated++;
                }
                
                // Check for expiring items
                if ($item->expiry_date && \Carbon\Carbon::parse($item->expiry_date)->lte(now()->addDays(30))) {
                    $this->createAlertIfNotExists($item, StockAlert::TYPE_EXPIRING_SOON, StockAlert::SEVERITY_INFO);
                    $generated++;
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Generated {$generated} alert(s)",
                'count' => $generated
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate alerts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create alert if it doesn't already exist.
     */
    protected function createAlertIfNotExists(InventoryItem $item, string $type, string $severity): void
    {
        // Check if unresolved alert already exists
        $exists = StockAlert::where('inventory_item_id', $item->id)
            ->where('type', $type)
            ->where('is_resolved', false)
            ->exists();
            
        if ($exists) {
            return;
        }
        
        $messages = [
            StockAlert::TYPE_OUT_OF_STOCK => "{$item->name} is out of stock",
            StockAlert::TYPE_LOW_STOCK => "{$item->name} is low on stock (Current: {$item->quantity_in_stock}, Threshold: " . ($item->reorder_level > 0 ? $item->reorder_level : $item->minimum_quantity) . ")",
            StockAlert::TYPE_EXPIRING_SOON => "{$item->name} expires on " . \Carbon\Carbon::parse($item->expiry_date)->format('Y-m-d'),
        ];
        
        StockAlert::create([
            'inventory_item_id' => $item->id,
            'type' => $type,
            'message' => $messages[$type],
            'severity' => $severity,
        ]);
    }

    /**
     * Delete resolved alerts older than 30 days.
     */
    public function cleanup()
    {
        $deleted = StockAlert::resolved()
            ->where('resolved_at', '<', now()->subDays(30))
            ->delete();
            
        return response()->json([
            'success' => true,
            'message' => "Deleted {$deleted} old alert(s)",
            'count' => $deleted
        ]);
    }
}
