<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('salons')) {
            Schema::create('salons', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('address')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('country')->nullable();
                $table->string('zip_code')->nullable();
                $table->string('phone', 50)->nullable();
                $table->string('email')->unique();
                $table->string('website')->nullable();
                $table->string('logo')->nullable();
                $table->boolean('status')->default(true);
                $table->string('license_key')->nullable();
                $table->string('subscription_id')->nullable();
                $table->string('subscription_status', 50)->nullable();
                $table->timestamp('trial_ends_at')->nullable();
                $table->timestamp('subscription_ends_at')->nullable();
                $table->json('mail_config')->nullable();
                $table->string('mail_password', 255)->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('salons');
    }
};
