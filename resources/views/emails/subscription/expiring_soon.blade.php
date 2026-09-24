@component('mail::message')
# Subscription Expiring Soon

Hello {{ $salon_name }},

This is a reminder that your **{{ $plan_name }}** subscription will expire in **{{ $days_left }}
day{{ $days_left > 1 ? 's' : '' }}** on {{ $expiry_date }}.

To ensure uninterrupted access to your booking system and salon tools, please renew your plan before it expires.

@component('mail::button', ['url' => $renew_url, 'color' => 'primary'])
Renew Subscription
@endcomponent

If you have enabled auto-renewal, no action is needed. Otherwise, please click the button above to secure your
subscription.

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent