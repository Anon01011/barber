<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToSalon;
use App\Traits\BelongsToBranch;

class StaffCommission extends Model
{
    use HasFactory, BelongsToSalon, BelongsToBranch;

    protected $fillable = [
        'salon_id',
        'branch_id',
        'staff_id',
        'booking_id',
        'pos_sale_id',
        'item_type',
        'item_id',
        'sale_amount',
        'commission_amount',
        'commission_profile_id',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'sale_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the salon that owns the commission.
     */
    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class);
    }

    /**
     * Get the staff member who earned the commission.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Get the booking associated with this commission.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the POS sale associated with this commission.
     */
    public function posSale(): BelongsTo
    {
        return $this->belongsTo(PosSale::class, 'pos_sale_id');
    }

    /**
     * Get the service associated with this commission (if item_type is service).
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'item_id');
    }

    /**
     * Get the package associated with this commission (if item_type is package).
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'item_id');
    }

    /**
     * Get the membership associated with this commission (if item_type is membership).
     */
    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class, 'item_id');
    }

    /**
     * Get the commission profile used.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(CommissionProfile::class, 'commission_profile_id');
    }

    /**
     * Get the related item.
     */
    public function item()
    {
        return match($this->item_type) {
            'service' => Service::find($this->item_id),
            'product' => Product::find($this->item_id),
            'membership' => Membership::find($this->item_id),
            'package' => Package::find($this->item_id),
            'tip' => $this->posSale,
            default => null,
        };
    }

    /**
     * Scope to pending commissions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to approved commissions.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to paid commissions.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope to commissions for a specific staff member.
     */
    public function scopeForStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    /**
     * Mark commission as approved.
     */
    public function approve()
    {
        $this->update(['status' => 'approved']);
    }

    /**
     * Mark commission as paid.
     */
    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }
}
