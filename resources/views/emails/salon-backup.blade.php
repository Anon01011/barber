<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Automated Data Backup</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; padding: 20px; }
        .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .card-header { background: #0f172a; color: #ffffff; padding: 24px; text-align: center; }
        .card-header h2 { margin: 0; font-size: 20px; font-weight: 700; }
        .card-body { padding: 24px; }
        .badge { display: inline-block; padding: 4px 12px; background: #ecfdf5; color: #047857; border-radius: 50px; font-weight: bold; font-size: 12px; margin-bottom: 16px; }
        .info-box { background: #f1f5f9; border-radius: 8px; padding: 16px; margin: 16px 0; font-size: 13px; line-height: 1.6; }
        .footer { text-align: center; font-size: 12px; color: #94a3b8; padding: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h2>{{ $salon->name }}</h2>
            <div style="font-size: 13px; color: #94a3b8; margin-top: 4px;">Automated 24-Hour Salon Data Backup</div>
        </div>
        <div class="card-body">
            <span class="badge">✓ Daily Backup Created Successfully</span>
            <p>Hello Administrator,</p>
            <p>Your automated 24-hour snapshot of salon operational data has been generated and is attached to this email.</p>
            
            <div class="info-box">
                <div><strong>Salon:</strong> {{ $salon->name }}</div>
                <div><strong>Backup Date:</strong> {{ $backupDate }}</div>
                <div><strong>Attached File:</strong> {{ $fileName }}</div>
                <div><strong>Contents:</strong> Services, Categories, Packages, Customers, Bookings, POS Sales, and Settings.</div>
            </div>

            <p style="font-size: 13px; color: #64748b;">
                You can import this backup file at any time from your Salon Admin Portal under <strong>Data Backup &amp; Restore</strong> should you ever need to restore your salon records.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved. Automated security notification.
        </div>
    </div>
</body>
</html>
