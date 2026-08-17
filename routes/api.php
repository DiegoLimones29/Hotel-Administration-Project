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
use App\Http\Controllers\ReportController; 


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/obtenerRoomTypes', [RoomTypeController::class, 'index']); 
Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{id}', [RoomController::class, 'show']);
Route::get('/room-types/{roomTypeId}/imagenes', [RoomTypeImageController::class, 'index']);
Route::post('/login', [AuthController::class, 'apiLogin']);


//rutas protegidas por verificacion
Route::middleware(['auth:sanctum'])->group(function() {

    // Solo Administrador: catálogo de habitaciones y tipos (PDF Módulo 2:
    // "Permite al Administrador registrar y administrar el catálogo de habitaciones")
    Route::middleware(['admin'])->group(function () {
        Route::post('/crearRoomType', [RoomTypeController::class, 'store']);
        Route::put('/updateRoomType/{id}', [RoomTypeController::class, 'update']);

        Route::post('/rooms', [RoomController::class, 'store']);
        Route::put('/rooms/{id}', [RoomController::class, 'update']);
        Route::patch('/rooms/{id}/fuera-de-servicio', [RoomController::class, 'markOutOfService']);

        Route::post('/room-types/{roomTypeId}/imagenes', [RoomTypeImageController::class, 'store']);

        // Un huésped define su propia contraseña (vía app móvil). El staff
        // en el panel web solo puede BUSCAR huéspedes existentes, nunca
        // registrar una cuenta nueva con contraseña puesta por alguien más.
        // Se deja disponible solo para admin como excepción operativa
        // (alta manual de emergencia), no como flujo normal de recepción.
        Route::post('/guests', [UserController::class, 'storeGuest']);
        Route::post('/staff', [UserController::class, 'storeStaff']);

        // Módulo 6: "Proporciona al Administrador información consolidada"
        Route::get('/reports/ocupacion', [ReportController::class, 'occupancy']);
        Route::get('/reports/ingresos', [ReportController::class, 'revenue']);
        Route::get('/reports/habitaciones-mas-solicitadas', [ReportController::class, 'mostRequestedRoomTypes']);
        Route::get('/reports/huespedes-frecuentes', [ReportController::class, 'frequentGuests']);
        Route::get('/reports/resumen-dia', [ReportController::class, 'dailySummary']);
        Route::get('/reports/export/csv', [ReportController::class, 'exportCsv']);
        Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf']);
    });

    Route::post('/logout', [AuthController::class, 'apiLogout']);

    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::get('/reservations/disponibilidad', [ReservationController::class, 'availability']);
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::put('/reservations/{id}', [ReservationController::class, 'update']);
    Route::patch('/reservations/{id}/confirmar', [ReservationController::class, 'confirm']);
    Route::patch('/reservations/{id}/cancelar', [ReservationController::class, 'cancel']);

    Route::get('/checkins/hoy', [CheckInOutController::class, 'todayCheckIns']);
    Route::get('/checkouts/hoy', [CheckInOutController::class, 'todayCheckOuts']);
    Route::post('/checkins/walkin', [CheckInOutController::class, 'walkIn']);
    Route::post('/checkins/{reservationId}', [CheckInOutController::class, 'checkIn']);
    Route::post('/checkouts/{reservationId}', [CheckInOutController::class, 'checkOut']);

    // Catálogo de servicios adicionales
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

