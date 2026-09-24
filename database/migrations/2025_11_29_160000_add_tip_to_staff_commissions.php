<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_commissions', function (Blueprint $table) {
            // Make item_id and commission_profile_id nullable for tips
            $table->unsignedBigInteger('item_id')->nullable()->change();
            $table->foreignId('commission_profile_id')->nullable()->change();
        });
        
        // Update the enum to include 'tip'
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE staff_commissions MODIFY COLUMN item_type ENUM('service', 'product', 'membership', 'package', 'tip')");
        }
    }

    public function down(): void
    {
        // Revert the enum
        DB::statement("ALTER TABLE staff_commissions MODIFY COLUMN item_type ENUM('service', 'product', 'membership', 'package')");
        
        Schema::table('staff_commissions', function (Blueprint $table) {
            // Make fields required again (but this might fail if there are tip records)
            $table->unsignedBigInteger('item_id')->nullable(false)->change();
            $table->foreignId('commission_profile_id')->nullable(false)->change();
        });
    }
};
