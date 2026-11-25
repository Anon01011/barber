<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToSalon;

class LoyaltyTransaction extends Model
{
    use HasFactory, BelongsToSalon;

    protected $table = 'loyalty_transactions';

    protected $fillable = [
        'salon_id',
        'customer_id',
        'pos_sale_id',
        'booking_id',
        'points',
        'type',
        'conversion_rate',
        'discount_amount',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'points' => 'integer',
        'conversion_rate' => 'decimal:4',
        'discount_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class);
    }

    public function posSale(): BelongsTo
    {
        return $this->belongsTo(PosSale::class, 'pos_sale_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDescriptionAttribute(): string
    {
        return $this->notes ?? (ucfirst($this->type) . ' ' . abs($this->points) . ' points');
    }

    public function getBalanceAfterAttribute(): int
    {
        return (int) static::where('customer_id', $this->customer_id)
            ->where('id', '<=', $this->id)
            ->sum('points');
    }
}
