<?php

namespace App\Http\Controllers;

use App\Exports\EmpresariosExport;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class EmpresariosController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    function list()
    { 
        return View("empresarios.index");
    }
    
    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new EmpresariosExport($query), 'empresarios.xlsx');
    }

    public function index(Request $request)
    {
        $query = $this->getQuery($request);
        $data = $this->paginate($query, $request);

        return response()->json( $data );
    }

    public function show($id)
    {
        $result = User::with('unidades')->findOrFail($id);

        return view('empresarios.detail', [ 'detalle' => $result ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        } else {
            unset($data['password']);
        }

        $isNew = !$request->filled('id');

        if ($isNew) {
            // Crear nuevo empresario
            $entity = User::create($data);
            
            // Enviar correo de bienvenida
            try {
                $this->enviarCorreoBienvenida($entity);
            } catch (\Exception $e) {
                Log::error('Error al enviar correo de bienvenida de empresario', [
                    'user_id' => $entity->id,
                    'error' => $e->getMessage()
                ]);
            }
        } 
        else 
        {
            $entity = User::findOrFail($request->id);
            $entity->update($data);
        }

        return response()->json([ 'message' => 'Stored' ], 201);
    }

    /**
     * Enviar correo de bienvenida por registro de empresario
     */
    private function enviarCorreoBienvenida(User $empresario)
    {
        try {
            $this->mailService->sendBienvenidaEmpresario($empresario);
            
            Log::info('Correo de bienvenida de empresario enviado exitosamente', [
                'user_id' => $empresario->id,
                'nombre' => ($empresario->name ?? '') . ' ' . ($empresario->lastname ?? '')
            ]);
        } catch (\Exception $e) {
            Log::error('Error al enviar correo de bienvenida de empresario', [
                'user_id' => $empresario->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // No relanzar la excepción para no interrumpir el flujo
        }
    }

    /**
     * Enviar correo de recuperación de contraseña a un empresario
     */
    public function sendPasswordResetEmail($id)
    {
        try {
            $empresario = User::findOrFail($id);

            if (!$empresario->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'El empresario no tiene un correo electrónico registrado.'
                ], 422);
            }

            Log::info('Enviando correo de recuperación de contraseña para empresario', [
                'user_id' => $empresario->id,
                'email' => $empresario->email
            ]);

            // Usar el sistema de recuperación de contraseña de Laravel
            $status = Password::sendResetLink(
                ['email' => $empresario->email]
            );

            if ($status === Password::RESET_LINK_SENT) {
                Log::info('Correo de recuperación enviado exitosamente', [
                    'user_id' => $empresario->id,
                    'email' => $empresario->email
                ]);

                return response()->json([
                    'success' => true,
                    'message' => '✅ Se ha enviado un correo de recuperación de contraseña a ' . $empresario->email
                ]);
            } else {
                Log::warning('Error al enviar correo de recuperación', [
                    'user_id' => $empresario->id,
                    'status' => $status
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo enviar el correo de recuperación. Inténtalo de nuevo.'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error al enviar correo de recuperación de contraseña', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el correo de recuperación. ' . ($e->getMessage() ?? 'Error desconocido')
            ], 500);
        }
    }

    private function getQuery(Request $request)
    {
        $search = $request->get('search');

        $query = User::whereNull('rol_id');

        if(!empty($search))
        {
            $filterts = ['identification', 'name', 'lastname', 'email'];
            $query->where(function ($q) use ($search, $filterts) {
                foreach ($filterts as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }

}
