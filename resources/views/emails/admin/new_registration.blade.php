@component('mail::message')
# New Salon Registration

A new salon has registered on the platform.

**Salon Name:** {{ $salon->name }}
**Email:** {{ $salon->email }}
**Plan:** {{ $salon->subscription->plan->name ?? 'N/A' }}
**Status:** {{ $salon->is_active ? 'Active' : 'Pending Approval' }}

@component('mail::button', ['url' => route('admin.salons.show', $salon)])
View Salon Details
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent