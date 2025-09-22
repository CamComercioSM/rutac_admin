@extends('layouts.layoutMaster')

@section('content')
<div class="dashboard-container">
    <!-- Indicador de Carga 
    <div id="loadingIndicator" class="loading-overlay" style="display: none;">
        <div class="loading-content">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-3 text-muted">Cargando dashboard...</p>
            <div class="loading-progress mt-2">
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>
-->
    <!-- Mensaje de Error -->
    @if(isset($error))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Error:</strong> {{ $error }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header del Dashboard -->
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="dashboard-title">
                    <i class="fas fa-chart-line text-primary me-3"></i>
                    Decídete a Crecer
                </h1>
                <p class="dashboard-subtitle text-muted">
                    Dashboard de Unidades Productivas - rutaC
                </p>
            </div>
     
        </div>
    </div>

    <!-- Filtros Rápidos -->
    <div class="quick-filters mb-4">
        <div class="row">
            <div class="col-md-3">
                <select class="form-select" id="periodoSelect" onchange="cambiarPeriodo(this.value)">
                    <option value="">Selecciona un periodo</option>
                    <option value="7">Últimos 7 días</option>
                    <option value="30">Últimos 30 días</option>
                    <option value="90">Últimos 3 meses</option>
                    <option value="365">Último año</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="departamentoSelect" onchange="filtrarPorDepartamento(this.value)">
                    <option value="">Todos los departamentos</option>
                    @if(isset($departamentos) && (is_object($departamentos) ? $departamentos->count() : count($departamentos)) > 0)
                        @foreach($departamentos as $departamento)
                            <option value="{{ $departamento->departamento_id }}" {{ $filtros['departamento_id'] == $departamento->departamento_id ? 'selected' : '' }}>
                                {{ $departamento->departamentoNOMBRE ?? 'Sin nombre' }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="sectorSelect" onchange="filtrarPorSector(this.value)">
                    <option value="">Todos los sectores</option>
                    @if(isset($sectores) && (is_object($sectores) ? $sectores->count() : count($sectores)) > 0)
                        @foreach($sectores as $sector)
                            <option value="{{ $sector->sector_id }}" {{ $filtros['sector_id'] == $sector->sector_id ? 'selected' : '' }}>
                                {{ $sector->sectorNOMBRE ?? 'Sin nombre' }}
                            </option>
                        @endforeach
                    @endif
                </select>
          
            </div>
            <div class="col-md-3">
                <select class="form-select" id="etapaSelect" onchange="filtrarPorEtapa(this.value)">
                    <option value="">Todas las etapas</option>
                    @if(isset($etapas) && (is_object($etapas) ? $etapas->count() : count($etapas)) > 0)
                        @foreach($etapas as $etapa)
                            <option value="{{ $etapa->etapa_id }}" {{ $filtros['etapa_id'] == $etapa->etapa_id ? 'selected' : '' }}>
                                {{ $etapa->name ?? 'Sin nombre' }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>

    <!-- Métrica Principal -->
    <div class="main-metric mb-4">
        <div class="card main-metric-card">
            <div class="card-body text-center">
                <div class="metric-icon">
                    <i class="fas fa-building fa-3x text-primary"></i>
                </div>
                <h2 class="metric-number" id="totalUnidadesMetric">{{ number_format($totalUnidades ?? 0) }}</h2>
                <p class="metric-label">Unidades Productivas</p>
                <div class="metric-trend" id="metricTrend">
                    <span class="badge bg-secondary">
                        <i class="fas fa-spinner fa-spin me-1"></i>Calculando...
                    </span>
                    <span class="text-muted ms-2">vs mes anterior</span>
                </div>
                <div class="metric-info mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Datos optimizados para mejor rendimiento
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos de Resumen con Carga Lazy -->
    <div class="charts-section mb-4">
        <div class="row">
            <!-- Distribución por Tipo de Organización -->
            <div class="col-md-6 mb-4">
                <div class="card summary-card" data-chart="tipoOrganizacion">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-users me-2 text-primary"></i>
                            Distribución por Tipo de Organización
                        </h6>
                        <div class="d-flex align-items-center">
                            <div class="chart-loading me-2" style="display: none;">
                                <div class="spinner-border spinner-border-sm text-primary"></div>
                            </div>
                            <div class="btn-group btn-group-sm me-2" role="group">
                                <input type="radio" class="btn-check" name="tipoOrganizacionChartType" id="tipoOrgDona" value="dona">
                                <label class="btn btn-outline-primary" for="tipoOrgDona">
                                    <i class="fas fa-chart-pie"></i> Dona
                                </label>
                                <input type="radio" class="btn-check" name="tipoOrganizacionChartType" id="tipoOrgBarra" value="barra" checked>
                                <label class="btn btn-outline-primary" for="tipoOrgBarra">
                                    <i class="fas fa-chart-bar"></i> Barra
                                </label>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="downloadChartAsImage('tipoOrganizacionChart', 'tipo_organizacion')" title="Descargar gráfico como imagen">
                                <i class="fas fa-image me-1"></i> Imagen
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container mb-3" style="position: relative; height: 200px;">
                            <canvas id="tipoOrganizacionChart"></canvas>
                        </div>
                        <div id="tipoOrganizacionContent">
                            @if(isset($porTipoOrganizacion) && (is_object($porTipoOrganizacion) ? $porTipoOrganizacion->count() : count($porTipoOrganizacion)) > 0)
                                @foreach($porTipoOrganizacion as $tipo)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">{{ $tipo->tipoPersona->tipoPersonaNOMBRE ?? 'No definido' }}</span>
                                        <span class="fw-bold">{{ $tipo->total }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted">
                                    <p>Natural: 2,157</p>
                                    <p>Jurídica: 750</p>
                                    <p>Establecimiento: 19</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estado del Diagnóstico Inicial -->
            <div class="col-md-6 mb-4">
                <div class="card summary-card" data-chart="estadoDiagnostico">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-clipboard-check me-2 text-success"></i>
                            Estado del Diagnóstico Inicial
                        </h6>
                        <div class="d-flex align-items-center">
                            <div class="chart-loading me-2" style="display: none;">
                                <div class="spinner-border spinner-border-sm text-success"></div>
                            </div>
                            <div class="btn-group btn-group-sm me-2" role="group">
                                <input type="radio" class="btn-check" name="estadoDiagnosticoChartType" id="estadoDiagnosticoDona" value="dona" checked>
                                <label class="btn btn-outline-success" for="estadoDiagnosticoDona">
                                    <i class="fas fa-chart-pie"></i> Dona
                                </label>
                                <input type="radio" class="btn-check" name="estadoDiagnosticoChartType" id="estadoDiagnosticoBarra" value="barra">
                                <label class="btn btn-outline-success" for="estadoDiagnosticoBarra">
                                    <i class="fas fa-chart-bar"></i> Barra
                                </label>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="downloadChartAsImage('estadoDiagnosticoChart', 'estado_diagnostico')" title="Descargar gráfico como imagen">
                                <i class="fas fa-image me-1"></i> Imagen
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container mb-3" style="position: relative; height: 200px;">
                            <canvas id="estadoDiagnosticoChart"></canvas>
                        </div>
                        <div id="estadoDiagnosticoContent">
                            <div class="text-center text-muted">
                                <div class="spinner-border spinner-border-sm me-2" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                Cargando datos...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Líneas - Evolución Temporal -->
    <div class="evolution-chart mb-4">
        <div class="card summary-card" data-chart="evolucionTemporal">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2 text-warning"></i>
                    Evolución de Registros (Últimos 12 Meses)
                </h6>
                <div class="chart-controls">
                    <button class="btn btn-sm btn-outline-primary" onclick="cambiarPeriodoGrafico('12')">12 Meses</button>
                    <button class="btn btn-sm btn-outline-primary" onclick="cambiarPeriodoGrafico('6')">6 Meses</button>
                    <button class="btn btn-sm btn-outline-primary" onclick="cambiarPeriodoGrafico('3')">3 Meses</button>
                    <div class="d-inline-block ms-2">
                        <input type="date" id="fechaDesde" class="form-control form-control-sm d-inline-block" style="width: 140px;" placeholder="Desde">
                        <input type="date" id="fechaHasta" class="form-control form-control-sm d-inline-block ms-1" style="width: 140px;" placeholder="Hasta">
                        <button class="btn btn-sm btn-success ms-1" onclick="aplicarRangoFechas()">
                            <i class="fas fa-filter"></i> Aplicar
                        </button>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-success ms-2" onclick="downloadChartAsImage('evolucionTemporalChart', 'evolucion_temporal')" title="Descargar gráfico como imagen">
                        <i class="fas fa-image me-1"></i> Imagen
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 300px;">
                    <canvas id="evolucionTemporalChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Mapa y Gráficos -->
    <div class="row mb-4">
        <!-- Mapa Interactivo -->
        <div class="col-md-8">
            <div class="card map-card" data-chart="mapa">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-map-marked-alt me-2 text-danger"></i>
                        Distribución Geográfica
                    </h6>
               
                </div>
                <div class="card-body">
                    <div id="mapaColombia" style="height: 400px; width: 100%; border-radius: 10px; overflow: hidden;">
                        <!-- El mapa se cargará aquí -->
                    </div>
                    @if(isset($datosMapa) && (is_object($datosMapa) ? $datosMapa->count() : count($datosMapa)) > 0)
                        <div class="map-legend mt-3">
                            <h6>Leyenda:</h6>
                            <div class="legend-items">
                                @foreach((is_object($datosMapa) ? $datosMapa->take(10) : array_slice($datosMapa, 0, 10)) as $dato)
                                    <div class="legend-item">
                                        <span class="legend-dot" style="background-color: {{ '#' . substr(md5($dato->municipio->municipioNOMBREOFICIAL ?? 'default'), 0, 6) }}"></span>
                                        <span class="legend-text">{{ $dato->municipio->municipioNOMBREOFICIAL ?? 'Sin Municipio' }} ({{ $dato->total }})</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Gráfico de Etapas -->
        <div class="col-md-4">
            <div class="card stages-card" data-chart="etapas">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2 text-warning"></i>
                        Distribución por Etapas
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="downloadChartAsImage('etapasChart', 'distribucion_etapas')" title="Descargar gráfico como imagen">
                        <i class="fas fa-image me-1"></i> Imagen
                    </button>
                </div>
                <div class="card-body">
                    <div class="chart-container mb-3" style="position: relative; height: 200px;">
                        <canvas id="etapasChart"></canvas>
                    </div>
                    <div id="etapasContent">
                        <div class="text-center text-muted">
                            <p>Datos cargados dinámicamente</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Unificada de Municipios -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card summary-card" data-chart="municipios">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                        Análisis de Municipios
                    </h6>
                    <div class="btn-group btn-group-sm me-2" role="group">
                        <input type="radio" class="btn-check" name="municipiosChartType" id="municipiosTop10" value="top10" checked>
                        <label class="btn btn-outline-primary" for="municipiosTop10">
                            <i class="fas fa-chart-bar"></i> Top 8
                        </label>
                        <input type="radio" class="btn-check" name="municipiosChartType" id="municipiosLista" value="lista">
                        <label class="btn btn-outline-primary" for="municipiosLista">
                            <i class="fas fa-list"></i> Lista Completa
                        </label>
                    </div>
                    <div class="btn-group btn-group-sm me-2" role="group">
                        <input type="radio" class="btn-check" name="municipiosChartVisualType" id="municipiosBarra" value="barra" checked>
                        <label class="btn btn-outline-secondary" for="municipiosBarra">
                            <i class="fas fa-chart-bar"></i> Barra
                        </label>
                        <input type="radio" class="btn-check" name="municipiosChartVisualType" id="municipiosDona" value="dona">
                        <label class="btn btn-outline-secondary" for="municipiosDona">
                            <i class="fas fa-chart-pie"></i> Dona
                        </label>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="downloadChartAsImage('municipiosChart', 'analisis_municipios')" title="Descargar gráfico como imagen">
                        <i class="fas fa-image me-1"></i> Imagen
                    </button>
                    
                </div>
                <div class="card-body">
                    <!-- Contenedor del gráfico -->
                    <div class="chart-container mb-3" style="position: relative; height: 300px;">
                        <canvas id="municipiosChart"></canvas>
                    </div>
                    
                    <!-- Contenedor de la tabla -->
                    <div id="municipiosContent">
                        <!-- Tabla de Top 8 Municipios -->
                        <div id="municipiosTop10Content" class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Municipio</th>
                                        <th class="text-center">Porcentaje</th>
                                        <th class="text-center">Unidades</th>
                                    </tr>
                                </thead>
                                <tbody id="municipiosTableBody">
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Cargando datos...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Lista Completa de Municipios -->
                        <div id="municipiosListaContent" class="table-responsive" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Lista Completa de Municipios</h6>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="mostrarVistaTop8()">
                                    <i class="fas fa-compress"></i> Ver Menos
                                </button>
                            </div>
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Municipio</th>
                                        <th class="text-center">Porcentaje</th>
                                        <th class="text-center">Unidades</th>
                                    </tr>
                                </thead>
                                <tbody id="municipiosListaTableBody">
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Cargando lista completa...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Sectores -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card summary-card" data-chart="sectores">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-industry me-2 text-primary"></i>
                        Distribución por Sectores
                    </h6>
                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="sectoresChartType" id="sectoresDona" value="dona" checked>
                        <label class="btn btn-outline-primary" for="sectoresDona">
                            <i class="fas fa-chart-pie"></i> Dona
                        </label>
                        <input type="radio" class="btn-check" name="sectoresChartType" id="sectoresBarra" value="barra">
                        <label class="btn btn-outline-primary" for="sectoresBarra">
                            <i class="fas fa-chart-bar"></i> Barra
                        </label>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-success ms-2" onclick="downloadChartAsImage('sectoresChart', 'distribucion_sectores')" title="Descargar gráfico como imagen">
                        <i class="fas fa-image me-1"></i> Imagen
                    </button>
                </div>
                <div class="card-body">
                    <div class="chart-container mb-3" style="position: relative; height: 200px;">
                        <canvas id="sectoresChart"></canvas>
                    </div>
                    <div id="sectoresContent">
                        <div class="text-center text-muted">
                            <p>Datos cargados dinámicamente</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Gráfico de Tamaños de Empresa -->
    <div class="col-lg-6 col-md-12 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2 text-info"></i>
                    Distribución por Tamaño
                </h6>
                                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="tamanosChartType" id="tamanosDona" value="dona">
                        <label class="btn btn-outline-info" for="tamanosDona">
                            <i class="fas fa-chart-pie"></i> Dona
                        </label>
                        <input type="radio" class="btn-check" name="tamanosChartType" id="tamanosBarra" value="barra" checked>
                        <label class="btn btn-outline-info" for="tamanosBarra">
                            <i class="fas fa-chart-bar"></i> Barra
                        </label>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-success ms-2" onclick="downloadChartAsImage('tamanosChart', 'distribucion_tamanos')" title="Descargar gráfico como imagen">
                        <i class="fas fa-image me-1"></i> Imagen
                    </button>
            </div>
            <div class="card-body">
                <div class="chart-container mb-3" style="position: relative; height: 200px;">
                    <canvas id="tamanosChart"></canvas>
                </div>
                <div id="tamanosContent">
                    <div class="text-center text-muted">
                        <p>Datos cargados dinámicamente</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Indicador de Rendimiento -->
   



@endsection

@section('styles')
<style>
.municip-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.municip-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}

.table-responsive {
    margin-top: 1rem;
}

.table th {
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
}

.table-info {
    background-color: #e7f3ff !important;
    font-weight: 600;
}

.table-secondary {
    background-color: #f8f9fa !important;
    font-style: italic;
}

.btn-group-sm .btn {
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
}

.text-center {
    text-align: center !important;
}

.chart-container {
    position: relative;
    height: 300px;
}

@media (max-width: 768px) {
    .btn-group-sm {
        flex-direction: column;
    }
    
    .btn-group-sm .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
    }
}
</style>
@endsection



@section('scripts')

<!-- Chart.js con fallback -->
<script>
    
// Variables globales
let dashboardData = null;

// Datos del dashboard desde el backend
        const backendData = @json($backendData ?? []);
        
        // Hacer backendData disponible globalmente para que las funciones locales puedan acceder
        window.backendData = backendData;
        
        console.log('📊 backendData cargado:', backendData);
        console.log('📊 backendData.porMunicipios:', backendData.porMunicipios);
        console.log('📊 backendData.porTipoOrganizacion:', backendData.porTipoOrganizacion);

// Configuración global del dashboard
window.DashboardConfig = {
    performanceMode: false,
    lazyLoading: false,
    cacheEnabled: true,
    autoRefresh: false,
    refreshInterval: 300000, // 5 minutos
    chartAnimationDuration: 1000,
    maxDataPoints: 20
};



// ===== FUNCIONES DE CARGA DE LIBRERÍAS =====

// Función para cargar Chart.js
function loadChartJS() {
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.js';
        script.onload = () => {
            resolve();
        };
        script.onerror = () => {
            console.warn('CDN principal falló, intentando CDN alternativo...');
            const script2 = document.createElement('script');
            script2.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js';
            script2.onload = () => {
                resolve();
            };
            script2.onerror = () => {
                reject(new Error('No se pudo cargar Chart.js'));
            };
            document.head.appendChild(script2);
        };
        document.head.appendChild(script);
    });
}

