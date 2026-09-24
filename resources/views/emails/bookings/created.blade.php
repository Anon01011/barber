<x-mail::message>
# Booking Confirmation

Dear {{ $booking->customer->name }},

Your booking has been successfully created.

**Booking Details:**
- **Booking ID:** #{{ $booking->id }}
- **Service:** {{ $booking->service->name }}
- **Date:** {{ $booking->start_time->format('F j, Y') }}
- **Time:** {{ $booking->start_time->format('g:i A') }}
- **Staff:** {{ $booking->staff->name ?? 'Any Available Staff' }}
- **Total Amount:** {{ $booking->salon->currency_symbol ?? '$' }}{{ number_format($booking->amount, 2) }}

<x-mail::button :url="route('home')">
View Booking
</x-mail::button>

Thanks,<br>
{{ $booking->salon->name ?? config('app.name') }}
</x-mail::message>
