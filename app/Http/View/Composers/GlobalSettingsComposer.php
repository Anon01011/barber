<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Services\SettingsService;

class GlobalSettingsComposer
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
        // SettingsService now auto-detects the salon_id from the authenticated user
        $globalSettings = [
            'theme_color' => $this->settingsService->getThemeColor(),
            'logo' => $this->settingsService->get('logo', 'default-logo.png'),
            'business_name' => $this->settingsService->getBusinessName(),
            'currency_symbol' => $this->settingsService->get('currency_symbol', 'QR'),
            'currency_code' => $this->settingsService->get('currency_code', 'QAR'),
        ];

        $view->with('globalSettings', $globalSettings);
        $view->with('settings', $this->settingsService);
    }
}
