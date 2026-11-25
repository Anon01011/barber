@extends('layouts.app')

@section('title', 'Inventory Analytics & Reports')

@section('content')
<div class="container-fluid py-3 px-2 px-md-3">
    <!-- Header Title -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <div class="d-flex align-items-center gap-2">
                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                    <i class="fas fa-boxes-stacked fa-lg"></i>
                </div>
                <div>
                    <h1 class="h4 fw-bold mb-0 text-dark">Inventory Analytics &amp; Reports</h1>
                    <span class="text-muted small">Real-time valuation, stock movement trends, recipes, and warehouse health</span>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.inventory.reports.export', ['type' => 'valuation']) }}" class="btn btn-outline-success btn-sm px-3 shadow-sm">
                <i class="fas fa-file-excel me-1"></i> Export Valuation
            </a>
            <a href="{{ route('admin.inventory.alerts.index') }}" class="btn btn-outline-danger btn-sm px-3 shadow-sm position-relative">
                <i class="fas fa-bell me-1"></i> Stock Alerts
                @if(($lowStockCount + $outOfStockCount) > 0)
                    <span class="badge bg-danger rounded-pill ms-1">{{ $lowStockCount + $outOfStockCount }}</span>
                @endif
            </a>
        </div>
    </div>

    <!-- Date Range Filter Component -->
    @include('inventory.reports.partials.date-filter', [
        'preset' => $preset,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'action' => route('admin.inventory.reports.index')
    ])

    @php
        $sym = currency_symbol();
    @endphp

    <!-- ============================================================== -->
    <!-- 8-CARD EXECUTIVE KPI GRID                                       -->
    <!-- ============================================================== -->
    <div class="row row-cols-2 row-cols-md-4 g-2 mb-4">
        <!-- 1. Total Products -->
        <div class="col">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3" style="border-top: 3px solid #0d6efd !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Active Products</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($totalProducts) }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Catalog items</div>
                    </div>
                    <div class="p-2 rounded-2 bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-box"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Total Stock Units -->
        <div class="col">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3" style="border-top: 3px solid #0dcaf0 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Stock Units</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($totalStockUnits, 1) }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Physical units on hand</div>
                    </div>
                    <div class="p-2 rounded-2 bg-info bg-opacity-10 text-info">
                        <i class="fas fa-cubes"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Inventory Value (Cost) -->
        <div class="col">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3" style="border-top: 3px solid #10b981 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Inventory Value (Cost)</div>
                        <div class="fs-4 fw-bold text-success mt-1">{{ $sym }}{{ number_format($totalValuationCost, 2) }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Capital tied up at purchase</div>
                    </div>
                    <div class="p-2 rounded-2 bg-success bg-opacity-10 text-success">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Potential Retail Value -->
        <div class="col">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3" style="border-top: 3px solid #6f42c1 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Potential Retail Value</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ $sym }}{{ number_format($totalValuationRetail, 2) }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Margin: <strong class="text-success">{{ $potentialMargin }}%</strong></div>
                    </div>
                    <div class="p-2 rounded-2" style="background: rgba(111,66,193,0.1); color: #6f42c1;">
                        <i class="fas fa-store"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Potential Profit -->
        <div class="col">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3" style="border-top: 3px solid #20c997 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Potential Profit</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ $sym }}{{ number_format($potentialProfit, 2) }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">If all stock is retailed</div>
                    </div>
                    <div class="p-2 rounded-2 bg-success bg-opacity-10 text-success">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Low Stock Alert -->
        <div class="col">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3" style="border-top: 3px solid #f59e0b !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Stock Alerts</div>
                        <div class="fs-4 fw-bold text-warning mt-1">{{ $lowStockCount }} <span class="fs-6 fw-normal text-danger">({{ $outOfStockCount }} empty)</span></div>
                        <div class="text-muted" style="font-size: 0.72rem;">Below reorder threshold</div>
                    </div>
                    <div class="p-2 rounded-2 bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. Period Movements -->
        <div class="col">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3" style="border-top: 3px solid #3b82f6 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Period Stock Flow</div>
                        <div class="fs-5 fw-bold text-dark mt-1">
                            <span class="text-success">+{{ number_format($stockInUnits, 0) }}</span> /
                            <span class="text-danger">-{{ number_format($stockOutUnits, 0) }}</span>
                        </div>
                        <div class="text-muted" style="font-size: 0.72rem;">{{ $totalTransactionsCount }} logs in period</div>
                    </div>
                    <div class="p-2 rounded-2 bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 8. Backbar Consumables Cost -->
        <div class="col">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3" style="border-top: 3px solid #ef4444 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Backbar Recipe COGS</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ $sym }}{{ number_format($consumablesCost, 2) }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Dispensed in service checkouts</div>
                    </div>
                    <div class="p-2 rounded-2 bg-danger bg-opacity-10 text-danger">
                        <i class="fas fa-flask"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- ROW 1: 3-COLUMN DONUT / PIE CHARTS (COMPACT, BALANCED)         -->
    <!-- ============================================================== -->
    <div class="row g-3 mb-4">
        <!-- Chart 1: Stock Health Distribution -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-heartbeat text-success me-2"></i>Stock Health Status</h6>
                        <span class="text-muted small">Warehouse balance distribution</span>
                    </div>
                </div>
                <div class="card-body pt-1">
                    <div id="chartStockStatus" style="height: 250px;"></div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Category Valuation -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-chart-pie text-primary me-2"></i>Category Valuation</h6>
                        <span class="text-muted small">Top categories by cost</span>
                    </div>
                </div>
                <div class="card-body pt-1">
                    <div id="chartCategoryValuation" style="height: 250px;"></div>
                </div>
            </div>
        </div>

        <!-- Chart 3: Catalog Classification -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-layer-group text-info me-2"></i>Catalog Classification</h6>
                        <span class="text-muted small">Retail vs Backbar vs Dual</span>
                    </div>
                </div>
                <div class="card-body pt-1">
                    <div id="chartClassification" style="height: 250px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- ROW 2: 2-COLUMN TREND & CAPITAL ANALYSIS (7 / 5 SPLIT)         -->
    <!-- ============================================================== -->
    <div class="row g-3 mb-4">
        <!-- Chart 4: Daily Movement In vs Out Trends -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-chart-line text-primary me-2"></i>Stock Inflow vs Outflow Trends</h6>
                        <span class="text-muted small">Daily purchases vs sales &amp; consumption for selected period</span>
                    </div>
                </div>
                <div class="card-body pt-1">
                    <div id="chartMovementTrends" style="height: 275px;"></div>
                </div>
            </div>
        </div>

        <!-- Chart 5: Top Products by Capital Tied Up -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-coins text-warning me-2"></i>Top Capital Tied-Up Items</h6>
                        <span class="text-muted small">Highest investment value on shelves</span>
                    </div>
                </div>
                <div class="card-body pt-1">
                    <div id="chartTopValued" style="height: 275px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- ROW 3: 2-COLUMN LOW STOCK ACTION & DEEP-DIVE LAUNCHER          -->
    <!-- ============================================================== -->
    <div class="row g-3 mb-4">
        <!-- Chart 6: Critical Stock vs Reorder Level -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Critical Stock Deficit</h6>
                        <span class="text-muted small">Current stock vs configured reorder point</span>
                    </div>
                    <a href="{{ route('admin.inventory.reports.low-stock') }}" class="btn btn-outline-danger btn-sm py-1 px-2" style="font-size:0.75rem;">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body pt-1">
                    <div id="chartLowStock" style="height: 275px;"></div>
                </div>
            </div>
        </div>

        <!-- Deep-Dive Quick Reports Launcher -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-compass text-primary me-2"></i>Detailed Inventory Reports</h6>
                        <span class="text-muted small">Specialized modules and audit trails</span>
                    </div>
                </div>
                <div class="card-body pt-1">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('admin.inventory.reports.stock-valuation') }}" class="p-3 rounded-3 border bg-light d-block text-decoration-none hover-lift h-100">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-wallet text-primary me-2"></i>
                                    <span class="fw-bold text-dark small">Stock Valuation</span>
                                </div>
                                <span class="text-muted d-block" style="font-size: 0.72rem;">Cost vs retail breakdown &amp; item valuations</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.inventory.reports.stock-movement') }}" class="p-3 rounded-3 border bg-light d-block text-decoration-none hover-lift h-100">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-exchange-alt text-success me-2"></i>
                                    <span class="fw-bold text-dark small">Stock Movement</span>
                                </div>
                                <span class="text-muted d-block" style="font-size: 0.72rem;">Full audit log of purchases, sales &amp; transfers</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.inventory.reports.profit-analysis') }}" class="p-3 rounded-3 border bg-light d-block text-decoration-none hover-lift h-100">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-percent text-info me-2"></i>
                                    <span class="fw-bold text-dark small">Profit Margins</span>
                                </div>
                                <span class="text-muted d-block" style="font-size: 0.72rem;">Analyze margin spread &amp; most profitable products</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.inventory.reports.dead-stock') }}" class="p-3 rounded-3 border bg-light d-block text-decoration-none hover-lift h-100">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-hourglass-end text-warning me-2"></i>
                                    <span class="fw-bold text-dark small">Dead / Slow Stock</span>
                                </div>
                                <span class="text-muted d-block" style="font-size: 0.72rem;">Identify items with zero movement in 90+ days</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.inventory.reports.service-consumption') }}" class="p-3 rounded-3 border bg-light d-block text-decoration-none hover-lift h-100">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-flask text-danger me-2"></i>
                                    <span class="fw-bold text-dark small">Recipe Usage</span>
                                </div>
                                <span class="text-muted d-block" style="font-size: 0.72rem;">Backbar usage logged during POS checkouts</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.inventory.reports.service-profitability') }}" class="p-3 rounded-3 border bg-light d-block text-decoration-none hover-lift h-100">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-balance-scale text-success me-2"></i>
                                    <span class="fw-bold text-dark small">Service Margins</span>
                                </div>
                                <span class="text-muted d-block" style="font-size: 0.72rem;">Net service revenue vs recipe product COGS</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.inventory.reports.wastage') }}" class="p-3 rounded-3 border bg-light d-block text-decoration-none hover-lift h-100">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-trash-alt text-secondary me-2"></i>
                                    <span class="fw-bold text-dark small">Wastage &amp; Loss</span>
                                </div>
                                <span class="text-muted d-block" style="font-size: 0.72rem;">Audit shrinkage, expired bottles and damaged stock</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.inventory.reports.low-stock') }}" class="p-3 rounded-3 border bg-light d-block text-decoration-none hover-lift h-100">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-bell text-danger me-2"></i>
                                    <span class="fw-bold text-dark small">Restock Priority</span>
                                </div>
                                <span class="text-muted d-block" style="font-size: 0.72rem;">Items needing reorder with estimated purchase cost</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    function initInventoryDashboardCharts() {
        if (typeof ApexCharts === 'undefined') {
            console.warn('ApexCharts library not loaded');
            return;
        }

        const sym = '{{ $sym }}';

        // --- 1. Stock Health Distribution (Donut) ---
        const stockStatusEl = document.querySelector("#chartStockStatus");
        if (stockStatusEl) {
            stockStatusEl.innerHTML = '';
            const statusSeries = @json($chartStockStatus['series']);
            const statusLabels = @json($chartStockStatus['labels']);
            const statusColors = @json($chartStockStatus['colors']);

            const stockStatusOptions = {
                chart: { type: 'donut', height: 250, fontFamily: 'inherit' },
                series: statusSeries,
                labels: statusLabels,
                colors: statusColors,
                legend: { position: 'bottom', fontSize: '11px', markers: { width: 8, height: 8 } },
                dataLabels: { enabled: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: { fontSize: '12px', fontWeight: 600, color: '#64748b' },
                                value: { fontSize: '16px', fontWeight: 700, color: '#0f172a' },
                                total: {
                                    show: true,
                                    label: 'Products',
                                    fontSize: '11px',
                                    fontWeight: 600,
                                    color: '#64748b',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                tooltip: { theme: 'dark' }
            };
            const stockStatusChart = new ApexCharts(stockStatusEl, stockStatusOptions);
            stockStatusChart.render();
        }

        // --- 2. Category Valuation (Donut) ---
        const catValEl = document.querySelector("#chartCategoryValuation");
        if (catValEl) {
            catValEl.innerHTML = '';
            const catLabels = @json($chartCategoryValuation['labels']);
            const catSeries = @json($chartCategoryValuation['series']);
            const catColors = @json($chartCategoryValuation['colors']);

            const catValOptions = {
                chart: { type: 'donut', height: 250, fontFamily: 'inherit' },
                series: catSeries,
                labels: catLabels,
                colors: catColors,
                legend: { position: 'bottom', fontSize: '11px', markers: { width: 8, height: 8 } },
                dataLabels: { enabled: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: { fontSize: '12px', fontWeight: 600, color: '#64748b' },
                                value: {
                                    fontSize: '15px',
                                    fontWeight: 700,
                                    formatter: val => sym + Number(val).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })
                                },
                                total: {
                                    show: true,
                                    label: 'Valuation',
                                    fontSize: '11px',
                                    fontWeight: 600,
                                    color: '#64748b',
                                    formatter: function (w) {
                                        const sum = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        return sym + Number(sum).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                                    }
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: val => sym + Number(val).toLocaleString(undefined, { minimumFractionDigits: 2 })
                    }
                }
            };
            const catValChart = new ApexCharts(catValEl, catValOptions);
            catValChart.render();
        }

        // --- 3. Catalog Classification (Donut) ---
        const classEl = document.querySelector("#chartClassification");
        if (classEl) {
            classEl.innerHTML = '';
            const classSeries = @json($chartClassification['series']);
            const classLabels = @json($chartClassification['labels']);
            const classColors = @json($chartClassification['colors']);

            const classOptions = {
                chart: { type: 'donut', height: 250, fontFamily: 'inherit' },
                series: classSeries,
                labels: classLabels,
                colors: classColors,
                legend: { position: 'bottom', fontSize: '11px', markers: { width: 8, height: 8 } },
                dataLabels: { enabled: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: { fontSize: '12px', fontWeight: 600, color: '#64748b' },
                                value: { fontSize: '16px', fontWeight: 700, color: '#0f172a' },
                                total: {
                                    show: true,
                                    label: 'Types',
                                    fontSize: '11px',
                                    fontWeight: 600,
                                    color: '#64748b',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                tooltip: { theme: 'dark' }
            };
            const classChart = new ApexCharts(classEl, classOptions);
            classChart.render();
        }

        // --- 4. Daily Movement Trends (Area) ---
        const moveEl = document.querySelector("#chartMovementTrends");
        if (moveEl) {
            moveEl.innerHTML = '';
            const trendDates = @json($trendDates);
            const trendIn = @json($trendIn);
            const trendOut = @json($trendOut);

            const moveOptions = {
                chart: {
                    type: 'area',
                    height: 275,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                series: [
                    { name: 'Stock In (Purchases/Returns)', data: trendIn },
                    { name: 'Stock Out (Sales/Consumption)', data: trendOut }
                ],
                xaxis: {
                    categories: trendDates,
                    labels: { style: { colors: '#64748b', fontSize: '10px' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        formatter: val => Number(val).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }),
                        style: { colors: '#64748b', fontSize: '10px' }
                    }
                },
                colors: ['#10b981', '#ef4444'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.35,
                        opacityTo: 0.05,
                        stops: [0, 95, 100]
                    }
                },
                stroke: { width: 2.2, curve: 'smooth' },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                legend: { position: 'top', horizontalAlign: 'right', fontSize: '11px' },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: val => Number(val).toLocaleString(undefined, { minimumFractionDigits: 1 }) + ' units'
                    }
                }
            };
            const moveChart = new ApexCharts(moveEl, moveOptions);
            moveChart.render();
        }

        // --- 5. Top Valued Items (Horizontal Bar) ---
        const topValEl = document.querySelector("#chartTopValued");
        if (topValEl) {
            topValEl.innerHTML = '';
            const topValLabels = @json($chartTopValued['labels']);
            const topValSeries = @json($chartTopValued['series']);

            const topValOptions = {
                chart: {
                    type: 'bar',
                    height: 275,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                series: [{ name: 'Inventory Value', data: topValSeries }],
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '55%',
                        borderRadius: 4,
                        distributed: true
                    }
                },
                colors: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#ec4899'],
                xaxis: {
                    categories: topValLabels,
                    labels: {
                        formatter: val => sym + Number(val).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }),
                        style: { colors: '#64748b', fontSize: '10px' }
                    }
                },
                yaxis: {
                    labels: { style: { colors: '#334155', fontSize: '11px', fontWeight: 500 } }
                },
                legend: { show: false },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: val => sym + Number(val).toLocaleString(undefined, { minimumFractionDigits: 2 })
                    }
                }
            };
            const topValChart = new ApexCharts(topValEl, topValOptions);
            topValChart.render();
        }

        // --- 6. Low Stock Deficit (Grouped Bar) ---
        const lowEl = document.querySelector("#chartLowStock");
        if (lowEl) {
            lowEl.innerHTML = '';
            const lowLabels = @json($chartLowStock['labels']);
            const currentStock = @json($chartLowStock['current']);
            const reorderPoint = @json($chartLowStock['reorder']);

            const lowOptions = {
                chart: {
                    type: 'bar',
                    height: 275,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                series: [
                    { name: 'Current Stock', data: currentStock },
                    { name: 'Reorder Level', data: reorderPoint }
                ],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '50%',
                        borderRadius: 4
                    }
                },
                colors: ['#ef4444', '#f59e0b'],
                xaxis: {
                    categories: lowLabels,
                    labels: {
                        style: { colors: '#64748b', fontSize: '10px' },
                        rotate: -25,
                        rotateAlways: true
                    }
                },
                yaxis: {
                    labels: {
                        formatter: val => Number(val).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }),
                        style: { colors: '#64748b', fontSize: '10px' }
                    }
                },
                legend: { position: 'top', horizontalAlign: 'right', fontSize: '11px' },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,
                    padding: { bottom: 35, left: 10, right: 10 }
                },
                tooltip: {
                    theme: 'dark',
                    x: {
                        formatter: function(val, opts) {
                            return lowLabels[opts.dataPointIndex] || val;
                        }
                    },
                    y: {
                        formatter: val => Number(val).toLocaleString(undefined, { minimumFractionDigits: 1 }) + ' units'
                    }
                }
            };
            const lowChart = new ApexCharts(lowEl, lowOptions);
            lowChart.render();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initInventoryDashboardCharts);
    } else {
        initInventoryDashboardCharts();
    }
})();
</script>
@endpush
