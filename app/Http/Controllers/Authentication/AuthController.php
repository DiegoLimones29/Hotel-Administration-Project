<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str; 

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

        

        $throttleKey = Str::lower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)){
            $seconds= RateLimiter::availableIn($throttleKey); 
            return response()->json([
                'message' => "Cuenta bloqueada termporalmente. Intenta de nuevo en {$seconds} segundos"
            ], 429);
        }

        if(! Auth::attempt($credentials)){ 
            RateLimiter::hit($throttleKey, 60); 
            return response()->json([
                'message' => 'Credenciales invalidas'
            ], 401); 
        }

        RateLimiter::clear($throttleKey);


        $user= Auth::user(); 
        if($user->role === 'guest'){
            Auth::logout(); 
            $request->session()->invalidate(); 
            $request->session()->regenerateToken(); 

            return response()->json([
                'message' => 'Solo personal del hotel puede acceder al panel'
            ], 403);
            }

        $request->session()->regenerate(); 
        return redirect()->intended('dashboard'); 

    }

    public function apiLogin(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    $user = \App\Models\User::where('email', $credentials['email'])->first();

    if (! $user || ! \Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
        return response()->json([
            'message' => 'Credenciales invalidas'
        ], 401);
    }

    if ($user->role === 'guest') {
        return response()->json([
            'message' => 'Solo personal del hotel puede acceder al panel'
        ], 403);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'message' => 'Login exitoso',
        'user' => $user,
        'token' => $token
    ], 200);
}

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate(); 
        $request->session()->regenerateToken(); 

        return redirect()->route('login'); 
    }

}
