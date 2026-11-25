@extends('layouts.app')

@section('title', 'Consumables Wastage & Variance Report')

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1 small">
                            <li class="breadcrumb-item"><a href="{{ route('admin.inventory.reports.index') }}">Inventory Reports</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Consumables Wastage & Variance</li>
                        </ol>
                    </nav>
                    <h1 class="h4 mb-0 fw-bold">Consumables Wastage & Variance</h1>
                    <p class="text-muted small mb-0">Track damaged, lost, expired, and discarded backbar stock against standard recipe consumption</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.inventory.reports.export', array_merge(['type' => 'wastage'], request()->all())) }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </a>
                    <a href="{{ route('admin.inventory.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.inventory.reports.wastage') }}" class="row g-3 align-items-end">
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">From Date</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">To Date</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.inventory.reports.wastage') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Total Wastage Loss</span>
                            <h3 class="h4 mb-0 fw-bold text-danger mt-1">{{ currency_symbol() }}{{ number_format($stats['total_wastage_cost'], 2) }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 text-danger p-2.5 rounded-3">
                            <i class="fas fa-trash-alt fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Standard Service Usage</span>
                            <h3 class="h4 mb-0 fw-bold text-dark mt-1">{{ currency_symbol() }}{{ number_format($stats['total_consumption_cost'], 2) }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary p-2.5 rounded-3">
                            <i class="fas fa-flask fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Wastage Rate</span>
                            <h3 class="h4 mb-0 fw-bold {{ $stats['wastage_rate'] > 5 ? 'text-danger' : 'text-success' }} mt-1">
                                {{ number_format($stats['wastage_rate'], 1) }}%
                            </h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning p-2.5 rounded-3">
                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Wastage Incidents</span>
                            <h3 class="h4 mb-0 fw-bold text-secondary mt-1">{{ number_format($stats['incident_count']) }}</h3>
                        </div>
                        <div class="bg-secondary bg-opacity-10 text-secondary p-2.5 rounded-3">
                            <i class="fas fa-clipboard-list fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2-Column Responsive Layout: Wastage vs Usage Chart & Audit Analysis -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-chart-pie text-danger me-2"></i>Wastage Loss vs Standard Usage</h6>
                    <span class="text-muted small">Comparison of lost stock vs billed usage</span>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    @if(($stats['total_wastage_cost'] + $stats['total_consumption_cost']) > 0)
                        <div id="wastageChart" class="w-100" style="min-height: 275px;"></div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-pie fa-2x mb-2 d-block opacity-50"></i>
                            No wastage or consumption data to display for this period.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-shield-alt text-primary me-2"></i>Loss Prevention &amp; Audit Analysis</h6>
                        <span class="text-muted small">Variance rate and loss impact summary</span>
                    </div>
                    <span class="badge bg-{{ $stats['wastage_rate'] > 5 ? 'danger' : 'success' }} px-2 py-1">
                        {{ $stats['wastage_rate'] }}% Wastage Rate
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <div class="p-3 bg-danger bg-opacity-10 rounded-3 border border-danger border-opacity-25 h-100">
                                <span class="text-danger small fw-bold text-uppercase d-block">Total Shrinkage / Loss</span>
                                <h4 class="fw-bold text-danger mb-0 mt-1">{{ currency_symbol() }}{{ number_format($stats['total_wastage_cost'], 2) }}</h4>
                                <span class="text-muted small">{{ $stats['incident_count'] }} recorded incidents</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-success bg-opacity-10 rounded-3 border border-success border-opacity-25 h-100">
                                <span class="text-success small fw-bold text-uppercase d-block">Authorized Service COGS</span>
                                <h4 class="fw-bold text-success mb-0 mt-1">{{ currency_symbol() }}{{ number_format($stats['total_consumption_cost'], 2) }}</h4>
                                <span class="text-muted small">Dispensed in customer services</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <div class="p-2 border rounded-2 bg-light d-flex align-items-center gap-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <span class="text-muted small">Backbar products are weighed or counted per recipe at each checkout.</span>
                        </div>
                        <div class="p-2 border rounded-2 bg-light d-flex align-items-center gap-2">
                            <i class="fas fa-info-circle text-primary"></i>
                            <span class="text-muted small">Damaged, expired, or spilled bottles require manager verification notes.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wastage Log Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-exclamation-circle text-danger me-2"></i>Wastage & Adjustment Log</h6>
            <span class="text-muted small">{{ $wastageTransactions->total() }} incidents recorded</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Date & Time</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Unit Cost</th>
                        <th>Total Loss ({{ currency_symbol() }})</th>
                        <th>Reason / Notes</th>
                        <th>Logged By</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($wastageTransactions as $t)
                        <tr>
                            <td class="text-muted">{{ $t->created_at->format('Y-m-d H:i') }}</td>
                            <td class="fw-semibold text-dark">{{ $t->item->name ?? 'Deleted Item' }}</td>
                            <td>
                                @if($t->transaction_type === 'damaged')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Damaged / Broken</span>
                                @elseif($t->transaction_type === 'lost')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Lost / Spilled</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border">Adjustment</span>
                                @endif
                            </td>
                            <td class="fw-semibold text-dark">
                                {{ number_format(abs($t->quantity), 2) }} {{ $t->item->base_unit ?? $t->item->unit_type ?? 'pcs' }}
                            </td>
                            <td class="text-muted">{{ currency_symbol() }}{{ number_format($t->unit_cost, 2) }}</td>
                            <td class="fw-bold text-danger">{{ currency_symbol() }}{{ number_format($t->total_cost, 2) }}</td>
                            <td class="text-muted">{{ $t->notes ?: '—' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $t->user->name ?? 'System' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-check-circle fa-2x mb-2 d-block text-success opacity-50"></i>
                                No damaged, lost, or wastage incidents recorded in this date range.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($wastageTransactions->hasPages())
            <div class="card-footer bg-white py-2 border-0">
                {{ $wastageTransactions->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const labels = @json($chartWastageLabels ?? []);
    const values = @json($chartWastageValues ?? []);

    if (labels.length > 0 && document.querySelector('#wastageChart')) {
        const options = {
            chart: { type: 'donut', height: 280 },
            labels: labels,
            series: values,
            colors: ['#dc3545', '#0d6efd'],
            legend: { position: 'bottom' },
            dataLabels: { enabled: true, formatter: (val) => val.toFixed(1) + '%' },
            tooltip: { y: { formatter: v => '{{ currency_symbol() }}' + parseFloat(v).toFixed(2) } }
        };
        const chart = new ApexCharts(document.querySelector('#wastageChart'), options);
        chart.render();
    }
})();
</script>
@endpush
