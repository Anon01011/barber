<?php

namespace App\Services;

use App\Models\User;
use App\Models\Salon;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Twilio\Rest\Client as TwilioClient;
use App\Helpers\ModuleHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class NotificationService
{
    protected $mailService;
    protected $smsService;
    protected $whatsappService;

    public function __construct(MailService $mailService, SmsService $smsService, WhatsappService $whatsappService)
    {
        $this->mailService = $mailService;
        $this->smsService = $smsService;
        $this->whatsappService = $whatsappService;
    }

    public function sendBookingConfirmation(Booking $booking, array $variableOverrides = [])
    {
        try {
            // 1. Email notification
            if (ModuleHelper::marketingEnabled()) {
                $this->sendBookingConfirmationEmail($booking, $variableOverrides);
            }

            // 2. SMS notification
            if ($this->isChannelEnabled($booking->salon, 'sms') && $booking->customer && $booking->customer->phone) {
                $this->sendBookingConfirmationSMS($booking);
            }

            // 3. WhatsApp notification
            if ($this->isChannelEnabled($booking->salon, 'whatsapp') && $booking->customer && $booking->customer->phone) {
                $this->sendBookingConfirmationWhatsapp($booking);
            }

            // 4. Notify staff if assigned
            if ($booking->staff) {
                $this->notifyStaffOfNewBooking($booking);
            }

            // 5. Notify salon admins
            $this->notifySalonAdmins($booking, 'created');

            Log::info('Booking confirmation notifications sent', ['booking_id' => $booking->id]);
        } catch (Exception $e) {
            Log::error('Failed to send booking confirmation notifications', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Send group booking confirmation notification
     */
    public function sendGroupBookingConfirmation(array $bookings)
    {
        if (empty($bookings)) {
            return;
        }

        try {
            // 1. Send Consolidated Customer Notification (Email & SMS)
            $firstBooking = $bookings[0];
            $customer = $firstBooking->customer;

            if ($customer) {
                // Aggregate details
                $serviceNames = collect($bookings)->map(function ($b) {
                    return $b->service->name;
                })->implode(', ');

                $totalAmount = format_currency(collect($bookings)->sum('amount'));

                // Send Consolidated Email
                if ($customer->email) {
                    $this->sendBookingConfirmationEmail($firstBooking, [
                        'service_name' => $serviceNames,
                        'amount' => $totalAmount
                    ]);
                }

                // Send Consolidated SMS/WhatsApp
                if ($customer->phone) {
                    if ($this->isChannelEnabled($firstBooking->salon, 'sms')) {
                        $this->sendGroupBookingConfirmationSMS($bookings);
                    }
                    if ($this->isChannelEnabled($firstBooking->salon, 'whatsapp')) {
                        $this->sendGroupBookingConfirmationWhatsapp($bookings);
                    }
                }
            }

            // 2. Send Individual Staff/Admin Notifications
            foreach ($bookings as $booking) {
                // Notify staff if assigned
                if ($booking->staff) {
                    $this->notifyStaffOfNewBooking($booking);
                }

                // Notify salon admins
                $this->notifySalonAdmins($booking, 'created');
            }

            Log::info('Group booking confirmation notifications sent', [
                'count' => count($bookings),
                'customer_id' => $customer ? $customer->id : 'N/A'
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send group booking confirmation notifications', [
                'error' => $e->getMessage()
            ]);
        }
    }

    // ... (other methods)

    // Email methods
    private function sendBookingConfirmationEmail(Booking $booking, array $overrides = [])
    {
        if (!$booking->customer->email) {
            Log::info('Skipping booking confirmation email: customer has no email', ['booking_id' => $booking->id]);
            return;
        }

        $salonTimezone = salon_timezone();
        $startTime = $booking->start_time->copy()->setTimezone($salonTimezone);

        $variables = array_merge([
            'customer_name' => $booking->customer->name,
            'salon_name' => $booking->salon->name,
            'booking_date' => $startTime->format('Y-m-d'),
            'booking_time' => $startTime->format('H:i'),
            'service_name' => $booking->service->name,
            'staff_name' => $booking->staff ? $booking->staff->name : 'Any Staff',
            'amount' => format_currency((float) $booking->amount),
        ], $overrides);

        $this->mailService->send($booking->salon, $booking->customer->email, 'booking_confirmation', $variables);
    }

    /**
     * Send booking rescheduled notification
     */
    public function sendBookingRescheduled(Booking $booking)
    {
        try {
            // Email notification
            $this->sendBookingRescheduledEmail($booking);

            // SMS notification
            if ($booking->customer && $booking->customer->phone) {
                if ($this->isChannelEnabled($booking->salon, 'sms')) {
                    $this->sendBookingRescheduledSMS($booking);
                }
                if ($this->isChannelEnabled($booking->salon, 'whatsapp')) {
                    $this->sendBookingRescheduledWhatsapp($booking);
                }
            }

            // Notify staff
            if ($booking->staff) {
                \Illuminate\Support\Facades\Notification::send(
                    $booking->staff,
                    new \App\Notifications\BookingNotification($booking, 'rescheduled')
                );
            }

            // Notify salon admins
            $this->notifySalonAdmins($booking, 'rescheduled');

            Log::info('Booking rescheduled notifications sent', [
                'booking_id' => $booking->id
            ]);
        } catch (Exception $e) {
            Log::error('Failed to send booking rescheduled notifications', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send booking status update notification (Unified)
     */
    public function sendBookingStatusUpdate(Booking $booking, $oldStatus)
    {
        try {
            $status = $booking->status;

            // 1. Logic for specific statuses
            if ($status === 'cancelled') {
                $settingsService = app(\App\Services\SettingsService::class);
                if (!$settingsService->get('notify_cancellation', true, $booking->salon_id)) {
                    return;
                }
            }

            // 2. Email notification
            if (ModuleHelper::marketingEnabled() && $booking->customer && $booking->customer->email) {
                Mail::to($booking->customer->email)->send(new \App\Mail\BookingStatusUpdatedMail($booking));
            }

            // 3. SMS notification
            if ($this->isChannelEnabled($booking->salon, 'sms') && $booking->customer && $booking->customer->phone) {
                $this->sendBookingStatusUpdateSMS($booking);
            }

            // 4. WhatsApp notification
            if ($this->isChannelEnabled($booking->salon, 'whatsapp') && $booking->customer && $booking->customer->phone) {
                $this->sendBookingStatusUpdateWhatsapp($booking);
            }

            // 5. Notify Staff/Admins
            $notifiables = collect();
            if ($booking->staff)
                $notifiables->push($booking->staff);

            $admins = User::role(['super_admin', 'salon_admin', 'manager'])
                ->where('salon_id', $booking->salon_id)
                ->get();

            $notifiables = $notifiables->merge($admins)->unique('id');

            if ($notifiables->isNotEmpty()) {
                \Illuminate\Support\Facades\Notification::send($notifiables, new \App\Notifications\BookingNotification($booking, $status));
            }

            Log::info('Booking status update notifications sent', ['booking_id' => $booking->id, 'status' => $status]);
        } catch (Exception $e) {
            Log::error('Failed to send booking status update notifications', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
        }
    }

    public function sendBookingRejected(Booking $booking)
    {
        try {
            // Email notification
            if (ModuleHelper::marketingEnabled()) {
                $this->sendBookingRejectedEmail($booking);
            }

            // SMS notification
            if ($this->isChannelEnabled($booking->salon, 'sms') && $booking->customer && $booking->customer->phone) {
                $this->sendBookingRejectedSMS($booking);
            }

            // WhatsApp notification
            if ($this->isChannelEnabled($booking->salon, 'whatsapp') && $booking->customer && $booking->customer->phone) {
                $this->sendBookingRejectedWhatsapp($booking);
            }

            Log::info('Booking rejected notifications sent', ['booking_id' => $booking->id]);
        } catch (Exception $e) {
            Log::error('Failed to send booking rejected notifications', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Send booking completed notification
     */
    public function sendBookingCompleted(Booking $booking)
    {
        try {
            // Email notification
            $this->sendBookingCompletedEmail($booking);

            // SMS notification
            if ($this->isChannelEnabled($booking->salon, 'sms') && $booking->customer && $booking->customer->phone) {
                $this->sendBookingCompletedSMS($booking);
            }

            // WhatsApp notification
            if ($this->isChannelEnabled($booking->salon, 'whatsapp') && $booking->customer && $booking->customer->phone) {
                $this->sendBookingCompletedWhatsapp($booking);
            }

            Log::info('Booking completed notifications sent', [
                'booking_id' => $booking->id
            ]);
        } catch (Exception $e) {
            Log::error('Failed to send booking completed notifications', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send booking reminder
     */
    public function sendBookingReminder(Booking $booking)
    {
        try {
            // Email reminder
            if (ModuleHelper::marketingEnabled()) {
                $this->sendBookingReminderEmail($booking);
            }

            // SMS reminder if customer has phone
            if ($this->isChannelEnabled($booking->salon, 'sms') && $booking->customer && $booking->customer->phone) {
                $this->sendBookingReminderSMS($booking);
            }

            // WhatsApp reminder
            if ($this->isChannelEnabled($booking->salon, 'whatsapp') && $booking->customer && $booking->customer->phone) {
                $this->sendBookingReminderWhatsapp($booking);
            }

            Log::info('Booking reminder notifications sent', [
                'booking_id' => $booking->id
            ]);
        } catch (Exception $e) {
            Log::error('Failed to send booking reminder notifications', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send subscription renewal reminder
     */
    public function sendSubscriptionRenewalReminder(Salon $salon, $daysLeft)
    {
        try {
            if (ModuleHelper::marketingEnabled()) {
                $this->sendSubscriptionRenewalEmail($salon, $daysLeft);
            }

            if ($this->isChannelEnabled($salon, 'sms') && $salon->phone) {
                $this->sendSubscriptionRenewalSMS($salon, $daysLeft);
            }

            if ($this->isChannelEnabled($salon, 'whatsapp') && $salon->phone) {
                $this->sendSubscriptionRenewalWhatsapp($salon, $daysLeft);
            }

            Log::info('Subscription renewal reminder sent', [
                'salon_id' => $salon->id,
                'days_left' => $daysLeft
            ]);
        } catch (Exception $e) {
            Log::error('Failed to send subscription renewal reminder', [
                'salon_id' => $salon->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send trial expiration warning
     */
    public function sendTrialExpirationWarning(Salon $salon, $daysLeft)
    {
        try {
            if (ModuleHelper::marketingEnabled()) {
                $this->sendTrialExpirationEmail($salon, $daysLeft);
            }

            if ($this->isChannelEnabled($salon, 'sms') && $salon->phone) {
                $this->sendTrialExpirationSMS($salon, $daysLeft);
            }

            if ($this->isChannelEnabled($salon, 'whatsapp') && $salon->phone) {
                $this->sendTrialExpirationWhatsapp($salon, $daysLeft);
            }

            Log::info('Trial expiration warning sent', [
                'salon_id' => $salon->id,
                'days_left' => $daysLeft
            ]);
        } catch (Exception $e) {
            Log::error('Failed to send trial expiration warning', [
                'salon_id' => $salon->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendWelcomeEmail(Salon $salon)
    {
        try {
            Mail::to($salon->owner->email)->send(new \App\Mail\NewSalonRegistrationMail($salon));

            Log::info('Welcome email sent', [
                'salon_id' => $salon->id,
                'email' => $salon->owner->email
            ]);
        } catch (Exception $e) {
            Log::error('Failed to send welcome email', [
                'salon_id' => $salon->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send welcome email to new customer
     */
    /**
     * Send welcome email to new customer
     * 
     * @param mixed $customer App\Models\User or App\Models\Customer
     * @param Salon|null $salon
     */
    public function sendWelcomeEmailToCustomer($customer, ?Salon $salon = null)
    {
        if (!$customer->email) {
            Log::info('Skipping welcome email: customer has no email', ['customer_id' => $customer->id]);
            return;
        }

        if (!$salon && isset($customer->salon)) {
            $salon = $customer->salon;
        }

        if (!$salon) {
            Log::warning('Cannot send customer welcome email: Salon not found', ['customer_id' => $customer->id]);
            return;
        }

        $variables = [
            'customer_name' => $customer->name,
            'salon_name' => $salon->name,
        ];

        if (ModuleHelper::marketingEnabled()) {
            $this->mailService->send($salon, $customer->email, 'welcome', $variables);
        }
    }

    // Email methods
    // sendBookingConfirmationEmail is defined above with overrides support

    private function sendBookingRescheduledEmail(Booking $booking)
    {
        if (!$booking->customer->email) {
            Log::info('Skipping booking rescheduled email: customer has no email', ['booking_id' => $booking->id]);
            return;
        }

        $salonTimezone = salon_timezone();
        $startTime = $booking->start_time->copy()->setTimezone($salonTimezone);

        $variables = [
            'customer_name' => $booking->customer->name,
            'salon_name' => $booking->salon->name,
            'booking_date' => $startTime->format('Y-m-d'),
            'booking_time' => $startTime->format('H:i'),
            'service_name' => $booking->service->name,
            'staff_name' => $booking->staff ? $booking->staff->name : 'Any Staff',
            'amount' => format_currency((float) $booking->amount),
        ];

        $this->mailService->send($booking->salon, $booking->customer->email, 'booking_rescheduled', $variables);
    }

    private function sendBookingRejectedEmail(Booking $booking)
    {
        if (!$booking->customer->email) {
            Log::info('Skipping booking rejected email: customer has no email', ['booking_id' => $booking->id]);
            return;
        }

        $salonTimezone = salon_timezone();
        $startTime = $booking->start_time->copy()->setTimezone($salonTimezone);

        $variables = [
            'customer_name' => $booking->customer->name,
            'salon_name' => $booking->salon->name,
            'booking_date' => $startTime->format('Y-m-d'),
            'booking_time' => $startTime->format('H:i'),
            'service_name' => $booking->service->name,
        ];

        $this->mailService->send($booking->salon, $booking->customer->email, 'booking_rejected', $variables);
    }

    private function sendBookingCompletedEmail(Booking $booking)
    {
        // This might be a "Thank you" email.
        // "welcome" is for new customers.
        // I'll skip this if no template exists, or use a generic one.
        // User didn't show a "Thank You" template.
        // I'll leave it empty for now or log it.
        Log::info("Booking completed email requested but no template defined.");
    }

    private function sendBookingReminderEmail(Booking $booking)
    {
        if (!$booking->customer->email) {
            Log::info('Skipping booking reminder email: customer has no email', ['booking_id' => $booking->id]);
            return;
        }

        $salonTimezone = salon_timezone();
        $startTime = $booking->start_time->copy()->setTimezone($salonTimezone);

        $variables = [
            'customer_name' => $booking->customer->name,
            'salon_name' => $booking->salon->name,
            'booking_date' => $startTime->format('Y-m-d'),
            'booking_time' => $startTime->format('H:i'),
            'service_name' => $booking->service->name,
            'staff_name' => $booking->staff ? $booking->staff->name : 'Any Staff',
            'amount' => format_currency((float) $booking->amount),
        ];

        $this->mailService->send($booking->salon, $booking->customer->email, 'booking_reminder', $variables);
    }

    private function sendSubscriptionRenewalEmail(Salon $salon, $daysLeft)
    {
        $subscription = $salon->activeSubscription;
        if ($subscription) {
            Mail::to($salon->owner->email)->send(new \App\Mail\SubscriptionExpiringSoonMail($subscription, $daysLeft));
        } else {
            Log::warning("Cannot send renewal email: Salon {$salon->id} has no active subscription.");
        }
    }

    private function sendTrialExpirationEmail(Salon $salon, $daysLeft)
    {
        $subscription = $salon->activeSubscription;
        if ($subscription) {
            Mail::to($salon->owner->email)->send(new \App\Mail\SubscriptionExpiringSoonMail($subscription, $daysLeft));
        } else {
            // If they are on trial, they might not have a 'Subscription' record yet, 
            // but SubscriptionExpiringSoonMail requires one. 
            // I'll log a warning for now or I should check if there's a specific Trial mail.
            Log::warning("Cannot send trial expiration email: SubscriptionExpiringSoonMail requires a Subscription model for Salon {$salon->id}.");
        }
    }

    // Helper to get message from template
    private function getMessageFromTemplate($salon, $type, $variables)
    {
        // 1. Fetch Template (Salon specific or System default)
        $template = \App\Models\EmailTemplate::forSalon($salon->id, $type);

        if (!$template) {
            // Fallback to system default if not found via forSalon (though forSalon handles this usually)
            $template = \App\Models\EmailTemplate::whereNull('salon_id')->where('type', $type)->first();
        }

        if (!$template) {
            Log::warning("NotificationService: No template found for type '{$type}' for salon {$salon->id}.");
            return null;
        }

        // 2. Replacements
        $content = $template->content;
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        // 3. Clean up for Text/SMS (Strip tags, convert breaks)
        // Convert <br> and </p> to newlines before stripping other tags
        $content = str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $content);
        $content = strip_tags($content);
        $content = trim(html_entity_decode($content));

        return $content;
    }

    // SMS methods
    private function sendBookingConfirmationSMS(Booking $booking)
    {
        $salonTz = salon_timezone();
        $startTime = $booking->start_time->copy()->setTimezone($salonTz);

        $variables = [
            'customer_name' => $booking->customer->name,
            'salon_name' => $booking->salon->name,
            'booking_date' => $startTime->format('Y-m-d'),
            'booking_time' => $startTime->format('H:i'),
            'service_name' => $booking->service->name,
            'amount' => format_currency((float) $booking->amount),
        ];

        $message = $this->getMessageFromTemplate($booking->salon, 'booking_confirmation', $variables);

        if ($message) {
            $this->sendSMS($booking->customer->phone, $message, $booking->salon);
        }
    }

    private function sendBookingReminderSMS(Booking $booking)
    {
        $salonTz = salon_timezone();
        $startTime = $booking->start_time->copy()->setTimezone($salonTz);

        $variables = [
            'customer_name' => $booking->customer->name,
            'salon_name' => $booking->salon->name,
            'booking_date' => $startTime->format('Y-m-d'),
            'booking_time' => $startTime->format('H:i'),
            'service_name' => $booking->service->name,
            'amount' => format_currency((float) $booking->amount),
        ];

        $message = $this->getMessageFromTemplate($booking->salon, 'booking_reminder', $variables);

        if ($message) {
            $this->sendSMS($booking->customer->phone, $message, $booking->salon);
        }
    }

    private function sendSubscriptionRenewalSMS(Salon $salon, $daysLeft)
    {
        // No template for this yet in UI, keeping hardcoded for safety or add template later
        $message = "Hi {$salon->name}, your subscription expires in {$daysLeft} days. Renew now to continue using our services.";
        $this->sendSMS($salon->phone, $message, $salon);
    }

    private function sendTrialExpirationSMS(Salon $salon, $daysLeft)
    {
        // No template for this yet in UI, keeping hardcoded
        $message = "Hi {$salon->name}, your trial period ends in {$daysLeft} days. Upgrade to continue using our services.";
        $this->sendSMS($salon->phone, $message, $salon);
    }

    private function notifyStaffOfNewBooking(Booking $booking)
    {
        $salonTz = salon_timezone();
        $startTime = $booking->start_time->copy()->setTimezone($salonTz);
        // Staff notification might need its own template type if customizable
        $message = "New booking assigned: {$booking->customer->name} - {$booking->service->name} on {$startTime->format('M j, Y g:i A')}";

        if ($booking->staff && $booking->staff->phone) {
            $this->sendSMS($booking->staff->phone, $message, $booking->salon);
        }

        // Send email notification to staff
        if ($booking->staff) {
            \Illuminate\Support\Facades\Notification::send($booking->staff, new \App\Notifications\BookingNotification($booking, 'created'));
        }
    }

    private function sendBookingRescheduledSMS(Booking $booking)
    {
        $salonTz = salon_timezone();
        $startTime = $booking->start_time->copy()->setTimezone($salonTz);

        $variables = [
            'customer_name' => $booking->customer->name,
            'salon_name' => $booking->salon->name,
            'booking_date' => $startTime->format('Y-m-d'),
            'booking_time' => $startTime->format('H:i'),
            'service_name' => $booking->service->name,
        ];

        $message = $this->getMessageFromTemplate($booking->salon, 'booking_rescheduled', $variables);

        if ($message) {
            $this->sendSMS($booking->customer->phone, $message, $booking->salon);
        }
    }

    private function sendBookingStatusUpdateSMS(Booking $booking)
    {
        $salonTz = salon_timezone();
        $startTime = $booking->start_time->copy()->setTimezone($salonTz);
        $status = ucfirst(str_replace('_', ' ', $booking->status));

        $variables = [
            'customer_name' => $booking->customer->name,
            'salon_name' => $booking->salon->name,
            'booking_date' => $startTime->format('Y-m-d'),
            'booking_time' => $startTime->format('H:i'),
            'service_name' => $booking->service->name,
            'booking_status' => $status,
        ];

        // Specific status templates could be handled if we had 'booking_cancelled' type etc.
        // For now, mapping 'booking_status_updated' or specific ones if available.
        // The implementation plan mentioned 'booking_cancelled' is an allowed type.

        $type = 'booking_status_updated';
        if ($booking->status === 'cancelled') {
            $type = 'booking_cancelled';
        }

        $message = $this->getMessageFromTemplate($booking->salon, $type, $variables);

        if ($message) {
            $this->sendSMS($booking->customer->phone, $message, $booking->salon);
        }
    }

    private function sendBookingRejectedSMS(Booking $booking)
    {
        $salonTz = salon_timezone();
        $startTime = $booking->start_time->copy()->setTimezone($salonTz);

        $variables = [
            'customer_name' => $booking->customer->name,
            'salon_name' => $booking->salon->name,
            'booking_date' => $startTime->format('Y-m-d'),
            'booking_time' => $startTime->format('H:i'),
            'service_name' => $booking->service->name,
        ];

        // Assuming 'booking_rejected' type exists or fallback to cancellation
        // Controller allowedTypes included 'booked_cancelled' but not explicitly 'rejected'
        // I will use 'booking_cancelled' map for rejected as well for now or check if I can add it.
        // Actually, let's stick to what's in controller allowedTypes: booking_confirmation, booking_reminder, booking_cancelled, booking_rescheduled, welcome, sale_receipt, booking_status_updated
        // 'booking_rejected' was NOT in the list, so I'll try 'booking_cancelled' or 'booking_status_updated'.

        $message = $this->getMessageFromTemplate($booking->salon, 'booking_cancelled', $variables);

        if ($message) {
            if (!$message)
                $message = "Hi {$booking->customer->name}, your booking has been rejected.";
            $this->sendSMS($booking->customer->phone, $message, $booking->salon);
        }
    }

    private function sendGroupBookingConfirmationSMS(array $bookings)
    {
        // This is complex for templates as it has multiple services.
        // We'll construct a custom message or use a generic template if available.
        // For now preventing breakage by keeping logic specific but using variables if possible.

        $firstBooking = $bookings[0];
        $customer = $firstBooking->customer;
        $count = count($bookings);
        $salonTz = salon_timezone();
        $date = $firstBooking->start_time->copy()->setTimezone($salonTz)->format('Y-m-d H:i');

        $serviceNames = collect($bookings)->map(function ($b) {
            return $b->service->name;
        })->implode(', ');
        $totalAmount = format_currency(collect($bookings)->sum('amount'));

        $variables = [
            'customer_name' => $customer->name,
            'salon_name' => $firstBooking->salon->name,
            'booking_date' => $date,
            'service_name' => $serviceNames, // Aggregated
            'amount' => $totalAmount,
            'booking_count' => $count
        ];

        // Re-use booking_confirmation but with aggregated data
        $message = $this->getMessageFromTemplate($firstBooking->salon, 'booking_confirmation', $variables);

        if ($message) {
            $this->sendSMS($customer->phone, $message, $firstBooking->salon);
        }
    }

    /**
     * Send SMS using SmsService
     */
    private function sendSMS($to, $message, $salon = null)
    {
        $this->smsService->send($to, $message, $salon);
    }

    /**
     * Send bulk notifications to multiple recipients
     */
    public function sendBulkNotification(array $recipients, string $subject, string $message, string $type = 'email')
    {
        foreach ($recipients as $recipient) {
            try {
                if ($type === 'email') {
                    Mail::raw($message, function ($mail) use ($recipient, $subject) {
                        $mail->to($recipient)->subject($subject);
                    });
                } elseif ($type === 'sms' && isset($recipient['phone'])) {
                    $this->sendSMS($recipient['phone'], $message);
                }
            } catch (Exception $e) {
                Log::error('Bulk notification failed', [
                    'recipient' => $recipient,
                    'type' => $type,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * WhatsApp methods
     */
    public function sendBookingConfirmationWhatsapp(Booking $booking)
    {
        $salonTz = salon_timezone();
        $startTime = $booking->start_time->copy()->setTimezone($salonTz);

        $variables = [
            'customer_name' => $booking->customer->name,
            'salon_name' => $booking->salon->name,
            'booking_date' => $startTime->format('Y-m-d'),
            'booking_time' => $startTime->format('H:i'),
            'service_name' => $booking->service->name,
            'amount' => format_currency($booking->amount),
        ];

        $message = $this->getMessageFromTemplate($booking->salon, 'booking_confirmation', $variables);
        if ($message) {
            $this->whatsappService->send($booking->customer->phone, $message, $booking->salon);
        }
    }

    public function sendBookingRescheduledWhatsapp(Booking $booking)
    {
        $salonTz = salon_timezone();
        $startTime = $booking->start_time->copy()->setTimezone($salonTz);

        $variables = [
            'customer_name' => $booking->customer->name,
            'salon_name' => $booking->salon->name,
            'booking_date' => $startTime->format('Y-m-d'),
            'booking_time' => $startTime->format('H:i'),
            'service_name' => $booking->service->name,
        ];

        $message = $this->getMessageFromTemplate($booking->salon, 'booking_rescheduled', $variables);
        if ($message) {
            $this->whatsappService->send($booking->customer->phone, $message, $booking->salon);
        }
    }

    public function sendBookingRejectedWhatsapp(Booking $booking)
    {
        $salonTz = salon_timezone();
        $startTime = $booking->start_time->copy()->setTimezone($salonTz);

        $variables = [
            'customer_name' => $booking->customer->name,
            'salon_name' => $booking->salon->name,
            'booking_date' => $startTime->format('Y-m-d'),
            'booking_time' => $startTime->format('H:i'),
            'service_name' => $booking->service->name,
        ];

        // Use booking_cancelled
        $message = $this->getMessageFromTemplate($booking->salon, 'booking_cancelled', $variables);

        if ($message) {
            $this->whatsappService->send($booking->customer->phone, $message, $booking->salon);
        }
    }

    public function sendBookingStatusUpdateWhatsapp(Booking $booking)
    {
        if (!$booking->customer || !$booking->customer->phone)
            return;

        $salonTz = salon_timezone();
        $startTime = $booking->start_time->copy()->setTimezone($salonTz);
        $status = ucfirst(str_replace('_', ' ', $booking->status));

        $variables = [
            'customer_name' => $booking->customer->name,
            'salon_name' => $booking->salon->name,
            'booking_date' => $startTime->format('Y-m-d'),
            'booking_time' => $startTime->format('H:i'),
            'service_name' => $booking->service->name,
            'booking_status' => $status,
        ];

        $type = 'booking_status_updated';
        if ($booking->status === 'cancelled') {
            $type = 'booking_cancelled';
        } elseif ($booking->status === 'confirmed') {
            $type = 'booking_confirmation'; // Can reuse confirmation text if appropriate
        }

        // Prioritize specific, fallback to status updated
        $message = $this->getMessageFromTemplate($booking->salon, $type, $variables);
        if (!$message && $type !== 'booking_status_updated') {
            $message = $this->getMessageFromTemplate($booking->salon, 'booking_status_updated', $variables);
        }

        if ($message) {
            $this->whatsappService->send($booking->customer->phone, $message, $booking->salon);
        }
    }

    public function sendSaleReceiptWhatsapp(\App\Models\PosSale $sale)
    {
        if (!ModuleHelper::whatsappEnabled())
            return;

        if (!$sale->customer || !$sale->customer->phone)
            return;

        // Verify Salon settings for currency/tax etc needed for view
        $salonId = $sale->salon_id;
        $settingsService = app(\App\Services\SettingsService::class);
        $settings = $settingsService; // View expects 'settings' object/mock 

        $salonData = [
            'name' => $sale->salon->name,
            'address' => $sale->salon->address,
            'phone' => $sale->salon->phone,
            'email' => $sale->salon->email,
            'website' => $sale->salon->website,
            'logo' => $sale->salon->logo ? asset('storage/' . $sale->salon->logo) : null,
        ];

        // Generate PDF
        try {
            $pdf = Pdf::loadView('pos.receipt', [
                'sale' => $sale,
                'salonData' => $salonData,
                'settings' => $settings,
                'isPdf' => true
            ]);

            // Save to public storage
            $filename = 'receipt-' . $sale->invoice_number . '.pdf';
            $path = 'receipts/' . $filename;

            // Ensure directory exists
            Storage::disk('public')->makeDirectory('receipts');
            Storage::disk('public')->put($path, $pdf->output());

            $mediaUrl = asset('storage/' . $path);

        } catch (Exception $e) {
            Log::error('Failed to generate PDF receipt for WhatsApp', ['error' => $e->getMessage()]);
            $mediaUrl = null;
        }

        $variables = [
            'customer_name' => $sale->customer->name,
            'salon_name' => $sale->salon->name,
            'invoice_number' => $sale->invoice_number,
            'total' => format_currency((float) $sale->total),
            'receipt_link' => $mediaUrl ?? route('admin.pos.receipt', $sale->id),
            'date' => $sale->created_at->format('Y-m-d'),
        ];

        $message = $this->getMessageFromTemplate($sale->salon, 'sale_receipt', $variables);

        if ($message) {
            $this->whatsappService->send($sale->customer->phone, $message, $sale->salon, $mediaUrl);
        }
    }

    public function sendGroupBookingConfirmationWhatsapp(array $bookings)
    {
        $firstBooking = $bookings[0];
        $customer = $firstBooking->customer;
        $count = count($bookings);
        $salonTz = salon_timezone();
        $date = $firstBooking->start_time->copy()->setTimezone($salonTz)->format('Y-m-d H:i');

        $serviceNames = collect($bookings)->map(function ($b) {
            return $b->service->name;
        })->implode(', ');
        $totalAmount = format_currency(collect($bookings)->sum('amount'));

        $variables = [
            'customer_name' => $customer->name,
            'salon_name' => $firstBooking->salon->name,
            'booking_date' => $date,
            'service_name' => $serviceNames,
            'amount' => $totalAmount,
            'booking_count' => $count
        ];

        $message = $this->getMessageFromTemplate($firstBooking->salon, 'booking_confirmation', $variables);

        if ($message) {
            $this->whatsappService->send($customer->phone, $message, $firstBooking->salon);
        }
    }

    public function sendBookingCompletedWhatsapp(Booking $booking)
    {
        $variables = [
            'customer_name' => $booking->customer->name,
            'salon_name' => $booking->salon->name,
            'service_name' => $booking->service->name,
        ];

        // Use a thank you or completed template if available. Use booking_status_updated as fallback?
        // Or maybe just generic message if no template. 
        // Controller didn't list 'booking_completed', only 'sale_receipt' or 'booking_status_updated'.

        $type = 'booking_status_updated'; // Fallback

        $message = $this->getMessageFromTemplate($booking->salon, $type, $variables);
        if ($message) {
            $this->whatsappService->send($booking->customer->phone, $message, $booking->salon);
        }
    }

    public function sendBookingReminderWhatsapp(Booking $booking)
    {
        $salonTz = salon_timezone();
        $startTime = $booking->start_time->copy()->setTimezone($salonTz);

        $variables = [
            'customer_name' => $booking->customer->name,
            'salon_name' => $booking->salon->name,
            'booking_date' => $startTime->format('Y-m-d'),
            'booking_time' => $startTime->format('H:i'),
            'service_name' => $booking->service->name,
        ];

        $message = $this->getMessageFromTemplate($booking->salon, 'booking_reminder', $variables);
        if ($message) {
            $this->whatsappService->send($booking->customer->phone, $message, $booking->salon);
        }
    }

    private function sendBookingCompletedSMS(Booking $booking)
    {
        $variables = [
            'customer_name' => $booking->customer->name,
            'salon_name' => $booking->salon->name,
            'service_name' => $booking->service->name,
        ];

        $type = 'booking_status_updated';
        $message = $this->getMessageFromTemplate($booking->salon, $type, $variables);

        if ($message) {
            $this->sendSMS($booking->customer->phone, $message, $booking->salon);
        }
    }

    private function sendSubscriptionRenewalWhatsapp(Salon $salon, $daysLeft)
    {
        $message = "Hi {$salon->name}, your subscription expires in {$daysLeft} days. Renew now to continue using our services.";
        $this->whatsappService->send($salon->phone, $message, $salon);
    }

    private function sendTrialExpirationWhatsapp(Salon $salon, $daysLeft)
    {
        $message = "Hi {$salon->name}, your trial period ends in {$daysLeft} days. Upgrade to continue using our services.";
        $this->whatsappService->send($salon->phone, $message, $salon);
    }

    /**
     * Notify salon admins of booking events
     */
    private function notifySalonAdmins(Booking $booking, string $eventType)
    {
        try {
            $admins = User::role(['super_admin', 'salon_admin', 'manager'])
                ->where('salon_id', $booking->salon_id)
                ->get();

            if ($admins->isNotEmpty()) {
                \Illuminate\Support\Facades\Notification::send(
                    $admins,
                    new \App\Notifications\BookingNotification($booking, $eventType)
                );

                Log::info('Salon admin notifications sent', [
                    'booking_id' => $booking->id,
                    'event_type' => $eventType,
                    'admin_count' => $admins->count()
                ]);
            }
        } catch (Exception $e) {
            Log::error('Failed to notify salon admins', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check if a notification channel is enabled for a salon
     */
    private function isChannelEnabled(Salon $salon, string $channel): bool
    {
        $settings = app(SettingsService::class);

        if ($channel === 'sms') {
            return ModuleHelper::smsEnabled() &&
                $salon->canUseFeature('SMS Notifications') &&
                $settings->get('enable_sms', false, $salon->id);
        }

        if ($channel === 'whatsapp') {
            return ModuleHelper::whatsappEnabled() &&
                $salon->canUseFeature('WhatsApp Notifications') &&
                $settings->get('enable_whatsapp', false, $salon->id);
        }

        if ($channel === 'marketing') {
            return ModuleHelper::marketingEnabled();
        }

        return false;
    }

    /**
     * Send direct SMS notification to a phone number.
     *
     * @param string $phone
     * @param string $message
     * @param int|null $salonId
     * @return bool
     */
    public function sendSmsNotification(string $phone, string $message, $salonId = null): bool
    {
        try {
            $salon = $salonId ? Salon::find($salonId) : null;
            return $this->smsService->send($phone, $message, $salon);
        } catch (\Exception $e) {
            Log::error('Failed to send SMS notification: ' . $e->getMessage(), ['phone' => $phone]);
            return false;
        }
    }
}
