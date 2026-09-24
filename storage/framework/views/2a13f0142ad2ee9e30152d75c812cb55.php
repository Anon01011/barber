<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }

        .highlight-box {
            background: white;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .amount {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            margin: 10px 0;
        }

        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }

        .urgent {
            background: #fff3cd;
            border-left-color: #ffc107;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1><?php echo e($daysUntilRenewal === 1 ? '⏰ Renewal Tomorrow!' : '📅 Renewal Reminder'); ?></h1>
    </div>

    <div class="content">
        <p>Hello <?php echo e($salon->owner->name); ?>,</p>

        <?php if($daysUntilRenewal === 1): ?>
            <div class="highlight-box urgent">
                <h2>Your subscription renews tomorrow!</h2>
                <p>Your <strong><?php echo e($plan->name); ?></strong> subscription for <strong><?php echo e($salon->name); ?></strong> will
                    automatically renew on <strong><?php echo e($renewalDate->format('F j, Y')); ?></strong>.</p>
            </div>
        <?php else: ?>
            <div class="highlight-box">
                <h2>Your subscription renews in <?php echo e($daysUntilRenewal); ?> days</h2>
                <p>Your <strong><?php echo e($plan->name); ?></strong> subscription for <strong><?php echo e($salon->name); ?></strong> will
                    automatically renew on <strong><?php echo e($renewalDate->format('F j, Y')); ?></strong>.</p>
            </div>
        <?php endif; ?>

        <div class="highlight-box">
            <h3>Renewal Details:</h3>
            <table style="width: 100%; margin: 15px 0;">
                <tr>
                    <td><strong>Plan:</strong></td>
                    <td><?php echo e($plan->name); ?></td>
                </tr>
                <tr>
                    <td><strong>Renewal Date:</strong></td>
                    <td><?php echo e($renewalDate->format('F j, Y')); ?></td>
                </tr>
                <tr>
                    <td><strong>Amount:</strong></td>
                    <td class="amount">$ <?php echo e(number_format($amount, 2)); ?></td>
                </tr>
                <tr>
                    <td><strong>Billing Cycle:</strong></td>
                    <td><?php echo e($plan->duration_in_days); ?> days</td>
                </tr>
            </table>
        </div>

        <p>Your payment method on file will be charged automatically. No action is required unless you wish to make
            changes.</p>

        <div style="text-align: center;">
            <a href="<?php echo e(route('admin.saas.subscription.index', ['salon_slug' => $salon->slug])); ?>" class="button">
                Manage Subscription
            </a>
        </div>

        <p style="margin-top: 30px;">
            <strong>Need to make changes?</strong><br>
            You can update your payment method, change your plan, or cancel your subscription anytime from your account
            dashboard.
        </p>

        <?php if($daysUntilRenewal > 1): ?>
            <p style="font-size: 14px; color: #666;">
                You'll receive another reminder closer to your renewal date.
            </p>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>This is an automated reminder from your Salon CMS subscription service.</p>
        <p>If you have any questions, please contact our support team.</p>
        <p>&copy; <?php echo e(date('Y')); ?> Salon CMS. All rights reserved.</p>
    </div>
</body>

</html><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\emails\subscription-renewing-soon.blade.php ENDPATH**/ ?>