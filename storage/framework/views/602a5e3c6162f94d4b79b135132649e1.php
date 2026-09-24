<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>Employee Bookings: <?php echo e($employee->name); ?>

                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.super.dashboard')); ?>"><i class="fas fa-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.employees.index')); ?>">Employees</a></li>
                        <li class="breadcrumb-item active">Bookings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-2">
                    <img src="<?php echo e($employee->avatar ? asset('storage/' . $employee->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($employee->name) . '&background=random'); ?>" 
                         alt="<?php echo e($employee->name); ?>" 
                         class="img-fluid rounded-circle" 
                         style="width: 100px; height: 100px; object-fit: cover;">
                </div>
                <div class="col-md-10">
                    <h4><?php echo e($employee->name); ?></h4>
                    <p class="text-muted mb-0"><?php echo e($employee->position ?? 'Employee'); ?></p>
                    <p class="text-muted"><?php echo e($employee->email); ?></p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-centered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date & Time</th>
                            <th>Customer</th>
                            <th>Services</th>
                            <th>Status</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($booking->id); ?></td>
                            <td>
                                <?php echo e(format_date($booking->booking_date)); ?><br>
                                <small class="text-muted"><?php echo e(format_time($booking->start_time)); ?> - <?php echo e(format_time($booking->end_time)); ?></small>
                            </td>
                            <td>
                                <?php echo e($booking->customer->name ?? 'N/A'); ?><br>
                                <small class="text-muted"><?php echo e($booking->customer->display_phone ?? ''); ?></small>
                            </td>
                            <td>
                                <?php $__currentLoopData = $booking->services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge bg-primary mb-1"><?php echo e($service->name); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </td>
                            <td>
                                <?php if($booking->status == 'completed'): ?>
                                    <span class="badge bg-success">Completed</span>
                                <?php elseif($booking->status == 'confirmed'): ?>
                                    <span class="badge bg-info">Confirmed</span>
                                <?php elseif($booking->status == 'cancelled'): ?>
                                    <span class="badge bg-danger">Cancelled</span>
                                <?php else: ?>
                                    <span class="badge bg-warning"><?php echo e(ucfirst($booking->status)); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e(currency_symbol()); ?><?php echo e(number_format($booking->total_amount, 2)); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center">No bookings found for this employee.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <?php echo e($bookings->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\employees\bookings.blade.php ENDPATH**/ ?>