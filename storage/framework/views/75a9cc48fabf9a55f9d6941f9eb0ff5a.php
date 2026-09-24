<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-money-bill-wave text-primary me-2"></i>Commission Reports
            </h1>
            <a href="<?php echo e(route('admin.commissions.index')); ?>" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Profiles
            </a>
        </div>

        <!-- Filter Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-gradient-primary">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-filter me-2"></i>Filter Options
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('admin.commissions.reports')); ?>" class="row g-3">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo e($startDate); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo e($endDate); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="all" <?php echo e($status == 'all' ? 'selected' : ''); ?>>All</option>
                            <option value="pending" <?php echo e($status == 'pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="approved" <?php echo e($status == 'approved' ? 'selected' : ''); ?>>Approved</option>
                            <option value="paid" <?php echo e($status == 'paid' ? 'selected' : ''); ?>>Paid</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Apply Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Pending Commissions</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo e(currency_symbol()); ?><?php echo e(number_format($summary['total_pending'], 2)); ?>

                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Approved Commissions</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo e(currency_symbol()); ?><?php echo e(number_format($summary['total_approved'], 2)); ?>

                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Paid Commissions</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo e(currency_symbol()); ?><?php echo e(number_format($summary['total_paid'], 2)); ?>

                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commissions Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Commission Details</h6>
                <div>
                    <button type="button" class="btn btn-sm btn-info text-white" id="approveSelected">
                        <i class="fas fa-check"></i> Approve Selected
                    </button>
                    <button type="button" class="btn btn-sm btn-success" id="markPaidSelected">
                        <i class="fas fa-dollar-sign"></i> Mark as Paid
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Date</th>
                                <th>Staff</th>
                                <th>Item Type</th>
                                <th>Sale Amount</th>
                                <th>Commission</th>
                                <th>Profile</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $commissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><input type="checkbox" class="commission-checkbox" value="<?php echo e($commission->id); ?>"></td>
                                    <td><?php echo e($commission->created_at->format('M d, Y')); ?></td>
                                    <td><?php echo e($commission->staff ? $commission->staff->name : 'N/A'); ?></td>
                                    <td><span
                                            class="badge <?php echo e($commission->item_type === 'tip' ? 'bg-warning' : ($commission->item_type === 'target' ? 'bg-secondary' : 'bg-primary')); ?>"><?php echo e(ucfirst($commission->item_type)); ?></span>
                                    </td>
                                    <td><?php echo e(currency_symbol()); ?><?php echo e(number_format($commission->sale_amount, 2)); ?></td>
                                    <td class="font-weight-bold text-success">
                                        <?php echo e(currency_symbol()); ?><?php echo e(number_format($commission->commission_amount, 2)); ?>

                                    </td>
                                    <td><?php echo e($commission->profile ? $commission->profile->name : 'N/A'); ?></td>
                                    <td>
                                        <?php if($commission->status == 'pending'): ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php elseif($commission->status == 'approved'): ?>
                                            <span class="badge bg-info">Approved</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Paid</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>





                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <?php echo e($commissions->links()); ?>

                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            $(document).ready(function () {
                $('#dataTable').DataTable({
                    "paging": false,
                    "searching": true,
                    "ordering": true,
                    "language": {
                        "emptyTable": "No commissions found for the selected period."
                    }
                });

                // Select all checkboxes
                $('#selectAll').click(function () {
                    $('.commission-checkbox').prop('checked', this.checked);
                });

                // Approve selected
                $('#approveSelected').click(function () {
                    const selected = $('.commission-checkbox:checked').map(function () {
                        return $(this).val();
                    }).get();

                    if (selected.length === 0) {
                        alert('Please select at least one commission.');
                        return;
                    }

                    if (confirm(`Approve ${selected.length} commission(s)?`)) {
                        $.ajax({
                            url: '<?php echo e(route("admin.commissions.approve")); ?>',
                            method: 'POST',
                            data: {
                                _token: '<?php echo e(csrf_token()); ?>',
                                commission_ids: selected
                            },
                            success: function (response) {
                                location.reload();
                            }
                        });
                    }
                });

                // Mark as paid
                $('#markPaidSelected').click(function () {
                    const selected = $('.commission-checkbox:checked').map(function () {
                        return $(this).val();
                    }).get();

                    if (selected.length === 0) {
                        alert('Please select at least one commission.');
                        return;
                    }

                    if (confirm(`Mark ${selected.length} commission(s) as paid?`)) {
                        $.ajax({
                            url: '<?php echo e(route("admin.commissions.mark-paid")); ?>',
                            method: 'POST',
                            data: {
                                _token: '<?php echo e(csrf_token()); ?>',
                                commission_ids: selected
                            },
                            success: function (response) {
                                location.reload();
                            }
                        });
                    }
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\commissions\reports.blade.php ENDPATH**/ ?>