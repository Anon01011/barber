<?php

namespace App\Models;

use App\Traits\BelongsToSalon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PosRefund extends Model
{
    use HasFactory, BelongsToSalon;

    protected $fillable = [
        'salon_id',
        'sale_id',
        'amount',
        'fee_amount',
        'net_refund_amount',
        'reason',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'net_refund_amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function sale()
    {
        return $this->belongsTo(PosSale::class, 'sale_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
