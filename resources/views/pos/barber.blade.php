@extends('layouts.app')

@section('title', 'Barbershop POS')

@push('styles')
    @php
        $themeColor = app(\App\Services\SettingsService::class)->getThemeColor();
        $colorMap = [
            'pink' => '#e83e8c',
            'blue' => '#0d6efd',
            'purple' => '#6f42c1',
            'green' => '#198754',
            'barber' => '#B45309'
        ];
        $colorRgbMap = [
            'pink' => '232, 62, 140',
            'blue' => '13, 110, 253',
            'purple' => '111, 66, 193',
            'green' => '25, 135, 84',
            'barber' => '180, 83, 9'
        ];
        $themeHex = $colorMap[$themeColor] ?? '#0d6efd';
        $themeRgb = $colorRgbMap[$themeColor] ?? '13, 110, 253';
    @endphp
    <!-- Modern Barber POS External Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/pos-barber.css') }}?v={{ file_exists(public_path('css/pos-barber.css')) ? filemtime(public_path('css/pos-barber.css')) : time() }}">
    <style>
        :root {
            --theme-color: {{ $themeHex }} !important;
            --theme-color-rgb: {{ $themeRgb }} !important;
            --theme-color-light: rgba({{ $themeRgb }}, 0.08) !important;
            --theme-color-hover: rgba({{ $themeRgb }}, 0.15) !important;
        }

        /* Mobile Viewport Fixes (< 992px) */
        @media (max-width: 991.98px) {
            html, body, body.pos-body {
                overflow-y: auto !important;
                overflow-x: hidden !important;
                height: auto !important;
                min-height: 100vh !important;
            }
            body .main-content {
                height: auto !important;
                min-height: 100vh !important;
                overflow: visible !important;
                padding: 4px 6px 130px 6px !important;
                margin-top: var(--header-height, 56px) !important;
                box-sizing: border-box !important;
                max-width: 100vw !important;
            }
            .pos-terminal-container {
                height: auto !important;
                max-height: none !important;
                overflow: visible !important;
                display: block !important;
                padding: 0 !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
            }
            .pos-main-row {
                display: block !important;
                height: auto !important;
                min-height: auto !important;
                overflow: visible !important;
                margin: 0 !important;
                width: 100% !important;
            }
            .pos-catalog-column {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                height: auto !important;
                flex: none !important;
                padding: 0 !important;
                overflow: visible !important;
            }
            .pos-cart-column {
                display: none;
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                height: auto !important;
                flex: none !important;
                padding: 0 !important;
                overflow: visible !important;
            }
            body.mobile-view-cart .pos-catalog-column {
                display: none !important;
            }
            body.mobile-view-cart .pos-cart-column {
                display: block !important;
            }
            .pos-catalog-card {
                display: block !important;
                height: auto !important;
                min-height: 200px !important;
                overflow: visible !important;
                border-radius: 10px !important;
                background: #FFFFFF !important;
                border: 1px solid #E2E8F0 !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            .pos-catalog-header {
                display: flex !important;
                flex-wrap: wrap !important;
                gap: 6px !important;
                padding: 8px !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            .pos-search-wrapper {
                flex: 1 1 100% !important;
                width: 100% !important;
                min-width: 0 !important;
            }
            .pos-category-select {
                flex: 1 1 100% !important;
                width: 100% !important;
                min-width: 0 !important;
            }
            .pos-catalog-body {
                display: block !important;
                flex: none !important;
                height: auto !important;
                min-height: 150px !important;
                max-height: none !important;
                overflow: visible !important;
                padding: 8px 6px !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            .grid-section {
                display: block !important;
                width: 100% !important;
            }
            .grid-section.d-none {
                display: none !important;
            }
            .pos-items-grid {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 8px !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            .pos-item-card {
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                height: 80px !important;
                min-height: 80px !important;
                padding: 8px 10px !important;
                background: #FFFFFF !important;
                border: 1.5px solid #E2E8F0 !important;
                border-radius: 10px !important;
                box-sizing: border-box !important;
                cursor: pointer !important;
            }
            .pos-professionals-grid {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 8px !important;
                width: 100% !important;
            }
            .pos-order-panel {
                display: block !important;
                height: auto !important;
                min-height: auto !important;
                overflow: visible !important;
                border-radius: 10px !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            .pos-order-items-scroll {
                display: block !important;
                flex: none !important;
                height: auto !important;
                max-height: none !important;
                overflow: visible !important;
                padding: 6px !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            .pos-order-summary {
                display: block !important;
                flex: none !important;
                height: auto !important;
                max-height: none !important;
                overflow: visible !important;
                padding: 8px 6px 20px 6px !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            .pos-nav-pills-bar {
                display: flex !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
                flex-wrap: nowrap !important;
                white-space: nowrap !important;
                max-width: 100% !important;
                padding: 2px 0 !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-2 px-md-3 py-1 pos-terminal-container">
        <!-- Top Bar with Exit, Title, Pill Tabs, and Utility Actions -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-1 mb-1 pb-1 border-bottom flex-shrink-0">
            <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap">
                <a href="{{ route('dashboard', ['salon_slug' => request()->route('salon_slug') ?? optional(auth()->user()->salon)->slug ?? 'admin']) }}" class="btn btn-sm btn-light border d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px; border-radius: 6px;" title="Exit to Dashboard">
                    <i class="fas fa-times text-muted"></i>
                </a>
                <div class="d-flex align-items-center flex-shrink-0">
                    <span class="fs-6 fw-bold text-dark text-nowrap d-flex align-items-center gap-1">
                        <i class="fas fa-scissors text-primary"></i> Barber POS
                    </span>
                </div>

                <!-- Navigation Pill Tabs (matching reference UI) -->
                <div class="pos-nav-pills-bar ms-md-3 d-flex align-items-center gap-1 overflow-x-auto">
                    <button type="button" class="pos-nav-pill" data-tab="professionals" id="tabPillProfessionals">
                        <i class="fas fa-user-tie me-1"></i>Professionals
                    </button>
                    <button type="button" class="pos-nav-pill active" data-tab="services" id="tabPillServices">
                        <i class="fas fa-scissors me-1"></i>Services
                    </button>
                    @php
                        $salon = auth()->user()->salon;
                        $canProducts = $salon && ($salon->canUseFeature('Inventory Management') || $salon->canUseFeature('Products')) && \App\Helpers\ModuleHelper::inventoryEnabled();
                        $canPackages = $salon && $salon->canUseFeature('Packages') && \App\Helpers\ModuleHelper::packagesEnabled();
                        $canMemberships = $salon && $salon->canUseFeature('Memberships') && \App\Helpers\ModuleHelper::membershipsEnabled();
                    @endphp
                    @if($canProducts)
                        <button type="button" class="pos-nav-pill" data-tab="products" id="tabPillProducts">
                            <i class="fas fa-box me-1"></i>Products
                        </button>
                    @endif
                    <button type="button" class="pos-nav-pill" data-tab="appointments" id="tabPillAppointments">
                        <i class="fas fa-calendar-check me-1"></i>Appointments
                        @if(isset($todayBookings) && count($todayBookings) > 0)
                            <span class="badge bg-primary text-white rounded-pill ms-1" style="font-size: 0.65rem;">{{ count($todayBookings) }}</span>
                        @endif
                    </button>
                    @if($canPackages)
                        <button type="button" class="pos-nav-pill" data-tab="packages" id="tabPillPackages">
                            <i class="fas fa-gift me-1"></i>Packages
                        </button>
                    @endif
                    @if($canMemberships)
                        <button type="button" class="pos-nav-pill" data-tab="memberships" id="tabPillMemberships">
                            <i class="fas fa-star me-1"></i>Memberships
                        </button>
                    @endif
                </div>
            </div>

            <!-- Top Right Utilities -->
            <div class="d-flex align-items-center justify-content-end gap-1 flex-shrink-0">
                @if(optional(auth()->user()->salon)->business_type === 'both' && !auth()->user()->hasRole('employee'))
                    <a href="{{ route('admin.pos.index', ['view' => 'salon']) }}" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 0.75rem;">
                        <i class="fas fa-exchange-alt"></i> <span class="d-none d-md-inline ms-1">Salon</span>
                    </a>
                @endif
                @can('pos.view_reports')
                    <a href="{{ route('admin.pos.analytics.index') }}" class="btn btn-sm btn-outline-info py-1 px-2" style="font-size: 0.75rem;" title="Analytics">
                        <i class="fas fa-chart-line"></i> <span class="d-none d-md-inline ms-1">Analytics</span>
                    </a>
                @endcan
                <a href="{{ route('admin.pos.sales.index') }}" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 0.75rem;" title="Sales History">
                    <i class="fas fa-history"></i> <span class="d-none d-md-inline ms-1">History</span>
                </a>
                <button class="btn btn-sm btn-primary py-1 px-2" data-bs-toggle="modal" data-bs-target="#newSaleModal" style="font-size: 0.75rem;">
                    <i class="fas fa-plus"></i> <span class="d-none d-sm-inline ms-1">Sale</span>
                </button>
            </div>
        </div>

        <!-- Mobile POS Segmented Switcher (Visible on mobile/tablet screens < 992px) -->
        <div class="pos-mobile-segment d-flex d-lg-none">
            <button type="button" class="pos-segment-btn active" id="mobileTabCatalog">
                <i class="fas fa-scissors me-1"></i> Catalog & Items
            </button>
            <button type="button" class="pos-segment-btn position-relative" id="mobileTabCart">
                <i class="fas fa-receipt me-1"></i> Your Order
                <span class="badge bg-primary rounded-pill ms-1 d-none" id="mobileCartBadge">0</span>
            </button>
        </div>

        <!-- Main Workspace Row -->
        <div class="row g-2 pos-main-row flex-grow-1" style="min-height: 0; margin-top: 0;">
            <!-- Left Column: Catalog & Grid Area -->
            <div class="col-12 col-lg-8 col-xl-8 col-xxl-9 pos-catalog-column">
                <div class="pos-catalog-card">
                    <!-- Catalog Subheader: Search + Category Filter + Active Staff Filter Pill -->
                    <div class="pos-catalog-header">
                        <div class="pos-search-wrapper">
                            <i class="fas fa-search pos-search-icon"></i>
                            <input type="text" id="searchInput" class="pos-search-input"
                                placeholder="Enter service name, price or duration..." autocomplete="off">
                            <input type="hidden" id="gridFilterInput">
                        </div>

                        <select class="pos-category-select" id="categorySelect">
                            <option value="">All categories</option>
                            @if(isset($categories))
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            @endif
                        </select>

                        <!-- Active Stylist Filter Pill -->
                        <div id="activeStaffFilterBadge" class="pos-active-stylist-badge d-none">
                            <i class="fas fa-user-check me-1"></i>
                            <span id="activeStaffFilterName">Staff</span>
                            <button type="button" class="btn-close ms-2" id="clearActiveStaffFilter" style="font-size: 0.65rem;" title="Clear Barber Filter"></button>
                        </div>
                    </div>

                    <!-- Catalog Content Body -->
                    <div class="pos-catalog-body">
                        <!-- Services Section (Default) -->
                        <div class="grid-section" id="servicesGridSection">
                            <div class="pos-items-grid">
                                @foreach($services as $service)
                                    @php
                                        $mins = $service->duration ?? 30;
                                        $durText = $mins >= 60 ? floor($mins/60).' hr'.($mins%60 > 0 ? ' '.($mins%60).' min' : '') : $mins.' min';
                                        $staffIds = $service->staff ? $service->staff->pluck('id')->implode(',') : '';
                                    @endphp
                                    <div class="pos-item-card grid-item-wrapper"
                                        data-type="service"
                                        data-id="{{ $service->id }}"
                                        data-name="{{ strtolower($service->name) }}"
                                        data-display-name="{{ $service->name }}"
                                        data-price="{{ $service->price }}"
                                        data-duration="{{ $mins }}"
                                        data-category-id="{{ $service->category_id ?? '' }}"
                                        data-staff-ids="{{ $staffIds }}">
                                        <div class="pos-item-title" title="{{ $service->name }}">
                                            {{ $service->name }}
                                        </div>
                                        <div class="pos-item-footer">
                                            <span class="pos-item-duration">{{ $durText }}</span>
                                            <span class="pos-item-price">{{ format_currency($service->price) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Professionals Tab Section -->
                        <div class="grid-section d-none" id="professionalsGridSection">
                            <div class="pos-professionals-grid">
                                @foreach($staff as $staffMember)
                                    @php
                                        $avatarUrl = $staffMember->employee?->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($staffMember->name).'&background=E2E8F0&color=334155';
                                        $serviceCount = $staffMember->services ? $staffMember->services->count() : 0;
                                    @endphp
                                    <div class="pos-pro-card" data-staff-id="{{ $staffMember->id }}" data-staff-name="{{ $staffMember->name }}">
                                        <img src="{{ $avatarUrl }}" alt="{{ $staffMember->name }}" class="pos-pro-avatar">
                                        <div class="pos-pro-name">{{ $staffMember->name }}</div>
                                        <div class="pos-pro-role">{{ $staffMember->employee?->position ?? 'Barber / Stylist' }}</div>
                                        <div class="pos-pro-services-count">
                                            <i class="fas fa-scissors me-1 text-primary"></i>{{ $serviceCount }} {{ Str::plural('Service', $serviceCount) }}
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary w-100 mt-2 btn-select-barber"
                                            data-staff-id="{{ $staffMember->id }}" data-staff-name="{{ $staffMember->name }}">
                                            <i class="fas fa-check me-1"></i>Select Barber
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Appointments Tab Section -->
                        <div class="grid-section d-none" id="appointmentsGridSection">
                            @if(isset($todayBookings) && count($todayBookings) > 0)
                                <div class="row g-2">
                                    @foreach($todayBookings as $booking)
                                        <div class="col-12 col-md-6">
                                            <div class="pos-appointment-card p-3 border rounded-3 bg-white shadow-sm d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="text-center px-2 py-1 bg-light rounded border">
                                                        <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }}</div>
                                                        <small class="text-muted" style="font-size: 0.7rem;">Today</small>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark" style="font-size: 0.9rem;">
                                                            {{ optional($booking->customer)->name ?? 'Walk-in' }}
                                                        </div>
                                                        <div class="text-muted small" style="font-size: 0.775rem;">
                                                            <i class="fas fa-scissors me-1 text-primary"></i>{{ optional($booking->service)->name ?? 'Service' }}
                                                            &bull; <i class="fas fa-user me-1 text-secondary"></i>{{ optional($booking->staff)->name ?? 'Staff' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <button type="button" class="btn btn-sm btn-primary btn-load-booking"
                                                        data-booking-id="{{ $booking->id }}"
                                                        data-customer-id="{{ $booking->customer_id }}"
                                                        data-customer-name="{{ optional($booking->customer)->name }}"
                                                        data-customer-phone="{{ optional($booking->customer)->phone }}"
                                                        data-customer-email="{{ optional($booking->customer)->email }}"
                                                        data-service-id="{{ $booking->service_id }}"
                                                        data-service-name="{{ optional($booking->service)->name }}"
                                                        data-service-price="{{ optional($booking->service)->price ?? 0 }}"
                                                        data-staff-id="{{ $booking->staff_id }}">
                                                        <i class="fas fa-plus me-1"></i>Load
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-2 opacity-25"></i>
                                    <p class="mb-0">No active appointments scheduled for today.</p>
                                </div>
                            @endif
                        </div>

                        @if($canProducts)
                            <!-- Products Grid Section -->
                            <div class="grid-section d-none" id="productsGridSection">
                                @if($products->isEmpty())
                                    <div class="pos-empty-section-msg">
                                        <i class="fas fa-box fa-2x mb-2 opacity-25"></i>
                                        <p class="mb-0 small text-muted">No products found.<br>Add products from <strong>Inventory</strong> to sell here.</p>
                                    </div>
                                @else
                                    <div class="pos-items-grid">
                                        @foreach($products as $product)
                                            <div class="pos-item-card grid-item-wrapper"
                                                data-type="product"
                                                data-id="{{ $product->id }}"
                                                data-name="{{ strtolower($product->name) }}"
                                                data-display-name="{{ $product->name }}"
                                                data-price="{{ $product->selling_price }}"
                                                data-stock="{{ $product->quantity_in_stock }}">
                                                <div class="pos-item-title" title="{{ $product->name }}">
                                                    {{ $product->name }}
                                                </div>
                                                <div class="pos-item-footer">
                                                    <span class="pos-item-duration text-muted">
                                                        <i class="fas fa-box me-1"></i>
                                                        Qty: {{ (int) $product->quantity_in_stock }}
                                                    </span>
                                                    <span class="pos-item-price">{{ format_currency($product->selling_price) }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if($canPackages)
                            <!-- Packages Grid Section -->
                            <div class="grid-section d-none" id="packagesGridSection">
                                @if($packages->isEmpty())
                                    <div class="pos-empty-section-msg">
                                        <i class="fas fa-gift fa-2x mb-2 opacity-25"></i>
                                        <p class="mb-0 small text-muted">No packages created yet.<br>Create packages from the <strong>Packages</strong> admin section.</p>
                                    </div>
                                @else
                                    <div class="pos-items-grid">
                                        @foreach($packages as $package)
                                            @php
                                                $pkgSavings = $package->savings ?? 0;
                                            @endphp
                                            <div class="pos-item-card grid-item-wrapper"
                                                data-type="package"
                                                data-id="{{ $package->id }}"
                                                data-name="{{ strtolower($package->name) }}"
                                                data-display-name="{{ $package->name }}"
                                                data-price="{{ $package->price }}">
                                                <div class="pos-item-title" title="{{ $package->name }}">
                                                    {{ $package->name }}
                                                </div>
                                                <div class="pos-item-footer">
                                                    <span class="pos-item-duration text-muted">
                                                        <i class="fas fa-gift me-1"></i>
                                                        {{ $package->services->count() }} {{ Str::plural('Service', $package->services->count()) }}
                                                        @if($pkgSavings > 0)
                                                            <span class="badge bg-success ms-1" style="font-size:0.6rem;">Save {{ format_currency($pkgSavings) }}</span>
                                                        @endif
                                                    </span>
                                                    <span class="pos-item-price">{{ format_currency($package->price) }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if($canMemberships)
                            <!-- Memberships Grid Section -->
                            <div class="grid-section d-none" id="membershipsGridSection">
                                @if($memberships->isEmpty())
                                    <div class="pos-empty-section-msg">
                                        <i class="fas fa-id-card fa-2x mb-2 opacity-25"></i>
                                        <p class="mb-0 small text-muted">No memberships created yet.<br>Create memberships from the <strong>Memberships</strong> admin section.</p>
                                    </div>
                                @else
                                    <div class="pos-items-grid">
                                        @foreach($memberships as $membership)
                                            @php
                                                $membershipPrice = $membership->calculateTotalMembershipPrice();
                                                $memSavings = $membership->savings ?? 0;
                                            @endphp
                                            <div class="pos-item-card grid-item-wrapper"
                                                data-type="membership"
                                                data-id="{{ $membership->id }}"
                                                data-name="{{ strtolower($membership->name) }}"
                                                data-display-name="{{ $membership->name }}"
                                                data-price="{{ $membershipPrice }}">
                                                <div class="pos-item-title" title="{{ $membership->name }}">
                                                    {{ $membership->name }}
                                                </div>
                                                <div class="pos-item-footer">
                                                    <span class="pos-item-duration text-muted">
                                                        <i class="fas fa-star me-1"></i>
                                                        {{ $membership->validity_value ?? '—' }} {{ $membership->validity_unit ?? '' }}
                                                        @if($memSavings > 0)
                                                            <span class="badge bg-warning text-dark ms-1" style="font-size:0.6rem;">Save {{ format_currency($memSavings) }}</span>
                                                        @endif
                                                    </span>
                                                    <span class="pos-item-price">{{ format_currency($membershipPrice) }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: "Your order" Panel -->
            <div class="col-12 col-lg-4 col-xl-4 col-xxl-3 pos-cart-column">
                <div class="pos-order-panel">
                    <!-- Order Header -->
                    <div class="pos-order-header">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="pos-order-title">
                                <i class="fas fa-receipt text-primary me-1"></i>Your order
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 fw-semibold" style="font-size: 0.75rem; border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                                <i class="fas fa-user-plus me-1"></i>New
                            </button>
                        </div>

                        <!-- Customer Selection -->
                        <div id="customerSearchSection" class="pos-customer-box">
                            <div class="position-relative">
                                <select class="form-select form-select-sm" id="customerSelect">
                                    <option value="">Walk-in Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer['id'] }}" data-name="{{ $customer['name'] }}"
                                            data-phone="{{ $customer['phone'] }}" data-email="{{ $customer['email'] }}">
                                            {{ $customer['text'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <input type="hidden" id="customerId" value="">
                        <input type="hidden" id="existingSaleId" value="">

                        <!-- Customer Info Display Card (compact) -->
                        <div id="customerDisplay" class="mt-1 px-2 py-1 bg-light rounded-2 border d-none">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.8rem;" id="customerName"></div>
                                    <div class="text-muted" style="font-size: 0.675rem;" id="customerPhone"></div>
                                    <div class="text-muted" style="font-size: 0.675rem;" id="customerEmail"></div>
                                    <div id="membershipInfo" class="mt-1 d-none">
                                        <span class="badge bg-success text-white" style="font-size: 0.6rem;">
                                            <i class="fas fa-star me-1"></i><span id="membershipName"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-danger py-0 px-1" id="clearCustomerBtn" style="font-size: 0.7rem; border-radius: 4px;" title="Clear Customer">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Scrollable Order Items Container (Grouped by Barber) -->
                    <div class="pos-order-items-scroll flex-grow-1" id="orderItemsScrollArea">
                        <div id="orderCardsContainer">
                            <!-- Dynamic Barber and Item cards rendered here by JS -->
                        </div>

                        <!-- Empty Placeholder (Modern Clean State) -->
                        <div id="emptyOrderPlaceholder" class="pos-empty-order-placeholder">
                            <div class="pos-empty-order-icon">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Your cart is empty</div>
                            <div class="small text-muted" style="max-width: 240px; font-size: 0.775rem;">
                                Tap any service, barber, or product on the left to start this order.
                            </div>
                        </div>

                        <!-- Hidden Table kept for full compatibility with existing POS calculations -->
                        <div class="d-none">
                            <table id="unifiedCartTable">
                                <tbody id="unifiedCartBody">
                                    <tr id="emptyCartRow"><td colspan="7"></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Order Summary & Checkout Footer -->
                    <div class="pos-order-summary">
                        <!-- Subtotal Line -->
                        <div class="pos-summary-line">
                            <span class="text-lbl">Subtotal</span>
                            <span class="text-val" id="cartSubtotal">0.00</span>
                        </div>

                        <!-- Discount Line (Sleek Inline Design - Always Visible) -->
                        <div class="pos-summary-line" id="cartDiscountRow">
                            <div class="d-flex align-items-center gap-1">
                                <span class="text-lbl">Discount</span>
                                <div class="pos-discount-control">
                                    <input type="number" id="discountValue" placeholder="0" min="0" step="0.01">
                                    <select id="discountType">
                                        <option value="percent">%</option>
                                        <option value="fixed">{{ $currencySymbol ?? '₹' }}</option>
                                    </select>
                                    <button type="button" id="applyDiscount" class="d-none"></button>
                                    <button type="button" id="clearDiscount" class="d-none disc-clear-btn" title="Clear Discount"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                            <span class="fw-bold text-danger" id="cartDiscount">-0.00</span>
                        </div>

                        <!-- Tax Line -->
                        <div class="pos-summary-line">
                            <span class="text-lbl">Tax ({{ number_format($taxRate ?? 0, 2) + 0 }}%)</span>
                            <span class="text-val" id="cartTax">0.00</span>
                        </div>

                        <!-- Paid Already Row (when editing unpaid sale) -->
                        <div class="pos-summary-line d-none" id="cartPaidRow" style="color: #065f46;">
                            <span class="fw-medium">Paid Already</span>
                            <span class="fw-bold text-success">0.00</span>
                        </div>

                        <!-- Tip Selector Pills (Reasonable percentages & compact single-row layout) -->
                        @if($tipEnabled ?? true)
                            <div class="pos-tip-wrapper d-flex align-items-center justify-content-between gap-1 py-1 px-2 my-1">
                                <span class="text-lbl fw-semibold" style="font-size: 0.7rem; white-space: nowrap;">Tip</span>
                                <div class="pos-tip-quick-group flex-grow-1">
                                    <button type="button" class="pos-tip-pill active" data-pct="0">No Tip</button>
                                    <button type="button" class="pos-tip-pill" data-pct="5">5%</button>
                                    <button type="button" class="pos-tip-pill" data-pct="10">10%</button>
                                    <button type="button" class="pos-tip-pill" data-pct="15">15%</button>
                                    <button type="button" class="pos-tip-pill" data-pct="custom">Custom</button>
                                </div>
                                <div class="d-flex align-items-center gap-1 bg-white border rounded px-1 flex-shrink-0" style="height: 22px;">
                                    <span class="text-muted" style="font-size: 0.65rem;">{{ $currencySymbol ?? '₹' }}</span>
                                    <input type="number" class="border-0 bg-transparent text-end fw-bold text-dark" style="width: 36px; font-size: 0.72rem; outline: none; padding: 0;" id="tipAmount" value="0" min="0" step="0.01">
                                </div>
                            </div>
                        @else
                            <input type="hidden" id="tipAmount" value="0">
                        @endif

                        <!-- Grand Total Banner (Prominent display matching reference UI) -->
                        <div class="pos-total-banner py-1 mb-1">
                            <div>
                                <div class="pos-order-total-lbl" style="font-size: 0.8rem;">Total Price</div>
                                <div class="pos-order-total-sub" style="font-size: 0.62rem;">Including all taxes & fees</div>
                            </div>
                            <div class="pos-order-total-amount" id="grandTotal" style="font-size: 1.25rem;">0.00</div>
                        </div>

                        <div class="d-none">
                            <span id="payableAmount">0.00</span>
                            <span id="outstandingAmount">0.00</span>
                        </div>

                        <!-- Payment Method Selection Tiles -->
                        <div class="payment-device-tiles">
                            <div class="payment-device-tile active" id="tilePaymentCash" data-method="cash" title="Cash">
                                <i class="fas fa-money-bill-wave text-success"></i>
                                <span>Cash</span>
                            </div>
                            <div class="payment-device-tile" id="tilePaymentCard" data-method="card" title="Card">
                                <i class="fas fa-credit-card text-primary"></i>
                                <span>Card</span>
                            </div>
                            <div class="payment-device-tile" id="tilePaymentSplit" data-method="mixed" title="Split Payment">
                                <i class="fas fa-columns text-dark"></i>
                                <span>Split</span>
                            </div>
                            @if($acceptOnline ?? true)
                            <div class="payment-device-tile" id="tilePaymentOnline" data-method="online" title="Online Payment">
                                <i class="fas fa-globe text-info"></i>
                                <span>Online</span>
                            </div>
                            @endif
                        </div>

                        <!-- Radio inputs maintained for seamless controller compatibility -->
                        <div class="d-none">
                            <input type="radio" name="payment_method" id="cartPaymentCash" value="cash" checked>
                            <input type="radio" name="payment_method" id="cartPaymentCard" value="card">
                            <input type="radio" name="payment_method" id="cartPaymentOnline" value="online">
                            <input type="radio" name="payment_method" id="cartPaymentOther" value="other">
                            <input type="radio" name="payment_method" id="cartPaymentMixed" value="mixed">
                            <input type="radio" name="payment_method" id="cartPaymentPackage" value="package">
                            <input type="radio" name="payment_method" id="cartPaymentNone" value="none">
                        </div>

                        <!-- Cash Payment Inputs (Compact Inline) -->
                        <div id="paymentDetailsSection" class="mb-1 py-1 px-2 bg-light rounded-2 border">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <div class="d-flex align-items-center gap-1 flex-grow-1">
                                    <span class="text-muted fw-semibold" style="font-size: 0.7rem; white-space: nowrap;">Received:</span>
                                    <input type="number" class="form-control form-control-sm py-0 px-1 bg-white" style="height: 24px; font-size: 0.75rem;"
                                        id="amountReceived" step="0.01" min="0">
                                </div>
                                <div class="d-flex align-items-center gap-1 flex-grow-1">
                                    <span class="text-muted fw-semibold" style="font-size: 0.7rem; white-space: nowrap;">Change:</span>
                                    <input type="text" class="form-control form-control-sm py-0 px-1 bg-white fw-bold text-success" style="height: 24px; font-size: 0.75rem;"
                                        id="changeAmount" readonly value="0.00">
                                </div>
                            </div>
                        </div>

                        <!-- Split Payment Details (Visible for Split) -->
                        <div id="splitPaymentSection" class="d-none mb-2 p-2 bg-light rounded-3 border">
                            <div class="row g-1">
                                <div class="col-6">
                                    <label class="small text-muted mb-0" style="font-size: 0.7rem;">Cash</label>
                                    <input type="number" class="form-control form-control-sm split-input py-0" style="height: 24px;" id="splitCash" placeholder="0.00" min="0" step="0.01">
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted mb-0" style="font-size: 0.7rem;">Card</label>
                                    <input type="number" class="form-control form-control-sm split-input py-0" style="height: 24px;" id="splitCard" placeholder="0.00" min="0" step="0.01">
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted mb-0" style="font-size: 0.7rem;">Online</label>
                                    <input type="number" class="form-control form-control-sm split-input py-0" style="height: 24px;" id="splitOnline" placeholder="0.00" min="0" step="0.01">
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted mb-0" style="font-size: 0.7rem;">Other</label>
                                    <input type="number" class="form-control form-control-sm split-input py-0" style="height: 24px;" id="splitOther" placeholder="0.00" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="mt-1 text-end">
                                <small class="text-muted" style="font-size: 0.725rem;">Remaining: <span id="splitRemaining" class="fw-bold text-danger">0.00</span></small>
                            </div>
                        </div>

                        <!-- Hidden fields -->
                        <input type="hidden" id="cashAmount" value="0">
                        <input type="hidden" id="cardAmount" value="0">
                        <input type="hidden" id="onlineAmount" value="0">
                        <input type="hidden" id="otherAmount" value="0">

                        <!-- Primary CTA Charge Button -->
                        <button type="button" class="pos-charge-btn" id="checkoutBtn" disabled data-bs-toggle="modal" data-bs-target="#saleConfirmationModal">
                            <i class="fas fa-bolt"></i>
                            <span id="chargeBtnText">Charge 0.00</span>
                        </button>

                        <button type="button" class="btn btn-sm btn-link text-muted w-100 mt-1 py-0 text-decoration-none" id="resetBtn" style="font-size: 0.72rem;">
                            <i class="fas fa-undo me-1"></i>Reset Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Floating Sticky Checkout Bar (Visible only on mobile when items in cart) -->
    <div id="posMobileStickyBar" class="pos-mobile-sticky-checkout" style="display: none;">
        <button type="button" class="btn btn-primary w-100 py-2 px-3 d-flex justify-content-between align-items-center fw-bold rounded-3 shadow-lg" id="btnStickyCheckout" style="background: #0D6EFD; color: #fff; border: none; font-size: 0.95rem;">
            <span class="d-flex align-items-center">
                <span class="badge bg-white text-primary rounded-pill me-2 px-2 py-1" id="mobileStickyItemCount">0 Items</span>
                <span id="mobileStickyTotal">0.00</span>
            </span>
            <span>
                <span>View Order</span>
                <i class="fas fa-arrow-right ms-2"></i>
            </span>
        </button>
    </div>

    <!-- New Customer Modal -->
    <!-- New Customer Modal -->
    <x-modals.add-customer />

    <!-- Assign Membership Modal -->
    <div class="modal fade" id="assignMembershipModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Assign Membership</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="assignMembershipForm">
                        <input type="hidden" id="assignCustomerId" name="customer_id">
                        <div class="mb-3">
                            <label class="form-label">Select Membership</label>
                            <select class="form-select" id="membershipSelect" name="membership_id" required>
                                <option value="">Choose a membership...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Start Date (Optional)</label>
                            <input type="date" class="form-control" id="startDate" name="start_date"
                                min="{{ date('Y-m-d') }}">
                            <div class="form-text">Leave blank to start today.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="assignMembershipSubmit">Assign
                        Membership</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sale Confirmation Modal -->
    <!-- Sale Confirmation Modal -->
    <div class="modal fade" id="saleConfirmationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <!-- Modern Gradient Header -->
                <div class="modal-header border-0 text-white py-3 px-4"
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <h5 class="modal-title d-flex align-items-center fw-semibold">
                        <i class="fas fa-check-circle me-2"></i> Confirm Sale
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center px-4 py-4">
                    <div class="mb-4">
                        <div
                            style="width: 70px; height: 70px; background: rgba(16, 185, 129, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                            <i class="fas fa-cash-register fa-2x text-success"></i>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-2 text-dark">Complete Transaction?</h5>
                    <p class="text-muted mb-4 small">Please verify the payment details below before finalizing.</p>

                    <div class="bg-light p-3 rounded-3 border border-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small text-uppercase fw-bold letter-spacing-1">Discount</span>
                            <span class="fw-bold text-danger" id="confirmDiscount">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small text-uppercase fw-bold letter-spacing-1">Total Payable</span>
                            <span class="fw-bold fs-6 text-dark" id="confirmTotal">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small text-uppercase fw-bold letter-spacing-1">Amount Received</span>
                            <span class="fw-bold fs-6 text-success" id="confirmReceived">0.00</span>
                        </div>

                        <div class="border-top border-dashed my-2 pt-2"></div>

                        <div class="d-flex justify-content-between align-items-center" id="confirmOutstandingRow">
                            <span class="fw-bold text-dark">Outstanding Balance</span>
                            <span class="h5 mb-0 fw-bolder text-danger" id="confirmOutstanding">0.00</span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <div class="d-flex gap-2 w-100">
                        <button type="button" class="btn btn-light flex-grow-1 border fw-semibold py-2"
                            data-bs-dismiss="modal" style="border-radius: 8px;">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-success flex-grow-1 fw-semibold py-2 shadow-sm"
                            id="confirmSaleBtn"
                            style="border-radius: 8px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
                            <i class="fas fa-check me-1"></i> Complete Sale
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Package Service Selection Modal -->
    <div class="modal fade" id="packageServiceModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Select Services for <span id="packageModalName"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="packageLimitText">Select up to 3 services from this package</span>
                    </div>

                    <div id="packageServicesContainer">
                        <!-- Services will be loaded here dynamically -->
                    </div>

                    <div class="mt-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Selected Services:</span>
                            <span class="badge bg-primary fs-6" id="selectedCount">0 / 0</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmPackageServicesBtn">
                        <i class="fas fa-check me-1"></i> Confirm Selection
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div><!-- End of main container-fluid wrapper -->

    <!-- POS Reset Confirmation Modal -->
    <div class="modal fade" id="posResetConfirmationModal" tabindex="-1" aria-hidden="true" style="z-index: 1095;">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <!-- Modern Warning Gradient Header -->
                <div class="modal-header border-0 text-white py-3 px-4"
                    style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <h5 class="modal-title d-flex align-items-center fw-semibold">
                        <i class="fas fa-exclamation-triangle me-2"></i> Confirm Reset
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center px-4 py-4">
                    <div class="mb-4">
                        <div
                            style="width: 70px; height: 70px; background: rgba(245, 158, 11, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                            <i class="fas fa-redo-alt fa-2x text-warning"></i>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-2 text-dark">Reset POS Terminal?</h5>
                    <p class="text-muted mb-0 small" id="resetModalMessage">Are you sure you want to reset the terminal for
                        a new sale?</p>
                </div>

                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <div class="d-flex gap-2 w-100">
                        <button type="button" class="btn btn-light flex-grow-1 border fw-semibold py-2"
                            data-bs-dismiss="modal" style="border-radius: 8px;">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-danger flex-grow-1 fw-semibold py-2 shadow-sm"
                            id="confirmResetBtn"
                            style="border-radius: 8px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none;">
                            Yes, Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            // Check if jQuery is loaded


            // Global variables for cart and discount
            let cart = [];
            let customerPackageBalances = [];
            let discountAmount = 0;
            let discountPercent = 0;
            let discountType = 'fixed'; // 'fixed' or 'percent'
            let grossSubtotal = 0;
            let totalItemDiscount = 0;
            let netSubtotal = 0;
            let totalTax = 0;
            let tipAmount = 0;
            let isPaidSale = false; // New flag to track if current sale is paid
            const taxRate = {{ $taxRate ?? 0 }};
            const salonData = @json($salonData);

            function togglePosControls(disabled) {
                // List of selectors to disable/enable
                const selectors = [
                    '#discountValue', '#discountType', '#applyDiscount', '#tipAmount',
                    '#amountReceived', '.split-input', '#checkoutBtn',
                    '#clearCustomerBtn', '.qty-input', '.disc-input'
                ];

                selectors.forEach(selector => {
                    $(selector).prop('disabled', disabled);
                });

                // Handle radio buttons separately
                $('input[name="payment_method"]').prop('disabled', disabled);

                // Handle customer select (select2)
                $('#customerSelect').prop('disabled', disabled);

                if (disabled) {
                    $('#checkoutBtn').attr('title', 'This sale is already paid').prop('disabled', true).html('<i class="fas fa-check-circle me-2"></i>Already Paid');
                    showAlert('This sale is already paid and cannot be modified.', 'info');
                } else {
                    $('#checkoutBtn').removeAttr('title').prop('disabled', false).html('<i class="fas fa-bolt me-2"></i><span id="chargeBtnText">Charge</span>');
                }

                isPaidSale = !!disabled;

                // If it was just disabled, we might need to re-render the table to disable row inputs
                if (typeof renderUnifiedTable === 'function') {
                    renderUnifiedTable();
                }
            }

            $(document).ready(function () {
                // Current user and role info
                const currentUserId = {{ auth()->id() ?? 'null' }};
                const isEmployeeUser = @json(auth()->check() && auth()->user()->hasRole('employee'));

                // Staff list for JS
                const staffList = @json($staff->map(function ($staff) {
                    return [
                        'id' => $staff->id,
                        'name' => $staff->name,
                        'service_ids' => $staff->services->pluck('id')->toArray()
                    ];
                }));

                // Apply discount when button is clicked
                $('#applyDiscount').on('click', function () {
                    const discountValue = parseFloat($('#discountValue').val()) || 0;
                    discountType = $('#discountType').val();

                    if (discountType === 'percent') {
                        discountPercent = Math.min(discountValue, 100); // Cap at 100%
                        discountAmount = 0;
                        if (discountValue > 0) {
                            $('#discountValue').val(discountPercent);
                        }
                    } else {
                        discountAmount = Math.max(0, discountValue);
                        discountPercent = 0;
                    }

                    updateCart();
                });

                // Auto-apply on type change or input
                $('#discountType').on('change', function () {
                    $('#applyDiscount').click();
                });
                $('#discountValue').on('input', function () {
                    $('#applyDiscount').click();
                });

                // Also apply discount when pressing Enter in the discount input
                $('#discountValue').on('keypress', function (e) {
                    if (e.which === 13) { // Enter key
                        $('#applyDiscount').click();
                    }
                });

                // Remove discount when clear button is clicked
                $('#clearDiscount').on('click', function () {
                    $('#discountValue').val('');
                    discountPercent = 0;
                    discountAmount = 0;
                    updateCart();
                });

                // Salon Data
                const salonData = @json($salonData ?? []);
                // Get tax rate from config or default to 8%
                const taxRate = {{ number_format($taxRate ?? 0, 2) + 0 }};
                const taxEnabledPos = @json($taxEnabledPos ?? false);
                const taxEnabledServices = @json($taxEnabledServices ?? false);

                const currencySymbol = {!! json_encode($currencySymbol ?? '$') !!};
                const posReceiptArabicEnabled = @json($posReceiptArabic ?? true);

                // Initialize cart array
                let cart = [];
                let previousPaidAmount = 0;

                // Initialize discount and tip variables
                let discountPercent = 0;
                let discountAmount = 0;
                let tipAmount = 0;
                let cashAmount = 0;
                let cardAmount = 0;
                let onlineAmount = 0;
                let otherAmount = 0;
                let discountType = 'fixed';
                let isPaidSale = false;

                // Initialize customer select functionality
                $('#customerSelect').select2({
                    placeholder: 'Search customers by name or phone...',
                    allowClear: true,
                    width: '100%',
                    data: {!! json_encode($customersJson) !!},
                    ajax: {
                        url: '{{ route("admin.pos.customer.mobile.search") }}',
                        dataType: 'json',
                        delay: 300,
                        data: function (params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.customers.map(function (customer) {
                                    return {
                                        id: customer.id,
                                        text: customer.text,
                                        name: customer.name,
                                        phone: customer.phone,
                                        email: customer.email
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 1,
                    escapeMarkup: function (markup) {
                        return markup;
                    },
                    templateResult: function (customer) {
                        if (customer.loading) return customer.text;
                        return customer.text;
                    },
                    templateSelection: function (customer) {
                        return customer.text || customer.name || 'Walk-in Customer';
                    }
                });

                // Active Barber filter state
                let activeStaffId = null;
                let activeStaffName = null;

                // Navigation Pill Tabs Switching
                $('.pos-nav-pill').on('click', function () {
                    const tab = $(this).data('tab');
                    $('.pos-nav-pill').removeClass('active');
                    $(this).addClass('active');

                    // Show selected section, hide others
                    $('.grid-section').addClass('d-none');
                    $(`#${tab}GridSection`).removeClass('d-none');
                });

                // Barber Selection from Professionals tab
                $(document).on('click', '.btn-select-barber, .pos-pro-card', function (e) {
                    e.stopPropagation();
                    const staffId = $(this).data('staff-id');
                    const staffName = $(this).data('staff-name');
                    activeStaffId = staffId;
                    activeStaffName = staffName;

                    $('#activeStaffFilterName').text(staffName);
                    $('#activeStaffFilterBadge').removeClass('d-none');

                    // Switch view to Services
                    $('#tabPillServices').click();

                    // Filter services to this stylist
                    filterCatalogItems();
                    showAlert(`Filtering services for ${staffName}`, 'info');
                });

                // Clear Active Barber filter
                $('#clearActiveStaffFilter').on('click', function (e) {
                    e.stopPropagation();
                    activeStaffId = null;
                    activeStaffName = null;
                    $('#activeStaffFilterBadge').addClass('d-none');
                    filterCatalogItems();
                });

                // Category filter dropdown
                $('#categorySelect').on('change', function () {
                    filterCatalogItems();
                });

                // Search input filter
                $('#searchInput, #gridFilterInput').on('input', function () {
                    filterCatalogItems();
                });

                function filterCatalogItems() {
                    const selectedCat = $('#categorySelect').val();
                    const query = ($('#searchInput').val() || '').toLowerCase().trim();

                    // Filter Services
                    $('#servicesGridSection .pos-item-card').each(function () {
                        const $card = $(this);
                        const cardCatId = String($card.data('category-id') || '');
                        const staffIds = String($card.data('staff-ids') || '').split(',');
                        const name = String($card.data('name') || '').toLowerCase();
                        const price = String($card.data('price') || '');
                        const dur = String($card.data('duration') || '');

                        let matchCat = !selectedCat || (cardCatId === String(selectedCat));
                        let matchStaff = !activeStaffId || staffIds.includes(String(activeStaffId));
                        let matchQuery = !query || name.includes(query) || price.includes(query) || dur.includes(query);

                        if (matchCat && matchStaff && matchQuery) {
                            $card.removeClass('d-none');
                        } else {
                            $card.addClass('d-none');
                        }
                    });

                    // Filter other tabs
                    $('.grid-section:not(#servicesGridSection) .pos-item-card, .pos-pro-card').each(function () {
                        const name = ($(this).data('name') || $(this).data('staff-name') || '').toLowerCase();
                        if (!query || name.includes(query)) {
                            $(this).removeClass('d-none');
                        } else {
                            $(this).addClass('d-none');
                        }
                    });
                }

                // Click item card to add to cart directly
                $(document).on('click', '.pos-item-card, .item-card', function () {
                    const $card = $(this);
                    const type = $card.data('type');
                    const id = $card.data('id');
                    const name = $card.data('display-name') || $card.data('name');
                    const price = parseFloat($card.data('price') || 0);

                    // Check if this service can be covered by a package balance
                    let packageId = null;
                    let finalPrice = price;
                    let notes = '';

                    if (type === 'service') {
                        if (typeof customerPackageBalances !== 'undefined' && customerPackageBalances.length > 0) {
                            const balance = customerPackageBalances.find(b => b.service_id == id && b.quantity_remaining > 0);
                            if (balance) {
                                packageId = balance.package_id;
                                finalPrice = 0;
                                notes = 'Used from Package: ' + (balance.package ? balance.package.name : 'Balance');

                                // Optimistically decrement local balance
                                balance.quantity_remaining -= 1;
                            }
                        }
                    }

                    addToCart({
                        type: type,
                        id: id,
                        name: name,
                        price: finalPrice,
                        quantity: 1,
                        staff_id: (typeof isEmployeeUser !== 'undefined' && isEmployeeUser) ? currentUserId : (activeStaffId || null),
                        discount_amount: 0,
                        notes: notes,
                        package_id: packageId
                    });
                });

                // Load Booking into Order
                $(document).on('click', '.btn-load-booking', function () {
                    const btn = $(this);
                    const customerId = btn.data('customer-id');
                    const customerName = btn.data('customer-name');
                    const customerPhone = btn.data('customer-phone');
                    const customerEmail = btn.data('customer-email');
                    const serviceId = btn.data('service-id');
                    const serviceName = btn.data('service-name');
                    const servicePrice = parseFloat(btn.data('service-price') || 0);
                    const staffId = btn.data('staff-id');

                    if (customerId) {
                        selectCustomer(customerId, customerName, customerPhone, customerEmail);
                        $('#customerSelect').val(customerId).trigger('change');
                    }

                    if (serviceId) {
                        addToCart({
                            type: 'service',
                            id: serviceId,
                            name: serviceName,
                            price: servicePrice,
                            quantity: 1,
                            staff_id: staffId || activeStaffId || null,
                            discount_amount: 0,
                            notes: 'Loaded from Booking'
                        });
                    }

                    $('#tabPillServices').click();
                    showAlert('Booking loaded into order!', 'success');
                });

                // Quick Tip Percentage Pills
                $('.pos-tip-pill').on('click', function () {
                    $('.pos-tip-pill').removeClass('active');
                    $(this).addClass('active');

                    const pct = $(this).data('pct');
                    if (pct === 'custom') {
                        $('#tipAmount').focus().select();
                    } else {
                        const percentVal = parseFloat(pct) || 0;
                        const calculatedTip = Math.max(0, netSubtotal * (percentVal / 100));
                        $('#tipAmount').val(calculatedTip.toFixed(2));
                        tipAmount = calculatedTip;
                        updateCart(false);
                    }
                });

                // Payment Method Tiles (Cash, Card, Split, Online)
                $('.payment-device-tile').on('click', function () {
                    const method = $(this).data('method');
                    $('.payment-device-tile').removeClass('active');
                    $(this).addClass('active');

                    if (method === 'cash') {
                        $('#cartPaymentCash').prop('checked', true).trigger('change');
                    } else if (method === 'card') {
                        $('#cartPaymentCard').prop('checked', true).trigger('change');
                    } else if (method === 'mixed') {
                        $('#cartPaymentMixed').prop('checked', true).trigger('change');
                    } else if (method === 'online') {
                        $('#cartPaymentOnline').prop('checked', true).trigger('change');
                    }
                });

                // Quantity controls in Order List
                $(document).on('click', '.btn-qty-minus', function (e) {
                    e.stopPropagation();
                    const index = $(this).data('index');
                    if (cart[index]) {
                        if (cart[index].quantity > 1) {
                            cart[index].quantity -= 1;
                        } else {
                            cart.splice(index, 1);
                        }
                        updateCart();
                    }
                });

                $(document).on('click', '.btn-qty-plus', function (e) {
                    e.stopPropagation();
                    const index = $(this).data('index');
                    if (cart[index]) {
                        cart[index].quantity += 1;
                        updateCart();
                    }
                });

                // Handle customer selection
                $('#customerSelect').on('select2:select', function (e) {
                    const data = e.params.data;
                    selectCustomer(data.id, data.name, data.phone, data.email);
                });

                function selectCustomer(id, name, phone, email) {
                    $('#customerId').val(id);
                    $('#customerName').text(name);
                    $('#customerPhone').text(phone);
                    $('#customerEmail').text(email || '');
                    $('#customerDisplay').removeClass('d-none');
                    $('#customerSearchSection').addClass('d-none');

                    // Check for active membership
                    if (typeof checkCustomerMembership === 'function') {
                        checkCustomerMembership(id);
                    }

                    // Fetch available package balances
                    if (typeof fetchCustomerPackageBalances === 'function') {
                        fetchCustomerPackageBalances(id);
                    }
                }


                // Handle customer clearing
                $('#customerSelect').on('select2:clear', function () {
                    $('#customerId').val('');
                    $('#customerDisplay').addClass('d-none');
                    $('#customerSearchSection').removeClass('d-none');
                });

                // Clear customer button
                $('#clearCustomerBtn').on('click', function () {
                    $('#customerSelect').val('').trigger('change');
                    $('#customerId').val('');
                    $('#customerDisplay').addClass('d-none');
                    $('#customerSearchSection').removeClass('d-none');
                    $('#membershipInfo').addClass('d-none');
                    customerPackageBalances = [];
                    showAlert('Customer cleared', 'info');
                });

                // Handle new customer creation
                $('#addCustomerForm').on('submit', function (e) {
                    e.preventDefault();

                    if (!this.checkValidity()) {
                        e.stopPropagation();
                        $(this).addClass('was-validated');
                        return;
                    }

                    const formData = $(this).serialize();

                    $.ajax({
                        url: '{{ route("admin.customers.store") }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: formData,
                        success: function (response) {
                            if (response.status === 'success') {
                                // Add new customer to select2
                                const customer = response.customer;
                                // Format: Name (Phone)
                                const displayText = `${customer.name || customer.first_name + ' ' + (customer.last_name || '')} (${customer.phone})`;
                                const newOption = new Option(displayText, customer.id, true, true);
                                $('#customerSelect').append(newOption).trigger('change');

                                // Update display
                                $('#customerId').val(customer.id);
                                $('#customerName').text(customer.name || customer.first_name + ' ' + (customer.last_name || ''));
                                $('#customerPhone').text(customer.phone);
                                $('#customerEmail').text(customer.email || '');
                                $('#customerDisplay').removeClass('d-none');
                                $('#customerSearchSection').addClass('d-none');

                                // Check for active membership
                                checkCustomerMembership(customer.id);

                                // Hide modal
                                const modal = bootstrap.Modal.getInstance(document.getElementById('addCustomerModal'));
                                if (modal) {
                                    modal.hide();
                                }

                                // Reset form
                                $('#addCustomerForm')[0].reset();
                                $('#addCustomerForm').removeClass('was-validated');

                                showAlert('Customer added successfully', 'success');
                            } else {
                                showAlert(response.message || 'Error adding customer', 'danger');
                            }
                        },
                        error: function (xhr) {
                            let message = 'Error adding customer';
                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.errors) {
                                    // Get the first error message
                                    const firstError = Object.values(xhr.responseJSON.errors)[0];
                                    message = Array.isArray(firstError) ? firstError[0] : firstError;
                                } else {
                                    message = xhr.responseJSON.message || message;
                                }
                            }
                            showAlert(message, 'danger');
                        }
                    });
                });

                // Handle booking_id or sale_id from URL
                const urlParams = new URLSearchParams(window.location.search);
                const bookingId = urlParams.get('booking_id');
                const saleId = urlParams.get('sale_id');

                if (bookingId) {
                    $('#booking_id').val(bookingId);
                    fetchBookingDetails({ booking_id: bookingId });
                } else if (saleId) {
                    $('#existingSaleId').val(saleId);
                    fetchBookingDetails({ sale_id: saleId });
                }

                function fetchBookingDetails(params) {
                    $.ajax({
                        url: '{{ route("admin.pos.booking-details") }}',
                        method: 'GET',
                        data: params,
                        success: function (response) {
                            if (response.customer_id) {
                                // Select customer
                                const newOption = new Option(response.customer_name + ' (' + response.customer_phone + ')', response.customer_id, true, true);
                                $('#customerSelect').append(newOption).trigger('change');
                                selectCustomer(response.customer_id, response.customer_name, response.customer_phone, response.customer_email);
                            }

                            // Check for future bookings and disable "Unpaid" option
                            if (response.bookings && response.bookings.length > 0) {
                                const now = new Date();
                                const hasFutureBooking = response.bookings.some(b => new Date(b.start_time) > now);

                                if (hasFutureBooking) {
                                    $('#cartPaymentNone').prop('disabled', true);
                                    $('label[for="cartPaymentNone"]').addClass('opacity-50 text-muted').attr('title', 'Unpaid option not allowed for future bookings');
                                    if ($('#cartPaymentNone').is(':checked')) {
                                        $('#cartPaymentCash').prop('checked', true).trigger('change');
                                    }
                                } else {
                                    $('#cartPaymentNone').prop('disabled', false);
                                    $('label[for="cartPaymentNone"]').removeClass('opacity-50 text-muted').removeAttr('title');
                                }
                            }

                            if (response.sale_id) {
                                $('#existingSaleId').val(response.sale_id);


                                // Disable controls if sale is already paid
                                if (response.sale_data && response.sale_data.payment_status === 'paid') {
                                    togglePosControls(true);
                                } else {
                                    togglePosControls(false);
                                }
                            }
                            // Check for existing payments in the sale data
                            if (response.sale_data) {
                                // Restore discount if exists
                                if (response.sale_data.discount > 0) {
                                    discountAmount = parseFloat(response.sale_data.discount);
                                    discountType = 'fixed'; // Backend stores amount, so we restore as fixed
                                    $('#discountType').val('fixed');
                                    $('#discountValue').val(discountAmount.toFixed(2));
                                }

                                // Sum up all payment fields regardless of status to accurately track what's already paid
                                const cash = parseFloat(response.sale_data.cash_amount) || 0;
                                const card = parseFloat(response.sale_data.card_amount) || 0;
                                const online = parseFloat(response.sale_data.online_amount) || 0;
                                const other = parseFloat(response.sale_data.other_amount) || 0;

                                // Reliable previous payment calculation: Total - Remaining Balance
                                const total = parseFloat(response.sale_data.total) || 0;
                                const outstanding = parseFloat(response.sale_data.outstanding_amount) || 0;
                                const calculatedPaid = Math.max(0, total - outstanding);

                                // Fallback to sum of fields if calculatedPaid is 0 but fields are not (shouldn't happen with correct DB state)
                                previousPaidAmount = calculatedPaid > 0 ? calculatedPaid : (cash + card + online + other);

                                if (previousPaidAmount > 0) {
                                    // Update cart will be called after adding items
                                    showAlert(`Existing payment found: ${formatCurrency(previousPaidAmount)}`, 'info');
                                }
                            } else {
                                previousPaidAmount = 0;
                            }

                            if (response.items && response.items.length > 0) {
                                cart = []; // Clear current cart to avoid duplicates when loading existing sale
                                response.items.forEach(item => {
                                    addToCart(item);
                                });
                                showAlert('Sale items loaded successfully', 'success');
                            } else if (response.bookings && response.bookings.length > 0) {
                                let addedCount = 0;
                                const processedPackages = new Set();

                                response.bookings.forEach(booking => {
                                    // Skip completed bookings to prevent double charging
                                    if (booking.status === 'completed') return;

                                    if (booking.package_id) {
                                        // Handle package booking
                                        // Check for explicit New Purchase status (2)
                                        if (booking.package_service_status === 2) {
                                            // Explicit New Package Purchase
                                            if (!processedPackages.has(booking.package_id)) {
                                                const packageItem = {
                                                    type: 'package',
                                                    id: booking.package_id,
                                                    name: booking.package_name,
                                                    price: parseFloat(booking.package_price),
                                                    quantity: 1,
                                                    staff_id: booking.staff_id,
                                                    notes: `New Package Purchase (from Booking)`,
                                                    booking_id: booking.id
                                                };

                                                // Include selected_services if available (for customizable packages)
                                                if (booking.selected_services && Array.isArray(booking.selected_services)) {
                                                    packageItem.selected_services = booking.selected_services;
                                                }

                                                addToCart(packageItem);
                                                processedPackages.add(booking.package_id);
                                                addedCount++;
                                            }

                                            // Add service item linked to package (price 0)
                                            addToCart({
                                                type: 'service',
                                                id: booking.service_id,
                                                name: booking.service_name,
                                                price: 0, // Price included in new package
                                                quantity: 1,
                                                staff_id: booking.staff_id,
                                                notes: `Included in New Package`,
                                                booking_id: booking.id,
                                                package_id: booking.package_id
                                            });
                                            addedCount++;
                                        }
                                        // Check if customer has balance for this package service
                                        // Use server-side flag to avoid race condition with fetchCustomerPackageBalances
                                        else if (booking.has_package_balance) {
                                            // 1. Has balance for this specific service: REDEEM
                                            addToCart({
                                                type: 'service',
                                                id: booking.service_id,
                                                name: booking.service_name,
                                                price: 0, // Price included in package
                                                quantity: 1,
                                                staff_id: booking.staff_id,
                                                notes: `Used from Package: ${booking.package_name}`,
                                                booking_id: booking.id,
                                                package_id: booking.package_id
                                            });

                                            // Try to decrement local balance if available (for UI sync)
                                            const balance = customerPackageBalances.find(b => b.service_id == booking.service_id && b.package_id == booking.package_id && b.quantity_remaining > 0);
                                            if (balance) {
                                                balance.quantity_remaining -= 1;
                                            }
                                            addedCount++;
                                        } else {
                                            // 3. No balance for this service: Check if customer OWNS the package
                                            if (booking.has_any_package_balance) {
                                                // Customer owns package but can't redeem this service (e.g. limit reached)
                                                // Charge for service independently
                                                addToCart({
                                                    type: 'service',
                                                    id: booking.service_id,
                                                    name: booking.service_name,
                                                    price: parseFloat(booking.service_price) || 0, // Use full service price
                                                    quantity: 1,
                                                    staff_id: booking.staff_id,
                                                    notes: `Package Service (Paid - Limit Reached/Unavailable)`,
                                                    booking_id: booking.id
                                                    // Do NOT include package_id
                                                });
                                                addedCount++;
                                            } else {
                                                // Customer does NOT own package -> New Package Purchase
                                                // (This handles cases where status might not be set correctly, legacy fallback)
                                                if (!processedPackages.has(booking.package_id)) {
                                                    const packageItem = {
                                                        type: 'package',
                                                        id: booking.package_id,
                                                        name: booking.package_name,
                                                        price: parseFloat(booking.package_price),
                                                        quantity: 1,
                                                        staff_id: booking.staff_id,
                                                        notes: `New Package Purchase (from Booking)`,
                                                        booking_id: booking.id
                                                    };

                                                    // Include selected_services if available (for customizable packages)
                                                    if (booking.selected_services && Array.isArray(booking.selected_services)) {
                                                        packageItem.selected_services = booking.selected_services;
                                                    }

                                                    addToCart(packageItem);
                                                    processedPackages.add(booking.package_id);
                                                    addedCount++;
                                                }

                                                // Add service item linked to package (price 0)
                                                addToCart({
                                                    type: 'service',
                                                    id: booking.service_id,
                                                    name: booking.service_name,
                                                    price: 0, // Price included in new package
                                                    quantity: 1,
                                                    staff_id: booking.staff_id,
                                                    notes: `Included in New Package`,
                                                    booking_id: booking.id,
                                                    package_id: booking.package_id
                                                });
                                                addedCount++;
                                            }
                                        }
                                    } else {
                                        // Handle regular service booking
                                        addToCart({
                                            type: 'service',
                                            id: booking.service_id,
                                            name: booking.service_name,
                                            price: parseFloat(booking.price),
                                            quantity: 1,
                                            staff_id: booking.staff_id,
                                            notes: `From Booking #${booking.id}`,
                                            booking_id: booking.id
                                        });
                                        addedCount++;
                                    }
                                });

                                if (addedCount > 0) {
                                    showAlert('Booking details loaded successfully', 'success');
                                } else {
                                    showAlert('All bookings in this group are already paid.', 'info');
                                }
                            }
                        },
                        error: function () {
                            showAlert('Error loading booking details', 'danger');
                        }
                    });
                }

                // Search functionality
                let searchTimeout;
                $('#searchInput').on('input', function () {
                    clearTimeout(searchTimeout);
                    const query = $(this).val().trim();

                    if (query.length < 2) {
                        $('#searchResults').addClass('d-none');
                        return;
                    }

                    searchTimeout = setTimeout(() => {
                        $.ajax({
                            url: '{{ route("admin.pos.search") }}',
                            method: 'GET',
                            data: { q: query },
                            success: function (response) {
                                const resultsList = $('#searchResultsList');
                                resultsList.empty();

                                if (response.services.length === 0 && response.products.length === 0) {
                                    resultsList.append('<div class="list-group-item text-muted">No results found</div>');
                                } else {
                                    // Add services to results
                                    if (response.services.length > 0) {
                                        resultsList.append('<div class="list-group-item bg-light fw-bold">Services</div>');
                                        response.services.forEach(service => {
                                            resultsList.append('<a href="#" class="list-group-item list-group-item-action search-result-item" ' +
                                                'data-type="service" ' +
                                                'data-id="' + service.id + '" ' +
                                                'data-name="' + service.name + '" ' +
                                                'data-price="' + service.price + '">' +
                                                '<div class="d-flex justify-content-between align-items-center">' +
                                                '<div>' +
                                                '<h6 class="mb-0">' + service.name + '</h6>' +
                                                '<small class="text-muted">' + (service.description || '') + '</small>' +
                                                '</div>' +
                                                '<span class="badge bg-primary">' + formatCurrency(service.price) + '</span>' +
                                                '</div>' +
                                                '</a>');
                                        });
                                    }

                                    // Add products to results
                                    if (response.products.length > 0) {
                                        resultsList.append('<div class="list-group-item bg-light fw-bold mt-2">Products</div>');
                                        response.products.forEach(product => {
                                            resultsList.append('<a href="#" class="list-group-item list-group-item-action search-result-item" ' +
                                                'data-type="product" ' +
                                                'data-id="' + product.id + '" ' +
                                                'data-name="' + product.name + '" ' +
                                                'data-price="' + product.price + '" ' +
                                                'data-quantity="' + product.quantity + '">' +
                                                '<div class="d-flex justify-content-between align-items-center">' +
                                                '<div>' +
                                                '<h6 class="mb-0">' + product.name + '</h6>' +
                                                '<small class="text-muted">' + product.quantity + ' in stock</small>' +
                                                '</div>' +
                                                '<span class="badge bg-success">' + formatCurrency(product.price) + '</span>' +
                                                '</div>' +
                                                '</a>');
                                        });
                                    }
                                }

                                $('#searchResults').removeClass('d-none');
                            },
                            error: function () {
                                showAlert('Error searching for items', 'danger');
                            }
                        });
                    }, 300);
                });

                // Handle click on search result item
                $(document).on('click', '.search-result-item', function (e) {
                    e.preventDefault();
                    const type = $(this).data('type');
                    const id = $(this).data('id');
                    const name = $(this).data('name');
                    const price = parseFloat($(this).data('price'));
                    const quantity = parseInt($(this).data('quantity') || 1);

                    if (type === 'product' && quantity <= 0) {
                        showAlert('This product is out of stock', 'warning');
                        return;
                    }

                    // Check if this service can be covered by a package balance
                    let packageId = null;
                    let finalPrice = price;
                    let notes = '';

                    if (type === 'service') {
                        const balance = customerPackageBalances.find(b => b.service_id == id && b.quantity_remaining > 0);
                        if (balance) {
                            packageId = balance.package_id;
                            finalPrice = 0;
                            notes = 'Used from Package: ' + (balance.package ? balance.package.name : 'Balance');

                            // Optimistically decrement local balance
                            balance.quantity_remaining -= 1;
                        }
                    }

                    addToCart({
                        type: type,
                        id: id,
                        name: name,
                        price: finalPrice,
                        quantity: 1,
                        staff_id: (typeof isEmployeeUser !== 'undefined' && isEmployeeUser) ? currentUserId : null,
                        package_id: packageId,
                        notes: notes
                    });

                    $('#searchInput').val('');
                    $('#searchResults').addClass('d-none');
                });



                // Add item to cart
                function addToCart(item) {
                    let existingItem = null;
                    if (item.type === 'service' && item.package_id) {
                        // For package services, use service_id as id, type service
                        existingItem = cart.find(i => i.type === 'service' && i.id === item.id && i.package_id === item.package_id);
                    } else {
                        existingItem = cart.find(i => i.type === item.type && i.id === item.id);
                    }

                    if (existingItem) {
                        existingItem.quantity += (item.quantity || 1);
                        existingItem.discount_amount = (parseFloat(existingItem.discount_amount) || 0) + (parseFloat(item.discount_amount) || 0);
                        existingItem.total = (existingItem.quantity * existingItem.price) - existingItem.discount_amount;

                        // Merge booking IDs
                        if (item.booking_id) {
                            if (!existingItem.booking_ids) existingItem.booking_ids = [];
                            if (!existingItem.booking_ids.includes(item.booking_id)) {
                                existingItem.booking_ids.push(item.booking_id);
                            }
                        }
                    } else {
                        const defaultStaffId = (typeof isEmployeeUser !== 'undefined' && isEmployeeUser) ? currentUserId : null;
                        const cartItem = {
                            ...item,
                            quantity: item.quantity || 1,
                            total: (item.price || 0) * (item.quantity || 1),
                            staff_id: item.staff_id || defaultStaffId,
                            discount_amount: item.discount_amount || 0,
                            notes: item.notes || '',
                            editable: true,
                            booking_ids: item.booking_id ? [item.booking_id] : []
                        };
                        if (item.type === 'service' && item.package_id) {
                            cartItem.type = 'service';
                            cartItem.id = item.id; // service_id
                            cartItem.package_id = item.package_id;
                            cartItem.price = 0;
                        }
                        cart.push(cartItem);
                    }
                    updateCart();
                    const itemType = item.type === 'package' ? 'Package' : item.name;
                    showAlert(`${itemType} added to cart`, 'success');
                }



                // Dynamic service add functionality
                $(document).ready(function () {

                    // Helper: get all checked service checkboxes
                    function getCheckedServices() {
                        return $('.service-checkbox:checked');
                    }

                    // Update label, price and staff filter on checkbox change
                    $(document).on('change', '.service-checkbox', function () {
                        const $checked = getCheckedServices();
                        const count = $checked.length;

                        // Update dropdown button label
                        if (count === 0) {
                            $('#servicesDropdownLabel').text('Select Services');
                        } else if (count === 1) {
                            $('#servicesDropdownLabel').text($checked.first().data('name'));
                        } else {
                            $('#servicesDropdownLabel').text(count + ' Services Selected');
                        }

                        // Sum prices
                        let totalPrice = 0;
                        $checked.each(function () {
                            totalPrice += parseFloat($(this).data('price') || 0);
                        });
                        $('#addPriceDisplay').val(formatCurrency(totalPrice));
                        calculateDynamicTotal();

                        // Filter staff based on selected service IDs
                        const serviceIds = $checked.map(function () { return $(this).val(); }).get();
                        filterAddStaffSelect(serviceIds);
                    });

                    // Keep dropdown open when clicking inside it
                    $(document).on('click', '.dropdown-menu', function (e) {
                        e.stopPropagation();
                    });

                    function filterAddStaffSelect(serviceIds) {
                        const $staffSelect = $('#addStaffSelect');
                        const currentVal = $staffSelect.val();

                        $staffSelect.find('option:not(:first)').remove();

                        let list = typeof staffList !== 'undefined' ? staffList : [];
                        if (typeof isEmployeeUser !== 'undefined' && isEmployeeUser && typeof currentUserId !== 'undefined' && currentUserId) {
                            list = list.filter(staff => String(staff.id) === String(currentUserId));
                        }
                        if (!serviceIds || serviceIds.length === 0) {
                            list.forEach(staff => {
                                $staffSelect.append(`<option value="${staff.id}">${staff.name}</option>`);
                            });
                        } else {
                            const idsArray = serviceIds.map(String);
                            const filteredStaff = list.filter(staff => {
                                return (staff.service_ids && staff.service_ids.some(id => idsArray.includes(String(id)))) ||
                                    (currentVal && String(staff.id) === String(currentVal));
                            });
                            filteredStaff.forEach(staff => {
                                $staffSelect.append(`<option value="${staff.id}">${staff.name}</option>`);
                            });
                        }

                        if (currentVal && $staffSelect.find(`option[value="${currentVal}"]`).length > 0) {
                            $staffSelect.val(currentVal);
                        } else if (typeof isEmployeeUser !== 'undefined' && isEmployeeUser && $staffSelect.find(`option[value="${currentUserId}"]`).length > 0) {
                            $staffSelect.val(currentUserId);
                        } else {
                            $staffSelect.val('');
                        }
                    }

                    // Toggle service card selection
                    $(document).on('click', '.service-card', function (e) {
                        if ($(e.target).is('input')) return;

                        const $card = $(this);
                        const $checkbox = $card.find('.service-checkbox');
                        const isChecked = !$checkbox.prop('checked');

                        $checkbox.prop('checked', isChecked).trigger('change');

                        if (isChecked) {
                            $card.addClass('selected');
                            $card.find('.selected-checkmark').removeClass('d-none');
                        } else {
                            $card.removeClass('selected');
                            $card.find('.selected-checkmark').addClass('d-none');
                        }
                    });

                    // Update total when qty or disc changes
                    $('#addQtyInput, #addDiscInput').on('input', function () {
                        calculateDynamicTotal();
                    });

                    // Calculate dynamic total from checked checkboxes
                    function calculateDynamicTotal() {
                        const $checked = getCheckedServices();
                        if ($checked.length > 0) {
                            let totalPrice = 0;
                            $checked.each(function () {
                                totalPrice += parseFloat($(this).data('price') || 0);
                            });
                            const qty = parseFloat($('#addQtyInput').val() || 1);
                            const disc = parseFloat($('#addDiscInput').val() || 0);
                            $('#addTotalDisplay').val(formatCurrency((totalPrice * qty) - disc));
                        } else {
                            $('#addTotalDisplay').val(formatCurrency(0));
                        }
                    }

                    // Add service button click
                    $('#addServiceBtn').on('click', function () {
                        const $checked = getCheckedServices();
                        if ($checked.length === 0) {
                            showAlert('Please select at least one service', 'warning');
                            return;
                        }

                        const staffId = $('#addStaffSelect').val();
                        const qty = parseFloat($('#addQtyInput').val() || 1);
                        const disc = parseFloat($('#addDiscInput').val() || 0);

                        if (qty < 1) {
                            showAlert('Quantity must be at least 1', 'warning');
                            return;
                        }

                        // Process each checked service
                        $checked.each(function () {
                            const $cb = $(this);
                            const serviceId = $cb.val();
                            const serviceName = $cb.data('name');
                            const servicePrice = parseFloat($cb.data('price') || 0);

                            let packageId = null;
                            let finalPrice = servicePrice;
                            let notes = '';

                            const balance = customerPackageBalances.find(b => b.service_id == serviceId && b.quantity_remaining > 0);
                            if (balance) {
                                packageId = balance.package_id;
                                finalPrice = 0;
                                notes = 'Used from Package: ' + (balance.package ? balance.package.name : 'Balance');
                                balance.quantity_remaining -= qty;
                            }

                            addToCart({
                                type: 'service',
                                id: serviceId,
                                name: serviceName,
                                price: finalPrice,
                                quantity: qty,
                                staff_id: staffId || null,
                                discount_amount: disc,
                                notes: notes,
                                package_id: packageId
                            });
                        });

                        // Reset form
                        $('.service-checkbox').prop('checked', false);
                        $('.service-card').removeClass('selected');
                        $('.selected-checkmark').addClass('d-none');
                        $('#servicesDropdownLabel').text('Select Services');
                        $('#addStaffSelect').val('');
                        $('#addQtyInput').val('1');
                        $('#addDiscInput').val('0');
                        $('#addPriceDisplay').val('0.00');
                        $('#addTotalDisplay').val('0.00');

                        showAlert('Services added successfully', 'success');
                    });

                });

                // Dynamic product add functionality
                $(document).ready(function () {
                    // Update price and total when product selected
                    $('#addProductSelect').on('change', function () {
                        const selectedOption = $(this).find(':selected');
                        const price = parseFloat(selectedOption.data('price') || 0);
                        const stock = parseInt(selectedOption.data('stock') || 0);
                        $('#addProductPriceDisplay').val(formatCurrency(price));
                        $('#addProductQtyInput').attr('max', stock);
                        calculateProductTotal();
                    });

                    // Update total when qty or disc changes
                    $('#addProductQtyInput, #addProductDiscInput').on('input', function () {
                        calculateProductTotal();
                    });

                    // Calculate dynamic total
                    function calculateProductTotal() {
                        const productSelect = $('#addProductSelect');
                        if (productSelect.val()) {
                            const selectedOption = productSelect.find(':selected');
                            const price = parseFloat(selectedOption.data('price') || 0);
                            const qty = parseFloat($('#addProductQtyInput').val() || 1);
                            const disc = parseFloat($('#addProductDiscInput').val() || 0);
                            const total = (price * qty) - disc;
                            $('#addProductTotalDisplay').val(formatCurrency(total));
                        } else {
                            $('#addProductTotalDisplay').val(formatCurrency(0));
                        }
                    }

                    // Add product button click
                    $('#addProductBtn').on('click', function () {
                        const productId = $('#addProductSelect').val();
                        if (!productId) {
                            showAlert('Please select a product', 'warning');
                            return;
                        }

                        const selectedOption = $('#addProductSelect').find(':selected');
                        const productName = selectedOption.data('name');
                        const productPrice = parseFloat(selectedOption.data('price') || 0);
                        const stock = parseInt(selectedOption.data('stock') || 0);
                        const staffId = $('#addProductStaffSelect').val();
                        const qty = parseFloat($('#addProductQtyInput').val() || 1);
                        const disc = parseFloat($('#addProductDiscInput').val() || 0);

                        if (qty < 1) {
                            showAlert('Quantity must be at least 1', 'warning');
                            return;
                        }

                        if (qty > stock) {
                            showAlert('Only ' + stock + ' items available in stock', 'warning');
                            return;
                        }

                        // Add to cart
                        const item = {
                            type: 'product',
                            id: productId,
                            name: productName,
                            price: productPrice,
                            quantity: qty,
                            staff_id: staffId || null,
                            discount_amount: disc,
                            notes: ''
                        };
                        addToCart(item);

                        // Reset form
                        $('#addProductSelect').val('');
                        $('#addProductStaffSelect').val('');
                        $('#addProductQtyInput').val('1');
                        $('#addProductDiscInput').val('0');
                        $('#addProductPriceDisplay').val('0.00');
                        $('#addProductTotalDisplay').val('0.00');
                    });

                });

                // Render unified cart table
                function renderUnifiedTable() {
                    const $body = $('#unifiedCartBody');
                    $body.empty();

                    if (cart.length === 0) {
                        $('.pos-item-card').removeClass('selected');
                        $('#emptyOrderPlaceholder').removeClass('d-none').show();
                        $('#orderCardsContainer').addClass('d-none').hide().empty();

                        $body.append(`<tr id="emptyCartRow">
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-shopping-cart fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">Your cart is empty. Add items to start billing.</p>
                            </td>
                        </tr>`);
                        return;
                    }

                    // Highlight selected cards in catalog
                    $('.pos-item-card').removeClass('selected');
                    cart.forEach(item => {
                        $(`.pos-item-card[data-type="${item.type}"][data-id="${item.id}"]`).addClass('selected');
                    });

                    // Render modern Barber-grouped order cards
                    $('#emptyOrderPlaceholder').addClass('d-none').hide();
                    const $cardsContainer = $('#orderCardsContainer');
                    $cardsContainer.removeClass('d-none').show().empty();

                    const groups = {};

                    cart.forEach((item, index) => {
                        const parentPackage = cart.find(i => i.type === 'package' && i.id == item.package_id);
                        if (item.package_id && item.price === 0 && parentPackage) return;

                        let groupKey = 'unassigned';
                        let groupTitle = 'General Services';
                        let groupAvatar = null;
                        let groupSubtitle = 'Walk-in / Unassigned Stylist';

                        if (item.type === 'product') {
                            groupKey = 'products';
                            groupTitle = 'Products & Retail';
                            groupSubtitle = 'In-store inventory';
                        } else if (item.type === 'package' || item.type === 'membership') {
                            groupKey = 'packages';
                            groupTitle = 'Packages & Memberships';
                            groupSubtitle = 'Prepaid / Subscriptions';
                        } else if (item.staff_id) {
                            const staffObj = typeof staffList !== 'undefined' ? staffList.find(s => String(s.id) === String(item.staff_id)) : null;
                            groupKey = 'staff_' + item.staff_id;
                            groupTitle = staffObj ? staffObj.name : 'Barber #' + item.staff_id;
                            groupAvatar = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(groupTitle) + '&background=DBEAFE&color=1D4ED8';
                            groupSubtitle = 'Assigned Stylist';
                        }

                        if (!groups[groupKey]) {
                            groups[groupKey] = {
                                key: groupKey,
                                title: groupTitle,
                                subtitle: groupSubtitle,
                                avatar: groupAvatar,
                                staffId: item.staff_id,
                                items: [],
                                subtotal: 0
                            };
                        }

                        const lineTotal = (item.price * item.quantity) - (item.discount_amount || 0);
                        groups[groupKey].items.push({ item: item, index: index, lineTotal: lineTotal });
                        groups[groupKey].subtotal += lineTotal;
                    });

                    Object.values(groups).forEach(group => {
                        let itemsHtml = '';
                        group.items.forEach(({ item, index, lineTotal }) => {
                            let staffSelectOptions = '';
                            if (item.type === 'service') {
                                let filtered = typeof staffList !== 'undefined' ? staffList : [];
                                staffSelectOptions = `<select class="pos-barber-select staff-select" data-index="${index}" ${isPaidSale ? 'disabled' : ''}>
                                    <option value="">👤 Stylist</option>
                                    ${filtered.map(s => `<option value="${s.id}" ${s.id == item.staff_id ? 'selected' : ''}>✂️ ${s.name}</option>`).join('')}
                                </select>`;
                            }

                            // Per-item discount display
                            const itemDiscAmt  = item.discount_amount  || 0;
                            const itemDiscPct  = item.discount_percent  || 0;
                            let discDisplayHtml = '';
                            if (!isPaidSale) {
                                const discVal = itemDiscPct > 0 ? itemDiscPct : itemDiscAmt;
                                const discType = itemDiscPct > 0 ? 'percent' : 'fixed';
                                discDisplayHtml = `
                                    <div class="pos-item-disc-control" title="Item Discount (${discType === 'percent' ? '%' : currencySymbol})">
                                        <input type="number" class="disc-input"
                                            data-index="${index}"
                                            data-disc-type="${discType}"
                                            value="${discVal > 0 ? discVal : ''}"
                                            placeholder="Disc" min="0" step="0.01">
                                        <select class="disc-type-select" data-index="${index}">
                                            <option value="fixed"  ${discType === 'fixed'   ? 'selected' : ''}>${currencySymbol}</option>
                                            <option value="percent"${discType === 'percent' ? 'selected' : ''}>%</option>
                                        </select>
                                        ${itemDiscAmt > 0 || itemDiscPct > 0 ? `<button type="button" class="disc-clear-btn btn-clear-item-discount" data-index="${index}" title="Clear"><i class="fas fa-times"></i></button>` : ''}
                                    </div>`;
                            } else if (itemDiscAmt > 0) {
                                discDisplayHtml = `<span style="font-size:0.65rem;color:#DC2626;">-${formatCurrency(itemDiscAmt)}</span>`;
                            }

                            itemsHtml += `
                                <div class="pos-order-item-row py-1 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="pos-order-item-title fw-semibold text-dark text-truncate me-2" style="font-size: 0.8rem; max-width: 160px;" title="${item.name}">${item.name}</div>
                                        <div class="pos-order-item-price fw-bold text-dark text-nowrap" style="font-size: 0.825rem;">${formatCurrency(lineTotal)}</div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1 gap-1">
                                        <div class="d-flex align-items-center gap-1 flex-grow-1" style="min-width: 0;">
                                            ${staffSelectOptions}
                                            ${discDisplayHtml}
                                            ${item.notes ? `<div class="text-muted extra-small" style="font-size: 0.65rem;">${item.notes}</div>` : ''}
                                        </div>
                                        <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                            <button type="button" class="qty-btn btn-qty-minus" data-index="${index}" ${isPaidSale ? 'disabled' : ''}>-</button>
                                            <span class="px-1 fw-bold text-dark" style="font-size: 0.775rem;">${item.quantity}</span>
                                            <button type="button" class="qty-btn btn-qty-plus" data-index="${index}" ${isPaidSale ? 'disabled' : ''}>+</button>
                                            <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1 btn-remove" data-index="${index}" ${isPaidSale ? 'disabled' : ''} title="Remove">
                                                <i class="fas fa-trash-alt" style="font-size: 0.75rem;"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });

                        const avatarMarkup = group.avatar
                            ? `<img src="${group.avatar}" alt="${group.title}" class="pos-barber-avatar">`
                            : `<div class="pos-barber-avatar bg-light d-flex align-items-center justify-content-center text-primary"><i class="fas ${group.key === 'products' ? 'fa-box' : 'fa-cut'}"></i></div>`;

                        const hasGroupHeader = group.key !== 'unassigned';
                        const groupCardHtml = `
                            <div class="pos-barber-order-card mb-1">
                                ${hasGroupHeader ? `
                                    <div class="pos-barber-header py-1 px-2">
                                        <div class="d-flex align-items-center gap-2">
                                            ${avatarMarkup}
                                            <div>
                                                <div class="pos-barber-name" style="font-size: 0.775rem;">${group.title}</div>
                                                <div class="pos-barber-subtitle" style="font-size: 0.625rem;">${group.subtitle}</div>
                                            </div>
                                        </div>
                                        <div class="pos-barber-total" style="font-size: 0.8rem;">${formatCurrency(group.subtotal)}</div>
                                    </div>
                                ` : ''}
                                <div class="pos-order-items-list px-2 py-0">
                                    ${itemsHtml}
                                </div>
                            </div>
                        `;

                        $cardsContainer.append(groupCardHtml);
                    });

                    cart.forEach((item, index) => {
                        // Check if this is a sub-item of a package that is ALSO in the cart
                        const parentPackage = cart.find(i => i.type === 'package' && i.id == item.package_id);
                        if (item.package_id && item.price === 0 && parentPackage) return;

                        if (!item.staff_id && typeof isEmployeeUser !== 'undefined' && isEmployeeUser && typeof currentUserId !== 'undefined' && currentUserId) {
                            item.staff_id = currentUserId;
                        }

                        let filteredStaffList = typeof staffList !== 'undefined' ? staffList : [];
                        if (typeof isEmployeeUser !== 'undefined' && isEmployeeUser && typeof currentUserId !== 'undefined' && currentUserId) {
                            filteredStaffList = filteredStaffList.filter(staff => String(staff.id) === String(currentUserId));
                        } else if (item.type === 'service') {
                            filteredStaffList = filteredStaffList.filter(staff =>
                                (staff.service_ids && staff.service_ids.map(String).includes(String(item.id))) ||
                                (item.staff_id && String(staff.id) === String(item.staff_id))
                            );
                        }
                        const staffOptions = filteredStaffList.map(staff =>
                            `<option value="${staff.id}" ${staff.id == item.staff_id ? 'selected' : ''}>${staff.name}</option>`
                        ).join('');

                        const staffSelect = `<select class="form-select form-select-sm staff-select" data-index="${index}" style="min-width: 100px;" ${isPaidSale ? 'disabled' : ''}>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <option value="">Select Barber</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ${staffOptions}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </select>`;

                        const qtyInput = `<input type="number" class="form-control form-control-sm text-center quantity-input" 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    data-index="${index}" value="${item.quantity}" min="1" style="width: 60px; margin: 0 auto;" ${isPaidSale ? 'disabled' : ''}>`;

                        const discInput = `
                                    <div class="input-group input-group-sm" style="width: 105px; margin-left: auto;">
                                        <input type="number" class="form-control text-end disc-input" 
                                            data-index="${index}" value="${item.discount_amount || 0}" min="0" step="0.01" ${isPaidSale ? 'disabled' : ''}>
                                        <button class="btn btn-outline-danger btn-clear-item-discount py-0 px-2" type="button" data-index="${index}" ${isPaidSale ? 'disabled' : ''} title="Clear Discount">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                `;

                        const removeBtn = `<button class="btn btn-sm btn-outline-danger btn-remove" data-index="${index}" ${isPaidSale ? 'disabled' : ''}>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <i class="fas fa-trash"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </button>`;

                        const total = (item.price * item.quantity) - (item.discount_amount || 0);

                        let icon = 'fa-spa';
                        let colorClass = 'text-primary';
                        let typeLabel = item.type;

                        if (item.type === 'product') { icon = 'fa-box'; colorClass = 'text-success'; }
                        else if (item.type === 'package') { icon = 'fa-gift'; colorClass = 'text-info'; }
                        else if (item.type === 'membership') { icon = 'fa-star'; colorClass = 'text-secondary'; }
                        else if (item.type === 'service' && item.package_id) {
                            if (item.price === 0) {
                                icon = 'fa-check-circle';
                                colorClass = 'text-success';
                                typeLabel = 'Redeemed';
                            } else {
                                icon = 'fa-box-open';
                                colorClass = 'text-info';
                                typeLabel = 'Package Service';
                            }
                        }

                        let rowHtml = `<tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <td class="ps-3">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="d-flex align-items-center">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="icon-shape icon-sm bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <i class="fas ${icon} ${colorClass}"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="fw-semibold small">${item.name}</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="text-muted extra-small text-uppercase" style="font-size: 0.65rem;">${typeLabel}</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <td class="text-center">${staffSelect}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <td class="text-center">${qtyInput}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <td class="text-end small">${formatCurrency(item.price)}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <td class="text-end">${discInput}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <td class="text-end fw-bold small">${formatCurrency(total)}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <td class="text-center">${removeBtn}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </tr>`;

                        $body.append(rowHtml);

                        // If it's a package, render its services
                        if (item.type === 'package') {
                            const packageServices = cart.filter(i => i.type === 'service' && i.package_id == item.id && i.price === 0);
                            packageServices.forEach(subItem => {
                                const subIndex = cart.indexOf(subItem);
                                if (!subItem.staff_id && typeof isEmployeeUser !== 'undefined' && isEmployeeUser && typeof currentUserId !== 'undefined' && currentUserId) {
                                    subItem.staff_id = currentUserId;
                                }
                                let filteredSubStaffList = typeof staffList !== 'undefined' ? staffList : [];
                                if (typeof isEmployeeUser !== 'undefined' && isEmployeeUser && typeof currentUserId !== 'undefined' && currentUserId) {
                                    filteredSubStaffList = filteredSubStaffList.filter(staff => String(staff.id) === String(currentUserId));
                                } else if (subItem.type === 'service') {
                                    filteredSubStaffList = filteredSubStaffList.filter(staff =>
                                        (staff.service_ids && staff.service_ids.map(String).includes(String(subItem.id))) ||
                                        (subItem.staff_id && String(staff.id) === String(subItem.staff_id))
                                    );
                                }
                                const subStaffOptions = filteredSubStaffList.map(staff =>
                                    `<option value="${staff.id}" ${staff.id == subItem.staff_id ? 'selected' : ''}>${staff.name}</option>`
                                ).join('');

                                const subRowHtml = `<tr class="bg-light bg-opacity-50">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td class="ps-5">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="d-flex align-items-center">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <i class="fas fa-level-up-alt fa-rotate-90 me-2 text-muted small"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <span class="small text-muted" style="font-size: 0.75rem;">${subItem.name}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td class="text-center">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     <select class="form-select form-select-sm staff-select" data-index="${subIndex}" style="min-width: 100px;" ${isPaidSale ? 'disabled' : ''}>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <option value="">Select Barber</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ${subStaffOptions}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </select>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td class="text-center small text-muted">1</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td class="text-end small text-muted">-</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td class="text-end">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <input type="number" class="form-control form-control-sm text-end disc-input" 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        data-index="${subIndex}" value="${subItem.discount_amount || 0}" min="0" step="0.01" style="width: 80px; margin-left: auto;" ${isPaidSale ? 'disabled' : ''}>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td class="text-end small text-muted">-</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td class="text-center">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <button class="btn btn-xs btn-outline-info btn-remove" data-index="${subIndex}" title="Save for later" ${isPaidSale ? 'disabled' : ''}>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <i class="fas fa-history"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </tr>`;
                                $body.append(subRowHtml);
                            });
                        }
                    });
                }

                // Store DataTable instance
                let cartDataTable = null;

                // Format currency helper
                function formatCurrency(amount) {
                    return currencySymbol + parseFloat(amount || 0).toFixed(2);
                }

                // Update cart display
                function updateCart(shouldRender = true) {
                    // Reset totals
                    grossSubtotal = 0;
                    totalItemDiscount = 0;
                    netSubtotal = 0;
                    totalTax = 0;

                    // Calculate totals from cart
                    cart.forEach(item => {
                        const itemTotal = item.price * item.quantity;
                        const itemDisc = item.discount_amount || 0;

                        // Update item total property
                        item.total = itemTotal - itemDisc;

                        grossSubtotal += itemTotal;
                        totalItemDiscount += itemDisc;

                        // Calculate tax for this item if applicable
                        // Assuming tax is applied on the discounted price
                        // You might need to adjust this based on your tax logic (inclusive vs exclusive)
                        // For now, using the global taxRate
                        const taxableAmount = itemTotal - itemDisc;
                        const itemTax = taxableAmount * (taxRate / 100);
                        totalTax += itemTax;
                    });

                    netSubtotal = grossSubtotal - totalItemDiscount;

                    // Render dynamic tables only if requested
                    if (shouldRender) {
                        renderUnifiedTable();
                    }

                    // Calculate global discount
                    let globalDiscount = 0;
                    if (discountPercent > 0) {
                        globalDiscount = netSubtotal * (discountPercent / 100);
                    } else if (discountAmount > 0) {
                        globalDiscount = Math.min(discountAmount, netSubtotal);
                    }

                    // Calculate tax on the discounted amount
                    let tax = 0;
                    if (taxEnabledPos && netSubtotal > 0) {
                        const taxableAmount = netSubtotal - globalDiscount;
                        tax = taxableAmount * (taxRate / 100);
                        totalTax = tax;
                    }

                    const grandTotal = (netSubtotal - globalDiscount) + tax;
                    const totalDiscount = totalItemDiscount + globalDiscount;
                    let payable = grandTotal + (tipAmount || 0);

                    // Deduct previous paid amount
                    if (previousPaidAmount > 0) {
                        payable = Math.max(0, payable - previousPaidAmount);
                    }

                    // Check if cart contains ONLY package redemptions (price 0 and package_id set)
                    const isPackageRedemptionOnly = cart.length > 0 && cart.every(item => item.package_id && parseFloat(item.price) === 0);

                    if (isPackageRedemptionOnly) {
                        // Auto-select package payment method
                        $('#cartPaymentPackage').prop('checked', true);

                        // Show package payment, hide others
                        $('#cartPaymentPackage').closest('.col-6, .col-12').show();
                        $('input[name="payment_method"]').not('#cartPaymentPackage').closest('.col-6, .col-12').hide();

                        // Make package button full width for better UI
                        $('#cartPaymentPackage').closest('.col-6').removeClass('col-6').addClass('col-12');
                    } else {
                        // Hide package payment method (it's only for redemptions)
                        $('#cartPaymentPackage').closest('.col-6, .col-12').hide();

                        // Show all other payment methods
                        $('input[name="payment_method"]').not('#cartPaymentPackage').closest('.col-6, .col-12').show();

                        // Restore package button width (for next time)
                        $('#cartPaymentPackage').closest('.col-12').removeClass('col-12').addClass('col-6');

                        // If package was selected but we are no longer in package-only mode, switch to cash
                        if ($('#cartPaymentPackage').is(':checked')) {
                            $('#cartPaymentCash').prop('checked', true);
                        }
                    }

                    // Update UI
                    $('#cartSubtotal').text(formatCurrency(grossSubtotal));
                    $('#cartTax').text(formatCurrency(tax));

                    // Always show discount row so user can enter a discount
                    $('#cartDiscountRow').show();
                    if (totalDiscount > 0) {
                        $('#cartDiscount').text('-' + formatCurrency(totalDiscount)).removeClass('text-muted').addClass('text-danger').show();
                    } else {
                        $('#cartDiscount').text(formatCurrency(0)).removeClass('text-danger').addClass('text-muted').show();
                    }

                    // Show/hide clear discount button (cross mark)
                    if (discountAmount > 0 || discountPercent > 0) {
                        $('#clearDiscount').removeClass('d-none');
                    } else {
                        $('#clearDiscount').addClass('d-none');
                    }

                    $('#grandTotal').text(formatCurrency(Math.max(0, grandTotal)));

                    // Handle Display of Previous Paid Row
                    if (previousPaidAmount > 0) {
                        $('#cartPaidRow').find('span').text(formatCurrency(previousPaidAmount));
                        $('#cartPaidRow').show();
                    } else {
                        $('#cartPaidRow').hide();
                    }

                    $('#payableAmount').text(formatCurrency(Math.max(0, payable)));

                    // Sync mobile cart badge and floating sticky checkout bar
                    const totalItemsCount = cart.reduce((sum, item) => sum + (parseInt(item.quantity) || 1), 0);
                    if (totalItemsCount > 0) {
                        $('#mobileCartBadge').text(totalItemsCount).removeClass('d-none');
                        $('#mobileStickyItemCount').text(totalItemsCount + (totalItemsCount === 1 ? ' Item' : ' Items'));
                        $('#mobileStickyTotal').text(formatCurrency(Math.max(0, payable)));
                        if ($('#mobileTabCatalog').hasClass('active') && window.innerWidth < 992) {
                            $('#posMobileStickyBar').fadeIn(150);
                        }
                    } else {
                        $('#mobileCartBadge').addClass('d-none');
                        $('#posMobileStickyBar').fadeOut(150);
                    }

                    // Sync amounts based on selected method
                    const method = $('input[name="payment_method"]:checked').val();
                    if (method === 'cash') {
                        // Smart auto-fill: Only update if field is empty (not touched by user)
                        // Allow user to set any value including 0 for partial payments
                        const currentValue = $('#amountReceived').val();
                        const oldPayable = parseFloat($('#amountReceived').data('last-payable')) || 0;

                        // Only auto-fill if field is completely empty OR value matches last auto-fill
                        if (currentValue === '' || parseFloat(currentValue) === oldPayable) {
                            $('#amountReceived').val(payable.toFixed(2));
                            $('#amountReceived').data('last-payable', payable);
                        }

                        // Update cashAmount based on current amountReceived value
                        const received = parseFloat($('#amountReceived').val()) || 0;
                        const actualCash = Math.min(received, payable);
                        $('#cashAmount').val(actualCash);
                        $('#cardAmount').val(0);
                        $('#onlineAmount').val(0);
                        $('#otherAmount').val(0);
                    } else if (method === 'card') {
                        $('#cashAmount').val(0);
                        $('#cardAmount').val(payable);
                        $('#onlineAmount').val(0);
                        $('#otherAmount').val(0);
                    } else if (method === 'online') {
                        $('#cashAmount').val(0);
                        $('#cardAmount').val(0);
                        $('#onlineAmount').val(payable);
                        $('#otherAmount').val(0);
                    } else if (method === 'package') {
                        $('#cashAmount').val(0);
                        $('#cardAmount').val(0);
                        $('#onlineAmount').val(0);
                        $('#otherAmount').val(0);
                        // Package payment covers the payable amount (usually 0 if all items are package services)
                    } else if (method === 'other') {
                        $('#cashAmount').val(0);
                        $('#cardAmount').val(0);
                        $('#onlineAmount').val(0);
                        $('#otherAmount').val(payable);
                    } else if (method === 'mixed') {
                        // For split payment, don't reset amounts, but update remaining
                        const cash = parseFloat($('#splitCash').val()) || 0;
                        const card = parseFloat($('#splitCard').val()) || 0;
                        const online = parseFloat($('#splitOnline').val()) || 0;
                        const other = parseFloat($('#splitOther').val()) || 0;

                        // Ensure hidden inputs match split inputs
                        $('#cashAmount').val(cash);
                        $('#cardAmount').val(card);
                        $('#onlineAmount').val(online);
                        $('#otherAmount').val(other);

                        const totalEntered = cash + card + online + other;
                        const remaining = Math.max(0, payable - totalEntered);
                        $('#splitRemaining').text(formatCurrency(remaining));
                    if (remaining <= 0.009) {
                        $('#splitRemaining').removeClass('text-danger').addClass('text-success');
                    } else {
                        $('#splitRemaining').removeClass('text-success').addClass('text-danger');
                    }
                    } else {
                        $('#cashAmount').val(0);
                        $('#cardAmount').val(0);
                        $('#onlineAmount').val(0);
                        $('#otherAmount').val(0);
                    }

                    // Calculate outstanding
                    const cash = parseFloat($('#cashAmount').val()) || 0;
                    const card = parseFloat($('#cardAmount').val()) || 0;
                    const online = parseFloat($('#onlineAmount').val()) || 0;
                    const other = parseFloat($('#otherAmount').val()) || 0;
                    const paid = cash + card + online + other;
                    const outstanding = payable - paid;

                    const $outstanding = $('#outstandingAmount');
                    $outstanding.text(formatCurrency(outstanding > 0 ? outstanding : 0));
                    if (outstanding <= 0.01) {
                        $outstanding.removeClass('text-danger').addClass('text-success');
                    } else {
                        $outstanding.removeClass('text-success').addClass('text-danger');
                    }

                    // Enable/disable checkout button
                    let isCheckoutDisabled = cart.length === 0;

                    // Check if this sale is effectively paid (either via status or because balance is zero with previous payments)
                    const isEffectivelyPaid = isPaidSale || (payable <= 0.01 && previousPaidAmount > 0 && cart.length > 0);

                    if (isEffectivelyPaid) {
                        isCheckoutDisabled = true;
                        $('#checkoutBtn').html('<i class="fas fa-check-circle me-2"></i><span id="chargeBtnText">Already Paid</span>').addClass('btn-secondary').removeClass('btn-success');
                    } else {
                        $('#checkoutBtn').html('<i class="fas fa-bolt me-2"></i><span id="chargeBtnText">Charge ' + formatCurrency(Math.max(0, payable)) + '</span>').addClass('btn-success').removeClass('btn-secondary');

                        if (method === 'cash') {
                            const received = parseFloat($('#amountReceived').val()) || 0;
                            if (received <= 0) {
                                isCheckoutDisabled = true;
                            }
                        }
                    }

                    $('#checkoutBtn').prop('disabled', isCheckoutDisabled);
                }



                // Handle quantity changes
                $(document).on('input', '.quantity-input', function () {
                    const index = $(this).data('index');
                    const newQty = parseFloat($(this).val());
                    const $row = $(this).closest('tr');

                    if (index >= 0 && index < cart.length && newQty >= 1) {
                        const item = cart[index];
                        item.quantity = newQty;
                        item.total = (item.quantity * item.price) - (item.discount_amount || 0);

                        // Update row total display
                        $row.find('td:eq(5)').text(formatCurrency(item.total));

                        // If package, update sub-services
                        if (item.type === 'package') {
                            cart.forEach(subItem => {
                                if (subItem.type === 'service' && subItem.package_id == item.id) {
                                    subItem.quantity = newQty;
                                    // subItem.total = 0; // Usually 0 for package services
                                }
                            });
                        }

                        updateCart(false); // Don't re-render table
                    }
                });

                // Handle remove item
                $(document).on('click', '.btn-remove', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const index = $(this).data('index');
                    if (index >= 0 && index < cart.length) {
                        const itemName = cart[index].name;
                        if (confirm(`Remove ${itemName} from cart?`)) {
                            cart.splice(index, 1);
                            updateCart(true); // Re-render table
                            showAlert(`${itemName} removed from cart`, 'info');
                        }
                    }
                });

                // Handle staff selection change
                $(document).on('change', '.staff-select', function () {
                    const index = $(this).data('index');
                    if (index >= 0 && index < cart.length) {
                        cart[index].staff_id = $(this).val() || null;
                        updateCart(true);
                    }
                });

                // Handle package quantity changes
                $(document).on('click', '.package-plus', function (e) {
                    e.preventDefault();
                    const packageId = $(this).data('package-id');
                    const packageItem = cart.find(i => i.type === 'package' && i.id == packageId);
                    if (packageItem) {
                        packageItem.quantity += 1;
                        packageItem.total = (packageItem.price * packageItem.quantity) - (packageItem.discount_amount || 0);
                        // Update related services quantities
                        const services = cart.filter(i => i.type === 'package_service' && i.package_id == packageId);
                        services.forEach(service => {
                            service.quantity = packageItem.quantity;
                            service.total = 0;
                        });
                        updateCart(true); // Re-render needed for package services potentially
                    }
                });

                $(document).on('click', '.package-minus', function (e) {
                    e.preventDefault();
                    const packageId = $(this).data('package-id');
                    const packageItem = cart.find(i => i.type === 'package' && i.id == packageId);
                    if (packageItem && packageItem.quantity > 1) {
                        packageItem.quantity -= 1;
                        packageItem.total = (packageItem.price * packageItem.quantity) - (packageItem.discount_amount || 0);
                        const services = cart.filter(i => i.type === 'package_service' && i.package_id == packageId);
                        services.forEach(service => {
                            service.quantity = packageItem.quantity;
                            service.total = 0;
                        });
                        updateCart(true); // Re-render needed
                    }
                });

                // Generic removal logic is handled by .btn-remove above

                // Helper: apply item discount based on type (fixed $ or percent %)
                function applyItemDiscount(index, discValue, discType) {
                    if (index < 0 || index >= cart.length) return;
                    const item = cart[index];
                    const baseTotal = item.quantity * item.price;
                    if (discType === 'percent') {
                        const pct = Math.min(parseFloat(discValue) || 0, 100);
                        item.discount_percent  = pct;
                        item.discount_amount   = parseFloat((baseTotal * pct / 100).toFixed(2));
                    } else {
                        item.discount_percent  = 0;
                        item.discount_amount   = Math.min(parseFloat(discValue) || 0, baseTotal);
                    }
                    item.total = baseTotal - item.discount_amount;
                    updateCart(false);
                }

                // Handle per-item discount value change
                $(document).on('input', '.disc-input', function () {
                    const index    = parseInt($(this).data('index'));
                    const $wrapper = $(this).closest('.pos-item-disc-control');
                    const discType = $wrapper.find('.disc-type-select').val() || $(this).data('disc-type') || 'fixed';
                    applyItemDiscount(index, $(this).val(), discType);
                });

                // Handle per-item discount type change (% ↔ ₹)
                $(document).on('change', '.disc-type-select', function () {
                    const index    = parseInt($(this).data('index'));
                    const $wrapper = $(this).closest('.pos-item-disc-control');
                    const discType = $(this).val();
                    const discVal  = $wrapper.find('.disc-input').val();
                    applyItemDiscount(index, discVal, discType);
                });

                // Handle clearing item discount
                $(document).on('click', '.btn-clear-item-discount', function () {
                    const index = parseInt($(this).data('index'));
                    if (index >= 0 && index < cart.length) {
                        cart[index].discount_amount  = 0;
                        cart[index].discount_percent = 0;
                        updateCart(true); // Re-render needed to refresh input values
                    }
                });

                // Handle checkbox changes (basic select all functionality)
                $(document).on('change', '#selectAllCheckbox', function () {
                    const isChecked = $(this).is(':checked');
                    $('.row-checkbox').prop('checked', isChecked);
                });

                $(document).on('change', '.row-checkbox', function () {
                    const totalCheckboxes = $('.row-checkbox').length;
                    const checkedCheckboxes = $('.row-checkbox:checked').length;
                    $('#selectAllCheckbox').prop('checked', totalCheckboxes > 0 && checkedCheckboxes === totalCheckboxes);
                });
                // Handle discount amount input
                $('#discountAmount').on('input', function () {
                    discountAmount = parseFloat($(this).val()) || 0;
                    discountPercent = 0; // Reset percent when amount is entered
                    $('#discountPercent').val('');
                    updateCart();
                });

                // Handle discount percent input
                $('#discountPercent').on('input', function () {
                    discountPercent = parseFloat($(this).val()) || 0;
                    discountAmount = 0; // Reset amount when percent is entered
                    $('#discountAmount').val('');
                    updateCart();
                });

                // Handle tip amount input
                $('#tipAmount').on('input', function () {
                    tipAmount = parseFloat($(this).val()) || 0;
                    $('.pos-tip-pill').removeClass('active');
                    if (tipAmount > 0) {
                        $('.pos-tip-pill[data-pct="custom"]').addClass('active');
                    } else {
                        $('.pos-tip-pill[data-pct="0"]').addClass('active');
                    }
                    updateCart(false);
                });

                // Handle cash amount input
                $('#cashAmount').on('input', function () {
                    cashAmount = parseFloat($(this).val()) || 0;
                    updateCart();
                });

                // Handle card amount input
                $('#cardAmount').on('input', function () {
                    cardAmount = parseFloat($(this).val()) || 0;
                    updateCart();
                });

                // Function to reset POS terminal
                function resetPosTerminal() {
                    cart = [];
                    discountAmount = 0;
                    discountPercent = 0;
                    tipAmount = 0;
                    cashAmount = 0;
                    cardAmount = 0;
                    onlineAmount = 0;
                    otherAmount = 0;
                    previousPaidAmount = 0;
                    isPaidSale = false; // Reset the paid flag

                    // Reset discount type to default
                    discountType = 'fixed';

                    $('#discountAmount, #discountValue').val('');
                    $('#discountPercent').val('');
                    $('#discountType').val('fixed');
                    $('#tipAmount').val('0');
                    $('#amountReceived').val('');
                    $('#cashAmount, #cardAmount, #onlineAmount, #otherAmount').val('0');
                    $('#customerId, #existingSaleId').val('');
                    $('#customerSelect').val('').trigger('change');
                    $('#customerDisplay').addClass('d-none');
                    $('#customerSearchSection').removeClass('d-none');

                    // Added for comprehensive reset
                    $('#saleNotes').val('');
                    $('#booking_id').val('');
                    $('#changeAmount').val('');
                    $('.split-input').val('');
                    $('#splitRemaining').text('0.00');
                    $('#splitPaymentSection').addClass('d-none');
                    $('#paymentDetailsSection').hide();

                    // Clear service cards state
                    $('.service-card').removeClass('selected');
                    $('.selected-checkmark').addClass('d-none');
                    $('.service-checkbox').prop('checked', false);

                    // Reset payment method to default
                    $('input[name="payment_method"][value="cash"]').prop('checked', true).trigger('change');

                    // Reset customer balances/memberships
                    $('#membershipInfo').addClass('d-none');
                    if (typeof customerPackageBalances !== 'undefined') {
                        customerPackageBalances = [];
                    }

                    // Clear URL parameters (booking_id, sale_id, customer) so stale data
                    // is not re-fetched if the page is refreshed or the URL is re-read
                    try {
                        const cleanUrl = new URL(window.location.href);
                        cleanUrl.searchParams.delete('booking_id');
                        cleanUrl.searchParams.delete('sale_id');
                        cleanUrl.searchParams.delete('customer');
                        window.history.replaceState({}, document.title, cleanUrl.toString());
                    } catch (e) {
                        // Silently ignore URL manipulation errors
                    }

                    // Re-enable controls
                    togglePosControls(false);
                    updateCart();
                }

                let resetActionType = 'reset'; // can be 'reset' or 'new_sale'

                // Handle reset button
                $('#resetBtn').on('click', function () {
                    if (cart.length > 0 || isPaidSale) {
                        resetActionType = 'reset';
                        $('#resetModalMessage').text('Are you sure you want to reset the terminal for a new sale?');
                        const modal = new bootstrap.Modal(document.getElementById('posResetConfirmationModal'));
                        modal.show();
                    } else {
                        resetPosTerminal();
                    }
                });

                // Handle "New Sale" button at the top
                $('button[data-bs-target="#newSaleModal"]').removeAttr('data-bs-toggle data-bs-target').on('click', function (e) {
                    e.preventDefault();
                    if (cart.length > 0) {
                        resetActionType = 'new_sale';
                        $('#resetModalMessage').text('A sale is in progress. Start a new sale anyway?');
                        const modal = new bootstrap.Modal(document.getElementById('posResetConfirmationModal'));
                        modal.show();
                    } else {
                        resetPosTerminal();
                        showAlert('New sale initialized', 'success');
                    }
                });

                // Handle confirm reset click inside modal
                $('#confirmResetBtn').on('click', function () {
                    const modalEl = document.getElementById('posResetConfirmationModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();

                    resetPosTerminal();
                    if (resetActionType === 'reset') {
                        showAlert('POS terminal reset successfully', 'info');
                    } else {
                        showAlert('New sale initialized', 'success');
                    }
                });

                // Checkout button click
                $('#checkoutBtn').click(function (e) {
                    e.preventDefault();

                    // Basic validation
                    if (cart.length === 0) {
                        showAlert('Please add items to the cart before checkout', 'warning');
                        return;
                    }

                    // Populate confirmation modal
                    const payable = parseFloat($('#payableAmount').text().replace(/[^0-9.-]+/g, "")) || 0;
                    const discountText = $('#cartDiscount').text().replace(/[^0-9.-]+/g, "") || "0";
                    const discount = Math.abs(parseFloat(discountText)) || 0;

                    // Calculate total received
                    const cash = parseFloat($('#cashAmount').val()) || 0;
                    const card = parseFloat($('#cardAmount').val()) || 0;
                    const online = parseFloat($('#onlineAmount').val()) || 0;
                    const other = parseFloat($('#otherAmount').val()) || 0;
                    const totalReceived = cash + card + online + other;
                    const outstanding = Math.max(0, payable - totalReceived);

                    $('#confirmTotal').text(formatCurrency(payable));
                    $('#confirmDiscount').text(formatCurrency(discount));
                    $('#confirmReceived').text(formatCurrency(totalReceived));
                    $('#confirmOutstanding').text(formatCurrency(outstanding));

                    // Show/hide outstanding row based on amount
                    if (outstanding > 0) {
                        $('#confirmOutstandingRow').show();
                    } else {
                        $('#confirmOutstandingRow').hide();
                    }
                });

                // Confirm Sale button in modal
                $('#confirmSaleBtn').click(function () {
                    const modalElement = document.getElementById('saleConfirmationModal');
                    const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                    modal.hide();
                    processSale();
                });

                // Payment modal input listeners
                $('#amountReceived').on('input', function () {
                    const received = parseFloat($(this).val()) || 0;
                    const payable = parseFloat($('#payableAmount').text().replace(/[^0-9.-]+/g, "")) || 0;
                    const change = Math.max(0, received - payable);
                    $('#changeAmount').val(change.toFixed(2));

                    // Update hidden amounts based on selected method
                    const method = $('input[name="payment_method"]:checked').val();
                    if (method === 'cash') {
                        const amount = Math.min(received, payable);
                        $('#cashAmount').val(amount);
                        cashAmount = amount; // Update global variable
                        updateCart(); // Ensure UI reflects the change
                    }
                });

                $('input[name="payment_method"]').on('change', function () {
                    const method = $(this).val();
                    const payable = parseFloat($('#payableAmount').text().replace(/[^0-9.-]+/g, "")) || 0;
                    const received = parseFloat($('#amountReceived').val()) || 0;

                    // Sync active payment tile
                    $('.payment-device-tile').removeClass('active');
                    $(`.payment-device-tile[data-method="${method}"]`).addClass('active');

                    // Update checkout button style based on payment method
                    const $checkoutBtn = $('#checkoutBtn');
                    const $paymentStatusBadge = $('#paymentStatusBadge');

                    if (method === 'none') {
                        $checkoutBtn.removeClass('btn-primary').addClass('btn-warning');
                        $paymentStatusBadge.show();
                    } else {
                        $checkoutBtn.removeClass('btn-warning').addClass('btn-primary');
                        $paymentStatusBadge.hide();
                    }

                    // Reset all payment amounts
                    $('#cashAmount').val('0');
                    cashAmount = 0;
                    $('#cardAmount').val('0');
                    cardAmount = 0;
                    $('#onlineAmount').val('0');
                    onlineAmount = 0;
                    $('#otherAmount').val('0');
                    otherAmount = 0;

                    // Hide both special sections by default
                    $('#paymentDetailsSection').hide();
                    $('#splitPaymentSection').addClass('d-none');

                    // Handle payment method specific UI
                    if (method === 'cash') {
                        $('#paymentDetailsSection').slideDown();
                        // Auto-fill amount received with total payable
                        $('#amountReceived').val(payable.toFixed(2));
                        $('#amountReceived').data('last-payable', payable); // Track auto-filled value
                        const amount = Math.min(payable, payable);
                        $('#cashAmount').val(amount);
                        cashAmount = amount;
                        // Trigger input event to update change amount
                        $('#amountReceived').trigger('input');
                    } else if (method === 'card') {
                        $('#cardAmount').val(payable);
                        cardAmount = payable;
                    } else if (method === 'online') {
                        $('#onlineAmount').val(payable);
                        onlineAmount = payable;
                    } else if (method === 'other') {
                        $('#otherAmount').val(payable);
                        otherAmount = payable;
                    } else if (method === 'mixed') {
                        $('#splitPaymentSection').removeClass('d-none');
                        // Initialize split inputs with 0
                        $('.split-input').val('');
                        $('#splitRemaining').text(formatCurrency(payable));
                    } else if (method === 'none') {
                        // No payment - all amounts remain 0
                    }

                    // Update cart to recalculate balance due
                    updateCart();
                });

                // Handle split payment inputs
                $(document).on('input', '.split-input', function () {
                    const payable = parseFloat($('#payableAmount').text().replace(/[^0-9.-]+/g, "")) || 0;

                    const cash = parseFloat($('#splitCash').val()) || 0;
                    const card = parseFloat($('#splitCard').val()) || 0;
                    const online = parseFloat($('#splitOnline').val()) || 0;
                    const other = parseFloat($('#splitOther').val()) || 0;

                    const totalEntered = cash + card + online + other;
                    const remaining = Math.max(0, payable - totalEntered);

                    $('#splitRemaining').text(formatCurrency(remaining));
                    if (remaining <= 0.009) {
                        $('#splitRemaining').removeClass('text-danger').addClass('text-success');
                    } else {
                        $('#splitRemaining').removeClass('text-success').addClass('text-danger');
                    }

                    // Update hidden inputs
                    $('#cashAmount').val(cash);
                    $('#cardAmount').val(card);
                    $('#onlineAmount').val(online);
                    $('#otherAmount').val(other);

                    // Update global variables
                    cashAmount = cash;
                    cardAmount = card;
                    onlineAmount = online;
                    otherAmount = other;

                    // Update outstanding amount display in summary
                    const outstanding = Math.max(0, payable - totalEntered);
                    $('#outstandingAmount').text(formatCurrency(outstanding));
                });

                // Process sale
                function processSale(confirmUnpaid = false) {
                    const customerId = $('#customerId').val();
                    const tip = parseFloat($('#tipAmount').val()) || 0;
                    const payable = parseFloat($('#payableAmount').text().replace(/[^0-9.-]+/g, "")) || 0;

                    // Get payment method and amounts from cart
                    const selectedPaymentMethod = $('input[name="payment_method"]:checked').val();
                    const cash = parseFloat($('#cashAmount').val()) || 0;
                    const card = parseFloat($('#cardAmount').val()) || 0;
                    const online = parseFloat($('#onlineAmount').val()) || 0;
                    const other = parseFloat($('#otherAmount').val()) || 0;

                    // Check if payment method is selected
                    if (!selectedPaymentMethod) {
                        showAlert('Please select a payment method before processing the sale', 'warning');
                        return;
                    }

                    // If payment method is 'none' (Pay Later), show confirmation modal
                    if (selectedPaymentMethod === 'none' && !confirmUnpaid) {
                        const unpaidModal = new bootstrap.Modal(document.getElementById('unpaidConfirmationModal'));
                        unpaidModal.show();
                        return;
                    }

                    // Validate payment amounts based on selected method (skip validation for 'none')
                    if (selectedPaymentMethod === 'cash' && cash <= 0) {
                        showAlert('Please enter the cash amount received', 'warning');
                        $('#amountReceived').focus();
                        return;
                    } else if (selectedPaymentMethod === 'card' && card <= 0) {
                        showAlert('Please enter the card payment amount', 'warning');
                        return;
                    } else if (selectedPaymentMethod === 'online' && online <= 0) {
                        showAlert('Please enter the online payment amount', 'warning');
                        return;
                    } else if (selectedPaymentMethod === 'other' && other <= 0 && payable > 0) {
                        showAlert('Please enter the payment amount', 'warning');
                        return;
                    } else if (selectedPaymentMethod === 'package') {
                        // For package payment, we allow 0 amount if payable is 0
                        if (payable > 0) {
                            showAlert('Package payment can only be used for pre-paid services. Please use another method for the remaining balance.', 'warning');
                            return;
                        }
                    } else if (selectedPaymentMethod === 'mixed') {
                        const totalPaid = cash + card + online + other;
                        // payable is already defined above

                        if (totalPaid <= 0) {
                            showAlert('Please enter at least one payment amount', 'warning');
                            return;
                        }

                        // Optional: Enforce full payment for split? 
                        // Usually POS allows partial, but let's warn if less than total
                        if (totalPaid < payable && !confirm('Total payment is less than payable amount. Continue as partial payment?')) {
                            return;
                        }
                    }

                    // Customer is optional - walk-in customers are allowed
                    // If no customer is selected, the backend will handle it as a walk-in sale

                    if (cart.length === 0) {
                        showAlert('Please add items to the cart before processing payment', 'warning');
                        return;
                    }

                    // Show loading state
                    const $checkoutBtn = $('#checkoutBtn');
                    const originalBtnText = $checkoutBtn.html();
                    $checkoutBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

                    // Prepare items for backend - map package_service to service, exclude display-only package headers? Wait, include package for pricing
                    // Actually, send package as type 'package', and package_service as type 'service' with service_id and package_id
                    const backendItems = cart.map(item => {
                        if (item.type === 'package_service') {
                            return {
                                type: 'service',
                                id: item.service_id,
                                name: item.name,
                                quantity: item.quantity,
                                price: item.price, // 0
                                staff_id: item.staff_id || null,
                                discount_amount: item.discount_amount || 0,
                                notes: item.notes || '',
                                package_id: item.package_id,
                                booking_ids: item.booking_ids || []
                            };
                        } else {
                            return {
                                type: item.type,
                                id: item.id,
                                name: item.name,
                                quantity: item.quantity,
                                price: item.price,
                                staff_id: item.staff_id || null,
                                discount_amount: item.discount_amount || 0,
                                notes: item.notes || '',
                                package_id: item.package_id || (item.type === 'package' ? item.id : null),
                                booking_ids: item.booking_ids || []
                            };
                        }
                    }).filter(item => item.type !== 'package' || true); // Include packages for pricing

                    // Collect all booking IDs from cart items
                    const bookingIds = [];
                    cart.forEach(item => {
                        if (item.booking_ids && Array.isArray(item.booking_ids)) {
                            bookingIds.push(...item.booking_ids);
                        }
                    });
                    // Remove duplicates
                    const uniqueBookingIds = [...new Set(bookingIds)];

                    const saleData = {
                        sale_id: $('#existingSaleId').val() || null,
                        salon_id: {{ $salonId ?? 1 }},
                        customer_id: customerId || null,
                        booking_ids: uniqueBookingIds,
                        items: backendItems,
                        tip: tip,
                        cash_amount: cash,
                        card_amount: card,
                        online_amount: online,
                        other_amount: other,
                        discount_amount: discountPercent > 0 ? discountPercent : discountAmount,
                        discount_type: discountPercent > 0 ? 'percent' : 'fixed',
                        payment_method: selectedPaymentMethod,
                        notes: $('#saleNotes').val() || '',
                        tendered_amount: ['cash'].includes(selectedPaymentMethod) ? $('#amountReceived').val() : 0,
                        change_amount: ['cash'].includes(selectedPaymentMethod) ? $('#changeAmount').val().replace(/[^0-9.-]+/g, "") : 0
                    };



                    // Submit sale to server
                    $.ajax({
                        url: '{{ route("admin.pos.store") }}',
                        method: 'POST',
                        data: JSON.stringify(saleData),
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function (response) {


                            // Show receipt
                            if (response.sale) {
                                showReceipt(response.sale, response.receipt_url);
                            }

                            // Reset terminal for next sale
                            resetPosTerminal();

                            // Close modal if open
                            const modalElement = document.getElementById('saleConfirmationModal');
                            const modal = bootstrap.Modal.getInstance(modalElement);
                            if (modal) {
                                modal.hide();
                            }

                            const unpaidModalElement = document.getElementById('unpaidConfirmationModal');
                            const unpaidModal = bootstrap.Modal.getInstance(unpaidModalElement);
                            if (unpaidModal) {
                                unpaidModal.hide();
                            }

                            showAlert('Sale processed successfully!', 'success');
                        },
                        error: function (xhr, status, error) {

                            let errorMessage = 'Error processing sale. Please try again.';

                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                } else if (xhr.responseJSON.errors) {
                                    // Handle validation errors
                                    const errors = [];
                                    for (const [key, value] of Object.entries(xhr.responseJSON.errors)) {
                                        errors.push(`${key}: ${value[0]}`);
                                    }
                                    errorMessage = errors.join('<br>');
                                }
                            } else if (xhr.status === 419) {
                                errorMessage = 'Your session has expired. Please refresh the page and try again.';
                            } else if (xhr.status === 500) {
                                errorMessage = 'A server error occurred. Please try again later.';
                            }

                            showAlert(errorMessage, 'danger');
                        },
                        complete: function () {
                            // Re-enable button
                            $checkoutBtn.prop('disabled', false).html(originalBtnText);
                        }
                    });
                }

                // Show receipt
                function showReceipt(sale, receiptUrl = null) {
                    // Use values from the sale object
                    const subtotal = parseFloat(sale.subtotal) || 0;
                    const tax = parseFloat(sale.tax) || 0;
                    const totalDiscount = parseFloat(sale.discount) || 0;
                    const tip = parseFloat(sale.tip) || 0;
                    const payable = parseFloat(sale.total) || 0;
                    // Determine status properties
                    const isUnpaid = sale.payment_status === 'pending';
                    const isPartial = sale.payment_status === 'partial';
                    const outstanding = parseFloat(sale.outstanding_amount) || 0;

                    let paidAmount = 0;
                    if (isUnpaid) {
                        paidAmount = 0;
                    } else if (isPartial) {
                        paidAmount = payable - outstanding;
                    } else {
                        paidAmount = payable;
                    }

                    // Update receipt modal based on payment status
                    const statusBanner = $('#receiptStatusBanner');
                    const statusIcon = $('#receiptStatusIcon');
                    const statusTitle = $('#receiptStatusTitle');
                    const statusMessage = $('#receiptStatusMessage');
                    const modalHeader = $('#receiptModalHeader');

                    if (sale.payment_method === 'package') {
                        // Update for package/prepaid receipt
                        statusBanner.css('background-color', '#f0fdf4');
                        statusIcon.removeClass('fa-clock text-warning').addClass('fa-check-circle text-success');
                        statusTitle.text('Prepaid').css('color', '#065f46');
                        statusMessage.text('Redeemed from Package').css('color', '#047857');
                        modalHeader.css('background', 'linear-gradient(135deg, #10b981, #34d399)');
                        $('.status-icon').removeClass('unpaid');
                    } else if (isUnpaid) {
                        // Update for unpaid receipt
                        statusBanner.css('background-color', '#fef2f2');
                        statusIcon.removeClass('fa-check-circle text-success').addClass('fa-clock text-warning');
                        statusTitle.text('Payment Pending').css('color', '#b45309');
                        statusMessage.text('This is an unpaid invoice').css('color', '#92400e');
                        modalHeader.css('background', 'linear-gradient(135deg, #f59e0b, #fbbf24)');
                        $('.status-icon').addClass('unpaid');
                    } else if (isPartial) {
                        // Update for partial receipt
                        statusBanner.css('background-color', '#fff7ed');
                        statusIcon.removeClass('fa-check-circle text-success').addClass('fa-adjust text-warning');
                        statusTitle.text('Partial Payment').css('color', '#9a3412');
                        statusMessage.text('Outstanding balance remains').css('color', '#9a3412');
                        modalHeader.css('background', 'linear-gradient(135deg, #f97316, #fb923c)');
                        $('.status-icon').addClass('unpaid');
                    } else {
                        // Default paid receipt
                        statusBanner.css('background-color', '#f0fdf4');
                        statusIcon.removeClass('fa-clock text-warning').addClass('fa-check-circle text-success');
                        statusTitle.text('Payment Successful').css('color', '#065f46');
                        statusMessage.text('Thank you for your payment').css('color', '#047857');
                        modalHeader.css('background', 'linear-gradient(135deg, #10b981, #34d399)');
                        $('.status-icon').removeClass('unpaid');
                    }

                    // Use the configured tax rate for display (fallback if not in sale)
                    const displayTaxRate = sale.tax_rate || taxRate;

                    // Format the date
                    const dateObj = new Date(sale.created_at || new Date());
                    const formattedDate = dateObj.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    // Get customer name
                    const customerName = sale.customer ? sale.customer.name : 'Walk-in Customer';
                    const cashierName = sale.employee ? sale.employee.name : "{{ auth()->user()->name }}";



                    // Generate receipt HTML
                    let receiptHtml = `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="receipt-wrapper" id="printableReceipt" style="font-family: 'Inter', sans-serif; color: #000000; line-height: 1.2; background: #ffffff; padding: 0; width: 100%; max-width: 100%;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div style="text-align: center; margin-bottom: 15px;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ${salonData.logo ? `<img src="${salonData.logo}" alt="${salonData.name}" style="max-height: 60px; width: auto; margin-bottom: 8px; object-fit: contain;">` : ''}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <h2 style="font-size: 1.1rem; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">${salonData.name || 'SALON'}</h2>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ${salonData.address ? `<p style="font-size: 0.8rem; margin: 4px 0 0 0; color: #444;">${salonData.address}</p>` : ''}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ${salonData.phone ? `<p style="font-size: 0.8rem; margin: 2px 0 0 0; color: #444;">Tel: ${salonData.phone}</p>` : ''}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div style="border-bottom: 1px dashed #000; margin-bottom: 15px;"></div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div style="font-size: 0.85rem; margin-bottom: 15px;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <span style="font-weight: 600;">Inv No:</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <span>#${sale.invoice_number || 'N/A'}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <span style="font-weight: 600;">Date:</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     <span>${formattedDate}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <span style="font-weight: 600;">Customer:</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     <span>${customerName}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span style="font-weight: 600;">Cashier:</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span>${cashierName}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span style="font-weight: 600;">Payment:</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span style="text-transform: uppercase;">${sale.payment_method || 'N/A'}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div style="border-bottom: 1px dashed #000; margin-bottom: 15px;"></div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 0.85rem;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <thead>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <th style="text-align: left; padding-bottom: 8px; font-weight: 800; border-bottom: 1px solid #eee;">ITEM</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <th style="text-align: center; padding-bottom: 8px; width: 40px; font-weight: 800; border-bottom: 1px solid #eee;">QTY</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <th style="text-align: right; padding-bottom: 8px; width: 70px; font-weight: 800; border-bottom: 1px solid #eee;">AMT</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </thead>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <tbody>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                `;

                    // Use sale items
                    let items = sale.items || [];
                    // Ensure items is an array
                    if (!Array.isArray(items) && typeof items === 'object') {
                        items = Object.values(items);
                    }

                    items.forEach(item => {
                        // Check if it's a package service (indent it)
                        // In the saved item, we might check package_id and price
                        const isPackageService = item.package_id && parseFloat(item.unit_price) === 0;
                        const indentStyle = isPackageService ? 'padding-left: 15px; font-size: 0.85rem; color: #555;' : 'font-weight: 600; font-size: 0.9rem; margin-bottom: 2px;';

                        // Use item_name if available, fallback to name
                        const itemName = item.item_name || item.name || 'Item';

                        receiptHtml += `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  <td style="padding: 8px 0; vertical-align: top;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      <div style="${isPackageService ? 'padding-left: 15px;' : ''}">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           <div style="${indentStyle}">${itemName}</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           ${item.staff ? `<div style="font-size: 0.75rem; color: #666;">Staff: ${item.staff.name}</div>` : ''}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  </td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td style="padding: 8px 0; text-align: center; vertical-align: top;">${parseFloat(item.quantity) || 1}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td style="padding: 8px 0; text-align: right; vertical-align: top; font-weight: 600;">${isPackageService ? '-' : formatCurrency(item.total || 0)}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        `;
                    });

                    receiptHtml += `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </tbody>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </table>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div style="border-top: 1px dashed #000; margin-bottom: 15px;"></div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div style="font-size: 0.9rem;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <span>Subtotal</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span style="font-weight: 600;">${formatCurrency(subtotal)}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ${tax > 0 ? `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.85rem; color: #444;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span>Tax (${displayTaxRate}%)</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span>${formatCurrency(tax)}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ` : ''}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ${Math.abs(totalDiscount) > 0 ? `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.85rem; color: #dc2626;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <span>Discount</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span>-${formatCurrency(Math.abs(totalDiscount))}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ` : ''}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ${tip > 0 ? `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.85rem;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span>Tip</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span>${formatCurrency(tip)}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ` : ''}

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div style="border-top: 1px solid #000; margin: 10px 0;"></div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; font-size: 1.2rem; margin-bottom: 5px;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <span style="font-weight: 800;">TOTAL</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span style="font-weight: 800;">${formatCurrency(payable)}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ${(isPartial || isUnpaid) ? `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 1rem; color: #065f46;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span style="font-weight: 600;">Paid Amount</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span style="font-weight: 600;">${formatCurrency(paidAmount)}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 1rem; color: #b45309;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span style="font-weight: 600;">Outstanding</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span style="font-weight: 600;">${formatCurrency(outstanding)}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ` : ''}<div style="display:none">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ${sale.payment_method === 'cash' ? `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div style="display: flex; justify-content: space-between; margin-bottom: 2px; font-size: 0.85rem;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <span>Cash Given:</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <span>${formatCurrency(parseFloat(sale.tendered_amount) || 0)}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div style="display: flex; justify-content: space-between; margin-bottom: 2px; font-size: 0.85rem;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <span>Change Return:</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <span>${formatCurrency(parseFloat(sale.change_amount) || 0)}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ` : ''}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div style="border-top: 1px dashed #000; margin: 15px 0 10px 0;"></div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div style="text-align: center; font-size: 0.8rem; color: #444;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <p style="margin: 0; font-weight: 600; margin-bottom: 4px;">Thank You!</p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ${salonData.website ? `<p style="margin: 0;">${salonData.website}</p>` : ''}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <style>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            @media print { 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                body * { visibility: hidden; } 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                #receiptModal, #receiptModal * { visibility: visible; }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                #receiptModal .modal-content { border: none; box-shadow: none; }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                #receiptModal .modal-header, #receiptModal .modal-footer { display: none; }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                #receiptModal .modal-body { padding: 0; }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                #printableReceipt { 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    position: absolute; 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    left: 0; 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    top: 0; 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    width: 100%; 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    max-width: 80mm;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    margin: 0; 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    padding: 0;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    font-size: 12px; /* Base font size for print */
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                } 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                @page { margin: 0; size: auto; }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </style>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    `;

                    // Set receipt content and show modal
                    const receiptContentEl = document.getElementById('receiptContent');
                    receiptContentEl.innerHTML = receiptHtml;

                    const receiptModalEl = document.getElementById('receiptModal');
                    const receiptModal = bootstrap.Modal.getOrCreateInstance(receiptModalEl);

                    // Handle print buttons
                    $('#printReceiptBtn').off('click').on('click', function () {
                        const finalReceiptUrl = receiptUrl || "{{ route('admin.pos.receipt', ['sale' => ':id']) }}".replace(':id', sale.id);
                        window.open(finalReceiptUrl, '_blank');
                    });

                    $('#printArabicReceiptBtn').off('click').on('click', function () {
                        const finalArabicReceiptUrl = "{{ route('admin.pos.receipt.arabic', ['sale' => ':id']) }}".replace(':id', sale.id);
                        window.open(finalArabicReceiptUrl, '_blank');
                    });

                    $('#sendReceiptBtn').off('click').on('click', function (e) {
                        e.preventDefault();
                        alert('Receipt will be sent to the customer\'s email');
                    });

                    receiptModal.show();

                    // Focus on the print button
                    setTimeout(() => {
                        const printBtn = document.getElementById('printReceiptBtn');
                        if (printBtn) printBtn.focus();
                    }, 100);
                }
                // Handle unpaid confirmation
                $('#confirmUnpaidBtn').click(function () {
                    const unpaidModal = bootstrap.Modal.getInstance(document.getElementById('unpaidConfirmationModal'));
                    unpaidModal.hide();
                    processSale(true); // Call processSale with confirmUnpaid=true
                });

                // Print receipt
                $('#printReceiptBtn').click(function () {
                    const url = $(this).data('url');
                    if (url) {
                        window.open(url, '_blank');
                    } else {
                        showAlert('Receipt URL not found', 'error');
                    }
                });

                // Handle modal hidden event to clean up
                $('#addCustomerModal').on('hidden.bs.modal', function () {
                    // Reset the form when modal is closed
                    const form = document.getElementById('addCustomerForm');
                    if (form) {
                        form.reset();
                        form.classList.remove('was-validated');
                    }
                    // Make sure to remove any remaining backdrop
                    document.body.classList.remove('modal-open');
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());
                });

                // Handle receipt modal close - reload page to reset POS
                $('#receiptModal').on('hidden.bs.modal', function () {
                    // Reload the page to reset the POS for the next sale
                    window.location.reload();
                });

                // Mobile Segmented Switcher & Sticky Checkout Listeners
                function syncMobileColumns() {
                    if (window.innerWidth < 992) {
                        if ($('#mobileTabCart').hasClass('active')) {
                            $('.pos-catalog-column').hide();
                            $('.pos-cart-column').show();
                            $('body').addClass('mobile-view-cart');
                        } else {
                            $('.pos-catalog-column').show();
                            $('.pos-cart-column').hide();
                            $('body').removeClass('mobile-view-cart');
                        }
                    } else {
                        $('.pos-catalog-column').css('display', '');
                        $('.pos-cart-column').css('display', '');
                        $('body').removeClass('mobile-view-cart');
                    }
                }
                syncMobileColumns();
                $(window).on('resize', syncMobileColumns);

                $('#mobileTabCatalog').on('click', function () {
                    $(this).addClass('active');
                    $('#mobileTabCart').removeClass('active');
                    $('body').removeClass('mobile-view-cart');
                    if (window.innerWidth < 992) {
                        $('.pos-catalog-column').show();
                        $('.pos-cart-column').hide();
                    }
                    if (cart.length > 0) {
                        $('#posMobileStickyBar').fadeIn(150);
                    }
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });

                $('#mobileTabCart').on('click', function () {
                    $(this).addClass('active');
                    $('#mobileTabCatalog').removeClass('active');
                    $('body').addClass('mobile-view-cart');
                    if (window.innerWidth < 992) {
                        $('.pos-catalog-column').hide();
                        $('.pos-cart-column').show();
                    }
                    $('#posMobileStickyBar').fadeOut(150);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });

                $('#btnStickyCheckout').on('click', function () {
                    $('#mobileTabCart').trigger('click');
                });

                // Helper function to format currency
                function formatCurrency(amount) {
                    return currencySymbol + new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(amount);
                }

                // Function to check customer membership
                function checkCustomerMembership(customerId) {
                    $.ajax({
                        url: '{{ route("admin.pos.customer.membership", ":id") }}'.replace(':id', customerId),
                        method: 'GET',
                        success: function (response) {
                            if (response.has_membership && response.membership) {
                                $('#membershipName').text(response.membership.name);
                                $('#membershipInfo').removeClass('d-none');
                            } else {
                                $('#membershipInfo').addClass('d-none');
                            }
                        },
                        error: function () {
                            $('#membershipInfo').addClass('d-none');
                        }
                    });
                }

                // Function to fetch customer package balances
                function fetchCustomerPackageBalances(customerId) {

                    const url = '{{ route("admin.bookings.customer-package-balances", ":id") }}'.replace(':id', customerId);

                    $.ajax({
                        url: url,
                        method: 'GET',
                        success: function (response) {

                            // Flatten the grouped response
                            customerPackageBalances = [];
                            if (Array.isArray(response)) {
                                response.forEach(pkg => {
                                    if (pkg.services && Array.isArray(pkg.services)) {
                                        pkg.services.forEach(svc => {
                                            customerPackageBalances.push({
                                                package_id: pkg.id,
                                                package: {
                                                    id: pkg.id,
                                                    name: pkg.name
                                                },
                                                service_id: svc.id,
                                                service_name: svc.name,
                                                quantity_remaining: svc.quantity_remaining
                                            });
                                        });
                                    }
                                });
                            }


                            // If total is 0 and we have package balances, maybe auto-select package payment?
                            // For now just log it.
                        },
                        error: function (xhr) {


                        }
                    });
                }

                // Package handlers
                // Update price and total when package selected
                $('#addPackageSelect').on('change', function () {
                    const selected = $(this).find('option:selected');
                    const price = parseFloat(selected.data('price') || 0);
                    $('#addPackagePriceDisplay').val(price.toFixed(2));
                    calculatePackageTotal();
                });

                // Update total on qty or discount change
                $('#addPackageQtyInput, #addPackageDiscInput').on('input', function () {
                    calculatePackageTotal();
                });

                function calculatePackageTotal() {
                    const price = parseFloat($('#addPackagePriceDisplay').val()) || 0;
                    const qty = parseInt($('#addPackageQtyInput').val()) || 1;
                    const disc = parseFloat($('#addPackageDiscInput').val()) || 0;
                    const total = (price * qty) - disc;
                    $('#addPackageTotalDisplay').val(total.toFixed(2));
                }

                // Add package row to table
                $('#addPackageBtn').on('click', function () {
                    const pkgId = $('#addPackageSelect').val();
                    if (!pkgId) {
                        showAlert('Please select a package', 'warning');
                        return;
                    }
                    const selectedOption = $('#addPackageSelect option:selected');
                    const pkgName = selectedOption.data('name');
                    const serviceLimit = selectedOption.data('service-limit');
                    const staffId = $('#addPackageStaffSelect').val();
                    const qty = parseInt($('#addPackageQtyInput').val()) || 1;
                    const price = parseFloat($('#addPackagePriceDisplay').val()) || 0;
                    const disc = parseFloat($('#addPackageDiscInput').val()) || 0;

                    // Check if this is a customizable package (has service_limit)
                    if (serviceLimit && serviceLimit > 0) {
                        // Show service selection modal for customizable package
                        showPackageServiceModal(pkgId, pkgName, serviceLimit, staffId, qty, price, disc);
                    } else {
                        // Standard package - add normally
                        const packageItem = {
                            type: 'package',
                            id: pkgId,
                            name: pkgName,
                            price: price,
                            quantity: qty,
                            staff_id: staffId || null,
                            discount_amount: disc,
                            notes: ''
                        };
                        addToCart(packageItem);

                        // Fetch and add services
                        fetchAndAddPackageServices(pkgId, qty);

                        // Reset form
                        resetPackageForm();
                        showAlert('Package added successfully', 'success');
                    }
                });

                function resetPackageForm() {
                    $('#addPackageSelect').val('');
                    $('#addPackageStaffSelect').val('');
                    $('#addPackageQtyInput').val(1);
                    $('#addPackageDiscInput').val(0);
                    $('#addPackagePriceDisplay').val('0.00');
                    $('#addPackageTotalDisplay').val('0.00');
                }

                function fetchAndAddPackageServices(packageId, quantity) {
                    $.ajax({
                        url: "{{ route('admin.pos.package.services', ':id') }}".replace(':id', packageId),
                        method: 'GET',
                        success: function (response) {
                            if (response.services && response.services.length > 0) {
                                response.services.forEach(service => {
                                    const serviceItem = {
                                        type: 'service',
                                        id: service.id,
                                        name: service.name,
                                        price: 0, // Package services are usually 0 price in cart
                                        quantity: quantity, // Match package quantity
                                        staff_id: null,
                                        discount_amount: 0,
                                        notes: 'Included in package',
                                        package_id: packageId,
                                        service_id: service.id
                                    };
                                    addToCart(serviceItem);
                                });
                            }
                        },
                        error: function (xhr) {

                        }
                    });
                }



                // Handle membership select change
                $('#addMembershipSelect').on('change', function () {
                    const selectedOption = $(this).find('option:selected');
                    let price = 0;

                    // Try data-price first
                    let priceData = selectedOption.data('price');
                    if (priceData !== undefined && priceData !== null) {
                        price = parseFloat(priceData);
                        if (isNaN(price)) {
                            price = parseFloat(priceData.toString().replace(/[^\d.-]/g, ''));
                        }
                    }

                    // Fallback: parse from option text like "NEW TEST ($100.00)"
                    if (price === 0 || isNaN(price)) {
                        const text = selectedOption.text();
                        const match = text.match(/\$([\d,]+\.?\d*)/);
                        if (match) {
                            price = parseFloat(match[1].replace(/,/g, ''));
                        }
                    }

                    const qty = parseInt($('#addMembershipQtyInput').val()) || 1;

                    const disc = parseFloat($('#addMembershipDiscInput').val()) || 0;

                    $('#addMembershipPriceDisplay').val(formatCurrency(price));
                    $('#addMembershipTotalDisplay').val(formatCurrency((price * qty) - disc));
                });

                // Handle membership quantity or discount change
                $('#addMembershipQtyInput, #addMembershipDiscInput').on('input', function () {
                    const selectedOption = $('#addMembershipSelect').find('option:selected');
                    const price = parseFloat(selectedOption.data('price')) || 0;
                    const qty = parseInt($('#addMembershipQtyInput').val()) || 1;
                    const disc = parseFloat($('#addMembershipDiscInput').val()) || 0;

                    $('#addMembershipTotalDisplay').val(formatCurrency((price * qty) - disc));
                });

                // Handle add membership to cart
                $('#addMembershipBtn').on('click', function () {
                    const selectedOption = $('#addMembershipSelect').find('option:selected');
                    const membershipId = selectedOption.val();
                    const membershipName = selectedOption.data('name');
                    let priceData = selectedOption.data('price');
                    let price = 0;
                    if (priceData !== undefined && priceData !== null) {
                        price = parseFloat(priceData);
                        if (isNaN(price)) {
                            price = parseFloat(priceData.toString().replace(/[^\d.-]/g, ''));
                        }
                    }
                    // Fallback parse from text
                    if (price === 0 || isNaN(price)) {
                        const text = selectedOption.text();
                        const match = text.match(/\$([\d,]+\.?\d*)/);
                        if (match) {
                            price = parseFloat(match[1].replace(/,/g, ''));
                        }
                    }
                    const qty = parseInt($('#addMembershipQtyInput').val()) || 1;
                    const type = selectedOption.data('type');
                    const validity = selectedOption.data('validity');

                    const disc = parseFloat($('#addMembershipDiscInput').val()) || 0;

                    if (!membershipId) {
                        showAlert('Please select a membership', 'warning');
                        return;
                    }

                    if (qty < 1) {
                        showAlert('Quantity must be at least 1', 'warning');
                        return;
                    }

                    // Add to cart (addToCart handles merging and discount accumulation)
                    const membershipItem = {
                        id: membershipId,
                        name: membershipName,
                        price: price,
                        quantity: qty,
                        type: 'membership',
                        membership_type: type,
                        validity_period: validity,
                        staff_id: null,
                        discount_amount: disc,
                        editable: true
                    };
                    addToCart(membershipItem);

                    // Reset form
                    $('#addMembershipSelect').val('');
                    $('#addMembershipQtyInput').val(1);
                    $('#addMembershipPriceDisplay').val('0.00');
                    $('#addMembershipTotalDisplay').val('0.00');

                    showAlert('Membership added successfully', 'success');
                });

                // Initialize payment UI
                const initialMethod = $('input[name="payment_method"]:checked').val();
                if (initialMethod === 'cash') {
                    $('#paymentDetailsSection').show();
                } else {
                    $('#paymentDetailsSection').hide();
                }

                // Initial Cart & UI calculation on page load
                updateCart(true);

            });

            // End of POS scripts
        </script>
    @endpush

    <!-- New Sale Modal will be handled by the newSaleModal -->

    <!-- Unpaid Confirmation Modal -->
    <div class="modal fade" id="unpaidConfirmationModal" tabindex="-1" aria-labelledby="unpaidConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="unpaidConfirmationModalLabel"><i
                            class="fas fa-exclamation-triangle me-2"></i> Confirm Unpaid Sale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div
                            class="bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center p-3 rounded-circle mb-3">
                            <i class="fas fa-exclamation-circle text-warning" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5>Are you sure you want to mark this sale as unpaid?</h5>
                        <p class="text-muted">The sale will be recorded but marked as unpaid. You can collect
                            payment later
                            from the sales records.</p>
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>Make sure to collect payment from the customer later.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="confirmUnpaidBtn">
                        <i class="fas fa-check-circle me-1"></i> Confirm Unpaid Sale
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 660px;">
            <div class="modal-content">
                <div class="modal-header" id="receiptModalHeader">
                    <h5 class="modal-title text-white" id="receiptModalLabel">
                        <i class="fas fa-receipt me-2"></i> Sale Receipt
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="text-center p-4" id="receiptStatusBanner">
                        <div class="status-icon mb-3">
                            <i class="fas fa-check-circle fa-3x text-success" id="receiptStatusIcon"></i>
                        </div>
                        <h4 class="mb-2" id="receiptStatusTitle">Payment Successful</h4>
                        <p class="text-muted mb-0" id="receiptStatusMessage">Thank you for your payment</p>
                    </div>
                    <div id="receiptContent" class="p-4"></div>
                </div>
                <div class="modal-footer d-flex justify-content-center gap-2 pb-4">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                    <a href="#" class="btn btn-outline-primary px-4" id="sendReceiptBtn">
                        <i class="fas fa-paper-plane me-1"></i> Send
                    </a>
                    <button type="button" class="btn btn-primary px-4" id="printReceiptBtn">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                    @if($posReceiptArabicButton ?? true)
                        <button type="button" class="btn btn-info text-white px-4" id="printArabicReceiptBtn">
                            <i class="fas fa-print me-1"></i> Arabic
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        #receiptModal .modal-content {
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }

        #receiptModal .modal-header {
            background: linear-gradient(135deg, #10b981, #059669);
            border-bottom: none;
            padding: 1rem 1.5rem;
        }

        #receiptStatusBanner {
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
        }

        .status-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(16, 185, 129, 0.1);
        }

        .status-icon.unpaid {
            background-color: rgba(239, 68, 68, 0.1);
        }

        .status-icon i {
            font-size: 2.5rem;
        }

        #printReceiptBtn,
        #sendReceiptBtn {
            min-width: 100px;
        }
    </style>

    <script> // Auto-select customer from URL parameter (for "Pay in POS" functionality)

        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const customerId = urlParams.get('customer');

            if (customerId) {
                const customerSelect = document.getElementById('customerSelect');

                if (customerSelect) {
                    // Set the customer select value
                    customerSelect.value = customerId;

                    // Trigger change event to update the UI
                    const event = new Event('change', {
                        bubbles: true
                    });
                    customerSelect.dispatchEvent(event);

                    // If using Select2, trigger it differently
                    if (typeof jQuery !== 'undefined' && jQuery(customerSelect).data('select2')) {
                        jQuery(customerSelect).val(customerId).trigger('change');
                    }

                    // Remove the customer parameter from URL to clean it up
                    // Note: booking_id parameter is handled by POS's built-in logic
                    const newUrl = new URL(window.location);
                    newUrl.searchParams.delete('customer');

                    window.history.replaceState({}

                        , document.title, newUrl.toString());
                }
            }
        });
</script>@endsection