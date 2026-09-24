@extends('layouts.app')

@push('styles')
    <style>
        .stat-card {
            transition: all 0.3s ease;
            border-left: 4px solid;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            font-size: 2rem;
            opacity: 0.8;
        }

        .stat-value {
            font-weight: 600;
            font-size: 1.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.8;
        }

        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);
        }

        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="page-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Booking Analytics
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i></a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Bookings</a></li>
                            <li class="breadcrumb-item active">Statistics</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <!-- Total Bookings -->
            <div class="col-xxl-2 col-md-4 col-6">
                <div class="card stat-card border-start border-primary">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm rounded-circle bg-primary bg-opacity-10 p-2 me-3">
                                <i class="fas fa-calendar-check text-primary stat-icon"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="stat-value">{{ number_format($totalBookings) }}</div>
                                <div class="stat-label text-muted">Total Bookings</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Bookings -->
            <div class="col-xxl-2 col-md-4 col-6">
                <div class="card stat-card border-start border-success">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                <i class="fas fa-calendar-day text-white stat-icon"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="stat-value">{{ $todayBookings }}</div>
                                <div class="stat-label text-muted">Today</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- This Week -->
            <div class="col-xxl-2 col-md-4 col-6">
                <div class="card stat-card border-start border-info">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm rounded-circle bg-info bg-opacity-10 p-2 me-3">
                                <i class="fas fa-calendar-week text-info stat-icon"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="stat-value">{{ $weekBookings ?? '0' }}</div>
                                <div class="stat-label text-muted">This Week</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- This Month -->
            <div class="col-xxl-2 col-md-4 col-6">
                <div class="card stat-card border-start border-warning">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm rounded-circle bg-warning bg-opacity-10 p-2 me-3">
                                <i class="fas fa-calendar-alt text-warning stat-icon"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="stat-value">{{ $monthBookings ?? '0' }}</div>
                                <div class="stat-label text-muted">This Month</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming -->
            <div class="col-xxl-2 col-md-4 col-6">
                <div class="card stat-card border-start border-purple">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm rounded-circle bg-purple bg-opacity-10 p-2 me-3">
                                <i class="fas fa-calendar-plus text-purple stat-icon"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="stat-value">{{ $upcomingBookings }}</div>
                                <div class="stat-label text-muted">Upcoming</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed -->
            <div class="col-xxl-2 col-md-4 col-6">
                <div class="card stat-card border-start border-success">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                <i class="fas fa-check-circle text-success stat-icon"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="stat-value">{{ $completedBookings }}</div>
                                <div class="stat-label text-muted">Completed</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <!-- Total Booking Revenue -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stat-card border-start border-primary">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm rounded-circle bg-primary bg-opacity-10 p-2 me-3">
                                <i class="fas fa-file-invoice-dollar text-primary stat-icon"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="stat-value">
                                    {{ currency_symbol() }}{{ number_format($totalBookingAmount ?? 0, 2) }}
                                </div>
                                <div class="stat-label text-muted">Booking Revenue</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Tips -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stat-card border-start border-info">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm rounded-circle bg-info bg-opacity-10 p-2 me-3">
                                <i class="fas fa-hand-holding-usd text-info stat-icon"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="stat-value">{{ currency_symbol() }}{{ number_format($totalTips ?? 0, 2) }}</div>
                                <div class="stat-label text-muted">Total Tips</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Earnings -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stat-card border-start border-success">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                <i class="fas fa-wallet text-success stat-icon"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="stat-value">{{ currency_symbol() }}{{ number_format($totalEarnings ?? 0, 2) }}
                                </div>
                                <div class="stat-label text-muted">Total Earnings</div>
                            </div>
                            <span
                                class="badge {{ ($earningsChange ?? 0) >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ ($earningsChange ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ ($earningsChange ?? 0) >= 0 ? '+' : '' }}{{ $earningsChange ?? 0 }}%
                                <i class="fas fa-arrow-{{ ($earningsChange ?? 0) >= 0 ? 'up' : 'down' }} ms-1"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Average Booking Value -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stat-card border-start border-warning">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm rounded-circle bg-warning bg-opacity-10 p-2 me-3">
                                <i class="fas fa-calculator text-warning stat-icon"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="stat-value">{{ currency_symbol() }}{{ number_format($avgBookingValue ?? 0, 2) }}
                                </div>
                                <div class="stat-label text-muted">Avg. Booking</div>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Tip:
                                    {{ currency_symbol() }}{{ number_format($avgTipValue ?? 0, 2) }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <!-- Monthly Trends Chart -->
            <div class="col-xxl-8">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-line text-primary me-2"></i>Booking Trends
                            </h5>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#">Last 6 Months</a></li>
                                    <li><a class="dropdown-item" href="#">This Year</a></li>
                                    <li><a class="dropdown-item" href="#">All Time</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="monthlyTrendsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bookings by Status -->
            <div class="col-xxl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-pie text-primary me-2"></i>Status Distribution
                            </h5>
                        </div>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Third Row: Detailed Charts -->
        <div class="row g-3 mb-4">
            <!-- Revenue Trend -->
            <div class="col-xxl-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-line text-primary me-2"></i>Revenue Trend
                            </h5>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#">This Month</a></li>
                                    <li><a class="dropdown-item" href="#">Last 3 Months</a></li>
                                    <li><a class="dropdown-item" href="#">This Year</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="revenueTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Distribution -->
            <div class="col-xxl-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-pie text-primary me-2"></i>Service Distribution
                            </h5>
                            <a href="{{ route('admin.services.categories.index') }}" class="btn btn-sm btn-link">View
                                All</a>
                        </div>
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="serviceDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fourth Row: Top Services & Employees -->
        <div class="row g-3 mb-4">
            <!-- Top Services -->
            <div class="col-xxl-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-spa text-primary me-2"></i>Top Services
                            </h5>
                            <a href="{{ route('admin.services.categories.index') }}" class="btn btn-sm btn-link">View
                                All</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-centered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Service</th>
                                        <th>Bookings</th>
                                        <th>Revenue</th>
                                        <th>Rating</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topServices as $service)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-2">
                                                        <span class="avatar-title bg-soft-primary text-primary rounded">
                                                            {{ substr($service->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $service->name }}</h6>
                                                        <small
                                                            class="text-muted">{{ $service->category->name ?? 'General' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-soft-primary text-primary">{{ $service->booking_count }}</span>
                                            </td>
                                            <td class="fw-semibold">
                                                {{ currency_symbol() }}{{ number_format($service->price * $service->booking_count, 2) }}
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @php
                                                        $rating = $service->average_rating ?? 0;
                                                        $flooredRating = floor($rating);
                                                        $hasHalfStar = ($rating - $flooredRating) >= 0.5;
                                                    @endphp
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $flooredRating)
                                                            <i class="fas fa-star text-warning small"></i>
                                                        @elseif($i == $flooredRating + 1 && $hasHalfStar)
                                                            <i class="fas fa-star-half-alt text-warning small"></i>
                                                        @else
                                                            <i class="far fa-star text-light small"></i>
                                                        @endif
                                                    @endfor
                                                    <span class="ms-1">({{ number_format($rating, 1) }})</span>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-light view-service-details"
                                                    data-bs-toggle="modal" data-bs-target="#serviceDetailsModal"
                                                    data-service-id="{{ $service->service_id }}"
                                                    data-service-name="{{ $service->name }}"
                                                    data-service-category="{{ $service->category->name ?? 'General' }}"
                                                    data-service-price="{{ $service->price }}"
                                                    data-service-bookings="{{ $service->booking_count }}"
                                                    data-service-rating="{{ $service->average_rating ?? 0 }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No services found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Employees -->
            <div class="col-xxl-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-users text-primary me-2"></i>Top Employees
                            </h5>
                            <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-link">View All</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-centered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Employee</th>
                                        <th>Bookings</th>
                                        <th>Earnings</th>
                                        <th>Rating</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topEmployees as $employee)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-2">
                                                        <img src="{{ $employee->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($employee->name) . '&background=random' }}"
                                                            alt="{{ $employee->name }}" class="rounded-circle"
                                                            onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name='+encodeURIComponent('{{ $employee->name[0] ?? 'U' }}')+''">
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $employee->name }}</h6>
                                                        <small
                                                            class="text-muted">{{ $employee->position ?? 'Employee' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">{{ $employee->booking_count }}</td>
                                            <td class="text-nowrap">
                                                @if(isset($employee->booking_amount) && $employee->booking_amount > 0)
                                                    <div class="fw-semibold">
                                                        {{ currency_symbol() }}{{ number_format($employee->booking_amount, 2) }}
                                                    </div>
                                                    <small class="text-muted">Tip:
                                                        {{ currency_symbol() }}{{ number_format($employee->tips, 2) }}</small>
                                                @else
                                                    {{ currency_symbol() }}0.00
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @php
                                                        $rating = $employee->rating ?? 0;
                                                        $flooredRating = floor($rating);
                                                        $hasHalfStar = ($rating - $flooredRating) >= 0.5;
                                                    @endphp
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $flooredRating)
                                                            <i class="fas fa-star text-warning small"></i>
                                                        @elseif($i == $flooredRating + 1 && $hasHalfStar)
                                                            <i class="fas fa-star-half-alt text-warning small"></i>
                                                        @else
                                                            <i class="far fa-star text-light small"></i>
                                                        @endif
                                                    @endfor
                                                    <span class="ms-1">({{ number_format($rating, 1) }})</span>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-light view-employee-details"
                                                    data-bs-toggle="modal" data-bs-target="#employeeDetailsModal"
                                                    data-employee-id="{{ $employee->staff_id }}"
                                                    data-employee-name="{{ $employee->name }}"
                                                    data-employee-position="{{ $employee->position ?? 'Employee' }}"
                                                    data-employee-avatar="{{ $employee->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($employee->name) . '&background=random' }}"
                                                    data-employee-bookings="{{ $employee->booking_count }}"
                                                    data-employee-earnings="{{ $employee->revenue ?? 0 }}"
                                                    data-employee-rating="{{ $employee->rating ?? 0 }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No employees found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sixth Row: Top Customers -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user-friends text-primary me-2"></i>Top Customers
                            </h5>
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-link">View All</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-centered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Customer</th>
                                        <th>Total Bookings</th>
                                        <th>Booking Amount</th>
                                        <th>Tips</th>
                                        <th>Total Spent</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topCustomers as $customer)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-2">
                                                        <span class="avatar-title bg-soft-info text-info rounded-circle">
                                                            {{ substr($customer->customer->full_name ?? 'C', 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $customer->customer->full_name ?? 'Unknown' }}</h6>
                                                        <small
                                                            class="text-muted">{{ $customer->customer->display_phone ?? 'No Phone' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-soft-info text-info">{{ $customer->booking_count }}</span>
                                            </td>
                                            <td class="fw-semibold">
                                                {{ currency_symbol() }}{{ number_format($customer->total_booking_amount, 2) }}
                                            </td>
                                            <td class="text-success">
                                                {{ currency_symbol() }}{{ number_format($customer->total_tips, 2) }}
                                            </td>
                                            <td class="fw-bold text-primary">
                                                {{ currency_symbol() }}{{ number_format($customer->total_spent, 2) }}
                                            </td>
                                            <td>
                                                <button type="button" onclick="viewCustomer({{ $customer->customer_id }})"
                                                    class="btn btn-sm btn-light">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No customers found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fifth Row: Recent Reviews & Performance Metrics -->
        <div class="row g-3">
            <!-- Recent Reviews -->
            <div class="col-xxl-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-comments text-primary me-2"></i>Recent Reviews
                            </h5>
                            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-link">View Dashboard</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-centered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Customer</th>
                                        <th>Service</th>
                                        <th>Rating</th>
                                        <th>Comment</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(($recentReviews ?? []) as $review)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-2">
                                                        <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                            {{ substr($review->customer_name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>{{ $review->customer_name }}</div>
                                                </div>
                                            </td>
                                            <td>{{ $review->service_name }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @php
                                                        $rating = $review->rating ?? 0;
                                                        $flooredRating = floor($rating);
                                                        $hasHalfStar = ($rating - $flooredRating) >= 0.5;
                                                    @endphp
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $flooredRating)
                                                            <i class="fas fa-star text-warning small"></i>
                                                        @elseif($i == $flooredRating + 1 && $hasHalfStar)
                                                            <i class="fas fa-star-half-alt text-warning small"></i>
                                                        @else
                                                            <i class="far fa-star text-light small"></i>
                                                        @endif
                                                    @endfor
                                                    <span class="ms-1">({{ number_format($rating, 1) }})</span>
                                                </div>
                                            </td>
                                            <td class="text-truncate" style="max-width: 200px;"
                                                title="{{ $review->comment ?? '' }}">
                                                {{ Str::limit($review->comment ?? 'No comment', 30) }}
                                            </td>
                                            <td>{{ $review->created_at->diffForHumans() }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-light view-review"
                                                    data-review-id="{{ $review->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No reviews found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="col-xxl-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-tachometer-alt text-primary me-2"></i>Performance Metrics
                        </h5>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Booking Completion</span>
                                <span class="fw-semibold">{{ $bookingCompletionRate ?? 0 }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ $bookingCompletionRate ?? 0 }}%"
                                    aria-valuenow="{{ $bookingCompletionRate ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Customer Satisfaction</span>
                                <span class="fw-semibold">{{ $customerSatisfaction ?? 0 }}/5</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-info" role="progressbar"
                                    style="width: {{ ($customerSatisfaction ?? 0) * 20 }}%"
                                    aria-valuenow="{{ $customerSatisfaction ?? 0 }}" aria-valuemin="0" aria-valuemax="5">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Repeat Customers</span>
                                <span class="fw-semibold">{{ $repeatCustomerRate ?? 0 }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ $repeatCustomerRate ?? 0 }}%"
                                    aria-valuenow="{{ $repeatCustomerRate ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Avg. Booking Value</span>
                                <span
                                    class="fw-semibold">{{ currency_symbol() }}{{ number_format($avgBookingValue ?? 0, 2) }}</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Peak Hours</span>
                                <span class="fw-semibold">{{ $peakHours ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Busiest Day</span>
                                <span class="fw-semibold">{{ $busiestDay ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Cancellation Rate</span>
                                <span class="fw-semibold">{{ $cancellationRate ?? 0 }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-danger" role="progressbar"
                                    style="width: {{ $cancellationRate ?? 0 }}%"
                                    aria-valuenow="{{ $cancellationRate ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h6 class="text-muted mb-3">Quick Stats</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="p-2 border rounded">
                                        <h6 class="mb-0">
                                            {{ currency_symbol() }}{{ number_format($avgBookingValue ?? 0, 2) }}
                                        </h6>
                                        <p class="text-muted mb-0 small">Avg. Booking Value</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 border rounded">
                                        <h6 class="mb-0">{{ $peakHours ?? 'N/A' }}</h6>
                                        <p class="text-muted mb-0 small">Peak Hours</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 border rounded">
                                        <h6 class="mb-0">{{ $busiestDay ?? 'N/A' }}</h6>
                                        <p class="text-muted mb-0 small">Busiest Day</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 border rounded">
                                        <h6 class="mb-0 text-danger">{{ $cancellationRate ?? 0 }}%</h6>
                                        <p class="text-muted mb-0 small">Cancellation Rate</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Details Modal -->
        <div class="modal fade" id="reviewDetailsModal" tabindex="-1" aria-labelledby="reviewDetailsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
                    <div class="modal-header text-white border-0 py-4"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-2 me-3" style="background: rgba(255,255,255,0.25);">
                                <i class="fas fa-star text-warning fs-4"></i>
                            </div>
                            <div>
                                <h5 class="modal-title mb-0" id="reviewDetailsModalLabel">Review Details</h5>
                                <small style="opacity: 0.75;">Customer Feedback</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <!-- Customer & Service Info -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100" style="border-radius: 0.75rem;">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-user-circle text-primary me-2"></i>
                                            <label class="text-muted small mb-0">Customer</label>
                                        </div>
                                        <h6 class="mb-0 fw-semibold" id="reviewCustomerName"></h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100" style="border-radius: 0.75rem;">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-spa text-primary me-2"></i>
                                            <label class="text-muted small mb-0">Service</label>
                                        </div>
                                        <h6 class="mb-0 fw-semibold" id="reviewServiceName"></h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rating Section -->
                        <div class="card border-0 mb-4" style="background: rgba(255, 193, 7, 0.1); border-radius: 0.75rem;">
                            <div class="card-body text-center py-4">
                                <label class="text-muted small d-block mb-3">Rating</label>
                                <div id="reviewRatingStars" class="d-flex align-items-center justify-content-center mb-2"
                                    style="font-size: 1.5rem;"></div>
                            </div>
                        </div>

                        <!-- Comment Section -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-comment-dots text-primary me-2"></i>
                                <label class="text-muted small mb-0">Customer Feedback</label>
                            </div>
                            <div class="card border-0 bg-light" style="border-radius: 0.75rem;">
                                <div class="card-body">
                                    <p id="reviewComment" class="mb-0 text-dark"
                                        style="line-height: 1.8; font-size: 0.95rem;"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Date Section -->
                        <div class="d-flex align-items-center text-muted">
                            <i class="fas fa-clock me-2"></i>
                            <small id="reviewDate"></small>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                // Currency settings
                const currencySymbol = '{{ currency_symbol() }}';

                document.addEventListener('DOMContentLoaded', function () {
                    // Revenue Trend Chart
                    const revenueCtx = document.getElementById('revenueTrendChart').getContext('2d');
                    new Chart(revenueCtx, {
                        type: 'bar',
                        data: {
                            labels: @json($monthlyLabels ?? []),
                            datasets: [
                                {
                                    label: 'Booking Revenue',
                                    data: @json($monthlyBookingAmount ?? []),
                                    backgroundColor: '#727cf5',
                                    borderColor: '#727cf5',
                                    borderWidth: 1,
                                    stack: 'combined'
                                },
                                {
                                    label: 'Tips',
                                    data: @json($monthlyTips ?? []),
                                    backgroundColor: '#0acf97',
                                    borderColor: '#0acf97',
                                    borderWidth: 1,
                                    stack: 'combined'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    callbacks: {
                                        label: function (context) {
                                            return context.dataset.label + ': ' + currencySymbol + context.raw.toLocaleString();
                                        },
                                        footer: function (tooltipItems) {
                                            let sum = 0;
                                            tooltipItems.forEach(function (tooltipItem) {
                                                sum += tooltipItem.raw;
                                            });
                                            return 'Total: ' + currencySymbol + sum.toLocaleString();
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    stacked: true,
                                },
                                y: {
                                    stacked: true,
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function (value) {
                                            return currencySymbol + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Service Distribution Chart
                    const serviceCtx = document.getElementById('serviceDistributionChart').getContext('2d');

                    // Prepare service data for the chart
                    const serviceData = @json($bookingsByService ?? []);
                    const serviceLabels = serviceData.map(item => item.service ? item.service.name : `Service #${item.service_id}`);
                    const serviceCounts = serviceData.map(item => item.count);

                    new Chart(serviceCtx, {
                        type: 'doughnut',
                        data: {
                            labels: serviceLabels,
                            datasets: [{
                                data: serviceCounts,
                                backgroundColor: [
                                    '#727cf5', '#6c757d', '#0acf97', '#fa5c7c', '#ffbc00',
                                    '#39afd1', '#e3eaef', '#313a46', '#f1f5f7', '#6b5fb5'
                                ],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: {
                                    position: 'right',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            const label = context.label || '';
                                            const value = context.raw || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = Math.round((value / total) * 100);
                                            return `${label}: ${value} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
        @endpush

        @push('scripts')
            <!-- Chart.js -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

            {{-- Remove duplicate script --}}
            <script>
                // Employee Details Modal Handler
                document.addEventListener('DOMContentLoaded', function () {
                    // Initialize the modal
                    const employeeModal = new bootstrap.Modal(document.getElementById('employeeDetailsModal'));

                    // Handle view employee details button click
                    document.querySelectorAll('.view-employee-details').forEach(button => {
                        button.addEventListener('click', function () {
                            // Prevent default action
                            event.preventDefault();

                            // Get employee data from data attributes
                            const employeeId = this.getAttribute('data-employee-id');
                            const employeeName = this.getAttribute('data-employee-name');
                            const employeePosition = this.getAttribute('data-employee-position');
                            const employeeAvatar = this.getAttribute('data-employee-avatar');
                            const employeeBookings = this.getAttribute('data-employee-bookings');
                            const employeeEarnings = parseFloat(this.getAttribute('data-employee-earnings')) || 0;
                            const employeeRating = parseFloat(this.getAttribute('data-employee-rating')) || 0;

                            // Update modal content
                            document.getElementById('employeeName').textContent = employeeName;
                            document.getElementById('employeePosition').textContent = employeePosition;
                            document.getElementById('employeeAvatar').src = employeeAvatar;
                            document.getElementById('totalBookings').textContent = employeeBookings;
                            document.getElementById('totalEarnings').textContent = '$' + employeeEarnings.toFixed(2);
                            document.getElementById('completedBookings').textContent = employeeBookings; // Same as total for now

                            // Update rating stars
                            updateRatingStars('employeeRating', employeeRating);
                            updateRatingStars('avgRating', employeeRating);

                            // Update view all bookings link with salon slug
                            const viewAllLink = document.getElementById('viewAllBookings');
                            const pathParts = window.location.pathname.split('/');
                            const salonSlug = pathParts[1]; // Get salon slug from URL
                            viewAllLink.href = `/${salonSlug}/admin/employees/${employeeId}/bookings`;
                            viewAllLink.setAttribute('data-employee-id', employeeId);

                            // Show the modal
                            employeeModal.show();

                            // Load employee-specific bookings
                            setTimeout(() => {
                                loadEmployeeBookings();
                            }, 500);
                        });
                    });

                    // Function to update rating stars
                    function updateRatingStars(containerId, rating) {
                        const container = document.getElementById(containerId);
                        if (!container) return;

                        container.innerHTML = '';
                        const fullStars = Math.floor(rating);
                        const hasHalfStar = rating % 1 >= 0.5;

                        // Create stars
                        for (let i = 1; i <= 5; i++) {
                            const star = document.createElement('i');
                            star.className = i <= fullStars ? 'fas fa-star text-warning' :
                                (i === fullStars + 1 && hasHalfStar ? 'fas fa-star-half-alt text-warning' : 'far fa-star text-light');
                            if (containerId === 'employeeRating') {
                                star.style.fontSize = '1.2rem';
                            }
                            container.appendChild(star);
                        }

                        // Add rating text
                        const ratingText = document.createElement('span');
                        ratingText.className = 'ms-1';
                        ratingText.textContent = `(${rating.toFixed(1)})`;
                        container.appendChild(ratingText);
                    }

                    // Function to load recent bookings from the database
                    function loadEmployeeBookings() {
                        const tbody = document.getElementById('recentBookings');
                        if (!tbody) return;

                        // Get employee ID from the modal's view all bookings link
                        const viewAllLink = document.getElementById('viewAllBookings');
                        const employeeId = viewAllLink ? viewAllLink.getAttribute('data-employee-id') : null;

                        if (!employeeId) {
                            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Employee ID not found</td></tr>';
                            return;
                        }

                        // Show loading state
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center"><div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div> Loading bookings...</td></tr>';

                        // Get the salon slug from the current URL
                        const pathParts = window.location.pathname.split('/');
                        const salonSlug = pathParts[1]; // Assuming URL is /{salon_slug}/admin/...

                        // Fetch employee-specific bookings from the server
                        fetch(`/${salonSlug}/admin/bookings/employee/${employeeId}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(bookings => {
                                if (!Array.isArray(bookings) || bookings.length === 0) {
                                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">No recent bookings found</td></tr>';
                                    return;
                                }

                                // Generate HTML for each booking
                                const html = bookings.map((booking, index) => {
                                    let ratingStars = '';
                                    const rating = parseFloat(booking.rating) || 0;
                                    const fullStars = Math.floor(rating);
                                    const hasHalfStar = rating % 1 >= 0.5;

                                    // Generate star rating HTML
                                    for (let i = 1; i <= 5; i++) {
                                        if (i <= fullStars) {
                                            ratingStars += '<i class="fas fa-star text-warning small"></i>';
                                        } else if (i === fullStars + 1 && hasHalfStar) {
                                            ratingStars += '<i class="fas fa-star-half-alt text-warning small"></i>';
                                        } else {
                                            ratingStars += '<i class="far fa-star text-light small"></i>';
                                        }
                                    }

                                    return `
                                                                                                                                                <tr>
                                                                                                                                                    <td>${index + 1}</td>
                                                                                                                                                    <td>${booking.date || 'N/A'}</td>
                                                                                                                                                     <td>${booking.service || 'N/A'}</td>
                                                                                                                                                     <td><span class="badge bg-success">${booking.status || 'N/A'}</span></td>
                                                                                                                                                     <td>${booking.amount ? `${currencySymbol}${booking.amount}` : 'N/A'}</td>
                                                                                                                                                     <td>${booking.tip_amount ? `${currencySymbol}${booking.tip_amount}` : 'N/A'}</td>
                                                                                                                                                     <td>${ratingStars}</td>
                                                                                                                                                 </tr>
                                                                                                                                             `;
                                }).join('');

                                tbody.innerHTML = html;
                            })
                            .catch(error => {

                                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading recent bookings</td></tr>';
                            });
                    }
                });
            </script>

            <script>
                // Monthly Trends Chart
                const monthlyCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
                const monthlyChart = new Chart(monthlyCtx, {
                    type: 'line',
                    data: {
                        labels: @json($monthlyLabels),
                        datasets: [
                            {
                                label: 'Bookings',
                                data: @json($monthlyData),
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true,
                                yAxisID: 'y'
                            },
                            {
                                label: 'Revenue',
                                data: @json($monthlyRevenue),
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: false,
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function (context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.datasetIndex === 1) {
                                            label += currencySymbol + context.raw.toLocaleString();
                                        } else {
                                            label += context.raw.toLocaleString();
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Bookings'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                beginAtZero: true,
                                grid: {
                                    drawOnChartArea: false,
                                },
                                title: {
                                    display: true,
                                    text: 'Revenue'
                                },
                                ticks: {
                                    callback: function (value) {
                                        return currencySymbol + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });

                // Status Chart
                const statusCtx = document.getElementById('statusChart').getContext('2d');
                const statusChart = new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($bookingsByStatus->keys()->map(fn($status) => ucfirst($status))->toArray()),
                        datasets: [{
                            data: @json($bookingsByStatus->values()->toArray()),
                            backgroundColor: [
                                '#4e73df',
                                '#1cc88a',
                                '#36b9cc',
                                '#f6c23e',
                                '#e74a3b',
                                '#858796'
                            ],
                            hoverBackgroundColor: [
                                '#2e59d9',
                                '#17a673',
                                '#2c9faf',
                                '#dda20a',
                                '#be2617',
                                '#6c757d'
                            ],
                            hoverBorderColor: "rgba(234, 236, 244, 1)",
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((value / total) * 100);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '70%',
                    }
                });
            </script>
        @endpush

        <style>
            .widget-box-one {
                position: relative;
                overflow: hidden;
                margin-bottom: 20px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
            }

            .widget-box-one:hover {
                transform: translateY(-5px);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }

            .widget-one-icon {
                position: absolute;
                right: 20px;
                top: 20px;
                opacity: 0.2;
                font-size: 60px;
            }

            .card {
                margin-bottom: 20px;
                border: none;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            }

            .card-body {
                padding: 1.5rem;
            }

            .header-title {
                font-size: 1.25rem;
                margin-bottom: 1.5rem;
                color: #2d3748;
                font-weight: 600;
            }

            .table th {
                border-top: none;
                font-weight: 600;
                color: #4a5568;
            }

            .badge {
                font-weight: 500;
                padding: 0.35em 0.65em;
                font-size: 0.75em;
            }

            .chart-container {
                position: relative;
                min-height: 300px;
            }
        </style>

        <!-- Employee Details Modal -->
        <div class="modal fade" id="employeeDetailsModal" tabindex="-1" aria-labelledby="employeeDetailsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="employeeDetailsModalLabel">Employee Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-4">
                            <div class="col-md-4 text-center">
                                <img id="employeeAvatar" src="https://ui-avatars.com/api/?name=Employee&background=random"
                                    class="rounded-circle avatar-xl mb-2" alt="Employee Avatar"
                                    onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Employee&background=random';">
                                <h4 id="employeeName" class="mb-1"></h4>
                                <p id="employeePosition" class="text-muted"></p>
                                <div id="employeeRating" class="mb-2"></div>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <h6 class="text-muted mb-2">Total Bookings</h6>
                                                <h3 id="totalBookings" class="mb-0">0</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <h6 class="text-muted mb-2">Total Tips (Earnings)</h6>
                                                <h3 id="totalEarnings" class="mb-0">{{ currency_symbol() }}0.00</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <h6 class="text-muted mb-2">Completed Bookings</h6>
                                                <h3 id="completedBookings" class="mb-0">0</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <h6 class="text-muted mb-2">Avg. Rating</h6>
                                                <div id="avgRating" class="d-flex justify-content-center"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Bookings</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-centered table-nowrap mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Service</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                                <th>Tip</th>
                                                <th>Rating</th>
                                            </tr>
                                        </thead>
                                        <tbody id="recentBookings">
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <p class="mt-2 mb-0">Loading recent bookings...</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <a href="#" id="viewAllBookings" class="btn btn-primary">
                            <i class="fas fa-calendar-alt me-2"></i>View All Bookings
                        </a>
                    </div>
                </div>
            </div>
        </div>


        <!-- Service Details Modal -->
        <div class="modal fade" id="serviceDetailsModal" tabindex="-1" aria-labelledby="serviceDetailsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-0 pb-4 px-4">
                        <!-- Service Header -->
                        <div class="text-center mb-4">
                            <div class="avatar-lg mx-auto mb-3 bg-soft-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                style="width: 80px; height: 80px;">
                                <i class="fas fa-spa fa-2x text-primary"></i>
                            </div>
                            <h4 id="serviceName" class="mb-1 fw-bold text-dark"></h4>
                            <span id="serviceCategory"
                                class="badge bg-light text-secondary mb-2 px-3 py-2 rounded-pill"></span>
                            <div id="serviceRating" class="mt-2"></div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="row g-3">
                            <!-- Total Bookings -->
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-3 text-center h-100 border border-light">
                                    <i class="fas fa-calendar-check text-primary mb-2 fs-5"></i>
                                    <h6 class="text-muted text-uppercase small fw-bold mb-1">Bookings</h6>
                                    <h4 id="serviceBookings" class="mb-0 fw-bold text-dark">0</h4>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-3 text-center h-100 border border-light">
                                    <i class="fas fa-tag text-white mb-2 fs-5"></i>
                                    <h6 class="text-muted text-uppercase small fw-bold mb-1">Price</h6>
                                    <h4 id="servicePrice" class="mb-0 fw-bold text-dark">{{ currency_symbol() }}0.00</h4>
                                </div>
                            </div>

                            <!-- Revenue -->
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-3 text-center h-100 border border-light">
                                    <i class="fas fa-coins text-warning mb-2 fs-5"></i>
                                    <h6 class="text-muted text-uppercase small fw-bold mb-1">Revenue</h6>
                                    <h4 id="serviceRevenue" class="mb-0 fw-bold text-dark">{{ currency_symbol() }}0.00</h4>
                                </div>
                            </div>

                            <!-- Avg Rating -->
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-3 text-center h-100 border border-light">
                                    <i class="fas fa-star text-warning mb-2 fs-5"></i>
                                    <h6 class="text-muted text-uppercase small fw-bold mb-1">Rating</h6>
                                    <div id="serviceAvgRating" class="d-flex justify-content-center fw-bold text-dark">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Button -->

                    </div>
                </div>
            </div>
        </div>

@endsection
    @push('components')
        <x-modals.view-customer />
    @endpush

    @push('scripts')
        <script>
            // Wait for the document to be fully loaded
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize modal variable
                let employeeModal = null;

                // Handle view employee details button click
                document.querySelectorAll('.view-employee-details').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();

                        // Initialize modal if not already done
                        if (!employeeModal) {
                            const modalElement = document.getElementById('employeeDetailsModal');
                            if (!modalElement) return;

                            employeeModal = new bootstrap.Modal(modalElement);

                            // Add event listener for when the modal is shown
                            modalElement.addEventListener('shown.bs.modal', function () {
                                // Get employee data from the button that was clicked
                                const button = document.querySelector('.view-employee-details[aria-expanded="true"]');
                                if (!button) return;

                                const employeeId = button.getAttribute('data-employee-id');
                                const employeeName = button.getAttribute('data-employee-name') || 'Employee';
                                const employeePosition = button.getAttribute('data-employee-position') || 'Employee';
                                const employeeAvatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(employeeName)}&background=random`;
                                const employeeBookings = button.getAttribute('data-employee-bookings') || '0';
                                const employeeEarnings = parseFloat(button.getAttribute('data-employee-earnings') || '0');
                                const employeeRating = parseFloat(button.getAttribute('data-employee-rating') || '0');

                                // Update modal content
                                document.getElementById('employeeName').textContent = employeeName;
                                document.getElementById('employeePosition').textContent = employeePosition;

                                // Set avatar with fallback
                                const avatarImg = document.getElementById('employeeAvatar');
                                if (avatarImg) {
                                    avatarImg.src = employeeAvatar;
                                    avatarImg.onerror = function () {
                                        this.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(employeeName || 'E')}&background=random`;
                                    };
                                }

                                document.getElementById('totalBookings').textContent = employeeBookings;
                                document.getElementById('totalEarnings').textContent = currencySymbol + employeeEarnings.toFixed(2);
                                document.getElementById('completedBookings').textContent = employeeBookings;

                                // Update rating stars
                                updateRatingStars('employeeRating', employeeRating);
                                updateRatingStars('avgRating', employeeRating);


                                // Update view all bookings link with salon slug
                                const viewAllLink = document.getElementById('viewAllBookings');
                                if (viewAllLink) {
                                    const pathParts = window.location.pathname.split('/');
                                    const salonSlug = pathParts[1];
                                    viewAllLink.href = `/${salonSlug}/admin/employees/${employeeId}/bookings`;
                                    viewAllLink.setAttribute('data-employee-id', employeeId);
                                }

                                // Load employee-specific bookings
                                setTimeout(loadEmployeeBookings, 100);
                            });
                        }

                        // Show the modal
                        if (employeeModal) {
                            employeeModal.show();
                        }
                    });
                });

                // Handle view service details button click
                document.querySelectorAll('.view-service-details').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();

                        const serviceId = this.getAttribute('data-service-id');
                        const serviceName = this.getAttribute('data-service-name');
                        const serviceCategory = this.getAttribute('data-service-category');
                        const servicePrice = parseFloat(this.getAttribute('data-service-price')) || 0;
                        const serviceBookings = parseInt(this.getAttribute('data-service-bookings')) || 0;
                        const serviceRating = parseFloat(this.getAttribute('data-service-rating')) || 0;

                        // Calculate estimated revenue
                        const serviceRevenue = servicePrice * serviceBookings;

                        // Update modal content
                        document.getElementById('serviceName').textContent = serviceName;
                        document.getElementById('serviceCategory').textContent = serviceCategory;
                        document.getElementById('serviceBookings').textContent = serviceBookings;
                        document.getElementById('servicePrice').textContent = '$' + servicePrice.toFixed(2);
                        document.getElementById('serviceRevenue').textContent = '$' + serviceRevenue.toFixed(2);

                        // Update rating stars
                        updateRatingStars('serviceRating', serviceRating);
                        updateRatingStars('serviceAvgRating', serviceRating);


                    });
                });

                // Function to update rating stars
                function updateRatingStars(containerId, rating) {
                    const container = document.getElementById(containerId);
                    if (!container) return;

                    container.innerHTML = '';
                    const fullStars = Math.floor(rating);
                    const hasHalfStar = rating % 1 >= 0.5;

                    // Create stars
                    for (let i = 1; i <= 5; i++) {
                        const star = document.createElement('i');
                        star.className = i <= fullStars ? 'fas fa-star text-warning' :
                            (i === fullStars + 1 && hasHalfStar ? 'fas fa-star-half-alt text-warning' : 'far fa-star text-light');
                        if (containerId === 'employeeRating') {
                            star.style.fontSize = '1.2rem';
                        }
                        container.appendChild(star);
                    }

                    // Add rating text
                    const ratingText = document.createElement('span');
                    ratingText.className = 'ms-1';
                    ratingText.textContent = `(${rating.toFixed(1)})`;
                    container.appendChild(ratingText);
                }

                // Handle view review button clicks
                document.querySelectorAll('.view-review').forEach(button => {
                    button.addEventListener('click', function () {
                        const reviewId = this.getAttribute('data-review-id');

                        // Get review data from the table row
                        const row = this.closest('tr');
                        const customerName = row.querySelector('td:nth-child(1) div').textContent.trim();
                        const serviceName = row.querySelector('td:nth-child(2)').textContent.trim();
                        const ratingText = row.querySelector('td:nth-child(3) span').textContent.trim();
                        const rating = parseFloat(ratingText.replace(/[()]/g, ''));
                        const comment = row.querySelector('td:nth-child(4)').getAttribute('title') || 'No comment';
                        const date = row.querySelector('td:nth-child(5)').textContent.trim();

                        // Populate modal
                        document.getElementById('reviewCustomerName').textContent = customerName;
                        document.getElementById('reviewServiceName').textContent = serviceName;
                        document.getElementById('reviewComment').textContent = comment;
                        document.getElementById('reviewDate').textContent = date;

                        // Create rating stars
                        const starsContainer = document.getElementById('reviewRatingStars');
                        starsContainer.innerHTML = '';

                        const fullStars = Math.floor(rating);
                        const hasHalfStar = (rating % 1) >= 0.5;

                        for (let i = 1; i <= 5; i++) {
                            const star = document.createElement('i');
                            if (i <= fullStars) {
                                star.className = 'fas fa-star text-warning';
                            } else if (i === fullStars + 1 && hasHalfStar) {
                                star.className = 'fas fa-star-half-alt text-warning';
                            } else {
                                star.className = 'far fa-star text-light';
                            }
                            starsContainer.appendChild(star);
                        }

                        // Add rating number
                        const ratingSpan = document.createElement('span');
                        ratingSpan.className = 'ms-2';
                        ratingSpan.textContent = `${rating.toFixed(1)}/5.0`;
                        starsContainer.appendChild(ratingSpan);

                        // Show modal
                        const modal = new bootstrap.Modal(document.getElementById('reviewDetailsModal'));
                        modal.show();
                    });
                });

            });
        </script>
    @endpush