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
        Schema::table('salons', function (Blueprint $table) {
            if (!Schema::hasColumn('salons', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('salons', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            if (!Schema::hasColumn('salons', 'country')) {
                $table->string('country')->nullable()->after('state');
            }
            if (!Schema::hasColumn('salons', 'zip_code')) {
                $table->string('zip_code')->nullable()->after('country');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salons', function (Blueprint $table) {
            $table->dropColumn(['city', 'state', 'country', 'zip_code']);
        });
    }
};
