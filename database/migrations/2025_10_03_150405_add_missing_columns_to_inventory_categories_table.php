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
        Schema::table('inventory_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_categories', 'color')) {
                $table->string('color', 20)->default('#6c757d')->after('slug');
            }
            
            if (!Schema::hasColumn('inventory_categories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('color');
            }
            
            if (!Schema::hasColumn('inventory_categories', 'icon')) {
                $table->string('icon', 50)->default('fas fa-box')->after('is_active');
            }
            
            if (!Schema::hasColumn('inventory_categories', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('icon');
                $table->foreign('parent_id')->references('id')->on('inventory_categories')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('inventory_categories', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('parent_id');
            }
            
            if (!Schema::hasColumn('inventory_categories', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            
            if (!Schema::hasColumn('inventory_categories', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            }
            
            if (!Schema::hasColumn('inventory_categories', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_categories', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_categories', 'parent_id')) {
                $table->dropForeign(['parent_id']);
            }
            
            $columnsToDrop = [
                'color', 'is_active', 'icon', 'parent_id', 
                'meta_title', 'meta_description', 'meta_keywords'
            ];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('inventory_categories', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            if (Schema::hasColumn('inventory_categories', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
