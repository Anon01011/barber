<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('saas_payments') && !Schema::hasColumn('saas_payments', 'metadata')) {
            Schema::table('saas_payments', function (Blueprint $table) {
                $table->json('metadata')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saas_payments', function (Blueprint $table) {
            $table->dropColumn('metadata');
        });
    }
};