// Función para cargar Google Maps (ya se carga via script tag)
function loadGoogleMaps() {
    return new Promise((resolve) => {
        if (typeof google !== 'undefined' && google.maps) {
            resolve();
        } else {
            // Esperar a que Google Maps se cargue
            const checkGoogleMaps = setInterval(() => {
                if (typeof google !== 'undefined' && google.maps) {
                    clearInterval(checkGoogleMaps);
                    resolve();
                }
            }, 100);
        }
    });
}

// ===== FUNCIONES PRINCIPALES DEL DASHBOARD =====

function initializeDashboard() {
    console.log('🔧 Iniciando initializeDashboard...');
    
    try {
        // Pasar datos del dashboard al JavaScript
        console.log('📊 Configurando datos del dashboard...');
        dashboardData = {
            totalUnidades: {{ $totalUnidades ?? 0 }},
            datosMapa: backendData.datosMapa,
            porMunicipios: backendData.porMunicipios,
            porTipoOrganizacion: backendData.porTipoOrganizacion,
            porEstadoDiagnostico: backendData.porEstadoDiagnostico,
            porEtapas: backendData.porEtapas,
            evolucionTemporal: backendData.evolucionTemporal,
            porTamanos: backendData.porTamanos
        };
        console.log('✅ Datos del dashboard configurados:', dashboardData);
        
        console.log('🎧 Configurando event listeners...');
        setupEventListeners();
        console.log('✅ Event listeners configurados');
        
        console.log('⏰ Actualizando tiempo de última actualización...');
        updateLastUpdateTime();
        console.log('✅ Tiempo actualizado');
        
        console.log('📈 Calculando tendencia de métricas...');
        updateMetricTrend();
        console.log('✅ Tendencia calculada');
        
        console.log('🚫 Ocultando indicador de carga...');
        hideLoading();
        console.log('✅ Indicador de carga ocultado');
        
        console.log('🎯 initializeDashboard completado exitosamente');
        hideLoading();
        // FORZAR ocultar loading después de un breve delay
        setTimeout(() => {
            console.log('🔄 Forzando ocultar loading desde initializeDashboard...');
            hideLoading();
        }, 1000);
        
    } catch (error) {
        console.error('❌ Error en initializeDashboard:', error);
        // Intentar ocultar el loading incluso si hay error
        try {
            hideLoading();
            console.log('✅ Indicador de carga ocultado (después del error)');
        } catch (hideError) {
            console.error('❌ Error al ocultar loading:', hideError);
        }
        
        // También forzar ocultar loading después de un delay
        setTimeout(() => {
            console.log('🔄 Forzando ocultar loading después de error en initializeDashboard...');
            hideLoading();
        }, 1000);
    }
}

function initializeCharts() {
    
    try {
        // Usar datos del dashboard desde el backend

        
        // Gráfico de Tipo de Organización (datos reales) - CAMBIADO A BARRAS para mostrar conteos
        if (backendData.porTipoOrganizacion && backendData.porTipoOrganizacion.length > 0) {
            const tipoOrgData = {
                labels: backendData.porTipoOrganizacion.map(item => 
                    item.tipoPersona?.tipoPersonaNOMBRE || `Tipo ${item.tipopersona_id}`
                ),
                data: backendData.porTipoOrganizacion.map(item => item.total),
                backgroundColor: ['#667eea', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c']
            };
            // Inicializar con gráfico de barras por defecto (como está configurado en el HTML)
            tipoOrganizacionChart = createBarChart('tipoOrganizacionChart', tipoOrgData);
            
            // Actualizar la leyenda del gráfico
            updateTipoOrganizacionContent();
        } else {
            const tipoOrgData = {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: ['#6c757d']
            };
            tipoOrganizacionChart = createBarChart('tipoOrganizacionChart', tipoOrgData);
            
            // Actualizar la leyenda con mensaje de no datos
            updateTipoOrganizacionContent();
        }

        // Gráfico de Estado del Diagnóstico (datos reales) - MOSTRAR PORCENTAJES en la dona
        if (backendData.porEstadoDiagnostico && backendData.porEstadoDiagnostico.length > 0) {
            // Calcular total para porcentajes
            const totalDiagnosticos = backendData.porEstadoDiagnostico.reduce((sum, item) => sum + item.total, 0);
            
            const estadoData = {
                labels: backendData.porEstadoDiagnostico.map(item => item.estado),
                data: backendData.porEstadoDiagnostico.map(item => item.total),
                backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c']
            };
            // Inicializar con gráfico de dona por defecto (como está configurado en el HTML)
            estadoDiagnosticoChart = createDoughnutChart('estadoDiagnosticoChart', estadoData);
            
            // Actualizar la leyenda del gráfico
            updateEstadoDiagnosticoContent();
        } else {
            const estadoData = {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: ['#6c757d']
            };
            estadoDiagnosticoChart = createDoughnutChart('estadoDiagnosticoChart', estadoData);
            
            // Actualizar la leyenda con mensaje de no datos
            updateEstadoDiagnosticoContent();
        }

        // Gráfico de Etapas (datos reales)
        if (backendData.porEtapas && backendData.porEtapas.length > 0) {
            const etapasData = {
                labels: backendData.porEtapas.map(item => 
                    item.etapa?.name || `Etapa ${item.etapa_id}`
                ),
                data: backendData.porEtapas.map(item => item.total),
                backgroundColor: ['#ffc107', '#17a2b8', '#28a745', '#fd7e14', '#6f42c1']
            };
            createBarChart('etapasChart', etapasData);
            updateEtapasContent();
        } else {
            
            createBarChart('etapasChart', {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: ['#6c757d']
            });
            updateEtapasContent();
        }

        // Gráfico de Municipios (datos reales - TOP 10)
        if (backendData.porMunicipios && backendData.porMunicipios.length > 0) {
            // Tomar solo los primeros 8 municipios
            const topMunicipios = backendData.porMunicipios.slice(0, 8);
            const municipiosData = {
                labels: topMunicipios.map(item => 
                    item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`
                ),
                data: topMunicipios.map(item => item.total),
                backgroundColor: topMunicipios.map(item => {
                    const nombre = item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`;
                    return generateColorFromName(nombre);
                })
            };
            window.municipiosChart = createBarChart('municipiosChart', municipiosData);
            
            // Actualizar la tabla de municipios
            updateMunicipiosTable();
        } else {
            // Datos de ejemplo con colores consistentes
            const municipiosEjemplo = ['Santa Marta', 'Ciénaga', 'El Banco', 'Plato', 'Fundación', 'Pivijay', 'Algarrobo', 'Zona Bananera'];
            const municipiosData = {
                labels: municipiosEjemplo,
                data: [1962, 159, 87, 84, 83, 57, 52, 50],
                backgroundColor: municipiosEjemplo.map(nombre => generateColorFromName(nombre))
            };
            window.municipiosChart = createBarChart('municipiosChart', municipiosData);
            
            // Actualizar la tabla con mensaje de no datos
            updateMunicipiosTable();
        }

 
        
        // Verificar si tenemos datos completos de municipios
        if (backendData.porMunicipiosCompletos && Array.isArray(backendData.porMunicipiosCompletos) && backendData.porMunicipiosCompletos.length > 0) {
            const totalUnidades = backendData.totalUnidades || backendData.porMunicipiosCompletos.reduce((sum, item) => sum + item.total, 0);
            
            // Crear colores dinámicos para todos los municipios
            const generateColors = (count) => {
                const colors = [];
                for (let i = 0; i < count; i++) {
                    const hue = (i * 137.508) % 360; // Número áureo para distribución de colores
                    const saturation = 70 + (i % 20); // Variación en saturación
                    const lightness = 50 + (i % 15); // Variación en luminosidad
                    colors.push(`hsl(${hue}, ${saturation}%, ${lightness}%)`);
                }
                return colors;
            };
            
            const proporcionesData = {
                labels: backendData.porMunicipiosCompletos.map(item => 
                    item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`
                ),
                data: backendData.porMunicipiosCompletos.map(item => 
                    Math.round((item.total / totalUnidades) * 100)
                ),
                backgroundColor: generateColors(backendData.porMunicipiosCompletos.length)
            };
            createPieChart('proporcionesChart', proporcionesData);
            
            // Actualizar el contenido con todos los municipios
            // updateProporcionesContent(); // COMENTADO - NO EXISTE LA TABLA HTML
        } else if (backendData.porMunicipios && Array.isArray(backendData.porMunicipios) && backendData.porMunicipios.length > 0) {
            // Fallback: usar datos de municipios limitados si no hay completos
            const totalUnidades = backendData.totalUnidades || backendData.porMunicipios.reduce((sum, item) => sum + item.total, 0);
            
            const proporcionesData = {
                labels: backendData.porMunicipios.map(item => 
                    item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`
                ),
                data: backendData.porMunicipios.map(item => 
                    Math.round((item.total / totalUnidades) * 100)
                ),
                backgroundColor: ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#17a2b8', '#ffc107', '#28a745']
            };
            createPieChart('proporcionesChart', proporcionesData);
            
            // Actualizar contenido con datos limitados
            // updateProporcionesContentFallback(); // COMENTADO - NO EXISTE LA TABLA HTML
        } else {
            
            createPieChart('proporcionesChart', {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: ['#6c757d']
            });
        }

        // Gráfico de Evolución Temporal (datos reales - últimos 12 meses)
        if (backendData.evolucionTemporal && backendData.evolucionTemporal.length > 0) {
            const evolucionData = {
                labels: backendData.evolucionTemporal.map(item => item.label),
                data: backendData.evolucionTemporal.map(item => item.total),
                backgroundColor: '#ffc107'
            };
            window.evolucionTemporalChart = createLineChart('evolucionTemporalChart', evolucionData);
        } else {
            
            window.evolucionTemporalChart = createLineChart('evolucionTemporalChart', {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: '#6c757d'
            });
        }

        // Gráfico de Tamaños de Empresa (datos reales)
        if (backendData.porTamanos && backendData.porTamanos.length > 0) {
            const tamanosData = {
                labels: backendData.porTamanos.map(item => 
                    item.tamano?.tamanoNOMBRE || `Tamaño ${item.tamano_id}`
                ),
                data: backendData.porTamanos.map(item => item.total),
                backgroundColor: '#17a2b8'
            };
            // Inicializar con gráfico de barras por defecto (como está configurado en el HTML)
            tamanosChart = createBarChart('tamanosChart', tamanosData);
            updateTamanosContent(tamanosData);
        } else {
            const tamanosData = {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: '#6c757d'
            };
            tamanosChart = createBarChart('tamanosChart', tamanosData);
            updateTamanosContent(tamanosData);
        }

        // Gráfico de Sectores (datos reales)
        if (backendData.porSectores && backendData.porSectores.length > 0) {
            const sectoresData = {
                labels: backendData.porSectores.map(item => 
                    item.sector?.sectorNOMBRE || `Sector ${item.sector_id}`
                ),
                data: backendData.porSectores.map(item => item.total),
                backgroundColor: ['#e83e8c', '#fd7e14', '#20c997', '#6f42c1', '#dc3545', '#28a745', '#17a2b8', '#ffc107']
            };
            // Inicializar con gráfico de dona por defecto (como está configurado en el HTML)
            sectoresChart = createDoughnutChart('sectoresChart', sectoresData);
            updateSectoresContent(sectoresData);
        } else {
            const sectoresData = {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: ['#6c757d']
            };
            sectoresChart = createDoughnutChart('sectoresChart', sectoresData);
            updateSectoresContent(sectoresData);
        }
        
 
        
        // Actualizar contenido estático con datos reales
        updateChartContent();
        
    } catch (error) {
        console.error('Error al inicializar gráficos con datos reales:', error);
        alert('Error al crear los gráficos: ' + error.message);
        
        // Crear gráficos con datos de ejemplo en caso de error
        createBarChart('tipoOrganizacionChart', {
            labels: ['Natural', 'Jurídica', 'Establecimiento'],
            data: [2157, 750, 19],
            backgroundColor: '#667eea'
        });
        createDoughnutChart('estadoDiagnosticoChart', {
            labels: ['Terminado', 'Pendiente'],
            data: [62, 38], // Porcentajes aproximados
            backgroundColor: ['#28a745', '#ffc107']
        });
        createBarChart('etapasChart', {
            labels: ['Despegue', 'Nacimiento', 'Crecimiento', 'Descubrimiento', 'Madurez'],
            data: [1145, 929, 322, 302, 30],
            backgroundColor: '#ffc107'
        });
        createBarChart('municipiosChart', {
            labels: ['Bogotá', 'Medellín', 'Cali', 'Barranquilla', 'Cartagena'],
            data: [450, 320, 280, 180, 150],
            backgroundColor: '#28a745'
        });
        createPieChart('proporcionesChart', {
            labels: ['Micro', 'Pequeña', 'Mediana', 'Gran Empresa'],
            data: [45, 30, 20, 5],
            backgroundColor: ['#667eea', '#764ba2', '#f093fb', '#f5576c']
        });
        window.evolucionTemporalChart = createLineChart('evolucionTemporalChart', {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            data: [100, 120, 110, 130, 140, 150, 160, 170, 180, 190, 200, 210],
            backgroundColor: '#ffc107'
        });
        const tamanosData = {
            labels: ['Micro', 'Pequeña', 'Mediana', 'Grande', 'Gigante'],
            data: [100, 200, 300, 250, 150],
            backgroundColor: '#17a2b8'
        };
        tamanosChart = createBarChart('tamanosChart', tamanosData);
        updateTamanosContent(tamanosData);
        
        const sectoresData = {
            labels: ['Servicios', 'Manufactura', 'Comercio'],
            data: [1118, 930, 794],
            backgroundColor: ['#e91e63', '#ff9800', '#00bcd4']
        };
        sectoresChart = createDoughnutChart('sectoresChart', sectoresData);
        updateSectoresContent(sectoresData);
    }
}

// ===== FUNCIONES DEL MAPA CON GOOGLE MAPS =====

let map = null;
let markers = [];
let currentMapType = 'roadmap';
let infoWindow = null;

function initializeMap() {
    
    try {
        // Mostrar indicador de carga del mapa
        const mapContainer = document.getElementById('mapaColombia');
        if (mapContainer) {
            mapContainer.innerHTML = `
                <div class="d-flex justify-content-center align-items-center h-100" style="background: #f8f9fa; border-radius: 10px;">
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-2" role="status">
                            <span class="visually-hidden">Cargando Google Maps...</span>
                        </div>
                        <p class="text-muted mb-0">Cargando Google Maps...</p>
                    </div>
                </div>
            `;
        }
        
        // Coordenadas de Colombia
        const colombiaCenter = { lat: 4.5709, lng: -74.2973 };
        
        // Crear el mapa de Google
        map = new google.maps.Map(document.getElementById('mapaColombia'), {
            center: colombiaCenter,
            zoom: 6,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapTypeControl: true,
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                position: google.maps.ControlPosition.TOP_RIGHT
            },
            zoomControl: true,
            zoomControlOptions: {
                position: google.maps.ControlPosition.RIGHT_CENTER
            },
            streetViewControl: false,
            fullscreenControl: true,
            styles: [
                {
                    featureType: 'poi',
                    elementType: 'labels',
                    stylers: [{ visibility: 'off' }]
                }
            ]
        });
        
        // Crear ventana de información
        infoWindow = new google.maps.InfoWindow();
        
        // Evento cuando el mapa está listo
        google.maps.event.addListenerOnce(map, 'idle', function() {
            // Cargar marcadores reales cuando el mapa esté listo
            loadRealMapMarkers();
        });
        
        
    } catch (error) {
        console.error('Error al inicializar Google Maps:', error);
        const mapContainer = document.getElementById('mapaColombia');
        if (mapContainer) {
            mapContainer.innerHTML = `
                <div class="d-flex justify-content-center align-items-center h-100" style="background: #f8f9fa; border-radius: 10px;">
                    <div class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p class="mb-0">Error al cargar Google Maps</p>
                        <small>${error.message}</small>
                    </div>
                </div>
            `;
        }
    }
}

