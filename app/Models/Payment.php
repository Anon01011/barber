<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToSalon;
use App\Traits\BelongsToBranch;

class Payment extends Model
{
    use HasFactory, BelongsToSalon, BelongsToBranch;

    protected $fillable = [
        'salon_id',
        'branch_id',
        'booking_id',
        'amount',
        'currency',
        'description',
        'type', // income, expense, refund
        'status', // pending, completed, failed, refunded
        'payment_method', // cash, card, bank_transfer, stripe, etc.
        'transaction_id',
        'metadata',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * Relationship with booking (if payment is for a booking)
     * 
     * Note: salon() relationship is provided by BelongsToSalon trait
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Scope for income payments
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope for expense payments
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope for completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment is refundable
     */
    public function isRefundable(): bool
    {
        return $this->isCompleted() && $this->type === 'income' && !$this->isRefunded();
    }

    /**
     * Check if payment is already refunded
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Get formatted amount with currency
     */
    public function getFormattedAmountAttribute(): string
    {
        return strtoupper($this->currency) . ' ' . number_format($this->amount, 2);
    }

    /**
     * Get payment type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'income' => 'Income',
            'expense' => 'Expense',
            'refund' => 'Refund',
            default => ucfirst($this->type),
        };
    }

    /**
     * Get payment status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'Cash',
            'card' => 'Card',
            'bank_transfer' => 'Bank Transfer',
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            default => ucfirst(str_replace('_', ' ', $this->payment_method)),
        };
    }
}
