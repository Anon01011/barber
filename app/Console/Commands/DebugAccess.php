<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Salon;
use App\Services\SettingsService;

class DebugAccess extends Command
{
    protected $signature = 'debug:access';
    protected $description = 'Debug access control for demo-salon';

    public function handle()
    {
        $this->info("--- DEBUG START ---");
        $salon = Salon::where('slug', 'demo-salon')->first();
        $this->info("Salon: " . ($salon ? $salon->name : 'Not Found'));

        if ($salon) {
            $sub = $salon->activeSubscription;
            $this->info("Subscription: " . ($sub ? 'Active' : 'Inactive'));

            if ($sub) {
                $plan = $sub->plan;
                $this->info("Plan: " . $plan->name);
                $featureName = 'Analytics & Reports';
                $hasFeature = $plan->hasFeature($featureName);
                $this->info("Has Feature '$featureName': " . ($hasFeature ? 'Yes' : 'No'));

                $this->info("Raw Plan Features: " . json_encode($plan->features));
            } else {
                $this->error("No active subscription.");
            }

            // Check Module
            $settings = app(SettingsService::class);
            $modules = $settings->get('enabled_modules', [], false, false);
            $this->info("Module 'reports' enabled: " . ((isset($modules['reports']) && $modules['reports']) ? 'Yes' : 'No'));
            $this->info("All Modules: " . json_encode($modules));

            // Check User
            $user = $salon->users()->first();

            if ($user) {
                $this->info("Checking User: " . $user->email);
                $this->info("Has Permission 'reports.view': " . ($user->can('reports.view') ? 'Yes' : 'No'));
                $this->info("User Roles: " . json_encode($user->getRoleNames()));
                // $this->info("User Permissions: " . json_encode($user->getAllPermissions()->pluck('name')));
            } else {
                $this->error("No user found for this salon.");
            }

        } else {
            $this->error("Salon 'demo-salon' not found.");
        }
        $this->info("--- DEBUG END ---");
    }
}
