<?php

namespace App\Http\Controllers;

use App\Exports\IntervencionesExport;
use App\Imports\UnidadProductivaIntervencionesImport;
use App\Http\Controllers\Controller;
use App\Models\Empresarios\UnidadProductivaIntervenciones;
use App\Models\Empresarios\UnidadProductiva;
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
use PDF;
use League\CommonMark\CommonMarkConverter;

class IntervencionesController extends Controller
{
    function list(Request $request)
    { 
        $data = [ 
            'categorias'=> CategoriasIntervenciones::get(),
            'tipos'=> TiposIntervenciones::get(),
            'modalidades'=> UnidadProductivaIntervenciones::$modalidades,
            'asesores' => User::whereNotNull('rol_id')->get(),
            'esAsesor'=> Auth::user()->rol_id == Role::ASESOR ?  1 : 0,
            'filtros'=> $request->all(),
            'unidades'=> []
        ];

        if ($unidad = $request->get('unidad')) {
            $data['unidades'] = UnidadProductiva::where('unidadproductiva_id', $unidad)->get();
        }

        return View("intervenciones.index", $data);
    }

    public function preview(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date',
        ]);

        $data = $this->getInformeData($request);
        // No llamamos a la API aquí, se hará desde el frontend para poder verlo en Network
        $data['analisis_ia'] = null;
        $data['api_url'] = config('services.analizar_intervenciones_ia.api_url');

        return view('intervenciones.preview', $data);
    }

    /**
     * Endpoint que devuelve el payload para la API de análisis IA.
     * Útil para que el frontend pueda obtener los datos estructurados.
     */
    public function getPayloadAnalisisIA(Request $request)
    {
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

    public function informe(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date',
        ]);

        $data = $this->getInformeData($request);
        $data['analisis_ia'] = $this->llamarApiAnalizarIntervencionesIA($request, $data);

        $pdf = PDF::loadView('intervenciones.informe', $data)->setPaper('a4', 'portrait');

        return $pdf->stream('informe_intervenciones.pdf');
    }

    private function getInformeData(Request $request)
    {
        $fi = $request->input('fecha_inicio') ?? $request->get('fecha_inicio');
        $ff = $request->input('fecha_fin') ?? $request->get('fecha_fin');

        // ----- LISTADO DETALLADO -----
        $query = UnidadProductivaIntervenciones::with([
            'unidadProductiva',
            'asesor',
            'categoria',
            'tipo'
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
    public static function buildPayloadInformeIntervenciones(Request $request, array $data): array
    {
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
    private function llamarApiAnalizarIntervencionesIA(Request $request, array $data): ?string
    {
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
    private function markdownToHtml(string $markdown): string
    {
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
    private function markdownToHtmlFallback(string $markdown): string
    {
        // Convertir **texto** a <strong>texto</strong>
        $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $markdown);
        // Convertir *texto* a <em>texto</em>
        $html = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);
        // Convertir saltos de línea a <br>
        $html = nl2br($html);
        return $html;
    }

    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new IntervencionesExport($query), 'Intervenciones.xlsx');
    }

    public function import(Request $request)
    {
        Excel::import(new UnidadProductivaIntervencionesImport, $request->file('archivo'));

        return back()->with('ok', 'Datos cargados correctamente.');
    }

    public function index(Request $request)
    {
        $query = $this->getQuery($request);
        $data = $this->paginate($query, $request);

        return response()->json( $data );
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $data['asesor_id'] = Auth::user()->id;

        if ($request->hasFile('formFile')) 
        {
            $path = $request->file('formFile')->store('storage/Intervenciones', 'public');
            $path = config('app.archivos_url') . $path;
            $data['soporte'] = $path;
        }

        foreach($request->unidades as $item)
        {
            $data['unidadproductiva_id'] = $item['unidadproductiva_id'];
            $data['participantes'] = $item['participantes'];
            $entity = UnidadProductivaIntervenciones::create($data);
        }

        return response()->json(['message' => 'Stored'], 201);
    }

    private function getQuery(Request $request)
    {
        $search = $request->get('search');

        $query = UnidadProductivaIntervenciones::query()
            ->select([
                'unidadesproductivas_intervenciones.*',
                DB::raw("CONCAT(users.name, ' ', users.lastname) as asesor"),
                'unidadesproductivas.business_name as unidad',
                'categorias_intervenciones.nombre as categoria',
                'tipos_intervenciones.nombre as tipo',
            ])
            ->join('categorias_intervenciones', 'categorias_intervenciones.id', '=', 'unidadesproductivas_intervenciones.categoria_id')
            ->join('tipos_intervenciones', 'tipos_intervenciones.id', '=', 'unidadesproductivas_intervenciones.tipo_id')
            ->join('users', 'users.id', '=', 'unidadesproductivas_intervenciones.asesor_id')
            ->join('unidadesproductivas', 'unidadesproductivas.unidadproductiva_id', '=', 'unidadesproductivas_intervenciones.unidadproductiva_id');


        $asesor = (Auth::user()->rol_id === Role::ASESOR) ? Auth::id() : $request->get('asesor');
        if ($asesor) {
            $query->where('unidadesproductivas_intervenciones.asesor_id', $asesor);
        }

        if ($unidad = $request->get('unidad')) {
            $query->where('unidadesproductivas_intervenciones.unidadproductiva_id', $unidad);
        }

        if ($fechaInicio = $request->get('fecha_inicio')) {
            $query->whereDate('fecha_inicio', '>=', $fechaInicio);
        }

        if ($fechaFin = $request->get('fecha_fin')) {
            $query->whereDate('fecha_fin', '<=', $fechaFin);
        }

        if(!empty($search))
        {
            $filters = ['descripcion'];
            $query->where(function ($q) use ($search, $filters) {
                foreach ($filters as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }
}