function loadRealMapMarkers() {
    
    try {
        // Limpiar marcadores existentes
        if (markers.length > 0) {
            markers.forEach(marker => {
                marker.setMap(null);
            });
            markers = [];
        }
        
        // Verificar si hay datos del mapa disponibles
        if (!backendData.datosMapa || backendData.datosMapa.length === 0) {
            
            addSampleMarkers();
            return;
        }
        
        
        // Mostrar indicador de progreso
        const mapContainer = document.getElementById('mapaColombia');
        if (mapContainer) {
            const progressDiv = document.createElement('div');
            progressDiv.id = 'mapProgress';
            progressDiv.className = 'position-absolute top-0 start-0 w-100 p-2';
            progressDiv.style.zIndex = '1000';
            progressDiv.innerHTML = `
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                </div>
                <small class="text-muted">Cargando marcadores: 0/${backendData.datosMapa.length}</small>
            `;
            mapContainer.appendChild(progressDiv);
        }
        
        // Cargar marcadores en lotes para mejor rendimiento
        const batchSize = 15;
        let currentIndex = 0;
        
        function loadBatch() {
            const endIndex = Math.min(currentIndex + batchSize, backendData.datosMapa.length);
            
            for (let i = currentIndex; i < endIndex; i++) {
                const item = backendData.datosMapa[i];
                
                if (item.geolocation && item.municipio) {
                    try {
                        // Parsear coordenadas (asumiendo formato "lat,lng")
                        const coords = item.geolocation.split(',').map(coord => parseFloat(coord.trim()));
                        
                        if (coords.length === 2 && !isNaN(coords[0]) && !isNaN(coords[1])) {
                            const [lat, lng] = coords;
                            const count = item.total || 1;
                            
                            // Crear marcador de Google Maps
                            const marker = new google.maps.Marker({
                                position: { lat, lng },
                                map: map,
                                title: `${item.municipio.municipioNOMBREOFICIAL || 'Sin nombre'} - ${count.toLocaleString()} unidades`,
                                icon: {
                                    path: google.maps.SymbolPath.CIRCLE,
                                    scale: Math.max(8, Math.min(25, Math.sqrt(count) * 1.5)),
                                    fillColor: getColorByCount(count),
                                    fillOpacity: 0.8,
                                    strokeColor: '#ffffff',
                                    strokeWeight: 2
                                }
                            });
                            
                            // Agregar evento de clic
                            marker.addListener('click', function() {
                                const content = `
                                    <div style="min-width: 250px; padding: 10px;">
                                        <h6 style="color: #667eea; margin-bottom: 15px; border-bottom: 2px solid #667eea; padding-bottom: 8px;">
                                            <i class="fas fa-map-marker-alt me-2"></i>${item.municipio.municipioNOMBREOFICIAL || 'Sin nombre'}
                                        </h6>
                                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                                <i class="fas fa-building me-2" style="color: #667eea;"></i>
                                                <strong>Unidades Productivas:</strong>
                                            </div>
                                            <div style="font-size: 1.2em; color: #667eea; text-align: center;">
                                                ${count.toLocaleString()}
                                            </div>
                                        </div>
                                        <div style="font-size: 0.9em; color: #6c757d; text-align: center;">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Coordenadas: ${lat.toFixed(4)}, ${lng.toFixed(4)}
                                        </div>
                                    </div>
                                `;
                                
                                infoWindow.setContent(content);
                                infoWindow.open(map, marker);
                            });
                            
                            // Agregar tooltip al hacer hover
                            marker.addListener('mouseover', function() {
                                const tooltip = document.createElement('div');
                                tooltip.className = 'google-map-tooltip';
                                tooltip.innerHTML = `
                                    <div style="text-align: center; padding: 8px;">
                                        <strong>${item.municipio.municipioNOMBREOFICIAL || 'Sin nombre'}</strong><br>
                                        <span style="color: #667eea;">${count.toLocaleString()} unidades</span>
                                    </div>
                                `;
                                document.body.appendChild(tooltip);
                                
                                // Posicionar tooltip
                                const rect = marker.getIcon().path.getBoundingClientRect();
                                tooltip.style.position = 'absolute';
                                tooltip.style.left = rect.left + 'px';
                                tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
                                tooltip.style.zIndex = '1000';
                                
                                marker.tooltip = tooltip;
                            });
                            
                            marker.addListener('mouseout', function() {
                                if (marker.tooltip) {
                                    marker.tooltip.remove();
                                    marker.tooltip = null;
                                }
                            });
                            
                            markers.push(marker);
                        }
                    } catch (error) {
                        console.warn('Error al procesar marcador:', item, error);
                    }
                }
            }
            
            // Actualizar progreso
            currentIndex = endIndex;
            if (mapContainer && document.getElementById('mapProgress')) {
                const progressBar = document.querySelector('#mapProgress .progress-bar');
                const progressText = document.querySelector('#mapProgress small');
                if (progressBar && progressText) {
                    const percentage = (currentIndex / backendData.datosMapa.length) * 100;
                    progressBar.style.width = percentage + '%';
                    progressText.textContent = `Cargando marcadores: ${currentIndex}/${backendData.datosMapa.length}`;
                }
            }
            
            // Continuar con el siguiente lote si hay más
            if (currentIndex < backendData.datosMapa.length) {
                setTimeout(loadBatch, 30); // Pausa de 30ms entre lotes (más rápido con Google Maps)
            } else {
                // Completado
                if (mapContainer && document.getElementById('mapProgress')) {
                    document.getElementById('mapProgress').remove();
                }
                
                // Ajustar vista del mapa si hay marcadores
                if (markers.length > 0) {
                    const bounds = new google.maps.LatLngBounds();
                    markers.forEach(marker => {
                        bounds.extend(marker.getPosition());
                    });
                    map.fitBounds(bounds);
                    
                    // Ajustar zoom si es muy cercano
                    google.maps.event.addListenerOnce(map, 'bounds_changed', function() {
                        if (map.getZoom() > 15) {
                            map.setZoom(15);
                        }
                    });
                }
            }
        }
        
        // Iniciar carga por lotes
        loadBatch();
        
    } catch (error) {
        console.error('Error al cargar marcadores del mapa:', error);
        // Fallback a marcadores de ejemplo
        addSampleMarkers();
    }
}



function getColorByCount(count) {
    if (count > 1000) return '#e74c3c';
    if (count > 500) return '#f39c12';
    if (count > 200) return '#f1c40f';
    if (count > 100) return '#2ecc71';
    return '#3498db';
}

function cambiarVistaMapa(tipo) {
    
    if (!map) return;
    
    try {
        if (tipo === 'satelite') {
            map.removeLayer(map.osmLayer);
            map.satelliteLayer.addTo(map);
            currentMapType = 'satellite';
        } else {
            map.removeLayer(map.satelliteLayer);
            map.osmLayer.addTo(map);
            currentMapType = 'osm';
        }
        
    } catch (error) {
        console.error('Error al cambiar vista del mapa:', error);
    }
}

function zoomToColombia() {
    if (map) {
        map.setCenter({ lat: 4.5709, lng: -74.2973 });
        map.setZoom(6);
    }
}

// ===== FUNCIONES DE GRÁFICOS =====

function createPieChart(canvasId, data) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error('Canvas no encontrado:', canvasId);
        return;
    }
    
    try {
        const chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: data.backgroundColor,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                return `${label}: ${value}%`;
                            }
                        }
                    }
                }
            }
        });
        return chart;
    } catch (error) {
        throw error;
    }
}

function createDoughnutChart(canvasId, data) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error('Canvas no encontrado:', canvasId);
        return;
    }
    
    try {
        // Validar que los datos existan y tengan la estructura correcta
        if (!data || !data.data || !Array.isArray(data.data) || data.data.length === 0) {
            console.error('Datos inválidos para createDoughnutChart:', data);
            return null;
        }
        
        // Calcular porcentajes para tooltips
        const total = data.data.reduce((sum, value) => sum + value, 0);
        const percentages = data.data.map(value => ((value / total) * 100).toFixed(0));
        
        console.log('Creando gráfico de dona con datos:', {
            labels: data.labels,
            data: data.data,
            total: total,
            percentages: percentages
        });
        
        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: data.backgroundColor,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const percentage = percentages[context.dataIndex];
                                return `${label}: ${percentage}%`;
                            }
                        }
                    }
                }
            }
        });
        return chart;
    } catch (error) {
        console.error('Error en createDoughnutChart:', error);
        console.error('Datos recibidos:', data);
        throw error;
    }
}

function createBarChart(canvasId, data) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error('Canvas no encontrado:', canvasId);
        return;
    }
    
    try {
        // Validar que los datos existan y tengan la estructura correcta
        if (!data || !data.data || !Array.isArray(data.data) || data.data.length === 0) {
            console.error('Datos inválidos para createBarChart:', data);
            return null;
        }
        
        console.log('Creando gráfico de barras con datos:', {
            labels: data.labels,
            data: data.data,
            backgroundColor: data.backgroundColor
        });
        
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Cantidad',
                    data: data.data,
                    backgroundColor: data.backgroundColor,
                    borderColor: data.backgroundColor,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        return chart;
    } catch (error) {
        console.error('Error al crear gráfico de barras:', canvasId, error);
        console.error('Datos recibidos:', data);
        throw error;
    }
}

function createLineChart(canvasId, data) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error('Canvas no encontrado:', canvasId);
        return;
    }
    
    try {
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Registros',
                    data: data.data,
                    backgroundColor: data.backgroundColor + '20',
                    borderColor: data.backgroundColor,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        return chart;
    } catch (error) {
        console.error('Error al crear gráfico de líneas:', canvasId, error);
        throw error;
    }
}

// ===== FUNCIONES DE FILTROS Y UTILIDADES =====

function filtrarPorDepartamento(departamentoId) {
    showLoading();
    const url = new URL(window.location);
    if (departamentoId) {
        url.searchParams.set('departamento_id', departamentoId);
    } else {
        url.searchParams.delete('departamento_id');
    }
    window.location.href = url.toString();
}

function filtrarPorSector(sectorId) {
    showLoading();
    const url = new URL(window.location);
    if (sectorId) {
        url.searchParams.set('sector_id', sectorId);
    } else {
        url.searchParams.delete('sector_id');
    }
    window.location.href = url.toString();
}

function filtrarPorEtapa(etapaId) {
    showLoading();
    const url = new URL(window.location);
    if (etapaId) {
        url.searchParams.set('etapa_id', etapaId);
    } else {
        url.searchParams.delete('etapa_id');
    }
    window.location.href = url.toString();
}

function cambiarPeriodo(dias) {
    if (dias) {
        showLoading();
        const fechaHasta = new Date();
        const fechaDesde = new Date();
        fechaDesde.setDate(fechaDesde.getDate() - parseInt(dias));
        
        const url = new URL(window.location);
        url.searchParams.set('fecha_desde', fechaDesde.toISOString().split('T')[0]);
        url.searchParams.set('fecha_hasta', fechaHasta.toISOString().split('T')[0]);
        window.location.href = url.toString();
    }
}

function resetFilters() {
    showLoading();
    window.location.href = window.location.pathname;
}

function refreshDashboard() {
    showLoading();
    window.location.reload();
}

function togglePerformanceMode() {
    DashboardConfig.performanceMode = !DashboardConfig.performanceMode;
    const modeText = DashboardConfig.performanceMode ? 'Rápido' : 'Normal';
    const modeElement = document.getElementById('performanceMode');
    if (modeElement) {
        modeElement.textContent = modeText;
    }
    
    if (DashboardConfig.performanceMode) {
        DashboardConfig.chartAnimationDuration = 300;
        DashboardConfig.maxDataPoints = 10;
        document.body.classList.add('performance-mode');
    } else {
        DashboardConfig.chartAnimationDuration = 1000;
        DashboardConfig.maxDataPoints = 20;
        document.body.classList.remove('performance-mode');
    }
}




function cambiarPeriodoGrafico(periodo) {
    
    try {
        // Actualizar estado visual de los botones
        document.querySelectorAll('.chart-controls .btn').forEach(btn => {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-primary');
        });
        
        // Marcar el botón seleccionado
        event.target.classList.remove('btn-outline-primary');
        event.target.classList.add('btn-primary');
        
        // Filtrar datos según el período seleccionado
        if (backendData.evolucionTemporal && backendData.evolucionTemporal.length > 0) {
            let datosFiltrados = [];
            const meses = parseInt(periodo);
            
            if (meses === 12) {
                datosFiltrados = backendData.evolucionTemporal;
            } else {
                // Tomar solo los últimos N meses
                datosFiltrados = backendData.evolucionTemporal.slice(-meses);
            }
            
            // Actualizar el gráfico con los datos filtrados
            const evolucionData = {
                labels: datosFiltrados.map(item => item.label),
                data: datosFiltrados.map(item => item.total),
                backgroundColor: '#ffc107'
            };
            
            // Destruir el gráfico existente si existe
            if (window.evolucionTemporalChart) {
                window.evolucionTemporalChart.destroy();
            }
            
            // Crear nuevo gráfico
            window.evolucionTemporalChart = createLineChart('evolucionTemporalChart', evolucionData);
            
        } else {
            
        }
        
        // Limpiar campos de fecha
        document.getElementById('fechaDesde').value = '';
        document.getElementById('fechaHasta').value = '';
        
    } catch (error) {
        console.error('Error al cambiar período del gráfico:', error);
    }
}

function aplicarRangoFechas() {
    const fechaDesde = document.getElementById('fechaDesde').value;
    const fechaHasta = document.getElementById('fechaHasta').value;
    
    if (!fechaDesde || !fechaHasta) {
        alert('Por favor selecciona ambas fechas');
        return;
    }
    
    if (fechaDesde > fechaHasta) {
        alert('La fecha de inicio debe ser anterior a la fecha final');
        return;
    }
    
    
    try {
        // Resetear estado de botones de período
        document.querySelectorAll('.chart-controls .btn').forEach(btn => {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-primary');
        });
        
        // Filtrar datos por rango de fechas
        if (backendData.evolucionTemporal && backendData.evolucionTemporal.length > 0) {
            const datosFiltrados = backendData.evolucionTemporal.filter(item => {
                // Asumiendo que item.label contiene la fecha en formato 'MMM YYYY'
                const itemDate = new Date(item.label + ' 01');
                const desde = new Date(fechaDesde);
                const hasta = new Date(fechaHasta);
                
                return itemDate >= desde && itemDate <= hasta;
            });
            
            if (datosFiltrados.length > 0) {
                // Actualizar el gráfico con los datos filtrados
                const evolucionData = {
                    labels: datosFiltrados.map(item => item.label),
                    data: datosFiltrados.map(item => item.total),
                    backgroundColor: '#ffc107'
                };
                
                // Destruir el gráfico existente si existe
                if (window.evolucionTemporalChart) {
                    window.evolucionTemporalChart.destroy();
                }
                
                // Crear nuevo gráfico
                window.evolucionTemporalChart = createLineChart('evolucionTemporalChart', evolucionData);
                
                
            } else {
                alert('No hay datos disponibles para el rango de fechas seleccionado');
            }
        } else {
            console.warn('No hay datos de evolución temporal disponibles');
        }
        
    } catch (error) {
        console.error('Error al aplicar rango de fechas:', error);
        alert('Error al filtrar por fechas: ' + error.message);
    }
}

