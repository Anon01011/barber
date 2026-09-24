<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Salon;
use App\Models\User;

trait HandlesSalonSettings
{
    /**
     * Get common validation rules for salon settings.
     */
    protected function getSalonSettingsRules($salonId)
    {
        return [
            'logo' => 'nullable|image|max:2048',
            'theme_color' => 'nullable|in:pink,blue,purple,green,barber',
            'business_name' => 'required|string|max:255',
            'business_email' => 'required|email|max:255|unique:salons,email,' . $salonId,
            'business_phone' => 'nullable|string|max:20',
            'business_address' => 'nullable|string|max:500',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',

            // Appointments
            'appointment_buffer_time' => 'nullable|integer|min:0',
            'allow_overlapping_bookings' => 'nullable|boolean',
            'auto_confirm_bookings' => 'nullable|boolean',
            'no_show_fee' => 'nullable|numeric|min:0',
            'cancellation_policy' => 'nullable|string',

            // Guest Booking
            'guest_booking_enabled' => 'nullable|boolean',
            'default_guest_status' => 'nullable|in:pending,confirmed',
            'working_hours_start' => 'nullable|date_format:H:i',
            'working_hours_end' => 'nullable|date_format:H:i',
            'advance_booking_days' => 'nullable|integer|min:1|max:365',
            'slot_duration' => 'nullable|integer|min:5|max:120',

            // Notifications
            'notify_new_booking' => 'nullable|boolean',
            'notify_cancellation' => 'nullable|boolean',
            'notify_low_stock' => 'nullable|boolean',
            'notify_new_customer' => 'nullable|boolean',
            'enable_sms' => 'nullable|boolean',
            'sms_gateway' => 'nullable|string|in:twilio,vonage,messagebird,custom',
            // Twilio
            'twilio_sid' => 'nullable|string|max:255',
            'twilio_token' => 'nullable|string|max:255',
            'twilio_from' => 'nullable|string|max:255',
            // Vonage
            'vonage_api_key' => 'nullable|string|max:255',
            'vonage_api_secret' => 'nullable|string|max:255',
            'vonage_from' => 'nullable|string|max:255',
            // MessageBird
            'messagebird_access_key' => 'nullable|string|max:255',
            'messagebird_originator' => 'nullable|string|max:255',
            // Custom
            'custom_sms_url' => 'nullable|url|max:255',
            'custom_sms_method' => 'nullable|string|in:GET,POST',
            'custom_sms_headers' => 'nullable|string',
            'custom_sms_payload' => 'nullable|string',
            'custom_sms_to_key' => 'nullable|string|max:50',
            'custom_sms_message_key' => 'nullable|string|max:50',

            // WhatsApp
            'enable_whatsapp' => 'nullable|boolean',
            'whatsapp_gateway' => 'nullable|string|in:twilio,custom',
            'whatsapp_twilio_sid' => 'nullable|string|max:255',
            'whatsapp_twilio_token' => 'nullable|string|max:255',
            'whatsapp_twilio_from' => 'nullable|string|max:255',
            'whatsapp_custom_url' => 'nullable|url|max:255',
            'whatsapp_custom_method' => 'nullable|string|in:GET,POST',
            'whatsapp_custom_headers' => 'nullable|string',
            'whatsapp_custom_payload' => 'nullable|string',
            'whatsapp_custom_to_key' => 'nullable|string|max:50',
            'whatsapp_custom_message_key' => 'nullable|string|max:50',
            'notification_email' => 'nullable|email',
            'daily_summary_enabled' => 'nullable|boolean',
            'daily_summary_time' => 'nullable|date_format:H:i',

            // Mail Configuration
            'mail_driver' => 'nullable|in:smtp,sendmail,log',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|in:tls,ssl',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',

            // Payments & Tax
            'tax_enabled' => 'nullable|boolean',
            'tax_name' => 'nullable|string|max:50',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_number' => 'nullable|string|max:50',
            'tax_enabled_pos' => 'nullable|boolean',
            // 'tax_enabled_services' removed per user request
            'refund_fee' => 'nullable|numeric|min:0',
            'refund_fee_type' => 'nullable|in:fixed,percentage',
            'accept_cash' => 'nullable|boolean',
            'accept_card' => 'nullable|boolean',
            'accept_online' => 'nullable|boolean',
            'currency_code' => 'required|string|max:10',
            'custom_currency_code' => 'nullable|string|max:3',
            'currency_symbol' => 'required|string|max:5',
            'tip_enabled' => 'nullable|boolean',
            'default_tip_percentage' => 'nullable|integer|min:0|max:100',
            'pos_receipt_arabic' => 'nullable|boolean',
            'pos_receipt_arabic_button' => 'nullable|boolean',

            // Interface Settings
            'enable_language_toggle' => 'nullable|boolean',

            // Localization
            'timezone' => 'required|string',
            'language' => 'required|string|max:5',
            'date_format' => 'required|string',
            'time_format' => 'required|in:12h,24h',
            'week_start_day' => 'required|in:monday,sunday,saturday',

            // Privacy & Security
            'customer_data_masking_enabled' => 'nullable|boolean',
            'mask_customer_phone' => 'nullable|boolean',
            'mask_customer_email' => 'nullable|boolean',
            'unmasked_data_users' => 'nullable|array',
            'unmasked_data_users.*' => 'nullable|integer',

            // AI & Automation Settings
            'enable_ai_insights' => 'nullable|boolean',
            'enable_ai_churn_auto_marketing' => 'nullable|boolean',
            'ai_churn_discount_percentage' => 'nullable|numeric|min:5|max:50',
            'enable_ai_inventory_alerts' => 'nullable|boolean',
            'enable_ai_dynamic_yield_pricing' => 'nullable|boolean',
            'enable_ai_floating_widget' => 'nullable|boolean',
        ];
    }

