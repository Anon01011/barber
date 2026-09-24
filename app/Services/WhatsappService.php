<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Send a WhatsApp message using the configured gateway.
     *
     * @param string $to Recipient phone number in E.164 format
     * @param string $message The message body
     * @param \App\Models\Salon|null $salon The salon context
     * @param string|null $mediaUrl Optional URL to a media file (PDF/Image)
     * @return bool True if successful or skipped, False otherwise
     */
    public function send($to, $message, $salon = null, $mediaUrl = null)
    {
        if (!$salon) {
            Log::warning("WhatsApp Service: No salon context provided. Skipping message to {$to}.");
            return false;
        }

        // 1. Plan Check
        if (!$salon->canUseFeature('WhatsApp Notifications')) {
            Log::info("WhatsApp Service: Salon {$salon->id} ({$salon->name}) does not have WhatsApp Notifications in their plan. Skipping message.");
            return true;
        }

        // 2. Global Enabled Check
        if (!$this->settingsService->get('enable_whatsapp', false, $salon->id)) {
            Log::info("WhatsApp Service: WhatsApp is disabled for salon {$salon->id}. Skipping message to {$to}.");
            return true;
        }

        // 3. Get Selected Gateway
        $gateway = $this->settingsService->get('whatsapp_gateway', 'twilio', $salon->id);

        // 4. Brand settings
        $salonName = $salon->name ?? 'Our Salon';
        $fullMessage = "{$salonName}: {$message}";

        switch ($gateway) {
            case 'twilio':
                return $this->sendTwilio($to, $fullMessage, $salon, $mediaUrl);
            case 'custom':
                return $this->sendCustomHttp($to, $fullMessage, $salon, $mediaUrl);
            default:
                Log::error("WhatsApp Service: Unknown gateway '{$gateway}' for salon {$salon->id}.");
                return false;
        }
    }

    protected function sendTwilio($to, $message, $salon, $mediaUrl = null)
    {
        $sid = $this->settingsService->get('whatsapp_twilio_sid', null, $salon->id);
        $token = $this->settingsService->get('whatsapp_twilio_token', null, $salon->id);
        $from = $this->settingsService->get('whatsapp_twilio_from', null, $salon->id);

        if (empty($sid) || empty($token) || empty($from)) {
            Log::error("WhatsApp Service [Twilio]: Credentials missing for salon {$salon->id}.");
            return false;
        }

        // Ensure "whatsapp:" prefix for Twilio WhatsApp API
        if (!str_starts_with($to, 'whatsapp:')) {
            $to = 'whatsapp:' . $to;
        }
        if (!str_starts_with($from, 'whatsapp:')) {
            $from = 'whatsapp:' . $from;
        }

        try {
            $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";

            $payload = [
                'To' => $to,
                'From' => $from,
                'Body' => $message,
            ];

            if ($mediaUrl) {
                $payload['MediaUrl'] = $mediaUrl;
            }

            $response = Http::withBasicAuth($sid, $token)->asForm()->post($url, $payload);

            return $this->handleResponse($response, 'Twilio', $salon->id, $to);
        } catch (\Exception $e) {
            Log::error("WhatsApp Service [Twilio]: Exception for salon {$salon->id}. " . $e->getMessage());
            return false;
        }
    }

    protected function sendCustomHttp($to, $message, $salon, $mediaUrl = null)
    {
        $url = $this->settingsService->get('whatsapp_custom_url', null, $salon->id);
        $method = $this->settingsService->get('whatsapp_custom_method', 'POST', $salon->id);

        $headersJson = $this->settingsService->get('whatsapp_custom_headers', '[]', $salon->id);
        $headers = json_decode($headersJson, true) ?: [];

        $toKey = $this->settingsService->get('whatsapp_custom_to_key', 'to', $salon->id);
        $msgKey = $this->settingsService->get('whatsapp_custom_message_key', 'message', $salon->id);

        $payloadJson = $this->settingsService->get('whatsapp_custom_payload', '[]', $salon->id);
        $additionalPayload = json_decode($payloadJson, true) ?: [];

        if (empty($url)) {
            Log::error("WhatsApp Service [Custom]: URL missing for salon {$salon->id}.");
            return false;
        }

        $payload = array_merge($additionalPayload, [
            $toKey => $to,
            $msgKey => $message,
        ]);

        // Add media URL to payload if custom gateway expects it (generic 'media_url' key for now)
        if ($mediaUrl) {
            $payload['media_url'] = $mediaUrl;
        }

        try {
            $request = Http::withHeaders($headers ?: []);
            $response = ($method === 'GET')
                ? $request->get($url, $payload)
                : $request->post($url, $payload);

            return $this->handleResponse($response, 'Custom', $salon->id, $to);
        } catch (\Exception $e) {
            Log::error("WhatsApp Service [Custom]: Exception for salon {$salon->id}. " . $e->getMessage());
            return false;
        }
    }

    protected function handleResponse($response, $driver, $salonId, $to)
    {
        if ($response->successful()) {
            Log::info("WhatsApp Service [{$driver}]: Message sent successfully for salon {$salonId} to {$to}.");
            return true;
        } else {
            Log::error("WhatsApp Service [{$driver}]: Failed to send for salon {$salonId} to {$to}. Error: " . $response->body());
            return false;
        }
    }
}