function updateChartContent() {
    
    
    try {
        // Actualizar contenido de proporciones (TODOS los municipios para 100% real)
        if (backendData.porMunicipiosCompletos && backendData.porMunicipiosCompletos.length > 0) {
            const totalUnidades = backendData.totalUnidades || backendData.porMunicipiosCompletos.reduce((sum, item) => sum + item.total, 0);
            
            let proporcionesHTML = '';
            
            // Mostrar todos los municipios con sus porcentajes reales
            backendData.porMunicipiosCompletos.forEach(item => {
                const porcentaje = Math.round((item.total / totalUnidades) * 100);
                const nombre = item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`;
                proporcionesHTML += `<p><strong>${nombre}:</strong> ${porcentaje}% (${item.total.toLocaleString()} unidades)</p>`;
            });
            
            // Agregar línea de total para confirmar que suma 100%
            proporcionesHTML += `<hr><p class="text-primary"><strong>Total: 100% (${totalUnidades.toLocaleString()} unidades)</strong></p>`;
            
            // Ya no necesitamos actualizar proporcionesContent, ahora usamos la tabla
            // La tabla se actualiza a través de updateProporcionesTable()
            console.log('Datos de proporciones disponibles, tabla se actualizará automáticamente');
        }
        
        // NOTA: El contenido de tamaños se actualiza por la función específica updateTamanosContent()

        // NOTA: El contenido de sectores se actualiza por la función específica updateSectoresContent()
        
        // Actualizar tendencia de métricas después de actualizar gráficos
        updateMetricTrend();
        
        
    } catch (error) {
        console.error('Error al actualizar contenido de gráficos:', error);
    }
}

// Función para actualizar el contenido del gráfico de proporciones - COMENTADA PORQUE NO EXISTE LA TABLA HTML
/*
function updateProporcionesContent() {
    console.log('Actualizando contenido de proporciones con todos los municipios...');
    
    try {
        if (backendData.porMunicipios && backendData.porMunicipios.length > 0) {
            const totalUnidades = backendData.totalUnidades || backendData.porMunicipios.reduce((sum, item) => sum + item.total, 0);
    
            // Actualizar la tabla de proporciones directamente (vista resumen)
            updateProporcionesTableLocal(false);
            
            // Mostrar botón para ver lista completa
            const btnVerMas = document.getElementById('btnVerMasProporciones');
            if (btnVerMas) {
                btnVerMas.style.display = 'inline-block';
            }
            
            console.log('Contenido de proporciones actualizado exitosamente');
        } else {
            console.warn('⚠️ No hay datos de municipios para proporciones');
        }
    } catch (error) {
        console.error('❌ Error al actualizar contenido de proporciones:', error);
    }
}
*/

// Función de fallback para actualizar contenido de proporciones con datos limitados - COMENTADA PORQUE NO EXISTE LA TABLA HTML
/*
function updateProporcionesContentFallback() {
    console.log('Actualizando contenido de proporciones con datos limitados (fallback)...');
    
    try {
        if (backendData.porMunicipios && backendData.porMunicipios.length > 0) {
            const totalUnidades = backendData.totalUnidades || backendData.porMunicipios.reduce((sum, item) => sum + item.total, 0);
            
            // Actualizar la tabla de proporciones con datos limitados
            updateProporcionesTableLocal(false);
            
            // Mostrar botón para ver lista completa si hay suficientes datos
            const btnVerMas = document.getElementById('btnVerMasProporciones');
            if (btnVerMas && backendData.porMunicipios.length > 0) {
                btnVerMas.style.display = 'inline-block';
            } else if (btnVerMas) {
                btnVerMas.style.display = 'none';
            }
            
            console.log('Contenido de proporciones (fallback) actualizado exitosamente');
        } else {
            console.warn('⚠️ No hay datos de municipios para proporciones (fallback)');
        }
    } catch (error) {
        console.error('❌ Error al actualizar contenido de proporciones (fallback):', error);
    }
}
*/

// Función para mostrar resumen de proporciones (top 5 + otros) - COMENTADA PORQUE NO EXISTE LA TABLA HTML
/*
function mostrarResumenProporciones(municipios, totalUnidades) {
    const proporcionesContent = document.getElementById('proporcionesContent');
    if (!proporcionesContent) return;
    
    let resumenHTML = '';
    
    // Mostrar top 5 municipios
    const top5 = municipios.slice(0, 5);
    
    top5.forEach(item => {
        const porcentaje = Math.round((item.total / totalUnidades) * 100);
        const nombre = item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`;
        resumenHTML += `<p class="mb-1"><strong>${nombre}:</strong> ${porcentaje}% (${item.total.toLocaleString()} unidades)</p>`;
    });
    
    // Si hay más de 5 municipios, mostrar resumen de "otros"
    if (municipios.length > 5) {
        const otrosMunicipios = municipios.slice(5);
        const totalOtros = otrosMunicipios.reduce((sum, item) => sum + item.total, 0);
        const porcentajeOtros = Math.round((totalOtros / totalUnidades) * 100);
        
        resumenHTML += `<hr class="my-2">`;
        resumenHTML += `<p class="mb-1 text-muted"><em>Otros ${municipios.length - 5} municipios: ${porcentajeOtros}% (${totalOtros.toLocaleString()} unidades)</em></p>`;
    }
    
    // Agregar línea de total
    resumenHTML += `<hr class="my-2">`;
    resumenHTML += `<p class="text-primary fw-bold mb-0"><strong>Total: 100% (${totalUnidades.toLocaleString()} unidades)</strong></p>`;
    
    // Actualizar la tabla de proporciones directamente (vista resumen)
    updateProporcionesTableLocal(false);
}
*/

// Función para generar colores consistentes basados en el nombre
function generateColorFromName(name) {
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    
    const hue = Math.abs(hash) % 360;
    const saturation = 70 + (Math.abs(hash) % 20);
    const lightness = 50 + (Math.abs(hash) % 15);
    
    return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
}

// Función para mostrar vista compacta de proporciones - COMENTADA PORQUE NO EXISTE LA TABLA HTML
/*
function mostrarVistaCompactaProporciones() {
    console.log('=== mostrarVistaCompactaProporciones EJECUTADA ===');
    
    try {
        // Obtener los datos limitados
        let municipios = null;
        let totalUnidades = 0;
        
        if (window.backendData && window.backendData.porMunicipios && window.backendData.porMunicipios.length > 0) {
            municipios = window.backendData.porMunicipios;
            totalUnidades = window.backendData.totalUnidades || municipios.reduce((sum, item) => sum + item.total, 0);
        } else {
            console.error('No hay datos disponibles para mostrar vista compacta');
            return;
        }
        
        console.log('Municipios a mostrar (vista compacta):', municipios.length);
        console.log('Total unidades:', totalUnidades);
        console.log('Llamando a updateProporcionesTableLocal(false)...');
        
        // Actualizar la tabla de proporciones con todos los municipios por defecto
        updateProporcionesTableLocal(true); // true = mostrar lista completa
        
        // Cambiar visibilidad de los botones
        const btnVerMas = document.getElementById('btnVerMasProporciones');
        const btnVerMenos = document.getElementById('btnVerMenosProporciones');
        
        if (btnVerMas && btnVerMenos) {
            btnVerMas.style.display = 'inline-block';
            btnVerMenos.style.display = 'none';
            console.log('Botones actualizados: Ver Más visible, Ver Menos oculto');
        }
        
        // También actualizar la tabla de lista completa si existe
        const tableBodyCompleta = document.getElementById('proporcionesTableBodyCompleta');
        if (tableBodyCompleta) {
            updateProporcionesTableCompletaLocal(false);
        }
        
        console.log('=== mostrarVistaCompactaProporciones COMPLETADA ===');
    } catch (error) {
        console.error('Error en mostrarVistaCompactaProporciones:', error);
    }
}
*/

// Función para mostrar lista completa de proporciones - COMENTADA PORQUE NO EXISTE LA TABLA HTML
/*
function mostrarListaCompletaProporciones() {
    console.log('=== mostrarListaCompletaProporciones EJECUTADA ===');
    
    try {
        // Obtener los datos completos
        let municipios = null;
        let totalUnidades = 0;
        
        if (window.backendData && window.backendData.porMunicipiosCompletos && window.backendData.porMunicipiosCompletos.length > 0) {
            console.log('Usando porMunicipiosCompletos para vista completa');
            municipios = window.backendData.porMunicipiosCompletos;
            totalUnidades = window.backendData.totalUnidades || municipios.reduce((sum, item) => sum + item.total, 0);
        } else if (window.backendData && window.backendData.porMunicipios && window.backendData.porMunicipios.length > 0) {
            console.log('Fallback: usando porMunicipios para vista completa');
            municipios = window.backendData.porMunicipios;
            totalUnidades = window.backendData.totalUnidades || municipios.reduce((sum, item) => sum + item.total, 0);
        } else {
            console.error('No hay datos disponibles para mostrar vista completa');
            return;
        }
        
        console.log('Municipios a mostrar:', municipios.length);
        console.log('Total unidades:', totalUnidades);
        console.log('Llamando a updateProporcionesTableLocal(true)...');
        
        // Actualizar la tabla de proporciones con todos los municipios
        updateProporcionesTableLocal(true); // true = mostrar lista completa
        
        // También actualizar la tabla de lista completa si existe
        const tableBodyCompleta = document.getElementById('proporcionesTableBodyCompletas');
        if (tableBodyCompleta) {
            updateProporcionesTableCompletaLocal(true);
        }
        
        // Cambiar visibilidad de los botones
        const btnVerMas = document.getElementById('btnVerMasProporciones');
        const btnVerMenos = document.getElementById('btnVerMenosProporciones');
        
        if (btnVerMas && btnVerMenos) {
            btnVerMas.style.display = 'none';
            btnVerMenos.style.display = 'inline-block';
            console.log('Botones actualizados: Ver Más oculto, Ver Menos visible');
        }
        
        console.log('=== mostrarListaCompletaProporciones COMPLETADA ===');
    } catch (error) {
        console.error('Error en mostrarListaCompletaProporciones:', error);
    }
}
*/

// Función para actualizar la tabla de municipios
function updateMunicipiosTable() {
    console.log('=== updateMunicipiosTable EJECUTADA ===');
    
    try {
        const tableBody = document.getElementById('municipiosTableBody');
        if (!tableBody) {
            console.error('Tabla de municipios no encontrada');
            return;
        }
        
        if (backendData.porMunicipios && backendData.porMunicipios.length > 0) {
            
            // Tomar solo los primeros 8 municipios para la tabla principal
            const topMunicipios = backendData.porMunicipios.slice(0, 8);
            // Usar el total real de todas las unidades
            const totalUnidades = backendData.totalUnidades || dashboardData.totalUnidades || 1;
            
            // Calcular total de municipios restantes
            const municipiosRestantes = backendData.porMunicipios.slice(8);
            const totalRestantes = municipiosRestantes.reduce((sum, item) => sum + item.total, 0);
            
            console.log('Top municipios:', topMunicipios.length);
            console.log('Municipios restantes:', municipiosRestantes.length);
            console.log('Total unidades restantes:', totalRestantes);
            
            let tableHTML = '';
            
            // Agregar filas de municipios principales
            topMunicipios.forEach((item, index) => {
                const nombre = item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`;
                const cantidad = item.total;
                const porcentaje = Math.round((cantidad / totalUnidades) * 100);
                // Usar el mismo color que en el gráfico
                const color = generateColorFromName(nombre);
                
                tableHTML += `
                    <tr>
                        <td>
                            <div class="municip-info">
                                <span class="municip-dot" style="background-color: ${color}"></span>
                                ${nombre}
                            </div>
                        </td>
                        <td class="text-center"><strong>${porcentaje}%</strong></td>
                        <td class="text-center"><strong>${cantidad.toLocaleString()}</strong></td>
                    </tr>
                `;
            });
            
            // Agregar fila de "Otros municipios" si hay municipios restantes
            if (municipiosRestantes.length > 0) {
                const porcentajeRestantes = Math.round((totalRestantes / totalUnidades) * 100);
                tableHTML += `
                    <tr class="table-secondary">
                        <td>
                            <div class="municip-info">
                                <span class="municip-dot" style="background-color: #6c757d"></span>
                                <em>Otros ${municipiosRestantes.length} municipios</em>
                            </div>
                        </td>
                        <td class="text-center"><strong>${porcentajeRestantes}%</strong></td>
                        <td class="text-center"><strong>${totalRestantes.toLocaleString()}</strong></td>
                    </tr>
                `;
            }
            
            // Agregar fila de total
            tableHTML += `
                <tr class="table-info">
                    <td><strong>Total del Sistema</strong></td>
                    <td class="text-center"><strong>100%</strong></td>
                    <td class="text-center"><strong>${totalUnidades.toLocaleString()}</strong></td>
                </tr>
            `;
            
            tableBody.innerHTML = tableHTML;
            
            // Agregar comentario explicativo debajo de la tabla
            const comentarioHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted small">
                        <em>* Top 8 municipios por cantidad de unidades productivas</em><br>
                        <em>** Otros municipios agrupados para mejor visualización</em><br>
                        <em>*** Total incluye todas las unidades del sistema</em>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', comentarioHTML);
            
            console.log('Tabla de municipios actualizada exitosamente');
            
        } else {
            console.warn('No hay datos de municipios para mostrar en la tabla');
            
            tableBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted">No hay datos disponibles</td>
                </tr>
            `;
        }
    } catch (error) {
        console.error('Error al actualizar tabla de municipios:', error);
        console.error('Error completo:', error.stack);
        
        const tableBody = document.getElementById('municipiosTableBody');
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-danger">Error al cargar datos: ${error.message}</td>
                </tr>
            `;
        }
    }
}

// Función para actualizar la leyenda del gráfico de estado de diagnóstico
function updateEstadoDiagnosticoContent() {
    
    try {
        const contentDiv = document.getElementById('estadoDiagnosticoContent');
        if (!contentDiv) {
            console.error('Contenido de estado de diagnóstico no encontrado');
            return;
        }
        
        if (backendData.porEstadoDiagnostico && backendData.porEstadoDiagnostico.length > 0) {
            // Calcular total para porcentajes
            const totalDiagnosticos = backendData.porEstadoDiagnostico.reduce((sum, item) => sum + item.total, 0);
            
            let contentHTML = '';
            backendData.porEstadoDiagnostico.forEach(item => {
                const estado = item.estado || 'Sin estado';
                const total = item.total || 0;
                const porcentaje = Math.round((total / totalDiagnosticos) * 100);
                
                // Determinar color del indicador según el estado
                let colorClass = 'text-muted';
                if (estado.toLowerCase().includes('terminado') || estado.toLowerCase().includes('completado')) {
                    colorClass = 'text-success';
                } else if (estado.toLowerCase().includes('pendiente') || estado.toLowerCase().includes('en proceso')) {
                    colorClass = 'text-warning';
                } else if (estado.toLowerCase().includes('cancelado') || estado.toLowerCase().includes('rechazado')) {
                    colorClass = 'text-danger';
                }
                
                contentHTML += `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="${colorClass}">
                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                            ${estado}
                        </span>
                        <span class="text-dark fw-bold"><strong>${total.toLocaleString()} (${porcentaje}%)</strong></span>
                    </div>
                `;
            });
            
            contentDiv.innerHTML = contentHTML;
        } else {
            console.warn('No hay datos de estado de diagnóstico disponibles');
            contentDiv.innerHTML = `
                <div class="text-center text-muted">
                    <p>No hay datos disponibles</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error al actualizar contenido de estado de diagnóstico:', error);
        const contentDiv = document.getElementById('estadoDiagnosticoContent');
        if (contentDiv) {
            contentDiv.innerHTML = `
                <div class="text-center text-danger">
                    <p>Error al cargar datos</p>
                </div>
            `;
        }
    }
}

// Función para actualizar la leyenda del gráfico de tipo de organización
function updateTipoOrganizacionContent() {
    
    try {
        const contentDiv = document.getElementById('tipoOrganizacionContent');
        if (!contentDiv) {
            console.error('Contenido de tipo de organización no encontrado');
            return;
        }
        
        if (backendData.porTipoOrganizacion && backendData.porTipoOrganizacion.length > 0) {
            // Calcular total para porcentajes
            const totalUnidades = backendData.porTipoOrganizacion.reduce((sum, item) => sum + (item.total || 0), 0);
            
            let contentHTML = '';
            backendData.porTipoOrganizacion.forEach((item, index) => {
                const nombre = item.tipoPersona?.tipoPersonaNOMBRE || `Tipo ${item.tipopersona_id}`;
                const total = item.total || 0;
                const porcentaje = total > 0 ? ((total / totalUnidades) * 100).toFixed(1) : '0.0';
                
                // Definir colores únicos para cada tipo
                const colores = ['#667eea', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c'];
                const color = colores[index % colores.length];
                
                contentHTML += `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">
                            <i class="fas fa-square me-1" style="color: ${color}; font-size: 8px;"></i>
                            ${nombre}
                        </span>
                        <span class="text-dark fw-bold"><strong>${total.toLocaleString()} (${porcentaje}%)</strong></span>
                    </div>
                `;
            });
            
            contentDiv.innerHTML = contentHTML;
        } else {
            contentDiv.innerHTML = `
                <div class="text-center text-muted">
                    <p>No hay datos disponibles</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error al actualizar contenido de tipo de organización:', error);
        const contentDiv = document.getElementById('tipoOrganizacionContent');
        if (contentDiv) {
            contentDiv.innerHTML = `
                <div class="text-center text-danger">
                    <p>Error al cargar datos</p>
                </div>
            `;
        }
    }
}

// Función para calcular y mostrar el porcentaje de crecimiento
function updateMetricTrend() {
    
    try {
        const trendElement = document.getElementById('metricTrend');
        if (!trendElement) {
            console.error('Elemento de tendencia no encontrado');
            return;
        }
        
        if (backendData.evolucionTemporal && backendData.evolucionTemporal.length >= 2) {
            // Obtener los últimos dos meses de datos
            const ultimosDatos = backendData.evolucionTemporal.slice(-2);
            const mesActual = ultimosDatos[ultimosDatos.length - 1];
            const mesAnterior = ultimosDatos[ultimosDatos.length - 2];
            
            if (mesActual && mesAnterior && mesActual.total && mesAnterior.total) {
                const totalActual = parseInt(mesActual.total);
                const totalAnterior = parseInt(mesAnterior.total);
                
                // Calcular porcentaje de cambio
                let porcentajeCambio = 0;
                let esIncremento = true;
                let badgeClass = 'bg-success';
                let iconClass = 'fa-arrow-up';
                
                if (totalAnterior > 0) {
                    porcentajeCambio = ((totalActual - totalAnterior) / totalAnterior) * 100;
                    esIncremento = porcentajeCambio >= 0;
                }
                
                // Determinar clase y icono según el cambio
                if (porcentajeCambio === 0) {
                    badgeClass = 'bg-secondary';
                    iconClass = 'fa-minus';
                } else if (porcentajeCambio < 0) {
                    badgeClass = 'bg-danger';
                    iconClass = 'fa-arrow-down';
                } else if (porcentajeCambio < 5) {
                    badgeClass = 'bg-warning';
                    iconClass = 'fa-arrow-up';
                } else {
                    badgeClass = 'bg-success';
                    iconClass = 'fa-arrow-up';
                }
                
                // Formatear el porcentaje
                const porcentajeFormateado = Math.abs(porcentajeCambio).toFixed(1);
                const signo = esIncremento ? '+' : '-';
                
                // Actualizar el HTML
                trendElement.innerHTML = `
                    <span class="badge ${badgeClass}">
                        <i class="fas ${iconClass} me-1"></i>${signo}${porcentajeFormateado}%
                    </span>
                    <span class="text-muted ms-2">vs mes anterior</span>
                `;
                
                
            } else {
                // No hay datos suficientes
                trendElement.innerHTML = `
                    <span class="badge bg-info">
                        <i class="fas fa-info-circle me-1"></i>Sin datos previos
                    </span>
                    <span class="text-muted ms-2">vs mes anterior</span>
                `;
            }
            
        } else {
            // No hay datos de evolución temporal
            trendElement.innerHTML = `
                <span class="badge bg-info">
                    <i class="fas fa-info-circle me-1"></i>Sin datos de evolución
                </span>
                <span class="text-muted ms-2">vs mes anterior</span>
            `;
        }
        
    } catch (error) {
        console.error('Error al calcular tendencia de métricas:', error);
        const trendElement = document.getElementById('metricTrend');
        if (trendElement) {
            trendElement.innerHTML = `
                <span class="badge bg-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>Error en cálculo
                </span>
                <span class="text-muted ms-2">vs mes anterior</span>
            `;
        }
    }
}

// ===== FUNCIONES DE UTILIDAD =====

function showLoading() {
    console.log('🚀 showLoading ejecutado');
    
    // Mostrar el loading del dashboard (loadingIndicator)
    const loadingIndicator = document.getElementById('loadingIndicator');
    if (loadingIndicator) {
        console.log('✅ Mostrando indicador de carga del dashboard');
        loadingIndicator.style.display = 'flex';
        loadingIndicator.style.visibility = 'visible';
        loadingIndicator.style.opacity = '1';
        loadingIndicator.style.pointerEvents = 'auto';
        loadingIndicator.classList.remove('d-none', 'invisible');
    } else {
        console.log('⚠️ Indicador de carga del dashboard no encontrado');
    }
    
    // Mostrar el loading del layout (cargando)
    const layoutLoading = document.querySelector('.cargando');
    if (layoutLoading) {
        console.log('✅ Mostrando indicador de carga del layout');
        layoutLoading.style.display = 'flex';
        layoutLoading.style.visibility = 'visible';
        layoutLoading.style.opacity = '1';
        layoutLoading.style.pointerEvents = 'auto';
        layoutLoading.classList.remove('d-none', 'invisible');
    } else {
        console.log('⚠️ Indicador de carga del layout no encontrado');
    }
}

function hideLoading() {
    console.log('🚫 hideLoading ejecutado');
    
    // Ocultar el loading del dashboard (loadingIndicator)
    const loadingIndicator = document.getElementById('loadingIndicator');
    if (loadingIndicator) {
        console.log('✅ Indicador de carga del dashboard encontrado, ocultando...');
        
        // Ocultar de múltiples maneras para asegurar que funcione
        loadingIndicator.style.display = 'none';
        loadingIndicator.style.visibility = 'hidden';
        loadingIndicator.style.opacity = '0';
        loadingIndicator.style.pointerEvents = 'none';
        
        // También agregar clase CSS para ocultar
        loadingIndicator.classList.add('d-none');
        loadingIndicator.classList.add('invisible');
        
        // Forzar el ocultamiento con !important
        loadingIndicator.setAttribute('style', 'display: none !important; visibility: hidden !important; opacity: 0 !important; pointer-events: none !important;');
        
        console.log('✅ Indicador de carga del dashboard ocultado');
    } else {
        console.log('⚠️ Indicador de carga del dashboard no encontrado');
    }
    
    // Ocultar el loading del layout (cargando)
    const layoutLoading = document.querySelector('.cargando');
    if (layoutLoading) {
        console.log('✅ Indicador de carga del layout encontrado, ocultando...');
        
        // Ocultar de múltiples maneras para asegurar que funcione
        layoutLoading.style.display = 'none';
        layoutLoading.style.visibility = 'hidden';
        layoutLoading.style.opacity = '0';
        layoutLoading.style.pointerEvents = 'none';
        
        // También agregar clase CSS para ocultar
        layoutLoading.classList.add('d-none');
        layoutLoading.classList.add('invisible');
        
        // Forzar el ocultamiento con !important
        layoutLoading.setAttribute('style', 'display: none !important; visibility: hidden !important; opacity: 0 !important; pointer-events: none !important;');
        
        console.log('✅ Indicador de carga del layout ocultado');
    } else {
        console.log('⚠️ Indicador de carga del layout no encontrado');
    }
    
    // Ocultar cualquier otro elemento con clase loading o spinner
    const allLoadingElements = document.querySelectorAll('.loading, .spinner, .loading-overlay, .app-overlay');
    allLoadingElements.forEach((element, index) => {
        console.log(`✅ Ocultando elemento de carga adicional ${index + 1}`);
        element.style.display = 'none';
        element.style.visibility = 'hidden';
        element.style.opacity = '0';
        element.style.pointerEvents = 'none';
        element.classList.add('d-none');
        element.classList.add('invisible');
    });
    
    console.log('✅ Todos los indicadores de carga han sido ocultados');
}

function updateLastUpdateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('es-CO');
    const lastUpdateElement = document.getElementById('lastUpdate');
    if (lastUpdateElement) {
        lastUpdateElement.textContent = timeString;
    }
}

// ===== FUNCIÓN PARA DESCARGAR GRÁFICOS COMO IMAGEN =====

function downloadChartAsImage(chartId, filename = 'grafico') {
    try {
        const canvas = document.getElementById(chartId);
        if (!canvas) {
            console.error('Canvas no encontrado:', chartId);
            return;
        }
        
        // Crear un enlace temporal para la descarga
        const link = document.createElement('a');
        link.download = `${filename}_${new Date().toISOString().slice(0, 10)}.png`;
        
        // Convertir el canvas a una imagen PNG
        canvas.toBlob(function(blob) {
            if (blob) {
                const url = URL.createObjectURL(blob);
                link.href = url;
                link.click();
                
                // Limpiar la URL temporal
                setTimeout(() => {
                    URL.revokeObjectURL(url);
                }, 100);
                
                console.log(`Gráfico ${chartId} descargado como imagen`);
            } else {
                console.error('No se pudo generar la imagen del gráfico');
            }
        }, 'image/png');
        
    } catch (error) {
        console.error('Error al descargar gráfico como imagen:', error);
    }
}

// ===== FUNCIONES DE ALTERNANCIA DE GRÁFICOS =====

// Variables globales para los gráficos
let sectoresChart = null;
let tamanosChart = null;
let tipoOrganizacionChart = null;
let estadoDiagnosticoChart = null;

// Función para alternar gráfico de sectores
function toggleSectoresChart() {
    console.log('=== FUNCIÓN toggleSectoresChart EJECUTADA ===');
    
    try {
        const checkedRadio = document.querySelector('input[name="sectoresChartType"]:checked');
        console.log('Radio button seleccionado:', checkedRadio);
        
        if (!checkedRadio) {
            console.error('No hay radio button seleccionado para sectores');
            return;
        }
        
        const chartType = checkedRadio.value;
        console.log('Tipo de gráfico seleccionado:', chartType);
        
        const canvas = document.getElementById('sectoresChart');
        console.log('Canvas de sectores encontrado:', canvas);
        
        if (!canvas) {
            console.error('Canvas de sectores no encontrado');
            return;
        }
        
        // Destruir gráfico existente si existe
        if (sectoresChart) {
            console.log('Destruyendo gráfico existente de sectores');
            sectoresChart.destroy();
        }
        
        // Obtener datos de sectores
        console.log('backendData completo:', backendData);
        console.log('backendData.porSectores:', backendData.porSectores);
        
        let sectoresData;
        if (backendData.porSectores && backendData.porSectores.length > 0) {
            // Transformar los datos del backend al formato esperado por las funciones de gráficos
            sectoresData = {
                labels: backendData.porSectores.map(item => 
                    item.sector?.sectorNOMBRE || `Sector ${item.sector_id}`
                ),
                data: backendData.porSectores.map(item => item.total),
                backgroundColor: ['#e83e8c', '#fd7e14', '#20c997', '#6f42c1', '#dc3545', '#28a745', '#17a2b8', '#ffc107']
            };
        } else {
            // Datos de ejemplo si no hay datos del backend
            sectoresData = {
                labels: ['Servicios', 'Manufactura', 'Comercio'],
                data: [1118, 930, 794],
                backgroundColor: ['#e91e63', '#ff9800', '#00bcd4']
            };
        }
        
        console.log('Datos de sectores transformados:', sectoresData);
        console.log('Estructura de datos:', {
            hasData: !!sectoresData,
            hasDataProperty: !!sectoresData.data,
            isArray: Array.isArray(sectoresData.data),
            dataLength: sectoresData.data ? sectoresData.data.length : 'undefined',
            labels: sectoresData.labels,
            backgroundColor: sectoresData.backgroundColor
        });
        
        if (chartType === 'dona') {
            console.log('Creando gráfico de dona para sectores');
            // Crear gráfico de dona
            sectoresChart = createDoughnutChart('sectoresChart', sectoresData);
            if (sectoresChart) {
                updateSectoresContent(sectoresData);
            } else {
                console.error('No se pudo crear el gráfico de dona para sectores');
            }
        } else {
            console.log('Creando gráfico de barras para sectores');
            // Crear gráfico de barras
            sectoresChart = createBarChart('sectoresChart', sectoresData);
            if (sectoresChart) {
                updateSectoresContent(sectoresData);
            } else {
                console.error('No se pudo crear el gráfico de barras para sectores');
            }
        }
        
        console.log('Gráfico de sectores alternado exitosamente a:', chartType);
    } catch (error) {
        console.error('Error al alternar gráfico de sectores:', error);
        console.error('Stack trace:', error.stack);
    }
}

// Función para alternar gráfico de tamaños
function toggleTamanosChart() {
    console.log('=== FUNCIÓN toggleTamanosChart EJECUTADA ===');
    
    try {
        const checkedRadio = document.querySelector('input[name="tamanosChartType"]:checked');
        console.log('Radio button seleccionado:', checkedRadio);
        
        if (!checkedRadio) {
            console.error('No hay radio button seleccionado para tamaños');
            return;
        }
        
        const chartType = checkedRadio.value;
        console.log('Tipo de gráfico seleccionado:', chartType);
        
        const canvas = document.getElementById('tamanosChart');
        console.log('Canvas de tamaños encontrado:', canvas);
        
        if (!canvas) {
            console.error('Canvas de tamaños no encontrado');
            return;
        }
        
        // Destruir gráfico existente si existe
        if (tamanosChart) {
            console.log('Destruyendo gráfico existente de tamaños');
            tamanosChart.destroy();
        }
        
        // Obtener datos de tamaños
        let tamanosData;
        if (backendData.porTamanos && backendData.porTamanos.length > 0) {
            // Transformar los datos del backend al formato esperado por las funciones de gráficos
            tamanosData = {
                labels: backendData.porTamanos.map(item => 
                    item.tamano?.tamanoNOMBRE || `Tamaño ${item.tamano_id}`
                ),
                data: backendData.porTamanos.map(item => item.total),
                backgroundColor: ['#17a2b8', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c']
            };
        } else {
            // Datos de ejemplo si no hay datos del backend
            tamanosData = {
                labels: ['Micro', 'Pequeña', 'Mediana', 'Gran empresa'],
                data: [2517, 156, 66, 40],
                backgroundColor: ['#17a2b8', '#28a745', '#ffc107', '#dc3545']
            };
        }
        
        console.log('Datos de tamaños transformados:', tamanosData);
        console.log('Estructura de datos:', {
            hasData: !!tamanosData,
            hasDataProperty: !!tamanosData.data,
            isArray: Array.isArray(tamanosData.data),
            dataLength: tamanosData.data ? tamanosData.data.length : 'undefined',
            labels: tamanosData.labels,
            backgroundColor: tamanosData.backgroundColor
        });
        
        if (chartType === 'dona') {
            console.log('Creando gráfico de dona para tamaños');
            // Crear gráfico de dona
            tamanosChart = createDoughnutChart('tamanosChart', tamanosData);
            if (tamanosChart) {
                updateTamanosContent(tamanosData);
            } else {
                console.error('No se pudo crear el gráfico de dona para tamaños');
            }
        } else {
            console.log('Creando gráfico de barras para tamaños');
            // Crear gráfico de barras
            tamanosChart = createBarChart('tamanosChart', tamanosData);
            if (tamanosChart) {
                updateTamanosContent(tamanosData);
            } else {
                console.error('No se pudo crear el gráfico de barras para tamaños');
            }
        }
        
        console.log('Gráfico de tamaños alternado exitosamente a:', chartType);
    } catch (error) {
        console.error('Error al alternar gráfico de tamaños:', error);
        console.error('Stack trace:', error.stack);
    }
}

// Función para actualizar contenido de sectores
function updateSectoresContent(data) {
    try {
        const contentDiv = document.getElementById('sectoresContent');
        if (!contentDiv) return;
        
        const total = data.data.reduce((sum, value) => sum + value, 0);
        
        let html = '<div class="chart-legend">';
        data.labels.forEach((label, index) => {
            const value = data.data[index];
            const percentage = ((value / total) * 100).toFixed(0);
            
            html += `
                <div class="legend-item d-flex justify-content-between align-items-center mb-2">
                    <span class="d-flex align-items-center">
                        <span class="legend-color me-2" style="width: 12px; height: 12px; background-color: ${data.backgroundColor[index] || data.backgroundColor}; border-radius: 50%;"></span>
                        <strong>${label}:</strong>
                    </span>
                    <span class="text-dark"><strong>${value} unidades (${percentage}%)</strong></span>
                </div>
            `;
        });
        html += '</div>';
        
        contentDiv.innerHTML = html;
    } catch (error) {
        console.error('Error al actualizar contenido de sectores:', error);
    }
}

// Función para actualizar contenido de tamaños
function updateTamanosContent(data) {
    try {
        const contentDiv = document.getElementById('tamanosContent');
        if (!contentDiv) return;
        
        const total = data.data.reduce((sum, value) => sum + value, 0);
        
        let html = '<div class="chart-legend">';
        data.labels.forEach((label, index) => {
            const value = data.data[index];
            const percentage = ((value / total) * 100).toFixed(0);
            
            html += `
                <div class="legend-item d-flex justify-content-between align-items-center mb-2">
                    <span class="d-flex align-items-center">
                        <span class="legend-color me-2" style="width: 12px; height: 12px; background-color: ${data.backgroundColor[index] || data.backgroundColor}; border-radius: 50%;"></span>
                        <strong>${label}:</strong>
                    </span>
                    <span class="text-dark"><strong>${value} unidades (${percentage}%)</strong></span>
                </div>
            `;
        });
        html += '</div>';
        
        contentDiv.innerHTML = html;
    } catch (error) {
        console.error('Error al actualizar contenido de tamaños:', error);
    }
}

// Función para actualizar contenido de etapas
function updateEtapasContent() {
    try {
        const contentDiv = document.getElementById('etapasContent');
        if (!contentDiv) return;
        
        if (backendData.porEtapas && backendData.porEtapas.length > 0) {
            const total = backendData.porEtapas.reduce((sum, item) => sum + (item.total || 0), 0);
            
            let html = '<div class="chart-legend">';
            backendData.porEtapas.forEach((item, index) => {
                const nombre = item.etapa?.name || `Etapa ${item.etapa_id}`;
                const cantidad = item.total || 0;
                const porcentaje = cantidad > 0 ? ((cantidad / total) * 100).toFixed(1) : '0.0';
                
                // Definir colores únicos para cada etapa
                const colores = ['#667eea', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c'];
                const color = colores[index % colores.length];
                
                html += `
                    <div class="legend-item d-flex justify-content-between align-items-center mb-2">
                        <span class="d-flex align-items-center">
                            <span class="legend-color me-2" style="width: 12px; height: 12px; background-color: ${color}; border-radius: 50%;"></span>
                            <strong>${nombre}:</strong>
                        </span>
                        <span class="text-dark"><strong>${cantidad.toLocaleString()} unidades (${porcentaje}%)</strong></span>
                    </div>
                `;
            });
            html += '</div>';
            
            contentDiv.innerHTML = html;
        } else {
            contentDiv.innerHTML = `
                <div class="text-center text-muted">
                    <p>No hay datos disponibles</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error al actualizar contenido de etapas:', error);
    }
}

function setupEventListeners() {
    console.log('=== CONFIGURANDO EVENT LISTENERS ===');
    
    // Actualizar municipios cuando cambie el departamento
    const modalDepartamento = document.getElementById('modalDepartamento');
    if (modalDepartamento) {
        modalDepartamento.addEventListener('change', function() {
            const departamentoId = this.value;
            const municipioSelect = document.getElementById('modalMunicipio');
            if (municipioSelect) {
                municipioSelect.value = '';
            }
        });
    }

    // Configurar filtros avanzados
    const filtrosForm = document.getElementById('filtrosForm');
    if (filtrosForm) {
        filtrosForm.addEventListener('submit', function(e) {
            e.preventDefault();
            showLoading();
            this.submit();
        });
    }
    
    console.log('Llamando a setupChartToggleListeners...');
    // Configurar event listeners para alternancia de gráficos
    setupChartToggleListeners();
    console.log('Event listeners configurados completamente');
}

// Función para configurar event listeners de alternancia de gráficos
function setupChartToggleListeners() {
    console.log('Configurando event listeners para alternancia de gráficos...');
    
    // Event listeners para gráfico de sectores
    const sectoresDona = document.getElementById('sectoresDona');
    const sectoresBarra = document.getElementById('sectoresBarra');
    
    console.log('Elementos de sectores encontrados:', { sectoresDona, sectoresBarra });
    
    if (sectoresDona) {
        sectoresDona.addEventListener('change', function() {
            console.log('Botón Dona de sectores clickeado');
            toggleSectoresChart();
        });
        console.log('Event listener agregado a sectoresDona');
    } else {
        console.warn('Elemento sectoresDona no encontrado');
    }
    
    if (sectoresBarra) {
        sectoresBarra.addEventListener('change', function() {
            console.log('Botón Barra de sectores clickeado');
            toggleSectoresChart();
        });
        console.log('Event listener agregado a sectoresBarra');
    } else {
        console.warn('Elemento sectoresBarra no encontrado');
    }
    
    // Event listeners para gráfico de tamaños
    const tamanosDona = document.getElementById('tamanosDona');
    const tamanosBarra = document.getElementById('tamanosBarra');
    
    console.log('Elementos de tamaños encontrados:', { tamanosDona, tamanosBarra });
    
    if (tamanosDona) {
        tamanosDona.addEventListener('change', function() {
            console.log('Botón Dona de tamaños clickeado');
            toggleTamanosChart();
        });
        console.log('Event listener agregado a tamanosDona');
    } else {
        console.warn('Elemento tamanosDona no encontrado');
    }
    
    if (tamanosBarra) {
        tamanosBarra.addEventListener('change', function() {
            console.log('Botón Barra de tamaños clickeado');
            toggleTamanosChart();
        });
        console.log('Event listener agregado a tamanosBarra');
    } else {
        console.warn('Elemento tamanosBarra no encontrado');
    }
    
    // Event listeners para gráfico de tipo de organización
    const tipoOrgDona = document.getElementById('tipoOrgDona');
    const tipoOrgBarra = document.getElementById('tipoOrgBarra');
    
    console.log('Elementos de tipo de organización encontrados:', { tipoOrgDona, tipoOrgBarra });
    
    if (tipoOrgDona) {
        tipoOrgDona.addEventListener('change', function() {
            console.log('Botón Dona de tipo de organización clickeado');
            toggleTipoOrganizacionChart();
        });
        console.log('Event listener agregado a tipoOrgDona');
    } else {
        console.warn('Elemento tipoOrgDona no encontrado');
    }
    
    if (tipoOrgBarra) {
        tipoOrgBarra.addEventListener('change', function() {
            console.log('Botón Barra de tipo de organización clickeado');
            toggleTipoOrganizacionChart();
        });
        console.log('Event listener agregado a tipoOrgBarra');
    } else {
        console.warn('Elemento tipoOrgBarra no encontrado');
    }
    
    // Event listeners para gráfico de municipios
    const municipiosTop10 = document.getElementById('municipiosTop10');
    const municipiosProporciones = document.getElementById('municipiosProporciones');
    const municipiosLista = document.getElementById('municipiosLista');
    
    console.log('Elementos de municipios encontrados:', { municipiosTop10, municipiosProporciones, municipiosLista });
    
    if (municipiosTop10) {
        municipiosTop10.addEventListener('change', function() {
            console.log('Botón Top 10 de municipios clickeado');
            toggleMunicipiosChart();
        });
        console.log('Event listener agregado a municipiosTop10');
    } else {
        console.warn('Elemento municipiosTop10 no encontrado');
    }
    
    if (municipiosProporciones) {
        municipiosProporciones.addEventListener('change', function() {
            console.log('Botón Proporciones de municipios clickeado');
            toggleMunicipiosChart();
        });
        console.log('Event listener agregado a municipiosProporciones');
    } else {
        console.warn('Elemento municipiosProporciones no encontrado');
    }
    
    if (municipiosLista) {
        municipiosLista.addEventListener('change', function() {
            console.log('Botón Lista de municipios clickeado');
            toggleMunicipiosChart();
        });
        console.log('Event listener agregado a municipiosLista');
    } else {
        console.warn('Elemento municipiosLista no encontrado');
    }
    
    // Event listeners para tipo visual de gráfico de municipios
    const municipiosBarra = document.getElementById('municipiosBarra');
    const municipiosDona = document.getElementById('municipiosDona');
    
    console.log('Elementos de tipo visual de municipios encontrados:', { municipiosBarra, municipiosDona });
    
    if (municipiosBarra) {
        municipiosBarra.addEventListener('change', function() {
            console.log('Botón Barra de municipios clickeado');
            toggleMunicipiosChartVisual();
        });
        console.log('Event listener agregado a municipiosBarra');
    } else {
        console.warn('Elemento municipiosBarra no encontrado');
    }
    
    if (municipiosDona) {
        municipiosDona.addEventListener('change', function() {
            console.log('Botón Dona de municipios clickeado');
            toggleMunicipiosChartVisual();
        });
        console.log('Event listener agregado a municipiosDona');
    } else {
        console.warn('Elemento municipiosDona no encontrado');
    }
    
    // Event listeners para gráfico de estado del diagnóstico
    const estadoDiagnosticoDona = document.getElementById('estadoDiagnosticoDona');
    const estadoDiagnosticoBarra = document.getElementById('estadoDiagnosticoBarra');
    
    console.log('Elementos de estado del diagnóstico encontrados:', { estadoDiagnosticoDona, estadoDiagnosticoBarra });
    
    if (estadoDiagnosticoDona) {
        estadoDiagnosticoDona.addEventListener('change', function() {
            console.log('Botón Dona de estado del diagnóstico clickeado');
            toggleEstadoDiagnosticoChart();
        });
        console.log('Event listener agregado a estadoDiagnosticoDona');
    } else {
        console.warn('Elemento estadoDiagnosticoDona no encontrado');
    }
    
    if (estadoDiagnosticoBarra) {
        estadoDiagnosticoBarra.addEventListener('change', function() {
            console.log('Botón Barra de estado del diagnóstico clickeado');
            toggleEstadoDiagnosticoChart();
        });
        console.log('Event listener agregado a estadoDiagnosticoBarra');
    } else {
        console.warn('Elemento estadoDiagnosticoBarra no encontrado');
    }
    
    console.log('Event listeners de alternancia configurados completamente');
}

// Función para alternar gráfico de tipo de organización
function toggleTipoOrganizacionChart() {
    console.log('=== FUNCIÓN toggleTipoOrganizacionChart EJECUTADA ===');
    
    try {
        const checkedRadio = document.querySelector('input[name="tipoOrganizacionChartType"]:checked');
        console.log('Radio button seleccionado:', checkedRadio);
        
        if (!checkedRadio) {
            console.error('No hay radio button seleccionado para tipo de organización');
            return;
        }
        
        const chartType = checkedRadio.value;
        console.log('Tipo de gráfico seleccionado:', chartType);
        
        const canvas = document.getElementById('tipoOrganizacionChart');
        console.log('Canvas de tipo de organización encontrado:', canvas);
        
        if (!canvas) {
            console.error('Canvas de tipo de organización no encontrado');
            return;
        }
        
        // Destruir gráfico existente si existe
        if (tipoOrganizacionChart) {
            console.log('Destruyendo gráfico existente de tipo de organización');
            tipoOrganizacionChart.destroy();
        }
        
        // Obtener datos de tipo de organización
        let tipoOrgData;
        if (backendData.porTipoOrganizacion && backendData.porTipoOrganizacion.length > 0) {
            // Transformar los datos del backend al formato esperado por las funciones de gráficos
            tipoOrgData = {
                labels: backendData.porTipoOrganizacion.map(item => 
                    item.tipoPersona?.tipoPersonaNOMBRE || `Tipo ${item.tipopersona_id}`
                ),
                data: backendData.porTipoOrganizacion.map(item => item.total),
                backgroundColor: ['#667eea', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c']
            };
        } else {
            // Datos de ejemplo si no hay datos del backend
            tipoOrgData = {
                labels: ['Natural', 'Jurídica', 'Establecimiento'],
                data: [2157, 750, 19],
                backgroundColor: ['#667eea', '#28a745', '#ffc107']
            };
        }
        
        console.log('Datos de tipo de organización transformados:', tipoOrgData);
        
        if (chartType === 'dona') {
            console.log('Creando gráfico de dona para tipo de organización');
            // Crear gráfico de dona
            tipoOrganizacionChart = createDoughnutChart('tipoOrganizacionChart', tipoOrgData);
            if (tipoOrganizacionChart) {
                updateTipoOrganizacionContent(tipoOrgData, true); // true = mostrar porcentajes
            } else {
                console.error('No se pudo crear el gráfico de dona para tipo de organización');
            }
        } else {
            console.log('Creando gráfico de barras para tipo de organización');
            // Crear gráfico de barras
            tipoOrganizacionChart = createBarChart('tipoOrganizacionChart', tipoOrgData);
            if (tipoOrganizacionChart) {
                updateTipoOrganizacionContent(tipoOrgData, false); // false = mostrar conteos
            } else {
                console.error('No se pudo crear el gráfico de barras para tipo de organización');
            }
        }
        
        console.log('Gráfico de tipo de organización alternado exitosamente a:', chartType);
    } catch (error) {
        console.error('Error al alternar gráfico de tipo de organización:', error);
        console.error('Stack trace:', error.stack);
    }
}

// Función para alternar gráfico de municipios
function toggleMunicipiosChart() {
    console.log('=== FUNCIÓN toggleMunicipiosChart EJECUTADA ===');
    
    try {
        const checkedRadio = document.querySelector('input[name="municipiosChartType"]:checked');
        console.log('Radio button seleccionado:', checkedRadio);
        
        if (!checkedRadio) {
            console.error('No hay radio button seleccionado para municipios');
            return;
        }
        
        const chartType = checkedRadio.value;
        console.log('Tipo de gráfico seleccionado:', chartType);
        
        // Ocultar todas las vistas de tabla
        document.getElementById('municipiosTop10Content').style.display = 'none';
        document.getElementById('municipiosListaContent').style.display = 'none';
        
        if (chartType === 'top10') {
            console.log('Mostrando vista Top 8 de municipios');
            document.getElementById('municipiosTop10Content').style.display = 'block';
            updateMunicipiosTable();
        } else if (chartType === 'lista') {
            console.log('Mostrando lista completa de municipios');
            document.getElementById('municipiosListaContent').style.display = 'block';
            mostrarListaCompletaMunicipios();
        }
        
        // Inicializar la vista por defecto si es la primera vez
        if (chartType === 'top10' && !window.municipiosInitialized) {
            window.municipiosInitialized = true;
            updateMunicipiosTable();
        }
        
        console.log('Vista de municipios alternada exitosamente a:', chartType);
    } catch (error) {
        console.error('Error al alternar vista de municipios:', error);
        console.error('Stack trace:', error.stack);
    }
}

// Función para mostrar vista Top 8
function mostrarVistaTop8() {
    console.log('=== mostrarVistaTop8 EJECUTADA ===');
    
    try {
        // Ocultar vista de lista completa
        document.getElementById('municipiosListaContent').style.display = 'none';
        // Mostrar vista Top 8
        document.getElementById('municipiosTop10Content').style.display = 'block';
        
        // Cambiar selección del radio button
        const radioTop8 = document.getElementById('municipiosTop10');
        if (radioTop8) {
            radioTop8.checked = true;
        }
        
        // Actualizar tabla Top 8
        updateMunicipiosTable();
        
        console.log('=== mostrarVistaTop8 COMPLETADA ===');
    } catch (error) {
        console.error('Error en mostrarVistaTop8:', error);
    }
}

// Función para mostrar lista completa de municipios
function mostrarListaCompletaMunicipios() {
    console.log('=== mostrarListaCompletaMunicipios EJECUTADA ===');
    
    try {
        // Obtener todos los datos de municipios disponibles
        let municipiosData = null;
        let totalUnidades = 0;
        let fuenteDatos = '';
        
        // Primero intentar obtener datos completos
        if (window.backendData?.porMunicipiosCompletos && window.backendData.porMunicipiosCompletos.length > 0) {
            console.log('✅ Usando porMunicipiosCompletos para lista completa');
            municipiosData = window.backendData.porMunicipiosCompletos;
            totalUnidades = window.backendData.totalUnidades || municipiosData.reduce((sum, item) => sum + item.total, 0);
            fuenteDatos = 'porMunicipiosCompletos';
        } 
        // Si no hay datos completos, intentar obtener todos los municipios disponibles
        else if (window.backendData?.porMunicipios && window.backendData.porMunicipios.length > 0) {
            console.log('⚠️ No hay porMunicipiosCompletos, usando porMunicipios');
            
            // Verificar si hay más municipios en otros arrays de datos
            let todosLosMunicipios = [];
            
            // Agregar municipios de porMunicipios
            if (window.backendData.porMunicipios) {
                todosLosMunicipios = todosLosMunicipios.concat(window.backendData.porMunicipios);
            }
            
            // Buscar en otros arrays que puedan contener municipios
            if (window.backendData.datosMapa) {
                console.log('🔍 Buscando municipios adicionales en datosMapa...');
                const municipiosMapa = window.backendData.datosMapa.filter(item => 
                    item.municipio && !todosLosMunicipios.some(existente => 
                        existente.municipio?.municipio_id === item.municipio?.municipio_id
                    )
                );
                if (municipiosMapa.length > 0) {
                    console.log(`📊 Encontrados ${municipiosMapa.length} municipios adicionales en datosMapa`);
                    todosLosMunicipios = todosLosMunicipios.concat(municipiosMapa);
                }
            }
            
            // Buscar en otros arrays que puedan contener municipios de otros departamentos
            console.log('🔍 Buscando municipios en todas las fuentes de datos disponibles...');
            
            // Revisar todas las propiedades de backendData que puedan contener municipios
            Object.keys(window.backendData).forEach(key => {
                const valor = window.backendData[key];
                if (Array.isArray(valor) && valor.length > 0) {
                    // Verificar si el array contiene objetos con información de municipios
                    const primerItem = valor[0];
                    if (primerItem && (primerItem.municipio || primerItem.municipality_id)) {
                        console.log(`🔍 Revisando ${key}: ${valor.length} registros`);
                        
                        const municipiosEncontrados = valor.filter(item => 
                            item.municipio && !todosLosMunicipios.some(existente => 
                                existente.municipio?.municipio_id === item.municipio?.municipio_id
                            )
                        );
                        
                        if (municipiosEncontrados.length > 0) {
                            console.log(`📊 Encontrados ${municipiosEncontrados.length} municipios adicionales en ${key}`);
                            todosLosMunicipios = todosLosMunicipios.concat(municipiosEncontrados);
                        }
                    }
                }
            });
            
            // Eliminar duplicados basándose en municipio_id y nombre
            const municipiosUnicos = [];
            const idsVistos = new Set();
            const nombresVistos = new Set();
            
            todosLosMunicipios.forEach(item => {
                const municipioId = item.municipio?.municipio_id || item.municipality_id;
                const municipioNombre = item.municipio?.municipioNOMBREOFICIAL || item.municipio?.nombre || 'Sin nombre';
                
                // Verificar si ya existe por ID o por nombre
                if (municipioId && !idsVistos.has(municipioId) && !nombresVistos.has(municipioNombre)) {
                    idsVistos.add(municipioId);
                    nombresVistos.add(municipioNombre);
                    municipiosUnicos.push(item);
                } else if (municipioId && !idsVistos.has(municipioId)) {
                    // Si no existe por ID pero sí por nombre, usar el que tenga más unidades
                    const existente = municipiosUnicos.find(exist => 
                        exist.municipio?.municipioNOMBREOFICIAL === municipioNombre
                    );
                    if (existente && (item.total || 0) > (existente.total || 0)) {
                        // Reemplazar el existente con el que tiene más unidades
                        const index = municipiosUnicos.indexOf(existente);
                        municipiosUnicos[index] = item;
                    }
                }
            });
            
            municipiosData = municipiosUnicos;
            totalUnidades = window.backendData.totalUnidades || municipiosUnicos.reduce((sum, item) => sum + (item.total || 0), 0);
            fuenteDatos = 'Todas las fuentes disponibles (consolidado)';
            
            console.log(`🔄 Consolidación: ${window.backendData.porMunicipios?.length || 0} municipios originales + ${municipiosUnicos.length - (window.backendData.porMunicipios?.length || 0)} adicionales = ${municipiosUnicos.length} total`);
            
            // Si solo encontramos pocos municipios, sugerir cargar más datos
            if (municipiosUnicos.length < 100) {
                console.log('⚠️ ADVERTENCIA: Solo se encontraron pocos municipios. Posibles causas:');
                console.log('   - Los datos están filtrados por departamento (solo Magdalena)');
                console.log('   - No se han cargado todos los departamentos');
                console.log('   - Los datos están limitados en el backend');
                console.log('💡 SUGERENCIA: Verificar filtros de departamento o cargar datos completos');
            }
            
            // Mostrar información sobre la diferencia de unidades
            const totalCalculado = municipiosUnicos.reduce((sum, item) => sum + (item.total || 0), 0);
            const totalSistema = window.backendData.totalUnidades || 0;
            const diferencia = totalSistema - totalCalculado;
            
            if (diferencia > 0) {
                console.log(`📊 ANÁLISIS DE UNIDADES:`);
                console.log(`   Total del sistema: ${totalSistema}`);
                console.log(`   Total en municipios: ${totalCalculado}`);
                console.log(`   Diferencia: ${diferencia} unidades`);
                console.log(`   Posibles causas:`);
                console.log(`   - ${diferencia} unidades sin municipio asignado (municipality_id NULL)`);
                console.log(`   - Municipios con IDs inválidos (no existen en tabla municipios)`);
                console.log(`   - Unidades excluidas por filtros adicionales`);
            }
        } else {
            console.error('❌ No hay datos disponibles para mostrar lista completa');
            return;
        }
        
        console.log(`📋 Municipios a mostrar: ${municipiosData.length}`);
        console.log(`📊 Total unidades: ${totalUnidades}`);
        console.log(`🔗 Fuente de datos: ${fuenteDatos}`);
        
        // Mostrar información detallada de los municipios
        municipiosData.forEach((item, index) => {
            const nombre = item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`;
            const cantidad = item.total || 0;
            console.log(`  ${index + 1}. ${nombre}: ${cantidad} unidades`);
        });
        
        // Actualizar la tabla de lista completa
        updateMunicipiosListaTable(municipiosData, totalUnidades);
        
        console.log('=== mostrarListaCompletaMunicipios COMPLETADA ===');
    } catch (error) {
        console.error('Error en mostrarListaCompletaMunicipios:', error);
    }
}

// Función para debug: mostrar qué datos están disponibles
function debugBackendData() {
    console.log('🔍 === DEBUG BACKEND DATA ===');
    
    if (window.backendData) {
        console.log('📊 Estructura de backendData:', Object.keys(window.backendData));
        
        if (window.backendData.porMunicipios) {
            console.log(`📍 porMunicipios: ${window.backendData.porMunicipios.length} municipios`);
            console.log('   Municipios:', window.backendData.porMunicipios.map(item => 
                item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`
            ));
        }
        
        if (window.backendData.porMunicipiosCompletos) {
            console.log(`📍 porMunicipiosCompletos: ${window.backendData.porMunicipiosCompletos.length} municipios`);
            console.log('   Municipios:', window.backendData.porMunicipiosCompletos.map(item => 
                item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`
            ));
        }
        
        if (window.backendData.datosMapa) {
            console.log(`📍 datosMapa: ${window.backendData.datosMapa.length} registros`);
            const municipiosMapa = window.backendData.datosMapa.filter(item => item.municipio);
            console.log(`   Municipios en mapa: ${municipiosMapa.length}`);
            console.log('   Municipios:', municipiosMapa.map(item => 
                item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`
            ));
        }
        
        if (window.backendData.totalUnidades) {
            console.log(`📍 totalUnidades: ${window.backendData.totalUnidades}`);
        }
        
        console.log('🔍 === FIN DEBUG BACKEND DATA ===');
    } else {
        console.log('❌ No hay backendData disponible');
    }
}

// Función para cargar datos de todos los departamentos
async function cargarTodosLosDepartamentos() {
    console.log('🌍 === CARGANDO TODOS LOS DEPARTAMENTOS ===');
    
    try {
        // Mostrar indicador de carga
        const btnTodos = document.querySelector('button[onclick="cargarTodosLosDepartamentos()"]');
        if (btnTodos) {
            btnTodos.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Cargando...';
            btnTodos.disabled = true;
        }
        
        // Intentar hacer una petición al backend para obtener todos los departamentos
        console.log('📡 Intentando cargar datos de todos los departamentos...');
        
        // Opción 1: Intentar hacer una petición AJAX al backend
        try {
            const response = await fetch('/api/dashboard/todos-departamentos', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const datosCompletos = await response.json();
                console.log('✅ Datos completos cargados:', datosCompletos);
                
                // Actualizar backendData con los datos completos
                if (datosCompletos.porMunicipiosCompletos) {
                    window.backendData.porMunicipiosCompletos = datosCompletos.porMunicipiosCompletos;
                    console.log(`📊 Municipios completos cargados: ${datosCompletos.porMunicipiosCompletos.length}`);
                }
                
                // Actualizar la vista
                mostrarListaCompletaMunicipios();
                
            } else {
                console.log('⚠️ Endpoint no disponible, intentando método alternativo...');
                await cargarDatosAlternativo();
            }
            
        } catch (error) {
            console.log('⚠️ Error en petición AJAX, intentando método alternativo...');
            await cargarDatosAlternativo();
        }
        
    } catch (error) {
        console.error('❌ Error al cargar todos los departamentos:', error);
    } finally {
        // Restaurar botón
        if (btnTodos) {
            btnTodos.innerHTML = '<i class="fas fa-globe me-1"></i> Todos';
            btnTodos.disabled = false;
        }
    }
}

// Método alternativo para cargar datos
async function cargarDatosAlternativo() {
    console.log('🔄 Intentando método alternativo...');
    
    // Opción 2: Intentar resetear filtros y recargar
    try {
        // Resetear filtro de departamento
        const selectDepartamento = document.getElementById('departamentoSelect');
        if (selectDepartamento) {
            selectDepartamento.value = '';
            console.log('🔄 Filtro de departamento reseteado');
        }
        
        // Resetear otros filtros
        const selectSector = document.getElementById('sectorSelect');
        if (selectSector) {
            selectSector.value = '';
        }
        
        const selectEtapa = document.getElementById('etapaSelect');
        if (selectEtapa) {
            selectEtapa.value = '';
        }
        
        // Intentar recargar el dashboard
        console.log('🔄 Recargando dashboard...');
        if (typeof refreshDashboard === 'function') {
            refreshDashboard();
        } else {
            // Si no hay función de refresh, recargar la página
            console.log('🔄 Recargando página...');
            window.location.reload();
        }
        
            } catch (error) {
            console.error('❌ Error en método alternativo:', error);
            alert('No se pudieron cargar todos los departamentos. Intenta recargar la página o contactar al administrador.');
        }
    }
    
    // Función para resetear todos los filtros
    function resetearFiltros() {
        console.log('🔄 === RESETEANDO TODOS LOS FILTROS ===');
        
        try {
            // Resetear select de departamento
            const selectDepartamento = document.getElementById('departamentoSelect');
            if (selectDepartamento) {
                selectDepartamento.value = '';
                console.log('🔄 Filtro de departamento reseteado');
            }
            
            // Resetear select de sector
            const selectSector = document.getElementById('sectorSelect');
            if (selectSector) {
                selectSector.value = '';
                console.log('🔄 Filtro de sector reseteado');
            }
            
            // Resetear select de etapa
            const selectEtapa = document.getElementById('etapaSelect');
            if (selectEtapa) {
                selectEtapa.value = '';
                console.log('🔄 Filtro de etapa reseteado');
            }
            
            // Resetear select de tamaño
            const selectTamano = document.getElementById('tamanoSelect');
            if (selectTamano) {
                selectTamano.value = '';
                console.log('🔄 Filtro de tamaño reseteado');
            }
            
            // Resetear select de tipo de persona
            const selectTipoPersona = document.getElementById('tipopersonaSelect');
            if (selectTipoPersona) {
                selectTipoPersona.value = '';
                console.log('🔄 Filtro de tipo de persona reseteado');
            }
            
            // Resetear fechas si existen
            const fechaDesde = document.getElementById('fechaDesde');
            if (fechaDesde) {
                fechaDesde.value = '';
                console.log('🔄 Filtro de fecha desde reseteado');
            }
            
            const fechaHasta = document.getElementById('fechaHasta');
            if (fechaHasta) {
                fechaHasta.value = '';
                console.log('🔄 Filtro de fecha hasta reseteado');
            }
            
            console.log('✅ Todos los filtros reseteados. Recargando página...');
            
            // Recargar la página para obtener datos sin filtros
            window.location.reload();
            
        } catch (error) {
            console.error('❌ Error al resetear filtros:', error);
            alert('Error al resetear filtros. Intenta recargar la página manualmente.');
        }
    }
    
    // Función para analizar unidades perdidas
    function analizarUnidadesPerdidas() {
        console.log('🔍 === ANALIZANDO UNIDADES PERDIDAS ===');
        
        if (!window.backendData) {
            console.error('❌ No hay backendData disponible');
            return;
        }
        
        const totalSistema = window.backendData.totalUnidades || 0;
        let totalEnMunicipios = 0;
        let municipiosConDatos = 0;
        
        // Calcular total en municipios
        if (window.backendData.porMunicipiosCompletos && window.backendData.porMunicipiosCompletos.length > 0) {
            totalEnMunicipios = window.backendData.porMunicipiosCompletos.reduce((sum, item) => sum + (item.total || 0), 0);
            municipiosConDatos = window.backendData.porMunicipiosCompletos.length;
        } else if (window.backendData.porMunicipios && window.backendData.porMunicipios.length > 0) {
            totalEnMunicipios = window.backendData.porMunicipios.reduce((sum, item) => sum + (item.total || 0), 0);
            municipiosConDatos = window.backendData.porMunicipios.length;
        }
        
        const diferencia = totalSistema - totalEnMunicipios;
        
        console.log('📊 RESUMEN DE ANÁLISIS:');
        console.log(`   Total del sistema: ${totalSistema} unidades`);
        console.log(`   Total en municipios: ${totalEnMunicipios} unidades`);
        console.log(`   Diferencia: ${diferencia} unidades`);
        console.log(`   Municipios con datos: ${municipiosConDatos}`);
        
        if (diferencia > 0) {
            console.log('🚨 UNIDADES PERDIDAS DETECTADAS:');
            console.log(`   ${diferencia} unidades no aparecen en la lista de municipios`);
            console.log('');
            console.log('🔍 POSIBLES CAUSAS:');
            console.log('   1. Unidades con municipality_id NULL (sin municipio asignado)');
            console.log('   2. Unidades con municipality_id inválido (no existe en tabla municipios)');
            console.log('   3. Filtros adicionales en el backend');
            console.log('   4. Problemas de integridad referencial en la base de datos');
            console.log('');
            console.log('💡 RECOMENDACIONES:');
            console.log('   - Revisar logs del backend para más detalles');
            console.log('   - Verificar integridad de municipality_id en tabla unidades_productivas');
            console.log('   - Comprobar que todos los municipality_id existan en tabla municipios');
            console.log('   - Revisar si hay filtros implícitos en las consultas');
            
            // Mostrar alerta al usuario
            alert(`Se detectaron ${diferencia} unidades no asignadas a municipios.\n\nRevisa la consola para más detalles.`);
        } else {
            console.log('✅ No se detectaron unidades perdidas');
            alert('No se detectaron unidades perdidas. Todos los datos están correctamente asignados.');
        }
    }

// Función para actualizar la tabla de lista completa de municipios
function updateMunicipiosListaTable(municipiosData, totalUnidades) {
    console.log('=== updateMunicipiosListaTable EJECUTADA ===');
    
    try {
        const tableBody = document.getElementById('municipiosListaTableBody');
        if (!tableBody) {
            console.error('Tabla de lista completa de municipios no encontrada');
            return;
        }
        
        console.log('Actualizando tabla de lista completa con:', municipiosData.length, 'municipios');
        
        let tableHTML = '';
        
        // Agregar filas de todos los municipios
        municipiosData.forEach((item, index) => {
            const nombre = item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`;
            const cantidad = item.total;
            const porcentaje = Math.round((cantidad / totalUnidades) * 100);
            // Usar el mismo color que en el gráfico para consistencia
            const color = generateColorFromName(nombre);
            
            tableHTML += `
                <tr>
                    <td>
                        <div class="municip-info">
                            <span class="municip-dot" style="background-color: ${color}"></span>
                            ${nombre}
                        </div>
                    </td>
                    <td class="text-center"><strong>${porcentaje}%</strong></td>
                    <td class="text-center"><strong>${cantidad.toLocaleString()}</strong></td>
                </tr>
            `;
        });
        
        // Agregar fila de total
        tableHTML += `
            <tr class="table-info">
                <td><strong>Total del Sistema</strong></td>
                <td class="text-center"><strong>100%</strong></td>
                <td class="text-center"><strong>${totalUnidades.toLocaleString()}</strong></td>
            </tr>
        `;
        
        tableBody.innerHTML = tableHTML;
        
        // Agregar comentario explicativo
        const comentarioHTML = `
            <tr>
                <td colspan="3" class="text-center text-muted small">
                    <em>Todos los municipios del sistema (${municipiosData.length} municipios)</em><br>
                    <em>** Total incluye todas las unidades del sistema</em>
                </td>
            </tr>
        `;
        tableBody.insertAdjacentHTML('beforeend', comentarioHTML);
        
        console.log('Tabla de lista completa actualizada exitosamente');
        console.log('=== updateMunicipiosListaTable COMPLETADA ===');
        
    } catch (error) {
        console.error('Error al actualizar tabla de lista completa:', error);
        
        const tableBody = document.getElementById('municipiosListaTableBody');
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-danger">Error al cargar datos: ${error.message}</td>
                </tr>
            `;
        }
    }
}

// Función para alternar tipo de gráfico de municipios (barra/dona)
function toggleMunicipiosChartVisual() {
    console.log('=== FUNCIÓN toggleMunicipiosChartVisual EJECUTADA ===');
    
    try {
        // Verificar que Chart.js esté disponible
        if (typeof Chart === 'undefined') {
            console.error('Chart.js no está disponible');
            alert('Chart.js no está cargado. Por favor, recarga la página.');
            return;
        }
        
        const checkedRadio = document.querySelector('input[name="municipiosChartVisualType"]:checked');
        console.log('Radio button visual seleccionado:', checkedRadio);
        
        if (!checkedRadio) {
            console.error('No hay radio button seleccionado para tipo visual de municipios');
            return;
        }
        
        const visualType = checkedRadio.value;
        console.log('Tipo visual seleccionado:', visualType);
        
        const canvas = document.getElementById('municipiosChart');
        if (!canvas) {
            console.error('Canvas de municipios no encontrado');
            return;
        }
        
        // Debug: verificar estado del gráfico actual
        console.log('Estado actual de municipiosChart:', {
            existe: !!window.municipiosChart,
            tipo: typeof window.municipiosChart,
            tieneDestroy: window.municipiosChart && typeof window.municipiosChart.destroy === 'function',
            esChart: window.municipiosChart && window.municipiosChart instanceof Chart
        });
        
        // Destruir gráfico existente si existe
        if (window.municipiosChart && typeof window.municipiosChart.destroy === 'function') {
            console.log('Destruyendo gráfico existente de municipios');
            window.municipiosChart.destroy();
        } else if (window.municipiosChart) {
            console.log('Gráfico existente encontrado pero no tiene método destroy, limpiando canvas');
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        } else {
            console.log('No hay gráfico existente para destruir');
        }
        
        // Obtener datos de municipios
        let municipiosData;
        if (backendData.porMunicipios && backendData.porMunicipios.length > 0) {
            // Tomar solo los primeros 8 municipios para el gráfico
            const topMunicipios = backendData.porMunicipios.slice(0, 8);
            municipiosData = {
                labels: topMunicipios.map(item => 
                    item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`
                ),
                data: topMunicipios.map(item => item.total),
                backgroundColor: topMunicipios.map(item => {
                    const nombre = item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`;
                    return generateColorFromName(nombre);
                })
            };
        } else {
            // Datos de ejemplo con colores consistentes
            const municipiosEjemplo = ['Santa Marta', 'Ciénaga', 'El Banco', 'Plato', 'Fundación', 'Pivijay', 'Algarrobo', 'Zona Bananera'];
            municipiosData = {
                labels: municipiosEjemplo,
                data: [1962, 159, 87, 84, 83, 57, 52, 50],
                backgroundColor: municipiosEjemplo.map(nombre => generateColorFromName(nombre))
            };
        }
        
        console.log('Datos de municipios transformados:', municipiosData);
        
        let nuevoGrafico = null;
        
        if (visualType === 'dona') {
            console.log('Creando gráfico de dona para municipios');
            nuevoGrafico = createDoughnutChart('municipiosChart', municipiosData);
            if (!nuevoGrafico) {
                console.error('No se pudo crear el gráfico de dona');
                return;
            }
        } else {
            console.log('Creando gráfico de barras para municipios');
            nuevoGrafico = createBarChart('municipiosChart', municipiosData);
            if (!nuevoGrafico) {
                console.error('No se pudo crear el gráfico de barras');
                return;
            }
        }
        
        // Verificar que el gráfico se creó correctamente
        if (nuevoGrafico && typeof nuevoGrafico.destroy === 'function') {
            window.municipiosChart = nuevoGrafico;
            console.log('Gráfico de municipios alternado exitosamente a:', visualType);
            console.log('Gráfico creado y asignado:', window.municipiosChart);
        } else {
            console.error('El gráfico creado no es válido:', nuevoGrafico);
            return;
        }
    } catch (error) {
        console.error('Error al alternar tipo visual de gráfico de municipios:', error);
        console.error('Stack trace:', error.stack);
    }
}

// Función para alternar gráfico de estado del diagnóstico
function toggleEstadoDiagnosticoChart() {
    console.log('=== FUNCIÓN toggleEstadoDiagnosticoChart EJECUTADA ===');
    
    try {
        const checkedRadio = document.querySelector('input[name="estadoDiagnosticoChartType"]:checked');
        console.log('Radio button seleccionado:', checkedRadio);
        
        if (!checkedRadio) {
            console.error('No hay radio button seleccionado para estado del diagnóstico');
            return;
        }
        
        const chartType = checkedRadio.value;
        console.log('Tipo de gráfico seleccionado:', chartType);
        
        const canvas = document.getElementById('estadoDiagnosticoChart');
        console.log('Canvas de estado del diagnóstico encontrado:', canvas);
        
        if (!canvas) {
            console.error('Canvas de estado del diagnóstico no encontrado');
            return;
        }
        
        // Destruir gráfico existente si existe
        if (estadoDiagnosticoChart) {
            console.log('Destruyendo gráfico existente de estado del diagnóstico');
            estadoDiagnosticoChart.destroy();
        }
        
        // Obtener datos de estado del diagnóstico
        let estadoData;
        if (backendData.porEstadoDiagnostico && backendData.porEstadoDiagnostico.length > 0) {
            // Calcular total para porcentajes
            const totalDiagnosticos = backendData.porEstadoDiagnostico.reduce((sum, item) => sum + item.total, 0);
            
            // Transformar los datos del backend al formato esperado por las funciones de gráficos
            estadoData = {
                labels: backendData.porEstadoDiagnostico.map(item => item.estado),
                data: backendData.porEstadoDiagnostico.map(item => item.total),
                backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c']
            };
        } else {
            // Datos de ejemplo si no hay datos del backend
            estadoData = {
                labels: ['Terminado', 'Pendiente'],
                data: [2716, 210],
                backgroundColor: ['#28a745', '#ffc107']
            };
        }
        
        console.log('Datos de estado del diagnóstico transformados:', estadoData);
        
        if (chartType === 'dona') {
            console.log('Creando gráfico de dona para estado del diagnóstico');
            // Crear gráfico de dona
            estadoDiagnosticoChart = createDoughnutChart('estadoDiagnosticoChart', estadoData);
            if (estadoDiagnosticoChart) {
                updateEstadoDiagnosticoContent(estadoData, true); // true = mostrar porcentajes
            } else {
                console.error('No se pudo crear el gráfico de dona para estado del diagnóstico');
            }
        } else {
            console.log('Creando gráfico de barras para estado del diagnóstico');
            // Crear gráfico de barras
            estadoDiagnosticoChart = createBarChart('estadoDiagnosticoChart', estadoData);
            if (estadoDiagnosticoChart) {
                updateEstadoDiagnosticoContent(estadoData, false); // false = mostrar conteos
            } else {
                console.error('No se pudo crear el gráfico de barras para estado del diagnóstico');
            }
        }
        
        console.log('Gráfico de estado del diagnóstico alternado exitosamente a:', chartType);
    } catch (error) {
        console.error('Error al alternar gráfico de estado del diagnóstico:', error);
        console.error('Stack trace:', error.stack);
    }
}

// ===== INICIALIZACIÓN PRINCIPAL =====

document.addEventListener('DOMContentLoaded', async function() {
    console.log('🚀 DOMContentLoaded iniciado');
    
    try {
        console.log('📚 Cargando librerías...');
        // Cargar Chart.js y Google Maps en paralelo
        const [chartJSLoaded, googleMapsLoaded] = await Promise.all([
            typeof Chart === 'undefined' ? loadChartJS() : Promise.resolve(),
            loadGoogleMaps()
        ]);
        console.log('✅ Librerías cargadas:', { chartJSLoaded, googleMapsLoaded });
        
        console.log('🎯 Inicializando dashboard...');
        // Inicializar dashboard
        initializeDashboard();
        console.log('✅ Dashboard inicializado');
        
        // Inicializar gráficos si Chart.js está disponible
        if (typeof Chart !== 'undefined') {
            console.log('📊 Inicializando gráficos...');
            initializeCharts();
            console.log('✅ Gráficos inicializados');
            
            // FORZAR la vista Top 8 después de un breve delay para asegurar que funcione
            setTimeout(() => {
                console.log('⏰ Timeout ejecutado, verificando datos de municipios...');
                if (window.backendData && window.backendData.porMunicipios) {
                    console.log('🔄 Actualizando tabla de municipios...');
                    updateMunicipiosTable();
                } else {
                    console.log('⚠️ No hay datos de municipios disponibles para inicializar');
                }
                
                // FORZAR ocultar loading después de verificar municipios
                console.log('🔄 Forzando ocultar loading después de verificar municipios...');
                hideLoading();
            }, 1000);
        } else {
            console.log('⚠️ Chart.js no está disponible');
        }
        
        // Inicializar mapa si Google Maps está disponible
        if (typeof google !== 'undefined' && google.maps) {
            console.log('🗺️ Inicializando mapa...');
            initializeMap();
            console.log('✅ Mapa inicializado');
        } else {
            console.log('⚠️ Google Maps no está disponible');
        }
        
        console.log('🎉 Inicialización completa del dashboard');
        
        // FORZAR ocultar el loading después de un breve delay para asegurar que se ejecute
        setTimeout(() => {
            console.log('🔄 Forzando ocultar loading después de timeout...');
            hideLoading();
        }, 2000);
        
    } catch (error) {
        console.error('❌ Error al cargar librerías:', error);
        alert('Error al cargar librerías: ' + error.message);
        // Continuar sin gráficos o mapa
        console.log('🔄 Continuando sin librerías...');
        initializeDashboard();
        
        // También forzar ocultar loading en caso de error
        setTimeout(() => {
            console.log('🔄 Forzando ocultar loading después de error...');
            hideLoading();
        }, 2000);
    }
});

// Hacer todas las funciones disponibles globalmente
window.cambiarVistaMapa = cambiarVistaMapa;
window.zoomToColombia = zoomToColombia;
window.filtrarPorDepartamento = filtrarPorDepartamento;
window.filtrarPorSector = filtrarPorSector;
window.filtrarPorEtapa = filtrarPorEtapa;
window.cambiarPeriodo = cambiarPeriodo;
window.resetFilters = resetFilters;
window.refreshDashboard = refreshDashboard;
window.togglePerformanceMode = togglePerformanceMode;


window.cambiarPeriodoGrafico = cambiarPeriodoGrafico;
window.aplicarRangoFechas = aplicarRangoFechas;
window.updateChartContent = updateChartContent;
window.updateMunicipiosTable = updateMunicipiosTable;
window.updateEstadoDiagnosticoContent = updateEstadoDiagnosticoContent;
window.updateTipoOrganizacionContent = updateTipoOrganizacionContent;
window.updateEtapasContent = updateEtapasContent;
window.updateSectoresContent = updateSectoresContent;
window.updateTamanosContent = updateTamanosContent;
window.loadRealMapMarkers = loadRealMapMarkers;
window.updateMetricTrend = updateMetricTrend;
window.mostrarResumenProporciones = mostrarResumenProporciones;
window.generateColorFromName = generateColorFromName;
window.mostrarVistaCompactaProporciones = mostrarVistaCompactaProporciones;
window.mostrarListaCompletaProporciones = mostrarListaCompletaProporciones;
// window.toggleProporcionesCompletas = toggleProporcionesCompletas; // Función eliminada
window.updateProporcionesTableLocal = updateProporcionesTableLocal;
window.updateProporcionesTableFallbackLocal = updateProporcionesTableFallbackLocal;
window.updateProporcionesTableCompletaLocal = updateProporcionesTableCompletaLocal;

// Funciones de alternancia de gráficos
window.toggleMunicipiosChart = toggleMunicipiosChart;
window.toggleMunicipiosChartVisual = toggleMunicipiosChartVisual;
window.mostrarVistaTop8 = mostrarVistaTop8;
window.mostrarListaCompletaMunicipios = mostrarListaCompletaMunicipios;
window.updateMunicipiosListaTable = updateMunicipiosListaTable;
window.debugBackendData = debugBackendData;
window.cargarTodosLosDepartamentos = cargarTodosLosDepartamentos;
window.cargarDatosAlternativo = cargarDatosAlternativo;
window.resetearFiltros = resetearFiltros;
window.analizarUnidadesPerdidas = analizarUnidadesPerdidas;
window.toggleSectoresChart = toggleSectoresChart;
window.toggleTamanosChart = toggleTamanosChart;
window.toggleTipoOrganizacionChart = toggleTipoOrganizacionChart;
window.toggleEstadoDiagnosticoChart = toggleEstadoDiagnosticoChart;
window.setupChartToggleListeners = setupChartToggleListeners;

// Función para descargar gráficos
window.downloadChartAsImage = downloadChartAsImage;




// Función para actualizar la tabla de proporciones (versión local) - COMENTADA PORQUE NO EXISTE LA TABLA HTML
/*
function updateProporcionesTableLocal(mostrarCompleta = false) {
    console.log('=== updateProporcionesTableLocal EJECUTADA ===');
    console.log('Parámetro mostrarCompleta:', mostrarCompleta);
    
    try {
        const tableBody = document.getElementById('proporcionesTableBody');
        if (!tableBody) {
            console.error('Tabla de proporciones no encontrada');
            return;
        }
        
        // ... resto de la función comentada ...
        
    } catch (error) {
        console.error('Error al actualizar tabla de proporciones:', error);
        
        const tableBody = document.getElementById('proporcionesTableBody');
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-danger">Error al cargar datos: ${error.message}</td>
                </tr>
            `;
        }
    }
}
*/

// Función para actualizar la tabla de proporciones con datos limitados (versión local) - COMENTADA PORQUE NO EXISTE LA TABLA HTML
/*
function updateProporcionesTableFallbackLocal() {
    console.log('Actualizando tabla de proporciones con datos limitados (fallback local)...');
    
    try {
        const tableBody = document.getElementById('proporcionesTableBody');
        if (!tableBody) {
            console.error('Tabla de proporciones no encontrada');
            return;
        }
        
        // ... resto de la función comentada ...
        
    } catch (error) {
        console.error('Error al actualizar tabla de proporciones (fallback local):', error);
        console.error('Error completo:', error.stack);
        
        const tableBody = document.getElementById('proporcionesTableBody');
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-danger">Error al cargar datos: ${error.message}</td>
                </tr>
            `;
        }
    }
}
*/

// Función para actualizar la tabla de proporciones completa (versión local) - COMENTADA PORQUE NO EXISTE LA TABLA HTML
/*
function updateProporcionesTableCompletaLocal(mostrarCompleta = false) {
    console.log('=== updateProporcionesTableCompletaLocal EJECUTADA ===');
    console.log('Parámetro mostrarCompleta:', mostrarCompleta);
    
    try {
        const tableBody = document.getElementById('proporcionesTableBodyCompleta');
        if (!tableBody) {
            console.error('Tabla de proporciones completa no encontrada');
            return;
        }
        
        // ... resto de la función comentada ...
        
    } catch (error) {
        console.error('Error al actualizar tabla de proporciones completa:', error);
        
        const tableBody = document.getElementById('proporcionesTableBodyCompleta');
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-danger">Error al cargar datos: ${error.message}</td>
                </tr>
            `;
        }
    }
}
*/

// Función callback para Google Maps
function initGoogleMaps() {
    // El mapa se inicializará automáticamente cuando se cargue la página
}
</script>

<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('dashboard.maps.google_maps_key') }}&libraries=places&callback=initGoogleMaps" async defer></script>


<script>
    const cargando = document.querySelectorAll('.cargando')[0];
    cargando.classList.add('d-none');
</script>


@endsection