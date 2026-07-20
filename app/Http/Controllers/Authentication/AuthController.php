<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login'); 
    }

    public function login(Request $request)
    {

        $credentials= $request->validate([
            'email' => ['required', 'email'], 
            'password' => ['required'],
        ]); 

        if(Auth::attempt($credentials)){
            
            $request->session()->regenerate(); //duda

            $user = Auth::user(); //obtenemos usuario

            if($user->role === 'guest'){
                Auth::logout();

                return back()->withErrors([
                    'email' => "Solo personal del hotel"
                ]); 
            }

            return redirect()->intended('dashboard'); 
        }

        return back()->withErrors([
            'email' => 'No se encontró el usuario'
        ]);

    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate(); 
        $request->session()->regenerateToken(); 

        return redirect()->route('login'); 
    }

}
