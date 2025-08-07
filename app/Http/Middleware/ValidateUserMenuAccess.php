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
        $currentRoute = '/' . ltrim($request->path(), '/');

        if ($currentRoute === 'dashboard') {
            return $next($request);
        }

        // Obtener rutas del menú de la sesión
        $userMenu = session('user_menu', collect());
        
        // Verificar si la ruta existe en el menú principal o en sus hijos
        $hasAccess = collect($userMenu)->some(function ($menu) use ($currentRoute) {
            // Verifica si el menú principal coincide
            if ($menu->url === $currentRoute) { return true; }

            // Verifica en los submenús
            foreach ($menu->submenus as $submenu) {
                if ($submenu->url === $currentRoute) { return true; }
            }

            return false;
        });

        if (!$hasAccess) {
            // Si no tiene acceso, redireccionar o lanzar 403
            abort(403, 'No tienes acceso a esta ruta.');
        }

        return $next($request);
    }

    private function extractUrls($items): array
    {
        $urls = [];

        foreach ($items as $item) {
            if (isset($item['url'])) {
                $urls[] = $item['url'];
            }

            if (isset($item['submenus']) && is_array($item['submenus'])) {
                $urls = array_merge($urls, $this->extractUrls($item['submenus']));
            }
        }

        return $urls;
    }

}
