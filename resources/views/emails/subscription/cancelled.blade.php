<x-mail::message>
# Subscription Cancelled

Hi {{ $salonName }},

We're sorry to see you go. Your subscription to the **{{ $planName }}** has been cancelled.

You will continue to have access to your account until **{{ $endDate }}**.

If you change your mind, you can renew your subscription at any time from your dashboard.

If you have any feedback on how we can improve, please let us know.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
