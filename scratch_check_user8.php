<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::find(8);
if ($user) {
    echo "User 8 exists: {$user->name}, email: {$user->email}, salon_id: {$user->salon_id}" . PHP_EOL;
    $pins = \App\Models\UserDevicePin::withoutGlobalScopes()->where('user_id', 8)->get();
    echo "Pins for user 8: " . $pins->count() . PHP_EOL;
    foreach ($pins as $p) {
        echo "  Pin ID: {$p->id}, enabled: {$p->pin_enabled}, salon_id: " . var_export($p->salon_id, true) . PHP_EOL;
    }
    $devices = \App\Models\TrustedDevice::withoutGlobalScopes()->where('user_id', 8)->get();
    echo "Devices for user 8: " . $devices->count() . PHP_EOL;
    foreach ($devices as $d) {
        echo "  Device ID: {$d->id}, name: {$d->device_name}, salon_id: " . var_export($d->salon_id, true) . PHP_EOL;
    }
} else {
    echo "User 8 not found." . PHP_EOL;
}

