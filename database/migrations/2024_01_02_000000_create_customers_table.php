<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salon_id')->nullable()->constrained('salons')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->enum('preferred_contact', ['email', 'phone', 'sms'])->default('email');
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->text('medical_notes')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('is_guest')->default(false);
            $table->timestamp('last_visit_at')->nullable();
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Add indexes for better performance
            $table->index('email');
            $table->index('phone');
            $table->index('status');
            $table->index('salon_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};