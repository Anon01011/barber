@component('mail::message')
# Subscription Renewed Successfully

Hi {{ $salon_name }},

We are pleased to inform you that your subscription to the **{{ $plan_name }}** plan has been successfully renewed!

Your new subscription period is valid until **{{ $end_date }}**.

@component('mail::panel')
All your premium features remain active. No further action is required.
@endcomponent

@component('mail::button', ['url' => $dashboard_url, 'color' => 'success'])
Go to Dashboard
@endcomponent

Thank you for continuing to partner with {{ config('app.name') }}.

Best regards,<br>
The {{ config('app.name') }} Team
@endcomponent