<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebugController extends Controller
{
    public function debugPlanFeatures()
    {
        $user = auth()->user();

        $debug = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_super_admin' => $user->isSuperAdmin(),
            ],
            'salon' => null,
            'subscription' => null,
            'plan' => null,
            'enabled_modules' => null,
        ];

        if ($user->salon) {
            $debug['salon'] = [
                'id' => $user->salon->id,
                'name' => $user->salon->name,
                'slug' => $user->salon->slug,
            ];

            $subscription = $user->salon->activeSubscription;
            if ($subscription) {
                $debug['subscription'] = [
                    'id' => $subscription->id,
                    'status' => $subscription->status,
                    'starts_at' => $subscription->starts_at?->toDateTimeString(),
                    'ends_at' => $subscription->ends_at?->toDateTimeString(),
                    'is_active' => $subscription->isActive(),
                ];

                $plan = $subscription->plan;
                if ($plan) {
                    $debug['plan'] = [
                        'id' => $plan->id,
                        'name' => $plan->name,
                        'slug' => $plan->slug,
                        'features_raw' => $plan->features,
                        'features_type' => gettype($plan->features),
                        'features_decoded' => is_string($plan->features) ? json_decode($plan->features, true) : $plan->features,
                    ];

                    // Test hasFeature method
                    $testFeatures = [
                        'Booking System',
                        'POS System',
                        'Inventory Management',
                        'Staff Management',
                        'Customer Management',
                        'Role Management',
                    ];

                    $debug['plan']['feature_checks'] = [];
                    foreach ($testFeatures as $feature) {
                        $debug['plan']['feature_checks'][$feature] = $plan->hasFeature($feature);
                    }
                }
            }

            // Check enabled modules
            $settingsService = app(\App\Services\SettingsService::class);
            $enabledModules = $settingsService->get('enabled_modules', [], false, false);

            $debug['enabled_modules'] = [
                'raw' => $enabledModules,
                'type' => gettype($enabledModules),
                'is_empty' => empty($enabledModules),
            ];
        }

        return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
    }
}
