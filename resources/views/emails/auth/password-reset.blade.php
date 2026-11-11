<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Password Reset Request</title>
<!--[if mso]><noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript><![endif]-->
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Oxygen, Ubuntu, sans-serif;
        background-color: #f1f5f9;
        color: #334155;
        line-height: 1.6;
        -webkit-text-size-adjust: 100%;
        -ms-text-size-adjust: 100%;
    }
    .email-wrapper {
        width: 100%;
        background-color: #f1f5f9;
        padding: 40px 16px;
    }
    .email-container {
        max-width: 580px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    }

    /* ── Header ── */
    .header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        padding: 36px 40px 32px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .header::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 160px; height: 160px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }
    .header::after {
        content: '';
        position: absolute;
        bottom: -50px; left: -30px;
        width: 120px; height: 120px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .header-icon {
        display: inline-flex;
        width: 56px; height: 56px;
        background: rgba(255,255,255,0.15);
        border-radius: 14px;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        border: 1px solid rgba(255,255,255,0.2);
    }
    .header-icon svg { width: 28px; height: 28px; }
    .header h1 {
        color: #ffffff;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.3px;
    }
    .header p {
        color: rgba(255,255,255,0.75);
        font-size: 13px;
        margin-top: 6px;
    }

    /* ── Salon Badge ── */
    .salon-badge {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 20px;
        margin: 24px 40px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .salon-avatar {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        display: flex; align-items: center; justify-content: center;
        color: white; font-weight: 700; font-size: 15px;
        flex-shrink: 0;
    }
    .salon-info { flex: 1; }
    .salon-name {
        font-size: 13px; font-weight: 700; color: #1e293b;
        line-height: 1.3;
    }
    .salon-role {
        display: inline-block;
        font-size: 10px; font-weight: 600;
        padding: 2px 8px;
        border-radius: 20px;
        margin-top: 3px;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    .role-owner    { background: #ede9fe; color: #6d28d9; }
    .role-admin    { background: #dbeafe; color: #1d4ed8; }
    .role-staff    { background: #d1fae5; color: #065f46; }
    .role-customer { background: #fef3c7; color: #92400e; }
    .role-default  { background: #f1f5f9; color: #475569; }

    /* ── Body ── */
    .body {
        padding: 28px 40px 32px;
    }
    .greeting {
        font-size: 18px; font-weight: 700; color: #1e293b;
        margin-bottom: 12px;
    }
    .body p {
        font-size: 14px; color: #64748b;
        margin-bottom: 16px; line-height: 1.65;
    }

    /* ── CTA Button ── */
    .cta-wrapper {
        text-align: center;
        margin: 28px 0;
    }
    .cta-btn {
        display: inline-block;
        padding: 14px 36px;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: #ffffff !important;
        text-decoration: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: 0.2px;
        box-shadow: 0 4px 16px rgba(79, 70, 229, 0.35);
    }

    /* ── Info Box ── */
    .info-box {
        background: #fafafa;
        border: 1px solid #e2e8f0;
        border-left: 4px solid #4f46e5;
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 20px;
    }
    .info-box p {
        margin: 0; font-size: 13px; color: #475569;
    }

    /* ── Warning Box ── */
    .warning-box {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-left: 4px solid #f59e0b;
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 20px;
    }
    .warning-box p {
        margin: 0; font-size: 13px; color: #92400e;
    }

    /* ── Link fallback ── */
    .link-fallback {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px dashed #e2e8f0;
    }
    .link-fallback p {
        font-size: 12px; color: #94a3b8; margin-bottom: 8px;
    }
    .link-url {
        font-size: 11px; color: #4f46e5;
        word-break: break-all; line-height: 1.4;
    }

    /* ── Footer ── */
    .footer {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        padding: 20px 40px;
        text-align: center;
    }
    .footer p {
        font-size: 11px; color: #94a3b8;
        margin-bottom: 4px; line-height: 1.5;
    }
    .footer strong { color: #64748b; }

    /* ── Responsive ── */
    @media only screen and (max-width: 600px) {
        .email-wrapper { padding: 0; }
        .email-container { border-radius: 0; }
        .header { padding: 28px 24px 24px; }
        .body { padding: 24px; }
        .salon-badge { margin: 20px 24px 0; }
        .footer { padding: 16px 24px; }
        .cta-btn { padding: 13px 28px; font-size: 14px; }
    }
</style>
</head>
<body>
<div class="email-wrapper">
<div class="email-container">

    {{-- ── HEADER ── --}}
    <div class="header">
        <div class="header-icon">
            <svg fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
        </div>
        <h1>Password Reset Request</h1>
        <p>Secure link to reset your password</p>
    </div>

    {{-- ── SALON BADGE ── --}}
    @if($salon)
    <div class="salon-badge">
        <div class="salon-avatar">{{ strtoupper(substr($salon->name, 0, 1)) }}</div>
        <div class="salon-info">
            <div class="salon-name">{{ $salon->name }}</div>
            <span class="salon-role
                @if(in_array($userRole, ['salon_owner','owner'])) role-owner
                @elseif(in_array($userRole, ['admin','salon_admin'])) role-admin
                @elseif(in_array($userRole, ['staff','employee','barber'])) role-staff
                @elseif($userRole === 'customer') role-customer
                @else role-default
                @endif">
                {{ ucwords(str_replace('_', ' ', $userRole ?? 'User')) }}
            </span>
        </div>
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
    </div>
    @endif

    {{-- ── BODY ── --}}
    <div class="body">
        <div class="greeting">Hi {{ $userName }} 👋</div>

        <p>We received a request to reset the password for your account associated with <strong>{{ $userEmail }}</strong>.</p>

        <p>Click the button below to create a new password. This link is <strong>valid for 60 minutes</strong> and can only be used once.</p>

        <div class="cta-wrapper">
            <a href="{{ $resetUrl }}" class="cta-btn">Reset My Password</a>
        </div>

        <div class="info-box">
            <p>
                🔒 <strong>Security tip:</strong> This link will expire in 60 minutes.
                After resetting, you'll be redirected to the login page.
            </p>
        </div>

        <div class="warning-box">
            <p>
                ⚠️ <strong>Didn't request this?</strong> If you didn't request a password reset, you can safely ignore this email.
                Your password will remain unchanged and no action is needed.
            </p>
        </div>

        <div class="link-fallback">
            <p>If the button above doesn't work, copy and paste this link into your browser:</p>
            <div class="link-url">{{ $resetUrl }}</div>
        </div>
    </div>

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <p>Sent by <strong>{{ $appName }}</strong> · Secure password reset system</p>
        @if($salon)
        <p>This email was sent because a reset was requested for a <strong>{{ $salon->name }}</strong> account.</p>
        @endif
        <p style="margin-top:8px;">© {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
    </div>

</div>
</div>
</body>
</html>

