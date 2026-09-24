@extends('layouts.app')

@section('content')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            --success-gradient: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            --info-gradient: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
            --warning-gradient: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
            --danger-gradient: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
            --purple-gradient: linear-gradient(135deg, #6f42c1 0%, #4e278b 100%);
        }

        .stat-card-premium {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            color: white;
        }

        .stat-card-premium:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-card-premium .card-body {
            position: relative;
            z-index: 1;
            padding: 1.5rem;
        }

        .stat-card-premium .bg-icon {
            position: absolute;
            right: -10px;
            bottom: -10px;
            font-size: 5rem;
            opacity: 0.15;
            z-index: 0;
            transform: rotate(-15deg);
        }

        .bg-primary-grad {
            background: var(--primary-gradient);
        }

        .bg-success-grad {
            background: var(--success-gradient);
        }

        .bg-info-grad {
            background: var(--info-gradient);
        }

        .bg-warning-grad {
            background: var(--warning-gradient);
        }

        .bg-danger-grad {
            background: var(--danger-gradient);
        }

        .bg-purple-grad {
            background: var(--purple-gradient);
        }

        .glass-card {
            background: #ffffff;
            border: 1px solid #edf2f7;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 12px;
        }

        .filter-label {
            font-size: 0.7rem;
            font-weight: 700;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .form-select,
        .form-control {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            padding: 0.6rem 0.75rem;
            color: #4a5568;
            font-size: 0.875rem;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #cbd5e0;
            box-shadow: none;
            background-color: #fff;
        }

        .btn-apply {
            background: linear-gradient(90deg, #ec4899 0%, #8b5cf6 100%);
            border: none;
            color: white;
            padding: 0.6rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: opacity 0.2s;
        }

        .btn-apply:hover {
            opacity: 0.9;
            color: white;
        }

        .btn-icon-only {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #edf2f7;
            border: 1px solid #e2e8f0;
            color: #4a5568;
            transition: background 0.2s;
        }

        .btn-icon-only:hover {
            background: #e2e8f0;
        }

        .btn-icon-only.active {
            background: #4299e1;
            color: white;
            border-color: #3182ce;
        }

        .advanced-filter-box {
            background-color: #f7fafc;
            border: 1px solid #edf2f7;
            border-radius: 12px;
            padding: 1.25rem;
        }

        .segment-badge {
            padding: 0.5em 1em;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-premium thead th {
            background-color: #f8f9fc;
            text-transform: uppercase;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 1px;
            color: #4e73df;
            border-top: none;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .at-risk-item {
            border-left: 4px solid #e74a3b;
            transition: background-color 0.2s;
        }

        .at-risk-item:hover {
            background-color: #fff5f5;
        }
    </style>

    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
            <div>
                <h1 class="h3 mb-1 font-weight-bold text-gray-800">
                    <i class="fas fa-chart-pie text-primary mr-2"></i>Customer Insights
                </h1>
                <p class="text-muted small mb-0">Deep dive into your customer behavior and retention</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-success btn-sm shadow-sm" id="exportCustomersBtn">
                    <i class="fas fa-download me-2"></i>Export Report
                </button>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Date & Advanced Filters -->
        <div class="card glass-card border-0 mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('admin.reports.customers') }}" id="filterForm">
                    <div class="row g-3 align-items-end mb-3">
                        <!-- Date Period -->
                        <div class="col-xl-3 col-md-4">
                            <label class="filter-label d-block">Date Period</label>
                            <select class="form-select" id="range" name="range">
                                <option value="today" {{ $range == 'today' ? 'selected' : '' }}>Today</option>
                                <option value="yesterday" {{ $range == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                                <option value="this_week" {{ $range == 'this_week' ? 'selected' : '' }}>This Week</option>
                                <option value="last_week" {{ $range == 'last_week' ? 'selected' : '' }}>Last Week</option>
                                <option value="this_month" {{ $range == 'this_month' ? 'selected' : '' }}>This Month</option>
                                <option value="last_month" {{ $range == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                <option value="3_months" {{ $range == '3_months' ? 'selected' : '' }}>Last 3 Months</option>
                                <option value="6_months" {{ $range == '6_months' ? 'selected' : '' }}>Last 6 Months</option>
                                <option value="this_year" {{ $range == 'this_year' ? 'selected' : '' }}>This Year</option>
                                <option value="custom" {{ $range == 'custom' ? 'selected' : '' }}>Custom Range</option>
                            </select>

                            <div class="input-group mt-2 date-inputs"
                                style="{{ $range !== 'custom' ? 'display: none;' : '' }}">
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                    value="{{ $startDate }}">
                                <span class="input-group-text bg-transparent border-0">to</span>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                    value="{{ $endDate }}">
                            </div>
                        </div>

                        <!-- Branch Filter -->
                        <div class="col-xl-2 col-md-3">
                            <label for="branch_id" class="filter-label d-block">Branch</label>
                            <select class="form-select" id="branch_id" name="branch_id">
                                <option value="">All Branches</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Segment Filter -->
                        <div class="col-xl-2 col-md-3">
                            <label for="segment" class="filter-label d-block">Segment</label>
                            <select class="form-select" id="segment" name="segment">
                                <option value="">All Segments</option>
                                <option value="VIP" {{ $segmentFilter == 'VIP' ? 'selected' : '' }}>VIP</option>
                                <option value="Loyal" {{ $segmentFilter == 'Loyal' ? 'selected' : '' }}>Loyal</option>
                                <option value="Regular" {{ $segmentFilter == 'Regular' ? 'selected' : '' }}>Regular</option>
                                <option value="New" {{ $segmentFilter == 'New' ? 'selected' : '' }}>New</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-xl-5 col-md-12">
                            <div class="d-flex align-items-center gap-2">
                                <button type="submit" class="btn btn-apply flex-grow-1">
                                    <i class="fas fa-filter"></i> Apply
                                </button>
                                <button type="button" class="btn btn-icon-only" data-bs-toggle="collapse"
                                    data-bs-target="#advancedFilters" aria-expanded="false" title="Advanced Filters">
                                    <i class="fas fa-sliders-h"></i>
                                </button>
                                <a href="{{ route('admin.reports.customers') }}" class="btn btn-icon-only"
                                    title="Reset Filters">
                                    <i class="fas fa-undo"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Filters Box -->
                    <div class="collapse w-100" id="advancedFilters">
                        <div class="advanced-filter-box mt-2">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label for="staff_id" class="filter-label d-block">Staff Member</label>
                                    <select class="form-select" id="staff_id" name="staff_id">
                                        <option value="">All Staff</option>
                                        @foreach($allStaff as $s)
                                            <option value="{{ $s->id }}" {{ $staffId == $s->id ? 'selected' : '' }}>{{ $s->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="service_id" class="filter-label d-block">Service</label>
                                    <select class="form-select" id="service_id" name="service_id">
                                        <option value="">All Services</option>
                                        @foreach($allServices as $s)
                                            <option value="{{ $s->id }}" {{ $serviceId == $s->id ? 'selected' : '' }}>
                                                {{ $s->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="status" class="filter-label d-block">Booking Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">All Statuses</option>
                                        <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed
                                        </option>
                                        <option value="confirmed" {{ $status == 'confirmed' ? 'selected' : '' }}>Confirmed
                                        </option>
                                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main Stats Row -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card-premium bg-primary-grad shadow h-100">
                    <div class="card-body">
                        <div class="text-white-50 small font-weight-bold text-uppercase mb-1">Total Customers</div>
                        <div class="h3 mb-0 font-weight-bold">{{ number_format($totalCustomers) }}</div>
                        <div class="mt-2 small">
                            <span class="text-white-50"><i class="fas fa-user-plus mr-1"></i> {{ $newCustomersCount }} new
                                this month</span>
                        </div>
                        <i class="fas fa-users bg-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card-premium bg-success-grad shadow h-100">
                    <div class="card-body">
                        <div class="text-white-50 small font-weight-bold text-uppercase mb-1">Active Customers</div>
                        <div class="h3 mb-0 font-weight-bold">{{ number_format($activeCustomers) }}</div>
                        <div class="mt-2 small">
                            <span class="text-white-50"><i class="fas fa-clock mr-1"></i> Visited in last 30 days</span>
                        </div>
                        <i class="fas fa-user-check bg-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card-premium bg-info-grad shadow h-100">
                    <div class="card-body">
                        <div class="text-white-50 small font-weight-bold text-uppercase mb-1">Retention Rate</div>
                        <div class="h3 mb-0 font-weight-bold">{{ number_format($retentionRate, 1) }}%</div>
                        <div class="mt-2 small">
                            <div class="progress progress-sm bg-white-50">
                                <div class="progress-bar bg-white" role="progressbar" style="width: {{ $retentionRate }}%">
                                </div>
                            </div>
                        </div>
                        <i class="fas fa-sync bg-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card-premium bg-purple-grad shadow h-100">
                    <div class="card-body">
                        <div class="text-white-50 small font-weight-bold text-uppercase mb-1">Avg Lifetime Value</div>
                        <div class="h3 mb-0 font-weight-bold">{{ currency_symbol() }}{{ number_format($averageLTV, 2) }}
                        </div>
                        <div class="mt-2 small">
                            <span class="text-white-50"><i class="fas fa-chart-line mr-1"></i> Average revenue per
                                customer</span>
                        </div>
                        <i class="fas fa-hand-holding-usd bg-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Segmentation Chart -->
            <div class="col-xl-4 col-lg-5 mb-4">
                <div class="card shadow h-100 border-0">
                    <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Customer Segments</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="segmentChart"></canvas>
                        </div>
                        <div class="mt-4 small">
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="fas fa-circle text-warning mr-1"></i> VIP</span>
                                <span class="font-weight-bold">{{ $vipCustomers }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="fas fa-circle text-primary mr-1"></i> Loyal</span>
                                <span class="font-weight-bold">{{ $loyalCustomers }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="fas fa-circle text-success mr-1"></i> Regular</span>
                                <span class="font-weight-bold">{{ $regularCustomers }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span><i class="fas fa-circle text-info mr-1"></i> New</span>
                                <span class="font-weight-bold">{{ $newCustomers }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- At Risk Customers -->
            <div class="col-xl-8 col-lg-7 mb-4">
                <div class="card shadow h-100 border-0">
                    <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-danger">
                            <i class="fas fa-exclamation-triangle mr-1"></i> At Risk Customers
                        </h6>
                        <span class="badge bg-danger-subtle text-danger">{{ $atRiskCustomersCount }} Total</span>
                    </div>
                    <div class="card-body p-0">
                        @if($atRiskCustomers->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($atRiskCustomers as $customer)
                                    <div class="list-group-item at-risk-item border-0 px-4 py-3">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <div class="font-weight-bold text-dark">{{ $customer->name }}</div>
                                                <div class="small text-muted">{{ $customer->display_phone }}</div>
                                            </div>
                                            <div class="col-auto text-right">
                                                <div class="small font-weight-bold text-danger">Last visit:
                                                    {{ \Carbon\Carbon::parse($customer->last_visit_date)->diffForHumans() }}
                                                </div>
                                                <div class="small text-muted">Total Spent:
                                                    {{ currency_symbol() }}{{ number_format($customer->total_spend, 2) }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <a href="{{ route('admin.customers.details', $customer->id) }}"
                                                    class="btn btn-sm btn-outline-primary rounded-pill">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                                <p class="text-muted">Great job! No customers are currently at risk.</p>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-white text-center py-3">
                        <small class="text-muted">Customers who haven't visited in 30-90 days</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Customers Table -->
        <div class="card shadow mb-4 border-0">
            <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-trophy mr-1 text-warning"></i> Top 20 Customers by Revenue
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 border-0 small text-uppercase font-weight-bold">Customer</th>
                                <th class="border-0 small text-uppercase font-weight-bold">Gender/Age</th>
                                <th class="border-0 small text-uppercase font-weight-bold">Customer Since</th>
                                <th class="border-0 small text-uppercase font-weight-bold">Visits</th>
                                <th class="border-0 small text-uppercase font-weight-bold">Last Visit</th>
                                <th class="border-0 small text-uppercase font-weight-bold">Total Paid</th>
                                <th class="border-0 small text-uppercase font-weight-bold">Refunded</th>
                                <th class="border-0 small text-uppercase font-weight-bold">Total Spend</th>
                                <th class="pe-4 border-0 small text-uppercase font-weight-bold text-right">Segment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topCustomers as $customer)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary-subtle text-primary rounded-circle mr-3 d-flex align-items-center justify-content-center"
                                                style="width: 32px; height: 32px;">
                                                <span class="small font-weight-bold">{{ substr($customer->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-dark">{{ $customer->name }}</div>
                                                <div class="text-muted small">{{ $customer->display_phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-dark">{{ ucfirst($customer->gender ?? 'N/A') }}</div>
                                        <div class="text-muted small">Age: {{ $customer->age }}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-dark">{{ format_date($customer->created_at) }}</div>
                                        <div class="text-muted small">First:
                                            {{ $customer->first_visit_date ? format_date($customer->first_visit_date) : 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $customer->total_visits }}</span>
                                    </td>
                                    <td class="small text-muted">
                                        {{ $customer->last_visit_date ? format_date($customer->last_visit_date) : 'N/A' }}
                                    </td>
                                    <td class="font-weight-medium">
                                        {{ format_currency($customer->total_paid) }}
                                    </td>
                                    <td class="font-weight-medium text-danger">
                                        {{ format_currency($customer->total_refunded) }}
                                    </td>
                                    <td class="font-weight-bold text-dark">
                                        {{ format_currency($customer->total_spend - $customer->total_refunded) }}
                                    </td>
                                    <td class="pe-4 text-right">
                                        @php
                                            $segmentClass = match ($customer->segment) {
                                                'VIP' => 'bg-warning-subtle text-warning border-warning',
                                                'Loyal' => 'bg-primary-subtle text-primary border-primary',
                                                'Regular' => 'bg-success-subtle text-success border-success',
                                                default => 'bg-info-subtle text-info border-info'
                                            };
                                        @endphp
                                        <span class="badge {{ $segmentClass }} border-0 px-3 py-2 rounded-pill small">
                                            {{ $customer->segment }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if($topCustomers->hasPages())
                <div class="card-footer bg-white border-top-0 py-3">
                    {{ $topCustomers->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            $(document).ready(function () {
                // Initialize DataTable
                $('#dataTable').DataTable({
                    "order": [[5, "desc"]],
                    "paging": false, // Disable client-side pagination as we use Laravel pagination
                    "info": false,   // Disable info as it might be confusing with server-side pagination
                    "language": {
                        "search": "_INPUT_",
                        "searchPlaceholder": "Search customers..."
                    }
                });

                // Segmentation Chart
                const ctx = document.getElementById('segmentChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['VIP', 'Loyal', 'Regular', 'New'],
                        datasets: [{
                            data: [{{ $vipCustomers }}, {{ $loyalCustomers }}, {{ $regularCustomers }}, {{ $newCustomers }}],
                            backgroundColor: ['#f6c23e', '#4e73df', '#1cc88a', '#36b9cc'],
                            hoverBackgroundColor: ['#dda20a', '#224abe', '#13855c', '#258391'],
                            hoverBorderColor: "rgba(234, 236, 244, 1)",
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: "rgb(255,255,255)",
                                bodyColor: "#858796",
                                borderColor: '#dddfeb',
                                borderWidth: 1,
                                xPadding: 15,
                                yPadding: 15,
                                displayColors: false,
                                caretPadding: 10,
                            }
                        }
                    }
                });

                // Handle Date Range Change
                $('#range').on('change', function () {
                    if ($(this).val() === 'custom') {
                        $('.date-inputs').show();
                    } else {
                        $('.date-inputs').hide();
                        $(this).closest('form').submit();
                    }
                });

                // Export customers
                $('#exportCustomersBtn').on('click', function () {
                    const branchId = $('#branch_id').val();
                    const staffId = $('#staff_id').val();
                    const serviceId = $('#service_id').val();
                    const status = $('#status').val();
                    const segment = $('#segment').val();
                    const range = $('#range').val();
                    const start_date = $('#start_date').val();
                    const end_date = $('#end_date').val();

                    const url = new URL('{{ route("admin.reports.customers.export") }}', window.location.origin);
                    if (branchId) url.searchParams.append('branch_id', branchId);
                    if (staffId) url.searchParams.append('staff_id', staffId);
                    if (serviceId) url.searchParams.append('service_id', serviceId);
                    if (status) url.searchParams.append('status', status);
                    if (segment) url.searchParams.append('segment', segment);
                    if (range) url.searchParams.append('range', range);
                    if (start_date) url.searchParams.append('start_date', start_date);
                    if (end_date) url.searchParams.append('end_date', end_date);

                    window.location.href = url.toString();
                });
            });
        </script>
    @endpush
@endsection