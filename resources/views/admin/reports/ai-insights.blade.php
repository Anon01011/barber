@extends('layouts.app')

@section('title', 'AI & ML Analytics Report - ' . ($salon->name ?? 'Salon'))

@push('styles')
<style>
    .ai-hero-card {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
        color: #ffffff;
        border-radius: 20px;
        padding: 26px 30px;
        box-shadow: 0 10px 30px rgba(49, 46, 129, 0.15);
    }
    .ai-badge-pro {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 5px 12px;
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        letter-spacing: 0.5px;
    }
    .stat-glass-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 22px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .stat-glass-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.06);
        border-color: #cbd5e1;
    }
    .metric-value {
        font-size: 1.85rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    .churn-badge-high {
        background-color: #fee2e2;
        color: #991b1b;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.8rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fas fa-chart-line text-indigo-600"></i> AI & Machine Learning Forecast Report
                <span class="ai-badge-pro bg-primary text-white">REAL-TIME ML</span>
            </h1>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">
                Machine learning demand predictions, customer churn risk analysis & inventory stockout forecasting for <strong>{{ $salon->name }}</strong>
            </p>
        </div>
        <div>
            <button onclick="window.location.reload();" class="btn btn-outline-primary rounded-pill px-4 fw-semibold shadow-sm">
                <i class="fas fa-sync-alt me-2"></i>Refresh Forecast Data
            </button>
        </div>
    </div>

    <!-- AI Executive Summary Banner -->
    <div class="ai-hero-card mb-4">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-indigo-600 text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; background: #4f46e5;">
                <i class="fas fa-brain fa-lg"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-white">Executive AI Operational Summary</h5>
                <small class="text-indigo-200" style="opacity: 0.85;">Synthesized database ledgers as of {{ $aiData['generated_at'] }}</small>
            </div>
        </div>
        <div class="row g-3">
            @foreach($aiData['executive_summary'] as $bullet)
                <div class="col-md-6">
                    <div class="p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10 text-white" style="backdrop-filter: blur(10px); font-size: 0.92rem; line-height: 1.55;">
                        {!! Str::markdown($bullet) !!}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Top Key Performance Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-glass-card">
                <span class="text-muted fw-semibold small text-uppercase">30-Day Revenue Forecast</span>
                <div class="metric-value text-indigo-700 mt-2" style="color: #4338ca;">
                    {{ $currencySymbol }}{{ number_format($aiData['forecast']['projected_30day_revenue'], 2) }}
                </div>
                <div class="small mt-1 text-{{ $aiData['forecast']['growth_rate_trend'] >= 0 ? 'success' : 'danger' }} fw-medium">
                    <i class="fas fa-arrow-{{ $aiData['forecast']['growth_rate_trend'] >= 0 ? 'up' : 'down' }} me-1"></i>
                    {{ $aiData['forecast']['growth_rate_trend'] }}% Trend Velocity
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-glass-card">
                <span class="text-muted fw-semibold small text-uppercase">30-Day Booking Forecast</span>
                <div class="metric-value text-purple mt-2" style="color: #8b5cf6;">
                    {{ number_format($aiData['forecast']['projected_30day_bookings']) }} Bookings
                </div>
                <div class="small text-muted mt-1">Avg ~{{ $aiData['forecast']['daily_forecast'][0]['predicted_bookings'] ?? 0 }} bookings/day</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-glass-card">
                <span class="text-muted fw-semibold small text-uppercase">Customer Churn Rate</span>
                <div class="metric-value text-danger mt-2">
                    {{ $aiData['churn_risk']['churn_rate_percentage'] }}%
                </div>
                <div class="small text-danger mt-1 fw-medium">
                    <i class="fas fa-exclamation-triangle me-1"></i>{{ $aiData['churn_risk']['high_risk_count'] }} High-Risk Customers
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-glass-card">
                <span class="text-muted fw-semibold small text-uppercase">Salon Staff Occupancy</span>
                <div class="metric-value text-success mt-2">
                    {{ $aiData['staff_yield']['avg_salon_occupancy'] }}%
                </div>
                <div class="small text-success mt-1 fw-medium">
                    <i class="fas fa-clock me-1"></i>Busiest: {{ $aiData['peak_hours']['busiest_hour'] }}
                </div>
            </div>
        </div>
    </div>

    <!-- Demand Chart & Peak Demand Hours Row -->
    <div class="row g-4 mb-4">
        <!-- 30-Day Demand Forecast Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: #ffffff;">
                <div class="card-header bg-white border-bottom border-slate-100 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-chart-line text-indigo-600 me-2"></i>30-Day Machine Learning Revenue Forecast
                    </h5>
                    <span class="badge bg-indigo-50 text-indigo-700 fw-semibold px-3 py-1 rounded-pill" style="background: #e0e7ff; color: #4338ca;">
                        Predictive Trend
                    </span>
                </div>
                <div class="card-body p-4">
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="forecastChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Peak Slot Demand Distribution -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: #ffffff;">
                <div class="card-header bg-white border-bottom border-slate-100 py-3 px-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-clock text-warning me-2"></i>Peak Slot Demand Matrix
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <small class="text-muted fw-semibold">Yield Strategy Recommendation:</small>
                        <div class="p-3 rounded-3 bg-warning-subtle text-warning-emphasis small mt-1 border border-warning-subtle">
                            <i class="fas fa-lightbulb me-1"></i> {{ $aiData['peak_hours']['yield_recommendation'] }}
                        </div>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach(array_slice($aiData['peak_hours']['hourly_data'], 0, 7) as $slot)
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0 border-bottom border-slate-100">
                                <span class="fw-medium text-dark">{{ $slot['hour_label'] }}</span>
                                <div>
                                    <span class="badge {{ $slot['is_peak'] ? 'bg-danger text-white' : 'bg-light text-dark border' }} me-2">
                                        {{ $slot['count'] }} bookings
                                    </span>
                                    @if($slot['is_peak'])
                                        <span class="badge bg-danger-subtle text-danger">PEAK</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer CRM Segmentation & Service Performance Row -->
    <div class="row g-4 mb-4">
        <!-- Service Performance Matrix -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: #ffffff;">
                <div class="card-header bg-white border-bottom border-slate-100 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-cut text-indigo-600 me-2"></i>Top Service Category Revenue Performance
                    </h5>
                    <a href="{{ route('admin.services.categories.index', ['salon_slug' => $salon->slug]) }}" class="btn btn-sm btn-outline-indigo rounded-pill px-3">
                        Manage Services
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Service Name</th>
                                    <th>Unit Price</th>
                                    <th>Completed Bookings</th>
                                    <th class="pe-4 text-end">Total Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($aiData['service_matrix'] ?? []) as $service)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">{{ $service['name'] }}</div>
                                        </td>
                                        <td class="fw-semibold text-slate-700">
                                            {{ $currencySymbol }}{{ number_format($service['price'], 2) }}
                                        </td>
                                        <td>
                                            <span class="badge bg-indigo-50 text-indigo-700 rounded-pill px-3 py-1" style="background: #e0e7ff; color: #4338ca;">
                                                {{ $service['completed_bookings'] }} bookings
                                            </span>
                                        </td>
                                        <td class="pe-4 text-end fw-bold text-dark">
                                            {{ $currencySymbol }}{{ number_format($service['total_revenue'], 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No completed service bookings recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer CRM Segmentation -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: #ffffff;">
                <div class="card-header bg-white border-bottom border-slate-100 py-3 px-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-users text-primary me-2"></i>Customer CRM Segmentation
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 rounded-3 border bg-light">
                                <small class="text-muted fw-semibold d-block">Total CRM Clients</small>
                                <span class="h4 fw-bold text-dark mb-0">{{ number_format($aiData['customer_segmentation']['total_customers'] ?? 0) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3 border bg-warning-subtle text-warning-emphasis">
                                <small class="fw-semibold d-block">VIP Spenders ($500+)</small>
                                <span class="h4 fw-bold mb-0">{{ number_format($aiData['customer_segmentation']['vip_count'] ?? 0) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3 border bg-success-subtle text-success-emphasis">
                                <small class="fw-semibold d-block">Regular Repeaters</small>
                                <span class="h4 fw-bold mb-0">{{ number_format($aiData['customer_segmentation']['regular_count'] ?? 0) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3 border bg-info-subtle text-info-emphasis">
                                <small class="fw-semibold d-block">New Clients (Last 30 Days)</small>
                                <span class="h4 fw-bold mb-0">{{ number_format($aiData['customer_segmentation']['new_30day_count'] ?? 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Churn Risk Watchlist & Inventory Stockout Warning Row -->
    <div class="row g-4">
        <!-- Churn Risk Watchlist -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4" style="background: #ffffff;">
                <div class="card-header bg-white border-bottom border-slate-100 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-user-shield text-danger me-2"></i>High-Value Customer Churn Watchlist
                    </h5>
                    <span class="badge bg-danger text-white rounded-pill px-3 py-1">{{ count($aiData['churn_risk']['high_risk_customers']) }} At-Risk</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Customer</th>
                                    <th>Days Idle</th>
                                    <th>Lifetime Spend</th>
                                    <th class="pe-4 text-end">Action Trigger</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($aiData['churn_risk']['high_risk_customers'] as $c)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">{{ $c['name'] }}</div>
                                            <small class="text-muted">{{ $c['phone'] }}</small>
                                        </td>
                                        <td>
                                            <span class="churn-badge-high">{{ $c['days_since_last_visit'] }} days ago</span>
                                        </td>
                                        <td class="fw-bold text-dark">
                                            {{ $currencySymbol }}{{ number_format($c['total_spent'], 2) }}
                                        </td>
                                        <td class="pe-4 text-end">
                                            <a href="{{ route('admin.ai.hub', ['salon_slug' => $salon->slug]) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold">
                                                Execute Campaign
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No high-risk churn customers identified. Customer retention is healthy!</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Stock-Out Depletion Prediction -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4" style="background: #ffffff;">
                <div class="card-header bg-white border-bottom border-slate-100 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-boxes text-info me-2"></i>Inventory Stock-Out Depletion Timeline
                    </h5>
                    <a href="{{ route('admin.ai.hub', ['salon_slug' => $salon->slug]) }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                        Draft PO
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($aiData['inventory_forecast']['items'] as $item)
                            <div class="list-group-item p-3 d-flex justify-content-between align-items-center border-0 border-bottom border-slate-100">
                                <div>
                                    <div class="fw-bold text-dark">{{ $item['name'] }}</div>
                                    <small class="text-muted">Current Stock: {{ $item['current_stock'] }} units | Reorder Threshold: {{ $item['reorder_level'] }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge {{ $item['urgency'] === 'CRITICAL' ? 'bg-danger text-white' : 'bg-warning text-dark' }} px-3 py-1 rounded-pill">
                                        ~{{ $item['estimated_days_left'] }} Days Left
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted">Zero stock-out risks predicted for current stock levels.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const forecastData = @json($aiData['forecast']['daily_forecast']);
    if (!forecastData || forecastData.length === 0) return;

    const labels = forecastData.map(item => item.date + ' (' + item.day_name + ')');
    const revenues = forecastData.map(item => item.predicted_revenue);
    const bookings = forecastData.map(item => item.predicted_bookings);

    const canvas = document.getElementById('forecastChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Predicted Revenue ({{ $currencySymbol }})',
                    data: revenues,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.08)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    yAxisID: 'y'
                },
                {
                    label: 'Predicted Bookings',
                    data: bookings,
                    borderColor: '#8b5cf6',
                    borderWidth: 2,
                    borderDash: [4, 4],
                    fill: false,
                    tension: 0.35,
                    pointRadius: 2,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        font: { size: 12, weight: '600' }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { maxTicksLimit: 10, font: { size: 11 } }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { color: '#f1f5f9' },
                    title: { display: true, text: 'Revenue ({{ $currencySymbol }})', font: { size: 12, weight: '600' } }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    title: { display: true, text: 'Bookings Count', font: { size: 12, weight: '600' } }
                }
            }
        }
    });
});
</script>
@endpush
