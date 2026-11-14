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
        // 1. Trusted Devices Table
        Schema::create('trusted_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('salon_id')->nullable()->constrained('salons')->onDelete('cascade');
            $table->string('device_uuid', 64)->unique();
            $table->string('device_name', 150);
            $table->string('device_token_hash', 64)->index();
            $table->string('browser', 100)->nullable();
            $table->string('operating_system', 100)->nullable();
            $table->string('last_ip', 45)->nullable();
            $table->timestamp('first_used_at');
            $table->timestamp('last_used_at');
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('revoked_at')->nullable()->index();
            $table->string('status', 30)->default('trusted')->index();
            $table->timestamps();

            $table->index(['user_id', 'salon_id']);
            $table->index(['user_id', 'status']);
        });

        // 2. User Device PINs Table
        Schema::create('user_device_pins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('salon_id')->nullable()->constrained('salons')->onDelete('cascade');
            $table->foreignId('trusted_device_id')->nullable()->constrained('trusted_devices')->onDelete('set null');
            $table->string('pin_hash', 255);
            $table->unsignedTinyInteger('failed_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->boolean('pin_enabled')->default(true)->index();
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'salon_id']);
            $table->index(['user_id', 'pin_enabled']);
        });

        // 3. Device & PIN Security Audit Logs Table
        Schema::create('device_pin_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('salon_id')->nullable()->constrained('salons')->onDelete('cascade');
            $table->foreignId('trusted_device_id')->nullable()->constrained('trusted_devices')->onDelete('set null');
            $table->string('event', 50)->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->json('details')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'event']);
            $table->index(['salon_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_pin_audit_logs');
        Schema::dropIfExists('user_device_pins');
        Schema::dropIfExists('trusted_devices');
    }
};
