<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Skip if customers table already exists (created by earlier migration)
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone', 20);
                $table->enum('preferred_contact', ['email', 'phone', 'sms'])->default('email');
                $table->text('address')->nullable();
                $table->text('notes')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
                $table->softDeletes();

                // Add indexes for better performance
                $table->index('email');
                $table->index('phone');
                $table->index('status');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
}; 