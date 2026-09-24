<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'type' => 'booking_confirmation',
                'subject' => 'Booking Confirmation - {{salon_name}}',
                'content' => '<h2>Booking Confirmed!</h2>
<p>Dear {{customer_name}},</p>
<p>Your appointment has been confirmed.</p>
<p><strong>Details:</strong></p>
<ul>
    <li>Service: {{service_name}}</li>
    <li>Date: {{booking_date}}</li>
    <li>Time: {{booking_time}}</li>
    <li>Staff: {{staff_name}}</li>
    <li>Amount: {{amount}}</li>
</ul>
<p>We look forward to seeing you!</p>
<p>Best regards,<br>{{salon_name}}</p>',
                'variables' => ['customer_name', 'salon_name', 'service_name', 'booking_date', 'booking_time', 'staff_name', 'amount'],
                'is_active' => true
            ],
            [
                'type' => 'booking_reminder',
                'subject' => 'Appointment Reminder - {{salon_name}}',
                'content' => '<h2>Appointment Reminder</h2>
<p>Dear {{customer_name}},</p>
<p>This is a friendly reminder about your upcoming appointment.</p>
<p><strong>Details:</strong></p>
<ul>
    <li>Service: {{service_name}}</li>
    <li>Date: {{booking_date}}</li>
    <li>Time: {{booking_time}}</li>
    <li>Staff: {{staff_name}}</li>
</ul>
<p>Please arrive 10 minutes early.</p>
<p>Best regards,<br>{{salon_name}}</p>',
                'variables' => ['customer_name', 'salon_name', 'service_name', 'booking_date', 'booking_time', 'staff_name'],
                'is_active' => true
            ],
            [
                'type' => 'booking_cancelled',
                'subject' => 'Booking Cancelled - {{salon_name}}',
                'content' => '<h2>Booking Cancelled</h2>
<p>Dear {{customer_name}},</p>
<p>Your appointment has been cancelled.</p>
<p><strong>Cancelled Appointment:</strong></p>
<ul>
    <li>Service: {{service_name}}</li>
    <li>Date: {{booking_date}}</li>
    <li>Time: {{booking_time}}</li>
</ul>
<p>If you would like to reschedule, please contact us.</p>
<p>Best regards,<br>{{salon_name}}</p>',
                'variables' => ['customer_name', 'salon_name', 'service_name', 'booking_date', 'booking_time'],
                'is_active' => true
            ],
            [
                'type' => 'booking_rescheduled',
                'subject' => 'Booking Rescheduled - {{salon_name}}',
                'content' => '<h2>Appointment Rescheduled</h2>
<p>Dear {{customer_name}},</p>
<p>Your appointment has been rescheduled to a new time.</p>
<p><strong>New Appointment Details:</strong></p>
<ul>
    <li>Service: {{service_name}}</li>
    <li>Date: {{booking_date}}</li>
    <li>Time: {{booking_time}}</li>
    <li>Staff: {{staff_name}}</li>
</ul>
<p>We look forward to seeing you at the new time!</p>
<p>Best regards,<br>{{salon_name}}</p>',
                'variables' => ['customer_name', 'salon_name', 'service_name', 'booking_date', 'booking_time', 'staff_name'],
                'is_active' => true
            ],
            [
                'type' => 'welcome',
                'subject' => 'Welcome to {{salon_name}}!',
                'content' => '<h2>Welcome!</h2>
<p>Dear {{customer_name}},</p>
<p>Thank you for choosing {{salon_name}}. We are excited to serve you!</p>
<p>You can now book appointments online and manage your bookings easily.</p>
<p>Best regards,<br>{{salon_name}}</p>',
                'variables' => ['customer_name', 'salon_name'],
                'is_active' => true
            ],
            [
                'type' => 'sale_receipt',
                'subject' => 'Receipt for Invoice #{{invoice_number}} - {{salon_name}}',
                'content' => '<h2>Thank you for your visit!</h2>
<p>Dear {{customer_name}},</p>
<p>Please find attached the receipt for your recent visit.</p>
<div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <p style="margin: 5px 0;"><strong>Invoice Number:</strong> #{{invoice_number}}</p>
    <p style="margin: 5px 0;"><strong>Date:</strong> {{date}}</p>
    <p style="margin: 5px 0;"><strong>Total Amount:</strong> {{total}}</p>
</div>
<p>We hope to see you again soon!</p>
<p>Best regards,<br>{{salon_name}}</p>',
                'variables' => ['customer_name', 'salon_name', 'invoice_number', 'date', 'total'],
                'is_active' => true
            ],
            [
                'type' => 'booking_status_updated',
                'subject' => 'Booking Status Update - {{booking_status}}',
                'content' => '<h2>Booking Status Update</h2>
<p>Dear {{customer_name}},</p>
<p>The status of your booking has been updated to <strong>{{booking_status}}</strong>.</p>
<p><strong>Details:</strong></p>
<ul>
    <li>Service: {{service_name}}</li>
    <li>Date: {{booking_date}}</li>
    <li>Time: {{booking_time}}</li>
    <li>Staff: {{staff_name}}</li>
</ul>
<p>Best regards,<br>{{salon_name}}</p>',
                'variables' => ['customer_name', 'salon_name', 'booking_status', 'service_name', 'booking_date', 'booking_time', 'staff_name'],
                'is_active' => true
            ]
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                [
                    'salon_id' => null, // System default
                    'type' => $template['type']
                ],
                $template
            );
        }
    }
}
