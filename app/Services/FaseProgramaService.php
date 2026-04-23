<?php

namespace App\Services;

use App\Models\Programas\FasePrograma;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;

class FaseProgramaService
{
    /**
     * Get all phases with optional filters and pagination.
     */
    public function listar($filters = [], $perPage = 15, $page = 1)
    {
        $query = FasePrograma::query();

        // Filtrar por nombre
        if (isset($filters['nombre']) && !empty($filters['nombre'])) {
            $query->where('nombre', 'like', '%' . $filters['nombre'] . '%');
        }

        // Filtrar por estado activa
        if (isset($filters['activa'])) {
            $query->where('activa', (bool)$filters['activa']);
        }

        // Ordenar
        $sortBy = $filters['sortBy'] ?? 'orden';
        $sortOrder = $filters['sortOrder'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get all active phases ordered by orden field.
     */
    public function obtenerActivas()
    {
        return FasePrograma::activas()
            ->ordenados()
            ->get();
    }

    /**
     * Get a phase by ID.
     */
    public function obtener($id)
    {
        return FasePrograma::findOrFail($id);
    }

    /**
     * Create a new phase.
     */
    public function crear(array $datos)
    {
        $datos['usuario_creo'] = Auth::id();

        return FasePrograma::create($datos);
    }

    /**
     * Update an existing phase.
     */
    public function actualizar($id, array $datos)
    {
        $fase = $this->obtener($id);

        $datos['usuario_actualizo'] = Auth::id();

        $fase->update($datos);

        return $fase;
    }

    /**
     * Delete (soft delete) a phase.
     */
    public function eliminar($id)
    {
        $fase = $this->obtener($id);

        $fase->delete();

        return true;
    }

    /**
     * Restore a soft deleted phase.
     */
    public function restaurar($id)
    {
        $fase = FasePrograma::withTrashed()->findOrFail($id);

        $fase->restore();

        return $fase;
    }

    /**
     * Permanently delete a phase.
     */
    public function eliminarPermanentemente($id)
    {
        $fase = FasePrograma::withTrashed()->findOrFail($id);

        $fase->forceDelete();

        return true;
    }

    /**
     * Get the next order value for a new phase.
     */
    public function obtenerProximoOrden()
    {
        return FasePrograma::max('orden') + 1 ?? 1;
    }

    /**
     * Reorder phases.
     */
    public function reordenar(array $ordenes)
    {
        foreach ($ordenes as $index => $faseId) {
            FasePrograma::where('fase_id', $faseId)
                ->update([
                    'orden' => $index + 1,
                    'usuario_actualizo' => Auth::id(),
                ]);
        }

        return true;
    }

    /**
     * Toggle active status of a phase.
     */
    public function toggleActivo($id)
    {
        $fase = $this->obtener($id);

        $fase->activa = !$fase->activa;
        $fase->usuario_actualizo = Auth::id();
        $fase->save();

        return $fase;
    }

    /**
     * Get statistics about phases.
     */
    public function obtenerEstadisticas()
    {
        return [
            'total' => FasePrograma::count(),
            'activas' => FasePrograma::where('activa', true)->count(),
            'inactivas' => FasePrograma::where('activa', false)->count(),
            'eliminadas' => FasePrograma::onlyTrashed()->count(),
        ];
    }
}
