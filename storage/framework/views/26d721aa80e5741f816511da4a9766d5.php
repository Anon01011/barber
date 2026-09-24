<?php $__env->startComponent('mail::message'); ?>
# Welcome to <?php echo e($salon->name); ?>!

Hello <?php echo e($customer->name); ?>,

We are thrilled to have you as a customer at <?php echo e($salon->name); ?>.

We look forward to serving you!

<?php $__env->startComponent('mail::button', ['url' => route('login')]); ?>
Book an Appointment
<?php echo $__env->renderComponent(); ?>

Thanks,<br>
<?php echo e($salon->name); ?>

<?php echo $__env->renderComponent(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\emails\customer\welcome.blade.php ENDPATH**/ ?>