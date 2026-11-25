@extends('layouts.app')

@section('title', 'Profit Analysis Report')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h4 mb-1 fw-bold">
                        <i class="fas fa-chart-line text-success me-2"></i>Inventory Profit & Margin Analysis
                    </h2>
                    <p class="text-muted mb-0 small">Margin breakdown and profit potential across inventory catalogue</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.inventory.reports.export', 'profit') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </a>
                    <a href="{{ route('admin.inventory.reports.index', ['salon_slug' => request()->route('salon_slug')]) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Average Profit Margin</div>
                    <div class="fs-4 fw-bold text-primary">{{ $stats['avg_profit_margin'] }}%</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total Potential Profit</div>
                    <div class="fs-4 fw-bold text-success">{{ currency_symbol() }}{{ number_format($stats['total_potential_profit'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Highest Margin Item</div>
                    <div class="fw-bold text-truncate text-dark">{{ $stats['highest_margin_item']['name'] ?? 'N/A' }}</div>
                    <small class="text-success">{{ $stats['highest_margin_item']['profit_margin'] ?? 0 }}% margin</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Lowest Margin Item</div>
                    <div class="fw-bold text-truncate text-dark">{{ $stats['lowest_margin_item']['name'] ?? 'N/A' }}</div>
                    <small class="text-warning">{{ $stats['lowest_margin_item']['profit_margin'] ?? 0 }}% margin</small>
                </div>
            </div>
        </div>
    </div>

    {{-- 2-Column Responsive Layout: Chart & Margin Tiers --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-chart-line text-success me-2"></i>Top Products by Profit Margin (%)</h6>
                    <span class="text-muted small">Products delivering highest markup percentage</span>
                </div>
                <div class="card-body pb-3" style="overflow: visible;">
                    @if(count($chartMarginLabels ?? []) > 0)
                        <div id="marginChart" style="min-height: 350px;"></div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-line fa-2x mb-2 d-block opacity-50"></i>
                            No margin data to display.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-layer-group text-primary me-2"></i>Profit Margin Health &amp; Tiers</h6>
                    <span class="text-muted small">Portfolio margin distribution</span>
                </div>
                <div class="card-body">
                    <div class="p-3 bg-success bg-opacity-10 rounded-3 mb-3 border border-success border-opacity-25">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-success fw-bold small text-uppercase">Average Portfolio Margin</span>
                                <h4 class="fw-bold text-success mb-0">{{ $stats['avg_profit_margin'] ?? 0 }}%</h4>
                            </div>
                            <div class="text-end">
                                <span class="text-muted small d-block">Total Potential Profit</span>
                                <span class="fw-bold text-dark fs-6">{{ currency_symbol() }}{{ number_format($stats['total_potential_profit'] ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <div class="p-2 border rounded-2 bg-light d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success p-1 rounded-circle" style="width:10px;height:10px;"></span>
                                <div>
                                    <strong class="small d-block text-dark">High Margin Tier (&gt; 50%)</strong>
                                    <span class="text-muted" style="font-size: 0.72rem;">Highest profitability items; ideal for upselling.</span>
                                </div>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold">Optimal</span>
                        </div>
                        <div class="p-2 border rounded-2 bg-light d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-warning p-1 rounded-circle" style="width:10px;height:10px;"></span>
                                <div>
                                    <strong class="small d-block text-dark">Moderate Margin Tier (20% &ndash; 50%)</strong>
                                    <span class="text-muted" style="font-size: 0.72rem;">Standard retail baseline markup.</span>
                                </div>
                            </div>
                            <span class="badge bg-warning bg-opacity-10 text-warning-emphasis fw-bold">Standard</span>
                        </div>
                        <div class="p-2 border rounded-2 bg-light d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-danger p-1 rounded-circle" style="width:10px;height:10px;"></span>
                                <div>
                                    <strong class="small d-block text-dark">Low Margin Tier (&lt; 20%)</strong>
                                    <span class="text-muted" style="font-size: 0.72rem;">Review supplier cost negotiations or pricing.</span>
                                </div>
                            </div>
                            <span class="badge bg-danger bg-opacity-10 text-danger fw-bold">Review</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-list text-secondary me-2"></i>Product Profit Margin Breakdown</h6>
            <span class="text-muted small">Showing {{ $items->count() }} of {{ $items->total() }} items</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th class="text-end">Cost Price</th>
                            <th class="text-end">Selling Price</th>
                            <th class="text-end">Profit / Unit</th>
                            <th class="text-end">Margin %</th>
                            <th class="text-end">In Stock</th>
                            <th class="text-end">Total Potential Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item['name'] }}</td>
                                <td><code class="text-muted small">{{ $item['sku'] }}</code></td>
                                <td><span class="badge bg-secondary-subtle text-secondary">{{ $item['category'] }}</span></td>
                                <td class="text-end">{{ currency_symbol() }}{{ number_format($item['purchase_price'], 2) }}</td>
                                <td class="text-end">{{ currency_symbol() }}{{ number_format($item['selling_price'], 2) }}</td>
                                <td class="text-end fw-semibold text-success">{{ currency_symbol() }}{{ number_format($item['profit_per_item'], 2) }}</td>
                                <td class="text-end">
                                    <span class="badge {{ $item['profit_margin'] >= 40 ? 'bg-success' : ($item['profit_margin'] >= 20 ? 'bg-primary' : 'bg-warning text-dark') }}">
                                        {{ $item['profit_margin'] }}%
                                    </span>
                                </td>
                                <td class="text-end">{{ number_format($item['quantity'], 2) }}</td>
                                <td class="text-end fw-bold text-success">{{ currency_symbol() }}{{ number_format($item['total_potential_profit'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-boxes fa-2x mb-2 d-block"></i>No products found.
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
    const rawLabels = @json($chartMarginLabels ?? []);
    const margins = @json($chartMarginValues ?? []);

    if (rawLabels.length > 0 && document.querySelector('#marginChart')) {
        function escapeHtml(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        // Truncate labels on the axis to keep clean spacing; full product name is displayed in tooltip on hover
        const shortLabels = rawLabels.map(name => {
            if (!name) return '';
            return name.length > 15 ? name.substring(0, 13) + '…' : name;
        });

        const options = {
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false },
                fontFamily: 'inherit',
                parentHeightOffset: 0
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '40%',
                    borderRadius: 6,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: v => parseFloat(v).toFixed(1) + '%',
                offsetY: -22,
                style: {
                    fontSize: '11px',
                    fontWeight: 700,
                    colors: ['#0f172a']
                }
            },
            series: [{ name: 'Profit Margin', data: margins }],
            xaxis: {
                categories: shortLabels,
                labels: {
                    rotate: -35,
                    rotateAlways: true,
                    hideOverlappingLabels: false,
                    trim: true,
                    maxHeight: 80,
                    offsetY: 2,
                    style: {
                        fontSize: '11px',
                        fontWeight: 600,
                        colors: '#64748b'
                    }
                },
                axisBorder: { show: false },
                axisTicks: { show: false },
                tooltip: {
                    enabled: true,
                    formatter: function(val, opts) {
                        const idx = opts ? opts.dataPointIndex : -1;
                        return (idx >= 0 && rawLabels[idx]) ? rawLabels[idx] : val;
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: v => v + '%',
                    style: { fontSize: '11px', colors: '#64748b' }
                },
                max: 100
            },
            colors: ['#10b981'],
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                padding: {
                    bottom: 30,
                    left: 15,
                    right: 15,
                    top: 10
                }
            },
            tooltip: {
                theme: 'dark',
                custom: function({series, seriesIndex, dataPointIndex, w}) {
                    const fullName = escapeHtml(rawLabels[dataPointIndex] || 'Product');
                    const val = series[seriesIndex][dataPointIndex];
                    return `
                        <div style="padding: 10px 14px; background: #0f172a; border-radius: 8px; color: #fff; font-size: 12px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.12);">
                            <div style="font-weight: 700; font-size: 13px; margin-bottom: 5px; color: #f8fafc; max-width: 280px; word-break: break-word; line-height: 1.3;">${fullName}</div>
                            <div style="display: flex; align-items: center; gap: 8px; color: #34d399; font-weight: 600;">
                                <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#10b981;"></span>
                                <span>Profit Margin: <strong>${parseFloat(val).toFixed(2)}%</strong></span>
                            </div>
                        </div>
                    `;
                }
            }
        };
        const chart = new ApexCharts(document.querySelector('#marginChart'), options);
        chart.render();
    }
})();
</script>
@endpush
