<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 fw-bold text-gray-800">Payment Details</h1>
                <p class="text-muted mb-0">Transaction #<?php echo e($payment->transaction_id); ?></p>
            </div>
            <a href="<?php echo e(route('admin.saas.payment.history')); ?>" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to History
            </a>
        </div>

        <div class="row">
            <!-- Payment Information -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold">Payment Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Transaction ID</label>
                                <div class="fw-semibold">
                                    <code><?php echo e($payment->transaction_id); ?></code>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Payment Date</label>
                                <div class="fw-semibold">
                                    <?php echo e($payment->created_at->format('F d, Y \a\t h:i A')); ?>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Amount</label>
                                <div class="fw-bold text-white fs-4">
                                    <?php echo e(system_currency_symbol()); ?><?php echo e(number_format($payment->amount, 2)); ?>

                                    <?php echo e(strtoupper($payment->currency)); ?>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Payment Method</label>
                                <div class="fw-semibold">
                                    <span class="badge bg-secondary px-3 py-2">
                                        <i class="fas fa-credit-card me-1"></i><?php echo e(ucfirst($payment->payment_method)); ?>

                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Status</label>
                                <div>
                                    <?php if($payment->status === 'completed'): ?>
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i>Completed
                                        </span>
                                    <?php elseif($payment->status === 'pending'): ?>
                                        <span class="badge bg-warning px-3 py-2">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </span>
                                    <?php elseif($payment->status === 'failed'): ?>
                                        <span class="badge bg-danger px-3 py-2">
                                            <i class="fas fa-times-circle me-1"></i>Failed
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary px-3 py-2"><?php echo e(ucfirst($payment->status)); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if($payment->paid_at): ?>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">Paid At</label>
                                    <div class="fw-semibold">
                                        <?php echo e($payment->paid_at->format('F d, Y \a\t h:i A')); ?>

                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Subscription Details -->
                <?php if($payment->subscription && $payment->subscription->plan): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-bold">Subscription Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">Plan Name</label>
                                    <div class="fw-semibold">
                                        <span class="badge bg-primary px-3 py-2"><?php echo e($payment->subscription->plan->name); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">Plan Price</label>
                                    <div class="fw-semibold">
                                        <?php echo e(system_format_currency($payment->subscription->plan->price)); ?>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">Subscription Period</label>
                                    <div class="fw-semibold">
                                        <?php echo e($payment->subscription->starts_at->format('M d, Y')); ?> -
                                        <?php echo e($payment->subscription->ends_at->format('M d, Y')); ?>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">Duration</label>
                                    <div class="fw-semibold">
                                        <?php echo e($payment->subscription->plan->duration_in_days); ?> days
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Actions Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <?php if($payment->subscription_id): ?>
                                <a href="<?php echo e(route('admin.saas.subscription.invoice', $payment->subscription_id)); ?>"
                                    class="btn btn-primary">
                                    <i class="fas fa-file-invoice me-2"></i>View Invoice
                                </a>
                                <a href="<?php echo e(route('admin.invoices.download', $payment->id)); ?>"
                                    class="btn btn-outline-secondary">
                                    <i class="fas fa-download me-2"></i>Download Invoice
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo e(route('admin.saas.payment.history')); ?>" class="btn btn-outline-primary">
                                <i class="fas fa-list me-2"></i>All Payments
                            </a>
                            <a href="<?php echo e(route('admin.saas.subscription.index')); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-crown me-2"></i>My Subscription
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Payment Timeline -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold">Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <div class="small text-muted"><?php echo e($payment->created_at->format('M d, Y h:i A')); ?></div>
                                    <div class="fw-semibold">Payment Created</div>
                                </div>
                            </div>
                            <?php if($payment->paid_at): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <div class="small text-muted"><?php echo e($payment->paid_at->format('M d, Y h:i A')); ?></div>
                                        <div class="fw-semibold">Payment Completed</div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('styles'); ?>
        <style>
            .timeline {
                position: relative;
                padding-left: 30px;
            }

            .timeline-item {
                position: relative;
                padding-bottom: 20px;
            }

            .timeline-item:not(:last-child)::before {
                content: '';
                position: absolute;
                left: -22px;
                top: 20px;
                width: 2px;
                height: calc(100% - 10px);
                background: #e9ecef;
            }

            .timeline-marker {
                position: absolute;
                left: -26px;
                top: 4px;
                width: 10px;
                height: 10px;
                border-radius: 50%;
                border: 2px solid white;
                box-shadow: 0 0 0 2px #e9ecef;
            }

            .timeline-content {
                padding-left: 10px;
            }
        </style>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\saas\payments\show.blade.php ENDPATH**/ ?>