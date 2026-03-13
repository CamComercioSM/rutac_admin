<?php

namespace App\Http\Controllers;

use App\Exports\ReporteMensualExport;
use App\Models\Empresarios\UnidadProductivaIntervenciones;
use App\Models\ReporteMensual;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReporteMensualController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $mesActual = now()->month;

        /*
    |---------------------------------------------------------
    | TOTAL INTERVENCIONES (histórico)
    |---------------------------------------------------------
    */
        $intervencionesTotal = \App\Models\Empresarios\UnidadProductivaIntervenciones::count();


        /*
    |---------------------------------------------------------
    | INTERVENCIONES POR MES
    |---------------------------------------------------------
    */
        $intervencionesMes = \App\Models\Empresarios\UnidadProductivaIntervenciones::select(
            DB::raw('MONTH(fecha_inicio) as mes'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy(DB::raw('MONTH(fecha_inicio)'))
            ->orderBy('mes')
            ->get()
            ->keyBy('mes');


        /*
    |---------------------------------------------------------
    | CONSTRUIR ARRAY DE MESES
    |---------------------------------------------------------
    */
        $meses = [];

        for ($m = 1; $m <= $mesActual; $m++) {

            $totalMes = $intervencionesMes[$m]->total ?? 0;

            $porcentaje = 0;

            if ($intervencionesTotal > 0) {
                $porcentaje = round(($totalMes / $intervencionesTotal) * 100, 1);
            }

            $meses[] = [
                'mes' => $m,
                'nombre' => \Carbon\Carbon::create()->month($m)->translatedFormat('F'),
                'total' => $totalMes,
                'porcentaje' => $porcentaje
            ];
        }


        /*
    |---------------------------------------------------------
    | DONUT CHART (usa los mismos datos de meses)
    |---------------------------------------------------------
    */
        $intervencionesDonut = collect($meses)->map(function ($m) {
            return [
                'mes' => ucfirst($m['nombre']),
                'total' => $m['total']
            ];
        });


        /*
    |---------------------------------------------------------
    | TOP ASESORES POR INTERVENCIONES
    |---------------------------------------------------------
    */
        $topAsesores = \App\Models\Empresarios\UnidadProductivaIntervenciones::select(
            'asesor_id',
            DB::raw('COUNT(*) as intervenciones')
        )
            ->with('asesor:id,name')
            ->groupBy('asesor_id')
            ->orderByDesc('intervenciones')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return (object)[
                    'nombre' => $item->asesor->name ?? 'Sin nombre',
                    'intervenciones' => $item->intervenciones
                ];
            });


        /*
    |---------------------------------------------------------
    | ESTADÍSTICAS DE REPORTES
    |---------------------------------------------------------
    */
        $reportesTotal = \App\Models\ReporteMensual::count();

        $reportesPendientes = \App\Models\ReporteMensual::where('estado', 'PENDIENTE_REVISION')->count();

        $reportesAprobados = \App\Models\ReporteMensual::where('estado', 'APROBADO')->count();

        $reportesRechazados = \App\Models\ReporteMensual::where('estado', 'RECHAZADO')->count();


        /*
    |---------------------------------------------------------
    | VARIACIÓN MES ACTUAL VS MES ANTERIOR
    |---------------------------------------------------------
    */
        $intervencionesMesActual = \App\Models\Empresarios\UnidadProductivaIntervenciones::whereMonth('fecha_inicio', now()->month)->count();

        $intervencionesMesAnterior = \App\Models\Empresarios\UnidadProductivaIntervenciones::whereMonth('fecha_inicio', now()->subMonth()->month)->count();

        $variacion = 0;

        if ($intervencionesMesAnterior > 0) {
            $variacion = (($intervencionesMesActual - $intervencionesMesAnterior) / $intervencionesMesAnterior) * 100;
        }


        /*
    |---------------------------------------------------------
    | RETORNAR VISTA
    |---------------------------------------------------------
    */
        return view('reporteMensual.index', [

            'stats' => [
                'reportes_total' => $reportesTotal,
                'reportes_pendientes' => $reportesPendientes,
                'reportes_aprobados' => $reportesAprobados,
                'reportes_rechazados' => $reportesRechazados,
                'intervenciones_total' => $intervencionesTotal,
                'intervenciones_variacion' => round($variacion, 1)
            ],

            'meses' => $meses,

            'intervencionesDonut' => $intervencionesDonut,

            'topAsesores' => $topAsesores

        ]);
    }

    public function reportesMensuales(Request $request): JsonResponse {
        $draw = intval($request->input('draw'));
        $start = intval($request->input('start', 0));
        $length = intval($request->input('length', 10));

        $query = ReporteMensual::with([
            'asesor:id,name,identification',
            'supervisor:id,name'
        ]);

        $totalData = ReporteMensual::count();

        // filtro por estado desde DataTables
        $estado = $request->input('columns.8.search.value');
        $gestor = $request->input('columns.5.search.value');
        $periodo = $request->input('columns.3.search.value');

        if (!empty($estado)) {
            $estado = preg_replace('/^\^|\$$/', '', $estado);
            $query->where('estado', $estado);
        }
        if (!empty($gestor)) {
            $gestor = preg_replace('/^\^|\$$/', '', $gestor);
            $query->whereHas('supervisor', function ($q) use ($gestor) {
                $q->where('name', 'like', "%{$gestor}%");
            });
        }
        if (!empty($periodo)) {
            $periodo = preg_replace('/^\^|\$$/', '', $periodo);

            if (preg_match('/^(\d{4})-(\d{2})$/', $periodo, $matches)) {
                $anio = $matches[1];
                $mes = $matches[2];

                $query->where('anio', $anio)
                    ->where('mes', $mes);
            }
        }
        // búsqueda global opcional
        $search = $request->input('search.value');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('estado', 'like', "%{$search}%")
                    ->orWhere('gestor', 'like', "%{$search}%")
                    ->orWhere('anio', 'like', "%{$search}%")
                    ->orWhere('mes', 'like', "%{$search}%");
            });
        }

        $totalFiltered = (clone $query)->count();

        $reportes = $query
            ->orderBy('fecha_generacion', 'desc')
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $reportes,
        ]);
    }

    public function export(Request $request) {
        $query = $this->getQuery($request);
        return Excel::download(new ReporteMensualExport($query), 'reportes_mensuales.xlsx');
    }

    public function ReporteMensualSupervision($id) {
        $reporte = ReporteMensual::with(['asesor:id,name,identification'])->findOrFail($id);

        return view('reporteMensual.previewSupervisor', compact('reporte'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse {
        $msg = '';

        if ($request->estado === "APROBADO") {
            ReporteMensual::where('id', $request->reporte_id)->update([
                'fecha_revision' => now(),
                'supervisor_id' => Auth::id(),
                'usuario_actualizo' => Auth::id(),
                'estado' => 'APROBADO',
                'observaciones_supervisor' => $request->observacionesSupervisor,
                'meta_intervenciones' => $request->meta_intervenciones,
                'avance_meta' => $request->avance_meta
            ]);
            $msg = 'Reporte aprobado exitosamente';
        } else if ($request->estado === "RECHAZADO") {
            ReporteMensual::where('id', $request->reporte_id)->update([
                'estado' => 'RECHAZADO',
                'fecha_revision' => now(),
                'supervisor_id' => Auth::id(),
                'usuario_actualizo' => Auth::id(),
                'observaciones_supervisor' => $request->observacionesSupervisor,
                'avance_meta' => $request->avanceMeta
            ]);
            $msg = 'Reporte rechazado exitosamente';
        }

        return response()->json([
            'success' => true,
            'message' => $msg
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        //
    }


    private function getQuery(Request $request) {
        $query = ReporteMensual::query([])->select([
            'id',
            'asesor_id',
            'anio',
            'mes',
            'total_intervenciones',
            'total_unidades',
            'estado',
            'observaciones_supervisor',
            'supervisor_id',
            'fecha_creacion',
            'fecha_revision',
            'informe_url',
        ]);
        return $query;
    }
}
