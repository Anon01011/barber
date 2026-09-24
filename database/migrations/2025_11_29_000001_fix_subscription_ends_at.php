<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Update existing subscriptions that don't have ends_at set
        $subscriptions = DB::table('subscriptions')
            ->whereNull('ends_at')
            ->get();

        foreach ($subscriptions as $subscription) {
            $plan = DB::table('plans')->find($subscription->plan_id);
            
            if ($plan && $subscription->starts_at) {
                $endsAt = \Carbon\Carbon::parse($subscription->starts_at)
                    ->addDays($plan->duration_in_days);
                
                DB::table('subscriptions')
                    ->where('id', $subscription->id)
                    ->update(['ends_at' => $endsAt]);
            }
        }
    }

    public function down()
    {
        // No rollback needed
    }
};
