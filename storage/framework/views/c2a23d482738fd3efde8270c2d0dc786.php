<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Subscription Management</h1>
        </div>

        <!-- Filter -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Active</option>
                            <option value="paused" <?php echo e(request('status') === 'paused' ? 'selected' : ''); ?>>Paused</option>
                            <option value="cancelled" <?php echo e(request('status') === 'cancelled' ? 'selected' : ''); ?>>Cancelled
                            </option>
                            <option value="expired" <?php echo e(request('status') === 'expired' ? 'selected' : ''); ?>>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Subscriptions Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">All Subscriptions (<?php echo e($subscriptions->total()); ?>)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Salon</th>
                                <th>Plan</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Started</th>
                                <th>Ends</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($subscription->id); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('admin.salons.show', $subscription->salon ?? $subscription->salon_id)); ?>">
                                            <?php echo e($subscription->salon->name); ?>

                                        </a>
                                    </td>
                                    <td><?php echo e($subscription->plan->name); ?></td>
                                    <td><?php echo e(system_currency_symbol()); ?> <?php echo e(number_format($subscription->plan->price, 2)); ?></td>
                                    <td>
                                        <?php if($subscription->status === 'active'): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php elseif($subscription->status === 'pending'): ?>
                                            <span class="badge badge-warning">Pending</span>
                                        <?php elseif($subscription->status === 'paused'): ?>
                                            <span class="badge badge-info">Paused</span>
                                        <?php elseif($subscription->status === 'cancelled'): ?>
                                            <span class="badge badge-danger">Cancelled</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><?php echo e(ucfirst($subscription->status)); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($subscription->starts_at->format('M d, Y')); ?></td>
                                    <td><?php echo e($subscription->ends_at ? $subscription->ends_at->format('M d, Y') : 'N/A'); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('admin.subscriptions.show', $subscription->id)); ?>"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if($subscription->status === 'pending'): ?>
                                            <form action="<?php echo e(route('admin.subscriptions.approve', $subscription->id)); ?>"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to approve this subscription?');">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-success" title="Approve Subscription">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No subscriptions found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <?php echo e($subscriptions->links()); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\super-admin\subscriptions\index.blade.php ENDPATH**/ ?>