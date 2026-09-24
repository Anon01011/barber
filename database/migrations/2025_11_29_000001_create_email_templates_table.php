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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salon_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type'); // e.g., 'booking_confirmation', 'booking_reminder', 'welcome'
            $table->string('subject');
            $table->text('content'); // HTML content
            $table->json('variables')->nullable(); // Available variables description
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Ensure one template per type per salon (or system-wide if salon_id is null)
            $table->unique(['salon_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
