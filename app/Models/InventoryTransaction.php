<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\BelongsToBranch;

class InventoryTransaction extends Model
{
    use HasFactory, BelongsToBranch;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id',
        'item_id',
        'transaction_type',
        'quantity',
        'unit_cost',
        'total_cost',
        'reference_id',
        'reference_type',
        'notes',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($transaction) {
            if (empty($transaction->created_by) && auth()->check()) {
                $transaction->created_by = auth()->id();
            }

            // Calculate total cost if not set
            if (empty($transaction->total_cost) && $transaction->unit_cost !== null) {
                $transaction->total_cost = $transaction->unit_cost * $transaction->quantity;
            }
        });
    }

    /**
     * Get the inventory item that owns the transaction.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    /**
     * Get the user who created the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include purchase transactions.
     */
    public function scopePurchases(Builder $query): Builder
    {
        return $query->where('transaction_type', 'purchase');
    }

    /**
     * Scope a query to only include sale transactions.
     */
    public function scopeSales(Builder $query): Builder
    {
        return $query->where('transaction_type', 'sale');
    }

    /**
     * Scope a query to only include return transactions.
     */
    public function scopeReturns(Builder $query): Builder
    {
        return $query->where('transaction_type', 'return');
    }

    /**
     * Scope a query to only include adjustment transactions.
     */
    public function scopeAdjustments(Builder $query): Builder
    {
        return $query->where('transaction_type', 'adjustment');
    }

    /**
     * Get the transaction type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return ucfirst($this->transaction_type);
    }

    /**
     * Get the transaction direction (in/out).
     */
    public function getDirectionAttribute(): string
    {
        return in_array($this->transaction_type, ['purchase', 'return']) ? 'in' : 'out';
    }

    /**
     * Get the transaction value (positive for in, negative for out).
     */
    public function getValueAttribute(): float
    {
        return $this->direction === 'in'
            ? $this->total_cost
            : -$this->total_cost;
    }
}
