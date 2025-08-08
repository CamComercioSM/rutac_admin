<?php

use App\Http\Controllers\AdminViewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConvocatoriaController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\CronLogController;
use App\Http\Controllers\DiagnosticosResultadosController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\UnidadProductivaController;
use App\Http\Middleware\ValidateUserMenuAccess;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'index']);
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::as('admin.')
->middleware('auth')
->group(function () {

    Route::get('/dashboard', [AdminViewController::class, 'dashboard'])->name("dashboard");

    Route::middleware(ValidateUserMenuAccess::class)
    ->group(function () {

        Route::get('/unidadesProductivas/list', [UnidadProductivaController::class, 'list']);
        Route::get('/inscriptions/list', [InscriptionController::class, 'list']);
        Route::get('/diagnosticosResultados/list', [DiagnosticosResultadosController::class, 'list']);
        Route::get('/convocatorias/list', [ConvocatoriaController::class, 'list']);

        Route::get('/users/list', [UserController::class, 'list']);
        Route::get('/menu/list', [MenuController::class, 'list']);
        Route::get('/crons/list', [CronController::class, 'list']);
        Route::get('/cronLog/list', [CronLogController::class, 'list']);
    });

    Route::get('/users/export', [UserController::class, 'export']);
    Route::get('/menu/export', [MenuController::class, 'export']);
    Route::get('/inscriptions/export', [InscriptionController::class, 'export']);
    Route::get('/crons/export', [CronController::class, 'export']);
    Route::get('/cronLog/export', [CronLogController::class, 'export']);
    Route::get('/diagnosticosResultados/export', [DiagnosticosResultadosController::class, 'export']);
    Route::get('/unidadesProductivas/export', [UnidadProductivaController::class, 'export']);
    Route::get('/convocatorias/export', [ConvocatoriaController::class, 'export']);

    Route::apiResource('users', UserController::class);
    Route::apiResource('menu', MenuController::class);
    Route::apiResource('inscriptions', InscriptionController::class);
    Route::apiResource('crons', CronController::class);
    Route::apiResource('cronLog', CronLogController::class);
    Route::apiResource('diagnosticosResultados', DiagnosticosResultadosController::class);
    Route::apiResource('unidadesProductivas', UnidadProductivaController::class);
    Route::apiResource('convocatorias', ConvocatoriaController::class);
    
    Route::get('/unidadProductiva/search', [UnidadProductivaController::class, 'search']);
});