<x-mail::message>
# Booking Status Update

Dear {{ $booking->customer->name }},

The status of your booking #{{ $booking->id }} has been updated to **{{ ucfirst($booking->status) }}**.

**Booking Details:**
- **Service:** {{ $booking->service->name }}
- **Date:** {{ $booking->start_time->format('F j, Y') }}
- **Time:** {{ $booking->start_time->format('g:i A') }}

@if($booking->status === 'cancelled')
If you have any questions, please contact us.
@endif

<x-mail::button :url="route('home')">
View Booking
</x-mail::button>

Thanks,<br>
{{ $booking->salon->name ?? config('app.name') }}
</x-mail::message>
