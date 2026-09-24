<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    protected $fillable = [
        'salon_id',
        'type',
        'subject',
        'content',
        'variables',
        'is_active'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get the salon that owns the template.
     */
    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class);
    }

    /**
     * Scope to get template for a specific salon or system default.
     * Returns salon-specific template if exists, otherwise system default.
     */
    public static function forSalon(?int $salonId, string $type): ?self
    {
        // Try to get salon-specific template first
        if ($salonId) {
            $template = self::where('salon_id', $salonId)
                ->where('type', $type)
                ->where('is_active', true)
                ->first();
            
            if ($template) {
                return $template;
            }
        }

        // Fall back to system default (salon_id = null)
        return self::whereNull('salon_id')
            ->where('type', $type)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Render the template with given data.
     */
    public function render(array $data): string
    {
        $content = $this->content;
        
        // Replace variables in format {{variable_name}}
        foreach ($data as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        
        return $content;
    }

    /**
     * Scope for system templates (no salon_id).
     */
    public function scopeSystem($query)
    {
        return $query->whereNull('salon_id');
    }

    /**
     * Scope for salon-specific templates.
     */
    public function scopeForSalonOnly($query, int $salonId)
    {
        return $query->where('salon_id', $salonId);
    }
}
