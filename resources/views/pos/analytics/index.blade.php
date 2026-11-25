@extends('layouts.app')

@section('title', 'Sales Analytics')

@push('styles')
    <link href="{{ asset('css/pos-analytics.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="analytics-wrapper py-3 px-2 px-md-3">
    <!-- Top Bar: Header & Date Filter Bar -->
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3 pb-2 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2">
                <div class="metric-icon-box bg-primary text-white shadow-sm">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h1 class="h4 fw-bold mb-0 text-dark">Sales Analytics</h1>
                    <span class="text-muted small">Real-time performance, revenue, tenders &amp; inventory insights</span>
                </div>
            </div>
        </div>

        <!-- Filter Presets & Date Range -->
        <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-2 flex-wrap">
            <!-- Quick Date Presets -->
            <div class="d-flex align-items-center gap-1 overflow-x-auto pb-1 pb-sm-0" id="filterPresetsContainer">
                <button type="button" class="filter-preset-btn" data-range="today">Today</button>
                <button type="button" class="filter-preset-btn" data-range="yesterday">Yesterday</button>
                <button type="button" class="filter-preset-btn" data-range="last7">7 Days</button>
                <button type="button" class="filter-preset-btn" data-range="thisMonth">This Month</button>
                <button type="button" class="filter-preset-btn" data-range="last30">30 Days</button>
            </div>

            <!-- Custom Date Range Form -->
            <form action="" method="GET" id="analyticsDateFilterForm" class="d-flex align-items-center gap-1 bg-white p-1 rounded-3 border shadow-sm">
                <div class="input-group input-group-sm" style="width: 125px;">
                    <span class="input-group-text bg-transparent border-0 px-1 text-muted" style="font-size:0.75rem;">From</span>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="form-control form-control-sm border-0 p-0" style="font-size: 0.78rem;">
                </div>
                <span class="text-muted" style="font-size:0.8rem;">&ndash;</span>
                <div class="input-group input-group-sm" style="width: 125px;">
                    <span class="input-group-text bg-transparent border-0 px-1 text-muted" style="font-size:0.75rem;">To</span>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="form-control form-control-sm border-0 p-0" style="font-size: 0.78rem;">
                </div>
                <button type="submit" class="btn btn-primary btn-sm px-2 py-1 rounded-2" title="Apply Filter">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>

    @php
        $sym = currency_symbol();
        $pmTotal   = $paymentMethodBreakdown['total_collected'] ?? 0;
        $pmRevenue = $paymentMethodBreakdown['revenue_paid'] ?? 0;
        $pmTip     = $paymentMethodBreakdown['tip_total'] ?? 0;
    @endphp

    <!-- ============================================================== -->
    <!-- SECTION 1: CORE FINANCIAL HEALTH METRICS (Unified 8-Card Grid) -->
    <!-- ============================================================== -->
    <div class="row row-cols-2 row-cols-md-4 g-2 mb-3">
        <!-- 1. Net Revenue -->
        <div class="col">
            <div class="analytics-card p-3 h-100 position-relative overflow-hidden" style="border-top: 3px solid #10b981;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="w-100">
                        <div class="analytics-metric-title text-success">Net Revenue</div>
                        <div class="analytics-metric-val text-success">{{ $sym }}{{ number_format($salesData['netRevenue'] ?? 0, 2) }}</div>
                        <div class="analytics-metric-sub">Paid Revenue &minus; Refunds</div>
                    </div>
                    <div class="metric-icon-box bg-success bg-opacity-10 text-success ms-2">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Total Invoiced -->
        <div class="col">
            <div class="analytics-card p-3 h-100 position-relative overflow-hidden" style="border-top: 3px solid #334155;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="w-100">
                        <div class="analytics-metric-title text-dark">Total Invoiced</div>
                        <div class="analytics-metric-val text-dark">{{ $sym }}{{ number_format($salesData['totalSales'] ?? 0, 2) }}</div>
                        <div class="analytics-metric-sub">{{ $salesData['totalTransactions'] ?? 0 }} total transactions</div>
                    </div>
                    <div class="metric-icon-box bg-dark bg-opacity-10 text-dark ms-2">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Total Paid / Cash Inflow -->
        <div class="col">
            <div class="analytics-card p-3 h-100 position-relative overflow-hidden" style="border-top: 3px solid #0d6efd;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="w-100">
                        <div class="analytics-metric-title text-primary">Total Paid (Inflow)</div>
                        <div class="analytics-metric-val text-primary">{{ $sym }}{{ number_format(($salesData['totalPaid'] ?? 0) + ($salesData['totalTips'] ?? 0), 2) }}</div>
                        <div class="analytics-metric-sub">Sales: {{ $sym }}{{ number_format($salesData['totalPaid'] ?? 0, 2) }} &bull; Tips: +{{ $sym }}{{ number_format($salesData['totalTips'] ?? 0, 2) }}</div>
                    </div>
                    <div class="metric-icon-box bg-primary bg-opacity-10 text-primary ms-2">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Total Outstanding -->
        <div class="col">
            <div class="analytics-card p-3 h-100 position-relative overflow-hidden" style="border-top: 3px solid #f59e0b;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="w-100">
                        <div class="analytics-metric-title text-warning">Total Outstanding</div>
                        <div class="analytics-metric-val text-warning">{{ $sym }}{{ number_format($salesData['totalUnpaid'] ?? 0, 2) }}</div>
                        <div class="analytics-metric-sub">{{ $salesData['unpaidTransactions'] ?? 0 }} unpaid/pending</div>
                    </div>
                    <div class="metric-icon-box bg-warning bg-opacity-10 text-warning ms-2">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Total Tips -->
        <div class="col">
            <div class="analytics-card p-3 h-100 position-relative overflow-hidden" style="border-top: 3px solid #06b6d4;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="w-100">
                        <div class="analytics-metric-title text-info">Total Tips</div>
                        <div class="analytics-metric-val text-info">{{ $sym }}{{ number_format($salesData['totalTips'] ?? 0, 2) }}</div>
                        <div class="analytics-metric-sub">Earned by staff</div>
                    </div>
                    <div class="metric-icon-box bg-info bg-opacity-10 text-info ms-2">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Total Discounts -->
        <div class="col">
            <div class="analytics-card p-3 h-100 position-relative overflow-hidden" style="border-top: 3px solid #8b5cf6;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="w-100">
                        <div class="analytics-metric-title" style="color: #8b5cf6;">Discounts</div>
                        <div class="analytics-metric-val" style="color: #8b5cf6;">{{ $sym }}{{ number_format($salesData['totalDiscounts'] ?? 0, 2) }}</div>
                        <div class="analytics-metric-sub">Coupons &amp; reductions</div>
                    </div>
                    <div class="metric-icon-box ms-2" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                        <i class="fas fa-tag"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. Net Refunded -->
        <div class="col">
            <div class="analytics-card p-3 h-100 position-relative overflow-hidden" style="border-top: 3px solid #ef4444;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="w-100">
                        <div class="analytics-metric-title text-danger">Net Refunded</div>
                        <div class="analytics-metric-val text-danger">{{ $sym }}{{ number_format($salesData['netRefunded'] ?? 0, 2) }}</div>
                        <div class="analytics-metric-sub">Gross refunds &minus; fees</div>
                    </div>
                    <div class="metric-icon-box bg-danger bg-opacity-10 text-danger ms-2">
                        <i class="fas fa-undo"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 8. Refund Fees -->
        <div class="col">
            <div class="analytics-card p-3 h-100 position-relative overflow-hidden" style="border-top: 3px solid #6366f1;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="w-100">
                        <div class="analytics-metric-title" style="color: #6366f1;">Refund Fees</div>
                        <div class="analytics-metric-val" style="color: #6366f1;">{{ $sym }}{{ number_format($salesData['refundFees'] ?? 0, 2) }}</div>
                        <div class="analytics-metric-sub">Retained fee revenue</div>
                    </div>
                    <div class="metric-icon-box ms-2" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                        <i class="fas fa-percentage"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- SECTION 2: COLLECTION BY PAYMENT METHOD (Tender Channels)       -->
    <!-- ============================================================== -->
    <div class="analytics-card p-3 mb-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-credit-card text-primary"></i> Collection by Payment Method
                </h6>
                <span class="text-muted small">Breakdown of cash drawer, card machines, and digital receipts</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-dark text-white rounded-pill px-3 py-1 fw-normal" style="font-size:0.75rem;">
                    Total Inflow: <strong>{{ $sym }}{{ number_format($pmTotal, 2) }}</strong> &bull; Sales: {{ $sym }}{{ number_format($pmRevenue, 2) }} &bull; Tips: {{ $sym }}{{ number_format($pmTip, 2) }}
                </span>
            </div>
        </div>

        <!-- 6 Compact Cards Grid -->
        <div class="row g-2 mb-3">
            {{-- Cash --}}
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="p-2 rounded-3 border bg-light h-100 d-flex flex-column justify-content-between" style="border-left: 3px solid #10b981 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="small fw-bold text-success"><i class="fas fa-money-bill-wave me-1"></i>Cash</span>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill" style="font-size:0.68rem;">{{ number_format($paymentMethodBreakdown['cash_pct'] ?? 0, 1) }}%</span>
                    </div>
                    <div class="h5 fw-bold text-dark mb-0">{{ $sym }}{{ number_format($paymentMethodBreakdown['cash'] ?? 0, 2) }}</div>
                    @php $cashMethod = $paymentMethodBreakdown['by_method']['cash'] ?? null; @endphp
                    <span class="text-muted" style="font-size:0.7rem;">{{ $cashMethod ? $cashMethod->cnt : 0 }} direct transactions</span>
                </div>
            </div>

            {{-- Card --}}
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="p-2 rounded-3 border bg-light h-100 d-flex flex-column justify-content-between" style="border-left: 3px solid #0d6efd !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="small fw-bold text-primary"><i class="fas fa-credit-card me-1"></i>Card</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill" style="font-size:0.68rem;">{{ number_format($paymentMethodBreakdown['card_pct'] ?? 0, 1) }}%</span>
                    </div>
                    <div class="h5 fw-bold text-dark mb-0">{{ $sym }}{{ number_format($paymentMethodBreakdown['card'] ?? 0, 2) }}</div>
                    @php $cardMethod = $paymentMethodBreakdown['by_method']['card'] ?? null; @endphp
                    <span class="text-muted" style="font-size:0.7rem;">{{ $cardMethod ? $cardMethod->cnt : 0 }} direct transactions</span>
                </div>
            </div>

            {{-- Online --}}
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="p-2 rounded-3 border bg-light h-100 d-flex flex-column justify-content-between" style="border-left: 3px solid #0dcaf0 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="small fw-bold text-info"><i class="fas fa-globe me-1"></i>Online</span>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill" style="font-size:0.68rem;">{{ number_format($paymentMethodBreakdown['online_pct'] ?? 0, 1) }}%</span>
                    </div>
                    <div class="h5 fw-bold text-dark mb-0">{{ $sym }}{{ number_format($paymentMethodBreakdown['online'] ?? 0, 2) }}</div>
                    @php $onlineMethod = $paymentMethodBreakdown['by_method']['online'] ?? null; @endphp
                    <span class="text-muted" style="font-size:0.7rem;">{{ $onlineMethod ? $onlineMethod->cnt : 0 }} direct transactions</span>
                </div>
            </div>

            {{-- Other --}}
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="p-2 rounded-3 border bg-light h-100 d-flex flex-column justify-content-between" style="border-left: 3px solid #6f42c1 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="small fw-bold" style="color: #6f42c1;"><i class="fas fa-ellipsis-h me-1"></i>Other</span>
                        <span class="badge rounded-pill" style="background: rgba(111,66,193,0.1); color:#6f42c1; font-size:0.68rem;">{{ number_format($paymentMethodBreakdown['other_pct'] ?? 0, 1) }}%</span>
                    </div>
                    <div class="h5 fw-bold text-dark mb-0">{{ $sym }}{{ number_format($paymentMethodBreakdown['other'] ?? 0, 2) }}</div>
                    @php $otherMethod = $paymentMethodBreakdown['by_method']['other'] ?? null; @endphp
                    <span class="text-muted" style="font-size:0.7rem;">{{ $otherMethod ? $otherMethod->cnt : 0 }} direct transactions</span>
                </div>
            </div>

            {{-- Tips Included --}}
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="p-2 rounded-3 border bg-light h-100 d-flex flex-column justify-content-between" style="border-left: 3px solid #20c997 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="small fw-bold" style="color: #20c997;"><i class="fas fa-hand-holding-usd me-1"></i>Tips</span>
                        <span class="badge bg-light text-secondary border rounded-pill" style="font-size:0.68rem;">Included</span>
                    </div>
                    <div class="h5 fw-bold text-dark mb-0">{{ $sym }}{{ number_format($pmTip, 2) }}</div>
                    <span class="text-muted" style="font-size:0.7rem;">Inflow tip portion</span>
                </div>
            </div>

            {{-- Total Collected --}}
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="p-2 rounded-3 border bg-dark text-white h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="small fw-bold text-white-50"><i class="fas fa-coins me-1 text-warning"></i>Total Inflow</span>
                        <span class="badge bg-white text-dark rounded-pill fw-bold" style="font-size:0.68rem;">100%</span>
                    </div>
                    <div class="h5 fw-bold text-white mb-0">{{ $sym }}{{ number_format($pmTotal, 2) }}</div>
                    <span class="text-white-50" style="font-size:0.68rem;">All payment tenders</span>
                </div>
            </div>
        </div>

        {{-- Split Payments Reconciliation Banner --}}
        @if(!empty($paymentMethodBreakdown['mixed_count']) && $paymentMethodBreakdown['mixed_count'] > 0)
            <div class="alert alert-light border py-2 px-3 mb-3 small text-muted d-flex align-items-center justify-content-between rounded-3">
                <div>
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    <span><strong>Split Payments:</strong> {{ $paymentMethodBreakdown['mixed_count'] }} transaction ({{ $sym }}{{ number_format($paymentMethodBreakdown['mixed_amount'], 2) }}) was split across Cash &amp; Card. Its split amounts are already counted in the Cash and Card totals above.</span>
                </div>
                <span class="badge bg-secondary bg-opacity-10 text-secondary fw-normal">Reconciled</span>
            </div>
        @endif

        <!-- Chart + Method Breakdown Row -->
        <div class="row g-3">
            <div class="col-12 col-lg-7">
                <div class="p-2 bg-light rounded-3 border">
                    <div class="d-flex align-items-center justify-content-between mb-2 px-2 pt-1">
                        <span class="small fw-bold text-dark"><i class="fas fa-chart-bar me-1 text-primary"></i>Tender Volume (excl. split double-counting)</span>
                        <span class="text-muted small" style="font-size:0.75rem;">Amounts by Channel</span>
                    </div>
                    <div id="paymentMethodChart" style="height: 240px;"></div>
                </div>
            </div>
            <div class="col-12 col-lg-5">
                <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-between">
                    <div>
                        <span class="small fw-bold text-dark d-block mb-3"><i class="fas fa-list-ul me-1 text-primary"></i>Collection Summary</span>
                        @php
                            $methods = [
                                ['label'=>'Cash',   'amount'=>$paymentMethodBreakdown['cash']??0,   'pct'=>$paymentMethodBreakdown['cash_pct']??0,   'color'=>'success',  'icon'=>'money-bill-wave'],
                                ['label'=>'Card',   'amount'=>$paymentMethodBreakdown['card']??0,   'pct'=>$paymentMethodBreakdown['card_pct']??0,   'color'=>'primary',  'icon'=>'credit-card'],
                                ['label'=>'Online', 'amount'=>$paymentMethodBreakdown['online']??0, 'pct'=>$paymentMethodBreakdown['online_pct']??0, 'color'=>'info',     'icon'=>'globe'],
                                ['label'=>'Other',  'amount'=>$paymentMethodBreakdown['other']??0,  'pct'=>$paymentMethodBreakdown['other_pct']??0,  'color'=>'secondary','icon'=>'ellipsis-h'],
                            ];
                        @endphp
                        @foreach($methods as $m)
                            <div class="mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-semibold text-dark"><i class="fas fa-{{ $m['icon'] }} text-{{ $m['color'] }} me-1" style="width:14px;"></i>{{ $m['label'] }}</span>
                                    <div>
                                        <span class="small fw-bold text-dark">{{ $sym }}{{ number_format($m['amount'], 2) }}</span>
                                        <span class="text-muted small ms-1" style="font-size:0.72rem;">({{ $m['pct'] }}%)</span>
                                    </div>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 3px;">
                                    <div class="progress-bar bg-{{ $m['color'] }}" role="progressbar" style="width: {{ $m['pct'] }}%;" aria-valuenow="{{ $m['pct'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-2 mt-2 border-top">
                        <div class="d-flex justify-content-between text-muted small mb-1" style="font-size:0.75rem;">
                            <span>Sales Revenue Portion:</span>
                            <span class="fw-semibold text-dark">{{ $sym }}{{ number_format($pmRevenue, 2) }}</span>
                        </div>
                        @if($pmTip > 0)
                            <div class="d-flex justify-content-between text-muted small mb-1" style="font-size:0.75rem;">
                                <span>Tips Portion:</span>
                                <span class="fw-semibold text-info">+ {{ $sym }}{{ number_format($pmTip, 2) }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between align-items-center pt-1 border-top">
                            <span class="fw-bold text-dark small">Total Inflow Collected:</span>
                            <span class="fw-bold text-dark fs-6">{{ $sym }}{{ number_format($pmTotal, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- SECTION 3: REVENUE TRENDS & PAYMENT DISTRIBUTION               -->
    <!-- ============================================================== -->
    <div class="row g-3 mb-3">
        <!-- Revenue Trends Area Chart -->
        <div class="col-12 col-lg-8">
            <div class="analytics-card chart-container-card h-100">
                <div class="analytics-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-chart-area text-primary me-2"></i>Revenue Trends</h6>
                        <span class="text-muted small">Daily, weekly, and monthly revenue flow</span>
                    </div>
                    <div class="btn-group btn-group-sm segmented-btn-group" role="group">
                        <button type="button" class="btn active" onclick="updateRevenueChart('day', this)">Day</button>
                        <button type="button" class="btn" onclick="updateRevenueChart('week', this)">Week</button>
                        <button type="button" class="btn" onclick="updateRevenueChart('month', this)">Month</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="revenueChart" style="height: 280px;"></div>
                </div>
            </div>
        </div>

        <!-- Payment Distribution Donut Chart -->
        <div class="col-12 col-lg-4">
            <div class="analytics-card chart-container-card h-100">
                <div class="analytics-card-header">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-chart-pie text-primary me-2"></i>Payment Distribution</h6>
                    <span class="text-muted small">Status breakdown of potential value</span>
                </div>
                <div class="card-body">
                    <div id="paymentDistributionChart" style="height: 230px;"></div>
                    <div class="mt-2 pt-2 border-top" style="font-size: 0.78rem;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted"><i class="fas fa-circle text-success me-1" style="font-size:0.5rem;"></i>Paid Revenue</span>
                            <span class="fw-semibold">{{ $sym }}{{ number_format($paymentAnalytics['paid_amount'] ?? 0, 2) }} <small class="text-muted">({{ number_format($paymentAnalytics['paid_percentage'] ?? 0, 1) }}%)</small></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 text-danger">
                            <span><i class="fas fa-circle text-danger me-1" style="font-size:0.5rem;"></i>Net Refunded</span>
                            <span class="fw-semibold">{{ ($paymentAnalytics['net_refunded'] ?? 0) > 0 ? '&minus;' : '' }}{{ $sym }}{{ number_format($paymentAnalytics['net_refunded'] ?? 0, 2) }} <small>({{ number_format($paymentAnalytics['refund_net_percentage'] ?? 0, 1) }}%)</small></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 text-info">
                            <span><i class="fas fa-circle text-info me-1" style="font-size:0.5rem;"></i>Tips</span>
                            <span class="fw-semibold">{{ $sym }}{{ number_format($paymentAnalytics['tip_amount'] ?? 0, 2) }} <small>({{ number_format($paymentAnalytics['tip_percentage'] ?? 0, 1) }}%)</small></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 text-warning">
                            <span><i class="fas fa-circle text-warning me-1" style="font-size:0.5rem;"></i>Unpaid</span>
                            <span class="fw-semibold">{{ $sym }}{{ number_format($paymentAnalytics['unpaid_amount'] ?? 0, 2) }} <small>({{ number_format($paymentAnalytics['unpaid_percentage'] ?? 0, 1) }}%)</small></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 text-secondary">
                            <span><i class="fas fa-circle text-secondary me-1" style="font-size:0.5rem;"></i>Discounts</span>
                            <span class="fw-semibold">{{ $sym }}{{ number_format($paymentAnalytics['discount_amount'] ?? 0, 2) }} <small>({{ number_format($paymentAnalytics['discount_percentage'] ?? 0, 1) }}%)</small></span>
                        </div>
                        <div class="d-flex justify-content-between text-muted">
                            <span><i class="fas fa-circle text-muted me-1" style="font-size:0.5rem;"></i>Voided</span>
                            <span class="fw-semibold">{{ $sym }}{{ number_format($paymentAnalytics['voided_amount'] ?? 0, 2) }} <small>({{ number_format($paymentAnalytics['voided_percentage'] ?? 0, 1) }}%)</small></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- SECTION 4: SALES BY TYPE & HOURLY SALES DISTRIBUTION           -->
    <!-- ============================================================== -->
    <div class="row g-3 mb-3">
        <!-- Sales by Type -->
        <div class="col-12 col-lg-6">
            <div class="analytics-card chart-container-card h-100">
                <div class="analytics-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-tags text-primary me-2"></i>Sales by Category</h6>
                        <span class="text-muted small">Services, Products, Packages &amp; Other</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-12 col-sm-6 text-center">
                            <div id="categoryChart" style="height: 220px;"></div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="mt-2 mt-sm-0">
                                @forelse($salesByCategory as $category)
                                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2 rounded-circle" style="width: 10px; height: 10px; background-color: {{ $loop->index < 6 ? $categoryColors[$loop->index] : '#6c757d' }};"></div>
                                            <span class="small fw-semibold text-dark">{{ $category['name'] }}</span>
                                        </div>
                                        <div class="text-end">
                                            <div class="small fw-bold text-dark">{{ $sym }}{{ number_format($category['total_revenue'] ?? 0, 2) }}</div>
                                            <span class="text-muted" style="font-size:0.7rem;">{{ $category['total_quantity'] }} items</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-3 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-1 opacity-25"></i>
                                        <p class="mb-0 small">No category sales data</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hourly Sales Distribution -->
        <div class="col-12 col-lg-6">
            <div class="analytics-card chart-container-card h-100">
                <div class="analytics-card-header">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-business-time text-primary me-2"></i>Hourly Sales Distribution</h6>
                    <span class="text-muted small">Peak traffic and revenue density by operating hour</span>
                </div>
                <div class="card-body">
                    <div id="hourlyChart" style="height: 250px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- SECTION 5: VOID ANALYSIS                                       -->
    <!-- ============================================================== -->
    <div class="analytics-card p-3 mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-2 border-bottom">
            <div>
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-ban text-warning me-2"></i>Void Analysis</h6>
                <span class="text-muted small">Audit cancelled / voided tickets</span>
            </div>
            <span class="badge bg-warning bg-opacity-25 text-dark px-3 py-1 rounded-pill fw-semibold" style="font-size:0.75rem;">
                {{ $voidAnalytics['total_voided'] }} Voided Transactions
            </span>
        </div>

        <div class="row g-3 align-items-center">
            <div class="col-12 col-md-3">
                <div class="p-3 bg-light rounded-3 border text-center">
                    <span class="analytics-metric-title text-warning d-block">Total Voided Amount</span>
                    <h4 class="fw-bold text-warning mb-0">{{ $sym }}{{ number_format($voidAnalytics['total_voided_amount'] ?? 0, 2) }}</h4>
                    <span class="text-muted small" style="font-size:0.7rem;">Loss / uncollected value</span>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="p-3 bg-light rounded-3 border text-center">
                    <span class="analytics-metric-title text-muted d-block">Average per Void</span>
                    <h4 class="fw-bold text-dark mb-0">{{ $sym }}{{ number_format($voidAnalytics['average_voided_amount'] ?? 0, 2) }}</h4>
                    <span class="text-muted small" style="font-size:0.7rem;">Mean transaction ticket size</span>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="p-2 bg-light rounded-3 border">
                    <span class="small fw-semibold text-muted d-block mb-1 px-2">Void Reasons Chart</span>
                    @if(!empty($voidAnalytics['total_voided']) && $voidAnalytics['total_voided'] > 0)
                        <div id="voidReasonsChart" style="height: 140px;"></div>
                    @else
                        <div class="d-flex flex-column align-items-center justify-content-center text-muted py-3" style="min-height: 140px;">
                            <i class="fas fa-check-circle text-success mb-2" style="font-size: 1.75rem; opacity: 0.85;"></i>
                            <span class="small fw-semibold text-dark">No Voided Transactions</span>
                            <span class="text-muted" style="font-size: 0.72rem;">All orders were completed successfully with zero cancellations</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(!$voidAnalytics['void_reasons']->isEmpty())
            <div class="mt-3 pt-2 border-top">
                <span class="small fw-bold text-muted d-block mb-2">Void Reasons Detail</span>
                <div class="table-responsive">
                    <table class="table table-sm table-hover analytics-table mb-0">
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
                                    <td class="text-end fw-semibold">{{ $reason->count }}</td>
                                    <td class="text-end fw-bold text-dark">{{ $sym }}{{ number_format($reason->total_amount ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- ============================================================== -->
    <!-- SECTION 6: TOP SELLING PRODUCTS & RECENT TRANSACTIONS          -->
    <!-- ============================================================== -->
    <div class="row g-3">
        <!-- Top Products -->
        <div class="col-12 col-lg-7">
            <div class="analytics-card h-100">
                <div class="analytics-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-trophy text-warning me-2"></i>Top Selling Products / Services</h6>
                        <span class="text-muted small">Best performing catalog items</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive analytics-table-scroll">
                        <table class="table table-hover analytics-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 45px;">#</th>
                                    <th>Item Name</th>
                                    <th class="text-end">Units Sold</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $product)
                                    <tr>
                                        <td>
                                            <span class="badge bg-light text-secondary border rounded-circle" style="width:24px;height:24px;display:inline-flex;align-items:center;justify-content:center;font-size:0.7rem;">
                                                {{ $loop->iteration }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $product->name }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill font-monospace">
                                                {{ $product->total_quantity }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold text-dark font-monospace">
                                            {{ $sym }}{{ number_format($product->total_revenue ?? 0, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2 opacity-25"></i>
                                            <p class="mb-0 small">No sales data available for this date range.</p>
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
        <div class="col-12 col-lg-5">
            <div class="analytics-card h-100">
                <div class="analytics-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-receipt text-primary me-2"></i>Recent Transactions</h6>
                        <span class="text-muted small">Latest closed sales feed</span>
                    </div>
                    <a href="{{ route('admin.pos.sales.index') }}" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size:0.75rem;">
                        View All <i class="fas fa-chevron-right ms-1" style="font-size:0.65rem;"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush analytics-table-scroll">
                        @forelse($recentTransactions as $transaction)
                            <a href="{{ route('admin.pos.sales.show', $transaction['invoice_number'] ?? $transaction['id']) }}" class="list-group-item list-group-item-action p-2 px-3 border-0 border-bottom text-decoration-none">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="metric-icon-box bg-light text-primary border me-2" style="width:32px;height:32px;font-size:0.8rem;">
                                            <i class="fas fa-receipt"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small mb-0">{{ $transaction['invoice_number'] }}</div>
                                            <span class="text-muted" style="font-size:0.7rem;"><i class="fas fa-user text-secondary me-1"></i>{{ $transaction['customer_name'] }} &bull; {{ $transaction['time_ago'] }}</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
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
                                                $labelText = 'Refund';
                                            } elseif ($paymentStatus === 'partial' || $paymentStatus === 'partial_paid') {
                                                $badgeClass = 'bg-warning';
                                                $labelText = 'Partial';
                                            }
                                        @endphp
                                        <div class="fw-bold text-dark font-monospace" style="font-size:0.88rem;">{{ $sym }}{{ number_format($transaction['total'] ?? 0, 2) }}</div>
                                        <span class="badge {{ $badgeClass }} bg-opacity-10 text-{{ str_replace('bg-', '', $badgeClass) }} rounded-pill" style="font-size:0.65rem;">
                                            {{ $labelText }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 opacity-25"></i>
                                <p class="mb-0 small">No recent transactions</p>
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
    <script>
        window.posAnalyticsConfig = {
            currencySymbol: '{{ currency_symbol() }}',
            dailyData: {!! json_encode($revenueTrends) !!},
            weeklyData: {!! json_encode($weeklySales->pluck('total', 'week')) !!},
            monthlyData: {!! json_encode($monthlySales->pluck('total', 'month')) !!},
            hourlyData: {!! json_encode($hourlySales) !!},
            paymentAnalytics: {
                paidAmount: {{ $paymentAnalytics['paid_amount'] ?? 0 }},
                netRefunded: {{ $paymentAnalytics['net_refunded'] ?? 0 }},
                tipAmount: {{ $paymentAnalytics['tip_amount'] ?? 0 }},
                unpaidAmount: {{ $paymentAnalytics['unpaid_amount'] ?? 0 }},
                discountAmount: {{ $paymentAnalytics['discount_amount'] ?? 0 }},
                voidedAmount: {{ $paymentAnalytics['voided_amount'] ?? 0 }}
            },
            categoryData: @json($salesByCategory),
            categoryColors: @json($categoryColors),
            paymentMethodData: {
                cash: {{ number_format($paymentMethodBreakdown['cash'] ?? 0, 2, '.', '') }},
                card: {{ number_format($paymentMethodBreakdown['card'] ?? 0, 2, '.', '') }},
                online: {{ number_format($paymentMethodBreakdown['online'] ?? 0, 2, '.', '') }},
                other: {{ number_format($paymentMethodBreakdown['other'] ?? 0, 2, '.', '') }}
            },
            voidReasonsData: @json($voidAnalytics['void_reasons']),
            voidReasonsLabels: @json($voidAnalytics['void_reasons']->pluck('void_reason')->map(fn($r) => $r ?: 'No reason')),
            voidReasonsCounts: @json($voidAnalytics['void_reasons']->pluck('count'))
        };
    </script>
    <script src="{{ asset('js/pos-analytics.js') }}"></script>
@endpush
