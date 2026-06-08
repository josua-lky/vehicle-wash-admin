<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PackageController;

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::get( '/packages', [PackageController::class, 'index'] );

Route::middleware('auth:sanctum')
->put('/profile', [
    AuthController::class,
    'updateProfile'
]);
Route::middleware('auth:sanctum')->group(function(){
    

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


Route::middleware('auth:sanctum') ->group(function () { Route::get( '/bookings', [BookingController::class, 'index'] ); Route::post( '/bookings', [BookingController::class, 'store'] ); Route::get( '/bookings/{id}', [BookingController::class, 'show'] ); Route::put( '/bookings/{id}/cancel', [BookingController::class, 'cancel'] ); Route::post( '/bookings/{id}/review', [BookingController::class, 'submitReview'] ); });

use App\Models\Outlet;
use App\Models\Technician;
use App\Models\Promo;

Route::get('/outlets', function() {
    return response()->json(Outlet::where('status', 'active')->get());
});

Route::get('/technicians', function() {
    return response()->json(Technician::where('status', 'active')->get());
});

Route::get('/promos', function() {
    return response()->json(Promo::where('status', 'active')->get());
});