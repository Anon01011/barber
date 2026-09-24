<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SalonScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model)
    {
        // Skip scope for User model to prevent infinite loop
        if ($model instanceof \App\Models\User) {
            return;
        }

        // Skip scope if user is Super Admin AND we are not in a specific salon context
        // We use a direct DB query here to avoid triggering Eloquent scopes recursively
        if (auth()->check()) {
            static $isSuperAdminCache = [];
            $userId = auth()->id();

            if (!isset($isSuperAdminCache[$userId])) {
                $isSuperAdminCache[$userId] = \Illuminate\Support\Facades\DB::table('model_has_roles')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->where('model_has_roles.model_id', $userId)
                    ->where('model_has_roles.model_type', get_class(auth()->user()))
                    ->where('roles.name', 'super_admin')
                    ->exists();
            }
            $isSuperAdmin = $isSuperAdminCache[$userId];

            if ($isSuperAdmin && !app()->bound('current_salon')) {
                return;
            }
        }

        if (app()->bound('current_salon') && app('current_salon')) {
            $builder->where(function ($q) use ($model) {
                $q->where($model->getTable() . '.salon_id', app('current_salon')->id);
                if ($model instanceof \App\Models\Role || (isset($model->allowNullSalon) && $model->allowNullSalon)) {
                    $q->orWhereNull($model->getTable() . '.salon_id');
                }
            });
        } elseif (auth()->check() && auth()->user()->salon_id) {
            // Fallback to authenticated user's salon_id only
            // Session fallback removed for security
            $builder->where(function ($q) use ($model) {
                $q->where($model->getTable() . '.salon_id', auth()->user()->salon_id);
                if ($model instanceof \App\Models\Role || (isset($model->allowNullSalon) && $model->allowNullSalon)) {
                    $q->orWhereNull($model->getTable() . '.salon_id');
                }
            });
        }
    }
}
