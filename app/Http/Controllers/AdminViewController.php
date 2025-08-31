<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empresarios\UnidadProductiva;
use App\Models\TablasReferencias\Departamento;
use App\Models\TablasReferencias\Municipio;
use App\Models\TablasReferencias\CiiuActividad;
use App\Models\TablasReferencias\Etapa;
use App\Models\TablasReferencias\UnidadProductivaTamano;
use App\Models\TablasReferencias\UnidadProductivaPersona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AdminViewController extends Controller
{
    public function dashboard(Request $request)
    {
        try {
    
            $filtros = [
                'departamento_id' => $request->get('departamento_id'),
                'municipio_id'    => $request->get('municipio_id'),
                'sector_id'       => $request->get('sector_id'),
                'etapa_id'        => $request->get('etapa_id'),
                'tamano_id'       => $request->get('tamano_id'),
                'tipopersona_id'  => $request->get('tipopersona_id'),
                'fecha_desde'     => $request->get('fecha_desde'),
                'fecha_hasta'     => $request->get('fecha_hasta'),
            ];
    
            // Construir query base
            $query = $this->buildOptimizedQuery($filtros);
    
            // Cargar datos (sin cache)
            $dashboardData = $this->loadDashboardDataAsync($query, $filtros);
    
      
            return view("dashboard", $dashboardData);
    
        } catch (\Exception $e) {
            \Log::error('Error en dashboard: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
    
            $backendData = [
                'porTipoOrganizacion'   => [],
                'porEstadoDiagnostico'  => [],
                'porEtapas'             => [],
                'porMunicipios'         => [],
                'evolucionTemporal'     => [],
                'porTamanos'            => [],
                'porSectores'           => [],
                'datosMapa'             => []
            ];
    
            return view("dashboard", [
                'totalUnidades' => 0,
                'backendData'   => $backendData,
                'departamentos' => [],
                'municipios'    => [],
                'sectores'      => [],
                'etapas'        => [],
                'tamanos'       => [],
                'tiposPersona'  => [],
                'filtros'       => $filtros ?? [],
                'error'         => 'Error al cargar datos. Intente nuevamente.'
            ]);
        }
    }
    

    /**
     * Construye query optimizada con índices y límites
     */
    private function buildOptimizedQuery($filtros)
    {
        $query = UnidadProductiva::query()
            ->select([
                'unidadproductiva_id',
                'tipopersona_id',
                'sector_id',
                'etapa_id',
                'tamano_id',
                'municipality_id',
                'department_id',
                'geolocation',
                'fecha_creacion',
                'complete_diagnostic'
            ]);

        // Aplicar filtros de manera eficiente
        if ($filtros['departamento_id']) {
            $query->where('department_id', $filtros['departamento_id']);
        }
        if ($filtros['municipio_id']) {
            $query->where('municipality_id', $filtros['municipio_id']);
        }
        if ($filtros['sector_id']) {
            $query->where('sector_id', $filtros['sector_id']);
        }
        if ($filtros['etapa_id']) {
            $query->where('etapa_id', $filtros['etapa_id']);
        }
        if ($filtros['tamano_id']) {
            $query->where('tamano_id', $filtros['tamano_id']);
        }
        if ($filtros['tipopersona_id']) {
            $query->where('tipopersona_id', $filtros['tipopersona_id']);
        }
        if ($filtros['fecha_desde'] && $filtros['fecha_hasta']) {
            $query->whereBetween('fecha_creacion', [$filtros['fecha_desde'], $filtros['fecha_hasta']]);
        }

        return $query;
    }

    function consultaExpedienteMercantil()
    { 
        return View("consultaExpedienteMercantil");
    }

    private function loadDashboardDataAsync($query, $filtros)
    {
    $totalUnidades = $query->count();

    $departamentos = Departamento::select('departamento_id', 'departamentoNOMBRE')
        ->orderBy('departamentoNOMBRE')->get();
    $municipios = Municipio::select('municipio_id', 'municipioNOMBREOFICIAL')
        ->orderBy('municipioNOMBREOFICIAL')->get();
    $sectores = DB::table('ciiu_macrosectores')
        ->select('sector_id','sectorNOMBRE')
        ->whereNotNull('sectorNOMBRE')
        ->orderBy('sectorNOMBRE')->get();
    $etapas = Etapa::select('etapa_id','name')->get();
    $tamanos = UnidadProductivaTamano::select('tamano_id','tamanoNOMBRE')
        ->orderBy('tamanoNOMBRE')->get();
    $tiposPersona = UnidadProductivaPersona::select('tipopersona_id','tipoPersonaNOMBRE')
        ->orderBy('tipoPersonaNOMBRE')->get();

    $qStats = clone $query;

    $porTipoOrganizacion   = $this->getTipoOrganizacionStats($qStats, $filtros);
    $porEstadoDiagnostico  = $this->getEstadoDiagnosticoStats($qStats, $filtros);
    $porEtapas             = $this->getEtapasStats($qStats, $filtros);
    $porMunicipios         = $this->getMunicipiosStats($qStats, $filtros);
    $porMunicipiosCompletos = $this->getMunicipiosStatsCompletos($qStats, $filtros);
    $evolucionTemporal     = $this->getEvolucionTemporalStats($qStats, $filtros);
    $porTamanos            = $this->getTamanosStats($qStats, $filtros);
    $porSectores           = $this->getSectoresStats($qStats, $filtros);
    $datosMapa             = $this->getMapaStats($qStats, $filtros);

    $backendData = [
        'porTipoOrganizacion'  => $porTipoOrganizacion,
        'porEstadoDiagnostico' => $porEstadoDiagnostico,
        'porEtapas'            => $porEtapas,
        'porMunicipios'        => $porMunicipios,
        'porMunicipiosCompletos' => $porMunicipiosCompletos,
        'evolucionTemporal'    => $evolucionTemporal,
        'porTamanos'           => $porTamanos,
        'porSectores'          => $porSectores,
        'datosMapa'            => $datosMapa,
        'totalUnidades'        => $totalUnidades,
    ];

    \Log::info('Dashboard data debug', [
        'totalUnidades' => $totalUnidades,
        'porMunicipios_count' => is_object($porMunicipios) ? $porMunicipios->count() : (is_array($porMunicipios) ? count($porMunicipios) : 0),
        'porMunicipiosCompletos_count' => is_object($porMunicipiosCompletos) ? $porMunicipiosCompletos->count() : (is_array($porMunicipiosCompletos) ? count($porMunicipiosCompletos) : 0),
    ]);

    return compact(
        'totalUnidades',
        'backendData',
        'departamentos',
        'municipios',
        'sectores',
        'etapas',
        'tamanos',
        'tiposPersona',
        'filtros'
    );
}


    /**
     * Obtiene estadísticas por tipo de organización optimizadas
     */
    private function getTipoOrganizacionStats($query, $filtros)
    {
        try {
            // Traer totales agrupados
            $stats = (clone $query)
                ->select('tipopersona_id', DB::raw('count(*) as total'))
                ->whereNotNull('tipopersona_id')
                ->groupBy('tipopersona_id')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
    
            // Diccionario: id => nombre
            $mapTipos = UnidadProductivaPersona::pluck('tipoPersonaNOMBRE', 'tipopersona_id');
    
            // Adjuntar nombres directamente
            return $stats->map(function ($item) use ($mapTipos) {
                $item->tipoPersona = (object)[
                    'tipopersona_id'    => $item->tipopersona_id,
                    'tipoPersonaNOMBRE' => $mapTipos[$item->tipopersona_id] ?? ('Tipo ID: ' . $item->tipopersona_id),
                ];
                return $item;
            });
    
        } catch (\Exception $e) {
            \Log::error('Error en getTipoOrganizacionStats', ['error' => $e->getMessage()]);
            return collect(); // colección vacía en caso de error
        }
    }
    

    /**
     * Obtiene estadísticas por estado del diagnóstico optimizadas
     */
    private function getEstadoDiagnosticoStats($query, $filtros)
    {
        try {
            return (clone $query)
                ->select('complete_diagnostic', DB::raw('count(*) as total'))
                ->groupBy('complete_diagnostic')
                ->orderByDesc('total')
                ->get()
                ->map(function ($item) {
                    $item->estado = $item->complete_diagnostic ? 'Terminado' : 'Pendiente';
                    return $item;
                });
        } catch (\Exception $e) {
            \Log::error('Error en getEstadoDiagnosticoStats', ['error' => $e->getMessage()]);
            return collect();
        }
    }
    

    /**
     * Obtiene estadísticas por etapas optimizadas
     */
    private function getEtapasStats($query, $filtros)
    {
        try {
            // Diccionario: etapa_id => name (una sola consulta)
            $mapEtapas = Etapa::pluck('name', 'etapa_id');
    
            // Agregado en BD (TOP 5)
            $rows = (clone $query)
                ->select('etapa_id', DB::raw('COUNT(*) AS total'))
                ->whereNotNull('etapa_id')
                ->groupBy('etapa_id')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
    
            // Adjuntar nombre sin disparar consultas por item
            return $rows->map(function ($item) use ($mapEtapas) {
                $item->etapa = (object)[
                    'etapa_id' => $item->etapa_id,
                    'name'     => $mapEtapas[$item->etapa_id] ?? ('Etapa ID: ' . $item->etapa_id),
                ];
                return $item;
            });
    
        } catch (\Exception $e) {
            \Log::error('Error en getEtapasStats', ['error' => $e->getMessage()]);
            return collect();
        }
    }
    

    /**
     * Obtiene estadísticas por municipios optimizadas
     */
    private function getMunicipiosStats($query, $filtros)
    {
        // Diccionario: municipio_id => nombre
        $mapMunicipios = Municipio::pluck('municipioNOMBREOFICIAL', 'municipio_id');
    
        // TOP 8 por conteo
        $rows = (clone $query)
            ->select('municipality_id', DB::raw('count(*) as total'))
            ->whereNotNull('municipality_id')
            ->groupBy('municipality_id')
            ->orderByDesc('total')
            ->limit(8)
            ->get();
    
        return $rows->map(function($item) use ($mapMunicipios) {
            $item->municipio = (object)[
                'municipio_id'           => $item->municipality_id,
                'municipioNOMBREOFICIAL' => $mapMunicipios[$item->municipality_id] ?? ('Municipio ID: '.$item->municipality_id),
            ];
            return $item;
        });
    }
    
    /**
     * Obtiene estadísticas por municipios COMPLETOS (sin límite para proporciones)
     */
    private function getMunicipiosStatsCompletos($query, $filtros)
    {
        try {
            // Diccionario: municipio_id => nombre (una sola consulta)
            $mapMunicipios = Municipio::pluck('municipioNOMBREOFICIAL', 'municipio_id');
    
            // IDs válidos (excluye 0 y los que no existan en la tabla municipios)
            $municipiosValidos = $mapMunicipios->keys()->filter(fn ($id) => (int)$id > 0)->all();
    
            if (empty($municipiosValidos)) {
                return collect(); // no hay municipios válidos
            }
    
            // Contar total antes de filtrar por municipios válidos
            $totalAntesFiltro = (clone $query)->whereNotNull('municipality_id')->count();
            
            // Contar unidades con municipality_id NULL
            $totalConMunicipioNull = (clone $query)->whereNull('municipality_id')->count();
            
            // Contar unidades con municipality_id inválido (no existe en tabla municipios)
            $totalConMunicipioInvalido = (clone $query)
                ->whereNotNull('municipality_id')
                ->whereNotIn('municipality_id', $municipiosValidos)
                ->count();
    
            // Agregado en BD (SIN LÍMITE)
            $rows = (clone $query)
                ->select('municipality_id', DB::raw('COUNT(*) AS total'))
                ->whereNotNull('municipality_id')
                ->whereIn('municipality_id', $municipiosValidos)
                ->groupBy('municipality_id')
                ->orderByDesc('total')
                ->get();
    
            // Log detallado para debugging
            \Log::info('Debug getMunicipiosStatsCompletos', [
                'totalUnidadesQuery' => $query->count(),
                'totalConMunicipioNotNull' => $totalAntesFiltro,
                'totalConMunicipioNull' => $totalConMunicipioNull,
                'totalConMunicipioInvalido' => $totalConMunicipioInvalido,
                'totalConMunicipioValido' => $rows->sum('total'),
                'municipiosValidosCount' => count($municipiosValidos),
                'municipiosEncontradosCount' => $rows->count(),
                'diferencia' => $totalAntesFiltro - $rows->sum('total')
            ]);
    
            // Adjuntar nombre sin disparar consultas por ítem
            return $rows->map(function ($item) use ($mapMunicipios) {
                $item->municipio = (object)[
                    'municipio_id'            => $item->municipality_id,
                    'municipioNOMBREOFICIAL'  => $mapMunicipios[$item->municipality_id] ?? ('Municipio ID: ' . $item->municipality_id),
                ];
                return $item;
            });
    
        } catch (\Exception $e) {
            \Log::error('Error en getMunicipiosStatsCompletos', ['error' => $e->getMessage()]);
            return collect();
        }
    }
    

    /**
     * Obtiene estadísticas por sectores optimizadas
     */
    private function getSectoresStats($query, $filtros)
    {
        try {
            // Diccionario: sector_id => sectorNOMBRE (una sola consulta)
            $mapSectores = DB::table('ciiu_macrosectores')
                ->pluck('sectorNOMBRE', 'sector_id');
    
            // Agregado en BD (TOP 8)
            $rows = (clone $query)
                ->select('sector_id', DB::raw('COUNT(*) AS total'))
                ->whereNotNull('sector_id')
                ->groupBy('sector_id')
                ->orderByDesc('total')
                ->limit(8)
                ->get();
    
            // Adjuntar nombre sin disparar consultas por ítem
            return $rows->map(function ($item) use ($mapSectores) {
                $item->sector = (object)[
                    'sector_id'    => $item->sector_id,
                    'sectorNOMBRE' => $mapSectores[$item->sector_id] ?? ('Sector ID: ' . $item->sector_id),
                ];
                return $item;
            });
    
        } catch (\Exception $e) {
            \Log::error('Error en getSectoresStats', ['error' => $e->getMessage()]);
            return collect();
        }
    }
    
    /**
     * Obtiene datos del mapa optimizados
     */
    private function getMapaStats($query, $filtros)
    {
        try {
            // Diccionario: municipio_id => nombre (una sola consulta)
            $mapMunicipios = Municipio::pluck('municipioNOMBREOFICIAL', 'municipio_id');
    
            // IDs válidos (excluye 0 y solo los existentes en catálogo)
            $municipiosValidos = $mapMunicipios->keys()->filter(fn ($id) => (int)$id > 0)->all();
    
            if (empty($municipiosValidos)) {
                return collect(); // no hay municipios válidos
            }
    
            // Agregado en BD para el mapa (TOP 50 por concentración)
            $rows = (clone $query)
                ->select('municipality_id', 'geolocation', DB::raw('COUNT(*) AS total'))
                ->whereNotNull('geolocation')
                ->whereNotNull('municipality_id')
                ->whereIn('municipality_id', $municipiosValidos)
                ->groupBy('municipality_id', 'geolocation')
                ->orderByDesc('total')
                ->limit(50)
                ->get();
    
            // Adjuntar nombre del municipio sin N+1
            return $rows->map(function ($item) use ($mapMunicipios) {
                $item->municipio = (object)[
                    'municipio_id'            => $item->municipality_id,
                    'municipioNOMBREOFICIAL'  => $mapMunicipios[$item->municipality_id] ?? ('Municipio ID: ' . $item->municipality_id),
                ];
                return $item;
            });
    
        } catch (\Exception $e) {
            \Log::error('Error en getMapaStats', ['error' => $e->getMessage()]);
            return collect();
        }
    }
    

    /**
     * Obtiene estadísticas por mes optimizadas
     */
    private function getEvolucionTemporalStats($query, $filtros)
    {
        try {
            // Últimos 12 meses contando el mes actual
            $desde = now()->copy()->startOfMonth()->subMonths(11); // 11 atrás + mes actual = 12
            $hasta = now()->copy()->endOfMonth();
    
            // Agregado en BD por año/mes
            $stats = (clone $query)
                ->select(
                    DB::raw('YEAR(fecha_creacion)  AS year'),
                    DB::raw('MONTH(fecha_creacion) AS month'),
                    DB::raw('COUNT(*)             AS total')
                )
                ->whereNotNull('fecha_creacion')
                ->whereBetween('fecha_creacion', [$desde, $hasta])
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
    
            // Mapa rápido año-mes -> total
            $map = [];
            foreach ($stats as $row) {
                $map[$row->year . '-' . str_pad($row->month, 2, '0', STR_PAD_LEFT)] = (int) $row->total;
            }
    
            // Construir la serie continua de 12 meses (rellenando ceros)
            $nombresMeses = [1=>'Ene',2=>'Feb',3=>'Mar',4=>'Abr',5=>'May',6=>'Jun',7=>'Jul',8=>'Ago',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dic'];
            $meses = [];
    
            $cursor = $desde->copy();
            while ($cursor->lte($hasta)) {
                $y = $cursor->year;
                $m = $cursor->month;
                $key = $y . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
    
                $meses[] = [
                    'label' => $nombresMeses[$m] . ' ' . $y,
                    'total' => $map[$key] ?? 0,
                    'year'  => $y,
                    'month' => $m,
                ];
    
                $cursor->addMonth();
            }
    
            return $meses;
    
        } catch (\Exception $e) {
            \Log::error('Error en getEvolucionTemporalStats', ['error' => $e->getMessage()]);
            return [];
        }
    }
    

    /**
     * Obtiene estadísticas por tamaño de empresa optimizadas
     */
    private function getTamanosStats($query, $filtros)
    {
        try {
            // Diccionario: tamano_id => tamanoNOMBRE (una sola consulta)
            $mapTamanos = UnidadProductivaTamano::pluck('tamanoNOMBRE', 'tamano_id');
    
            // Agregado en BD (TOP 5)
            $rows = (clone $query)
                ->select('tamano_id', DB::raw('COUNT(*) AS total'))
                ->whereNotNull('tamano_id')
                ->groupBy('tamano_id')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
    
            // Adjuntar nombre sin disparar consultas por ítem
            return $rows->map(function ($item) use ($mapTamanos) {
                $item->tamano = (object)[
                    'tamano_id'    => $item->tamano_id,
                    'tamanoNOMBRE' => $mapTamanos[$item->tamano_id] ?? ('Tamaño ID: ' . $item->tamano_id),
                ];
                return $item;
            });
    
        } catch (\Exception $e) {
            \Log::error('Error en getTamanosStats', ['error' => $e->getMessage()]);
            return collect();
        }
    }
    

    // Métodos de cache para datos de filtros (tiempo de vida extendido)
    private function getDepartamentos()
    {
        try {
            return Departamento::select('departamento_id', 'departamentoNOMBRE')
                ->orderBy('departamentoNOMBRE')
                ->limit(30)
                ->get();
        } catch (\Exception $e) {
            \Log::error('Error en getDepartamentos', ['error' => $e->getMessage()]);
            return collect();
        }
    }
    

    private function getMunicipios()
    {
        try {
            return Municipio::select('municipio_id', 'municipioNOMBREOFICIAL')
                ->orderBy('municipioNOMBREOFICIAL')              
                ->get();
        } catch (\Exception $e) {
            \Log::error('Error en getMunicipios', ['error' => $e->getMessage()]);
            return collect(); // colección vacía en caso de error
        }
    }
    

    private function getSectores()
    {
        try {
            \Log::info('=== INICIANDO CARGA DE SECTORES ===');
    
            // Traer sectores directamente desde la tabla
            $sectores = DB::table('ciiu_macrosectores')
                ->select('sector_id', 'sectorNOMBRE')
                ->whereNotNull('sectorNOMBRE')
                ->orderBy('sectorNOMBRE')
                ->get();
    
            if ($sectores->isEmpty()) {
                \Log::warning('No se encontraron sectores en la tabla ciiu_macrosectores');
            } else {
                \Log::info('Sectores cargados desde BD', [
                    'count' => $sectores->count()
                ]);
            }
    
            \Log::info('=== FINALIZANDO CARGA DE SECTORES ===');
            return $sectores;
    
        } catch (\Exception $e) {
            \Log::error('Error en getSectores', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine()
            ]);
            return collect(); // Retornar colección vacía en caso de error
        }
    }
    

    private function getEtapas()
    {
        try {
            return Etapa::select('etapa_id', 'name')
                ->limit(15)
                ->get();
        } catch (\Exception $e) {
            \Log::error('Error en getEtapas', ['error' => $e->getMessage()]);
            return collect(); // colección vacía en caso de error
        }
    }
    

    private function getTamanos()
{
    try {
        return UnidadProductivaTamano::select('tamano_id', 'tamanoNOMBRE')
            ->orderBy('tamanoNOMBRE')
            ->limit(8)
            ->get();
    } catch (\Exception $e) {
        \Log::error('Error en getTamanos', ['error' => $e->getMessage()]);
        return collect(); // Retorna colección vacía en caso de error
    }
}


private function getTiposPersona()
{
    try {
        return UnidadProductivaPersona::select('tipopersona_id', 'tipoPersonaNOMBRE')
            ->orderBy('tipoPersonaNOMBRE')
            ->limit(10)
            ->get();
    } catch (\Exception $e) {
        \Log::error('Error en getTiposPersona', ['error' => $e->getMessage()]);
        return collect(); // colección vacía en caso de error
    }
}



   

    // Método para cargar datos adicionales via AJAX (optimizado)
    public function loadMoreData(Request $request)
    {
        try {
            $offset = $request->get('offset', 0);
            $limit = $request->get('limit', 25); // Reducido de 50 a 25
            
            $query = UnidadProductiva::query()
                ->select(['unidadproductiva_id', 'business_name', 'nit', 'fecha_creacion'])
                ->offset($offset)
                ->limit($limit);
            
            $data = $query->get();
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'hasMore' => $data->count() === $limit
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en loadMoreData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar datos'
            ], 500);
        }
    }

// Método para estadísticas en tiempo real (sin caché)
public function getRealTimeStats(Request $request)
{
    try {
        $stats = [
            'total_hoy' => UnidadProductiva::whereDate('fecha_creacion', today())->count(),
            'total_semana' => UnidadProductiva::whereBetween(
                'fecha_creacion',
                [now()->startOfWeek(), now()->endOfWeek()]
            )->count(),
            'total_mes' => UnidadProductiva::whereMonth('fecha_creacion', now()->month)
                ->whereYear('fecha_creacion', now()->year)
                ->count(),
        ];

        return response()->json($stats);

    } catch (\Exception $e) {
        \Log::error('Error en getRealTimeStats: ' . $e->getMessage());
        return response()->json(['error' => 'Error al obtener estadísticas'], 500);
    }
}

}
