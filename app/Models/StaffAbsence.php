<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToSalon;
use App\Traits\BelongsToBranch;

class StaffAbsence extends Model
{
    use BelongsToSalon, BelongsToBranch;

    protected $fillable = [
        'salon_id',
        'branch_id',
        'staff_id',
        'start_at',
        'end_at',
        'reason'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
