<style>
    .text-decoration-underline-hover:hover {
        text-decoration: underline !important;
    }
    .hover-underline-primary-hover:hover {
        color: #7c3aed !important;
        text-decoration: underline !important;
    }
</style>

@if($bookings->count() > 0)
    @foreach($bookings as $booking)
        <tr data-booking-id="{{ $booking->id }}" class="align-middle border-bottom hover-bg-light transition-all">
            <!-- Appt. Nr Column (Direct Edit Booking) -->
            <td class="ps-4" style="cursor: pointer;" onclick="if(window.showEditModal) window.showEditModal({{ $booking->id }});" title="Click to Edit Booking #{{ $booking->id }}">
                <span class="text-secondary fw-semibold small text-decoration-underline-hover">#{{ $booking->id }}</span>
            </td>

            <!-- Customer Column (Direct View Customer Details) -->
            <td style="cursor: pointer;" onclick="if(window.viewCustomer && {{ $booking->customer_id ?? 'null' }}) window.viewCustomer({{ $booking->customer_id }});" title="Click to View Customer Details">
                <div class="d-flex align-items-center py-2">
                    <div class="avatar avatar-md me-3 position-relative">
                        <span class="avatar-initial rounded-circle bg-primary-subtle text-primary fw-bold shadow-sm" style="width: 45px; height: 45px; font-size: 1.1rem; display: flex; align-items: center; justify-content: center;">
                            {{ substr($booking->customer->name ?? 'Guest', 0, 1) }}
                        </span>
                        @if(isset($booking->customer->is_vip) && $booking->customer->is_vip)
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-warning border border-light rounded-circle">
                                <span class="visually-hidden">VIP</span>
                            </span>
                        @endif
                    </div>
                    <div class="d-flex flex-column">
                        <h6 class="mb-0 text-dark fw-bold text-decoration-underline-hover">{{ $booking->customer->name ?? 'Guest' }}</h6>
                        <div class="d-flex flex-column small">
                            <span class="text-muted">
                                <i class="fas fa-phone-alt me-1" style="font-size: 0.75rem;"></i>
                                {{ \App\Helpers\CustomerDataHelper::getMaskedPhone($booking->customer) }}
                            </span>
                            @if($booking->customer && $booking->customer->email)
                                <span class="text-muted">
                                    <i class="fas fa-envelope me-1" style="font-size: 0.75rem;"></i>
                                    {{ \App\Helpers\CustomerDataHelper::getMaskedEmail($booking->customer) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </td>

            <!-- Service Column (Direct Navigation to Service Categories) -->
            <td style="cursor: pointer;" onclick="window.location.href='{{ route('admin.services.categories.index') }}';" title="Click to Manage Service Categories">
                <div class="d-flex flex-column">
                    <span class="fw-semibold text-dark hover-underline-primary-hover">{{ $booking->service->name ?? 'Unknown Service' }}</span>
                    @if(isset($booking->service->price))
                        <small class="text-muted">{{ currency_symbol() }}{{ number_format($booking->service->price, 2) }}</small>
                    @endif
                </div>
            </td>

            <!-- Staff Column (Direct View Staff details and services) -->
            <td style="cursor: pointer;" onclick="if(window.viewStaffDetails && {{ $booking->staff_id ?? 'null' }}) window.viewStaffDetails({{ $booking->staff_id }});" title="Click to View Staff Details">
                @if($booking->staff)
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-xs me-2">
                            <span class="avatar-initial rounded-circle bg-light text-secondary border d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                {{ substr($booking->staff->name, 0, 1) }}
                            </span>
                        </div>
                        <span class="text-dark fw-medium small hover-underline-primary-hover">{{ $booking->staff->name }}</span>
                    </div>
                @else
                    <span class="badge bg-light text-secondary border fw-normal">Unassigned</span>
                @endif
            </td>

            <!-- Date & Time Column (Direct Edit Booking) -->
            <td style="cursor: pointer;" onclick="if(window.showEditModal) window.showEditModal({{ $booking->id }});" title="Click to Edit Booking #{{ $booking->id }}">
                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center mb-1">
                        <i class="fas fa-calendar-alt text-muted small me-2" style="width: 14px;"></i>
                        <span class="fw-semibold text-dark text-decoration-underline-hover">{{ format_date($booking->start_time) }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock text-muted small me-2" style="width: 14px;"></i>
                        <span class="small text-muted">{{ format_time($booking->start_time) }} - {{ format_time($booking->end_time) }}</span>
                    </div>
                </div>
            </td>

            <!-- Duration Column (Direct Edit Booking) -->
            <td style="cursor: pointer;" onclick="if(window.showEditModal) window.showEditModal({{ $booking->id }});" title="Click to Edit Booking #{{ $booking->id }}">
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-pill fw-medium">
                    {{ $booking->service->duration ?? 0 }} mins
                </span>
            </td>

            <!-- Status Column (Direct Edit Booking) -->
            <td style="cursor: pointer;" onclick="if(window.showEditModal) window.showEditModal({{ $booking->id }});" title="Click to Edit Booking #{{ $booking->id }}">
                @php
                    $statusConfig = [
                        'pending' => ['bg' => 'bg-warning-subtle', 'text' => 'text-warning-emphasis', 'icon' => 'fa-clock'],
                        'confirmed' => ['bg' => 'bg-success-subtle', 'text' => 'text-success-emphasis', 'icon' => 'fa-check-circle'],
                        'completed' => ['bg' => 'bg-primary-subtle', 'text' => 'text-primary-emphasis', 'icon' => 'fa-check-double'],
                        'cancelled' => ['bg' => 'bg-danger-subtle', 'text' => 'text-danger-emphasis', 'icon' => 'fa-times-circle'],
                        'no-show' => ['bg' => 'bg-dark-subtle', 'text' => 'text-dark-emphasis', 'icon' => 'fa-user-slash'],
                    ];
                    $config = $statusConfig[$booking->status] ?? ['bg' => 'bg-secondary-subtle', 'text' => 'text-secondary-emphasis', 'icon' => 'fa-circle'];
                @endphp
                <span class="badge {{ $config['bg'] }} {{ $config['text'] }} border border-0 px-3 py-2 rounded-pill d-inline-flex align-items-center gap-2">
                    <i class="fas {{ $config['icon'] }} small"></i>
                    <span class="text-capitalize fw-semibold">{{ str_replace('_', ' ', $booking->status) }}</span>
                </span>
            </td>

            <!-- Actions Column -->
            <td class="text-end pe-4">
                <div class="dropdown">
                    <button class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px;">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 rounded-3">
                        <li>
                            <a class="dropdown-item rounded-2 py-2 view-booking" href="#" data-bs-toggle="modal"
                                data-bs-target="#bookingDetailsModal" data-booking-id="{{ $booking->id }}">
                                <i class="fas fa-eye me-2 text-primary w-20"></i>View Details
                            </a>
                        </li>
                        @if($booking->status !== 'completed')
                            <li>
                                <a class="dropdown-item rounded-2 py-2 edit-booking" href="#" data-booking-id="{{ $booking->id }}">
                                    <i class="fas fa-edit me-2 text-info w-20"></i>Edit Booking
                                </a>
                            </li>
                        @endif
                        <li><hr class="dropdown-divider my-1"></li>
                        @if($booking->status !== 'cancelled' && $booking->status !== 'completed')
                            <li>
                                <a class="dropdown-item rounded-2 py-2 text-danger change-status" href="#" data-booking-id="{{ $booking->id }}"
                                    data-status="cancelled">
                                    <i class="fas fa-ban me-2 w-20"></i>Cancel Booking
                                </a>
                            </li>
                        @endif
                        @if($booking->status === 'pending')
                            <li>
                                <a class="dropdown-item rounded-2 py-2 text-success change-status" href="#" data-booking-id="{{ $booking->id }}"
                                    data-status="confirmed">
                                    <i class="fas fa-check me-2 w-20"></i>Confirm Booking
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="8" class="text-center py-5">
            <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                <div class="bg-light rounded-circle p-4 mb-3">
                    <i class="fas fa-calendar-times fa-2x text-secondary"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">No bookings found</h6>
                <p class="small mb-0">Try adjusting your filters or search criteria</p>
            </div>
        </td>
    </tr>
@endif