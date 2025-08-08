<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{

    public function index($mensaje = null)
    {
        return View('login', compact('mensaje'));
    }

    public function login(Request $request)
    {
        if (!Auth::attempt([
                'email' => $request->email, 
                'password' => $request->password,
                'active' => true
            ])) {

            return $this->index('Usuario o contraseña no valida.');
        }

        $user = Auth::user();

        // ✅ Obtener los menús según el rol del usuario
        $menus = Menu::whereHas('roles', function ($query) use ($user) {
            $query->where('roles.id', $user->rol_id);
        })->with('children')->get();

        // ✅ Guardar en sesión
        Session::put('user_menu', $menus);

        return redirect()->route('admin.dashboard');
    }

    public function logout()
    {
        Auth::logout();        
        return redirect()->route('login')->with('mensaje', 'Sesión cerrada correctamente.');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users_admin,email',
        ], [
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.exists' => 'No existe una cuenta con este correo electrónico.',
        ]);

        try {
            \Log::info('Intentando enviar correo de recuperación para: ' . $request->email);
            
            $status = Password::sendResetLink(
                $request->only('email')
            );

            \Log::info('Estado del envío: ' . $status);

            if ($status === Password::RESET_LINK_SENT) {
                \Log::info('Correo de recuperación enviado exitosamente para: ' . $request->email);
                return response()->json([
                    'success' => true,
                    'message' => '✅ Se ha enviado un enlace de recuperación a tu correo electrónico. Revisa tu bandeja de entrada y la carpeta de spam.'
                ]);
            } else {
                $errorMessage = '';
                switch ($status) {
                    case Password::INVALID_USER:
                        $errorMessage = 'No existe una cuenta con este correo electrónico.';
                        break;
                    case Password::RESET_THROTTLED:
                        $errorMessage = 'Debes esperar antes de solicitar otro enlace de recuperación.';
                        break;
                    default:
                        $errorMessage = 'No se pudo enviar el enlace de recuperación. Inténtalo de nuevo.';
                }
                
                \Log::warning('Error al enviar correo de recuperación: ' . $status . ' para: ' . $request->email);
                
                return response()->json([
                    'success' => false,
                    'message' => '❌ ' . $errorMessage
                ], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Error en sendResetLink: ' . $e->getMessage(), [
                'email' => $request->email,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '❌ Error al enviar el correo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users_admin,email',
            'password' => 'required|min:8|confirmed',
        ], [
            'token.required' => 'El token es requerido.',
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.exists' => 'No existe una cuenta con este correo electrónico.',
            'password.required' => 'La contraseña es requerida.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            \Log::info('Contraseña restablecida exitosamente para: ' . $request->email);
            return response()->json([
                'success' => true,
                'message' => 'Tu contraseña ha sido restablecida correctamente. Ahora puedes iniciar sesión con tu nueva contraseña.'
            ]);
        } else {
            $errorMessage = '';
            switch ($status) {
                case Password::INVALID_TOKEN:
                    $errorMessage = 'El token de recuperación no es válido o ha expirado.';
                    break;
                case Password::INVALID_USER:
                    $errorMessage = 'No se encontró un usuario con este correo electrónico.';
                    break;
                default:
                    $errorMessage = 'No se pudo restablecer la contraseña. El enlace puede haber expirado.';
            }
            
            \Log::warning('Error al restablecer contraseña: ' . $status . ' para: ' . $request->email);
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 400);
        }
    }

    /**
     * Redirigir al usuario a Google para autenticación
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Manejar el callback de Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Buscar usuario existente
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                // Usuario existe, iniciar sesión
                Auth::login($user);
                
                // Obtener los menús según el rol del usuario
                $menus = Menu::whereHas('roles', function ($query) use ($user) {
                    $query->where('roles.id', $user->rol_id);
                })->select('id', 'label', 'url', 'icon')->get();

                // Guardar en sesión
                Session::put('user_menu', $menus);
                
                return redirect()->route('admin.dashboard');
            } else {
                // Usuario no existe, crear nuevo usuario
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'lastname' => '', // Puedes ajustar esto según tus necesidades
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(16)), // Contraseña aleatoria
                    'rol_id' => 1, // Rol por defecto, ajusta según tu sistema
                    'active' => true,
                ]);

                Auth::login($user);
                
                // Obtener los menús según el rol del usuario
                $menus = Menu::whereHas('roles', function ($query) use ($user) {
                    $query->where('roles.id', $user->rol_id);
                })->select('id', 'label', 'url', 'icon')->get();

                // Guardar en sesión
                Session::put('user_menu', $menus);
                
                return redirect()->route('admin.dashboard');
            }
            
        } catch (\Exception $e) {
            \Log::error('Error en Google OAuth: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Error al iniciar sesión con Google. Inténtalo de nuevo.');
        }
    }
}
