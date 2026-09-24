<?php

namespace App\Jobs;

use App\Models\Plan;
use App\Services\SalonRoleSeederService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncPlanPermissions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $plan;

    /**
     * Create a new job instance.
     */
    public function __construct(Plan $plan)
    {
        $this->plan = $plan;
    }

    /**
     * Execute the job.
     */
    public function handle(SalonRoleSeederService $seeder): void
    {
        Log::info("Starting permission sync for Plan ID: {$this->plan->id}");

        // Find all active subscriptions for this plan
        // We use the active scope from Subscription model
        $this->plan->subscriptions()->active()->chunk(50, function ($subscriptions) use ($seeder) {
            foreach ($subscriptions as $subscription) {
                try {
                    Log::info("Syncing permissions for Salon ID: {$subscription->salon_id}");
                    $seeder->seedDefaultRoles($subscription->salon_id, $this->plan);
                } catch (\Exception $e) {
                    Log::error("Failed to sync permissions for salon {$subscription->salon_id}: " . $e->getMessage());
                }
            }
        });

        Log::info("Completed permission sync for Plan ID: {$this->plan->id}");
    }
}
