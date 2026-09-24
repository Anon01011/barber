<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create permissions table if not exists
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 100);       // Reduced length
                $table->string('guard_name', 100); // Reduced length
                $table->timestamps();
                
                $table->unique(['name', 'guard_name']);
            });
        }

        // Create roles table if not exists
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 100);       // Reduced length
                $table->string('guard_name', 100); // Reduced length
                $table->timestamps();
                
                $table->unique(['name', 'guard_name']);
            });
        }

        // Create model_has_permissions table if not exists
        if (!Schema::hasTable('model_has_permissions')) {
            Schema::create('model_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                
                $table->index(['model_id', 'model_type']);
                
                if (Schema::hasTable('permissions')) {
                    $table->foreign('permission_id')
                        ->references('id')
                        ->on('permissions')
                        ->onDelete('cascade');
                }
                
                $table->primary(
                    ['permission_id', 'model_id', 'model_type'],
                    'model_has_permissions_permission_model_type_primary'
                );
            });
        }

        // Create model_has_roles table if not exists
        if (!Schema::hasTable('model_has_roles')) {
            Schema::create('model_has_roles', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                
                $table->index(['model_id', 'model_type']);
                
                if (Schema::hasTable('roles')) {
                    $table->foreign('role_id')
                        ->references('id')
                        ->on('roles')
                        ->onDelete('cascade');
                }
                
                $table->primary(
                    ['role_id', 'model_id', 'model_type'],
                    'model_has_roles_role_model_type_primary'
                );
            });
        }

        // Create role_has_permissions table if not exists
        if (!Schema::hasTable('role_has_permissions')) {
            Schema::create('role_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('role_id');
                
                if (Schema::hasTable('permissions')) {
                    $table->foreign('permission_id')
                        ->references('id')
                        ->on('permissions')
                        ->onDelete('cascade');
                }
                
                if (Schema::hasTable('roles')) {
                    $table->foreign('role_id')
                        ->references('id')
                        ->on('roles')
                        ->onDelete('cascade');
                }
                
                $table->primary(['permission_id', 'role_id']);
            });
        }
    }

    public function down()
    {
        // Don't drop tables in the down method to prevent data loss
        // Schema::dropIfExists('role_has_permissions');
        // Schema::dropIfExists('model_has_roles');
        // Schema::dropIfExists('model_has_permissions');
        // Schema::dropIfExists('roles');
        // Schema::dropIfExists('permissions');
    }
};
