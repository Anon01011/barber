<?php $__env->startSection('content'); ?>
    <div class="container mt-4">
        <h2 class="mb-4">Earnings Overview</h2>

        <div class="row">
            <!-- Today's Earnings -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Today's Earnings</h5>
                    </div>
                    <div class="card-body">
                        <?php if($todayEarnings->total_earnings > 0): ?>
                            <h3 class="text-primary">
                                <?php echo e(currency_symbol()); ?><?php echo e(number_format($todayEarnings->total_earnings, 2)); ?></h3>
                        <?php else: ?>
                            <p class="text-muted mb-0">No earnings today</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Weekly Earnings -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">This Week's Earnings</h5>
                    </div>
                    <div class="card-body">
                        <?php if($weeklyEarnings->total_earnings > 0): ?>
                            <h3 class="text-white">
                                <?php echo e(currency_symbol()); ?><?php echo e(number_format($weeklyEarnings->total_earnings, 2)); ?></h3>
                        <?php else: ?>
                            <p class="text-muted mb-0">No earnings this week</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- All-time Earnings -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">All-time Earnings</h5>
                    </div>
                    <div class="card-body">
                        <?php if($allTimeEarnings->total_earnings > 0): ?>
                            <h3 class="text-info">
                                <?php echo e(currency_symbol()); ?><?php echo e(number_format($allTimeEarnings->total_earnings, 2)); ?></h3>
                        <?php else: ?>
                            <p class="text-muted mb-0">No earnings recorded</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Earnings History -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Earnings History</h5>
            </div>
            <div class="card-body">
                <?php if($recentEarnings->isEmpty()): ?>
                    <div class="alert alert-info">No recent earnings found.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $recentEarnings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $earning): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($earning->start_time->format('Y-m-d')); ?></td>
                                        <td><?php echo e($earning->start_time->format('h:i A')); ?></td>
                                        <td><?php echo e($earning->customer->name ?? 'N/A'); ?></td>
                                        <td><?php echo e($earning->service->name ?? 'N/A'); ?></td>
                                        <td><?php echo e(currency_symbol()); ?><?php echo e(number_format($earning->amount, 2)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\staff\earnings\index.blade.php ENDPATH**/ ?>