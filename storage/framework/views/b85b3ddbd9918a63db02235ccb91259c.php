<?php $__env->startComponent('mail::message'); ?>
# New Salon Registration

A new salon has registered on the platform.

**Salon Name:** <?php echo e($salon->name); ?>

**Email:** <?php echo e($salon->email); ?>

**Plan:** <?php echo e($salon->subscription->plan->name ?? 'N/A'); ?>

**Status:** <?php echo e($salon->is_active ? 'Active' : 'Pending Approval'); ?>


<?php $__env->startComponent('mail::button', ['url' => route('admin.salons.show', $salon)]); ?>
View Salon Details
<?php echo $__env->renderComponent(); ?>

Thanks,<br>
<?php echo e(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?><?php /**PATH C:\Ketan\projects\barber\resources\views\emails\admin\new_registration.blade.php ENDPATH**/ ?>