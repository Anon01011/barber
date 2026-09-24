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
        Schema::table('pos_sales', function (Blueprint $table) {
            // Check if columns exist before adding
            if (!Schema::hasColumn('pos_sales', 'status')) {
                $table->string('status', 50)->default('completed')->after('payment_status');
            }
            if (!Schema::hasColumn('pos_sales', 'voided_by')) {
                $table->foreignId('voided_by')->nullable()->constrained('users')->onDelete('set null')->after('notes');
            }
            if (!Schema::hasColumn('pos_sales', 'voided_at')) {
                $table->timestamp('voided_at')->nullable()->after('voided_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_sales', function (Blueprint $table) {
            $table->dropForeign(['voided_by']);
            $table->dropColumn(['status', 'voided_by', 'voided_at']);
        });
    }
};
