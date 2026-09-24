<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToSalon;

class CustomerPackageBalance extends Model
{
    use HasFactory, BelongsToSalon;

    protected $fillable = [
        'salon_id',
        'customer_id',
        'pos_sale_id',
        'package_id',
        'service_id',
        'quantity_remaining',
        'payment_status',
        'expiry_date',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }

    public function sale()
    {
        return $this->belongsTo(PosSale::class, 'pos_sale_id');
    }
}
