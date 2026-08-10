<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthController; 
use App\Http\Controllers\RoomController;
use App\Http\Controllers\DashboardController;

// 1. Redirección inicial
Route::get('/', function (){
    return redirect()->route('login');
}); 

// 2. Rutas para usuarios no autenticados (Invitados)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login'); 
    Route::post('login', [AuthController::class, 'login']); 
});

// 3. Rutas protegidas para el personal de la Intranet
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Tus rutas administrativas de habitaciones
    Route::get('habitaciones', [RoomController::class, 'index'])->name('rooms.index');
    Route::put('habitaciones/{id}', [RoomController::class, 'update'])->name('rooms.update');
}); // <-- Aquí estaba la llave que faltaba por cerrar
