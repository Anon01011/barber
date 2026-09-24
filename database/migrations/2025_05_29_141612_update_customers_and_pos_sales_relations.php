<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // First ensure users table exists
        if (!Schema::hasTable('users')) {
            throw new \Exception('Users table must exist before running this migration');
        }

        // Add user_id to customers
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'user_id')) {
                $table->unsignedBigInteger('user_id')->unique()->nullable()->after('id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });

        // Migrate existing customer records to set user_id by matching email
        if (Schema::hasTable('users') && Schema::hasTable('customers')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement('UPDATE customers c JOIN users u ON c.email = u.email SET c.user_id = u.id');
            } else if (DB::getDriverName() === 'sqlite') {
                DB::statement('UPDATE customers SET user_id = (SELECT id FROM users WHERE users.email = customers.email) WHERE EXISTS (SELECT 1 FROM users WHERE users.email = customers.email)');
            }
        }

        // Update pos_sales to reference customers instead of users for customer_id
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('pos_sales') && Schema::hasColumn('pos_sales', 'customer_id')) {
            $foreignKeys = collect(DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = 'pos_sales' 
                  AND COLUMN_NAME = 'customer_id' 
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            "))->pluck('CONSTRAINT_NAME');

            if ($foreignKeys->isNotEmpty()) {
                Schema::table('pos_sales', function (Blueprint $table) use ($foreignKeys) {
                    foreach ($foreignKeys as $fk) {
                        $table->dropForeign($fk);
                    }
                });
            }

            // Add foreign key pointing to customers table if not referencing customers already
            $referencesCustomers = DB::select("
                SELECT REFERENCED_TABLE_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = 'pos_sales' 
                  AND COLUMN_NAME = 'customer_id' 
                  AND REFERENCED_TABLE_NAME = 'customers'
            ");

            if (empty($referencesCustomers)) {
                Schema::table('pos_sales', function (Blueprint $table) {
                    $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
                });
            }
        }
    }

    public function down()
    {
        // Revert pos_sales customer_id to reference users
        if (Schema::hasTable('pos_sales') && Schema::hasColumn('pos_sales', 'customer_id')) {
            $foreignKeys = collect(DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = 'pos_sales' 
                  AND COLUMN_NAME = 'customer_id' 
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            "))->pluck('CONSTRAINT_NAME');

            if ($foreignKeys->isNotEmpty()) {
                Schema::table('pos_sales', function (Blueprint $table) use ($foreignKeys) {
                    foreach ($foreignKeys as $fk) {
                        $table->dropForeign($fk);
                    }
                });
            }

            Schema::table('pos_sales', function (Blueprint $table) {
                $table->foreign('customer_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // Remove user_id from customers
        if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'user_id')) {
            $userFks = collect(DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = 'customers' 
                  AND COLUMN_NAME = 'user_id' 
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            "))->pluck('CONSTRAINT_NAME');

            Schema::table('customers', function (Blueprint $table) use ($userFks) {
                foreach ($userFks as $fk) {
                    $table->dropForeign($fk);
                }
                $table->dropColumn('user_id');
            });
        }
    }
}; 