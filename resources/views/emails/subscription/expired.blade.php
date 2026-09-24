@component('mail::message')
# Subscription Expired

Hello {{ $salon_name }},

Your subscription to the **{{ $plan_name }}** plan expired on **{{ $expiry_date }}**.

Because your subscription has expired, access to premium features (including booking management, staff scheduling, and
reporting) has been suspended.

### Restore Access Now
To reactivate your account and restore full access immediately, please renew your subscription.

@component('mail::button', ['url' => $renew_url, 'color' => 'primary'])
Renew Subscription
@endcomponent

If you believe this is an error or need assistance, please contact our support team.

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent