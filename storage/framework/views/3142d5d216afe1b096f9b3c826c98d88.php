<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-0 text-gray-800">My Earnings</h2>
                <p class="text-muted mb-0">Track your commissions and performance</p>
            </div>

            <!-- Date Filter -->
            <form action="<?php echo e(route('employee.earnings', ['salon_slug' => optional(auth()->user()->salon)->slug])); ?>"
                method="GET" class="d-flex gap-2 align-items-center">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-calendar"></i></span>
                    <input type="date" name="start_date" class="form-control" value="<?php echo e($startDate->format('Y-m-d')); ?>">
                    <span class="input-group-text bg-light">to</span>
                    <input type="date" name="end_date" class="form-control" value="<?php echo e($endDate->format('Y-m-d')); ?>">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <?php if(request('start_date') || request('end_date')): ?>
                    <a href="<?php echo e(route('employee.earnings', ['salon_slug' => optional(auth()->user()->salon)->slug])); ?>"
                        class="btn btn-outline-secondary">
                        <i class="fas fa-undo"></i>
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <!-- Total Earnings -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Earnings (Period)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo e(currency_symbol()); ?><?php echo e(number_format($summary['total_all'], 2)); ?>

                                </div>
                                <div class="mt-2 text-xs text-muted">
                                    <?php echo e($summary['count_all']); ?> commissions
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Pending Approval</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo e(currency_symbol()); ?><?php echo e(number_format($summary['total_pending'], 2)); ?>

                                </div>
                                <div class="mt-2 text-xs text-muted">
                                    <?php echo e($summary['count_pending']); ?> commissions
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approved -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Approved (Unpaid)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo e(currency_symbol()); ?><?php echo e(number_format($summary['total_approved'], 2)); ?>

                                </div>
                                <div class="mt-2 text-xs text-muted">
                                    <?php echo e($summary['count_approved']); ?> commissions
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paid -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                    Paid Out</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo e(currency_symbol()); ?><?php echo e(number_format($summary['total_paid'], 2)); ?>

                                </div>
                                <div class="mt-2 text-xs text-muted">
                                    <?php echo e($summary['count_paid']); ?> commissions
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-wallet fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Monthly Earnings Chart -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Earnings Overview (Last 6 Months)</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area" style="height: 320px;">
                            <canvas id="earningsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commission Breakdown -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Commission Sources</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2" style="height: 250px;">
                            <canvas id="sourcesChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small">
                            <?php $__currentLoopData = $breakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="mr-2">
                                    <i
                                        class="fas fa-circle text-<?php echo e($loop->index == 0 ? 'primary' : ($loop->index == 1 ? 'success' : 'info')); ?>"></i>
                                    <?php echo e(ucfirst($item->item_type)); ?>

                                    (<?php echo e(number_format(($item->total / $summary['total_all']) * 100, 1)); ?>%)
                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Commission List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Commission History</h6>
                <div class="dropdown no-arrow">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="statusFilter"
                        data-bs-toggle="dropdown">
                        Filter Status: <?php echo e(ucfirst(request('status', 'All'))); ?>

                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow animated--fade-in">
                        <a class="dropdown-item"
                            href="<?php echo e(route('employee.earnings', array_merge(request()->all(), ['salon_slug' => optional(auth()->user()->salon)->slug, 'status' => 'all']))); ?>">All</a>
                        <a class="dropdown-item"
                            href="<?php echo e(route('employee.earnings', array_merge(request()->all(), ['salon_slug' => optional(auth()->user()->salon)->slug, 'status' => 'pending']))); ?>">Pending</a>
                        <a class="dropdown-item"
                            href="<?php echo e(route('employee.earnings', array_merge(request()->all(), ['salon_slug' => optional(auth()->user()->salon)->slug, 'status' => 'approved']))); ?>">Approved</a>
                        <a class="dropdown-item"
                            href="<?php echo e(route('employee.earnings', array_merge(request()->all(), ['salon_slug' => optional(auth()->user()->salon)->slug, 'status' => 'paid']))); ?>">Paid</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Item</th>
                                <th>Sale Amount</th>
                                <th>Commission</th>
                                <th>Status</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $commissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <?php echo e($commission->created_at->format('M d, Y')); ?><br>
                                        <small class="text-muted"><?php echo e($commission->created_at->format('h:i A')); ?></small>
                                    </td>
                                    <td>
                                        <?php if($commission->item_type == 'service'): ?>
                                            <span class="badge bg-primary bg-opacity-10 text-primary">Service</span>
                                            <?php if($commission->booking_id): ?>
                                                <?php echo e($commission->booking->service->name ?? 'Unknown Service'); ?>

                                            <?php else: ?>
                                                <?php
                                                    $service = \App\Models\Service::find($commission->item_id);
                                                ?>
                                                <?php echo e($service->name ?? 'POS Service'); ?>

                                            <?php endif; ?>
                                        <?php elseif($commission->item_type == 'product'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-white">Product</span>
                                            <?php
                                                $product = \App\Models\InventoryItem::find($commission->item_id);
                                            ?>
                                            <?php echo e($product->name ?? 'POS Product'); ?>

                                        <?php elseif($commission->item_type == 'package'): ?>
                                            <span class="badge bg-info bg-opacity-10 text-info">Package</span>
                                            <?php
                                                $package = \App\Models\Package::find($commission->item_id);
                                            ?>
                                            <?php echo e($package->name ?? 'POS Package'); ?>

                                        <?php elseif($commission->item_type == 'membership'): ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning">Membership</span>
                                            <?php
                                                $membership = \App\Models\Membership::find($commission->item_id);
                                            ?>
                                            <?php echo e($membership->name ?? 'POS Membership'); ?>

                                        <?php elseif($commission->item_type == 'tip'): ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning">Tip</span>
                                            POS Sale #<?php echo e($commission->posSale->invoice_number ?? 'N/A'); ?>

                                        <?php elseif($commission->item_type == 'target'): ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">Target</span>
                                            Period: <?php echo e($commission->period_start); ?> to <?php echo e($commission->period_end); ?>

                                        <?php else: ?>
                                            <?php echo e(ucfirst($commission->item_type)); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e(currency_symbol()); ?><?php echo e(number_format($commission->sale_amount, 2)); ?></td>
                                    <td class="font-weight-bold text-white">
                                        <?php echo e(currency_symbol()); ?><?php echo e(number_format($commission->commission_amount, 2)); ?>

                                    </td>
                                    <td>
                                        <?php if($commission->status == 'paid'): ?>
                                            <span class="badge bg-success">Paid</span>
                                            <div class="small text-muted mt-1">
                                                <?php echo e($commission->paid_at ? $commission->paid_at->format('M d') : ''); ?>

                                            </div>
                                        <?php elseif($commission->status == 'approved'): ?>
                                            <span class="badge bg-info">Approved</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($commission->booking_id): ?>
                                            <button class="btn btn-sm btn-info btn-circle"
                                                onclick="viewAppointment(<?php echo e($commission->booking_id); ?>)" title="View Appointment">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">No commissions found for this period</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <?php echo e($commissions->appends(request()->all())->links()); ?>

                </div>
            </div>
        </div>
    </div>

    <!-- View Appointment Modal -->
    <div class="modal fade" id="viewAppointmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-check text-primary me-2"></i>
                        Appointment Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" id="appointmentDetails">
                    <!-- Loading State -->
                    <div id="loadingState" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading appointment details...</p>
                    </div>

                    <!-- Content will be loaded here -->
                    <div id="appointmentContent" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Currency settings
            const currencySymbol = '<?php echo e(currency_symbol()); ?>';

            $(document).ready(function () {
                // Helper function to get salon slug from URL (reused from other views)
                function getSalonSlug() {
                    const bladeSlug = '<?php echo e(request()->route("salon_slug") ?? ""); ?>';
                    if (bladeSlug) return bladeSlug;
                    const pathParts = window.location.pathname.split('/').filter(p => p);
                    return pathParts[0] || '';
                }

                // View Appointment Function
                function viewAppointment(id) {
                    const modal = $('#viewAppointmentModal');
                    const loadingState = $('#loadingState');
                    const content = $('#appointmentContent');

                    // Show loading state
                    loadingState.show();
                    content.hide().empty();

                    // Show modal
                    modal.modal('show');

                    // Fetch appointment details
                    const salonSlug = getSalonSlug();
                    $.ajax({
                        url: `/${salonSlug}/employee/appointments/${id}`,
                        method: 'GET',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.success) {
                                const appointment = response.data;
                                const startTime = new Date(appointment.start_time);
                                const endTime = new Date(startTime.getTime() + (appointment.service?.duration || 0) * 60000);

                                // Format times
                                const timeOptions = { hour: '2-digit', minute: '2-digit' };
                                const dateString = startTime.toLocaleDateString('en-US', {
                                    weekday: 'short',
                                    month: 'short',
                                    day: 'numeric',
                                    year: 'numeric'
                                });
                                const timeString = `${startTime.toLocaleTimeString('en-US', timeOptions)} - ${endTime.toLocaleTimeString('en-US', timeOptions)}`;

                                const html = `
                                                        <div class="p-4">
                                                            <!-- Header with customer info -->
                                                            <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                                                                <div class="flex-shrink-0 me-3">
                                                                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(appointment.customer?.name || 'Customer')}&background=4f46e5&color=fff" 
                                                                         alt="${appointment.customer?.name || 'Customer'}"
                                                                         class="rounded-circle" width="64" height="64">
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <h4 class="mb-1">${appointment.customer?.name || 'N/A'}</h4>
                                                                    <div class="text-muted mb-2">${appointment.customer?.email || ''}</div>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="badge bg-success bg-opacity-10 text-white">
                                                                            <i class="fas fa-check-circle me-1"></i> ${appointment.status ? appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1) : 'N/A'}
                                                                        </span>
                                                                        <span class="ms-2 text-muted">
                                                                            <i class="far fa-calendar-alt me-1"></i> ${dateString}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row g-4">
                                                                <!-- Appointment Details -->
                                                                <div class="col-md-6">
                                                                    <div class="card border-0 shadow-sm h-100">
                                                                        <div class="card-body">
                                                                            <h5 class="card-title d-flex align-items-center mb-3">
                                                                                <i class="fas fa-calendar-day text-primary me-2"></i>
                                                                                Appointment
                                                                            </h5>
                                                                            <ul class="list-unstyled mb-0">
                                                                                <li class="d-flex mb-2">
                                                                                    <i class="fas fa-cut text-muted mt-1 me-2" style="width: 20px;"></i>
                                                                                    <div>
                                                                                        <div class="fw-semibold">${appointment.service?.name || 'N/A'}</div>
                                                                                        <small class="text-muted">${appointment.service?.category?.name || 'Service'}</small>
                                                                                    </div>
                                                                                </li>
                                                                                <li class="d-flex mb-2">
                                                                                    <i class="far fa-clock text-muted mt-1 me-2" style="width: 20px;"></i>
                                                                                    <div>
                                                                                        <div>${timeString}</div>
                                                                                        <small class="text-muted">${appointment.service?.duration || 0} minutes</small>
                                                                                    </div>
                                                                                </li>
                                                                                <li class="d-flex">
                                                                                    <i class="fas fa-tag text-muted mt-1 me-2" style="width: 20px;"></i>
                                                                                    <div>
                                                                                        <div>$${parseFloat(appointment.amount || 0).toFixed(2)}</div>
                                                                                        <small class="text-muted">Total Amount</small>
                                                                                    </div>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Service Provider & Payment -->
                                                                <div class="col-md-6">
                                                                    <div class="card border-0 shadow-sm h-100">
                                                                        <div class="card-body">
                                                                            <h5 class="card-title d-flex align-items-center mb-3">
                                                                                <i class="fas fa-user-tie text-primary me-2"></i>
                                                                                Service Provider
                                                                            </h5>
                                                                            <div class="d-flex align-items-center mb-4">
                                                                                <div class="flex-shrink-0 me-3">
                                                                                    <div class="avatar avatar-md bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center">
                                                                                        <i class="fas fa-user-tie"></i>
                                                                                    </div>
                                                                                </div>
                                                                                <div>
                                                                                    <h6 class="mb-0">${appointment.staff?.name || 'Not Assigned'}</h6>
                                                                                    <small class="text-muted">${appointment.staff?.role || 'Staff'}</small>
                                                                                </div>
                                                                            </div>

                                                                            <h5 class="card-title d-flex align-items-center mb-3">
                                                                                <i class="fas fa-credit-card text-primary me-2"></i>
                                                                                Payment
                                                                            </h5>
                                                                            <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                                                                <div>
                                                                                    <div class="fw-semibold">${appointment.payment_status || 'Pending'}</div>
                                                                                    <small class="text-muted">Payment Status</small>
                                                                                </div>
                                                                                <div class="text-end">
                                                                                    <div class="fw-semibold">$${parseFloat(appointment.amount || 0).toFixed(2)}</div>
                                                                                    <small class="text-muted">Amount Paid</small>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Notes & Actions -->
                                                                <div class="col-12">
                                                                    <div class="card border-0 shadow-sm">
                                                                        <div class="card-body">
                                                                            <h5 class="card-title d-flex align-items-center mb-3">
                                                                                <i class="fas fa-sticky-note text-primary me-2"></i>
                                                                                Notes
                                                                            </h5>
                                                                            <div class="bg-light p-3 rounded">
                                                                                ${appointment.notes ?
                                        `<p class="mb-0">${appointment.notes}</p>` :
                                        '<p class="text-muted mb-0">No notes available for this appointment.</p>'
                                    }
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    `;

                                content.html(html);
                                loadingState.hide();
                                content.slideDown();
                            }
                        },
                        error: function (xhr, status, error) {

                            let errorMessage = 'We couldn\'t load the appointment details. Please check your connection and try again.';

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.status === 404) {
                                errorMessage = 'Appointment not found. It may have been deleted or moved.';
                            } else if (xhr.status >= 500) {
                                errorMessage = 'Server error occurred. Please try again later.';
                            }

                            loadingState.html(`
                                                    <div class="text-center py-5">
                                                        <i class="fas fa-exclamation-triangle text-danger fa-2x mb-3"></i>
                                                        <h5 class="text-danger mb-3">Failed to load appointment details</h5>
                                                        <p class="text-muted mb-2">${errorMessage}</p>
                                                        <p class="small text-muted mb-4">Status: ${xhr.status} - ${xhr.statusText || 'Unknown Error'}</p>
                                                        <button class="btn btn-primary" onclick="viewAppointment(${id})">
                                                            <i class="fas fa-sync-alt me-2"></i> Try Again
                                                        </button>
                                                    </div>
                                                `);
                        }
                    });
                }

                // Reset modal when closed
                $('#viewAppointmentModal').on('hidden.bs.modal', function () {
                    const content = $('#appointmentContent');
                    content.hide().empty();
                    $('#loadingState').show();
                });

                // Charts
                document.addEventListener('DOMContentLoaded', function () {
                    // Earnings Chart
                    const ctx = document.getElementById('earningsChart').getContext('2d');
                    const months = <?php echo json_encode($monthlyEarnings->keys()); ?>;
                    const paidData = <?php echo json_encode($monthlyEarnings->map(fn($m) => $m->where('status', 'paid')->sum('total'))->values()); ?>;
                    const pendingData = <?php echo json_encode($monthlyEarnings->map(fn($m) => $m->where('status', '!=', 'paid')->sum('total'))->values()); ?>;

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: months,
                            datasets: [{
                                label: 'Paid Earnings',
                                data: paidData,
                                backgroundColor: '#1cc88a',
                                borderRadius: 4
                            }, {
                                label: 'Pending/Approved',
                                data: pendingData,
                                backgroundColor: '#f6c23e',
                                borderRadius: 4
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            scales: {
                                x: { stacked: true },
                                y: {
                                    stacked: true,
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function (value) {
                                            return currencySymbol + value;
                                        }
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            return context.dataset.label + ': $' + context.raw.toFixed(2);
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Sources Chart
                    const pieCtx = document.getElementById('sourcesChart').getContext('2d');
                    const breakdownLabels = <?php echo json_encode($breakdown->pluck('item_type')->map(fn($t) => ucfirst($t))); ?>;
                    const breakdownData = <?php echo json_encode($breakdown->pluck('total')); ?>;

                    new Chart(pieCtx, {
                        type: 'doughnut',
                        data: {
                            labels: breakdownLabels,
                            datasets: [{
                                data: breakdownData,
                                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a'],
                                hoverBorderColor: "rgba(234, 236, 244, 1)",
                            }],
                        },
                        options: {
                            maintainAspectRatio: false,
                            tooltips: {
                                backgroundColor: "rgb(255,255,255)",
                                bodyFontColor: "#858796",
                                borderColor: '#dddfeb',
                                borderWidth: 1,
                                xPadding: 15,
                                yPadding: 15,
                                displayColors: false,
                                caretPadding: 10,
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            return context.label + ': $' + context.raw.toFixed(2);
                                        }
                                    }
                                }
                            },
                            cutout: '80%',
                        },
                    });
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\staff\earnings.blade.php ENDPATH**/ ?>