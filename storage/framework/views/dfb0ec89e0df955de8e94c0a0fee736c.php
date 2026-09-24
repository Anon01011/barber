<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-900 fw-bold">Revenue Analytics</h1>
                <p class="text-muted mb-0">Detailed financial breakdown and transaction history.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('admin.saas.reports.index')); ?>" class="btn btn-light border text-muted shadow-sm">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>
                <a href="<?php echo e(route('admin.saas.reports.export.revenue', request()->query())); ?>" class="btn btn-success shadow-sm fw-medium">
                    <i class="fas fa-file-excel me-2"></i> Export Report (CSV)
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Date From</label>
                        <input type="date" name="date_from" class="form-control"
                            value="<?php echo e($dateFrom->format('Y-m-d')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="<?php echo e($dateTo->format('Y-m-d')); ?>">
                    </div>
                    <div class="col-md-4">
                         <label class="form-label small fw-bold text-muted">Salon Workspace</label>
                         <select name="salon_id" class="form-select">
                            <option value="">All Salons</option>
                            <?php $__currentLoopData = $salons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($salon->id); ?>" <?php echo e(request('salon_id') == $salon->id ? 'selected' : ''); ?>>
                                    <?php echo e($salon->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 fw-medium">
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
             <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                         <h6 class="text-uppercase text-muted fw-bold small mb-2">Total Revenue</h6>
                         <h3 class="fw-bold text-success mb-0">
                            <?php echo e(system_currency_symbol()); ?><?php echo e(number_format($stats['total_revenue'], 2)); ?>

                         </h3>
                         <div class="mt-2 text-muted small">
                            From <?php echo e($stats['total_transactions']); ?> transactions
                         </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                 <div class="card border-0 shadow-sm h-100">
                     <div class="card-body">
                        <h6 class="text-uppercase text-muted fw-bold small mb-2">Avg. Transaction</h6>
                         <h3 class="fw-bold text-primary mb-0">
                            <?php echo e(system_currency_symbol()); ?><?php echo e(number_format($stats['average_transaction'], 2)); ?>

                         </h3>
                         <div class="mt-2 text-muted small">
                            Per payment avg.
                         </div>
                     </div>
                 </div>
            </div>

             <div class="col-xl-3 col-md-6">
                  <div class="card border-0 shadow-sm h-100">
                     <div class="card-body">
                        <h6 class="text-uppercase text-muted fw-bold small mb-2">Transactions</h6>
                         <h3 class="fw-bold text-dark mb-0">
                            <?php echo e(number_format($stats['total_transactions'])); ?>

                         </h3>
                         <div class="mt-2 text-muted small">
                            Successful payments
                         </div>
                     </div>
                 </div>
            </div>

             <div class="col-xl-3 col-md-6">
                 <div class="card border-0 shadow-sm h-100">
                     <div class="card-body">
                        <h6 class="text-uppercase text-muted fw-bold small mb-2">Methods</h6>
                         <h3 class="fw-bold text-info mb-0">
                            <?php echo e($stats['payment_methods']->count()); ?>

                         </h3>
                         <div class="mt-2 text-muted small">
                            Active payment types
                         </div>
                     </div>
                 </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Monthly Revenue Trend -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="m-0 fw-bold text-gray-800">Monthly Revenue Trend</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 text-muted small text-uppercase">Month</th>
                                    <th class="text-muted small text-uppercase">Transactions</th>
                                    <th class="text-muted small text-uppercase">Revenue</th>
                                    <th class="pe-4 text-end text-muted small text-uppercase">Average</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $monthlyRevenue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="ps-4 fw-medium"><?php echo e(\Carbon\Carbon::parse($month)->format('F Y')); ?></td>
                                        <td><?php echo e($data['count']); ?></td>
                                        <td class="text-success fw-bold"><?php echo e(system_currency_symbol()); ?><?php echo e(number_format($data['revenue'], 2)); ?></td>
                                        <td class="pe-4 text-end text-muted"><?php echo e(system_currency_symbol()); ?><?php echo e(number_format($data['count'] > 0 ? $data['revenue'] / $data['count'] : 0, 2)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No data available for this period</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Revenue by Plan -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="m-0 fw-bold text-gray-800">Revenue by Plan</h6>
                    </div>
                    <div class="card-body">
                        <?php $__empty_1 = true; $__currentLoopData = $planRevenue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-medium text-dark"><?php echo e($plan); ?></span>
                                    <span class="fw-bold text-success"><?php echo e(system_currency_symbol()); ?><?php echo e(number_format($data['revenue'], 2)); ?></span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: <?php echo e($stats['total_revenue'] > 0 ? ($data['revenue'] / $stats['total_revenue']) * 100 : 0); ?>%">
                                    </div>
                                </div>
                                <div class="small text-muted mt-1"><?php echo e($data['count']); ?> transactions</div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center py-4 text-muted">No plan revenue data</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <h5 class="fw-bold text-gray-800 mt-5 mb-3">Recent Transactions</h5>
        <div class="card border-0 shadow-sm mb-5">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted small text-uppercase">Date</th>
                            <th class="py-3 text-muted small text-uppercase">Salon</th>
                            <th class="py-3 text-muted small text-uppercase">Plan</th>
                            <th class="py-3 text-muted small text-uppercase">Method</th>
                            <th class="pe-4 py-3 text-end text-muted small text-uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $payments->take(20); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="ps-4 text-muted small"><?php echo e($payment->created_at->format('M d, Y h:i A')); ?></td>
                                <td class="fw-medium text-dark"><?php echo e($payment->salon->name ?? 'Deleted Salon'); ?></td>
                                <td>
                                    <?php if($payment->subscription && $payment->subscription->plan): ?>
                                        <span class="badge bg-light text-dark border"><?php echo e($payment->subscription->plan->name); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-secondary-subtle text-secondary"><?php echo e(ucfirst($payment->payment_method ?? 'Unknown')); ?></span></td>
                                <td class="pe-4 text-end fw-bold text-success"><?php echo e(system_currency_symbol()); ?><?php echo e(number_format($payment->amount, 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted">No recent transactions found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\super-admin\reports\revenue.blade.php ENDPATH**/ ?>