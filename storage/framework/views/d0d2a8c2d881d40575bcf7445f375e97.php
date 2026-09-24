<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-0 text-gray-800">
                    Commission Reports
                    <?php if(isset($branch) && $branch): ?>
                        <span class="badge bg-primary text-white ms-2"
                            style="font-size: 0.5em; vertical-align: middle;"><?php echo e($branch->name); ?></span>
                    <?php endif; ?>
                </h2>
                <p class="text-muted mb-0">Overview of commission performance and payouts</p>
            </div>

            <!-- Date Filter -->
            <form action="<?php echo e(route('admin.reports.commissions.index')); ?>" method="GET"
                class="d-flex gap-2 align-items-center">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-calendar"></i></span>
                    <input type="date" name="start_date" class="form-control" value="<?php echo e($startDate->format('Y-m-d')); ?>">
                    <span class="input-group-text bg-light">to</span>
                    <input type="date" name="end_date" class="form-control" value="<?php echo e($endDate->format('Y-m-d')); ?>">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <!-- Total Commissions -->
            <div class="col-xl-4 col-md-6">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Commissions</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo e(currency_symbol()); ?><?php echo e(number_format($totalCommissions, 2)); ?>

                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Amount -->
            <div class="col-xl-4 col-md-6">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Pending Approval</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo e(currency_symbol()); ?><?php echo e(number_format($pendingAmount, 2)); ?>

                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paid Amount -->
            <div class="col-xl-4 col-md-6">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                    Paid Out</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo e(currency_symbol()); ?><?php echo e(number_format($paidAmount, 2)); ?>

                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Top Staff Chart -->
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Top Staff by Commission</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-bar">
                            <canvas id="topStaffChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Commissions -->
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Commissions</h6>
                        <a href="<?php echo e(route('admin.reports.commissions.by-staff')); ?>" class="btn btn-sm btn-primary">View
                            All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Staff</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $recentCommissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($commission->staff->name); ?></td>
                                            <td><?php echo e(currency_symbol()); ?><?php echo e(number_format($commission->commission_amount, 2)); ?>

                                            </td>
                                            <td>
                                                <?php if($commission->status == 'paid'): ?>
                                                    <span class="badge bg-success">Paid</span>
                                                <?php elseif($commission->status == 'approved'): ?>
                                                    <span class="badge bg-info">Approved</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e(format_date($commission->created_at, 'M d')); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No recent commissions</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            $(document).ready(function () {
                // Define currency symbol for use in charts
                const currencySymbol = '<?php echo e(currency_symbol()); ?>';

                const topStaff = <?php echo json_encode($topStaff, 15, 512) ?>;
                const staffNames = topStaff.map(s => s.staff_name);
                const staffTotals = topStaff.map(s => parseFloat(s.total_commission));

                const ctx = document.getElementById('topStaffChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: staffNames,
                        datasets: [{
                            label: 'Commission Earned',
                            data: staffTotals,
                            backgroundColor: '#4e73df',
                            hoverBackgroundColor: '#2e59d9',
                            borderColor: '#4e73df',
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function (value) {
                                        return currencySymbol + value;
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return context.dataset.label + ': ' + currencySymbol + context.raw.toFixed(2);
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\admin\reports\commissions\index.blade.php ENDPATH**/ ?>