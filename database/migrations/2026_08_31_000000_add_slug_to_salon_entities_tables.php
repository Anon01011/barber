<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        $tables = ['memberships', 'packages', 'services', 'service_categories', 'branches'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                if (!Schema::hasColumn($tableName, 'slug')) {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->string('slug')->nullable()->after('name');
                    });
                }

                // Backfill slugs for existing records
                $rows = DB::table($tableName)->get(['id', 'name', 'slug']);
                foreach ($rows as $row) {
                    if (empty($row->slug) && !empty($row->name)) {
                        $baseSlug = Str::slug($row->name);
                        $slug = $baseSlug ?: 'item';
                        $count = 1;
                        while (DB::table($tableName)->where('slug', $slug)->where('id', '!=', $row->id)->exists()) {
                            $slug = $baseSlug . '-' . $count;
                            $count++;
                        }
                        DB::table($tableName)->where('id', $row->id)->update(['slug' => $slug]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        $tables = ['memberships', 'packages', 'services', 'service_categories', 'branches'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'slug')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('slug');
                });
            }
        }
    }
};
