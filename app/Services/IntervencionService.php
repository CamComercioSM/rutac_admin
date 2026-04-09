<?php

namespace App\Services;

use App\Helpers\QueryHelper;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\ReporteMensual;
use App\Models\Empresarios\UnidadProductivaIntervenciones;
use App\Models\Intervenciones\IntervencionLead;
use App\Models\Intervenciones\IntervencionUnidad;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class IntervencionService {

    /**
     * Construye la consulta base para el listado con todos los Joins, 
     * clasificaciones, filtros y ORDENAMIENTO original.
     */
    public function getListQuery(array $filters, $user) {
        $query = UnidadProductivaIntervenciones::query() 
            ->select([
                'unidadesproductivas_intervenciones.*',
                DB::raw('(COALESCE(unidadesproductivas_intervenciones.participantes, 0) + COALESCE(unidadesproductivas_intervenciones.participantes_otros, 0)) AS participantes_total'),
                DB::raw("CONCAT(users.name, ' ', users.lastname) as asesor"),
                DB::raw("CASE 
                            WHEN COALESCE(unidadesproductivas_intervenciones.cant_unidades, 0) > 0 AND COALESCE(unidadesproductivas_intervenciones.cant_leads, 0) > 0 THEN 'MIXTO'
                            WHEN COALESCE(unidadesproductivas_intervenciones.cant_unidades, 0) > 0 THEN 'REGISTRADO'
                            WHEN COALESCE(unidadesproductivas_intervenciones.cant_leads, 0) > 0 THEN 'NO REGISTRADO'
                            ELSE 'SIN PARTICIPANTES'
                        END as clasificacion"),
                DB::raw("CASE 
                        WHEN (COALESCE(unidadesproductivas_intervenciones.cant_unidades, 0) + COALESCE(unidadesproductivas_intervenciones.cant_leads, 0)) > 1 
                            THEN CONCAT(COALESCE(unidadesproductivas_intervenciones.cant_unidades, 0), ' UP / ', COALESCE(unidadesproductivas_intervenciones.cant_leads, 0), ' Leads')
                        WHEN COALESCE(unidadesproductivas_intervenciones.cant_unidades, 0) = 1 THEN '1 Unidad Productiva'
                        WHEN COALESCE(unidadesproductivas_intervenciones.cant_leads, 0) = 1 THEN '1 Lead / Ciudadano'
                        ELSE 'N/A'
                    END as unidad"),
                'fases_programas.nombre as fase',
                'pc.nombre_convocatoria as convocatoria',
                'p.nombre as programa',
                'categorias_intervenciones.nombre as categoria',
                'tipos_intervenciones.nombre as tipo',
            ])
            ->leftJoin('fases_programas', 'fases_programas.fase_id', '=', 'unidadesproductivas_intervenciones.fase_id')
            ->leftJoin('programas_convocatorias as pc', 'pc.convocatoria_id', '=', 'unidadesproductivas_intervenciones.convocatoria_id')
            ->leftJoin('programas as p', 'p.programa_id', '=', 'unidadesproductivas_intervenciones.programa_id')
            ->join('categorias_intervenciones', 'categorias_intervenciones.id', '=', 'unidadesproductivas_intervenciones.categoria_id')
            ->join('tipos_intervenciones', 'tipos_intervenciones.id', '=', 'unidadesproductivas_intervenciones.tipo_id')
            ->join('users', 'users.id', '=', 'unidadesproductivas_intervenciones.asesor_id');

        // Seguridad por Rol
        $asesor = ($user->rol_id === Role::ASESOR) ? $user->id : ($filters['asesor'] ?? null);
        if ($asesor) {
            $query->where('unidadesproductivas_intervenciones.asesor_id', $asesor);
        }

        // Filtros básicos (Llamando a la función privada que tenías)
        $this->applyFilter($query, $filters, 'programa', 'unidadesproductivas_intervenciones.programa_id');
        $this->applyFilter($query, $filters, 'convocatoria', 'unidadesproductivas_intervenciones.convocatoria_id');
        $this->applyFilter($query, $filters, 'fase', 'unidadesproductivas_intervenciones.fase_id');
        $this->applyFilter($query, $filters, 'categoria', 'unidadesproductivas_intervenciones.categoria_id');
        $this->applyFilter($query, $filters, 'tipo', 'unidadesproductivas_intervenciones.tipo_id');

        if ($fechaInicio = ($filters['fecha_inicio'] ?? null)) {
            $query->whereDate('unidadesproductivas_intervenciones.fecha_inicio', '>=', $fechaInicio);
        }
        if ($fechaFin = ($filters['fecha_fin'] ?? null)) {
            $query->whereDate('unidadesproductivas_intervenciones.fecha_fin', '<=', $fechaFin);
        }

        // Búsqueda Global (Incluyendo Hijos)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->orWhere('unidadesproductivas_intervenciones.descripcion', 'like', "%{$search}%")
                    ->orWhere('p.nombre', 'like', "%{$search}%")
                    ->orWhere('pc.nombre_convocatoria', 'like', "%{$search}%")
                    ->orWhere('fases_programas.nombre', 'like', "%{$search}%")
                    ->orWhere('users.name', 'like', "%{$search}%")
                    ->orWhere('users.lastname', 'like', "%{$search}%")
                    ->orWhereHas('unidades.unidadProductiva', function ($sub) use ($search) {
                        $sub->where('business_name', 'like', "%{$search}%")->orWhere('nit', 'like', "%{$search}%");
                    })
                    ->orWhereHas('leads', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%")->orWhere('document', 'like', "%{$search}%");
                    });
            });
        }

        // Ordenamiento dinámico (Tu mapeo original)
        $sortName = $filters['sortName'] ?? null;
        $sortOrder = $filters['sortOrder'] ?? 'desc';
        $map = [
            'fecha_creacion' => 'unidadesproductivas_intervenciones.fecha_creacion',
            'id' => 'unidadesproductivas_intervenciones.id',
            'titulo' => 'unidadesproductivas_intervenciones.titulo',
            'fecha_inicio' => 'unidadesproductivas_intervenciones.fecha_inicio',
            'fecha_fin' => 'unidadesproductivas_intervenciones.fecha_fin',
            'asesor' => "CONCAT(users.name, ' ', users.lastname)",
            'programa' => 'p.nombre',
            'convocatoria' => 'pc.nombre_convocatoria',
            'fase' => 'fases_programas.nombre',
            'categoria' => 'categorias_intervenciones.nombre',
            'tipo' => 'tipos_intervenciones.nombre',
            'participantes' => 'unidadesproductivas_intervenciones.participantes',
            'participantes_otros' => 'unidadesproductivas_intervenciones.participantes_otros',
            'cant_unidades' => 'unidadesproductivas_intervenciones.cant_unidades',
            'cant_leads' => 'unidadesproductivas_intervenciones.cant_leads',
        ];

        if ($sortName && isset($map[$sortName])) {
            $query->orderByRaw($map[$sortName] . ' ' . $sortOrder);
        } else {
            $query->orderBy('unidadesproductivas_intervenciones.fecha_creacion', 'desc');
        }

        return $query;
    }

    /**
     * Lógica de filtrado interna.
     */
    private function applyFilter($query, array $filters, $param, $column) {
        $value = $filters[$param] ?? null;
        if (!is_null($value) && $value !== '') {
            $query->where($column, $value);
        }
    }

    /**
     * Procesa el guardado (Original).
     */
    public function storeIntervencion(array $data, array $unidades, array $leads) {
        $intervencion = UnidadProductivaIntervenciones::create($data);
        $this->syncParticipantes($intervencion, $unidades, $leads);
        return $intervencion;
    }

    public function syncParticipantes($intervencion, array $unidades, array $leads) { // Limpiar actuales
        $intervencion->unidades()->delete();
        $intervencion->leads()->delete();

        $unidadesIds = [];
        $leadsIds = [];

        // Insertar unidades
        $totalParticipantes = 0;
        foreach ($unidades as $item) {
            $id = $item['unidadproductiva_id'] ?? $item['value'] ?? null;

            if ($id) {
                $participantes = (int) ($item['participantes'] ?? 0);

                $registro = IntervencionUnidad::create([
                    'intervencion_id' => $intervencion->id,
                    'unidadproductiva_id' => $id,
                    'participantes' => $participantes
                ]);

                $unidadesIds[] = $registro->id;

                $totalParticipantes += $participantes;
            }
        }

        // Insertar leads
        $participantes_otros = 0;
        foreach ($leads as $item) {
            $id = $item['lead_id'] ?? $item['value'] ?? null;

            if ($id) {
                $participantes = (int) ($item['participantes'] ?? 0);

                $registro = IntervencionLead::create([
                    'intervencion_id' => $intervencion->id,
                    'lead_id' => $id,
                    'participantes' => $participantes
                ]);

                $leadsIds[] = $registro->id;

                $participantes_otros += $participantes;
            }
        }

        // Actualizar contadores
        $intervencion->update([
            'cant_unidades' => count($unidadesIds), // contar solo insertados válidos
            'cant_leads'    => count($leadsIds),
            'participantes' => $totalParticipantes,
            'participantes_otros' => $participantes_otros,
        ]);

        return [
            'unidades' => $unidadesIds,
            'leads'    => $leadsIds,
            'participantes' => $totalParticipantes,
        ];
    }



    /**
     * Obtiene y agrupa la data para informes (Original).
     */
    /**
     * ========================= FUNCIÓN COMPLETA CORREGIDA =========================
     * Corrige:
     * - Filtro por unidad (whereHas)
     * - Agrupación por unidad (JOIN)
     * - Agrupación por leads (JOIN)
     */
    public function getInformeData(array $params, $user): array {
        $fi = $params['fecha_inicio'] ?? null;
        $ff = $params['fecha_fin'] ?? null;

        if (!$fi || !$ff) {
            throw new \Exception("Fechas requeridas para generar informe");
        }

        $asesorReq = $params['asesor'] ?? null;
        $unidadReq = $params['unidad'] ?? null;

        $asesor = ($user->rol_id === Role::ASESOR) ? $user->id : $asesorReq;

        // ========================= QUERY BASE =========================
        $baseQuery = UnidadProductivaIntervenciones::whereBetween('fecha_inicio', [$fi, $ff])
            ->whereNull('fecha_eliminacion') //
            //->where('estado', 'REPORTADO')   //
            //->when($asesor, fn($q) => $q->where('asesor_id', $asesor))
            ->when($unidadReq, function ($q) use ($unidadReq) {
                // ✅ filtro correcto por relación hija
                $q->whereHas('unidades', function ($sub) use ($unidadReq) {
                    $sub->where('unidadproductiva_id', $unidadReq);
                });
            });

        // ========================= DATA DETALLE =========================
        $intervenciones = (clone $baseQuery)
            ->with(['unidadProductiva', 'lead', 'asesor', 'categoria', 'tipo', 'fase'])
            ->orderBy('fecha_inicio', 'ASC')
            ->get();

        // ========================= RESPUESTA =========================
        return [
            'inicio'         => Carbon::parse($fi)->translatedFormat('Y-m-d H:i'),
            'fin'            => Carbon::parse($ff)->translatedFormat('Y-m-d H:i'),
            'conclusiones'   => $params['conclusiones'] ?? '',
            'intervenciones' => $intervenciones,
            'totalGeneral'   => (clone $baseQuery)->count(),

            // ========================= AGRUPACIONES =========================

            'porCategoria' => QueryHelper::agrupar($baseQuery, 'categoria_id', 'categoria'),
            'porTipo'      => QueryHelper::agrupar($baseQuery, 'tipo_id', 'tipo'),

            // ========================= POR UNIDAD =========================
            'porUnidad' => DB::table('unidadesproductivas_intervenciones as i')
                ->join('intervencion_unidades as iu', 'iu.intervencion_id', '=', 'i.id') // ⚠️ relación real
                ->select(
                    'iu.unidadproductiva_id',
                    DB::raw('COUNT(*) as total')
                )
                ->whereBetween('i.fecha_inicio', [$fi, $ff])
                ->whereNull('i.fecha_eliminacion')
                ->where('i.estado', 'REPORTADO')
                ->when($asesor, fn($q) => $q->where('i.asesor_id', $asesor))
                ->groupBy('iu.unidadproductiva_id')
                ->get(),

            // ========================= POR LEADS =========================
            'porLeads' => DB::table('unidadesproductivas_intervenciones as i')
                ->join('intervencion_leads as il', 'il.intervencion_id', '=', 'i.id') // ⚠️ relación real
                ->select(
                    'il.lead_id',
                    DB::raw('COUNT(*) as total')
                )
                ->whereBetween('i.fecha_inicio', [$fi, $ff])
                ->whereNull('i.fecha_eliminacion')
                ->where('i.estado', 'REPORTADO')
                ->when($asesor, fn($q) => $q->where('i.asesor_id', $asesor))
                ->groupBy('il.lead_id')
                ->get(),
        ];
    }

    /**
     * Valida si ya existe un reporte mensual para evitar duplicados (Original).
     */
    public function validarReporteDuplicado(int $asesorId, int $anio, int $mes, ?int $reporteId = null): bool {
        return ReporteMensual::where('asesor_id', $asesorId)
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->whereNull('fecha_eliminacion')
            ->when($reporteId, fn($q) => $q->where('id', '!=', $reporteId))
            ->exists();
    }

    public function generarInformePDF(array $data, string $rutaPublica): string {
        $pdf = Pdf::loadView('intervenciones.informe', $data)
            ->setPaper('a4', 'portrait');

        $pathDestino = public_path($rutaPublica);

        if (!file_exists(dirname($pathDestino))) {
            Storage::disk('public')->makeDirectory(dirname($rutaPublica));
        }

        $pdf->save($pathDestino);

        return $rutaPublica;
    }
}
