@component('mail::message')
# Welcome to {{ $salon->name }}!

Hello {{ $user->name }},

Your staff account has been created successfully. You can now log in to the staff portal to manage your schedule and
appointments.

@component('mail::button', ['url' => route('login')])
Login to Portal
@endcomponent

@if($password)
    **Your temporary password is:** {{ $password }}

    Please change your password after your first login.
@endif

Thanks,<br>
{{ $salon->name }}
@endcomponent