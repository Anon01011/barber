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
        Schema::table('customers', function (Blueprint $table) {
            // Check and add columns only if they don't exist
            if (!Schema::hasColumn('customers', 'first_name')) {
                $table->string('first_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('customers', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('customers', 'customer_id')) {
                $table->string('customer_id')->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('customers', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('customers', 'dob')) {
                $table->date('dob')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('customers', 'anniversary')) {
                $table->date('anniversary')->nullable()->after('dob');
            }
            if (!Schema::hasColumn('customers', 'secondary_number')) {
                $table->string('secondary_number', 20)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('customers', 'location')) {
                $table->string('location')->nullable()->after('address');
            }
            if (!Schema::hasColumn('customers', 'source')) {
                $table->string('source')->nullable()->after('location');
            }
            if (!Schema::hasColumn('customers', 'send_promotional_sms')) {
                $table->boolean('send_promotional_sms')->default(true)->after('preferred_contact');
            }
            if (!Schema::hasColumn('customers', 'send_transactional_sms')) {
                $table->boolean('send_transactional_sms')->default(true)->after('send_promotional_sms');
            }
            if (!Schema::hasColumn('customers', 'custom_fields')) {
                $table->json('custom_fields')->nullable()->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'customer_id',
                'gender',
                'dob',
                'anniversary',
                'secondary_number',
                'location',
                'source',
                'send_promotional_sms',
                'send_transactional_sms',
                'custom_fields'
            ]);
        });
    }
};
