<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2);
            $table->integer('duration_in_days')->default(30);
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('max_users')->default(0);
            $table->integer('max_services')->default(0);
            $table->integer('max_employees')->default(0);
            $table->integer('max_appointments')->default(0);
            $table->integer('max_branches')->default(1);
            $table->json('limits')->nullable();
            $table->integer('trial_days')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('plans');
    }
};
