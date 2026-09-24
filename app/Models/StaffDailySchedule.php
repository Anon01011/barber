<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToSalon;
use App\Traits\BelongsToBranch;

class StaffDailySchedule extends Model
{
    use BelongsToSalon, BelongsToBranch;

    protected $fillable = [
        'salon_id',
        'branch_id',
        'staff_id',
        'date',
        'start_time',
        'end_time',
        'is_working',
        'allows_overtime',
        'overtime_start',
        'overtime_end'
    ];

    protected $casts = [
        'date' => 'date',
        'is_working' => 'boolean',
        'allows_overtime' => 'boolean',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
