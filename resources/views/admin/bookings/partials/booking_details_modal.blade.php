@php
    $modalSalonSlug = $salonSlug ?? request()->route('salon_slug') ?? optional(auth()->user()?->salon)->slug ?? (app()->bound('current_salon') ? optional(app('current_salon'))->slug : null) ?? session('current_salon_slug') ?? 'default';
@endphp

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-labelledby="bookingDetailsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header -->
            <div class="modal-header border-bottom py-3 px-4 bg-white">
                <h5 class="modal-title fw-bold text-dark" id="bookingDetailsModalLabel">
                    <span id="bookingCustomerHeader"
                        style="color: #000 !important; font-weight: bold !important;">Customer Name</span>
                    <span class="fw-normal ms-2" id="bookingCustomerPhoneHeader"
                        style="color: #000 !important; font-weight: bold !important;">(Phone)</span>
                    <span id="bookingStatusBadge" class="ms-2"></span>
                    <span id="groupBookingBadge" class="badge text-white ms-2 d-none" style="background-color: #7c3aed !important; font-size: 0.72rem !important; padding: 3px 10px !important; border-radius: 9999px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 5px !important; vertical-align: middle !important; font-weight: 600 !important; line-height: 1 !important;">
                        <i class="fas fa-users" style="font-size: 0.72rem !important; width: 12px !important; height: 12px !important; line-height: 1 !important; color: #fff !important; fill: #fff !important; flex-shrink: 0 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important;"></i>
                        <span>Group Booking (<span id="groupBookingCount">0</span> services)</span>
                    </span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Left Column: Details -->
                    <div class="col-md-8 p-4 border-end">
                        <div class="booking-details-list">
                            <!-- Appointment Nr -->
                            <div class="row mb-3">
                                <div class="col-4 text-muted fw-medium">Appointment Nr :</div>
                                <div class="col-8 fw-medium text-dark" id="bookingIdDisplay"></div>
                            </div>
                            <!-- Service Name -->
                            <div class="row mb-3">
                                <div class="col-4 text-muted fw-medium">Service Name :</div>
                                <div class="col-8 fw-medium text-dark" id="bookingService"></div>
                            </div>

                            <!-- Package Name (only shown if service is from package) -->
                            <div class="row mb-3 d-none" id="bookingPackageRow">
                                <div class="col-4 text-muted fw-medium">Package :</div>
                                <div class="col-8 fw-medium text-dark">
                                    <span id="bookingPackageName"></span>
                                    <span class="badge bg-success text-white ms-2" style="font-size: 0.7rem;"
                                        id="bookingPackageType"></span>
                                </div>
                            </div>

                            <!-- Service Price -->
                            <div class="row mb-3">
                                <div class="col-4 text-muted fw-medium">Service Price :</div>
                                <div class="col-8 fw-medium text-dark" id="bookingServicePrice"></div>
                            </div>

                            <!-- Time -->
                            <div class="row mb-3">
                                <div class="col-4 text-muted fw-medium">Time :</div>
                                <div class="col-8 fw-medium text-dark">
                                    <span id="bookingTimeRange"></span>
                                </div>
                            </div>

                            <!-- Service Duration -->
                            <div class="row mb-3">
                                <div class="col-4 text-muted fw-medium">Service Duration :</div>
                                <div class="col-8 fw-medium text-dark" id="bookingServiceDuration"></div>
                            </div>

                            <!-- Stylist -->
                            <div class="row mb-3">
                                <div class="col-4 text-muted fw-medium">Stylist / Staff :</div>
                                <div class="col-8 fw-medium text-dark d-flex align-items-center flex-wrap gap-2" id="bookingStaffWrapper">
                                    <span id="bookingStaff"></span>
                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-0.5 d-none" id="quickAssignStaffBtn" onclick="toggleStaffAssignDropdown()" style="font-size: 0.75rem;">
                                        <i class="fas fa-user-plus me-1"></i> Assign Staff
                                    </button>
                                </div>
                                <!-- Quick Staff Assign Dropdown Container -->
                                <div class="col-12 mt-2 d-none" id="staffAssignContainer">
                                    <div class="p-2.5 rounded-3 bg-light border d-flex align-items-center gap-2">
                                        <select class="form-select form-select-sm" id="assignStaffSelect">
                                            <option value="">Choose Staff Member...</option>
                                        </select>
                                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 text-nowrap" id="confirmAssignStaffBtn" onclick="submitStaffAssignment()">
                                            <i class="fas fa-check me-1"></i> Save
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2" onclick="toggleStaffAssignDropdown()">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Resource -->
                            <div class="row mb-3">
                                <div class="col-4 text-muted fw-medium">Resource :</div>
                                <div class="col-8 fw-medium text-dark">-Nil-</div>
                            </div>

                            <!-- Booking Type -->
                            <div class="row mb-3">
                                <div class="col-4 text-muted fw-medium">Booking Type :</div>
                                <div class="col-8 fw-medium text-dark" id="bookingType"></div>
                            </div>

                            <!-- Payment Status -->
                            <div class="row mb-3">
                                <div class="col-4 text-muted fw-medium">Payment Status :</div>
                                <div class="col-8 fw-medium text-dark" id="bookingPaymentStatus"></div>
                            </div>

                            <!-- Payment Method -->
                            <div class="row mb-3 d-none" id="bookingPaymentMethodRow">
                                <div class="col-4 text-muted fw-medium">Payment Method :</div>
                                <div class="col-8 fw-medium text-dark" id="bookingPaymentMethod"></div>
                            </div>

                            <!-- Booking Note -->
                            <div class="row mb-3">
                                <div class="col-4 text-muted fw-medium">Booking Note :</div>
                                <div class="col-8 fw-medium text-dark" id="bookingNotes">-Nil-</div>
                            </div>

                            <!-- Created Date -->
                            <div class="row mb-3">
                                <div class="col-4 text-muted fw-medium">Created Date :</div>
                                <div class="col-8 fw-medium text-dark" id="bookingCreatedDate"></div>
                            </div>

                            <!-- Created By -->
                            <div class="row mb-3">
                                <div class="col-4 text-muted fw-medium">Created By :</div>
                                <div class="col-8 fw-medium text-dark" id="bookingCreatedBy"></div>
                            </div>

                            <!-- Address -->
                            <div class="row mb-3">
                                <div class="col-4 text-muted fw-medium">Address :</div>
                                <div class="col-8 fw-medium text-dark" id="bookingAddress"></div>
                            </div>

                            <!-- Group Booking Services & Staff Breakdown -->
                            <div id="groupBookingSection" class="mt-3 p-3 rounded-3 d-none" style="background: rgba(124, 58, 237, 0.05); border: 1.5px solid rgba(124, 58, 237, 0.2);">
                                <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                                    <h6 class="mb-0 fw-bold d-flex align-items-center gap-1.5" style="color: #6d28d9; font-size: 0.88rem;">
                                        <i class="fas fa-users" style="font-size: 0.82rem;"></i> Group Booking — Services &amp; Assigned Staff
                                    </h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <div id="groupBulkAssignContainer" class="d-none">
                                            <select class="form-select form-select-sm py-0 px-2" id="groupBulkAssignSelect"
                                                    style="font-size: 0.72rem; height: 26px; border-radius: 6px; border: 1px solid #c4b5fd; background: #fff;"
                                                    onchange="bulkAssignAllGroupServices(this.value)">
                                                <option value="">Assign All Services To...</option>
                                            </select>
                                        </div>
                                        <span class="badge text-white rounded-pill px-2.5 py-1" style="background-color: #7c3aed;" id="groupTotalBadge"></span>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless align-middle mb-0" style="font-size: 0.82rem;">
                                        <thead class="text-muted border-bottom" style="font-size: 0.75rem;">
                                            <tr>
                                                <th class="ps-1">Service</th>
                                                <th style="white-space: nowrap;">Time</th>
                                                <th>Assigned Staff</th>
                                                <th class="text-end" style="white-space: nowrap;">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody id="groupBookingsTableBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Status Actions -->
                    <div class="col-md-4 p-4 bg-light">
                        <div class="d-grid gap-2 status-actions">
                            <button class="btn btn-outline-primary text-start position-relative status-btn"
                                data-status="pending" disabled>
                                New in-app booking
                                <span class="status-indicator bg-primary"></span>
                            </button>
                            <button class="btn btn-outline-purple text-start position-relative status-btn"
                                data-status="online_pending" disabled>
                                New online booking
                                <span class="status-indicator bg-purple"></span>
                            </button>
                            <button class="btn btn-outline-info text-start position-relative status-btn"
                                data-status="confirm" onclick="updateBookingStatus('confirm')">
                                Confirm
                                <span class="status-indicator bg-info"></span>
                            </button>
                            <button class="btn btn-outline-warning text-start position-relative status-btn"
                                data-status="arrived" onclick="updateBookingStatus('arrived')">
                                Arrived
                                <span class="status-indicator bg-warning"></span>
                            </button>
                            <button class="btn btn-outline-info text-start position-relative status-btn"
                                data-status="started" onclick="updateBookingStatus('started')">
                                Employee Started
                                <span class="status-indicator bg-info"></span>
                            </button>
                            <button class="btn btn-outline-danger text-start position-relative status-btn"
                                data-status="no_show" onclick="updateBookingStatus('no_show')">
                                No show
                                <span class="status-indicator bg-danger"></span>
                            </button>
                            <button class="btn btn-outline-success text-start position-relative status-btn"
                                data-status="staff_completed" onclick="updateBookingStatus('staff_completed')">
                                Employee Completed
                                <span class="status-indicator bg-success"></span>
                            </button>
                            <button class="btn btn-outline-success text-start position-relative status-btn d-none"
                                data-status="completed" onclick="updateBookingStatus('completed')">
                                Completed
                                <span class="status-indicator bg-success"></span>
                            </button>
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-outline-primary w-100 d-none" id="printBookingBtn">
                                Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-top py-3 px-4 bg-white justify-content-between">
                <button type="button" class="btn btn-danger" id="cancelBookingBtn">
                    Cancel Appointment
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" id="freezeBookingBtn">Freeze</button>
                    <button type="button" class="btn btn-outline-primary" id="editBookingBtn">Edit</button>
                    <div class="d-flex gap-2" id="printReceiptWrapper" style="display:none!important;">
                        <div class="btn-group d-none" id="printReceiptBtnGroup">
                            <button type="button" class="btn btn-success" id="printReceiptBtn">
                                <i class="fas fa-print me-2"></i>Print Receipt
                            </button>
                            <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="visually-hidden">Size options</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 190px;">
                                <li><a class="dropdown-item" id="printReceipt80mm" href="#"><i class="fas fa-print me-2 small"></i>80mm Thermal (Standard)</a></li>
                                <li><a class="dropdown-item" id="printReceipt58mm" href="#"><i class="fas fa-print me-2 small"></i>58mm Thermal (Mini)</a></li>
                                <li><a class="dropdown-item" id="printReceiptA4" href="#"><i class="fas fa-file-alt me-2 small"></i>A4 Full Page</a></li>
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" id="raiseSaleBtn">Raise Sale</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #bookingDetailsModal .modal-lg {
        max-width: 900px;
    }

    #bookingDetailsModal .booking-details-list {
        font-size: 0.95rem;
    }

    #bookingDetailsModal .status-btn {
        padding: 0.75rem 1rem;
        background: white;
        border: 1px solid #dee2e6;
        color: #495057;
        transition: all 0.2s;
    }

    #bookingDetailsModal .status-btn:hover:not(:disabled) {
        background-color: #f8f9fa;
        border-color: #ccedff;
    }

    #bookingDetailsModal .status-btn.active {
        background-color: #e6f7ff;
        border-color: #1890ff;
        color: #1890ff;
        font-weight: 600;
    }

    #bookingDetailsModal .status-indicator {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        width: 12px;
        height: 12px;
        border-radius: 4px;
    }

    #bookingDetailsModal .bg-purple {
        background-color: var(--theme-accent, #c5a880) !important;
    }

    #bookingDetailsModal .btn-outline-purple {
        color: var(--theme-accent, #c5a880);
        border-color: rgba(197, 168, 128, 0.45);
    }

    #bookingDetailsModal .btn-outline-purple:hover {
        background-color: rgba(197, 168, 128, 0.15);
        border-color: var(--theme-accent, #c5a880);
    }

    #groupBookingBadge i,
    #groupBookingBadge svg {
        font-size: 11px !important;
        width: 12px !important;
        height: 12px !important;
        max-width: 12px !important;
        max-height: 12px !important;
        line-height: 1 !important;
        color: #ffffff !important;
        fill: #ffffff !important;
        display: inline-block !important;
        vertical-align: middle !important;
        flex-shrink: 0 !important;
    }
