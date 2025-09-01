<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminViewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CapsulasController;
use App\Http\Controllers\ConvocatoriaController;
use App\Http\Controllers\UnidadProductivaController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\CronLogController;
use App\Http\Controllers\DiagnosticosController;
use App\Http\Controllers\DiagnosticosPreguntasController;
use App\Http\Controllers\DiagnosticosResultadosController;
use App\Http\Controllers\InscripcionesController;
use App\Http\Controllers\InscripcionesRequisitosController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\DefaultEmailTemplateController;
use App\Http\Middleware\ValidateUserMenuAccess;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('login');
})->name('login');

Route::as('admin.')
->middleware('auth')
->group(function () {
    Route::get('/dashboard', [AdminViewController::class, 'dashboard'])->name("dashboard");
    Route::get('/consultaExpedienteMercantil', [AdminViewController::class, 'consultaExpedienteMercantil']);

    Route::middleware(ValidateUserMenuAccess::class)
    ->group(function () {

        Route::get('/unidadesProductivas/list', [UnidadProductivaController::class, 'list']);
        Route::get('/inscripciones/list', [InscripcionesController::class, 'list']);
        Route::get('/diagnosticosResultados/list', [DiagnosticosResultadosController::class, 'list']);
        Route::get('/convocatorias/list', [ConvocatoriaController::class, 'list']);
        Route::get('/programas/list', [ProgramaController::class, 'list']);

        Route::get('/users/list', [UserController::class, 'list']);
        Route::get('/menu/list', [MenuController::class, 'list']);
        Route::get('/crons/list', [CronController::class, 'list']);
        Route::get('/cronLog/list', [CronLogController::class, 'list']);

        Route::get('/diagnosticos/list', [DiagnosticosController::class, 'list']);
        Route::get('/diagnosticosPreguntas/list/{id?}', [DiagnosticosPreguntasController::class, 'list']);
        Route::get('/convocatoriasRequisitos/list', [InscripcionesRequisitosController::class, 'list']);
        Route::get('/capsulas/list', [CapsulasController::class, 'list']);
        Route::get('/banners/list', [BannerController::class, 'list']);
    });

    Route::get('/users/export', [UserController::class, 'export']);
    Route::get('/menu/export', [MenuController::class, 'export']);
    Route::get('/inscripciones/export', [InscripcionesController::class, 'export']);
    Route::get('/crons/export', [CronController::class, 'export']);
    Route::get('/cronLog/export', [CronLogController::class, 'export']);
    Route::get('/diagnosticosResultados/export', [DiagnosticosResultadosController::class, 'export']);
    Route::get('/unidadesProductivas/export', [UnidadProductivaController::class, 'export']);
    Route::get('/programas/export', [ProgramaController::class, 'export']);
    Route::get('/convocatorias/export', [ConvocatoriaController::class, 'export']);
    Route::get('/diagnosticos/export', [DiagnosticosController::class, 'export']);
    Route::get('/diagnosticosPreguntas/export', [DiagnosticosPreguntasController::class, 'export']);
    Route::get('/convocatoriasRequisitos/export', [InscripcionesRequisitosController::class, 'export']);
    Route::get('/capsulas/export', [CapsulasController::class, 'export']);
    Route::get('/banners/export', [BannerController::class, 'export']);

    Route::apiResource('users', UserController::class);
    Route::apiResource('menu', MenuController::class);
    Route::apiResource('inscripciones', InscripcionesController::class);
    Route::apiResource('crons', CronController::class);
    Route::apiResource('cronLog', CronLogController::class);
    Route::apiResource('diagnosticosResultados', DiagnosticosResultadosController::class);
    Route::apiResource('unidadesProductivas', UnidadProductivaController::class);
    Route::apiResource('programas', ProgramaController::class);
    Route::apiResource('convocatorias', ConvocatoriaController::class);
    Route::apiResource('diagnosticos', DiagnosticosController::class);
    Route::apiResource('diagnosticosPreguntas', DiagnosticosPreguntasController::class);
    Route::apiResource('convocatoriasRequisitos', InscripcionesRequisitosController::class);
    Route::apiResource('capsulas', CapsulasController::class);
    Route::apiResource('banners', BannerController::class);
    Route::apiResource('emailTemplates', EmailTemplateController::class);
    Route::apiResource('defaultEmailTemplates', DefaultEmailTemplateController::class);

    Route::post('/inscripciones/updateRespuesta', [InscripcionesController::class, 'updateRespuesta']);
    
    // Rutas para carga lazy de datos
    Route::get('/dashboard/load-more', [AdminViewController::class, 'loadMoreData'])->name("dashboard.loadMore");
    Route::get('/dashboard/stats', [AdminViewController::class, 'getRealTimeStats'])->name("dashboard.stats");
    
    // Las rutas de users están manejadas por Route::apiResource('users', UserController::class)
    
    // Las rutas de unidadesProductivas están manejadas por Route::apiResource('unidadesProductivas', UnidadProductivaController::class)
    // Ruta adicional para búsqueda
    Route::get('/unidadProductiva/search', [UnidadProductivaController::class, 'search']);
    
    // Las rutas de menu están manejadas por Route::apiResource('menu', MenuController::class)
    
    // Las rutas de crons están manejadas por Route::apiResource('crons', CronController::class)
    
    // Las rutas de cronLog están manejadas por Route::apiResource('cronLog', CronLogController::class)
    
    // Las rutas de diagnosticosResultados están manejadas por Route::apiResource('diagnosticosResultados', DiagnosticosResultadosController::class)
    
    // Las rutas de inscriptions están manejadas por Route::apiResource('inscripciones', InscripcionesController::class)
    
    // Las rutas básicas de emailTemplates están manejadas por Route::apiResource('emailTemplates', EmailTemplateController::class)
    // Rutas adicionales específicas
    Route::post('/emailTemplates/toggle-status/{id}', [EmailTemplateController::class, 'toggleStatus'])->name("emailTemplates.toggle-status");
    Route::post('/emailTemplates/send-test', [EmailTemplateController::class, 'sendTestEmail'])->name("emailTemplates.send-test");
    
    // Las rutas de defaultEmailTemplates están manejadas por Route::apiResource('defaultEmailTemplates', DefaultEmailTemplateController::class)
});

Route::get('/auth', [App\Http\Controllers\AuthController::class, 'index'])->name("auth.index");
Route::post('/auth', [App\Http\Controllers\AuthController::class, 'login'])->name("auth.login");
Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name("auth.logout");


// Rutas para Google OAuth
Route::get('/auth/google', [App\Http\Controllers\GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [App\Http\Controllers\GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

// Rutas para recuperación de contraseña
Route::post('/forgot-password', [App\Http\Controllers\AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [App\Http\Controllers\AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\AuthController::class, 'resetPassword'])->name('password.update');