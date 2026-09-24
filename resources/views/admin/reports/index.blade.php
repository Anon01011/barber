@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800 fw-bold">
                    <i class="fas fa-chart-pie text-primary me-2"></i>Reports Dashboard
                    @if(isset($branch) && $branch)
                        <span class="badge bg-primary text-white ms-2"
                            style="font-size: 0.5em; vertical-align: middle;">{{ $branch->name }}</span>
                    @endif
                </h1>
                <p class="text-muted small mb-0 mt-1">Comprehensive overview of your salon's performance</p>
            </div>
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-white btn-sm shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-download me-2"></i>Export All
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.export') }}"><i
                                    class="fas fa-file-excel text-success me-2"></i>Sales Report</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.appointments.export') }}"><i
                                    class="fas fa-file-excel text-info me-2"></i>Appointments</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.staff.export') }}"><i
                                    class="fas fa-file-excel text-warning me-2"></i>Staff Performance</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4 g-2 flex-nowrap overflow-auto pb-2">
            <!-- Today's Revenue -->
            <div class="col-xl col-md-4" style="min-width: 160px;">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase x-small fw-bold text-primary mb-1">Today's Revenue</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">
                                    {{ currency_symbol() }}{{ number_format($stats['today_revenue'], 2) }}
                                </div>
                                <div class="x-small text-muted mt-1">
                                    <i class="fas fa-calendar-day me-1"></i>{{ $stats['today_bookings'] }} Bookings
                                </div>
                            </div>
                            <div class="rounded-circle bg-primary bg-opacity-10 p-2">
                                <i class="fas fa-dollar-sign fa-lg text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Refunds -->
            <div class="col-xl col-md-4" style="min-width: 160px;">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase x-small fw-bold text-danger mb-1">Today's Refunds</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">
                                    {{ currency_symbol() }}{{ number_format($stats['today_refunds'] ?? 0, 2) }}
                                </div>
                                <div class="x-small text-muted mt-1">
                                    Net: {{ currency_symbol() }}{{ number_format($stats['today_net_refunds'] ?? 0, 2) }}
                                    @if(($stats['today_refund_fees'] ?? 0) > 0)
                                        <span class="text-info ms-1">(Fees: {{ number_format($stats['today_refund_fees'], 2) }})</span>
                                    @endif
                                </div>
                            </div>
                            <div class="rounded-circle bg-danger bg-opacity-10 p-2">
                                <i class="fas fa-undo fa-lg text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Revenue -->
            <div class="col-xl col-md-4" style="min-width: 160px;">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase x-small fw-bold text-success mb-1">Monthly Revenue</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">
                                    {{ currency_symbol() }}{{ number_format($stats['month_revenue'], 2) }}
                                </div>
                                <div
                                    class="x-small mt-1 {{ $stats['revenue_growth'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    <i class="fas fa-arrow-{{ $stats['revenue_growth'] >= 0 ? 'up' : 'down' }} me-1"></i>
                                    {{ number_format(abs($stats['revenue_growth']), 1) }}%
                                </div>
                            </div>
                            <div class="rounded-circle bg-success bg-opacity-10 p-2">
                                <i class="fas fa-chart-line fa-lg text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Refunds -->
            <div class="col-xl col-md-4" style="min-width: 160px;">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase x-small fw-bold text-danger mb-1">Monthly Refunds</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">
                                    {{ currency_symbol() }}{{ number_format($stats['month_refunds'] ?? 0, 2) }}
                                </div>
                                <div class="x-small text-muted mt-1">
                                    Net: {{ currency_symbol() }}{{ number_format($stats['month_net_refunds'] ?? 0, 2) }}
                                    @if(($stats['month_refund_fees'] ?? 0) > 0)
                                        <span class="text-info ms-1">(Fees: {{ number_format($stats['month_refund_fees'], 2) }})</span>
                                    @endif
                                </div>
                            </div>
                            <div class="rounded-circle bg-danger bg-opacity-10 p-2">
                                <i class="fas fa-history fa-lg text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Bookings -->
            <div class="col-xl col-md-4" style="min-width: 160px;">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase x-small fw-bold text-info mb-1">Monthly Bookings</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['month_bookings'] }}</div>
                                <div class="x-small text-muted mt-1">
                                    Bookings this month
                                </div>
                            </div>
                            <div class="rounded-circle bg-info bg-opacity-10 p-2">
                                <i class="fas fa-calendar-check fa-lg text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-xl col-md-4" style="min-width: 160px;">
                <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white">
                    <div class="card-body p-2 d-flex flex-column justify-content-center">
                        <div class="text-uppercase x-small fw-bold mb-1 opacity-75">Quick Action</div>
                        <div class="d-grid gap-1">
                            <a href="{{ route('admin.reports.sales') }}" class="btn btn-xs btn-light text-primary fw-bold" style="font-size: 0.7rem; padding: 2px 5px;">
                                <i class="fas fa-eye me-1"></i>Sales
                            </a>
                            <a href="{{ route('admin.reports.appointments') }}"
                                class="btn btn-xs btn-outline-light fw-bold" style="font-size: 0.7rem; padding: 2px 5px;">
                                <i class="fas fa-calendar me-1"></i>Bookings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Revenue Trend Chart -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-area me-2"></i>Revenue Trend (Last 6 Months)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px;">
                            <canvas id="revenueTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Categories -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-folder me-2"></i>Report Categories
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('admin.reports.sales') }}"
                                class="list-group-item list-group-item-action py-3">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-money-bill-wave text-success me-3"></i>
                                        <span class="fw-bold">Sales Reports</span>
                                    </div>
                                    <i class="fas fa-chevron-right small text-muted"></i>
                                </div>
                            </a>
                            <a href="{{ route('admin.reports.appointments') }}"
                                class="list-group-item list-group-item-action py-3">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-calendar-alt text-info me-3"></i>
                                        <span class="fw-bold">Appointment Reports</span>
                                    </div>
                                    <i class="fas fa-chevron-right small text-muted"></i>
                                </div>
                            </a>
                            <a href="{{ route('admin.reports.staff') }}"
                                class="list-group-item list-group-item-action py-3">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-user-tie text-warning me-3"></i>
                                        <span class="fw-bold">Staff Performance</span>
                                    </div>
                                    <i class="fas fa-chevron-right small text-muted"></i>
                                </div>
                            </a>
                            <a href="{{ route('admin.reports.customers') }}"
                                class="list-group-item list-group-item-action py-3">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-user-friends text-primary me-3"></i>
                                        <span class="fw-bold">Customer Insights</span>
                                    </div>
                                    <i class="fas fa-chevron-right small text-muted"></i>
                                </div>
                            </a>
                            <a href="{{ route('admin.reports.inventory') }}"
                                class="list-group-item list-group-item-action py-3">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-boxes text-secondary me-3"></i>
                                        <span class="fw-bold">Inventory Reports</span>
                                    </div>
                                    <i class="fas fa-chevron-right small text-muted"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Top Performers -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-star text-warning me-2"></i>Top Services (30 Days)
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light small text-uppercase">
                                    <tr>
                                        <th class="border-0">Service</th>
                                        <th class="border-0 text-end">Bookings</th>
                                        <th class="border-0 text-end">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['top_services'] as $service)
                                        <tr>
                                            <td class="fw-bold">{{ $service->name }}</td>
                                            <td class="text-end"><span
                                                    class="badge bg-info bg-opacity-10 text-info">{{ $service->count }}</span>
                                            </td>
                                            <td class="text-end fw-bold">
                                                {{ currency_symbol() }}{{ number_format($service->revenue, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-medal text-primary me-2"></i>Top Staff (30 Days)
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light small text-uppercase">
                                    <tr>
                                        <th class="border-0">Staff</th>
                                        <th class="border-0 text-end">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['top_staff'] as $staff)
                                        <tr>
                                            <td class="fw-bold">{{ $staff->name }}</td>
                                            <td class="text-end fw-bold text-success">
                                                {{ currency_symbol() }}{{ number_format($staff->revenue, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-history me-2"></i>Recent Activity
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light small text-uppercase">
                                    <tr>
                                        <th class="border-0">Date</th>
                                        <th class="border-0">Type</th>
                                        <th class="border-0">Customer</th>
                                        <th class="border-0 text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['recent_activity'] as $activity)
                                        <tr>
                                            <td class="small">
                                                <div class="fw-bold">
                                                    {{ \Carbon\Carbon::parse($activity->date)->format('M d, Y') }}</div>
                                                <div class="text-muted">
                                                    {{ \Carbon\Carbon::parse($activity->date)->format('h:i A') }}</div>
                                            </td>
                                            <td>
                                                @if($activity->type == 'booking')
                                                    <span class="badge bg-info bg-opacity-10 text-info">Booking</span>
                                                @else
                                                    <span class="badge bg-warning bg-opacity-10 text-warning">POS Sale</span>
                                                @endif
                                            </td>
                                            <td class="fw-bold">{{ $activity->customer }}</td>
                                            <td class="text-end fw-bold">
                                                {{ currency_symbol() }}{{ number_format($activity->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            $(document).ready(function () {
                const ctx = document.getElementById('revenueTrendChart').getContext('2d');
                const trendData = @json($stats['monthly_trend']);

                const labels = Object.keys(trendData);
                const bookingData = labels.map(l => trendData[l].bookings);
                const posData = labels.map(l => trendData[l].pos);
                const totalData = labels.map(l => trendData[l].total);

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Total Revenue',
                                data: totalData,
                                borderColor: '#4e73df',
                                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                                fill: true,
                                tension: 0.3,
                                borderWidth: 3
                            },
                            {
                                label: 'Bookings',
                                data: bookingData,
                                borderColor: '#36b9cc',
                                borderDash: [5, 5],
                                fill: false,
                                tension: 0.3
                            },
                            {
                                label: 'POS Sales',
                                data: posData,
                                borderColor: '#f6c23e',
                                borderDash: [5, 5],
                                fill: false,
                                tension: 0.3
                            }
                        ]
                    },
                    options: {
                        maintainAspectRatio: false,
                        // The provided snippet contained CSS which is not valid JS here.
                        // If you intended to add padding to the chart area, Chart.js uses `layout.padding`.
                        // For example: layout: { padding: { left: 0, right: 0, top: 30, bottom: 0 } }
                        // The `.x-small` CSS class should be defined in a <style> tag or CSS file.
                        // The instruction "Change col-xl-3 to col-xl" was not present in the provided snippet.
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function (value) {
                                        return '{{ currency_symbol() }}' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection