@extends('layouts.app')

@section('title', 'Membership Details - ' . $membership->name)

@push('styles')
<style>
    .card-compact {
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }
    .icon-shape {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .stat-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-value {
        font-size: 1.35rem;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 px-4">
    <!-- Breadcrumbs & Action Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small text-muted">
                    <li class="breadcrumb-item"><a href="{{ route('admin.memberships.index') }}" class="text-decoration-none text-muted">Memberships</a></li>
                    <li class="breadcrumb-item active text-primary" aria-current="page">{{ $membership->name }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-3">
                <h1 class="h4 mb-0 fw-bold text-dark">{{ $membership->name }}</h1>
                <span class="badge bg-{{ $membership->is_active ? 'success' : 'secondary' }}-subtle text-{{ $membership->is_active ? 'success' : 'secondary' }} border border-{{ $membership->is_active ? 'success' : 'secondary' }}-subtle px-3 py-1 rounded-pill small">
                    <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                    {{ $membership->is_active ? 'Active' : 'Inactive' }}
                </span>
                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 rounded-pill small text-capitalize">
                    <i class="fas fa-id-card me-1"></i>
                    {{ ucfirst($membership->membership_type ?? 'All') }} Items
                </span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.memberships.edit', $membership) }}" class="btn btn-primary px-3 shadow-sm">
                <i class="fas fa-edit me-1"></i> Edit Membership
            </a>
            <a href="{{ route('admin.memberships.index') }}" class="btn btn-light border px-3">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Description Notice (if present) -->
    @if($membership->description)
        <div class="card card-compact bg-light-subtle mb-4">
            <div class="card-body py-3 px-4 d-flex align-items-center">
                <i class="fas fa-info-circle text-primary me-3 fa-lg"></i>
                <div class="text-dark small">{{ $membership->description }}</div>
            </div>
        </div>
    @endif

    <!-- Key Metrics Grid -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card card-compact h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="icon-shape bg-primary-subtle text-primary me-3 flex-shrink-0">
                        <i class="fas fa-tag fa-lg"></i>
                    </div>
                    <div>
                        <div class="stat-label text-muted">Discount Value</div>
                        <div class="stat-value text-dark">
                            {{ format_currency($membership->discount_value) }}
                        </div>
                        <small class="text-muted">Tier Discount</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card card-compact h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="icon-shape bg-success-subtle text-success me-3 flex-shrink-0">
                        <i class="fas fa-calendar-alt fa-lg"></i>
                    </div>
                    <div>
                        <div class="stat-label text-muted">Validity</div>
                        <div class="stat-value text-dark">
                            {{ $membership->formatted_validity ?? ($membership->validity_value . ' ' . ucfirst($membership->validity_unit ?? 'Days')) }}
                        </div>
                        <small class="text-muted">Membership Duration</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card card-compact h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="icon-shape bg-info-subtle text-info me-3 flex-shrink-0">
                        <i class="fas fa-calculator fa-lg"></i>
                    </div>
                    <div>
                        <div class="stat-label text-muted">Taxable</div>
                        <div class="stat-value text-dark">{{ $membership->is_taxable ? 'Yes' : 'No' }}</div>
                        <small class="text-muted">Subject to tax</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card card-compact h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="icon-shape me-3 flex-shrink-0" style="background: rgba(111, 66, 193, 0.1); color: #6f42c1;">
                        <i class="fas fa-box-open fa-lg"></i>
                    </div>
                    <div>
                        <div class="stat-label text-muted">Total Benefit Price</div>
                        <div class="stat-value text-dark">
                            {{ format_currency($membership->calculateTotalMembershipPrice()) }}
                        </div>
                        <small class="text-muted">
                            {{ $membership->services->count() + $membership->inventoryItems->count() }} Included Items
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4">
        <!-- Included Items Section -->
        <div class="col-lg-8">
            <div class="card card-compact mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-list-check text-primary"></i>
                        Included Services & Products
                    </h2>
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">
                        {{ $membership->services->count() + $membership->inventoryItems->count() }} Total
                    </span>
                </div>
                <div class="card-body p-0">
                    @if(($membership->services->count() + $membership->inventoryItems->count()) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light-subtle text-secondary small text-uppercase fw-bold">
                                    <tr>
                                        <th class="ps-4 py-3">Item Name</th>
                                        <th class="py-3">Type</th>
                                        <th class="text-center py-3">Qty</th>
                                        <th class="text-center py-3">Discount</th>
                                        <th class="text-end py-3">Original Price</th>
                                        <th class="text-end pe-4 py-3">Member Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalOriginalPrice = 0;
                                        $totalMemberPrice = 0;
                                    @endphp

                                    {{-- Included Services --}}
                                    @foreach($membership->services as $service)
                                        @php
                                            $qty = $service->pivot->quantity ?? 1;
                                            $origPrice = ($service->price ?? 0) * $qty;
                                            $memPrice = ($service->pivot->membership_price ?? 0) * $qty;
                                            $totalOriginalPrice += $origPrice;
                                            $totalMemberPrice += $memPrice;
                                        @endphp
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark">{{ $service->name }}</div>
                                                @if($service->description)
                                                    <div class="small text-muted text-truncate" style="max-width: 300px;">{{ $service->description }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded small">
                                                    <i class="fas fa-concierge-bell me-1"></i> Service
                                                </span>
                                            </td>
                                            <td class="text-center fw-medium">{{ $qty }}x</td>
                                            <td class="text-center small">
                                                @if($service->pivot->discount_type === 'percent')
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded">{{ $service->pivot->discount_value }}% OFF</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded">{{ format_currency($service->pivot->discount_value) }} OFF</span>
                                                @endif
                                            </td>
                                            <td class="text-end text-muted">{{ format_currency($origPrice) }}</td>
                                            <td class="text-end pe-4 fw-bold text-primary">{{ format_currency($memPrice) }}</td>
                                        </tr>
                                    @endforeach

                                    {{-- Included Inventory Items / Products --}}
                                    @foreach($membership->inventoryItems as $item)
                                        @php
                                            $qty = $item->pivot->quantity ?? 1;
                                            $origPrice = ($item->selling_price ?? 0) * $qty;
                                            $memPrice = ($item->pivot->membership_price ?? 0) * $qty;
                                            $totalOriginalPrice += $origPrice;
                                            $totalMemberPrice += $memPrice;
                                        @endphp
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark">{{ $item->name }}</div>
                                                @if($item->sku)
                                                    <div class="small text-muted font-monospace">SKU: {{ $item->sku }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-purple-subtle text-purple border border-purple-subtle px-2 py-1 rounded small" style="background: rgba(111, 66, 193, 0.1); color: #6f42c1; border-color: rgba(111, 66, 193, 0.2)!important;">
                                                    <i class="fas fa-box me-1"></i> Product
                                                </span>
                                            </td>
                                            <td class="text-center fw-medium">{{ $qty }}x</td>
                                            <td class="text-center small">
                                                @if($item->pivot->discount_type === 'percent')
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded">{{ $item->pivot->discount_value }}% OFF</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded">{{ format_currency($item->pivot->discount_value) }} OFF</span>
                                                @endif
                                            </td>
                                            <td class="text-end text-muted">{{ format_currency($origPrice) }}</td>
                                            <td class="text-end pe-4 fw-bold text-primary">{{ format_currency($memPrice) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light-subtle border-top">
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold text-muted small text-uppercase">Original Value:</td>
                                        <td class="text-end fw-bold text-muted">{{ format_currency($totalOriginalPrice) }}</td>
                                        <td class="text-end pe-4 fw-bold text-primary fs-6">{{ format_currency($totalMemberPrice) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-inbox fa-2x mb-2 text-light"></i>
                            <p class="mb-0 small">No services or products associated with this membership.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Section -->
        <div class="col-lg-4">
            <!-- Membership Summary Card -->
            <div class="card card-compact mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h2 class="h6 mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-chart-pie text-success"></i>
                        Membership Summary
                    </h2>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted small">Services Count</span>
                        <span class="fw-bold text-dark">{{ $membership->services->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted small">Products Count</span>
                        <span class="fw-bold text-dark">{{ $membership->inventoryItems->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted small">Original Market Value</span>
                        <span class="fw-bold text-dark">{{ format_currency($totalOriginalPrice ?? $membership->calculateTotalValue()) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Total Member Price</span>
                        <span class="fw-bold text-primary fs-6">{{ format_currency($totalMemberPrice ?? $membership->calculateTotalMembershipPrice()) }}</span>
                    </div>
                </div>
            </div>

            <!-- Membership Info & History -->
            <div class="card card-compact mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h2 class="h6 mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-history text-secondary"></i>
                        Membership Info & History
                    </h2>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted small">Membership ID</span>
                        <span class="font-monospace small text-muted">#{{ $membership->id }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted small">Created Date</span>
                        <span class="small fw-medium text-dark">{{ $membership->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Last Updated</span>
                        <span class="small fw-medium text-dark">{{ $membership->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card card-compact">
                <div class="card-body p-4">
                    <h2 class="h6 mb-3 fw-bold text-dark">Quick Actions</h2>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.memberships.edit', $membership) }}" class="btn btn-primary shadow-sm">
                            <i class="fas fa-edit me-1"></i> Edit Membership
                        </a>
                        <a href="{{ route('admin.memberships.create') }}" class="btn btn-outline-success">
                            <i class="fas fa-plus me-1"></i> Create New Membership
                        </a>
                        <a href="{{ route('admin.memberships.index') }}" class="btn btn-light border text-muted">
                            <i class="fas fa-list me-1"></i> View All Memberships
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection