<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomTypeController; 
use App\Http\Controllers\RoomController; 
use App\Http\Controllers\Authentication\AuthController; 
use App\Http\Controllers\ReservationController; 
use App\Http\Controllers\CheckInOutController; 
use App\Http\Controllers\ServiceController; 
use App\Http\Controllers\ReservationServiceController; 
use App\Http\Controllers\UserController; 
use App\Http\Controllers\RoomTypeImageController; 


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/obtenerRoomTypes', [RoomTypeController::class, 'index']); 
Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{id}', [RoomController::class, 'show']);
Route::get('/room-types/{roomTypeId}/imagenes', [RoomTypeImageController::class, 'index']);
Route::post('/login', [AuthController::class, 'apiLogin']);


//rutas protegidas por verificacion
Route::middleware(['auth:sanctum'])->group(function() {

    
    Route::middleware(['admin'])->group(function () {
        Route::post('/crearRoomType', [RoomTypeController::class, 'store']);
        Route::put('/updateRoomType/{id}', [RoomTypeController::class, 'update']);

        Route::post('/rooms', [RoomController::class, 'store']);
        Route::put('/rooms/{id}', [RoomController::class, 'update']);
        Route::patch('/rooms/{id}/fuera-de-servicio', [RoomController::class, 'markOutOfService']);

        Route::post('/room-types/{roomTypeId}/imagenes', [RoomTypeImageController::class, 'store']);

        
        Route::post('/guests', [UserController::class, 'storeGuest']);
        Route::post('/staff', [UserController::class, 'storeStaff']);
    });

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
    Route::patch('/services/{id}/activar', [ServiceController::class, 'activate']);

    // Servicios asignados a la cuenta de un huésped
    Route::post('/reservation-services', [ReservationServiceController::class, 'store']);
    Route::get('/reservations/{reservationId}/services', [ReservationServiceController::class, 'byReservation']);
    Route::patch('/reservation-services/{id}/estado', [ReservationServiceController::class, 'updateStatus']);

    // Búsqueda de huéspedes desde recepción (para reservarles/check-in).
    // El registro de cuenta nueva (POST) es admin-only — ver grupo de arriba.
    Route::get('/guests', [UserController::class, 'guests']);
     
});

