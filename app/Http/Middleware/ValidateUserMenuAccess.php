<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidateUserMenuAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Si no hay usuario autenticado, negar acceso
        if (!$user) {
            abort(403, 'Acceso no autorizado');
        }

        // Ruta actual
        $currentRoute = ltrim($request->path(), '/');

        if ($currentRoute === 'dashboard') {
            return $next($request);
        }

        // Obtener rutas del menú de la sesión
        $menu = session('user_menu', collect());

        // Extraer solo los URLs permitidos
        $allowedUrls = $menu->pluck('url')->filter()->map(function ($url) {
            return ltrim(parse_url($url, PHP_URL_PATH), '/'); // remover slash inicial
        });

        // Validar acceso
        if (!$allowedUrls->contains($currentRoute)) {
            abort(403, 'No tienes permiso para acceder a esta ruta.');
        }

        return $next($request);
    }
}
