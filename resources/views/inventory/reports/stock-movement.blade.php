@extends('layouts.app')

@section('title', 'Stock Movement Report')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h4 mb-1 fw-bold">
                        <i class="fas fa-exchange-alt text-info me-2"></i>Stock Movement Report
                    </h2>
                    <p class="text-muted mb-0 small">Track all inventory transactions over a date range</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.inventory.reports.export', array_merge(request()->except('type'), ['type' => 'movement', 'transaction_type' => request('type')])) }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </a>
                    <a href="{{ route('admin.inventory.reports.index', ['salon_slug' => request()->route('salon_slug')]) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">From Date</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">To Date</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Transaction Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                        <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>Sale</option>
                        <option value="service_consumption" {{ request('type') == 'service_consumption' ? 'selected' : '' }}>Service Consumption</option>
                        <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                        <option value="damaged" {{ request('type') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                        <option value="lost" {{ request('type') == 'lost' ? 'selected' : '' }}>Lost</option>
                        <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Return</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.inventory.reports.stock-movement') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total In</div>
                    <div class="fs-4 fw-bold text-success">+{{ number_format($stats['total_in'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total Out</div>
                    <div class="fs-4 fw-bold text-danger">-{{ number_format($stats['total_out'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Service Consumed</div>
                    <div class="fs-4 fw-bold text-warning">{{ number_format($stats['total_consumed'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total Value</div>
                    <div class="fs-4 fw-bold text-primary">{{ number_format($stats['total_value'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2-Column Responsive Layout: Chart & Transaction Type Breakdown --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-chart-pie text-primary me-2"></i>Stock Volume by Transaction Type</h6>
                    <span class="text-muted small">Movement share in selected period</span>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    @if(count($chartMovementLabels ?? []) > 0)
                        <div id="movementChart" class="w-100" style="min-height: 275px;"></div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-exchange-alt fa-2x mb-2 d-block opacity-50"></i>
                            No movements to chart for this period.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-list-check text-secondary me-2"></i>Movement Type Breakdown</h6>
                        <span class="text-muted small">Summary of transactions and total costs</span>
                    </div>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ count($typeStats ?? []) }} Types Active</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr style="font-size: 0.75rem;">
                                    <th>Type</th>
                                    <th class="text-center">Count</th>
                                    <th class="text-end">Total Units</th>
                                    <th class="text-end">Total Valuation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($typeStats ?? [] as $t)
                                    @php
                                        $type = $t->transaction_type;
                                        $badge = match($type) {
                                            'purchase' => 'success',
                                            'sale' => 'primary',
                                            'service_consumption' => 'warning',
                                            'damaged' => 'danger',
                                            'lost' => 'dark',
                                            'return' => 'info',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="badge bg-{{ $badge }} bg-opacity-10 text-{{ $badge }} text-uppercase" style="font-size:0.7rem;">
                                                {{ str_replace('_', ' ', $type) }}
                                            </span>
                                        </td>
                                        <td class="text-center font-monospace small">{{ number_format($t->count) }}</td>
                                        <td class="text-end font-monospace fw-semibold">{{ number_format($t->total_qty, 1) }}</td>
                                        <td class="text-end font-monospace fw-bold text-dark">{{ currency_symbol() }}{{ number_format($t->total_cost, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">No movement records found.</td>
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
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Unit Cost</th>
                            <th class="text-end">Total Cost</th>
                            <th>Notes</th>
                            <th>By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $txn)
                            @php
                                $typeColors = [
                                    'purchase' => 'success',
                                    'sale' => 'primary',
                                    'service_consumption' => 'info',
                                    'adjustment' => 'warning',
                                    'damaged' => 'danger',
                                    'lost' => 'danger',
                                    'return' => 'secondary',
                                ];
                                $color = $typeColors[$txn->transaction_type] ?? 'secondary';
                            @endphp
                            <tr>
                                <td class="text-muted small">{{ $txn->created_at->format('d M Y H:i') }}</td>
                                <td class="fw-semibold">{{ $txn->item->name ?? '—' }}</td>
                                <td><code class="small text-muted">{{ $txn->item->sku ?? '—' }}</code></td>
                                <td><span class="badge bg-secondary-subtle text-secondary small">{{ $txn->item->category->name ?? '—' }}</span></td>
                                <td><span class="badge bg-{{ $color }}-subtle text-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $txn->transaction_type)) }}</span></td>
                                <td class="text-end fw-semibold {{ $txn->quantity >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $txn->quantity >= 0 ? '+' : '' }}{{ number_format($txn->quantity, 3) }}
                                </td>
                                <td class="text-end">{{ number_format($txn->unit_cost ?? 0, 2) }}</td>
                                <td class="text-end fw-semibold">{{ number_format($txn->total_cost ?? 0, 2) }}</td>
                                <td class="text-muted small">{{ Str::limit($txn->notes, 40) }}</td>
                                <td class="small text-muted">{{ $txn->user->name ?? 'System' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fas fa-history fa-2x mb-2 d-block"></i>No transactions found for the selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transactions->hasPages())
        <div class="card-footer bg-transparent">
            {{ $transactions->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const labels = @json($chartMovementLabels ?? []);
    const values = @json($chartMovementValues ?? []);

    if (labels.length > 0 && document.querySelector('#movementChart')) {
        const options = {
            chart: { type: 'donut', height: 280 },
            labels: labels,
            series: values,
            colors: ['#28a745', '#007bff', '#17a2b8', '#ffc107', '#dc3545', '#6c757d'],
            legend: { position: 'bottom' },
            dataLabels: { enabled: true, formatter: (val) => val.toFixed(1) + '%' },
            tooltip: { y: { formatter: v => parseFloat(v).toFixed(2) + ' units' } }
        };
        const chart = new ApexCharts(document.querySelector('#movementChart'), options);
        chart.render();
    }
})();
</script>
@endpush
