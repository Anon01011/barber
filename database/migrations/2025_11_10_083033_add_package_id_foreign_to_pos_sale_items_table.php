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
        try {
            Schema::table('pos_sale_items', function (Blueprint $table) {
                if (Schema::hasColumn('pos_sale_items', 'package_id')) {
                    $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
                }
            });
        } catch (\Exception $e) {
            // Ignore "index already exists" error, common when running tests with SQLite or re-running migrations
            if (!str_contains($e->getMessage(), 'already exists') && !str_contains($e->getMessage(), 'Duplicate key name')) {
                throw $e;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_sale_items', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
        });
    }
};
