@extends('layouts.super-admin')

@section('content')
    <div class="container-fluid py-4 px-3 px-md-4">
        <!-- Header & Action -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        <i class="fas fa-layer-group me-1"></i> SaaS Tiers
                    </span>
                    <span class="text-muted small">Subscription Management</span>
                </div>
                <h1 class="h3 fw-bold text-gray-900 mb-0">Subscription Plans</h1>
                <p class="text-muted small mb-0">Define billing frequencies, feature entitlements, and quota boundaries for salons.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.modules.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm rounded-3">
                    <i class="fas fa-cubes me-1"></i> Global Modules
                </a>
                <a href="{{ route('admin.plans.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm rounded-3 d-flex align-items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span class="fw-semibold">Create New Plan</span>
                </a>
            </div>
        </div>

        @if($plans->count() > 0)
            <!-- KPI Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary me-3">
                                <i class="fas fa-tags fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;">Total Tiers</div>
                                <h4 class="fw-bold mb-0 text-dark">{{ $plans->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="p-3 rounded-3 bg-success bg-opacity-10 text-success me-3">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;">Active Tiers</div>
                                <h4 class="fw-bold mb-0 text-dark">{{ $plans->where('is_active', true)->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="p-3 rounded-3 bg-info bg-opacity-10 text-info me-3">
                                <i class="fas fa-store fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;">Active Subscriptions</div>
                                <h4 class="fw-bold mb-0 text-dark">{{ $plans->sum('subscriptions_count') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="p-3 rounded-3 bg-warning bg-opacity-10 text-warning me-3">
                                <i class="fas fa-star fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;">Popular Tier</div>
                                <h5 class="fw-bold mb-0 text-dark text-truncate" style="max-width: 140px;">
                                    {{ optional($plans->where('is_popular', true)->first())->name ?? 'None' }}
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Toolbar -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-body p-3">
                    <div class="row g-2 align-items-center justify-content-between">
                        <div class="col-md-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-0 ps-3">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" id="planSearchInput" class="form-control bg-light border-0 shadow-none" 
                                    placeholder="Filter by plan name, features, or description...">
                            </div>
                        </div>
                        <div class="col-md-7 d-flex flex-wrap gap-2 justify-content-md-end align-items-center">
                            <div class="btn-group btn-group-sm" role="group" id="statusFilterGroup">
                                <button type="button" class="btn btn-outline-secondary active" data-filter="all">All Plans</button>
                                <button type="button" class="btn btn-outline-secondary" data-filter="active">Active</button>
                                <button type="button" class="btn btn-outline-secondary" data-filter="inactive">Inactive</button>
                                <button type="button" class="btn btn-outline-secondary" data-filter="popular">Popular</button>
                            </div>
                            <select id="planSortSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="default">Sort by Order</option>
                                <option value="price-asc">Price: Low to High</option>
                                <option value="price-desc">Price: High to Low</option>
                                <option value="subscribers">Most Subscribers</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($plans->count() == 0)
            <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                <div class="card-body p-5">
                    <div class="mb-3">
                        <div class="avatar avatar-xl rounded-circle bg-primary bg-opacity-10 text-primary mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-clipboard-list fa-3x"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-gray-800">No Subscription Plans Found</h4>
                    <p class="text-muted small mb-4" style="max-width: 480px; margin: 0 auto;">
                        Get started by creating your first subscription tier with custom feature bundles and resource limits.
                    </p>
                    <a href="{{ route('admin.plans.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">
                        <i class="fas fa-plus me-1"></i> Create First Plan
                    </a>
                </div>
            </div>
        @else
            <!-- Plan Cards Grid -->
            <div class="row g-4" id="plansContainer">
                @foreach($plans as $plan)
                    @php
                        $features = is_string($plan->features) ? (json_decode($plan->features, true) ?? []) : ($plan->features ?? []);
                        $features = is_array($features) ? $features : [];
                        $shownFeatures = array_slice($features, 0, 5);
                        $remainingCount = count($features) - count($shownFeatures);
                    @endphp
                    <div class="col-lg-4 col-md-6 plan-card-wrapper" 
                         data-name="{{ strtolower($plan->name) }}"
                         data-description="{{ strtolower($plan->description ?? '') }}"
                         data-features="{{ strtolower(implode(' ', $features)) }}"
                         data-status="{{ $plan->is_active ? 'active' : 'inactive' }}"
                         data-popular="{{ $plan->is_popular ? 'popular' : 'standard' }}"
                         data-price="{{ (float)$plan->price }}"
                         data-subscribers="{{ (int)$plan->subscriptions_count }}"
                         data-order="{{ $loop->index }}">
                        <div class="card h-100 border-0 shadow-sm rounded-4 position-relative overflow-hidden hover-lift plan-card transition-all"
                             style="{{ $plan->is_popular ? 'border: 2px solid #f59e0b !important;' : 'border: 1px solid rgba(0,0,0,0.06) !important;' }}">
                            
                            <!-- Popular Ribbon -->
                            @if($plan->is_popular)
                                <div class="position-absolute top-0 end-0 bg-warning text-dark px-3 py-1 rounded-bottom-start-3 shadow-sm fw-bold small"
                                     style="z-index: 2; font-size: 0.72rem; letter-spacing: 0.5px;">
                                    <i class="fas fa-star me-1 text-dark"></i> MOST POPULAR
                                </div>
                                <div class="position-absolute top-0 start-0 w-100" style="height: 5px; background: linear-gradient(90deg, #f59e0b, #fbbf24);"></div>
                            @else
                                <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(90deg, #3b82f6, #60a5fa);"></div>
                            @endif

                            <div class="card-body p-4 d-flex flex-column">
                                <!-- Status Badge & Actions -->
                                <div class="d-flex justify-content-between align-items-center mb-3 pt-1">
                                    <span class="badge {{ $plan->is_active ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25' }} rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.72rem;">
                                        <i class="fas fa-circle me-1" style="font-size: 0.45rem;"></i> {{ $plan->is_active ? 'Active Tier' : 'Inactive' }}
                                    </span>

                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center text-muted" 
                                            style="width: 32px; height: 32px;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 py-2">
                                            <li>
                                                <a class="dropdown-item py-2 px-3 small d-flex align-items-center" href="{{ route('admin.plans.edit', $plan->id) }}">
                                                    <i class="fas fa-edit me-2 text-primary w-20"></i> Edit Plan
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.plans.duplicate', $plan->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item py-2 px-3 small d-flex align-items-center">
                                                        <i class="fas fa-copy me-2 text-info w-20"></i> Duplicate
                                                    </button>
                                                </form>
                                            </li>
                                            @if($plan->subscriptions_count == 0)
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li>
                                                    <form action="{{ route('admin.plans.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this plan?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item py-2 px-3 small text-danger d-flex align-items-center">
                                                            <i class="fas fa-trash-alt me-2 w-20"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>

                                <!-- Title & Price -->
                                <div class="text-center mb-4">
                                    <h4 class="fw-bold text-dark mb-1">{{ $plan->name }}</h4>
                                    <div class="text-muted x-small text-uppercase fw-semibold mb-2">
                                        <i class="fas fa-briefcase me-1"></i> {{ ucfirst($plan->business_type ?? 'both') }} Mode
                                    </div>

                                    <div class="d-flex align-items-baseline justify-content-center text-primary mt-3">
                                        <span class="h4 mb-0 fw-bold me-1 text-primary">{{ system_currency_symbol() }}</span>
                                        <span class="display-5 fw-bold text-dark">{{ number_format($plan->price, 0) }}</span>
                                        <span class="text-muted ms-1 small fw-normal">/ {{ $plan->duration_in_days }} days</span>
                                    </div>

                                    @if($plan->trial_days > 0)
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-1 mt-2" style="font-size: 0.7rem;">
                                            <i class="fas fa-gift me-1"></i> {{ $plan->trial_days }} Days Free Trial
                                        </span>
                                    @endif

                                    @if($plan->description)
                                        <p class="text-muted small mt-2 mb-0" style="min-height: 38px;">
                                            {{ Str::limit($plan->description, 75) }}
                                        </p>
                                    @else
                                        <p class="text-muted small mt-2 mb-0 fst-italic" style="min-height: 38px;">
                                            Standard feature bundle with dedicated salon cloud storage.
                                        </p>
                                    @endif
                                </div>

                                <hr class="opacity-10 my-0 mb-3">

                                <!-- Resource Limits Micro-Grid -->
                                <div class="p-3 mb-3 rounded-3" style="background: #f8fafc; border: 1px solid #edf2f7;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="text-uppercase text-muted fw-bold small" style="font-size: 0.68rem; letter-spacing: 0.05em;">Resource Quotas</span>
                                        <span class="badge bg-white text-muted border px-2 py-0" style="font-size: 0.65rem;">Limits</span>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="d-flex align-items-center gap-2 p-2 bg-white rounded-2 border border-light">
                                                <div class="p-1 rounded bg-primary bg-opacity-10 text-primary">
                                                    <i class="fas fa-users fa-xs"></i>
                                                </div>
                                                <div class="lh-1 overflow-hidden">
                                                    <div class="small fw-bold text-dark">{{ $plan->max_users === null ? '∞' : $plan->max_users }}</div>
                                                    <div class="text-muted text-uppercase" style="font-size: 0.62rem;">Staff</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center gap-2 p-2 bg-white rounded-2 border border-light">
                                                <div class="p-1 rounded bg-info bg-opacity-10 text-info">
                                                    <i class="fas fa-store fa-xs"></i>
                                                </div>
                                                <div class="lh-1 overflow-hidden">
                                                    <div class="small fw-bold text-dark">{{ $plan->max_branches === null ? '∞' : $plan->max_branches }}</div>
                                                    <div class="text-muted text-uppercase" style="font-size: 0.62rem;">Branches</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center gap-2 p-2 bg-white rounded-2 border border-light">
                                                <div class="p-1 rounded bg-danger bg-opacity-10 text-danger">
                                                    <i class="fas fa-calendar-check fa-xs"></i>
                                                </div>
                                                <div class="lh-1 overflow-hidden">
                                                    <div class="small fw-bold text-dark">{{ !isset($plan->limits['max_bookings_per_month']) || $plan->limits['max_bookings_per_month'] === -1 ? '∞' : $plan->limits['max_bookings_per_month'] }}</div>
                                                    <div class="text-muted text-uppercase" style="font-size: 0.62rem;">Bookings</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center gap-2 p-2 bg-white rounded-2 border border-light">
                                                <div class="p-1 rounded bg-secondary bg-opacity-10 text-secondary">
                                                    <i class="fas fa-user-friends fa-xs"></i>
                                                </div>
                                                <div class="lh-1 overflow-hidden">
                                                    <div class="small fw-bold text-dark">{{ !isset($plan->limits['max_customers']) || $plan->limits['max_customers'] === -1 ? '∞' : $plan->limits['max_customers'] }}</div>
                                                    <div class="text-muted text-uppercase" style="font-size: 0.62rem;">Clients</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Key Features List -->
                                <div class="mb-4 flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="text-uppercase text-muted fw-bold small mb-0" style="font-size: 0.68rem; letter-spacing: 0.05em;">Included Modules</h6>
                                        <span class="badge bg-light text-muted px-2 py-1" style="font-size: 0.68rem;">{{ count($features) }} Features</span>
                                    </div>
                                    <ul class="list-unstyled mb-0">
                                        @foreach($shownFeatures as $feature)
                                            <li class="d-flex align-items-center mb-2 small text-dark">
                                                <i class="fas fa-check-circle text-success me-2" style="font-size: 0.85rem;"></i>
                                                <span class="fw-medium text-truncate">{{ $feature }}</span>
                                            </li>
                                        @endforeach

                                        @if($remainingCount > 0)
                                            <li class="mt-2">
                                                <button type="button" class="btn btn-link btn-sm p-0 text-primary text-decoration-none small fw-semibold"
                                                    data-bs-toggle="modal" data-bs-target="#featuresModal{{ $plan->id }}">
                                                    <i class="fas fa-plus-circle me-1"></i> +{{ $remainingCount }} more features...
                                                </button>
                                            </li>

                                            <!-- Features Modal -->
                                            <div class="modal fade" id="featuresModal{{ $plan->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                                        <div class="modal-header border-0 pb-0">
                                                            <div>
                                                                <h5 class="modal-title fw-bold text-dark">{{ $plan->name }}</h5>
                                                                <p class="text-muted small mb-0">Complete list of included modules and features</p>
                                                            </div>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="row g-2">
                                                                @foreach($features as $feat)
                                                                    <div class="col-12 col-sm-6">
                                                                        <div class="p-2 rounded-3 bg-light border d-flex align-items-center gap-2 small">
                                                                            <i class="fas fa-check-circle text-success"></i>
                                                                            <span class="fw-medium text-dark">{{ $feat }}</span>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0">
                                                            <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Close</button>
                                                            <a href="{{ route('admin.plans.edit', $plan->id) }}" class="btn btn-primary rounded-3 px-4">Edit Features</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </ul>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-auto">
                                    <div class="d-grid mb-3">
                                        <a href="{{ route('admin.plans.edit', $plan->id) }}" class="btn btn-outline-primary fw-semibold py-2 rounded-3 shadow-none">
                                            <i class="fas fa-sliders-h me-1"></i> Configure Tier
                                        </a>
                                    </div>
                                    <div class="text-center pt-2 border-top border-light">
                                        <small class="text-muted fw-medium" style="font-size: 0.75rem;">
                                            <i class="fas fa-store text-primary me-1"></i> {{ $plan->subscriptions_count }} Active Subscriptions
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Empty State on Search/Filter -->
            <div id="noFilterResults" class="card border-0 shadow-sm rounded-4 text-center py-5 d-none">
                <div class="card-body p-5">
                    <div class="avatar avatar-lg rounded-circle bg-light text-muted mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-search fa-2x"></i>
                    </div>
                    <h5 class="fw-bold text-gray-800">No Matching Plans</h5>
                    <p class="text-muted small mb-0">Try adjusting your search query or status filter.</p>
                </div>
            </div>
        @endif
    </div>

    @push('styles')
        <style>
            .hover-lift {
                transition: transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.22s ease;
            }
            .hover-lift:hover {
                transform: translateY(-6px);
                box-shadow: 0 1.25rem 2.5rem rgba(15, 23, 42, 0.1) !important;
            }
            .w-20 {
                width: 20px;
                text-align: center;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const searchInput = document.getElementById('planSearchInput');
                const statusButtons = document.querySelectorAll('#statusFilterGroup button');
                const sortSelect = document.getElementById('planSortSelect');
                const planCards = Array.from(document.querySelectorAll('.plan-card-wrapper'));
                const container = document.getElementById('plansContainer');
                const noResults = document.getElementById('noFilterResults');

                let currentStatus = 'all';
                let currentSearch = '';

                function filterAndSortPlans() {
                    let visibleCount = 0;

                    planCards.forEach(card => {
                        const name = card.dataset.name || '';
                        const desc = card.dataset.description || '';
                        const feats = card.dataset.features || '';
                        const status = card.dataset.status || '';
                        const popular = card.dataset.popular || '';

                        const matchesSearch = currentSearch === '' || 
                            name.includes(currentSearch) || 
                            desc.includes(currentSearch) || 
                            feats.includes(currentSearch);

                        let matchesStatus = true;
                        if (currentStatus === 'active') matchesStatus = (status === 'active');
                        else if (currentStatus === 'inactive') matchesStatus = (status === 'inactive');
                        else if (currentStatus === 'popular') matchesStatus = (popular === 'popular');

                        if (matchesSearch && matchesStatus) {
                            card.classList.remove('d-none');
                            visibleCount++;
                        } else {
                            card.classList.add('d-none');
                        }
                    });

                    if (noResults) {
                        if (visibleCount === 0) {
                            noResults.classList.remove('d-none');
                        } else {
                            noResults.classList.add('d-none');
                        }
                    }

                    // Sort visible cards
                    if (sortSelect && container) {
                        const sortVal = sortSelect.value;
                        const sortedCards = [...planCards];

                        if (sortVal === 'price-asc') {
                            sortedCards.sort((a, b) => parseFloat(a.dataset.price) - parseFloat(b.dataset.price));
                        } else if (sortVal === 'price-desc') {
                            sortedCards.sort((a, b) => parseFloat(b.dataset.price) - parseFloat(a.dataset.price));
                        } else if (sortVal === 'subscribers') {
                            sortedCards.sort((a, b) => parseInt(b.dataset.subscribers) - parseInt(a.dataset.subscribers));
                        } else {
                            sortedCards.sort((a, b) => parseInt(a.dataset.order) - parseInt(b.dataset.order));
                        }

                        sortedCards.forEach(c => container.appendChild(c));
                    }
                }

                if (searchInput) {
                    searchInput.addEventListener('input', function () {
                        currentSearch = this.value.trim().toLowerCase();
                        filterAndSortPlans();
                    });
                }

                if (statusButtons) {
                    statusButtons.forEach(btn => {
                        btn.addEventListener('click', function () {
                            statusButtons.forEach(b => b.classList.remove('active', 'btn-primary'));
                            this.classList.add('active');
                            currentStatus = this.dataset.filter;
                            filterAndSortPlans();
                        });
                    });
                }

                if (sortSelect) {
                    sortSelect.addEventListener('change', filterAndSortPlans);
                }
            });
        </script>
    @endpush
@endsection