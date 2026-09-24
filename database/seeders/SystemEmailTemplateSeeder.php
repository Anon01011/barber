<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class SystemEmailTemplateSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            [
                'type' => 'subscription_welcome',
                'subject' => 'Welcome to {{ $app_name }}!',
                'content' => "<p>Hi {{ \$salon_name }},</p>\n\n<p>Thank you for registering with us. We're excited to have you on board!</p>\n\n<p>You have subscribed to the <strong>{{ \$plan_name }}</strong>.</p>\n\n@if(\$trial_days > 0)\n<p>Your {{ \$trial_days }}-day free trial starts today.</p>\n@endif\n\n<p style=\"text-align: center;\">\n    <a href=\"{{ \$login_url }}\" class=\"button\">Login to Dashboard</a>\n</p>\n\n<p>If you have any questions, feel free to reply to this email.</p>\n\n<p>Thanks,<br>{{ \$app_name }}</p>",
                'variables' => ['app_name', 'salon_name', 'plan_name', 'trial_days', 'login_url'],
            ],
            [
                'type' => 'subscription_approved',
                'subject' => '🎉 Congratulations! Your Salon Has Been Approved',
                'content' => "<p>Hello <strong>{{ \$salon_name }}</strong>,</p>\n\n<p>Great news! Your manual payment has been verified and your salon account has been <strong>approved and activated</strong>.</p>\n\n<h3>Your Subscription Details</h3>\n<ul>\n    <li><strong>Plan:</strong> {{ \$plan_name }}</li>\n    <li><strong>Status:</strong> Active ✅</li>\n</ul>\n\n<p>You can now access all the features of your subscription plan and start managing your salon operations.</p>\n\n<h3>What's Next?</h3>\n<ol>\n    <li><strong>Login to Your Dashboard</strong>: Click the button below to access your salon dashboard</li>\n    <li><strong>Complete Your Setup</strong>: Add your services, staff, and customize your settings</li>\n    <li><strong>Start Taking Bookings</strong>: Your salon is ready to accept appointments!</li>\n</ol>\n\n<p style=\"text-align: center;\">\n    <a href=\"{{ \$dashboard_url }}\" class=\"button\">Access Dashboard</a>\n</p>\n\n<p>If you have any questions or need assistance getting started, our support team is here to help.</p>\n\n<p>Thank you for choosing {{ \$app_name }}!</p>\n\n<p>Best regards,<br>The {{ \$app_name }} Team</p>\n\n<hr>\n\n<p style=\"font-size: 12px; color: #888;\">If you're having trouble clicking the button, copy and paste this URL into your browser: {{ \$dashboard_url }}</p>",
                'variables' => ['app_name', 'salon_name', 'plan_name', 'dashboard_url'],
            ],
            [
                'type' => 'subscription_renewed',
                'subject' => 'Subscription Renewed',
                'content' => "<p>Hi {{ \$salon_name }},</p>\n\n<p>Good news! Your subscription to the <strong>{{ \$plan_name }}</strong> has been successfully renewed.</p>\n\n<p>Your new subscription period ends on <strong>{{ \$end_date }}</strong>.</p>\n\n<p style=\"text-align: center;\">\n    <a href=\"{{ \$dashboard_url }}\" class=\"button\">Go to Dashboard</a>\n</p>\n\n<p>Thank you for continuing your journey with us!</p>\n\n<p>Thanks,<br>{{ \$app_name }}</p>",
                'variables' => ['app_name', 'salon_name', 'plan_name', 'end_date', 'dashboard_url'],
            ],
            [
                'type' => 'subscription_cancelled',
                'subject' => 'Subscription Cancelled',
                'content' => "<p>Hi {{ \$salon_name }},</p>\n\n<p>We're sorry to see you go. Your subscription to the <strong>{{ \$plan_name }}</strong> has been cancelled.</p>\n\n<p>You will continue to have access to your account until <strong>{{ \$end_date }}</strong>.</p>\n\n<p>If you change your mind, you can renew your subscription at any time from your dashboard.</p>\n\n<p>If you have any feedback on how we can improve, please let us know.</p>\n\n<p>Thanks,<br>{{ \$app_name }}</p>",
                'variables' => ['app_name', 'salon_name', 'plan_name', 'end_date'],
            ],
            [
                'type' => 'subscription_expired',
                'subject' => 'Subscription Expired',
                'content' => "<p>Hello {{ \$salon_name }},</p>\n\n<p>Your <strong>{{ \$plan_name }}</strong> subscription expired on <strong>{{ \$expiry_date }}</strong>.</p>\n\n<p>To continue using our services, please renew your subscription as soon as possible.</p>\n\n<p>Without an active subscription, you will lose access to:</p>\n<ul>\n    <li>Booking management</li>\n    <li>Customer database</li>\n    <li>Staff scheduling</li>\n    <li>Reports and analytics</li>\n    <li>And all other premium features</li>\n</ul>\n\n<p style=\"text-align: center;\">\n    <a href=\"{{ \$renew_url }}\" class=\"button\">Renew Subscription</a>\n</p>\n\n<p>If you have any questions, please don't hesitate to contact our support team.</p>\n\n<p>Best regards,<br>The {{ \$app_name }} Team</p>",
                'variables' => ['app_name', 'salon_name', 'plan_name', 'expiry_date', 'renew_url'],
            ],
            [
                'type' => 'subscription_expiring_soon',
                'subject' => '⚠️ Subscription Expiring Soon',
                'content' => "<p>Hello {{ \$salon_name }},</p>\n\n<div style=\"background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;\">\n    <strong>Important Notice:</strong> Your <strong>{{ \$plan_name }}</strong> subscription will expire in <strong>{{ \$days_left }} day{{ \$days_left > 1 ? 's' : '' }}</strong> on {{ \$expiry_date }}.\n</div>\n\n<p>To avoid any interruption to your service, please renew your subscription before it expires.</p>\n\n<p><strong>What happens if my subscription expires?</strong></p>\n<ul>\n    <li>You will lose access to all premium features</li>\n    <li>Your staff won't be able to manage bookings</li>\n    <li>Customer data will be inaccessible</li>\n    <li>Reports and analytics will be unavailable</li>\n</ul>\n\n<p style=\"text-align: center;\">\n    <a href=\"{{ \$renew_url }}\" class=\"button\">Renew Now</a>\n</p>\n\n<p>Renewing is quick and easy. Simply click the button above to continue enjoying all the features of {{ \$app_name }}.</p>\n\n<p>If you have any questions or need assistance, our support team is here to help.</p>\n\n<p>Best regards,<br>The {{ \$app_name }} Team</p>",
                'variables' => ['app_name', 'salon_name', 'plan_name', 'days_left', 'expiry_date', 'renew_url'],
            ],
            // Salon Operational Templates
            [
                'type' => 'booking_confirmation',
                'subject' => 'Booking Confirmation - {{ $salon_name }}',
                'content' => "<p>Dear {{ \$customer_name }},</p>\n\n<p>Your appointment has been confirmed!</p>\n\n<p><strong>Booking Details:</strong></p>\n<ul>\n    <li><strong>Service:</strong> {{ \$service_name }}</li>\n    <li><strong>Date:</strong> {{ \$booking_date }}</li>\n    <li><strong>Time:</strong> {{ \$booking_time }}</li>\n    <li><strong>Staff:</strong> {{ \$staff_name }}</li>\n</ul>\n\n<p>Address: {{ \$salon_address }}</p>\n<p>Phone: {{ \$salon_phone }}</p>\n\n<p>We look forward to seeing you!</p>\n\n<p>Best regards,<br>{{ \$salon_name }}</p>",
                'variables' => ['customer_name', 'salon_name', 'service_name', 'booking_date', 'booking_time', 'staff_name', 'salon_address', 'salon_phone'],
            ],
            [
                'type' => 'booking_reminder',
                'subject' => 'Reminder: Appointment Tomorrow at {{ $salon_name }}',
                'content' => "<p>Hi {{ \$customer_name }},</p>\n\n<p>This is a friendly reminder about your appointment tomorrow.</p>\n\n<p><strong>When:</strong> {{ \$booking_date }} at {{ \$booking_time }}</p>\n<p><strong>Service:</strong> {{ \$service_name }}</p>\n<p><strong>With:</strong> {{ \$staff_name }}</p>\n\n<p>If you need to reschedule, please contact us at {{ \$salon_phone }}.</p>\n\n<p>See you soon!</p>\n\n<p>Best regards,<br>{{ \$salon_name }}</p>",
                'variables' => ['customer_name', 'salon_name', 'booking_date', 'booking_time', 'service_name', 'staff_name', 'salon_phone'],
            ],
            [
                'type' => 'booking_cancellation',
                'subject' => 'Booking Cancelled - {{ $salon_name }}',
                'content' => "<p>Dear {{ \$customer_name }},</p>\n\n<p>Your appointment for <strong>{{ \$service_name }}</strong> on <strong>{{ \$booking_date }}</strong> at <strong>{{ \$booking_time }}</strong> has been cancelled.</p>\n\n<p>If you did not request this cancellation or would like to reschedule, please contact us immediately.</p>\n\n<p>Phone: {{ \$salon_phone }}</p>\n\n<p>Best regards,<br>{{ \$salon_name }}</p>",
                'variables' => ['customer_name', 'salon_name', 'service_name', 'booking_date', 'booking_time', 'salon_phone'],
            ],
            [
                'type' => 'payment_receipt',
                'subject' => 'Payment Receipt - {{ $salon_name }}',
                'content' => "<p>Hi {{ \$customer_name }},</p>\n\n<p>Thank you for your payment.</p>\n\n<p><strong>Total Paid:</strong> {{ \$amount }}</p>\n<p><strong>Service:</strong> {{ \$service_name }}</p>\n<p><strong>Date:</strong> {{ \$booking_date }}</p>\n\n<p>We appreciate your business!</p>\n\n<p>Best regards,<br>{{ \$salon_name }}</p>",
                'variables' => ['customer_name', 'salon_name', 'amount', 'service_name', 'booking_date'],
            ],
            [
                'type' => 'welcome_email',
                'subject' => 'Welcome to {{ $salon_name }}!',
                'content' => "<p>Hi {{ \$customer_name }},</p>\n\n<p>Welcome to <strong>{{ \$salon_name }}</strong>! We are thrilled to have you as a client.</p>\n\n<p>You can now book appointments easily online and keep track of your visits.</p>\n\n<p>If you have any questions, feel free to reach out to us at {{ \$salon_phone }}.</p>\n\n<p>We can't wait to serve you!</p>\n\n<p>Best regards,<br>The {{ \$salon_name }} Team</p>",
                'variables' => ['customer_name', 'salon_name', 'salon_phone'],
            ],
            [
                'type' => 'guest_booking_confirmation',
                'subject' => 'Booking Confirmation (Guest) - {{ $salon_name }}',
                'content' => "<p>Dear {{ \$customer_name }},</p>\n\n<p>Thank you for booking with us! Your appointment is confirmed.</p>\n\n<p><strong>Details:</strong></p>\n<ul>\n    <li><strong>Service:</strong> {{ \$service_name }}</li>\n    <li><strong>Date:</strong> {{ \$booking_date }}</li>\n    <li><strong>Time:</strong> {{ \$booking_time }}</li>\n    <li><strong>Staff:</strong> {{ \$staff_name }}</li>\n</ul>\n\n<p>Address: {{ \$salon_address }}</p>\n\n<p>Please arrive 5 minutes early. We look forward to seeing you!</p>\n\n<p>Best regards,<br>{{ \$salon_name }}</p>",
                'variables' => ['customer_name', 'salon_name', 'service_name', 'booking_date', 'booking_time', 'staff_name', 'salon_address'],
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                [
                    'type' => $template['type'],
                    'salon_id' => null, // System template
                ],
                [
                    'subject' => $template['subject'],
                    'content' => $template['content'],
                    'variables' => $template['variables'],
                    'is_active' => true,
                ]
            );
        }
    }
}
