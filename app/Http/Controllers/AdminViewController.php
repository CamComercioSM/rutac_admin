<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empresarios\UnidadProductiva;
use App\Models\TablasReferencias\Departamento;
use App\Models\TablasReferencias\Municipio;
use App\Models\TablasReferencias\Sector;
use App\Models\TablasReferencias\Etapa;
use App\Models\TablasReferencias\UnidadProductivaTamano;
use App\Models\TablasReferencias\UnidadProductivaPersona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AdminViewController extends Controller
{
    function dashboard(Request $request)
    {
        try {
            // Obtener filtros de la request
            $filtros = [
                'departamento_id' => $request->get('departamento_id'),
                'municipio_id' => $request->get('municipio_id'),
                'sector_id' => $request->get('sector_id'),
                'etapa_id' => $request->get('etapa_id'),
                'tamano_id' => $request->get('tamano_id'),
                'tipopersona_id' => $request->get('tipopersona_id'),
                'fecha_desde' => $request->get('fecha_desde'),
                'fecha_hasta' => $request->get('fecha_hasta'),
            ];

            // Construir query base con límites para evitar timeouts
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
                    'fecha_creacion'
                ])
                ->limit(10000); // Límite máximo para evitar consultas muy pesadas

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

            // Estadísticas generales con cache
            $cacheKey = 'dashboard_stats_' . md5(serialize($filtros));
            $totalUnidades = Cache::remember($cacheKey . '_total', 300, function() use ($query) {
                return $query->count();
            });

            // Estadísticas por tipo de organización (limitado a top 10)
            $porTipoOrganizacion = Cache::remember($cacheKey . '_tipo_org', 300, function() use ($query) {
                return $query->clone()
                    ->select('tipopersona_id', DB::raw('count(*) as total'))
                    ->groupBy('tipopersona_id')
                    ->orderBy('total', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function($item) {
                        $tipoPersona = UnidadProductivaPersona::select('tipopersona_id', 'tipoPersonaNOMBRE')
                            ->find($item->tipopersona_id);
                        $item->tipoPersona = $tipoPersona;
                        return $item;
                    })
                    ->keyBy('tipopersona_id');
            });

            // Estadísticas por estado del diagnóstico (limitado)
            $porEstadoDiagnostico = Cache::remember($cacheKey . '_estado_diag', 300, function() use ($query) {
                return $query->clone()
                    ->select('complete_diagnostic', DB::raw('count(*) as total'))
                    ->groupBy('complete_diagnostic')
                    ->orderBy('total', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function($item) {
                        $item->estado = $item->complete_diagnostic ? 'Terminado' : 'Pendiente';
                        return $item;
                    });
            });

            // Estadísticas por etapas (limitado a top 8)
            $porEtapas = Cache::remember($cacheKey . '_etapas', 300, function() use ($query) {
                return $query->clone()
                    ->select('etapa_id', DB::raw('count(*) as total'))
                    ->groupBy('etapa_id')
                    ->orderBy('total', 'desc')
                    ->limit(8)
                    ->get()
                    ->map(function($item) {
                        $etapa = Etapa::select('etapa_id', 'name')->find($item->etapa_id);
                        $item->etapa = $etapa;
                        return $item;
                    })
                    ->keyBy('etapa_id');
            });

            // Estadísticas por tamaño y macro-sector (limitado)
            $porTamanoSector = Cache::remember($cacheKey . '_tamano_sector', 300, function() use ($query) {
                return $query->clone()
                    ->select('tamano_id', 'sector_id', DB::raw('count(*) as total'))
                    ->groupBy('tamano_id', 'sector_id')
                    ->orderBy('total', 'desc')
                    ->limit(50) // Limitar a 50 combinaciones
                    ->get()
                    ->map(function($item) {
                        $tamano = UnidadProductivaTamano::select('tamano_id', 'tamanoNOMBRE')->find($item->tamano_id);
                        $sector = Sector::select('sector_id', 'sectorNOMBRE')->find($item->sector_id);
                        $item->tamano = $tamano;
                        $item->sector = $sector;
                        return $item;
                    });
            });

            // Estadísticas por municipios (solo top 10)
            $porMunicipios = Cache::remember($cacheKey . '_municipios', 300, function() use ($query) {
                return $query->clone()
                    ->select('municipality_id', DB::raw('count(*) as total'))
                    ->groupBy('municipality_id')
                    ->orderBy('total', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function($item) {
                        $municipio = Municipio::select('municipio_id', 'municipionombreoficial')->find($item->municipality_id);
                        $item->municipio = $municipio;
                        return $item;
                    });
            });

            // Datos para el mapa (limitado a 100 ubicaciones)
            $datosMapa = Cache::remember($cacheKey . '_mapa', 300, function() use ($query) {
                return $query->clone()
                    ->select('municipality_id', 'geolocation', DB::raw('count(*) as total'))
                    ->whereNotNull('geolocation')
                    ->groupBy('municipality_id', 'geolocation')
                    ->orderBy('total', 'desc')
                    ->limit(100) // Solo 100 ubicaciones para el mapa
                    ->get()
                    ->map(function($item) {
                        $municipio = Municipio::select('municipio_id', 'municipionombreoficial')->find($item->municipality_id);
                        $item->municipio = $municipio;
                        return $item;
                    });
            });

            // Obtener datos para los filtros (con cache)
            $departamentos = Cache::remember('departamentos_all', 600, function() {
                return Departamento::select('departamento_id', 'departamentonombre')
                    ->orderBy('departamentonombre')
                    ->limit(50) // Solo top 50 departamentos
                    ->get();
            });

            $municipios = Cache::remember('municipios_all', 600, function() {
                return Municipio::select('municipio_id', 'municipionombreoficial')
                    ->orderBy('municipionombreoficial')
                    ->limit(200) // Solo top 200 municipios
                    ->get();
            });

            $sectores = Cache::remember('sectores_all', 600, function() {
                return Sector::select('sector_id', 'sectorNOMBRE')
                    ->orderBy('sectorNOMBRE')
                    ->limit(30) // Solo top 30 sectores
                    ->get();
            });

            $etapas = Cache::remember('etapas_all', 600, function() {
                return Etapa::select('etapa_id', 'name')
                    ->orderBy('name')
                    ->limit(20) // Solo top 20 etapas
                    ->get();
            });

            $tamanos = Cache::remember('tamanos_all', 600, function() {
                return UnidadProductivaTamano::select('tamano_id', 'tamanoNOMBRE')
                    ->orderBy('tamanoNOMBRE')
                    ->limit(10) // Solo top 10 tamaños
                    ->get();
            });

            $tiposPersona = Cache::remember('tipos_persona_all', 600, function() {
                return UnidadProductivaPersona::select('tipopersona_id', 'tipoPersonaNOMBRE')
                    ->orderBy('tipoPersonaNOMBRE')
                    ->limit(15) // Solo top 15 tipos
                    ->get();
            });

            return View("dashboard", compact(
                'totalUnidades',
                'porTipoOrganizacion',
                'porEstadoDiagnostico',
                'porEtapas',
                'porTamanoSector',
                'porMunicipios',
                'datosMapa',
                'departamentos',
                'municipios',
                'sectores',
                'etapas',
                'tamanos',
                'tiposPersona',
                'filtros'
            ));

        } catch (\Exception $e) {
            // Log del error
            \Log::error('Error en dashboard: ' . $e->getMessage());
            
            // Retornar vista con datos mínimos en caso de error
            return View("dashboard", [
                'totalUnidades' => 0,
                'porTipoOrganizacion' => collect(),
                'porEstadoDiagnostico' => collect(),
                'porEtapas' => collect(),
                'porTamanoSector' => collect(),
                'porMunicipios' => collect(),
                'datosMapa' => collect(),
                'departamentos' => collect(),
                'municipios' => collect(),
                'sectores' => collect(),
                'etapas' => collect(),
                'tamanos' => collect(),
                'tiposPersona' => collect(),
                'filtros' => [],
                'error' => 'Error al cargar datos. Intente nuevamente.'
            ]);
        }
    }

    // Método para cargar datos adicionales via AJAX
    public function loadMoreData(Request $request)
    {
        try {
            $offset = $request->get('offset', 0);
            $limit = $request->get('limit', 50);
            
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
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar datos'
            ], 500);
        }
    }

    // Método para estadísticas en tiempo real
    public function getRealTimeStats(Request $request)
    {
        try {
            $cacheKey = 'realtime_stats_' . date('Y-m-d-H');
            
            $stats = Cache::remember($cacheKey, 60, function() {
                return [
                    'total_hoy' => UnidadProductiva::whereDate('fecha_creacion', today())->count(),
                    'total_semana' => UnidadProductiva::whereBetween('fecha_creacion', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                    'total_mes' => UnidadProductiva::whereMonth('fecha_creacion', now()->month)->count()
                ];
            });
            
            return response()->json($stats);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener estadísticas'], 500);
        }
    }
}
