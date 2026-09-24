@extends('layouts.super-admin')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-900 fw-bold">Subscription Intelligence</h1>
                <p class="text-muted mb-0">Track subscriber growth, churn, and plan distribution.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.saas.reports.index') }}" class="btn btn-light border text-muted shadow-sm">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>
                <a href="{{ route('admin.saas.reports.export.subscriptions', request()->query()) }}" class="btn btn-success shadow-sm fw-medium">
                    <i class="fas fa-file-excel me-2"></i> Export Report (CSV)
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                         <label class="form-label small fw-bold text-muted">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Subscription Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Paused</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 fw-medium">
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Overview -->
        <h6 class="text-uppercase text-muted fw-bold small mb-3">Overview Metrics</h6>
        <div class="row g-3 mb-4">
            <!-- Total -->
            <div class="col-lg-2 col-md-4 col-6">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body p-3">
                         <h3 class="fw-bold text-dark mb-1">{{ number_format($stats['total_subscriptions']) }}</h3>
                         <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem;">Total</div>
                    </div>
                </div>
            </div>
            <!-- Active -->
            <div class="col-lg-2 col-md-4 col-6">
                <div class="card border-0 shadow-sm text-center h-100 border-bottom border-success border-3">
                    <div class="card-body p-3">
                         <h3 class="fw-bold text-success mb-1">{{ number_format($stats['active']) }}</h3>
                         <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem;">Active</div>
                    </div>
                </div>
            </div>
            <!-- Pending -->
            <div class="col-lg-2 col-md-4 col-6">
                 <div class="card border-0 shadow-sm text-center h-100 border-bottom border-warning border-3">
                    <div class="card-body p-3">
                         <h3 class="fw-bold text-warning mb-1">{{ number_format($stats['pending']) }}</h3>
                         <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem;">Pending</div>
                    </div>
                </div>
            </div>
            <!-- Paused -->
            <div class="col-lg-2 col-md-4 col-6">
                <div class="card border-0 shadow-sm text-center h-100 border-bottom border-info border-3">
                    <div class="card-body p-3">
                         <h3 class="fw-bold text-info mb-1">{{ number_format($stats['paused']) }}</h3>
                         <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem;">Paused</div>
                    </div>
                </div>
            </div>
            <!-- Cancelled -->
             <div class="col-lg-2 col-md-4 col-6">
                <div class="card border-0 shadow-sm text-center h-100 border-bottom border-danger border-3">
                    <div class="card-body p-3">
                         <h3 class="fw-bold text-danger mb-1">{{ number_format($stats['cancelled']) }}</h3>
                         <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem;">Cancelled</div>
                    </div>
                </div>
            </div>
            <!-- Expired -->
             <div class="col-lg-2 col-md-4 col-6">
                <div class="card border-0 shadow-sm text-center h-100 border-bottom border-secondary border-3">
                    <div class="card-body p-3">
                         <h3 class="fw-bold text-secondary mb-1">{{ number_format($stats['expired']) }}</h3>
                         <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem;">Expired</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
             <!-- Growth Chart -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="m-0 fw-bold text-gray-800">New Subscriptions Trend</h6>
                    </div>
                    <div class="table-responsive">
                         <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 text-muted small text-uppercase">Month</th>
                                    <th class="pe-4 text-end text-muted small text-uppercase">New Subscriptions</th>
                                </tr>
                            </thead>
                             <tbody>
                                @forelse($monthlySubscriptions as $month => $count)
                                    <tr>
                                        <td class="ps-4 fw-medium">{{ \Carbon\Carbon::parse($month)->format('F Y') }}</td>
                                        <td class="pe-4 text-end">
                                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3">{{ $count }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-center py-4 text-muted">No data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Distribution -->
             <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                         <h6 class="m-0 fw-bold text-gray-800">Plan Distribution</h6>
                    </div>
                     <div class="card-body">
                         @forelse($planDistribution as $plan => $count)
                             <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-medium text-dark">{{ $plan }}</span>
                                    <span class="fw-bold text-primary">{{ $count }}</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: {{ $stats['total_subscriptions'] > 0 ? ($count / $stats['total_subscriptions']) * 100 : 0 }}%">
                                    </div>
                                </div>
                                <div class="small text-muted mt-1">
                                    {{ $stats['total_subscriptions'] > 0 ? round(($count / $stats['total_subscriptions']) * 100, 1) : 0 }}% share
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4">No plan data available</p>
                        @endforelse
                     </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                 <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-gray-800">Churn Rate Analysis</h6>
                         <span class="badge bg-light text-muted border">Last 6 Months</span>
                    </div>
                     <div class="card-body">
                        <div class="alert alert-light border small text-muted mb-3">
                            <i class="fas fa-info-circle me-1 text-primary"></i> 
                            Churn rate calculates the percentage of active customers who cancelled their subscription during each month. Lower is better.
                        </div>
                        <div class="table-responsive">
                             <table class="table table-bordered mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        @foreach($churnData as $month => $rate)
                                             <th class="text-center text-muted small text-uppercase">{{ \Carbon\Carbon::parse($month)->format('M') }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                         @foreach($churnData as $month => $rate)
                                            <td class="text-center">
                                                @if($rate <= 2)
                                                    <span class="text-success fw-bold">{{ $rate }}%</span>
                                                @elseif($rate <= 5)
                                                    <span class="text-warning fw-bold">{{ $rate }}%</span>
                                                @else
                                                     <span class="text-danger fw-bold">{{ $rate }}%</span>
                                                @endif
                                            </td>
                                         @endforeach
                                    </tr>
                                </tbody>
                             </table>
                        </div>
                     </div>
                </div>
            </div>
        </div>

        <!-- Latest Subscriptions Table -->
        <h5 class="fw-bold text-gray-800 mt-5 mb-3">Recent Subscriptions</h5>
        <div class="card border-0 shadow-sm mb-5">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                     <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted small text-uppercase">Salon</th>
                            <th class="py-3 text-muted small text-uppercase">Plan</th>
                            <th class="py-3 text-muted small text-uppercase">Status</th>
                            <th class="py-3 text-muted small text-uppercase">Started</th>
                            <th class="pe-4 py-3 text-end text-muted small text-uppercase">Ends On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions->take(10) as $sub)
                            <tr>
                                <td class="ps-4 fw-medium text-dark">{{ $sub->salon->name ?? 'Deleted Salon' }}</td>
                                <td>{{ $sub->plan->name ?? 'Unknown Plan' }}</td>
                                <td>
                                    @if($sub->status === 'active')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Active</span>
                                    @elseif($sub->status === 'cancelled')
                                         <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Cancelled</span>
                                    @else
                                        <span class="badge bg-light text-dark border rounded-pill">{{ ucfirst($sub->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $sub->starts_at ? $sub->starts_at->format('M d, Y') : '-' }}</td>
                                <td class="pe-4 text-end text-muted small">{{ $sub->ends_at ? $sub->ends_at->format('M d, Y') : 'Never' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No recent subscriptions</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
