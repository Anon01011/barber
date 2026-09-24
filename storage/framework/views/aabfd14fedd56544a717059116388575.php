<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-0 text-gray-800">
                    Staff Commission Report
                    <?php if(isset($branch) && $branch): ?>
                        <span class="badge bg-primary text-white ms-2"
                            style="font-size: 0.5em; vertical-align: middle;"><?php echo e($branch->name); ?></span>
                    <?php endif; ?>
                </h2>
                <p class="text-muted mb-0">Detailed commission breakdown by staff member</p>
            </div>

            <a href="<?php echo e(route('admin.reports.commissions.index')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Overview
            </a>
        </div>

        <!-- Filter Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filter Options</h6>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.reports.commissions.by-staff')); ?>" method="GET"
                    class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="staff_id" class="form-label">Staff Member</label>
                        <select name="staff_id" id="staff_id" class="form-select select2">
                            <option value="">All Staff</option>
                            <?php $__currentLoopData = $staffMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($staff->id); ?>" <?php echo e($staffId == $staff->id ? 'selected' : ''); ?>>
                                    <?php echo e($staff->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="<?php echo e($startDate->format('Y-m-d')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control"
                            value="<?php echo e($endDate->format('Y-m-d')); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Commission Details</h6>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    Total: <?php echo e(currency_symbol()); ?><?php echo e(number_format($summaryTotal, 2)); ?>

                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Staff</th>
                                <th>Item</th>
                                <th>Sale Amount</th>
                                <th>Tip</th>
                                <th>Commission</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $commissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <?php echo e(format_date($commission->created_at)); ?><br>
                                        <small class="text-muted"><?php echo e(format_time($commission->created_at)); ?></small>
                                    </td>
                                    <td><?php echo e($commission->staff->name); ?></td>
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
                                    <td>
                                        <?php if($commission->booking && $commission->booking->tip_amount > 0): ?>
                                            <span
                                                class="text-white">+<?php echo e(currency_symbol()); ?><?php echo e(number_format($commission->booking->tip_amount, 2)); ?></span>
                                        <?php elseif($commission->item_type == 'tip'): ?>
                                            <span
                                                class="text-white"><?php echo e(currency_symbol()); ?><?php echo e(number_format($commission->sale_amount, 2)); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="font-weight-bold text-white">
                                        <?php echo e(currency_symbol()); ?><?php echo e(number_format($commission->commission_amount, 2)); ?>

                                    </td>
                                    <td>
                                        <?php if($commission->status == 'paid'): ?>
                                            <span class="badge bg-success">Paid</span>
                                            <div class="small text-muted mt-1">
                                                <?php echo e($commission->paid_at ? format_date($commission->paid_at, 'M d') : ''); ?>

                                            </div>
                                        <?php elseif($commission->status == 'approved'): ?>
                                            <span class="badge bg-info">Approved</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">No commissions found matching your criteria</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted small">
                        Showing <?php echo e($commissions->firstItem() ?? 0); ?> to <?php echo e($commissions->lastItem() ?? 0); ?> of
                        <?php echo e($commissions->total()); ?> entries
                    </div>
                    <div>
                        <?php echo e($commissions->appends(request()->all())->links('pagination::bootstrap-5')); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            $(document).ready(function () {
                $('.select2').select2({
                    theme: 'bootstrap-5'
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\reports\commissions\by-staff.blade.php ENDPATH**/ ?>