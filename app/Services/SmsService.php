<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Send an SMS message using the configured gateway.
     *
     * @param string $to Recipient phone number in E.164 format
     * @param string $message The message body
     * @param \App\Models\Salon|null $salon The salon context
     * @return bool True if successful or skipped, False otherwise
     */
    public function send($to, $message, $salon = null)
    {
        if (!$salon) {
            Log::warning("SMS Service: No salon context provided. Skipping message to {$to}.");
            return false;
        }

        // 1. Plan Check
        if (!$salon->canUseFeature('SMS Notifications')) {
            Log::info("SMS Service: Salon {$salon->id} ({$salon->name}) does not have SMS Notifications in their plan. Skipping message.");
            return true;
        }

        // 2. Global Enabled Check
        if (!$this->settingsService->get('enable_sms', false, $salon->id)) {
            Log::info("SMS Service: SMS is disabled for salon {$salon->id}. Skipping message to {$to}.");
            return true;
        }

        // 3. Get Selected Gateway
        $gateway = $this->settingsService->get('sms_gateway', 'twilio', $salon->id);

        // 4. Brand settings
        $salonName = $salon->name ?? 'Our Salon';
        $fullMessage = "{$salonName}: {$message}";

        switch ($gateway) {
            case 'twilio':
                return $this->sendTwilio($to, $fullMessage, $salon);
            case 'vonage':
                return $this->sendVonage($to, $fullMessage, $salon);
            case 'messagebird':
                return $this->sendMessageBird($to, $fullMessage, $salon);
            case 'custom':
                return $this->sendCustomHttp($to, $fullMessage, $salon);
            default:
                Log::error("SMS Service: Unknown gateway '{$gateway}' for salon {$salon->id}.");
                return false;
        }
    }

    protected function sendTwilio($to, $message, $salon)
    {
        $sid = $this->settingsService->get('twilio_sid', null, $salon->id);
        $token = $this->settingsService->get('twilio_token', null, $salon->id);
        $from = $this->settingsService->get('twilio_from', null, $salon->id);

        if (empty($sid) || empty($token) || empty($from)) {
            Log::error("SMS Service [Twilio]: Credentials missing for salon {$salon->id}.");
            return false;
        }

        try {
            $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";
            $response = Http::withBasicAuth($sid, $token)->asForm()->post($url, [
                'To' => $to,
                'From' => $from,
                'Body' => $message,
            ]);

            return $this->handleResponse($response, 'Twilio', $salon->id, $to);
        } catch (\Exception $e) {
            Log::error("SMS Service [Twilio]: Exception for salon {$salon->id}. " . $e->getMessage());
            return false;
        }
    }

    protected function sendVonage($to, $message, $salon)
    {
        $apiKey = $this->settingsService->get('vonage_api_key', null, $salon->id);
        $apiSecret = $this->settingsService->get('vonage_api_secret', null, $salon->id);
        $from = $this->settingsService->get('vonage_from', null, $salon->id);

        if (empty($apiKey) || empty($apiSecret) || empty($from)) {
            Log::error("SMS Service [Vonage]: Credentials missing for salon {$salon->id}.");
            return false;
        }

        try {
            $response = Http::post("https://rest.nexmo.com/sms/json", [
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
                'to' => $to,
                'from' => $from,
                'text' => $message,
            ]);

            return $this->handleResponse($response, 'Vonage', $salon->id, $to);
        } catch (\Exception $e) {
            Log::error("SMS Service [Vonage]: Exception for salon {$salon->id}. " . $e->getMessage());
            return false;
        }
    }

    protected function sendMessageBird($to, $message, $salon)
    {
        $accessKey = $this->settingsService->get('messagebird_access_key', null, $salon->id);
        $originator = $this->settingsService->get('messagebird_originator', null, $salon->id);

        if (empty($accessKey) || empty($originator)) {
            Log::error("SMS Service [MessageBird]: Credentials missing for salon {$salon->id}.");
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'AccessKey ' . $accessKey,
            ])->post("https://rest.messagebird.com/messages", [
                        'recipients' => [$to],
                        'originator' => $originator,
                        'body' => $message,
                    ]);

            return $this->handleResponse($response, 'MessageBird', $salon->id, $to);
        } catch (\Exception $e) {
            Log::error("SMS Service [MessageBird]: Exception for salon {$salon->id}. " . $e->getMessage());
            return false;
        }
    }

    protected function sendCustomHttp($to, $message, $salon)
    {
        $url = $this->settingsService->get('custom_sms_url', null, $salon->id);
        $method = $this->settingsService->get('custom_sms_method', 'POST', $salon->id);

        $headersJson = $this->settingsService->get('custom_sms_headers', '[]', $salon->id);
        $headers = json_decode($headersJson, true) ?: [];

        $toKey = $this->settingsService->get('custom_sms_to_key', 'to', $salon->id);
        $msgKey = $this->settingsService->get('custom_sms_message_key', 'message', $salon->id);

        $payloadJson = $this->settingsService->get('custom_sms_payload', '[]', $salon->id);
        $additionalPayload = json_decode($payloadJson, true) ?: [];

        if (empty($url)) {
            Log::error("SMS Service [Custom]: URL missing for salon {$salon->id}.");
            return false;
        }

        $payload = array_merge($additionalPayload, [
            $toKey => $to,
            $msgKey => $message,
        ]);

        try {
            $request = Http::withHeaders($headers ?: []);
            $response = ($method === 'GET')
                ? $request->get($url, $payload)
                : $request->post($url, $payload);

            return $this->handleResponse($response, 'Custom', $salon->id, $to);
        } catch (\Exception $e) {
            Log::error("SMS Service [Custom]: Exception for salon {$salon->id}. " . $e->getMessage());
            return false;
        }
    }

    protected function handleResponse($response, $driver, $salonId, $to)
    {
        if ($response->successful()) {
            Log::info("SMS Service [{$driver}]: Message sent successfully for salon {$salonId} to {$to}.");
            return true;
        } else {
            Log::error("SMS Service [{$driver}]: Failed to send for salon {$salonId} to {$to}. Error: " . $response->body());
            return false;
        }
    }
}