</style>

@push('scripts')
    <script>
        // Global function to update booking status
        window.updateBookingStatus = function (status) {
            const bookingId = document.getElementById('bookingDetailsModal').getAttribute('data-booking-id');
            if (!bookingId) return;

            // Show loading state
            const btn = document.querySelector(`button[data-status="${status}"]`);
            let originalText = '';
            if (btn) {
                originalText = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
                btn.disabled = true;
            }

            const bladeFallbackSlug = "{{ $modalSalonSlug }}";
            const currentSalonSlug = (typeof getSalonSlug === 'function' ? getSalonSlug() : '') 
                || bladeFallbackSlug 
                || (window.location.pathname.split('/').filter(Boolean)[0] || 'default');
            const url = `/${currentSalonSlug}/admin/bookings/${bookingId}/status`;

            fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: status })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI
                        document.querySelectorAll('.status-btn').forEach(b => {
                            b.classList.remove('active');
                            b.disabled = false;
                        });

                        // Re-fetch booking details to update modal and all status buttons
                        if (typeof window.openBookingDetailsModal === 'function') {
                            window.openBookingDetailsModal(bookingId);
                        }

                        // Refresh calendar events
                        if (window.calendar) {
                            window.calendar.refetchEvents();
                        }

                        // Show success toast
                        if (typeof Swal !== 'undefined') {
                            const isFreeze = (status === 'frozen');
                            Swal.fire({
                                icon: 'success',
                                title: isFreeze ? 'Appointment Frozen' : 'Status Updated',
                                text: data.message || `Booking status updated to ${status}.`,
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2500
                            });
                        }
                    } else {
                        throw new Error(data.message || 'Failed to update status');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: error.message || 'Error updating status'
                    });
                })
                .finally(() => {
                    if (btn) {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                    const freezeBtn = document.getElementById('freezeBookingBtn');
                    if (freezeBtn) {
                        freezeBtn.disabled = false;
                    }
                });
        };

        document.addEventListener('DOMContentLoaded', function () {
            // Initialize modal instance
            const modalElement = document.getElementById('bookingDetailsModal');
            let modalInstance = null;
            const modalCurrencySymbol = '{{ currency_symbol() }}';

            if (modalElement) {
                modalInstance = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });

                // Add event listener for when the modal is shown
                modalElement.addEventListener('show.bs.modal', function (event) {
                    const button = event ? event.relatedTarget : null;
                    const bookingId = button ? button.getAttribute('data-booking-id') : modalElement.getAttribute('data-booking-id');
                    if (!bookingId) return;

                    // Set booking ID on the modal for other actions
                    modalElement.setAttribute('data-booking-id', bookingId);

                    // Reset fields to loading state
                    document.getElementById('bookingCustomerHeader').textContent = 'Loading...';
                    document.getElementById('bookingCustomerPhoneHeader').textContent = '';
                    document.getElementById('bookingStatusBadge').innerHTML = '';
                    document.getElementById('bookingIdDisplay').textContent = '...';
                    document.getElementById('bookingService').textContent = '...';
                    document.getElementById('bookingPackageRow').classList.add('d-none');
                    document.getElementById('bookingServicePrice').textContent = '...';
                    document.getElementById('bookingTimeRange').textContent = '...';
                    document.getElementById('bookingServiceDuration').textContent = '...';
                    document.getElementById('bookingStaff').textContent = '...';
                    document.getElementById('bookingType').textContent = '...';
                    document.getElementById('bookingPaymentStatus').textContent = '...';
                    document.getElementById('bookingNotes').textContent = '...';
                    document.getElementById('bookingCreatedDate').textContent = '...';
                    document.getElementById('bookingCreatedBy').textContent = '...';
                    document.getElementById('bookingAddress').textContent = '...';
                    document.getElementById('bookingPaymentMethodRow').classList.add('d-none');
                    document.getElementById('printReceiptBtnGroup').classList.add('d-none');
                    document.getElementById('groupBookingBadge').classList.add('d-none');
                    document.getElementById('groupBookingSection').classList.add('d-none');
                    document.getElementById('quickAssignStaffBtn').classList.add('d-none');
                    document.getElementById('staffAssignContainer').classList.add('d-none');

                    // Get salon slug safely with multiple layers of fallback
                    @php
                        $currentSalon = app()->bound('current_salon') ? app('current_salon') : null;
                        $salonSlug = $currentSalon ? $currentSalon->slug : request()->route('salon_slug');
                        if (!$salonSlug && auth()->check() && auth()->user()->salon) {
                            $salonSlug = auth()->user()?->salon?->slug;
                        }
                        if (!$salonSlug && auth()->check() && auth()->user()->salon_id) {
                            $salonSlug = optional(\App\Models\Salon::find(auth()->user()->salon_id))->slug;
                        }
                    @endphp
                    const bladeSlug = "{{ $salonSlug }}";
                    const activeSalonSlug = (typeof getSalonSlug === 'function' ? getSalonSlug() : '') 
                        || bladeSlug 
                        || (window.location.pathname.split('/').filter(Boolean)[0] || 'default');
                    const url = `/${activeSalonSlug}/admin/api/bookings/${bookingId}`;

                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        credentials: 'same-origin'
                    })
                        .then(response => {
                            if (!response.ok) throw new Error(`Server error ${response.status}`);
                            const contentType = response.headers.get('content-type');
                            if (contentType && contentType.includes('application/json')) {
                                return response.json();
                            }
                            // Got a redirect / HTML instead of JSON
                            throw new Error('Unexpected response type. The booking API returned HTML.');
                        })
                        .then(booking => {
                            if (booking.error) throw new Error(booking.error);

                            // Populate fields
                            document.getElementById('bookingCustomerHeader').textContent = booking.customer?.name || 'Unknown';
                            document.getElementById('bookingCustomerPhoneHeader').textContent = booking.customer?.phone ? `(${booking.customer.phone})` : '';

                            // Status Badge
                            const statusColors = {
                                'pending': 'warning',
                                'confirmed': 'info',
                                'completed': 'success',
                                'cancelled': 'danger',
                                'no_show': 'danger',
                                'arrived': 'warning',
                                'started': 'info',
                                'staff_completed': 'success',
                                'frozen': 'secondary'
                            };
                            const color = statusColors[booking.status] || 'secondary';
                            const statusLabel = booking.status.charAt(0).toUpperCase() + booking.status.slice(1).replace('_', ' ');
                            document.getElementById('bookingStatusBadge').innerHTML = `<span class="badge bg-${color}">${statusLabel}</span>`;

                            document.getElementById('bookingIdDisplay').textContent = booking.id;

                            // Service Name
                            let serviceName = '-';
                            if (booking.service) {
                                serviceName = booking.service.name;
                            } else if (booking.services && booking.services.length > 0) {
                                serviceName = booking.services.map(s => s.name).join(', ');
                            } else if (booking.packages && booking.packages.length > 0) {
                                serviceName = booking.packages.map(p => p.name).join(', ');
                            }
                            document.getElementById('bookingService').textContent = serviceName;

                            // Package Name (if booking is from a package)
                            if (booking.package_id && booking.package) {
                                document.getElementById('bookingPackageName').textContent = booking.package.name;
                                const packageType = booking.package.type || 'fixed';
                                const typeLabel = packageType === 'customizable' ? 'Customizable' : 'Fixed';
                                const typeBadgeClass = packageType === 'customizable' ? 'bg-info' : 'bg-success';
                                document.getElementById('bookingPackageType').textContent = typeLabel;
                                document.getElementById('bookingPackageType').className = `badge ${typeBadgeClass} text-white ms-2`;
                                document.getElementById('bookingPackageRow').classList.remove('d-none');
                            } else {
                                document.getElementById('bookingPackageRow').classList.add('d-none');
                            }

                            document.getElementById('bookingServicePrice').textContent = modalCurrencySymbol + (parseFloat(booking.amount) || 0).toFixed(2);

                            // Time Range
                            if (booking.start_time) {
                                const startTime = new Date(booking.start_time);
                                const endTime = booking.end_time ? new Date(booking.end_time) : new Date(startTime.getTime() + (booking.duration || 30) * 60000);
                                const timeStr = startTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) + ' - ' + endTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                                document.getElementById('bookingTimeRange').textContent = timeStr;
                            }

                            document.getElementById('bookingServiceDuration').textContent = (booking.duration || 0) + ' min';

                            // Stylist display & Quick Assign setup
                            const staffEl = document.getElementById('bookingStaff');
                            const assignBtn = document.getElementById('quickAssignStaffBtn');
                            const staffSelect = document.getElementById('assignStaffSelect');

                            if (staffSelect) {
                                staffSelect.innerHTML = '<option value="">Choose Staff Member...</option>';
                                if (booking.salon_staff_list && booking.salon_staff_list.length > 0) {
                                    booking.salon_staff_list.forEach(st => {
                                        const opt = document.createElement('option');
                                        opt.value = st.id;
                                        opt.textContent = st.name;
                                        if (booking.staff && booking.staff.id == st.id) {
                                            opt.selected = true;
                                        }
                                        staffSelect.appendChild(opt);
                                    });
                                }
                            }

                            if (booking.staff && booking.staff.name) {
                                staffEl.innerHTML = `<span class="badge bg-light text-dark border px-2.5 py-1"><i class="fas fa-user-tie me-1 text-primary"></i> ${booking.staff.name}</span>`;
                                if (assignBtn) {
                                    assignBtn.classList.remove('d-none');
                                    assignBtn.innerHTML = '<i class="fas fa-edit me-1"></i> Reassign';
                                }
                            } else {
                                staffEl.innerHTML = `<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i> Unassigned</span>`;
                                if (assignBtn) {
                                    assignBtn.classList.remove('d-none');
                                    assignBtn.innerHTML = '<i class="fas fa-user-plus me-1"></i> Assign Staff';
                                }
                            }

                            // Group Booking Breakdown Handling
                            const groupBadge = document.getElementById('groupBookingBadge');
                            const groupSection = document.getElementById('groupBookingSection');
                            const groupTableBody = document.getElementById('groupBookingsTableBody');
                            const groupCountSpan = document.getElementById('groupBookingCount');
                            const groupTotalBadge = document.getElementById('groupTotalBadge');

                            if (booking.is_group_booking && booking.group_bookings && booking.group_bookings.length > 0) {
                                window._currentGroupBookings = booking.group_bookings;
                                window._currentSalonStaff = booking.salon_staff_list || [];

                                if (groupBadge) groupBadge.classList.remove('d-none');
                                if (groupCountSpan) groupCountSpan.textContent = booking.group_services_count || booking.group_bookings.length;
                                if (groupSection) groupSection.classList.remove('d-none');
                                if (groupTotalBadge) groupTotalBadge.textContent = 'Total: ' + modalCurrencySymbol + (parseFloat(booking.group_total_amount) || 0).toFixed(2);

                                // Bulk staff assignment options
                                const bulkAssignContainer = document.getElementById('groupBulkAssignContainer');
                                const bulkAssignSelect = document.getElementById('groupBulkAssignSelect');
                                if (bulkAssignSelect && booking.salon_staff_list && booking.salon_staff_list.length > 0) {
                                    bulkAssignSelect.innerHTML = '<option value="">⚡ Assign All Services To...</option>' +
                                        booking.salon_staff_list.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
                                    if (bulkAssignContainer) bulkAssignContainer.classList.remove('d-none');
                                } else if (bulkAssignContainer) {
                                    bulkAssignContainer.classList.add('d-none');
                                }

                                if (groupTableBody) {
                                    groupTableBody.innerHTML = '';
                                    const staffOptions = (booking.salon_staff_list || []).map(st =>
                                        `<option value="${st.id}">${st.name}</option>`
                                    ).join('');

                                    booking.group_bookings.forEach(item => {
                                        const row = document.createElement('tr');
                                        row.className = 'border-bottom border-light';

                                        let staffActionHtml = '';
                                        if (item.staff_name) {
                                            staffActionHtml = `
                                                <div class="d-inline-flex align-items-center gap-1.5 flex-wrap">
                                                    <span class="badge bg-light text-dark border px-2 py-0.5"><i class="fas fa-user-tie me-1 text-primary"></i>${item.staff_name}</span>
                                                    <select class="form-select form-select-sm py-0 px-1 border-0 bg-transparent text-muted"
                                                            style="font-size:0.7rem; height:22px; width:auto; cursor:pointer;"
                                                            title="Reassign Stylist"
                                                            onchange="window.assignGroupItemStaff(${item.id}, this.value, this)">
                                                        <option value="">Reassign...</option>
                                                        ${staffOptions}
                                                    </select>
                                                </div>`;
                                        } else {
                                            staffActionHtml = `
                                                <div class="d-inline-flex align-items-center gap-1.5 flex-wrap">
                                                    <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i>Unassigned</span>
                                                    <select class="form-select form-select-sm py-0 px-2 border"
                                                            style="font-size:0.72rem; height:25px; width:auto; border-color:#f59e0b; background:#fffbeb; font-weight:600; color:#92400e; border-radius:6px; cursor:pointer;"
                                                            title="Assign staff to this service"
                                                            onchange="window.assignGroupItemStaff(${item.id}, this.value, this)">
                                                        <option value="">+ Assign Staff...</option>
                                                        ${staffOptions}
                                                    </select>
                                                </div>`;
                                        }

                                        const isCurrent = item.id === booking.id ? ' <span class="badge bg-primary text-white x-small">This Service</span>' : '';
                                        row.innerHTML = `
                                            <td class="ps-1 fw-semibold text-dark">${item.service_name}${isCurrent}</td>
                                            <td class="text-muted small" style="white-space: nowrap;">${item.start_time || '-'}</td>
                                            <td>${staffActionHtml}</td>
                                            <td class="text-end fw-bold" style="white-space: nowrap;">${modalCurrencySymbol}${parseFloat(item.amount).toFixed(2)}</td>
                                        `;
                                        groupTableBody.appendChild(row);
                                    });
                                }
                            } else {
                                window._currentGroupBookings = [];
                                if (groupBadge) groupBadge.classList.add('d-none');
                                if (groupSection) groupSection.classList.add('d-none');
                            }
                            let bookingSource = booking.booking_type || booking.source || 'In-App';
                            bookingSource = bookingSource.toLowerCase();
                            const isOnline = (bookingSource === 'guest' || bookingSource === 'online');

                            if (isOnline) {
                                document.getElementById('bookingType').textContent = 'Online Booking';
                            } else {
                                document.getElementById('bookingType').textContent = bookingSource.charAt(0).toUpperCase() + bookingSource.slice(1);
                            }

                            const paymentStatus = booking.payment_status || 'pending';
                            document.getElementById('bookingPaymentStatus').textContent = paymentStatus.charAt(0).toUpperCase() + paymentStatus.slice(1);

                            if (booking.payment_method) {
                                document.getElementById('bookingPaymentMethod').textContent = booking.payment_method.charAt(0).toUpperCase() + booking.payment_method.slice(1);
                                document.getElementById('bookingPaymentMethodRow').classList.remove('d-none');
                            }

                            document.getElementById('bookingNotes').textContent = booking.notes || '-Nil-';
                            document.getElementById('bookingCreatedDate').textContent = new Date(booking.created_at).toLocaleDateString();
                            document.getElementById('bookingCreatedBy').textContent = booking.created_by_user?.name || 'System';
                            document.getElementById('bookingAddress').textContent = booking.customer?.address || '-';

                            // Update Status Buttons
                            let finalTargetStatus = booking.status.trim().toLowerCase();
                            if (finalTargetStatus === 'pending' && isOnline) {
                                finalTargetStatus = 'online_pending';
                            }
                            // VISUAL DEBUG: Show the calculated status in the label
                            // document.getElementById('bookingType').textContent += ` [${finalTargetStatus}]`;

                            document.querySelectorAll('.status-btn').forEach(btn => {
                                btn.classList.remove('active');
                                if (btn.dataset.status === finalTargetStatus) {
                                    btn.classList.add('active');
                                }
                            });

                            // Show/Hide Print Receipt
                            if (paymentStatus === 'paid') {
                                const btnGroup = document.getElementById('printReceiptBtnGroup');
                                if (btnGroup) btnGroup.classList.remove('d-none');
                                // Main print button => default (salon-configured) width
                                const printBtn = document.getElementById('printReceiptBtn');
                                if (printBtn) {
                                    printBtn.onclick = function () {
                                        window.open(`/${activeSalonSlug}/admin/bookings/${booking.id}/receipt`, '_blank');
                                    };
                                }
                                // Size-specific links
                                const print80 = document.getElementById('printReceipt80mm');
                                if (print80) {
                                    print80.onclick = function(e) {
                                        e.preventDefault();
                                        window.open(`/${activeSalonSlug}/admin/bookings/${booking.id}/receipt?width=80mm`, '_blank');
                                    };
                                }
                                const print58 = document.getElementById('printReceipt58mm');
                                if (print58) {
                                    print58.onclick = function(e) {
                                        e.preventDefault();
                                        window.open(`/${activeSalonSlug}/admin/bookings/${booking.id}/receipt?width=58mm`, '_blank');
                                    };
                                }
                                const printA4 = document.getElementById('printReceiptA4');
                                if (printA4) {
                                    printA4.onclick = function(e) {
                                        e.preventDefault();
                                        window.open(`/${activeSalonSlug}/admin/bookings/${booking.id}/receipt?width=a4`, '_blank');
                                    };
                                }
                            } else {
                                const btnGroup = document.getElementById('printReceiptBtnGroup');
                                if (btnGroup) btnGroup.classList.add('d-none');
                            }

                            // Cancel Appointment Button Handling
                            const cancelBtn = document.getElementById('cancelBookingBtn');
                            if (cancelBtn) {
                                if (booking.status === 'cancelled' || booking.status === 'completed') {
                                    cancelBtn.classList.add('d-none');
                                } else {
                                    cancelBtn.classList.remove('d-none');
                                    cancelBtn.disabled = false;
                                    cancelBtn.innerHTML = '<i class="fas fa-times-circle me-1"></i> Cancel Appointment';
                                    cancelBtn.onclick = function () {
                                        Swal.fire({
                                            title: 'Cancel Appointment?',
                                            text: 'Are you sure you want to cancel this appointment?',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#dc2626',
                                            cancelButtonColor: '#6b7280',
                                            confirmButtonText: 'Yes, cancel it',
                                            cancelButtonText: 'No, keep it'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                cancelBtn.disabled = true;
                                                cancelBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Cancelling...';
                                                window.updateBookingStatus('cancelled');
                                            }
                                        });
                                    };
                                }
                            }

                            // Freeze / Unfreeze Button Handling
                            const freezeBtn = document.getElementById('freezeBookingBtn');
                            if (freezeBtn) {
                                const isCompletedOrPaid = paymentStatus === 'paid' || booking.status === 'completed' || booking.status === 'cancelled';
                                if (isCompletedOrPaid) {
                                    freezeBtn.classList.add('d-none');
                                } else {
                                    freezeBtn.classList.remove('d-none');
                                    freezeBtn.disabled = false;
                                    const isFrozen = (booking.status === 'frozen');
                                    if (isFrozen) {
                                        freezeBtn.innerHTML = '<i class="fas fa-snowflake me-1 text-info"></i> Unfreeze';
                                        freezeBtn.className = 'btn btn-secondary';
                                        freezeBtn.title = 'Unfreeze this booking to return to confirmed status';
                                        freezeBtn.onclick = function () {
                                            freezeBtn.disabled = true;
                                            freezeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Unfreezing...';
                                            window.updateBookingStatus('confirmed');
                                        };
                                    } else {
                                        freezeBtn.innerHTML = '<i class="fas fa-snowflake me-1"></i> Freeze';
                                        freezeBtn.className = 'btn btn-outline-secondary';
                                        freezeBtn.title = 'Freeze this booking to hold this slot';
                                        freezeBtn.onclick = function () {
                                            freezeBtn.disabled = true;
                                            freezeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Freezing...';
                                            window.updateBookingStatus('frozen');
                                        };
                                    }
                                }
                            }

                            // Handle Raise/View Sale Button
                            let raiseSaleBtn = document.getElementById('raiseSaleBtn');
                            if (raiseSaleBtn) {
                                // Clone button to remove any existing event listeners (to prevent double actions or old POS redirect)
                                const newRaiseSaleBtn = raiseSaleBtn.cloneNode(true);
                                raiseSaleBtn.parentNode.replaceChild(newRaiseSaleBtn, raiseSaleBtn);
                                raiseSaleBtn = newRaiseSaleBtn;

                                const isPaid = paymentStatus.toLowerCase() === 'paid';
                                if (booking.status === 'cancelled') {
                                    raiseSaleBtn.classList.add('d-none');
                                } else if (booking.pos_sale_id && isPaid) {
                                    // Sale exists and is paid -> View Sale
                                    raiseSaleBtn.innerHTML = '<i class="fas fa-receipt me-1"></i> View Sale';
                                    raiseSaleBtn.classList.remove('btn-primary', 'btn-success');
                                    raiseSaleBtn.classList.add('btn-info');
                                    raiseSaleBtn.onclick = function () {
                                        window.location.href = `/${activeSalonSlug}/admin/pos/sales/${booking.pos_sale_id}`;
                                    };
                                    raiseSaleBtn.classList.remove('d-none');
                                } else {
                                    // No sale OR sale exists but is NOT paid -> Raise Sale
                                    raiseSaleBtn.innerHTML = '<i class="fas fa-cash-register me-1"></i> Raise Sale';
                                    raiseSaleBtn.classList.add('btn-success');
                                    raiseSaleBtn.classList.remove('btn-info', 'btn-primary');

                                    if (isPaid && !booking.pos_sale_id) {
                                        // If paid but no sale ID (unlikely), hide to prevent duplicate
                                        raiseSaleBtn.classList.add('d-none');
                                    } else {
                                        raiseSaleBtn.onclick = function () {
                                            window.location.href = `/${activeSalonSlug}/admin/pos?booking_id=${booking.id}`;
                                        };
                                        raiseSaleBtn.classList.remove('d-none');
                                    }
                                }
                            }

                            // Update Edit Button
                            const editBtn = document.getElementById('editBookingBtn');
                            if (editBtn) {
                                if (booking.status === 'cancelled') {
                                    editBtn.classList.add('d-none');
                                } else {
                                    editBtn.classList.remove('d-none');
                                    editBtn.onclick = function () {
                                        window.location.href = `/${activeSalonSlug}/admin/bookings?edit_booking_id=${booking.id}`;
                                    };
                                }
                            }

                        })
                        .catch(error => {
                            console.error('[BookingModal] Error loading booking details:', error);
                            document.getElementById('bookingCustomerHeader').textContent = 'Could not load booking';
                            document.getElementById('bookingStatusBadge').innerHTML = `<span class="badge bg-danger">Error</span>`;
                            document.getElementById('bookingIdDisplay').textContent = `#${bookingId}`;
                            document.getElementById('bookingService').innerHTML = `<span class="text-danger small"><i class="fas fa-exclamation-triangle me-1"></i>${error.message || 'Failed to load booking details. Please refresh and try again.'}</span>`;
                        });
                });
            }
        });

        // Quick Staff Assignment Functions
        window.toggleStaffAssignDropdown = function () {
            const container = document.getElementById('staffAssignContainer');
            if (container) {
                container.classList.toggle('d-none');
            }
        };

        window.submitStaffAssignment = function () {
            const modalEl = document.getElementById('bookingDetailsModal');
            const bookingId = modalEl ? modalEl.getAttribute('data-booking-id') : null;
            const staffSelect = document.getElementById('assignStaffSelect');
            const staffId = staffSelect ? staffSelect.value : null;

            if (!bookingId || !staffId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Select Staff',
                    text: 'Please select a staff member to assign.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                return;
            }

            const saveBtn = document.getElementById('confirmAssignStaffBtn');
            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';
            }

            @php
                $salonSlug = $salonSlug ?? request()->route('salon_slug') ?? optional(auth()->user()?->salon)->slug ?? (auth()->check() && auth()->user()->salon_id ? optional(\App\Models\Salon::find(auth()->user()->salon_id))->slug : 'default');
            @endphp
            const assignBladeSlug = "{{ $salonSlug }}";
            const assignActiveSlug = (typeof getSalonSlug === 'function' ? getSalonSlug() : '') 
                || assignBladeSlug 
                || (window.location.pathname.split('/').filter(Boolean)[0] || 'default');
            const assignUrl = `/${assignActiveSlug}/admin/bookings/${bookingId}/assign`;

            fetch(assignUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ staff_id: staffId })
            })
            .then(res => res.json())
            .then(data => {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="fas fa-check me-1"></i> Save';
                }
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Staff Assigned',
                        text: data.message || 'Staff assigned successfully.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    // Update UI immediately
                    const selectedName = staffSelect.options[staffSelect.selectedIndex]?.textContent || 'Assigned';
                    const staffEl = document.getElementById('bookingStaff');
                    if (staffEl) {
                        staffEl.innerHTML = `<span class="badge bg-light text-dark border px-2.5 py-1"><i class="fas fa-user-tie me-1 text-primary"></i> ${selectedName}</span>`;
                    }
                    const assignBtn = document.getElementById('quickAssignStaffBtn');
                    if (assignBtn) {
                        assignBtn.innerHTML = '<i class="fas fa-edit me-1"></i> Reassign';
                    }
                    window.toggleStaffAssignDropdown();
                } else {
                    throw new Error(data.message || 'Failed to assign staff');
                }
            })
            .catch(err => {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="fas fa-check me-1"></i> Save';
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Assignment Failed',
                    text: err.message || 'Could not assign staff at this time.'
                });
            });
        };

        // Assign Staff to a specific item inside Group Booking
        window.assignGroupItemStaff = function (targetBookingId, staffId, selectEl) {
            if (!targetBookingId) return;
            if (!staffId) return;

            const modalEl = document.getElementById('bookingDetailsModal');
            const currentBookingId = modalEl ? modalEl.getAttribute('data-booking-id') : targetBookingId;

            if (selectEl) selectEl.disabled = true;

            @php
                $salonSlug = $salonSlug ?? request()->route('salon_slug') ?? optional(auth()->user()?->salon)->slug ?? (auth()->check() && auth()->user()->salon_id ? optional(\App\Models\Salon::find(auth()->user()->salon_id))->slug : 'default');
            @endphp
            const assignBladeSlug = "{{ $salonSlug }}";
            const assignActiveSlug = (typeof getSalonSlug === 'function' ? getSalonSlug() : '') 
                || assignBladeSlug 
                || (window.location.pathname.split('/').filter(Boolean)[0] || 'default');
            const assignUrl = `/${assignActiveSlug}/admin/bookings/${targetBookingId}/assign`;

            fetch(assignUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ staff_id: staffId })
            })
            .then(res => res.json())
            .then(data => {
                if (selectEl) selectEl.disabled = false;
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Staff Assigned',
                        text: data.message || 'Staff assigned to service successfully.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    // Re-open/refresh modal to reflect updated staff assignment
                    if (currentBookingId && typeof window.openBookingDetailsModal === 'function') {
                        window.openBookingDetailsModal(currentBookingId);
                    }
                    if (window.calendar) {
                        window.calendar.refetchEvents();
                    }
                } else {
                    throw new Error(data.message || 'Failed to assign staff');
                }
            })
            .catch(err => {
                if (selectEl) selectEl.disabled = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Assignment Failed',
                    text: err.message || 'Could not assign staff at this time.'
                });
            });
        };

        // Bulk assign all services in the group booking to one staff member
        window.bulkAssignAllGroupServices = function (staffId) {
            if (!staffId) return;
            const items = window._currentGroupBookings || [];
            if (!items.length) return;

            const modalEl = document.getElementById('bookingDetailsModal');
            const currentBookingId = modalEl ? modalEl.getAttribute('data-booking-id') : null;
            const bulkSelect = document.getElementById('groupBulkAssignSelect');
            if (bulkSelect) bulkSelect.disabled = true;

            const assignBladeSlug = "{{ $salonSlug }}";
            const assignActiveSlug = (typeof getSalonSlug === 'function' ? getSalonSlug() : '') 
                || assignBladeSlug 
                || (window.location.pathname.split('/').filter(Boolean)[0] || 'default');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            const promises = items.map(item => {
                return fetch(`/${assignActiveSlug}/admin/bookings/${item.id}/assign`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ staff_id: staffId })
                }).then(r => r.json());
            });

            Promise.all(promises)
                .then(results => {
                    if (bulkSelect) {
                        bulkSelect.disabled = false;
                        bulkSelect.value = '';
                    }
                    const successCount = results.filter(r => r && r.success).length;
                    Swal.fire({
                        icon: 'success',
                        title: 'Group Staff Assigned',
                        text: `Successfully assigned staff to ${successCount} service(s).`,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    if (currentBookingId && typeof window.openBookingDetailsModal === 'function') {
                        window.openBookingDetailsModal(currentBookingId);
                    }
                    if (window.calendar) {
                        window.calendar.refetchEvents();
                    }
                })
                .catch(err => {
                    if (bulkSelect) {
                        bulkSelect.disabled = false;
                        bulkSelect.value = '';
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Bulk Assignment Failed',
                        text: err.message || 'Error assigning staff to all services.'
                    });
                });
        };

        // Global opener for Booking Details Modal
        window.openBookingDetailsModal = function (bookingId) {
            const modalEl = document.getElementById('bookingDetailsModal');
            if (modalEl) {
                modalEl.setAttribute('data-booking-id', bookingId);
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        };

        // Automatically open modal if ?booking_id= is in the URL on page load
        (function() {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const targetBookingId = urlParams.get('booking_id') || urlParams.get('open_booking');
                if (targetBookingId) {
                    setTimeout(function () {
                        if (typeof window.openBookingDetailsModal === 'function') {
                            window.openBookingDetailsModal(targetBookingId);
                        }
                    }, 350);
                }
            } catch (e) {
                // Ignore search param errors
            }
        })();
    </script>
@endpush