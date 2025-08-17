<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Redirige al usuario a Google para autenticación
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Maneja la respuesta de Google después de la autenticación
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            \Log::info('Google OAuth callback para email', ['email' => $googleUser->email]);
            
            // Buscar usuario existente por email
            $user = User::where('email', $googleUser->email)->first();
            
            if (!$user) {
                \Log::info('Creando nuevo usuario para Google OAuth', ['email' => $googleUser->email]);
                // Crear nuevo usuario si no existe
                $user = User::create([
                    'name' => $googleUser->name,
                    'lastname' => '', // Campo requerido por users_admin
                    'email' => $googleUser->email,
                    'password' => Hash::make(Str::random(16)), // Contraseña aleatoria
                    'google_id' => $googleUser->id,
                    'email_verified_at' => now(), // Google ya verificó el email
                    'rol_id' => 1, // Rol por defecto (ajustar según tu sistema)
                    'active' => 1, // Usuario activo por defecto
                ]);
            } else {
                \Log::info('Usuario existente encontrado para Google OAuth', ['email' => $user->email]);
                // Actualizar google_id si no lo tenía
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->id,
                        'email_verified_at' => now(),
                    ]);
                }
            }
            
            // Iniciar sesión
            Auth::login($user);
            
            \Log::info('Login exitoso con Google para usuario', ['name' => $user->name]);
            
            // Redirigir a la ruta correcta
            return redirect()->route('admin.dashboard');
            
        } catch (\Exception $e) {
            \Log::error('Error en Google OAuth: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('login')->with('mensaje', 'Error al autenticarse con Google. Inténtalo de nuevo.');
        }
    }
}
