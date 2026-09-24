<?php

namespace App\Traits;

use App\Models\Salon;
use App\Scopes\SalonScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToSalon
{
    /**
     * The "booted" method of the model.
     */
    protected static function bootBelongsToSalon()
    {
        static::addGlobalScope(new SalonScope);

        static::creating(function ($model) {
            // Skip auto-assignment for User models (they handle salon_id explicitly)
            if ($model instanceof \App\Models\User) {
                return;
            }

            // Only use app('current_salon') as single source of truth
            // This prevents inconsistencies from multiple fallback sources
            if (app()->bound('current_salon')) {
                $model->salon_id = app()->bound('current_salon') ? app('current_salon')->id : null;
            } elseif (auth()->check() && auth()->user()->salon_id) {
                // Fallback to authenticated user's salon_id
                $model->salon_id = auth()->user()->salon_id;
            } elseif (isset($model->allowNullSalon) && $model->allowNullSalon) {
                // Explicitly allow null salon_id if model property is set
                // This is useful for global settings, roles, etc.
                return;
            } else {
                // If no salon context is available, throw exception
                // This prevents orphaned records with null salon_id
                throw new \Exception('Cannot create model without salon context. Ensure middleware sets current_salon.');
            }
        });
    }

    /**
     * Get the salon that owns the model.
     */
    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class);
    }
}
