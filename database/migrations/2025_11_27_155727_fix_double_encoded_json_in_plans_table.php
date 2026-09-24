<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix double-encoded JSON in features and limits columns
        $plans = DB::table('plans')->get();
        
        foreach ($plans as $plan) {
            $updates = [];
            
            // Fix features if it's double-encoded
            if ($plan->features) {
                $decoded = json_decode($plan->features, true);
                if ($decoded !== null) {
                    $updates['features'] = json_encode($decoded);
                }
            }
            
            // Fix limits if it's double-encoded
            if ($plan->limits) {
                $decoded = json_decode($plan->limits, true);
                if ($decoded !== null) {
                    $updates['limits'] = json_encode($decoded);
                }
            }
            
            if (!empty($updates)) {
                DB::table('plans')->where('id', $plan->id)->update($updates);
            }
        }
        Schema::table('plans', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            //
        });
    }
};
