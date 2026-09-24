<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add salon_id to appointments table (if table exists)
        if (Schema::hasTable('appointments') && !Schema::hasColumn('appointments', 'salon_id')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->foreignId('salon_id')->nullable()->after('id');
                $table->index('salon_id');
            });

            // Populate salon_id from customer's salon_id
            if (DB::getDriverName() === 'mysql') {
                DB::statement('
                    UPDATE appointments a
                    INNER JOIN customers c ON a.customer_id = c.id
                    SET a.salon_id = c.salon_id
                    WHERE a.salon_id IS NULL AND c.salon_id IS NOT NULL
                ');
            } else if (DB::getDriverName() === 'sqlite') {
                DB::statement('
                    UPDATE appointments 
                    SET salon_id = (SELECT salon_id FROM customers WHERE customers.id = appointments.customer_id)
                    WHERE salon_id IS NULL AND EXISTS (SELECT 1 FROM customers WHERE customers.id = appointments.customer_id)
                ');
            }

            // Delete orphaned records
            DB::statement('DELETE FROM appointments WHERE salon_id IS NULL');

            // Make salon_id non-nullable and add foreign key
            Schema::table('appointments', function (Blueprint $table) {
                $table->foreignId('salon_id')->nullable(false)->change();
                $table->foreign('salon_id')->references('id')->on('salons')->onDelete('cascade');
            });
        }

        // Add salon_id to reviews table (if table exists)
        if (Schema::hasTable('reviews') && !Schema::hasColumn('reviews', 'salon_id')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->foreignId('salon_id')->nullable()->after('id');
                $table->index('salon_id');
            });

            // Populate salon_id from service's salon_id
            if (DB::getDriverName() === 'mysql') {
                DB::statement('
                    UPDATE reviews r
                    INNER JOIN services s ON r.service_id = s.id
                    SET r.salon_id = s.salon_id
                    WHERE r.salon_id IS NULL AND s.salon_id IS NOT NULL
                ');
            } else if (DB::getDriverName() === 'sqlite') {
                DB::statement('
                    UPDATE reviews 
                    SET salon_id = (SELECT salon_id FROM services WHERE services.id = reviews.service_id)
                    WHERE salon_id IS NULL AND EXISTS (SELECT 1 FROM services WHERE services.id = reviews.service_id)
                ');
            }

            // Delete orphaned records
            DB::statement('DELETE FROM reviews WHERE salon_id IS NULL');

            // Make salon_id non-nullable and add foreign key
            Schema::table('reviews', function (Blueprint $table) {
                $table->foreignId('salon_id')->nullable(false)->change();
                $table->foreign('salon_id')->references('id')->on('salons')->onDelete('cascade');
            });
        }

        // Add salon_id to ratings table (if table exists)
        if (Schema::hasTable('ratings') && !Schema::hasColumn('ratings', 'salon_id')) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->foreignId('salon_id')->nullable()->after('id');
                $table->index('salon_id');
            });

            // Populate salon_id from customer's salon_id
            if (DB::getDriverName() === 'mysql') {
                DB::statement('
                    UPDATE ratings r
                    INNER JOIN users u ON r.customer_id = u.id
                    SET r.salon_id = u.salon_id
                    WHERE r.salon_id IS NULL AND u.salon_id IS NOT NULL
                ');
            } else if (DB::getDriverName() === 'sqlite') {
                DB::statement('
                    UPDATE ratings 
                    SET salon_id = (SELECT salon_id FROM users WHERE users.id = ratings.customer_id)
                    WHERE salon_id IS NULL AND EXISTS (SELECT 1 FROM users WHERE users.id = ratings.customer_id)
                ');
            }

            // Delete orphaned records
            DB::statement('DELETE FROM ratings WHERE salon_id IS NULL');

            // Make salon_id non-nullable and add foreign key
            Schema::table('ratings', function (Blueprint $table) {
                $table->foreignId('salon_id')->nullable(false)->change();
                $table->foreign('salon_id')->references('id')->on('salons')->onDelete('cascade');
            });
        }

        // Add salon_id to pos_sale_items table (if table exists)
        if (Schema::hasTable('pos_sale_items') && !Schema::hasColumn('pos_sale_items', 'salon_id')) {
            Schema::table('pos_sale_items', function (Blueprint $table) {
                $table->foreignId('salon_id')->nullable()->after('id');
                $table->index('salon_id');
            });

            // Populate salon_id from pos_sales's salon_id
            if (DB::getDriverName() === 'mysql') {
                DB::statement('
                    UPDATE pos_sale_items psi
                    INNER JOIN pos_sales ps ON psi.sale_id = ps.id
                    SET psi.salon_id = ps.salon_id
                    WHERE psi.salon_id IS NULL AND ps.salon_id IS NOT NULL
                ');
            } else if (DB::getDriverName() === 'sqlite') {
                DB::statement('
                    UPDATE pos_sale_items 
                    SET salon_id = (SELECT salon_id FROM pos_sales WHERE pos_sales.id = pos_sale_items.sale_id)
                    WHERE salon_id IS NULL AND EXISTS (SELECT 1 FROM pos_sales WHERE pos_sales.id = pos_sale_items.sale_id)
                ');
            }

            // Delete orphaned records
            DB::statement('DELETE FROM pos_sale_items WHERE salon_id IS NULL');

            // Make salon_id non-nullable and add foreign key
            Schema::table('pos_sale_items', function (Blueprint $table) {
                $table->foreignId('salon_id')->nullable(false)->change();
                $table->foreign('salon_id')->references('id')->on('salons')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('appointments') && Schema::hasColumn('appointments', 'salon_id')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropForeign(['salon_id']);
                $table->dropIndex(['salon_id']);
                $table->dropColumn('salon_id');
            });
        }

        if (Schema::hasTable('reviews') && Schema::hasColumn('reviews', 'salon_id')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropForeign(['salon_id']);
                $table->dropIndex(['salon_id']);
                $table->dropColumn('salon_id');
            });
        }

        if (Schema::hasTable('ratings') && Schema::hasColumn('ratings', 'salon_id')) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->dropForeign(['salon_id']);
                $table->dropIndex(['salon_id']);
                $table->dropColumn('salon_id');
            });
        }

        if (Schema::hasTable('pos_sale_items') && Schema::hasColumn('pos_sale_items', 'salon_id')) {
            Schema::table('pos_sale_items', function (Blueprint $table) {
                $table->dropForeign(['salon_id']);
                $table->dropIndex(['salon_id']);
                $table->dropColumn('salon_id');
            });
        }
    }
};
