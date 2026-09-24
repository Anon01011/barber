@extends('layouts.app')

@section('content')

    @push('styles')
        <style>
            :root {
                --glass-bg: rgba(255, 255, 255, 0.95);
                --glass-border: rgba(255, 255, 255, 0.3);
                --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
                --orange-gradient: linear-gradient(135deg, #ff9966 0%, #ff5e62 100%);
                --blue-gradient: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%);
                --danger-gradient: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
                --purple-gradient: linear-gradient(135deg, #6f42c1 0%, #4e278b 100%);
                --info-gradient: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
                --warning-gradient: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
            }

            .stat-card-premium {
                border: none;
                border-radius: 20px;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                overflow: hidden;
                color: white;
                position: relative;
                min-height: 140px;
            }

            .stat-card-premium:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            }

            .stat-card-premium .card-body {
                position: relative;
                z-index: 2;
                padding: 1.5rem;
            }

            .stat-card-premium .bg-icon {
                position: absolute;
                right: -15px;
                bottom: -15px;
                font-size: 5.5rem;
                opacity: 0.2;
                z-index: 1;
                transform: rotate(-15deg);
                color: white;
            }

            .bg-primary-grad {
                background: var(--primary-gradient);
            }

            .bg-success-grad {
                background: var(--success-gradient);
            }

            .bg-orange-grad {
                background: var(--orange-gradient);
            }

            .bg-blue-grad {
                background: var(--blue-gradient);
            }

            .bg-danger-grad {
                background: var(--danger-gradient);
            }

            .bg-purple-grad {
                background: var(--purple-gradient);
            }

            .bg-info-grad {
                background: var(--info-gradient);
            }

            .bg-warning-grad {
                background: var(--warning-gradient);
            }

            .glass-card {
                background: var(--glass-bg);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border: 1px solid var(--glass-border);
                box-shadow: var(--premium-shadow);
                border-radius: 20px;
            }

            .filter-card {
                border-radius: 20px;
                border: none;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            }

            .form-control,
            .form-select {
                border-radius: 12px;
                border: 1px solid #e0e0e0;
                padding: 0.6rem 1rem;
                background-color: #f8f9fa;
            }

            .form-control:focus,
            .form-select:focus {
                box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.1);
                border-color: #667eea;
                background-color: #fff;
            }

            .btn-premium {
                background: var(--primary-gradient);
                border: none;
                color: white;
                border-radius: 12px;
                padding: 0.6rem 1.5rem;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .btn-premium:hover {
                transform: scale(1.02);
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
                color: white;
            }

            .table-premium thead th {
                background-color: #f8f9fc;
                text-transform: uppercase;
                font-size: 0.75rem;
                letter-spacing: 0.05em;
                font-weight: 700;
                color: #4e73df;
                border: none;
                padding: 1rem;
            }

            .badge-premium {
                padding: 0.5em 1em;
                border-radius: 30px;
                font-weight: 600;
            }

            /* Staff Specific Styles */
            .chart-container {
                position: relative;
                height: 300px;
                width: 100%;
            }

            .staff-avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                object-fit: cover;
                border: 2px solid #fff;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

            .staff-initials {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: #4e73df;
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                font-size: 0.9rem;
                border: 2px solid #fff;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

            .trend-up {
                color: #1cc88a;
            }

            .trend-down {
                color: #e74a3b;
            }
        </style>
    @endpush

    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
            <div>
                <h1 class="h3 mb-1 font-weight-bold text-gray-800">
                    <i class="fas fa-users-cog text-primary mr-2"></i>Staff Performance
                </h1>
                <p class="text-muted small mb-0">Monitor staff productivity, revenue, and earnings</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-success btn-sm shadow-sm" id="exportStaffBtn">
                    <i class="fas fa-download me-2"></i>Export Report
                </button>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Date & Advanced Filters -->
            <div class="card glass-card mb-4">
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('admin.reports.staff') }}" id="filterForm" class="row g-3 align-items-end">
                        <!-- Date Period -->
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label small fw-bold text-uppercase text-muted mb-1">Date Period</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="far fa-calendar-alt"></i></span>
                                <input type="date" class="form-control border-start-0 ps-0" id="start_date" name="start_date" value="{{ $startDate }}" placeholder="From">
                                <span class="input-group-text bg-white text-muted border-0">to</span>
                                <input type="date" class="form-control border-start-0 ps-0" id="end_date" name="end_date" value="{{ $endDate }}" placeholder="To">
                            </div>
                        </div>

                        <!-- Primary Filters -->
                        <div class="col-xl-3 col-md-6">
                            <label for="branch_id" class="form-label small fw-bold text-uppercase text-muted mb-1">Branch</label>
                            <select class="form-select" id="branch_id" name="branch_id">
                                <option value="">All Branches</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <label for="staff_id" class="form-label small fw-bold text-uppercase text-muted mb-1">Staff</label>
                            <select class="form-select" id="staff_id" name="staff_id">
                                <option value="">All Staff</option>
                                @foreach($allStaff as $s)
                                    <option value="{{ $s->id }}" {{ $staffIdFilter == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="d-flex gap-2">
                                 <button type="submit" class="btn btn-premium flex-grow-1 shadow-sm">
                                    <i class="fas fa-filter me-2"></i>Apply
                                </button>
                                <button type="button" class="btn btn-light border shadow-sm" data-bs-toggle="collapse" data-bs-target="#advancedFilters" aria-expanded="false" title="Advanced Filters">
                                    <i class="fas fa-sliders-h text-primary"></i>
                                </button>
                                <a href="{{ route('admin.reports.staff') }}" class="btn btn-light border shadow-sm" title="Reset Filters">
                                    <i class="fas fa-undo text-secondary"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Advanced Filters Collapsed -->
                         <div class="collapse w-100 mt-2" id="advancedFilters">
                            <div class="p-3 bg-light rounded-4 border mt-2">
                                <div class="row g-3">
                                     <div class="col-md-6">
                                        <label for="service_id" class="form-label small fw-bold text-uppercase text-muted mb-1">Service</label>
                                        <select class="form-select" id="service_id" name="service_id">
                                            <option value="">All Services</option>
                                            @foreach($allServices as $s)
                                                <option value="{{ $s->id }}" {{ $serviceId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="customer_id" class="form-label small fw-bold text-uppercase text-muted mb-1">Customer</label>
                                        <select class="form-select" id="customer_id" name="customer_id">
                                            <option value="">All Customers</option>
                                            @foreach($allCustomers as $c)
                                                <option value="{{ $c->id }}" {{ $customerId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card-premium bg-primary-grad shadow h-100">
                        <div class="card-body">
                            <div class="text-white-50 small font-weight-bold text-uppercase mb-1">Total Salon Revenue</div>
                            <div class="h3 mb-0 font-weight-bold">
                                {{ currency_symbol() }}{{ number_format($totalSalonRevenue, 2) }}
                            </div>
                            <div class="mt-2 small">
                                <span class="text-white-50"><i class="fas fa-calendar-alt mr-1"></i> Selected Period</span>
                            </div>
                            <i class="fas fa-dollar-sign bg-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card-premium bg-success-grad shadow h-100">
                        <div class="card-body">
                            <div class="text-white-50 small font-weight-bold text-uppercase mb-1">Total Commissions</div>
                            <div class="h3 mb-0 font-weight-bold">
                                {{ currency_symbol() }}{{ number_format($totalSalonCommission, 2) }}
                            </div>
                            <div class="mt-2 small">
                                <span class="text-white-50"><i class="fas fa-percentage mr-1"></i> Paid to staff</span>
                            </div>
                            <i class="fas fa-hand-holding-usd bg-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card-premium bg-info-grad shadow h-100">
                        <div class="card-body">
                            <div class="text-white-50 small font-weight-bold text-uppercase mb-1">Total Tips</div>
                            <div class="h3 mb-0 font-weight-bold">{{ currency_symbol() }}{{ number_format($totalSalonTips, 2) }}
                            </div>
                            <div class="mt-2 small">
                                <span class="text-white-50"><i class="fas fa-coins mr-1"></i> Customer gratuity</span>
                            </div>
                            <i class="fas fa-piggy-bank bg-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card-premium bg-warning-grad shadow h-100">
                        <div class="card-body">
                            <div class="text-white-50 small font-weight-bold text-uppercase mb-1">Top Performer</div>
                            <div class="h3 mb-0 font-weight-bold text-truncate">
                                {{ $topPerformer ? $topPerformer['name'] : 'N/A' }}
                            </div>
                            <div class="mt-2 small">
                                <span class="text-white-50"><i class="fas fa-trophy mr-1"></i> Highest revenue generator</span>
                            </div>
                            <i class="fas fa-star bg-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <!-- Revenue Chart -->
                <div class="col-xl-8 col-lg-7 mb-4">
                    <div class="card shadow h-100 border-0">
                        <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Revenue by Staff</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="staffRevenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Earnings Breakdown Pie -->
                <div class="col-xl-4 col-lg-5 mb-4">
                    <div class="card shadow h-100 border-0">
                        <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Earnings Distribution</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="earningsPieChart"></canvas>
                            </div>
                            <div class="mt-4 small text-center">
                                <span class="mr-2"><i class="fas fa-circle text-primary"></i> Commissions</span>
                                <span class="mr-2"><i class="fas fa-circle text-info"></i> Tips</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Performance Table -->
            <div class="card shadow mb-4 border-0">
                <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Detailed Staff Performance</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-premium" id="staffTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Staff Member</th>
                                    <th class="text-center">Activity</th>
                                    <th class="text-right">Service Rev.</th>
                                    <th class="text-right">Product Rev.</th>
                                    <th class="text-right">Package Rev.</th>
                                    <th class="text-right">Total Rev.</th>
                                    <th class="text-right">Commission</th>
                                    <th class="text-right">Tips</th>
                                    <th class="text-right">Earnings</th>
                                    <th class="text-center">Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staffStats as $staff)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($staff['avatar'])
                                                    <img src="{{ $staff['avatar'] }}" class="staff-avatar mr-3">
                                                @else
                                                    <div class="staff-initials mr-3">
                                                        {{ strtoupper(substr($staff['name'], 0, 2)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="font-weight-bold text-dark">{{ $staff['name'] }}</div>
                                                    <div class="small text-muted">Avg:
                                                        {{ currency_symbol() }}{{ number_format($staff['avg_per_booking'], 2) }}/visit
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="small">
                                                <span class="badge bg-light text-dark">{{ $staff['booking_count'] }} Bookings</span>
                                            </div>
                                            <div class="small mt-1">
                                                <span class="badge bg-light text-dark">{{ $staff['pos_count'] }} POS</span>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            {{ currency_symbol() }}{{ number_format($staff['service_revenue'], 2) }}
                                        </td>
                                        <td class="text-right">
                                            {{ currency_symbol() }}{{ number_format($staff['product_revenue'], 2) }}
                                        </td>
                                        <td class="text-right">
                                            {{ currency_symbol() }}{{ number_format($staff['package_revenue'], 2) }}
                                        </td>
                                        <td class="text-right font-weight-bold text-primary">
                                            {{ currency_symbol() }}{{ number_format($staff['total_revenue'], 2) }}
                                        </td>
                                        <td class="text-right text-success">
                                            {{ currency_symbol() }}{{ number_format($staff['commission'], 2) }}
                                        </td>
                                        <td class="text-right text-info">
                                            {{ currency_symbol() }}{{ number_format($staff['tips'], 2) }}
                                        </td>
                                        <td class="text-right font-weight-bold text-success">
                                            {{ currency_symbol() }}{{ number_format($staff['total_earnings'], 2) }}
                                        </td>
                                        <td class="text-center">
                                            @if($staff['trend'] > 0)
                                                <span class="trend-up"><i class="fas fa-caret-up"></i>
                                                    {{ number_format($staff['trend'], 1) }}%</span>
                                            @elseif($staff['trend'] < 0)
                                                <span class="trend-down"><i class="fas fa-caret-down"></i>
                                                    {{ number_format(abs($staff['trend']), 1) }}%</span>
                                            @else
                                                <span class="text-muted small">--</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                $(document).ready(function () {
                    // Initialize DataTable
                    if ($.fn.DataTable.isDataTable('#staffTable')) {
                        $('#staffTable').DataTable().destroy();
                    }
                    $('#staffTable').DataTable({
                        "order": [[5, "desc"]],
                        "pageLength": 10,
                        "language": {
                            "search": "_INPUT_",
                            "searchPlaceholder": "Search staff..."
                        }
                    });

                    // Staff Revenue Bar Chart
                    const revCtx = document.getElementById('staffRevenueChart').getContext('2d');
                    new Chart(revCtx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($staffStats->pluck('name')) !!},
                            datasets: [{
                                label: 'Service Revenue',
                                data: {!! json_encode($staffStats->pluck('service_revenue')) !!},
                                backgroundColor: '#4e73df',
                                borderRadius: 5,
                            }, {
                                label: 'Product Revenue',
                                data: {!! json_encode($staffStats->pluck('product_revenue')) !!},
                                backgroundColor: '#1cc88a',
                                borderRadius: 5,
                            }, {
                                label: 'Package Revenue',
                                data: {!! json_encode($staffStats->pluck('package_revenue')) !!},
                                backgroundColor: '#f6c23e',
                                borderRadius: 5,
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            scales: {
                                x: { stacked: true, grid: { display: false } },
                                y: { stacked: true, beginAtZero: true, grid: { color: '#f8f9fc' } }
                            },
                            plugins: {
                                legend: { position: 'top', align: 'end' }
                            }
                        }
                    });

                    // Earnings Pie Chart
                    const pieCtx = document.getElementById('earningsPieChart').getContext('2d');
                    new Chart(pieCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Commissions', 'Tips'],
                            datasets: [{
                                data: [{{ $totalSalonCommission }}, {{ $totalSalonTips }}],
                                backgroundColor: ['#4e73df', '#36b9cc'],
                                hoverBackgroundColor: ['#2e59d9', '#2c9faf'],
                                hoverBorderColor: "rgba(234, 236, 244, 1)",
                                borderWidth: 0,
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            cutout: '75%',
                            plugins: {
                                legend: { display: false }
                            }
                        }
                    });

                    // Export Staff Report
                    $('#exportStaffBtn').on('click', function () {
                        const startDate = $('#start_date').val();
                        const endDate = $('#end_date').val();
                        const branchId = $('#branch_id').val();
                        const staffId = $('#staff_id').val();
                        const serviceId = $('#service_id').val();
                        const customerId = $('#customer_id').val();

                        const url = new URL('{{ route("admin.reports.staff.export") }}', window.location.origin);
                        if (startDate) url.searchParams.append('start_date', startDate);
                        if (endDate) url.searchParams.append('end_date', endDate);
                        if (branchId) url.searchParams.append('branch_id', branchId);
                        if (staffId) url.searchParams.append('staff_id', staffId);
                        if (serviceId) url.searchParams.append('service_id', serviceId);
                        if (customerId) url.searchParams.append('customer_id', customerId);

                        window.location.href = url.toString();
                    });
                });
            </script>
        @endpush
@endsection