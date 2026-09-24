<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-0 text-gray-800">
                    Pending Commissions
                    <?php if(isset($branch) && $branch): ?>
                        <span class="badge bg-primary text-white ms-2"
                            style="font-size: 0.5em; vertical-align: middle;"><?php echo e($branch->name); ?></span>
                    <?php endif; ?>
                </h2>
                <p class="text-muted mb-0">Review and approve pending staff commissions</p>
            </div>

            <a href="<?php echo e(route('admin.reports.commissions.index')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Overview
            </a>
        </div>

        <!-- Filters -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="<?php echo e(route('admin.reports.commissions.pending')); ?>" method="GET"
                    class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo e($startDate); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo e($endDate); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Staff Member</label>
                        <select name="staff_id" class="form-select">
                            <option value="">All Staff</option>
                            <?php $__currentLoopData = $staffMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($staff->id); ?>" <?php echo e($staffId == $staff->id ? 'selected' : ''); ?>>
                                    <?php echo e($staff->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>



        <!-- Pending List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-warning">Pending Approvals</h6>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success btn-sm" onclick="submitBulkAction('approve')">
                        <i class="fas fa-check me-1"></i> Approve Selected
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form id="bulkActionForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" id="formAction">

                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="40" class="text-center">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Date</th>
                                    <th>Staff</th>
                                    <th>Item</th>
                                    <th>Sale Amount</th>
                                    <th>Commission</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $pendingCommissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="commission_ids[]" value="<?php echo e($commission->id); ?>"
                                                class="form-check-input commission-checkbox">
                                        </td>
                                        <td>
                                            <?php echo e(format_date($commission->created_at)); ?><br>
                                            <small class="text-muted"><?php echo e(format_time($commission->created_at)); ?></small>
                                        </td>
                                        <td><?php echo e($commission->staff->name); ?></td>
                                        <td>
                                            <?php if($commission->item_type == 'service'): ?>
                                                <span class="badge bg-primary bg-opacity-10 text-primary">Service</span>
                                                <?php echo e($commission->service->name ?? ($commission->booking->service->name ?? 'Unknown Service')); ?>

                                            <?php elseif($commission->item_type == 'product'): ?>
                                                <span class="badge bg-success bg-opacity-10 text-white">Product</span>
                                                <?php echo e($commission->posSale->items->first()->product->name ?? 'Unknown Product'); ?>

                                            <?php elseif($commission->item_type == 'package'): ?>
                                                <span class="badge bg-info bg-opacity-10 text-info">Package</span>
                                                <?php echo e($commission->package->name ?? 'Unknown Package'); ?>

                                            <?php elseif($commission->item_type == 'membership'): ?>
                                                <span class="badge bg-warning bg-opacity-10 text-warning">Membership</span>
                                                <?php echo e($commission->membership->name ?? 'Unknown Membership'); ?>

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
                                        <td class="font-weight-bold text-success">
                                            <?php echo e(currency_symbol()); ?><?php echo e(number_format($commission->commission_amount, 2)); ?>

                                        </td>
                                        <td>
                                            <!-- Single actions could go here -->
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">No pending commissions found</div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <?php echo e($pendingCommissions->links()); ?>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            document.getElementById('selectAll').addEventListener('change', function () {
                const checkboxes = document.querySelectorAll('.commission-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
            });

            function submitBulkAction(action) {
                const form = document.getElementById('bulkActionForm');
                const checkboxes = document.querySelectorAll('.commission-checkbox:checked');

                if (checkboxes.length === 0) {
                    Swal.fire('No Selection', 'Please select at least one commission to ' + action, 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to ${action} ${checkboxes.length} commissions.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, ' + action + ' them!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (action === 'approve') {
                            form.action = "<?php echo e(route('admin.reports.commissions.bulk-approve')); ?>";
                        } else if (action === 'pay') {
                            form.action = "<?php echo e(route('admin.reports.commissions.bulk-pay')); ?>";
                        }
                        form.submit();
                    }
                });
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\reports\commissions\pending.blade.php ENDPATH**/ ?>