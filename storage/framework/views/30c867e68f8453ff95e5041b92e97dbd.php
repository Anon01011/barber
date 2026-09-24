<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 fw-bold text-gray-800">Invoice</h1>
                <p class="text-muted mb-0">Subscription #<?php echo e($subscription->id); ?></p>
            </div>
            <div>
                <a href="<?php echo e(route('admin.saas.subscription.history')); ?>" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print me-2"></i>Print Invoice
                </button>
            </div>
        </div>

        <!-- Invoice Card -->
        <div class="card border-0 shadow-sm" id="invoice">
            <div class="card-body p-5">
                <!-- Invoice Header -->
                <div class="row mb-5">
                    <div class="col-md-6">
                        <h2 class="fw-bold text-primary mb-3">INVOICE</h2>
                        <div class="text-muted">
                            <p class="mb-1"><strong>Invoice #:</strong>
                                INV-<?php echo e(str_pad($subscription->id, 6, '0', STR_PAD_LEFT)); ?></p>
                            <p class="mb-1"><strong>Date:</strong> <?php echo e($subscription->created_at->format('M d, Y')); ?></p>
                            <p class="mb-1"><strong>Status:</strong>
                                <?php if($subscription->status === 'active'): ?>
                                    <span class="badge bg-success">Paid</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?php echo e(ucfirst($subscription->status)); ?></span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h4 class="fw-bold mb-2"><?php echo e(config('app.name', 'Salon CMS')); ?></h4>
                        <p class="text-muted mb-0">
                            SaaS Subscription Service<br>
                            support@salonpro.com<br>
                            www.salonpro.com
                        </p>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Bill To -->
                <div class="row mb-5">
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Bill To:</h5>
                        <p class="mb-1"><strong><?php echo e($salon->name); ?></strong></p>
                        <p class="text-muted mb-0">
                            <?php echo e($salon->email); ?><br>
                            <?php if($salon->phone): ?><?php echo e($salon->phone); ?><br><?php endif; ?>
                            <?php if($salon->address): ?><?php echo e($salon->address); ?><br><?php endif; ?>
                            <?php if($salon->city): ?><?php echo e($salon->city); ?>, <?php endif; ?>
                            <?php if($salon->state): ?><?php echo e($salon->state); ?> <?php endif; ?>
                            <?php if($salon->zip_code): ?><?php echo e($salon->zip_code); ?><?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Subscription Period:</h5>
                        <p class="text-muted mb-0">
                            <strong>Start Date:</strong> <?php echo e($subscription->starts_at->format('M d, Y')); ?><br>
                            <strong>End Date:</strong>
                            <?php echo e($subscription->ends_at ? $subscription->ends_at->format('M d, Y') : 'Lifetime'); ?><br>
                            <strong>Duration:</strong> <?php echo e($subscription->plan->duration_in_days); ?> days
                        </p>
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="table-responsive mb-5">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Description</th>
                                <th class="border-0 text-center">Duration</th>
                                <th class="border-0 text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <strong><?php echo e($subscription->plan->name); ?></strong>
                                    <div class="small text-muted">Subscription Plan</div>
                                </td>
                                <td class="text-center"><?php echo e($subscription->plan->duration_in_days); ?> days</td>
                                <td class="text-end"><?php echo e(system_format_currency($subscription->plan->price)); ?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-end border-0 pt-4"><strong>Subtotal:</strong></td>
                                <td class="text-end border-0 pt-4"><?php echo e(system_format_currency($subscription->plan->price)); ?>

                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-end border-0"><strong>Tax (0%):</strong></td>
                                <td class="text-end border-0"><?php echo e(system_format_currency(0)); ?></td>
                            </tr>
                            <tr class="table-light">
                                <td colspan="2" class="text-end border-0 pt-3">
                                    <h5 class="mb-0"><strong>Total:</strong></h5>
                                </td>
                                <td class="text-end border-0 pt-3">
                                    <h5 class="mb-0 text-primary">
                                        <strong><?php echo e(system_format_currency($subscription->plan->price)); ?></strong>
                                    </h5>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Payment Info -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-light border">
                            <h6 class="fw-bold mb-2">Payment Information</h6>
                            <p class="mb-1 small text-muted">
                                <?php
                                    $payment = $subscription->latestPayment;
                                ?>
                                <strong>Payment Method:</strong>
                                <?php echo e($payment ? ucfirst($payment->payment_method) : ($subscription->payment_method ?? 'Not specified')); ?><br>
                                <strong>Transaction ID:</strong>
                                <?php echo e($payment ? $payment->transaction_id : ($subscription->payment_id ?? 'N/A')); ?><br>
                                <strong>Payment Date:</strong>
                                <?php echo e($payment ? $payment->created_at->format('M d, Y h:i A') : $subscription->created_at->format('M d, Y h:i A')); ?>

                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-5 pt-4 border-top">
                    <p class="text-muted small mb-0">
                        Thank you for your business!<br>
                        For questions about this invoice, please contact support@salonpro.com
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('styles'); ?>
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }

                #invoice,
                #invoice * {
                    visibility: visible;
                }

                #invoice {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    margin: 0;
                    padding: 0;
                    box-shadow: none !important;
                    border: none !important;
                    background-color: white;
                }

                .btn,
                .navbar,
                .sidebar,
                #sidebar,
                .header,
                #header,
                nav,
                .no-print,
                .toggle-sidebar {
                    display: none !important;
                }

                .main-content {
                    margin: 0 !important;
                    padding: 0 !important;
                    width: 100% !important;
                }

                .container-fluid {
                    padding: 0 !important;
                    margin: 0 !important;
                }
            }
        </style>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\saas\subscription\invoice.blade.php ENDPATH**/ ?>