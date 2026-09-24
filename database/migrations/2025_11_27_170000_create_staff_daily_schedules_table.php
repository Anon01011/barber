<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_daily_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salon_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_working')->default(true);
            $table->boolean('allows_overtime')->default(false);
            $table->time('overtime_start')->nullable();
            $table->time('overtime_end')->nullable();
            $table->timestamps();

            $table->unique(['salon_id', 'staff_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_daily_schedules');
    }
};
