<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Notification' }}</title>
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
            margin: 0;
            padding: 0;
            background-color: #f0ece6;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
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
            letter-spacing: 0.5px;
        }

        .content {
            padding: 36px 32px;
            color: #4b5563;
            font-size: 15px;
        }

        .content h2 {
            color: #0b0f17;
            font-size: 20px;
            margin-top: 0;
            font-weight: 700;
        }

        .content p {
            margin-bottom: 16px;
            line-height: 1.65;
        }

        .button {
            display: inline-block;
            padding: 13px 32px;
            background: linear-gradient(135deg, {{ $accentColor }} 0%, {{ $accentHover }} 100%);
            color: #0b0f17 !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 4px 14px rgba(197, 168, 128, 0.35);
        }

        .footer {
            background-color: {{ $headerColor }};
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .footer a {
            color: {{ $accentColor }};
            text-decoration: none;
        }

        @media only screen and (max-width: 600px) {
            .container {
                margin: 0;
                border-radius: 0;
                width: 100%;
            }

            .content {
                padding: 24px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            @if(!empty($appLogo))
                <div style="margin-bottom: 10px;">
                    <img src="{{ $appLogo }}" alt="{{ $appName }}" style="max-height: 42px; max-width: 180px;">
                </div>
            @endif
            <h1>{{ $title ?? $appName }}</h1>
        </div>
        <div class="content">
            {!! $content !!}
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            <p style="margin-top: 4px; font-size: 11px; color: #64748b;">This is an automated notification.</p>
        </div>
    </div>
</body>

</html>