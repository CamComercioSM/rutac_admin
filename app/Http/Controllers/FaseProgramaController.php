<?php

namespace App\Http\Controllers;

use App\Models\Programas\FasePrograma;
use App\Services\FaseProgramaService;
use App\Exports\FaseProgramaExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class FaseProgramaController extends Controller
{
    protected $faseProgramaService;

    public function __construct(FaseProgramaService $faseProgramaService)
    {
        $this->faseProgramaService = $faseProgramaService;
    }

    /**
     * Display a listing of all phases.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 15);
        $page = $request->input('page', 1);

        $filters = [
            'nombre' => $request->input('nombre'),
            'activa' => $request->input('activa'),
            'sortBy' => $request->input('sortBy', 'orden'),
            'sortOrder' => $request->input('sortOrder', 'asc'),
        ];

        $data = $this->faseProgramaService->listar($filters, $perPage, $page);

        return response()->json($data);
    }

    /**
     * Export phases to Excel.
     */
    public function export(Request $request)
    {
        $query = FasePrograma::query();

        if ($request->has('nombre') && !empty($request->nombre)) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }

        if ($request->has('activa')) {
            $query->where('activa', (bool)$request->activa);
        }

        return Excel::download(new FaseProgramaExport($query), 'fases_programas.xlsx');
    }

    /**
     * Get all active phases without pagination.
     */
    public function activas()
    {
        $fases = $this->faseProgramaService->obtenerActivas();
        return response()->json($fases);
    }

    /**
     * Store a newly created phase in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'orden' => 'nullable|integer|min:1',
            'activa' => 'boolean',
        ]);

        try {
            $fase = $this->faseProgramaService->crear($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Fase creada correctamente',
                'data' => $fase
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la fase: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified phase.
     */
    public function show($id)
    {
        $fase = $this->faseProgramaService->obtener($id);
        return response()->json($fase);
    }

    /**
     * Update the specified phase in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'orden' => 'nullable|integer|min:1',
            'activa' => 'boolean',
        ]);

        try {
            $fase = $this->faseProgramaService->actualizar($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Fase actualizada correctamente',
                'data' => $fase
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la fase: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified phase from storage (soft delete).
     */
    public function destroy($id)
    {
        try {
            $this->faseProgramaService->eliminar($id);

            return response()->json([
                'success' => true,
                'message' => 'Fase eliminada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la fase: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore a soft deleted phase.
     */
    public function restore($id)
    {
        try {
            $fase = $this->faseProgramaService->restaurar($id);

            return response()->json([
                'success' => true,
                'message' => 'Fase restaurada correctamente',
                'data' => $fase
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar la fase: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Force delete a phase permanently.
     */
    public function forceDelete($id)
    {
        try {
            $this->faseProgramaService->eliminarPermanentemente($id);

            return response()->json([
                'success' => true,
                'message' => 'Fase eliminada permanentemente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar permanentemente la fase: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle active status of a phase.
     */
    public function toggleActivo($id)
    {
        try {
            $fase = $this->faseProgramaService->toggleActivo($id);

            return response()->json([
                'success' => true,
                'message' => 'Estado de fase actualizado correctamente',
                'data' => $fase
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder phases.
     */
    public function reordenar(Request $request)
    {
        $this->validate($request, [
            'ordenes' => 'required|array',
        ]);

        try {
            $this->faseProgramaService->reordenar($request->ordenes);

            return response()->json([
                'success' => true,
                'message' => 'Fases reordenadas correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reordenar las fases: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics about phases.
     */
    public function estadisticas()
    {
        $stats = $this->faseProgramaService->obtenerEstadisticas();
        return response()->json($stats);
    }

    /**
     * Get the next order value for a new phase.
     */
    public function proximoOrden()
    {
        $orden = $this->faseProgramaService->obtenerProximoOrden();
        return response()->json(['proximo_orden' => $orden]);
    }
}
