<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToSalon;
use App\Traits\BelongsToBranch;

class StaffSchedule extends Model
{
    use BelongsToSalon, BelongsToBranch;

    protected $fillable = [
        'salon_id',
        'branch_id',
        'staff_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_working',
        'allows_overtime',
        'overtime_start',
        'overtime_end'
    ];

    protected $casts = [
        'is_working' => 'boolean',
        'allows_overtime' => 'boolean',
        'day_of_week' => 'integer',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
