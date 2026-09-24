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
        Schema::create('super_admin_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->string('action'); // e.g., 'salon.update', 'subscription.create'
            $table->foreignId('salon_id')->nullable()->constrained()->onDelete('set null');
            $table->string('target_type')->nullable(); // Model class name
            $table->unsignedBigInteger('target_id')->nullable(); // Model ID
            $table->json('data')->nullable(); // Additional context (changes, params, etc.)
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            // Indexes for efficient querying
            $table->index(['admin_id', 'created_at']);
            $table->index(['salon_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['target_type', 'target_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('super_admin_audit_logs');
    }
};
