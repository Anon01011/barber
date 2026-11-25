<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Live Rolling 3-Replica Database Protection: keeps 3 latest healthy snapshots updated at runtime
Schedule::command('db:replica --create')->hourly();
