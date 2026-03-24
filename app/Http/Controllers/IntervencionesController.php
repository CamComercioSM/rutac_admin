<?php

namespace App\Http\Controllers;

use App\Exports\IntervencionesExport;
use App\Imports\UnidadProductivaIntervencionesImport;
use App\Http\Controllers\Controller;
use App\Models\Empresarios\UnidadProductivaIntervenciones;
use App\Models\Empresarios\UnidadProductiva;
use App\Models\Intervenciones\IntervencionLead;
use App\Models\Intervenciones\IntervencionUnidad;
use App\Models\Lead;
use App\Models\Programas\FasePrograma;
use App\Models\Programas\Programa;
use App\Models\Programas\ProgramaConvocatoria;
use App\Models\ReporteMensual;
use App\Models\User;
use App\Models\Role;
use App\Models\TablasReferencias\CategoriasIntervenciones;
use App\Models\TablasReferencias\TiposIntervenciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PDF;
use League\CommonMark\CommonMarkConverter;

class IntervencionesController extends Controller {
    function list(Request $request) {
        $data = [
            'programas' => Programa::get(),
            'convocatorias' => ProgramaConvocatoria::get(),
            'categorias' => CategoriasIntervenciones::get(),
            'fasesProgramas' => FasePrograma::get(),
            'tipos' => TiposIntervenciones::get(),
            'modalidades' => UnidadProductivaIntervenciones::$modalidades,
            'asesores' => User::whereNotNull('rol_id')->get(),
            'esAsesor' => Auth::user()->rol_id == Role::ASESOR ?  1 : 0,
            'filtros' => $request->all(),
            'unidades' => [],
            'leads' => [],
        ];

        if ($unidad = $request->get('unidad')) {
            $data['unidades'] = UnidadProductiva::where('unidadproductiva_id', $unidad)->get();
        }

        if ($lead = $request->get('otroParticipante')) {
            $data['leads'] = Lead::where('id', $lead)->get();
        }


        return View("intervenciones.index", $data);
    }

