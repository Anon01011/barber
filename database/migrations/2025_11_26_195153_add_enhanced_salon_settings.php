<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            // Appointment & Booking Settings
            ['key' => 'appointment_buffer_time', 'value' => json_encode(15), 'type' => 'appointment'],
            ['key' => 'allow_overlapping_bookings', 'value' => json_encode(false), 'type' => 'appointment'],
            ['key' => 'require_deposit', 'value' => json_encode(false), 'type' => 'appointment'],
            ['key' => 'deposit_amount', 'value' => json_encode(0), 'type' => 'appointment'],
            ['key' => 'deposit_type', 'value' => json_encode('fixed'), 'type' => 'appointment'],
            ['key' => 'cancellation_policy', 'value' => json_encode(''), 'type' => 'appointment'],
            ['key' => 'cancellation_hours', 'value' => json_encode(24), 'type' => 'appointment'],
            ['key' => 'no_show_fee', 'value' => json_encode(0), 'type' => 'appointment'],
            ['key' => 'auto_confirm_bookings', 'value' => json_encode(false), 'type' => 'appointment'],
            ['key' => 'send_booking_confirmation', 'value' => json_encode(true), 'type' => 'appointment'],
            ['key' => 'send_reminder_sms', 'value' => json_encode(false), 'type' => 'appointment'],
            ['key' => 'reminder_hours_before', 'value' => json_encode(24), 'type' => 'appointment'],
            
            // Notification Preferences
            ['key' => 'notify_new_booking', 'value' => json_encode(true), 'type' => 'notification'],
            ['key' => 'notify_cancellation', 'value' => json_encode(true), 'type' => 'notification'],
            ['key' => 'notify_low_stock', 'value' => json_encode(true), 'type' => 'notification'],
            ['key' => 'notify_new_customer', 'value' => json_encode(false), 'type' => 'notification'],
            ['key' => 'notification_email', 'value' => json_encode(''), 'type' => 'notification'],
            ['key' => 'notification_channels', 'value' => json_encode(['email']), 'type' => 'notification'],
            ['key' => 'daily_summary_enabled', 'value' => json_encode(false), 'type' => 'notification'],
            ['key' => 'daily_summary_time', 'value' => json_encode('18:00'), 'type' => 'notification'],
            
            // Payment & Tax Settings
            ['key' => 'tax_enabled', 'value' => json_encode(false), 'type' => 'payment'],
            ['key' => 'tax_rate', 'value' => json_encode(0), 'type' => 'payment'],
            ['key' => 'tax_name', 'value' => json_encode('Tax'), 'type' => 'payment'],
            ['key' => 'tax_number', 'value' => json_encode(''), 'type' => 'payment'],
            ['key' => 'currency_code', 'value' => json_encode('USD'), 'type' => 'payment'],
            ['key' => 'currency_symbol', 'value' => json_encode('$'), 'type' => 'payment'],
            ['key' => 'payment_methods', 'value' => json_encode(['cash', 'card']), 'type' => 'payment'],
            ['key' => 'accept_cash', 'value' => json_encode(true), 'type' => 'payment'],
            ['key' => 'accept_card', 'value' => json_encode(true), 'type' => 'payment'],
            ['key' => 'accept_online', 'value' => json_encode(false), 'type' => 'payment'],
            ['key' => 'stripe_enabled', 'value' => json_encode(false), 'type' => 'payment'],
            ['key' => 'paypal_enabled', 'value' => json_encode(false), 'type' => 'payment'],
            ['key' => 'tip_enabled', 'value' => json_encode(true), 'type' => 'payment'],
            ['key' => 'default_tip_percentage', 'value' => json_encode(15), 'type' => 'payment'],
            
            // Email & SMS Templates
            ['key' => 'email_signature', 'value' => json_encode(''), 'type' => 'template'],
            ['key' => 'booking_confirmation_template', 'value' => json_encode('Dear {customer_name}, your appointment is confirmed for {date} at {time}.'), 'type' => 'template'],
            ['key' => 'booking_reminder_template', 'value' => json_encode('Reminder: You have an appointment tomorrow at {time}.'), 'type' => 'template'],
            ['key' => 'cancellation_template', 'value' => json_encode('Your appointment on {date} at {time} has been cancelled.'), 'type' => 'template'],
            ['key' => 'welcome_email_template', 'value' => json_encode('Welcome to {business_name}! We\'re excited to serve you.'), 'type' => 'template'],
            ['key' => 'sms_provider', 'value' => json_encode(''), 'type' => 'template'],
            ['key' => 'sms_from_number', 'value' => json_encode(''), 'type' => 'template'],
            
            // Security & Privacy
            ['key' => 'require_customer_verification', 'value' => json_encode(false), 'type' => 'security'],
            ['key' => 'gdpr_enabled', 'value' => json_encode(false), 'type' => 'security'],
            ['key' => 'data_retention_days', 'value' => json_encode(365), 'type' => 'security'],
            ['key' => 'allow_customer_data_export', 'value' => json_encode(true), 'type' => 'security'],
            ['key' => 'allow_customer_data_deletion', 'value' => json_encode(true), 'type' => 'security'],
            ['key' => 'session_timeout_minutes', 'value' => json_encode(120), 'type' => 'security'],
            ['key' => 'require_2fa', 'value' => json_encode(false), 'type' => 'security'],
            
            // Localization & Regional
            ['key' => 'timezone', 'value' => json_encode('UTC'), 'type' => 'localization'],
            ['key' => 'date_format', 'value' => json_encode('Y-m-d'), 'type' => 'localization'],
            ['key' => 'time_format', 'value' => json_encode('24h'), 'type' => 'localization'],
            ['key' => 'language', 'value' => json_encode('en'), 'type' => 'localization'],
            ['key' => 'week_start_day', 'value' => json_encode('monday'), 'type' => 'localization'],
            
            // Advanced Features
            ['key' => 'loyalty_program_enabled', 'value' => json_encode(false), 'type' => 'advanced'],
            ['key' => 'points_per_dollar', 'value' => json_encode(1), 'type' => 'advanced'],
            ['key' => 'referral_program_enabled', 'value' => json_encode(false), 'type' => 'advanced'],
            ['key' => 'referral_discount', 'value' => json_encode(0), 'type' => 'advanced'],
            ['key' => 'birthday_discount_enabled', 'value' => json_encode(false), 'type' => 'advanced'],
            ['key' => 'birthday_discount_percentage', 'value' => json_encode(10), 'type' => 'advanced'],
            ['key' => 'maintenance_mode', 'value' => json_encode(false), 'type' => 'advanced'],
            ['key' => 'maintenance_message', 'value' => json_encode('We are currently under maintenance. Please check back soon.'), 'type' => 'advanced'],
        ];

        foreach ($settings as $setting) {
            $setting['created_at'] = now();
            $setting['updated_at'] = now();
            
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = [
            'appointment_buffer_time', 'allow_overlapping_bookings', 'require_deposit', 'deposit_amount',
            'deposit_type', 'cancellation_policy', 'cancellation_hours', 'no_show_fee',
            'auto_confirm_bookings', 'send_booking_confirmation', 'send_reminder_sms', 'reminder_hours_before',
            'notify_new_booking', 'notify_cancellation', 'notify_low_stock', 'notify_new_customer',
            'notification_email', 'notification_channels', 'daily_summary_enabled', 'daily_summary_time',
            'tax_enabled', 'tax_rate', 'tax_name', 'tax_number', 'currency_code', 'currency_symbol',
            'payment_methods', 'accept_cash', 'accept_card', 'accept_online', 'stripe_enabled',
            'paypal_enabled', 'tip_enabled', 'default_tip_percentage',
            'email_signature', 'booking_confirmation_template', 'booking_reminder_template',
            'cancellation_template', 'welcome_email_template', 'sms_provider', 'sms_from_number',
            'require_customer_verification', 'gdpr_enabled', 'data_retention_days',
            'allow_customer_data_export', 'allow_customer_data_deletion', 'session_timeout_minutes', 'require_2fa',
            'timezone', 'date_format', 'time_format', 'language', 'week_start_day',
            'loyalty_program_enabled', 'points_per_dollar', 'referral_program_enabled', 'referral_discount',
            'birthday_discount_enabled', 'birthday_discount_percentage', 'maintenance_mode', 'maintenance_message',
        ];

        DB::table('settings')->whereIn('key', $keys)->delete();
    }
};
