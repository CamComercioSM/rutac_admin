<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\InscripcionesController;
use App\Http\Controllers\EmpresariosController;
use App\Http\Controllers\ConvocatoriaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API de Correos Electrónicos
Route::prefix('email')->middleware([\App\Http\Middleware\ApiKeyMiddleware::class])->group(function () {
    // Enviar correo de recuperación de contraseña
    Route::post('/password-reset', [EmailController::class, 'sendPasswordReset']);
    
    // Enviar correo personalizado
    Route::post('/custom', [EmailController::class, 'sendCustomEmail']);
    
    // Enviar correo con HTML personalizado desde otra aplicación
    Route::post('/html', [EmailController::class, 'sendHtml']);
    
    
    // Verificar estado del servicio
    Route::get('/health', [EmailController::class, 'healthCheck']);
});

// Ruta de prueba para verificar que la API funciona
Route::get('/test', function () {
    return response()->json([
        'message' => 'API funcionando correctamente',
        'timestamp' => now()->format('Y-m-d H:i:s'),
        'version' => '1.0.0'
    ]);
});

// Obtener convocatorias por programa
Route::get('/convocatorias/by-programa/{programa_id}', [ConvocatoriaController::class, 'getByPrograma']);