    /**
     * Update salon settings and salon model.
     */
    protected function performSalonUpdate(Request $request, Salon $salon)
    {
        $salonId = $salon->id;
        $rules = $this->getSalonSettingsRules($salonId);

        // Custom validation for unmasked_data_users to prevent security leak
        $request->validate($rules);

        $validated = $request->all();

        DB::beginTransaction();
        try {
            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo
                if ($salon->logo && Storage::disk('public')->exists($salon->logo)) {
                    Storage::disk('public')->delete($salon->logo);
                }
                $logoPath = $request->file('logo')->store('salon-logos', 'public');
                $salon->update(['logo' => $logoPath]);
                $this->settingsService->set('logo', $logoPath, $salonId);
            }


            // Boolean fields handling
            $booleanFields = [
                'allow_overlapping_bookings',
                'auto_confirm_bookings',
                'guest_booking_enabled',
                'notify_new_booking',
                'notify_cancellation',
                'notify_low_stock',
                'notify_new_customer',
                'enable_sms',
                'enable_whatsapp',
                'daily_summary_enabled',
                'tax_enabled',
                'tax_enabled_pos',
                'accept_cash',
                'accept_card',
                'accept_online',
                'tip_enabled',
                'customer_data_masking_enabled',
                'mask_customer_phone',
                'mask_customer_email',
                'pos_receipt_arabic',
                'pos_receipt_arabic_button',
                'enable_language_toggle',
                'enable_ai_insights',
                'enable_ai_churn_auto_marketing',
                'enable_ai_inventory_alerts',
                'enable_ai_dynamic_yield_pricing',
                'enable_ai_floating_widget',
                'enable_ai_visual_consultation',
                'enable_ai_noshow_prevention',
                'enable_ai_marketing_generator',
                'enable_ai_whatsapp_assistant'
            ];

            foreach ($booleanFields as $field) {
                $value = $request->boolean($field);

                // Security Check: Only allow enabling if plan supports the feature
                if ($field === 'enable_sms' && $value && !$salon->canUseFeature('SMS Notifications')) {
                    $value = false;
                }
                if ($field === 'enable_whatsapp' && $value && !$salon->canUseFeature('WhatsApp Notifications')) {
                    $value = false;
                }
                if (in_array($field, ['enable_ai_insights', 'enable_ai_churn_auto_marketing', 'enable_ai_inventory_alerts', 'enable_ai_dynamic_yield_pricing', 'enable_ai_floating_widget', 'enable_ai_visual_consultation', 'enable_ai_noshow_prevention', 'enable_ai_marketing_generator', 'enable_ai_whatsapp_assistant']) && $value && !$salon->canUseFeature('AI Insights & Automation')) {
                    $value = false;
                }

                $this->settingsService->set($field, $value, $salonId);
            }


            // Tax for services is forced to false per user request
            $this->settingsService->set('tax_enabled_services', false, $salonId);

            // Handle unmasked_data_users with security check
            $unmaskedDataUsers = $request->input('unmasked_data_users', []);
            if (!empty($unmaskedDataUsers)) {
                // Security: Verify that all user IDs belong to this salon
                $validUserIds = User::where('salon_id', $salonId)
                    ->whereIn('id', $unmaskedDataUsers)
                    ->pluck('id')
                    ->toArray();
                $this->settingsService->set('unmasked_data_users', json_encode($validUserIds), $salonId);
            } else {
                $this->settingsService->set('unmasked_data_users', json_encode([]), $salonId);
            }

            // Handle Custom Currency persistence fix
            $currencyCode = $request->input('currency_code');
            if ($currencyCode === 'CUSTOM') {
                $customCurrency = $request->input('custom_currency_code');
                $this->settingsService->set('custom_currency_code', $customCurrency, $salonId);
                // Also update the main currency_code in salon model if needed 
                // but usually we keep currency_code as CUSTOM in DB settings
            }

            // Update other fields
            $fieldsToUpdate = [
                'theme_color',
                'business_name',
                'business_email',
                'business_phone',
                'business_address',
                'facebook_url',
                'instagram_url',
                'twitter_url',
                'linkedin_url',
                'appointment_buffer_time',
                'no_show_fee',
                'cancellation_policy',
                'default_guest_status',
                'working_hours_start',
                'working_hours_end',
                'advance_booking_days',
                'slot_duration',
                'sms_gateway',
                'twilio_sid',
                'twilio_token',
                'twilio_from',
                'vonage_api_key',
                'vonage_api_secret',
                'vonage_from',
                'messagebird_access_key',
                'messagebird_originator',
                'custom_sms_url',
                'custom_sms_method',
                'custom_sms_headers',
                'custom_sms_payload',
                'custom_sms_to_key',
                'custom_sms_message_key',
                'notification_email',
                'daily_summary_time',
                'tax_name',
                'tax_rate',
                'tax_number',
                'currency_code',
                'currency_symbol',
                'default_tip_percentage',
                'timezone',
                'language',
                'date_format',
                'time_format',
                'week_start_day',
                'refund_fee',
                'refund_fee_type',

                // WhatsApp configuration
                'whatsapp_gateway',
                'whatsapp_twilio_sid',
                'whatsapp_twilio_token',
                'whatsapp_twilio_from',
                'whatsapp_custom_url',
                'whatsapp_custom_method',
                'whatsapp_custom_headers',
                'whatsapp_custom_payload',
                'whatsapp_custom_to_key',
                'whatsapp_custom_message_key',

                // Mail fields
                'mail_driver',
                'mail_host',
                'mail_port',
                'mail_username',
                'mail_password',
                'mail_encryption',
                'mail_from_address',
                'mail_from_name'
            ];

            $salonUpdates = [];
            foreach ($fieldsToUpdate as $field) {
                if ($request->has($field)) {
                    $value = $request->input($field);
                    $this->settingsService->set($field, $value, $salonId);

                    // Sync Salon Model Core Fields
                    if (in_array($field, ['business_name', 'business_email', 'business_phone', 'business_address', 'timezone', 'currency_code', 'mail_driver', 'mail_host', 'mail_port', 'mail_username', 'mail_encryption', 'mail_from_address', 'mail_from_name', 'refund_fee', 'refund_fee_type'])) {
                        $salonKey = str_replace('business_', '', $field);
                        $salonUpdates[$salonKey] = $value;
                    }

                    // Handle mail password separately to avoid clearing it if empty
                    if ($field === 'mail_password') {
                        if (!empty($value)) {
                            $salonUpdates['mail_password'] = $value;
                        }
                        // If empty, do nothing (keep existing)
                    }
                }
            }

            // Map 'name' from SaaS request to 'business_name' in settings
            if ($request->has('name')) {
                $this->settingsService->set('business_name', $request->input('name'), $salonId);
                $salonUpdates['name'] = $request->input('name');
            }
            if ($request->has('phone')) {
                $this->settingsService->set('business_phone', $request->input('phone'), $salonId);
                $salonUpdates['phone'] = $request->input('phone');
            }
            if ($request->has('email')) {
                $this->settingsService->set('business_email', $request->input('email'), $salonId);
                $salonUpdates['email'] = $request->input('email');
            }
            if ($request->has('address')) {
                $this->settingsService->set('business_address', $request->input('address'), $salonId);
                $salonUpdates['address'] = $request->input('address');
            }

            if (!empty($salonUpdates)) {
                $salon->update($salonUpdates);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('HandlesSalonSettings: Update failed', [
                'salon_id' => $salonId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
