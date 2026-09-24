<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Summary Report</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8f9fc;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .header {
            background-color: #4e73df;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 16px;
        }

        .content {
            padding: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #fdfdfd;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }

        .stat-card .label {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 700;
            color: #858796;
            margin-bottom: 5px;
        }

        .stat-card .value {
            font-size: 20px;
            font-weight: 700;
            color: #4e73df;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            margin: 30px 0 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f3f9;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #f1f3f9;
        }

        .table th {
            color: #858796;
            font-size: 12px;
            text-transform: uppercase;
        }

        .table td {
            font-size: 14px;
        }

        .footer {
            background: #f1f3f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #858796;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #e1f5fe;
            color: #0288d1;
        }

        .badge-info {
            background: #efebe9;
            color: #5d4037;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Daily Summary Report</h1>
            <p><?php echo e($salon->name); ?> | <?php echo e($date->format('F j, Y')); ?></p>
        </div>

        <div class="content">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="label">Total Revenue</div>
                    <div class="value"><?php echo e(format_currency($stats['total_revenue'])); ?>

                    </div>
                </div>
                <div class="stat-card">
                    <div class="label">Total Bookings</div>
                    <div class="value"><?php echo e($stats['total_bookings']); ?></div>
                </div>
                <div class="stat-card">
                    <div class="label">Completed</div>
                    <div class="value" style="color: #1cc88a;"><?php echo e($stats['completed_bookings']); ?></div>
                </div>
                <div class="stat-card">
                    <div class="label">New Customers</div>
                    <div class="value" style="color: #36b9cc;"><?php echo e($stats['new_customers']); ?></div>
                </div>
                <div class="stat-card">
                    <div class="label" style="color: #e74a3b;">Unpaid Amount</div>
                    <div class="value" style="color: #e74a3b;">
                        <?php echo e(format_currency($stats['unpaid_amount'])); ?>

                    </div>
                </div>
                <div class="stat-card">
                    <div class="label" style="color: #f6c23e;">Partially Paid</div>
                    <div class="value" style="color: #f6c23e;">
                        <?php echo e(format_currency($stats['partial_paid_amount'])); ?>

                    </div>
                </div>
            </div>

            <div class="section-title">Staff Performance (Yesterday)</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Staff Member</th>
                        <th>Bookings</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $stats['staff_performance']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($staff->name); ?></strong></td>
                            <td><?php echo e($staff->count); ?></td>
                            <td><?php echo e(format_currency($staff->revenue)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="3" style="text-align: center; color: #ccc;">No staff activity recorded.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="section-title">Yesterday's Booking Breakdown</div>
            <table class="table">
                <tbody>
                    <tr>
                        <td>Confirmed Bookings</td>
                        <td style="text-align: right;"><?php echo e($stats['completed_bookings'] + $stats['confirmed_bookings']); ?>

                        </td>
                    </tr>
                    <tr>
                        <td>Cancelled Bookings</td>
                        <td style="text-align: right; color: #e74a3b;"><?php echo e($stats['cancelled_bookings']); ?></td>
                    </tr>
                    <tr>
                        <td>Pending / Other</td>
                        <td style="text-align: right;"><?php echo e($stats['pending_bookings']); ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="section-title">Next Steps (Today's Schedule)</div>
            <p style="font-size: 14px; margin-bottom: 20px;">
                You have <strong><?php echo e($stats['upcoming_today']); ?></strong> appointment(s) scheduled for today,
                <?php echo e(now()->format('M j')); ?>.
            </p>
        </div>

        <div class="footer">
            <p>This is an automated summary from your <?php echo e(config('app.name')); ?> dashboard.</p>
            <p>&copy; <?php echo e(date('Y')); ?> <?php echo e($salon->name); ?>. All rights reserved.</p>
        </div>
    </div>
</body>

</html><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\emails\daily_summary.blade.php ENDPATH**/ ?>