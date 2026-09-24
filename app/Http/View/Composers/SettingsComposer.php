<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Storage;

class SettingsComposer
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        $appName = $this->settingsService->get('app_name', config('app.name', 'SalonSaaS'));
        $appLogo = $this->settingsService->get('app_logo');

        // Decode JSON if needed (SettingsService stores values as JSON)
        if (is_string($appName) && $this->isJson($appName)) {
            $appName = json_decode($appName);
        }

        // Generate full URL for logo if it exists
        $logoUrl = $appLogo ? Storage::url($appLogo) : null;

        $view->with([
            'appName' => $appName,
            'appLogo' => $logoUrl,
            'settings' => $this->settingsService,
        ]);
    }

    /**
     * Check if a string is valid JSON
     */
    private function isJson($string)
    {
        if (!is_string($string)) {
            return false;
        }
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
