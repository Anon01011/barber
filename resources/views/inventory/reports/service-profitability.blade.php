@extends('layouts.app')

@section('title', 'Service Profitability & COGS Report')

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
                            <li class="breadcrumb-item active" aria-current="page">Service Profitability</li>
                        </ol>
                    </nav>
                    <h1 class="h4 mb-0 fw-bold">Service Profitability & COGS</h1>
                    <p class="text-muted small mb-0">Analyze real service gross margins after deducting consumable product costs</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.inventory.reports.export', array_merge(['type' => 'profitability'], request()->all())) }}" class="btn btn-outline-success btn-sm">
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
            <form method="GET" action="{{ route('admin.inventory.reports.service-profitability') }}" class="row g-3 align-items-end">
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
                    <a href="{{ route('admin.inventory.reports.service-profitability') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
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
                            <span class="text-muted small text-uppercase fw-semibold">Service Revenue</span>
                            <h3 class="h4 mb-0 fw-bold text-dark mt-1">{{ currency_symbol() }}{{ number_format($totals['total_revenue'], 2) }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary p-2.5 rounded-3">
                            <i class="fas fa-dollar-sign fa-lg"></i>
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
                            <span class="text-muted small text-uppercase fw-semibold">Consumables COGS</span>
                            <h3 class="h4 mb-0 fw-bold text-danger mt-1">{{ currency_symbol() }}{{ number_format($totals['total_cogs'], 2) }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 text-danger p-2.5 rounded-3">
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
                            <span class="text-muted small text-uppercase fw-semibold">Gross Profit</span>
                            <h3 class="h4 mb-0 fw-bold text-success mt-1">{{ currency_symbol() }}{{ number_format($totals['total_profit'], 2) }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success p-2.5 rounded-3">
                            <i class="fas fa-chart-line fa-lg"></i>
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
                            <span class="text-muted small text-uppercase fw-semibold">Overall Margin</span>
                            <h3 class="h4 mb-0 fw-bold text-info mt-1">{{ number_format($totals['overall_margin'], 1) }}%</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 text-info p-2.5 rounded-3">
                            <i class="fas fa-percentage fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2-Column Responsive Layout: Revenue vs COGS Chart & Top Profitable Services -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-chart-bar text-primary me-2"></i>Revenue vs Consumables COGS vs Profit</h6>
                    <span class="text-muted small">Top 8 services performance comparison</span>
                </div>
                <div class="card-body">
                    @if(count($serviceStats) > 0)
                        <div id="profitabilityChart" style="min-height: 280px;"></div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-chart-bar fa-2x mb-2 d-block opacity-50"></i>
                            No data to chart for this period.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-trophy text-warning me-2"></i>Top Margin Services</h6>
                        <span class="text-muted small">Services with highest profit retention</span>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success fw-bold">{{ $totals['overall_margin'] ?? 0 }}% Avg Margin</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($serviceStats->take(5) as $s)
                            <div class="list-group-item border-0 border-bottom px-3 py-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark small">{{ $s['name'] }}</span>
                                    <span class="badge bg-{{ $s['margin_percent'] >= 60 ? 'success' : ($s['margin_percent'] >= 30 ? 'primary' : 'warning') }} rounded-pill" style="font-size:0.7rem;">
                                        {{ $s['margin_percent'] }}% Margin
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between text-muted small" style="font-size: 0.72rem;">
                                    <span>Revenue: <strong>{{ currency_symbol() }}{{ number_format($s['revenue'], 2) }}</strong> &bull; COGS: <span class="text-danger">{{ currency_symbol() }}{{ number_format($s['cogs'], 2) }}</span></span>
                                    <span class="fw-bold text-success">+{{ currency_symbol() }}{{ number_format($s['gross_profit'], 2) }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted small">No service profitability data available.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profitability Breakdown Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-calculator text-primary me-2"></i>Service Margin Analysis</h6>
            <span class="badge bg-light text-muted border">Showing {{ $serviceStats->count() }} of {{ $serviceStats->total() }} Services</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Service</th>
                        <th>Category</th>
                        <th class="text-center">Count</th>
                        <th class="text-end">Revenue</th>
                        <th class="text-end">Consumables (COGS)</th>
                        <th class="text-end">Gross Profit</th>
                        <th style="min-width: 140px;">Gross Margin %</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($serviceStats as $stat)
                        <tr>
                            <td class="fw-bold text-dark">
                                {{ $stat['name'] }}
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">{{ $stat['category'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary">{{ $stat['quantity_sold'] }}</span>
                            </td>
                            <td class="text-end fw-semibold text-dark">
                                {{ currency_symbol() }}{{ number_format($stat['revenue'], 2) }}
                            </td>
                            <td class="text-end text-danger fw-semibold">
                                {{ currency_symbol() }}{{ number_format($stat['cogs'], 2) }}
                            </td>
                            <td class="text-end fw-bold text-success">
                                {{ currency_symbol() }}{{ number_format($stat['gross_profit'], 2) }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        @php
                                            $barClass = $stat['margin_percent'] >= 75 ? 'bg-success' : ($stat['margin_percent'] >= 50 ? 'bg-warning' : 'bg-danger');
                                        @endphp
                                        <div class="progress-bar {{ $barClass }}" role="progressbar" style="width: {{ max(0, min(100, $stat['margin_percent'])) }}%"></div>
                                    </div>
                                    <span class="fw-bold small {{ $stat['margin_percent'] >= 75 ? 'text-success' : ($stat['margin_percent'] >= 50 ? 'text-warning-emphasis' : 'text-danger') }}" style="min-width: 45px; text-align: right;">
                                        {{ number_format($stat['margin_percent'], 1) }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-chart-pie fa-2x mb-2 d-block text-secondary opacity-50"></i>
                                No services performed in this date range.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($serviceStats->hasPages())
            <div class="card-footer bg-white py-2 border-0">
                {{ $serviceStats->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const chartLabels = @json($chartLabels);
    const chartRevenue = @json($chartRevenue);
    const chartCogs = @json($chartCogs);
    const chartProfit = @json($chartProfit);

    if (chartLabels.length > 0) {
        const options = {
            chart: { type: 'bar', height: 320, toolbar: { show: false } },
            plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 4 } },
            dataLabels: { enabled: false },
            series: [
                { name: 'Revenue', data: chartRevenue },
                { name: 'Consumables COGS', data: chartCogs },
                { name: 'Gross Profit', data: chartProfit }
            ],
            xaxis: { categories: chartLabels, labels: { rotate: -35, style: { fontSize: '11px' } } },
            yaxis: { labels: { formatter: v => '{{ currency_symbol() }}' + parseFloat(v).toFixed(0) } },
            colors: ['#3b82f6', '#ef4444', '#22c55e'],
            legend: { position: 'top' },
            tooltip: { y: { formatter: v => '{{ currency_symbol() }}' + parseFloat(v).toFixed(2) } }
        };
        const chart = new ApexCharts(document.querySelector('#profitabilityChart'), options);
        chart.render();
    }
})();
</script>
@endpush
