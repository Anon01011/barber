<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\ModuleHelper;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register @moduleEnabled directive
        Blade::if('moduleEnabled', function ($moduleName) {
            return ModuleHelper::isEnabled($moduleName);
        });

        // Register @branchesEnabled directive
        Blade::if('branchesEnabled', function () {
            return ModuleHelper::branchesEnabled();
        });
    }
}
