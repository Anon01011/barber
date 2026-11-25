@extends('layouts.app')

@section('content')
    <div class="container-fluid py-3">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="h5 mb-0 text-gray-800"><i
                                    class="fas fa-calendar-check text-primary me-2"></i>{{ __('My Appointments') }}</h4>
                            {{-- <a href="{{ route('customer.book') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i>New Appointment
                            </a> --}}
                        </div>
                    </div>

                    <div class="card-body p-3">
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show py-2 mb-3" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(!empty($hasLoyalty))
                        <!-- Customer Loyalty Rewards Balance Card -->
                        <div class="card border-0 shadow-sm mb-3 text-white overflow-hidden" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%); border-radius: 12px;">
                            <div class="card-body p-3 p-md-4">
                                <div class="row align-items-center">
                                    <div class="col-md-7">
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="badge bg-warning text-dark px-2 py-1 rounded-pill fw-bold text-uppercase me-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                                <i class="fas fa-coins me-1"></i> Loyalty Rewards
                                            </span>
                                            <span class="text-white-50 small">Customer VIP Tier</span>
                                        </div>
                                        <h5 class="fw-bold mb-1 text-white">Your Loyalty Points Balance</h5>
                                        <p class="text-white-50 small mb-0">Earn points on every salon visit and redeem them for instant discounts at POS checkout.</p>
                                    </div>
                                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                        <div class="display-6 fw-bold text-warning mb-0">
                                            {{ number_format($loyaltyPoints ?? 0) }} <span class="fs-6 text-white-50">pts</span>
                                        </div>
                                        <div class="small text-white opacity-75">
                                            Approx. Value: <strong>{{ currency_symbol() }}{{ number_format($loyaltyValue ?? 0, 2) }}</strong>
                                            @if(($loyaltyPoints ?? 0) >= ($minPointsToRedeem ?? 50))
                                                <span class="badge bg-success text-white ms-1" style="font-size: 0.65rem;">Ready to Redeem</span>
                                            @else
                                                <span class="badge bg-secondary text-white-50 ms-1" style="font-size: 0.65rem;">Min {{ $minPointsToRedeem ?? 50 }} pts to redeem</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if(isset($loyaltyTransactions) && $loyaltyTransactions->count() > 0)
                                <div class="mt-3 pt-3 border-top border-white border-opacity-10">
                                    <button class="btn btn-sm text-white p-0 text-decoration-none fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#loyaltyHistoryCollapse" aria-expanded="false" aria-controls="loyaltyHistoryCollapse">
                                        <i class="fas fa-history me-1 text-warning"></i> View Points History <i class="fas fa-chevron-down ms-1 small"></i>
                                    </button>
                                    <div class="collapse mt-2" id="loyaltyHistoryCollapse">
                                        <div class="table-responsive rounded-2" style="background: rgba(0,0,0,0.25);">
                                            <table class="table table-sm table-borderless text-white mb-0" style="font-size: 0.8rem;">
                                                <thead>
                                                    <tr class="text-white-50 border-bottom border-white border-opacity-10">
                                                        <th class="ps-2">Date</th>
                                                        <th>Type</th>
                                                        <th>Points</th>
                                                        <th>Reason / Note</th>
                                                        <th class="pe-2 text-end">Balance</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($loyaltyTransactions as $tx)
                                                    <tr class="border-bottom border-white border-opacity-10">
                                                        <td class="ps-2 text-white-50">{{ $tx->created_at ? $tx->created_at->format('M d, Y') : '-' }}</td>
                                                        <td>
                                                            @if($tx->type === 'earned')
                                                                <span class="badge bg-success text-white" style="font-size: 0.65rem;">+ Earned</span>
                                                            @elseif($tx->type === 'redeemed')
                                                                <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">- Redeemed</span>
                                                            @else
                                                                <span class="badge bg-info text-white" style="font-size: 0.65rem;">{{ ucfirst($tx->type) }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="fw-bold {{ $tx->points > 0 ? 'text-success' : 'text-danger' }}">
                                                            {{ $tx->points > 0 ? '+' : '' }}{{ number_format($tx->points) }}
                                                        </td>
                                                        <td class="text-white-50 text-truncate" style="max-width: 180px;">{{ $tx->notes ?: ($tx->reference_type ? class_basename($tx->reference_type) : '-') }}</td>
                                                        <td class="pe-2 text-end font-monospace text-white">{{ number_format($tx->balance_after) }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Compact Statistics Cards -->
                        <div class="row g-2 mb-3">
                            <div class="col-6 col-md-3">
                                <div class="card bg-primary bg-opacity-10 border-0 h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-muted small mb-1">Total</h6>
                                                <h5 class="mb-0 text-primary">{{ $stats['total'] }}</h5>
                                            </div>
                                            <i class="fas fa-calendar-alt text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card bg-success bg-opacity-10 border-0 h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-muted small mb-1">Upcoming</h6>
                                                <h5 class="mb-0 text-success">{{ $stats['upcoming'] }}</h5>
                                            </div>
                                            <i class="fas fa-clock text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card bg-info bg-opacity-10 border-0 h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-muted small mb-1">Completed</h6>
                                                <h5 class="mb-0 text-info">{{ $stats['completed'] }}</h5>
                                            </div>
                                            <i class="fas fa-check-circle text-info"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card bg-danger bg-opacity-10 border-0 h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-muted small mb-1">Cancelled</h6>
                                                <h5 class="mb-0 text-danger">{{ $stats['cancelled'] }}</h5>
                                            </div>
                                            <i class="fas fa-times-circle text-danger"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Compact Filter Section -->
                        <div class="card border-0 mb-3 bg-light">
                            <div class="card-body p-2">
                                <form method="GET" action="{{ route('customer.appointments.index') }}"
                                    class="row g-2 align-items-center">
                                    <div class="col-md-4">
                                        <select name="status" id="status" class="form-select form-select-sm">
                                            <option value="">All Appointments</option>
                                            <option value="upcoming" {{ $status == 'upcoming' ? 'selected' : '' }}>Upcoming
                                            </option>
                                            <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed
                                            </option>
                                            <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fas fa-filter me-1"></i>Apply
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Compact Appointments Table -->
                        <div class="card border-0">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle" style="font-size: 0.85rem;">
                                    <thead class="bg-light">
                                        <tr class="small text-muted">
                                            <th class="py-1 px-2">Service</th>
                                            <th class="py-1 px-2">Staff</th>
                                            <th class="py-1 px-2">Date & Time</th>
                                            <th class="py-1 px-2">Status</th>
                                            <th class="py-1 px-2 text-end">Amount</th>
                                            <th class="py-1 px-2 text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @php
                                            $statusIcons = [
                                                'completed' => 'check-circle text-success',
                                                'cancelled' => 'times-circle text-danger',
                                                'confirmed' => 'check-circle text-info',
                                                'pending' => 'clock text-warning'
                                            ];

                                            $statusColors = [
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                                'confirmed' => 'info',
                                                'pending' => 'warning'
                                            ];
                                        @endphp

                                        @forelse($appointments as $appointment)
                                            <tr class="border-bottom">
                                                <td class="py-1 px-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-cut text-primary me-2" style="font-size: 0.9rem;"></i>
                                                        <div>
                                                            <div class="fw-medium" style="font-size: 0.85rem;">
                                                                {{ $appointment->service->name }}</div>
                                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                                {{ $appointment->service->duration }} min</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-1 px-2">
                                                    @if($appointment->staff)
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ $appointment->staff->profile_photo_url }}"
                                                                alt="{{ $appointment->staff->name }}" class="rounded-circle me-2"
                                                                width="24" height="24" style="object-fit: cover;">
                                                            <span style="font-size: 0.85rem;">{{ $appointment->staff->name }}</span>
                                                        </div>
                                                    @else
                                                        <span
                                                            class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25"
                                                            style="font-size: 0.75rem;">
                                                            <i class="fas fa-user-clock me-1"></i> Awaiting Staff
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-1 px-2">
                                                    <div style="font-size: 0.85rem;">
                                                        <div>{{ format_date($appointment->start_time) }}</div>
                                                        <div class="text-muted" style="font-size: 0.8rem;">
                                                            {{ format_time($appointment->start_time) }}</div>
                                                    </div>
                                                </td>
                                                <td class="py-1 px-2">
                                                    <div class="d-flex flex-column gap-1">
                                                        @php
                                                            $status = $appointment->status;
                                                            $statusColor = $statusColors[$status] ?? 'secondary';
                                                            $statusIcon = $statusIcons[$status] ?? 'circle';
                                                        @endphp
                                                        <span
                                                            class="badge d-inline-flex align-items-center bg-{{ $statusColor }}-subtle text-{{ $statusColor }} border border-{{ $statusColor }}-subtle rounded-pill"
                                                            style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                            <i class="fas {{ $statusIcon }} me-1"
                                                                style="font-size: 0.7rem;"></i>
                                                            {{ ucfirst($status) }}
                                                        </span>
                                                        @if($appointment->staff_assignment_status === 'pending')
                                                            <span
                                                                class="badge d-inline-flex align-items-center bg-warning-subtle text-warning border border-warning-subtle rounded-pill"
                                                                style="font-size: 0.7rem; padding: 0.2rem 0.4rem;">
                                                                <i class="fas fa-user-clock me-1" style="font-size: 0.65rem;"></i>
                                                                Pending
                                                            </span>
                                                        @elseif($appointment->staff_assignment_status === 'assigned')
                                                            <span
                                                                class="badge d-inline-flex align-items-center bg-success-subtle text-white border border-success-subtle rounded-pill"
                                                                style="font-size: 0.7rem; padding: 0.2rem 0.4rem;">
                                                                <i class="fas fa-user-check me-1" style="font-size: 0.65rem;"></i>
                                                                Assigned
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="py-1 px-2 text-end">
                                                    <div class="fw-medium" style="font-size: 0.9rem;">
                                                        {{ currency_symbol() }}{{ number_format($appointment->amount, 2) }}
                                                    </div>
                                                    @if($appointment->payment_status === 'paid')
                                                        <span
                                                            class="badge bg-success-subtle text-white border border-success-subtle rounded-pill"
                                                            style="font-size: 0.7rem; padding: 0.2rem 0.4rem;">
                                                            <i class="fas fa-check-circle me-1" style="font-size: 0.65rem;"></i>
                                                            Paid
                                                        </span>
                                                    @else
                                                        <span
                                                            class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill"
                                                            style="font-size: 0.7rem; padding: 0.2rem 0.4rem;">
                                                            <i class="fas fa-clock me-1" style="font-size: 0.65rem;"></i>
                                                            Pending
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-1 px-2 text-end">
                                                    <div class="d-flex justify-content-end gap-1">
                                                        <a href="{{ route('customer.appointments.show', $appointment) }}"
                                                            class="btn btn-sm btn-outline-secondary px-2"
                                                            data-bs-toggle="tooltip" title="View Details"
                                                            style="font-size: 0.75rem;">
                                                            <i class="fas fa-eye fa-fw"></i>
                                                        </a>
                                                        @if($appointment->status == 'pending')
                                                            <form method="POST"
                                                                action="{{ route('customer.appointments.destroy', $appointment) }}"
                                                                class="d-inline"
                                                                onsubmit="return confirm('Cancel this appointment?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2"
                                                                    data-bs-toggle="tooltip" title="Cancel Appointment"
                                                                    style="font-size: 0.75rem;">
                                                                    <i class="fas fa-times fa-fw"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        @if($appointment->status === 'completed')
                                                            @if(!$appointment->is_rated && !$appointment->rating && !$appointment->ratings()->exists())
                                                                <button type="button" class="btn btn-sm btn-warning text-dark fw-bold px-2 py-1 shadow-sm"
                                                                    onclick="showRatingModal({{ $appointment->id }})"
                                                                    data-bs-toggle="tooltip" title="Rate Service"
                                                                    style="font-size: 0.75rem; border-radius: 6px;">
                                                                    <i class="fas fa-star text-dark me-1"></i> Rate
                                                                </button>
                                                            @else
                                                                <button type="button" class="btn btn-sm btn-light text-muted border px-2 py-1 shadow-none" disabled
                                                                    data-bs-toggle="tooltip" title="Already Rated {{ $appointment->rating ?? 5 }}/5"
                                                                    style="font-size: 0.75rem; border-radius: 6px;">
                                                                    <i class="fas fa-check-circle text-success me-1"></i> Rated ({{ $appointment->rating ?? 5 }}/5 &#9733;)
                                                                </button>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <div class="text-muted py-4">
                                                        <i class="fas fa-calendar-times fa-2x mb-3 opacity-25"></i>
                                                        <p class="mb-0">No appointments found</p>
                                                        {{-- <a href="{{ route('customer.book') }}"
                                                            class="btn btn-sm btn-primary mt-2">
                                                            <i class="fas fa-plus me-1"></i>New Appointment
                                                        </a> --}}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if($appointments->hasPages())
                                <div class="card-footer bg-white border-0 py-2 px-3">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                                        <div class="text-muted small mb-2 mb-md-0">
                                            Showing {{ $appointments->firstItem() }} to {{ $appointments->lastItem() }} of
                                            {{ $appointments->total() }} entries
                                        </div>
                                        <div class="d-flex">
                                            {{ $appointments->onEachSide(1)->links('pagination::bootstrap-5') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating Modal -->
    <div class="modal fade" id="ratingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title"><i class="fas fa-star text-warning me-2"></i>Rate Your Experience</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <form id="ratingForm">
                        <input type="hidden" id="appointment_id" name="appointment_id">

                        <div class="text-center mb-4">
                            <p class="text-muted mb-3">How would you rate your experience?</p>
                            <div class="rating justify-content-center mb-2">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}">
                                    <label for="star{{ $i }}" class="mx-1">
                                        <i class="fas fa-star" style="font-size: 2rem;"></i>
                                    </label>
                                @endfor
                            </div>
                            <div class="d-flex justify-content-between small text-muted w-75 mx-auto">
                                <span>Poor</span>
                                <span>Excellent</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="comment" class="form-label small text-muted">Share your experience
                                (optional)</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3"
                                placeholder="What did you like or dislike about the service?"></textarea>
                        </div>

                        <div id="ratingError" class="alert alert-danger py-2 d-none" role="alert">
                            <i class="fas fa-exclamation-circle me-1"></i> Please select a rating
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-sm" id="submitRatingBtn" onclick="submitRating()">
                        <i class="fas fa-paper-plane me-1"></i>Submit Rating
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Compact rating stars */
            .rating {
                display: flex;
                flex-direction: row-reverse;
                justify-content: flex-start;
            }

            .rating input {
                display: none;
            }

            .rating label {
                cursor: pointer;
                font-size: 1.25rem;
                color: #e9ecef;
                padding: 0 0.1em;
                transition: color 0.2s;
            }

            .rating input:checked~label,
            .rating label:hover,
            .rating label:hover~label {
                color: #ffc107;
            }

            /* Hover effects */
            .card-hover {
                transition: all 0.2s ease;
            }

            .card-hover:hover {
                transform: translateY(-1px);
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05) !important;
            }

            /* Table styling */
            .table {
                --bs-table-hover-bg: rgba(0, 0, 0, 0.02);
                margin-bottom: 0;
            }

            .table th {
                font-weight: 600;
                font-size: 0.7rem;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                color: #6c757d;
                border-bottom-width: 1px;
                white-space: nowrap;
            }

            .table td {
                vertical-align: middle;
                padding: 0.5rem 0.5rem;
            }

            .table> :not(:first-child) {
                border-top: 0;
            }

            /* Badges */
            .badge {
                font-weight: 500;
                letter-spacing: 0.3px;
                display: inline-flex;
                align-items: center;
            }

            /* Pagination */
            .pagination {
                margin-bottom: 0;
            }

            .page-link {
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
                min-width: 32px;
                text-align: center;
            }

            /* Buttons */
            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .card-body {
                    padding: 0.75rem;
                }

                .table-responsive {
                    font-size: 0.875rem;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Initialize tooltips
            document.addEventListener('DOMContentLoaded', function () {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                const ratingModalEl = document.getElementById('ratingModal');
                if (ratingModalEl) {
                    ratingModalEl.addEventListener('hidden.bs.modal', function () {
                        const apptId = $('#appointment_id').val();
                        if (apptId) {
                            try {
                                sessionStorage.setItem('feedback_modal_dismissed_' + apptId, '1');
                            } catch(e) {}
                        }
                    });
                }

                @if(!empty($pendingFeedbackBooking))
                // Auto show feedback popup once per session for recent completed unrated appointment
                const feedbackDismissKey = 'feedback_modal_dismissed_{{ $pendingFeedbackBooking->id }}';
                if (!sessionStorage.getItem(feedbackDismissKey)) {
                    setTimeout(function () {
                        showRatingModal({{ $pendingFeedbackBooking->id }});
                    }, 800);
                }
                @endif
            });

            function showRatingModal(appointmentId) {
                $('#appointment_id').val(appointmentId);
                // Reset form
                $('#ratingForm')[0].reset();
                $('.rating input').prop('checked', false);

                // Show modal
                var ratingModal = new bootstrap.Modal(document.getElementById('ratingModal'));
                ratingModal.show();
            }

            function submitRating() {
                const bookingId = $('#appointment_id').val();
                const rating = $('input[name="rating"]:checked').val();
                const comment = $('#comment').val();

                if (!rating) {
                    // Show error in modal instead of alert
                    $('#ratingError').removeClass('d-none');
                    return;
                }

                try {
                    sessionStorage.setItem('feedback_modal_dismissed_' + bookingId, '1');
                } catch(e) {}

                // Disable submit button and show loading state
                const submitBtn = $('#submitRatingBtn');
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Submitting...');

                // Clear any previous errors
                $('#ratingError').addClass('d-none');

                const rateUrlTemplate = "{{ route('customer.appointments.rate', ['salon_slug' => request()->route('salon_slug') ?? optional(auth()->user()->salon)->slug ?? 'default', 'booking' => ':id']) }}";
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
                        // Show success message
                        const alert = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>Thank you for your rating!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>`;

                        // Close modal after a short delay
                        bootstrap.Modal.getInstance(document.getElementById('ratingModal')).hide();

                        // Show success message and reload
                        $('.card-body').prepend(alert);
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    },
                    error: function (xhr) {
                        // Re-enable button on error
                        submitBtn.prop('disabled', false).html('Submit Rating');

                        // Show error in modal
                        const errorMsg = xhr.responseJSON?.message || 'Error submitting rating. Please try again.';
                        $('#ratingError').removeClass('d-none').text(errorMsg);
                    }
                });
            }
        </script>
    @endpush
@endsection