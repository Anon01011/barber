<?php $__env->startComponent('mail::message'); ?>
# Subscription Renewed Successfully

Hi <?php echo e($salon_name); ?>,

We are pleased to inform you that your subscription to the **<?php echo e($plan_name); ?>** plan has been successfully renewed!

Your new subscription period is valid until **<?php echo e($end_date); ?>**.

<?php $__env->startComponent('mail::panel'); ?>
All your premium features remain active. No further action is required.
<?php echo $__env->renderComponent(); ?>

<?php $__env->startComponent('mail::button', ['url' => $dashboard_url, 'color' => 'success']); ?>
Go to Dashboard
<?php echo $__env->renderComponent(); ?>

Thank you for continuing to partner with <?php echo e(config('app.name')); ?>.

Best regards,<br>
The <?php echo e(config('app.name')); ?> Team
<?php echo $__env->renderComponent(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\emails\subscription\renewed.blade.php ENDPATH**/ ?>