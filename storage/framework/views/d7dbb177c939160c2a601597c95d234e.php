<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row g-4">
            <!-- Quick Stats -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-lg bg-primary-subtle text-primary rounded">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Upcoming</h6>
                                <h4 class="mb-0"><?php echo e(auth()->user()->customer?->bookings()->upcoming()->count() ?? 0); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-lg bg-success-subtle text-white rounded">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Completed</h6>
                                <h4 class="mb-0"><?php echo e(auth()->user()->customer?->bookings()->completed()->count() ?? 0); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-lg bg-warning-subtle text-warning rounded">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Pending</h6>
                                <h4 class="mb-0"><?php echo e(auth()->user()->customer?->bookings()->pending()->count() ?? 0); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-lg bg-danger-subtle text-danger rounded">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Cancelled</h6>
                                <h4 class="mb-0"><?php echo e(auth()->user()->customer?->bookings()->cancelled()->count() ?? 0); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Book Button -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <h4 class="mb-3">Need to book an appointment?</h4>
                        <p class="text-muted mb-4">Quick and easy booking process - just a few clicks away!</p>
                        <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal"
                            data-bs-target="#quickBookModal">
                            <i class="fas fa-calendar-plus me-2"></i>Book Appointment Now
                        </button>
                    </div>
                </div>
            </div>

            <!-- Upcoming Appointments -->
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i>
                            Upcoming Appointments
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php $__empty_1 = true; $__currentLoopData = auth()->user()->customer?->bookings()->with(['service', 'staff', 'package'])->upcoming()->get() ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="avatar avatar-sm bg-<?php echo e($booking->status === 'pending' ? 'warning' : 'info'); ?>-subtle text-<?php echo e($booking->status === 'pending' ? 'warning' : 'info'); ?> rounded">
                                                <i class="fas fa-calendar-check"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">
                                                <?php echo e($booking->service->name); ?>

                                                <?php if($booking->package): ?>
                                                    <span class="badge bg-purple-subtle text-purple-emphasis rounded-pill ms-2"
                                                        style="background-color: #f3e8ff !important; color: #7c3aed !important; font-size: 0.65em;">
                                                        <i class="fas fa-box me-1"></i><?php echo e($booking->package->name); ?>

                                                    </span>
                                                <?php endif; ?>
                                            </h6>
                                            <p class="mb-1 small text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?php echo e(format_datetime($booking->start_time)); ?>

                                            </p>
                                            <p class="mb-0 small text-muted">
                                                <i class="fas fa-user-tie me-1"></i>
                                                <?php echo e($booking->staff->name); ?>

                                            </p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <a href="<?php echo e(route('customer.appointments.show', $booking->id)); ?>"
                                                class="btn btn-sm btn-outline-primary">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="list-group-item text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-calendar-times fa-2x mb-3"></i>
                                        <p class="mb-0">No upcoming appointments</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2 text-primary"></i>
                            Recent Activity
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php $__empty_1 = true; $__currentLoopData = auth()->user()->customer?->bookings()->with(['service', 'package'])->latest()->take(5)->get() ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="list-group-item py-0">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="avatar avatar-sm bg-<?php echo e($booking->status === 'completed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'info')); ?>-subtle text-<?php echo e($booking->status === 'completed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'info')); ?> rounded">
                                                <i
                                                    class="fas fa-<?php echo e($booking->status === 'completed' ? 'check' : ($booking->status === 'cancelled' ? 'times' : 'clock')); ?>"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0 text-truncate" style="font-size: 0.9rem;">
                                                <?php echo e($booking->service->name); ?>

                                                <?php if($booking->package): ?>
                                                    <span class="badge bg-purple-subtle text-purple-emphasis rounded-pill ms-1"
                                                        style="background-color: #f3e8ff !important; color: #7c3aed !important; font-size: 0.6em;">
                                                        <i class="fas fa-box me-1"></i><?php echo e($booking->package->name); ?>

                                                    </span>
                                                <?php endif; ?>
                                            </h6>
                                            <p class="mb-0 small text-muted" style="font-size: 0.75rem;">
                                                <?php echo e(format_date($booking->start_time)); ?> • <?php echo e(ucfirst($booking->status)); ?>

                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="list-group-item text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-history fa-2x mb-3"></i>
                                        <p class="mb-0">No recent activity</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Book Modal -->
    <div class="modal fade" id="quickBookModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-plus me-2 text-primary"></i>
                        Quick Book Appointment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="quickBookForm" action="<?php echo e(route('customer.appointments.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <!-- Customer Details (Auto-filled) -->
                        <div class="mb-3">
                            <label class="form-label">Customer Details</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" class="form-control" value="<?php echo e(auth()->user()->name); ?>" readonly>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="tel" class="form-control"
                                    value="<?php echo e(auth()->user()->customer?->phone ?? 'Not set'); ?>" readonly>
                            </div>
                        </div>

                        <!-- Service Selection -->
                        <div class="mb-3">
                            <label class="form-label">Select Service</label>
                            <select class="form-select" name="service_id" required>
                                <option value="">Choose a service...</option>
                                <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($service->id); ?>" data-duration="<?php echo e($service->duration); ?>">
                                        <?php echo e($service->name); ?> - <?php echo e(currency_symbol()); ?><?php echo e(number_format($service->price, 2)); ?>

                                        (<?php echo e($service->duration); ?> min)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Date & Time Selection -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Date</label>
                                <input type="date" class="form-control" name="date" min="<?php echo e(date('Y-m-d')); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Time</label>
                                <select class="form-select" name="time" required disabled>
                                    <option value="">Select date first</option>
                                </select>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3 mt-3">
                            <label class="form-label">Additional Notes</label>
                            <textarea class="form-control" name="notes" rows="2"
                                placeholder="Any special requests or requirements..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calendar-check me-2"></i>Book Appointment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize calendar if the element exists
                const calendarEl = document.getElementById('calendar');
                if (calendarEl) {
                    window.calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay'
                        },
                        events: {
                            url: '/customer/api/appointments',
                            method: 'GET',
                            failure: function () {
                                alert('There was an error fetching events!');
                            }
                        },
                        eventClick: function (info) {
                            // Handle event click if needed
                        }
                    });

                    window.calendar.render();
                }

                const quickBookForm = document.getElementById('quickBookForm');
                if (!quickBookForm) return;

                const dateInput = quickBookForm.querySelector('input[name="date"]');
                const timeSelect = quickBookForm.querySelector('select[name="time"]');
                const serviceSelect = quickBookForm.querySelector('select[name="service_id"]');
                const submitButton = quickBookForm.querySelector('button[type="submit"]');

                // Load available time slots when date is selected
                function loadTimeSlots() {
                    if (!dateInput.value || !serviceSelect.value) return;

                    timeSelect.disabled = true;
                    timeSelect.innerHTML = '<option value="">Loading available slots...</option>';

                    fetch(`/customer/api/available-slots?date=${dateInput.value}&service_id=${serviceSelect.value}`)
                        .then(response => response.json())
                        .then(data => {
                            timeSelect.innerHTML = '<option value="">Select a time</option>';

                            if (data.length === 0) {
                                timeSelect.innerHTML = '<option value="">No available slots</option>';
                                return;
                            }

                            data.forEach(slot => {
                                if (slot.available) {
                                    const time = new Date(slot.time);
                                    timeSelect.innerHTML += `<option value="${slot.time}">${time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</option>`;
                                }
                            });

                            timeSelect.disabled = false;
                        })
                        .catch(error => {

                            timeSelect.innerHTML = '<option value="">Error loading slots</option>';
                        });
                }

                dateInput.addEventListener('change', loadTimeSlots);

                // Reset time slots when service changes
                serviceSelect.addEventListener('change', function () {
                    if (dateInput.value) {
                        loadTimeSlots();
                    }
                });

                // Form submission
                quickBookForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalButtonText = submitButton.innerHTML;

                    // Show loading state
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
                            start_time: new Date(`${formData.get('date')}T${formData.get('time')}`).toISOString(),
                            notes: formData.get('notes')
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Close the modal
                                const modal = bootstrap.Modal.getInstance(document.getElementById('quickBookModal'));
                                if (modal) modal.hide();

                                // Show success message
                                alert('Appointment booked successfully!');

                                // Refresh the calendar if it exists
                                if (window.calendar && typeof window.calendar.refetchEvents === 'function') {
                                    window.calendar.refetchEvents();
                                }

                                // Redirect if needed
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                }

                                // Reset the form
                                quickBookForm.reset();
                            } else {
                                throw new Error(data.message || 'Failed to book appointment');
                            }
                        })
                        .catch(error => {

                            alert(error.message || 'Failed to book appointment. Please try again.');
                        })
                        .finally(() => {
                            // Reset button state
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalButtonText;
                        });
                });
            });
        </script>
    <?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\customer\dashboard.blade.php ENDPATH**/ ?>