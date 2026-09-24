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
        Schema::table('customer_memberships', function (Blueprint $table) {
            // Add salon_id column as nullable first
            $table->foreignId('salon_id')->nullable()->after('id');
            
            // Add index for better query performance
            $table->index('salon_id');
        });

        // Populate salon_id from customer's salon_id
        if (DB::getDriverName() === 'mysql') {
            DB::statement('
                UPDATE customer_memberships cm
                INNER JOIN customers c ON cm.customer_id = c.id
                SET cm.salon_id = c.salon_id
                WHERE cm.salon_id IS NULL AND c.salon_id IS NOT NULL
            ');
        } else if (DB::getDriverName() === 'sqlite') {
            DB::statement('
                UPDATE customer_memberships 
                SET salon_id = (SELECT salon_id FROM customers WHERE customers.id = customer_memberships.customer_id)
                WHERE salon_id IS NULL AND EXISTS (SELECT 1 FROM customers WHERE customers.id = customer_memberships.customer_id)
            ');
        }

        // Delete orphaned records (customer_memberships without valid customer or salon)
        DB::statement('
            DELETE FROM customer_memberships
            WHERE salon_id IS NULL
        ');

        // Now make salon_id non-nullable and add foreign key
        Schema::table('customer_memberships', function (Blueprint $table) {
            $table->foreignId('salon_id')->nullable(false)->change();
            $table->foreign('salon_id')->references('id')->on('salons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_memberships', function (Blueprint $table) {
            $table->dropForeign(['salon_id']);
            $table->dropIndex(['salon_id']);
            $table->dropColumn('salon_id');
        });
    }
};
