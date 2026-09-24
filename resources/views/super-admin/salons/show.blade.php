@extends('layouts.super-admin')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-5">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.super.dashboard') }}"
                                class="text-muted text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.salons.index') }}"
                                class="text-muted text-decoration-none">Salons</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Details</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white rounded-circle shadow-sm border p-1" style="width: 50px; height: 50px;">
                        <img src="{{ $salon->logo_url }}" alt="{{ $salon->name }}" class="w-100 h-100 rounded-circle object-fit-cover">
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800 fw-bold d-flex align-items-center gap-3">
                            {{ $salon->name }}
                        </h1>
                    </div>
                    @if($salon->isOnTrial())
                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3">On
                            Trial</span>
                    @elseif($salon->activeSubscription)
                        <span
                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">Active</span>
                    @else
                        <span
                            class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">Inactive</span>
                    @endif
                </div>
            </div>
            <div class="d-flex gap-2 mt-3 mt-md-0">
                @if((!$salon->is_active || ($salon->activeSubscription && $salon->activeSubscription->status === 'pending')))
                    <form action="{{ route('admin.salons.approve', $salon) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success text-white shadow-sm fw-medium px-4"
                            onclick="return confirm('Are you sure you want to approve this salon?')">
                            <i class="fas fa-check me-2"></i> Approve Salon
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.salons.edit', $salon) }}"
                    class="btn btn-white bg-white border shadow-sm text-dark fw-medium">
                    <i class="fas fa-edit me-2"></i> Edit Details
                </a>
                <a href="{{ route('admin.salons.index') }}"
                    class="btn btn-white bg-white border shadow-sm text-dark fw-medium">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>
            </div>
        </div>

        <!-- Quick Stats Row -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-circle bg-primary-subtle p-3 d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="fas fa-dollar-sign text-primary fa-lg"></i>
                            </div>
                            <span class="badge bg-success-subtle text-success rounded-pill">+12%</span>
                        </div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">Total Revenue</h6>
                        <h3 class="fw-bold text-dark mb-0">
                            {{ app(\App\Services\SettingsService::class)->get('currency_symbol', '$', $salon->id) }}{{ number_format($stats['total_revenue'], 2) }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-circle bg-success-subtle p-3 d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="fas fa-calendar-check text-success fa-lg"></i>
                            </div>
                            <span class="badge bg-success-subtle text-success rounded-pill">+5%</span>
                        </div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">Total Bookings</h6>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['total_bookings']) }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-circle bg-info-subtle p-3 d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="fas fa-users text-info fa-lg"></i>
                            </div>
                            <span class="badge bg-success-subtle text-success rounded-pill">+8%</span>
                        </div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">Total Customers</h6>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['total_customers']) }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-circle bg-warning-subtle p-3 d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="fas fa-store text-warning fa-lg"></i>
                            </div>
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">Fixed</span>
                        </div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">Active Branches</h6>
                        <h3 class="fw-bold text-dark mb-0">{{ $stats['total_branches'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <ul class="nav nav-pills card-header-pills gap-2" id="salonTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-medium px-4" id="overview-tab" data-bs-toggle="tab"
                            data-bs-target="#overview" type="button" role="tab">
                            <i class="fas fa-home me-2"></i>Overview
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium px-4" id="subscription-tab" data-bs-toggle="tab"
                            data-bs-target="#subscription" type="button" role="tab">
                            <i class="fas fa-credit-card me-2"></i>Subscription
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium px-4" id="users-tab" data-bs-toggle="tab" data-bs-target="#users"
                            type="button" role="tab">
                            <i class="fas fa-users me-2"></i>Staff & Users
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium px-4" id="branches-tab" data-bs-toggle="tab"
                            data-bs-target="#branches" type="button" role="tab">
                            <i class="fas fa-code-branch me-2"></i>Branches
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium px-4" id="payments-tab" data-bs-toggle="tab"
                            data-bs-target="#payments" type="button" role="tab">
                            <i class="fas fa-history me-2"></i>Payments
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium px-4" id="settings-tab" data-bs-toggle="tab"
                            data-bs-target="#settings" type="button" role="tab">
                            <i class="fas fa-cog me-2"></i>Settings
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <div class="tab-content" id="salonTabContent">
                    <!-- Overview Tab -->
                    <div class="tab-pane fade show active" id="overview" role="tabpanel">
                        <div class="row g-4">
                            <!-- Left Column: Details -->
                            <div class="col-lg-8">
                                <div class="row g-4">
                                    <div class="col-12">
                                        <div class="card border bg-light h-100">
                                            <div class="card-body">
                                                <h6 class="text-uppercase text-muted fw-bold mb-3 small">Salon Information
                                                </h6>
                                                <div class="row g-3">
                                                    <div class="col-sm-6">
                                                        <label class="text-muted small">Salon Name</label>
                                                        <p class="fw-bold text-dark mb-0">{{ $salon->name }}</p>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="text-muted small">Business Type</label>
                                                        <p class="fw-bold text-dark mb-0">{{ ucfirst($salon->business_type ?? 'salon') }}</p>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="text-muted small">Slug / URL</label>
                                                        <p class="mb-0"><code>{{ $salon->slug }}</code></p>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="text-muted small">Email Address</label>
                                                        <p class="mb-0"><a href="mailto:{{ $salon->email }}"
                                                                class="text-decoration-none">{{ $salon->email }}</a></p>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="text-muted small">Phone Number</label>
                                                        <p class="fw-bold text-dark mb-0">{{ $salon->phone ?? 'N/A' }}</p>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="text-muted small">Website</label>
                                                        <p class="mb-0">
                                                            {!! $salon->website ? '<a href="' . $salon->website . '" target="_blank" class="text-decoration-none">' . $salon->website . '</a>' : 'N/A' !!}
                                                        </p>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="text-muted small">Registered On</label>
                                                        <p class="fw-bold text-dark mb-0">
                                                            {{ $salon->created_at->format('M d, Y') }} <span
                                                                class="text-muted fw-normal small">({{ $salon->created_at->diffForHumans() }})</span>
                                                        </p>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="text-muted small">Address</label>
                                                        <p class="fw-bold text-dark mb-0">{{ $salon->address ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="card border bg-light h-100">
                                            <div class="card-body">
                                                <h6 class="text-uppercase text-muted fw-bold mb-3 small">Subscription Status</h6>
                                                @if($salon->isOnTrial())
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="bg-info-subtle text-info rounded-circle p-2 me-3">
                                                            <i class="fas fa-clock fa-lg"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <h6 class="fw-bold text-dark mb-0">Trial Period</h6>
                                                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">On Trial</span>
                                                            </div>
                                                            <div class="progress" style="height: 6px;">
                                                                @php
                                                                    $totalTrialDays = $salon->created_at->diffInDays($salon->trial_ends_at);
                                                                    $daysLeft = $salon->trialDaysLeft();
                                                                    $percent = $totalTrialDays > 0 ? (1 - ($daysLeft / $totalTrialDays)) * 100 : 100;
                                                                @endphp
                                                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $percent }}%"></div>
                                                            </div>
                                                            <p class="small text-muted mt-2 mb-0">
                                                                <strong>{{ $daysLeft }} days remaining</strong>. Ends on {{ $salon->trial_ends_at->format('M d, Y') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                @elseif($salon->activeSubscription)
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-success-subtle text-success rounded-circle p-2 me-3">
                                                            <i class="fas fa-gem fa-lg"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <h6 class="fw-bold text-dark mb-0">{{ $salon->activeSubscription->plan->name }} Plan</h6>
                                                                @if($salon->activeSubscription->ends_at && $salon->activeSubscription->ends_at->diffInDays(now()) <= 7)
                                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Expiring Soon</span>
                                                                @else
                                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Active</span>
                                                                @endif
                                                            </div>
                                                            <p class="small text-muted mb-0">
                                                                @if($salon->activeSubscription->ends_at)
                                                                    Renews on <strong>{{ $salon->activeSubscription->ends_at->format('M d, Y') }}</strong>
                                                                    ({{ $salon->activeSubscription->ends_at->diffForHumans() }})
                                                                @else
                                                                    Never Expires
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-danger-subtle text-danger rounded-circle p-2 me-3">
                                                            <i class="fas fa-times-circle fa-lg"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="fw-bold text-dark mb-1">No Active Subscription</h6>
                                                            <p class="small text-muted mb-0">Updates and features are disabled.</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="card border bg-light h-100">
                                            <div class="card-body">
                                                <h6 class="text-uppercase text-muted fw-bold mb-3 small">Owner Details</h6>
                                                <div class="d-flex align-items-center">
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($salon->owner->name ?? 'N/A') }}&background=random&size=128"
                                                        class="rounded-circle shadow-sm me-3"
                                                        style="width: 64px; height: 64px;">
                                                    <div>
                                                        <h5 class="fw-bold text-dark mb-1">
                                                            {{ $salon->owner->name ?? 'N/A' }}
                                                        </h5>
                                                        <div class="text-muted small mb-1">
                                                            <i class="fas fa-envelope me-1"></i>
                                                            {{ $salon->owner->email ?? 'N/A' }}
                                                        </div>
                                                        @if($salon->owner && $salon->owner->phone)
                                                            <div class="text-muted small">
                                                                <i class="fas fa-phone me-1"></i> {{ $salon->owner->phone }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ms-auto text-end">
                                                        <div class="text-muted small">Last Login</div>
                                                        <div class="fw-bold text-dark">
                                                            {{ $salon->last_login_at ? $salon->last_login_at->format('M d, Y H:i') : 'Never' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Performance & Actions -->
                            <div class="col-lg-4">
                                <div class="card border h-100">
                                    <div class="card-body">
                                        <h6 class="text-uppercase text-muted fw-bold mb-4 small">Performance (This Month)
                                        </h6>

                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between align-items-end mb-2">
                                                <div>
                                                    <span class="text-dark fw-medium d-block">Revenue</span>
                                                    <small class="text-muted">{{ $stats['monthly_revenue'] > 0 && $stats['total_revenue'] > 0 ? round(($stats['monthly_revenue'] / $stats['total_revenue']) * 100, 1) . '% of total' : 'New' }}</small>
                                                </div>
                                                <span class="fw-bold text-success h5 mb-0">
                                                    {{ app(\App\Services\SettingsService::class)->get('currency_symbol', '$', $salon->id) }}
                                                    {{ number_format($stats['monthly_revenue'], 2) }}
                                                </span>
                                            </div>
                                            @php
                                                $revPercent = $stats['total_revenue'] > 0 ? ($stats['monthly_revenue'] / $stats['total_revenue']) * 100 : 0;
                                                $revPercent = min(100, max(5, $revPercent)); // Min 5% for visibility, max 100%
                                            @endphp
                                            <div class="progress bg-success-subtle" style="height: 8px;">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width: {{ $revPercent }}%; border-radius: 6px;" aria-valuenow="{{ $revPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between align-items-end mb-2">
                                                <div>
                                                    <span class="text-dark fw-medium d-block">Bookings</span>
                                                    @php
                                                        $bookingLimit = $salon->getBookingLimit();
                                                        $isUnlimited = $bookingLimit === null || $bookingLimit === -1;
                                                        $bookingPercent = 0;
                                                        if (!$isUnlimited && $bookingLimit > 0) {
                                                            $bookingPercent = ($stats['monthly_bookings'] / $bookingLimit) * 100;
                                                            $limitText = $stats['monthly_bookings'] . ' / ' . $bookingLimit;
                                                        } else {
                                                             $bookingPercent = $stats['total_bookings'] > 0 ? ($stats['monthly_bookings'] / $stats['total_bookings']) * 100 : 0;
                                                             $limitText = $isUnlimited ? 'Unlimited' : 'No Limit';
                                                        }
                                                        $barPercent = min(100, max(5, $bookingPercent));
                                                    @endphp
                                                    <small class="text-muted">{{ $isUnlimited ? 'Contribution: ' . round($bookingPercent, 1) . '%' : 'Usage: ' . round($bookingPercent, 1) . '%' }}</small>
                                                </div>
                                                <span class="fw-bold text-primary h5 mb-0">{{ number_format($stats['monthly_bookings']) }} <small class="text-muted fs-6 fw-normal">/ {{ $isUnlimited ? '∞' : $bookingLimit }}</small></span>
                                            </div>
                                            <div class="progress bg-primary-subtle" style="height: 8px;">
                                                <div class="progress-bar bg-primary" role="progressbar"
                                                    style="width: {{ $barPercent }}%; border-radius: 6px;" aria-valuenow="{{ $barPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between align-items-end mb-2">
                                                <div>
                                                    <span class="text-dark fw-medium d-block">Packages</span>
                                                    @php
                                                        $pkgLimit = $stats['package_limit'] ?? null;
                                                        $isPkgUnlimited = $pkgLimit === null || $pkgLimit === -1;
                                                    @endphp
                                                    <small class="text-muted">{{ $isPkgUnlimited ? 'Unlimited' : 'Limit: ' . $pkgLimit }}</small>
                                                </div>
                                                <span class="fw-bold text-dark h6 mb-0">{{ $stats['total_packages'] ?? 0 }} <small class="text-muted fw-normal">/ {{ $isPkgUnlimited ? '∞' : $pkgLimit }}</small></span>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between align-items-end mb-2">
                                                <div>
                                                    <span class="text-dark fw-medium d-block">Memberships</span>
                                                    @php
                                                        $memLimit = $stats['membership_limit'] ?? null;
                                                        $isMemUnlimited = $memLimit === null || $memLimit === -1;
                                                    @endphp
                                                    <small class="text-muted">{{ $isMemUnlimited ? 'Unlimited' : 'Limit: ' . $memLimit }}</small>
                                                </div>
                                                <span class="fw-bold text-dark h6 mb-0">{{ $stats['total_memberships'] ?? 0 }} <small class="text-muted fw-normal">/ {{ $isMemUnlimited ? '∞' : $memLimit }}</small></span>
                                            </div>
                                        </div>

                                        <hr class="my-4">

                                        <h6 class="text-uppercase text-muted fw-bold mb-3 small">Quick Actions</h6>
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('booking.guest', ['salon_slug' => $salon->slug]) }}"
                                                target="_blank" class="btn btn-outline-primary text-start">
                                                <i class="fas fa-external-link-alt me-2"></i> Visit Public Page
                                            </a>
                                            <form action="{{ route('admin.salons.impersonate', $salon) }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-dark text-start w-100">
                                                    <i class="fas fa-user-shield me-2"></i> Login as Owner
                                                </button>
                                            </form>
                                            <a href="{{ route('admin.notifications.create', ['salon_id' => $salon->id]) }}"
                                                class="btn btn-outline-secondary text-start">
                                                <i class="fas fa-envelope me-2"></i> Send Notification
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subscription Tab -->
                    <div class="tab-pane fade" id="subscription" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="card bg-white border shadow-sm h-100">
                                    <div class="card-body p-4">
                                        <h6 class="text-uppercase text-muted small fw-bold mb-4">Current Plan</h6>
                                        @if($salon->activeSubscription)
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="bg-primary-subtle text-primary rounded-circle p-3 me-3 d-flex align-items-center justify-content-center"
                                                    style="width: 56px; height: 56px;">
                                                    <i class="fas fa-gem fa-2x"></i>
                                                </div>
                                                <div>
                                                    <h4 class="mb-1 fw-bold text-dark">
                                                        {{ $salon->activeSubscription->plan->name }}
                                                    </h4>
                                                    <span
                                                        class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">Active</span>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Price</span>
                                                <span
                                                    class="fw-bold text-dark">{{ app(\App\Services\SettingsService::class)->get('currency_symbol', '$', $salon->id) }}
                                                    {{ number_format($salon->activeSubscription->plan->price, 2) }} /
                                                    month</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Billing Cycle</span>
                                                <span class="fw-medium text-dark">Monthly</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Next Renewal</span>
                                                <span
                                                    class="fw-medium text-dark">{{ $salon->activeSubscription->ends_at ? $salon->activeSubscription->ends_at->format('M d, Y') : 'N/A' }}</span>
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <div
                                                    class="bg-warning-subtle text-warning rounded-circle d-inline-flex p-3 mb-3">
                                                    <i class="fas fa-exclamation-circle fa-2x"></i>
                                                </div>
                                                <p class="mb-0 text-muted fw-medium">No active subscription found.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card bg-white border shadow-sm h-100">
                                    <div class="card-body p-4">
                                        <h6 class="text-uppercase text-muted small fw-bold mb-4">Trial Status</h6>
                                        @if($salon->isOnTrial())
                                            <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis mb-0">
                                                <div class="d-flex">
                                                    <div class="me-3">
                                                        <i class="fas fa-clock fa-2x"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="alert-heading h6 fw-bold">Active Trial Period</h5>
                                                        <p class="mb-0 small">{{ $salon->trialDaysLeft() }} days remaining.
                                                            Trial ends on <span
                                                                class="fw-bold">{{ $salon->trial_ends_at->format('M d, Y') }}</span>.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center py-4 text-muted">
                                                <div
                                                    class="bg-secondary-subtle text-secondary rounded-circle d-inline-flex p-3 mb-3">
                                                    <i class="fas fa-history fa-2x"></i>
                                                </div>
                                                <p class="mb-0 fw-medium">Trial period has ended or was never started.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card border shadow-sm">
                                    <div class="card-header bg-white py-3 border-bottom">
                                        <h6 class="m-0 font-weight-bold text-dark">Subscription History</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th
                                                            class="px-4 py-3 text-secondary small text-uppercase fw-bold border-0">
                                                            Plan</th>
                                                        <th
                                                            class="px-4 py-3 text-secondary small text-uppercase fw-bold border-0">
                                                            Amount</th>
                                                        <th
                                                            class="px-4 py-3 text-secondary small text-uppercase fw-bold border-0">
                                                            Status</th>
                                                        <th
                                                            class="px-4 py-3 text-secondary small text-uppercase fw-bold border-0">
                                                            Started</th>
                                                        <th
                                                            class="px-4 py-3 text-secondary small text-uppercase fw-bold border-0">
                                                            Ended</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($salon->subscriptions as $sub)
                                                        <tr>
                                                            <td class="px-4 fw-medium">{{ $sub->plan->name }}</td>
                                                            <td class="px-4">
                                                                {{ app(\App\Services\SettingsService::class)->get('currency_symbol', '$', $salon->id) }}
                                                                {{ number_format($sub->plan->price, 2) }}
                                                            </td>
                                                            <td class="px-4">
                                                                <span
                                                                    class="badge bg-{{ $sub->status === 'active' ? 'success' : ($sub->status === 'expired' ? 'danger' : 'secondary') }}-subtle text-{{ $sub->status === 'active' ? 'success' : ($sub->status === 'expired' ? 'danger' : 'secondary') }} border border-{{ $sub->status === 'active' ? 'success' : ($sub->status === 'expired' ? 'danger' : 'secondary') }}-subtle px-2 py-1 rounded-pill">
                                                                    {{ ucfirst($sub->status) }}
                                                                </span>
                                                            </td>
                                                            <td class="px-4 text-muted small">
                                                                {{ $sub->starts_at->format('M d, Y') }}
                                                            </td>
                                                            <td class="px-4 text-muted small">
                                                                {{ $sub->ends_at ? $sub->ends_at->format('M d, Y') : 'N/A' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Users Tab -->
                    <div class="tab-pane fade" id="users" role="tabpanel">
                        <div class="card border shadow-sm">
                            <div
                                class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-dark">Staff & System Users</h6>
                                <span
                                    class="badge bg-primary-subtle text-primary rounded-pill px-3">{{ $salon->users->count() }}
                                    Total</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="px-4 py-3 text-secondary small text-uppercase fw-bold border-0">
                                                    User</th>
                                                <th class="px-4 py-3 text-secondary small text-uppercase fw-bold border-0">
                                                    Email</th>
                                                <th class="px-4 py-3 text-secondary small text-uppercase fw-bold border-0">
                                                    Roles</th>
                                                <th class="px-4 py-3 text-secondary small text-uppercase fw-bold border-0">
                                                    Status</th>
                                                <th class="px-4 py-3 text-secondary small text-uppercase fw-bold border-0">
                                                    Joined</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($salon->users as $user)
                                                <tr>
                                                    <td class="px-4">
                                                        <div class="d-flex align-items-center">
                                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random"
                                                                class="rounded-circle me-3 border"
                                                                style="width: 36px; height: 36px;">
                                                            <span class="fw-bold text-dark">{{ $user->name }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 text-muted">{{ $user->email }}</td>
                                                    <td class="px-4">
                                                        @foreach($user->roles as $role)
                                                            <span
                                                                class="badge bg-light text-dark border me-1 fw-normal">{{ $role->name }}</span>
                                                        @endforeach
                                                    </td>
                                                    <td class="px-4">
                                                        <span
                                                            class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }}-subtle text-{{ $user->status === 'active' ? 'success' : 'danger' }} border border-{{ $user->status === 'active' ? 'success' : 'danger' }}-subtle rounded-pill px-2">
                                                            {{ ucfirst($user->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 text-muted small">{{ $user->created_at->format('M d, Y') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Branches Tab -->
                    <div class="tab-pane fade" id="branches" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="fw-bold text-dark mb-0">Salon Branches</h5>
                                    <span class="badge bg-dark rounded-pill px-3">{{ $branches->count() }} Total</span>
                                </div>
                            </div>
                            @foreach($branches as $branch)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border shadow-sm h-100 position-relative overflow-hidden">
                                        @if($branch->is_active)
                                            <div class="position-absolute top-0 end-0 mt-3 me-3">
                                                <span
                                                    class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Active</span>
                                            </div>
                                        @else
                                            <div class="position-absolute top-0 end-0 mt-3 me-3">
                                                <span
                                                    class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Inactive</span>
                                            </div>
                                        @endif

                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="bg-primary-subtle text-primary rounded-circle p-3 d-flex align-items-center justify-content-center me-3"
                                                    style="width: 48px; height: 48px;">
                                                    <i class="fas fa-store"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-1">{{ $branch->name }}</h6>
                                                    <div class="text-muted small"><i class="fas fa-map-marker-alt me-1"></i>
                                                        {{ \Illuminate\Support\Str::limit($branch->address ?? 'No address', 30) }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="vstack gap-2 mb-4">
                                                <div class="d-flex align-items-center text-muted small">
                                                    <i class="fas fa-phone fa-fw me-2"></i> {{ $branch->phone ?? 'No phone' }}
                                                </div>
                                                <div class="d-flex align-items-center text-muted small">
                                                    <i class="fas fa-envelope fa-fw me-2"></i>
                                                    {{ $branch->email ?? 'No email' }}
                                                </div>
                                            </div>

                                            <div class="row g-2 pt-3 border-top">
                                                <div class="col-6">
                                                    <div class="text-center p-2 rounded bg-light">
                                                        <div class="fw-bold text-dark h5 mb-0">{{ $branch->users_count }}</div>
                                                        <div class="text-muted small text-uppercase" style="font-size: 0.7rem;">
                                                            Staff</div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-center p-2 rounded bg-light">
                                                        <div class="fw-bold text-dark h5 mb-0">{{ $branch->bookings_count }}
                                                        </div>
                                                        <div class="text-muted small text-uppercase" style="font-size: 0.7rem;">
                                                            Bookings</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Payments Tab -->
                    <div class="tab-pane fade" id="payments" role="tabpanel">
                        <div class="card border shadow-sm">
                                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                        <h6 class="m-0 font-weight-bold text-dark">Recent SaaS Payments</h6>
                                        <a href="{{ route('admin.payments.index', ['salon_id' => $salon->id]) }}" class="btn btn-sm btn-primary shadow-sm px-3">View All History</a>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th class="px-4 py-3 text-secondary small text-uppercase fw-bold border-0">ID</th>
                                                        <th class="px-4 py-3 text-secondary small text-uppercase fw-bold border-0">Date</th>
                                                        <th class="px-4 py-3 text-secondary small text-uppercase fw-bold border-0">Amount</th>
                                                        <th class="px-4 py-3 text-secondary small text-uppercase fw-bold border-0">Method</th>
                                                        <th class="px-4 py-3 text-secondary small text-uppercase fw-bold border-0">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($payments as $payment)
                                                        <tr>
                                                            <td class="px-4 text-muted small">#{{ $payment->id }}</td>
                                                            <td class="px-4 text-muted small">{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                                            <td class="px-4 fw-bold text-dark">
                                                                {{ app(\App\Services\SettingsService::class)->get('currency_symbol', '$', $salon->id) }}{{ number_format(abs($payment->amount), 2) }}
                                                            </td>
                                                            <td class="px-4 text-muted small">{{ ucfirst($payment->payment_method ?? 'N/A') }}</td>
                                                            <td class="px-4">
                                                                <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}-subtle text-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }} border border-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}-subtle rounded-pill px-2">
                                                                    {{ ucfirst($payment->status) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center py-5 text-muted">
                                                                <div class="mb-2"><i class="fas fa-receipt fa-2x text-light"></i></div>
                                                                No payments found.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings Tab -->
                            <div class="tab-pane fade" id="settings" role="tabpanel">
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Mail Configuration (SMTP)</h6>
                                        <div class="card border shadow-sm">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-borderless mb-0">
                                                        <tr>
                                                            <th width="40%" class="text-muted small py-2">Driver</th>
                                                            <td class="small fw-bold text-dark py-2">{{ $salon->mail_driver ?? 'Default' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-muted small py-2">Host</th>
                                                            <td class="small fw-bold text-dark py-2">{{ $salon->mail_host ?? 'N/A' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-muted small py-2">Port</th>
                                                            <td class="small fw-bold text-dark py-2">{{ $salon->mail_port ?? 'N/A' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-muted small py-2">Encryption</th>
                                                            <td class="small fw-bold text-dark py-2">{{ $salon->mail_encryption ?? 'N/A' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-muted small py-2">From Address</th>
                                                            <td class="small fw-bold text-dark py-2">{{ $salon->mail_from_address ?? 'N/A' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-muted small py-2">From Name</th>
                                                            <td class="small fw-bold text-dark py-2">{{ $salon->mail_from_name ?? 'N/A' }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <h6 class="text-uppercase text-muted small fw-bold mb-3">System Settings</h6>
                                        <div class="card border-0 bg-info-subtle mb-3">
                                            <div class="card-body d-flex">
                                                <i class="fas fa-info-circle text-info mt-1 me-3"></i>
                                                <div class="small text-info-emphasis">
                                                    These settings are managed by the salon owner. As a Super Admin, you can override them in the Edit section if necessary.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-grid">
                                            <a href="{{ route('admin.salons.edit', $salon) }}" class="btn btn-primary shadow-sm">
                                                <i class="fas fa-cog me-2"></i> Configure System Settings
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Handle tab switching from URL hash
            var hash = window.location.hash;
            if (hash) {
                $('.nav-link[data-bs-target="' + hash + '"]').tab('show');
            }

            // Update URL hash when tab changes
            $('.nav-link').on('shown.bs.tab', function (e) {
                window.location.hash = $(e.target).attr('data-bs-target');
            });
        });
    </script>
@endpush