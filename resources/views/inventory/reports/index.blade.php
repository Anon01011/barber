@extends('layouts.admin')

@section('title', 'Inventory Reports')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 mb-0">Inventory Reports</h1>
                <p class="text-muted">Analyze your inventory performance and trends</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Stock Valuation -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                                <i class="fas fa-dollar-sign text-primary fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Stock Valuation</h5>
                                <small class="text-muted">Total inventory value</small>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">View the total value of your inventory based on purchase and
                            selling prices.</p>
                        <a href="{{ route('admin.inventory.reports.stock-valuation') }}"
                            class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-chart-line me-1"></i> View Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stock Movement -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                                <i class="fas fa-exchange-alt text-white fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Stock Movement</h5>
                                <small class="text-muted">In/Out transactions</small>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">Track all stock movements including purchases, adjustments, and
                            usage.</p>
                        <a href="{{ route('admin.inventory.reports.stock-movement') }}"
                            class="btn btn-success btn-sm w-100">
                            <i class="fas fa-list me-1"></i> View Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Dead Stock -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning bg-opacity-10 p-3 rounded me-3">
                                <i class="fas fa-box text-warning fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Dead Stock</h5>
                                <small class="text-muted">No movement items</small>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">Identify items with no movement in the last 90 days.</p>
                        <a href="{{ route('admin.inventory.reports.dead-stock') }}" class="btn btn-warning btn-sm w-100">
                            <i class="fas fa-exclamation-triangle me-1"></i> View Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Profit Analysis -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                                <i class="fas fa-chart-pie text-info fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Profit Analysis</h5>
                                <small class="text-muted">Margin & profitability</small>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">Analyze profit margins and identify most profitable items.</p>
                        <a href="{{ route('admin.inventory.reports.profit-analysis') }}" class="btn btn-info btn-sm w-100">
                            <i class="fas fa-percentage me-1"></i> View Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Low Stock -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-danger bg-opacity-10 p-3 rounded me-3">
                                <i class="fas fa-exclamation-circle text-danger fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Low Stock</h5>
                                <small class="text-muted">Reorder alerts</small>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">Items that need reordering based on minimum stock levels.</p>
                        <a href="{{ route('admin.inventory.reports.low-stock') }}" class="btn btn-danger btn-sm w-100">
                            <i class="fas fa-bell me-1"></i> View Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stock Alerts -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-secondary bg-opacity-10 p-3 rounded me-3">
                                <i class="fas fa-bell text-secondary fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Stock Alerts</h5>
                                <small class="text-muted">Active notifications</small>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">View and manage all active stock alerts and notifications.</p>
                        <a href="{{ route('admin.inventory.alerts.index') }}" class="btn btn-secondary btn-sm w-100">
                            <i class="fas fa-list-alt me-1"></i> View Alerts
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .hover-lift {
                transition: transform 0.2s, box-shadow 0.2s;
            }

            .hover-lift:hover {
                transform: translateY(-5px);
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            }
        </style>
    @endpush
@endsection