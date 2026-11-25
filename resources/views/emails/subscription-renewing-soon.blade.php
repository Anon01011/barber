<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Renewal Reminder</title>
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
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f0ece6;
        }

        .container {
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
            font-size: 22px;
            font-weight: 700;
            color: {{ $accentColor }};
        }

        .content {
            background: #ffffff;
            padding: 32px;
            color: #4b5563;
        }

        .highlight-box {
            background: #fdfcfa;
            border-left: 4px solid {{ $accentColor }};
            border: 1px solid #e8dccb;
            border-left: 4px solid {{ $accentColor }};
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }

        .highlight-box h2 {
            margin-top: 0;
            font-size: 18px;
            color: #0b0f17;
            font-weight: 700;
        }

        .highlight-box h3 {
            margin-top: 0;
            font-size: 15px;
            color: #0b0f17;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .amount {
            font-size: 28px;
            font-weight: bold;
            color: #0b0f17;
            margin: 10px 0;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, {{ $accentColor }} 0%, {{ $accentHover }} 100%);
            color: #0b0f17 !important;
            padding: 13px 36px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            margin: 20px 0;
            box-shadow: 0 4px 14px rgba(197, 168, 128, 0.35);
        }

        .footer {
            text-align: center;
            padding: 24px;
            background: {{ $headerColor }};
            color: #94a3b8;
            font-size: 12px;
        }

        .urgent {
            background: #fffbeb;
            border-left-color: #f59e0b;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            @if(!empty($appLogo))
                <div style="margin-bottom: 10px;">
                    <img src="{{ $appLogo }}" alt="{{ $appName }}" style="max-height: 40px; max-width: 180px;">
                </div>
            @endif
            <h1>{{ $daysUntilRenewal === 1 ? 'Renewal Tomorrow' : 'Subscription Renewal Reminder' }}</h1>
        </div>

        <div class="content">
            <p>Hello {{ $salon->owner->name ?? 'Salon Owner' }},</p>

            @if($daysUntilRenewal === 1)
                <div class="highlight-box urgent">
                    <h2>Your subscription renews tomorrow</h2>
                    <p>Your <strong>{{ $plan->name }}</strong> subscription for <strong>{{ $salon->name }}</strong> will
                        automatically renew on <strong>{{ $renewalDate->format('F j, Y') }}</strong>.</p>
                </div>
            @else
                <div class="highlight-box">
                    <h2>Your subscription renews in {{ $daysUntilRenewal }} days</h2>
                    <p>Your <strong>{{ $plan->name }}</strong> subscription for <strong>{{ $salon->name }}</strong> will
                        automatically renew on <strong>{{ $renewalDate->format('F j, Y') }}</strong>.</p>
                </div>
            @endif

            <div class="highlight-box">
                <h3>Renewal Details:</h3>
                <table style="width: 100%; margin: 15px 0;">
                    <tr>
                        <td><strong>Plan:</strong></td>
                        <td>{{ $plan->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Renewal Date:</strong></td>
                        <td>{{ $renewalDate->format('F j, Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Amount:</strong></td>
                        <td class="amount">${{ number_format($amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Billing Cycle:</strong></td>
                        <td>{{ $plan->duration_in_days }} days</td>
                    </tr>
                </table>
            </div>

            <p>Your payment method on file will be charged automatically. No action is required unless you wish to make changes.</p>

            <div style="text-align: center;">
                <a href="{{ route('admin.saas.subscription.index', ['salon_slug' => $salon->slug]) }}" class="button">
                    Manage Subscription &rarr;
                </a>
            </div>

            <p style="margin-top: 30px; font-size: 13px; color: #64748b;">
                <strong>Need to make changes?</strong><br>
                You can update your payment method, change your plan, or manage your subscription anytime from your account dashboard.
            </p>

            @if($daysUntilRenewal > 1)
                <p style="font-size: 13px; color: #64748b;">
                    You will receive another reminder closer to your renewal date.
                </p>
            @endif
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            <p style="margin-top: 4px; font-size: 11px; color: #64748b;">This is an automated subscription reminder.</p>
        </div>
    </div>
</body>

</html>