@extends('layouts.app')

@section('title', 'Service Consumables Usage Report')

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
                            <li class="breadcrumb-item active" aria-current="page">Service Consumables Usage</li>
                        </ol>
                    </nav>
                    <h1 class="h4 mb-0 fw-bold">Service Consumables Usage</h1>
                    <p class="text-muted small mb-0">Track backbar and consumable usage logged during POS service checkouts</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.inventory.reports.export', array_merge(['type' => 'consumption'], request()->all())) }}" class="btn btn-outline-success btn-sm">
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
            <form method="GET" action="{{ route('admin.inventory.reports.service-consumption') }}" class="row g-3 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">From Date</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">To Date</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Staff Member</label>
                    <select name="staff_id" class="form-select form-select-sm">
                        <option value="">All Staff</option>
                        @foreach($staffList as $staff)
                            <option value="{{ $staff->id }}" {{ $staffId == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Consumable Product</label>
                    <select name="inventory_item_id" class="form-select form-select-sm">
                        <option value="">All Consumables</option>
                        @foreach($consumableItems as $item)
                            <option value="{{ $item->id }}" {{ $itemId == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.inventory.reports.service-consumption') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
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
                            <span class="text-muted small text-uppercase fw-semibold">Total Consumed Cost</span>
                            <h3 class="h4 mb-0 fw-bold text-dark mt-1">{{ currency_symbol() }}{{ number_format($stats['total_cost'], 2) }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary p-2.5 rounded-3">
                            <i class="fas fa-coins fa-lg"></i>
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
                            <span class="text-muted small text-uppercase fw-semibold">Billed to Clients</span>
                            <h3 class="h4 mb-0 fw-bold text-success mt-1">{{ currency_symbol() }}{{ number_format($stats['total_billed'] ?? 0, 2) }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success p-2.5 rounded-3">
                            <i class="fas fa-file-invoice-dollar fa-lg"></i>
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
                            <span class="text-muted small text-uppercase fw-semibold">Net Material Cost</span>
                            @php
                                $netCost = max(0, $stats['total_cost'] - ($stats['total_billed'] ?? 0));
                            @endphp
                            <h3 class="h4 mb-0 fw-bold text-warning mt-1">{{ currency_symbol() }}{{ number_format($netCost, 2) }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning p-2.5 rounded-3">
                            <i class="fas fa-balance-scale fa-lg"></i>
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
                            <span class="text-muted small text-uppercase fw-semibold">Total Dispensed Units</span>
                            <h3 class="h4 mb-0 fw-bold text-info mt-1">{{ number_format($stats['total_items_used'], 1) }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 text-info p-2.5 rounded-3">
                            <i class="fas fa-flask fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summaries: Product Breakdown & Staff Breakdown -->
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-boxes text-primary me-2"></i>Usage by Product</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Product</th>
                                <th>Total Dispensed</th>
                                <th class="text-end">Total Cost</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse($productSummary as $summary)
                                <tr>
                                    <td class="fw-semibold text-dark">
                                        {{ $summary->inventoryItem->name ?? 'Deleted Item' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ number_format($summary->total_quantity, 2) }} {{ $summary->unit }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        {{ currency_symbol() }}{{ number_format($summary->total_cost, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No consumable items recorded in this range.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-user-check text-success me-2"></i>Usage by Staff</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Staff Member</th>
                                <th>Services</th>
                                <th class="text-end">Cost</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse($staffSummary as $st)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $st->staff->name ?? 'Unassigned' }}</td>
                                    <td><span class="badge bg-secondary-subtle text-secondary">{{ $st->total_services }}</span></td>
                                    <td class="text-end fw-bold text-dark">{{ currency_symbol() }}{{ number_format($st->total_cost, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No staff usage recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 2-Column Responsive Layout: Top Consumed Products Chart & Staff Summary -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-chart-bar text-primary me-2"></i>Top Consumed Backbar Products by Material Cost</h6>
                    <span class="text-muted small">Highest expenditure in service recipes during selected period</span>
                </div>
                <div class="card-body">
                    @if(count($chartProductLabels ?? []) > 0)
                        <div id="consumptionChart" style="min-height: 280px;"></div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-pie fa-2x mb-2 d-block opacity-50"></i>
                            No consumable usage to display in chart for this period.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-users-cog text-secondary me-2"></i>Staff Usage Summary</h6>
                        <span class="text-muted small">Backbar products dispensed by stylist</span>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ count($staffSummary ?? []) }} Stylists</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr style="font-size: 0.75rem;">
                                    <th>Stylist / Staff</th>
                                    <th class="text-center">Services</th>
                                    <th class="text-end">COGS Dispensed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staffSummary ?? [] as $st)
                                    <tr>
                                        <td class="fw-semibold text-dark">
                                            <i class="fas fa-user-circle text-muted me-1"></i>
                                            {{ $st->staff->name ?? 'Unassigned / Direct' }}
                                        </td>
                                        <td class="text-center font-monospace small">
                                            <span class="badge bg-light text-secondary border rounded-pill">{{ $st->total_services }}</span>
                                        </td>
                                        <td class="text-end font-monospace fw-bold text-dark">{{ currency_symbol() }}{{ number_format($st->total_cost, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">No staff usage logged.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Transactions Log -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-list text-secondary me-2"></i>Detailed Consumption Logs</h6>
            <span class="text-muted small">Showing latest {{ $consumptions->count() }} of {{ $consumptions->total() }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Date & Time</th>
                        <th>Sale / Service</th>
                        <th>Product Consumed</th>
                        <th>Quantity Used</th>
                        <th>Cost / Unit</th>
                        <th>Total COGS</th>
                        <th>Billed to Client</th>
                        <th>Performed By / Cashier</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($consumptions as $c)
                        <tr>
                            <td class="text-muted">{{ $c->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($c->posSaleItem && $c->posSaleItem->sale)
                                    <a href="{{ route('admin.pos.sales.show', $c->posSaleItem->sale->invoice_number ?? $c->posSaleItem->sale->id) }}" class="fw-semibold text-decoration-none">
                                        #{{ $c->posSaleItem->sale->invoice_number }}
                                    </a>
                                    <div class="text-muted small" style="font-size: 0.75rem;">{{ $c->posSaleItem->item_name }}</div>
                                @else
                                    <span class="text-muted">POS Sale #{{ $c->pos_sale_id }}</span>
                                @endif
                            </td>
                            <td class="fw-semibold text-dark">
                                {{ $c->inventoryItem->name ?? 'Deleted Item' }}
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    {{ number_format($c->quantity_used, 2) }} {{ $c->unit }}
                                </span>
                            </td>
                            <td class="text-muted">{{ currency_symbol() }}{{ number_format($c->unit_cost, 2) }}</td>
                            <td class="fw-bold text-danger">{{ currency_symbol() }}{{ number_format($c->total_cost, 2) }}</td>
                            <td>
                                @if((float)($c->billed_amount ?? 0) > 0)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        +{{ currency_symbol() }}{{ number_format($c->billed_amount, 2) }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border">Included</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $cashier = $c->sale->employee ?? $c->posSaleItem->sale->employee ?? null;
                                    $staff = $c->staff;
                                @endphp
                                <div class="fw-semibold text-dark">{{ $staff->name ?? 'Unassigned Staff' }}</div>
                                @if($cashier)
                                    <small class="text-muted d-block" style="font-size: 0.72rem;" title="Cashier who processed the checkout">
                                        <i class="fas fa-cash-register me-1 text-primary"></i>Cashier: {{ $cashier->name }}
                                    </small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-clipboard-list fa-2x mb-2 d-block text-secondary opacity-50"></i>
                                No service consumptions logged in this date range.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($consumptions->hasPages())
            <div class="card-footer bg-white py-2 border-0">
                {{ $consumptions->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const productLabels = @json($chartProductLabels ?? []);
    const productCosts = @json($chartProductCost ?? []);

    if (productLabels.length > 0 && document.querySelector('#consumptionChart')) {
        const options = {
            chart: { type: 'bar', height: 280, toolbar: { show: false } },
            plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '50%' } },
            dataLabels: { enabled: false },
            series: [{ name: 'Cost', data: productCosts }],
            xaxis: { categories: productLabels, labels: { formatter: v => '{{ currency_symbol() }}' + parseFloat(v).toFixed(0) } },
            colors: ['#0d6efd'],
            tooltip: { y: { formatter: v => '{{ currency_symbol() }}' + parseFloat(v).toFixed(2) } }
        };
        const chart = new ApexCharts(document.querySelector('#consumptionChart'), options);
        chart.render();
    }
})();
</script>
@endpush
