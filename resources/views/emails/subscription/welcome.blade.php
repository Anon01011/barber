<x-mail::message>
# Welcome to {{ config('app.name') }}!

Hi {{ $salonName }},

Thank you for registering with us. We're excited to have you on board!

You have subscribed to the **{{ $planName }}**.
@if($trialDays > 0)
Your {{ $trialDays }}-day free trial starts today.
@endif

<x-mail::button :url="$loginUrl">
Login to Dashboard
</x-mail::button>

If you have any questions, feel free to reply to this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
