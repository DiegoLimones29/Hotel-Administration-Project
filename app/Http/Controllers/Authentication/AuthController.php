<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Carbon\Carbon;

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

    $throttleKey = Str::lower($credentials['email']).'|'.$request->ip();

    if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
        $seconds = RateLimiter::availableIn($throttleKey);
        return response()->json([
            'message' => "Cuenta bloqueada temporalmente. Intenta de nuevo en {$seconds} segundos"
        ], 429);
    }

    $user = \App\Models\User::where('email', $credentials['email'])->first();

    if (! $user || ! \Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
        RateLimiter::hit($throttleKey, 60);
        return response()->json([
            'message' => 'Credenciales invalidas'
        ], 401);
    }

    RateLimiter::clear($throttleKey);

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

    // Cierre de sesión con invalidación real del token (PDF Módulo 1).
    // Antes el frontend solo borraba el token de localStorage, pero el
    // token seguía siendo válido en el servidor indefinidamente.
    public function apiLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada, token invalidado'
        ], 200);
    }

    // PDF Módulo 1: "Recuperación de contraseña mediante enlace enviado al
    // correo registrado". MAIL_MAILER=log en .env por default: el correo
    // no se envía de verdad, se escribe en storage/logs/laravel.log — útil
    // para demostrar el flujo sin necesitar SMTP configurado.
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        // Por seguridad, respondemos igual exista o no el correo
        // (no revelamos si un email está registrado).
        if (!$user) {
            return response()->json([
                'message' => 'Si el correo existe, se envió un enlace de recuperación'
            ], 200);
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $resetUrl = url('/reset-password') . '?token=' . $token . '&email=' . urlencode($user->email);

        Mail::to($user->email)->send(new ResetPasswordMail($resetUrl));

        return response()->json([
            'message' => 'Si el correo existe, se envió un enlace de recuperación'
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $data['email'])->first();

        if (!$record) {
            return response()->json(['message' => 'Token inválido o expirado'], 400);
        }

        // Expira a los 60 minutos.
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $data['email'])->delete();
            return response()->json(['message' => 'Token inválido o expirado'], 400);
        }

        if (!Hash::check($data['token'], $record->token)) {
            return response()->json(['message' => 'Token inválido o expirado'], 400);
        }

        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        
        DB::table('password_reset_tokens')->where('email', $data['email'])->delete();

        // Por seguridad, invalida todos los tokens de API activos de ese usuario.
        $user->tokens()->delete();

        return response()->json(['message' => 'Contraseña actualizada correctamente'], 200);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate(); 
        $request->session()->regenerateToken(); 

        return redirect()->route('login'); 
    }

}
