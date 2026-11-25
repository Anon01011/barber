<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\PosSaleItem;
use App\Models\PosSaleItemConsumable;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class InventoryReportController extends Controller
{
    /**
     * Resolve date range from request preset or custom dates.
     * Supports 12 presets: today, yesterday, last7, last30, this_week, last_week, this_month, last_month, 3months, 6months, this_year, last_year.
     */
    public function resolveDateRange(Request $request): array
    {
        $preset = $request->input('range', $request->input('preset'));
        $now = now();

        if ($preset) {
            switch ($preset) {
                case 'today':
                    $start = $now->copy()->startOfDay();
                    $end = $now->copy()->endOfDay();
                    break;
                case 'yesterday':
                    $start = $now->copy()->subDay()->startOfDay();
                    $end = $now->copy()->subDay()->endOfDay();
                    break;
                case 'last7':
                    $start = $now->copy()->subDays(6)->startOfDay();
                    $end = $now->copy()->endOfDay();
                    break;
                case 'this_week':
                    $start = $now->copy()->startOfWeek();
                    $end = $now->copy()->endOfDay();
                    break;
                case 'last_week':
                    $start = $now->copy()->subWeek()->startOfWeek();
                    $end = $now->copy()->subWeek()->endOfWeek();
                    break;
                case 'this_month':
                    $start = $now->copy()->startOfMonth();
                    $end = $now->copy()->endOfDay();
                    break;
                case 'last_month':
                    $start = $now->copy()->subMonth()->startOfMonth();
                    $end = $now->copy()->subMonth()->endOfMonth();
                    break;
                case '3months':
                case 'last_3_months':
                    $start = $now->copy()->subMonths(3)->startOfDay();
                    $end = $now->copy()->endOfDay();
                    break;
                case '6months':
                case 'last_6_months':
                    $start = $now->copy()->subMonths(6)->startOfDay();
                    $end = $now->copy()->endOfDay();
                    break;
                case 'this_year':
                    $start = $now->copy()->startOfYear();
                    $end = $now->copy()->endOfDay();
                    break;
                case 'last_year':
                    $start = $now->copy()->subYear()->startOfYear();
                    $end = $now->copy()->subYear()->endOfYear();
                    break;
                case 'last30':
                default:
                    $preset = 'last30';
                    $start = $now->copy()->subDays(29)->startOfDay();
                    $end = $now->copy()->endOfDay();
                    break;
            }
            return [$start->format('Y-m-d'), $end->format('Y-m-d'), $preset];
        }

        // Custom date handling or default to last30
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate && $endDate) {
            return [$startDate, $endDate, 'custom'];
        }

        return [$now->copy()->subDays(29)->format('Y-m-d'), $now->format('Y-m-d'), 'last30'];
    }

    /**
     * Display inventory reports dashboard.
     */
    public function index(Request $request)
    {
        [$startDate, $endDate, $preset] = $this->resolveDateRange($request);
        $salonId = $this->getSalonId();

        // 1. All Active Inventory Items
        $allItems = InventoryItem::with('category')
            ->active()
            ->when($salonId, fn($q) => $q->where('salon_id', $salonId))
            ->get();

        // Core KPIs
        $totalProducts = $allItems->count();
        $totalStockUnits = (float) $allItems->sum('quantity_in_stock');
        $totalValuationCost = (float) $allItems->sum(fn($i) => (float)$i->quantity_in_stock * (float)$i->purchase_price);
        $totalValuationRetail = (float) $allItems->sum(fn($i) => (float)$i->quantity_in_stock * (float)$i->selling_price);
        $potentialProfit = max(0, $totalValuationRetail - $totalValuationCost);
        $potentialMargin = $totalValuationRetail > 0 ? round(($potentialProfit / $totalValuationRetail) * 100, 1) : 0;

        $lowStockCount = $allItems->filter(fn($i) => (float)$i->quantity_in_stock <= (float)$i->reorder_level && (float)$i->quantity_in_stock > 0)->count();
        $outOfStockCount = $allItems->filter(fn($i) => (float)$i->quantity_in_stock <= 0)->count();
        $healthyStockCount = max(0, $totalProducts - $lowStockCount - $outOfStockCount);

        // Period Movements Stats
        $periodStart = $startDate . ' 00:00:00';
        $periodEnd = $endDate . ' 23:59:59';

        $txQuery = InventoryTransaction::whereHas('item', fn($q) => $salonId ? $q->where('salon_id', $salonId) : null)
            ->whereBetween('created_at', [$periodStart, $periodEnd]);

        $stockInUnits = (float) (clone $txQuery)->whereIn('transaction_type', ['purchase', 'return'])->sum('quantity');
        $stockOutUnits = (float) (clone $txQuery)->whereIn('transaction_type', ['sale', 'adjustment', 'damaged', 'lost', 'service_consumption'])->sum(DB::raw('ABS(quantity)'));
        $totalTransactionsCount = (clone $txQuery)->count();

        // Backbar Consumable Costs in Period
        $consumablesCost = (float) PosSaleItemConsumable::when($salonId, fn($q) => $q->where('salon_id', $salonId))
            ->activeSales()
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->sum(DB::raw('quantity_used * unit_cost'));

        // CHARTS DATA:
        // Chart 1: Stock Health Status (Donut)
        $chartStockStatus = [
            'labels' => ['Healthy Stock', 'Low Stock', 'Out of Stock'],
            'series' => [$healthyStockCount, $lowStockCount, $outOfStockCount],
            'colors' => ['#10b981', '#f59e0b', '#ef4444'],
        ];

        // Chart 2: Category Valuation (Donut - Top 5 + Others)
        $categoryGroups = $allItems->groupBy(fn($i) => $i->category->name ?? 'Uncategorized')
            ->map(fn($group) => round($group->sum(fn($i) => (float)$i->quantity_in_stock * (float)$i->purchase_price), 2))
            ->sortDesc();

        $top5Categories = $categoryGroups->take(5);
        $otherValuation = $categoryGroups->slice(5)->sum();
        if ($otherValuation > 0) {
            $top5Categories['Others'] = round($otherValuation, 2);
        }
        $chartCategoryValuation = [
            'labels' => $top5Categories->keys()->toArray(),
            'series' => $top5Categories->values()->toArray(),
            'colors' => ['#0d6efd', '#10b981', '#0dcaf0', '#f59e0b', '#6f42c1', '#94a3b8'],
        ];

        // Chart 3: Catalog Classification Breakdown (Donut)
        $retailCount = $allItems->where('product_type', 'retail')->count();
        $consumableCount = $allItems->where('product_type', 'consumable')->count();
        $dualCount = $allItems->where('product_type', 'dual')->count();
        $chartClassification = [
            'labels' => ['Retail Only', 'Consumable Recipe', 'Dual-Use'],
            'series' => [$retailCount, $consumableCount, $dualCount],
            'colors' => ['#0d6efd', '#6f42c1', '#0dcaf0'],
        ];

        // Chart 4: Daily Movement In vs Out Trends (Area / Line)
        $trendTx = (clone $txQuery)->select([
            DB::raw('DATE(created_at) as tx_date'),
            DB::raw("SUM(CASE WHEN transaction_type IN ('purchase', 'return') THEN quantity ELSE 0 END) as in_qty"),
            DB::raw("SUM(CASE WHEN transaction_type IN ('sale', 'adjustment', 'damaged', 'lost', 'service_consumption') THEN ABS(quantity) ELSE 0 END) as out_qty")
        ])
        ->groupBy('tx_date')
        ->orderBy('tx_date', 'asc')
        ->get();

        $trendDates = $trendTx->pluck('tx_date')->map(fn($d) => date('M d', strtotime($d)))->toArray();
        $trendIn = $trendTx->pluck('in_qty')->map(fn($v) => round((float)$v, 2))->toArray();
        $trendOut = $trendTx->pluck('out_qty')->map(fn($v) => round((float)$v, 2))->toArray();

        // Chart 5: Top 6 Products by Tied-Up Capital (Horizontal Bar)
        $topValuedItems = $allItems->sortByDesc(fn($i) => (float)$i->quantity_in_stock * (float)$i->purchase_price)->take(6);
        $chartTopValued = [
            'labels' => $topValuedItems->pluck('name')->map(fn($n) => \Illuminate\Support\Str::limit($n, 20))->values()->toArray(),
            'series' => $topValuedItems->map(fn($i) => round((float)$i->quantity_in_stock * (float)$i->purchase_price, 2))->values()->toArray(),
        ];

        // Chart 6: Critical Low Stock vs Reorder Level (Grouped Bar)
        $criticalLowItems = $allItems->filter(fn($i) => (float)$i->quantity_in_stock <= (float)$i->reorder_level)
            ->sortBy(fn($i) => (float)$i->quantity_in_stock)
            ->take(6);
        $chartLowStock = [
            'labels' => $criticalLowItems->pluck('name')->map(fn($n) => \Illuminate\Support\Str::limit($n, 18))->values()->toArray(),
            'current' => $criticalLowItems->pluck('quantity_in_stock')->map(fn($v) => (float)$v)->values()->toArray(),
            'reorder' => $criticalLowItems->pluck('reorder_level')->map(fn($v) => (float)$v)->values()->toArray(),
        ];

        return view('inventory.reports.index', compact(
            'startDate', 'endDate', 'preset',
            'totalProducts', 'totalStockUnits', 'totalValuationCost', 'totalValuationRetail',
            'potentialProfit', 'potentialMargin', 'lowStockCount', 'outOfStockCount',
            'stockInUnits', 'stockOutUnits', 'totalTransactionsCount', 'consumablesCost',
            'chartStockStatus', 'chartCategoryValuation', 'chartClassification',
            'trendDates', 'trendIn', 'trendOut',
            'chartTopValued', 'chartLowStock'
        ));
    }

    /**
     * Resolve current salon ID safely (works for owners, staff, and super_admin context).
     */
    protected function getSalonId(): ?int
    {
        if (app()->bound('current_salon') && app('current_salon')) {
            return app('current_salon')->id;
        }
        $salonSlug = request()->route('salon_slug') ?? session('current_salon_slug');
        if ($salonSlug) {
            $salon = \App\Models\Salon::where('slug', $salonSlug)->first();
            if ($salon) {
                return $salon->id;
            }
        }
        return auth()->user()?->salon_id ?? session('current_salon_id');
    }

    /**
     * Stock valuation report.
     */
    public function stockValuation(Request $request)
    {
        $salonId = $this->getSalonId();

        $allItems = InventoryItem::with('category')
            ->active()
            ->when($salonId, fn($q) => $q->where('salon_id', $salonId))
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'category' => $item->category->name ?? 'Uncategorized',
                    'quantity' => (float)$item->quantity_in_stock,
                    'unit_type' => $item->unit_type,
                    'purchase_price' => (float)$item->purchase_price,
                    'selling_price' => (float)$item->selling_price,
                    'purchase_value' => (float)($item->quantity_in_stock * $item->purchase_price),
                    'selling_value' => (float)($item->quantity_in_stock * $item->selling_price),
                    'potential_profit' => (float)(($item->selling_price - $item->purchase_price) * $item->quantity_in_stock),
                ];
            });

        $totals = [
            'total_items' => $allItems->count(),
            'total_quantity' => $allItems->sum('quantity'),
            'total_purchase_value' => $allItems->sum('purchase_value'),
            'total_selling_value' => $allItems->sum('selling_value'),
            'total_potential_profit' => $allItems->sum('potential_profit'),
        ];

        // Chart data & Table breakdown: Valuation by Category (Top 6)
        $categoryBreakdown = $allItems->groupBy('category')->map(function ($catItems) use ($totals) {
            $val = round($catItems->sum('purchase_value'), 2);
            $qty = round($catItems->sum('quantity'), 2);
            $pct = ($totals['total_purchase_value'] > 0) ? round(($val / $totals['total_purchase_value']) * 100, 1) : 0;
            return [
                'purchase_value' => $val,
                'quantity' => $qty,
                'count' => $catItems->count(),
                'percent' => $pct,
            ];
        })->sortByDesc('purchase_value');

        $chartCategoryLabels = $categoryBreakdown->take(6)->keys()->toArray();
        $chartCategoryValues = $categoryBreakdown->take(6)->pluck('purchase_value')->toArray();

        // Paginate items (20 per page)
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $items = new LengthAwarePaginator(
            $allItems->forPage($currentPage, $perPage)->values(),
            $allItems->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('inventory.reports.stock-valuation', compact(
            'items', 'totals', 'chartCategoryLabels', 'chartCategoryValues', 'categoryBreakdown'
        ));
    }

    /**
     * Stock movement report.
     */
    public function stockMovement(Request $request)
    {
        [$startDate, $endDate, $preset] = $this->resolveDateRange($request);
        $salonId = $this->getSalonId();

        $query = InventoryTransaction::whereHas('item', function ($q) use ($salonId) {
                if ($salonId) $q->where('salon_id', $salonId);
            })
            ->with(['item.category', 'user'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }

        $transactions = $query->latest()->paginate(50)->withQueryString();

        $stats = [
            'total_in' => InventoryTransaction::whereHas('item', fn($q) => $salonId ? $q->where('salon_id', $salonId) : null)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereIn('transaction_type', ['purchase', 'return'])
                ->sum('quantity'),
            'total_out' => InventoryTransaction::whereHas('item', fn($q) => $salonId ? $q->where('salon_id', $salonId) : null)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereIn('transaction_type', ['sale', 'adjustment', 'damaged', 'lost'])
                ->sum(DB::raw('ABS(quantity)')),
            'total_consumed' => InventoryTransaction::whereHas('item', fn($q) => $salonId ? $q->where('salon_id', $salonId) : null)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->where('transaction_type', 'service_consumption')
                ->sum(DB::raw('ABS(quantity)')),
            'total_value' => InventoryTransaction::whereHas('item', fn($q) => $salonId ? $q->where('salon_id', $salonId) : null)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->sum('total_cost'),
        ];

        // Chart data: Transaction volume by type
        $typeStats = InventoryTransaction::whereHas('item', fn($q) => $salonId ? $q->where('salon_id', $salonId) : null)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select('transaction_type', DB::raw('SUM(ABS(quantity)) as total_qty'), DB::raw('COUNT(*) as count'), DB::raw('SUM(total_cost) as total_cost'))
            ->groupBy('transaction_type')
            ->get();

        $chartMovementLabels = $typeStats->map(fn($t) => ucfirst(str_replace('_', ' ', $t->transaction_type)))->toArray();
        $chartMovementValues = $typeStats->map(fn($t) => round((float)$t->total_qty, 2))->toArray();

        return view('inventory.reports.stock-movement', compact(
            'transactions', 'stats', 'startDate', 'endDate', 'preset',
            'chartMovementLabels', 'chartMovementValues', 'typeStats'
        ));
    }

    /**
     * Dead stock report (items with no movement).
     */
    public function deadStock(Request $request)
    {
        $days = (int) ($request->days ?? 90);
        $salonId = $this->getSalonId();

        $allItems = InventoryItem::with('category')
            ->withMax('transactions as last_transaction_at', 'created_at')
            ->active()
            ->when($salonId, fn($q) => $q->where('salon_id', $salonId))
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
                    'category' => $item->category->name ?? 'Uncategorized',
                    'quantity' => (float)$item->quantity_in_stock,
                    'purchase_value' => (float)($item->quantity_in_stock * $item->purchase_price),
                    'last_transaction' => $item->last_transaction_at,
                ];
            })
            ->sortByDesc('purchase_value')
            ->values();

        $totalValue = $allItems->sum('purchase_value');

        // Top 6 dead stock items by tied-up capital
        $chartDeadLabels = $allItems->take(6)->pluck('name')->toArray();
        $chartDeadValues = $allItems->take(6)->pluck('purchase_value')->toArray();

        // Paginate items (20 per page)
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $items = new LengthAwarePaginator(
            $allItems->forPage($currentPage, $perPage)->values(),
            $allItems->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('inventory.reports.dead-stock', compact(
            'items', 'totalValue', 'days', 'chartDeadLabels', 'chartDeadValues'
        ));
    }

    /**
     * Profit analysis report.
     */
    public function profitAnalysis(Request $request)
    {
        $salonId = $this->getSalonId();

        $allItems = InventoryItem::with('category')
            ->active()
            ->when($salonId, fn($q) => $q->where('salon_id', $salonId))
            ->get()
            ->map(function ($item) {
                $profitPerItem = (float)($item->selling_price - $item->purchase_price);
                $profitMargin = $item->selling_price > 0 
                    ? (($profitPerItem / $item->selling_price) * 100) 
                    : 0;

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'category' => $item->category->name ?? 'Uncategorized',
                    'purchase_price' => (float)$item->purchase_price,
                    'selling_price' => (float)$item->selling_price,
                    'profit_per_item' => $profitPerItem,
                    'profit_margin' => round($profitMargin, 2),
                    'quantity' => (float)$item->quantity_in_stock,
                    'total_potential_profit' => round($profitPerItem * $item->quantity_in_stock, 2),
                ];
            })
            ->sortByDesc('profit_margin')
            ->values();

        $stats = [
            'avg_profit_margin' => round($allItems->avg('profit_margin'), 2),
            'total_potential_profit' => $allItems->sum('total_potential_profit'),
            'highest_margin_item' => $allItems->first(),
            'lowest_margin_item' => $allItems->last(),
        ];

        // Top 8 items by margin
        $chartMarginLabels = $allItems->take(8)->pluck('name')->toArray();
        $chartMarginValues = $allItems->take(8)->pluck('profit_margin')->toArray();

        // Paginate items (20 per page)
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $items = new LengthAwarePaginator(
            $allItems->forPage($currentPage, $perPage)->values(),
            $allItems->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('inventory.reports.profit-analysis', compact(
            'items', 'stats', 'chartMarginLabels', 'chartMarginValues'
        ));
    }

    /**
     * Low stock report.
     */
    public function lowStock(Request $request)
    {
        $salonId = $this->getSalonId();

        $query = InventoryItem::with('category')
            ->active()
            ->when($salonId, fn($q) => $q->where('salon_id', $salonId))
            ->whereColumn('quantity_in_stock', '<=', 'reorder_level')
            ->orderBy('quantity_in_stock', 'asc');

        $allLowItems = (clone $query)->get();

        // Chart: Top 8 lowest stock items compared to reorder level
        $chartLowLabels = $allLowItems->take(8)->pluck('name')->toArray();
        $chartStockValues = $allLowItems->take(8)->map(fn($i) => (float)$i->quantity_in_stock)->toArray();
        $chartReorderValues = $allLowItems->take(8)->map(fn($i) => (float)$i->reorder_level)->toArray();

        $items = $query->paginate(20)->withQueryString();

        $stats = [
            'total_low_stock' => $allLowItems->count(),
            'out_of_stock' => $allLowItems->where('quantity_in_stock', '<=', 0)->count(),
            'total_reorder_cost' => $allLowItems->sum(function ($i) {
                $needed = max(0, $i->reorder_level - $i->quantity_in_stock);
                return $needed * (float)($i->purchase_price ?? 0);
            }),
        ];

        return view('inventory.reports.low-stock', compact(
            'items', 'stats', 'chartLowLabels', 'chartStockValues', 'chartReorderValues'
        ));
    }

    /**
     * Export inventory report to styled XLSX using PhpSpreadsheet.
     */
    public function export(Request $request, ...$args)
    {
        $type = $request->route('type');
        if (!$type) {
            foreach ($args as $arg) {
                if (is_string($arg) && in_array($arg, [
                    'valuation', 'movement', 'dead-stock', 'profit',
                    'consumption', 'service-consumption',
                    'profitability', 'service-profitability',
                    'wastage', 'low-stock'
                ])) {
                    $type = $arg;
                    break;
                }
            }
            $type = $type ?? end($args) ?: $request->input('type', 'valuation');
        }

        $salonId   = $this->getSalonId();
        $salon     = \App\Models\Salon::find($salonId);
        $salonName = $salon?->name ?? 'Salon';
        $days      = max(1, (int) $request->input('days', 90));

        $spreadsheet = new Spreadsheet();
        $filename    = "inventory_{$type}_" . now()->format('Y-m-d_His') . '.xlsx';

        switch ($type) {
            case 'valuation':
                $this->exportValuation($spreadsheet, $salonName, $request);
                break;
            case 'movement':
                $this->exportMovement($spreadsheet, $salonName, $request);
                break;
            case 'dead-stock':
                $this->exportDeadStock($spreadsheet, $salonName, $days);
                break;
            case 'profit':
                $this->exportProfit($spreadsheet, $salonName);
                break;
            case 'consumption':
            case 'service-consumption':
                $this->exportConsumption($spreadsheet, $salonName, $request);
                break;
            case 'profitability':
            case 'service-profitability':
                $this->exportProfitability($spreadsheet, $salonName, $request);
                break;
            case 'wastage':
                $this->exportWastage($spreadsheet, $salonName, $request);
                break;
            case 'low-stock':
                $this->exportLowStock($spreadsheet, $salonName, $request);
                break;
            default:
                abort(404);
        }

        $writer = new Xlsx($spreadsheet);

        if (ob_get_level()) {
            ob_end_clean();
        }

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control'       => 'max-age=0, must-revalidate',
            'Pragma'              => 'public',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // XLSX formatting helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Apply a solid fill to a cell range.
     */
    private function fillRange($sheet, string $range, string $rgb): void
    {
        $sheet->getStyle($range)->getFill()
              ->setFillType(Fill::FILL_SOLID)
              ->getStartColor()->setRGB($rgb);
    }

    /**
     * Write a styled title/header block.
     * Returns the next available row number.
     */
    private function writeReportHeader(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        string $salonName,
        string $reportTitle,
        string $headerColor,
        string $metaLine,
        int    $totalCols
    ): int {
        $lastCol = Coordinate::stringFromColumnIndex($totalCols);

        // Row 1 – salon name, dark navy
        $sheet->setCellValue('A1', strtoupper($salonName));
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('FFFFFF');
        $this->fillRange($sheet, "A1:{$lastCol}1", '0F172A');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Row 2 – report title, theme colour
        $sheet->setCellValue('A2', $reportTitle);
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(13)->getColor()->setRGB('FFFFFF');
        $this->fillRange($sheet, "A2:{$lastCol}2", $headerColor);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(24);

        // Row 3 – metadata / date range
        $sheet->setCellValue('A3', $metaLine);
        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getRowDimension(3)->setRowHeight(18);

        // Row 4 – blank spacer
        $sheet->getRowDimension(4)->setRowHeight(8);

        return 5; // next row for column headers
    }

    /**
     * Write column header row in given theme colour. Returns next data row.
     */
    private function writeColumnHeaders(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        int    $row,
        array  $headers,
        string $headerColor
    ): int {
        foreach ($headers as $i => $label) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue("{$col}{$row}", $label);
            $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $this->fillRange($sheet, "{$col}{$row}", $headerColor);
            $sheet->getStyle("{$col}{$row}")->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                  ->setVertical(Alignment::VERTICAL_CENTER);
        }
        $sheet->getRowDimension($row)->setRowHeight(20);
        return $row + 1;
    }

    /**
     * Write a summary section header + KV rows and a footer. Returns next row.
     */
    private function writeSummaryBlock(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        int    $row,
        string $salonName,
        array  $summaryItems,
        string $headerColor,
        int    $totalCols
    ): int {
        $lastCol = Coordinate::stringFromColumnIndex($totalCols);

        // blank spacer
        $row++;

        // Summary title bar
        $sheet->setCellValue("A{$row}", 'SUMMARY');
        $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12)->getColor()->setRGB('FFFFFF');
        $this->fillRange($sheet, "A{$row}:{$lastCol}{$row}", $headerColor);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        foreach ($summaryItems as $item) {
            $sheet->setCellValue("A{$row}", $item['label']);
            $sheet->setCellValue("B{$row}", $item['value']);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            if (!empty($item['format'])) {
                $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode($item['format']);
            }
            if (!empty($item['color'])) {
                $sheet->getStyle("B{$row}")->getFont()->getColor()->setRGB($item['color']);
            }
            $row++;
        }

        // Footer
        $row++;
        $footerText = 'Report exported from ' . $salonName . ' on ' . now()->format('d M Y H:i');
        $sheet->setCellValue("A{$row}", $footerText);
        $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB('888888');
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return $row;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1. STOCK VALUATION EXPORT
    // ─────────────────────────────────────────────────────────────────────────
    protected function exportValuation(Spreadsheet $spreadsheet, string $salonName, Request $request): void
    {
        $salonId = $this->getSalonId();
        $currSym = currency_symbol();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Stock Valuation');

        $headers     = ['Item Name', 'SKU', 'Category', 'Qty in Stock', 'Purchase Price', 'Selling Price', 'Purchase Value', 'Selling Value', 'Potential Profit'];
        $totalCols   = count($headers);
        $headerColor = '1E40AF'; // blue

        $metaLine = 'Generated: ' . now()->format('d M Y H:i') . '  |  Currency: ' . $currSym;
        $row = $this->writeReportHeader($sheet, $salonName, 'STOCK VALUATION REPORT', $headerColor, $metaLine, $totalCols);
        $row = $this->writeColumnHeaders($sheet, $row, $headers, $headerColor);

        $totQty     = 0;
        $totPurVal  = 0;
        $totSelVal  = 0;
        $totProfit  = 0;
        $alternate  = false;

        $items = InventoryItem::with('category')
            ->active()
            ->when($salonId, fn($q) => $q->where('salon_id', $salonId))
            ->get();

        foreach ($items as $item) {
            $purchaseValue = (float) $item->quantity_in_stock * (float) $item->purchase_price;
            $sellingValue  = (float) $item->quantity_in_stock * (float) $item->selling_price;
            $profit        = $sellingValue - $purchaseValue;

            $totQty    += $item->quantity_in_stock;
            $totPurVal += $purchaseValue;
            $totSelVal += $sellingValue;
            $totProfit += $profit;

            $sheet->setCellValue("A{$row}", $item->name);
            $sheet->setCellValue("B{$row}", $item->sku ?? '');
            $sheet->setCellValue("C{$row}", $item->category?->name ?? 'N/A');
            $sheet->setCellValue("D{$row}", $item->quantity_in_stock);
            $sheet->setCellValue("E{$row}", $item->purchase_price);
            $sheet->setCellValue("F{$row}", $item->selling_price);
            $sheet->setCellValue("G{$row}", $purchaseValue);
            $sheet->setCellValue("H{$row}", $sellingValue);
            $sheet->setCellValue("I{$row}", $profit);

            foreach (['E', 'F', 'G', 'H'] as $col) {
                $sheet->getStyle("{$col}{$row}")->getNumberFormat()->setFormatCode('"' . $currSym . '"#,##0.00');
                $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $profitFmt = '"' . $currSym . '"#,##0.00';
            $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode($profitFmt);
            $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("I{$row}")->getFont()->getColor()->setRGB($profit >= 0 ? '15803D' : 'B91C1C');

            if ($alternate) {
                $this->fillRange($sheet, "A{$row}:I{$row}", 'EFF6FF');
            }
            $alternate = !$alternate;
            $row++;
        }

        // Totals row
        $this->fillRange($sheet, "A{$row}:I{$row}", $headerColor);
        $sheet->setCellValue("A{$row}", 'TOTALS');
        $sheet->setCellValue("D{$row}", $totQty);
        $sheet->setCellValue("G{$row}", $totPurVal);
        $sheet->setCellValue("H{$row}", $totSelVal);
        $sheet->setCellValue("I{$row}", $totProfit);
        foreach (['A', 'D', 'G', 'H', 'I'] as $col) {
            $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        }
        foreach (['G', 'H', 'I'] as $col) {
            $sheet->getStyle("{$col}{$row}")->getNumberFormat()->setFormatCode('"' . $currSym . '"#,##0.00');
            $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $row++;

        // Summary block
        $this->writeSummaryBlock($sheet, $row, $salonName, [
            ['label' => 'Total Items',              'value' => $items->count(),   'format' => '#,##0',                           'color' => ''],
            ['label' => 'Total Quantity in Stock',  'value' => $totQty,           'format' => '#,##0',                           'color' => '1E40AF'],
            ['label' => 'Total Purchase Value',     'value' => $totPurVal,        'format' => '"' . $currSym . '"#,##0.00',      'color' => ''],
            ['label' => 'Total Selling Value',      'value' => $totSelVal,        'format' => '"' . $currSym . '"#,##0.00',      'color' => ''],
            ['label' => 'Total Potential Profit',   'value' => $totProfit,        'format' => '"' . $currSym . '"#,##0.00',      'color' => $totProfit >= 0 ? '15803D' : 'B91C1C'],
        ], $headerColor, $totalCols);

        foreach (range(1, $totalCols) as $i) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. STOCK MOVEMENT EXPORT
    // ─────────────────────────────────────────────────────────────────────────
    protected function exportMovement(Spreadsheet $spreadsheet, string $salonName, Request $request): void
    {
        $salonId   = $this->getSalonId();
        $currSym   = currency_symbol();
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Stock Movement');

        $headers     = ['Date & Time', 'Product', 'SKU', 'Type', 'Quantity', 'Unit Cost', 'Total Cost', 'Recorded By'];
        $totalCols   = count($headers);
        $headerColor = 'C2410C'; // orange

        $metaLine = "Period: {$startDate} to {$endDate}  |  Currency: {$currSym}";
        $row = $this->writeReportHeader($sheet, $salonName, 'STOCK MOVEMENT REPORT', $headerColor, $metaLine, $totalCols);
        $row = $this->writeColumnHeaders($sheet, $row, $headers, $headerColor);

        $txCount    = 0;
        $netQty     = 0;
        $totalCost  = 0;
        $alternate  = false;

        $typeColors = [
            'purchase'   => '15803D',
            'return'     => '1E40AF',
            'sale'       => 'B91C1C',
            'adjustment' => 'C2410C',
            'damaged'    => 'B91C1C',
            'lost'       => 'B91C1C',
        ];

        $query = InventoryTransaction::with(['item', 'user'])
            ->when($salonId, fn($q) => $q->whereHas('item', fn($iq) => $iq->where('salon_id', $salonId)))
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->when($request->filled('transaction_type') || ($request->filled('type') && $request->type !== 'movement'), fn($q) => $q->where('transaction_type', $request->input('transaction_type', $request->type)))
            ->latest();

        $query->chunk(200, function ($transactions) use (
            $sheet, &$row, &$txCount, &$netQty, &$totalCost, &$alternate,
            $typeColors, $currSym
        ) {
            foreach ($transactions as $tx) {
                $txType   = $tx->transaction_type ?? $tx->type ?? 'adjustment';
                $qty      = (float) $tx->quantity;
                $unitCost = (float) $tx->unit_cost;
                $ttlCost  = (float) $tx->total_cost;

                $txCount++;
                $netQty    += $qty;
                $totalCost += $ttlCost;

                $sheet->setCellValue("A{$row}", $tx->created_at?->format('d M Y H:i') ?? '');
                $sheet->setCellValue("B{$row}", $tx->item?->name ?? 'N/A');
                $sheet->setCellValue("C{$row}", $tx->item?->sku ?? '');
                $sheet->setCellValue("D{$row}", ucfirst(str_replace('_', ' ', $txType)));
                $sheet->setCellValue("E{$row}", $qty);
                $sheet->setCellValue("F{$row}", $unitCost);
                $sheet->setCellValue("G{$row}", $ttlCost);
                $sheet->setCellValue("H{$row}", $tx->user?->name ?? 'System');

                foreach (['F', 'G'] as $col) {
                    $sheet->getStyle("{$col}{$row}")->getNumberFormat()->setFormatCode('"' . $currSym . '"#,##0.00');
                    $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }
                $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $rgb = $typeColors[strtolower($txType)] ?? '555555';
                $sheet->getStyle("D{$row}")->getFont()->getColor()->setRGB($rgb);
                $sheet->getStyle("D{$row}")->getFont()->setBold(true);

                $sheet->getStyle("E{$row}")->getFont()->getColor()
                      ->setRGB($qty >= 0 ? '15803D' : 'B91C1C');

                if ($alternate) {
                    $this->fillRange($sheet, "A{$row}:H{$row}", 'FFF7ED');
                }
                $alternate = !$alternate;
                $row++;
            }
        });

        // Totals row
        $this->fillRange($sheet, "A{$row}:H{$row}", $headerColor);
        $sheet->setCellValue("A{$row}", 'TOTALS');
        $sheet->setCellValue("E{$row}", $netQty);
        $sheet->setCellValue("G{$row}", $totalCost);
        foreach (['A', 'E', 'G'] as $col) {
            $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        }
        $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('"' . $currSym . '"#,##0.00');
        $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $row++;

        $this->writeSummaryBlock($sheet, $row, $salonName, [
            ['label' => 'Total Transactions',  'value' => $txCount,    'format' => '#,##0',                      'color' => ''],
            ['label' => 'Net Quantity Change',  'value' => $netQty,    'format' => '#,##0.##',                   'color' => $netQty >= 0 ? '15803D' : 'B91C1C'],
            ['label' => 'Total Cost Value',     'value' => $totalCost, 'format' => '"' . $currSym . '"#,##0.00', 'color' => ''],
        ], $headerColor, $totalCols);

        foreach (range(1, $totalCols) as $i) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. DEAD STOCK EXPORT
    // ─────────────────────────────────────────────────────────────────────────
    protected function exportDeadStock(Spreadsheet $spreadsheet, string $salonName, int $days = 90): void
    {
        $salonId = $this->getSalonId();
        $currSym = currency_symbol();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Dead Stock');

        $headers     = ['Item Name', 'SKU', 'Category', 'Qty in Stock', 'Dead Capital Value', 'Last Activity'];
        $totalCols   = count($headers);
        $headerColor = 'B91C1C'; // red

        $metaLine = "No movement in last {$days} days  |  Analysis Date: " . now()->format('d M Y') . '  |  Currency: ' . $currSym;
        $row = $this->writeReportHeader($sheet, $salonName, "DEAD STOCK REPORT — No Movement in {$days} Days", $headerColor, $metaLine, $totalCols);
        $row = $this->writeColumnHeaders($sheet, $row, $headers, $headerColor);

        $totItems    = 0;
        $totCapital  = 0;
        $alternate   = false;
        $cutoff180   = now()->subDays(180);

        $items = InventoryItem::with('category')
            ->withMax('transactions as last_transaction_at', 'created_at')
            ->active()
            ->when($salonId, fn($q) => $q->where('salon_id', $salonId))
            ->whereDoesntHave('transactions', fn($q) => $q->where('created_at', '>=', now()->subDays($days)))
            ->where('quantity_in_stock', '>', 0)
            ->get();

        foreach ($items as $item) {
            $deadCapital = (float) $item->quantity_in_stock * (float) $item->purchase_price;
            $lastAt      = $item->last_transaction_at
                           ? \Carbon\Carbon::parse($item->last_transaction_at)
                           : null;
            $lastLabel   = $lastAt ? $lastAt->format('d M Y') : 'Never';

            $totItems++;
            $totCapital += $deadCapital;

            $sheet->setCellValue("A{$row}", $item->name);
            $sheet->setCellValue("B{$row}", $item->sku ?? '');
            $sheet->setCellValue("C{$row}", $item->category?->name ?? 'N/A');
            $sheet->setCellValue("D{$row}", $item->quantity_in_stock);
            $sheet->setCellValue("E{$row}", $deadCapital);
            $sheet->setCellValue("F{$row}", $lastLabel);

            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('"' . $currSym . '"#,##0.00');
            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("E{$row}")->getFont()->getColor()->setRGB('B91C1C');
            $sheet->getStyle("E{$row}")->getFont()->setBold(true);

            if ($lastLabel === 'Never') {
                $sheet->getStyle("F{$row}")->getFont()->getColor()->setRGB('B91C1C');
                $sheet->getStyle("F{$row}")->getFont()->setBold(true);
            } elseif ($lastAt && $lastAt->lt($cutoff180)) {
                $sheet->getStyle("F{$row}")->getFont()->getColor()->setRGB('C2410C');
            }

            if ($alternate) {
                $this->fillRange($sheet, "A{$row}:F{$row}", 'FEF2F2');
            }
            $alternate = !$alternate;
            $row++;
        }

        // Totals row
        $this->fillRange($sheet, "A{$row}:F{$row}", $headerColor);
        $sheet->setCellValue("A{$row}", 'TOTALS');
        $sheet->setCellValue("D{$row}", $totItems);
        $sheet->setCellValue("E{$row}", $totCapital);
        foreach (['A', 'D', 'E'] as $col) {
            $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        }
        $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('"' . $currSym . '"#,##0.00');
        $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $row++;

        $this->writeSummaryBlock($sheet, $row, $salonName, [
            ['label' => 'Dead Stock Items',          'value' => $totItems,   'format' => '#,##0',                      'color' => ''],
            ['label' => 'Total Dead Capital Tied Up', 'value' => $totCapital, 'format' => '"' . $currSym . '"#,##0.00', 'color' => 'B91C1C'],
        ], $headerColor, $totalCols);

        foreach (range(1, $totalCols) as $i) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. PROFIT ANALYSIS EXPORT
    // ─────────────────────────────────────────────────────────────────────────
    protected function exportProfit(Spreadsheet $spreadsheet, string $salonName): void
    {
        $salonId = $this->getSalonId();
        $currSym = currency_symbol();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Profit Analysis');

        $headers     = ['Item Name', 'SKU', 'Category', 'Cost Price', 'Selling Price', 'Profit / Unit', 'Margin %', 'Qty in Stock', 'Total Potential Profit'];
        $totalCols   = count($headers);
        $headerColor = '15803D'; // green

        $metaLine = 'Generated: ' . now()->format('d M Y H:i') . '  |  Currency: ' . $currSym;
        $row = $this->writeReportHeader($sheet, $salonName, 'PROFIT ANALYSIS REPORT', $headerColor, $metaLine, $totalCols);
        $row = $this->writeColumnHeaders($sheet, $row, $headers, $headerColor);

        $totItems   = 0;
        $totProfit  = 0;
        $alternate  = false;

        $items = InventoryItem::with('category')
            ->active()
            ->when($salonId, fn($q) => $q->where('salon_id', $salonId))
            ->get();

        foreach ($items as $item) {
            $profitPerUnit = (float) $item->selling_price - (float) $item->purchase_price;
            $margin        = (float) $item->selling_price > 0
                             ? round(($profitPerUnit / (float) $item->selling_price) * 100, 2)
                             : 0;
            $totalPotential = $profitPerUnit * (float) $item->quantity_in_stock;

            $totItems++;
            $totProfit += $totalPotential;

            $sheet->setCellValue("A{$row}", $item->name);
            $sheet->setCellValue("B{$row}", $item->sku ?? '');
            $sheet->setCellValue("C{$row}", $item->category?->name ?? 'N/A');
            $sheet->setCellValue("D{$row}", $item->purchase_price);
            $sheet->setCellValue("E{$row}", $item->selling_price);
            $sheet->setCellValue("F{$row}", $profitPerUnit);
            $sheet->setCellValue("G{$row}", $margin);
            $sheet->setCellValue("H{$row}", $item->quantity_in_stock);
            $sheet->setCellValue("I{$row}", $totalPotential);

            foreach (['D', 'E', 'F'] as $col) {
                $sheet->getStyle("{$col}{$row}")->getNumberFormat()->setFormatCode('"' . $currSym . '"#,##0.00');
                $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('0.00"%"');
            $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode('"' . $currSym . '"#,##0.00');
            $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $sheet->getStyle("F{$row}")->getFont()->getColor()
                  ->setRGB($profitPerUnit >= 0 ? '15803D' : 'B91C1C');

            $marginRgb = $margin >= 40 ? '15803D' : ($margin >= 20 ? '1E40AF' : 'B91C1C');
            $sheet->getStyle("G{$row}")->getFont()->getColor()->setRGB($marginRgb);

            $sheet->getStyle("I{$row}")->getFont()->setBold(true)->getColor()
                  ->setRGB($totalPotential >= 0 ? '15803D' : 'B91C1C');

            if ($alternate) {
                $this->fillRange($sheet, "A{$row}:I{$row}", 'F0FDF4');
            }
            $alternate = !$alternate;
            $row++;
        }

        // Totals row
        $this->fillRange($sheet, "A{$row}:I{$row}", $headerColor);
        $sheet->setCellValue("A{$row}", 'TOTALS');
        $sheet->setCellValue("H{$row}", $totItems);
        $sheet->setCellValue("I{$row}", $totProfit);
        foreach (['A', 'H', 'I'] as $col) {
            $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        }
        $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode('"' . $currSym . '"#,##0.00');
        $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $row++;

        $this->writeSummaryBlock($sheet, $row, $salonName, [
            ['label' => 'Total Items Analysed',      'value' => $totItems,  'format' => '#,##0',                      'color' => ''],
            ['label' => 'Total Potential Profit',    'value' => $totProfit, 'format' => '"' . $currSym . '"#,##0.00', 'color' => $totProfit >= 0 ? '15803D' : 'B91C1C'],
        ], $headerColor, $totalCols);

        foreach (range(1, $totalCols) as $i) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 5. SERVICE CONSUMPTION EXPORT
    // ─────────────────────────────────────────────────────────────────────────
    protected function exportConsumption(Spreadsheet $spreadsheet, string $salonName, Request $request): void
    {
        $salonId   = $this->getSalonId();
        $currSym   = currency_symbol();
        [$startDate, $endDate] = $this->resolveDateRange($request);
        $staffId   = $request->input('staff_id');
        $itemId    = $request->input('inventory_item_id');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Consumables Usage');

        $headers     = ['Date & Time', 'Service Name', 'Consumable Item', 'SKU', 'Qty Used', 'Unit', 'Unit Cost', 'Total Cost', 'Staff Member', 'Cashier', 'Sale / Invoice #'];
        $totalCols   = count($headers);
        $headerColor = '4F46E5'; // indigo

        $metaLine = "Period: {$startDate} to {$endDate}  |  Currency: {$currSym}";
        $row = $this->writeReportHeader($sheet, $salonName, 'SERVICE CONSUMABLES USAGE REPORT', $headerColor, $metaLine, $totalCols);
        $row = $this->writeColumnHeaders($sheet, $row, $headers, $headerColor);

        $totQty      = 0;
        $totCost     = 0;
        $recordCount = 0;
        $alternate   = false;

        $query = PosSaleItemConsumable::where('salon_id', $salonId)
            ->activeSales()
            ->with(['inventoryItem.category', 'posSaleItem.sale.employee', 'sale.employee', 'staff'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->when($staffId, fn($q) => $q->where('staff_id', $staffId))
            ->when($itemId, fn($q) => $q->where('inventory_item_id', $itemId))
            ->latest();

        $query->chunk(200, function ($consumptions) use (
            $sheet, &$row, &$totQty, &$totCost, &$recordCount, &$alternate, $currSym
        ) {
            foreach ($consumptions as $c) {
                $recordCount++;
                $qty = (float) $c->quantity_used;
                $cost = (float) $c->total_cost;
                $unitCost = $qty > 0 ? ($cost / $qty) : 0;

                $totQty  += $qty;
                $totCost += $cost;

                $saleRef = $c->posSaleItem?->sale?->invoice_number ?? ('#' . ($c->posSaleItem?->sale?->id ?? 'N/A'));

                $cashierName = $c->sale?->employee?->name ?? $c->posSaleItem?->sale?->employee?->name ?? 'N/A';

                $sheet->setCellValue("A{$row}", $c->created_at?->format('d M Y H:i') ?? '');
                $sheet->setCellValue("B{$row}", $c->posSaleItem?->item_name ?? 'Service');
                $sheet->setCellValue("C{$row}", $c->inventoryItem?->name ?? 'Unknown');
                $sheet->setCellValue("D{$row}", $c->inventoryItem?->sku ?? 'N/A');
                $sheet->setCellValue("E{$row}", $qty);
                $sheet->setCellValue("F{$row}", $c->unit ?? 'unit');
                $sheet->setCellValue("G{$row}", $unitCost);
                $sheet->setCellValue("H{$row}", $cost);
                $sheet->setCellValue("I{$row}", $c->staff?->name ?? 'Unassigned');
                $sheet->setCellValue("J{$row}", $cashierName);
                $sheet->setCellValue("K{$row}", $saleRef);

                $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('"' . $currSym . '"#,##0.00');
                $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('"' . $currSym . '"#,##0.00');
                $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                if ($alternate) {
                    $this->fillRange($sheet, "A{$row}:K{$row}", 'EEF2FF');
                }
                $alternate = !$alternate;
                $row++;
            }
        });

        // Totals row
        $this->fillRange($sheet, "A{$row}:J{$row}", $headerColor);
        $sheet->setCellValue("A{$row}", 'TOTALS');
        $sheet->setCellValue("E{$row}", $totQty);
        $sheet->setCellValue("H{$row}", $totCost);
        foreach (['A', 'E', 'H'] as $col) {
            $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        }
        $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('"' . $currSym . '"#,##0.00');
        $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $row++;

        $this->writeSummaryBlock($sheet, $row, $salonName, [
            ['label' => 'Total Consumption Events', 'value' => $recordCount, 'format' => '#,##0',                      'color' => ''],
            ['label' => 'Total Quantity Used',       'value' => $totQty,      'format' => '#,##0.##',                   'color' => '4F46E5'],
            ['label' => 'Total Consumable Cost',     'value' => $totCost,     'format' => '"' . $currSym . '"#,##0.00', 'color' => 'B91C1C'],
        ], $headerColor, $totalCols);

        $lastCol = Coordinate::stringFromColumnIndex($totalCols);
        foreach (range(1, $totalCols) as $i) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 6. SERVICE PROFITABILITY EXPORT
    // ─────────────────────────────────────────────────────────────────────────
    protected function exportProfitability(Spreadsheet $spreadsheet, string $salonName, Request $request): void
    {
        $salonId   = $this->getSalonId();
        $currSym   = currency_symbol();
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Service Profitability');

        $headers     = ['Service Name', 'Category', 'Price', 'Units Sold', 'Net Revenue', 'COGS (Consumables)', 'Gross Profit', 'Margin %'];
        $totalCols   = count($headers);
        $headerColor = '059669'; // emerald green

        $metaLine = "Period: {$startDate} to {$endDate}  |  Currency: {$currSym}";
        $row = $this->writeReportHeader($sheet, $salonName, 'SERVICE PROFITABILITY & MARGIN REPORT', $headerColor, $metaLine, $totalCols);
        $row = $this->writeColumnHeaders($sheet, $row, $headers, $headerColor);

        $saleItems = PosSaleItem::whereHas('sale', function ($q) use ($salonId, $startDate, $endDate) {
                $q->where('salon_id', $salonId)
                  ->whereNotIn('status', ['voided'])
                  ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->where('item_type', 'App\Models\Service')
            ->with(['service.category', 'consumables', 'sale'])
            ->get();

        $serviceStats = $saleItems->groupBy('item_id')->map(function ($items, $serviceId) {
            $first = $items->first();
            $service = $first->service;
            $quantitySold = (float) $items->sum('quantity');
            $grossRevenue = (float) $items->sum('subtotal');
            $netRevenue = (float) $items->sum(function ($i) {
                $lineNet = (float) $i->subtotal - (float) $i->discount_amount;
                $allocatedCartDiscount = 0;
                $sale = $i->sale;
                if ($sale && (float)$sale->discount > 0 && (float)$sale->subtotal > 0) {
                    $ratio = (float)$i->subtotal / (float)$sale->subtotal;
                    $allocatedCartDiscount = (float)$sale->discount * $ratio;
                }
                return max(0, $lineNet - $allocatedCartDiscount);
            });

            $cogs = (float) $items->sum(function ($item) use ($service) {
                if ($item->consumables && $item->consumables->isNotEmpty()) {
                    return (float) $item->consumables->sum('total_cost');
                }
                return $service ? (float) ($service->calculateEstimatedCogs() * (float) $item->quantity) : 0.0;
            });

            $grossProfit = $netRevenue - $cogs;
            $marginPct = $netRevenue > 0 ? round(($grossProfit / $netRevenue) * 100, 1) : 0;

            return [
                'name' => $service->name ?? $first->item_name ?? 'Unknown Service',
                'category' => $service->category->name ?? 'Uncategorized',
                'price' => (float) ($service->price ?? 0),
                'quantity_sold' => $quantitySold,
                'revenue' => $netRevenue,
                'cogs' => $cogs,
                'gross_profit' => $grossProfit,
                'margin_percent' => $marginPct,
            ];
        })->sortByDesc('revenue')->values();

        $totUnits  = 0;
        $totRev    = 0;
        $totCogs   = 0;
        $totProfit = 0;
        $alternate = false;

        foreach ($serviceStats as $stat) {
            $totUnits  += $stat['quantity_sold'];
            $totRev    += $stat['revenue'];
            $totCogs   += $stat['cogs'];
            $totProfit += $stat['gross_profit'];

            $sheet->setCellValue("A{$row}", $stat['name']);
            $sheet->setCellValue("B{$row}", $stat['category']);
            $sheet->setCellValue("C{$row}", $stat['price']);
            $sheet->setCellValue("D{$row}", $stat['quantity_sold']);
            $sheet->setCellValue("E{$row}", $stat['revenue']);
            $sheet->setCellValue("F{$row}", $stat['cogs']);
            $sheet->setCellValue("G{$row}", $stat['gross_profit']);
            $sheet->setCellValue("H{$row}", $stat['margin_percent']);

            foreach (['C', 'E', 'F', 'G'] as $c) {
                $sheet->getStyle("{$c}{$row}")->getNumberFormat()->setFormatCode('"' . $currSym . '"#,##0.00');
                $sheet->getStyle("{$c}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('0.0"%"');
            $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $marginColor = $stat['margin_percent'] >= 50 ? '059669' : ($stat['margin_percent'] >= 25 ? '1E40AF' : 'DC2626');
            $sheet->getStyle("H{$row}")->getFont()->getColor()->setRGB($marginColor);

            if ($alternate) {
                $this->fillRange($sheet, "A{$row}:H{$row}", 'ECFDF5');
            }
            $alternate = !$alternate;
            $row++;
        }

        $overallMargin = $totRev > 0 ? round(($totProfit / $totRev) * 100, 1) : 0;

        // Totals row
        $this->fillRange($sheet, "A{$row}:H{$row}", $headerColor);
        $sheet->setCellValue("A{$row}", 'TOTALS');
        $sheet->setCellValue("D{$row}", $totUnits);
        $sheet->setCellValue("E{$row}", $totRev);
        $sheet->setCellValue("F{$row}", $totCogs);
        $sheet->setCellValue("G{$row}", $totProfit);
        $sheet->setCellValue("H{$row}", $overallMargin);
        foreach (['A', 'D', 'E', 'F', 'G', 'H'] as $col) {
            $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        }
        foreach (['E', 'F', 'G'] as $c) {
            $sheet->getStyle("{$c}{$row}")->getNumberFormat()->setFormatCode('"' . $currSym . '"#,##0.00');
            $sheet->getStyle("{$c}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('0.0"%"');
        $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $row++;

        $this->writeSummaryBlock($sheet, $row, $salonName, [
            ['label' => 'Total Services Sold', 'value' => $totUnits,       'format' => '#,##0',                      'color' => ''],
            ['label' => 'Net Service Revenue',  'value' => $totRev,         'format' => '"' . $currSym . '"#,##0.00', 'color' => '059669'],
            ['label' => 'Total Consumables COGS','value' => $totCogs,       'format' => '"' . $currSym . '"#,##0.00', 'color' => 'DC2626'],
            ['label' => 'Total Gross Profit',   'value' => $totProfit,      'format' => '"' . $currSym . '"#,##0.00', 'color' => $totProfit >= 0 ? '059669' : 'DC2626'],
            ['label' => 'Overall Gross Margin', 'value' => $overallMargin / 100, 'format' => '0.0%',                 'color' => $overallMargin >= 40 ? '059669' : 'DC2626'],
        ], $headerColor, $totalCols);

        foreach (range(1, $totalCols) as $i) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 7. WASTAGE & VARIANCE EXPORT
    // ─────────────────────────────────────────────────────────────────────────
    protected function exportWastage(Spreadsheet $spreadsheet, string $salonName, Request $request): void
    {
        $salonId   = $this->getSalonId();
        $currSym   = currency_symbol();
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Wastage & Variance');

        $headers     = ['Date & Time', 'Item Name', 'Category', 'Incident Type', 'Quantity Lost', 'Unit Cost', 'Total Cost Loss', 'Reported By', 'Reason / Notes'];
        $totalCols   = count($headers);
        $headerColor = 'DC2626'; // crimson red

        $metaLine = "Period: {$startDate} to {$endDate}  |  Currency: {$currSym}";
        $row = $this->writeReportHeader($sheet, $salonName, 'CONSUMABLES WASTAGE & SHRINKAGE REPORT', $headerColor, $metaLine, $totalCols);
        $row = $this->writeColumnHeaders($sheet, $row, $headers, $headerColor);

        $totQty     = 0;
        $totLoss    = 0;
        $incidents  = 0;
        $alternate  = false;

        $query = InventoryTransaction::whereHas('item', function ($q) use ($salonId) {
                $q->where('salon_id', $salonId)->consumables();
            })
            ->with(['item.category', 'user'])
            ->where(function ($q) {
                $q->whereIn('transaction_type', ['damaged', 'lost'])
                  ->orWhere(function ($sub) {
                      $sub->where('transaction_type', 'adjustment')
                          ->where('quantity', '<', 0);
                  });
            })
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->latest();

        $query->chunk(200, function ($transactions) use (
            $sheet, &$row, &$totQty, &$totLoss, &$incidents, &$alternate, $currSym
        ) {
            foreach ($transactions as $tx) {
                $incidents++;
                $qty = abs((float) $tx->quantity);
                $unitCost = (float) $tx->unit_cost;
                $loss = (float) $tx->total_cost;

                $totQty  += $qty;
                $totLoss += $loss;

                $txType = ucfirst($tx->transaction_type ?? 'Wastage');
                if ($tx->transaction_type === 'adjustment') {
                    $txType = 'Inventory Shrinkage';
                }

                $sheet->setCellValue("A{$row}", $tx->created_at?->format('d M Y H:i') ?? '');
                $sheet->setCellValue("B{$row}", $tx->item?->name ?? 'Unknown Item');
                $sheet->setCellValue("C{$row}", $tx->item?->category?->name ?? 'N/A');
                $sheet->setCellValue("D{$row}", $txType);
                $sheet->setCellValue("E{$row}", $qty);
                $sheet->setCellValue("F{$row}", $unitCost);
                $sheet->setCellValue("G{$row}", $loss);
                $sheet->setCellValue("H{$row}", $tx->user?->name ?? 'System');
                $sheet->setCellValue("I{$row}", $tx->notes ?? '');

                $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('"' . $currSym . '"#,##0.00');
                $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('"' . $currSym . '"#,##0.00');
                $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("G{$row}")->getFont()->getColor()->setRGB('DC2626')->setBold(true);

                if ($alternate) {
                    $this->fillRange($sheet, "A{$row}:I{$row}", 'FEF2F2');
                }
                $alternate = !$alternate;
                $row++;
            }
        });

        // Totals row
        $this->fillRange($sheet, "A{$row}:I{$row}", $headerColor);
        $sheet->setCellValue("A{$row}", 'TOTALS');
        $sheet->setCellValue("E{$row}", $totQty);
        $sheet->setCellValue("G{$row}", $totLoss);
        foreach (['A', 'E', 'G'] as $col) {
            $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        }
        $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('"' . $currSym . '"#,##0.00');
        $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $row++;

        $recipeCost = (float) PosSaleItemConsumable::where('salon_id', $salonId)
            ->activeSales()
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('total_cost');

        $wastageRate = ($recipeCost + $totLoss) > 0 ? round(($totLoss / ($recipeCost + $totLoss)) * 100, 1) : 0;

        $this->writeSummaryBlock($sheet, $row, $salonName, [
            ['label' => 'Total Wastage Incidents',  'value' => $incidents,          'format' => '#,##0',                      'color' => ''],
            ['label' => 'Total Quantity Discarded',  'value' => $totQty,             'format' => '#,##0.##',                   'color' => 'DC2626'],
            ['label' => 'Total Direct Wastage Loss', 'value' => $totLoss,            'format' => '"' . $currSym . '"#,##0.00', 'color' => 'DC2626'],
            ['label' => 'Standard Recipe Cost',      'value' => $recipeCost,         'format' => '"' . $currSym . '"#,##0.00', 'color' => ''],
            ['label' => 'Consumables Wastage Rate',  'value' => $wastageRate / 100,  'format' => '0.0%',                       'color' => $wastageRate > 5 ? 'DC2626' : '059669'],
        ], $headerColor, $totalCols);

        foreach (range(1, $totalCols) as $i) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 8. LOW STOCK & REORDER DEFICIT EXPORT
    // ─────────────────────────────────────────────────────────────────────────
    protected function exportLowStock(Spreadsheet $spreadsheet, string $salonName, Request $request): void
    {
        $salonId = $this->getSalonId();
        $currSym = currency_symbol();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Low Stock Alert');

        $headers     = ['Item Name', 'SKU', 'Category', 'Current Stock', 'Reorder Level', 'Deficit Qty', 'Unit Cost', 'Estimated Reorder Cost', 'Stock Status'];
        $totalCols   = count($headers);
        $headerColor = 'B91C1C'; // red

        $metaLine = 'Generated: ' . now()->format('d M Y H:i') . '  |  Currency: ' . $currSym;
        $row = $this->writeReportHeader($sheet, $salonName, 'LOW STOCK & REORDER DEFICIT REPORT', $headerColor, $metaLine, $totalCols);
        $row = $this->writeColumnHeaders($sheet, $row, $headers, $headerColor);

        $totItems       = 0;
        $totDeficit     = 0;
        $totReorderCost = 0;
        $alternate      = false;

        $items = InventoryItem::with('category')
            ->active()
            ->when($salonId, fn($q) => $q->where('salon_id', $salonId))
            ->whereColumn('quantity_in_stock', '<=', 'reorder_level')
            ->orderBy('quantity_in_stock', 'asc')
            ->get();

        foreach ($items as $item) {
            $currentStock = (float) $item->quantity_in_stock;
            $reorderLevel = (float) $item->reorder_level;
            $deficit      = max(0, $reorderLevel - $currentStock);
            $unitCost     = (float) $item->purchase_price;
            $reorderCost  = $deficit * $unitCost;
            $status       = $currentStock <= 0 ? 'Out of Stock' : 'Low Stock';

            $totItems++;
            $totDeficit     += $deficit;
            $totReorderCost += $reorderCost;

            $sheet->setCellValue("A{$row}", $item->name);
            $sheet->setCellValue("B{$row}", $item->sku ?? '');
            $sheet->setCellValue("C{$row}", $item->category?->name ?? 'N/A');
            $sheet->setCellValue("D{$row}", $currentStock);
            $sheet->setCellValue("E{$row}", $reorderLevel);
            $sheet->setCellValue("F{$row}", $deficit);
            $sheet->setCellValue("G{$row}", $unitCost);
            $sheet->setCellValue("H{$row}", $reorderCost);
            $sheet->setCellValue("I{$row}", $status);

            foreach (['D', 'E', 'F'] as $c) {
                $sheet->getStyle("{$c}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            foreach (['G', 'H'] as $c) {
                $sheet->getStyle("{$c}{$row}")->getNumberFormat()->setFormatCode('"' . $currSym . '"#,##0.00');
                $sheet->getStyle("{$c}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }

            $statusColor = $currentStock <= 0 ? 'B91C1C' : 'C2410C';
            $sheet->getStyle("I{$row}")->getFont()->setBold(true)->getColor()->setRGB($statusColor);
            $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($alternate) {
                $this->fillRange($sheet, "A{$row}:I{$row}", 'FEF2F2');
            }
            $alternate = !$alternate;
            $row++;
        }

        // Totals row
        $this->fillRange($sheet, "A{$row}:I{$row}", $headerColor);
        $sheet->setCellValue("A{$row}", 'TOTALS');
        $sheet->setCellValue("D{$row}", $items->sum('quantity_in_stock'));
        $sheet->setCellValue("F{$row}", $totDeficit);
        $sheet->setCellValue("H{$row}", $totReorderCost);
        foreach (['A', 'D', 'F', 'H', 'I'] as $col) {
            $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        }
        foreach (['D', 'F'] as $c) {
            $sheet->getStyle("{$c}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('"' . $currSym . '"#,##0.00');
        $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $row++;

        $this->writeSummaryBlock($sheet, $row, $salonName, [
            ['label' => 'Total Low/Out of Stock Items', 'value' => $totItems,       'format' => '#,##0',                      'color' => 'B91C1C'],
            ['label' => 'Out of Stock Count',           'value' => $items->where('quantity_in_stock', '<=', 0)->count(), 'format' => '#,##0', 'color' => 'B91C1C'],
            ['label' => 'Total Reorder Deficit Qty',    'value' => $totDeficit,     'format' => '#,##0.##',                   'color' => 'C2410C'],
            ['label' => 'Total Capital Needed to Restock','value' => $totReorderCost,'format' => '"' . $currSym . '"#,##0.00', 'color' => 'B91C1C'],
        ], $headerColor, $totalCols);

        foreach (range(1, $totalCols) as $i) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }
    }

    /**
     * Service Consumables Usage Report.
     */
    public function serviceConsumption(Request $request)
    {
        $salonId = $this->getSalonId();
        [$startDate, $endDate, $preset] = $this->resolveDateRange($request);
        $staffId = $request->staff_id;
        $itemId = $request->inventory_item_id;

        $query = PosSaleItemConsumable::where('salon_id', $salonId)
            ->activeSales()
            ->with(['inventoryItem.category', 'posSaleItem.sale.employee', 'sale.employee', 'staff'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($staffId) {
            $query->where('staff_id', $staffId);
        }
        if ($itemId) {
            $query->where('inventory_item_id', $itemId);
        }

        $consumptions = $query->latest()->paginate(50);

        // Summaries
        $productSummary = PosSaleItemConsumable::where('salon_id', $salonId)
            ->activeSales()
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->when($staffId, fn($q) => $q->where('staff_id', $staffId))
            ->when($itemId, fn($q) => $q->where('inventory_item_id', $itemId))
            ->select(
                'inventory_item_id',
                DB::raw('SUM(quantity_used) as total_quantity'),
                DB::raw('SUM(total_cost) as total_cost'),
                'unit'
            )
            ->groupBy('inventory_item_id', 'unit')
            ->with('inventoryItem')
            ->get();

        $staffSummary = PosSaleItemConsumable::where('salon_id', $salonId)
            ->activeSales()
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->when($staffId, fn($q) => $q->where('staff_id', $staffId))
            ->when($itemId, fn($q) => $q->where('inventory_item_id', $itemId))
            ->select(
                'staff_id',
                DB::raw('SUM(total_cost) as total_cost'),
                DB::raw('COUNT(DISTINCT pos_sale_item_id) as total_services')
            )
            ->groupBy('staff_id')
            ->with('staff')
            ->get();

        $baseStatsQuery = PosSaleItemConsumable::where('salon_id', $salonId)
            ->activeSales()
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->when($staffId, fn($q) => $q->where('staff_id', $staffId))
            ->when($itemId, fn($q) => $q->where('inventory_item_id', $itemId));

        $stats = [
            'total_cost' => (float) (clone $baseStatsQuery)->sum('total_cost'),
            'total_items_used' => (float) (clone $baseStatsQuery)->sum('quantity_used'),
            'total_records' => (clone $baseStatsQuery)->count(),
            'total_billed' => (float) (clone $baseStatsQuery)->sum('billed_amount'),
        ];

        $staffList = User::where('salon_id', $salonId)->role('employee')->orderBy('name')->get();
        $consumableItems = InventoryItem::where('salon_id', $salonId)->consumables()->orderBy('name')->get();

        // Chart data for top products
        $chartProductLabels = $productSummary->take(8)->map(fn($p) => $p->inventoryItem->name ?? 'Item')->toArray();
        $chartProductCost = $productSummary->take(8)->map(fn($p) => (float)$p->total_cost)->toArray();

        return view('inventory.reports.service-consumption', compact(
            'consumptions', 'productSummary', 'staffSummary', 'stats',
            'startDate', 'endDate', 'preset', 'staffList', 'consumableItems', 'staffId', 'itemId',
            'chartProductLabels', 'chartProductCost'
        ));
    }

    /**
     * Service Profitability (COGS & Gross Margin) Report.
     */
    public function serviceProfitability(Request $request)
    {
        $salonId = $this->getSalonId();
        [$startDate, $endDate, $preset] = $this->resolveDateRange($request);

        // Services sold through POS in this date range
        $saleItems = PosSaleItem::whereHas('sale', function ($q) use ($salonId, $startDate, $endDate) {
                $q->where('salon_id', $salonId)
                  ->whereNotIn('status', ['voided'])
                  ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->where('item_type', 'App\Models\Service')
            ->with(['service.category', 'consumables', 'sale'])
            ->get();

        // Group by service_id
        $serviceStats = $saleItems->groupBy('item_id')->map(function ($items, $serviceId) {
            $first = $items->first();
            $service = $first->service;
            $quantitySold = (float) $items->sum('quantity');
            $grossRevenue = (float) $items->sum('subtotal');
            $netRevenue = (float) $items->sum(function ($i) {
                $lineNet = (float) $i->subtotal - (float) $i->discount_amount;
                $allocatedCartDiscount = 0;
                $sale = $i->sale;
                if ($sale && (float)$sale->discount > 0 && (float)$sale->subtotal > 0) {
                    $ratio = (float)$i->subtotal / (float)$sale->subtotal;
                    $allocatedCartDiscount = (float)$sale->discount * $ratio;
                }
                return max(0, $lineNet - $allocatedCartDiscount);
            });

            // Calculate total COGS processing each sale item independently
            $cogs = (float) $items->sum(function ($item) use ($service) {
                if ($item->consumables && $item->consumables->isNotEmpty()) {
                    return (float) $item->consumables->sum('total_cost');
                }
                return $service ? (float) ($service->calculateEstimatedCogs() * (float) $item->quantity) : 0.0;
            });

            $grossProfit = $netRevenue - $cogs;
            $marginPct = $netRevenue > 0 ? round(($grossProfit / $netRevenue) * 100, 1) : 0;

            return [
                'service_id' => $serviceId,
                'name' => $service->name ?? $first->item_name ?? 'Unknown Service',
                'category' => $service->category->name ?? 'Uncategorized',
                'price' => (float) ($service->price ?? 0),
                'quantity_sold' => $quantitySold,
                'revenue' => $netRevenue,
                'cogs' => $cogs,
                'gross_profit' => $grossProfit,
                'margin_percent' => $marginPct,
            ];
        })->sortByDesc('revenue')->values();

        $totals = [
            'total_services_sold' => $serviceStats->sum('quantity_sold'),
            'total_revenue' => $serviceStats->sum('revenue'),
            'total_cogs' => $serviceStats->sum('cogs'),
            'total_profit' => $serviceStats->sum('gross_profit'),
            'overall_margin' => $serviceStats->sum('revenue') > 0
                ? round(($serviceStats->sum('gross_profit') / $serviceStats->sum('revenue')) * 100, 1)
                : 0,
        ];

        // Chart data: top 8 services by revenue
        $chartLabels = $serviceStats->take(8)->pluck('name')->toArray();
        $chartRevenue = $serviceStats->take(8)->pluck('revenue')->toArray();
        $chartCogs = $serviceStats->take(8)->pluck('cogs')->toArray();
        $chartProfit = $serviceStats->take(8)->pluck('gross_profit')->toArray();

        // Paginate serviceStats (20 per page)
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $paginatedStats = new LengthAwarePaginator(
            $serviceStats->forPage($currentPage, $perPage)->values(),
            $serviceStats->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('inventory.reports.service-profitability', [
            'serviceStats' => $paginatedStats,
            'totals' => $totals,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'preset' => $preset,
            'chartLabels' => $chartLabels,
            'chartRevenue' => $chartRevenue,
            'chartCogs' => $chartCogs,
            'chartProfit' => $chartProfit,
        ]);
    }

    /**
     * Consumables Wastage & Variance Report.
     */
    public function wastageReport(Request $request)
    {
        $salonId = $this->getSalonId();
        [$startDate, $endDate, $preset] = $this->resolveDateRange($request);

        // Query transactions for damaged/lost/adjustments (shrinkage) on consumable items
        $wastageTransactions = InventoryTransaction::whereHas('item', function ($q) use ($salonId) {
                $q->where('salon_id', $salonId)->consumables();
            })
            ->with(['item.category', 'user'])
            ->where(function ($q) {
                $q->whereIn('transaction_type', ['damaged', 'lost'])
                  ->orWhere(function ($sub) {
                      $sub->where('transaction_type', 'adjustment')
                          ->where('quantity', '<', 0);
                  });
            })
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $totalWastageCost = (float) InventoryTransaction::whereHas('item', function ($q) use ($salonId) {
                $q->where('salon_id', $salonId)->consumables();
            })
            ->where(function ($q) {
                $q->whereIn('transaction_type', ['damaged', 'lost'])
                  ->orWhere(function ($sub) {
                      $sub->where('transaction_type', 'adjustment')
                          ->where('quantity', '<', 0);
                  });
            })
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('total_cost');

        $totalRecipeConsumption = (float) PosSaleItemConsumable::where('salon_id', $salonId)
            ->activeSales()
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('total_cost');

        $wastageRate = ($totalRecipeConsumption + $totalWastageCost) > 0
            ? round(($totalWastageCost / ($totalRecipeConsumption + $totalWastageCost)) * 100, 1)
            : 0;

        $stats = [
            'total_wastage_cost' => $totalWastageCost,
            'total_consumption_cost' => $totalRecipeConsumption,
            'wastage_rate' => $wastageRate,
            'incident_count' => $wastageTransactions->total(),
        ];

        $chartWastageLabels = ['Logged Wastage / Shrinkage', 'Recipe Consumed in Services'];
        $chartWastageValues = [$totalWastageCost, $totalRecipeConsumption];

        return view('inventory.reports.wastage', compact(
            'wastageTransactions', 'stats', 'startDate', 'endDate', 'preset',
            'chartWastageLabels', 'chartWastageValues'
        ));
    }
}
