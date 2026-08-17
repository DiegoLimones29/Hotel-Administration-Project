<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomTypeController; 
use App\Http\Controllers\RoomController; 
use App\Http\Controllers\Authentication\AuthController; 
use App\Http\Controllers\ReservationController; 
use App\Http\Controllers\ServiceController; 
use App\Http\Controllers\ReservationServiceController;
use App\Http\Controllers\CheckInOutController; 


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/obtenerRoomTypes', [RoomTypeController::class, 'index']); 
Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{id}', [RoomController::class, 'show']);
Route::post('/login', [AuthController::class, 'apiLogin']);


//rutas protegidas por verificacion
Route::middleware(['auth:sanctum', 'staff'])->group(function() {
    Route::post('/crearRoomType', [RoomTypeController::class, 'store']);
    Route::put('/updateRoomType/{id}', [RoomTypeController::class, 'update']);

    Route::post('/rooms', [RoomController::class, 'store']);
    Route::put('/rooms/{id}', [RoomController::class, 'update']);
    Route::patch('/rooms/{id}/fuera-de-servicio', [RoomController::class, 'markOutOfService']);

    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::get('/reservations/disponibilidad', [ReservationController::class, 'availability']);
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::put('/reservations/{id}', [ReservationController::class, 'update']);
    Route::patch('/reservations/{id}/cancelar', [ReservationController::class, 'cancel']);

    Route::get('/checkins/hoy', [CheckInOutController::class, 'todayCheckIns']);
    Route::get('/checkouts/hoy', [CheckInOutController::class, 'todayCheckOuts']);
    Route::post('/checkins/walkin', [CheckInOutController::class, 'walkIn']);
    Route::post('/checkins/{reservationId}', [CheckInOutController::class, 'checkIn']);
    Route::post('/checkouts/{reservationId}', [CheckInOutController::class, 'checkOut']);

       
    Route::get('/services', [ServiceController::class, 'index']);
    Route::post('/services', [ServiceController::class, 'store']);
    Route::put('/services/{id}', [ServiceController::class, 'update']);
    Route::patch('/services/{id}/desactivar', [ServiceController::class, 'deactivate']);

    Route::get('/reservations/{reservationId}/services', [ReservationServiceController::class, 'byReservation']);
     Route::patch('/reservation-services/{id}/estado', [ReservationServiceController::class, 'updateStatus']); 
});

Route::middleware(['auth:sanctum'])->group(function() {
    Route::post('/reservation-services', [ReservationServiceController::class, 'store']);
   
});

