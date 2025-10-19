<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminViewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\HistoriaController;
use App\Http\Controllers\LinkController;
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
use App\Http\Controllers\EmpresariosController;
use App\Http\Controllers\SeccionesController;
use App\Http\Middleware\ValidateUserMenuAccess;


Route::get('/', [AuthController::class, 'index']);
Route::get('/login', [AuthController::class, 'index']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::as('admin.')
->middleware('auth')
->group(function () {
    Route::get('/dashboard', [AdminViewController::class, 'dashboard'])->name("dashboard");

    Route::middleware(ValidateUserMenuAccess::class)
    ->group(function () {

        Route::get('/empresarios/list', [EmpresariosController::class, 'list']);
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
        Route::get('/historias/list', [HistoriaController::class, 'list']);
        Route::get('/links/list', [LinkController::class, 'list']);
        Route::get('/secciones', [SeccionesController::class, 'index']);
    });

    Route::get('/users/export', [UserController::class, 'export']);
    Route::get('/menu/export', [MenuController::class, 'export']);
    Route::get('/inscripciones/export', [InscripcionesController::class, 'export']);
    Route::get('/inscripciones/exportRespuestas', [InscripcionesController::class, 'exportRespuestas']);
    Route::get('/crons/export', [CronController::class, 'export']);
    Route::get('/cronLog/export', [CronLogController::class, 'export']);
    Route::get('/diagnosticosResultados/export', [DiagnosticosResultadosController::class, 'export']);
    Route::get('/diagnosticosResultados/exportRespuestas', [DiagnosticosResultadosController::class, 'exportRespuestas']);
    Route::get('/unidadesProductivas/export', [UnidadProductivaController::class, 'export']);
    Route::get('/programas/export', [ProgramaController::class, 'export']);
    Route::get('/convocatorias/export', [ConvocatoriaController::class, 'export']);
    Route::get('/diagnosticos/export', [DiagnosticosController::class, 'export']);
    Route::get('/diagnosticosPreguntas/export', [DiagnosticosPreguntasController::class, 'export']);
    Route::get('/convocatoriasRequisitos/export', [InscripcionesRequisitosController::class, 'export']);
    Route::get('/capsulas/export', [CapsulasController::class, 'export']);
    Route::get('/banners/export', [BannerController::class, 'export']);
    Route::get('/empresarios/export', [EmpresariosController::class, 'export']);
    Route::get('/historias/export', [HistoriaController::class, 'export']);
    Route::get('/links/export', [LinkController::class, 'export']);

    Route::resource('empresarios', EmpresariosController::class);
    Route::resource('users', UserController::class);
    Route::resource('menu', MenuController::class);
    Route::resource('inscripciones', InscripcionesController::class);
    Route::resource('crons', CronController::class);
    Route::resource('cronLog', CronLogController::class);
    Route::resource('diagnosticosResultados', DiagnosticosResultadosController::class);
    Route::resource('unidadesProductivas', UnidadProductivaController::class);
    Route::resource('programas', ProgramaController::class);
    Route::resource('convocatorias', ConvocatoriaController::class);
    Route::resource('diagnosticos', DiagnosticosController::class);
    Route::resource('diagnosticosPreguntas', DiagnosticosPreguntasController::class);
    Route::resource('convocatoriasRequisitos', InscripcionesRequisitosController::class);
    Route::resource('capsulas', CapsulasController::class);
    Route::resource('banners', BannerController::class);
    Route::resource('historias', HistoriaController::class);
    Route::resource('links', LinkController::class);
    Route::resource('secciones', SeccionesController::class);
    Route::resource('emailTemplates', EmailTemplateController::class);
    Route::resource('defaultEmailTemplates', DefaultEmailTemplateController::class);

    Route::post('/inscripciones/updateRespuesta', [InscripcionesController::class, 'updateRespuesta']);
    
    Route::get('/unidadProductiva/search', [UnidadProductivaController::class, 'search']);
    Route::get('/unidadesProductivas/{id}/{transformar}', [UnidadProductivaController::class, 'edit']);
    
    Route::post('/emailTemplates/toggle-status/{id}', [EmailTemplateController::class, 'toggleStatus'])->name("emailTemplates.toggle-status");
    Route::post('/emailTemplates/send-test', [EmailTemplateController::class, 'sendTestEmail'])->name("emailTemplates.send-test");
});

// Rutas para Google OAuth
Route::get('/auth/google', [App\Http\Controllers\GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [App\Http\Controllers\GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

// Rutas para recuperación de contraseña
Route::post('/forgot-password', [App\Http\Controllers\AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/auth/reset-password/{token}', [App\Http\Controllers\AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/auth/reset-password', [App\Http\Controllers\AuthController::class, 'resetPassword'])->name('password.update');
