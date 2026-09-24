<?php $__env->startSection('title', 'Sales History'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .sale-actions .btn {
            padding: 0.25rem 0.5rem;
            background: transparent;
            border: none;
            color: #6c757d;
            transition: color 0.2s;
        }

        .sale-actions .btn:hover {
            color: #0d6efd;
            background: transparent;
        }

        .sale-actions .btn:active,
        .sale-actions .btn:focus {
            box-shadow: none;
            outline: none;
        }

        .table {
            margin-bottom: 0;
        }

        .table> :not(caption)>*>* {
            padding: 0.75rem 1rem;
            vertical-align: middle;
        }

        .table thead th {
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #6c757d;
        }

        .table tbody tr {
            transition: background-color 0.15s;
        }

        .table tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.03);
        }

        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Sales History</h5>
                            <a href="<?php echo e(route('admin.pos.index')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> New Sale
                            </a>
                        </div>
                        
                        <!-- Filter Form -->
                        <form action="<?php echo e(route('admin.pos.sales.index')); ?>" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="search" class="form-label small text-muted">Search</label>
                                <input type="text" class="form-control form-control-sm" id="search" name="search" 
                                    value="<?php echo e(request('search')); ?>" placeholder="Invoice # or Customer Name/Phone">
                            </div>
                            <div class="col-md-2">
                                <label for="start_date" class="form-label small text-muted">Start Date</label>
                                <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" 
                                    value="<?php echo e(request('start_date')); ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="end_date" class="form-label small text-muted">End Date</label>
                                <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" 
                                    value="<?php echo e(request('end_date')); ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="customer_id" class="form-label small text-muted">Customer</label>
                                <select class="form-select form-select-sm" id="customer_id" name="customer_id">
                                    <option value="">All Customers</option>
                                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($customer->id); ?>" <?php echo e(request('customer_id') == $customer->id ? 'selected' : ''); ?>>
                                            <?php echo e($customer->name); ?> (<?php echo e($customer->phone); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="d-grid gap-2 d-md-flex">
                                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                        <i class="fas fa-filter me-1"></i> Filter
                                    </button>
                                    <a href="<?php echo e(route('admin.pos.sales.index')); ?>" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Sale #</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Payment Mode</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td class="ps-4 fw-medium"><?php echo e($sale->invoice_number ?? 'N/A'); ?></td>
                                            <td><?php echo e(format_datetime($sale->created_at)); ?></td>
                                            <td>
                                                <?php echo e($sale->customer->name ?? 'Walk-in Customer'); ?>

                                            </td>
                                            <td><?php echo e($sale->items_count ?? '0'); ?> items</td>
                                            <td class="fw-medium"><?php echo e(format_currency($sale->total)); ?></td>
                                            <td>
                                                <?php if($sale->status === 'voided'): ?>
                                                    <span class="badge bg-secondary text-white">Voided</span>
                                                <?php elseif($sale->status === 'refunded'): ?>
                                                    <span class="badge bg-danger text-white">Refunded</span>
                                                <?php elseif($sale->status === 'partially_refunded'): ?>
                                                    <span class="badge bg-warning text-dark">Partially Refunded</span>
                                                <?php elseif($sale->payment_status === 'paid'): ?>
                                                    <span class="badge bg-success text-white">Paid</span>
                                                <?php elseif($sale->payment_status === 'partial'): ?>
                                                    <span class="badge bg-warning text-dark">Partial</span>
                                                <?php elseif($sale->payment_status === 'refunded'): ?>
                                                    <span class="badge bg-danger text-white">Refunded</span>
                                                <?php elseif($sale->payment_status === 'partially_refunded'): ?>
                                                    <span class="badge bg-warning text-dark">Partially Refunded</span>
                                                <?php else: ?>
                                                    <span class="badge bg-info text-white"><?php echo e(ucfirst($sale->payment_status)); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($sale->payment_method): ?>
                                                    <span class="badge bg-light text-dark border"><?php echo e(ucfirst($sale->payment_method)); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end pe-4 sale-actions">
                                                <a href="<?php echo e(route('admin.pos.sales.show', $sale->id)); ?>" class="btn"
                                                    title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('admin.pos.sales.edit', $sale->id)); ?>" class="btn"
                                                    title="Edit Sale">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?php echo e(route('admin.pos.receipt', $sale->id)); ?>" class="btn"
                                                    target="_blank" title="View Receipt">
                                                    <i class="fas fa-receipt"></i>
                                                </a>
                                                <?php if($posReceiptArabicButton ?? true): ?>
                                                <a href="<?php echo e(route('admin.pos.receipt.arabic', $sale->id)); ?>" class="btn text-info"
                                                    target="_blank" title="View Arabic Receipt">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-receipt fa-2x mb-3"></i>
                                                    <p class="mb-0">No sales records found</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if($sales->hasPages()): ?>
                        <div class="card-footer bg-white border-top-0 py-3">
                            <nav aria-label="Sales pagination">
                                <?php echo e($sales->links()); ?>

                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function () {
            // Table is using Laravel pagination, no DataTables needed

        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\pos\sales\index.blade.php ENDPATH**/ ?>