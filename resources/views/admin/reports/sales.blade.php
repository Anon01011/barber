@extends('layouts.app')

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
        }

        .x-small {
            font-size: 0.75rem !important;
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

        .avatar-xs {
            width: 32px;
            height: 32px;
            line-height: 32px;
            font-size: 0.8rem;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800 fw-bold">
                    <i class="fas fa-chart-line text-primary me-2"></i>Sales Report
                    @if(isset($branch) && $branch)
                        <span class="badge bg-primary text-white ms-2"
                            style="font-size: 0.5em; vertical-align: middle;">{{ $branch->name }}</span>
                    @endif
                </h1>
                <p class="text-muted small mb-0 mt-1">Overview of your salon's financial performance</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-success btn-sm shadow-sm" id="exportSalesBtn">
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
                <form method="GET" action="{{ route('admin.reports.sales') }}" id="filterForm"
                    class="row g-3 align-items-start">
                    <!-- Date Period -->
                    <div class="col-xl-auto col-md-12 pe-xl-4 pe-0">
                        <label class="form-label small fw-bold text-uppercase text-muted mb-1">Date Period</label>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <div style="min-width: 160px; max-width: 200px;">
                                <select class="form-select" id="range" name="range">
                                    <option value="today" {{ $range == 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="yesterday" {{ $range == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                                    <option value="this_week" {{ $range == 'this_week' ? 'selected' : '' }}>This Week</option>
                                    <option value="last_week" {{ $range == 'last_week' ? 'selected' : '' }}>Last Week</option>
                                    <option value="this_month" {{ $range == 'this_month' ? 'selected' : '' }}>This Month
                                    </option>
                                    <option value="last_month" {{ $range == 'last_month' ? 'selected' : '' }}>Last Month
                                    </option>
                                    <option value="3_months" {{ $range == '3_months' ? 'selected' : '' }}>Last 3 Months
                                    </option>
                                    <option value="6_months" {{ $range == '6_months' ? 'selected' : '' }}>Last 6 Months
                                    </option>
                                    <option value="this_year" {{ $range == 'this_year' ? 'selected' : '' }}>This Year</option>
                                    <option value="custom" {{ $range == 'custom' ? 'selected' : '' }}>Custom Range</option>
                                </select>
                            </div>

                            <!-- Custom Dates side-by-side -->
                            <div class="date-inputs" style="{{ $range !== 'custom' ? 'display: none;' : '' }}">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                        value="{{ $startDate }}" title="From Date" style="max-width: 140px;">
                                    <span class="text-muted small px-1">to</span>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                        value="{{ $endDate }}" title="To Date" style="max-width: 140px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Primary Filters -->
                    <div class="col-xl-2 col-md-4 col-sm-6" style="max-width: 200px;">
                        <label for="staff_id" class="form-label small fw-bold text-uppercase text-muted mb-1">Staff</label>
                        <select class="form-select" id="staff_id" name="staff_id">
                            <option value="">All Staff</option>
                            @foreach($staffMembers as $s)
                                <option value="{{ $s->id }}" {{ $staffId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xl-2 col-md-4 col-sm-6" style="max-width: 200px;">
                        <label for="source" class="form-label small fw-bold text-uppercase text-muted mb-1">Source</label>
                        <select class="form-select" id="source" name="source">
                            <option value="all" {{ $source == 'all' ? 'selected' : '' }}>All Sources</option>
                            <option value="bookings" {{ $source == 'bookings' ? 'selected' : '' }}>Bookings Only</option>
                            <option value="pos" {{ $source == 'pos' ? 'selected' : '' }}>POS Sales Only</option>
                        </select>
                    </div>

                    <div class="col-xl-auto col-md-12 col-sm-12 ms-xl-auto mt-xl-0 mt-3 align-self-end">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-premium shadow-sm px-4">
                                <i class="fas fa-filter me-2"></i>Apply
                            </button>
                            <button type="button" class="btn btn-light border shadow-sm" data-bs-toggle="collapse"
                                data-bs-target="#advancedFilters" aria-expanded="false" title="Advanced Filters">
                                <i class="fas fa-sliders-h text-primary"></i>
                            </button>
                            <a href="{{ route('admin.reports.sales') }}" class="btn btn-light border shadow-sm"
                                title="Reset Filters">
                                <i class="fas fa-undo text-secondary"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Advanced Filters Collapsed -->
                    <div class="collapse w-100 mt-2" id="advancedFilters">
                        <div class="p-3 bg-light rounded-4 border mt-2">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="customer_id"
                                        class="form-label small fw-bold text-uppercase text-muted mb-1">Customer</label>
                                    <select class="form-select select2" id="customer_id" name="customer_id">
                                        <option value="">All Customers</option>
                                        @foreach($customers as $c)
                                            <option value="{{ $c->id }}" {{ $customerId == $c->id ? 'selected' : '' }}>
                                                {{ $c->name }} ({{ $c->phone }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="payment_status"
                                        class="form-label small fw-bold text-uppercase text-muted mb-1">Payment
                                        Status</label>
                                    <select class="form-select" id="payment_status" name="payment_status">
                                        <option value="">All Status</option>
                                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid
                                        </option>
                                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>
                                            Unpaid</option>
                                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="payment_method"
                                        class="form-label small fw-bold text-uppercase text-muted mb-1">Payment
                                        Method</label>
                                    <select class="form-select" id="payment_method" name="payment_method">
                                        <option value="">All Methods</option>
                                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash
                                        </option>
                                        <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card
                                        </option>
                                        <option value="online" {{ request('payment_method') == 'online' ? 'selected' : '' }}>
                                            Online</option>
                                        <option value="package" {{ request('payment_method') == 'package' ? 'selected' : '' }}>Package</option>
                                        <option value="other" {{ request('payment_method') == 'other' ? 'selected' : '' }}>
                                            Other</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="cashier_id"
                                        class="form-label small fw-bold text-uppercase text-muted mb-1">Cashier/Staff</label>
                                    <select class="form-select select2" id="cashier_id" name="cashier_id">
                                        <option value="">All Staff</option>
                                        @foreach($staffMembers as $s)
                                            <option value="{{ $s->id }}" {{ $cashierId == $s->id ? 'selected' : '' }}>
                                                {{ $s->name }}
                                            </option>
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
        <div class="row mb-4 g-2 flex-nowrap overflow-auto pb-2">
            <!-- Total Revenue -->
            <div class="col-xl col-md-4" style="min-width: 180px;">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase x-small fw-bold text-success mb-1">Total Revenue</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">
                                    {{ currency_symbol() }}{{ number_format($totalRevenue, 2) }}
                                </div>
                                @if($source == 'all')
                                    <div class="x-small text-muted mt-1 d-flex flex-wrap gap-2">
                                        <span><i class="fas fa-circle text-info" style="font-size: 6px;"></i> B:
                                            {{ currency_symbol() }}{{ number_format($bookingRevenue, 2) }}</span>
                                        <span><i class="fas fa-circle text-warning" style="font-size: 6px;"></i> P:
                                            {{ currency_symbol() }}{{ number_format($posRevenue, 2) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="rounded-circle bg-success bg-opacity-10 p-2">
                                <i class="fas fa-dollar-sign fa-lg text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unpaid / Outstanding -->
            <div class="col-xl col-md-4" style="min-width: 180px;">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase x-small fw-bold text-danger mb-1">Unpaid / Due</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">
                                    {{ currency_symbol() }}{{ number_format($totalUnpaid, 2) }}
                                </div>
                                <div class="x-small text-muted mt-1">
                                    Outstanding
                                </div>
                            </div>
                            <div class="rounded-circle bg-danger bg-opacity-10 p-2">
                                <i class="fas fa-exclamation-circle fa-lg text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Refunded (NEW) -->
            <div class="col-xl col-md-4" style="min-width: 180px;">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-secondary">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase x-small fw-bold text-secondary mb-1">Refunded</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">
                                    {{ currency_symbol() }}{{ number_format($totalRefunded ?? 0, 2) }}
                                </div>
                                <div class="x-small text-muted mt-1 d-flex flex-wrap gap-2">
                                    <span title="Net Returned to Customer">
                                        <i class="fas fa-hand-holding-usd me-1"></i>Net:
                                        {{ currency_symbol() }}{{ number_format(($totalRefunded ?? 0) - ($posRefundFees ?? 0), 2) }}
                                    </span>
                                    @if(($posRefundFees ?? 0) > 0)
                                        <span class="text-danger" title="Total Fees Retained">
                                            <i class="fas fa-cut me-1"></i>Fees:
                                            {{ currency_symbol() }}{{ number_format($posRefundFees, 2) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="rounded-circle bg-secondary bg-opacity-10 p-2">
                                <i class="fas fa-undo fa-lg text-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Tips -->
            <div class="col-xl col-md-4" style="min-width: 180px;">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase x-small fw-bold text-primary mb-1">Total Tips</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">
                                    {{ currency_symbol() }}{{ number_format($totalTips, 2) }}
                                </div>
                                <div class="x-small text-muted mt-1">
                                    Customer Gratitude
                                </div>
                            </div>
                            <div class="rounded-circle bg-primary bg-opacity-10 p-2">
                                <i class="fas fa-coins fa-lg text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Net Revenue -->
            <div class="col-xl col-md-4" style="min-width: 180px;">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase x-small fw-bold text-info mb-1">Net Revenue</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">
                                    {{ currency_symbol() }}{{ number_format($netRevenue, 2) }}
                                </div>
                                <div class="x-small text-muted mt-1">
                                    <span class="text-danger"><i class="fas fa-minus-circle me-1"></i>Tax:
                                        {{ currency_symbol() }}{{ number_format($totalTax, 2) }}</span>
                                </div>
                            </div>
                            <div class="rounded-circle bg-info bg-opacity-10 p-2">
                                <i class="fas fa-wallet fa-lg text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commission Cost -->
            <div class="col-xl col-md-4" style="min-width: 180px;">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase x-small fw-bold text-warning mb-1">Commissions</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">
                                    {{ currency_symbol() }}{{ number_format($totalCommission, 2) }}
                                </div>
                                <div class="x-small text-muted mt-1">
                                    Staff Payouts
                                </div>
                            </div>
                            <div class="rounded-circle bg-warning bg-opacity-10 p-2">
                                <i class="fas fa-hand-holding-usd fa-lg text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions -->
            <div class="col-xl col-md-4" style="min-width: 180px;">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase x-small fw-bold text-primary mb-1">Transactions</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $totalTransactions }}</div>
                                <div class="x-small text-muted mt-1">
                                    Avg: {{ currency_symbol() }}{{ number_format($averageTransaction, 2) }}
                                </div>
                            </div>
                            <div class="rounded-circle bg-primary bg-opacity-10 p-2">
                                <i class="fas fa-receipt fa-lg text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Transactions Table -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list me-2"></i>Transaction Details
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 rounded-start">Date</th>
                                <th class="border-0">Source</th>
                                <th class="border-0">Customer</th>
                                <th class="border-0">Items/Service</th>
                                <th class="border-0">Staff</th>
                                <th class="border-0">Payment</th>
                                <th class="border-0 text-end">Amount</th>
                                <th class="border-0 text-end">Tip</th>
                                <th class="border-0 text-center">Status</th>
                                <th class="border-0 rounded-end text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paginatedTransactions as $transaction)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ format_date($transaction['date']) }}</div>
                                        <div class="small text-muted">{{ format_time($transaction['date']) }}</div>
                                    </td>
                                    <td>
                                        @if($transaction['type'] === 'booking')
                                            @if(!empty($transaction['booking_group_id']))
                                                <span class="badge bg-info bg-opacity-10 text-info">Grouped Booking</span>
                                            @else
                                                <span class="badge bg-info bg-opacity-10 text-info">Booking</span>
                                            @endif
                                        @elseif($transaction['type'] === 'pos' && ($transaction['subtype'] ?? '') === 'booking')
                                            <span class="badge bg-primary bg-opacity-10 text-primary">POS Booking</span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning">POS Sale</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">
                                            {{ $transaction['customer'] ? $transaction['customer']->name : ($transaction['type'] === 'booking' ? 'Guest' : 'Walk-in') }}
                                        </div>
                                        @if($transaction['customer'] && isset($transaction['customer']->is_guest) && $transaction['customer']->is_guest)
                                            <span class="badge bg-secondary" style="font-size: 0.6em;">Guest</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction['type'] === 'booking')
                                            @if(!empty($transaction['booking_group_id']))
                                                <small class="text-muted italic">Multiple Services</small>
                                            @else
                                                {{ $transaction['data']->service ? $transaction['data']->service->name : 'N/A' }}
                                            @endif
                                        @else
                                            <small class="text-muted">
                                                {{ $transaction['data']->items->count() }} item(s)
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction['staff'])
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle {{ $transaction['type'] === 'booking' ? 'bg-primary' : 'bg-success' }} text-white d-flex align-items-center justify-content-center me-2"
                                                    style="width: 24px; height: 24px; font-size: 10px;">
                                                    {{ substr($transaction['staff']->name, 0, 1) }}
                                                </div>
                                                {{ $transaction['staff']->name }}
                                            </div>
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                    <td><span
                                            class="badge bg-light text-dark border">{{ $transaction['payment_method'] ?: 'N/A' }}</span>
                                    </td>
                                    <td class="text-end fw-bold">
                                        {{ currency_symbol() }}{{ number_format($transaction['amount'], 2) }}
                                    </td>
                                    <td class="text-end text-success">
                                        {{ currency_symbol() }}{{ number_format($transaction['tip'], 2) }}
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $status = $transaction['status'] ?? '';
                                            $paymentStatus = $transaction['payment_status'] ?? '';
                                            $refundedAmount = $transaction['refunded_amount'] ?? 0;
                                            $badgeClass = 'bg-success';
                                            $labelText = ucfirst($paymentStatus);

                                            if ($status === 'cancelled' || $status === 'refunded' || $paymentStatus === 'refunded') {
                                                $badgeClass = 'bg-secondary';
                                                $labelText = 'Refunded';
                                            } elseif ($refundedAmount > 0 && $refundedAmount < $transaction['amount']) {
                                                $badgeClass = 'bg-info';
                                                $labelText = 'Partial Refund';
                                            } elseif ($paymentStatus === 'partial' || $status === 'partially_refunded' || $status === 'partial') {
                                                $badgeClass = 'bg-warning';
                                                $labelText = 'Partial';
                                            }
                                        @endphp
                                        <span
                                            class="badge {{ $badgeClass }} bg-opacity-10 text-{{ str_replace('bg-', '', $badgeClass) }} badge-premium">
                                            {{ $labelText }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary view-transaction-btn"
                                            data-type="{{ $transaction['type'] }}" data-id="{{ $transaction['id'] }}">
                                            <i class="fas fa-eye me-1"></i>View
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <p class="mb-0">No transactions found for the selected period</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        Showing {{ $paginatedTransactions->firstItem() ?? 0 }} to
                        {{ $paginatedTransactions->lastItem() ?? 0 }} of {{ $paginatedTransactions->total() }} transactions
                    </div>
                    <div>
                        {{ $paginatedTransactions->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1: Daily Revenue -->
        <div class="row mb-4">
            <div class="col-12">

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-area me-2"></i>Daily Revenue Trend
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area" style="height: 350px;">
                            <canvas id="dailyRevenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2: Payment Methods & Top Services -->
        <div class="row mb-4">
            <!-- Payment Methods -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-credit-card me-2"></i>Payment Methods
                        </h6>
                        <span class="badge bg-primary rounded-pill">
                            {{ currency_symbol() }}{{ number_format($totalRevenue, 2) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2" style="height: 250px;">
                            <canvas id="paymentMethodsChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small">
                            @foreach($paymentMethods as $method)
                                <span class="me-3 d-inline-block">
                                    <i class="fas fa-circle"
                                        style="color: {{ $loop->iteration == 1 ? '#4e73df' : ($loop->iteration == 2 ? '#1cc88a' : '#36b9cc') }}"></i>
                                    {{ $method['method'] }}
                                    <span
                                        class="fw-bold text-gray-800 ms-1">{{ currency_symbol() }}{{ number_format($method['total'], 2) }}</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Services/Items -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-star me-2"></i>Top Performers
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($source != 'bookings')
                                <div class="col-12">
                                    <h6 class="text-center small font-weight-bold text-uppercase text-muted mb-3">Top Items
                                    </h6>
                                    <div class="chart-bar" style="height: 250px;">
                                        <canvas id="topPosItemsChart"></canvas>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 3: Staff Performance & Category Revenue -->
        <div class="row mb-4">
            <!-- Staff Performance -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-users me-2"></i>Staff Performance
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-bar" style="height: 300px;">
                            <canvas id="staffPerformanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue by Category -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-pie me-2"></i>Revenue by Category
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2" style="height: 250px;">
                            <canvas id="categoryRevenueChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small">
                            @foreach($categoryRevenue as $category => $revenue)
                                <span class="me-2">
                                    <i class="fas fa-circle"
                                        style="color: {{ $loop->iteration == 1 ? '#4e73df' : ($loop->iteration == 2 ? '#1cc88a' : ($loop->iteration == 3 ? '#36b9cc' : '#f6c23e')) }}"></i>
                                    {{ $category }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 4: Hourly Traffic & Customer Metrics -->
        <div class="row mb-4">
            <!-- Hourly Traffic -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-clock me-2"></i>Hourly Traffic (Sales Count)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area" style="height: 300px;">
                            <canvas id="hourlyTrafficChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Metrics -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-user-friends me-2"></i>Customer Metrics
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="p-3 border rounded bg-light text-center">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">New Customers
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $customerMetrics['new'] }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 border rounded bg-light text-center">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Returning
                                        Customers</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $customerMetrics['returning'] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 border rounded bg-light text-center">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Unique
                                        Customers</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $customerMetrics['total'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Transaction Details Modal -->
        <div class="modal fade" id="transactionDetailsModal" tabindex="-1" aria-labelledby="transactionDetailsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="transactionDetailsModalLabel">
                            <i class="fas fa-receipt me-2"></i>Transaction Details
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="transactionDetailsContent">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
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
                // Define currency symbol for use in                        charts
                const currencySymbol = '{{ currency_symbol() }}';

                // View Transaction Details
                $('.view-transaction-btn').on('click', function () {
                    const type = $(this).data('type');
                    const id = $(this).data('id');

                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('transactionDetailsModal'));
                    modal.show();

                    // Load transaction details
                    $('#transactionDetailsContent').html(`
                                                                                                                                                                                                                                                                                <div class="text-center py-5">
                                                                                                                                                                                                                                                                                    <div class="spinner-border text-primary" role="status">
                                                                                                                                                                                                                                                                                        <span class="visually-hidden">Loading...</span>
                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                     `);

                    $.ajax({
                        url: `/{{ auth()->user()->salon->slug }}/admin/reports/transaction/${type}/${id}`,
                        method: 'GET',
                        success: function (response) {
                            renderTransactionDetails(response);
                        },
                        error: function () {
                            $('#transactionDetailsContent').html(`
                                                                                                                                                                                                                                                                                        <div class="alert alert-danger">
                                                                                                                                                                                                                                                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                                                                                                                                                                                                                                                            Failed to load transaction details. Please try again.
                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                    `);
                        }
                    });
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

                function renderTransactionDetails(data) {
                    const transaction = data.transaction;
                    const items = data.items;
                    const commissions = data.commissions;
                    const isBooking = data.type === 'booking';
                    const isGrouped = data.is_grouped;
                    const isPosBooking = data.type === 'pos' && data.subtype === 'booking';

                    let html = `
                                                                                                                                                                                                                                                                                <!-- Transaction Header -->
                                                                                                                                                                                                                                                                                <div class="card mb-3 border-0 bg-light">
                                                                                                                                                                                                                                                                                    <div class="card-body">
                                                                                                                                                                                                                                                                                        <div class="row">
                                                                                                                                                                                                                                                                                            <div class="col-md-6">
                                                                                                                                                                                                                                                                                                <h6 class="text-muted small text-uppercase mb-2">Transaction Type</h6>
                                                                                                                                                                                                                                                                                                <span class="badge ${isBooking ? 'bg-info' : (isPosBooking ? 'bg-primary' : 'bg-warning')} mb-3">
                                                                                                                                                                                                                                                                                                    ${isBooking ? (isGrouped ? 'Grouped Booking' : 'Booking') : (isPosBooking ? 'POS Booking' : 'POS Sale')}
                                                                                                                                                                                                                                                                                                </span>
                                                                                                                                                                                                                                                                                                <h6 class="text-muted small text-uppercase mb-2 mt-2">Transaction ID</h6>
                                                                                                                                                                                                                                                                                                <p class="mb-0 fw-bold">#${transaction.id}</p>
                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                            <div class="col-md-6 text-md-end">
                                                                                                                                                                                                                                                                                                <h6 class="text-muted small text-uppercase mb-2">Date & Time</h6>
                                                                                                                                                                                                                                                                                                <p class="mb-0">${new Date(isBooking ? transaction.start_time : transaction.created_at).toLocaleString()}</p>
                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                </div>

                                                                                                                                                                                                                                                                                <!-- Customer & Staff Info -->
                                                                                                                                                                                                                                                                                <div class="row mb-3">
                                                                                                                                                                                                                                                                                    <div class="col-md-6">
                                                                                                                                                                                                                                                                                        <div class="card h-100">
                                                                                                                                                                                                                                                                                            <div class="card-body">
                                                                                                                                                                                                                                                                                                <h6 class="card-title text-primary mb-3">
                                                                                                                                                                                                                                                                                                    <i class="fas fa-user me-2"></i>Customer Information
                                                                                                                                                                                                                                                                                                </h6>
                                                                                                                                                                                                                                                                                                <p class="mb-1"><strong>Name:</strong> ${transaction.customer ? transaction.customer.name : (isBooking ? 'Guest' : 'Walk-in')}</p>
                                                                                                                                                                                                                                                                                                ${transaction.customer && transaction.customer.phone ? `<p class="mb-1"><strong>Phone:</strong> ${transaction.customer.phone}</p>` : ''}
                                                                                                                                                                                                                                                                                                ${transaction.customer && transaction.customer.email ? `<p class="mb-0"><strong>Email:</strong> ${transaction.customer.email}</p>` : ''}
                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                    <div class="col-md-6">
                                                                                                                                                                                                                                                                                        <div class="card h-100">
                                                                                                                                                                                                                                                                                            <div class="card-body">
                                                                                                                                                                                                                                                                                                <h6 class="card-title text-primary mb-3">
                                                                                                                                                                                                                                                                                                    <i class="fas fa-user-tie me-2"></i>Staff Information
                                                                                                                                                                                                                                                                                                </h6>
                                                                                                                                                                                                                                                                                                <p class="mb-0"><strong>Name:</strong> ${transaction.staff ? transaction.staff.name : (transaction.employee ? transaction.employee.name : 'Unassigned')}</p>
                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                </div>

                                                                                                                                                                                                                                                                                <!-- Items/Services -->
                                                                                                                                                                                                                                                                                <div class="card mb-3">
                                                                                                                                                                                                                                                                                    <div class="card-body">
                                                                                                                                                                                                                                                                                        <h6 class="card-title text-primary mb-3">
                                                                                                                                                                                                                                                                                            <i class="fas fa-shopping-cart me-2"></i>Items/Services
                                                                                                                                                                                                                                                                                        </h6>
                                                                                                                                                                                                                                                                                        <div class="table-responsive">
                                                                                                                                                                                                                                                                                            <table class="table table-sm">
                                                                                                                                                                                                                                                                                                <thead>
                                                                                                                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                                                                                                                        <th>Item</th>
                                                                                                                                                                                                                                                                                                        <th>Type</th>
                                                                                                                                                                                                                                                                                                        <th>Staff</th>
                                                                                                                                                                                                                                                                                                        <th class="text-center">Qty</th>
                                                                                                                                                                                                                                                                                                        <th class="text-end">Price</th>
                                                                                                                                                                                                                                                                                                        <th class="text-end">Total</th>
                                                                                                                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                                                                                                                </thead>
                                                                                                                                                                                                                                                                                                <tbody>
                                                                                                                                                                                                                                                                            `;

                    items.forEach(item => {
                        html += `
                                                                                                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                                                                                                        <td>${item.name}</td>
                                                                                                                                                                                                                                                                                        <td><span class="badge bg-secondary">${item.type}</span></td>
                                                                                                                                                                                                                                                                                        <td>${item.staff_name || 'Unassigned'}</td>
                                                                                                                                                                                                                                                                                        <td class="text-center">${item.quantity}</td>
                                                                                                                                                                                                                                                                                        <td class="text-end">${currencySymbol}${parseFloat(item.price).toFixed(2)}</td>
                                                                                                                                                                                                                                                                                        <td class="text-end fw-bold">${currencySymbol}${parseFloat(item.total).toFixed(2)}</td>
                                                                                                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                                                                                                `;
                    });

                    html += `
                                                                                                                                                                                                                                                                                                </tbody>
                                                                                                                                                                                                                                                                                            </table>
                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                </div>

                                                                                                                                                                                                                                                                                <!-- Payment Summary -->
                                                                                                                                                                                                                                                                                <div class="card mb-3">
                                                                                                                                                                                                                                                                                    <div class="card-body">
                                                                                                                                                                                                                                                                                        <h6 class="card-title text-primary mb-3">
                                                                                                                                                                                                                                                                                            <i class="fas fa-money-bill-wave me-2"></i>Payment Summary
                                                                                                                                                                                                                                                                                        </h6>
                                                                                                                                                                                                                                                                                        <table class="table table-sm table-borderless">
                                                                                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                                                                                <td>Subtotal:</td>
                                                                                                                                                                                                                                                                                                <td class="text-end">${currencySymbol}${parseFloat(isBooking ? (data.total_amount || transaction.amount) : transaction.subtotal).toFixed(2)}</td>
                                                                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                                                            `;

                    if (!isBooking || (data.total_tip && parseFloat(data.total_tip) > 0)) {
                        html += `
                                                                                                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                                                                                                        <td>Tax:</td>
                                                                                                                                                                                                                                                                                        <td class="text-end">${currencySymbol}${parseFloat(isBooking ? 0 : (transaction.tax || 0)).toFixed(2)}</td>
                                                                                                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                                                                                                        <td>Discount:</td>
                                                                                                                                                                                                                                                                                        <td class="text-end text-danger">-${currencySymbol}${parseFloat(isBooking ? 0 : (transaction.discount || 0)).toFixed(2)}</td>
                                                                                                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                                                                                                        <td>Tip:</td>
                                                                                                                                                                                                                                                                                        <td class="text-end">${currencySymbol}${parseFloat(isBooking ? (data.total_tip || 0) : (transaction.tip || 0)).toFixed(2)}</td>
                                                                                                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                                                                                                `;
                    }

                    html += `
                                                                                                                                                                                                                                                                                            <tr class="border-top">
                                                                                                                                                                                                                                                                                                <td class="fw-bold">Total:</td>
                                                                                                                                                                                                                                                                                                <td class="text-end fw-bold fs-5">${currencySymbol}${parseFloat(isBooking ? (data.total_amount || transaction.amount) : transaction.total).toFixed(2)}</td>
                                                                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                                                                                <td>Payment Method:</td>
                                                                                                                                                                                                                                                                                                <td class="text-end"><span class="badge bg-light text-dark border">${transaction.payment_method || 'N/A'}</span></td>
                                                                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                                                                        </table>
                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                            `;

                    // Commission Details
                    if (commissions && commissions.length > 0) {
                        html += `
                                                                                                                                                                                                                                                                                    <div class="card mb-3">
                                                                                                                                                                                                                                                                                        <div class="card-body">
                                                                                                                                                                                                                                                                                            <h6 class="card-title text-primary mb-3">
                                                                                                                                                                                                                                                                                                <i class="fas fa-percentage me-2"></i>Commission Details
                                                                                                                                                                                                                                                                                            </h6>
                                                                                                                                                                                                                                                                                            <div class="table-responsive">
                                                                                                                                                                                                                                                                                                <table class="table table-sm">
                                                                                                                                                                                                                                                                                                    <thead>
                                                                                                                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                                                                                                                            <th>Staff</th>
                                                                                                                                                                                                                                                                                                            <th>Type</th>
                                                                                                                                                                                                                                                                                                            <th class="text-end">Amount</th>
                                                                                                                                                                                                                                                                                                            <th class="text-center">Status</th>
                                                                                                                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                                                                                                                    </thead>
                                                                                                                                                                                                                                                                                                    <tbody>
                                                                                                                                                                                                                                                                                `;

                        commissions.forEach(comm => {
                            html += `
                                                                                                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                                                                                                            <td>${comm.staff ? comm.staff.name : 'N/A'}</td>
                                                                                                                                                                                                                                                                                            <td><span class="badge bg-secondary">${comm.item_type || 'Service'}</span></td>
                                                                                                                                                                                                                                                                                            <td class="text-end">${currencySymbol}${parseFloat(comm.commission_amount).toFixed(2)}</td>
                                                                                                                                                                                                                                                                                            <td class="text-center"><span class="badge bg-${comm.status === 'paid' ? 'success' : 'warning'}">${comm.status || 'pending'}</span></td>
                                                                                                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                                                                                                    `;
                        });

                        html += `
                                                                                                                                                                                                                                                                                                    </tbody>
                                                                                                                                                                                                                                                                                                </table>
                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                `;
                    }

                    // Notes
                    if (transaction.notes) {
                        html += `
                                                                                                                                                                                                                                                                                    <div class="card">
                                                                                                                                                                                                                                                                                        <div class="card-body">
                                                                                                                                                                                                                                                                                            <h6 class="card-title text-primary mb-2">
                                                                                                                                                                                                                                                                                                <i class="fas fa-sticky-note me-2"></i>Notes
                                                                                                                                                                                                                                                                                            </h6>
                                                                                                                                                                                                                                                                                            <p class="mb-0 text-muted">${transaction.notes}</p>
                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                `;
                    }

                    $('#transactionDetailsContent').html(html);
                }

                // --- Chart Data Preparation ---

                // Daily Revenue Data
                const dailyData = @json($dailyRevenue);
                const dates = Object.keys(dailyData);
                const bookingRevenue = Object.values(dailyData).map(d => d.booking_revenue);
                const posRevenue = Object.values(dailyData).map(d => d.pos_revenue);
                const totalRevenue = Object.values(dailyData).map(d => d.revenue);
                const totalTips = Object.values(dailyData).map(d => d.tip);

                // Payment Methods Data
                const paymentMethods = @json($paymentMethods);
                const paymentLabels = paymentMethods.map(p => p.method);
                const paymentValues = paymentMethods.map(p => p.total);

                // Top Services Data
                const topServices = @json($serviceBreakdown);
                const serviceLabels = topServices.map(s => s.service);
                const serviceRevenue = topServices.map(s => s.revenue);

                // Top POS Items Data
                const topPosItems = @json($posItemsBreakdown);
                const posItemLabels = topPosItems.map(i => i.item);
                const posItemRevenue = topPosItems.map(i => i.revenue);

                // Staff Performance Data
                const staffStats = @json($staffRevenue);
                const staffLabels = staffStats.map(s => s.staff);
                const staffRevenueData = staffStats.map(s => s.revenue);


                // --- Chart Initialization ---

                // 1. Daily Revenue Chart (Line)
                const ctxDaily = document.getElementById('dailyRevenueChart').getContext('2d');
                new Chart(ctxDaily, {
                    type: 'line',
                    data: {
                        labels: dates,
                        datasets: [
                            {
                                label: 'Total Revenue',
                                data: totalRevenue,
                                borderColor: '#4e73df',
                                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'Total Tips',
                                data: totalTips,
                                borderColor: '#1cc88a',
                                backgroundColor: 'rgba(28, 200, 138, 0.05)',
                                tension: 0.3,
                                fill: true,
                                hidden: true
                            },
                            @if($source == 'all')
                                                                                                                                                                                                                                                                                                                                                                                            {
                                    label: 'Bookings',
                                    data: bookingRevenue,
                                    borderColor: '#36b9cc',
                                    borderDash: [5, 5],
                                    tension: 0.3,
                                    fill: false,
                                    hidden: true
                                },
                                {
                                    label: 'POS',
                                    data: posRevenue,
                                    borderColor: '#f6c23e',
                                    borderDash: [5, 5],
                                    tension: 0.3,
                                    fill: false,
                                    hidden: true
                                }
                            @endif
                                                                                                                                                                                                                                                                                ]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function (context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += currencySymbol + new Intl.NumberFormat('en-US').format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function (value) {
                                        return currencySymbol + value;
                                    }
                                }
                            }
                        }
                    }
                });

                // 2. Payment Methods Chart (Doughnut)
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
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += currencySymbol + new Intl.NumberFormat('en-US').format(context.parsed);
                                        return label;
                                    }
                                }
                            }
                        },
                        cutout: '70%',
                    }
                });

                // 3. Top Services Chart Removed


                // 4. Top POS Items Chart (Horizontal Bar)
                @if($source != 'bookings')
                    const ctxPos = document.getElementById('topPosItemsChart').getContext('2d');
                    new Chart(ctxPos, {
                        type: 'bar',
                        data: {
                            labels: posItemLabels,
                            datasets: [{
                                label: 'Revenue',
                                data: posItemRevenue,
                                backgroundColor: '#f6c23e',
                                borderRadius: 5
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
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

                                                                                                                                                                                                                                                                        // 5. Staff Performance Chart (Bar)
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
                                        return 'Revenue: ' + currencySymbol + new Intl.NumberFormat('en-US').format(context.parsed.y);
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

                // 6. Category Revenue Chart (Doughnut)
                const categoryRevenueData = @json($categoryRevenue);
                const categoryLabels = Object.keys(categoryRevenueData);
                const categoryValues = Object.values(categoryRevenueData);

                const ctxCategory = document.getElementById('categoryRevenueChart').getContext('2d');
                new Chart(ctxCategory, {
                    type: 'doughnut',
                    data: {
                        labels: categoryLabels,
                        datasets: [{
                            data: categoryValues,
                            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                            hoverBorderColor: "rgba(234, 236, 244, 1)",
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += currencySymbol + new Intl.NumberFormat('en-US').format(context.parsed);
                                        return label;
                                    }
                                }
                            }
                        },
                        cutout: '70%',
                    }
                });

                // 7. Hourly Traffic Chart (Line/Area)
                const hourlyTrafficData = @json($hourlyTraffic);
                const hourlyLabels = Object.keys(hourlyTrafficData).map(h => h + ':00');
                const hourlyValues = Object.values(hourlyTrafficData);

                const ctxHourly = document.getElementById('hourlyTrafficChart').getContext('2d');
                new Chart(ctxHourly, {
                    type: 'line',
                    data: {
                        labels: hourlyLabels,
                        datasets: [{
                            label: 'Transactions',
                            data: hourlyValues,
                            borderColor: '#4e73df',
                            backgroundColor: 'rgba(78, 115, 223, 0.05)',
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });

                // Export Sales Report
                document.getElementById('exportSalesBtn').addEventListener('click', function () {
                    const startDate = document.getElementById('start_date').value;
                    const endDate = document.getElementById('end_date').value;
                    const source = document.getElementById('source').value;
                    const branchId = document.getElementById('branch_id').value;
                    const staffId = document.getElementById('staff_id').value;
                    const customerId = document.getElementById('customer_id').value;
                    const paymentMethod = document.getElementById('payment_method').value;
                    const paymentStatus = document.getElementById('payment_status').value;

                    const url = new URL('{{ route("admin.reports.sales.export") }}', window.location.origin);
                    if (startDate) url.searchParams.append('start_date', startDate);
                    if (endDate) url.searchParams.append('end_date', endDate);
                    if (source) url.searchParams.append('source', source);
                    if (branchId) url.searchParams.append('branch_id', branchId);
                    if (staffId) url.searchParams.append('staff_id', staffId);
                    if (customerId) url.searchParams.append('customer_id', customerId);
                    if (paymentMethod) url.searchParams.append('payment_method', paymentMethod);
                    if (paymentStatus) url.searchParams.append('payment_status', paymentStatus);

                    window.location.href = url.toString();
                });
            });
        </script>
    @endpush
@endsection