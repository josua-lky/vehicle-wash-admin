<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\TechnicianAppController;

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/technician/login',[TechnicianAppController::class,'login']);

Route::get( '/packages', [PackageController::class, 'index'] );

Route::middleware(['auth:sanctum', 'customer.active'])
->put('/profile', [
    AuthController::class,
    'updateProfile'
]);
Route::middleware(['auth:sanctum', 'customer.active'])->group(function(){
    

    Route::get('/profile',
        [AuthController::class,'profile']);

    Route::get('/profile/address',
        [AuthController::class,'getAddress']);
    Route::put('/profile/address',
        [AuthController::class,'updateAddress']);

    Route::post('/logout',
        [AuthController::class,'logout']);
    Route::post('/change-password',
        [AuthController::class,'changePassword']);
    Route::post('/profile/register-onopay',
        [AuthController::class,'registerOnoPay']);

        Route::get(
            '/vehicles',
            [VehicleController::class, 'index']
        );
    
        Route::post(
            '/vehicles',
            [VehicleController::class, 'store']
        );
    
        Route::put(
            '/vehicles/{vehicle}',
            [VehicleController::class, 'update']
        );
    
        Route::delete(
            '/vehicles/{vehicle}',
            [VehicleController::class, 'destroy']
        );
    

});


Route::middleware(['auth:sanctum', 'customer.active'])->group(function () {
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::put('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
    Route::post('/bookings/{id}/review', [BookingController::class, 'submitReview']);

    // Technician App routes
    Route::put('/technician/profile', [TechnicianAppController::class, 'updateProfile']);
    Route::post('/technician/location', [TechnicianAppController::class, 'updateLocation']);
    Route::get('/technician/bookings', [TechnicianAppController::class, 'bookings']);
    Route::post('/technician/bookings/{id}/status', [TechnicianAppController::class, 'updateStatus']);
    Route::get('/bookings/{id}/chat', [TechnicianAppController::class, 'getChatMessages']);
    Route::post('/bookings/{id}/chat', [TechnicianAppController::class, 'sendChatMessage']);
});

use App\Models\Outlet;
use App\Models\Technician;
use App\Models\Promo;

Route::get('/outlets', function() {
    return response()->json(Outlet::where('status', 'active')->get());
});

Route::get('/technicians', function() {
    return response()->json(Technician::where('status', 'active')
        ->with(['bookings' => function($query) {
            $query->whereNotIn('status', ['cancelled', 'completed'])
                  ->select('id', 'technician_id', 'scheduled_at', 'status');
        }])
        ->get());
});

Route::get('/promos', function() {
    return response()->json(Promo::where('status', 'active')->get());
});