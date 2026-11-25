@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row g-4">
            <!-- Quick Stats -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-lg bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1 small text-uppercase fw-semibold">Upcoming</h6>
                                <h3 class="mb-0 fw-bold">{{ $stats['upcoming'] ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-lg bg-success-subtle text-success rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    <i class="fas fa-check-double"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1 small text-uppercase fw-semibold">Completed</h6>
                                <h3 class="mb-0 fw-bold text-success">{{ $stats['completed'] ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-lg bg-warning-subtle text-warning rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1 small text-uppercase fw-semibold">Pending</h6>
                                <h3 class="mb-0 fw-bold">{{ $stats['pending'] ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-lg bg-info-subtle text-info rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    <i class="fas fa-history"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1 small text-uppercase fw-semibold">Total History</h6>
                                <h3 class="mb-0 fw-bold">{{ $stats['total'] ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Loyalty Rewards Balance Banner --}}
            @if(!empty($hasLoyalty))
            <div class="col-12">
                <div class="card border-0 shadow-sm text-white overflow-hidden"
                    style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%); border-radius: 14px; border: 1px solid rgba(197,168,128,0.3) !important;">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-warning text-dark px-2 py-1 rounded-pill fw-bold text-uppercase me-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                        <i class="fas fa-crown me-1 text-dark"></i> VIP Loyalty Rewards
                                    </span>
                                    <span class="text-white-50 small">Exclusive Client Privileges</span>
                                </div>
                                <h4 class="fw-bold mb-1 text-white">Your Reward Balance</h4>
                                <p class="text-white-50 small mb-2">Earn points on every salon visit and redeem them for instant discounts at POS checkout.</p>
                                <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                                    <span class="small" style="color: #cbd5e1;">
                                        <i class="fas fa-coins text-warning me-1"></i> Worth: <strong class="text-white">{{ currency_symbol() }}{{ number_format($loyaltyValue ?? 0, 2) }}</strong>
                                    </span>
                                    @if(($loyaltyPoints ?? 0) >= ($minPointsToRedeem ?? 50))
                                        <span class="badge bg-success bg-opacity-75 text-white" style="font-size: 0.7rem;"><i class="fas fa-check me-1"></i>Ready to Redeem</span>
                                    @else
                                        <span class="badge bg-secondary text-white-50" style="font-size: 0.7rem;">Min {{ $minPointsToRedeem ?? 50 }} pts to redeem</span>
                                    @endif
                                    <span class="badge shadow-none" style="background: rgba(15, 23, 42, 0.5) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.25) !important; font-size: 0.72rem; font-weight: 500;">
                                        <i class="fas fa-hourglass-half me-1 text-warning"></i>
                                        @if(($loyaltyExpiryMonths ?? 12) > 0)
                                            Points valid for {{ $loyaltyExpiryMonths ?? 12 }} months
                                        @else
                                            Points never expire
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                <div class="display-5 fw-bold text-warning mb-0">
                                    {{ number_format($loyaltyPoints ?? 0) }} <span class="fs-6 text-white-50 fw-normal">pts</span>
                                </div>
                                <small class="text-warning opacity-90 d-block mt-1">
                                    <i class="fas fa-check-circle me-1"></i> Redeemable at checkout
                                </small>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top border-white border-opacity-15">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="mb-0 text-white fw-bold">
                                    <i class="fas fa-receipt me-1 text-warning"></i> Loyalty Points Transaction History
                                </h6>
                                <span class="badge shadow-none" style="background: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.2) !important; font-size: 0.72rem;">
                                    {{ isset($loyaltyTransactions) ? $loyaltyTransactions->count() : 0 }} Records
                                </span>
                            </div>

                            @if(isset($loyaltyTransactions) && $loyaltyTransactions->count() > 0)
                                <div class="table-responsive rounded-3 mt-2" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.08);">
                                    <table class="table table-sm table-borderless text-white align-middle mb-0" style="font-size: 0.82rem;">
                                        <thead>
                                            <tr class="text-white-50 border-bottom border-white border-opacity-10 small">
                                                <th class="ps-3 py-2">Date &amp; Time</th>
                                                <th class="py-2">Type</th>
                                                <th class="py-2">Points</th>
                                                <th class="py-2">Note / Invoice</th>
                                                <th class="pe-3 py-2 text-end">Balance After</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($loyaltyTransactions as $trans)
                                            <tr class="border-bottom border-white border-opacity-5">
                                                <td class="ps-3 py-2 text-white-50">{{ format_datetime($trans->created_at) }}</td>
                                                <td class="py-2">
                                                    <span class="badge bg-{{ $trans->type === 'earned' ? 'success' : ($trans->type === 'redeemed' ? 'danger' : 'secondary') }} bg-opacity-90 fw-semibold px-2 py-1">
                                                        <i class="fas fa-{{ $trans->type === 'earned' ? 'arrow-down' : ($trans->type === 'redeemed' ? 'arrow-up' : 'sync') }} me-1 small"></i>
                                                        {{ ucfirst($trans->type) }}
                                                    </span>
                                                </td>
                                                <td class="py-2 fw-bold {{ $trans->points > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $trans->points > 0 ? '+' : '' }}{{ $trans->points }} pts
                                                </td>
                                                <td class="py-2 text-white-75">{{ $trans->description ?? '-' }}</td>
                                                <td class="pe-3 py-2 text-end fw-bold text-warning">{{ number_format($trans->balance_after) }} pts</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-3 rounded-3 text-center mt-2" style="background: rgba(0,0,0,0.2);">
                                    <p class="mb-0 text-white-50 small">
                                        <i class="fas fa-coins text-warning me-1"></i> No transactions recorded yet. Earn points on every completed salon appointment!
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Book CTA Banner -->
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%); border: 1px solid #e2e8f0 !important; border-radius: 14px;">
                    <div class="card-body p-4 text-center">
                        <h4 class="fw-bold mb-2 text-dark">Need to book your next appointment?</h4>
                        <p class="text-muted mb-3">Quick and easy booking process — select your preferred service and schedule in seconds!</p>
                        <x-btn type="button" variant="primary" size="lg" icon="fas fa-calendar-plus" data-bs-toggle="modal"
                            data-bs-target="#quickBookModal" class="shadow-sm px-4">
                            Book Appointment Now
                        </x-btn>
                    </div>
                </div>
            </div>

            <!-- Left Main Column: Upcoming & Completed Appointments -->
            <div class="col-xl-8">
                <!-- Upcoming Appointments -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i>
                            Upcoming Appointments
                        </h5>
                        <a href="{{ route('customer.appointments.index', ['status' => 'upcoming']) }}" class="btn btn-sm btn-link text-decoration-none p-0 fw-semibold">
                            View All <i class="fas fa-chevron-right ms-1 small"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($upcomingAppointments ?? [] as $booking)
                                <div class="list-group-item p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar avatar-md bg-{{ $booking->status === 'pending' ? 'warning' : 'primary' }}-subtle text-{{ $booking->status === 'pending' ? 'warning' : 'primary' }} rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                                <i class="fas fa-calendar-day"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <h6 class="mb-0 fw-bold text-dark">{{ $booking->service->name ?? 'Appointment' }}</h6>
                                                @if($booking->package)
                                                    <span class="badge bg-purple-subtle text-purple-emphasis rounded-pill"
                                                        style="background-color: #f3e8ff !important; color: #7c3aed !important; font-size: 0.68em;">
                                                        <i class="fas fa-box me-1"></i>{{ $booking->package->name }}
                                                    </span>
                                                @endif
                                                <span class="badge bg-{{ $booking->status === 'pending' ? 'warning' : 'info' }}-subtle text-{{ $booking->status === 'pending' ? 'warning' : 'info' }}-emphasis" style="font-size: 0.7rem;">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </div>
                                            <div class="text-muted small mt-1">
                                                <span class="me-3"><i class="fas fa-clock text-primary opacity-75 me-1"></i>{{ format_datetime($booking->start_time) }}</span>
                                                @if($booking->staff)
                                                    <span><i class="fas fa-user-tie text-secondary opacity-75 me-1"></i>{{ $booking->staff->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <a href="{{ route('customer.appointments.show', $booking->id) }}"
                                                class="btn btn-sm btn-outline-secondary">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-calendar-times fa-2x mb-3 text-secondary opacity-50"></i>
                                        <h6 class="fw-semibold text-dark mb-1">No upcoming appointments</h6>
                                        <p class="small text-muted mb-0">Book your next haircut or grooming service today!</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Completed Appointments & Reviews -->
                <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 fw-bold">
                                <i class="fas fa-check-double me-2 text-success"></i>
                                Completed Appointments &amp; Reviews
                            </h5>
                            <span class="badge bg-success-subtle text-success ms-2 fw-bold" style="font-size: 0.75rem;">
                                {{ $stats['completed'] ?? 0 }} Completed
                            </span>
                        </div>
                        <a href="{{ route('customer.appointments.index', ['status' => 'completed']) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                            Full History <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($completedAppointments ?? [] as $completed)
                                <div class="list-group-item p-3">
                                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar avatar-md bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                            </div>
                                            <div class="ms-3">
                                                <h6 class="mb-0 fw-bold text-dark">
                                                    {{ $completed->service->name ?? 'Completed Service' }}
                                                    @if($completed->package)
                                                        <span class="badge bg-purple-subtle text-purple-emphasis rounded-pill ms-1"
                                                            style="background-color: #f3e8ff !important; color: #7c3aed !important; font-size: 0.65em;">
                                                            <i class="fas fa-box me-1"></i>{{ $completed->package->name }}
                                                        </span>
                                                    @endif
                                                </h6>
                                                <div class="text-muted small mt-1">
                                                    <span class="me-3"><i class="fas fa-calendar-alt text-muted me-1"></i>{{ format_date($completed->start_time) }}</span>
                                                    @if($completed->staff)
                                                        <span class="me-3"><i class="fas fa-user-tie text-muted me-1"></i>{{ $completed->staff->name }}</span>
                                                    @endif
                                                    @if(isset($completed->service->price))
                                                        <span class="fw-semibold text-dark">{{ currency_symbol() }}{{ number_format($completed->service->price, 2) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Rating Status & Action Button --}}
                                        <div class="text-sm-end mt-2 mt-sm-0">
                                            @if(!$completed->is_rated && !$completed->rating && !$completed->ratings()->exists())
                                                <button type="button" class="btn btn-sm btn-warning text-dark fw-bold px-3 py-1 shadow-sm"
                                                    onclick="showRatingModal({{ $completed->id }})"
                                                    title="Share feedback on your appointment">
                                                    <i class="fas fa-star text-dark me-1"></i> Rate Appointment
                                                </button>
                                            @else
                                                <div>
                                                    <button type="button" class="btn btn-sm btn-light text-muted border px-2 py-1 shadow-none" disabled title="Already rated">
                                                        <i class="fas fa-check-circle text-success me-1"></i> Rated ({{ $completed->rating ?? optional($completed->ratings()->first())->rating ?? 5 }}/5 &#9733;)
                                                    </button>
                                                    @if(!empty($completed->feedback))
                                                        <div class="small text-muted fst-italic mt-1" style="max-width: 260px;">
                                                            "{{ Str::limit($completed->feedback, 55) }}"
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-check-circle fa-2x mb-3 text-secondary opacity-50"></i>
                                        <h6 class="fw-semibold text-dark mb-1">No completed appointments yet</h6>
                                        <p class="small text-muted mb-0">Your finished appointments and rating history will appear here.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Profile Summary & Recent Activity -->
            <div class="col-xl-4">
                <!-- Client Profile Summary -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fas fa-user-circle me-2 text-primary"></i>
                            Your Profile
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-lg bg-primary-subtle text-primary rounded-circle fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; font-size: 1.3rem;">
                                {{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-bold text-dark">{{ auth()->user()->name }}</h6>
                                <span class="badge bg-light text-muted border py-1 px-2 mt-1" style="font-size: 0.72rem;">Customer</span>
                            </div>
                        </div>
                        <ul class="list-unstyled mb-3 small">
                            <li class="py-1 text-muted d-flex align-items-center">
                                <i class="fas fa-envelope fa-fw me-2 text-secondary"></i> {{ auth()->user()->email }}
                            </li>
                            @if(!empty(auth()->user()->phone) || !empty(auth()->user()->customer?->phone))
                            <li class="py-1 text-muted d-flex align-items-center">
                                <i class="fas fa-phone fa-fw me-2 text-secondary"></i> {{ auth()->user()->customer?->phone ?? auth()->user()->phone }}
                            </li>
                            @endif
                            @if(optional(auth()->user()->salon)->name)
                            <li class="py-1 text-muted d-flex align-items-center">
                                <i class="fas fa-store fa-fw me-2 text-secondary"></i> {{ optional(auth()->user()->salon)->name }}
                            </li>
                            @endif
                        </ul>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="fas fa-user-edit me-1"></i> Edit Profile
                        </a>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fas fa-history me-2 text-primary"></i>
                            Recent Activity
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse(auth()->user()->customer?->bookings()->with(['service', 'package'])->latest()->take(6)->get() ?? [] as $booking)
                                <div class="list-group-item p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar avatar-sm bg-{{ $booking->status === 'completed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'info') }}-subtle text-{{ $booking->status === 'completed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'info') }} rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; font-size: 0.85rem;">
                                                <i class="fas fa-{{ $booking->status === 'completed' ? 'check' : ($booking->status === 'cancelled' ? 'times' : 'clock') }}"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0 text-truncate fw-semibold" style="font-size: 0.88rem;">
                                                {{ $booking->service->name ?? 'Appointment' }}
                                                @if($booking->package)
                                                    <span class="badge bg-purple-subtle text-purple-emphasis rounded-pill ms-1"
                                                        style="background-color: #f3e8ff !important; color: #7c3aed !important; font-size: 0.6em;">
                                                        <i class="fas fa-box me-1"></i>{{ $booking->package->name }}
                                                    </span>
                                                @endif
                                            </h6>
                                            <p class="mb-0 small text-muted" style="font-size: 0.75rem;">
                                                {{ format_date($booking->start_time) }} • <span class="text-capitalize">{{ str_replace('_', ' ', $booking->status) }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-history fa-2x mb-3 text-secondary opacity-50"></i>
                                        <p class="mb-0 small">No recent activity</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating Modal -->
    <div class="modal fade" id="ratingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-star text-warning me-2"></i>Rate Your Experience</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pt-2 pb-3">
                    <form id="ratingForm">
                        <input type="hidden" id="appointment_id" name="appointment_id">

                        <div class="text-center my-3">
                            <p class="text-muted mb-2">How satisfied were you with your service?</p>
                            <div class="rating justify-content-center mb-2">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}">
                                    <label for="star{{ $i }}" class="mx-1" title="{{ $i }} Stars">
                                        <i class="fas fa-star" style="font-size: 2.2rem;"></i>
                                    </label>
                                @endfor
                            </div>
                            <div class="d-flex justify-content-between small text-muted w-75 mx-auto">
                                <span>Poor</span>
                                <span>Excellent</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="comment" class="form-label small fw-semibold text-muted">Share your experience (optional)</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3"
                                placeholder="Tell us what you loved or how we can improve..."></textarea>
                        </div>

                        <div id="ratingError" class="alert alert-danger py-2 d-none" role="alert">
                            <i class="fas fa-exclamation-circle me-1"></i> Please select a star rating
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-sm px-4 fw-semibold" id="submitRatingBtn" onclick="submitRating()">
                        <i class="fas fa-paper-plane me-1"></i> Submit Rating
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Book Modal -->
    <div class="modal fade" id="quickBookModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-calendar-plus me-2 text-primary"></i>
                        Quick Book Appointment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="quickBookForm" action="{{ route('customer.appointments.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <!-- Customer Details (Auto-filled) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Customer Details</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-user text-secondary"></i>
                                </span>
                                <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-phone text-secondary"></i>
                                </span>
                                <input type="tel" class="form-control"
                                    value="{{ auth()->user()->customer?->phone ?? 'Not set' }}" readonly>
                            </div>
                        </div>

                        <!-- Service Selection -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Select Service</label>
                            <select class="form-select" name="service_id" required>
                                <option value="">Choose a service...</option>
                                @foreach($services ?? [] as $service)
                                    <option value="{{ $service->id }}" data-duration="{{ $service->duration }}">
                                        {{ $service->name }} - {{ currency_symbol() }}{{ number_format($service->price, 2) }}
                                        ({{ $service->duration }} min)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date & Time Selection -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Date</label>
                                <input type="date" class="form-control" name="date" min="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Time</label>
                                <select class="form-select" name="time" required disabled>
                                    <option value="">Select date first</option>
                                </select>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3 mt-3">
                            <label class="form-label fw-semibold small text-muted">Additional Notes (optional)</label>
                            <textarea class="form-control" name="notes" rows="2"
                                placeholder="Any special requests or notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top p-3">
                        <x-btn type="button" variant="light" data-bs-dismiss="modal">Cancel</x-btn>
                        <x-btn type="submit" variant="primary" icon="fas fa-calendar-check">
                            Book Appointment
                        </x-btn>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Rating stars interactive styling */
            .rating {
                display: flex;
                flex-direction: row-reverse;
                justify-content: center;
                gap: 2px;
            }
            .rating input {
                position: absolute;
                opacity: 0;
                width: 1px;
                height: 1px;
                pointer-events: none;
            }
            .rating input:focus-visible ~ label {
                outline: 2px solid #f59e0b;
                outline-offset: 2px;
                border-radius: 4px;
            }
            .rating label {
                cursor: pointer;
                color: #e2e8f0;
                padding: 0 0.15em;
                transition: color 0.15s ease-in-out, transform 0.15s ease-in-out;
            }
            .rating label:hover {
                transform: scale(1.15);
            }
            .rating input:checked ~ label,
            .rating label:hover,
            .rating label:hover ~ label {
                color: #f59e0b !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function showRatingModal(bookingId) {
                document.getElementById('appointment_id').value = bookingId;
                document.getElementById('ratingForm').reset();
                document.getElementById('ratingError').classList.add('d-none');
                try {
                    sessionStorage.setItem('feedback_modal_dismissed_' + bookingId, '1');
                } catch(e) {}
                const modal = new bootstrap.Modal(document.getElementById('ratingModal'));
                modal.show();
            }

            function submitRating() {
                const bookingId = document.getElementById('appointment_id').value;
                const ratingRadio = document.querySelector('input[name="rating"]:checked');
                const rating = ratingRadio ? ratingRadio.value : null;
                const comment = document.getElementById('comment').value;

                if (!rating) {
                    document.getElementById('ratingError').classList.remove('d-none');
                    return;
                }

                try {
                    sessionStorage.setItem('feedback_modal_dismissed_' + bookingId, '1');
                } catch(e) {}

                const submitBtn = document.getElementById('submitRatingBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Submitting...';
                document.getElementById('ratingError').classList.add('d-none');

                const rateUrlTemplate = "{{ route('customer.appointments.rate', ['salon_slug' => request()->route('salon_slug') ?? optional(auth()->user()->salon)->slug ?? 'default', 'booking' => ':id']) }}";
                const targetUrl = rateUrlTemplate.replace(':id', bookingId);

                fetch(targetUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        rating: rating,
                        comment: comment
                    })
                })
                .then(response => response.json())
                .then(data => {
                    const modalEl = document.getElementById('ratingModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();

                    if (window.notifications) {
                        window.notifications.show('Thank you! Your rating and review have been submitted.', 'success');
                    } else if (window.showModalAlert) {
                        window.showModalAlert('Thank you! Your rating and review have been submitted.', 'success');
                    } else {
                        alert('Thank you! Your rating and review have been submitted.');
                    }

                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                })
                .catch(error => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Submit Rating';
                    document.getElementById('ratingError').classList.remove('d-none');
                    document.getElementById('ratingError').textContent = error.message || 'Error submitting rating. Please try again.';
                });
            }

            document.addEventListener('DOMContentLoaded', function () {
                {{-- Dismiss listener to prevent repeat popup when modal closed without rating --}}
                const ratingModalEl = document.getElementById('ratingModal');
                if (ratingModalEl) {
                    ratingModalEl.addEventListener('hidden.bs.modal', function () {
                        const apptId = document.getElementById('appointment_id')?.value;
                        if (apptId) {
                            try {
                                sessionStorage.setItem('feedback_modal_dismissed_' + apptId, '1');
                            } catch(e) {}
                        }
                    });
                }

                {{-- Automatically show rating modal once per session after appointment is done and pending review --}}
                @if(!empty($pendingFeedbackBooking))
                const feedbackDismissKey = 'feedback_modal_dismissed_{{ $pendingFeedbackBooking->id }}';
                if (!sessionStorage.getItem(feedbackDismissKey)) {
                    setTimeout(function () {
                        showRatingModal({{ $pendingFeedbackBooking->id }});
                    }, 800);
                }
                @endif

                const quickBookForm = document.getElementById('quickBookForm');
                if (!quickBookForm) return;

                const dateInput = quickBookForm.querySelector('input[name="date"]');
                const timeSelect = quickBookForm.querySelector('select[name="time"]');
                const serviceSelect = quickBookForm.querySelector('select[name="service_id"]');

                function formatTime12(timeStr) {
                    if (!timeStr) return '';
                    const parts = timeStr.split(':');
                    const hour = parseInt(parts[0], 10);
                    const minute = parts[1] || '00';
                    const ampm = hour >= 12 ? 'PM' : 'AM';
                    const hour12 = hour % 12 || 12;
                    return `${hour12}:${minute} ${ampm}`;
                }

                const slotsBaseUrl = "{{ route('customer.api.appointments.available-slots', ['salon_slug' => request()->route('salon_slug') ?? optional(auth()->user()->salon)->slug ?? 'default']) }}";

                function loadTimeSlots() {
                    if (!dateInput.value || !serviceSelect.value) return;

                    timeSelect.disabled = true;
                    timeSelect.innerHTML = '<option value="">Loading available slots...</option>';

                    fetch(`${slotsBaseUrl}?date=${encodeURIComponent(dateInput.value)}&service_id=${encodeURIComponent(serviceSelect.value)}`)
                        .then(response => response.json())
                        .then(data => {
                            timeSelect.innerHTML = '<option value="">Select a time</option>';

                            if (!data || data.length === 0) {
                                timeSelect.innerHTML = '<option value="">No available slots</option>';
                                return;
                            }

                            let availableCount = 0;
                            data.forEach(slot => {
                                if (slot.available) {
                                    availableCount++;
                                    timeSelect.innerHTML += `<option value="${slot.time}">${formatTime12(slot.time)}</option>`;
                                }
                            });

                            if (availableCount === 0) {
                                timeSelect.innerHTML = '<option value="">No available slots on this date</option>';
                            } else {
                                timeSelect.disabled = false;
                            }
                        })
                        .catch(error => {
                            timeSelect.innerHTML = '<option value="">Error loading slots</option>';
                        });
                }

                dateInput.addEventListener('change', loadTimeSlots);
                serviceSelect.addEventListener('change', function () {
                    if (dateInput.value) {
                        loadTimeSlots();
                    }
                });

                quickBookForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalButtonText = submitButton.innerHTML;

                    const timeVal = formData.get('time');
                    if (!timeVal) {
                        alert('Please select an appointment time.');
                        return;
                    }

                    submitButton.disabled = true;
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Booking...';

                    fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            service_id: formData.get('service_id'),
                            date: formData.get('date'),
                            time: timeVal,
                            notes: formData.get('notes')
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('quickBookModal'));
                            if (modal) modal.hide();

                            if (window.notifications) {
                                window.notifications.show('Appointment booked successfully!', 'success');
                            } else if (window.showModalAlert) {
                                window.showModalAlert('Appointment booked successfully!', 'success');
                            }

                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                window.location.reload();
                            }
                        } else {
                            throw new Error(data.message || 'Failed to book appointment');
                        }
                    })
                    .catch(error => {
                        const errMsg = error.message || 'Failed to book appointment. Please try again.';
                        if (window.notifications) {
                            window.notifications.show(errMsg, 'error');
                        } else if (window.showModalAlert) {
                            window.showModalAlert(errMsg, 'error');
                        } else {
                            alert(errMsg);
                        }
                    })
                    .finally(() => {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonText;
                    });
                });
            });
        </script>
    @endpush

@endsection
