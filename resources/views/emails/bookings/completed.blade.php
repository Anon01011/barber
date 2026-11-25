<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Completed</title>
    @php
        $settingsService = app(\App\Services\SettingsService::class);
        $palette = $settingsService->getThemePalette();
        $headerColor = $palette['primary'] ?? '#0b0f17';
        $accentColor = $palette['accent'] ?? '#c5a880';
        $accentHover = $palette['accent_hover'] ?? '#b38e5d';
        $appLogo     = $settingsService->getLogoUrl();
        $appName     = $settingsService->get('app_name', config('app.name', 'SalonPro'));
    @endphp
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f0ece6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e8dccb;
        }

        .header {
            background-color: {{ $headerColor }};
            color: #ffffff;
            padding: 32px 30px;
            text-align: center;
            border-bottom: 3px solid {{ $accentColor }};
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: {{ $accentColor }};
            letter-spacing: 0.5px;
        }

        .content {
            padding: 36px 32px;
            color: #4b5563;
        }

        .booking-details {
            background: #fdfcfa;
            border-left: 4px solid {{ $accentColor }};
            border: 1px solid #e8dccb;
            border-left: 4px solid {{ $accentColor }};
            padding: 20px 24px;
            margin: 24px 0;
            border-radius: 0 8px 8px 0;
        }

        .booking-details h3 {
            margin-top: 0;
            color: #0b0f17;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #64748b;
        }

        .detail-value {
            color: #0f172a;
            font-weight: 600;
        }

        .rating-section {
            text-align: center;
            margin: 28px 0;
            padding: 28px 24px;
            background: {{ $headerColor }};
            border-radius: 10px;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .rating-section h2 {
            margin-top: 0;
            font-size: 20px;
            color: {{ $accentColor }};
            font-weight: 700;
        }

        .rating-section p {
            font-size: 14px;
            margin: 10px 0;
            color: #cbd5e1;
        }

        .btn {
            display: inline-block;
            padding: 13px 36px;
            background: linear-gradient(135deg, {{ $accentColor }} 0%, {{ $accentHover }} 100%);
            color: #0b0f17 !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            margin-top: 15px;
            box-shadow: 0 4px 14px rgba(197, 168, 128, 0.35);
        }

        .footer {
            background: #fafafa;
            padding: 20px;
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            border-top: 1px solid #f1f5f9;
        }

        .thank-you {
            text-align: center;
            color: #0b0f17;
            font-size: 16px;
            font-weight: 600;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            @if(!empty($appLogo))
                <div style="margin-bottom: 10px;">
                    <img src="{{ $appLogo }}" alt="{{ $booking->salon->name ?? $appName }}" style="max-height: 42px; max-width: 180px;">
                </div>
            @endif
            <h1>Thank You for Visiting</h1>
        </div>

        <div class="content">
            <p>Dear {{ $booking->customer->name }},</p>

            <p>Thank you for choosing <strong>{{ $booking->salon->name ?? 'our salon' }}</strong>. We hope you enjoyed your visit with us.</p>

            <div class="booking-details">
                <h3>Booking Summary</h3>
                <div class="detail-row">
                    <span class="detail-label">Service:</span>
                    <span class="detail-value">{{ $booking->service->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Staff Member:</span>
                    <span class="detail-value">{{ $booking->staff->name ?? 'Assigned Staff' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date & Time:</span>
                    <span class="detail-value">{{ $booking->start_time->format('F j, Y \a\t g:i A') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount:</span>
                    <span class="detail-value">{{ currency_symbol() }}{{ number_format($booking->amount, 2) }}</span>
                </div>
            </div>

            <div class="rating-section">
                <h2>How Was Your Experience?</h2>
                <p>We would love to hear your feedback. Your rating helps us continuously improve our service quality.</p>
                <a href="{{ $ratingUrl }}" class="btn">Rate Your Experience &rarr;</a>
            </div>

            <p class="thank-you">We look forward to serving you again soon.</p>

            <p>If you have any questions or feedback, please feel free to contact us directly.</p>

            <p>Warm regards,<br>
                <strong>{{ $booking->salon->name ?? $appName }} Team</strong>
            </p>
        </div>

        <div class="footer">
            <p>This is an automated notification. Please do not reply directly to this message.</p>
            @if($booking->customer->is_guest)
                <p style="font-size: 11px; margin-top: 6px;">
                    This rating link will expire in 30 days.
                </p>
            @endif
        </div>
    </div>
</body>

</html>