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

    Route::post('/inscripciones/updateRespuesta', [InscripcionesController::class, 'updateRespuesta']);
    
    // Rutas para carga lazy de datos
    Route::get('/dashboard/load-more', [AdminViewController::class, 'loadMoreData'])->name("dashboard.loadMore");
    Route::get('/dashboard/stats', [AdminViewController::class, 'getRealTimeStats'])->name("dashboard.stats");
    
    Route::get('/users/list', [UserController::class, 'index'])->name("users.index");
    Route::get('/users/create', [UserController::class, 'create'])->name("users.create");
    Route::post('/users/store', [UserController::class, 'store'])->name("users.store");
    Route::get('/users/edit/{id}', [UserController::class, 'edit'])->name("users.edit");
    Route::put('/users/update/{id}', [UserController::class, 'update'])->name("users.update");
    Route::delete('/users/delete/{id}', [UserController::class, 'destroy'])->name("users.destroy");
    
    Route::get('/unidadesProductivas/list', [UnidadProductivaController::class, 'index'])->name("unidadesProductivas.index");
    Route::get('/unidadesProductivas/create', [UnidadProductivaController::class, 'create'])->name("unidadesProductivas.create");
    Route::post('/unidadesProductivas/store', [UnidadProductivaController::class, 'store'])->name("unidadesProductivas.store");
    Route::get('/unidadesProductivas/edit/{id}', [UnidadProductivaController::class, 'edit'])->name("unidadesProductivas.edit");
    Route::put('/unidadesProductivas/update/{id}', [UnidadProductivaController::class, 'update'])->name("unidadesProductivas.update");
    Route::delete('/unidadesProductivas/delete/{id}', [UnidadProductivaController::class, 'destroy'])->name("unidadesProductivas.destroy");
    Route::get('/unidadesProductivas/show/{id}', [UnidadProductivaController::class, 'show'])->name("unidadesProductivas.show");
    Route::get('/unidadesProductivas/search', [UnidadProductivaController::class, 'search'])->name("unidadesProductivas.search");
    
    Route::get('/menu/list', [MenuController::class, 'index'])->name("menu.index");
    Route::get('/menu/create', [MenuController::class, 'create'])->name("menu.create");
    Route::post('/menu/store', [MenuController::class, 'store'])->name("menu.store");
    Route::get('/menu/edit/{id}', [MenuController::class, 'edit'])->name("menu.edit");
    Route::put('/menu/update/{id}', [MenuController::class, 'update'])->name("menu.update");
    Route::delete('/menu/delete/{id}', [MenuController::class, 'destroy'])->name("menu.destroy");
    
    Route::get('/crons/list', [CronController::class, 'index'])->name("crons.index");
    Route::get('/crons/create', [CronController::class, 'create'])->name("crons.create");
    Route::post('/crons/store', [CronController::class, 'store'])->name("crons.store");
    Route::get('/crons/edit/{id}', [CronController::class, 'edit'])->name("crons.edit");
    Route::put('/crons/update/{id}', [CronController::class, 'update'])->name("crons.update");
    Route::delete('/crons/delete/{id}', [CronController::class, 'destroy'])->name("crons.destroy");
    
    Route::get('/cronLogs/list', [CronLogController::class, 'index'])->name("cronLogs.index");
    Route::get('/cronLogs/show/{id}', [CronLogController::class, 'show'])->name("cronLogs.show");
    
    Route::get('/diagnosticosResultados/list', [DiagnosticosResultadosController::class, 'index'])->name("diagnosticosResultados.index");
    Route::get('/diagnosticosResultados/show/{id}', [DiagnosticosResultadosController::class, 'show'])->name("diagnosticosResultados.show");
    
    Route::get('/inscriptions/list', [InscriptionController::class, 'index'])->name("inscriptions.index");
    Route::get('/inscriptions/show/{id}', [InscriptionController::class, 'show'])->name("inscriptions.show");
    
    Route::get('/emailTemplates/list', [EmailTemplateController::class, 'index'])->name("emailTemplates.index");
    Route::get('/emailTemplates/create', [EmailTemplateController::class, 'create'])->name("emailTemplates.create");
    Route::post('/emailTemplates/store', [EmailTemplateController::class, 'store'])->name("emailTemplates.store");
    Route::get('/emailTemplates/show/{id}', [EmailTemplateController::class, 'show'])->name("emailTemplates.show");
    Route::get('/emailTemplates/edit/{id}', [EmailTemplateController::class, 'edit'])->name("emailTemplates.edit");
    Route::put('/emailTemplates/update/{id}', [EmailTemplateController::class, 'update'])->name("emailTemplates.update");
    Route::delete('/emailTemplates/delete/{id}', [EmailTemplateController::class, 'destroy'])->name("emailTemplates.destroy");
    Route::post('/emailTemplates/toggle-status/{id}', [EmailTemplateController::class, 'toggleStatus'])->name("emailTemplates.toggle-status");
    Route::post('/emailTemplates/send-test', [EmailTemplateController::class, 'sendTestEmail'])->name("emailTemplates.send-test");
    
    Route::get('/defaultEmailTemplates/list', [DefaultEmailTemplateController::class, 'index'])->name("defaultEmailTemplates.index");
    Route::get('/defaultEmailTemplates/create', [DefaultEmailTemplateController::class, 'create'])->name("defaultEmailTemplates.create");
    Route::post('/defaultEmailTemplates/store', [DefaultEmailTemplateController::class, 'store'])->name("defaultEmailTemplates.store");
    Route::get('/defaultEmailTemplates/edit/{id}', [DefaultEmailTemplateController::class, 'edit'])->name("defaultEmailTemplates.edit");
    Route::put('/defaultEmailTemplates/update/{id}', [DefaultEmailTemplateController::class, 'update'])->name("defaultEmailTemplates.update");
    Route::delete('/defaultEmailTemplates/delete/{id}', [DefaultEmailTemplateController::class, 'destroy'])->name("defaultEmailTemplates.destroy");
});

Route::get('/auth', [App\Http\Controllers\AuthController::class, 'index'])->name("auth.index");
Route::post('/auth', [App\Http\Controllers\AuthController::class, 'login'])->name("auth.login");
Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name("auth.logout");

// Ruta para limpiar cache de sectores
Route::get('/clear-sectores-cache', [App\Http\Controllers\AdminViewController::class, 'clearSectoresCache'])->name("clear.sectores.cache");

// Ruta para debug de sectores
Route::get('/debug-sectores', [App\Http\Controllers\AdminViewController::class, 'debugSectores'])->name("debug.sectores");

// Rutas para Google OAuth
Route::get('/auth/google', [App\Http\Controllers\GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [App\Http\Controllers\GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

// Rutas para recuperación de contraseña
Route::post('/forgot-password', [App\Http\Controllers\AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [App\Http\Controllers\AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\AuthController::class, 'resetPassword'])->name('password.update');