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
            \Log::info('Dashboard iniciando...');
            
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

            \Log::info('Filtros aplicados', ['filtros' => $filtros]);

            // Generar clave de cache única basada en filtros
            $cacheKey = 'dashboard_' . md5(serialize($filtros));
            
            // Intentar obtener datos del cache primero
            $cachedData = Cache::get($cacheKey);
            if ($cachedData && !$request->has('refresh')) {
                \Log::info('Usando datos del cache');
                return view("dashboard", $cachedData);
            }

            \Log::info('Cache no encontrado, cargando datos frescos...');

            // Construir query base optimizada
            $query = $this->buildOptimizedQuery($filtros);

            // Cargar datos principales
            $dashboardData = $this->loadDashboardDataAsync($query, $filtros);

            \Log::info('Datos del dashboard cargados', [
                'totalUnidades' => $dashboardData['totalUnidades'] ?? 0,
                'sectores_count' => is_object($dashboardData['sectores']) ? $dashboardData['sectores']->count() : count($dashboardData['sectores'] ?? []),
                'departamentos_count' => is_object($dashboardData['departamentos']) ? $dashboardData['departamentos']->count() : count($dashboardData['departamentos'] ?? [])
            ]);

            // Guardar en cache por 15 minutos
            Cache::put($cacheKey, $dashboardData, 900);

            return view("dashboard", $dashboardData);

        } catch (\Exception $e) {
            \Log::error('Error en dashboard: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Retornar vista con datos mínimos en caso de error
            $backendData = [
                'porTipoOrganizacion' => [],
                'porEstadoDiagnostico' => [],
                'porEtapas' => [],
                'porMunicipios' => [],
                'evolucionTemporal' => [],
                'porTamanos' => [],
                'porSectores' => [],
                'datosMapa' => []
            ];

            return view("dashboard", [
                'totalUnidades' => 0,
                'backendData' => $backendData,
                'departamentos' => [],
                'municipios' => [],
                'sectores' => [],
                'etapas' => [],
                'tamanos' => [],
                'tiposPersona' => [],
                'filtros' => $filtros ?? [],
                'error' => 'Error al cargar datos. Intente nuevamente.'
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

    /**
     * Carga datos del dashboard de forma asíncrona y optimizada
     */
    private function loadDashboardDataAsync($query, $filtros)
    {
        // Estadísticas generales con cache extendido (SIN límite para conteo total)
        $totalUnidades = Cache::remember('dashboard_total_' . md5(serialize($filtros)), 1800, function() use ($query) {
            return $query->count();
        });

        // Cargar datos de filtros desde cache (más tiempo de vida)
        $departamentos = $this->getCachedDepartamentos();
        $municipios = $this->getCachedMunicipios();
        $sectores = $this->getCachedSectores();
        $etapas = $this->getCachedEtapas();
        $tamanos = $this->getCachedTamanos();
        $tiposPersona = $this->getCachedTiposPersona();

        // Crear query limitada solo para estadísticas que lo requieran
        $queryLimitada = $query->clone()->limit(10000); // Límite solo para estadísticas

        // Estadísticas por tipo de organización (optimizado)
        $porTipoOrganizacion = $this->getTipoOrganizacionStats($queryLimitada, $filtros);

        // Estadísticas por estado del diagnóstico (optimizado)
        $porEstadoDiagnostico = $this->getEstadoDiagnosticoStats($queryLimitada, $filtros);

        // Estadísticas por etapas (optimizado)
        $porEtapas = $this->getEtapasStats($queryLimitada, $filtros);

        // Estadísticas por municipios (solo top 10 para la tabla)
        $porMunicipios = $this->getMunicipiosStats($queryLimitada, $filtros);
        
        // Estadísticas por municipios (TODOS para proporciones)
        $porMunicipiosCompletos = $this->getMunicipiosStatsCompletos($queryLimitada, $filtros);

        // Estadísticas de evolución temporal (últimos 12 meses)
        $evolucionTemporal = $this->getEvolucionTemporalStats($queryLimitada, $filtros);

        // Estadísticas por tamaños de empresa
        $porTamanos = $this->getTamanosStats($queryLimitada, $filtros);

        // Datos para el mapa (limitado a 50 ubicaciones)
        $datosMapa = $this->getMapaStats($queryLimitada, $filtros);

        // Estadísticas por sectores
        $porSectores = $this->getSectoresStats($queryLimitada, $filtros);

        // Crear array backendData para JavaScript
        $backendData = [
            'porTipoOrganizacion' => $porTipoOrganizacion,
            'porEstadoDiagnostico' => $porEstadoDiagnostico,
            'porEtapas' => $porEtapas,
            'porMunicipios' => $porMunicipios,
            'porMunicipiosCompletos' => $porMunicipiosCompletos, // Todos los municipios para proporciones
            'evolucionTemporal' => $evolucionTemporal,
            'porTamanos' => $porTamanos,
            'porSectores' => $porSectores,
            'datosMapa' => $datosMapa,
            'totalUnidades' => $totalUnidades
        ];
        
        // Log temporal para depuración
        \Log::info('Dashboard data debug', [
            'totalUnidades' => $totalUnidades,
            'porMunicipios_count' => $porMunicipios->count(),
            'porMunicipiosCompletos_count' => $porMunicipiosCompletos->count(),
            'porMunicipiosCompletos_sample' => $porMunicipiosCompletos->take(3)->toArray()
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
        $cacheKey = 'tipo_org_' . md5(serialize($filtros));
        
        return Cache::remember($cacheKey, 1800, function() use ($query) {
            try {
                $stats = $query->clone()
                    ->select('tipopersona_id', DB::raw('count(*) as total'))
                    ->whereNotNull('tipopersona_id')
                    ->groupBy('tipopersona_id')
                    ->orderBy('total', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function($item) {
                        try {
                            $tipoPersona = UnidadProductivaPersona::select('tipopersona_id', 'tipoPersonaNOMBRE')
                                ->where('tipopersona_id', $item->tipopersona_id)
                                ->first();
                            
                            if ($tipoPersona) {
                                $item->tipoPersona = $tipoPersona;
                            } else {
                                $item->tipoPersona = (object) [
                                    'tipopersona_id' => $item->tipopersona_id,
                                    'tipoPersonaNOMBRE' => 'Tipo ID: ' . $item->tipopersona_id
                                ];
                            }
                            
                            return $item;
                        } catch (\Exception $e) {
                            $item->tipoPersona = (object) [
                                'tipopersona_id' => $item->tipopersona_id,
                                'tipoPersonaNOMBRE' => 'Error al cargar tipo'
                            ];
                            return $item;
                        }
                    });
                
                return $stats; // Removido ->toArray() para mantener colección
            } catch (\Exception $e) {
                \Log::error('Error en getTipoOrganizacionStats', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Obtiene estadísticas por estado del diagnóstico optimizadas
     */
    private function getEstadoDiagnosticoStats($query, $filtros)
    {
        $cacheKey = 'estado_diag_' . md5(serialize($filtros));
        
        return Cache::remember($cacheKey, 1800, function() use ($query) {
            return $query->clone()
                ->select('complete_diagnostic', DB::raw('count(*) as total'))
                ->groupBy('complete_diagnostic')
                ->orderBy('total', 'desc')
                ->get()
                ->map(function($item) {
                    $item->estado = $item->complete_diagnostic ? 'Terminado' : 'Pendiente';
                    return $item;
                });
        });
    }

    /**
     * Obtiene estadísticas por etapas optimizadas
     */
    private function getEtapasStats($query, $filtros)
    {
        $cacheKey = 'etapas_' . md5(serialize($filtros));
        
        return Cache::remember($cacheKey, 1800, function() use ($query) {
            return $query->clone()
                ->select('etapa_id', DB::raw('count(*) as total'))
                ->groupBy('etapa_id')
                ->orderBy('total', 'desc')
                ->limit(5) // Reducido de 8 a 5
                ->get()
                ->map(function($item) {
                    $etapa = Etapa::select('etapa_id', 'name')->find($item->etapa_id);
                    $item->etapa = $etapa;
                    return $item;
                });
        });
    }

    /**
     * Obtiene estadísticas por municipios optimizadas
     */
    private function getMunicipiosStats($query, $filtros)
    {
        $cacheKey = 'municipios_' . md5(serialize($filtros));
        
        return Cache::remember($cacheKey, 1800, function() use ($query) {
            try {
                // Obtener solo municipios válidos (que existan en la tabla municipios)
                $municipiosValidos = DB::table('municipios')
                    ->where('municipio_id', '>', 0) // Excluir el ID 0 (SIN MUNICIPIO)
                    ->pluck('municipio_id')
                    ->toArray();
                
                if (empty($municipiosValidos)) {
                    return collect(); // Retornar colección vacía si no hay municipios válidos
                }
                
                return $query->clone()
                    ->select('municipality_id', DB::raw('count(*) as total'))
                    ->whereNotNull('municipality_id')
                    ->whereIn('municipality_id', $municipiosValidos) // Solo municipios válidos
                    ->groupBy('municipality_id')
                    ->orderBy('total', 'desc')
                    ->limit(8)
                    ->get()
                    ->map(function($item) {
                        try {
                            // Obtener el municipio con la relación correcta
                            $municipio = Municipio::select('municipio_id', 'municipioNOMBREOFICIAL')
                                ->where('municipio_id', $item->municipality_id)
                                ->first();
                            
                            if ($municipio) {
                                $item->municipio = $municipio;
                            } else {
                                // Si no existe el municipio, crear un objeto por defecto
                                $item->municipio = (object) [
                                    'municipio_id' => $item->municipality_id,
                                    'municipioNOMBREOFICIAL' => 'Municipio ID: ' . $item->municipality_id
                                ];
                            }
                            
                            return $item;
                        } catch (\Exception $e) {
                            // En caso de error, retornar item con municipio por defecto
                            $item->municipio = (object) [
                                'municipio_id' => $item->municipality_id,
                                'municipioNOMBREOFICIAL' => 'Error al cargar municipio'
                            ];
                            return $item;
                        }
                    });
            } catch (\Exception $e) {
                \Log::error('Error en getMunicipiosStats', ['error' => $e->getMessage()]);
                return collect(); // Retornar colección vacía en caso de error
            }
        });
    }

    /**
     * Obtiene estadísticas por municipios COMPLETOS (sin límite para proporciones)
     */
    private function getMunicipiosStatsCompletos($query, $filtros)
    {
        $cacheKey = 'municipios_completos_' . md5(serialize($filtros));
        
        return Cache::remember($cacheKey, 1800, function() use ($query) {
            try {
                // Obtener solo municipios válidos (que existan en la tabla municipios)
                $municipiosValidos = DB::table('municipios')
                    ->where('municipio_id', '>', 0) // Excluir el ID 0 (SIN MUNICIPIO)
                    ->pluck('municipio_id')
                    ->toArray();
                
                if (empty($municipiosValidos)) {
                    return collect(); // Retornar colección vacía si no hay municipios válidos
                }
                
                return $query->clone()
                    ->select('municipality_id', DB::raw('count(*) as total'))
                    ->whereNotNull('municipality_id')
                    ->whereIn('municipality_id', $municipiosValidos) // Solo municipios válidos
                    ->groupBy('municipality_id')
                    ->orderBy('total', 'desc')
                    ->get() // SIN LÍMITE para incluir todos los municipios
                    ->map(function($item) {
                        try {
                            // Obtener el municipio con la relación correcta
                            $municipio = Municipio::select('municipio_id', 'municipioNOMBREOFICIAL')
                                ->where('municipio_id', $item->municipality_id)
                                ->first();
                            
                            if ($municipio) {
                                $item->municipio = $municipio;
                            } else {
                                // Si no existe el municipio, crear un objeto por defecto
                                $item->municipio = (object) [
                                    'municipio_id' => $item->municipality_id,
                                    'municipioNOMBREOFICIAL' => 'Municipio ID: ' . $item->municipality_id
                                ];
                            }
                            
                            return $item;
                        } catch (\Exception $e) {
                            // En caso de error, retornar item con municipio por defecto
                            $item->municipio = (object) [
                                'municipio_id' => $item->municipality_id,
                                'municipioNOMBREOFICIAL' => 'Error al cargar municipio'
                            ];
                            return $item;
                        }
                    });
            } catch (\Exception $e) {
                \Log::error('Error en getMunicipiosStatsCompletos', ['error' => $e->getMessage()]);
                return collect(); // Retornar colección vacía en caso de error
            }
        });
    }

    /**
     * Obtiene estadísticas por sectores optimizadas
     */
    private function getSectoresStats($query, $filtros)
    {
        $cacheKey = 'sectores_stats_' . md5(serialize($filtros));
        
        return Cache::remember($cacheKey, 1800, function() use ($query) {
            try {
                return $query->clone()
                    ->select('sector_id', DB::raw('count(*) as total'))
                    ->whereNotNull('sector_id')
                    ->groupBy('sector_id')
                    ->orderBy('total', 'desc')
                    ->limit(8)
                    ->get()
                    ->map(function($item) {
                        try {
                            // Obtener el sector con la relación correcta
                            $sector = DB::table('ciiu_macrosectores')
                                ->select('sector_id', 'sectorNOMBRE')
                                ->where('sector_id', $item->sector_id)
                                ->first();
                            
                            if ($sector) {
                                $item->sector = $sector;
                            } else {
                                // Si no existe el sector, crear un objeto por defecto
                                $item->sector = (object) [
                                    'sector_id' => $item->sector_id,
                                    'sectorNOMBRE' => 'Sector ID: ' . $item->sector_id
                                ];
                            }
                            
                            return $item;
                        } catch (\Exception $e) {
                            // En caso de error, retornar item con sector por defecto
                            $item->sector = (object) [
                                'sector_id' => $item->sector_id,
                                'sectorNOMBRE' => 'Error al cargar sector'
                            ];
                            return $item;
                        }
                    });
            } catch (\Exception $e) {
                \Log::error('Error en getSectoresStats', ['error' => $e->getMessage()]);
                return collect(); // Retornar colección vacía en caso de error
            }
        });
    }

    /**
     * Obtiene datos del mapa optimizados
     */
    private function getMapaStats($query, $filtros)
    {
        $cacheKey = 'mapa_' . md5(serialize($filtros));
        
        return Cache::remember($cacheKey, 1800, function() use ($query) {
            try {
                // Obtener solo municipios válidos (que existan en la tabla municipios)
                $municipiosValidos = DB::table('municipios')
                    ->where('municipio_id', '>', 0) // Excluir el ID 0 (SIN MUNICIPIO)
                    ->pluck('municipio_id')
                    ->toArray();
                
                if (empty($municipiosValidos)) {
                    return collect(); // Retornar colección vacía si no hay municipios válidos
                }
                
                return $query->clone()
                    ->select('municipality_id', 'geolocation', DB::raw('count(*) as total'))
                    ->whereNotNull('geolocation')
                    ->whereNotNull('municipality_id')
                    ->whereIn('municipality_id', $municipiosValidos) // Solo municipios válidos
                    ->groupBy('municipality_id', 'geolocation')
                    ->orderBy('total', 'desc')
                    ->limit(50)
                    ->get()
                    ->map(function($item) {
                        try {
                            // Obtener el municipio con la relación correcta
                            $municipio = Municipio::select('municipio_id', 'municipioNOMBREOFICIAL')
                                ->where('municipio_id', $item->municipality_id)
                                ->first();
                            
                            if ($municipio) {
                                $item->municipio = $municipio;
                            } else {
                                // Si no existe el municipio, crear un objeto por defecto
                                $item->municipio = (object) [
                                    'municipio_id' => $item->municipality_id,
                                    'municipioNOMBREOFICIAL' => 'Municipio ID: ' . $item->municipality_id
                                ];
                            }
                            
                            return $item;
                        } catch (\Exception $e) {
                            // En caso de error, retornar item con municipio por defecto
                            $item->municipio = (object) [
                                'municipio_id' => $item->municipality_id,
                                'municipioNOMBREOFICIAL' => 'Error al cargar municipio'
                            ];
                            return $item;
                        }
                    });
            } catch (\Exception $e) {
                \Log::error('Error en getMapaStats', ['error' => $e->getMessage()]);
                return collect(); // Retornar colección vacía en caso de error
            }
        });
    }

    /**
     * Obtiene estadísticas por mes optimizadas
     */
    private function getEvolucionTemporalStats($query, $filtros)
    {
        $cacheKey = 'evolucion_temporal_' . md5(serialize($filtros));
        
        return Cache::remember($cacheKey, 1800, function() use ($query) {
            try {
                // Obtener estadísticas de los últimos 12 meses
                $stats = $query->clone()
                    ->select(
                        DB::raw('YEAR(fecha_creacion) as year'),
                        DB::raw('MONTH(fecha_creacion) as month'),
                        DB::raw('count(*) as total')
                    )
                    ->whereNotNull('fecha_creacion')
                    ->where('fecha_creacion', '>=', now()->subMonths(12))
                    ->groupBy('year', 'month')
                    ->orderBy('year', 'asc')
                    ->orderBy('month', 'asc')
                    ->get();
                
                // Crear array con todos los meses (incluso los que no tienen datos)
                $meses = [];
                $nombresMeses = [
                    1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
                    7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
                ];
                
                for ($i = 11; $i >= 0; $i--) {
                    $fecha = now()->subMonths($i);
                    $year = $fecha->year;
                    $month = $fecha->month;
                    $key = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
                    
                    // Buscar si hay datos para este mes
                    $mesData = $stats->where('year', $year)->where('month', $month)->first();
                    $total = $mesData ? $mesData->total : 0;
                    
                    $meses[] = [
                        'label' => $nombresMeses[$month] . ' ' . $year,
                        'total' => $total,
                        'year' => $year,
                        'month' => $month
                    ];
                }
                
                return $meses;
                
            } catch (\Exception $e) {
                \Log::error('Error en getEvolucionTemporalStats', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Obtiene estadísticas por tamaño de empresa optimizadas
     */
    private function getTamanosStats($query, $filtros)
    {
        $cacheKey = 'tamanos_' . md5(serialize($filtros));
        
        return Cache::remember($cacheKey, 1800, function() use ($query) {
            try {
                return $query->clone()
                    ->select('tamano_id', DB::raw('count(*) as total'))
                    ->whereNotNull('tamano_id')
                    ->groupBy('tamano_id')
                    ->orderBy('total', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function($item) {
                        $tamano = UnidadProductivaTamano::select('tamano_id', 'tamanoNOMBRE')->find($item->tamano_id);
                        $item->tamano = $tamano;
                        return $item;
                    });
            } catch (\Exception $e) {
                \Log::error('Error en getTamanosStats', ['error' => $e->getMessage()]);
                return collect();
            }
        });
    }

    // Métodos de cache para datos de filtros (tiempo de vida extendido)
    private function getCachedDepartamentos()
    {
        return Cache::remember('departamentos_all', 3600, function() {
            try {
                $departamentos = Departamento::select('departamento_id', 'departamentoNOMBRE')
                    ->orderBy('departamentoNOMBRE')
                    ->limit(30)
                    ->get();
                return $departamentos; // Removido ->toArray() para mantener objetos
            } catch (\Exception $e) {
                \Log::error('Error en getCachedDepartamentos', ['error' => $e->getMessage()]);
                return collect(); // Retornar colección vacía en lugar de array vacío
            }
        });
    }

    private function getCachedMunicipios()
    {
        return Cache::remember('municipios_all', 3600, function() {
            try {
                $municipios = Municipio::select('municipio_id', 'municipioNOMBREOFICIAL')
                    ->orderBy('municipioNOMBREOFICIAL')
                    ->limit(100)
                    ->get();
                return $municipios; // Removido ->toArray() para mantener objetos
            } catch (\Exception $e) {
                \Log::error('Error en getCachedMunicipios', ['error' => $e->getMessage()]);
                return collect(); // Retornar colección vacía en lugar de array vacío
            }
        });
    }

    private function getCachedSectores()
    {
        return Cache::remember('sectores_all', 3600, function() {
            try {
                \Log::info('=== INICIANDO CARGA DE SECTORES ===');
                
                // Verificar si la tabla existe
                try {
                    $tableExists = DB::select("SELECT to_regclass('ciiu_macrosectores')");
                    \Log::info('Verificación de tabla ciiu_macrosectores', ['tableExists' => $tableExists]);
                } catch (\Exception $e) {
                    \Log::error('Error al verificar tabla ciiu_macrosectores', ['error' => $e->getMessage()]);
                }
                
                // Intentar contar registros
                try {
                    $count = DB::table('ciiu_macrosectores')->count();
                    \Log::info('Total de registros en ciiu_macrosectores', ['count' => $count]);
                } catch (\Exception $e) {
                    \Log::error('Error al contar registros', ['error' => $e->getMessage()]);
                }
                
                // Usar la tabla ciiu_macrosectores que ya existe
                $sectores = DB::table('ciiu_macrosectores')
                    ->select('sector_id', 'sectorNOMBRE')
                    ->whereNotNull('sectorNOMBRE')
                    ->orderBy('sectorNOMBRE')
                    ->get();
                
                \Log::info('Sectores cargados desde BD', [
                    'count' => $sectores->count(),
                    'data' => $sectores->toArray()
                ]);
                
                // Si no hay sectores, crear algunos de ejemplo
                if ($sectores->count() == 0) {
                    \Log::info('No hay sectores en BD, creando datos de ejemplo');
                    $sectores = collect([
                        (object) ['sector_id' => 1, 'sectorNOMBRE' => 'Manufactura'],
                        (object) ['sector_id' => 2, 'sectorNOMBRE' => 'Servicios'],
                        (object) ['sector_id' => 3, 'sectorNOMBRE' => 'Comercio'],
                        (object) ['sector_id' => 4, 'sectorNOMBRE' => 'Agricultura'],
                        (object) ['sector_id' => 5, 'sectorNOMBRE' => 'Construcción']
                    ]);
                    \Log::info('Datos de ejemplo creados', ['sectores' => $sectores->toArray()]);
                }
                
                \Log::info('=== FINALIZANDO CARGA DE SECTORES ===');
                return $sectores;
                
            } catch (\Exception $e) {
                \Log::error('Error en getCachedSectores: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Retornar datos de ejemplo en caso de error
                $sectoresEjemplo = collect([
                    (object) ['sector_id' => 1, 'sectorNOMBRE' => 'Manufactura'],
                    (object) ['sector_id' => 2, 'sectorNOMBRE' => 'Servicios'],
                    (object) ['sector_id' => 3, 'sectorNOMBRE' => 'Comercio'],
                    (object) ['sector_id' => 4, 'sectorNOMBRE' => 'Agricultura'],
                    (object) ['sector_id' => 5, 'sectorNOMBRE' => 'Construcción']
                ]);
                
                \Log::info('Retornando datos de ejemplo por error', ['sectores' => $sectoresEjemplo->toArray()]);
                return $sectoresEjemplo;
            }
        });
    }

    private function getCachedEtapas()
    {
        return Cache::remember('etapas_all', 3600, function() {
            try {
                $etapas = Etapa::select('etapa_id', 'name')
                    ->limit(15)
                    ->get();
                return $etapas; // Removido ->toArray() para mantener objetos
            } catch (\Exception $e) {
                \Log::error('Error en getCachedEtapas', ['error' => $e->getMessage()]);
                return collect(); // Retornar colección vacía en lugar de array vacío
            }
        });
    }

    private function getCachedTamanos()
    {
        return Cache::remember('tamanos_all', 3600, function() {
            try {
                $tamanos = UnidadProductivaTamano::select('tamano_id', 'tamanoNOMBRE')
                    ->orderBy('tamanoNOMBRE')
                    ->limit(8)
                    ->get();
                return $tamanos; // Removido ->toArray() para mantener objetos
            } catch (\Exception $e) {
                \Log::error('Error en getCachedTamanos', ['error' => $e->getMessage()]);
                return collect(); // Retornar colección vacía en lugar de array vacío
            }
        });
    }

    private function getCachedTiposPersona()
    {
        return Cache::remember('tipos_persona_all', 3600, function() {
            try {
                $tipos = UnidadProductivaPersona::select('tipopersona_id', 'tipoPersonaNOMBRE')
                    ->orderBy('tipoPersonaNOMBRE')
                    ->limit(10)
                    ->get();
                return $tipos; // Removido ->toArray() para mantener objetos
            } catch (\Exception $e) {
                \Log::error('Error en getCachedTiposPersona', ['error' => $e->getMessage()]);
                return collect(); // Retornar colección vacía en lugar de array vacío
            }
        });
    }

    /**
     * Limpia el cache de sectores para forzar recarga
     */
    public function clearSectoresCache()
    {
        try {
            Cache::forget('sectores_all');
            \Log::info('Cache de sectores limpiado exitosamente');
            return response()->json(['success' => true, 'message' => 'Cache limpiado']);
        } catch (\Exception $e) {
            \Log::error('Error al limpiar cache de sectores', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error al limpiar cache: ' . $e->getMessage()]);
        }
    }

    /**
     * Debug de sectores - fuerza recarga completa
     */
    public function debugSectores()
    {
        try {
            \Log::info('=== DEBUG SECTORES INICIADO ===');
            
            // Limpiar todos los caches relacionados
            Cache::forget('sectores_all');
            Cache::forget('departamentos_all');
            Cache::forget('municipios_all');
            
            // Forzar recarga de sectores
            $sectores = $this->getCachedSectores();
            
            \Log::info('Sectores después de debug', ['sectores' => $sectores->toArray()]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Debug completado',
                'sectores_count' => is_object($sectores) ? $sectores->count() : count($sectores)
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error en debugSectores', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error en debug: ' . $e->getMessage()]);
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

    // Método para estadísticas en tiempo real (optimizado)
    public function getRealTimeStats(Request $request)
    {
        try {
            $cacheKey = 'realtime_stats_' . date('Y-m-d-H');
            
            $stats = Cache::remember($cacheKey, 300, function() { // 5 minutos
                return [
                    'total_hoy' => UnidadProductiva::whereDate('fecha_creacion', today())->count(),
                    'total_semana' => UnidadProductiva::whereBetween('fecha_creacion', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                    'total_mes' => UnidadProductiva::whereMonth('fecha_creacion', now()->month)->count()
                ];
            });
            
            return response()->json($stats);
            
        } catch (\Exception $e) {
            Log::error('Error en getRealTimeStats: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener estadísticas'], 500);
        }
    }
}
