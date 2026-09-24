<div class="table-responsive p-0">
    <table class="table align-items-center mb-0">
        <thead>
            <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Customer</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Service & Staff
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Schedule</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end pe-4">Actions
                </th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="booking-row">
                    <td class="ps-4">
                        <div class="d-flex py-1">
                            <div
                                class="avatar avatar-sm me-3 bg-gradient-light rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-user text-secondary text-xs"></i>
                            </div>
                            <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-sm font-weight-bold">
                                    <?php echo e($booking->customer->name ?? $booking->customer->user->name ?? 'Guest'); ?>

                                </h6>
                                <p class="text-xs text-secondary mb-0">
                                    <?php echo e($booking->customer->display_email ?? $booking->customer->user->email ?? 'No email'); ?>

                                </p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm"><?php echo e($booking->service->name); ?></h6>
                            <p class="text-xs text-secondary mb-0">
                                <i class="fas fa-user-tie me-1"></i><?php echo e($booking->staff->name ?? 'Unassigned'); ?>

                            </p>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column justify-content-center">
                            <span class="text-sm font-weight-bold text-dark">
                                <?php echo e($booking->start_time->setTimezone(salon_timezone())->format('M d, Y')); ?>

                            </span>
                            <span class="text-xs text-secondary">
                                <i
                                    class="far fa-clock me-1"></i><?php echo e($booking->start_time->setTimezone(salon_timezone())->format('g:i A')); ?>

                            </span>
                        </div>
                    </td>
                    <td>
                        <?php
                            $statusConfig = [
                                'pending' => ['class' => 'bg-light text-secondary', 'icon' => 'fa-clock'],
                                'confirmed' => ['class' => 'bg-success-soft text-success', 'icon' => 'fa-check'],
                                'staff_completed' => ['class' => 'bg-info-soft text-info', 'icon' => 'fa-user-check'],
                                'completed' => ['class' => 'bg-primary-soft text-primary', 'icon' => 'fa-check-double'],
                                'cancelled' => ['class' => 'bg-danger-soft text-danger', 'icon' => 'fa-times'],
                                'no_show' => ['class' => 'bg-warning-soft text-warning', 'icon' => 'fa-user-slash'],
                                'partially_completed' => ['class' => 'bg-info-soft text-info', 'icon' => 'fa-user-clock'],
                            ];
                            $currentStatus = $booking->status;
                            $config = $statusConfig[$currentStatus] ?? $statusConfig['pending'];
                        ?>
                        <span class="badge badge-sm rounded-pill <?php echo e($config['class']); ?> font-weight-bold px-3">
                            <i class="fas <?php echo e($config['icon']); ?> me-1"></i>
                            <?php echo e(ucfirst(str_replace('-', ' ', $currentStatus))); ?>

                        </span>
                    </td>
                    <td class="align-middle text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-link text-secondary p-0 mb-0 view-booking"
                                data-booking-id="<?php echo e($booking->id); ?>" title="View Details">
                                <i class="fas fa-eye text-sm"></i>
                            </button>

                            <?php if(!in_array($booking->status, ['completed', 'cancelled'])): ?>
                                <?php if(auth()->user()->hasAnyRole(['super_admin', 'salon_admin', 'manager', 'receptionist'])): ?>
                                    <button class="btn btn-link text-primary p-0 mb-0"
                                        onclick="updateBookingStatus(<?php echo e($booking->id); ?>, 'completed', '<?php echo e($booking->start_time->toIso8601String()); ?>')"
                                        title="Complete Appointment">
                                        <i class="fas fa-check-circle text-sm"></i>
                                    </button>
                                <?php endif; ?>

                                <?php if(!in_array($booking->status, ['staff_completed'])): ?>
                                    <button class="btn btn-link text-info p-0 mb-0"
                                        onclick="updateBookingStatus(<?php echo e($booking->id); ?>, 'staff_completed', '<?php echo e($booking->start_time->toIso8601String()); ?>')"
                                        title="Mark as Staff Completed">
                                        <i class="fas fa-user-check text-sm"></i>
                                    </button>
                                <?php endif; ?>

                                <button class="btn btn-link text-danger p-0 mb-0"
                                    onclick="updateBookingStatus(<?php echo e($booking->id); ?>, 'cancelled', '<?php echo e($booking->start_time->toIso8601String()); ?>')"
                                    title="Cancel Appointment">
                                    <i class="fas fa-times-circle text-sm"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center">
                            <i class="fas fa-calendar-times text-secondary opacity-3 mb-3" style="font-size: 3rem;"></i>
                            <p class="text-secondary font-weight-bold">No appointments found.</p>
                            <p class="text-xs text-secondary">Try adjusting your filters or check another date.</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
    .booking-row {
        transition: all 0.2s ease;
    }

    .booking-row:hover {
        background-color: #f8f9fa;
    }

    .bg-success-soft {
        background-color: #e6fffa;
    }

    .bg-info-soft {
        background-color: #e6f6ff;
    }

    .bg-primary-soft {
        background-color: #f0f5ff;
    }

    .bg-danger-soft {
        background-color: #fff5f5;
    }

    .bg-warning-soft {
        background-color: #fffaf0;
    }

    .text-success {
        color: #38b2ac !important;
    }

    .text-info {
        color: #4299e1 !important;
    }

    .text-primary {
        color: #5a67d8 !important;
    }

    .text-danger {
        color: #f56565 !important;
    }

    .text-warning {
        color: #ed8936 !important;
    }

    .avatar-sm {
        width: 36px !important;
        height: 36px !important;
    }

    .btn-link:hover i {
        transform: scale(1.2);
        transition: transform 0.2s ease;
    }
</style><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\bookings\partials\booking_table.blade.php ENDPATH**/ ?>