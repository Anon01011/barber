@extends('layouts.super-admin')

@section('content')
    <div class="container-fluid px-3 px-md-4 py-4">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        <i class="fas fa-layer-group me-1"></i> SaaS Tiers
                    </span>
                    <span class="text-muted small">Subscription Plan Creator</span>
                </div>
                <h1 class="h3 fw-bold text-gray-900 mb-0">Create Subscription Plan</h1>
                <p class="text-muted small mb-0">Configure pricing, billing intervals, module permissions, and operational limits for salon subscribers.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-secondary btn-sm px-3 rounded-3 shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to Plans
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.plans.store') }}" id="planCreateForm">
            @csrf

            <div class="row g-4">
                <!-- Main Form (Tabs) -->
                <div class="col-lg-8">
                    <!-- Modern Pill Tabs -->
                    <ul class="nav nav-pills mb-3 p-1 rounded-4 bg-white shadow-sm border" id="planTabs" role="tablist">
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link active rounded-3 py-2 fw-semibold d-flex align-items-center justify-content-center" id="basic-tab" data-bs-toggle="pill" data-bs-target="#basic" type="button" role="tab">
                                <i class="fas fa-info-circle me-2 text-primary"></i>Basic Info
                            </button>
                        </li>
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link rounded-3 py-2 fw-semibold d-flex align-items-center justify-content-center" id="features-tab" data-bs-toggle="pill" data-bs-target="#features" type="button" role="tab">
                                <i class="fas fa-cubes me-2 text-info"></i>Features &amp; Modules
                                <span id="features-tab-count" class="badge bg-primary ms-2 rounded-pill px-2">0</span>
                            </button>
                        </li>
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link rounded-3 py-2 fw-semibold d-flex align-items-center justify-content-center" id="limits-tab" data-bs-toggle="pill" data-bs-target="#limits" type="button" role="tab">
                                <i class="fas fa-sliders-h me-2 text-warning"></i>Resource Quotas
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="planTabsContent">
                        <!-- 1. Basic Info Tab -->
                        <div class="tab-pane fade show active" id="basic" role="tabpanel">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-dark">
                                        <i class="fas fa-id-card text-primary me-2"></i>Tier Identification &amp; Pricing
                                    </h6>
                                    <span class="badge bg-light text-muted border px-2 py-1 small">Step 1 of 3</span>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <!-- Name -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small text-uppercase text-muted">Plan Name <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-tag text-muted"></i></span>
                                                <input type="text" class="form-control form-control-lg border-start-0 ps-0 @error('name') is-invalid @enderror"
                                                    id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Barbershop Pro Elite" required>
                                            </div>
                                            @error('name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Slug -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small text-uppercase text-muted">Slug (URL Key) <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-link text-muted"></i></span>
                                                <input type="text" class="form-control form-control-lg border-start-0 ps-0 @error('slug') is-invalid @enderror"
                                                    id="slug" name="slug" value="{{ old('slug') }}" placeholder="barbershop-pro-elite" required>
                                            </div>
                                            <div class="form-text small text-muted">Auto-generated identifier for billing contracts</div>
                                            @error('slug')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Business Type -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small text-uppercase text-muted">Business Type <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-briefcase text-muted"></i></span>
                                                <select name="business_type" id="business_type" class="form-select form-select-lg border-start-0 ps-0 @error('business_type') is-invalid @enderror" required>
                                                    <option value="both" {{ old('business_type') == 'both' ? 'selected' : '' }}>Both (Salon &amp; Barbershop)</option>
                                                    <option value="salon" {{ old('business_type') == 'salon' ? 'selected' : '' }}>Salon Only</option>
                                                    <option value="barber" {{ old('business_type') == 'barber' ? 'selected' : '' }}>Barber Only</option>
                                                </select>
                                            </div>
                                            @error('business_type')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Price -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small text-uppercase text-muted">Price ({{ system_currency_symbol() }}) <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text bg-light fw-bold text-primary">{{ system_currency_symbol() }}</span>
                                                <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror"
                                                    id="price" name="price" value="{{ old('price', '49.00') }}" placeholder="49.00" required>
                                            </div>
                                            @error('price')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Duration Preset Pills & Input -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small text-uppercase text-muted">Duration (Days) <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-lg mb-2">
                                                <span class="input-group-text bg-light"><i class="fas fa-calendar-alt text-muted"></i></span>
                                                <input type="number" class="form-control @error('duration_in_days') is-invalid @enderror"
                                                    id="duration_in_days" name="duration_in_days" value="{{ old('duration_in_days', 30) }}" placeholder="30" required>
                                            </div>
                                            <div class="d-flex gap-1 flex-wrap">
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill duration-preset-btn" data-days="30">Monthly (30d)</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill duration-preset-btn" data-days="90">Quarterly (90d)</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill duration-preset-btn" data-days="180">Semi-Annual (180d)</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill duration-preset-btn" data-days="365">Annual (365d)</button>
                                            </div>
                                            @error('duration_in_days')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Trial Duration -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small text-uppercase text-muted">Trial Duration (Days)</label>
                                            <div class="input-group input-group-lg mb-2">
                                                <span class="input-group-text bg-light"><i class="fas fa-gift text-info"></i></span>
                                                <input type="number" class="form-control @error('trial_days') is-invalid @enderror"
                                                    id="trial_days" name="trial_days" value="{{ old('trial_days', 14) }}" placeholder="14">
                                            </div>
                                            <div class="d-flex gap-1 flex-wrap">
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill trial-preset-btn" data-days="0">No Trial (0d)</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill trial-preset-btn" data-days="7">7 Days</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill trial-preset-btn" data-days="14">14 Days</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill trial-preset-btn" data-days="30">30 Days</button>
                                            </div>
                                            @error('trial_days')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Description -->
                                        <div class="col-12">
                                            <label class="form-label fw-semibold small text-uppercase text-muted">Tier Description</label>
                                            <textarea class="form-control rounded-3" id="description" name="description" rows="3"
                                                placeholder="Highlight the key audience and business scope of this tier...">{{ old('description') }}</textarea>
                                        </div>

                                        <!-- Switches -->
                                        <div class="col-12 pt-2">
                                            <div class="p-3 rounded-3 bg-light border d-flex flex-wrap gap-4 align-items-center">
                                                <div class="form-check form-switch mb-0">
                                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                                                    <label class="form-check-label fw-semibold text-dark" for="is_active">
                                                        <i class="fas fa-check-circle text-success me-1"></i> Tier is Active
                                                    </label>
                                                </div>
                                                <div class="vr opacity-25 d-none d-sm-block"></div>
                                                <div class="form-check form-switch mb-0">
                                                    <input type="checkbox" class="form-check-input" id="is_popular" name="is_popular" value="1" {{ old('is_popular') ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-semibold text-dark" for="is_popular">
                                                        <i class="fas fa-star text-warning me-1"></i> Highlight as Most Popular
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Features Tab -->
                        <div class="tab-pane fade" id="features" role="tabpanel">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">
                                            <i class="fas fa-cubes text-info me-2"></i>Included Features &amp; Modules
                                        </h6>
                                        <small class="text-muted">Features control the modules salons are entitled to access</small>
                                    </div>
                                    <span class="badge bg-light text-muted border px-2 py-1 small">Step 2 of 3</span>
                                </div>
                                <div class="card-body p-4">
                                    <!-- Search & Add Bar -->
                                    <div class="position-relative mb-3">
                                        <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border">
                                            <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                                            <input type="text" class="form-control border-0 feature-input shadow-none ps-0" id="featureInput"
                                                placeholder="Search or type custom feature name..." autocomplete="off">
                                            <button type="button" class="btn btn-primary px-4 fw-semibold" id="btnAddFeature" onclick="addFeature()">
                                                <i class="fas fa-plus me-1"></i> Add Feature
                                            </button>
                                        </div>

                                        <!-- Suggestions Dropdown -->
                                        <div id="suggestions" class="list-group position-absolute w-100 shadow-lg rounded-3 border-0 mt-1"
                                            style="z-index: 1050; display: none; max-height: 280px; overflow-y: auto;">
                                        </div>
                                    </div>

                                    <!-- Quick Add Feature Pills -->
                                    <div class="p-3 rounded-3 bg-light border mb-4">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <small class="text-uppercase text-muted fw-bold" style="font-size: 0.68rem; letter-spacing: 0.05em;">
                                                <i class="fas fa-magic text-warning me-1"></i>Quick Add Popular Modules:
                                            </small>
                                            <span class="text-muted small">Click to toggle</span>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill fw-semibold quick-feature-btn" data-feature="AI Insights & Automation" onclick="quickAddFeature('AI Insights & Automation')">
                                                <i class="fas fa-brain text-warning me-1"></i>AI Insights &amp; Automation
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill quick-feature-btn" data-feature="Booking System" onclick="quickAddFeature('Booking System')">
                                                <i class="fas fa-calendar-alt text-danger me-1"></i>Booking System
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill quick-feature-btn" data-feature="POS System" onclick="quickAddFeature('POS System')">
                                                <i class="fas fa-cash-register text-success me-1"></i>POS System
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill quick-feature-btn" data-feature="Inventory Management" onclick="quickAddFeature('Inventory Management')">
                                                <i class="fas fa-boxes text-warning me-1"></i>Inventory Management
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill quick-feature-btn" data-feature="Staff Management" onclick="quickAddFeature('Staff Management')">
                                                <i class="fas fa-users text-primary me-1"></i>Staff Management
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill quick-feature-btn" data-feature="Customer Management" onclick="quickAddFeature('Customer Management')">
                                                <i class="fas fa-user-friends text-info me-1"></i>Customer Management
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill quick-feature-btn" data-feature="Analytics & Reports" onclick="quickAddFeature('Analytics & Reports')">
                                                <i class="fas fa-chart-line text-purple me-1"></i>Analytics &amp; Reports
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill fw-semibold text-dark quick-feature-btn" data-feature="Loyalty Program" onclick="quickAddFeature('Loyalty Program')">
                                                <i class="fas fa-coins text-warning me-1"></i>Loyalty Program
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill fw-semibold text-dark quick-feature-btn" data-feature="Customer Feedback & Reviews" onclick="quickAddFeature('Customer Feedback & Reviews')">
                                                <i class="fas fa-star text-warning me-1"></i>Feedback &amp; Reviews
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill fw-semibold quick-feature-btn" data-feature="Data Backup & Safety" onclick="quickAddFeature('Data Backup & Safety')">
                                                <i class="fas fa-database text-primary me-1"></i>Data Backup &amp; Safety
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Selected Features Section -->
                                    <div class="mb-2">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h6 class="fw-bold text-dark small mb-0 text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                                                Selected Features in this Plan:
                                            </h6>
                                            <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none p-0 small" onclick="clearAllFeatures()">
                                                <i class="fas fa-trash-alt me-1"></i> Clear All
                                            </button>
                                        </div>
                                        <input type="hidden" name="features" id="features-json">
                                        <div id="features-list" class="d-flex flex-wrap gap-2 p-3 rounded-3 bg-light border min-h-60"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Limits Tab -->
                        <div class="tab-pane fade" id="limits" role="tabpanel">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">
                                            <i class="fas fa-sliders-h text-warning me-2"></i>Resource Quotas &amp; Caps
                                        </h6>
                                        <small class="text-muted">Dynamic limits activate when their corresponding modules are enabled</small>
                                    </div>
                                    <span class="badge bg-light text-muted border px-2 py-1 small">Step 3 of 3</span>
                                </div>
                                <div class="card-body p-4">
                                    <div class="alert alert-info border-0 rounded-3 mb-4 d-flex align-items-center gap-2">
                                        <i class="fas fa-info-circle fa-lg"></i>
                                        <span class="small">Leave fields empty or set to <strong>0</strong> to grant <strong>unlimited access (∞)</strong> for that resource.</span>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-4" id="container_max_users">
                                            <div class="p-3 rounded-3 border bg-white h-100">
                                                <label class="form-label fw-semibold small text-uppercase text-muted">
                                                    <i class="fas fa-users text-primary me-2"></i>Max Staff
                                                </label>
                                                <input type="number" class="form-control form-control-lg" id="max_users" name="max_users"
                                                    value="{{ old('max_users') }}" placeholder="∞ (Unlimited)">
                                            </div>
                                        </div>

                                        <div class="col-md-4" id="container_max_branches">
                                            <div class="p-3 rounded-3 border bg-white h-100">
                                                <label class="form-label fw-semibold small text-uppercase text-muted">
                                                    <i class="fas fa-store text-info me-2"></i>Max Branches
                                                </label>
                                                <input type="number" class="form-control form-control-lg" id="max_branches" name="max_branches"
                                                    value="{{ old('max_branches') }}" placeholder="∞ (Unlimited)">
                                            </div>
                                        </div>

                                        <div class="col-md-4" id="container_max_customers">
                                            <div class="p-3 rounded-3 border bg-white h-100">
                                                <label class="form-label fw-semibold small text-uppercase text-muted">
                                                    <i class="fas fa-user-friends text-secondary me-2"></i>Max Clients
                                                </label>
                                                <input type="number" class="form-control form-control-lg" id="max_customers"
                                                    name="limits[max_customers]" value="{{ old('limits.max_customers') }}" placeholder="∞ (Unlimited)">
                                            </div>
                                        </div>

                                        <div class="col-md-4" id="container_max_services">
                                            <div class="p-3 rounded-3 border bg-white h-100">
                                                <label class="form-label fw-semibold small text-uppercase text-muted">
                                                    <i class="fas fa-cut text-warning me-2"></i>Max Services
                                                </label>
                                                <input type="number" class="form-control form-control-lg" id="max_services"
                                                    name="limits[max_services]" value="{{ old('limits.max_services') }}" placeholder="∞ (Unlimited)">
                                            </div>
                                        </div>

                                        <div class="col-md-4" id="container_max_products">
                                            <div class="p-3 rounded-3 border bg-white h-100">
                                                <label class="form-label fw-semibold small text-uppercase text-muted">
                                                    <i class="fas fa-box text-success me-2"></i>Max Products
                                                </label>
                                                <input type="number" class="form-control form-control-lg" id="max_products"
                                                    name="limits[max_products]" value="{{ old('limits.max_products') }}" placeholder="∞ (Unlimited)">
                                            </div>
                                        </div>

                                        <div class="col-md-4" id="container_max_packages">
                                            <div class="p-3 rounded-3 border bg-white h-100">
                                                <label class="form-label fw-semibold small text-uppercase text-muted">
                                                    <i class="fas fa-gift text-purple me-2"></i>Max Packages
                                                </label>
                                                <input type="number" class="form-control form-control-lg" id="max_packages"
                                                    name="limits[max_packages]" value="{{ old('limits.max_packages') }}" placeholder="∞ (Unlimited)">
                                            </div>
                                        </div>

                                        <div class="col-md-4" id="container_max_memberships">
                                            <div class="p-3 rounded-3 border bg-white h-100">
                                                <label class="form-label fw-semibold small text-uppercase text-muted">
                                                    <i class="fas fa-id-card text-teal me-2"></i>Max Memberships
                                                </label>
                                                <input type="number" class="form-control form-control-lg" id="max_memberships"
                                                    name="limits[max_memberships]" value="{{ old('limits.max_memberships') }}" placeholder="∞ (Unlimited)">
                                            </div>
                                        </div>

                                        <div class="col-md-4" id="container_max_bookings_per_month">
                                            <div class="p-3 rounded-3 border bg-white h-100">
                                                <label class="form-label fw-semibold small text-uppercase text-muted">
                                                    <i class="fas fa-calendar-check text-danger me-2"></i>Bookings / Month
                                                </label>
                                                <input type="number" class="form-control form-control-lg" id="max_bookings_per_month"
                                                    name="limits[max_bookings_per_month]" value="{{ old('limits.max_bookings_per_month') }}" placeholder="∞ (Unlimited)">
                                            </div>
                                        </div>

                                        <div class="col-md-4" id="container_max_guest_bookings_per_month">
                                            <div class="p-3 rounded-3 border bg-white h-100">
                                                <label class="form-label fw-semibold small text-uppercase text-muted">
                                                    <i class="fas fa-user-clock text-muted me-2"></i>Guest Bookings / Month
                                                </label>
                                                <input type="number" class="form-control form-control-lg" id="max_guest_bookings_per_month"
                                                    name="limits[max_guest_bookings_per_month]" value="{{ old('limits.max_guest_bookings_per_month') }}" placeholder="∞ (Unlimited)">
                                            </div>
                                        </div>

                                        <div class="col-md-4" id="container_max_backup_size_mb">
                                            <div class="p-3 rounded-3 border bg-white h-100">
                                                <label class="form-label fw-semibold small text-uppercase text-muted">
                                                    <i class="fas fa-database text-muted me-2"></i>Max Backup Size (MB)
                                                </label>
                                                <input type="number" class="form-control form-control-lg" id="max_backup_size_mb"
                                                    name="limits[max_backup_size_mb]" value="{{ old('limits.max_backup_size_mb') }}" placeholder="∞ (Unlimited, default 50 MB)">
                                                <div class="form-text x-small text-muted mt-1">Export/import limit per salon in Megabytes.</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4" id="container_max_backup_snapshots">
                                            <div class="p-3 rounded-3 border bg-white h-100">
                                                <label class="form-label fw-semibold small text-uppercase text-muted">
                                                    <i class="fas fa-history text-muted me-2"></i>Max Backup Snapshots
                                                </label>
                                                <input type="number" class="form-control form-control-lg" id="max_backup_snapshots"
                                                    name="limits[max_backup_snapshots]" value="{{ old('limits.max_backup_snapshots') }}" placeholder="∞ (Unlimited, default 30)">
                                                <div class="form-text x-small text-muted mt-1">Maximum stored snapshots allowed for salon.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Action Bar -->
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <a href="{{ route('admin.plans.index') }}" class="btn btn-light rounded-3 px-4">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary rounded-3 px-5 py-2 fw-semibold shadow-sm">
                                <i class="fas fa-save me-2"></i> Save &amp; Create Plan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar: Interactive Live Preview & Quick Add -->
                <div class="col-lg-4">
                    <!-- Live Interactive Card Preview -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden position-relative">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                            <span class="text-uppercase text-muted fw-bold small" style="font-size: 0.68rem; letter-spacing: 0.05em;">
                                <i class="fas fa-eye text-primary me-1"></i> Live Card Preview
                            </span>
                            <span class="badge bg-light text-muted border px-2 py-0" style="font-size: 0.65rem;">Client View</span>
                        </div>
                        <div class="card-body p-4 position-relative">
                            <!-- Popular Ribbon Preview -->
                            <div id="previewPopularRibbon" class="position-absolute top-0 end-0 bg-warning text-dark px-3 py-1 rounded-bottom-start-3 shadow-sm fw-bold small d-none"
                                 style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                <i class="fas fa-star text-dark me-1"></i> MOST POPULAR
                            </div>

                            <div class="text-center mb-3 pt-2">
                                <h4 class="fw-bold text-dark mb-1" id="previewPlanName">Plan Name Preview</h4>
                                <div class="text-muted x-small text-uppercase fw-semibold" id="previewBusinessType">Both (Salon &amp; Barber)</div>
                                
                                <div class="d-flex align-items-baseline justify-content-center text-primary mt-3">
                                    <span class="h4 mb-0 fw-bold me-1 text-primary">{{ system_currency_symbol() }}</span>
                                    <span class="display-6 fw-bold text-dark" id="previewPrice">49</span>
                                    <span class="text-muted ms-1 small" id="previewDuration">/ 30 days</span>
                                </div>
                            </div>

                            <hr class="opacity-10 my-3">

                            <div class="mb-3">
                                <h6 class="text-uppercase text-muted fw-bold small mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">Included Features:</h6>
                                <ul class="list-unstyled mb-0 small" id="previewFeaturesList">
                                    <li class="text-muted fst-italic small">No features added yet</li>
                                </ul>
                            </div>

                            <div class="p-2 rounded-3 bg-light border text-center small text-muted">
                                <i class="fas fa-shield-alt text-success me-1"></i> Tenant Data Scoped &amp; Encrypted
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Quick Add Features -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="fas fa-th-list text-primary me-2"></i>All Available Modules
                            </h6>
                            <span class="badge bg-light text-muted border px-2 py-0 small">19 Modules</span>
                        </div>
                        <div class="card-body p-3">
                            <p class="text-muted small mb-3">Click any module below to quickly toggle it into this tier:</p>
                            <div class="d-flex flex-wrap gap-2">
                                @php
                                    $allFeatures = [
                                        ['name' => 'POS System', 'module' => 'pos', 'icon' => 'fa-cash-register'],
                                        ['name' => 'Inventory Management', 'module' => 'inventory', 'icon' => 'fa-boxes'],
                                        ['name' => 'Staff Management', 'module' => 'staff', 'icon' => 'fa-users-cog'],
                                        ['name' => 'Customer Management', 'module' => 'customers', 'icon' => 'fa-user-friends'],
                                        ['name' => 'Booking System', 'module' => 'appointments', 'icon' => 'fa-calendar-alt'],
                                        ['name' => 'Service Management', 'module' => 'services', 'icon' => 'fa-cut'],
                                        ['name' => 'Analytics & Reports', 'module' => 'reports', 'icon' => 'fa-chart-line'],
                                        ['name' => 'Multi-Branch Support', 'module' => 'branches', 'icon' => 'fa-code-branch'],
                                        ['name' => 'Packages', 'module' => 'packages', 'icon' => 'fa-gift'],
                                        ['name' => 'Memberships', 'module' => 'memberships', 'icon' => 'fa-id-card'],
                                        ['name' => 'Commission Management', 'module' => 'commissions', 'icon' => 'fa-percent'],
                                        ['name' => 'Role Management', 'module' => 'roles', 'icon' => 'fa-user-shield'],
                                        ['name' => 'SMS Notifications', 'module' => 'sms', 'icon' => 'fa-sms'],
                                        ['name' => 'WhatsApp Notifications', 'module' => 'whatsapp', 'icon' => 'fa-whatsapp'],
                                        ['name' => 'Email Notifications', 'module' => 'marketing', 'icon' => 'fa-envelope'],
                                        ['name' => 'AI Insights & Automation', 'module' => 'ai', 'icon' => 'fa-brain'],
                                        ['name' => 'Loyalty Program', 'module' => 'loyalty', 'icon' => 'fa-coins'],
                                        ['name' => 'Customer Feedback & Reviews', 'module' => 'feedback', 'icon' => 'fa-star'],
                                        ['name' => 'Data Backup & Safety', 'module' => 'data_backup', 'icon' => 'fa-database'],
                                    ];
                                @endphp

                                @foreach ($allFeatures as $feature)
                                    @if (!$feature['module'] || empty($enabledModules) || !empty($enabledModules[$feature['module']]))
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill quick-feature-btn"
                                            data-feature="{{ $feature['name'] }}"
                                            onclick="quickAddFeature('{{ $feature['name'] }}')">
                                            <i class="fas {{ $feature['icon'] }} me-1"></i>{{ $feature['name'] }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            let features = [];
            try {
                const oldFeatures = @json(old('features') ? (is_string(old('features')) ? json_decode(old('features'), true) : old('features')) : []);
                if (typeof oldFeatures === 'string') {
                    features = JSON.parse(oldFeatures);
                } else if (Array.isArray(oldFeatures)) {
                    features = oldFeatures;
                } else if (typeof oldFeatures === 'object' && oldFeatures !== null) {
                    features = Object.values(oldFeatures);
                }
                if (!Array.isArray(features)) {
                    features = [];
                }
            } catch (e) {
                features = [];
            }

            const allFeatures = [
                { name: 'POS System', module: 'pos' },
                { name: 'Inventory Management', module: 'inventory' },
                { name: 'Staff Management', module: 'staff' },
                { name: 'Customer Management', module: 'customers' },
                { name: 'Booking System', module: 'appointments' },
                { name: 'Service Management', module: 'services' },
                { name: 'Analytics & Reports', module: 'reports' },
                { name: 'Multi-Branch Support', module: 'branches' },
                { name: 'Packages', module: 'packages' },
                { name: 'Memberships', module: 'memberships' },
                { name: 'Commission Management', module: 'commissions' },
                { name: 'Role Management', module: 'roles' },
                { name: 'Email Notifications', module: 'marketing' },
                { name: 'SMS Notifications', module: 'sms' },
                { name: 'WhatsApp Notifications', module: 'whatsapp' },
                { name: 'AI Insights & Automation', module: 'ai' },
                { name: 'Loyalty Program', module: 'loyalty' },
                { name: 'Customer Feedback & Reviews', module: 'feedback' },
                { name: 'Data Backup & Safety', module: 'data_backup' }
            ];

            const enabledModules = @json($enabledModules ?? []);

            const commonFeatures = allFeatures.filter(feature => {
                if (!feature.module) return true;
                if (Array.isArray(enabledModules) && enabledModules.length === 0) return true;
                return enabledModules[feature.module] === true || enabledModules[feature.module] === '1' || enabledModules[feature.module] === 1;
            }).map(f => f.name);

            const featureLimits = {
                'Staff Management': ['max_users'],
                'Multi-Branch Support': ['max_branches'],
                'Customer Management': ['max_customers'],
                'Service Management': ['max_services'],
                'Inventory Management': ['max_products'],
                'Packages': ['max_packages'],
                'Memberships': ['max_memberships'],
                'Booking System': ['max_bookings_per_month', 'max_guest_bookings_per_month'],
                'Data Backup & Safety': ['max_backup_size_mb', 'max_backup_snapshots'],
                'data_backup': ['max_backup_size_mb', 'max_backup_snapshots'],
                'Data Backup': ['max_backup_size_mb', 'max_backup_snapshots'],
                'Backup System': ['max_backup_size_mb', 'max_backup_snapshots'],
                'salon_backup': ['max_backup_size_mb', 'max_backup_snapshots'],
                'backup': ['max_backup_size_mb', 'max_backup_snapshots']
            };

            // Auto-generate slug from name
            document.getElementById('name').addEventListener('input', function () {
                const slug = this.value.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                document.getElementById('slug').value = slug;
                updateLivePreview();
            });

            // Live preview bindings
            ['price', 'duration_in_days', 'business_type', 'is_popular'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', updateLivePreview);
                    el.addEventListener('change', updateLivePreview);
                }
            });

            // Duration Presets
            document.querySelectorAll('.duration-preset-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.getElementById('duration_in_days').value = this.dataset.days;
                    updateLivePreview();
                });
            });

            // Trial Presets
            document.querySelectorAll('.trial-preset-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.getElementById('trial_days').value = this.dataset.days;
                });
            });

            function updateLivePreview() {
                const name = document.getElementById('name').value.trim() || 'Plan Name Preview';
                const price = document.getElementById('price').value.trim() || '0';
                const duration = document.getElementById('duration_in_days').value.trim() || '30';
                const bType = document.getElementById('business_type').value;
                const isPopular = document.getElementById('is_popular').checked;

                document.getElementById('previewPlanName').textContent = name;
                document.getElementById('previewPrice').textContent = parseFloat(price).toFixed(0);
                document.getElementById('previewDuration').textContent = `/ ${duration} days`;

                const typeLabels = { 'both': 'Both (Salon & Barber)', 'salon': 'Salon Only', 'barber': 'Barber Only' };
                document.getElementById('previewBusinessType').textContent = typeLabels[bType] || bType;

                const popularRibbon = document.getElementById('previewPopularRibbon');
                if (popularRibbon) {
                    popularRibbon.classList.toggle('d-none', !isPopular);
                }

                // Update Preview Features
                const previewList = document.getElementById('previewFeaturesList');
                if (previewList) {
                    if (features.length === 0) {
                        previewList.innerHTML = '<li class="text-muted fst-italic small">No features added yet</li>';
                    } else {
                        const topFeats = features.slice(0, 5);
                        let html = topFeats.map(f => `<li class="d-flex align-items-center mb-1 text-dark"><i class="fas fa-check-circle text-success me-2"></i><span>${f}</span></li>`).join('');
                        if (features.length > 5) {
                            html += `<li class="text-muted fst-italic small ps-4">+ ${features.length - 5} more features...</li>`;
                        }
                        previewList.innerHTML = html;
                    }
                }
            }

            // Autocomplete functionality
            const featureInput = document.getElementById('featureInput');
            const suggestionsDiv = document.getElementById('suggestions');

            featureInput.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                const addBtn = document.getElementById('btnAddFeature');

                if (query.length > 0 && features.map(f => f.toLowerCase()).includes(query)) {
                    if (addBtn) {
                        addBtn.disabled = true;
                        addBtn.classList.add('disabled');
                        addBtn.setAttribute('title', 'Feature already added');
                    }
                } else if (addBtn) {
                    addBtn.disabled = false;
                    addBtn.classList.remove('disabled');
                    addBtn.removeAttribute('title');
                }

                if (query.length === 0) {
                    suggestionsDiv.style.display = 'none';
                    return;
                }

                const matches = commonFeatures.filter(feature =>
                    feature.toLowerCase().includes(query) && !features.includes(feature)
                );

                if (matches.length === 0) {
                    suggestionsDiv.style.display = 'none';
                    return;
                }

                suggestionsDiv.innerHTML = matches.map(feature => `
                    <button type="button" class="list-group-item list-group-item-action d-flex align-items-center py-2" onclick="selectSuggestion('${feature}')">
                        <i class="fas fa-plus-circle text-primary me-2"></i>
                        <span>${feature}</span>
                    </button>
                `).join('');

                suggestionsDiv.style.display = 'block';
            });

            document.addEventListener('click', function (e) {
                if (!featureInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                    suggestionsDiv.style.display = 'none';
                }
            });

            function selectSuggestion(feature) {
                featureInput.value = feature;
                suggestionsDiv.style.display = 'none';
                addFeature();
            }

            function addFeature() {
                const input = document.querySelector('.feature-input');
                const feature = input.value.trim();

                if (feature && !features.includes(feature)) {
                    features.push(feature);
                    updateFeaturesList();
                    input.value = '';
                    suggestionsDiv.style.display = 'none';
                    const addBtn = document.getElementById('btnAddFeature');
                    if (addBtn) {
                        addBtn.disabled = false;
                        addBtn.classList.remove('disabled');
                        addBtn.removeAttribute('title');
                    }
                }
            }

            function quickAddFeature(feature) {
                if (!features.includes(feature)) {
                    features.push(feature);
                    updateFeaturesList();
                }
            }

            function removeFeature(index) {
                features.splice(index, 1);
                updateFeaturesList();
            }

            function clearAllFeatures() {
                if (features.length === 0) return;
                if (confirm('Clear all features from this plan?')) {
                    features = [];
                    updateFeaturesList();
                }
            }

            function toggleLimitFields() {
                Object.values(featureLimits).flat().forEach(id => {
                    const container = document.getElementById('container_' + id);
                    if (container) {
                        container.style.display = 'none';
                        const input = document.getElementById(id);
                        if (input) input.value = '';
                    }
                });

                features.forEach(feature => {
                    if (featureLimits[feature]) {
                        featureLimits[feature].forEach(id => {
                            const container = document.getElementById('container_' + id);
                            if (container) {
                                container.style.display = 'block';
                            }
                        });
                    }
                });
            }

            function syncFeatureButtons() {
                document.querySelectorAll('.quick-feature-btn').forEach(btn => {
                    const feat = btn.getAttribute('data-feature');
                    if (!feat) return;

                    if (!btn.dataset.originalContent) {
                        btn.dataset.originalContent = btn.innerHTML;
                    }

                    if (features.includes(feat)) {
                        btn.disabled = true;
                        btn.classList.add('disabled');
                        btn.setAttribute('title', 'Already added to plan');
                        btn.innerHTML = `<i class="fas fa-check-circle text-success me-1"></i>${feat} <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 ms-1" style="font-size: 0.65rem;">Added</span>`;
                    } else {
                        btn.disabled = false;
                        btn.classList.remove('disabled');
                        btn.removeAttribute('title');
                        if (btn.dataset.originalContent) {
                            btn.innerHTML = btn.dataset.originalContent;
                        }
                    }
                });
            }

            function updateFeaturesList() {
                const container = document.getElementById('features-list');
                const countBadge = document.getElementById('features-tab-count');

                if (countBadge) {
                    countBadge.textContent = features.length;
                }

                if (features.length === 0) {
                    container.innerHTML = '<p class="text-muted small mb-0 py-2"><i class="fas fa-info-circle me-2"></i>No features added yet. Use the search bar or click quick-add buttons above.</p>';
                } else {
                    container.innerHTML = features.map((feature, index) => `
                        <span class="badge bg-white text-dark border px-3 py-2 fs-6 d-inline-flex align-items-center mb-1 me-1 shadow-sm rounded-pill">
                            <i class="fas fa-check-circle text-success me-2"></i>${feature}
                            <button type="button" class="btn btn-link text-danger p-0 ms-2" onclick="removeFeature(${index})" title="Remove feature" style="text-decoration: none;">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </span>
                    `).join('');
                }

                document.getElementById('features-json').value = JSON.stringify(features);
                toggleLimitFields();
                syncFeatureButtons();
                updateLivePreview();
            }

            document.addEventListener('DOMContentLoaded', function () {
                document.querySelector('.feature-input').addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        addFeature();
                    }
                });
                updateFeaturesList();
                updateLivePreview();
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .quick-feature-btn.disabled,
            .quick-feature-btn:disabled {
                opacity: 0.65 !important;
                cursor: not-allowed !important;
                pointer-events: auto !important;
                border-style: dashed !important;
                background-color: #f1f5f9 !important;
                color: #475569 !important;
                border-color: #cbd5e1 !important;
            }

            .btn-xs {
                font-size: 0.72rem;
                padding: 0.25rem 0.6rem;
            }

            .min-h-60 {
                min-height: 60px;
            }

            .text-purple {
                color: #6f42c1;
            }

            .text-teal {
                color: #20c997;
            }

            .nav-pills .nav-link {
                color: #64748b;
                border: 1px solid transparent;
            }

            .nav-pills .nav-link.active {
                background-color: #f8fafc;
                color: #0d6efd;
                border-color: #e2e8f0;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }
        </style>
    @endpush
@endsection