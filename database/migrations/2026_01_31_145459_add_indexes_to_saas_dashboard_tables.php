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
        Schema::table('saas_payments', function (Blueprint $table) {
            $table->index('status');
            $table->index('created_at');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index('status');
            $table->index('ends_at');
        });

        Schema::table('salons', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('created_at');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saas_payments', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['ends_at']);
        });

        Schema::table('salons', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['created_at']);
        });

    }
};
