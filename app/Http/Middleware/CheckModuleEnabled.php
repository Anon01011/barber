<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SettingsService;

class CheckModuleEnabled
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function handle(Request $request, Closure $next, string $module)
    {
        // Get enabled modules from settings (global, no salon context)
        // IMPORTANT: Pass false (not null) to force global system settings
        $enabledModules = $this->settingsService->get('enabled_modules', [], false, false);

        // If module is explicitly disabled, block access
        if (isset($enabledModules[$module]) && !$enabledModules[$module]) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'This module is currently disabled by the system administrator.'
                ], 403);
            }

            return redirect()->route('admin.dashboard')
                ->with('error', 'This module is currently disabled by the system administrator.');
        }

        return $next($request);
    }
}
