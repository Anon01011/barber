<?php $__env->startSection('title', 'Sales History'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .sale-card {
            transition: all 0.2s ease-in-out;
        }

        .sale-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Sales History</h2>
                    <div>
                        <a href="<?php echo e(route('admin.pos.index')); ?>" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-1"></i> Back to POS
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="salesTable">
                        <thead>
                            <tr>
                                <th>Sale #</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th class="text-end">Total</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($sale->sale_number); ?></td>
                                    <td><?php echo e(format_datetime($sale->created_at)); ?></td>
                                    <td><?php echo e($sale->customer->name ?? 'Walk-in'); ?></td>
                                    <td><?php echo e($sale->items->sum('quantity')); ?> items</td>
                                    <td class="text-end"><?php echo e(format_currency($sale->total)); ?></td>
                                    <td>
                                        <span
                                            class="badge bg-<?php echo e($sale->payment_status === 'paid' ? 'success' : ($sale->payment_status === 'partially_paid' ? 'warning' : 'danger')); ?>">
                                            <?php echo e(ucfirst(str_replace('_', ' ', $sale->payment_status))); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo e($sale->status === 'completed' ? 'success' : 'secondary'); ?>">
                                            <?php echo e(ucfirst($sale->status)); ?>

                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="<?php echo e(route('admin.pos.show', $sale->id)); ?>"
                                                class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('admin.pos.receipt', $sale->id)); ?>" target="_blank"
                                                class="btn btn-sm btn-outline-secondary" title="Print Receipt">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-receipt fa-3x mb-3"></i>
                                            <p class="mb-0">No sales records found</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    <?php echo e($sales->links()); ?>

                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            $(document).ready(function () {
                // Table is using Laravel pagination, no DataTables needed

            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\pos\history.blade.php ENDPATH**/ ?>