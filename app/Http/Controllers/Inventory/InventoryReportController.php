<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryReportController extends Controller
{
    /**
     * Display inventory reports dashboard.
     */
    public function index()
    {
        return view('inventory.reports.index');
    }

    /**
     * Stock valuation report.
     */
    public function stockValuation(Request $request)
    {
        $items = InventoryItem::with('category')
            ->active()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'category' => $item->category->name ?? 'N/A',
                    'quantity' => $item->quantity_in_stock,
                    'unit_type' => $item->unit_type,
                    'purchase_price' => $item->purchase_price,
                    'selling_price' => $item->selling_price,
                    'purchase_value' => $item->quantity_in_stock * $item->purchase_price,
                    'selling_value' => $item->quantity_in_stock * $item->selling_price,
                    'potential_profit' => ($item->selling_price - $item->purchase_price) * $item->quantity_in_stock,
                ];
            });

        $totals = [
            'total_items' => $items->count(),
            'total_quantity' => $items->sum('quantity'),
            'total_purchase_value' => $items->sum('purchase_value'),
            'total_selling_value' => $items->sum('selling_value'),
            'total_potential_profit' => $items->sum('potential_profit'),
        ];

        return view('inventory.reports.stock-valuation', compact('items', 'totals'));
    }

    /**
     * Stock movement report.
     */
    public function stockMovement(Request $request)
    {
        $startDate = $request->start_date ?? now()->subDays(30)->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');

        $transactions = InventoryTransaction::with(['item.category', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->paginate(50);

        $stats = [
            'total_in' => InventoryTransaction::whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('type', ['purchase', 'return'])
                ->sum('quantity'),
            'total_out' => InventoryTransaction::whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('type', ['adjustment', 'damaged', 'lost'])
                ->sum('quantity'),
            'total_value' => InventoryTransaction::whereBetween('created_at', [$startDate, $endDate])
                ->sum('total_cost'),
        ];

        return view('inventory.reports.stock-movement', compact('transactions', 'stats', 'startDate', 'endDate'));
    }

    /**
     * Dead stock report (items with no movement).
     */
    public function deadStock(Request $request)
    {
        $days = $request->days ?? 90;

        $items = InventoryItem::with('category')
            ->active()
            ->whereDoesntHave('transactions', function ($query) use ($days) {
                $query->where('created_at', '>=', now()->subDays($days));
            })
            ->where('quantity_in_stock', '>', 0)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'category' => $item->category->name ?? 'N/A',
                    'quantity' => $item->quantity_in_stock,
                    'purchase_value' => $item->quantity_in_stock * $item->purchase_price,
                    'last_transaction' => $item->transactions()->latest()->first()?->created_at,
                ];
            });

        $totalValue = $items->sum('purchase_value');

        return view('inventory.reports.dead-stock', compact('items', 'totalValue', 'days'));
    }

    /**
     * Profit analysis report.
     */
    public function profitAnalysis(Request $request)
    {
        $items = InventoryItem::with('category')
            ->active()
            ->get()
            ->map(function ($item) {
                $profitPerItem = $item->selling_price - $item->purchase_price;
                $profitMargin = $item->purchase_price > 0 
                    ? (($profitPerItem / $item->purchase_price) * 100) 
                    : 0;

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'category' => $item->category->name ?? 'N/A',
                    'purchase_price' => $item->purchase_price,
                    'selling_price' => $item->selling_price,
                    'profit_per_item' => $profitPerItem,
                    'profit_margin' => round($profitMargin, 2),
                    'quantity' => $item->quantity_in_stock,
                    'total_potential_profit' => $profitPerItem * $item->quantity_in_stock,
                ];
            })
            ->sortByDesc('profit_margin');

        $stats = [
            'avg_profit_margin' => round($items->avg('profit_margin'), 2),
            'total_potential_profit' => $items->sum('total_potential_profit'),
            'highest_margin_item' => $items->first(),
            'lowest_margin_item' => $items->last(),
        ];

        return view('inventory.reports.profit-analysis', compact('items', 'stats'));
    }

    /**
     * Low stock report.
     */
    public function lowStock()
    {
        $items = InventoryItem::with('category')
            ->active()
            ->whereColumn('quantity_in_stock', '<=', 'reorder_level')
            ->where('quantity_in_stock', '>', 0)
            ->get();

        return view('inventory.reports.low-stock', compact('items'));
    }

    /**
     * Export report to CSV.
     */
    public function export(Request $request, string $type)
    {
        $filename = "inventory_{$type}_" . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($type) {
            $file = fopen('php://output', 'w');
            
            switch ($type) {
                case 'valuation':
                    $this->exportValuation($file);
                    break;
                case 'movement':
                    $this->exportMovement($file);
                    break;
                case 'dead-stock':
                    $this->exportDeadStock($file);
                    break;
                case 'profit':
                    $this->exportProfit($file);
                    break;
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function exportValuation($file)
    {
        fputcsv($file, ['Name', 'SKU', 'Category', 'Quantity', 'Purchase Price', 'Selling Price', 'Purchase Value', 'Selling Value', 'Potential Profit']);
        
        InventoryItem::with('category')->active()->chunk(100, function ($items) use ($file) {
            foreach ($items as $item) {
                fputcsv($file, [
                    $item->name,
                    $item->sku,
                    $item->category->name ?? 'N/A',
                    $item->quantity_in_stock,
                    $item->purchase_price,
                    $item->selling_price,
                    $item->quantity_in_stock * $item->purchase_price,
                    $item->quantity_in_stock * $item->selling_price,
                    ($item->selling_price - $item->purchase_price) * $item->quantity_in_stock,
                ]);
            }
        });
    }

    protected function exportMovement($file)
    {
        fputcsv($file, ['Date', 'Product', 'SKU', 'Type', 'Quantity', 'Unit Cost', 'Total Cost', 'User']);
        
        InventoryTransaction::with(['item', 'user'])->latest()->chunk(100, function ($transactions) use ($file) {
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->created_at->format('Y-m-d H:i'),
                    $transaction->item->name,
                    $transaction->item->sku,
                    $transaction->type,
                    $transaction->quantity,
                    $transaction->unit_cost,
                    $transaction->total_cost,
                    $transaction->user->name ?? 'System',
                ]);
            }
        });
    }

    protected function exportDeadStock($file)
    {
        fputcsv($file, ['Name', 'SKU', 'Category', 'Quantity', 'Purchase Value', 'Last Transaction']);
        
        InventoryItem::with('category')->active()
            ->whereDoesntHave('transactions', function ($query) {
                $query->where('created_at', '>=', now()->subDays(90));
            })
            ->where('quantity_in_stock', '>', 0)
            ->chunk(100, function ($items) use ($file) {
                foreach ($items as $item) {
                    fputcsv($file, [
                        $item->name,
                        $item->sku,
                        $item->category->name ?? 'N/A',
                        $item->quantity_in_stock,
                        $item->quantity_in_stock * $item->purchase_price,
                        $item->transactions()->latest()->first()?->created_at?->format('Y-m-d') ?? 'Never',
                    ]);
                }
            });
    }

    protected function exportProfit($file)
    {
        fputcsv($file, ['Name', 'SKU', 'Category', 'Purchase Price', 'Selling Price', 'Profit Per Item', 'Profit Margin %', 'Quantity', 'Total Potential Profit']);
        
        InventoryItem::with('category')->active()->chunk(100, function ($items) use ($file) {
            foreach ($items as $item) {
                $profitPerItem = $item->selling_price - $item->purchase_price;
                $profitMargin = $item->purchase_price > 0 ? (($profitPerItem / $item->purchase_price) * 100) : 0;
                
                fputcsv($file, [
                    $item->name,
                    $item->sku,
                    $item->category->name ?? 'N/A',
                    $item->purchase_price,
                    $item->selling_price,
                    $profitPerItem,
                    round($profitMargin, 2),
                    $item->quantity_in_stock,
                    $profitPerItem * $item->quantity_in_stock,
                ]);
            }
        });
    }
}
