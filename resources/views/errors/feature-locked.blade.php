@extends('layouts.app')

@section('title', 'Feature Locked - ' . ($feature ?? 'Upgrade Required'))

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden text-center">
                <!-- Header Banner -->
                <div class="p-5 text-white position-relative" style="background: linear-gradient(135deg, #0b0f17 0%, #1e293b 100%); border-bottom: 2px solid rgba(197, 168, 128, 0.4);">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow"
                         style="width: 80px; height: 80px; background: rgba(197, 168, 128, 0.15); border: 2px solid #c5a880;">
                        <i class="fas fa-lock fa-2x" style="color: #c5a880;"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-white">Upgrade Required</h3>
                    <p class="text-white-50 mb-0">The <strong>{{ $feature ?? 'requested feature' }}</strong> is not included in your current subscription plan.</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if(isset($currentPlan))
                        <div class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill mb-4" style="font-size: 0.85rem;">
                            Current Active Plan: <strong class="text-dark">{{ $currentPlan->name ?? 'Basic' }}</strong>
                        </div>
                    @endif

                    @if(($feature ?? '') === 'Inventory Management' || ($feature ?? '') === 'Inventory')
                        <div class="card bg-light border-0 rounded-3 p-4 mb-4 text-start">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-boxes text-primary me-2"></i>What you'll unlock with Enterprise Inventory:
                            </h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2 d-flex align-items-start">
                                    <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                    <span><strong>Consumables & Backbar Recipes:</strong> Define exact product bill-of-materials per service to calculate accurate recipe COGS.</span>
                                </li>
                                <li class="mb-2 d-flex align-items-start">
                                    <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                    <span><strong>Automated POS Stock Deductions:</strong> Automatically dispense and log backbar materials when services are checked out.</span>
                                </li>
                                <li class="mb-2 d-flex align-items-start">
                                    <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                    <span><strong>Wastage & Spillage Audits:</strong> Track bottle breakages, color tube over-dispensing, and damaged inventory.</span>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                    <span><strong>Service Profitability Reports:</strong> Compare real gross margins and analyze true service profitability.</span>
                                </li>
                            </ul>
                        </div>
                    @else
                        <div class="card bg-light border-0 rounded-3 p-4 mb-4 text-start">
                            <h6 class="fw-bold text-dark mb-2">
                                <i class="fas fa-star text-warning me-2"></i>Unlock Advanced Salon Capabilities
                            </h6>
                            <p class="text-muted small mb-0">
                                Upgrade to our higher tier plans to access this advanced feature, automate your salon operations, and maximize revenue.
                            </p>
                        </div>
                    @endif

                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                        @php
                            $salonSlug = $salon?->slug ?? request()->route('salon_slug') ?? (auth()->user()?->salon?->slug);
                        @endphp
                        @if($salonSlug)
                            <a href="{{ route('admin.saas.subscription.index', ['salon_slug' => $salonSlug]) }}" 
                               class="btn btn-primary px-4 py-2.5 rounded-pill fw-semibold shadow-sm">
                                <i class="fas fa-arrow-circle-up me-2"></i> Upgrade Subscription
                            </a>
                        @endif
                        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard', ['salon_slug' => $salonSlug ?? '']) }}" 
                           class="btn btn-outline-secondary px-4 py-2.5 rounded-pill fw-semibold">
                            <i class="fas fa-arrow-left me-2"></i> Return Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
