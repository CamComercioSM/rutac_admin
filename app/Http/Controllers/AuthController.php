<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
        })->get();

        // ✅ Guardar en sesión
        Session::put('user_menu', $menus);

        return redirect()->route('admin.dashboard');
    }

    public function logout()
    {
        Auth::logout();        
        return redirect()->route('login')->with('mensaje', 'Sesión cerrada correctamente.');
    }
}
