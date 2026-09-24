@extends('layouts.app')

@section('title', 'Package Details - ' . $package->name)

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
                    <li class="breadcrumb-item"><a href="{{ route('admin.packages.index') }}" class="text-decoration-none text-muted">Packages</a></li>
                    <li class="breadcrumb-item active text-primary" aria-current="page">{{ $package->name }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-3">
                <h1 class="h4 mb-0 fw-bold text-dark">{{ $package->name }}</h1>
                <span class="badge bg-{{ $package->is_active ? 'success' : 'secondary' }}-subtle text-{{ $package->is_active ? 'success' : 'secondary' }} border border-{{ $package->is_active ? 'success' : 'secondary' }}-subtle px-3 py-1 rounded-pill small">
                    <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                    {{ $package->is_active ? 'Active' : 'Inactive' }}
                </span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill small text-capitalize">
                    <i class="fas fa-{{ $package->type == 'fixed' ? 'lock' : 'sliders-h' }} me-1"></i>
                    {{ $package->type }} Package
                </span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-primary px-3 shadow-sm">
                <i class="fas fa-edit me-1"></i> Edit Package
            </a>
            <a href="{{ route('admin.packages.index') }}" class="btn btn-light border px-3">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Description Notice (if present) -->
    @if($package->description)
        <div class="card card-compact bg-light-subtle mb-4">
            <div class="card-body py-3 px-4 d-flex align-items-center">
                <i class="fas fa-info-circle text-primary me-3 fa-lg"></i>
                <div class="text-dark small">{{ $package->description }}</div>
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
                        <div class="stat-label text-muted">Selling Price</div>
                        <div class="stat-value text-dark">
                            {{ format_currency($package->effective_price ?? $package->price) }}
                        </div>
                        @if($package->special_price && $package->special_price < $package->price)
                            <small class="text-success fw-medium">
                                <i class="fas fa-arrow-down me-1"></i>Save {{ format_currency($package->price - $package->special_price) }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card card-compact h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="icon-shape bg-success-subtle text-success me-3 flex-shrink-0">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                    <div>
                        <div class="stat-label text-muted">Validity</div>
                        <div class="stat-value text-dark">
                            {{ $package->validity_value }} {{ ucfirst($package->validity_unit) }}
                        </div>
                        <small class="text-muted">Duration</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card card-compact h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="icon-shape bg-info-subtle text-info me-3 flex-shrink-0">
                        <i class="fas fa-percent fa-lg"></i>
                    </div>
                    <div>
                        <div class="stat-label text-muted">Tax Rate</div>
                        <div class="stat-value text-dark">{{ $package->tax_rate }}%</div>
                        <small class="text-muted">Applied on sale</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card card-compact h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="icon-shape me-3 flex-shrink-0" style="background: rgba(111, 66, 193, 0.1); color: #6f42c1;">
                        <i class="fas fa-layer-group fa-lg"></i>
                    </div>
                    <div>
                        <div class="stat-label text-muted">Included Items</div>
                        <div class="stat-value text-dark">{{ $package->services->count() }} Services</div>
                        <small class="text-muted">
                            Limit: {{ $package->service_limit ?? 'Unlimited' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4">
        <!-- Included Services Section -->
        <div class="col-lg-8">
            <div class="card card-compact mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-concierge-bell text-primary"></i>
                        Included Services
                    </h2>
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">{{ $package->services->count() }} Total</span>
                </div>
                <div class="card-body p-0">
                    @if($package->services->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light-subtle text-secondary small text-uppercase fw-bold">
                                    <tr>
                                        <th class="ps-4 py-3">Service Name</th>
                                        <th class="text-center py-3">Qty</th>
                                        <th class="text-end py-3">Unit Price</th>
                                        <th class="text-end pe-4 py-3">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalOriginalValue = 0; @endphp
                                    @foreach($package->services as $service)
                                        @php
                                            $qty = $service->pivot->quantity ?? 1;
                                            $subtotal = $service->price * $qty;
                                            $totalOriginalValue += $subtotal;
                                        @endphp
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark">{{ $service->name }}</div>
                                                @if($service->description)
                                                    <div class="small text-muted text-truncate" style="max-width: 350px;">{{ $service->description }}</div>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded">
                                                    {{ $qty }}x
                                                </span>
                                            </td>
                                            <td class="text-end text-muted">{{ format_currency($service->price) }}</td>
                                            <td class="text-end pe-4 fw-bold text-dark">{{ format_currency($subtotal) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light-subtle border-top">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold text-muted small text-uppercase">Total Original Value:</td>
                                        <td class="text-end pe-4 fw-bold text-dark">{{ format_currency($totalOriginalValue) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold text-primary small text-uppercase">Package Selling Price:</td>
                                        <td class="text-end pe-4 fw-bold text-primary fs-6">
                                            {{ format_currency($package->effective_price ?? $package->price) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-inbox fa-2x mb-2 text-light"></i>
                            <p class="mb-0 small">No services associated with this package.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Validity Description / Special Rules Card -->
            @if($package->validity_description)
                <div class="card card-compact mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h2 class="h6 mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="fas fa-clock text-info"></i>
                            Validity Terms & Instructions
                        </h2>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-muted small leading-relaxed">{{ $package->validity_description }}</div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Section -->
        <div class="col-lg-4">
            <!-- Package Summary Card -->
            <div class="card card-compact mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h2 class="h6 mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-chart-pie text-success"></i>
                        Package Value Breakdown
                    </h2>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted small">Included Services Count</span>
                        <span class="fw-bold text-dark">{{ $package->services->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted small">Total Sessions / Items</span>
                        <span class="fw-bold text-dark">{{ $package->services->sum('pivot.quantity') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted small">Base Value</span>
                        <span class="fw-bold text-dark">{{ format_currency($totalOriginalValue ?? $package->price) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Package Price</span>
                        <span class="fw-bold text-primary fs-6">{{ format_currency($package->effective_price ?? $package->price) }}</span>
                    </div>
                </div>
            </div>

            <!-- Package Metadata & Audit -->
            <div class="card card-compact mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h2 class="h6 mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-history text-secondary"></i>
                        Package Info & History
                    </h2>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted small">Package ID</span>
                        <span class="font-monospace small text-muted">#{{ $package->id }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted small">Created Date</span>
                        <span class="small fw-medium text-dark">{{ $package->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Last Updated</span>
                        <span class="small fw-medium text-dark">{{ $package->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card card-compact">
                <div class="card-body p-4">
                    <h2 class="h6 mb-3 fw-bold text-dark">Quick Actions</h2>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-primary shadow-sm">
                            <i class="fas fa-edit me-1"></i> Edit Package
                        </a>
                        <a href="{{ route('admin.packages.create') }}" class="btn btn-outline-success">
                            <i class="fas fa-plus me-1"></i> Create New Package
                        </a>
                        <a href="{{ route('admin.packages.index') }}" class="btn btn-light border text-muted">
                            <i class="fas fa-list me-1"></i> View All Packages
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection