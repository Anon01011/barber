@extends('layouts.super-admin')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-5">
            <div>
                <h1 class="h3 mb-1 text-gray-900 fw-bold">Analytics Dashboard</h1>
                <p class="text-muted mb-0">Overview of platform performance and financial health.</p>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-light text-dark border px-3 py-2">
                    <i class="far fa-calendar-alt me-2"></i> {{ now()->format('F Y') }}
                </span>
            </div>
        </div>

        <!-- Executive Summary Cards -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body position-relative">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-success-subtle text-success p-3 rounded-circle">
                                <i class="fas fa-dollar-sign fa-lg"></i>
                            </div>
                        </div>
                        <h6 class="text-muted text-uppercase fw-bold small mb-1">Total Revenue</h6>
                        <h2 class="fw-bold text-dark mb-0">
                            {{ system_currency_symbol() }}{{ number_format($totalRevenue, 2) }}</h2>
                        <div class="mt-2 small text-muted">Lifetime earnings</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body position-relative">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                                <i class="fas fa-chart-line fa-lg"></i>
                            </div>
                        </div>
                        <h6 class="text-muted text-uppercase fw-bold small mb-1">Revenue (This Month)</h6>
                        <h2 class="fw-bold text-dark mb-0">
                            {{ system_currency_symbol() }}{{ number_format($thisMonthRevenue, 2) }}</h2>
                        <div class="mt-2 small d-flex align-items-center">
                            @if($revenueGrowth > 0)
                                <span class="text-success fw-bold me-2"><i class="fas fa-arrow-up"></i>
                                    {{ number_format($revenueGrowth, 1) }}%</span>
                                <span class="text-muted">vs last month</span>
                            @elseif($revenueGrowth < 0)
                                <span class="text-danger fw-bold me-2"><i class="fas fa-arrow-down"></i>
                                    {{ number_format(abs($revenueGrowth), 1) }}%</span>
                                <span class="text-muted">vs last month</span>
                            @else
                                <span class="text-muted">No change vs last month</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body position-relative">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-info-subtle text-info p-3 rounded-circle">
                                <i class="fas fa-crown fa-lg"></i>
                            </div>
                        </div>
                        <h6 class="text-muted text-uppercase fw-bold small mb-1">Active Plans</h6>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($activeSubscriptions) }}</h2>
                        <div class="mt-2 small text-muted">Current active subscriptions</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body position-relative">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-warning-subtle text-warning p-3 rounded-circle">
                                <i class="fas fa-store fa-lg"></i>
                            </div>
                        </div>
                        <h6 class="text-muted text-uppercase fw-bold small mb-1">Total Workspaces</h6>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($totalSalons) }}</h2>
                        <div class="mt-2 small text-muted">Registered salons</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Reports Section -->
        <h5 class="fw-bold text-gray-800 mb-4">Detailed Reports</h5>

        <div class="row g-4 mb-5">
            <!-- Revenue Report Card -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100 hover-lift transition-all">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <div class="icon-shape icon-lg bg-primary-subtle text-primary rounded-3 text-center"
                                    style="width: 50px; height: 50px; line-height: 50px;">
                                    <i class="fas fa-file-invoice-dollar fa-lg"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-1">Revenue Analysis</h5>
                                <p class="text-muted small mb-3">
                                    Deep dive into payment history, Monthly Recurring Revenue (MRR), and payment method
                                    breakdowns.
                                </p>
                                <a href="{{ route('admin.saas.reports.revenue') }}"
                                    class="btn btn-outline-primary btn-sm fw-medium stretched-link">
                                    View Report <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscription Report Card -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100 hover-lift transition-all">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <div class="icon-shape icon-lg bg-success-subtle text-success rounded-3 text-center"
                                    style="width: 50px; height: 50px; line-height: 50px;">
                                    <i class="fas fa-users-cog fa-lg"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-1">Subscription Metrics</h5>
                                <p class="text-muted small mb-3">
                                    Track subscriber growth, churn rates, and plan popularity over time.
                                </p>
                                <a href="{{ route('admin.saas.reports.subscriptions') }}"
                                    class="btn btn-outline-success btn-sm fw-medium stretched-link">
                                    View Report <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access / Exports -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="m-0 fw-bold text-gray-800"><i class="fas fa-download me-2 text-muted"></i> Data Export</h6>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <p class="mb-md-0 text-muted small">
                            Download raw data for external analysis in Excel or other tools. Generates CSV files including
                            all historical records.
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="btn-group shadow-sm">
                            <a href="{{ route('admin.saas.reports.export.revenue') }}"
                                class="btn btn-white border text-dark fw-medium">
                                <i class="fas fa-file-csv me-2 text-success"></i> Revenue.csv
                            </a>
                            <a href="{{ route('admin.saas.reports.export.subscriptions') }}"
                                class="btn btn-white border text-dark fw-medium">
                                <i class="fas fa-file-csv me-2 text-primary"></i> Subscriptions.csv
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('styles')
        <style>
            .hover-lift {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .hover-lift:hover {
                transform: translateY(-5px);
                box-shadow: 0 1rem 3rem rgba(0, 0, 0, .175) !important;
            }

            .bg-success-subtle {
                background-color: #d1e7dd;
            }

            .bg-primary-subtle {
                background-color: #cfe2ff;
            }

            .bg-info-subtle {
                background-color: #cff4fc;
            }

            .bg-warning-subtle {
                background-color: #fff3cd;
            }

            .btn-white {
                background-color: #ffffff;
            }

            .btn-white:hover {
                background-color: #f8f9fa;
            }
        </style>
    @endpush
@endsection