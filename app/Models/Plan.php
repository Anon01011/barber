<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'business_type',
        'price',
        'duration_in_days',
        'description',
        'features',
        'limits',
        'max_users',
        'max_branches',
        'is_active',
        'is_popular',
        'sort_order',
        'trial_days'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'limits' => 'array',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'duration_in_days' => 'integer',
        'sort_order' => 'integer',
        'trial_days' => 'integer',
        'max_users' => 'integer',
        'max_branches' => 'integer',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Retrieve the model for a bound value with slug support and ID fallback.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if (is_numeric($value)) {
            return static::where('id', $value)->first() 
                ?? static::where('slug', $value)->firstOrFail();
        }

        return static::where('slug', $value)->firstOrFail();
    }

    /**
     * Check if plan has no features assigned
     */
    public function isEmptyFeatures(): bool
    {
        $features = $this->features;
        if (is_string($features)) {
            $features = json_decode($features, true) ?? [];
        }
        return empty($features);
    }

    /**
     * Check if plan has a specific feature
     */
    public function hasFeature(string $feature): bool
    {
        $features = $this->features;
        if (is_string($features)) {
            $features = json_decode($features, true) ?? [];
        }

        $features = (array) ($features ?? []);

        // Handle "All Features" or "All Premium Features" aliases
        if (in_array('All Features', $features) || in_array('All Premium Features', $features)) {
            return true;
        }

        return in_array($feature, $features);
    }

    /**
     * Get limit for a specific feature
     */
    public function getLimit(string $feature): ?int
    {
        // Handle direct columns
        if ($feature === 'max_users') {
            return $this->max_users;
        }
        if ($feature === 'max_branches') {
            return $this->max_branches;
        }

        $limits = $this->limits;
        if (is_string($limits)) {
            $limits = json_decode($limits, true) ?? [];
        }
        return $limits[$feature] ?? null;
    }

    /**
     * Check if plan allows unlimited for a feature
     * Only null is treated as unlimited (not -1)
     */
    public function isUnlimited(string $feature): bool
    {
        return $this->getLimit($feature) === null;
    }

    /**
     * Scope for active plans
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for popular plans
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    /**
     * Scope ordered by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }
}
