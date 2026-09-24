@extends('layouts.app')

@section('title', 'Point of Sale')

@push('styles')
    <style>
        body {
            overflow: hidden !important;
        }

        /* Tight layout styles to make everything fit */
        .card-body {
            padding: 0.5rem !important;
        }
        .card-header {
            padding: 0.5rem 0.75rem !important;
        }
        .table th, .table td {
            padding: 0.4rem 0.5rem !important;
            vertical-align: middle !important;
        }
        .form-control, .form-select, .btn {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.85rem !important;
        }
        .icon-shape.icon-sm {
            width: 28px !important;
            height: 28px !important;
        }
        .form-label {
            margin-bottom: 0.25rem !important;
        }
        .row.g-3 {
            --bs-gutter-y: 0.5rem !important;
            --bs-gutter-x: 0.5rem !important;
        }

        .pos-cart {
            height: calc(100vh - 220px);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Fix POS left section layout - no extra bottom gap */
        #posFormContainer {
            flex-shrink: 0;
        }
        .table-responsive.flex-fill-table {
            flex: 1 1 0;
            min-height: 0;
            overflow-y: auto;
        }
        /* Ensure the row fills available height */
        .pos-main-row {
            flex: 1 1 0;
            min-height: 0;
        }
        .pos-main-row .col-lg-8,
        .pos-main-row .col-lg-4 {
            display: flex;
            flex-direction: column;
        }
        .pos-main-row .col-lg-8 > .card,
        .pos-main-row .col-lg-4 > .card {
            flex: 1 1 0;
            min-height: 0;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-3 py-1 bg-light d-flex flex-column" style="height: calc(100vh - var(--header-height) - 15px); overflow: hidden;">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-2 pb-2 border-bottom">
            <div class="mb-1 mb-md-0">
                <h1 class="fs-5 mb-0 fw-semibold">
                    <i class="fas fa-cash-register me-2 text-primary"></i>Point of Sale
                </h1>
                <nav aria-label="breadcrumb" class="d-none d-md-block">
                    <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"
                                class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">POS Terminal</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-md-end gap-2">
                    @if(optional(auth()->user()->salon)->business_type === 'both')
                        <a href="{{ route('admin.pos.index', ['view' => 'barber']) }}" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-exchange-alt me-1"></i> Switch to Barber POS
                        </a>
                    @endif
                    <a href="{{ route('admin.pos.analytics.index') }}" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-chart-line me-1"></i> Analytics
                    </a>
                    <a href="{{ route('admin.pos.sales.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-history me-1"></i> Sales History
                    </a>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newSaleModal">
                        <i class="fas fa-plus me-1"></i> New Sale
                    </button>
                </div>
            </div>
        </div>
        <!-- End of Page Header -->

        <div class="row g-2 pb-2 pos-main-row flex-grow-1" style="min-height: 0; margin-top: 0; overflow: hidden;">
        <!-- Left Column - Products/Services -->
        <div class="col-lg-8">
            <div class="card shadow-sm" style="height: 100%; display: flex; flex-direction: column; overflow: hidden;">
                <!-- Card Header with Tabs -->
                <div class="card-header bg-white py-2 border-bottom">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="fas fa-store me-2 text-primary"></i>Products & Services
                        </h5>
                        <div class="position-relative" style="width: 220px; flex-shrink: 0;">
                            <div class="input-group input-group-sm shadow-sm">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" id="searchInput" class="form-control form-control-sm border-start-0"
                                    placeholder="Search products or services..." autocomplete="off">
                            </div>
                            <div id="searchResults" class="position-absolute w-100 z-3 mt-1 shadow d-none">
                                <div class="card border-0">
                                    <div class="list-group list-group-flush" id="searchResultsList">
                                        <div class="list-group-item text-muted py-3 text-center">
                                            <i class="fas fa-search me-2"></i>Type to search products or services
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap gap-2 mt-3" id="posActionButtons">
                        <button class="btn btn-outline-primary active" data-form="services">
                            <i class="fas fa-spa me-1"></i> Services
                        </button>
                        <button class="btn btn-outline-success" data-form="products">
                            <i class="fas fa-box me-1"></i> Products
                        </button>
                        <button class="btn btn-outline-info" data-form="packages">
                            <i class="fas fa-gift me-1"></i> Packages
                        </button>
                        <button class="btn btn-outline-secondary" data-form="memberships">
                            <i class="fas fa-star me-1"></i> Memberships
                        </button>
                </div>
                </div>
                <div class="card-body p-2 flex-grow-1 d-flex flex-column" style="overflow: hidden; min-height: 0;">
                    <!-- Dynamic Form Container -->
                    <div id="posFormContainer" class="d-flex flex-column" style="flex-shrink: 0;">
                        <!-- Services Form -->
                        <div class="pos-form" id="servicesForm">
                            <div class="d-flex align-items-end gap-2 w-100" style="flex-wrap: nowrap;">
                                {{-- Service: flex:3 (~60% of remaining) --}}
                                <div style="flex: 3 1 0; min-width: 0;">
                                    <label class="form-label fw-medium small mb-1">Service</label>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center"
                                            type="button" id="servicesDropdown"
                                            data-bs-toggle="dropdown"
                                            data-bs-auto-close="outside"
                                            aria-expanded="false"
                                            style="height: 31px; border: 1px solid #ced4da; background: white; color: #495057; overflow: hidden;">
                                            <span id="servicesDropdownLabel" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Select Services</span>
                                        </button>
                                        <ul class="dropdown-menu p-2" aria-labelledby="servicesDropdown" style="max-height: 250px; overflow-y: auto; min-width: 240px;">
                                            @foreach($services as $service)
                                                <li>
                                                    <div class="form-check py-1">
                                                        <input class="form-check-input service-checkbox" type="checkbox" value="{{ $service->id }}" id="service_cb_{{ $service->id }}" data-name="{{ $service->name }}" data-price="{{ $service->price }}">
                                                        <label class="form-check-label small w-100" style="cursor:pointer;" for="service_cb_{{ $service->id }}">
                                                            {{ $service->name }} ({{ format_currency($service->price) }})
                                                        </label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                {{-- Staff: flex:2 (~40% of remaining) --}}
                                <div style="flex: 2 1 0; min-width: 0;">
                                    <label class="form-label fw-medium small mb-1">Staff</label>
                                    <select class="form-select form-select-sm staff-select" id="addStaffSelect">
                                        <option value="">Select Staff</option>
                                        @foreach($staff as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- Qty: fixed 58px --}}
                                <div style="flex: 0 0 58px;">
                                    <label class="form-label fw-medium small mb-1">Qty</label>
                                    <input type="number" class="form-control form-control-sm qty-input" id="addQtyInput" value="1" min="1">
                                </div>
                                {{-- Price: fixed 85px --}}
                                <div style="flex: 0 0 85px;">
                                    <label class="form-label fw-medium small mb-1">Price</label>
                                    <input type="text" class="form-control form-control-sm price-display" id="addPriceDisplay" readonly value="0.00">
                                </div>
                                {{-- Disc: fixed 65px --}}
                                <div style="flex: 0 0 65px;">
                                    <label class="form-label fw-medium small mb-1">Disc</label>
                                    <input type="number" class="form-control form-control-sm disc-input" id="addDiscInput" value="0" min="0" step="0.01">
                                </div>
                                {{-- Add button --}}
                                <div style="flex: 0 0 85px; padding-top: 22px;">
                                    <button class="btn btn-primary btn-sm w-100" id="addServiceBtn">
                                        <i class="fas fa-plus me-1"></i>Add
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Products Form -->
                        <div class="pos-form d-none" id="productsForm">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-medium small">Product</label>
                                    <select class="form-select form-select-sm product-select" id="addProductSelect">
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-name="{{ $product->name }}"
                                                data-price="{{ $product->selling_price }}"
                                                data-stock="{{ $product->quantity_in_stock }}" {{ $product->quantity_in_stock <= 0 ? 'disabled' : '' }}>
                                                {{ $product->name }} ({{ format_currency($product->selling_price) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-medium small">Staff</label>
                                    <select class="form-select form-select-sm staff-select" id="addProductStaffSelect">
                                        <option value="">Select Staff</option>
                                        @foreach($staff as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label fw-medium small">Qty</label>
                                    <input type="number" class="form-control form-control-sm qty-input"
                                        id="addProductQtyInput" value="1" min="1">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-medium small">Price</label>
                                    <input type="text" class="form-control form-control-sm price-display"
                                        id="addProductPriceDisplay" readonly value="0.00">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-medium small">Disc</label>
                                    <input type="number" class="form-control form-control-sm disc-input"
                                        id="addProductDiscInput" value="0" min="0" step="0.01">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button class="btn btn-success btn-sm w-100" id="addProductBtn">
                                        <i class="fas fa-plus me-1"></i>Add
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Packages Form -->
                        <div class="pos-form d-none" id="packagesForm">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-medium small">Package</label>
                                    <select class="form-select form-select-sm" id="addPackageSelect">
                                        <option value="">Select Package</option>
                                        @foreach($packages as $package)
                                            <option value="{{ $package->id }}" data-name="{{ $package->name }}"
                                                data-price="{{ $package->price }}"
                                                data-service-limit="{{ $package->service_limit ?? '' }}">
                                                {{ $package->name }} ({{ format_currency($package->price) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-medium small">Staff</label>
                                    <select class="form-select form-select-sm" id="addPackageStaffSelect">
                                        <option value="">Select Staff</option>
                                        @foreach($staff as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label fw-medium small">Qty</label>
                                    <input type="number" class="form-control form-control-sm" id="addPackageQtyInput"
                                        value="1" min="1">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-medium small">Price</label>
                                    <input type="text" class="form-control form-control-sm" id="addPackagePriceDisplay"
                                        readonly value="0.00">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-medium small">Disc %</label>
                                    <input type="number" class="form-control form-control-sm" id="addPackageDiscInput"
                                        value="0" min="0" max="100" step="0.01">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button class="btn btn-info btn-sm w-100 text-white" id="addPackageBtn">
                                        <i class="fas fa-plus me-1"></i>Add
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Memberships Form -->
                        <div class="pos-form d-none" id="membershipsForm">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-medium small">Membership</label>
                                    <select class="form-select form-select-sm" id="addMembershipSelect">
                                        <option value="">Select Membership</option>
                                        @foreach($memberships as $membership)
                                            <option value="{{ $membership->id }}" data-name="{{ $membership->name }}"
                                                data-price="{{ $membership->calculateTotalMembershipPrice() }}">
                                                {{ $membership->name }}
                                                ({{ format_currency($membership->calculateTotalMembershipPrice()) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label fw-medium small">Qty</label>
                                    <input type="number" class="form-control form-control-sm" id="addMembershipQtyInput"
                                        value="1" min="1">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-medium small">Price</label>
                                    <input type="text" class="form-control form-control-sm" id="addMembershipPriceDisplay"
                                        readonly value="0.00">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-medium small">Disc</label>
                                    <input type="number" class="form-control form-control-sm" id="addMembershipDiscInput"
                                        value="0" min="0" step="0.01">
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button class="btn btn-secondary btn-sm w-100" id="addMembershipBtn">
                                        <i class="fas fa-plus me-1"></i>Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Unified Cart Table -->
                    <div class="table-responsive mt-2 border rounded shadow-sm" style="overflow-y: auto; flex: 1 1 0; min-height: 0;">
                        <table class="table table-sm table-hover mb-0" id="unifiedCartTable">
                            <thead class="table-light">
                                <tr class="text-uppercase small text-muted">
                                    <th class="ps-3 py-2 fw-semibold">Item Details</th>
                                    <th class="text-center py-2 fw-semibold">Staff</th>
                                    <th class="text-center py-2 fw-semibold" style="width: 80px;">Qty</th>
                                    <th class="text-end py-2 fw-semibold">Price</th>
                                    <th class="text-end py-2 fw-semibold" style="width: 100px;">Disc</th>
                                    <th class="text-end py-2 fw-semibold">Total</th>
                                    <th class="text-center py-2 fw-semibold">Action</th>
                                </tr>
                            </thead>
                            <tbody id="unifiedCartBody">
                                <!-- Dynamic rows will be added here -->
                                <tr id="emptyCartRow">
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-shopping-cart fa-3x mb-3 opacity-25"></i>
                                        <p class="mb-0">Your cart is empty. Add items to start billing.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Billing Summary -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="height: 100%; display: flex; flex-direction: column; overflow: hidden;">
                <div class="card-header bg-white py-1">
                    <h5 class="card-title mb-0" style="font-size: 0.95rem;">
                        <i class="fas fa-receipt me-2"></i>Billing Summary
                    </h5>
                </div>
                <div class="card-body p-2 flex-grow-1 overflow-y-auto">
                    <!-- Customer Info -->
                    <div class="mb-2">
                        <div id="customerSearchSection">
                            <label class="form-label fw-medium">Customer</label>
                            <div class="position-relative">
                                <select class="form-select" id="customerSelect">
                                    <option value="">Walk-in Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer['id'] }}" data-name="{{ $customer['name'] }}"
                                            data-phone="{{ $customer['phone'] }}" data-email="{{ $customer['email'] }}">
                                            {{ $customer['text'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-2 text-end">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#addCustomerModal">
                                    <i class="fas fa-plus"></i> New Customer
                                </button>
                            </div>
                        </div>
                        <input type="hidden" id="customerId" value="">
                        <input type="hidden" id="existingSaleId" value="">
                        <div id="customerDisplay" class="mt-2 p-2 bg-light rounded d-none">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong id="customerName"></strong><br>
                                    <small id="customerPhone"></small><br>
                                    <small id="customerEmail" class="text-muted"></small>
                                    <div id="membershipInfo" class="mt-1 d-none">
                                        <small class="badge bg-success text-white">
                                            <i class="fas fa-star me-1"></i>Membership: <span id="membershipName"></span>
                                            <small id="membershipDates" class="ms-1"></small>
                                        </small>
                                    </div>
                                    <div id="assignMembershipSection" class="mt-2 d-none">
                                        <button class="btn btn-sm btn-outline-primary" id="assignMembershipBtn">
                                            <i class="fas fa-plus me-1"></i>Assign Membership
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-secondary" id="manageMembershipBtn"
                                        style="display: none;">
                                        <i class="fas fa-edit me-1"></i>Manage
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" id="clearCustomerBtn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-top pt-2">
                        <div class=" row mb-1">
                            <div class="col-8"><small class="text-muted">Subtotal</small></div>
                            <div class="col-4 text-end"><span id="cartSubtotal" class="fw-medium">0.00</span></div>
                        </div>

                        <!-- Discount Row -->
                        <div class="row mb-1 align-items-center">
                            <div class="col-8">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-tag text-muted"></i>
                                    </span>
                                    <input type="number" class="form-control border-start-0 border-end-0"
                                        id="discountValue" placeholder="Discount" min="0" step="0.01">
                                    <select class="form-select border-start-0 border-end-0" id="discountType" style="max-width: 65px;">
                                        <option value="fixed">$</option>
                                        <option value="percent">%</option>
                                    </select>
                                    <button class="btn btn-outline-secondary" type="button" id="applyDiscount" title="Apply Discount">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-outline-danger d-none" type="button" id="clearDiscount" title="Remove Discount">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <span id="cartDiscount" class="fw-medium text-danger">-0.00</span>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-8"><small class="text-muted">Tax
                                    ({{ number_format($taxRate ?? 0, 2) + 0 }}%)</small></div>
                            <div class="col-4 text-end"><span id="cartTax" class="fw-medium">0.00</span></div>
                        </div>
                        <hr class="my-1">
                        <div class="row mb-1">
                            <div class="col-8"><strong>Total</strong></div>
                            <div class="col-4 text-end"><strong><span id="grandTotal"
                                        class="text-primary">0.00</span></strong>
                            </div>
                        </div>

                        <div class="row mb-1">
                            <div class="col-8"><small class="text-muted">Payable</small></div>
                            <div class="col-4 text-end"><span id="payableAmount" class="fw-medium">0.00</span></div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-8"><small class="text-muted">Balance Due</small></div>
                            <div class="col-4 text-end"><span id="outstandingAmount"
                                    class="fw-medium text-danger">0.00</span></div>
                        </div>

                        <!-- Tip -->
                        <div class="row mb-1">
                            <div class="col-6">
                                <label class="form-label small mb-0">Tip</label>
                                <input type="number" class="form-control form-control-sm py-0" style="height: 26px;" id="tipAmount" value="0" min="0"
                                    step="0.01">
                            </div>

                            <!-- Payment Method Selection -->
                            <div class="mb-2 mt-1">
                                <label class="form-label small fw-semibold mb-1">Payment Method</label>
                                <div class="row g-1">
                                    @if($acceptCash ?? true)
                                        <div class="col-6">
                                            <input type="radio" class="btn-check" name="payment_method" id="cartPaymentCash"
                                                value="cash" autocomplete="off" checked>
                                            <label class="btn btn-outline-success btn-sm w-100 py-1" for="cartPaymentCash">
                                                <i class="fas fa-money-bill-wave me-1"></i>Cash
                                            </label>
                                        </div>
                                    @endif

                                    @if($acceptCard ?? true)
                                        <div class="col-6">
                                            <input type="radio" class="btn-check" name="payment_method" id="cartPaymentCard"
                                                value="card" autocomplete="off">
                                            <label class="btn btn-outline-primary btn-sm w-100 py-1" for="cartPaymentCard">
                                                <i class="fas fa-credit-card me-1"></i>Card
                                            </label>
                                        </div>
                                    @endif

                                    @if($acceptOnline ?? true)
                                        <div class="col-6">
                                            <input type="radio" class="btn-check" name="payment_method" id="cartPaymentOnline"
                                                value="online" autocomplete="off">
                                            <label class="btn btn-outline-info btn-sm w-100 py-1" for="cartPaymentOnline">
                                                <i class="fas fa-globe me-1"></i>Online
                                            </label>
                                        </div>
                                    @endif

                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="payment_method" id="cartPaymentOther"
                                            value="other" autocomplete="off">
                                        <label class="btn btn-outline-secondary btn-sm w-100 py-1" for="cartPaymentOther">
                                            <i class="fas fa-ellipsis-h me-1"></i>Other
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="payment_method" id="cartPaymentMixed"
                                            value="mixed" autocomplete="off">
                                        <label class="btn btn-outline-dark btn-sm w-100 py-1" for="cartPaymentMixed">
                                            <i class="fas fa-columns me-1"></i>Split
                                        </label>
                                    </div>
                                    <div class="col-6" style="display: none;">
                                        <input type="radio" class="btn-check" name="payment_method" id="cartPaymentPackage"
                                            value="package" autocomplete="off">
                                        <label class="btn btn-outline-primary btn-sm w-100 py-1" for="cartPaymentPackage">
                                            <i class="fas fa-box-open me-1"></i>Package
                                        </label>
                                    </div>
                                    <div class="col-12">
                                        <input type="radio" class="btn-check" name="payment_method" id="cartPaymentNone"
                                            value="none" autocomplete="off">
                                        <label class="btn btn-outline-warning btn-sm w-100 py-1" for="cartPaymentNone">
                                            <i class="fas fa-clock me-1"></i>Unpaid / Pay Later
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details (Visible for Cash) -->
                        <div id="paymentDetailsSection">
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label class="form-label small mb-0">Amount Received <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control form-control-sm py-0" style="height: 26px;" id="amountReceived"
                                        step="0.01" min="0">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small mb-0">Change</label>
                                    <input type="text" class="form-control form-control-sm py-0" style="height: 26px;" id="changeAmount" readonly
                                        value="0.00">
                                </div>
                            </div>
                        </div>

                        <!-- Split Payment Details (Visible for Split) -->
                        <div id="splitPaymentSection" class="d-none">
                            <div class="mb-2">
                                <label class="form-label small fw-semibold mb-1">Split Amounts</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small text-muted mb-0">Cash</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text py-0"><i class="fas fa-money-bill-wave"></i></span>
                                            <input type="number" class="form-control split-input py-0" style="height: 26px;" id="splitCash"
                                                placeholder="0.00" min="0" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small text-muted mb-0">Card</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text py-0"><i class="fas fa-credit-card"></i></span>
                                            <input type="number" class="form-control split-input py-0" style="height: 26px;" id="splitCard"
                                                placeholder="0.00" min="0" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small text-muted mb-0">Online</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text py-0"><i class="fas fa-globe"></i></span>
                                            <input type="number" class="form-control split-input py-0" style="height: 26px;" id="splitOnline"
                                                placeholder="0.00" min="0" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small text-muted mb-0">Other</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text py-0"><i class="fas fa-ellipsis-h"></i></span>
                                            <input type="number" class="form-control split-input py-0" style="height: 26px;" id="splitOther"
                                                placeholder="0.00" min="0" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-1 text-end">
                                    <small class="text-muted">Remaining: <span id="splitRemaining"
                                            class="fw-bold text-danger">0.00</span></small>
                                </div>
                            </div>
                        </div>

                            <!-- Hidden inputs for cash/card amounts -->
                            <input type="hidden" id="cashAmount" value="0">
                            <input type="hidden" id="cardAmount" value="0">
                            <input type="hidden" id="onlineAmount" value="0">
                            <input type="hidden" id="otherAmount" value="0">
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top p-2">
                    <!-- Checkout Button -->
                    <button class="btn btn-success w-100" id="checkoutBtn" disabled data-bs-toggle="modal"
                        data-bs-target="#saleConfirmationModal">
                        <i class="fas fa-check-circle me-2"></i>Process Sale
                    </button>
                    <button class="btn btn-outline-secondary w-100 mt-2" id="resetBtn">
                        <i class="fas fa-undo me-2"></i>Reset
                    </button>
                </div>
            </div>
        </div>
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
                    <p class="text-muted mb-0 small" id="resetModalMessage">Are you sure you want to reset the terminal for a new sale?</p>
                </div>

                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <div class="d-flex gap-2 w-100">
                        <button type="button" class="btn btn-light flex-grow-1 border fw-semibold py-2"
                            data-bs-dismiss="modal" style="border-radius: 8px;">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-danger flex-grow-1 fw-semibold py-2 shadow-sm"
                            id="confirmResetBtn" style="border-radius: 8px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none;">
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
                    '#addServiceBtn', '#addProductBtn', '#addPackageBtn', '#addMembershipBtn',
                    '#clearCustomerBtn', '.qty-input', '.disc-input',
                    '#servicesDropdown', '#addProductSelect', '#addPackageSelect', '#addMembershipSelect',
                    '#addStaffSelect', '#addProductStaffSelect', '#addPackageStaffSelect'
                ];

                selectors.forEach(selector => {
                    $(selector).prop('disabled', disabled);
                });

                // Handle radio buttons separately
                $('input[name="payment_method"]').prop('disabled', disabled);

                // Handle customer select (select2)
                $('#customerSelect').prop('disabled', disabled);

                if (disabled) {
                    $('#checkoutBtn').attr('title', 'This sale is already paid').addClass('btn-secondary').removeClass('btn-success').html('<i class="fas fa-check-circle me-2"></i>Already Paid');
                    showAlert('This sale is already paid and cannot be modified.', 'info');
                } else {
                    $('#checkoutBtn').removeAttr('title').addClass('btn-success').removeClass('btn-secondary').html('<i class="fas fa-check-circle me-2"></i>Process Sale');
                }

                isPaidSale = !!disabled;

                // If it was just disabled, we might need to re-render the table to disable row inputs
                if (typeof renderUnifiedTable === 'function') {
                    renderUnifiedTable();
                }
            }

            $(document).ready(function () {
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
                        $('#discountValue').val(discountPercent.toFixed(2));
                    } else {
                        discountAmount = Math.max(0, discountValue);
                        discountPercent = 0;
                    }

                    updateCart();
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

                // POS Action Buttons Toggling
                $('#posActionButtons button').on('click', function () {
                    const formId = $(this).data('form');

                    // Update buttons active state
                    $('#posActionButtons button').removeClass('active');
                    $(this).addClass('active');

                    // Show selected form, hide others
                    $('.pos-form').addClass('d-none');
                    $(`#${formId}Form`).removeClass('d-none');
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
                        const cartItem = {
                            ...item,
                            quantity: item.quantity || 1,
                            total: (item.price || 0) * (item.quantity || 1),
                            staff_id: item.staff_id || null,
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

                        const list = typeof staffList !== 'undefined' ? staffList : [];
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
                        } else {
                            $staffSelect.val('');
                        }
                    }

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
                        $body.append(`<tr id="emptyCartRow">
                                                                                                                                                                                                                                                                                                                                                                                                                                                            <td colspan="7" class="text-center py-5 text-muted">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                <i class="fas fa-shopping-cart fa-3x mb-3 opacity-25"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                <p class="mb-0">Your cart is empty. Add items to start billing.</p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                            </td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                <                                            /tr>`);
                        return;
                    }

                    cart.forEach((item, index) => {
                        // Check if this is a sub-item of a package that is ALSO in the cart
                        const parentPackage = cart.find(i => i.type === 'package' && i.id == item.package_id);
                        if (item.package_id && item.price === 0 && parentPackage) return;

                        let filteredStaffList = typeof staffList !== 'undefined' ? staffList : [];
                        if (item.type === 'service') {
                            filteredStaffList = filteredStaffList.filter(staff => 
                                (staff.service_ids && staff.service_ids.map(String).includes(String(item.id))) ||
                                (item.staff_id && String(staff.id) === String(item.staff_id))
                            );
                        }
                        const staffOptions = filteredStaffList.map(staff =>
                            `<option value="${staff.id}" ${staff.id == item.staff_id ? 'selected' : ''}>${staff.name}</option>`
                        ).join('');

                        const staffSelect = `<select class="form-select form-select-sm staff-select" data-index="${index}" style="min-width: 100px;" ${isPaidSale ? 'disabled' : ''}>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <option value="">Select Staff</option>
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
                                let filteredSubStaffList = typeof staffList !== 'undefined' ? staffList : [];
                                if (subItem.type === 'service') {
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
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <option value="">Select Staff</option>
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

                    // Only show discount if there is one
                    if (totalDiscount > 0) {
                        $('#cartDiscount').parent().show();
                        $('#cartDiscount').text('-' + formatCurrency(totalDiscount));
                    } else {
                        $('#cartDiscount').parent().hide();
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
                        if ($('#cartPaidRow').length === 0) {
                            $('#payableAmount').closest('.row').before(`
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <div class="row mb-2" id="cartPaidRow" style="color: #065f46;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             <div class="col-8"><small>Paid Already</small></div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             <div class="col-4 text-end"><span class="fw-medium">${formatCurrency(previousPaidAmount)}</span></div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     `);
                        } else {
                            $('#cartPaidRow').find('span').text(formatCurrency(previousPaidAmount));
                            $('#cartPaidRow').show();
                        }
                    } else {
                        $('#cartPaidRow').hide();
                    }

                    $('#payableAmount').text(formatCurrency(Math.max(0, payable)));

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
                        $('#checkoutBtn').html('<i class="fas fa-check-circle me-2"></i>Already Paid').addClass('btn-secondary').removeClass('btn-success');
                    } else {
                        $('#checkoutBtn').html('<i class="fas fa-check-circle me-2"></i>Process Sale').addClass('btn-success').removeClass('btn-secondary');

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

                // Handle discount input change
                $(document).on('input', '.disc-input', function () {
                    const index = $(this).data('index');
                    const $row = $(this).closest('tr');
                    if (index >= 0 && index < cart.length) {
                        const newDisc = parseFloat($(this).val()) || 0;
                        cart[index].discount_amount = newDisc;
                        // Recalculate totals
                        const baseTotal = cart[index].quantity * cart[index].price;
                        cart[index].total = baseTotal - newDisc;

                        // Update row total display
                        $row.find('td:eq(5)').text(formatCurrency(cart[index].total));

                        updateCart(false); // Don't re-render table
                    }
                });

                // Handle clearing item discount
                $(document).on('click', '.btn-clear-item-discount', function () {
                    const index = $(this).data('index');
                    if (index >= 0 && index < cart.length) {
                        cart[index].discount_amount = 0;
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
                    // Show/hide tip staff section
                    if (tipAmount > 0) {
                        $('#tipStaffSection').show();
                    } else {
                        $('#tipStaffSection').hide();
                        $('#tipStaffSelect').val('');
                    }
                    updateCart();
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