<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Traits\HandlesSalonSettings;

class SettingsController extends Controller
{
    use HandlesSalonSettings;
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->middleware(['auth', 'verified']);
        // Add role middleware for specific methods
        $this->middleware('role:super_admin')->only(['index', 'update']);
        $this->middleware('role:salon_admin')->only(['salonIndex', 'salonUpdate']);
        $this->settingsService = $settingsService;
    }

    public function index()
    {
        $settings = $this->settingsService->getAll();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            // General
            'logo' => 'nullable|image|max:2048',
            'theme_color' => 'required|in:pink,blue,purple,green,barber',
            'business_name' => 'required|string|max:255',
            'business_email' => 'required|email|max:255',
            'business_phone' => 'nullable|string|max:20',
            'business_address' => 'nullable|string|max:255',
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
            'default_guest_status' => 'required_if:guest_booking_enabled,1|in:pending,confirmed',
            'working_hours_start' => 'required_if:guest_booking_enabled,1|date_format:H:i',
            'working_hours_end' => 'required_if:guest_booking_enabled,1|date_format:H:i',
            'advance_booking_days' => 'required_if:guest_booking_enabled,1|integer|min:1|max:365',
            'slot_duration' => 'required_if:guest_booking_enabled,1|integer|min:15|max:120',

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
            'notification_email' => 'nullable|email',
            'daily_summary_enabled' => 'nullable|boolean',
            'daily_summary_time' => 'nullable|date_format:H:i',

            // Payments & Tax
            'tax_enabled' => 'nullable|boolean',
            'tax_name' => 'nullable|string|max:50',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_number' => 'nullable|string|max:50',
            'refund_fee' => 'nullable|numeric|min:0',
            'refund_fee_type' => 'nullable|in:fixed,percentage',
            'accept_cash' => 'nullable|boolean',
            'accept_card' => 'nullable|boolean',
            'accept_online' => 'nullable|boolean',
            'currency_code' => 'required|string|size:3',
            'currency_symbol' => 'required|string|max:5',
            'tip_enabled' => 'nullable|boolean',
            'default_tip_percentage' => 'nullable|integer|min:0|max:100',

            // Localization
            'timezone' => 'required|string',
            'language' => 'required|string|size:2',
            'date_format' => 'required|string',
            'time_format' => 'required|in:12h,24h',
            'week_start_day' => 'required|in:monday,sunday,saturday',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $oldLogo = $this->settingsService->get('logo');
            if ($oldLogo && $oldLogo !== 'default-logo.png' && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        // Handle boolean fields explicitly (checkboxes don't send value if unchecked)
        $booleanFields = [
            'allow_overlapping_bookings',
            'auto_confirm_bookings',
            'guest_booking_enabled',
            'notify_new_booking',
            'notify_cancellation',
            'notify_low_stock',
            'notify_new_customer',
            'tip_enabled',
            'enable_sms'
        ];

        foreach ($booleanFields as $field) {
            $validated[$field] = $request->has($field);
        }

        // Save all settings
        foreach ($validated as $key => $value) {
            $this->settingsService->set($key, $value);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    public function salonIndex()
    {
        $user = Auth::user();
        $settings = $this->settingsService->getAll($user->salon_id);
        return view('admin.settings.index', compact('settings'));
    }

    public function salonUpdate(Request $request)
    {
        $salon = Auth::user()->salon;

        try {
            $this->performSalonUpdate($request, $salon);

            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');

            return back()->with('success', 'Salon settings updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update salon settings: ' . $e->getMessage());
        }
    }

}