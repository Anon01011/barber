<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmsSetupController extends Controller
{
    protected $settingsService;
    protected $smsService;

    public function __construct(SettingsService $settingsService, SmsService $smsService)
    {
        $this->settingsService = $settingsService;
        $this->smsService = $smsService;
    }

    public function index()
    {
        $salon = auth()->user()->salon;

        if (!$salon->canUseFeature('SMS Notifications')) {
            return redirect()->route('admin.salon-settings.index', ['salon_slug' => $salon->slug])
                ->with('error', 'SMS Notifications are not included in your current plan.');
        }

        $settings = $this->settingsService->getAll($salon->id);

        return view('admin.settings.sms_setup', compact('salon', 'settings'));
    }

    public function testSms(Request $request)
    {
        $gateway = $request->input('gateway', 'twilio');

        $validationRules = [
            'gateway' => 'required|string|in:twilio,vonage,messagebird,custom',
            'phone' => 'required|string',
        ];

        if ($gateway === 'twilio') {
            $validationRules = array_merge($validationRules, [
                'twilio_sid' => 'required|string',
                'twilio_token' => 'required|string',
                'twilio_from' => 'required|string',
            ]);
        } elseif ($gateway === 'vonage') {
            $validationRules = array_merge($validationRules, [
                'vonage_api_key' => 'required|string',
                'vonage_api_secret' => 'required|string',
                'vonage_from' => 'required|string',
            ]);
        } elseif ($gateway === 'messagebird') {
            $validationRules = array_merge($validationRules, [
                'messagebird_access_key' => 'required|string',
                'messagebird_originator' => 'required|string',
            ]);
        } elseif ($gateway === 'custom') {
            $validationRules = array_merge($validationRules, [
                'custom_sms_url' => 'required|url',
                'custom_sms_method' => 'required|in:GET,POST',
                'custom_sms_to_key' => 'required|string',
                'custom_sms_message_key' => 'required|string',
            ]);
        }

        $request->validate($validationRules);

        $salon = auth()->user()->salon;

        if (!$salon->canUseFeature('SMS Notifications')) {
            return response()->json([
                'success' => false,
                'message' => 'SMS Notifications are not included in your current plan.'
            ], 403);
        }

        $message = "Test message from " . $salon->name . ". Your SMS configuration is correct!";

        try {
            $response = null;

            if ($gateway === 'twilio') {
                $response = \Illuminate\Support\Facades\Http::withBasicAuth($request->twilio_sid, $request->twilio_token)
                    ->asForm()
                    ->post("https://api.twilio.com/2010-04-01/Accounts/{$request->twilio_sid}/Messages.json", [
                        'To' => $request->phone,
                        'From' => $request->twilio_from,
                        'Body' => $message,
                    ]);
            } elseif ($gateway === 'vonage') {
                $response = \Illuminate\Support\Facades\Http::post("https://rest.nexmo.com/sms/json", [
                    'api_key' => $request->vonage_api_key,
                    'api_secret' => $request->vonage_api_secret,
                    'to' => $request->phone,
                    'from' => $request->vonage_from,
                    'text' => $message,
                ]);
            } elseif ($gateway === 'messagebird') {
                $response = \Illuminate\Support\Facades\Http::withHeaders(['Authorization' => 'AccessKey ' . $request->messagebird_access_key])
                    ->post("https://rest.messagebird.com/messages", [
                        'recipients' => [$request->phone],
                        'originator' => $request->messagebird_originator,
                        'body' => $message,
                    ]);
            } elseif ($gateway === 'custom') {
                if (!$this->isSafeWebhookUrl($request->custom_sms_url)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid or restricted custom gateway URL. Internal and private network addresses are blocked for security.'
                    ], 422);
                }

                $headers = json_decode($request->input('custom_sms_headers', '[]'), true);
                $payload = json_decode($request->input('custom_sms_payload', '[]'), true);
                $payload[$request->custom_sms_to_key] = $request->phone;
                $payload[$request->custom_sms_message_key] = $message;

                $req = \Illuminate\Support\Facades\Http::timeout(10)->withHeaders($headers ?: []);
                $response = ($request->custom_sms_method === 'GET')
                    ? $req->get($request->custom_sms_url, $payload)
                    : $req->post($request->custom_sms_url, $payload);
            }

            if ($response && $response->successful()) {
                return response()->json(['success' => true, 'message' => 'Test SMS sent successfully!']);
            } else {
                $error = $response ? ($response->json('message') ?? 'Gateway returned an error code ' . $response->status()) : 'Unknown error';
                return response()->json(['success' => false, 'message' => 'Gateway Error: ' . $error]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: Failed to deliver test message.']);
        }
    }

    /**
     * Validate that a webhook URL does not resolve to private/reserved networks (SSRF defense).
     */
    protected function isSafeWebhookUrl(string $url): bool
    {
        $parsed = parse_url($url);
        if (!$parsed || empty($parsed['scheme']) || empty($parsed['host'])) {
            return false;
        }

        if (!in_array(strtolower($parsed['scheme']), ['https', 'http'], true)) {
            return false;
        }

        $host = strtolower($parsed['host']);

        // Explicit blocked hostnames
        $blockedHosts = ['localhost', '127.0.0.1', '::1', 'metadata.google.internal', 'instance-data'];
        if (in_array($host, $blockedHosts, true) || str_ends_with($host, '.localhost') || str_ends_with($host, '.local')) {
            return false;
        }

        // Resolve IP addresses
        $ipList = [];
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $ipList[] = $host;
        } else {
            $ip = @gethostbyname($host);
            if ($ip && $ip !== $host) {
                $ipList[] = $ip;
            }
        }

        if (empty($ipList)) {
            return false;
        }

        foreach ($ipList as $ip) {
            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return false;
            }
        }

        return true;
    }
}
