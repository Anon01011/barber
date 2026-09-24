<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SettingsService;

class FeatureEnabled
{
    public function handle(Request $request, Closure $next, $feature)
    {
        if (!SettingsService::featureEnabled($feature)) {
            abort(403, 'This feature is disabled.');
        }
        return $next($request);
    }
} 