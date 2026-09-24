<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToBranch
{
    /**
     * Boot the BelongsToBranch trait for a model.
     */
    protected static function bootBelongsToBranch(): void
    {
        // Add global scope to filter by current branch
        static::addGlobalScope('branch', function (Builder $query) {
            // Only apply scope if current_branch is set in app container
            if (app()->has('current_branch')) {
                $branch = app('current_branch');
                $query->where(static::getQualifiedBranchIdColumn(), $branch->id);
            }
        });

        // Automatically set branch_id when creating new records
        static::creating(function ($model) {
            if (app()->has('current_branch') && !$model->branch_id) {
                $model->branch_id = app('current_branch')->id;
            }
        });
    }

    /**
     * Get the fully qualified branch_id column.
     */
    public static function getQualifiedBranchIdColumn(): string
    {
        return (new static)->getTable() . '.branch_id';
    }

    /**
     * Get the branch that owns the model.
     */
    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }

    /**
     * Scope a query to include records from all branches.
     */
    public function scopeAllBranches(Builder $query): Builder
    {
        return $query->withoutGlobalScope('branch');
    }

    /**
     * Scope a query to a specific branch.
     */
    public function scopeForBranch(Builder $query, $branchId): Builder
    {
        return $query->withoutGlobalScope('branch')
            ->where(static::getQualifiedBranchIdColumn(), $branchId);
    }
}
