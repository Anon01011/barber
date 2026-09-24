<?php $__env->startComponent('mail::message'); ?>
# Subscription Expired

Hello <?php echo e($salon_name); ?>,

Your subscription to the **<?php echo e($plan_name); ?>** plan expired on **<?php echo e($expiry_date); ?>**.

Because your subscription has expired, access to premium features (including booking management, staff scheduling, and
reporting) has been suspended.

### Restore Access Now
To reactivate your account and restore full access immediately, please renew your subscription.

<?php $__env->startComponent('mail::button', ['url' => $renew_url, 'color' => 'primary']); ?>
Renew Subscription
<?php echo $__env->renderComponent(); ?>

If you believe this is an error or need assistance, please contact our support team.

Thanks,<br>
<?php echo e(config('app.name')); ?> Team
<?php echo $__env->renderComponent(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\emails\subscription\expired.blade.php ENDPATH**/ ?>