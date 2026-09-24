<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'type',
        'target_salon_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the salon this notification targets
     */
    public function salon()
    {
        return $this->belongsTo(Salon::class, 'target_salon_id');
    }

    /**
     * Scope to get active notifications
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get notifications for a specific salon
     */
    public function scopeForSalon($query, $salonId)
    {
        return $query->where(function($q) use ($salonId) {
            $q->whereNull('target_salon_id')
              ->orWhere('target_salon_id', $salonId);
        });
    }
}
