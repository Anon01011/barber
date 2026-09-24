<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_profile_id')->constrained()->onDelete('cascade');
            $table->enum('item_type', ['service', 'product', 'membership', 'package'])->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('commission_value', 10, 2);
            $table->decimal('target_amount', 10, 2)->nullable();
            $table->timestamps();

            $table->index(['commission_profile_id', 'item_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_rules');
    }
};
