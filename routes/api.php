<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomTypeController; 

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/obtenerRoomTypes', [RoomTypeController::class, 'index']); 
Route::post('/crearRoomType', [RoomTypeController::class, 'store']); 
