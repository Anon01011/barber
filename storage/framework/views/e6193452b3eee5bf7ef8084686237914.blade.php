<p>Hi {{ $salon_name }},</p>

<p>Thank you for registering with us. We're excited to have you on board!</p>

<p>You have subscribed to the <strong>{{ $plan_name }}</strong>.</p>

@if($trial_days > 0)
<p>Your {{ $trial_days }}-day free trial starts today.</p>
@endif

<p style="text-align: center;">
    <a href="{{ $login_url }}" class="button">Login to Dashboard</a>
</p>

<p>If you have any questions, feel free to reply to this email.</p>

<p>Thanks,<br>{{ $app_name }}</p>