<style>
    .text-decoration-underline-hover:hover {
        text-decoration: underline !important;
    }
    .hover-underline-primary-hover:hover {
        color: #7c3aed !important;
        text-decoration: underline !important;
    }

    /* ===== MOBILE CARD STYLES for booking rows ===== */
    @media (max-width: 767.98px) {
        #bookingsList tr.bk-card-row {
            display: block !important;
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07), 0 1px 3px rgba(0,0,0,0.05);
            margin: 0 0 12px 0;
            padding: 0;
            border: none !important;
            overflow: hidden;
            transition: box-shadow 0.2s, transform 0.15s;
        }
        #bookingsList tr.bk-card-row:active {
            transform: scale(0.985);
            box-shadow: 0 1px 6px rgba(0,0,0,0.06);
        }
        #bookingsList td { display: none !important; }
        #bookingsList td.bk-mobile-card-td {
            display: block !important;
            padding: 0 !important;
            border: none !important;
        }
        #bookingsList { display: block !important; padding: 4px 2px 8px; }

        .bk-mob-card { padding: 14px 14px 10px; }
        .bk-mob-top  { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .bk-mob-avatar {
            width: 46px; height: 46px; border-radius: 14px;
            background: linear-gradient(135deg, #ede9fe, #ddd6fe);
            color: #7c3aed; font-weight: 700; font-size: 1.1rem;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; position: relative;
        }
        .bk-mob-name  { font-weight: 700; font-size: 0.92rem; color: #1e293b; line-height: 1.2; }
        .bk-mob-phone { font-size: 0.75rem; color: #94a3b8; margin-top: 1px; }
        .bk-mob-status { margin-left: auto; flex-shrink: 0; }
        .bk-mob-status .badge { font-size: 0.72rem; padding: 5px 10px; border-radius: 20px; font-weight: 600; }
        .bk-mob-divider { height: 1px; background: #f1f5f9; margin: 0 0 10px; }
        .bk-mob-meta  { display: flex; flex-wrap: wrap; gap: 5px 10px; margin-bottom: 10px; }
        .bk-mob-meta-item { display: flex; align-items: center; gap: 4px; font-size: 0.78rem; color: #475569; }
        .bk-mob-meta-item i { color: #a5b4fc; font-size: 0.72rem; width: 12px; }
        .bk-mob-meta-item .val { font-weight: 600; color: #1e293b; }
        .bk-mob-footer { display: flex; align-items: center; gap: 8px; border-top: 1px solid #f1f5f9; padding-top: 9px; }
        .bk-mob-id    { font-size: 0.72rem; color: #94a3b8; font-weight: 600; }
        .bk-mob-actions { margin-left: auto; display: flex; gap: 6px; }
        .bk-mob-action-btn {
            width: 32px; height: 32px; border-radius: 50%;
            border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.78rem; transition: background 0.15s;
        }
        .bk-mob-action-btn.bk-view-btn   { background: #eff6ff; color: #3b82f6; }
        .bk-mob-action-btn.bk-edit-btn   { background: #f0fdf4; color: #16a34a; }
        .bk-mob-action-btn.bk-cancel-btn { background: #fef2f2; color: #dc2626; }
        .bk-mob-action-btn.bk-confirm-btn{ background: #ecfdf5; color: #059669; }
    }
</style>

@if($bookings->count() > 0)
    @foreach($bookings as $booking)
        @php
            $statusConfig = [
                'pending'         => ['bg' => 'bg-warning-subtle',  'text' => 'text-warning-emphasis',  'icon' => 'fa-clock'],
                'confirmed'       => ['bg' => 'bg-success-subtle',  'text' => 'text-success-emphasis',  'icon' => 'fa-check-circle'],
                'completed'       => ['bg' => 'bg-primary-subtle',  'text' => 'text-primary-emphasis',  'icon' => 'fa-check-double'],
                'staff_completed' => ['bg' => 'bg-success-subtle',  'text' => 'text-success-emphasis',  'icon' => 'fa-check-double'],
                'cancelled'       => ['bg' => 'bg-danger-subtle',   'text' => 'text-danger-emphasis',   'icon' => 'fa-times-circle'],
                'no-show'         => ['bg' => 'bg-dark-subtle',     'text' => 'text-dark-emphasis',     'icon' => 'fa-user-slash'],
                'no_show'         => ['bg' => 'bg-dark-subtle',     'text' => 'text-dark-emphasis',     'icon' => 'fa-user-slash'],
            ];
            $config = $statusConfig[$booking->status] ?? ['bg' => 'bg-secondary-subtle', 'text' => 'text-secondary-emphasis', 'icon' => 'fa-circle'];
            $isVip  = isset($booking->customer->is_vip) && $booking->customer->is_vip;
        @endphp
        <tr data-booking-id="{{ $booking->id }}" class="bk-card-row align-middle border-bottom hover-bg-light transition-all">
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

            {{-- ===== MOBILE CARD (visible on mobile via CSS only) ===== --}}
            <td class="bk-mobile-card-td">
                <div class="bk-mob-card">
                    {{-- Top row: avatar + name/phone + status --}}
                    <div class="bk-mob-top">
                        <div class="bk-mob-avatar"
                            onclick="if(window.viewCustomer && {{ $booking->customer_id ?? 'null' }}) window.viewCustomer({{ $booking->customer_id }});"
                            style="cursor:pointer;">
                            {{ strtoupper(substr($booking->customer->name ?? 'G', 0, 1)) }}
                        </div>
                        <div style="min-width:0; flex:1;">
                            <div class="bk-mob-name"
                                onclick="if(window.showEditModal) window.showEditModal({{ $booking->id }});"
                                style="cursor:pointer;">
                                {{ $booking->customer->name ?? 'Guest' }}
                                @if($isVip)
                                    <span style="font-size:0.62rem;background:#fbbf24;color:#fff;border-radius:4px;padding:1px 5px;font-weight:700;vertical-align:middle;margin-left:3px;">VIP</span>
                                @endif
                            </div>
                            <div class="bk-mob-phone">
                                <i class="fas fa-phone-alt" style="font-size:0.65rem;margin-right:3px;"></i>
                                {{ \App\Helpers\CustomerDataHelper::getMaskedPhone($booking->customer) }}
                            </div>
                        </div>
                        <div class="bk-mob-status">
                            <span class="badge {{ $config['bg'] }} {{ $config['text'] }}">
                                <i class="fas {{ $config['icon'] }}" style="font-size:0.65rem;margin-right:3px;"></i>
                                {{ str_replace('_', ' ', ucfirst($booking->status)) }}
                            </span>
                        </div>
                    </div>

                    <div class="bk-mob-divider"></div>

                    {{-- Meta grid --}}
                    <div class="bk-mob-meta">
                        <div class="bk-mob-meta-item">
                            <i class="fas fa-cut"></i>
                            <span class="val">{{ $booking->service->name ?? 'Unknown' }}</span>
                        </div>
                        @if(isset($booking->service->price))
                        <div class="bk-mob-meta-item">
                            <i class="fas fa-tag"></i>
                            <span class="val">{{ currency_symbol() }}{{ number_format($booking->service->price, 2) }}</span>
                        </div>
                        @endif
                        @if($booking->staff)
                        <div class="bk-mob-meta-item">
                            <i class="fas fa-user-tie"></i>
                            <span class="val">{{ $booking->staff->name }}</span>
                        </div>
                        @endif
                        <div class="bk-mob-meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span class="val">{{ format_date($booking->start_time) }}</span>
                        </div>
                        <div class="bk-mob-meta-item">
                            <i class="fas fa-clock"></i>
                            <span class="val">{{ format_time($booking->start_time) }} – {{ format_time($booking->end_time) }}</span>
                        </div>
                        <div class="bk-mob-meta-item">
                            <i class="fas fa-hourglass-half"></i>
                            <span class="val">{{ $booking->service->duration ?? 0 }} min</span>
                        </div>
                    </div>

                    {{-- Footer: booking # + action buttons --}}
                    <div class="bk-mob-footer">
                        <span class="bk-mob-id">#{{ $booking->id }}</span>
                        <div class="bk-mob-actions">
                            <button class="bk-mob-action-btn bk-view-btn view-booking"
                                data-bs-toggle="modal" data-bs-target="#bookingDetailsModal"
                                data-booking-id="{{ $booking->id }}" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if($booking->status !== 'completed')
                            <button class="bk-mob-action-btn bk-edit-btn edit-booking"
                                data-booking-id="{{ $booking->id }}" title="Edit Booking">
                                <i class="fas fa-pen"></i>
                            </button>
                            @endif
                            @if($booking->status === 'pending')
                            <button class="bk-mob-action-btn bk-confirm-btn change-status"
                                data-booking-id="{{ $booking->id }}" data-status="confirmed" title="Confirm">
                                <i class="fas fa-check"></i>
                            </button>
                            @endif
                            @if($booking->status !== 'cancelled' && $booking->status !== 'completed')
                            <button class="bk-mob-action-btn bk-cancel-btn change-status"
                                data-booking-id="{{ $booking->id }}" data-status="cancelled" title="Cancel">
                                <i class="fas fa-times"></i>
                            </button>
                            @endif
                        </div>
                    </div>
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