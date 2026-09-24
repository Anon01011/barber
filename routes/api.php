<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BookingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('api')->get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::prefix('v1/{salon_slug}')->middleware(['check.salon.slug'])->group(function () {
    // Services API
    Route::get('/services', [App\Http\Controllers\Api\ServiceController::class, 'index']);
});

// Admin API routes
Route::group(['prefix' => 'admin/api', 'middleware' => ['auth', 'api']], function () {
    // Booking routes
    Route::get('/bookings', [BookingController::class, 'getBookings']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::put('/bookings/{id}', [BookingController::class, 'update']);
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);

    // Staff routes
    Route::get('/staff', [BookingController::class, 'getStaff']);
    
    // Customer routes
    Route::get('/customers', [BookingController::class, 'getCustomers']);
    
    // Service routes
    Route::get('/services', [BookingController::class, 'getServices']);
}); 