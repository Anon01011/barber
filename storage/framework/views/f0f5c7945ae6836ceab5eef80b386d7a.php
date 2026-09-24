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
                                <div class="col-4 text-muted fw-medium">Stylist :</div>
                                <div class="col-8 fw-medium text-dark" id="bookingStaff"></div>
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
                    <button type="button" class="btn btn-success d-none" id="printReceiptBtn">
                        <i class="fas fa-print me-2"></i>Print Receipt
                    </button>
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
        background-color: #d3adf7 !important;
    }

    #bookingDetailsModal .btn-outline-purple {
        color: #722ed1;
        border-color: #d3adf7;
    }

    #bookingDetailsModal .btn-outline-purple:hover {
        background-color: #f9f0ff;
        border-color: #722ed1;
    }
</style>

<?php $__env->startPush('scripts'); ?>
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

            const url = "<?php echo e(route('admin.bookings.status.update', ['booking' => 'BOOKING_ID'])); ?>".replace('BOOKING_ID', bookingId);

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

                        // Re-fetch booking details to update everything properly
                        // Or just update the UI locally
                        btn.classList.add('active');

                        // Refresh calendar events
                        if (window.calendar) {
                            window.calendar.refetchEvents();
                        }

                        // Show success message
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-success position-fixed top-0 end-0 m-3';
                        alert.style.zIndex = '9999';
                        alert.textContent = 'Status updated successfully';
                        document.body.appendChild(alert);
                        setTimeout(() => alert.remove(), 3000);
                    } else {
                        throw new Error(data.message || 'Failed to update status');
                    }
                })
                .catch(error => {

                    alert('Error updating status: ' + error.message);
                })
                .finally(() => {
                    if (btn) {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                });
        };

        document.addEventListener('DOMContentLoaded', function () {
            // Initialize modal instance
            const modalElement = document.getElementById('bookingDetailsModal');
            let modalInstance = null;
            const modalCurrencySymbol = '<?php echo e(currency_symbol()); ?>';

            if (modalElement) {
                modalInstance = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });

                // Add event listener for when the modal is shown
                modalElement.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    if (!button) return; // Event might be triggered manually

                    const bookingId = button.getAttribute('data-booking-id');
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
                    document.getElementById('printReceiptBtn').classList.add('d-none');

                    // Get salon slug safely from Blade
                    <?php
                        $currentSalon = app()->bound('current_salon') ? app('current_salon') : null;
                        $salonSlug = $currentSalon ? $currentSalon->slug : request()->route('salon_slug');
                        if (!$salonSlug && auth()->check() && auth()->user()->salon) {
                            $salonSlug = auth()->user()->salon->slug;
                        }
                    ?>
                    const salonSlug = "<?php echo e($salonSlug); ?>";
                    const url = `/${salonSlug}/admin/api/bookings/${bookingId}`;

                    fetch(url)
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
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
                            document.getElementById('bookingStaff').textContent = booking.staff?.name || 'Unassigned';
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
                                document.getElementById('printReceiptBtn').classList.remove('d-none');
                                document.getElementById('printReceiptBtn').onclick = function () {
                                    window.open(`/${salonSlug}/admin/bookings/${booking.id}/receipt`, '_blank');
                                };
                            }

                            // Hide Freeze button if paid or completed
                            const isCompletedOrPaid = paymentStatus === 'paid' || booking.status === 'completed';
                            if (isCompletedOrPaid) {
                                document.getElementById('freezeBookingBtn').classList.add('d-none');
                            } else {
                                document.getElementById('freezeBookingBtn').classList.remove('d-none');
                            }

                            // Handle Raise/View Sale Button
                            let raiseSaleBtn = document.getElementById('raiseSaleBtn');
                            // Clone button to remove any existing event listeners (to prevent double actions or old POS redirect)
                            const newRaiseSaleBtn = raiseSaleBtn.cloneNode(true);
                            raiseSaleBtn.parentNode.replaceChild(newRaiseSaleBtn, raiseSaleBtn);
                            raiseSaleBtn = newRaiseSaleBtn;

                            const isPaid = paymentStatus.toLowerCase() === 'paid';
                            if (booking.pos_sale_id && isPaid) {
                                // Sale exists and is paid -> View Sale
                                raiseSaleBtn.innerHTML = '<i class="fas fa-receipt me-1"></i> View Sale';
                                raiseSaleBtn.classList.remove('btn-primary', 'btn-success');
                                raiseSaleBtn.classList.add('btn-info');
                                raiseSaleBtn.onclick = function () {
                                    window.location.href = `/${salonSlug}/admin/pos/sales/${booking.pos_sale_id}`;
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
                                        window.location.href = `/${salonSlug}/admin/pos?booking_id=${booking.id}`;
                                    };
                                    raiseSaleBtn.classList.remove('d-none');
                                }
                            }

                            // Update Edit Button
                            document.getElementById('editBookingBtn').onclick = function () {
                                window.location.href = `/${salonSlug}/admin/bookings?edit_booking_id=${booking.id}`;
                            };

                        })
                        .catch(error => {

                            document.getElementById('bookingCustomerHeader').textContent = 'Error loading details';
                        });
                });
            }
        });
    </script>
<?php $__env->stopPush(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\bookings\partials\booking_details_modal.blade.php ENDPATH**/ ?>