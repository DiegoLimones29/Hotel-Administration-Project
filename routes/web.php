<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthController; 
use \Illuminate\Support\Facades\Auth; 

Route::get('/', function (){
    return redirect()->route('login');
}); 

Route::get('login', [AuthController::class, 'showLogin'])->name('login');


Route::get('panel', function () {
    return view('panel');
})->name('panel');
