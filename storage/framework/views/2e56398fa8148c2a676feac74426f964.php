<?php $__env->startComponent('mail::message'); ?>
# Welcome to <?php echo new \Illuminate\Support\EncodedHtmlString($salon->name); ?>!

Hello <?php echo new \Illuminate\Support\EncodedHtmlString($user->name); ?>,

Your staff account has been created successfully. You can now log in to the staff portal to manage your schedule and
appointments.

<?php $__env->startComponent('mail::button', ['url' => route('login')]); ?>
Login to Portal
<?php echo $__env->renderComponent(); ?>

<?php if($password): ?>
    **Your temporary password is:** <?php echo new \Illuminate\Support\EncodedHtmlString($password); ?>


    Please change your password after your first login.
<?php endif; ?>

Thanks,<br>
<?php echo new \Illuminate\Support\EncodedHtmlString($salon->name); ?>

<?php echo $__env->renderComponent(); ?><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views/emails/staff/welcome.blade.php ENDPATH**/ ?>