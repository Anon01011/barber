@extends('layouts.app')

@section('title', 'Subscription & Plans')

@section('content')
<div class="container-fluid py-3 px-2 px-md-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-2 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2">
                <div class="p-2 rounded-3 text-white shadow-sm" style="background: var(--theme-primary, #0d6efd);">
                    <i class="fas fa-crown fa-lg"></i>
                </div>
                <div>
                    <h1 class="h4 fw-bold mb-0 text-dark">Salon Subscription &amp; Plans</h1>
                    <span class="text-muted small">Manage your billing tier, feature allocations, and subscription renewal</span>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.saas.subscription.history', ['salon_slug' => request()->route('salon_slug')]) }}"
                class="btn btn-outline-secondary btn-sm px-3 shadow-sm rounded-3">
                <i class="fas fa-receipt me-1"></i> Billing History &amp; Invoices
            </a>
        </div>
    </div>

    <!-- Pending Payment Alert Banner -->
    @if(isset($pendingPayment) && $pendingPayment)
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="border-left: 5px solid #f59e0b !important; background: #fffdf5;">
            <div class="card-body p-3 p-md-4 d-flex align-items-start gap-3">
                <div class="p-3 rounded-circle bg-warning bg-opacity-20 text-warning-emphasis flex-shrink-0">
                    <i class="fas fa-hourglass-half fa-lg"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                        <h5 class="fw-bold mb-0 text-dark">Payment Approval Pending</h5>
                        <span class="badge bg-warning text-dark font-monospace px-3 py-1 rounded-pill">Status: Under Review</span>
                    </div>
                    <p class="text-muted mb-2 small">
                        You submitted a <strong>{{ ucfirst($pendingPayment->metadata['type'] ?? 'subscription') }}</strong> request for
                        <strong class="text-dark">{{ system_currency_symbol() }}{{ number_format($pendingPayment->amount, 2) }}</strong>
                        via <span class="badge bg-light text-dark border">{{ ucfirst($pendingPayment->payment_method) }}</span>.
                        Our platform administrators are currently reviewing the transfer. Your plan will activate automatically upon verification.
                    </p>
                    <div class="small text-muted font-monospace" style="font-size: 0.75rem;">
                        <i class="fas fa-tag me-1"></i> Reference ID: {{ $pendingPayment->transaction_id }} &bull; Submitted: {{ $pendingPayment->created_at->format('M d, Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Current Subscription Hero Card -->
    @if($subscription)
        @php
            $daysRemaining = $subscription->daysUntilExpiration();
            if ($daysRemaining === null && $salon->trial_ends_at) {
                $daysRemaining = (int) now()->diffInDays($salon->trial_ends_at, false);
                $isTrial = true;
            } else {
                $daysRemaining = $daysRemaining !== null ? (int) $daysRemaining : null;
                $isTrial = false;
            }

            $isNearExpiry = $daysRemaining !== null && $daysRemaining <= 7 && $daysRemaining >= 0;
            $isExpired = $daysRemaining !== null && $daysRemaining < 0;
        @endphp

        <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden position-relative current-sub-hero"
             style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff;">
            <!-- Decorative Background Accent -->
            <div class="sub-hero-glow position-absolute" style="top: -60px; right: -60px; width: 280px; height: 280px; background: radial-gradient(circle, rgba(13, 110, 253, 0.3) 0%, rgba(0,0,0,0) 70%); border-radius: 50%; pointer-events: none;"></div>

            <div class="card-body p-4 p-md-5 position-relative" style="z-index: 2;">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge px-3 py-1 rounded-pill fw-semibold" style="background: rgba(255, 255, 255, 0.15); font-size: 0.75rem; letter-spacing: 0.04em;">
                                <i class="fas fa-circle-check text-success me-1"></i> CURRENT ACTIVE PLAN
                            </span>
                            @if($subscription->status === 'cancelled')
                                <span class="badge bg-danger rounded-pill px-3 py-1">Cancels at period end</span>
                            @endif
                        </div>

                        <h2 class="display-6 fw-bold mb-2 text-white">{{ $subscription->plan->name }}</h2>
                        <p class="text-white-50 mb-3" style="max-width: 650px;">
                            {{ $subscription->plan->description ?? 'Full enterprise feature suite enabled for your salon workflow.' }}
                        </p>

                        <div class="d-flex flex-wrap align-items-center gap-3 pt-2 border-top border-white border-opacity-10">
                            <!-- Expiry Countdown Pill -->
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-calendar-alt text-white-50"></i>
                                <span class="small text-white-50">Valid Until:</span>
                                <strong class="text-white small">
                                    {{ $subscription->ends_at ? $subscription->ends_at->format('M d, Y') : ($salon->trial_ends_at ? $salon->trial_ends_at->format('M d, Y') : 'Ongoing') }}
                                </strong>
                            </div>

                            <div class="vr bg-white opacity-25 d-none d-sm-block" style="height: 18px;"></div>

                            <!-- Remaining Days Status -->
                            <div>
                                @if($daysRemaining !== null)
                                    @if($daysRemaining > 7)
                                        <span class="badge rounded-pill px-3 py-1" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3);">
                                            <i class="fas fa-clock me-1"></i> {{ $daysRemaining }} {{ Str::plural('day', $daysRemaining) }} remaining
                                        </span>
                                    @elseif($daysRemaining > 0)
                                        <span class="badge rounded-pill px-3 py-1" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3);">
                                            <i class="fas fa-exclamation-circle me-1"></i> Expires in {{ $daysRemaining }} {{ Str::plural('day', $daysRemaining) }}
                                        </span>
                                    @elseif($daysRemaining == 0)
                                        <span class="badge bg-danger rounded-pill px-3 py-1">
                                            <i class="fas fa-bell me-1"></i> Expires Today
                                        </span>
                                    @else
                                        <span class="badge bg-danger rounded-pill px-3 py-1">
                                            <i class="fas fa-times-circle me-1"></i> Expired
                                        </span>
                                    @endif
                                @endif
                            </div>

                            <div class="vr bg-white opacity-25 d-none d-sm-block" style="height: 18px;"></div>

                            <!-- Cost Pill -->
                            <div class="d-flex align-items-center gap-1">
                                <span class="small text-white-50">Investment:</span>
                                <strong class="text-white small">{{ system_currency_symbol() }}{{ number_format($subscription->plan->price, 0) }} / {{ $subscription->plan->duration_in_days }} days</strong>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 text-lg-end">
                        <div class="d-inline-flex flex-column flex-sm-row flex-lg-column gap-2 w-100 justify-content-lg-end" style="max-width: 280px;">
                            <button type="button" class="btn btn-light btn-lg fw-bold px-4 py-2 text-dark shadow-sm rounded-3"
                                onclick="document.getElementById('available-plans').scrollIntoView({behavior: 'smooth'})"
                                @if(isset($pendingPayment) && $pendingPayment) disabled @endif>
                                <i class="fas fa-arrow-up text-primary me-2"></i> Change / Upgrade Plan
                            </button>

                            <button type="button" class="btn btn-outline-light btn-lg fw-semibold px-4 py-2 rounded-3"
                                data-bs-toggle="modal" data-bs-target="#renewModal"
                                @if(isset($pendingPayment) && $pendingPayment) disabled @endif>
                                <i class="fas fa-sync-alt me-2"></i> Renew Current Plan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif($salon->trial_ends_at)
        <!-- Trial Period Hero Banner -->
        @php
            $trialDays = now()->diffInDays($salon->trial_ends_at, false);
        @endphp
        <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden"
             style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%); color: #ffffff;">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge shadow-none px-3 py-1 rounded-pill mb-2" style="background: rgba(255, 255, 255, 0.2) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.3) !important;">
                            <i class="fas fa-gift me-1"></i> FREE TRIAL PERIOD
                        </span>
                        <h2 class="display-6 fw-bold mb-2">Welcome to Your Free Trial</h2>
                        <p class="text-white-50 mb-3">
                            You currently have full access to explore the salon management platform.
                            @if($trialDays > 0)
                                Your trial will conclude in <strong class="text-white">{{ $trialDays }} {{ Str::plural('day', $trialDays) }}</strong> on {{ $salon->trial_ends_at->format('M d, Y') }}.
                            @else
                                Your trial has expired. Subscribe below to continue uninterrupted access.
                            @endif
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <button type="button" class="btn btn-light btn-lg fw-bold px-4 py-2 text-primary shadow-sm rounded-3"
                            onclick="document.getElementById('available-plans').scrollIntoView({behavior: 'smooth'})"
                            @if(isset($pendingPayment) && $pendingPayment) disabled @endif>
                            <i class="fas fa-arrow-up me-2"></i> Select Subscription Plan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Active Subscription Hero -->
        <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden"
             style="background: linear-gradient(135deg, #334155 0%, #1e293b 100%); color: #ffffff;">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-50 px-3 py-1 rounded-pill mb-2">
                            <i class="fas fa-exclamation-triangle me-1"></i> NO ACTIVE SUBSCRIPTION
                        </span>
                        <h2 class="display-6 fw-bold mb-2">Activate Your Salon Subscription</h2>
                        <p class="text-white-50 mb-0">
                            Choose an enterprise plan below to unlock automated POS checkouts, inventory tracking, staff commissions, and appointment scheduling.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <button type="button" class="btn btn-primary btn-lg fw-bold px-4 py-2 text-white shadow-sm rounded-3"
                            onclick="document.getElementById('available-plans').scrollIntoView({behavior: 'smooth'})"
                            @if(isset($pendingPayment) && $pendingPayment) disabled @endif>
                            <i class="fas fa-rocket me-2"></i> View Pricing Plans
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Available Plans Section -->
    <div id="available-plans" class="mb-4">
        <div class="text-center mb-4 pb-2">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                TRANSPARENT PRICING
            </span>
            <h3 class="fw-bold text-dark mt-2 mb-1">Tailored Plans for Every Salon &amp; Barber</h3>
            <p class="text-muted small mb-0">Upgrade or switch anytime. All plans include automated database security and cloud backups.</p>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($plans as $plan)
                @php
                    $isCurrentPlan = $subscription && $subscription->plan_id == $plan->id;
                    $features = $plan->features;
                    if (is_string($features)) {
                        $features = json_decode($features, true) ?? [];
                    }
                    $features = is_array($features) ? $features : [];
                    $visibleFeatures = array_slice($features, 0, 6);
                    $hiddenFeatures = array_slice($features, 6);

                    $limits = [
                        ['icon' => 'fas fa-users text-primary', 'label' => 'Staff', 'value' => $plan->max_users],
                        ['icon' => 'fas fa-store text-info', 'label' => 'Branches', 'value' => $plan->max_branches],
                        ['icon' => 'fas fa-user-friends text-secondary', 'label' => 'Customers', 'value' => $plan->limits['max_customers'] ?? 0],
                        ['icon' => 'fas fa-cut text-warning', 'label' => 'Services', 'value' => $plan->limits['max_services'] ?? 0],
                        ['icon' => 'fas fa-box text-success', 'label' => 'Products', 'value' => $plan->limits['max_products'] ?? 0],
                        ['icon' => 'fas fa-gift text-purple', 'label' => 'Packages', 'value' => $plan->limits['max_packages'] ?? 0],
                        ['icon' => 'fas fa-id-card text-teal', 'label' => 'Memberships', 'value' => $plan->limits['max_memberships'] ?? 0],
                        ['icon' => 'fas fa-calendar-check text-danger', 'label' => 'Bookings', 'value' => $plan->limits['max_bookings_per_month'] ?? 0],
                    ];
                @endphp

                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 enterprise-pricing-card position-relative {{ $plan->is_popular ? 'popular-card' : '' }} {{ $isCurrentPlan ? 'current-card' : '' }}">
                        <!-- Popular / Current Badge -->
                        @if($isCurrentPlan)
                            <div class="plan-ribbon current-ribbon">
                                <i class="fas fa-check-circle me-1"></i> ACTIVE PLAN
                            </div>
                        @elseif($plan->is_popular)
                            <div class="plan-ribbon popular-ribbon">
                                <i class="fas fa-star me-1"></i> MOST POPULAR
                            </div>
                        @endif

                        <!-- Card Header -->
                        <div class="p-4 pb-3 border-bottom text-center bg-white rounded-top-4">
                            <h4 class="fw-bold mb-1 text-dark">{{ $plan->name }}</h4>
                            <p class="text-muted small mb-3" style="min-height: 38px;">{{ Str::limit($plan->description ?? 'Ideal setup for ambitious beauty businesses.', 75) }}</p>

                            <div class="pricing-display py-2">
                                <div class="d-flex align-items-baseline justify-content-center">
                                    <span class="fs-4 fw-bold text-muted me-1">{{ system_currency_symbol() }}</span>
                                    <span class="display-5 fw-extrabold text-dark">{{ number_format($plan->price, 0) }}</span>
                                </div>
                                <span class="text-muted small fw-semibold">billed every {{ $plan->duration_in_days }} days</span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body p-4 d-flex flex-column bg-white">
                            <!-- Resource Limits Matrix -->
                            <div class="mb-4">
                                <span class="text-muted text-uppercase fw-bold d-block mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">Resource Capacity</span>
                                <div class="row g-2">
                                    @foreach(array_slice($limits, 0, 4) as $lim)
                                        <div class="col-6">
                                            <div class="p-2 rounded-3 bg-light border d-flex align-items-center gap-2">
                                                <i class="{{ $lim['icon'] }}" style="width: 16px;"></i>
                                                <div class="overflow-hidden">
                                                    <span class="text-muted d-block text-truncate" style="font-size: 0.68rem;">{{ $lim['label'] }}</span>
                                                    <strong class="d-block text-dark small" style="font-size: 0.78rem;">
                                                        {{ ($lim['value'] === null || $lim['value'] === -1) ? 'Unlimited' : number_format($lim['value']) }}
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Collapsible Extra Limits -->
                                <div class="collapse mt-2" id="limitsCollapse{{ $plan->id }}">
                                    <div class="row g-2">
                                        @foreach(array_slice($limits, 4) as $lim)
                                            <div class="col-6">
                                                <div class="p-2 rounded-3 bg-light border d-flex align-items-center gap-2">
                                                    <i class="{{ $lim['icon'] }}" style="width: 16px;"></i>
                                                    <div class="overflow-hidden">
                                                        <span class="text-muted d-block text-truncate" style="font-size: 0.68rem;">{{ $lim['label'] }}</span>
                                                        <strong class="d-block text-dark small" style="font-size: 0.78rem;">
                                                            {{ ($lim['value'] === null || $lim['value'] === -1) ? 'Unlimited' : number_format($lim['value']) }}
                                                        </strong>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="text-center mt-2">
                                    <a class="text-primary text-decoration-none small fw-semibold limits-toggle-btn"
                                       data-bs-toggle="collapse" href="#limitsCollapse{{ $plan->id }}" role="button"
                                       aria-expanded="false" aria-controls="limitsCollapse{{ $plan->id }}">
                                        <span>View more limits</span> <i class="fas fa-chevron-down ms-1" style="font-size: 0.65rem;"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Features List -->
                            <div class="mb-4 flex-grow-1">
                                <span class="text-muted text-uppercase fw-bold d-block mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">Included Features</span>
                                <ul class="list-unstyled mb-0">
                                    @forelse($visibleFeatures as $feat)
                                        <li class="d-flex align-items-start gap-2 mb-2 small text-dark">
                                            <i class="fas fa-check-circle text-success mt-1 flex-shrink-0" style="font-size: 0.85rem;"></i>
                                            <span>{{ $feat }}</span>
                                        </li>
                                    @empty
                                        <li class="text-muted small fst-italic">Standard salon features included.</li>
                                    @endforelse

                                    @if(count($hiddenFeatures) > 0)
                                        <div class="collapse" id="featuresCollapse{{ $plan->id }}">
                                            @foreach($hiddenFeatures as $feat)
                                                <li class="d-flex align-items-start gap-2 mb-2 small text-dark">
                                                    <i class="fas fa-check-circle text-success mt-1 flex-shrink-0" style="font-size: 0.85rem;"></i>
                                                    <span>{{ $feat }}</span>
                                                </li>
                                            @endforeach
                                        </div>
                                        <li class="mt-2 text-center">
                                            <a class="text-primary text-decoration-none small fw-semibold features-toggle-btn"
                                               data-bs-toggle="collapse" href="#featuresCollapse{{ $plan->id }}" role="button"
                                               aria-expanded="false" aria-controls="featuresCollapse{{ $plan->id }}">
                                                <span>+{{ count($hiddenFeatures) }} more features</span> <i class="fas fa-chevron-down ms-1" style="font-size: 0.65rem;"></i>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            <!-- CTA Button -->
                            <div class="mt-auto pt-3 border-top">
                                @if($isCurrentPlan)
                                    <button class="btn btn-outline-success w-100 fw-bold py-2 rounded-3" disabled>
                                        <i class="fas fa-check-circle me-1"></i> Current Plan
                                    </button>
                                @elseif(isset($pendingPayment) && $pendingPayment)
                                    <button class="btn btn-light text-muted w-100 fw-bold py-2 rounded-3 border" disabled>
                                        <i class="fas fa-clock me-1"></i> Request Pending
                                    </button>
                                @else
                                    <button class="btn btn-primary w-100 fw-bold py-2 rounded-3 shadow-sm select-plan-btn"
                                        onclick="setUpgradePlan('{{ $plan->id }}', '{{ $plan->name }}')"
                                        data-bs-toggle="modal" data-bs-target="#upgradeModal">
                                        Upgrade to {{ $plan->name }} <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Upgrade Subscription Modal -->
    <div class="modal fade" id="upgradeModal" tabindex="-1" aria-labelledby="upgradeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <form action="{{ route('admin.saas.subscription.upgrade', ['salon_slug' => request()->route('salon_slug')]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan_id" id="upgrade_plan_id">

                    <div class="modal-header bg-light border-0 py-3 px-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 rounded-circle bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                            <h5 class="modal-title fw-bold text-dark" id="upgradeModalLabel">Upgrade Salon Plan</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <span class="text-muted small d-block">Selected Plan:</span>
                            <h5 class="fw-bold text-primary mb-0" id="upgrade_plan_name">Selected Plan</h5>
                        </div>

                        <label class="form-label small fw-bold text-dark text-uppercase mb-2">Select Payment Method</label>

                        <!-- Manual Payment Radio Card -->
                        <div class="payment-method-card p-3 rounded-3 border mb-2 active" onclick="document.getElementById('payment_manual').checked = true; updatePaymentCardSelection();">
                            <div class="form-check d-flex align-items-start gap-2 m-0">
                                <input class="form-check-input mt-1" type="radio" name="payment_method" id="payment_manual" value="manual" checked>
                                <label class="form-check-label w-100 cursor-pointer" for="payment_manual">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-dark small">Manual Transfer / Cash</strong>
                                        <span class="badge bg-success bg-opacity-10 text-success">Recommended</span>
                                    </div>
                                    <span class="text-muted d-block" style="font-size: 0.75rem;">
                                        Bank wire, UPI, or cash receipt. An administrator will verify and approve your subscription.
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Stripe Radio Card (Disabled/Coming Soon) -->
                        <div class="payment-method-card p-3 rounded-3 border opacity-60">
                            <div class="form-check d-flex align-items-start gap-2 m-0">
                                <input class="form-check-input mt-1" type="radio" name="payment_method" id="payment_stripe" value="stripe" disabled>
                                <label class="form-check-label w-100 text-muted" for="payment_stripe">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="small">Instant Card Checkout (Stripe)</strong>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">Coming Soon</span>
                                    </div>
                                    <span class="d-block" style="font-size: 0.75rem;">Credit card, Apple Pay, Google Pay automated renewals.</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-0 py-3 px-4 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary px-3 rounded-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold rounded-3 shadow-sm">
                            <i class="fas fa-check me-1"></i> Confirm Upgrade Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Renew Subscription Modal -->
    <div class="modal fade" id="renewModal" tabindex="-1" aria-labelledby="renewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <form action="{{ route('admin.saas.subscription.renew', ['salon_slug' => request()->route('salon_slug')]) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-light border-0 py-3 px-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 rounded-circle bg-success bg-opacity-10 text-success">
                                <i class="fas fa-sync-alt"></i>
                            </div>
                            <h5 class="modal-title fw-bold text-dark" id="renewModalLabel">Renew Active Subscription</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                        @if($subscription)
                            <div class="p-3 bg-light rounded-3 mb-3 border">
                                <span class="text-muted small d-block">Renewing Plan:</span>
                                <h5 class="fw-bold text-dark mb-0">{{ $subscription->plan->name }}</h5>
                                <span class="small text-muted">{{ system_currency_symbol() }}{{ number_format($subscription->plan->price, 2) }} / {{ $subscription->plan->duration_in_days }} days</span>
                            </div>
                        @endif

                        <label class="form-label small fw-bold text-dark text-uppercase mb-2">Payment Method</label>

                        <div class="payment-method-card p-3 rounded-3 border mb-2 active">
                            <div class="form-check d-flex align-items-start gap-2 m-0">
                                <input class="form-check-input mt-1" type="radio" name="payment_method" id="renew_payment_manual" value="manual" checked>
                                <label class="form-check-label w-100 cursor-pointer" for="renew_payment_manual">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-dark small">Manual Payment (Bank Transfer / Cash)</strong>
                                        <span class="badge bg-success bg-opacity-10 text-success">Recommended</span>
                                    </div>
                                    <span class="text-muted d-block" style="font-size: 0.75rem;">Submit renewal request. Admin verification required.</span>
                                </label>
                            </div>
                        </div>

                        <div class="payment-method-card p-3 rounded-3 border opacity-60">
                            <div class="form-check d-flex align-items-start gap-2 m-0">
                                <input class="form-check-input mt-1" type="radio" name="payment_method" id="renew_payment_stripe" value="stripe" disabled>
                                <label class="form-check-label w-100 text-muted" for="renew_payment_stripe">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="small">Online Payment (Stripe)</strong>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">Coming Soon</span>
                                    </div>
                                    <span class="d-block" style="font-size: 0.75rem;">Automatic checkout gateway.</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-0 py-3 px-4 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary px-3 rounded-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success px-4 fw-bold rounded-3 shadow-sm">
                            <i class="fas fa-check me-1"></i> Submit Renewal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.fw-extrabold {
    font-weight: 800;
}

.text-purple {
    color: #6f42c1;
}

.text-teal {
    color: #20c997;
}

.enterprise-pricing-card {
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s;
    border: 1px solid #e2e8f0 !important;
}

.enterprise-pricing-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08), 0 4px 10px rgba(0, 0, 0, 0.04) !important;
    border-color: #cbd5e1 !important;
}

.enterprise-pricing-card.popular-card {
    border: 2px solid var(--theme-primary, #0d6efd) !important;
    box-shadow: 0 8px 24px rgba(13, 110, 253, 0.08) !important;
}

.enterprise-pricing-card.current-card {
    border: 2px solid #10b981 !important;
}

.plan-ribbon {
    position: absolute;
    top: 12px;
    right: 12px;
    padding: 0.3rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    z-index: 5;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

.popular-ribbon {
    background: var(--theme-primary, #0d6efd);
    color: #ffffff;
}

.current-ribbon {
    background: #10b981;
    color: #ffffff;
}

.payment-method-card {
    cursor: pointer;
    transition: border-color 0.15s, background-color 0.15s;
    background: #ffffff;
}

.payment-method-card:hover {
    border-color: var(--theme-primary, #0d6efd) !important;
    background: #f8fafc;
}

.payment-method-card.active {
    border-color: var(--theme-primary, #0d6efd) !important;
    background: rgba(13, 110, 253, 0.03);
}

.cursor-pointer {
    cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script>
function setUpgradePlan(id, name) {
    const idInput = document.getElementById('upgrade_plan_id');
    const nameEl = document.getElementById('upgrade_plan_name');
    if (idInput) idInput.value = id;
    if (nameEl) nameEl.textContent = name;
}

function updatePaymentCardSelection() {
    document.querySelectorAll('.payment-method-card').forEach(card => {
        const radio = card.querySelector('input[type="radio"]');
        if (radio && radio.checked) {
            card.classList.add('active');
        } else {
            card.classList.remove('active');
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    // Sync chevron rotation on collapse
    document.querySelectorAll('.collapse').forEach(function (coll) {
        coll.addEventListener('show.bs.collapse', function () {
            const toggle = document.querySelector('[href="#' + this.id + '"]');
            if (toggle) {
                const icon = toggle.querySelector('.fa-chevron-down');
                if (icon) icon.className = 'fas fa-chevron-up ms-1';
            }
        });
        coll.addEventListener('hide.bs.collapse', function () {
            const toggle = document.querySelector('[href="#' + this.id + '"]');
            if (toggle) {
                const icon = toggle.querySelector('.fa-chevron-up');
                if (icon) icon.className = 'fas fa-chevron-down ms-1';
            }
        });
    });
});
</script>
@endpush