    public function preview(Request $request) {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date',
        ]);

        $data = $this->getInformeData($request);
        $data['analisis_ia'] = $this->llamarApiAnalizarIntervencionesIA($request, $data);


        $reporte = ReporteMensual::create([
            'asesor_id'        => Auth::id(),
            'fecha_generacion' => now(),
            'total_intervenciones' => $data['totalGeneral'] ?? 0,
            'total_unidades' => count($data['porUnidad'] ?? []),
            'conclusiones' => $data['conclusiones'] ?? '',
            'usuario_creo'     => Auth::id(),
            'usuario_actualizo' => Auth::id(),
        ]);

        $data['reporte_id'] = $reporte->id;

        return view('intervenciones.preview', $data);
    }


    public function informe(Request $request) {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date',
        ]);

        $data = $this->getInformeData($request);
        $data['analisis_ia'] = null; //$this->llamarApiAnalizarIntervencionesIA($request, $data);

        $carpeta = public_path('storage/InformesIntervenciones');
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $pdf = PDF::loadView('intervenciones.informe', $data)->setPaper('a4', 'portrait');
        $path = $carpeta . '/informe_' . time() . '.pdf';
        $pdf->save($path);

        return $pdf->stream('informe_intervenciones.pdf');
    }

    public function saveInforme(Request $request) {
        $request->validate([
            'mes'  => 'required|integer',
            'anio' => 'required|integer',
        ]);

        $reporteId = $request->input('reporte_id');
        $asesorId  = Auth::id(); // o $request->asesor si aplica

        // 🔴 VALIDAR DUPLICADO ANTES DE GUARDAR
        $existe = ReporteMensual::where('asesor_id', $asesorId)
            ->where('anio', $request->anio)
            ->where('mes', $request->mes)
            ->whereNull('fecha_eliminacion')
            ->when($reporteId, function ($q) use ($reporteId) {
                $q->where('id', '!=', $reporteId); // 🔥 evitar conflicto consigo mismo
            })
            ->exists();

        if ($existe) {
            return response()->json([
                'message' => 'Ya existe un informe para ese asesor, año y mes.'
            ], 422);
        }

        // 🔹 GENERAR DATA
        $data = $this->getInformeData($request);
        $data['analisis_ia'] = null;

        // 🔹 GENERAR PDF
        $pdf = PDF::loadView('intervenciones.informe', $data)
            ->setPaper('a4', 'portrait');

        $nombre = "informe_" . time() . ".pdf";
        $carpeta = public_path('informes');

        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $ruta = $carpeta . '/' . $nombre;
        $pdf->save($ruta);

        try {

            // 🔹 ACTUALIZAR REPORTE
            ReporteMensual::where('id', $reporteId)->update([
                'anio' => $request->anio,
                'mes' => $request->mes,
                'conclusiones' => $data['conclusiones'] ?? '',
                'estado' => 'PENDIENTE_REVISION',
                'informe_url' => "informes/$nombre",
                'usuario_actualizo' => $asesorId,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {

            // 🔥 SEGURIDAD EXTRA (por si falla la validación previa)
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'message' => 'Ya existe un informe para ese periodo.'
                ], 422);
            }

            throw $e;
        }

        return response()->file($ruta);
    }


    /**
     * Endpoint que devuelve el payload para la API de análisis IA.
     * Útil para que el frontend pueda obtener los datos estructurados.
     */
    public function getPayloadAnalisisIA(Request $request) {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date',
        ]);

        $data = $this->getInformeData($request);
        $payload = self::buildPayloadInformeIntervenciones($request, $data);

        return response()->json([
            'payload' => $payload,
            'api_url' => config('services.analizar_intervenciones_ia.api_url'),
        ]);
    }

    private function getInformeData(Request $request) {
        $fi = $request->input('fecha_inicio') ?? $request->get('fecha_inicio');
        $ff = $request->input('fecha_fin') ?? $request->get('fecha_fin');

        // ----- LISTADO DETALLADO -----
        $query = UnidadProductivaIntervenciones::with([
            'unidadProductiva',
            'lead',
            'asesor',
            'categoria',
            'tipo',
            'fase'
        ])
            ->whereBetween('fecha_inicio', [$fi, $ff])
            ->orderBy('fecha_inicio', 'ASC');

        $asesor = (Auth::user()->rol_id === Role::ASESOR) ? Auth::id() : ($request->input('asesor') ?? $request->get('asesor'));
        if ($asesor) {
            $query->where('asesor_id', $asesor);
        }

        if ($unidad = $request->input('unidad') ?? $request->get('unidad')) {
            $query->where('unidadproductiva_id', $unidad);
        }

        // Aplicar filtros también a las agrupaciones
        $queryAgrupaciones = UnidadProductivaIntervenciones::whereBetween('fecha_inicio', [$fi, $ff]);

        if ($asesor) {
            $queryAgrupaciones->where('asesor_id', $asesor);
        }

        if ($unidad) {
            $queryAgrupaciones->where('unidadproductiva_id', $unidad);
        }

        // ----- AGRUPACIONES -----
        // Conteo por Categoría
        $porCategoria = (clone $queryAgrupaciones)
            ->select('categoria_id', DB::raw('COUNT(*) as total'))
            ->groupBy('categoria_id')
            ->with('categoria')
            ->get();

        // Conteo por Tipo
        $porTipo = (clone $queryAgrupaciones)
            ->select('tipo_id', DB::raw('COUNT(*) as total'))
            ->groupBy('tipo_id')
            ->with('tipo')
            ->get();

        // Conteo por Unidad Productiva
        $porUnidad = (clone $queryAgrupaciones)
            ->select('unidadproductiva_id', DB::raw('COUNT(*) as total'))
            ->groupBy('unidadproductiva_id')
            ->with('unidadProductiva')
            ->get();

        $conclusiones = $request->input('conclusiones') ?? $request->get('conclusiones', '');

        return [
            'inicio' => Carbon::parse($fi)->translatedFormat('Y-m-d H:i'),
            'fin'    => Carbon::parse($ff)->translatedFormat('Y-m-d H:i'),
            'conclusiones' => $conclusiones,
            'intervenciones' => $query->get(),
            'porCategoria' => $porCategoria,
            'porTipo' => $porTipo,
            'porUnidad' => $porUnidad,
            'totalGeneral' => $query->count(),
        ];
    }

    /**
     * Construye el payload para la API analizarIntervencionesIA.
     * Estructura: conclusiones (texto) y estadisticas (resto de campos).
     */
    public static function buildPayloadInformeIntervenciones(Request $request, array $data): array {
        $fi = $request->input('fecha_inicio') ?? $request->get('fecha_inicio');
        $ff = $request->input('fecha_fin') ?? $request->get('fecha_fin');

        $categorias = collect($data['porCategoria'] ?? [])->map(function ($c) {
            return [
                'categoria' => $c->categoria->nombre ?? 'Sin categoría',
                'cantidad'  => (int) $c->total,
            ];
        })->values()->all();

        $tipos = collect($data['porTipo'] ?? [])->map(function ($t) {
            return [
                'tipo'      => $t->tipo->nombre ?? 'Sin tipo',
                'cantidad' => (int) $t->total,
            ];
        })->values()->all();

        $unidades = collect($data['porUnidad'] ?? [])->map(function ($u) {
            return [
                'unidad_productiva' => $u->unidadProductiva?->business_name ?? 'Sin unidad productiva',
                'cantidad'          => (int) $u->total,
            ];
        })->values()->all();

        $estadisticas = [
            'fecha_inicio'            => $fi,
            'fecha_fin'               => $ff,
            'total_intervenciones'    => (int) ($data['totalGeneral'] ?? 0),
            'categorias_intervencion' => $categorias,
            'tipos_intervencion'      => $tipos,
            'unidades_productivas'    => $unidades,
        ];

        // La API hace trim() a todos los parámetros; si recibe un array falla. Enviamos estadisticas como JSON string.
        return [
            'conclusiones' => (string) ($data['conclusiones'] ?? ''),
            'estadisticas' => \json_encode($estadisticas, JSON_UNESCAPED_UNICODE),
        ];
    }

    /**
     * Llama a la API analizarIntervencionesIA con el payload del informe y devuelve el MENSAJE para el reporte.
     */
    private function llamarApiAnalizarIntervencionesIA(Request $request, array $data): ?string {
        $url = config('services.analizar_intervenciones_ia.api_url');
        if (empty($url)) {
            return null;
        }

        $payload = self::buildPayloadInformeIntervenciones($request, $data);

        // Log del payload para debugging (visible en logs de Laravel)
        Log::info('analizarIntervencionesIA: Payload enviado', [
            'url' => $url,
            'payload' => $payload,
        ]);

        try {
            $response = Http::timeout(30)->asJson()->post($url, $payload);

            if (!$response->successful()) {
                Log::warning('analizarIntervencionesIA: respuesta no exitosa', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $body = $response->json();
            // Formato nuevo: { mensaje, tipo, analisis_id } o formato antiguo: { RESPUESTA, MENSAJE }
            $mensaje = $body['mensaje'] ?? $body['MENSAJE'] ?? null;
            if (!empty($mensaje) && is_string($mensaje)) {
                return $this->markdownToHtml($mensaje);
            }

            return null;
        } catch (\Throwable $e) {
            Log::error('analizarIntervencionesIA: error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Convierte texto Markdown a HTML para renderizar correctamente negritas, listas, etc.
     */
    private function markdownToHtml(string $markdown): string {
        try {
            $converter = new CommonMarkConverter([
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]);
            return $converter->convert($markdown)->getContent();
        } catch (\Throwable $e) {
            Log::warning('Error al convertir Markdown a HTML', ['message' => $e->getMessage()]);
            // Fallback: convertir solo negritas básicas si falla CommonMark
            return $this->markdownToHtmlFallback($markdown);
        }
    }

    /**
     * Fallback básico para convertir Markdown simple si CommonMark falla.
     */
    private function markdownToHtmlFallback(string $markdown): string {
        // Convertir **texto** a <strong>texto</strong>
        $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $markdown);
        // Convertir *texto* a <em>texto</em>
        $html = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);
        // Convertir saltos de línea a <br>
        $html = nl2br($html);
        return $html;
    }

    function export(Request $request) {
        $query = $this->getQuery($request);
        return Excel::download(new IntervencionesExport($query), 'Intervenciones.xlsx');
    }

    public function import(Request $request) {
        Excel::import(new UnidadProductivaIntervencionesImport, $request->file('archivo'));

        return back()->with('ok', 'Datos cargados correctamente.');
    }

    public function index(Request $request) {
        $query = $this->getQuery($request);
        $data = $this->paginate($query, $request);

        return response()->json($data);
    }

    public function store(Request $request) {
        $data = $request->all();
        $data['asesor_id'] = Auth::user()->id;

        // Manejo de archivo opcional
        if ($request->hasFile('formFile')) {
            $path = $request->file('formFile')->store('intervenciones', 'public');
            $data['soporte'] = config('app.archivos_url') . $path;
        }

        $unidades = $request->unidades;
        $otrosParticipantes = $request->otrosParticipantes;

        // Decodificar JSON de Tagify solo si tienen contenido
        if (!empty($unidades) && is_string($unidades)) {
            $unidades = json_decode($unidades, true);
        }
        if (!empty($otrosParticipantes) && is_string($otrosParticipantes)) {
            $otrosParticipantes = json_decode($otrosParticipantes, true);
        }

        $intervenciones = [];
        /*
        |--------------------------------------------------------------------------
        | CASO 1: UNIDADES PRODUCTIVAS (REGISTRADOS)
        |--------------------------------------------------------------------------
        */
        if (is_array($unidades) && count($unidades) > 0) {

            foreach ($unidades as $item) {

                $data['unidadproductiva_id'] = $item['value'] ?? null;
                $data['lead_id'] = null;

                $data['participantes'] = (int) preg_replace(
                    '/[^0-9]/',
                    '',
                    $item['participantes'] ?? 0
                );

                $intervencion = UnidadProductivaIntervenciones::create($data);

                // Mantener compatibilidad con tabla intermedia
                IntervencionUnidad::create([
                    'intervencion_id'     => $intervencion->id,
                    'unidadproductiva_id' => $data['unidadproductiva_id'],
                    'participantes'       => $data['participantes'],
                ]);

                $intervenciones[] = $intervencion;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CASO 2: LEADS (NO REGISTRADOS)
        |--------------------------------------------------------------------------
        */
        if (is_array($otrosParticipantes) && count($otrosParticipantes) > 0) {

            foreach ($otrosParticipantes as $l) {

                $data['unidadproductiva_id'] = null;
                $data['lead_id'] = $l['value'];

                $data['participantes'] = 1; // o ajustable si luego lo necesitas

                $intervencion = UnidadProductivaIntervenciones::create($data);

                // Mantener compatibilidad con tabla intermedia
                IntervencionLead::create([
                    'intervencion_id' => $intervencion->id,
                    'lead_id'         => $l['value'],
                    'asesor_id'       => $data['asesor_id'],
                    'tipo_id'         => $data['tipo_id'],
                    'fecha_inicio'    => $data['fecha_inicio'],
                    'fecha_fin'       => $data['fecha_fin'],
                    'descripcion'     => $data['descripcion'],
                ]);

                $intervenciones[] = $intervencion;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CASO 3: SIN PARTICIPANTES
        |--------------------------------------------------------------------------
        */
        if (empty($intervenciones)) {

            $data['unidadproductiva_id'] = null;
            $data['lead_id'] = null;
            $data['participantes'] = 0;

            $intervencion = UnidadProductivaIntervenciones::create($data);
            $intervenciones[] = $intervencion;
        }

        return response()->json(['message' => 'Intervención guardada correctamente'], 201);
    }

    private function getQuery(Request $request) {
        $search = $request->get('search');

        $query = UnidadProductivaIntervenciones::query()
            ->select([
                'unidadesproductivas_intervenciones.*',
                DB::raw("CONCAT(users.name, ' ', users.lastname) as asesor"),
                DB::raw("
                    CASE
                        WHEN unidadesproductivas_intervenciones.unidadproductiva_id IS NOT NULL THEN 'REGISTRADO'
                        WHEN unidadesproductivas_intervenciones.lead_id IS NOT NULL THEN 'NO REGISTRADO'
                        ELSE 'SIN PARTICIPANTE'
                    END as clasificacion
                "),
                DB::raw("
                    CASE
                        WHEN unidadesproductivas_intervenciones.unidadproductiva_id IS NOT NULL THEN unidadesproductivas.business_name
                        WHEN unidadesproductivas_intervenciones.lead_id IS NOT NULL THEN leads.name
                        ELSE NULL
                    END as unidad
                "),
                'fases_programas.nombre as fase',
                'pc.nombre_convocatoria as convocatoria',
                'p.nombre as programa',

                'categorias_intervenciones.nombre as categoria',
                'tipos_intervenciones.nombre as tipo',

                'unidadesproductivas.unidadproductiva_id as unidadproductiva_id_rel',
                'unidadesproductivas.business_name as unidad_nombre',
                'unidadesproductivas.nit as unidad_nit',
                'unidadesproductivas.registration_email as unidad_email',
                'unidadesproductivas.mobile as unidad_telefono',

                'leads.id as lead_id',
                'leads.name as lead_nombre',
                'leads.document as lead_documento',
                'leads.email as lead_email',
                'leads.phone as lead_telefono',

            ])
            ->leftJoin('fases_programas', 'fases_programas.fase_id', '=', 'unidadesproductivas_intervenciones.fase_id')
            ->leftJoin('programas_convocatorias as pc', 'pc.convocatoria_id', '=', 'unidadesproductivas_intervenciones.convocatoria_id')
            ->leftJoin('programas as p', 'p.programa_id', '=', 'unidadesproductivas_intervenciones.programa_id')
            ->join('categorias_intervenciones', 'categorias_intervenciones.id', '=', 'unidadesproductivas_intervenciones.categoria_id')
            ->join('tipos_intervenciones', 'tipos_intervenciones.id', '=', 'unidadesproductivas_intervenciones.tipo_id')
            ->join('users', 'users.id', '=', 'unidadesproductivas_intervenciones.asesor_id')
            ->leftJoin(
                'unidadesproductivas',
                'unidadesproductivas.unidadproductiva_id',
                '=',
                'unidadesproductivas_intervenciones.unidadproductiva_id'
            )
            ->leftJoin(
                'intervencion_unidades',
                'intervencion_unidades.intervencion_id',
                '=',
                'unidadesproductivas_intervenciones.id'
            )
            ->leftJoin(
                'intervencion_leads',
                'intervencion_leads.intervencion_id',
                '=',
                'unidadesproductivas_intervenciones.id'
            )
            ->leftJoin(
                'leads',
                'leads.id',
                '=',
                'unidadesproductivas_intervenciones.lead_id'
            );


        $asesor = (Auth::user()->rol_id === Role::ASESOR) ? Auth::id() : $request->get('asesor');
        if ($asesor) {
            $query->where('unidadesproductivas_intervenciones.asesor_id', $asesor);
        }

        // Filtros básicos
        $this->applyFilter($query, $request, 'unidad', 'unidadesproductivas_intervenciones.unidadproductiva_id');
        $this->applyFilter($query, $request, 'programa', 'unidadesproductivas_intervenciones.programa_id');
        $this->applyFilter($query, $request, 'convocatoria', 'unidadesproductivas_intervenciones.convocatoria_id');
        $this->applyFilter($query, $request, 'fase', 'unidadesproductivas_intervenciones.fase_id');
        $this->applyFilter($query, $request, 'categoria', 'unidadesproductivas_intervenciones.categoria_id');
        $this->applyFilter($query, $request, 'tipo', 'unidadesproductivas_intervenciones.tipo_id');

        if ($fechaInicio = $request->get('fecha_inicio')) {
            $query->whereDate('unidadesproductivas_intervenciones.fecha_inicio', '>=', $fechaInicio);
        }

        if ($fechaFin = $request->get('fecha_fin')) {
            $query->whereDate('unidadesproductivas_intervenciones.fecha_fin', '<=', $fechaFin);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('unidadesproductivas_intervenciones.descripcion', 'like', "%{$search}%")
                    ->orWhere('p.nombre', 'like', "%{$search}%")
                    ->orWhere('pc.nombre_convocatoria', 'like', "%{$search}%")
                    ->orWhere('fases_programas.nombre', 'like', "%{$search}%");
            });
        }

        // Orden dinámico (DataTables + backend propio)
        $sortName = $request->get('sortName');
        $sortOrder = $request->get('sortOrder', 'desc');

        // Mapeo seguro de columnas
        $map = [
            'fecha_creacion' => 'unidadesproductivas_intervenciones.fecha_creacion',
            'id' => 'unidadesproductivas_intervenciones.id',
            'titulo' => 'unidadesproductivas_intervenciones.titulo',
            'fecha_inicio' => 'unidadesproductivas_intervenciones.fecha_inicio',
            'fecha_fin' => 'unidadesproductivas_intervenciones.fecha_fin',
            'asesor' => "CONCAT(users.name, ' ', users.lastname)",
            'unidad' => "
                CASE
                    WHEN unidadesproductivas_intervenciones.unidadproductiva_id IS NOT NULL THEN unidadesproductivas.business_name
                    WHEN unidadesproductivas_intervenciones.lead_id IS NOT NULL THEN leads.name
                    ELSE NULL
                END
            ",
            'clasificacion' => "
                CASE
                    WHEN unidadesproductivas_intervenciones.unidadproductiva_id IS NOT NULL THEN 'REGISTRADO'
                    WHEN unidadesproductivas_intervenciones.lead_id IS NOT NULL THEN 'NO REGISTRADO'
                    ELSE 'SIN PARTICIPANTE'
                END
            ",
            'programa' => 'p.nombre',
            'convocatoria' => 'pc.nombre_convocatoria',
            'fase' => 'fases_programas.nombre',
            'categoria' => 'categorias_intervenciones.nombre',
            'tipo' => 'tipos_intervenciones.nombre',
            'participantes' => 'unidadesproductivas_intervenciones.participantes',
        ];

        if ($sortName && isset($map[$sortName])) {
            $column = $map[$sortName];

            // Forzar RAW para evitar problemas con joins y alias
            $query->orderByRaw($column . ' ' . $sortOrder);
        } else {
            $query->orderBy('unidadesproductivas_intervenciones.fecha_creacion', 'desc');
        }
        return $query;
    }


    private function applyFilter($query, Request $request, $param, $column) {
        $value = $request->get($param);

        if (!is_null($value) && $value !== '') {
            $query->where($column, $value);
        }
    }

    // Eliminar una intervención y sus relaciones (unidades y leads asociados)

    public function destroy($id) {
        try {
            DB::beginTransaction();

            $intervencion = UnidadProductivaIntervenciones::findOrFail($id);

            // Al eliminar la intervención principal, SoftDeletes se encarga si está configurado,
            // pero es buena práctica eliminar las relaciones relacionadas si no son en cascada a nivel DB.
            $intervencion->unidades()->delete(); // Borrado lógico de intervencion_unidades
            $intervencion->leads()->delete();    // Borrado lógico de intervencion_leads
            $intervencion->delete();             // Borrado lógico de la intervención

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'La registro ha sido eliminada correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al intentar eliminar: ' . $e->getMessage()
            ], 500);
        }
    }
}
