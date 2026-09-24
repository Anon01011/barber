<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToSalon;

class Setting extends Model
{
    use BelongsToSalon;

    /**
     * Allow settings to exist without a salon context (global settings).
     */
    public $allowNullSalon = true;

    protected $fillable = [
        'key',
        'value',
        'type',
        'branch_id',
        'salon_id',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }
}
