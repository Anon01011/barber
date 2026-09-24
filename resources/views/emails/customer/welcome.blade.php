@component('mail::message')
# Welcome to {{ $salon->name }}!

Hello {{ $customer->name }},

We are thrilled to have you as a customer at {{ $salon->name }}.

We look forward to serving you!

@component('mail::button', ['url' => route('login')])
Book an Appointment
@endcomponent

Thanks,<br>
{{ $salon->name }}
@endcomponent