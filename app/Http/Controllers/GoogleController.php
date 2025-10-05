<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
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
        $googleUser = null;
        
        try {
            $googleUser = Socialite::driver('google')->user();
            
            Log::info('Intento de login con Google', ['email' => $googleUser->getEmail()]);
            
            // Buscar usuario existente con todas las condiciones requeridas
            $user = User::where('email', $googleUser->getEmail())
                ->where('active', 1)
                ->where('rol_id', '>', 0)
                ->first();
            
            if ($user) {
                // Usuario existe y cumple las condiciones, iniciar sesión
                Log::info('Usuario existente encontrado para Google OAuth', ['email' => $user->email]);
                
                // Actualizar google_id si no lo tenía
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->id,
                        'email_verified_at' => now(),
                    ]);
                }
                
                Auth::login($user);
                
                Log::info('Login exitoso con Google', ['user_id' => $user->id, 'email' => $user->email]);
                
                // Construir el menú basado en el rol del usuario
                $this->setMenu();
                
                // Redirigir a la ruta correcta
                return redirect()->route('admin.dashboard');
            } 

            // Usuario no existe o no cumple las condiciones
            Log::warning('Intento de login con Google fallido', [
                'email' => $googleUser->getEmail(),
                'reason' => 'Usuario no encontrado o no cumple condiciones'
            ]);
            
            return redirect()->to('/')->with('mensaje', 'Este correo de Gmail no se encuentra en nuestro sistema. Por favor comuníquese con el administrador.');
            
        } catch (\Exception $e) {
            Log::error('Error en Google OAuth', [
                'error' => $e->getMessage(),
                'email' => $googleUser ? $googleUser->getEmail() : 'No disponible',
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->to('/')->with('mensaje', 'Error en autenticación con Google. Inténtalo de nuevo.');
        }
    }

    /**
     * Construir el menú basado en el rol del usuario autenticado
     */
    private function setMenu()
    {
        $user = Auth::user();

        // Obtener los menús según el rol del usuario
        $menus = Menu::with('children')
            ->whereHas('roles', function ($query) use ($user) {
                $query->where('roles.id', $user->rol_id);
            })
            ->orderBy('order')
            ->get();

        // Agrupar por niveles
        $groupedMenus = $menus
            ->whereNull('parent_id') // Menús principales
            ->map(function ($menu) use ($menus) {
                $menu->submenus = $menus->where('parent_id', $menu->id)->sortBy('order');
                return $menu;
            });

        // Guardar en sesión
        Session::put('user_menu', $groupedMenus);
    }
}
