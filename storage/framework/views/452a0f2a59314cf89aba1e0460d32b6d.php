<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Invoice #<?php echo e($payment->transaction_id); ?></h1>
        <div>
            <a href="<?php echo e(route('admin.invoices.download', $payment->id)); ?>" class="btn btn-primary">
                <i class="fas fa-download me-2"></i>Download PDF
            </a>
            <a href="<?php echo e(url()->previous()); ?>" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-sm-6">
                    <h5 class="mb-3">From:</h5>
                    <h3 class="text-dark mb-1">Salon CMS</h3>
                    <div>123 Admin Street</div>
                    <div>New York, NY 10001</div>
                    <div>Email: support@saloncms.com</div>
                    <div>Phone: +1 (555) 123-4567</div>
                </div>
                <div class="col-sm-6 text-sm-end">
                    <h5 class="mb-3">To:</h5>
                    <h3 class="text-dark mb-1"><?php echo e($payment->salon->name); ?></h3>
                    <div><?php echo e($payment->salon->address); ?></div>
                    <div>Email: <?php echo e($payment->salon->email); ?></div>
                    <div>Phone: <?php echo e($payment->salon->phone); ?></div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="center">#</th>
                            <th>Item</th>
                            <th>Description</th>
                            <th class="right">Unit Cost</th>
                            <th class="center">Qty</th>
                            <th class="right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="center">1</td>
                            <td class="strong"><?php echo e($payment->subscription->plan->name); ?> Subscription</td>
                            <td class="left">Subscription for <?php echo e($payment->subscription->plan->duration_in_days); ?> days</td>
                            <td class="right"><?php echo e($payment->currency); ?> <?php echo e(number_format($payment->amount, 2)); ?></td>
                            <td class="center">1</td>
                            <td class="right"><?php echo e($payment->currency); ?> <?php echo e(number_format($payment->amount, 2)); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-lg-4 col-sm-5 ms-auto">
                    <table class="table table-clear">
                        <tbody>
                            <tr>
                                <td class="left"><strong>Subtotal</strong></td>
                                <td class="right"><?php echo e($payment->currency); ?> <?php echo e(number_format($payment->amount, 2)); ?></td>
                            </tr>
                            <tr>
                                <td class="left"><strong>Total</strong></td>
                                <td class="right"><strong><?php echo e($payment->currency); ?> <?php echo e(number_format($payment->amount, 2)); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\super-admin\invoices\show.blade.php ENDPATH**/ ?>