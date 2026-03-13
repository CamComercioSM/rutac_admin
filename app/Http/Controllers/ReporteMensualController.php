<?php

namespace App\Http\Controllers;

use App\Exports\ReporteMensualExport;
use App\Models\ReporteMensual;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReporteMensualController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        return view('reporteMensual.index', [

            'stats' => [
                'reportes_total' => 120,
                'reportes_pendientes' => 25,
                'reportes_aprobados' => 70,
                'reportes_rechazados' => 25,
                'intervenciones_total' => 560,

                // NUEVO → variación para el badge
                'intervenciones_variacion' => 18.4
            ],

            /* intervenciones por mes (para el gráfico horizontal) */
            'meses' => [
                ['mes' => 1, 'nombre' => 'Enero', 'total' => 45],
                ['mes' => 2, 'nombre' => 'Febrero', 'total' => 38],
                ['mes' => 3, 'nombre' => 'Marzo', 'total' => 52],
                ['mes' => 4, 'nombre' => 'Abril', 'total' => 40],
                ['mes' => 5, 'nombre' => 'Mayo', 'total' => 60],
                ['mes' => 6, 'nombre' => 'Junio', 'total' => 48],
            ],

            /* datos para el donut */
            'intervencionesDonut' => [
                ['mes' => 'Enero', 'total' => 45],
                ['mes' => 'Febrero', 'total' => 38],
                ['mes' => 'Marzo', 'total' => 52],
                ['mes' => 'Abril', 'total' => 40],
                ['mes' => 'Mayo', 'total' => 60],
                ['mes' => 'Junio', 'total' => 48],
            ],

            /* ranking de asesores */
            'topAsesores' => [
                (object)[
                    'nombre' => 'Juan Perez',
                    'intervenciones' => 34
                ],
                (object)[
                    'nombre' => 'Maria Gomez',
                    'intervenciones' => 28
                ],
                (object)[
                    'nombre' => 'Carlos Ruiz',
                    'intervenciones' => 21
                ],
                (object)[
                    'nombre' => 'Laura Martinez',
                    'intervenciones' => 19
                ],
            ],
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
            ]);
            $msg = 'Reporte aprobado exitosamente';
        } else if ($request->estado === "RECHAZADO") {
            ReporteMensual::where('id', $request->reporte_id)->update([
                'estado' => 'RECHAZADO',
                'fecha_revision' => now(),
                'supervisor_id' => Auth::id(),
                'usuario_actualizo' => Auth::id(),
                'observaciones_supervisor' => $request->observacionesSupervisor,
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
