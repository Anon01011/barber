<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Subscription Details #<?php echo e($subscription->id); ?></h1>
            <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>



        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Subscription Information</h6>
                        <?php if($subscription->status === 'active'): ?>
                            <span class="badge badge-success">Active</span>
                        <?php elseif($subscription->status === 'pending'): ?>
                            <span class="badge badge-warning">Pending</span>
                        <?php elseif($subscription->status === 'cancelled'): ?>
                            <span class="badge badge-danger">Cancelled</span>
                        <?php elseif($subscription->status === 'paused'): ?>
                            <span class="badge badge-info">Paused</span>
                        <?php elseif($subscription->status === 'expired'): ?>
                            <span class="badge badge-secondary">Expired</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">Salon:</th>
                                <td>
                                    <a href="<?php echo e(route('admin.salons.show', $subscription->salon ?? $subscription->salon_id)); ?>">
                                        <?php echo e($subscription->salon->name); ?>

                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Plan:</th>
                                <td><?php echo e($subscription->plan->name); ?></td>
                            </tr>
                            <tr>
                                <th>Price:</th>
                                <td>
                                    <h5 class="text-success"><?php echo e(system_currency_symbol()); ?>

                                        <?php echo e(number_format($subscription->plan->price, 2)); ?>

                                    </h5>
                                </td>
                            </tr>
                            <tr>
                                <th>Duration:</th>
                                <td><?php echo e($subscription->plan->duration_in_days); ?> days</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <?php if($subscription->status === 'active'): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php elseif($subscription->status === 'pending'): ?>
                                        <span class="badge badge-warning">Pending</span>
                                    <?php elseif($subscription->status === 'cancelled'): ?>
                                        <span class="badge badge-danger">Cancelled</span>
                                    <?php elseif($subscription->status === 'paused'): ?>
                                        <span class="badge badge-info">Paused</span>
                                    <?php elseif($subscription->status === 'expired'): ?>
                                        <span class="badge badge-secondary">Expired</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Started:</th>
                                <td><?php echo e($subscription->starts_at ? $subscription->starts_at->format('M d, Y H:i') : 'N/A'); ?>

                                </td>
                            </tr>
                            <tr>
                                <th>Ends:</th>
                                <td>
                                    <?php echo e($subscription->ends_at ? $subscription->ends_at->format('M d, Y H:i') : 'N/A'); ?>

                                    <?php if($subscription->ends_at && $subscription->ends_at->isFuture()): ?>
                                        <span class="text-muted">(<?php echo e($subscription->ends_at->diffForHumans()); ?>)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Trial Ends:</th>
                                <td><?php echo e($subscription->trial_ends_at ? $subscription->trial_ends_at->format('M d, Y') : 'N/A'); ?>

                                </td>
                            </tr>
                            <?php if($subscription->cancelled_at): ?>
                                <tr>
                                    <th>Cancelled At:</th>
                                    <td class="text-danger"><?php echo e($subscription->cancelled_at->format('M d, Y H:i')); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if($subscription->paused_at): ?>
                                <tr>
                                    <th>Paused At:</th>
                                    <td class="text-info"><?php echo e($subscription->paused_at->format('M d, Y H:i')); ?></td>
                                </tr>
                            <?php endif; ?>
                        </table>

                        <hr>

                        <h6 class="font-weight-bold">Manage Subscription</h6>
                        <form method="POST" action="<?php echo e(route('admin.subscriptions.update', $subscription->id)); ?>"
                            class="mt-3">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="active" <?php echo e($subscription->status === 'active' ? 'selected' : ''); ?>>
                                            Active</option>
                                        <option value="paused" <?php echo e($subscription->status === 'paused' ? 'selected' : ''); ?>>
                                            Paused</option>
                                        <option value="cancelled" <?php echo e($subscription->status === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                                        <option value="expired" <?php echo e($subscription->status === 'expired' ? 'selected' : ''); ?>>
                                            Expired</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="ends_at" class="form-label">End Date</label>
                                    <input type="date" name="ends_at" id="ends_at" class="form-control"
                                        value="<?php echo e($subscription->ends_at ? $subscription->ends_at->format('Y-m-d') : ''); ?>">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Subscription
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <?php if($subscription->status === 'pending'): ?>
                            <form method="POST" action="<?php echo e(route('admin.subscriptions.approve', $subscription->id)); ?>"
                                class="mb-2">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-success btn-block"
                                    onclick="return confirm('Approve this subscription?')">
                                    <i class="fas fa-check"></i> Approve Subscription
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if($subscription->status === 'active'): ?>
                            <form method="POST" action="<?php echo e(route('admin.subscriptions.extend', $subscription->id)); ?>"
                                class="mb-2">
                                <?php echo csrf_field(); ?>
                                <div class="input-group mb-2">
                                    <input type="number" name="days" class="form-control" placeholder="Days" min="1" value="30"
                                        required>
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-calendar-plus"></i> Extend
                                    </button>
                                </div>
                            </form>

                            <button type="button" class="btn btn-warning btn-block mb-2" data-toggle="modal"
                                data-target="#pauseModal">
                                <i class="fas fa-pause"></i> Pause Subscription
                            </button>

                            <button type="button" class="btn btn-danger btn-block" data-toggle="modal"
                                data-target="#cancelModal">
                                <i class="fas fa-times"></i> Cancel Subscription
                            </button>
                        <?php endif; ?>

                        <?php if($subscription->status === 'paused'): ?>
                            <form method="POST" action="<?php echo e(route('admin.subscriptions.resume', $subscription->id)); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-success btn-block"
                                    onclick="return confirm('Resume this subscription?')">
                                    <i class="fas fa-play"></i> Resume Subscription
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if($subscription->status === 'cancelled' || $subscription->status === 'expired'): ?>
                            <div class="alert alert-info">
                                No actions available for <?php echo e($subscription->status); ?> subscriptions.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Plan Features -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Plan Features</h6>
                    </div>
                    <div class="card-body">
                        <?php
                            $features = $subscription->plan->features;
                            if (is_string($features)) {
                                $features = json_decode($features, true) ?? [];
                            }
                        ?>
                        <?php if($features && is_array($features) && count($features) > 0): ?>
                            <ul class="list-unstyled">
                                <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success"></i> <?php echo e($feature); ?>

                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted">No features defined</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="<?php echo e(route('admin.subscriptions.cancel', $subscription->id)); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Cancel Subscription</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <strong>Warning:</strong> This will cancel the subscription immediately.
                        </div>
                        <div class="form-group">
                            <label>Cancellation Reason (Optional)</label>
                            <textarea name="cancellation_reason" class="form-control" rows="3" maxlength="500"
                                placeholder="Enter reason for cancellation..."></textarea>
                            <small class="form-text text-muted">Maximum 500 characters</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Cancel Subscription</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Pause Modal -->
    <div class="modal fade" id="pauseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="<?php echo e(route('admin.subscriptions.pause', $subscription->id)); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Pause Subscription</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Note:</strong> This will temporarily pause the subscription. You can resume it later.
                        </div>
                        <p>Are you sure you want to pause this subscription?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning">Pause Subscription</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\super-admin\subscriptions\show.blade.php ENDPATH**/ ?>