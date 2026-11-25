@extends('layouts.super-admin')

@section('content')
    <style>
        /* ── Page Shell ── */
        .sa-page {
            padding: 1.75rem 2rem;
        }

        /* ── Page Header ── */
        .sa-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.75rem;
        }

        .sa-page-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .sa-page-subtitle {
            font-size: 0.82rem;
            color: #6b7280;
            margin: 0.2rem 0 0;
        }

        .btn-create {
            background: #111827;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1.1rem;
            font-size: 0.84rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            text-decoration: none;
            transition: background 0.18s;
            white-space: nowrap;
        }

        .btn-create:hover {
            background: #374151;
            color: #fff;
        }

        /* ── Filter Bar ── */
        .sa-filter-bar {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.25rem;
        }

        .sa-filter-bar .form-control,
        .sa-filter-bar .form-select {
            border: 1px solid #d1d5db;
            border-radius: 7px;
            font-size: 0.84rem;
            padding: 0.45rem 0.85rem;
            color: #111827;
            background: #fff;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .sa-filter-bar .form-control:focus,
        .sa-filter-bar .form-select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        .sa-filter-bar .input-group-text {
            background: #f9fafb;
            border: 1px solid #d1d5db;
            border-right: none;
            border-radius: 7px 0 0 7px;
            color: #6b7280;
            font-size: 0.82rem;
            padding: 0.45rem 0.75rem;
        }

        .sa-filter-bar .input-group .form-control {
            border-left: none;
            border-radius: 0 7px 7px 0;
        }

        .sa-filter-bar .input-group:focus-within .input-group-text {
            border-color: #6366f1;
        }

        .sa-filter-bar .input-group:focus-within .form-control {
            border-color: #6366f1;
            box-shadow: none;
        }

        .btn-apply {
            background: #6366f1;
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: 0.45rem 1.2rem;
            font-size: 0.84rem;
            font-weight: 600;
            transition: background 0.15s;
        }

        .btn-apply:hover {
            background: #4f46e5;
            color: #fff;
        }

        .btn-reset-filter {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
            border-radius: 7px;
            padding: 0.45rem 1rem;
            font-size: 0.84rem;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.15s;
        }

        .btn-reset-filter:hover {
            background: #e5e7eb;
            color: #111827;
        }

        /* ── Table Card ── */
        .sa-table-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }

        .sa-table-card table {
            margin: 0;
        }

        .sa-table-card thead tr {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .sa-table-card thead th {
            font-size: 0.73rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #6b7280;
            padding: 0.85rem 1rem;
            border: none;
            white-space: nowrap;
        }

        .sa-table-card tbody td {
            padding: 0.9rem 1rem;
            border-top: 1px solid #f3f4f6;
            border-bottom: none;
            vertical-align: middle;
        }

        .sa-table-card tbody tr {
            transition: background 0.12s;
        }

        .sa-table-card tbody tr:hover {
            background: #f9fafb;
            cursor: pointer;
        }

        /* ── Salon Avatar / Logo ── */
        .salon-avatar {
            width: 42px;
            height: 42px;
            border-radius: 9px;
            overflow: hidden;
            flex-shrink: 0;
            border: 1px solid #e5e7eb;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .salon-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .salon-avatar-initials {
            font-size: 1rem;
            font-weight: 700;
            color: #374151;
        }

        .salon-name-text {
            font-size: 0.9rem;
            font-weight: 600;
            color: #111827;
            margin: 0;
            line-height: 1.3;
        }

        .salon-email-text {
            font-size: 0.78rem;
            color: #9ca3af;
            margin: 0.1rem 0 0;
        }

        .salon-slug-tag {
            display: inline-block;
            background: #f3f4f6;
            color: #4b5563;
            border-radius: 4px;
            padding: 0.12rem 0.5rem;
            font-size: 0.7rem;
            font-family: ui-monospace, monospace;
            margin-top: 0.3rem;
        }

        /* ── Owner Cell ── */
        .owner-initials {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e0e7ff;
            color: #4338ca;
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .owner-name {
            font-size: 0.85rem;
            font-weight: 500;
            color: #374151;
        }

        /* ── Badges ── */
        .sa-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.73rem;
            font-weight: 600;
            padding: 0.3rem 0.65rem;
            border-radius: 5px;
            white-space: nowrap;
        }

        .sa-badge-active {
            background: #ecfdf5;
            color: #065f46;
        }

        .sa-badge-inactive {
            background: #fef2f2;
            color: #991b1b;
        }

        .sa-badge-pending {
            background: #fffbeb;
            color: #92400e;
        }

        .sa-badge-expired {
            background: #f3f4f6;
            color: #6b7280;
        }

        .sa-badge-plan {
            background: #ede9fe;
            color: #5b21b6;
        }

        .sa-badge-no-plan {
            background: #f3f4f6;
            color: #6b7280;
        }

        .sa-badge-barber {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
        }

        .sa-badge-salon {
            background: #fdf2f8;
            color: #be185d;
            border: 1px solid #fce7f3;
        }

        .sa-badge-spa {
            background: #f0fdfa;
            color: #0f766e;
            border: 1px solid #ccfbf1;
        }

        .sa-badge-business {
            background: #f8fafc;
            color: #334155;
            border: 1px solid #e2e8f0;
        }

        .sa-badge-trial {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
        }

        /* ── Date ── */
        .date-text {
            font-size: 0.82rem;
            color: #6b7280;
        }

        /* ── Actions Dropdown ── */
        .sa-actions-btn {
            background: none;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 0.28rem 0.7rem;
            color: #6b7280;
            font-size: 0.82rem;
            transition: border-color 0.15s, color 0.15s;
        }

        .sa-actions-btn:hover,
        .sa-actions-btn:focus {
            border-color: #6366f1;
            color: #6366f1;
            background: #f5f3ff;
        }

        .sa-actions-btn::after {
            display: none;
        }

        .sa-dropdown-menu {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.10);
            padding: 0.35rem;
            min-width: 160px;
        }

        .sa-dropdown-menu .dropdown-item {
            font-size: 0.82rem;
            padding: 0.5rem 0.75rem;
            border-radius: 5px;
            color: #374151;
            font-weight: 500;
        }

        .sa-dropdown-menu .dropdown-item:hover {
            background: #f5f3ff;
            color: #4338ca;
        }

        .sa-dropdown-menu .dropdown-item.text-danger:hover {
            background: #fef2f2;
            color: #b91c1c;
        }

        .sa-dropdown-menu .dropdown-divider {
            margin: 0.25rem 0;
            border-color: #f3f4f6;
        }

        /* ── Empty State ── */
        .sa-empty-state {
            padding: 4rem 2rem;
            text-align: center;
            color: #9ca3af;
        }

        .sa-empty-state i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            display: block;
            color: #d1d5db;
        }

        .sa-empty-state p {
            font-size: 0.9rem;
            margin: 0 0 1rem;
        }

        /* ── Pagination ── */
        .sa-pagination {
            padding: 0.9rem 1.25rem;
            border-top: 1px solid #f3f4f6;
        }

        .sa-count {
            font-size: 0.8rem;
            color: #9ca3af;
            padding: 0.9rem 1.25rem 0;
        }
    </style>

    <div class="sa-page">

        {{-- Page Header --}}
        <div class="sa-page-header">
            <div>
                <h1 class="sa-page-title">
                    <i class="fas fa-store" style="color:#6366f1;font-size:1.1rem;"></i>
                    Salon Management
                </h1>
                <p class="sa-page-subtitle">Manage all registered salons on the platform</p>
            </div>
            <a href="{{ route('admin.salons.create') }}" class="btn-create">
                <i class="fas fa-plus" style="font-size:0.75rem;"></i> Add New Salon
            </a>
        </div>

        {{-- Filter Bar --}}
        <div class="sa-filter-bar">
            <form method="GET" action="{{ route('admin.salons.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-3 flex-grow-1" style="max-width:340px;">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by name, email or owner…"
                            value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-sm-auto" style="min-width:150px;">
                    <select name="business_type" class="form-select">
                        <option value="">All Business Types</option>
                        @foreach($businessTypes ?? [] as $bType)
                            <option value="{{ $bType }}" {{ request('business_type') === $bType ? 'selected' : '' }}>
                                {{ ucfirst($bType) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-auto" style="min-width:150px;">
                    <select name="plan_id" class="form-select">
                        <option value="">All Plans</option>
                        @foreach($plans ?? [] as $p)
                            <option value="{{ $p->id }}" {{ request('plan_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} ({{ ucfirst($p->business_type ?? 'Universal') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-auto" style="min-width:130px;">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn-apply"><i class="fas fa-filter me-1"></i> Filter</button>
                </div>
                @if(request('search') || request('status') || request('business_type') || request('plan_id'))
                    <div class="col-auto">
                        <a href="{{ route('admin.salons.index') }}" class="btn-reset-filter"><i class="fas fa-undo me-1"></i> Clear</a>
                    </div>
                @endif
            </form>
        </div>

        {{-- Results Count --}}
        @if($salons->total() > 0)
            <p class="sa-count mb-0" style="padding: 0 0 0.6rem 0;">
                Showing {{ $salons->firstItem() }}–{{ $salons->lastItem() }} of {{ $salons->total() }} salons
            </p>
        @endif

        {{-- Table --}}
        <div class="sa-table-card">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th class="ps-3">Salon</th>
                            <th>Business Type</th>
                            <th>Owner</th>
                            <th>Plan &amp; Tier</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salons as $salon)
                            <tr onclick="window.location='{{ route('admin.salons.show', $salon) }}'">
                                {{-- Salon Info --}}
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="salon-avatar">
                                            @if($salon->logo_url)
                                                <img src="{{ $salon->logo_url }}" alt="{{ $salon->name }}">
                                            @else
                                                <span class="salon-avatar-initials">
                                                    {{ strtoupper(substr($salon->name, 0, 1)) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="salon-name-text">{{ $salon->name }}</p>
                                            <p class="salon-email-text">{{ $salon->email }}</p>
                                            @if($salon->slug)
                                                <span class="salon-slug-tag">/{{ $salon->slug }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Business Type --}}
                                <td>
                                    @php
                                        $bType = strtolower($salon->business_type ?? 'salon');
                                        $bBadge = match($bType) {
                                            'barber' => ['class' => 'sa-badge-barber', 'icon' => 'fas fa-scissors', 'label' => 'Barbershop'],
                                            'salon'  => ['class' => 'sa-badge-salon', 'icon' => 'fas fa-spa', 'label' => 'Hair Salon'],
                                            'spa'    => ['class' => 'sa-badge-spa', 'icon' => 'fas fa-hot-tub', 'label' => 'Spa & Wellness'],
                                            default  => ['class' => 'sa-badge-business', 'icon' => 'fas fa-store', 'label' => ucfirst($bType)]
                                        };
                                    @endphp
                                    <span class="sa-badge {{ $bBadge['class'] }}">
                                        <i class="{{ $bBadge['icon'] }}"></i>
                                        {{ $bBadge['label'] }}
                                    </span>
                                </td>

                                {{-- Owner --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="owner-initials">
                                            {{ strtoupper(substr($salon->owner->name ?? 'N', 0, 2)) }}
                                        </div>
                                        <div>
                                            <span class="owner-name d-block">{{ $salon->owner->name ?? '—' }}</span>
                                            @if($salon->owner?->email)
                                                <span class="text-muted" style="font-size:0.72rem;">{{ $salon->owner->email }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Plan & Tier --}}
                                <td>
                                    @if($salon->activeSubscription && $salon->activeSubscription->plan)
                                        @php
                                            $subPlan = $salon->activeSubscription->plan;
                                            $daysRemaining = $salon->activeSubscription->daysUntilExpiration();
                                        @endphp
                                        <div>
                                            <div class="d-flex align-items-center gap-1 mb-1 flex-wrap">
                                                <span class="sa-badge sa-badge-plan">
                                                    <i class="fas fa-gem" style="font-size:0.65rem;"></i>
                                                    {{ $subPlan->name }}
                                                </span>
                                                <span class="badge bg-light text-secondary border rounded-pill" style="font-size:0.65rem;">
                                                    {{ ucfirst($subPlan->business_type ?? 'All') }}
                                                </span>
                                            </div>
                                            <div class="text-muted small d-flex align-items-center gap-1" style="font-size:0.72rem;">
                                                <i class="fas fa-clock text-muted"></i>
                                                @if($daysRemaining !== null)
                                                    @if($daysRemaining > 0)
                                                        <span>{{ $daysRemaining }}d remaining</span>
                                                    @elseif($daysRemaining == 0)
                                                        <span class="text-warning fw-bold">Expires today</span>
                                                    @else
                                                        <span class="text-danger fw-bold">Expired</span>
                                                    @endif
                                                @else
                                                    <span>{{ $subPlan->duration_in_days }}d cycle</span>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif($salon->trial_ends_at)
                                        @php
                                            $trialDays = now()->diffInDays($salon->trial_ends_at, false);
                                        @endphp
                                        <div>
                                            <span class="sa-badge sa-badge-trial mb-1">
                                                <i class="fas fa-gift text-info" style="font-size:0.65rem;"></i> Free Trial
                                            </span>
                                            <div class="text-muted small" style="font-size:0.72rem;">
                                                @if($trialDays > 0)
                                                    <span>{{ $trialDays }}d remaining</span>
                                                @else
                                                    <span class="text-danger fw-semibold">Trial ended</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="sa-badge sa-badge-no-plan">No Plan</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if($salon->activeSubscription && $salon->activeSubscription->status === 'pending')
                                        <span class="sa-badge sa-badge-pending">
                                            <i class="fas fa-circle" style="font-size:0.45rem;"></i> Pending
                                        </span>
                                    @elseif(!$salon->is_active)
                                        <span class="sa-badge sa-badge-inactive">
                                            <i class="fas fa-circle" style="font-size:0.45rem;"></i> Suspended
                                        </span>
                                    @elseif(!$salon->isActive())
                                        <span class="sa-badge sa-badge-expired">
                                            <i class="fas fa-circle" style="font-size:0.45rem;"></i> Expired
                                        </span>
                                    @else
                                        <span class="sa-badge sa-badge-active">
                                            <i class="fas fa-circle" style="font-size:0.45rem;"></i> Active
                                        </span>
                                    @endif
                                </td>

                                {{-- Date --}}
                                <td>
                                    <span class="date-text">{{ $salon->created_at->format('d M Y') }}</span>
                                </td>

                                {{-- Actions --}}
                                <td class="text-end pe-3" onclick="event.stopPropagation()">
                                    <div class="dropdown">
                                        <button class="sa-actions-btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end sa-dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.salons.show', $salon) }}">
                                                    <i class="fas fa-eye me-2 text-muted" style="width:14px;"></i> View
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.salons.edit', $salon) }}">
                                                    <i class="fas fa-pen me-2 text-muted" style="width:14px;"></i> Edit
                                                </a>
                                            </li>
                                            @if($salon->owner)
                                                <li>
                                                    <form action="{{ route('admin.salons.impersonate', $salon) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-user-secret me-2 text-muted" style="width:14px;"></i>
                                                            Login as Owner
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.salons.destroy', $salon) }}" method="POST"
                                                    onsubmit="return confirm('Delete this salon? All data will be permanently removed.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash me-2" style="width:14px;"></i> Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="sa-empty-state">
                                        <i class="fas fa-store-slash"></i>
                                        <p>No salons found</p>
                                        @if(request('search') || request('status'))
                                            <a href="{{ route('admin.salons.index') }}" class="btn-apply"
                                                style="text-decoration:none; font-size:0.82rem;">Clear filters</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($salons->hasPages())
                <div class="sa-pagination">
                    {{ $salons->links() }}
                </div>
            @endif
        </div>

    </div>

    @push('scripts')
        <script>
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });
        </script>
    @endpush
@endsection