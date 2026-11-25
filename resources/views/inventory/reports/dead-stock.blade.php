@extends('layouts.app')

@section('title', 'Dead Stock Report')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h4 mb-1 fw-bold">
                        <i class="fas fa-hourglass-end text-secondary me-2"></i>Dead Stock Report
                    </h2>
                    <p class="text-muted mb-0 small">Identify items with zero inventory movement over a specified period</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.inventory.reports.export', ['type' => 'dead-stock', 'days' => $days]) }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </a>
                    <a href="{{ route('admin.inventory.reports.index', ['salon_slug' => request()->route('salon_slug')]) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Inactivity Period (Days)</label>
                    <select name="days" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="30" {{ ($days ?? 90) == 30 ? 'selected' : '' }}>No movement in 30 days</option>
                        <option value="60" {{ ($days ?? 90) == 60 ? 'selected' : '' }}>No movement in 60 days</option>
                        <option value="90" {{ ($days ?? 90) == 90 ? 'selected' : '' }}>No movement in 90 days (Default)</option>
                        <option value="180" {{ ($days ?? 90) == 180 ? 'selected' : '' }}>No movement in 180 days</option>
                        <option value="365" {{ ($days ?? 90) == 365 ? 'selected' : '' }}>No movement in 1 year</option>
                    </select>
                </div>
                <div class="col-md-8 text-md-end">
                    <span class="text-muted small">Total Dead Capital Tied Up:</span>
                    <span class="fs-5 fw-bold text-danger ms-2">{{ currency_symbol() }}{{ number_format($totalValue ?? 0, 2) }}</span>
                </div>
            </form>
        </div>
    </div>

    {{-- 2-Column Responsive Layout: Chart & Liquidation Insights --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-chart-bar text-danger me-2"></i>Highest Capital Tied Up in Dead Stock</h6>
                    <span class="text-muted small">Top items with zero activity in {{ $days }} days</span>
                </div>
                <div class="card-body">
                    @if(count($chartDeadLabels ?? []) > 0)
                        <div id="deadStockChart" style="min-height: 260px;"></div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle text-success fa-2x mb-2 d-block"></i>
                            No dead stock items to display in chart.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-lightbulb text-warning me-2"></i>Capital Recovery &amp; Liquidation</h6>
                    <span class="text-muted small">Recommended actions for slow-moving inventory</span>
                </div>
                <div class="card-body">
                    <div class="p-3 bg-danger bg-opacity-10 rounded-3 mb-3 border border-danger border-opacity-25">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-danger fw-bold small text-uppercase">Tied Up Capital</span>
                                <h4 class="fw-bold text-danger mb-0">{{ currency_symbol() }}{{ number_format($totalValue ?? 0, 2) }}</h4>
                            </div>
                            <span class="badge bg-danger rounded-pill">{{ $items->total() }} Items</span>
                        </div>
                        <span class="text-muted small d-block mt-1">Capital sitting idle with zero movements in {{ $days }} days.</span>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <div class="p-2 border rounded-2 bg-light d-flex align-items-start gap-2">
                            <i class="fas fa-tags text-primary mt-1"></i>
                            <div>
                                <strong class="small d-block text-dark">Promotional Bundle / Sale</strong>
                                <span class="text-muted" style="font-size: 0.72rem;">Bundle slow-moving products with popular services at 20-30% discount to clear shelves.</span>
                            </div>
                        </div>
                        <div class="p-2 border rounded-2 bg-light d-flex align-items-start gap-2">
                            <i class="fas fa-hand-holding-usd text-success mt-1"></i>
                            <div>
                                <strong class="small d-block text-dark">Staff Incentive Flash Sales</strong>
                                <span class="text-muted" style="font-size: 0.72rem;">Offer extra stylist commission on retailing these specific slow-moving SKUs.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-list text-secondary me-2"></i>Inactive Items List</h6>
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
                            <th class="text-end">Current Stock</th>
                            <th class="text-end">Dead Capital Value</th>
                            <th>Last Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item['name'] }}</td>
                                <td><code class="text-muted small">{{ $item['sku'] }}</code></td>
                                <td><span class="badge bg-secondary-subtle text-secondary">{{ $item['category'] }}</span></td>
                                <td class="text-end">{{ number_format($item['quantity'], 2) }}</td>
                                <td class="text-end fw-bold text-danger">{{ currency_symbol() }}{{ number_format($item['purchase_value'], 2) }}</td>
                                <td class="text-muted small">
                                    {{ $item['last_transaction'] ? \Carbon\Carbon::parse($item['last_transaction'])->format('d M Y') : 'Never' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2 d-block"></i>
                                    Great! No dead stock found for the selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($items->count() > 0)
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="3">Total Inactive Items: {{ $items->total() }}</td>
                            <td class="text-end">{{ number_format($items->sum('quantity'), 2) }}</td>
                            <td class="text-end text-danger">{{ currency_symbol() }}{{ number_format($totalValue ?? 0, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
        @if($items->hasPages())
            <div class="card-footer bg-white py-2 border-0">
                {{ $items->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const labels = @json($chartDeadLabels ?? []);
    const values = @json($chartDeadValues ?? []);

    if (labels.length > 0 && document.querySelector('#deadStockChart')) {
        const options = {
            chart: { type: 'bar', height: 260, toolbar: { show: false } },
            plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '50%' } },
            dataLabels: { enabled: false },
            series: [{ name: 'Dead Capital', data: values }],
            xaxis: { categories: labels, labels: { formatter: v => '{{ currency_symbol() }}' + parseFloat(v).toFixed(0) } },
            colors: ['#dc3545'],
            tooltip: { y: { formatter: v => '{{ currency_symbol() }}' + parseFloat(v).toFixed(2) } }
        };
        const chart = new ApexCharts(document.querySelector('#deadStockChart'), options);
        chart.render();
    }
})();
</script>
@endpush
