<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AiAnalyticsService;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class AiReportController extends Controller
{
    protected $aiService;
    protected $settingsService;

    public function __construct(AiAnalyticsService $aiService, SettingsService $settingsService)
    {
        $this->middleware(['auth', 'verified']);
        $this->aiService = $aiService;
        $this->settingsService = $settingsService;
    }

    /**
     * Display Smart AI & ML Insights Report.
     */
    public function index($salon_slug = null)
    {
        $salon = auth()->user()->salon;

        if (!$salon) {
            abort(403, 'Unauthorized salon access.');
        }

        if ($salon_slug && $salon->slug !== $salon_slug) {
            abort(403, 'Unauthorized salon access.');
        }

        if (!$salon->canUseFeature('AI Insights & Automation')) {
            abort(403, 'AI Insights & Automation feature is not included in your active subscription plan.');
        }

        $aiData = $this->aiService->getAiInsights($salon);
        $currencySymbol = $this->settingsService->get('currency_symbol', '$', $salon->id);

        return view('admin.reports.ai-insights', compact('salon', 'aiData', 'currencySymbol'));
    }

    /**
     * AJAX Endpoint to fetch updated forecast and ML analytics JSON data.
     */
    public function getForecastData(Request $request, $salon_slug = null)
    {
        $salon = auth()->user()->salon;

        if (!$salon) {
            return response()->json(['error' => 'Unauthorized salon access.'], 403);
        }

        if ($salon_slug && $salon->slug !== $salon_slug) {
            return response()->json(['error' => 'Unauthorized salon access.'], 403);
        }

        if (!$salon->canUseFeature('AI Insights & Automation')) {
            return response()->json(['error' => 'AI Insights & Automation feature is not included in your active subscription plan.'], 403);
        }

        $aiData = $this->aiService->getAiInsights($salon);

        return response()->json($aiData);
    }
}
