@extends('layouts.app')

@section('title', 'Stock Valuation Report')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h4 mb-1 fw-bold">
                        <i class="fas fa-dollar-sign text-primary me-2"></i>Stock Valuation Report
                    </h2>
                    <p class="text-muted mb-0 small">Total value of inventory at purchase and selling price</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.inventory.reports.export', 'valuation') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </a>
                    <a href="{{ route('admin.inventory.reports.index', ['salon_slug' => request()->route('salon_slug')]) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                            <i class="fas fa-boxes text-primary fa-lg"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold">{{ $totals['total_items'] }}</div>
                            <div class="text-muted small">Total Items</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                            <i class="fas fa-cubes text-info fa-lg"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold">{{ number_format($totals['total_quantity'], 2) }}</div>
                            <div class="text-muted small">Total Units</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-3 rounded me-3">
                            <i class="fas fa-shopping-cart text-warning fa-lg"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold">{{ currency_symbol() }}{{ number_format($totals['total_purchase_value'], 2) }}</div>
                            <div class="text-muted small">Purchase Value</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                            <i class="fas fa-chart-line text-success fa-lg"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold">{{ currency_symbol() }}{{ number_format($totals['total_potential_profit'], 2) }}</div>
                            <div class="text-muted small">Potential Profit</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2-Column Responsive Layout: Chart & Category Distribution Table --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-chart-pie text-primary me-2"></i>Stock Valuation by Category</h6>
                    <span class="text-muted small">Top 6 categories by capital investment</span>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    @if(count($chartCategoryLabels ?? []) > 0)
                        <div id="valuationChart" class="w-100" style="min-height: 280px;"></div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-pie fa-2x mb-2 d-block opacity-50"></i>
                            No category data available to display.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-layer-group text-secondary me-2"></i>Category Capital Distribution</h6>
                        <span class="text-muted small">Share of total inventory purchase value</span>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ count($categoryBreakdown ?? []) }} Categories</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr style="font-size: 0.75rem;">
                                    <th>Category</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-end">Units</th>
                                    <th class="text-end">Valuation</th>
                                    <th style="width: 130px;">Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($categoryBreakdown ?? collect())->take(6) as $catName => $catData)
                                    <tr>
                                        <td class="fw-semibold text-dark">{{ $catName }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-secondary border rounded-pill">{{ $catData['count'] }}</span>
                                        </td>
                                        <td class="text-end font-monospace small">{{ number_format($catData['quantity'], 1) }}</td>
                                        <td class="text-end font-monospace fw-bold text-dark">{{ currency_symbol() }}{{ number_format($catData['purchase_value'], 2) }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-fill" style="height: 6px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $catData['percent'] }}%;" aria-valuenow="{{ $catData['percent'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="text-muted small" style="font-size: 0.72rem; min-width: 32px;">{{ $catData['percent'] }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">No category breakdown available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-list text-secondary me-2"></i>Product Valuation Details</h6>
            <span class="text-muted small">Showing {{ $items->count() }} of {{ $items->total() }} items</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Purchase Price</th>
                            <th class="text-end">Selling Price</th>
                            <th class="text-end">Purchase Value</th>
                            <th class="text-end">Selling Value</th>
                            <th class="text-end">Potential Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item['name'] }}</td>
                                <td><code class="text-muted small">{{ $item['sku'] }}</code></td>
                                <td><span class="badge bg-secondary-subtle text-secondary">{{ $item['category'] }}</span></td>
                                <td class="text-end">{{ number_format($item['quantity'], 2) }} <small class="text-muted">{{ $item['unit_type'] }}</small></td>
                                <td class="text-end">{{ currency_symbol() }}{{ number_format($item['purchase_price'], 2) }}</td>
                                <td class="text-end">{{ currency_symbol() }}{{ number_format($item['selling_price'], 2) }}</td>
                                <td class="text-end fw-semibold">{{ currency_symbol() }}{{ number_format($item['purchase_value'], 2) }}</td>
                                <td class="text-end fw-semibold text-primary">{{ currency_symbol() }}{{ number_format($item['selling_value'], 2) }}</td>
                                <td class="text-end fw-bold {{ $item['potential_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ currency_symbol() }}{{ number_format($item['potential_profit'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fa-2x mb-2 d-block"></i>No inventory items found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($items->count() > 0)
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="6">Totals (Current Page)</td>
                            <td class="text-end">{{ currency_symbol() }}{{ number_format($items->sum('purchase_value'), 2) }}</td>
                            <td class="text-end text-primary">{{ currency_symbol() }}{{ number_format($items->sum('selling_value'), 2) }}</td>
                            <td class="text-end text-success">{{ currency_symbol() }}{{ number_format($items->sum('potential_profit'), 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
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
    const labels = @json($chartCategoryLabels ?? []);
    const values = @json($chartCategoryValues ?? []);

    if (labels.length > 0 && document.querySelector('#valuationChart')) {
        const options = {
            chart: { type: 'donut', height: 290 },
            labels: labels,
            series: values,
            colors: ['#0d6efd', '#20c997', '#ffc107', '#fd7e14', '#6f42c1', '#0dcaf0'],
            legend: { position: 'bottom' },
            tooltip: {
                y: { formatter: v => '{{ currency_symbol() }}' + parseFloat(v).toFixed(2) }
            },
            dataLabels: { enabled: true, formatter: (val) => val.toFixed(1) + '%' }
        };
        const chart = new ApexCharts(document.querySelector('#valuationChart'), options);
        chart.render();
    }
})();
</script>
@endpush
