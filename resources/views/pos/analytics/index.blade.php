@extends('layouts.app')

@section('title', 'Sales Analytics')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.css" rel="stylesheet">
    <style>
        .stat-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }

        .trend-up {
            color: #198754;
        }

        .trend-down {
            color: #dc3545;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #6c757d;
        }

        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-3">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom pb-3">
                    <div class="mb-3 mb-md-0">
                        <h1 class="h3 mb-2">
                            <i class="fas fa-chart-line text-primary me-2"></i>Sales Analytics
                        </h1>
                        <nav aria-label="breadcrumb" class="d-none d-md-block">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Sales Analytics</li>
                            </ol>
                        </nav>
                    </div>

                    <!-- Date Range Picker -->
                    <div class="mt-3 mt-md-0">
                        <form action="" method="GET" class="row g-2 align-items-center">
                            <div class="col-auto">
                                <label for="start_date" class="col-form-label">From:</label>
                            </div>
                            <div class="col-auto">
                                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-auto">
                                <label for="end_date" class="col-form-label">To:</label>
                            </div>
                            <div class="col-auto">
                                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-filter me-1"></i> Apply
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards Row 1 -->
        <div class="row g-3 mb-3">
            <!-- Total Revenue (Net) -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-uppercase text-muted small fw-bold mb-1">Net Revenue</p>
                                <h3 class="mb-0 fw-bold text-success">
                                    {{ currency_symbol() }}{{ number_format($salesData['netRevenue'] ?? 0, 2) }}
                                </h3>
                                <small class="text-muted">Total Paid - Net Refunds</small>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="fas fa-wallet text-success fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Refunded (Actual) -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-uppercase text-muted small fw-bold mb-1">Net Refunded</p>
                                <h3 class="mb-0 fw-bold text-danger">
                                    {{ currency_symbol() }}{{ number_format($salesData['netRefunded'] ?? 0, 2) }}
                                </h3>
                                <small class="text-muted">Actual amount returned</small>
                            </div>
                            <div class="bg-danger bg-opacity-10 p-3 rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="fas fa-undo text-danger fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Refund Fees -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-uppercase text-muted small fw-bold mb-1">Refund Fees</p>
                                <h3 class="mb-0 fw-bold text-primary">
                                    {{ currency_symbol() }}{{ number_format($salesData['refundFees'] ?? 0, 2) }}
                                </h3>
                                <small class="text-muted">Retained by salon</small>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="fas fa-percentage text-primary fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Tips -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-uppercase text-muted small fw-bold mb-1">Total Tips</p>
                                <h3 class="mb-0 fw-bold text-info">
                                    {{ currency_symbol() }}{{ number_format($salesData['totalTips'] ?? 0, 2) }}
                                </h3>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="fas fa-hand-holding-usd text-info fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards Row 2 (Secondary Metrics) -->
        <div class="row g-3 mb-4">
            <!-- Total Paid Inflow -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <p class="text-uppercase text-muted small fw-bold mb-1">Total Paid (Inflow)</p>
                        <h4 class="mb-0 font-weight-bold text-success">
                            {{ currency_symbol() }}{{ number_format($salesData['totalPaid'] ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>
            <!-- Total Discounts -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <p class="text-uppercase text-muted small fw-bold mb-1">Total Discounts</p>
                        <h4 class="mb-0 font-weight-bold text-secondary">
                            {{ currency_symbol() }}{{ number_format($salesData['totalDiscounts'] ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>
            <!-- Total Invoiced -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <p class="text-uppercase text-muted small fw-bold mb-1">Total Invoiced</p>
                        <h4 class="mb-0 font-weight-bold text-dark">
                            {{ currency_symbol() }}{{ number_format($salesData['totalSales'] ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>
            <!-- Total Outstanding -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <p class="text-uppercase text-muted small fw-bold mb-1">Total Outstanding</p>
                        <h4 class="mb-0 font-weight-bold text-warning">
                            {{ currency_symbol() }}{{ number_format($salesData['totalUnpaid'] ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="row g-3 mb-4">
            <!-- Revenue Trends -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Revenue Trends</h5>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary active"
                                onclick="updateRevenueChart('day')">Day</button>
                            <button type="button" class="btn btn-outline-secondary"
                                onclick="updateRevenueChart('week')">Week</button>
                            <button type="button" class="btn btn-outline-secondary"
                                onclick="updateRevenueChart('month')">Month</button>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div id="revenueChart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>

            <!-- Payment Distribution -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Payment Distribution</h5>
                    </div>
                    <div class="card-body">
                        <div id="paymentDistributionChart" style="height: 280px;"></div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Paid Revenue</span>
                                <span>{{ currency_symbol() }}{{ number_format($paymentAnalytics['paid_amount'] ?? 0, 2) }}
                                    ({{ number_format($paymentAnalytics['paid_percentage'] ?? 0, 1) }}%)</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1 text-danger">
                                <span>Net Refunded</span>
                                <span>-
                                    {{ currency_symbol() }}{{ number_format($paymentAnalytics['net_refunded'] ?? 0, 2) }}
                                    ({{ number_format($paymentAnalytics['refund_net_percentage'] ?? 0, 1) }}%)</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Tips</span>
                                <span>{{ currency_symbol() }}{{ number_format($paymentAnalytics['tip_amount'] ?? 0, 2) }}
                                    ({{ number_format($paymentAnalytics['tip_percentage'] ?? 0, 1) }}%)</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1 text-warning">
                                <span>Unpaid</span>
                                <span>{{ currency_symbol() }}{{ number_format($paymentAnalytics['unpaid_amount'] ?? 0, 2) }}
                                    ({{ number_format($paymentAnalytics['unpaid_percentage'] ?? 0, 1) }}%)</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1 text-muted">
                                <span>Discounts</span>
                                <span>{{ currency_symbol() }}{{ number_format($paymentAnalytics['discount_amount'] ?? 0, 2) }}
                                    ({{ number_format($paymentAnalytics['discount_percentage'] ?? 0, 1) }}%)</span>
                            </div>
                            <div class="d-flex justify-content-between text-danger">
                                <span>Voided</span>
                                <span>{{ currency_symbol() }}{{ number_format($paymentAnalytics['voided_amount'] ?? 0, 2) }}
                                    ({{ number_format($paymentAnalytics['voided_percentage'] ?? 0, 1) }}%)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="row g-3 mb-4">
            <!-- Sales by Category -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Sales by Type</h5>
                    </div>
                    <div class="card-body">
                        <div id="categoryChart" style="height: 300px;"></div>
                        <div class="mt-3">
                            @forelse($salesByCategory as $category)
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="category-color me-2"
                                            style="width: 12px; height: 12px; background-color: {{ $loop->index < 6 ? $categoryColors[$loop->index] : '#6c757d' }}; border-radius: 2px;">
                                        </div>
                                        <span class="small">{{ $category['name'] }}</span>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold">
                                            {{ currency_symbol() }}{{ number_format($category['total_revenue'] ?? 0, 2) }}
                                        </div>
                                        <div class="text-muted small">{{ $category['total_quantity'] }} items</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                    <p class="mb-0 text-muted">No data available</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hourly Sales (Working Graph) -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Hourly Sales Distribution</h5>
                    </div>
                    <div class="card-body p-3">
                        <div id="hourlyChart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 3 -->
        <!-- Void Analysis -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Void Analysis</h5>
                        <span class="badge bg-warning text-dark">{{ $voidAnalytics['total_voided'] }} Voided
                            Transactions</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center p-3">
                                    <h3 class="text-warning">
                                        {{ currency_symbol() }}{{ number_format($voidAnalytics['total_voided_amount'] ?? 0, 2) }}
                                    </h3>
                                    <p class="text-muted mb-0">Total Voided Amount</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3">
                                    <h3>{{ currency_symbol() }}{{ number_format($voidAnalytics['average_voided_amount'] ?? 0, 2) }}
                                    </h3>
                                    <p class="text-muted mb-0">Average per Void</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div id="voidReasonsChart" style="height: 200px;"></div>
                            </div>
                        </div>

                        @if(!$voidAnalytics['void_reasons']->isEmpty())
                            <div class="mt-4">
                                <h6>Top Void Reasons</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Reason</th>
                                                <th class="text-end">Count</th>
                                                <th class="text-end">Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($voidAnalytics['void_reasons'] as $reason)
                                                <tr>
                                                    <td>{{ $reason->void_reason ?: 'No reason specified' }}</td>
                                                    <td class="text-end">{{ $reason->count }}</td>
                                                    <td class="text-end">
                                                        {{ currency_symbol() }}{{ number_format($reason->total_amount ?? 0, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Top Products -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Top Selling Products</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Quantity</th>
                                        <th class="text-end">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topProducts as $product)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="ms-2">
                                                        <h6 class="mb-0">{{ $product->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <span
                                                    class="badge bg-primary bg-opacity-10 text-primary">{{ $product->total_quantity }}</span>
                                            </td>
                                            <td class="text-end fw-semibold">
                                                {{ currency_symbol() }}{{ number_format($product->total_revenue ?? 0, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">
                                                <i class="fas fa-inbox me-2"></i> No sales data available
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Recent Transactions</h5>
                        <a href="{{ route('admin.pos.sales.index') }}" class="btn btn-sm btn-outline-primary">
                            View All
                            <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($recentTransactions as $transaction)
                                <a href="{{ route('admin.pos.sales.show', $transaction['id']) }}"
                                    class="list-group-item list-group-item-action border-0 py-3 text-decoration-none">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                                                <i class="fas fa-shopping-cart text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-dark">{{ $transaction['invoice_number'] }}</h6>
                                                <small class="text-muted">{{ $transaction['time_ago'] }}</small>
                                                <div class="text-muted small mt-1">
                                                    <i class="fas fa-user me-1"></i>{{ $transaction['customer_name'] }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $status = $transaction['status'] ?? '';
                                                $paymentStatus = $transaction['payment_status'] ?? '';
                                                $refundedAmount = $transaction['refunded_amount'] ?? 0;
                                                $badgeClass = 'bg-success';
                                                $labelText = ucfirst($paymentStatus);

                                                if ($status === 'voided') {
                                                    $badgeClass = 'bg-secondary';
                                                    $labelText = 'Voided';
                                                } elseif ($status === 'refunded' || $paymentStatus === 'refunded') {
                                                    $badgeClass = 'bg-danger';
                                                    $labelText = 'Refunded';
                                                } elseif ($refundedAmount > 0) {
                                                    $badgeClass = 'bg-info';
                                                    $labelText = 'Partial Refund';
                                                } elseif ($paymentStatus === 'partial' || $paymentStatus === 'partial_paid') {
                                                    $badgeClass = 'bg-warning';
                                                    $labelText = 'Partial';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }} bg-opacity-10 text-{{ str_replace('bg-', '', $badgeClass) }} me-2">
                                                {{ $labelText }}
                                            </span>
                                            <span class="fw-bold text-dark">{{ currency_symbol() }}{{ number_format($transaction['total'] ?? 0, 2) }}</span>
                                            <i class="fas fa-chevron-right text-muted small ms-2"></i>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                    <p class="mb-0 text-muted">No recent transactions</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.js"></script>
    <script>
        // Currency settings
        const currencySymbol = '{{ currency_symbol() }}';

        // Payment Distribution Chart
        const paymentDistributionOptions = {
            series: [
                        {{ $paymentAnalytics['paid_amount'] }},
                        {{ $paymentAnalytics['net_refunded'] }},
                        {{ $paymentAnalytics['tip_amount'] }},
                        {{ $paymentAnalytics['unpaid_amount'] }},
                        {{ $paymentAnalytics['discount_amount'] }},
                {{ $paymentAnalytics['voided_amount'] }}
            ],
            chart: {
                type: 'donut',
                height: 280
            },
            labels: ['Paid Revenue', 'Net Refunded', 'Tips', 'Unpaid', 'Discounts', 'Voided'],
            colors: ['#28a745', '#dc3545', '#17a2b8', '#ffc107', '#6c757d', '#adb5bd'],
            legend: {
                position: 'bottom'
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return Math.round(val) + '%';
                }
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return currencySymbol + value.toFixed(2);
                    }
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function (w) {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return currencySymbol + total.toFixed(2);
                                }
                            }
                        }
                    }
                }
            }
        };

        // Void Reasons Chart
        const voidReasonsData = @json($voidAnalytics['void_reasons']);
        const voidReasonsLabels = @json($voidAnalytics['void_reasons']->pluck('void_reason')->map(fn($reason) => $reason ?: 'No reason'));
        const voidReasonsCounts = @json($voidAnalytics['void_reasons']->pluck('count'));

        const voidReasonsOptions = {
            series: [{
                name: 'Void Count',
                data: voidReasonsCounts
            }],
            chart: {
                type: 'bar',
                height: 200,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '80%',
                    distributed: true,
                    borderRadius: 4,
                }
            },
            colors: ['#ffc107', '#ffd54f', '#ffe082', '#ffecb3', '#fff8e1'],
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: voidReasonsLabels,
                labels: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    show: true
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + (val === 1 ? ' void' : ' voids');
                    }
                }
            }
        };

        // Data from Controller
        const dailyData = {!! json_encode($revenueTrends) !!};
        const weeklyData = {!! json_encode($weeklySales->pluck('total', 'week')) !!};
        const monthlyData = {!! json_encode($monthlySales->pluck('total', 'month')) !!};

        const hourlyData = {!! json_encode($hourlySales) !!};

        // --- Revenue Trends Chart ---
        let revenueChart;

        function initRevenueChart(data, type) {
            const dates = Object.keys(data);
            const values = Object.values(data);

            const options = {
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: { show: false },
                    animations: { enabled: true }
                },
                series: [{
                    name: 'Revenue',
                    data: values.map((value, index) => ({
                        x: dates[index],
                        y: value
                    }))
                }],
                xaxis: {
                    type: type === 'day' ? 'datetime' : 'category',
                    labels: {
                        style: { colors: '#6B7280', fontSize: '12px' }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return currencySymbol + value.toLocaleString(undefined, {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                        }
                    }
                },
                colors: ['#3B82F6'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                    }
                },
                stroke: { width: 3, curve: 'smooth' },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return currencySymbol + value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                    }
                }
            };

            if (revenueChart) {
                revenueChart.destroy();
            }
            revenueChart = new ApexCharts(document.querySelector("#revenueChart"), options);
            revenueChart.render();
        }

        // Initialize with daily data
        initRevenueChart(dailyData, 'day');

        // Function to update chart
        window.updateRevenueChart = function (period) {
            // Update active button
            document.querySelectorAll('.btn-group button').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            if (period === 'day') {
                initRevenueChart(dailyData, 'day');
            } else if (period === 'week') {
                initRevenueChart(weeklyData, 'category');
            } else if (period === 'month') {
                initRevenueChart(monthlyData, 'category');
            }
        };

        // --- Hourly Sales Chart ---
        const hours = Array.from({ length: 24 }, (_, i) => i);
        const hourlyValues = hours.map(h => hourlyData[h] || 0);

        const hourlyOptions = {
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: false }
            },
            series: [{
                name: 'Sales',
                data: hourlyValues
            }],
            xaxis: {
                categories: hours.map(h => `${h}:00`),
                labels: {
                    style: { colors: '#6B7280', fontSize: '12px' }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return currencySymbol + value.toLocaleString();
                    }
                }
            },
            colors: ['#10B981'],
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '60%',
                }
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return currencySymbol + value.toLocaleString(undefined, { minimumFractionDigits: 2 });
                    }
                }
            }
        };

        const hourlyChart = new ApexCharts(document.querySelector("#hourlyChart"), hourlyOptions);
        hourlyChart.render();

        // --- Category Chart ---
        // Initialize charts when document is ready
        document.addEventListener('DOMContentLoaded', function () {
            // Payment Distribution Chart
            const paymentDistributionChart = new ApexCharts(
                document.querySelector("#paymentDistributionChart"),
                paymentDistributionOptions
            );
            paymentDistributionChart.render();

            // Void Reasons Chart
            if (voidReasonsData.length > 0) {
                const voidReasonsChart = new ApexCharts(
                    document.querySelector("#voidReasonsChart"),
                    voidReasonsOptions
                );
                voidReasonsChart.render();
            }
        });

        const categoryData = @json($salesByCategory);
        const categoryColors = @json($categoryColors);
        const categoryLabels = categoryData.map(item => item.name);
        const categorySeries = categoryData.map(item => item.total_revenue);

        const categoryOptions = {
            chart: {
                type: 'donut',
                height: 200,
                toolbar: { show: false }
            },
            series: categorySeries,
            labels: categoryLabels,
            colors: categoryColors,
            legend: { show: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function (w) {
                                    const sum = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return currencySymbol + sum.toLocaleString(undefined, {
                                        minimumFractionDigits: 0,
                                        maximumFractionDigits: 0
                                    });
                                }
                            }
                        }
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return currencySymbol + value.toLocaleString(undefined, { minimumFractionDigits: 2 });
                    }
                }
            }
        };

        const categoryChart = new ApexCharts(document.querySelector("#categoryChart"), categoryOptions);
        categoryChart.render();

        // Resize handler
        window.addEventListener('resize', function () {
            revenueChart.updateOptions({ chart: { width: '100%' } });
            hourlyChart.updateOptions({ chart: { width: '100%' } });
            categoryChart.updateOptions({ chart: { width: '100%' } });
        });
    </script>
@endpush