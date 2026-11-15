<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsappSetupController extends Controller
{
    protected $settingsService;
    protected $whatsappService;

    public function __construct(SettingsService $settingsService, WhatsappService $whatsappService)
    {
        $this->settingsService = $settingsService;
        $this->whatsappService = $whatsappService;
    }

    public function index()
    {
        $salon = auth()->user()->salon;

        if (!$salon->canUseFeature('WhatsApp Notifications')) {
            return redirect()->route('admin.salon-settings.index', ['salon_slug' => $salon->slug])
                ->with('error', 'WhatsApp Notifications are not included in your current plan.');
        }

        $settings = $this->settingsService->getAll($salon->id);

        return view('admin.settings.whatsapp_setup', compact('salon', 'settings'));
    }

    public function testWhatsapp(Request $request)
    {
        $gateway = $request->input('gateway', 'twilio');

        $validationRules = [
            'gateway' => 'required|string|in:twilio,custom',
            'phone' => 'required|string',
        ];

        if ($gateway === 'twilio') {
            $validationRules = array_merge($validationRules, [
                'whatsapp_twilio_sid' => 'required|string',
                'whatsapp_twilio_token' => 'required|string',
                'whatsapp_twilio_from' => 'required|string',
            ]);
        } elseif ($gateway === 'custom') {
            $validationRules = array_merge($validationRules, [
                'whatsapp_custom_url' => 'required|url',
                'whatsapp_custom_method' => 'required|in:GET,POST',
                'whatsapp_custom_to_key' => 'required|string',
                'whatsapp_custom_message_key' => 'required|string',
            ]);
        }

        $request->validate($validationRules);

        $salon = auth()->user()->salon;

        if (!$salon->canUseFeature('WhatsApp Notifications')) {
            return response()->json([
                'success' => false,
                'message' => 'WhatsApp Notifications are not included in your current plan.'
            ], 403);
        }

        $message = "Test WhatsApp message from " . $salon->name . ". Your WhatsApp configuration is correct!";

        try {
            $response = null;

            if ($gateway === 'twilio') {
                $to = $request->phone;
                $from = $request->whatsapp_twilio_from;

                if (!str_starts_with($to, 'whatsapp:')) {
                    $to = 'whatsapp:' . $to;
                }
                if (!str_starts_with($from, 'whatsapp:')) {
                    $from = 'whatsapp:' . $from;
                }

                $response = Http::withBasicAuth($request->whatsapp_twilio_sid, $request->whatsapp_twilio_token)
                    ->asForm()
                    ->post("https://api.twilio.com/2010-04-01/Accounts/{$request->whatsapp_twilio_sid}/Messages.json", [
                        'To' => $to,
                        'From' => $from,
                        'Body' => $message,
                    ]);
            } elseif ($gateway === 'custom') {
                if (!$this->isSafeWebhookUrl($request->whatsapp_custom_url)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid or restricted custom gateway URL. Internal and private network addresses are blocked for security.'
                    ], 422);
                }

                $headers = json_decode($request->input('whatsapp_custom_headers', '[]'), true);
                $payload = json_decode($request->input('whatsapp_custom_payload', '[]'), true);
                $payload[$request->whatsapp_custom_to_key] = $request->phone;
                $payload[$request->whatsapp_custom_message_key] = $message;

                $req = Http::timeout(10)->withHeaders($headers ?: []);
                $response = ($request->whatsapp_custom_method === 'GET')
                    ? $req->get($request->whatsapp_custom_url, $payload)
                    : $req->post($request->whatsapp_custom_url, $payload);
            }

            if ($response && $response->successful()) {
                return response()->json(['success' => true, 'message' => 'Test WhatsApp sent successfully!']);
            } else {
                $error = $response ? ($response->json('message') ?? 'Gateway returned an error code ' . $response->status()) : 'Unknown error';
                return response()->json(['success' => false, 'message' => 'Gateway Error: ' . $error]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: Failed to deliver test WhatsApp message.']);
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
