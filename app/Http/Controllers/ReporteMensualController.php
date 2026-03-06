<?php

namespace App\Http\Controllers;

use App\Models\ReporteMensual;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReporteMensualController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('reporteMensual.index');
    }

    public function reportesMensuales(): JsonResponse
    {
        $reportes = ReporteMensual::with(['asesor:id,name,identification', 'supervisor:id,name'])->get();

        return response()->json([
            'data' => $reportes
        ]);
    }

    public function ReporteMensualSupervision($id)
    {
        $reporte = ReporteMensual::with(['asesor:id,name,identification'])->findOrFail($id);

        return view('reporteMensual.previewSupervisor', compact('reporte'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) : JsonResponse
    {
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
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
