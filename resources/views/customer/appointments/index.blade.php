@extends('layouts.app')

@php
    $salonSlug = $salonSlug ?? request()->route('salon_slug') ?? optional(auth()->user()?->salon)->slug ?? session('current_salon_slug') ?? 'default';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer-dashboard.css') }}?v={{ file_exists(public_path('css/customer-dashboard.css')) ? filemtime(public_path('css/customer-dashboard.css')) : '1.0' }}">
    <style>
        /* Refined Enterprise Appointments Styling */
        .appt-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            transition: all 0.2s ease;
        }

        .appt-stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1.15rem 1.25rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
        }
        .appt-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.07);
        }
        .appt-stat-number {
            font-size: 1.65rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }
        .appt-stat-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
        }
        .appt-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            background: var(--theme-accent-light, rgba(197, 168, 128, 0.12));
            color: var(--theme-accent, #c5a880);
            border: 1px solid var(--theme-accent-light, rgba(197, 168, 128, 0.2));
        }

        /* Filter Pills */
        .filter-pill-bar {
            display: inline-flex;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 50px;
            gap: 4px;
            border: 1px solid #e2e8f0;
        }
        .filter-pill-item {
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .filter-pill-item:hover {
            color: #0f172a;
            background: rgba(255, 255, 255, 0.6);
        }
        .filter-pill-item.active {
            background: var(--theme-accent, #c5a880);
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(197, 168, 128, 0.35);
        }

        /* Modern Table */
        .appt-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 0.73rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .appt-table td {
            padding: 0.95rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
        .appt-table tbody tr:hover td {
            background: #fcfdfe;
        }

        /* Status Pills (Harmonized, Clean, Professional) */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.73rem;
            font-weight: 600;
            padding: 0.3em 0.75em;
            border-radius: 50px;
            letter-spacing: 0.2px;
        }
        .status-pill-confirmed {
            background: rgba(197, 168, 128, 0.15);
            color: #8f6e42;
            border: 1px solid rgba(197, 168, 128, 0.35);
        }
        .status-pill-completed {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        .status-pill-pending {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .status-pill-cancelled {
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        /* Loyalty Card */
        .loyalty-theme-card {
            background: linear-gradient(135deg, var(--theme-gradient-start, #0b0f17) 0%, var(--theme-sidebar, #1a2232) 50%, var(--theme-surface, #0f172a) 100%);
            border: 1px solid var(--theme-border, rgba(197, 168, 128, 0.25));
            border-radius: 16px;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(11, 15, 23, 0.08);
        }
    </style>
@endpush

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('customer.dashboard', ['salon_slug' => $salonSlug]) }}" class="text-muted text-decoration-none small">&larr; Dashboard</a>
                <span class="text-muted small">/</span>
                <span class="small fw-semibold" style="color: var(--theme-accent, #c5a880);">Appointments</span>
            </div>
            <h1 class="h4 mb-0 fw-bold text-dark">My Appointments</h1>
            <p class="text-muted small mb-0">Track scheduled visits, review service history, and manage appointments.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('customer.dashboard', ['salon_slug' => $salonSlug]) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold">
                <i class="fas fa-home me-1"></i> Dashboard
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show py-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(!empty($hasLoyalty))
        <!-- Customer Loyalty Rewards Balance Card -->
        <div class="loyalty-theme-card p-3 p-md-4 mb-4 position-relative overflow-hidden">
            <div class="row align-items-center position-relative" style="z-index: 2;">
                <div class="col-md-7">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge px-2.5 py-1 rounded-pill fw-bold text-uppercase me-2" 
                              style="background: rgba(197, 168, 128, 0.2); color: var(--theme-accent, #c5a880); border: 1px solid rgba(197, 168, 128, 0.35); font-size: 0.7rem; letter-spacing: 0.5px;">
                            <i class="fas fa-gem me-1"></i> VIP Rewards
                        </span>
                        <span class="text-white-50 small">Customer Loyalty Program</span>
                    </div>
                    <h5 class="fw-bold mb-1 text-white">Your Loyalty Points Balance</h5>
                    <p class="text-white-50 small mb-0">Earn reward points on every service and redeem them for instant discounts at checkout.</p>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <div class="display-6 fw-bold mb-0" style="color: var(--theme-accent, #c5a880);">
                        {{ number_format($loyaltyPoints ?? 0) }} <span class="fs-6 text-white-50">pts</span>
                    </div>
                    <div class="small text-white-50 mt-1 d-flex flex-wrap align-items-center justify-content-md-end gap-2">
                        <span>Approx. Value: <strong class="text-white">{{ currency_symbol() }}{{ number_format($loyaltyValue ?? 0, 2) }}</strong></span>
                        @if(($loyaltyPoints ?? 0) >= ($minPointsToRedeem ?? 50))
                            <span class="badge rounded-pill bg-success bg-opacity-75 text-white" style="font-size: 0.65rem;">Ready to Redeem</span>
                        @else
                            <span class="badge rounded-pill text-white-50" style="background: rgba(255,255,255,0.15); font-size: 0.65rem;">Min {{ $minPointsToRedeem ?? 50 }} pts to redeem</span>
                        @endif
                    </div>
                </div>
            </div>

            @if(isset($loyaltyTransactions) && $loyaltyTransactions->count() > 0)
                <div class="mt-3 pt-3 border-top border-white border-opacity-10 position-relative" style="z-index: 2;">
                    <button class="btn btn-sm text-white-50 p-0 text-decoration-none fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#loyaltyHistoryCollapse" aria-expanded="false" aria-controls="loyaltyHistoryCollapse">
                        <i class="fas fa-history me-1" style="color: var(--theme-accent, #c5a880);"></i> View Points History <i class="fas fa-chevron-down ms-1 small"></i>
                    </button>
                    <div class="collapse mt-2" id="loyaltyHistoryCollapse">
                        <div class="table-responsive rounded-3" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.08);">
                            <table class="table table-sm table-borderless text-white mb-0" style="font-size: 0.8rem;">
                                <thead>
                                    <tr class="text-white-50 border-bottom border-white border-opacity-10">
                                        <th class="ps-3 py-2">Date</th>
                                        <th class="py-2">Type</th>
                                        <th class="py-2">Points</th>
                                        <th class="py-2">Reason / Note</th>
                                        <th class="pe-3 py-2 text-end">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($loyaltyTransactions as $tx)
                                        <tr class="border-bottom border-white border-opacity-10">
                                            <td class="ps-3 text-white-50 py-2">{{ $tx->created_at ? $tx->created_at->format('M d, Y') : '-' }}</td>
                                            <td class="py-2">
                                                @if($tx->type === 'earned')
                                                    <span class="badge rounded-pill bg-success text-white" style="font-size: 0.65rem;">+ Earned</span>
                                                @elseif($tx->type === 'redeemed')
                                                    <span class="badge rounded-pill text-dark" style="background: var(--theme-accent, #c5a880); font-size: 0.65rem;">- Redeemed</span>
                                                @else
                                                    <span class="badge rounded-pill bg-secondary text-white" style="font-size: 0.65rem;">{{ ucfirst($tx->type) }}</span>
                                                @endif
                                            </td>
                                            <td class="fw-bold py-2 {{ $tx->points > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $tx->points > 0 ? '+' : '' }}{{ number_format($tx->points) }}
                                            </td>
                                            <td class="text-white-50 text-truncate py-2" style="max-width: 200px;">{{ $tx->notes ?: ($tx->reference_type ? class_basename($tx->reference_type) : '-') }}</td>
                                            <td class="pe-3 text-end font-monospace text-white py-2">{{ number_format($tx->balance_after) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Subtle background ambient decoration -->
            <div class="position-absolute" style="top: -30px; right: -30px; width: 140px; height: 140px; background: rgba(197, 168, 128, 0.1); border-radius: 50%; pointer-events: none; filter: blur(30px);"></div>
        </div>
    @endif

    <!-- Clean Enterprise Stat Cards (Monochromatic Elegance) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="appt-stat-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="appt-stat-label mb-1">Total Bookings</div>
                        <div class="appt-stat-number">{{ number_format($stats['total'] ?? 0) }}</div>
                    </div>
                    <div class="appt-stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="appt-stat-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="appt-stat-label mb-1">Upcoming</div>
                        <div class="appt-stat-number">{{ number_format($stats['upcoming'] ?? 0) }}</div>
                    </div>
                    <div class="appt-stat-icon" style="background: rgba(15, 23, 42, 0.06); color: #0f172a; border-color: #e2e8f0;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="appt-stat-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="appt-stat-label mb-1">Completed</div>
                        <div class="appt-stat-number">{{ number_format($stats['completed'] ?? 0) }}</div>
                    </div>
                    <div class="appt-stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #059669; border-color: rgba(16, 185, 129, 0.25);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="appt-stat-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="appt-stat-label mb-1">Cancelled</div>
                        <div class="appt-stat-number">{{ number_format($stats['cancelled'] ?? 0) }}</div>
                    </div>
                    <div class="appt-stat-icon" style="background: rgba(100, 116, 139, 0.1); color: #64748b; border-color: #e2e8f0;">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Appointments Table Container -->
    <div class="appt-card overflow-hidden">
        <!-- Filter Toolbar -->
        <div class="p-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3 bg-white">
            <div class="filter-pill-bar">
                <a href="{{ route('customer.appointments.index', ['salon_slug' => $salonSlug]) }}" 
                   class="filter-pill-item {{ empty($status) ? 'active' : '' }}">
                    All <span class="badge rounded-pill ms-1 {{ empty($status) ? 'bg-white text-dark' : 'bg-light text-muted' }}">{{ $stats['total'] ?? 0 }}</span>
                </a>
                <a href="{{ route('customer.appointments.index', ['salon_slug' => $salonSlug, 'status' => 'upcoming']) }}" 
                   class="filter-pill-item {{ $status === 'upcoming' ? 'active' : '' }}">
                    Upcoming <span class="badge rounded-pill ms-1 {{ $status === 'upcoming' ? 'bg-white text-dark' : 'bg-light text-muted' }}">{{ $stats['upcoming'] ?? 0 }}</span>
                </a>
                <a href="{{ route('customer.appointments.index', ['salon_slug' => $salonSlug, 'status' => 'completed']) }}" 
                   class="filter-pill-item {{ $status === 'completed' ? 'active' : '' }}">
                    Completed <span class="badge rounded-pill ms-1 {{ $status === 'completed' ? 'bg-white text-dark' : 'bg-light text-muted' }}">{{ $stats['completed'] ?? 0 }}</span>
                </a>
                <a href="{{ route('customer.appointments.index', ['salon_slug' => $salonSlug, 'status' => 'cancelled']) }}" 
                   class="filter-pill-item {{ $status === 'cancelled' ? 'active' : '' }}">
                    Cancelled <span class="badge rounded-pill ms-1 {{ $status === 'cancelled' ? 'bg-white text-dark' : 'bg-light text-muted' }}">{{ $stats['cancelled'] ?? 0 }}</span>
                </a>
            </div>

            <div class="d-none d-md-block text-muted x-small">
                Showing {{ $appointments->firstItem() ?? 0 }} - {{ $appointments->lastItem() ?? 0 }} of {{ $appointments->total() }} records
            </div>
        </div>

        <!-- Appointments Table -->
        <div class="table-responsive">
            <table class="table appt-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Stylist / Staff</th>
                        <th>Schedule</th>
                        <th>Status</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        @php
                            $st = $appointment->status;
                            $statusPillClass = 'status-pill-pending';
                            $statusIcon = 'fa-clock';
                            if ($st === 'completed') {
                                $statusPillClass = 'status-pill-completed';
                                $statusIcon = 'fa-check-circle';
                            } elseif ($st === 'confirmed') {
                                $statusPillClass = 'status-pill-confirmed';
                                $statusIcon = 'fa-calendar-check';
                            } elseif ($st === 'cancelled') {
                                $statusPillClass = 'status-pill-cancelled';
                                $statusIcon = 'fa-times-circle';
                            }
                        @endphp
                        <tr>
                            <!-- Service Details -->
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm rounded-3 d-flex align-items-center justify-content-center me-3" 
                                         style="width: 36px; height: 36px; background: var(--theme-accent-light, rgba(197, 168, 128, 0.12)); color: var(--theme-accent, #c5a880);">
                                        <i class="fas fa-cut"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $appointment->service->name ?? 'Standard Service' }}</div>
                                        <div class="text-muted x-small">
                                            <i class="far fa-clock me-1"></i>{{ $appointment->service->duration ?? 30 }} mins
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Staff Member -->
                            <td>
                                @if($appointment->staff)
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $appointment->staff->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($appointment->staff->name).'&background=f1f5f9&color=475569' }}"
                                             alt="{{ $appointment->staff->name }}" 
                                             class="rounded-circle me-2 border" 
                                             width="28" height="28" style="object-fit: cover;">
                                        <div>
                                            <span class="fw-semibold text-dark small">{{ $appointment->staff->name }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-light text-muted border rounded-pill px-2 py-1 x-small">
                                        <i class="fas fa-user-clock me-1"></i> Awaiting Staff
                                    </span>
                                @endif
                            </td>

                            <!-- Date & Time -->
                            <td>
                                <div class="text-dark fw-semibold small">
                                    {{ format_date($appointment->start_time) }}
                                </div>
                                <div class="text-muted x-small">
                                    <i class="far fa-clock me-1"></i>{{ format_time($appointment->start_time) }}
                                </div>
                            </td>

                            <!-- Status -->
                            <td>
                                <span class="status-pill {{ $statusPillClass }}">
                                    <i class="fas {{ $statusIcon }}"></i> {{ ucfirst($st) }}
                                </span>
                            </td>

                            <!-- Amount -->
                            <td class="text-end">
                                <div class="fw-bold text-dark" style="font-size: 0.92rem;">
                                    {{ currency_symbol() }}{{ number_format($appointment->amount, 2) }}
                                </div>
                                @if($appointment->payment_status === 'paid')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill x-small px-2 py-0.5">
                                        <i class="fas fa-check-circle me-1"></i> Paid
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border rounded-pill x-small px-2 py-0.5">
                                        Unpaid
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="text-end">
                                <div class="d-flex justify-content-end align-items-center gap-1.5 flex-nowrap">
                                    <a href="{{ route('customer.appointments.show', ['salon_slug' => $salonSlug, 'booking' => $appointment->id]) }}"
                                       class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 d-inline-flex align-items-center"
                                       style="font-size: 0.75rem; line-height: 1.2;"
                                       data-bs-toggle="tooltip" title="View Details">
                                        <i class="fas fa-eye me-1"></i> Details
                                    </a>

                                    @if($appointment->status === 'pending')
                                        <form method="POST"
                                              action="{{ route('customer.appointments.destroy', ['salon_slug' => $salonSlug, 'booking' => $appointment->id]) }}"
                                              class="d-inline m-0 p-0"
                                              onsubmit="return confirm('Are you sure you want to cancel this appointment?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 d-inline-flex align-items-center"
                                                    style="font-size: 0.75rem; line-height: 1.2;"
                                                    data-bs-toggle="tooltip" title="Cancel Appointment">
                                                <i class="fas fa-times me-1"></i> Cancel
                                            </button>
                                        </form>
                                    @endif

                                    @if($appointment->status === 'completed')
                                        @if(!$appointment->is_rated && !$appointment->rating && !$appointment->ratings()->exists())
                                            <button type="button" 
                                                    class="btn btn-sm rounded-pill px-2.5 py-1 fw-bold shadow-sm d-inline-flex align-items-center"
                                                    style="background: var(--theme-accent, #c5a880); color: #ffffff; font-size: 0.75rem; line-height: 1.2;"
                                                    onclick="showRatingModal({{ $appointment->id }})"
                                                    data-bs-toggle="tooltip" title="Rate Service">
                                                <i class="fas fa-star me-1"></i> Rate
                                            </button>
                                        @else
                                            @php
                                                $savedScore = $appointment->rating ?? optional($appointment->ratings()->first())->rating;
                                            @endphp
                                            <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1 d-inline-flex align-items-center"
                                                  style="font-size: 0.72rem; line-height: 1.2;"
                                                  data-bs-toggle="tooltip" title="{{ $savedScore ? 'Rated ' . $savedScore . '/5' : 'Rated' }}">
                                                <i class="fas fa-check-circle text-success me-1"></i> Rated
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-4">
                                    <div class="avatar avatar-lg rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3"
                                         style="width: 54px; height: 54px; background: rgba(15, 23, 42, 0.05); color: #94a3b8;">
                                        <i class="fas fa-calendar-times fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">No appointments found</h6>
                                    <p class="text-muted small mb-3">You don't have any {{ $status ? $status : '' }} appointments listed.</p>
                                    <a href="{{ route('customer.dashboard', ['salon_slug' => $salonSlug]) }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
                                        <i class="fas fa-calendar-plus me-1"></i> Book an Appointment
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($appointments->hasPages())
            <div class="p-3 border-top bg-white d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <div class="text-muted x-small">
                    Showing {{ $appointments->firstItem() }} to {{ $appointments->lastItem() }} of {{ $appointments->total() }} entries
                </div>
                <div>
                    {{ $appointments->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Rating Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 420px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark fs-6">
                    <i class="fas fa-star me-2" style="color: var(--theme-accent, #c5a880);"></i>Rate Your Experience
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <form id="ratingForm">
                    <input type="hidden" id="appointment_id" name="appointment_id">

                    <div class="text-center my-3">
                        <p class="text-muted small mb-2">How satisfied were you with this service?</p>
                        <div class="rating justify-content-center mb-1">
                            @for($i = 5; $i >= 1; $i--)
                                <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}">
                                <label for="star{{ $i }}" class="mx-1" title="{{ $i }} Stars">
                                    <i class="fas fa-star" style="font-size: 1.8rem;"></i>
                                </label>
                            @endfor
                        </div>
                        <div class="d-flex justify-content-between x-small text-muted px-4 mt-1">
                            <span>Poor</span>
                            <span>Excellent</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="comment" class="form-label x-small fw-bold text-muted text-uppercase">Feedback (Optional)</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3"
                                  placeholder="Tell us what you liked about your stylist or service..."
                                  style="font-size: 0.85rem; border-radius: 10px;"></textarea>
                    </div>

                    <div id="ratingError" class="alert alert-danger py-2 d-none x-small" role="alert">
                        <i class="fas fa-exclamation-circle me-1"></i> Please select a star rating
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm" id="submitRatingBtn" onclick="submitRating()">
                    <i class="fas fa-paper-plane me-1"></i> Submit Rating
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        const ratingModalEl = document.getElementById('ratingModal');
        if (ratingModalEl) {
            ratingModalEl.addEventListener('hidden.bs.modal', function () {
                const apptId = $('#appointment_id').val();
                if (apptId) {
                    try {
                        localStorage.setItem('feedback_modal_dismissed_' + apptId, '1');
                        sessionStorage.setItem('feedback_modal_dismissed_' + apptId, '1');
                    } catch(e) {}
                }
            });
        }

        @if(!empty($pendingFeedbackBooking))
            const feedbackDismissKey = 'feedback_modal_dismissed_{{ $pendingFeedbackBooking->id }}';
            if (!localStorage.getItem(feedbackDismissKey) && !sessionStorage.getItem(feedbackDismissKey)) {
                setTimeout(function () {
                    showRatingModal({{ $pendingFeedbackBooking->id }});
                }, 800);
            }
        @endif
    });

    function showRatingModal(appointmentId) {
        $('#ratingForm')[0].reset();
        $('#appointment_id').val(appointmentId);
        $('.rating input').prop('checked', false);
        $('#ratingError').addClass('d-none');

        try {
            localStorage.setItem('feedback_modal_dismissed_' + appointmentId, '1');
            sessionStorage.setItem('feedback_modal_dismissed_' + appointmentId, '1');
        } catch(e) {}

        const modalEl = document.getElementById('ratingModal');
        var ratingModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        ratingModal.show();
    }

    function submitRating() {
        const bookingId = $('#appointment_id').val();
        const rating = $('input[name="rating"]:checked').val();
        const comment = $('#comment').val();

        if (!rating) {
            $('#ratingError').removeClass('d-none');
            return;
        }

        try {
            localStorage.setItem('feedback_modal_dismissed_' + bookingId, '1');
            sessionStorage.setItem('feedback_modal_dismissed_' + bookingId, '1');
        } catch(e) {}

        const submitBtn = $('#submitRatingBtn');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Submitting...');
        $('#ratingError').addClass('d-none');

        const rateUrlTemplate = "{{ route('customer.appointments.rate', ['salon_slug' => $salonSlug, 'booking' => ':id']) }}";
        const targetUrl = rateUrlTemplate.replace(':id', bookingId);

        $.ajax({
            url: targetUrl,
            method: 'POST',
            data: {
                rating: rating,
                comment: comment,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                bootstrap.Modal.getInstance(document.getElementById('ratingModal')).hide();
                window.location.reload();
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> Submit Rating');
                const errorMsg = xhr.responseJSON?.message || 'Error submitting rating. Please try again.';
                $('#ratingError').removeClass('d-none').text(errorMsg);
            }
        });
    }
</script>
@endpush
