<p>Hi <?php echo e($salon_name); ?>,</p>

<p>Thank you for registering with us. We're excited to have you on board!</p>

<p>You have subscribed to the <strong><?php echo e($plan_name); ?></strong>.</p>

<?php if($trial_days > 0): ?>
<p>Your <?php echo e($trial_days); ?>-day free trial starts today.</p>
<?php endif; ?>

<p style="text-align: center;">
    <a href="<?php echo e($login_url); ?>" class="button">Login to Dashboard</a>
</p>

<p>If you have any questions, feel free to reply to this email.</p>

<p>Thanks,<br><?php echo e($app_name); ?></p><?php /**PATH C:\Ketan\projects\barber\storage\framework\views/e6193452b3eee5bf7ef8084686237914.blade.php ENDPATH**/ ?>