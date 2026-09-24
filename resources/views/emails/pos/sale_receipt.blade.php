<!DOCTYPE html>
<html>

<head>
    <title>Receipt</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        @if(!empty($content))
            {!! $content !!}
        @else
            {{-- Fallback Default Content --}}
            <h2 style="color: #2c3e50;">Thank you for your visit, {{ $sale->customer->name ?? 'Guest' }}!</h2>

            <p>Please find attached the receipt for your recent visit to <strong>{{ $salonData['name'] }}</strong>.</p>

            <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <p style="margin: 5px 0;"><strong>Invoice Number:</strong> #{{ $sale->invoice_number }}</p>
                <p style="margin: 5px 0;"><strong>Date:</strong> {{ $sale->created_at->format('M d, Y H:i') }}</p>
                <p style="margin: 5px 0;"><strong>Total Amount:</strong> {{ number_format($sale->total, 2) }}</p>
            </div>

            <p>We hope to see you again soon!</p>

            <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">

            <p style="font-size: 0.8em; color: #7f8c8d;">
                {{ $salonData['name'] }}<br>
                @if(!empty($salonData['phone'])) Tel: {{ $salonData['phone'] }}<br> @endif
                @if(!empty($salonData['website'])) {{ $salonData['website'] }} @endif
            </p>
        @endif
    </div>
</body>

</html>