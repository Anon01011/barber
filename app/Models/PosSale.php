<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToSalon;
use App\Traits\BelongsToBranch;

class PosSale extends Model
{
    use HasFactory, BelongsToSalon, BelongsToBranch;

    protected $fillable = [
        'salon_id',
        'branch_id',
        'customer_id',
        'employee_id',
        'invoice_number',
        'subtotal',
        'tax',
        'discount',
        'tip',
        'cash_amount',
        'card_amount',
        'online_amount',
        'other_amount',
        'outstanding_amount',
        'total',
        'payment_method',
        'payment_status',
        'status',
        'currency',
        'exchange_rate',
        'notes',
        'voided_by',
        'voided_at',
        'void_reason',
        'tendered_amount',
        'change_amount',
        'refunded_amount',
        'sale_date'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'tip' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'card_amount' => 'decimal:2',
        'online_amount' => 'decimal:2',
        'other_amount' => 'decimal:2',
        'outstanding_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'voided_at' => 'datetime',
        'tendered_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'sale_date' => 'datetime'
    ];

    /**
     * Get the salon that owns the sale.
     */
    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class);
    }

    /**
     * Get the customer that made the purchase.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the employee who processed the sale.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get the user who voided the sale.
     */
    public function voidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    /**
     * Get the items in the sale.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PosSaleItem::class, 'sale_id');
    }

    /**
     * Scope a query to only include paid sales.
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Scope a query to only include pending sales.
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Calculate the total amount including tax, discount, and tip.
     */
    public function calculateTotal(): float
    {
        $total = $this->subtotal + $this->tax - $this->discount + $this->tip;
        return round($total, 2);
    }

    /**
     * Check if the sale is paid.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Get the refunds for the sale.
     */
    public function refunds(): HasMany
    {
        return $this->hasMany(PosRefund::class, 'sale_id');
    }

    /**
     * Get the total refunded amount.
     */
    public function getTotalRefundedAttribute(): float
    {
        return $this->refunds()->sum('amount');
    }

    /**
     * Get the total net refunded amount (after fees).
     */
    public function getTotalNetRefundedAttribute(): float
    {
        return $this->refunds()->sum('net_refund_amount');
    }

    /**
     * Get the total refund fees.
     */
    public function getTotalFeesAttribute(): float
    {
        return $this->refunds()->sum('fee_amount');
    }

    /**
     * Get the formatted total amount.
     */
    public function getFormattedTotalAttribute(): string
    {
        return number_format((float) $this->total, 2) . ' ' . $this->currency;
    }
}
