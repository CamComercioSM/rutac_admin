<?php

namespace App\Services;

use App\Models\Empresarios\UnidadProductivaIntervenciones;
use App\Models\Intervenciones\IntervencionLead;
use App\Models\Intervenciones\IntervencionUnidad;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IntervencionService
{
    /**
     * Obtiene y agrupa la data para informes.
     */
    public function getInformeData(array $filters): array
    {
        $fi = $filters['fecha_inicio'];
        $ff = $filters['fecha_fin'];

        $baseQuery = UnidadProductivaIntervenciones::whereBetween('fecha_inicio', [$fi, $ff])
            ->when($filters['asesor'] ?? null, fn($q, $v) => $q->where('asesor_id', $v))
            ->when($filters['unidad'] ?? null, fn($q, $v) => $q->where('unidadproductiva_id', $v));

        $intervenciones = (clone $baseQuery)->with(['unidadProductiva', 'lead', 'asesor', 'categoria', 'tipo', 'fase'])
            ->orderBy('fecha_inicio', 'ASC')
            ->get();

        return [
            'inicio'         => Carbon::parse($fi)->translatedFormat('Y-m-d H:i'),
            'fin'            => Carbon::parse($ff)->translatedFormat('Y-m-d H:i'),
            'conclusiones'   => $filters['conclusiones'] ?? '',
            'intervenciones' => $intervenciones,
            'porCategoria'   => (clone $baseQuery)->select('categoria_id', DB::raw('COUNT(*) as total'))->groupBy('categoria_id')->with('categoria')->get(),
            'porTipo'        => (clone $baseQuery)->select('tipo_id', DB::raw('COUNT(*) as total'))->groupBy('tipo_id')->with('tipo')->get(),
            'porUnidad'      => (clone $baseQuery)->select('unidadproductiva_id', DB::raw('COUNT(*) as total'))->groupBy('unidadproductiva_id')->with('unidadProductiva')->get(),
            'totalGeneral'   => $intervenciones->count(),
        ];
    }

    /**
     * Procesa el guardado de una intervención y sus participantes.
     */
    public function storeIntervencion(array $data, array $unidades, array $leads): UnidadProductivaIntervenciones
    {
        return DB::transaction(function () use ($data, $unidades, $leads) {
            $intervencion = UnidadProductivaIntervenciones::create($data);

            $totalAsistentes = 0;
            
            foreach ($unidades as $u) {
                $cant = (int) filter_var($u['participantes'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
                IntervencionUnidad::create([
                    'intervencion_id'     => $intervencion->id,
                    'unidadproductiva_id' => $u['value'],
                    'participantes'       => $cant,
                ]);
                $totalAsistentes += $cant;
            }

            foreach ($leads as $l) {
                $cant = (int) filter_var($l['participantes'] ?? 1, FILTER_SANITIZE_NUMBER_INT);
                IntervencionLead::create([
                    'intervencion_id' => $intervencion->id,
                    'lead_id'         => $l['value'],
                    'participantes'   => $cant,
                ]);
                $totalAsistentes += $cant;
            }

            $intervencion->update([
                'cant_unidades' => count($unidades),
                'cant_leads'    => count($leads),
                'participantes' => $totalAsistentes
            ]);

            return $intervencion;
        });
    }
}