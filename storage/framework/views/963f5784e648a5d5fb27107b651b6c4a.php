<?php $__env->startComponent('mail::message'); ?>
# Subscription Expiring Soon

Hello <?php echo e($salon_name); ?>,

This is a reminder that your **<?php echo e($plan_name); ?>** subscription will expire in **<?php echo e($days_left); ?>

day<?php echo e($days_left > 1 ? 's' : ''); ?>** on <?php echo e($expiry_date); ?>.

To ensure uninterrupted access to your booking system and salon tools, please renew your plan before it expires.

<?php $__env->startComponent('mail::button', ['url' => $renew_url, 'color' => 'primary']); ?>
Renew Subscription
<?php echo $__env->renderComponent(); ?>

If you have enabled auto-renewal, no action is needed. Otherwise, please click the button above to secure your
subscription.

Thanks,<br>
<?php echo e(config('app.name')); ?> Team
<?php echo $__env->renderComponent(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\emails\subscription\expiring_soon.blade.php ENDPATH**/ ?>