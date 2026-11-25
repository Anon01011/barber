<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\BelongsToSalon;

class StockAdjustment extends Model
{
    use HasFactory, BelongsToSalon;
}
