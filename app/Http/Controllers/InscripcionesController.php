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
use App\Models\Empresarios\UnidadProductiva;
use App\Models\Empresarios\UnidadProductivaIntervenciones;
use App\Models\Inscripciones\ConvocatoriaInscripcionHistorial;
use App\Services\MailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;


class InscripcionesController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

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

        if ($unidad = $request->get('unidad')) {
            $data['unidades'] = UnidadProductiva::where('unidadproductiva_id', $unidad)->get();
        }
        
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
        $asesor = Auth::user();

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

                // Enviar correo de bienvenida por inscripción
                try {
                    // Cargar relaciones necesarias para el correo
                    $entity->load(['unidadProductiva', 'convocatoria.programa']);
                    $this->enviarCorreoInscripcion($entity, $asesor);
                } catch (\Exception $e) {
                    // Log del error pero no fallar la creación de la inscripción
                    Log::error('Error al enviar correo de inscripción', [
                        'inscripcion_id' => $entity->inscripcion_id,
                        'unidad_productiva_id' => $u,
                        'error' => $e->getMessage()
                    ]);
                }
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
        // Validar que se haya seleccionado un estado válido
        $validator = Validator::make($request->all(), [
            'inscripcionestado_id' => [
                'required',
                'integer',
                'exists:inscripciones_estados,inscripcionestado_id',
            ],
        ], [
            'inscripcionestado_id.required' => 'El campo estado es obligatorio. Debe seleccionar un estado válido.',
            'inscripcionestado_id.integer' => 'El estado seleccionado no es válido.',
            'inscripcionestado_id.exists' => 'El estado seleccionado no existe en el sistema.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $path = null;

        if($request->hasFile('archivo')) 
        {
            $path = $request->file('archivo')->store('storage/aplications', 'archivos');
            $path = Storage::disk('archivos')->url($path);
        }

        foreach($request->inscripciones as $ins_id)
        {
            $entity = ConvocatoriaInscripcion::with(['unidadProductiva', 'estado', 'convocatoria.programa'])
                ->findOrFail($ins_id);
            
            // Guardar valores anteriores para comparar
            $estadoAnterior = $entity->inscripcionestado_id;
            $activarPreguntasAnterior = $entity->activarPreguntas;
            
            $entity->archivo = $path;            
            $entity->inscripcionestado_id = $request->input('inscripcionestado_id');
            $entity->comentarios = $request->input('comentarios');
            $entity->activarPreguntas = $request->input('activarPreguntas') == 1;
            $entity->save();

            // Enviar correo si el estado cambió O si activarPreguntas cambió
            $cambioEstado = ($estadoAnterior != $entity->inscripcionestado_id);
            $cambioActivarPreguntas = ($activarPreguntasAnterior != $entity->activarPreguntas);
            
            if ($cambioEstado || $cambioActivarPreguntas) {
                // Recargar la inscripción con las relaciones actualizadas para obtener el nuevo estado
                $entity->refresh();
                $entity->load(['unidadProductiva', 'estado', 'convocatoria.programa']);
                $this->enviarCorreoCambioEstado($entity);
            }

            $this->intervencion($entity);
        }

        return response()->json([ 'message' => 'Stored' ], 201);
    }

    /**
     * Enviar correo de bienvenida por inscripción a programa
     */
    private function enviarCorreoInscripcion(ConvocatoriaInscripcion $inscripcion, $asesor)
    {
        try {
            $this->mailService->sendInscripcionPrograma($inscripcion, $asesor);
            
            Log::info('Correo de inscripción enviado exitosamente', [
                'inscripcion_id' => $inscripcion->inscripcion_id,
                'unidad_productiva_id' => $inscripcion->unidadproductiva_id,
                'programa' => $inscripcion->convocatoria->programa->nombre ?? 'N/A'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al enviar correo de inscripción', [
                'inscripcion_id' => $inscripcion->inscripcion_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // No relanzar la excepción para no interrumpir el flujo
        }
    }

    /**
     * Enviar correo de notificación de cambio de estado
     */
    private function enviarCorreoCambioEstado(ConvocatoriaInscripcion $inscripcion)
    {
        try {
            // Email principal: registration_email; CC: contact_email
            $to = $inscripcion->unidadProductiva->registration_email;
            $cc = $inscripcion->unidadProductiva->contact_email;

            if (!$to && !$cc) {
                Log::warning('No se encontró email para la unidad productiva', [
                    'inscripcion_id' => $inscripcion->inscripcion_id,
                    'unidad_productiva_id' => $inscripcion->unidadproductiva_id
                ]);
                return;
            }

            $mailable = new CambioEstadoInscripcionMail($inscripcion);
            if ($to && $cc && strcasecmp($to, $cc) !== 0) {
                Mail::to($to)->cc($cc)->send($mailable);
            } elseif ($to) {
                Mail::to($to)->send($mailable);
            } else { // fallback: solo contacto si no hay registration_email
                Mail::to($cc)->send($mailable);
            }

            Log::info('Correo de cambio de estado enviado exitosamente', [
                'inscripcion_id' => $inscripcion->inscripcion_id,
                'to' => $to,
                'cc' => $cc,
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

    public function intervencion($inscripcion)
    {
        // Crear intervención solo si el estado es Admitido (3) o No Admitido (4)
        if($inscripcion->inscripcionestado_id == 3 || $inscripcion->inscripcionestado_id == 4)
        {
            $fecha_fin = ConvocatoriaInscripcionHistorial::where('inscripcion_id', $inscripcion->inscripcion_id)
                ->where('inscripcionestado_id', $inscripcion->inscripcionestado_id)
                ->orderBy('fecha_creacion', 'desc')
                ->value('fecha_creacion');

            $data = [
                'asesor_id' => Auth::user()->id,
                'unidadproductiva_id' => $inscripcion->unidadproductiva_id,
                'descripcion' => 'Intervención generada automáticamente tras cambio de estado de inscripción.',
                'fecha_inicio' => $fecha_fin,
                'fecha_fin' => Carbon::now(),
                'categoria_id' => 1,
                'tipo_id' => 1,
                'referencia_id' => $inscripcion->convocatoria_id,
                'modalidad' => 'Virtual',
                'participantes' => 1,
                'conclusiones' => 'Intervención generada automáticamente tras cambio de estado de inscripción.',
            ];

            UnidadProductivaIntervenciones::create($data);
        }
    }

}
