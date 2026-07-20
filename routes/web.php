<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthController; 
use \Illuminate\Support\Facades\Auth; 

Route::get('/', function (){
    return redirect()->route('login');
}); 

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login'); 

    Route::post('login', [AuthController::class, 'login']); 
});

Route::middleware('auth')->group(function () {

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('dashboard', function () {
        $user = Auth::user();
        return 'Bienvenido al panel de gestion, ' . e($user->name);
    })->name('dashboard');
});