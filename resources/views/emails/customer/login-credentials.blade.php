<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Your Client Portal Access — {{ $salon->name }}</title>
    <style>
        /* Reset */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }

        body {
            margin: 0;
            padding: 0;
            background-color: #f0ece6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f0ece6;
            padding: 40px 16px;
            box-sizing: border-box;
        }

        .email-card {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(0,0,0,0.10);
        }

        /* Header */
        .email-header {
            background: linear-gradient(135deg, #0b0f17 0%, #131926 100%);
            padding: 36px 40px;
            text-align: center;
            border-bottom: 3px solid #c5a880;
        }

        .email-header .salon-logo {
            max-height: 48px;
            max-width: 200px;
            margin-bottom: 12px;
        }

        .email-header .salon-name {
            font-size: 22px;
            font-weight: 700;
            color: #c5a880;
            letter-spacing: 0.5px;
            margin: 0 0 4px;
        }

        .email-header .portal-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #94a3b8;
            margin-top: 6px;
        }

        /* Content */
        .email-body {
            padding: 40px 40px 32px;
        }

        .greeting-line {
            font-size: 24px;
            font-weight: 700;
            color: #0b0f17;
            margin: 0 0 12px;
            line-height: 1.3;
        }

        .intro-text {
            font-size: 15px;
            color: #4b5563;
            line-height: 1.65;
            margin: 0 0 28px;
        }

        /* Credentials Box */
        .credentials-box {
            background: #fdfcfa;
            border-left: 4px solid #c5a880;
            border-radius: 0 10px 10px 0;
            padding: 20px 24px;
            margin-bottom: 24px;
            border: 1px solid #e8dccb;
            border-left: 4px solid #c5a880;
        }

        .credentials-box-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9ca3af;
            margin: 0 0 14px;
        }

        .cred-row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 12px;
            gap: 10px;
        }

        .cred-label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            min-width: 130px;
            padding-top: 2px;
        }

        .cred-value {
            font-size: 14px;
            color: #111827;
            font-weight: 500;
            word-break: break-all;
        }

        .cred-password {
            font-family: 'Courier New', Courier, monospace;
            font-size: 20px;
            font-weight: 700;
            color: #0b0f17;
            letter-spacing: 3px;
            background: #fff8f0;
            padding: 6px 14px;
            border-radius: 6px;
            border: 1px dashed #c5a880;
            display: inline-block;
        }

        /* Security Notice */
        .security-notice {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 28px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .security-notice .icon {
            font-size: 16px;
            margin-top: 1px;
            flex-shrink: 0;
        }

        .security-notice p {
            font-size: 13px;
            color: #92400e;
            margin: 0;
            line-height: 1.55;
        }

        /* CTA Button */
        .cta-wrapper {
            text-align: center;
            margin-bottom: 32px;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #c5a880 0%, #b38e5d 100%);
            color: #0b0f17 !important;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            padding: 14px 36px;
            border-radius: 8px;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 16px rgba(197,168,128,0.35);
        }

        /* Divider */
        .divider {
            border: none;
            border-top: 1px solid #e8dccb;
            margin: 0 0 24px;
        }

        /* Help Section */
        .help-section {
            background: #f9fafb;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 8px;
        }

        .help-section p {
            font-size: 13px;
            color: #6b7280;
            margin: 0 0 6px;
            line-height: 1.5;
        }

        .help-section a {
            color: #c5a880;
            text-decoration: none;
            font-weight: 600;
        }

        /* Footer */
        .email-footer {
            background: linear-gradient(135deg, #0b0f17 0%, #131926 100%);
            padding: 24px 40px;
            text-align: center;
        }

        .email-footer p {
            font-size: 12px;
            color: #64748b;
            margin: 0 0 4px;
            line-height: 1.6;
        }

        .email-footer .footer-brand {
            color: #c5a880;
            font-weight: 600;
        }

        /* Responsive */
        @media only screen and (max-width: 480px) {
            .email-body { padding: 28px 24px 24px; }
            .email-header { padding: 28px 24px; }
            .email-footer { padding: 20px 24px; }
            .cred-row { flex-direction: column; gap: 4px; }
            .cred-label { min-width: auto; }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-card">

            {{-- ============================================================ --}}
            {{-- HEADER --}}
            {{-- ============================================================ --}}
            <div class="email-header">
                @if(!empty($appLogo))
                    <div style="margin-bottom: 14px;">
                        <img src="{{ $appLogo }}" alt="{{ $salon->name }}" class="salon-logo">
                    </div>
                @endif
                <div class="salon-name">{{ $salon->name }}</div>
                <div class="portal-badge">Client Portal Access</div>
            </div>

            {{-- ============================================================ --}}
            {{-- BODY --}}
            {{-- ============================================================ --}}
            <div class="email-body">

                <p class="greeting-line">Hello, {{ $customer->name }}</p>

                <p class="intro-text">
                    Great news — <strong>{{ $salon->name }}</strong> has set up a personal client portal account for you.
                    You can now log in to view your upcoming appointments, track your service history, and enjoy exclusive
                    member benefits — all in one place.
                </p>

                {{-- Credentials Box --}}
                <div class="credentials-box">
                    <div class="credentials-box-title">Account Credentials</div>

                    <div class="cred-row">
                        <span class="cred-label">Login Email:</span>
                        <span class="cred-value">{{ $customer->email }}</span>
                    </div>

                    <div class="cred-row">
                        <span class="cred-label">Temporary Password:</span>
                        <span class="cred-value">
                            <span class="cred-password">{{ $plainPassword }}</span>
                        </span>
                    </div>

                    <div class="cred-row">
                        <span class="cred-label">Login URL:</span>
                        <span class="cred-value">
                            <a href="{{ $loginUrl }}" style="color: #c5a880; text-decoration: none; font-weight: 600;">{{ $loginUrl }}</a>
                        </span>
                    </div>
                </div>

                {{-- Security Notice --}}
                <div class="security-notice">
                    <p>
                        <strong>Security reminder:</strong> Please change your password immediately after your first login.
                        This temporary password will expire if not used within <strong>7 days</strong>.
                        Never share your credentials with anyone.
                    </p>
                </div>

                {{-- CTA Button --}}
                <div class="cta-wrapper">
                    <a href="{{ $loginUrl }}" class="cta-button">
                        Sign In to Your Portal &rarr;
                    </a>
                </div>

                <hr class="divider">

                {{-- Help Section --}}
                <div class="help-section">
                    <p><strong>Need help accessing your account?</strong></p>
                    <p>Contact {{ $salon->name }} and we will be happy to assist you.</p>
                    @if(!empty($salon->email))
                        <p>Email: <a href="mailto:{{ $salon->email }}">{{ $salon->email }}</a></p>
                    @endif
                    @if(!empty($salon->phone))
                        <p>Phone: <a href="tel:{{ $salon->phone }}">{{ $salon->phone }}</a></p>
                    @endif
                </div>

            </div>

            {{-- ============================================================ --}}
            {{-- FOOTER --}}
            {{-- ============================================================ --}}
            <div class="email-footer">
                <p class="footer-brand">{{ $salon->name }}</p>
                <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
                <p style="margin-top: 10px; font-size: 11px; color: #475569;">
                    You received this email because {{ $salon->name }} created a client portal account for you.<br>
                    If you believe this was a mistake, please ignore this email or contact {{ $salon->name }} directly.
                </p>
            </div>

        </div>
    </div>
</body>
</html>
