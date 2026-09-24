@extends('layouts.app')

@push('styles')
    <style>
        /* Modern Dashboard Styles */
        .dashboard-container {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        /* Modern Card Styles */
        .card-modern {
            border-radius: 16px;
            border: none;
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .card-modern:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Gradient Cards */
        .gradient-card-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .gradient-card-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }

        .gradient-card-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .gradient-card-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        /* Stats Cards */
        .stat-card {
            border-radius: 16px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        /* Modern Tabs */
        .nav-tabs-modern {
            border-bottom: 2px solid #e5e7eb;
            background: white;
            border-radius: 12px 12px 0 0;
            padding: 0.5rem 1rem 0;
        }

        .nav-tabs-modern .nav-link {
            border: none;
            color: #6b7280;
            font-weight: 600;
            padding: 1rem 1.5rem;
            position: relative;
            transition: all 0.3s ease;
            border-radius: 8px 8px 0 0;
        }

        .nav-tabs-modern .nav-link:hover {
            color: #4f46e5;
            background: rgba(79, 70, 229, 0.05);
        }

        .nav-tabs-modern .nav-link.active {
            color: #4f46e5;
            background: transparent;
        }

        .nav-tabs-modern .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 3px 3px 0 0;
        }

        /* Widget Headers */
        .widget-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.25rem 1.5rem;
            border-radius: 12px 12px 0 0;
            font-weight: 600;
        }

        .widget-header-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .widget-header-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .widget-header-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .widget-header-danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        /* List Items */
        .list-item-modern {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
            background: #f9fafb;
            border-left: 3px solid transparent;
        }

        .list-item-modern:hover {
            background: #f3f4f6;
            border-left-color: #4f46e5;
            transform: translateX(4px);
        }

        /* Badges Modern */
        .badge-modern {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        /* Progress Bars */
        .progress-modern {
            height: 8px;
            border-radius: 10px;
            background: #e5e7eb;
            overflow: hidden;
        }

        .progress-modern .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 16px;
            padding: 1.5rem;
        }

        /* Spacing */
        .section-gap {
            margin-bottom: 2rem;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.6s ease-out;
        }

        .x-small {
            font-size: 0.7rem !important;
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3.5rem 2rem;
            text-align: center;
        }

        .empty-state i {
            font-size: 3.5rem;
            margin-bottom: 1.25rem;
            color: #cbd5e1;
            opacity: 0.8;
            display: block;
        }

        .empty-state p {
            color: #64748b;
            font-size: 1.05rem;
            font-weight: 500;
            margin: 0;
        }

        /* Multi-Staff Avatars */
        .staff-avatars {
            display: flex;
            align-items: center;
        }

        .staff-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 600;
            border: 2px solid white;
            margin-left: -8px;
            position: relative;
            transition: all 0.2s ease;
        }

        .staff-avatar:first-child {
            margin-left: 0;
        }

        .staff-avatar:hover {
            transform: translateY(-2px) scale(1.1);
            z-index: 100 !important;
        }

        .staff-avatar-more {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #e5e7eb;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 700;
            border: 2px solid white;
            margin-left: -8px;
        }

        /* Tooltip Styling */
        .tooltip-inner {
            max-width: 300px;
            text-align: left;
            padding: 0.75rem;
        }

        .tooltip-service-item {
            padding: 0.25rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .tooltip-service-item:last-child {
            border-bottom: none;
        }

        .group-booking-badge {
            cursor: help;
        }

        /* Modern Filter Bar Styles */
        .filter-wrapper {
            position: sticky;
            top: 1rem;
            z-index: 1000;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }

        .filter-glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px) saturate(180%);
            -webkit-backdrop-filter: blur(12px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 100px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
            padding: 0.6rem 1.2rem;
        }

        .period-pill-group {
            display: flex;
            gap: 0.5rem;
            background: rgba(0, 0, 0, 0.04);
            padding: 0.3rem;
            border-radius: 50px;
        }

        .period-pill {
            border: none;
            background: transparent;
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
        }

        .period-pill:hover:not(.active) {
            background: rgba(0, 0, 0, 0.05);
            color: #1e293b;
        }

        .period-pill.active {
            background: #fff;
            color: #4f46e5;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
        }

        .date-range-display {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.5rem 1rem;
            background: rgba(79, 70, 229, 0.06);
            border-radius: 50px;
            color: #4f46e5;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid rgba(79, 70, 229, 0.1);
        }

        .custom-range-integrated {
            overflow: hidden;
            max-width: 0;
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
        }

        .custom-range-integrated.show {
            max-width: 600px;
            opacity: 1;
            margin-left: 1rem;
            padding-left: 1rem;
            border-left: 2px solid rgba(79, 70, 229, 0.15);
        }

        .integrated-input-wrapper {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e2e8f0;
            border-radius: 50px;
            padding: 2px 12px;
            transition: all 0.2s ease;
        }

        .integrated-input-wrapper:focus-within {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .integrated-input {
            border: none;
            background: transparent;
            font-size: 0.85rem;
            font-weight: 600;
            color: #1e293b;
            width: 120px;
            padding: 4px;
            outline: none !important;
        }

        .btn-apply-integrated {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            transition: all 0.3s ease;
            margin-left: 0.5rem;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        .btn-apply-integrated:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(79, 70, 229, 0.3);
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
        }

        .btn-refresh {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #4f46e5;
            color: white;
            border: none;
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .btn-refresh:hover {
            transform: rotate(180deg);
            background: #4338ca;
        }

        .modern-input {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .modern-input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        @media (max-width: 991px) {
            .filter-glass-card {
                border-radius: 20px;
                padding: 1rem;
            }

            .period-pill-group {
                flex-wrap: wrap;
                background: transparent;
                padding: 0;
            }

            .period-pill {
                background: rgba(0, 0, 0, 0.04);
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Modern Date Range Filter -->
        @can('system.view_dashboard')
            <div class="filter-wrapper animate-fade-in">
                <div class="filter-glass-card">
                    <form action="{{ url()->current() }}" method="GET" id="dateFilterForm"
                        class="d-flex flex-wrap align-items-center justify-content-between g-3">

                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="period-pill-group shadow-inner">
                                <button type="button" class="period-pill {{ $dateRange == 'today' ? 'active' : '' }}"
                                    onclick="setDateRange('today')">Today</button>
                                <button type="button" class="period-pill {{ $dateRange == 'yesterday' ? 'active' : '' }}"
                                    onclick="setDateRange('yesterday')">Yesterday</button>
                                <button type="button" class="period-pill {{ $dateRange == 'last_7_days' ? 'active' : '' }}"
                                    onclick="setDateRange('last_7_days')">7 Days</button>
                                <button type="button" class="period-pill {{ $dateRange == 'this_month' ? 'active' : '' }}"
                                    onclick="setDateRange('this_month')">Month</button>
                                <button type="button" class="period-pill {{ $dateRange == 'custom' ? 'active' : '' }}"
                                    onclick="toggleCustomRange()">
                                    <i class="fas fa-sliders-h me-1"></i> Custom
                                </button>
                                <!-- Integrated Custom Range -->
                                <div id="custom-range-integrated"
                                    class="custom-range-integrated {{ $dateRange == 'custom' ? 'show' : '' }}">
                                    <div class="integrated-input-wrapper">
                                        <i class="far fa-calendar-alt text-muted me-2 small"></i>
                                        <input type="date" name="start_date" id="start_date" class="integrated-input"
                                            value="{{ $startDate->format('Y-m-d') }}">
                                    </div>
                                    <span class="text-muted small mx-2">to</span>
                                    <div class="integrated-input-wrapper">
                                        <i class="far fa-calendar-alt text-muted me-2 small"></i>
                                        <input type="date" name="end_date" id="end_date" class="integrated-input"
                                            value="{{ $endDate->format('Y-m-d') }}">
                                    </div>
                                    <button type="submit" class="btn-apply-integrated">
                                        Apply Filter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="date_range" id="date_range_input" value="{{ $dateRange }}">

                        @if($startDate && $endDate && $dateRange != 'custom')
                            <div class="date-range-display shadow-sm">
                                <i class="fas fa-calendar-day"></i>
                                <span>{{ $startDate->format('M d') }} — {{ $endDate->format('M d, Y') }}</span>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        @endcan
        <!-- Welcome Card -->
        <div class="row section-gap animate-fade-in">
            <div class="col-12">
                <div class="welcome-card card-modern">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random"
                                alt="{{ auth()->user()->name }}" class="rounded-circle border border-3 border-white shadow"
                                style="width: 64px; height: 64px;">
                        </div>
                        <div class="flex-grow-1 ms-4">
                            <h4 class="mb-2 fw-bold">Welcome back, {{ auth()->user()->name }}! 👋</h4>
                            <p class="mb-0 opacity-90">
                                <i class="fas fa-user-tag me-2"></i>
                                {{ ucfirst(auth()->user()->roles->first()->name ?? 'No Role') }}
                                @if(auth()->user()->last_login_at)
                                    <span class="ms-3">
                                        <i class="fas fa-clock me-2"></i>
                                        Last Login: {{ auth()->user()->last_login_at->diffForHumans() }}
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        @can('salon.view')
            @if(isset($salon_quick_stats))
                <div class="row section-gap animate-fade-in">
                    <div class="col-xl col-md-6 mb-3">
                        <div class="stat-card gradient-card-primary card-modern p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-uppercase x-small fw-semibold opacity-90 mb-1">Active Staff</div>
                                    <div class="h5 mb-0 fw-bold">{{ $salon_quick_stats['active_staff'] ?? 0 }}</div>
                                </div>
                                <div class="stat-icon" style="width: 40px; height: 40px; font-size: 18px;">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl col-md-6 mb-3">
                        <div class="stat-card gradient-card-success card-modern p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-uppercase x-small fw-semibold opacity-90 mb-1">
                                        Appointments
                                    </div>
                                    <div class="h5 mb-0 fw-bold">{{ $salon_quick_stats['today_appointments'] ?? 0 }}</div>
                                </div>
                                <div class="stat-icon" style="width: 40px; height: 40px; font-size: 18px;">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl col-md-6 mb-3">
                        <div class="stat-card gradient-card-warning card-modern p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-uppercase x-small fw-semibold opacity-90 mb-1">Pending</div>
                                    <div class="h5 mb-0 fw-bold">{{ $salon_quick_stats['pending_appointments'] ?? 0 }}</div>
                                </div>
                                <div class="stat-icon" style="width: 40px; height: 40px; font-size: 18px;">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl col-md-6 mb-3">
                        <div class="stat-card gradient-card-info card-modern p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-uppercase x-small fw-semibold opacity-90 mb-1">Completed</div>
                                    <div class="h5 mb-0 fw-bold">{{ $salon_quick_stats['completed_today'] ?? 0 }}</div>
                                </div>
                                <div class="stat-icon" style="width: 40px; height: 40px; font-size: 18px;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endcan

        <!-- Today's Revenue & Low Stock Alerts -->
        @can('system.view_dashboard')
            @if(isset($todays_sales) || isset($low_stock_products))
                <div class="row section-gap">
                    <!-- Today's Revenue Widget -->
                    @if(isset($todays_sales) && (auth()->user()->can('reports.sales') || auth()->user()->can('bookings.view_stats')))
                        <div class="col-xl mb-4" style="min-width: 250px;">
                            <div class="card-modern h-100">
                                <div class="widget-header widget-header-success py-2 px-3">
                                    <i class="fas fa-dollar-sign me-2 small"></i><span
                                        class="small fw-bold">{{ $dateRange == 'today' ? "Today's" : "Period" }} Revenue</span>
                                </div>
                                <div class="card-body p-3">
                                    <div class="text-center">
                                        <div class="h2 mb-2 fw-bold text-success">
                                            {{ currency_symbol() }}{{ number_format($todays_sales['today_revenue'], 2) }}
                                        </div>
                                        <div class="mb-2">
                                            @if($todays_sales['revenue_growth'] > 0)
                                                <span class="badge badge-modern bg-success x-small py-1 px-2">
                                                    <i class="fas fa-arrow-up me-1"></i> {{ $todays_sales['revenue_growth'] }}%
                                                </span>
                                            @elseif($todays_sales['revenue_growth'] < 0)
                                                <span class="badge badge-modern bg-danger x-small py-1 px-2">
                                                    <i class="fas fa-arrow-down me-1"></i> {{ abs($todays_sales['revenue_growth']) }}%
                                                </span>
                                            @else
                                                <span class="badge badge-modern bg-secondary x-small py-1 px-2">No Change</span>
                                            @endif
                                        </div>
                                        <div class="x-small text-muted">
                                            vs Prev: {{ currency_symbol() }}{{ number_format($todays_sales['yesterday_revenue'], 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Today's Refunds Widget (NEW) -->
                    @if(isset($todays_sales) && (auth()->user()->can('reports.sales') || auth()->user()->can('bookings.view_stats')))
                        <div class="col-xl mb-4" style="min-width: 250px;">
                            <div class="card-modern h-100">
                                <div class="widget-header widget-header-danger py-2 px-3">
                                    <i class="fas fa-undo me-2 small"></i><span
                                        class="small fw-bold">{{ $dateRange == 'today' ? "Today's" : "Period" }} Refunds</span>
                                </div>
                                <div class="card-body p-3">
                                    <div class="text-center">
                                        <div class="h2 mb-2 fw-bold text-danger">
                                            {{ currency_symbol() }}{{ number_format($todays_sales['today_refunds'] ?? 0, 2) }}
                                        </div>
                                        <div class="mb-2">
                                            <span class="badge badge-modern bg-secondary x-small py-1 px-2">
                                                Count: {{ $todays_sales['today_refunds_count'] ?? 0 }}
                                            </span>
                                            @if(($todays_sales['today_refunds_fees'] ?? 0) > 0)
                                                <span class="badge badge-modern bg-info x-small py-1 px-2 ms-1">
                                                    Fees: {{ currency_symbol() }}{{ number_format($todays_sales['today_refunds_fees'], 2) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="x-small text-muted">
                                            Net Outflow:
                                            {{ currency_symbol() }}{{ number_format($todays_sales['today_net_refunds'] ?? 0, 2) }}
                                        </div>
                                        <div class="x-small text-muted mt-1">
                                            vs Prev:
                                            {{ currency_symbol() }}{{ number_format($todays_sales['yesterday_refunds'] ?? 0, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Payment Methods Widget -->
                    <div class="col-lg-6 mb-4">
                        <div class="card-modern h-100 payment-methods-widget">
                            <div class="widget-header widget-header-info d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-credit-card me-2"></i>Payment Methods
                                </div>
                                @if(isset($payment_methods) && count($payment_methods) > 0)
                                    <span class="badge bg-white text-info fw-bold">
                                        Total: {{ currency_symbol() }}{{ number_format($total_bill_value ?? 0, 2) }}
                                    </span>
                                @endif
                            </div>
                            <div class="card-body p-4">
                                @if(isset($payment_methods) && count($payment_methods) > 0)
                                    <div class="row g-4">
                                        <!-- Chart Section -->
                                        <div class="col-md-6 d-flex align-items-center justify-content-center">
                                            <div class="payment-chart-container"
                                                style="position: relative; height: 280px; width: 280px;">
                                                <canvas id="paymentMethodsChart"></canvas>
                                            </div>
                                        </div>

                                        <!-- Payment Method List -->
                                        <div class="col-md-6">
                                            <div class="payment-methods-list h-100 d-flex flex-column justify-content-center">
                                                @foreach($payment_methods as $method)
                                                    @php
                                                        $percent = $total_bill_value > 0 ? ($method['value'] / $total_bill_value) * 100 : 0;
                                                        $colors = [
                                                            'Cash' => ['text' => 'primary', 'bg' => 'primary', 'icon' => 'fa-money-bill-wave'],
                                                            'Card' => ['text' => 'success', 'bg' => 'success', 'icon' => 'fa-credit-card'],
                                                            'Online' => ['text' => 'info', 'bg' => 'info', 'icon' => 'fa-globe'],
                                                            'Other' => ['text' => 'warning', 'bg' => 'warning', 'icon' => 'fa-wallet'],
                                                            'Unpaid' => ['text' => 'danger', 'bg' => 'danger', 'icon' => 'fa-exclamation-circle']
                                                        ];
                                                        $colorData = $colors[$method['name']] ?? ['text' => 'secondary', 'bg' => 'secondary', 'icon' => 'fa-coins'];
                                                    @endphp
                                                    <div class="payment-method-item mb-3">
                                                        <div class="d-flex align-items-center">
                                                            <div
                                                                class="payment-icon-wrapper bg-{{ $colorData['bg'] }} bg-opacity-10 text-{{ $colorData['text'] }} me-3">
                                                                <i class="fas {{ $colorData['icon'] }}"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <span class="fw-bold text-dark">{{ $method['name'] }}</span>
                                                                    <div class="text-end">
                                                                        <span
                                                                            class="fw-bold text-dark">{{ currency_symbol() }}{{ number_format($method['value'], 2) }}</span>
                                                                        <small
                                                                            class="text-muted ms-1">({{ number_format($percent, 1) }}%)</small>
                                                                    </div>
                                                                </div>
                                                                <div class="progress"
                                                                    style="height: 6px; border-radius: 10px; background-color: rgba(0,0,0,0.05);">
                                                                    <div class="progress-bar bg-{{ $colorData['text'] }}" role="progressbar"
                                                                        style="width: {{ $percent }}%; border-radius: 10px;"
                                                                        aria-valuenow="{{ $percent }}" aria-valuemin="0"
                                                                        aria-valuemax="100"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <i class="fas fa-credit-card text-muted"></i>
                                        <p class="mb-0">No payment data available</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <style>
                        /* Revised Payment Methods Styles */
                        .payment-methods-widget {
                            overflow: hidden;
                        }

                        .payment-chart-container {
                            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.08));
                        }

                        .payment-method-item {
                            padding: 0.5rem 0.75rem;
                            border-radius: 12px;
                            transition: background-color 0.2s ease;
                        }

                        .payment-method-item:hover {
                            background-color: #f8f9fa;
                        }

                        .payment-icon-wrapper {
                            width: 48px;
                            height: 48px;
                            border-radius: 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 1.25rem;
                            transition: transform 0.2s ease;
                        }

                        .payment-method-item:hover .payment-icon-wrapper {
                            transform: scale(1.1);
                        }

                        @media (max-width: 768px) {
                            .payment-chart-container {
                                height: 220px !important;
                                width: 220px !important;
                            }
                        }
                    </style>
                </div>
            @endif
        @endcan

        <!-- Analytics Tabs -->
        @can('system.view_dashboard')
            @if(isset($bill_count) || isset($total_bill_value) || isset($average_bill_value))
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-0 pt-3 px-3 pb-0">
                                @php
                                    // Check if modules are enabled AND user has permission to view them
                                    $showSales = \App\Helpers\ModuleHelper::reportsEnabled() && (auth()->user()->can('reports.sales') || auth()->user()->can('pos.view_reports'));
                                    $showStaff = \App\Helpers\ModuleHelper::staffEnabled() && (auth()->user()->can('reports.staff') || auth()->user()->can('staff.view_performance'));
                                    $showCustomer = \App\Helpers\ModuleHelper::customersEnabled() && (auth()->user()->can('reports.customers') || auth()->user()->can('customers.view'));

                                    $activeTab = '';
                                    if ($showSales) {
                                        $activeTab = 'sales';
                                    } elseif ($showStaff) {
                                        $activeTab = 'staff';
                                    } elseif ($showCustomer) {
                                        $activeTab = 'customer';
                                    }
                                @endphp
                                <ul class="nav nav-tabs-modern" id="analyticsTabs" role="tablist">
                                    @if($showSales)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ $activeTab == 'sales' ? 'active' : '' }}" id="sales-tab"
                                                data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab">
                                                <i class="fas fa-chart-line me-2"></i>Sales Insight
                                            </button>
                                        </li>
                                    @endif
                                    @if($showStaff)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ $activeTab == 'staff' ? 'active' : '' }}" id="staff-tab"
                                                data-bs-toggle="tab" data-bs-target="#staff" type="button" role="tab">
                                                <i class="fas fa-users me-2"></i>Staff Insight
                                            </button>
                                        </li>
                                    @endif
                                    @if($showCustomer)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ $activeTab == 'customer' ? 'active' : '' }}" id="customer-tab"
                                                data-bs-toggle="tab" data-bs-target="#customer" type="button" role="tab">
                                                <i class="fas fa-user-friends me-2"></i>Customer Insight
                                            </button>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="analyticsTabsContent">

                                    <!-- Sales Insight Tab -->
                                    @if($showSales)
                                        <div class="tab-pane fade {{ $activeTab == 'sales' ? 'show active' : '' }}" id="sales"
                                            role="tabpanel">
                                            <div class="d-flex justify-content-end mb-3">
                                                <a href="{{ route('admin.reports.sales', ['salon_slug' => $salon_slug ?? (request()->route('salon_slug') ?? ($current_salon->slug ?? ''))]) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-external-link-alt me-1"></i> Full Report
                                                </a>
                                            </div>

                                            <!-- Today's Sales Comparison Widget -->
                                            @if(isset($todays_sales))
                                                <div class="card-modern mb-4">
                                                    <div class="widget-header">
                                                        <i
                                                            class="fas fa-calendar-day me-2"></i>{{ $dateRange == 'today' ? "Today's" : "Period" }}
                                                        Sales Performance
                                                    </div>
                                                    <div class="card-body p-4">
                                                        <div class="row g-4">
                                                            <div class="col-md-4">
                                                                <div class="text-center p-4 rounded"
                                                                    style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);">
                                                                    <div class="text-muted small mb-2 text-uppercase fw-semibold">
                                                                        {{ $dateRange == 'today' ? "Today's" : "Selected" }}
                                                                        Revenue
                                                                    </div>
                                                                    <div class="h3 mb-2 fw-bold text-primary">
                                                                        {{ currency_symbol() }}{{ number_format($todays_sales['today_revenue'], 2) }}
                                                                    </div>
                                                                    <div class="small">
                                                                        @if($todays_sales['revenue_growth'] > 0)
                                                                            <span class="badge badge-modern bg-success">
                                                                                <i class="fas fa-arrow-up me-1"></i>
                                                                                {{ $todays_sales['revenue_growth'] }}%
                                                                            </span>
                                                                        @elseif($todays_sales['revenue_growth'] < 0)
                                                                            <span class="badge badge-modern bg-danger">
                                                                                <i class="fas fa-arrow-down me-1"></i>
                                                                                {{ abs($todays_sales['revenue_growth']) }}%
                                                                            </span>
                                                                        @else
                                                                            <span class="badge badge-modern bg-secondary">No Change</span>
                                                                        @endif
                                                                        <span class="text-muted ms-1">vs
                                                                            {{ $dateRange == 'today' ? 'Yesterday' : 'Prev. Period' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="text-center p-4 rounded"
                                                                    style="background: linear-gradient(135deg, #11998e15 0%, #38ef7d15 100%);">
                                                                    <div class="text-muted small mb-2 text-uppercase fw-semibold">
                                                                        Transactions</div>
                                                                    <div class="h3 mb-2 fw-bold text-success">
                                                                        {{ $todays_sales['today_count'] }}
                                                                    </div>
                                                                    <div class="small">
                                                                        @if($todays_sales['count_growth'] > 0)
                                                                            <span class="badge badge-modern bg-success">
                                                                                <i class="fas fa-arrow-up me-1"></i>
                                                                                {{ $todays_sales['count_growth'] }}%
                                                                            </span>
                                                                        @elseif($todays_sales['count_growth'] < 0)
                                                                            <span class="badge badge-modern bg-danger">
                                                                                <i class="fas fa-arrow-down me-1"></i>
                                                                                {{ abs($todays_sales['count_growth']) }}%
                                                                            </span>
                                                                        @else
                                                                            <span class="badge badge-modern bg-secondary">No Change</span>
                                                                        @endif
                                                                        <span class="text-muted ms-1">vs
                                                                            {{ $dateRange == 'today' ? 'Yesterday' : 'Prev. Period' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="text-center p-4 rounded"
                                                                    style="background: linear-gradient(135deg, #4facfe15 0%, #00f2fe15 100%);">
                                                                    <div class="text-muted small mb-2 text-uppercase fw-semibold">Peak Hour
                                                                    </div>
                                                                    <div class="h3 mb-2 fw-bold text-info">
                                                                        {{ $todays_sales['peak_hour'] }}
                                                                    </div>
                                                                    <div class="small text-muted">Most sales activity</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Sales Summary Cards -->
                                            <!-- Sales Summary Cards -->
                                            <div class="row g-4 mb-4">
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="card-modern h-100"
                                                        style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="text-uppercase small fw-bold text-primary mb-1">Bill
                                                                        Count</div>
                                                                    <div class="h3 mb-0 fw-bold text-dark">{{ $bill_count ?? 0 }}</div>
                                                                </div>
                                                                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                                                    <i class="fas fa-receipt fa-lg"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="card-modern h-100"
                                                        style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="text-uppercase small fw-bold text-success mb-1">Net
                                                                        Revenue</div>
                                                                    <div class="h3 mb-0 fw-bold text-dark">
                                                                        {{ currency_symbol() }}{{ number_format($total_bill_value ?? 0, 2) }}
                                                                    </div>
                                                                </div>
                                                                <div class="stat-icon bg-success bg-opacity-10 text-success">
                                                                    <i class="fas fa-dollar-sign fa-lg"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="card-modern h-100"
                                                        style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="text-uppercase small fw-bold text-info mb-1">Avg. Bill
                                                                        Value</div>
                                                                    <div class="h3 mb-0 fw-bold text-dark">
                                                                        {{ currency_symbol() }}{{ number_format($average_bill_value ?? 0, 2) }}
                                                                    </div>
                                                                </div>
                                                                <div class="stat-icon bg-info bg-opacity-10 text-info">
                                                                    <i class="fas fa-chart-bar fa-lg"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="card-modern h-100"
                                                        style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="text-uppercase small fw-bold text-warning mb-1">Unpaid
                                                                        Value</div>
                                                                    <div class="h3 mb-0 fw-bold text-dark">
                                                                        {{ currency_symbol() }}{{ number_format($unpaid_value ?? 0, 2) }}
                                                                    </div>
                                                                </div>
                                                                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                                                                    <i class="fas fa-exclamation-circle fa-lg"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if(($refund_gross ?? 0) > 0)
                                                <div class="row g-4 mb-4">
                                                    <div class="col-xl-4 col-md-6">
                                                        <div class="card-modern h-100" style="border-left: 4px solid #dc3545;">
                                                            <div class="card-body p-4">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <div class="text-uppercase small fw-bold text-danger mb-1">Gross
                                                                            Refunded</div>
                                                                        <div class="h4 mb-0 fw-bold text-dark">
                                                                            {{ currency_symbol() }}{{ number_format($refund_gross ?? 0, 2) }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                                                                        <i class="fas fa-undo-alt fa-lg"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-6">
                                                        <div class="card-modern h-100" style="border-left: 4px solid #17a2b8;">
                                                            <div class="card-body p-4">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <div class="text-uppercase small fw-bold text-info mb-1">Refund Fees
                                                                        </div>
                                                                        <div class="h4 mb-0 fw-bold text-dark">
                                                                            {{ currency_symbol() }}{{ number_format($refund_fees ?? 0, 2) }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                                                                        <i class="fas fa-percentage fa-lg"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-6">
                                                        <div class="card-modern h-100" style="border-left: 4px solid #6f42c1;">
                                                            <div class="card-body p-4">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <div class="text-uppercase small fw-bold text-purple mb-1"
                                                                            style="color: #6f42c1;">Net Refunded Outflow</div>
                                                                        <div class="h4 mb-0 fw-bold text-dark">
                                                                            {{ currency_symbol() }}{{ number_format($refund_net ?? 0, 2) }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="stat-icon bg-purple bg-opacity-10 text-purple"
                                                                        style="color: #6f42c1; background-color: rgba(111, 66, 193, 0.1);">
                                                                        <i class="fas fa-file-invoice-dollar fa-lg"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif


                                            <!-- Revenue Trend Chart -->
                                            <div class="mb-4">
                                                <div class="card-modern">
                                                    <div class="widget-header widget-header-primary">
                                                        <i class="fas fa-chart-line me-2"></i>Revenue Trend (Last 6 Months)
                                                    </div>
                                                    <div class="card-body p-4">
                                                        <div class="chart-area" style="height: 350px;">
                                                            <canvas id="revenueTrendChart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Charts Row -->
                                            <div class="row g-4 mb-4">
                                                <!-- Payment Methods Chart (Moved to Top) -->


                                                <!-- Top Services Chart -->
                                                <div class="col-lg-12">
                                                    <div class="card-modern h-100">
                                                        <div class="widget-header widget-header-success">
                                                            <i class="fas fa-star me-2"></i>Top 10 Services by Revenue
                                                        </div>
                                                        <div class="card-body p-4">
                                                            @if(isset($top_services) && count($top_services) > 0)
                                                                <div class="chart-bar" style="height: 300px;">
                                                                    <canvas id="topServicesChart"></canvas>
                                                                </div>
                                                            @else
                                                                <div class="empty-state">
                                                                    <i class="fas fa-cut text-muted"></i>
                                                                    <p class="mb-0">No service data available</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Recent Bookings Table (All Bookings with Assign functionality) -->
                                            <div class="card-modern mb-4">
                                                <div class="widget-header widget-header-primary">
                                                    <i class="fas fa-calendar-check me-2"></i>Recent Bookings
                                                </div>
                                                <div class="card-body p-0">
                                                    @if(isset($recent_bookings_all) && count($recent_bookings_all) > 0)
                                                        <div class="table-responsive">
                                                            <table class="table table-hover align-middle mb-0">
                                                                <thead class="bg-light">
                                                                    <tr>
                                                                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Date
                                                                        </th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Service
                                                                        </th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Customer
                                                                        </th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Staff</th>
                                                                        <th class="text-end py-3 text-uppercase small fw-bold text-muted">
                                                                            Amount</th>
                                                                        <th
                                                                            class="text-center py-3 text-uppercase small fw-bold text-muted">
                                                                            Status</th>
                                                                        <th
                                                                            class="text-end pe-4 py-3 text-uppercase small fw-bold text-muted">
                                                                            Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($recent_bookings_all as $booking)
                                                                        <tr>
                                                                            <td class="ps-4">
                                                                                <div class="fw-bold text-dark">
                                                                                    {{ format_date($booking->start_time, 'M d') }}
                                                                                </div>
                                                                                <div class="small text-muted">
                                                                                    {{ format_time($booking->start_time) }}
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                @if($booking->group_count > 1)
                                                                                    @php
                                                                                        $tooltipHtml = "<div class='text-start'>";
                                                                                        foreach ($booking->groupBookings as $gb) {
                                                                                            $tooltipHtml .= "<div class='tooltip-service-item'>";
                                                                                            $tooltipHtml .= "<strong>" . ($gb->service->name ?? 'Unknown') . "</strong><br>";
                                                                                            $tooltipHtml .= "<small>Staff: " . ($gb->staff->name ?? 'Unassigned') . "</small>";
                                                                                            $tooltipHtml .= "</div>";
                                                                                        }
                                                                                        $tooltipHtml .= "</div>";
                                                                                    @endphp
                                                                                    <span
                                                                                        class="badge bg-info bg-opacity-10 text-info me-2 group-booking-badge"
                                                                                        style="font-size: 0.75em;" data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top" data-bs-html="true"
                                                                                        title="{{ $tooltipHtml }}">
                                                                                        Group
                                                                                    </span>
                                                                                    <span class="fw-semibold text-dark">Group Booking
                                                                                        ({{ $booking->group_count }} services)</span>
                                                                                @else
                                                                                    <span
                                                                                        class="fw-semibold text-dark">{{ $booking->service->name ?? 'Unknown' }}</span>
                                                                                    @if($booking->package)
                                                                                        <div class="mt-1">
                                                                                            <span
                                                                                                class="badge bg-purple-subtle text-purple-emphasis rounded-pill px-2 py-1"
                                                                                                style="background-color: #f3e8ff !important; color: #7c3aed !important; font-size: 0.7em;">
                                                                                                <i
                                                                                                    class="fas fa-box me-1"></i>{{ $booking->package->name }}
                                                                                            </span>
                                                                                        </div>
                                                                                    @endif
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                <div class="d-flex align-items-center">
                                                                                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center me-2"
                                                                                        style="width: 32px; height: 32px;">
                                                                                        {{ substr($booking->customer->name ?? 'U', 0, 1) }}
                                                                                    </div>
                                                                                    <div>
                                                                                        <div class="fw-semibold text-dark">
                                                                                            {{ $booking->customer->name ?? 'Unknown' }}
                                                                                        </div>
                                                                                        @if($booking->customer && $booking->customer->is_guest)
                                                                                            <span class="badge bg-secondary"
                                                                                                style="font-size: 0.6em;">Guest</span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                @if($booking->group_count > 1)
                                                                                    @php
                                                                                        $uniqueStaff = $booking->groupBookings->pluck('staff')->filter()->unique('id');
                                                                                    @endphp
                                                                                    @if($uniqueStaff->count() > 0)
                                                                                        <div class="staff-avatars">
                                                                                            @foreach($uniqueStaff->take(3) as $index => $staff)
                                                                                                <div class="staff-avatar"
                                                                                                    style="z-index: {{ 10 - $index }};"
                                                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                                                    title="{{ $staff->name }}">
                                                                                                    {{ substr($staff->name, 0, 1) }}
                                                                                                </div>
                                                                                            @endforeach
                                                                                            @if($uniqueStaff->count() > 3)
                                                                                                <div class="staff-avatar-more" data-bs-toggle="tooltip"
                                                                                                    data-bs-placement="top"
                                                                                                    title="{{ $uniqueStaff->skip(3)->pluck('name')->implode(', ') }}">
                                                                                                    +{{ $uniqueStaff->count() - 3 }}
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>
                                                                                    @else
                                                                                        <span class="badge bg-light text-muted border">Unassigned</span>
                                                                                    @endif
                                                                                @else
                                                                                    @if($booking->staff)
                                                                                        <div class="d-flex align-items-center">
                                                                                            <div class="rounded-circle bg-light text-success d-flex align-items-center justify-content-center me-2"
                                                                                                style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                                                                {{ substr($booking->staff->name, 0, 1) }}
                                                                                            </div>
                                                                                            <span class="small">{{ $booking->staff->name }}</span>
                                                                                        </div>
                                                                                    @else
                                                                                        <span class="badge bg-light text-muted border">Unassigned</span>
                                                                                    @endif
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-end fw-bold text-dark">
                                                                                {{ currency_symbol() }}{{ number_format($booking->group_total, 2) }}
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span
                                                                                    class="badge badge-modern bg-{{ $booking->status === 'completed' ? 'success' : ($booking->status === 'pending' ? 'warning' : ($booking->status === 'confirmed' ? 'info' : 'danger')) }}">
                                                                                    {{ ucfirst($booking->status) }}
                                                                                </span>
                                                                            </td>
                                                                            <td class="text-end pe-4">
                                                                                @if(is_null($booking->staff_id) && $booking->staff_assignment_status === 'pending')
                                                                                    <button
                                                                                        class="btn btn-sm btn-outline-primary assign-btn rounded-pill px-3"
                                                                                        data-id="{{ $booking->id }}"
                                                                                        data-date="{{ $booking->start_time->format('Y-m-d') }}">
                                                                                        Assign
                                                                                    </button>
                                                                                @else
                                                                                    <button
                                                                                        class="btn btn-sm btn-light text-muted view-booking rounded-circle"
                                                                                        data-booking-id="{{ $booking->id }}" data-bs-toggle="modal"
                                                                                        data-bs-target="#bookingDetailsModal" title="View Details">
                                                                                        <i class="fas fa-eye"></i>
                                                                                    </button>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="p-3">
                                                            {{ $recent_bookings_all->links() }}
                                                        </div>
                                                    @else
                                                        <div class="empty-state">
                                                            <i class="fas fa-calendar-check text-muted"></i>
                                                            <p class="mb-0">No recent bookings found.</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Recent Sales Table -->
                                            <div class="card-modern">
                                                <div class="widget-header widget-header-primary">
                                                    <i class="fas fa-history me-2"></i>Recent Sales
                                                </div>
                                                <div class="card-body p-0">
                                                    @if(isset($recent_bookings) && count($recent_bookings) > 0)
                                                        <div class="table-responsive">
                                                            <table class="table table-hover align-middle mb-0">
                                                                <thead class="bg-light">
                                                                    <tr>
                                                                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Date
                                                                        </th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Type /
                                                                            Details</th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Customer
                                                                        </th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Staff</th>
                                                                        <th class="text-end py-3 text-uppercase small fw-bold text-muted">
                                                                            Amount</th>
                                                                        <th
                                                                            class="text-center py-3 text-uppercase small fw-bold text-muted">
                                                                            Status</th>
                                                                        <th
                                                                            class="text-end pe-4 py-3 text-uppercase small fw-bold text-muted">
                                                                            Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($recent_bookings as $sale)
                                                                        <tr>
                                                                            <td class="ps-4">
                                                                                <div class="fw-bold text-dark">
                                                                                    {{ format_date(\Carbon\Carbon::parse($sale->date), 'M d') }}
                                                                                </div>
                                                                                <div class="small text-muted">
                                                                                    {{ format_time(\Carbon\Carbon::parse($sale->date)) }}
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                @if($sale->type === 'booking')
                                                                                    <span class="badge bg-primary bg-opacity-10 text-primary me-2"
                                                                                        style="font-size: 0.75em;">Service</span>
                                                                                    @if($sale->model && $sale->model->group_count > 1)
                                                                                        <span class="fw-semibold text-dark">Group Booking
                                                                                            ({{ $sale->model->group_count }} services)</span>
                                                                                    @else
                                                                                        <span
                                                                                            class="fw-semibold text-dark">{{ $sale->model->service->name ?? 'Unknown' }}</span>
                                                                                        @if($sale->model->package)
                                                                                            <div class="mt-1">
                                                                                                <span
                                                                                                    class="badge bg-purple-subtle text-purple-emphasis rounded-pill px-2 py-1"
                                                                                                    style="background-color: #f3e8ff !important; color: #7c3aed !important; font-size: 0.65em;">
                                                                                                    <i
                                                                                                        class="fas fa-box me-1"></i>{{ $sale->model->package->name }}
                                                                                                </span>
                                                                                            </div>
                                                                                        @endif
                                                                                    @endif
                                                                                @else
                                                                                    <span class="badge bg-warning bg-opacity-10 text-warning me-2"
                                                                                        style="font-size: 0.75em;">POS</span>
                                                                                    <span class="fw-semibold text-dark">POS Sale
                                                                                        #{{ $sale->model->invoice_number }}</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                <div class="d-flex align-items-center">
                                                                                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center me-2"
                                                                                        style="width: 32px; height: 32px;">
                                                                                        {{ substr($sale->model->customer->name ?? 'U', 0, 1) }}
                                                                                    </div>
                                                                                    <div>
                                                                                        <div class="fw-semibold text-dark">
                                                                                            {{ $sale->model->customer->name ?? 'Unknown' }}
                                                                                        </div>
                                                                                        @if($sale->model->customer && $sale->model->customer->is_guest)
                                                                                            <span class="badge bg-secondary"
                                                                                                style="font-size: 0.6em;">Guest</span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                @if($sale->type === 'booking')
                                                                                    @if($sale->model->staff)
                                                                                        <div class="d-flex align-items-center">
                                                                                            <div class="rounded-circle bg-light text-success d-flex align-items-center justify-content-center me-2"
                                                                                                style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                                                                {{ substr($sale->model->staff->name, 0, 1) }}
                                                                                            </div>
                                                                                            <span class="small">{{ $sale->model->staff->name }}</span>
                                                                                        </div>
                                                                                    @else
                                                                                        <span class="badge bg-light text-muted border">Unassigned</span>
                                                                                    @endif
                                                                                @else
                                                                                    @if($sale->model->employee)
                                                                                        <div class="d-flex align-items-center">
                                                                                            <div class="rounded-circle bg-light text-success d-flex align-items-center justify-content-center me-2"
                                                                                                style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                                                                {{ substr($sale->model->employee->name, 0, 1) }}
                                                                                            </div>
                                                                                            <span
                                                                                                class="small">{{ $sale->model->employee->name }}</span>
                                                                                        </div>
                                                                                    @else
                                                                                        <span class="text-muted">Unknown</span>
                                                                                    @endif
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-end fw-bold text-dark">
                                                                                {{ currency_symbol() }}{{ number_format($sale->amount, 2) }}
                                                                            </td>
                                                                            <td class="text-center">
                                                                                @php
                                                                                    $badgeClass = 'success';
                                                                                    $statusLabel = 'Paid';
                                                                                    $displayAmount = $sale->amount;

                                                                                    if ($sale->type === 'booking') {
                                                                                        $statusLabel = ucfirst($sale->status);
                                                                                        if ($sale->payment_status === 'refunded' || $sale->status === 'cancelled') {
                                                                                            $badgeClass = 'secondary';
                                                                                            $statusLabel = 'Refunded';
                                                                                        } elseif ($sale->payment_status === 'partial' || $sale->status === 'partial') {
                                                                                            $badgeClass = 'warning';
                                                                                            $statusLabel = 'Partial';
                                                                                        } elseif ($sale->payment_status === 'unpaid') {
                                                                                            $badgeClass = 'danger';
                                                                                            $statusLabel = 'Unpaid';
                                                                                        }
                                                                                    } else {
                                                                                        $statusLabel = ucfirst($sale->payment_status);
                                                                                        if ($sale->status === 'refunded') {
                                                                                            $badgeClass = 'secondary';
                                                                                            $statusLabel = 'Refunded';
                                                                                        } elseif ($sale->status === 'partially_refunded' || $sale->payment_status === 'partial') {
                                                                                            $badgeClass = 'warning';
                                                                                            $statusLabel = 'Partial';
                                                                                        } elseif ($sale->payment_status === 'unpaid') {
                                                                                            $badgeClass = 'danger';
                                                                                        }
                                                                                    }
                                                                                 @endphp
                                                                                <span class="badge badge-modern bg-{{ $badgeClass }}">
                                                                                    {{ $statusLabel }}
                                                                                </span>
                                                                            </td>
                                                                            <td class="text-end pe-4">
                                                                                @if($sale->type === 'booking')
                                                                                    <button
                                                                                        class="btn btn-sm btn-light text-muted view-booking rounded-circle"
                                                                                        data-booking-id="{{ $sale->id }}" data-bs-toggle="modal"
                                                                                        data-bs-target="#bookingDetailsModal" title="View Details">
                                                                                        <i class="fas fa-eye"></i>
                                                                                    </button>
                                                                                @else
                                                                                    <a href="{{ route('admin.pos.sales.show', $sale->id) }}"
                                                                                        class="btn btn-sm btn-light text-muted rounded-circle"
                                                                                        title="View Details">
                                                                                        <i class="fas fa-eye"></i>
                                                                                    </a>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="p-3">
                                                            {{ $recent_bookings->links() }}
                                                        </div>
                                                    @else
                                                        <div class="empty-state">
                                                            <i class="fas fa-history text-muted"></i>
                                                            <p class="mb-0">No recent sales found.</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Upcoming Appointments Widget -->
                                    @if($showSales)
                                        <div class="mb-4">
                                            <div class="card-modern">
                                                <div class="widget-header widget-header-primary">
                                                    <i class="fas fa-calendar-alt me-2"></i>Upcoming Appointments
                                                </div>
                                                <div class="card-body p-0">
                                                    @php
                                                        $upcomingAppointments = \App\Models\Booking::where('salon_id', auth()->user()->salon_id)
                                                            ->where('start_time', '>', now())
                                                            ->whereIn('status', ['pending', 'confirmed'])
                                                            ->select('bookings.*')
                                                            ->selectRaw('COALESCE((SELECT COUNT(*) FROM bookings b2 WHERE b2.booking_group_id = bookings.booking_group_id AND bookings.booking_group_id IS NOT NULL AND b2.salon_id = bookings.salon_id), 1) as group_count')
                                                            ->selectRaw('COALESCE((SELECT SUM(amount) FROM bookings b3 WHERE b3.booking_group_id = bookings.booking_group_id AND bookings.booking_group_id IS NOT NULL AND b3.salon_id = bookings.salon_id), amount) as group_total')
                                                            ->joinSub(
                                                                DB::table('bookings')
                                                                    ->select(DB::raw('MIN(id) as min_id'))
                                                                    ->where('salon_id', auth()->user()->salon_id)
                                                                    ->groupBy(DB::raw('COALESCE(booking_group_id, CAST(id AS CHAR))')),
                                                                'grouped',
                                                                'bookings.id',
                                                                '=',
                                                                'grouped.min_id'
                                                            )
                                                            ->with(['customer', 'service', 'staff', 'package'])
                                                            ->orderBy('start_time', 'asc')
                                                            ->take(10)
                                                            ->get()
                                                            ->map(function ($appointment) {
                                                                // Load all bookings in the group for tooltip
                                                                if ($appointment->booking_group_id) {
                                                                    $appointment->groupBookings = \App\Models\Booking::where('salon_id', auth()->user()->salon_id)
                                                                        ->where('booking_group_id', $appointment->booking_group_id)
                                                                        ->with(['service', 'staff', 'package'])
                                                                        ->get();
                                                                } else {
                                                                    $appointment->groupBookings = collect([$appointment]);
                                                                }
                                                                return $appointment;
                                                            });
                                                    @endphp

                                                    @if($upcomingAppointments->count() > 0)
                                                        <div class="table-responsive">
                                                            <table class="table table-hover align-middle mb-0">
                                                                <thead class="bg-light">
                                                                    <tr>
                                                                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Date &
                                                                            Time</th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Customer
                                                                        </th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Service
                                                                        </th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Staff</th>
                                                                        <th
                                                                            class="text-center py-3 text-uppercase small fw-bold text-muted">
                                                                            Status</th>
                                                                        <th
                                                                            class="text-end pe-4 py-3 text-uppercase small fw-bold text-muted">
                                                                            Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($upcomingAppointments as $appointment)
                                                                        <tr>
                                                                            <td class="ps-4">
                                                                                <div class="fw-bold text-dark">
                                                                                    {{ format_date($appointment->start_time, 'M d, Y') }}
                                                                                </div>
                                                                                <div class="small text-muted">
                                                                                    {{ format_time($appointment->start_time) }}
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="d-flex align-items-center">
                                                                                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center me-2"
                                                                                        style="width: 32px; height: 32px;">
                                                                                        {{ substr($appointment->customer->name ?? 'U', 0, 1) }}
                                                                                    </div>
                                                                                    <div>
                                                                                        <div class="fw-semibold text-dark">
                                                                                            {{ $appointment->customer->name ?? 'Unknown' }}
                                                                                        </div>
                                                                                        @if($appointment->customer && $appointment->customer->is_guest)
                                                                                            <span class="badge bg-secondary"
                                                                                                style="font-size: 0.6em;">Guest</span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                @if($appointment->group_count > 1)
                                                                                    @php
                                                                                        $tooltipHtml = "<div class='text-start'>";
                                                                                        foreach ($appointment->groupBookings as $gb) {
                                                                                            $tooltipHtml .= "<div class='tooltip-service-item'>";
                                                                                            $tooltipHtml .= "<strong>" . ($gb->service->name ?? 'Unknown') . "</strong><br>";
                                                                                            $tooltipHtml .= "<small>Staff: " . ($gb->staff->name ?? 'Unassigned') . "</small>";
                                                                                            $tooltipHtml .= "</div>";
                                                                                        }
                                                                                        $tooltipHtml .= "</div>";
                                                                                    @endphp
                                                                                    <span
                                                                                        class="badge bg-info bg-opacity-10 text-info me-2 group-booking-badge"
                                                                                        style="font-size: 0.75em;" data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top" data-bs-html="true"
                                                                                        title="{{ $tooltipHtml }}">
                                                                                        Group
                                                                                    </span>
                                                                                    <span class="fw-semibold text-dark">Group Booking
                                                                                        ({{ $appointment->group_count }} services)</span>
                                                                                @else
                                                                                    <span
                                                                                        class="fw-semibold text-dark">{{ $appointment->service->name ?? 'Unknown' }}</span>
                                                                                    @if($appointment->package)
                                                                                        <div class="mt-1">
                                                                                            <span
                                                                                                class="badge bg-purple-subtle text-purple-emphasis rounded-pill px-2 py-1"
                                                                                                style="background-color: #f3e8ff !important; color: #7c3aed !important; font-size: 0.7em;">
                                                                                                <i
                                                                                                    class="fas fa-box me-1"></i>{{ $appointment->package->name }}
                                                                                            </span>
                                                                                        </div>
                                                                                    @endif
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if($appointment->group_count > 1)
                                                                                    @php
                                                                                        $uniqueStaff = $appointment->groupBookings->pluck('staff')->filter()->unique('id');
                                                                                    @endphp
                                                                                    @if($uniqueStaff->count() > 0)
                                                                                        <div class="staff-avatars">
                                                                                            @foreach($uniqueStaff->take(3) as $index => $staff)
                                                                                                <div class="staff-avatar"
                                                                                                    style="z-index: {{ 10 - $index }};"
                                                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                                                    title="{{ $staff->name }}">
                                                                                                    {{ substr($staff->name, 0, 1) }}
                                                                                                </div>
                                                                                            @endforeach
                                                                                            @if($uniqueStaff->count() > 3)
                                                                                                <div class="staff-avatar-more" data-bs-toggle="tooltip"
                                                                                                    data-bs-placement="top"
                                                                                                    title="{{ $uniqueStaff->skip(3)->pluck('name')->implode(', ') }}">
                                                                                                    +{{ $uniqueStaff->count() - 3 }}
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>
                                                                                    @else
                                                                                        <span class="badge bg-light text-muted border">Unassigned</span>
                                                                                    @endif
                                                                                @else
                                                                                    @if($appointment->staff)
                                                                                        <div class="d-flex align-items-center">
                                                                                            <div class="rounded-circle bg-light text-success d-flex align-items-center justify-content-center me-2"
                                                                                                style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                                                                {{ substr($appointment->staff->name, 0, 1) }}
                                                                                            </div>
                                                                                            <span class="small">{{ $appointment->staff->name }}</span>
                                                                                        </div>
                                                                                    @else
                                                                                        <span class="badge bg-light text-muted border">Unassigned</span>
                                                                                    @endif
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span
                                                                                    class="badge badge-modern bg-{{ $appointment->status === 'confirmed' ? 'success' : 'warning' }}">
                                                                                    {{ ucfirst($appointment->status) }}
                                                                                </span>
                                                                            </td>
                                                                            <td class="text-end pe-4">
                                                                                <button
                                                                                    class="btn btn-sm btn-light text-muted view-booking rounded-circle"
                                                                                    data-booking-id="{{ $appointment->id }}"
                                                                                    data-bs-toggle="modal" data-bs-target="#bookingDetailsModal"
                                                                                    title="View Details">
                                                                                    <i class="fas fa-eye"></i>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @else
                                                        <div class="empty-state">
                                                            <i class="fas fa-calendar-check text-muted"></i>
                                                            <p class="mb-0">No upcoming appointments</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- No-Show Alerts Widget -->
                                    @if($showSales && isset($todays_no_shows))
                                        <div class="mb-4">
                                            <div class="card-modern">
                                                <div class="widget-header widget-header-danger">
                                                    <i class="fas fa-user-times me-2"></i>No-Shows
                                                    ({{ $dateRange == 'today' ? 'Today' : 'Period' }})
                                                </div>
                                                <div class="card-body p-4">
                                                    @if($todays_no_shows->count() > 0)
                                                        <div class="space-y-2">
                                                            @foreach($todays_no_shows as $noShow)
                                                                <div class="list-item-modern">
                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                        <div>
                                                                            <div class="fw-bold text-dark">
                                                                                {{ $noShow->customer->name ?? 'Unknown' }}
                                                                            </div>
                                                                            <div class="small text-muted">
                                                                                <i
                                                                                    class="fas fa-cut me-1"></i>{{ $noShow->service->name ?? 'N/A' }}
                                                                                <span class="mx-2">•</span>
                                                                                <i
                                                                                    class="fas fa-clock me-1"></i>{{ format_time($noShow->start_time) }}
                                                                            </div>
                                                                        </div>
                                                                        <span class="badge badge-modern bg-danger">No Show</span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="empty-state">
                                                            <i class="fas fa-check-circle text-success"></i>
                                                            <p class="mb-0">No no-shows {{ $dateRange == 'today' ? 'today' : 'in period' }}!
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Outstanding Payments Widget -->
                                    @if($showSales && isset($outstanding_payments))
                                        <div class="mb-4">
                                            <div class="card-modern">
                                                <div
                                                    class="widget-header widget-header-warning d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-credit-card me-2"></i>Outstanding Payments
                                                    </div>
                                                    <span class="badge bg-white text-warning fw-bold">
                                                        Total:
                                                        {{ currency_symbol() }}{{ number_format($outstanding_payments['total'], 2) }}
                                                    </span>
                                                </div>
                                                <div class="card-body p-4">
                                                    @if($outstanding_payments['items']->count() > 0)
                                                        <div class="space-y-2">
                                                            @foreach($outstanding_payments['items'] as $payment)
                                                                <div class="list-item-modern">
                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                        <div>
                                                                            <div class="fw-bold text-dark">
                                                                                @if(isset($payment->group_count) && $payment->group_count > 1)
                                                                                    <span class="badge bg-info bg-opacity-10 text-info me-2"
                                                                                        style="font-size: 0.75em;">Group</span>
                                                                                    {{ $payment->customer_name }} (Group Booking)
                                                                                @else
                                                                                    {{ $payment->customer_name }}
                                                                                @endif
                                                                            </div>
                                                                            <div class="small text-muted mt-1">
                                                                                <span
                                                                                    class="badge badge-modern bg-{{ $payment->type === 'booking' ? 'primary' : 'info' }} me-1">
                                                                                    {{ ucfirst($payment->type) }}
                                                                                </span>
                                                                                @if(isset($payment->status))
                                                                                    <span
                                                                                        class="badge badge-modern bg-{{ $payment->status === 'Unpaid' ? 'danger' : 'warning' }} me-1">
                                                                                        {{ $payment->status }}
                                                                                    </span>
                                                                                @endif
                                                                                <i
                                                                                    class="fas fa-calendar-alt me-1"></i>{{ format_date($payment->date, 'M d, Y') }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="text-end">
                                                                            <div class="fw-bold text-danger">
                                                                                {{ currency_symbol() }}{{ number_format($payment->amount, 2) }}
                                                                            </div>
                                                                            <div class="small mt-1">
                                                                                <span
                                                                                    class="badge badge-modern bg-{{ $payment->days_overdue > 7 ? 'danger' : ($payment->days_overdue > 3 ? 'warning' : 'secondary') }}">
                                                                                    {{ $payment->days_overdue }}
                                                                                    {{ $payment->days_overdue == 1 ? 'day' : 'days' }} overdue
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="empty-state">
                                                            <i class="fas fa-check-circle text-success"></i>
                                                            <p class="mb-0">All payments are up to date!</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Staff Insight Tab -->
                                    @if($showStaff)
                                        <div class="tab-pane fade {{ $activeTab == 'staff' ? 'show active' : '' }}" id="staff"
                                            role="tabpanel">
                                            <div class="d-flex justify-content-end mb-3">
                                                <a href="{{ route('admin.reports.staff') }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-external-link-alt me-1"></i> Full Report
                                                </a>
                                            </div>

                                            <!-- Staff Commissions Widget -->
                                            @if(isset($staff_commissions))
                                                <div class="card-modern mb-4">
                                                    <div class="widget-header widget-header-success">
                                                        <i class="fas fa-dollar-sign me-2"></i>Staff Commissions & Tips
                                                    </div>
                                                    <div class="card-body p-4">
                                                        <!-- Tabs for Today/Week/Month -->
                                                        <ul class="nav nav-pills mb-4" role="tablist">
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link active" data-bs-toggle="pill"
                                                                    data-bs-target="#commissions-today"
                                                                    type="button">{{ $dateRange == 'today' ? 'Today' : 'Period' }}</button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" data-bs-toggle="pill"
                                                                    data-bs-target="#commissions-week" type="button">This Week</button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" data-bs-toggle="pill"
                                                                    data-bs-target="#commissions-month" type="button">This Month</button>
                                                            </li>
                                                        </ul>

                                                        <div class="tab-content">
                                                            <!-- Today -->
                                                            <div class="tab-pane fade show active" id="commissions-today">
                                                                <div class="row g-4">
                                                                    <div class="col-md-6">
                                                                        <div class="text-center p-4 rounded"
                                                                            style="background: linear-gradient(135deg, #11998e15 0%, #38ef7d15 100%);">
                                                                            <div class="text-muted small mb-2 text-uppercase fw-semibold">
                                                                                Commissions</div>
                                                                            <div class="h3 mb-0 fw-bold text-success">
                                                                                {{ currency_symbol() }}{{ number_format($staff_commissions['today'], 2) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="text-center p-4 rounded"
                                                                            style="background: linear-gradient(135deg, #4facfe15 0%, #00f2fe15 100%);">
                                                                            <div class="text-muted small mb-2 text-uppercase fw-semibold">
                                                                                Tips</div>
                                                                            <div class="h3 mb-0 fw-bold text-info">
                                                                                {{ currency_symbol() }}{{ number_format($staff_commissions['today_tips'], 2) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Week -->
                                                            <div class="tab-pane fade" id="commissions-week">
                                                                <div class="row g-4">
                                                                    <div class="col-md-6">
                                                                        <div class="text-center p-4 rounded"
                                                                            style="background: linear-gradient(135deg, #11998e15 0%, #38ef7d15 100%);">
                                                                            <div class="text-muted small mb-2 text-uppercase fw-semibold">
                                                                                Commissions</div>
                                                                            <div class="h3 mb-0 fw-bold text-success">
                                                                                {{ currency_symbol() }}{{ number_format($staff_commissions['week'], 2) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="text-center p-4 rounded"
                                                                            style="background: linear-gradient(135deg, #4facfe15 0%, #00f2fe15 100%);">
                                                                            <div class="text-muted small mb-2 text-uppercase fw-semibold">
                                                                                Tips</div>
                                                                            <div class="h3 mb-0 fw-bold text-info">
                                                                                {{ currency_symbol() }}{{ number_format($staff_commissions['week_tips'], 2) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Month -->
                                                            <div class="tab-pane fade" id="commissions-month">
                                                                <div class="row g-4 mb-4">
                                                                    <div class="col-md-6">
                                                                        <div class="text-center p-4 rounded"
                                                                            style="background: linear-gradient(135deg, #11998e15 0%, #38ef7d15 100%);">
                                                                            <div class="text-muted small mb-2 text-uppercase fw-semibold">
                                                                                Commissions</div>
                                                                            <div class="h3 mb-0 fw-bold text-success">
                                                                                {{ currency_symbol() }}{{ number_format($staff_commissions['month'], 2) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="text-center p-4 rounded"
                                                                            style="background: linear-gradient(135deg, #4facfe15 0%, #00f2fe15 100%);">
                                                                            <div class="text-muted small mb-2 text-uppercase fw-semibold">
                                                                                Tips</div>
                                                                            <div class="h3 mb-0 fw-bold text-info">
                                                                                {{ currency_symbol() }}{{ number_format($staff_commissions['month_tips'], 2) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Top Earners -->
                                                                @if(count($staff_commissions['top_earners']) > 0)
                                                                    <div class="mt-4">
                                                                        <h6 class="text-muted mb-3 text-uppercase fw-semibold small">Top Earners
                                                                            This Month</h6>
                                                                        <div class="space-y-2">
                                                                            @foreach($staff_commissions['top_earners'] as $earner)
                                                                                <div class="list-item-modern">
                                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                                        <span
                                                                                            class="fw-semibold text-dark">{{ $earner['name'] }}</span>
                                                                                        <span
                                                                                            class="badge badge-modern bg-success">{{ currency_symbol() }}{{ number_format($earner['commission'], 2) }}</span>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="row g-3">
                                                <!-- Staff Performance Chart -->
                                                <div class="col-lg-6">
                                                    <div class="card-modern h-100">
                                                        <div class="widget-header widget-header-primary">
                                                            <i class="fas fa-chart-bar me-2"></i>Staff Revenue Performance
                                                        </div>
                                                        <div class="card-body p-4">
                                                            @if(isset($staff_performance) && count($staff_performance) > 0)
                                                                <div class="chart-bar" style="height: 300px;">
                                                                    <canvas id="staffPerformanceChart"></canvas>
                                                                </div>
                                                            @else
                                                                <div class="empty-state">
                                                                    <i class="fas fa-chart-bar text-muted"></i>
                                                                    <p class="mb-0">No staff performance data available</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Staff Schedule Table -->
                                                <div class="col-lg-6">
                                                    <div class="card-modern h-100">
                                                        <div class="widget-header widget-header-primary">
                                                            <i class="fas fa-calendar-day me-2"></i>Today's Schedule
                                                        </div>
                                                        <div class="card-body p-0">
                                                            @if(isset($staff_schedule) && count($staff_schedule) > 0)
                                                                <div class="table-responsive">
                                                                    <table class="table table-hover align-middle mb-0">
                                                                        <thead class="bg-light">
                                                                            <tr>
                                                                                <th
                                                                                    class="ps-4 py-3 text-uppercase small fw-bold text-muted">
                                                                                    Staff</th>
                                                                                <th
                                                                                    class="text-center py-3 text-uppercase small fw-bold text-muted">
                                                                                    Appts</th>
                                                                                <th class="py-3 text-uppercase small fw-bold text-muted">
                                                                                    Next Slot</th>
                                                                                <th
                                                                                    class="text-end pe-4 py-3 text-uppercase small fw-bold text-muted">
                                                                                    Status</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($staff_schedule as $schedule)
                                                                                <tr>
                                                                                    <td class="ps-4">
                                                                                        <div class="d-flex align-items-center">
                                                                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3"
                                                                                                style="width: 36px; height: 36px;">
                                                                                                {{ substr($schedule->staff->name, 0, 1) }}
                                                                                            </div>
                                                                                            <span
                                                                                                class="fw-semibold text-dark">{{ $schedule->staff->name }}</span>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td class="text-center">
                                                                                        <span
                                                                                            class="badge bg-light text-dark border">{{ $schedule->appointments_count }}</span>
                                                                                    </td>
                                                                                    <td class="text-muted small">{{ $schedule->next_available }}
                                                                                    </td>
                                                                                    <td class="text-end pe-4">
                                                                                        <span
                                                                                            class="badge badge-modern bg-{{ $schedule->status === 'available' ? 'success' : 'warning' }}">
                                                                                            {{ ucfirst($schedule->status) }}
                                                                                        </span>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @else
                                                                <div class="empty-state">
                                                                    <i class="fas fa-calendar-times text-muted"></i>
                                                                    <p class="mb-0">No schedule data available.</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Customer Insight Tab -->
                                    @if($showCustomer)
                                        <div class="tab-pane fade {{ $activeTab == 'customer' ? 'show active' : '' }}" id="customer"
                                            role="tabpanel">
                                            <div class="d-flex justify-content-end mb-3">
                                                <a href="{{ route('admin.reports.customers') }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-external-link-alt me-1"></i> Full Report
                                                </a>
                                            </div>

                                            <!-- Customer Feedback Widget -->
                                            @if(isset($customer_feedback))
                                                <div class="card-modern mb-4">
                                                    <div class="widget-header widget-header-warning">
                                                        <i class="fas fa-star me-2"></i>Customer Feedback & Ratings
                                                    </div>
                                                    <div class="card-body p-4">
                                                        <div class="row g-4 mb-4">
                                                            <div class="col-md-6">
                                                                <div class="text-center p-4 rounded"
                                                                    style="background: linear-gradient(135deg, #f093fb15 0%, #f5576c15 100%);">
                                                                    <div class="text-muted small mb-2 text-uppercase fw-semibold">Average
                                                                        Rating</div>
                                                                    <div class="h2 mb-2 fw-bold text-warning">
                                                                        {{ $customer_feedback['average_rating'] }}
                                                                        <i class="fas fa-star"></i>
                                                                    </div>
                                                                    <div class="small text-muted">Based on
                                                                        {{ $customer_feedback['total_reviews'] }} reviews
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="text-center p-4 rounded"
                                                                    style="background: linear-gradient(135deg, #4facfe15 0%, #00f2fe15 100%);">
                                                                    <div class="text-muted small mb-2 text-uppercase fw-semibold">Total
                                                                        Reviews</div>
                                                                    <div class="h2 mb-2 fw-bold text-info">
                                                                        {{ $customer_feedback['total_reviews'] }}
                                                                    </div>
                                                                    <div class="small text-muted">All time</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        @if(count($customer_feedback['recent_reviews']) > 0)
                                                            <div class="mt-4">
                                                                <h6 class="text-muted mb-3 text-uppercase fw-semibold small">Recent Reviews</h6>
                                                                <div class="space-y-2">
                                                                    @foreach($customer_feedback['recent_reviews'] as $review)
                                                                        <div class="list-item-modern">
                                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                                <div class="fw-semibold text-dark">
                                                                                    {{ $review->customer->name ?? 'Anonymous' }}
                                                                                </div>
                                                                                <div>
                                                                                    @for($i = 1; $i <= 5; $i++)
                                                                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"
                                                                                            style="font-size: 0.8rem;"></i>
                                                                                    @endfor
                                                                                </div>
                                                                            </div>
                                                                            @if(isset($review->comment))
                                                                                <p class="mb-2 small text-muted">{{ Str::limit($review->comment, 100) }}
                                                                                </p>
                                                                            @endif
                                                                            <small
                                                                                class="text-muted d-block text-end">{{ $review->created_at->diffForHumans() }}</small>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="empty-state">
                                                                <i class="fas fa-comments text-muted"></i>
                                                                <p class="mb-0">No reviews yet</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Birthday Reminders Widget -->
                                            @if(isset($upcoming_birthdays) && $upcoming_birthdays->count() > 0)
                                                <div class="card-modern mb-4">
                                                    <div class="widget-header widget-header-info">
                                                        <i class="fas fa-birthday-cake me-2"></i>Upcoming Birthdays (Next 7 Days)
                                                    </div>
                                                    <div class="card-body p-4">
                                                        <div class="space-y-2">
                                                            @foreach($upcoming_birthdays as $customer)
                                                                <div class="list-item-modern d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <div class="fw-semibold text-dark">{{ $customer->name }}</div>
                                                                        <small class="text-muted">
                                                                            <i
                                                                                class="fas fa-phone me-1"></i>{{ $customer->phone ?? 'No phone' }}
                                                                        </small>
                                                                    </div>
                                                                    <div class="text-end">
                                                                        <div class="fw-bold text-info">
                                                                            {{ $customer->birthday->format('M d') }}
                                                                        </div>
                                                                        <small class="text-muted">
                                                                            @if($customer->days_until == 0)
                                                                                <span class="badge badge-modern bg-success">Today!</span>
                                                                            @elseif($customer->days_until == 1)
                                                                                <span class="badge badge-modern bg-warning">Tomorrow</span>
                                                                            @else
                                                                                {{ $customer->days_until }} days
                                                                            @endif
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="row g-4 mb-4">
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="card-modern h-100"
                                                        style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="text-uppercase small fw-bold text-primary mb-1">Total
                                                                        Customers</div>
                                                                    <div class="h3 mb-0 fw-bold text-dark">{{ $total_customers ?? 0 }}
                                                                    </div>
                                                                </div>
                                                                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                                                    <i class="fas fa-users fa-lg"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="card-modern h-100"
                                                        style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="text-uppercase small fw-bold text-success mb-1">Active
                                                                        Customers</div>
                                                                    <div class="h3 mb-0 fw-bold text-dark">{{ $active_customers ?? 0 }}
                                                                    </div>
                                                                </div>
                                                                <div class="stat-icon bg-success bg-opacity-10 text-success">
                                                                    <i class="fas fa-user-check fa-lg"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="card-modern h-100"
                                                        style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="text-uppercase small fw-bold text-info mb-1">New This
                                                                        Month</div>
                                                                    <div class="h3 mb-0 fw-bold text-dark">
                                                                        {{ $new_customers_month ?? 0 }}
                                                                    </div>
                                                                </div>
                                                                <div class="stat-icon bg-info bg-opacity-10 text-info">
                                                                    <i class="fas fa-user-plus fa-lg"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="card-modern h-100"
                                                        style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="text-uppercase small fw-bold text-warning mb-1">
                                                                        Returning Rate</div>
                                                                    <div class="h3 mb-0 fw-bold text-dark">
                                                                        {{ number_format($returning_rate ?? 0, 1) }}%
                                                                    </div>
                                                                </div>
                                                                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                                                                    <i class="fas fa-sync-alt fa-lg"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Top Customers Table -->
                                            <div class="card-modern">
                                                <div class="widget-header widget-header-primary">
                                                    <i class="fas fa-crown me-2"></i>Top Customers
                                                </div>
                                                <div class="card-body p-0">
                                                    @if(isset($top_customers) && count($top_customers) > 0)
                                                        <div class="table-responsive">
                                                            <table class="table table-hover align-middle mb-0">
                                                                <thead class="bg-light">
                                                                    <tr>
                                                                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">
                                                                            Customer</th>
                                                                        <th
                                                                            class="text-center py-3 text-uppercase small fw-bold text-muted">
                                                                            Visits</th>
                                                                        <th class="text-end py-3 text-uppercase small fw-bold text-muted">
                                                                            Total Spent</th>
                                                                        <th class="py-3 text-uppercase small fw-bold text-muted">Last Visit
                                                                        </th>
                                                                        <th
                                                                            class="text-end pe-4 py-3 text-uppercase small fw-bold text-muted">
                                                                            Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($top_customers as $customer)
                                                                        <tr>
                                                                            <td class="ps-4">
                                                                                <div class="d-flex align-items-center">
                                                                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3"
                                                                                        style="width: 36px; height: 36px;">
                                                                                        {{ substr($customer->name, 0, 1) }}
                                                                                    </div>
                                                                                    <span
                                                                                        class="fw-semibold text-dark">{{ $customer->name }}</span>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span
                                                                                    class="badge bg-light text-dark border">{{ $customer->visits_count }}</span>
                                                                            </td>
                                                                            <td class="text-end fw-bold text-dark">
                                                                                {{ currency_symbol() }}{{ number_format($customer->total_spent, 2) }}
                                                                            </td>
                                                                            <td class="text-muted small">{{ $customer->last_visit }}</td>
                                                                            <td class="text-end pe-4">
                                                                                <span
                                                                                    class="badge badge-modern bg-{{ $customer->status === 'active' ? 'success' : 'warning' }}">
                                                                                    {{ ucfirst($customer->status) }}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @else
                                                        <div class="empty-state">
                                                            <i class="fas fa-users-slash text-muted"></i>
                                                            <p class="mb-0">No top customers data available.</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endcan

        <!-- Customer Dashboard -->
        @role('customer')
        <div class="row g-3 mb-4">
            <!-- Stats Cards -->
            <div class="col-xl-4 col-md-6">
                <div class="stat-card gradient-card-1">
                    <div class="stat-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">Upcoming Appointments</div>
                                <div class="stat-value">{{ $upcoming_appointments_count ?? 0 }}</div>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6">
                <div class="stat-card gradient-card-2">
                    <div class="stat-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">Completed Visits</div>
                                <div class="stat-value">{{ $completed_appointments ?? 0 }}</div>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6">
                <div class="stat-card gradient-card-3">
                    <div class="stat-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">Total Spent</div>
                                <div class="stat-value">
                                    {{ currency_symbol() }}{{ number_format($total_spent ?? 0, 2) }}
                                </div>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Upcoming Appointments -->
            <div class="col-lg-8">
                <div class="card-modern h-100">
                    <div class="widget-header widget-header-primary d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-calendar-day me-2"></i>Upcoming Appointments
                        </div>
                        <a href="{{ route('customer.appointments.create') }}"
                            class="btn btn-sm btn-light text-primary fw-bold rounded-pill px-3">
                            <i class="fas fa-plus me-1"></i> Book New
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @if(isset($upcoming_appointments) && $upcoming_appointments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Date & Time</th>
                                            <th class="py-3 text-uppercase small fw-bold text-muted">Service</th>
                                            <th class="py-3 text-uppercase small fw-bold text-muted">Staff</th>
                                            <th class="text-center py-3 text-uppercase small fw-bold text-muted">Status</th>
                                            <th class="text-end pe-4 py-3 text-uppercase small fw-bold text-muted">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($upcoming_appointments as $appointment)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold text-dark">{{ format_date($appointment->start_time) }}</div>
                                                    <div class="small text-muted">{{ format_time($appointment->start_time) }}</div>
                                                </td>
                                                <td><span
                                                        class="fw-semibold text-dark">{{ $appointment->service->name ?? 'Unknown' }}</span>
                                                </td>
                                                <td>
                                                    @if($appointment->staff)
                                                        <div class="d-flex align-items-center">
                                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-2"
                                                                style="width: 28px; height: 28px; font-size: 0.7rem;">
                                                                {{ substr($appointment->staff->name, 0, 1) }}
                                                            </div>
                                                            <span class="small">{{ $appointment->staff->name }}</span>
                                                        </div>
                                                    @else
                                                        <span class="badge bg-light text-muted border">Any Staff</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge badge-modern bg-{{ $appointment->status === 'confirmed' ? 'success' : ($appointment->status === 'pending' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($appointment->status) }}
                                                    </span>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('customer.appointments.show', $appointment->id) }}"
                                                            class="btn btn-light text-primary rounded-circle me-1"
                                                            title="View Details"
                                                            style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if(!in_array($appointment->status, ['completed', 'cancelled']))
                                                            <button type="button"
                                                                class="btn btn-light text-info reschedule-btn rounded-circle"
                                                                data-appointment-id="{{ $appointment->id }}"
                                                                data-service-id="{{ $appointment->service_id }}"
                                                                data-current-date="{{ $appointment->start_time->format('Y-m-d') }}"
                                                                data-current-time="{{ $appointment->start_time->format('H:i') }}"
                                                                title="Reschedule"
                                                                style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-calendar-alt"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($upcoming_appointments->hasPages())
                                <div class="card-footer bg-white border-top p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-muted small">
                                            Showing {{ $upcoming_appointments->firstItem() }} to
                                            {{ $upcoming_appointments->lastItem() }} of {{ $upcoming_appointments->total() }}
                                            appointments
                                        </div>
                                        <div>
                                            {{ $upcoming_appointments->links('pagination::bootstrap-5') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="empty-state">
                                <i class="fas fa-calendar-check text-muted"></i>
                                <p class="mb-3">No upcoming appointments</p>
                                <a href="{{ route('customer.appointments.create') }}" class="btn btn-primary rounded-pill px-4">
                                    Book Your First Appointment
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-lg-4">
                <div class="card-modern h-100">
                    <div class="widget-header widget-header-info">
                        <i class="fas fa-history me-2"></i>Recent Activity
                    </div>
                    <div class="card-body p-4">
                        @if(isset($recent_activities) && count($recent_activities) > 0)
                            <div class="space-y-2">
                                @foreach($recent_activities as $activity)
                                    <div class="list-item-modern">
                                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                            <small class="text-muted">
                                                <i class="far fa-clock me-1"></i>
                                                {{ $activity->created_at->diffForHumans() }}
                                            </small>
                                            <span class="badge badge-modern bg-light text-dark border">
                                                {{ ucfirst($activity->type) }}
                                            </span>
                                        </div>
                                        <p class="mb-0 text-dark small">{{ $activity->description }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-history text-muted"></i>
                                <p class="mb-0">No recent activity found</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Section -->
        <div class="row g-3 mt-1">
            <div class="col-12">
                <div class="card-modern">
                    <div class="widget-header widget-header-primary d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-calendar-alt me-2"></i>Appointment Calendar
                        </div>
                        <span class="badge bg-white text-primary">
                            <i class="fas fa-info-circle me-1"></i> Click a date to book
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div id="calendar" class="p-4"></div>
                    </div>
                </div>
            </div>
        </div>
        @endrole


        @push('styles')
            <style>
                .fc-event {
                    cursor: pointer;
                }

                .fc-daygrid-day {
                    cursor: pointer;
                    transition: background-color 0.2s;
                }

                .fc-daygrid-day:hover {
                    background-color: rgba(0, 0, 0, 0.02);
                }

                .fc-toolbar-title {
                    font-size: 1.2rem !important;
                }

                .fc-button {
                    font-size: 0.8rem !important;
                }
            </style>
        @endpush

        <!-- Include Bill Activity Modal -->
        @include('admin.customers.partials.bill_activity_modal')

        <!-- Booking Modal -->
        <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white border-0">
                        <h5 class="modal-title fw-bold" id="bookingModalLabel">
                            <i class="fas fa-calendar-plus me-2"></i>Book Your Appointment
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="modalBookingForm">
                            @csrf

                            <!-- Service Selection Dropdown -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark mb-2">
                                    <i class="fas fa-concierge-bell me-2 text-primary"></i>Select Service
                                </label>
                                <select class="form-select form-select-lg" name="service_id" id="modal_service_select"
                                    required>
                                    <option value="">Choose a service...</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" data-duration="{{ $service->duration }}"
                                            data-price="{{ $service->price }}">
                                            {{ $service->name }} -
                                            {{ currency_symbol() }}{{ number_format($service->price, 2) }}
                                            ({{ $service->duration }} min)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Service Summary Card -->
                            <div class="alert alert-light border-start border-primary border-4 d-none mb-4"
                                id="modalServiceSummary">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-info-circle fa-2x text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 fw-bold text-dark">Service Details</h6>
                                        <div class="row g-2 small">
                                            <div class="col-6">
                                                <i class="fas fa-clock text-muted me-1"></i>
                                                <strong>Duration:</strong> <span id="modalServiceDuration">0</span> minutes
                                            </div>
                                            <div class="col-6">
                                                <i class="fas fa-dollar-sign text-muted me-1"></i>
                                                <strong>Price:</strong> $<span id="modalServicePrice">0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Date and Time Selection -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-calendar me-2 text-primary"></i>Appointment Date
                                    </label>
                                    <input type="date" name="date" id="modal_date" class="form-control form-control-lg"
                                        required min="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-clock me-2 text-primary"></i>Appointment Time
                                    </label>
                                    <select name="time" id="modal_time" class="form-select form-select-lg" required>
                                        <option value="">Choose a time slot...</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Notes Section -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark mb-2">
                                    <i class="fas fa-sticky-note me-2 text-primary"></i>Additional Notes <span
                                        class="text-muted small">(Optional)</span>
                                </label>
                                <textarea name="notes" id="modal_notes" class="form-control" rows="3"
                                    placeholder="Any special requests or requirements..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-primary px-4" onclick="submitModalBooking()">
                            <i class="fas fa-calendar-check me-1"></i>Book Appointment
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointment Details Modal -->
        <div class="modal fade" id="appointmentDetailsModal" tabindex="-1" aria-labelledby="appointmentDetailsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="appointmentDetailsModalLabel">
                            <i class="fas fa-calendar-alt me-2"></i>Appointment Details
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="appointment-details">
                            <!-- Service Info -->
                            <div class="detail-item mb-3">
                                <div class="detail-label">
                                    <i class="fas fa-cut text-primary me-2"></i>Service
                                </div>
                                <div class="detail-value fw-bold" id="detail_service">-</div>
                            </div>

                            <!-- Staff Info -->
                            <div class="detail-item mb-3">
                                <div class="detail-label">
                                    <i class="fas fa-user text-primary me-2"></i>Staff Member
                                </div>
                                <div class="detail-value" id="detail_staff">-</div>
                            </div>

                            <!-- Date & Time -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="fas fa-calendar text-primary me-2"></i>Date
                                        </div>
                                        <div class="detail-value" id="detail_date">-</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="fas fa-clock text-primary me-2"></i>Time
                                        </div>
                                        <div class="detail-value" id="detail_time">-</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="detail-item mb-3">
                                <div class="detail-label">
                                    <i class="fas fa-info-circle text-primary me-2"></i>Status
                                </div>
                                <div class="detail-value">
                                    <span class="badge" id="detail_status">-</span>
                                </div>
                            </div>

                            <!-- Amount -->
                            <div class="detail-item mb-3">
                                <div class="detail-label">
                                    <i class="fas fa-dollar-sign text-primary me-2"></i>Amount
                                </div>
                                <div class="detail-value fw-bold text-white" id="detail_amount">-</div>
                            </div>

                            <!-- Notes (if any) -->
                            <div class="detail-item" id="detail_notes_container" style="display: none;">
                                <div class="detail-label">
                                    <i class="fas fa-sticky-note text-primary me-2"></i>Notes
                                </div>
                                <div class="detail-value text-muted" id="detail_notes">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Close
                        </button>
                        <a href="#" id="view_appointment_btn" class="btn btn-primary">
                            <i class="fas fa-eye me-1"></i>View Full Details
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reschedule Appointment Modal -->
        <div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="rescheduleModalLabel">
                            <i class="fas fa-calendar-alt me-2"></i>Reschedule Appointment
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="rescheduleForm">
                        <div class="modal-body">
                            <input type="hidden" id="reschedule_appointment_id">
                            <input type="hidden" id="reschedule_service_id">

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>Select a new date and time for your appointment</small>
                            </div>

                            <!-- Date & Time Selection -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-calendar text-info me-2"></i>New Date
                                    </label>
                                    <input type="date" id="reschedule_date" class="form-control form-control-lg" required
                                        min="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-clock text-info me-2"></i>New Time
                                    </label>
                                    <select id="reschedule_time" class="form-select form-select-lg" required>
                                        <option value="">Choose time slot...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-check me-1"></i>Confirm Reschedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('styles')
            <style>
                /* Appointment Details Modal Styles */
                .appointment-details {
                    padding: 0.5rem;
                }

                .detail-item {
                    padding: 0.75rem;
                    background: #f8f9fa;
                    border-radius: 0.5rem;
                }

                .detail-label {
                    font-size: 0.875rem;
                    color: #6c757d;
                    margin-bottom: 0.25rem;
                }

                .detail-value {
                    font-size: 1rem;
                    color: #212529;
                }

                /* Modal Enhancements */
                .modal-content {
                    border-radius: 1rem;
                    overflow: hidden;
                }

                .modal-header {
                    padding: 1.5rem;
                }

                .modal-body {
                    background-color: #f8f9fa;
                }

                .form-select-lg,
                .form-control-lg {
                    border-radius: 0.5rem;
                    border: 2px solid #e9ecef;
                    transition: all 0.3s ease;
                }

                .form-select-lg:focus,
                .form-control-lg:focus {
                    border-color: #0d6efd;
                    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
                }

                .form-control:focus,
                .form-select:focus {
                    border-color: #0d6efd;
                    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
                }

                .alert-light {
                    background-color: #fff;
                }
            </style>
        @endpush

        @push('scripts')
            <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
            <script>         // Helper function to get browser's timezone         function getBrowserTimezone() {             try {                 return Intl.DateTimeFormat().resolvedOptions().timeZone;             } catch (e) {                 return 'local';             }         }
                                                 // Helper function to get timezone with salon preference and browser fallback         function getCalendarTimezone(salonTimezone) {             if (salonTimezone && salonTimezone.trim() !== '' && salonTimezone !== 'UTC') {                 try {                     Intl.DateTimeFormat(undefined, { timeZone: salonTimezone });                     return salonTimezone;                 } catch (e) {
                                                         }             }             return getBrowserTimezone();         }         // Helper functions to format date and time in specific timezone         function formatDateInTimezone(date, timezone) {             return new Intl.DateTimeFormat('en-CA', {                 timeZone: timezone,                 year: 'numeric',                 month: '2-digit',                 day: '2-digit'             }).format(date);         }         function formatTimeInTimezone(date, timezone) { return new Intl.DateTimeFormat('en-GB', { timeZone: timezone, hour: '2-digit', minute: '2-digit', hour12: false }).format(date); }         document.addEventListener('DOMContentLoaded', function () {             // Only initialize if calendar element exists             var calendarEl = document.getElementById('calendar');             if (calendarEl) {                 // Get timezone: use salon timezone with fallback to browser timezone                 const salonTimezone = '{{ $settings->get("timezone", "UTC") }}';                 const calendarTimezone = getCalendarTimezone(salonTimezone);             var calendar = new FullCalendar.Calendar(calendarEl, {                 initialView: 'dayGridMonth', timeZone: calendarTimezone, headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' }, events: '{{ route("customer.api.appointments.index") }}', dateClick: function (info) {                         // Open modal with selected date                         document.getElementById('modal_date').value = info.dateStr;                         var bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));                         bookingModal.show();                     },                     eventClick: function (info) {                         // Fetch and show appointment details in modal                         info.jsEvent.preventDefault();                     if (info.event.id) {                             // Get salon slug from current URL                             const pathParts = window.location.pathname.split('/');                             const salonSlug = getSalonSlug(); // Using global helper                         fetch(`/${salonSlug}/customer/api/appointments/${info.event.id}`).then(response => response.json()).then(data => {                                     // Populate modal with appointment data                                     document.getElementById('detail_service').textContent = data.service?.name || '-';                                     document.getElementById('detail_staff').textContent = data.staff?.name || 'Any Staff';                             // Format date and time using timezone                                     const startTime = new Date(data.start_time);                                     document.getElementById('detail_date').textContent = formatDateInTimezone(startTime, calendarTimezone);                                     document.getElementById('detail_time').textContent = formatTimeInTimezone(startTime, calendarTimezone);                             // Status badge                                     const statusBadge = document.getElementById('detail_status');                                     statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);                                     statusBadge.className = 'badge';                                     if (data.status === 'confirmed') {                                         statusBadge.classList.add('bg-success');                                     } else if (data.status === 'pending') {                                         statusBadge.classList.add('bg-warning');                                     } else if (data.status === 'completed') {                                         statusBadge.classList.add('bg-info');                                     } else {                                         statusBadge.classList.add('bg-danger');                                     }                             // Amount                                     document.getElementById('detail_amount').textContent = '$' + parseFloat(data.amount || 0).toFixed(2);                             // Notes                                     if (data.notes) {                                         document.getElementById('detail_notes').textContent = data.notes;                                         document.getElementById('detail_notes_container').style.display = 'block';                                     } else {                                         document.getElementById('detail_notes_container').style.display = 'none';                                     }                             // Update view details button link                                     document.getElementById('view_appointment_btn').href = '/' + salonSlug + '/customer/appointments/' + data.id;                             // Show modal                                     var detailsModal = new bootstrap.Modal(document.getElementById('appointmentDetailsModal'));                                     detailsModal.show();                                 })                                 .catch(error => {                                     alert('Unable to load appointment details. Please try again.');                                 });                         }                     },                     eventDidMount: function (info) {                         // Add tooltip                         if (info.event.extendedProps.status) {                             info.el.title = info.event.title + ' (' + info.event.extendedProps.status + ')';                         }                     }                 });                 calendar.render();             }                             // Modal service selection handler (dropdown)             const modalServiceSelect = document.getElementById('modal_service_select');             const modalDateInput = document.getElementById('modal_date');             const modalTimeSelect = document.getElementById('modal_time');                             modalServiceSelect.addEventListener('change', function () {                                 const selectedOption = this.options[this.selectedIndex]; if (selectedOption.value) {                                     const duration = selectedOption.dataset.duration; const price = selectedOption.dataset.price;                                     document.getElementById('modalServiceDuration').textContent = duration; document.getElementById('modalServicePrice').textContent = parseFloat(price).toFixed(2); document.getElementById('modalServiceSummary').classList.remove('d-none');                                     // Fetch available slots if date is selected                     if (modalDateInput.value) {                         fetchModalAvailableSlots(modalDateInput.value, selectedOption.value);                     }                 } else {                     document.getElementById('modalServiceSummary').classList.add('d-none');                 }             });                                     // Modal date change handler             modalDateInput.addEventListener('change', function () {                 const selectedService = modalServiceSelect.value;                 if (selectedService) {                     fetchModalAvailableSlots(this.value, selectedService);                 }             });                                     // Fetch available time slots for modal             function fetchModalAvailableSlots(date, serviceId) {                 fetch(`{{ route('customer.api.appointments.available-slots') }}?date=${date}&service_id=${serviceId}`)                     .then(response => response.json())                     .then(data => {                         modalTimeSelect.innerHTML = '<option value="">Choose a time slot</option>';                         data.forEach(slot => {                             if (slot.available) {                                 const option = document.createElement('option');                                 option.value = slot.time;                                 option.textContent = slot.time;                                 modalTimeSelect.appendChild(option);                             }                         });                     })                     .catch(error => {                         Swal.fire({                             icon: 'error',                             title: 'Oops...',                             text: 'Failed to fetch available time slots. Please try again.'                         });                     });             }         });                                     // Submit modal booking         function submitModalBooking() {             const form = document.getElementById('modalBookingForm');             const formData = new FormData(form);                                     fetch('{{ route("customer.appointments.store") }}', { method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } }).then(response => response.json()).then(data => {                                         if (data.message) { Swal.fire({ icon: 'success', title: 'Success!', text: data.message, showConfirmButton: false, timer: 1500 }).then(() => { location.reload(); }); } else { throw new Error('Failed to book appointment'); }                                     }
                // Reschedule Functionality         document.addEventListener('DOMContentLoaded', function () {             const rescheduleModal = new bootstrap.Modal(document.getElementById('rescheduleModal'));             const rescheduleForm = document.getElementById('rescheduleForm');             const rescheduleDateInput = document.getElementById('reschedule_date');             const rescheduleTimeSelect = document.getElementById('reschedule_time');
                // Handle Reschedule Button Click             document.querySelectorAll('.reschedule-btn').forEach(button => {                 button.addEventListener('click', function () {                     const appointmentId = this.dataset.appointmentId;                     const serviceId = this.dataset.serviceId;                     const currentDate = this.dataset.currentDate;
                document.getElementById('reschedule_appointment_id').value = appointmentId; document.getElementById('reschedule_service_id').value = serviceId; rescheduleDateIn                                                             put.value = currentDate;
                // Clear time select                     rescheduleTimeSelect.innerHTML = '<option value="">Choose time slot...</option>';
                // Show modal                     rescheduleModal.show();
                // Trigger slot fetch for current date                     fetchRescheduleSlots(currentDate, serviceId);                 });             });
                // Handle Date Change             rescheduleDateInput.addEventListener('change', function () {                 const serviceId = document.getElementById('reschedule_service_id').value;                 if (this.value && serviceId) {                     fetchRescheduleSlots(this.value, serviceId);                 }             });
                // Fetch Slots Function             function fetchRescheduleSlots(date, serviceId) {                 // Get salon slug from URL                 const salonSlug = "{{ app()->bound('current_salon') ? app('current_salon')->slug : '' }}";
                rescheduleTimeSelect.innerHTML = '<option value="">Loading...</option>'; rescheduleTimeSelect.disabled = true;
                fetch(`/${salonSlug}/customer/api/available-slots?date=${date}&service_id=${serviceId}`).then(response => response.json()).then(data => {
                    rescheduleTimeSelect.innerHTML = '<option value="">Choose time slot...</option>'; rescheduleTimeSelect.disabled = false;
                    if (data.length === 0) { rescheduleTimeSelect.innerHTML = '<option value="">No slots available</option>'; return; }
                    data.forEach(slot => { if (slot.available) { const option = document.createElement('option'); option.value = slot.time; option.textContent = slot.time; rescheduleTimeSelect.appendChild(option); } });
                }).catch(error => { rescheduleTimeSelect.innerHTML = '<option value="">Error loading slots</option>'; rescheduleTimeSelect.disabled = false; });
                                                                                }
                // Handle Form Submission             reschedule        Form.addEventListener('submit', function (e) {                 e.preventDefault();
                const appointmentId = document.getElementById('r        eschedule_appointment_id').value; const date = rescheduleDateInput.value; const time = rescheduleTimeSelect.value;
                if (!date || !time) { Swal.fire('Error', 'Please select both date and time', 'error'); return; }
                // Get salon slug from URL                 const salonSlug = "{{ app()->bound('current_salon') ? app('current_salon')->slug : '' }}";
                const submitBtn = this.querySelector('button[type="submit"]'); const originalText = submitBtn.innerHTML; submitBtn.disabled = true; submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';
                fetch(`/${salonSlug}/customer/appointments/${appointmentId}/reschedule`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ date: date, time: time }) }).then(response => response.json()).then(data => { if (data.message) { rescheduleModal.hide(); Swal.fire({ icon: 'success', title: 'Rescheduled!', text: data.message, showConfirmButton: false, timer: 1500 }).then(() => { location.reload(); }); } else { throw new Error(data.message || 'Failed to reschedule'); } }).catch(error => { Swal.fire('Error', error.message || 'Failed to reschedule appointment', 'error'); }).finally(() => { submitBtn.disabled = false; submitBtn.innerHTML = originalText; });
                                                                            });
                                                                        });
            </script>
        @endpush

        <!-- Role-specific Content (Super Admin, Salon Admin, Manager) -->
        <!-- ... (Keeping existing role-specific content blocks but wrapping them in cleaner containers if needed, 
                                                                                                                                                                                                                                                     for now assuming the abo    ve analytics block replaces the main dashboard view for these roles as per original code structure) ... -->

        <!-- NOTE: The original code had role-specific blocks below the analytics tabs. 
                                                                                                                                                                                                                                                     I will preserve them but     ensure they are styled consistently. -->

        @role('super_admin')
        <div class="row g-3 mb-4">
            <!-- System Overview -->
            <div class="col-xl-8">
                <div class="card-modern h-100">
                    <div class="widget-header widget-header-primary">
                        <i class="fas fa-server me-2"></i>System Overview
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light text-center h-100">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Total Users</div>
                                    <div class="h3 mb-0 fw-bold text-gray-800">{{ $total_users }}</div>
                                </div>
                            </div>
                            @if(\App\Helpers\ModuleHelper::staffEnabled())
                                <div class="col-md-4">
                                    <div class="p-3 border rounded bg-light text-center h-100">
                                        <div class="text-uppercase small fw-bold text-success mb-2">Active Employees</div>
                                        <div class="h3 mb-0 fw-bold text-gray-800">{{ $active_employees }}</div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light text-center h-100">
                                    <div class="text-uppercase small fw-bold text-info mb-2">Total Roles</div>
                                    <div class="h3 mb-0 fw-bold text-gray-800">{{ $total_roles }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-xl-4">
                <div class="card-modern h-100">
                    <div class="widget-header widget-header-primary">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('admin.salon-settings.index', ['salon_slug' => $salon->slug]) }}"
                                class="list-group-item list-group-item-action px-4 py-3 border-0">
                                <i class="fas fa-cog me-3 text-muted"></i>System Settings
                            </a>
                            <a href="{{ route('admin.roles.index') }}"
                                class="list-group-item list-group-item-action px-4 py-3 border-0 bg-light">
                                <i class="fas fa-user-tag me-3 text-muted"></i>Manage Roles
                            </a>
                            @if(\App\Helpers\ModuleHelper::staffEnabled())
                                <a href="{{ route('admin.employees.index') }}"
                                    class="list-group-item list-group-item-action px-4 py-3 border-0">
                                    <i class="fas fa-users me-3 text-muted"></i>Manage Employees
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endrole

        @can('salon.view')
                <div class="row g-3 mb-4">
                    <!-- Salon Overview -->
                    <div class="col-xl-8">
                        <div class="card-modern h-100">
                            <div class="widget-header widget-header-info">
                                <i class="fas fa-store me-2"></i>Salon Overview
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    @if(\App\Helpers\ModuleHelper::appointmentsEnabled())
                                        <div class="col-md-4">
                                            <div class="p-3 border rounded bg-light text-center h-100">
                                                <div class="text-uppercase small fw-bold text-info mb-2">Today's Appts</div>
                                                <div class="h3 mb-0 fw-bold text-gray-800">{{ $today_appointments }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(\App\Helpers\ModuleHelper::servicesEnabled())
                                        <div class="col-md-4">
                                            <div class="p-3 border rounded bg-light text-center h-100">
                                                <div class="text-uppercase small fw-bold text-primary mb-2">Total Services</div>
                                                <div class="h3 mb-0 fw-bold text-gray-800">{{ $total_services }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(\App\Helpers\ModuleHelper::customersEnabled())
                                        <div class="col-md-4">
                                            <div class="p-3 border rounded bg-light text-center h-100">
                                                <div class="text-uppercase small fw-bold text-warning mb-2">Total Customers</div>
                                                <div class="h3 mb-0 fw-bold text-gray-800">{{ $total_customers }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="col-xl-4">
                        <div class="card-modern h-100">
                            <div class="widget-header widget-header-info">
                                <i class="fas fa-bolt me-2"></i>Quick Actions
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    @can('salon.manage_settings')
                                        <a href="{{ route('admin.salon-settings.index') }}"
                                            class="list-group-item list-group-item-action px-4 py-3 border-0">
                                            <i class="fas fa-cog me-3 text-muted"></i>Salon Settings
                                        </a>
                                    @endcan
                                    @if(\App\Helpers\ModuleHelper::staffEnabled())
                                        <a href="{{ route('admin.staff.index') }}"
                                            class="list-group-item list-group-item-action px-4 py-3 border-0">
                                            <i class="fas fa-user-tie me-3 text-muted"></i>Add Staff
                                        </a>
                                    @endif
                                    @if(\App\Helpers\ModuleHelper::customersEnabled())
                                        <a href="{{ route('admin.customers.create') }}"
                                            class="list-group-item list-group-item-action px-4 py-3 border-0">
                                            <i class="fas fa-user-plus me-3 text-muted"></i>Add Customer
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subscription Usage -->
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="card-modern">
                            @can('salon.manage_settings')
                                <div class="widget-header widget-header-primary">
                                    <i class="fas fa-crown me-2"></i>Subscription Usage ({{ $subscription['name'] ?? 'N/A' }})
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        @if(isset($subscription['limits']))
                                            @php
                                                $featureMapping = [
                                                    'staff' => 'Staff Management',
                                                    'branches' => 'Multi-Branch Support',
                                                    'customers' => 'Customer Management',
                                                    'services' => 'Service Management',
                                                    'products' => 'Inventory Management',
                                                    'bookings' => 'Booking System',
                                                    'guest_bookings' => 'Booking System',
                                                    'memberships' => 'Memberships',
                                                    'packages' => 'Packages',
                                                ];
                                                $planFeatures = $subscription['features'] ?? [];
                                                // Ensure features is always an array (defensive check)
                                                if (!is_array($planFeatures)) {
                                                    $planFeatures = json_decode($planFeatures, true) ?? [];
                                                }
                                                $hasAllFeatures = in_array('All Features', $planFeatures) || in_array('All Premium Features', $planFeatures);
                                            @endphp
                                            @foreach($subscription['limits'] as $key => $limit)
                                                @php
                                                    $shouldShow = true;

                                                    // 1. Check Global Module Settings
                                                    if ($key == 'staff' && !\App\Helpers\ModuleHelper::staffEnabled())
                                                        $shouldShow = false;
                                                    if ($key == 'branches' && !\App\Helpers\ModuleHelper::branchesEnabled())
                                                        $shouldShow = false;
                                                    if ($key == 'customers' && !\App\Helpers\ModuleHelper::customersEnabled())
                                                        $shouldShow = false;
                                                    if ($key == 'services' && !\App\Helpers\ModuleHelper::servicesEnabled())
                                                        $shouldShow = false;
                                                    if ($key == 'products' && !\App\Helpers\ModuleHelper::inventoryEnabled())
                                                        $shouldShow = false;
                                                    if ($key == 'bookings' && !\App\Helpers\ModuleHelper::appointmentsEnabled())
                                                        $shouldShow = false;
                                                    if ($key == 'guest_bookings' && !\App\Helpers\ModuleHelper::appointmentsEnabled())
                                                        $shouldShow = false;
                                                    if ($key == 'memberships' && !\App\Helpers\ModuleHelper::membershipsEnabled())
                                                        $shouldShow = false;
                                                    if ($key == 'packages' && !\App\Helpers\ModuleHelper::packagesEnabled())
                                                        $shouldShow = false;

                                                    // 2. Check Plan Features
                                                    if ($shouldShow && !$hasAllFeatures && isset($featureMapping[$key])) {
                                                        if (!in_array($featureMapping[$key], $planFeatures)) {
                                                            $shouldShow = false;
                                                        }
                                                    }
                                                @endphp

                                                @if($shouldShow)
                                                    <div class="col-md-3 col-sm-6">
                                                        <div class="border rounded p-3 h-100 bg-light">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <div class="text-xs font-weight-bold text-uppercase text-muted">
                                                                    {{ $key == 'guest_bookings' ? 'Guest Bookings' : ucfirst($key) }}
                                                                </div>
                                                                @php
                                                                    $icon = match ($key) {
                                                                        'staff' => 'users',
                                                                        'branches' => 'building',
                                                                        'customers' => 'user-friends',
                                                                        'services' => 'cut',
                                                                        'products' => 'box',
                                                                        'bookings' => 'calendar-alt',
                                                                        'guest_bookings' => 'user-clock',
                                                                        'memberships' => 'id-card',
                                                                        'packages' => 'cubes',
                                                                        default => 'check-circle'
                                                                    };
                                                                @endphp
                                                                <i class="fas fa-{{ $icon }} text-gray-300"></i>
                                                            </div>
                                                            <div class="h5 mb-2 fw-bold text-gray-800">
                                                                {{ $limit['current'] }} <span class="text-muted small fw-normal">/
                                                                    {{ $limit['is_unlimited'] ? 'Unlimited' : $limit['max'] }}</span>
                                                            </div>
                                                            @if(!$limit['is_unlimited'])
                                                                @php
                                                                    $percentage = $limit['max'] > 0 ? ($limit['current'] / $limit['max']) * 100 : 0;
                                                                    $color = $percentage >= 90 ? 'danger' : ($percentage >= 75 ? 'warning' : 'success');
                                                                @endphp
                                                                <div class="progress progress-modern">
                                                                    <div class="progress-bar bg-{{ $color }}" role="progressbar"
                                                                        style="width: {{ $percentage }}%" aria-valuenow="{{ $limit['current'] }}"
                                                                        aria-valuemin="0" aria-valuemax="{{ $limit['max'] }}"></div>
                                                                </div>
                                                            @else
                                                                <div class="progress progress-modern">
                                                                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%"
                                                                        aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @endcan

    @can('bookings.view')
        <div class="row g-3 mb-4">
            <!-- Daily Overview -->
            <div class="col-xl-8">
                <div class="card-modern h-100">
                    <div class="widget-header widget-header-info">
                        <i class="fas fa-calendar-day me-2"></i>Daily Overview
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light text-center h-100">
                                    <div class="text-uppercase small fw-bold text-info mb-2">
                                        {{ $dateRange == 'today' ? "Today's" : "Selected Period" }} Tasks
                                    </div>
                                    <div class="h3 mb-0 fw-bold text-gray-800">{{ $today_tasks }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light text-center h-100">
                                    <div class="text-uppercase small fw-bold text-success mb-2">Completed</div>
                                    <div class="h3 mb-0 fw-bold text-gray-800">{{ $completed_tasks }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light text-center h-100">
                                    <div class="text-uppercase small fw-bold text-warning mb-2">Pending</div>
                                    <div class="h3 mb-0 fw-bold text-gray-800">{{ $pending_tasks }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-xl-4">
                <div class="card-modern h-100">
                    <div class="widget-header widget-header-info">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('admin.bookings.index') }}"
                                class="list-group-item list-group-item-action px-4 py-3 border-0">
                                <i class="fas fa-tasks me-3 text-muted"></i>Manage Tasks
                            </a>
                            @if(\App\Helpers\ModuleHelper::staffEnabled())
                                <a href="{{ route('admin.staff.index') }}"
                                    class="list-group-item list-group-item-action px-4 py-3 border-0">
                                    <i class="fas fa-user-tie me-3 text-muted"></i>Add Staff
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
    @endcan

    </div>

    @include('admin.bookings.partials.booking_details_modal')

    <!-- Assign Staff Modal -->
    <div class="modal fade" id="assignStaffModal" tabindex="-1" aria-labelledby="assignStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignStaffModalLabel">Assign Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="assignStaffForm">
                        <input type="hidden" id="assignBookingId" name="booking_id">

                        <div class="mb-3">
                            <label for="assignStaffId" class="form-label">Select Staff</label>
                            <select class="form-select" id="assignStaffId" name="staff_id" required>
                                <option value="">Choose staff member...</option>
                                @foreach(\App\Models\User::role('employee')->where('salon_id', auth()->user()->salon_id)->where('status', 'active')->get() as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="assignDate" class="form-label">Date</label>
                            <input type="date" class="form-control" id="assignDate" name="date" required
                                min="{{ date('Y-m-d') }}">
                        </div>

                        <div class="mb-3">
                            <label for="assignTime" class="form-label">Time</label>
                            <select class="form-select" id="assignTime" name="time" required disabled>
                                <option value="">Select date and staff first...</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmAssignBtn">Confirm Assignment</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Currency settings
            const currencySymbol = @json(currency_symbol());

            window.setDateRange = function (range) {
                const input = document.getElementById('date_range_input');
                input.value = range;
                if (range !== 'custom') {
                    document.getElementById('dateFilterForm').submit();
                } else {
                    toggleCustomRange();
                }
            }

            window.toggleCustomRange = function () {
                const integrated = document.getElementById('custom-range-integrated');
                integrated.classList.toggle('show');
                document.getElementById('date_range_input').value = 'custom';
            }
            $(document).ready(function () {
                // --- Chart Data Preparation ---

                // Revenue Trend Data
                @if(isset($monthly_revenue) && count($monthly_revenue) > 0)
                    const monthlyRevenue = @json($monthly_revenue);
                    const revenueLabels = monthlyRevenue.map(m => m.month);
                    const revenueData = monthlyRevenue.map(m => m.revenue);

                    const ctxRevenue = document.getElementById('revenueTrendChart').getContext('2d');
                    new Chart(ctxRevenue, {
                        type: 'line',
                        data: {
                            labels: revenueLabels,
                            datasets: [{
                                label: 'Total Revenue',
                                data: revenueData,
                                fill: true,
                                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                                borderColor: 'rgba(78, 115, 223, 1)',
                                pointRadius: 3,
                                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                                pointBorderColor: 'rgba(78, 115, 223, 1)',
                                pointHoverRadius: 5,
                                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                                pointHitRadius: 10,
                                pointBorderWidth: 2,
                                tension: 0.3
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            return 'Revenue: ' + currencySymbol + context.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function (value) { return currencySymbol + value; }
                                    }
                                }
                            }
                        }
                    });
                @endif

                    // Payment Methods Data
                    @if(isset($payment_methods) && count($payment_methods) > 0)
                        const paymentMethods = @json($payment_methods);
                        const paymentLabels = paymentMethods.map(p => p.name);
                        const paymentValues = paymentMethods.map(p => p.value);

                        const ctxPayment = document.getElementById('paymentMethodsChart').getContext('2d');
                        new Chart(ctxPayment, {
                            type: 'doughnut',
                            data: {
                                labels: paymentLabels,
                                datasets: [{
                                    data: paymentValues,
                                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
                                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                                }]
                            },
                            options: {
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { position: 'bottom' },
                                    tooltip: {
                                        callbacks: {
                                            label: function (context) {
                                                let label = context.label || '';
                                                if (label) { label += ': '; }
                                                label += currencySymbol + context.parsed.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                                return label;
                                            }
                                        }
                                    }
                                },
                                cutout: '70%',
                            }
                        });
                    @endif

                    // Top Services Data
                    @if(isset($top_services) && count($top_services) > 0)
                        const topServices = @json($top_services);
                        const serviceLabels = topServices.map(s => s.name);
                        const serviceRevenue = topServices.map(s => s.value);

                        const ctxServices = document.getElementById('topServicesChart').getContext('2d');
                        new Chart(ctxServices, {
                            type: 'bar',
                            data: {
                                labels: serviceLabels,
                                datasets: [{
                                    label: 'Revenue',
                                    data: serviceRevenue,
                                    backgroundColor: '#36b9cc',
                                    borderRadius: 5
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        callbacks: {
                                            label: function (context) {
                                                return 'Revenue: ' + currencySymbol + context.parsed.x.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function (value) { return currencySymbol + value; }
                                        }
                                    }
                                }
                            }
                        });
                    @endif

                    // Staff Performance Data
                    @if(isset($staff_performance) && count($staff_performance) > 0)
                        const staffStats = @json($staff_performance);
                        const staffLabels = staffStats.map(s => s.name);
                        const staffRevenueData = staffStats.map(s => s.revenue);

                        const ctxStaff = document.getElementById('staffPerformanceChart').getContext('2d');
                        new Chart(ctxStaff, {
                            type: 'bar',
                            data: {
                                labels: staffLabels,
                                datasets: [{
                                    label: 'Revenue Generated',
                                    data: staffRevenueData,
                                    backgroundColor: '#4e73df',
                                    borderRadius: 5,
                                    barPercentage: 0.5
                                }]
                            },
                            options: {
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        callbacks: {
                                            label: function (context) {
                                                return 'Revenue: ' + currencySymbol + context.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function (value) { return currencySymbol + value; }
                                        }
                                    }
                                }
                            }
                        });
                    @endif
                // --- Assign Staff Functionality ---
                let assignModal = new bootstrap.Modal(document.getElementById('assignStaffModal'));

                // Open Modal
                $(document).on('click', '.assign-btn', function () {
                    const bookingId = $(this).data('id');
                    const bookingDate = $(this).data('date');

                    $('#assignBookingId').val(bookingId);
                    $('#assignStaffId').val('');
                    $('#assignDate').val(bookingDate); // Pre-fill date
                    $('#assignTime').html('<option value="">Select staff first...</option>').prop('disabled', true);
                    assignModal.show();
                });

                // Fetch Available Slots
                function fetchSlots() {
                    const staffId = $('#assignStaffId').val();
                    const date = $('#assignDate').val();

                    if (staffId && date) {
                        $('#assignTime').html('<option value="">Loading...</option>').prop('disabled', true);

                        $.ajax({
                            url: "{{ route('admin.bookings.available-slots') }}",
                            method: 'GET',
                            data: { staff_id: staffId, date: date },
                            success: function (response) {
                                let options = '<option value="">Select time...</option>';
                                if (response.slots && response.slots.length > 0) {
                                    response.slots.forEach(function (slot) {
                                        // Format time for display (e.g., 2023-10-27 14:30:00 -> 14:30)
                                        const time = slot.split(' ')[1].substring(0, 5);
                                        options += `<option value="${slot}">${time}</option>`;
                                    });
                                    $('#assignTime').html(options).prop('disabled', false);
                                } else {
                                    $('#assignTime').html('<option value="">No slots available</option>');
                                }
                            },
                            error: function () {
                                $('#assignTime').html('<option value="">Error loading slots</option>');
                            }
                        });
                    }
                }

                $('#assignStaffId, #assignDate').on('change', fetchSlots);

                // Confirm Assignment
                $('#confirmAssignBtn').click(function () {
                    const bookingId = $('#assignBookingId').val();
                    const staffId = $('#assignStaffId').val();
                    const assignedTime = $('#assignTime').val(); // This is the full datetime string from the slot

                    if (!staffId || !assignedTime) {
                        alert('Please select staff and time.');
                        return;
                    }

                    const btn = $(this);
                    btn.prop('disabled', true).text('Assigning...');

                    $.ajax({
                        url: "{{ route('admin.bookings.assign', ['booking' => 0]) }}".replace('/0/assign', '/' + bookingId + '/assign'),
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            staff_id: staffId,
                            assigned_time: assignedTime
                        },
                        success: function (response) {
                            if (response.success) {
                                assignModal.hide();
                                // Reload page to show updated status
                                window.location.reload();
                            } else {
                                alert(response.message || 'Failed to assign staff.');
                                btn.prop('disabled', false).text('Confirm Assignment');
                            }
                        },
                        error: function (xhr) {
                            alert(xhr.responseJSON?.message || 'An error occurred.');
                            btn.prop('disabled', false).text('Confirm Assignment');
                        }
                    });
                });

                // Initialize Bootstrap tooltips for group bookings and multi-staff icons
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl, {
                        html: true
                    });
                });

            });
        </script>
    @endpush
@endsection