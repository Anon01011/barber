<?php $settings = app('App\Services\SettingsService'); ?>


<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <!-- Welcome Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-gradient-primary text-white overflow-hidden position-relative">
                    <div class="card-body p-5 position-relative z-index-2">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h2 class="display-6 fw-bold mb-2">Welcome back, <?php echo e(Auth::user()->name); ?>! 👋</h2>
                                <p class="lead mb-0 opacity-90">Here's what's happening in your schedule today.</p>
                            </div>
                            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                                <a href="<?php echo e(route('employee.appointments.today', ['salon_slug' => optional(auth()->user()->salon)->slug])); ?>"
                                    class="btn btn-light text-primary fw-bold shadow-sm px-4 py-2 rounded-pill">
                                    <i class="fas fa-calendar-day me-2"></i>View My Schedule
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Decorative Circles (Subtle) -->
                    <div class="position-absolute top-0 end-0 translate-middle p-5 rounded-circle bg-white opacity-10"
                        style="width: 300px; height: 300px; filter: blur(40px);"></div>
                    <div class="position-absolute bottom-0 start-0 translate-middle p-5 rounded-circle bg-white opacity-10"
                        style="width: 200px; height: 200px; filter: blur(30px);"></div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-5">
            <!-- Today's Appointments -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 transition-hover">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-md bg-primary-subtle text-primary rounded-3 p-3 me-3">
                                <i class="fas fa-calendar-check fa-lg"></i>
                            </div>
                            <h6 class="text-uppercase fw-bold text-xs text-muted mb-0 ls-1">Today's Appointments</h6>
                        </div>
                        <div class="d-flex align-items-end justify-content-between">
                            <h2 class="mb-0 fw-bold text-dark"><?php echo e($todayAppointmentsCount ?? 0); ?></h2>
                            <!-- <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 text-xs fw-bold">Today</span> -->
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0 pb-4">
                        <a href="<?php echo e(route('employee.appointments.today', ['salon_slug' => optional(auth()->user()->salon)->slug])); ?>"
                            class="text-xs text-primary fw-bold text-decoration-none stretched-link">
                            View Details <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Upcoming -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 transition-hover">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-md bg-info-subtle text-info rounded-3 p-3 me-3">
                                <i class="fas fa-calendar-week fa-lg"></i>
                            </div>
                            <h6 class="text-uppercase fw-bold text-xs text-muted mb-0 ls-1">Upcoming</h6>
                        </div>
                        <div class="d-flex align-items-end justify-content-between">
                            <h2 class="mb-0 fw-bold text-dark"><?php echo e($upcomingAppointmentsCount ?? 0); ?></h2>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0 pb-4">
                        <a href="<?php echo e(route('employee.appointments.upcoming', ['salon_slug' => optional(auth()->user()->salon)->slug])); ?>"
                            class="text-xs text-info fw-bold text-decoration-none stretched-link">
                            View Upcoming <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Completed -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 transition-hover">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-md bg-success-subtle text-success rounded-3 p-3 me-3">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </div>
                            <h6 class="text-uppercase fw-bold text-xs text-muted mb-0 ls-1">Completed</h6>
                        </div>
                        <div class="d-flex align-items-end justify-content-between">
                            <h2 class="mb-0 fw-bold text-dark"><?php echo e($completedAppointmentsCount ?? 0); ?></h2>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0 pb-4">
                        <a href="<?php echo e(route('employee.appointments.completed', ['salon_slug' => optional(auth()->user()->salon)->slug])); ?>"
                            class="text-xs text-success fw-bold text-decoration-none stretched-link">
                            View History <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 transition-hover">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-md bg-warning-subtle text-warning rounded-3 p-3 me-3">
                                <i class="fas fa-wallet fa-lg"></i>
                            </div>
                            <h6 class="text-uppercase fw-bold text-xs text-muted mb-0 ls-1">Total Revenue</h6>
                        </div>
                        <div class="d-flex align-items-end justify-content-between">
                            <h2 class="mb-0 fw-bold text-dark">
                                <?php echo e(currency_symbol()); ?><?php echo e(number_format($totalRevenue ?? 0, 2)); ?>

                            </h2>
                        </div>
                    </div>
                    <!-- <div class="card-footer bg-transparent border-0 pt-0 pb-4">
                             <span class="text-xs text-warning fw-bold">This Month</span>
                        </div> -->
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Today's Schedule -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold text-gray-800">
                                <i class="fas fa-clock me-2 text-primary"></i>Today's Schedule
                            </h5>
                            <p class="text-muted small mb-0">Your appointments for today</p>
                        </div>
                        <a href="<?php echo e(route('employee.appointments.today', ['salon_slug' => optional(auth()->user()->salon)->slug])); ?>"
                            class="btn btn-sm btn-light text-primary fw-bold rounded-pill px-3">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th
                                            class="px-4 py-3 border-0 text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ls-1">
                                            Time</th>
                                        <th
                                            class="py-3 border-0 text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ls-1">
                                            Customer</th>
                                        <th
                                            class="py-3 border-0 text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ls-1">
                                            Service</th>
                                        <th
                                            class="py-3 border-0 text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-end px-4 ls-1">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $todayAppointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td class="px-4 py-3 text-nowrap" style="width: 120px;">
                                                <span
                                                    class="fw-bold text-dark h6 mb-0"><?php echo e($appointment->start_time->format('h:i A')); ?></span>
                                            </td>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($appointment->customer->name)); ?>&background=random&size=40"
                                                        alt="<?php echo e($appointment->customer->name); ?>"
                                                        class="rounded-circle me-3 shadow-sm border border-white"
                                                        style="width: 40px; height: 40px;">
                                                    <div>
                                                        <div class="fw-bold text-dark"><?php echo e($appointment->customer->name); ?></div>
                                                        <div class="text-muted text-xs">
                                                            <?php echo e($appointment->customer->phone ?? ''); ?>

                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold text-dark"><?php echo e($appointment->service->name); ?></span>
                                                    <span class="text-muted text-xs"><i
                                                            class="far fa-clock me-1"></i><?php echo e($appointment->service->duration); ?>

                                                        mins</span>
                                                </div>
                                            </td>
                                            <td class="py-3 text-end px-4">
                                                <button
                                                    class="btn btn-sm btn-light text-primary hover-scale rounded-circle shadow-sm"
                                                    onclick="viewAppointment(<?php echo e($appointment->id); ?>)" title="View Details"
                                                    style="width: 32px; height: 32px; padding: 0;">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <div class="mb-3">
                                                    <i class="fas fa-calendar-day fa-3x text-muted opacity-25"></i>
                                                </div>
                                                <p class="text-muted fw-bold mb-0">No appointments scheduled for today.</p>
                                                <p class="text-muted small">Enjoy your free time!</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column / Quick Actions -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4 h-100">
                    <div class="card-header bg-white border-0 py-4 px-4">
                        <h5 class="mb-0 fw-bold text-gray-800">
                            <i class="fas fa-bolt me-2 text-warning"></i>Quick Actions
                        </h5>
                        <p class="text-muted small mb-0">Shortcuts to manage your work</p>
                    </div>
                    <div class="card-body px-4 pb-4 pt-2">
                        <div class="d-grid gap-3">
                            <a href="<?php echo e(route('employee.appointments.upcoming', ['salon_slug' => optional(auth()->user()->salon)->slug])); ?>"
                                class="btn btn-outline-light text-dark text-start p-3 d-flex align-items-center border-0 shadow-sm bg-white hover-lift">
                                <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle me-3 d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">Check Upcoming</div>
                                    <div class="text-muted text-xs">View future appointments</div>
                                </div>
                                <i class="fas fa-chevron-right ms-auto text-muted small"></i>
                            </a>

                            <a href="<?php echo e(route('employee.appointments.completed', ['salon_slug' => optional(auth()->user()->salon)->slug])); ?>"
                                class="btn btn-outline-light text-dark text-start p-3 d-flex align-items-center border-0 shadow-sm bg-white hover-lift">
                                <div class="avatar avatar-sm bg-success-subtle text-success rounded-circle me-3 d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-history"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">View History</div>
                                    <div class="text-muted text-xs">See past work</div>
                                </div>
                                <i class="fas fa-chevron-right ms-auto text-muted small"></i>
                            </a>

                            <a href="<?php echo e(route('employee.earnings', ['salon_slug' => optional(auth()->user()->salon)->slug])); ?>"
                                class="btn btn-outline-light text-dark text-start p-3 d-flex align-items-center border-0 shadow-sm bg-white hover-lift">
                                <div class="avatar avatar-sm bg-warning-subtle text-warning rounded-circle me-3 d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-coins"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">My Earnings</div>
                                    <div class="text-muted text-xs">Check your performance</div>
                                </div>
                                <i class="fas fa-chevron-right ms-auto text-muted small"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Appointment Modal (Consistent with other pages) -->
    <div class="modal fade" id="viewAppointmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Appointment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="appointmentModalBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading details...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('styles'); ?>
        <style>
            .ls-1 {
                letter-spacing: 0.5px;
            }

            .text-xs {
                font-size: 0.75rem;
            }

            .bg-primary-subtle {
                background-color: #e5edff !important;
                color: #4e73df !important;
            }

            .bg-success-subtle {
                background-color: #e6fffa !important;
                color: #1cc88a !important;
            }

            .bg-info-subtle {
                background-color: #e0f2f1 !important;
                color: #36b9cc !important;
            }

            .bg-warning-subtle {
                background-color: #fff3cd !important;
                color: #f6c23e !important;
            }

            .bg-gradient-primary {
                background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            }

            .opacity-7 {
                opacity: 0.7;
            }

            .opacity-10 {
                opacity: 0.1;
            }

            .opacity-90 {
                opacity: 0.9;
            }

            .transition-hover {
                transition: all 0.2s ease-in-out;
            }

            .transition-hover:hover {
                transform: translateY(-3px);
                box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
            }

            .hover-lift {
                transition: transform 0.2s;
            }

            .hover-lift:hover {
                transform: translateY(-2px);
                background-color: #f8f9fc !important;
            }

            .hover-scale {
                transition: transform 0.2s;
            }

            .hover-scale:hover {
                transform: scale(1.1);
            }
        </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script>
            const currencySymbol = "<?php echo e($settings->get('currency_symbol', '$', auth()->user()->salon_id)); ?>";

            function viewAppointment(id) {
                const modal = new bootstrap.Modal(document.getElementById('viewAppointmentModal'));
                const modalBody = document.getElementById('appointmentModalBody');

                modalBody.innerHTML = `
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Loading details...</p>
                                </div>
                            `;

                modal.show();
                const salonSlug = getSalonSlug();

                fetch(`/${salonSlug}/employee/appointments/${id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(res => res.json())
                    .then(res => {
                        if (!res.success) throw new Error(res.message || 'Failed to load');

                        const data = res.data;
                        const serviceName = data.service?.name || 'N/A';
                        const customerName = data.customer?.name || 'N/A';
                        const startTime = new Date(data.start_time).toLocaleString();

                        const html = `
                                    <div class="row g-4">
                                        <div class="col-12 text-center pb-3 border-bottom">
                                            <div class="mb-2">
                                                <span class="badge bg-${getStatusColor(data.status)} rounded-pill fs-6 px-3 py-2">
                                                    ${data.status.toUpperCase()}
                                                </span>
                                            </div>
                                            <h4 class="mb-0 fw-bold">${serviceName}</h4>
                                            <p class="text-muted mb-0">${startTime}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent ps-0">
                                                    <span class="text-muted small text-uppercase fw-bold">Customer</span>
                                                    <span class="fw-bold text-dark">${customerName}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent ps-0">
                                                    <span class="text-muted small text-uppercase fw-bold">Service Time</span>
                                                    <span class="fw-bold text-dark">${data.service?.duration || 0} mins</span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                             <ul class="list-group list-group-flush">
                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent ps-0">
                                                    <span class="text-muted small text-uppercase fw-bold">Price</span>
                                                    <span class="fw-bold text-dark">${currencySymbol}${parseFloat(data.service?.price || 0).toFixed(2)}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent ps-0">
                                                    <span class="text-muted small text-uppercase fw-bold">Total</span>
                                                    <span class="fw-bold text-primary">${currencySymbol}${parseFloat(data.amount || 0).toFixed(2)}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                `;
                        modalBody.innerHTML = html;
                    })
                    .catch(err => {
                        modalBody.innerHTML = `
                                    <div class="text-center text-danger py-4">
                                        <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                                        <p>Failed to load details. Please try again.</p>
                                    </div>
                                `;
                    });
            }

            function getStatusColor(status) {
                const colors = { 'pending': 'warning', 'confirmed': 'success', 'completed': 'info', 'cancelled': 'danger' };
                return colors[status?.toLowerCase()] || 'secondary';
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\staff\dashboard.blade.php ENDPATH**/ ?>