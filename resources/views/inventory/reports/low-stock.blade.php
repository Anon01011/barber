@extends('layouts.app')

@section('title', 'Low Stock Alert Report')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h4 mb-1 fw-bold">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>Low Stock Alert Report
                    </h2>
                    <p class="text-muted mb-0 small">Items at or below their designated reorder threshold</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.inventory.reports.export', ['type' => 'low-stock']) }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </a>
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Quick Restock
                    </a>
                    <a href="{{ route('admin.inventory.reports.index', ['salon_slug' => request()->route('salon_slug')]) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-3 rounded me-3">
                            <i class="fas fa-exclamation-triangle text-warning fa-lg"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold text-warning">{{ $stats['total_low_stock'] ?? $items->total() }}</div>
                            <div class="text-muted small">Low Stock Items</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 p-3 rounded me-3">
                            <i class="fas fa-times-circle text-danger fa-lg"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold text-danger">{{ $stats['out_of_stock'] ?? 0 }}</div>
                            <div class="text-muted small">Critical Zero Stock</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                            <i class="fas fa-coins text-primary fa-lg"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold text-primary">{{ currency_symbol() }}{{ number_format($stats['total_reorder_cost'] ?? 0, 2) }}</div>
                            <div class="text-muted small">Est. Reorder Cost to Par</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2-Column Responsive Layout: Chart & Action Plan --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-chart-bar text-warning me-2"></i>Stock Level vs Reorder Threshold</h6>
                    <span class="text-muted small">Top depleted items compared against minimum par</span>
                </div>
                <div class="card-body">
                    @if(count($chartLowLabels ?? []) > 0)
                        <div id="lowStockChart" style="min-height: 280px;"></div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-shield-alt text-success fa-2x mb-2 d-block"></i>
                            All stock levels are optimal.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-truck-loading text-primary me-2"></i>Restock Priority Action</h6>
                    <span class="text-muted small">Purchasing requirement to restore par levels</span>
                </div>
                <div class="card-body">
                    <div class="p-3 bg-warning bg-opacity-10 rounded-3 mb-3 border border-warning border-opacity-25">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-warning-emphasis fw-bold small text-uppercase">Est. Capital to Restore Par</span>
                                <h4 class="fw-bold text-dark mb-0">{{ currency_symbol() }}{{ number_format($stats['total_reorder_cost'] ?? 0, 2) }}</h4>
                            </div>
                            <span class="badge bg-warning text-dark rounded-pill">{{ $stats['total_low_stock'] ?? 0 }} Items</span>
                        </div>
                        <span class="text-muted small d-block mt-1">Includes {{ $stats['out_of_stock'] ?? 0 }} completely exhausted products.</span>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <div class="p-2 border rounded-2 bg-light d-flex align-items-center justify-content-between">
                            <div>
                                <span class="small fw-bold text-dark d-block">Immediate Vendor Restock</span>
                                <span class="text-muted" style="font-size: 0.72rem;">Review supplier reorder templates to dispatch POs.</span>
                            </div>
                            <a href="{{ route('admin.inventory.alerts.index') }}" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size: 0.75rem;">
                                Alert Center <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="p-2 border rounded-2 bg-light d-flex align-items-center justify-content-between">
                            <div>
                                <span class="small fw-bold text-dark d-block">Quick Stock Adjustment</span>
                                <span class="text-muted" style="font-size: 0.72rem;">Receive received shipments into warehouse stock.</span>
                            </div>
                            <a href="{{ route('admin.inventory.index') }}" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 0.75rem;">
                                Inventory <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-list text-secondary me-2"></i>Low Stock Products List</h6>
            <span class="text-muted small">Showing {{ $items->count() }} of {{ $items->total() }} items</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Item Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th class="text-end">Current Stock</th>
                            <th class="text-end">Reorder Level</th>
                            <th class="text-end">Deficit</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            @php
                                $deficit = max(0, $item->reorder_level - $item->quantity_in_stock);
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $item->name }}</td>
                                <td><code class="text-muted small">{{ $item->sku }}</code></td>
                                <td><span class="badge bg-secondary-subtle text-secondary">{{ $item->category->name ?? 'Uncategorized' }}</span></td>
                                <td>
                                    <span class="badge bg-info-subtle text-info text-capitalize">{{ $item->product_type ?? 'retail' }}</span>
                                </td>
                                <td class="text-end fw-bold text-danger">
                                    {{ number_format($item->quantity_in_stock, 2) }} <small class="text-muted">{{ $item->base_unit ?? $item->unit_type }}</small>
                                </td>
                                <td class="text-end fw-semibold">
                                    {{ number_format($item->reorder_level, 2) }}
                                </td>
                                <td class="text-end text-danger fw-bold">
                                    -{{ number_format($deficit, 2) }}
                                </td>
                                <td>
                                    @if($item->quantity_in_stock <= 0)
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Low Stock</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-shield-alt fa-2x text-success mb-2 d-block"></i>
                                    All inventory levels are healthy! No items are below their reorder threshold.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($items->hasPages())
            <div class="card-footer bg-white py-2 border-0">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const rawLabels = @json($chartLowLabels ?? []);
    const stockValues = @json($chartStockValues ?? []);
    const reorderValues = @json($chartReorderValues ?? []);

    if (rawLabels.length > 0 && document.querySelector('#lowStockChart')) {
        const shortLabels = rawLabels.map(name => {
            if (!name) return '';
            return name.length > 13 ? name.substring(0, 11) + '…' : name;
        });

        const options = {
            chart: {
                type: 'bar',
                height: 330,
                toolbar: { show: false },
                fontFamily: 'inherit',
                parentHeightOffset: 0
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 4
                }
            },
            dataLabels: { enabled: false },
            series: [
                { name: 'Current Stock', data: stockValues },
                { name: 'Reorder Threshold', data: reorderValues }
            ],
            xaxis: {
                categories: shortLabels,
                labels: {
                    rotate: -25,
                    rotateAlways: true,
                    hideOverlappingLabels: false,
                    trim: false,
                    style: {
                        fontSize: '11px',
                        colors: '#64748b'
                    }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    formatter: v => parseFloat(v).toFixed(0),
                    style: { fontSize: '11px', colors: '#64748b' }
                }
            },
            colors: ['#dc3545', '#f59e0b'],
            legend: { position: 'top', horizontalAlign: 'right', fontSize: '11px' },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                padding: {
                    bottom: 35,
                    left: 10,
                    right: 10,
                    top: 10
                }
            },
            tooltip: {
                theme: 'dark',
                x: {
                    formatter: function(val, opts) {
                        return rawLabels[opts.dataPointIndex] || val;
                    }
                },
                y: {
                    formatter: function(v, opts) {
                        return parseFloat(v).toFixed(2) + ' units';
                    }
                }
            }
        };
        const chart = new ApexCharts(document.querySelector('#lowStockChart'), options);
        chart.render();
    }
})();
</script>
@endpush
