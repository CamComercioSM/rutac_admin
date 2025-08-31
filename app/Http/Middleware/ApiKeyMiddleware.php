<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key') ?? $request->header('Authorization');
        
        // Si no hay clave de API configurada, permitir acceso
        if (empty(config('app.api_key'))) {
            return $next($request);
        }
        
        // Verificar si la clave de API es válida
        if ($apiKey !== config('app.api_key')) {
            return response()->json([
                'success' => false,
                'message' => 'Clave de API inválida o faltante',
                'error' => 'UNAUTHORIZED'
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        return $next($request);
    }
}
