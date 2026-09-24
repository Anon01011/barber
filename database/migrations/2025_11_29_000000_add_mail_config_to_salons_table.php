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
            if (!Schema::hasColumn('salons', 'mail_driver')) {
                $table->string('mail_driver')->default('smtp')->after('email');
            }
            if (!Schema::hasColumn('salons', 'mail_host')) {
                $table->string('mail_host')->nullable()->after('mail_driver');
            }
            if (!Schema::hasColumn('salons', 'mail_port')) {
                $table->integer('mail_port')->nullable()->after('mail_host');
            }
            if (!Schema::hasColumn('salons', 'mail_username')) {
                $table->string('mail_username')->nullable()->after('mail_port');
            }
            if (!Schema::hasColumn('salons', 'mail_password')) {
                $table->string('mail_password')->nullable()->after('mail_username');
            }
            if (!Schema::hasColumn('salons', 'mail_encryption')) {
                $table->string('mail_encryption')->nullable()->after('mail_password');
            }
            if (!Schema::hasColumn('salons', 'mail_from_address')) {
                $table->string('mail_from_address')->nullable()->after('mail_encryption');
            }
            if (!Schema::hasColumn('salons', 'mail_from_name')) {
                $table->string('mail_from_name')->nullable()->after('mail_from_address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salons', function (Blueprint $table) {
            $table->dropColumn([
                'mail_driver',
                'mail_host',
                'mail_port',
                'mail_username',
                'mail_password',
                'mail_encryption',
                'mail_from_address',
                'mail_from_name'
            ]);
        });
    }
};
