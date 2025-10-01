<?php

namespace App\Http\Controllers;

use App\Exports\InscripcionesExport;
use App\Exports\InscripcionesRespuestasExport;
use App\Http\Controllers\Controller;
use App\Mail\CambioEstadoInscripcionMail;
use App\Models\Inscripciones\ConvocatoriaInscripcion;
use App\Models\Inscripciones\ConvocatoriaRespuesta;
use App\Models\Programas\Programa;
use App\Models\Programas\ProgramaConvocatoria;
use App\Models\Role;
use App\Models\TablasReferencias\InscripcionEstado;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;


class InscripcionesController extends Controller
{
    function list(Request $request)
    { 
        $programas = [];
        $convocatorias = [];

        if(Auth::user()->rol_id == Role::ASESOR)
        {
            $userId = Auth::id();
            $convocatorias = ProgramaConvocatoria::whereHas('asesores', fn($q) => $q->where('user_id', $userId))->get();
            $pgms = $convocatorias->pluck('programa_id');
            $programas = Programa::whereIn('programa_id', $pgms)->get();

            if (!$request->convocatoria) 
            {
                $convocatoria = ProgramaConvocatoria::whereHas('asesores', fn($q) =>
                        $q->where('user_id', $userId)
                    )
                    ->whereDate('fecha_cierre_convocatoria', '>=', Carbon::today())
                    ->orderBy('fecha_cierre_convocatoria', 'asc')
                    ->first();

                if (!$convocatoria) {
                    $convocatoria = ProgramaConvocatoria::whereHas('asesores', fn($q) =>
                            $q->where('user_id', $userId)
                        )
                        ->orderBy('fecha_cierre_convocatoria', 'desc') 
                        ->first();
                }

                if ($convocatoria) {
                    $request->merge([
                        'convocatoria' => $convocatoria->convocatoria_id,
                        'programa' => $convocatoria->programa_id,
                    ]);
                }
            }
        }
        else{
            $convocatorias = ProgramaConvocatoria::all();
            $programas = Programa::all();
        }

        if(!$request->programa && $request->convocatoria)
        {
            $convocatoria = ProgramaConvocatoria::select('programa_id')->find($request->convocatoria);
            $request->merge(['programa' => $convocatoria->programa_id]);
        }
        
        $data = [
            'estados'=> InscripcionEstado::get(),
            'programas'=> $programas,
            'convocatorias'=> $convocatorias,
            'unidades'=> [],
            'filtros'=> $request->all(),
            'esAsesor'=> Auth::user()->rol_id == Role::ASESOR ?  1 : 0
        ];
        
        return View("inscripciones.index", $data);
    }

    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new InscripcionesExport($query), 'inscripciones.xlsx');
    }

    public function store(Request $request)
    {
        $convocatoria = $request->input('convocatoriaAdd');

        foreach($request->input('unidades') as $u)
        {
            $existe = ConvocatoriaInscripcion::
                where('convocatoria_id', $convocatoria)->
                where('unidadproductiva_id', $u)->
                exists();

            if(!$existe)
            {
                $entity = new ConvocatoriaInscripcion();
                $entity->convocatoria_id = $convocatoria;
                $entity->unidadproductiva_id = $u;
                $entity->inscripcionestado_id = 1;
                $entity->comentarios = "Solicitud de registro creada";
                $entity->activarPreguntas = true;
                $entity->save();
            }
        }

        return response()->json([ 'message' => 'Stored' ], 201);
    }

    public function index(Request $request)
    {
        $query = $this->getQuery($request);
        $data = $this->paginate($query, $request);

        return response()->json( $data );
    }

    public function show($id)
    {
        $result = ConvocatoriaInscripcion::with([
            'convocatoria',
            'unidadProductiva'=> function ($q) { $q->with(['etapa', 'sectorUnidad', 'ventaAnual']); },
            'estado',
            'historial',
            'respuestas'
        ])->findOrFail($id);

        $estados = InscripcionEstado::get();

        return view('inscripciones.detail', ['detalle' => $result, 'estados' => $estados]);
    }

    public function update($id, Request $request)
    {
        $entity = ConvocatoriaInscripcion::with(['unidadProductiva', 'estado', 'convocatoria.programa'])->findOrFail($id);
        
        // Guardar el estado anterior para comparar
        $estadoAnterior = $entity->inscripcionestado_id;
        $nuevoEstado = $request->input('inscripcionestado_id');

        // DEBUG: Log del cambio de estado
        Log::info('DEBUG - Cambio de estado de inscripción', [
            'inscripcion_id' => $id,
            'estado_anterior_id' => $estadoAnterior,
            'nuevo_estado_id' => $nuevoEstado,
            'estado_anterior_nombre' => $entity->estado->inscripcionEstadoNOMBRE ?? 'NO ENCONTRADO',
            'cambia_estado' => $estadoAnterior != $nuevoEstado
        ]);

        if($request->hasFile('archivo')) 
        {
            $path = $request->file('archivo')->store('storage/aplications', 'public');
            $entity->archivo = $path;
        }

        $entity->inscripcionestado_id = $nuevoEstado;
        $entity->comentarios = $request->input('comentarios');
        $entity->activarPreguntas = $request->input('activarPreguntas') == 1;
        $entity->save();

        // Enviar correo solo si el estado cambió
        if ($estadoAnterior != $entity->inscripcionestado_id) {
            Log::info('DEBUG - Estado cambió, enviando correo', [
                'inscripcion_id' => $id,
                'estado_anterior' => $estadoAnterior,
                'nuevo_estado' => $entity->inscripcionestado_id
            ]);
            $this->enviarCorreoCambioEstado($entity);
        } else {
            Log::info('DEBUG - Estado no cambió, no se envía correo', [
                'inscripcion_id' => $id,
                'estado' => $estadoAnterior
            ]);
        }

        return response()->json([ 'message' => 'Stored' ], 201);
    }

    /**
     * Enviar correo de notificación de cambio de estado
     */
    private function enviarCorreoCambioEstado(ConvocatoriaInscripcion $inscripcion)
    {
        try {
            // Recargar la inscripción con todas las relaciones necesarias
            $inscripcion->load(['estado', 'unidadProductiva', 'convocatoria.programa']);
            
            // Obtener el email de la unidad productiva
            $email = $inscripcion->unidadProductiva->contact_email ?? 
                     $inscripcion->unidadProductiva->registration_email;

            if (!$email) {
                Log::warning('No se encontró email para la unidad productiva', [
                    'inscripcion_id' => $inscripcion->inscripcion_id,
                    'unidad_productiva_id' => $inscripcion->unidadproductiva_id
                ]);
                return;
            }

            // DEBUG: Log detallado de la información que se va a enviar
            Log::info('DEBUG - Información del correo a enviar', [
                'inscripcion_id' => $inscripcion->inscripcion_id,
                'estado_id' => $inscripcion->inscripcionestado_id,
                'estado_nombre' => $inscripcion->estado->inscripcionEstadoNOMBRE ?? 'NO ENCONTRADO',
                'unidad_productiva' => $inscripcion->unidadProductiva->business_name ?? 'NO ENCONTRADO',
                'email_destino' => $email,
                'convocatoria' => $inscripcion->convocatoria->nombre_convocatoria ?? 'NO ENCONTRADO',
                'programa' => $inscripcion->convocatoria->programa->nombre ?? 'NO ENCONTRADO',
                'comentarios' => $inscripcion->comentarios ?? 'Sin comentarios'
            ]);

            // Enviar el correo con copia a cpc591@gmail.com para verificación
            Mail::to($email)->send(new CambioEstadoInscripcionMail($inscripcion));

            Log::info('Correo de cambio de estado enviado exitosamente', [
                'inscripcion_id' => $inscripcion->inscripcion_id,
                'email' => $email,
                'cc' => 'cpc591@gmail.com',
                'estado' => $inscripcion->estado->inscripcionEstadoNOMBRE ?? 'No especificado'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al enviar correo de cambio de estado', [
                'inscripcion_id' => $inscripcion->inscripcion_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function updateRespuesta(Request $request)
    {
        $entity = ConvocatoriaRespuesta::findOrFail($request->respuestaId);
        $entity->value = $request->valorPregunta;
        $entity->save();

        return response()->json([ 'message' => 'Stored' ], 201);
    }

    private function getQuery(Request $request)
    {
        $search = $request->get('search');
        $programa = $request->get('programa');
        $convocatoria = $request->get('convocatoria');
        $estado = $request->get('estado');
        $unidad = $request->get('unidad');
        $fecha_inicio = $request->get('fecha_inicio');
        $fecha_fin = $request->get('fecha_fin');

        $query = ConvocatoriaInscripcion::select([
                'inscripcion_id AS id',
                'convocatorias_inscripciones.fecha_creacion',
                'pc.nombre_convocatoria',
                'p.nombre as nombre_programa',
                'up.nit',
                'up.business_name',
                'st.sectorNOMBRE as sector',
                'vt.ventasAnualesNOMBRE as ventas',
                'ie.inscripcionEstadoNOMBRE as estado',
                'pc.convocatoria_id'
            ])
            ->join('programas_convocatorias as pc', 'convocatorias_inscripciones.convocatoria_id', '=', 'pc.convocatoria_id')
            ->join('programas as p', 'pc.programa_id', '=', 'p.programa_id')
            ->join('unidadesproductivas as up', 'convocatorias_inscripciones.unidadproductiva_id', '=', 'up.unidadproductiva_id')
            ->leftJoin('ciiu_macrosectores as st', 'up.sector_id', '=', 'st.sector_id')
            ->leftJoin('ventasanuales as vt', 'up.ventaanual_id', '=', 'vt.ventasAnualesID')
            ->join('inscripciones_estados as ie', 'convocatorias_inscripciones.inscripcionestado_id', '=', 'ie.inscripcionestado_id');

        if(!empty($search))
        {
            $filterts = ['pc.nombre_convocatoria','p.nombre','up.nit','up.business_name'];
            $query->where(function ($q) use ($search, $filterts) {
                foreach ($filterts as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        if(!empty($programa)){
            $query->where('p.programa_id', $programa);
        }

        if(!empty($convocatoria)){
            $query->where('pc.convocatoria_id', $convocatoria);
        }

        if(!empty($estado)){
            $query->where('ie.inscripcionestado_id', $estado);
        }

        if(!empty($unidad)){
            $query->where('up.unidadproductiva_id', $unidad);
        }

        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $query->whereBetween('convocatorias_inscripciones.fecha_creacion', [$fecha_inicio, $fecha_fin]);
        } elseif (!empty($fecha_inicio)) {
            $query->whereDate('convocatorias_inscripciones.fecha_creacion', '>=', $fecha_inicio);
        } elseif (!empty($fecha_fin)) {
            $query->whereDate('convocatorias_inscripciones.fecha_creacion', '<=', $fecha_fin);
        }

        if (Auth::user()->rol_id === Role::ASESOR) {
            $userId = Auth::id();

            $convocatoriaIds = ProgramaConvocatoria::whereHas('asesores', fn($q) => 
                $q->where('user_id', $userId)
            )->pluck('convocatoria_id');

            $query->whereIn('pc.convocatoria_id', $convocatoriaIds);
        }

        return $query;
    }

    function exportRespuestas(Request $request)
    { 
        $query = ConvocatoriaRespuesta::select([
                'convocatorias_respuestas.inscripcion_id',
                'convocatorias_respuestas.convocatoriarespuesta_id',
                'convocatorias_respuestas.fecha_creacion',
                'p.requisito_titulo',
                'convocatorias_respuestas.value',
            ])
        ->join('inscripciones_requisitos as p', 'convocatorias_respuestas.requisito_id', '=', 'p.requisito_id');

        $query = $query->where('inscripcion_id', $request->id);

        return Excel::download(new InscripcionesRespuestasExport($query), 'respuestasInscripcion.xlsx');
    }
}
