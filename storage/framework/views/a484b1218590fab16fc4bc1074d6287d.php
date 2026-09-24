<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Bookings</h6>
                        <div class="d-flex gap-2">
                            <div class="input-group input-group-outline" style="width: 250px;">
                                <select id="staffFilter" class="form-select form-select-sm">
                                    <option value="">All Staff</option>
                                    <?php $__currentLoopData = $staffMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($staff->id); ?>" <?php echo e(request('staff_id') == $staff->id ? 'selected' : ''); ?>>
                                            <?php echo e($staff->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <a href="<?php echo e(route('admin.bookings.calendar')); ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-calendar-alt me-2"></i>Calendar View
                            </a>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                            Appt. Nr</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Customer</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Service</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Staff</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date
                                            & Time</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Duration</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Status</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end pe-3">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="bookingsTableBody">
                                    <?php echo $__env->make('admin.bookings.partials.booking_rows', ['bookings' => $bookings], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="px-4 pt-3">
                            <?php echo e($bookings->links()); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Booking Details Modal -->
    <?php echo $__env->make('admin.bookings.partials.booking_details_modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.bookings.partials.payment_modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Handle staff filter change
                const staffFilter = document.getElementById('staffFilter');
                if (staffFilter) {
                    staffFilter.addEventListener('change', function () {
                        const staffId = this.value;
                        window.location.href = window.location.pathname + (staffId ? `?staff_id=${staffId}` : '');
                    });
                }

                // Handle status change
                window.updateBookingStatus = function (bookingId, status) {
                    if (!confirm('Are you sure you want to change the status to ' + status.replace('_', ' ') + '?')) return;

                    fetch(`<?php echo e(url(auth()->user()->salon->slug)); ?>/admin/bookings/${bookingId}/status`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ status: status })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.requires_payment) {
                                if (typeof showPaymentModal === 'function') {
                                    showPaymentModal(bookingId, data.amount);
                                } else {
                                    alert('Payment modal not found');
                                }
                            } else if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message || 'Failed to update status');
                            }
                        })
                        .catch(error => {

                            alert('An error occurred');
                        });
                };

                // Add event listeners for change-status buttons
                document.querySelectorAll('.change-status').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();
                        const bookingId = this.getAttribute('data-booking-id');
                        const status = this.getAttribute('data-status');
                        updateBookingStatus(bookingId, status);
                    });
                });

                // Handle edit booking
                document.querySelectorAll('.edit-booking').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();
                        const bookingId = this.getAttribute('data-booking-id');
                        window.location.href = `<?php echo e(route('admin.bookings.index')); ?>?edit_booking_id=${bookingId}`;
                    });
                });

                // Initialize tooltips
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\bookings\index.blade.php ENDPATH**/ ?>