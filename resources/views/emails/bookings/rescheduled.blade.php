<x-mail::message>
# Booking Rescheduled

Dear {{ $booking->customer->name }},

Your booking #{{ $booking->id }} has been rescheduled.

**New Details:**
- **Service:** {{ $booking->service->name }}
- **Date:** {{ $booking->start_time->format('F j, Y') }}
- **Time:** {{ $booking->start_time->format('g:i A') }}
- **Staff:** {{ $booking->staff->name ?? 'Any Available Staff' }}

<x-mail::button :url="route('home')">
View Booking
</x-mail::button>

Thanks,<br>
{{ $booking->salon->name ?? config('app.name') }}
</x-mail::message>
