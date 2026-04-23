<?php

namespace App\Http\Controllers;

use App\Exports\UnidadProductivaExport;
use App\Http\Controllers\Controller;
use App\Models\Diagnosticos\ResultadosDiagnostico;
use App\Models\Empresarios\UnidadProductiva;
use App\Models\Empresarios\UnidadProductivaIntervenciones;
use App\Models\Intervenciones\IntervencionCategoria;
use App\Models\Intervenciones\IntervencionIndividual;
use App\Models\Intervenciones\IntervencionTipo;
use App\Models\TablasReferencias\Etapa;
use App\Models\TablasReferencias\Sector;
use App\Models\TablasReferencias\SectorSecciones;
use App\Models\TablasReferencias\UnidadProductivaPersona;
use App\Models\TablasReferencias\UnidadProductivaTamano;
use App\Models\TablasReferencias\UnidadProductivaTipo;
use App\Models\TablasReferencias\Departamento;
use App\Models\TablasReferencias\Municipio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\SICAM32;
use App\Services\WhatsappService;
use App\Models\WhatsappTemplate;
use App\Models\WhatsappMessageLog;

use App\Models\Role;
use App\Models\UnidadProductivaWhatsappLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class UnidadProductivaController extends Controller {
    function list() {
        $data = [
            'etapas' => Etapa::get(),
            'sectores' => Sector::get(),
            'tamanos' => UnidadProductivaTamano::get(),
            'tipoPersona' => UnidadProductivaPersona::get(),
            'unidades' => [],
            'esAsesor' => Auth::user()->rol_id == Role::ASESOR ?  1 : 0
        ];

        return View("unidadesProductivas.index", $data);
    }

    function edit($id, $transformar = null) {
        $data = [
            'cargos' => SICAM32::listadoViculosCargos(),
            'SectorSecciones' => SectorSecciones::with('actividades')->get(),
            'sectores' => Sector::get(),
            'tamanos' => UnidadProductivaTamano::get(),
            'tipoPersona' => UnidadProductivaPersona::get(),
            'tipoUnidad' => UnidadProductivaTipo::get(),
            'departamentos' => Departamento::get(),
            'municipios' => Municipio::get(),
            "elemento" => UnidadProductiva::find($id),
            "api" => "/unidadesProductivas" . ($transformar != null ? "/$id" : ''),
            "accion" => $transformar != null ? "Transformar" : 'Editar'
        ];

        return View("unidadesProductivas.edit", $data);
    }

    function export(Request $request) {
        $query = $this->getQuery($request);
        return Excel::download(new UnidadProductivaExport($query), uniqid('UnidadesProductivas-') . '.xlsx');
    }

    public function index(Request $request) {
        $query = $this->getQuery($request);
        $data = $this->paginate($query, $request);

        return response()->json($data);
    }

    public function show($id) {
        // Si es una petición AJAX, devolver JSON
        if (request()->wantsJson() || request()->ajax()) {
            $unidadProductiva = UnidadProductiva::findOrFail($id);
            return response()->json([
                'id' => $unidadProductiva->unidadproductiva_id,
                'business_name' => $unidadProductiva->business_name,
                'name_legal_representative' => $unidadProductiva->name_legal_representative,
                'contact_person' => $unidadProductiva->contact_person,
                'mobile' => $unidadProductiva->mobile,
                'telephone' => $unidadProductiva->telephone,
                'contact_phone' => $unidadProductiva->contact_phone,
            ]);
        }

        $unidadProductiva = UnidadProductiva::with([
            'etapa',
            'tipoPersona',
            'inscripciones.convocatoria.programa',
            'diagnosticos.etapa',
            'usuario',
            'transformadaDesde',
            'transformadaEn'
        ])->findOrFail($id);

        $diagnostico = $unidadProductiva->diagnosticos->last();

        // Inicializar arrays vacíos por defecto
        $dimensiones = [];
        $resultados = [];

        // Solo procesar resultados si existe un diagnóstico
        if ($diagnostico && $diagnostico->resultado_id) {
            $rows = DB::table('resultados_diagnosticos')
                ->where('resultado_id', $diagnostico->resultado_id)
                ->get(['dimension', 'valor']);

            if ($rows->isNotEmpty()) {
                $dimensiones = $rows->pluck('dimension')->values()->toArray();
                $resultados = $rows->pluck('valor')->map(function ($v) {
                    return round((float)$v, 3);
                })->values()->toArray();
            } else {
                // Fallback: reconstruir desde respuestas si no hay resultados precomputados
                $reconstruidos = DB::table('diagnosticos_respuestas as dr')
                    ->join('diagnosticos_preguntas as dp', 'dp.pregunta_id', '=', 'dr.pregunta_id')
                    ->join('preguntas_dimensiones as pd', 'pd.preguntadimension_id', '=', 'dp.preguntadimension_id')
                    ->where('dr.resultado_id', $diagnostico->resultado_id)
                    ->groupBy('pd.preguntadimension_nombre')
                    ->orderBy('pd.preguntadimension_nombre')
                    ->select([
                        'pd.preguntadimension_nombre as dimension',
                        DB::raw('ROUND(SUM(dp.pregunta_porcentaje/100 * dr.diagnosticorespuesta_valor), 2) as valor')
                    ])->get();

                $dimensiones = $reconstruidos->pluck('dimension')->values()->toArray();
                $resultados = $reconstruidos->pluck('valor')->map(function ($v) {
                    return round((float)$v, 3);
                })->values()->toArray();
            }
        }

        return view(
            'unidadesProductivas.detail',
            [
                'detalle' => $unidadProductiva,
                'dimensions' => $dimensiones,
                'results' => $resultados,
                'esAsesor' => Auth::user()->rol_id == Role::ASESOR ?  1 : 0
            ]
        );
    }

    /**
     * Permite que la unidad productiva pueda realizar un nuevo diagnóstico.
     * Solo pone complete_diagnostic = 0; la nueva fila en diagnosticos_resultados
     * se crea cuando el usuario complete el diagnóstico en el portal.
     */
    public function allowNewDiagnostic($id) {
        $unidad = UnidadProductiva::findOrFail($id);
        $unidad->complete_diagnostic = 0;
        $unidad->save();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Se permitió un nuevo diagnóstico para esta unidad. Los diagnósticos anteriores se mantienen.',
            ]);
        }

        return redirect()->route('admin.unidadesProductivas.show', $id)
            ->with('success', 'Se permitió un nuevo diagnóstico para esta unidad. Los diagnósticos anteriores se mantienen.');
    }

    public function store(Request $request) {
        $entity = UnidadProductiva::findOrFail($request->unidadproductiva_id);
        $entity->update($request->all());

        return response()->json(['message' => 'Stored'], 201);
    }

    public function update($id, Request $request) {
        $current = UnidadProductiva::findOrFail($id);

        // Validación: número de matrícula único al transformar (creación de nueva unidad)
        $rules = [];
        if ($request->filled('registration_number')) {
            $rules['registration_number'] = [
                'string',
                'max:191',
                'unique:unidadesproductivas,registration_number'
            ];
        }

        if (!empty($rules)) {
            $validator = Validator::make($request->all(), $rules, [
                'registration_number.unique' => 'El número de matrícula ya está registrado para otra unidad productiva.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }
        }

        $data = $request->except('unidadproductiva_id');
        $data['transformada_desde'] = $current->unidadproductiva_id;
        $data['complete_diagnostic'] = 0;
        $data['user_id'] = $current->user_id;
        $data['logo'] = UnidadProductiva::getLogo($data['unidadtipo_id']);
        $entity = UnidadProductiva::create($data);

        $current->etapa_intervencion = 'TRANSFORMADA';
        $current->transformada_fecha = now();
        $current->transformada_en = $entity->unidadproductiva_id;
        $current->save();

        return response()->json(['message' => 'Stored'], 201);
    }

    private function getQuery(Request $request) {
        $search = $request->get('search');
        $tipopersona = $request->get('tipopersona');
        $sector = $request->get('sector');
        $tamano = $request->get('tamano');
        $etapa = $request->get('etapa');
        $fecha_inicio = $request->get('fecha_inicio');
        $fecha_fin = $request->get('fecha_fin');

        $query = UnidadProductiva::select([
            'unidadesproductivas.unidadproductiva_id AS id',
            'unidadesproductivas.fecha_creacion',
            'unidadesproductivas.tipo_registro_rutac',
            'unidadesproductivas.business_name',
            'unidadesproductivas.nit',
            'unidadesproductivas.name_legal_representative',
            'unidadesproductivas.registration_email',
            'tp.tipoPersonaNOMBRE as tipo_persona',
            'st.sectorNOMBRE as sector',
            'tm.tamanoNOMBRE as tamano',
            'dp.departamentonombre as departamento',
            'mp.municipionombreoficial as municipio',
            'etapas.name as etapa',
            'va.ventasAnualesNOMBRE as ventas_anuales',
            'va.ventasAnualesINICIO as ventas_inicio',
            'va.ventasAnualesFINAL as ventas_final',
        ])
            ->leftJoin('etapas', 'unidadesproductivas.etapa_id', '=', 'etapas.etapa_id')
            ->leftJoin('unidadesproductivas_personas as tp', 'unidadesproductivas.tipopersona_id', '=', 'tp.tipopersona_id')
            ->leftJoin('ciiu_macrosectores as st', 'unidadesproductivas.sector_id', '=', 'st.sector_id')
            ->leftJoin('unidadesproductivas_tamanos as tm', 'unidadesproductivas.tamano_id', '=', 'tm.tamano_id')
            ->leftJoin('departamentos as dp', 'unidadesproductivas.department_id', '=', 'dp.departamento_id')
            ->leftJoin('municipios as mp', 'unidadesproductivas.municipality_id', '=', 'mp.municipio_id')
            ->leftJoin('ventasanuales as va', 'unidadesproductivas.ventaanual_id', '=', 'va.ventasAnualesID');

        if (!empty($search)) {
            $filterts = ['unidadesproductivas.nit', 'unidadesproductivas.business_name', 'unidadesproductivas.name_legal_representative'];
            $query->where(function ($q) use ($search, $filterts) {
                foreach ($filterts as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        if (!empty($tipopersona)) {
            $query->where('tp.tipopersona_id', $tipopersona);
        }

        if (!empty($sector)) {
            $query->where('st.sector_id', $sector);
        }

        if (!empty($tamano)) {
            $query->where('tm.tamano_id', $tamano);
        }

        if (!empty($etapa)) {
            $query->where('etapas.etapa_id', $etapa);
        }

        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $query->whereBetween('unidadesproductivas.fecha_creacion', [$fecha_inicio, $fecha_fin]);
        } elseif (!empty($fecha_inicio)) {
            $query->whereDate('unidadesproductivas.fecha_creacion', '>=', $fecha_inicio);
        } elseif (!empty($fecha_fin)) {
            $query->whereDate('unidadesproductivas.fecha_creacion', '<=', $fecha_fin);
        }

        return $query;
    }

    function search(Request $request) {
        $busqueda = $request->term;

        $items = UnidadProductiva::where('nit', 'like', "%{$busqueda}%")
            ->orWhere('business_name', 'like', "%{$busqueda}%")
            ->take(10)
            ->get([
                'unidadproductiva_id as id',
                DB::raw("CONCAT(nit, ' - ', business_name) as text")
            ]);

        return response()->json(['results' => $items]);
    }

    public function checkRegistrationNumber(Request $request) {
        $registrationNumber = trim((string) $request->get('registration_number'));
        $ignoreId = $request->get('ignore_id');

        if ($registrationNumber === '') {
            return response()->json(['exists' => false]);
        }

        $query = UnidadProductiva::where('registration_number', $registrationNumber);
        if (!empty($ignoreId)) {
            $query->where('unidadproductiva_id', '!=', $ignoreId);
        }

        return response()->json(['exists' => $query->exists()]);
    }

    public function enviarWhatsApp($id, Request $request) {
        $request->validate([
            'telefono' => 'required|string',
            'phone_type' => 'nullable|string|in:mobile,telephone,contact_phone',
            'mensaje' => 'required|string|max:1000',
            'nombre_empresario' => 'nullable|string|max:255',
        ]);

        $unidadProductiva = UnidadProductiva::findOrFail($id);

        // Normalizar número: solo dígitos y agregar 57 si viene en formato nacional (10 dígitos)
        $rawPhone = $request->telefono;
        $to = preg_replace('/[^0-9]/', '', $rawPhone);
        if (!preg_match('/^57/', $to) && strlen($to) === 10) {
            $to = '57' . $to;
        }

        // 1) Buscar plantilla activa por group_code = mensaje_desde_rutac
        $template = WhatsappTemplate::where('group_code', 'mensaje_desde_rutac')
            ->where('is_active', true)
            ->orderByDesc('id')
            ->first();

        if (! $template) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró una plantilla activa con el grupo "mensaje_desde_rutac".'
            ], 422);
        }

        // 2) Construir los datos para plantillaDatos[...] a partir de expected_fields
        $expected = is_array($template->expected_fields) ? $template->expected_fields : [];
        $templateData = [];

        foreach ($expected as $item) {
            if (!is_array($item) || empty($item['key'])) {
                continue;
            }
            $key = $item['key'];

            switch ($key) {
                case 'usuarioNOMBRE':
                    $templateData[$key] = optional($unidadProductiva->usuario)->name
                        ?? $unidadProductiva->contact_person
                        ?? $unidadProductiva->name_legal_representative
                        ?? '';
                    break;
                case 'empresaRAZONSOCIAL':
                    $templateData[$key] = $unidadProductiva->business_name ?? '';
                    break;
                case 'etapaRUTAC':
                    $templateData[$key] = optional($unidadProductiva->etapa)->name
                        ?? $unidadProductiva->etapa_intervencion
                        ?? '';
                    break;
                case 'mensaje_usuario':
                    // Mensaje que el asesor escribe en el modal
                    $templateData[$key] = $request->mensaje;
                    break;
                case 'nombre_empresario':
                    // Nombre del empresario: el que el usuario eligió en el modal (representante legal o persona de contacto)
                    $templateData[$key] = $request->input('nombre_empresario') ?: ($unidadProductiva->contact_person ?? '');
                    break;
                case 'nombre_asesor':
                    // Nombre del asesor: usuario autenticado (campo name)
                    $templateData[$key] = Auth::user()->name ?? '';
                    break;
                default:
                    // Para claves desconocidas, enviar vacío para no romper el endpoint
                    $templateData[$key] = '';
            }
        }

        $log = UnidadProductivaWhatsappLog::create([
            'unidadproductiva_id' => $unidadProductiva->unidadproductiva_id,
            'user_id' => Auth::id(),
            'phone' => $to,
            'phone_type' => $request->phone_type,
            'message' => $request->mensaje,
            'status' => 'pending',
        ]);

        // 3) Llamar API externa de plantillas
        $apiUrl = rtrim(config('services.whatsapp_templates.api_url'), '/');

        // Formato x-www-form-urlencoded / form-data
        $payload = [
            'whatsappNumber' => $to,
            'plantillaNombre' => $template->name,
            'plantillaGrupo' => $template->group_code,
        ];

        foreach ($templateData as $k => $v) {
            $payload["plantillaDatos[$k]"] = $v;
        }

        // Registrar en whatsapp_message_logs (payload para auditoría)
        $messageLogPayload = [
            'whatsappNumber' => $to,
            'plantillaNombre' => $template->name,
            'plantillaGrupo' => $template->group_code,
            'plantillaDatos' => $templateData,
            'unidadproductiva_id' => $unidadProductiva->unidadproductiva_id,
        ];
        $messageLog = WhatsappMessageLog::create([
            'template_id' => $template->id,
            'user_id' => Auth::id(),
            'phone' => $to,
            'template_name' => $template->name,
            'template_group' => $template->group_code,
            'status' => 'pending',
            'payload' => $messageLogPayload,
        ]);

        try {
            $response = Http::asForm()->post($apiUrl, $payload);

            $success = $response->successful();
            $body = $response->json() ?? $response->body();

            Log::info('Respuesta API plantilla WhatsApp', [
                'url' => $apiUrl,
                'payload' => $payload,
                'status' => $response->status(),
                'body' => $body,
            ]);

            $resultado = [
                'success' => $success,
                'message' => $success ? 'Mensaje enviado correctamente' : 'Error al enviar el mensaje',
                'data' => $success ? $body : null,
                'error' => $success ? null : $body,
            ];
        } catch (\Throwable $e) {
            Log::error('Error al llamar API de plantilla WhatsApp', [
                'error' => $e->getMessage(),
            ]);

            $resultado = [
                'success' => false,
                'message' => 'Error al comunicarse con el servicio de WhatsApp',
                'error' => $e->getMessage(),
            ];
        }

        $log->update([
            'status' => $resultado['success'] ? 'sent' : 'failed',
            'provider_response' => $resultado['data'] ?? $resultado['error'] ?? null,
            'error_message' => $resultado['success'] ? null : ($resultado['message'] ?? 'Error al enviar el mensaje'),
        ]);

        // Actualizar whatsapp_message_logs con resultado del proveedor
        $providerMessageId = null;
        $providerResponse = $resultado['data'] ?? $resultado['error'];
        if (is_array($providerResponse) && isset($providerResponse['contact']['id'])) {
            $providerMessageId = $providerResponse['contact']['id'];
        } elseif (is_array($providerResponse) && isset($providerResponse['contact']['wAid'])) {
            $providerMessageId = $providerResponse['contact']['wAid'];
        }
        $messageLog->update([
            'status' => $resultado['success'] ? 'sent' : 'failed',
            'provider_message_id' => $providerMessageId,
            'provider_response' => is_array($providerResponse) ? $providerResponse : ['raw' => $providerResponse],
            'error_message' => $resultado['success'] ? null : ($resultado['message'] ?? 'Error al enviar el mensaje'),
        ]);

        if ($resultado['success']) {

            $tipoTelefonoLabel = [
                'mobile' => 'Móvil',
                'telephone' => 'Teléfono Fijo',
                'contact_phone' => 'Teléfono de Contacto'
            ][$request->phone_type] ?? 'No especificado';

            $conclusionesEnriquecidas = sprintf(
                "WhatsApp enviado exitosamente por %s. \nDestinatario: %s (%s) \nNúmero: %s \nPlantilla: %s",
                Auth::user()->name,
                $request->nombre_empresario ?? 'No especificado',
                $tipoTelefonoLabel,
                $to,
                $template->name
            );

            IntervencionIndividual::registrarParaUnidad($unidadProductiva->unidadproductiva_id, [
                'categoria_id' => IntervencionCategoria::CATEGORIA_GESTION_PROGRAMAS,
                'tipo_id'      => IntervencionTipo::TIPO_WHATSAPP,
                'descripcion'  => 'Contenido del mensaje: ' . $request->mensaje,
                'conclusiones' => $conclusionesEnriquecidas,
            ]);

            Log::info('WhatsApp enviado', [
                'unidad_productiva_id' => $unidadProductiva->unidadproductiva_id,
                'business_name' => $unidadProductiva->business_name,
                'phone' => $request->telefono,
                'log_id' => $log->id,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Mensaje enviado correctamente a ' . $unidadProductiva->business_name
            ]);
        } else {
            Log::warning('WhatsApp NO enviado', [
                'unidad_productiva_id' => $unidadProductiva->unidadproductiva_id,
                'business_name' => $unidadProductiva->business_name,
                'phone' => $request->telefono,
                'error' => $resultado['message'] ?? 'Error desconocido',
                'log_id' => $log->id,
            ]);
            return response()->json([
                'success' => false,
                'message' => $resultado['message'] ?? 'Error al enviar el mensaje'
            ], 400);
        }
    }
}
