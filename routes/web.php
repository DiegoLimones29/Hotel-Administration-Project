<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthController; 
use \Illuminate\Support\Facades\Auth; 

Route::get('/', function (){
    return redirect()->route('login');
}); 

Route::get('login', [AuthController::class, 'showLogin'])->name('login');

Route::get('reset-password', function () {
    return view('auth.reset-password');
})->name('password.reset');

// El panel valida el token en el propio JS (localStorage) y llama a /api/*,
// que sí está protegido por auth:sanctum. Esta ruta solo sirve la plantilla.
Route::get('panel', function () {
    return view('panel');
})->name('panel');
