@component('mail::message')
# 🎉 Congratulations! Your Salon Has Been Approved

Hello **{{ $salon_name }}**,

Great news! Your manual payment has been verified and your salon account has been **approved and activated**.

## Your Subscription Details

- **Plan**: {{ $plan_name }}
- **Status**: Active ✅
- **Dashboard**: [Access Your Dashboard]({{ $dashboard_url }})

You can now access all the features of your subscription plan and start managing your salon operations.

## What's Next?

1. **Login to Your Dashboard**: Click the button below to access your salon dashboard
2. **Complete Your Setup**: Add your services, staff, and customize your settings
3. **Start Taking Bookings**: Your salon is ready to accept appointments!

@component('mail::button', ['url' => $dashboard_url, 'color' => 'success'])
Access Dashboard
@endcomponent

If you have any questions or need assistance getting started, our support team is here to help.

Thank you for choosing {{ config('app.name') }}!

Best regards,<br>
The {{ config('app.name') }} Team

---

<small>If you're having trouble clicking the button, copy and paste this URL into your browser:
    {{ $dashboard_url }}</small>
@endcomponent