<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 fw-bold text-gray-800">Payment History</h1>
                <p class="text-muted mb-0">View all your subscription payments and invoices</p>
            </div>
            <a href="<?php echo e(route('admin.saas.subscription.index')); ?>" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Subscription
            </a>
        </div>

        <!-- Payments Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <?php if($payments->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Transaction ID</th>
                                    <th>Plan</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold"><?php echo e($payment->created_at->format('M d, Y')); ?></div>
                                            <small class="text-muted"><?php echo e($payment->created_at->format('h:i A')); ?></small>
                                        </td>
                                        <td>
                                            <code class="small"><?php echo e($payment->transaction_id); ?></code>
                                        </td>
                                        <td>
                                            <?php if($payment->subscription && $payment->subscription->plan): ?>
                                                <span class="badge bg-primary"><?php echo e($payment->subscription->plan->name); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span
                                                class="fw-bold text-white"><?php echo e(system_currency_symbol()); ?><?php echo e(number_format($payment->amount, 2)); ?></span>
                                            <small class="text-muted"><?php echo e(strtoupper($payment->currency)); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo e(ucfirst($payment->payment_method)); ?></span>
                                        </td>
                                        <td>
                                            <?php if($payment->status === 'completed'): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Completed
                                                </span>
                                            <?php elseif($payment->status === 'pending'): ?>
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>Pending
                                                </span>
                                            <?php elseif($payment->status === 'failed'): ?>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Failed
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?php echo e(ucfirst($payment->status)); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?php echo e(route('admin.saas.payment.show', $payment->id)); ?>"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>
                                            <?php if($payment->subscription_id): ?>
                                                <a href="<?php echo e(route('admin.saas.subscription.invoice', $payment->subscription_id)); ?>"
                                                    class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-file-invoice me-1"></i>Invoice
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        <?php echo e($payments->links()); ?>

                    </div>
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Payment History</h5>
                        <p class="text-muted">You haven't made any payments yet.</p>
                        <a href="<?php echo e(route('admin.saas.subscription.index')); ?>" class="btn btn-primary mt-3">
                            <i class="fas fa-crown me-2"></i>View Plans
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\saas\payments\history.blade.php ENDPATH**/ ?>