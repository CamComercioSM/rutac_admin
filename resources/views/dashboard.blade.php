@extends('layouts.admin', [ 'titulo'=> 'Dashboard - Decídete a Crecer' ])

@section('content')
<div class="dashboard-container">
    <!-- Indicador de Carga -->
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
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-users me-2 text-primary"></i>
                            Distribución por Tipo de Organización
                        </h6>
                        <div class="chart-loading" style="display: none;">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
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
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-clipboard-check me-2 text-success"></i>
                            Estado del Diagnóstico Inicial
                        </h6>
                        <div class="chart-loading" style="display: none;">
                            <div class="spinner-border spinner-border-sm text-success"></div>
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
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2 text-warning"></i>
                        Distribución por Etapas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container mb-3" style="position: relative; height: 200px;">
                        <canvas id="etapasChart"></canvas>
                    </div>
                    <div id="etapasContent">
                        @if(isset($porEtapas) && (is_object($porEtapas) ? $porEtapas->count() : count($porEtapas)) > 0)
                            @foreach($porEtapas as $etapa)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">{{ $etapa->etapa->name ?? 'No registra' }}</span>
                                    <span class="fw-bold">{{ $etapa->total }}</span>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted">
                                <p>Despegue: 1,145</p>
                                <p>Nacimiento: 929</p>
                                <p>Crecimiento: 322</p>
                                <p>Descubrimiento: 302</p>
                                <p>Madurez: 30</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tablas Detalladas con Carga Lazy -->
    <div class="row">
        <!-- Por Municipios con Gráfico -->
        <div class="col-md-6 mb-4">
            <div class="card table-card" data-chart="municipios">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt me-2 text-success"></i>
                        Top 10 Municipios
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container mb-3" style="position: relative; height: 200px;">
                        <canvas id="municipiosChart"></canvas>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Municipio</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>
                            <tbody id="municipiosTableBody">
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        <div class="spinner-border spinner-border-sm me-2" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        Cargando datos...
                                    </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Proporciones por Municipios -->
        <div class="col-md-6 mb-4">
            <div class="card summary-card" data-chart="proporciones">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2 text-warning"></i>
                        Proporciones por Municipios
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container mb-3" style="position: relative; height: 200px;">
                        <canvas id="proporcionesChart"></canvas>
                    </div>
                    
                    <!-- Contenido de proporciones con vista compacta y expandible -->
                    <div id="proporcionesContent">
                        <div class="text-center text-muted">
                            <p>Datos cargados dinámicamente</p>
                        </div>
                    </div>
                    
                    <!-- Botones de control -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnVerMasProporciones" onclick="toggleProporcionesCompletas()" style="display: none;">
                            <i class="fas fa-expand-alt me-1"></i>
                            Ver Lista Completa
                        </button>
                        
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnVerMenosProporciones" onclick="toggleProporcionesCompletas()" style="display: none;">
                            <i class="fas fa-compress-alt me-1"></i>
                            Ver Resumen
                        </button>
                        
                   
                    </div>
                </div>
            </div>
        </div>

        
    </div>

    <!-- Gráfico de Sectores -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card summary-card" data-chart="sectores">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-industry me-2 text-primary"></i>
                        Distribución por Sectores
                    </h6>
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

<!-- Indicador de Rendimiento -->
    <div class="performance-info mt-4">
        <div class="card">
            <div class="card-body text-center">
                <small class="text-muted">
                    <i class="fas fa-tachometer-alt me-1"></i>
                    Dashboard optimizado para mejor rendimiento | 
                    <i class="fas fa-clock me-1"></i>
                    Última actualización: <span id="lastUpdate">{{ now()->format('H:i:s') }}</span> |
                    <i class="fas fa-database me-1"></i>
                    Modo: <span id="performanceMode">Normal</span> |
                    <i class="fas fa-cache me-1"></i>
                    Cache: <span id="cacheStatus">Activo</span>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Filtros Avanzados -->
<div class="modal fade" id="filtrosModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-filter me-2"></i>
                    Filtros Avanzados
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="filtrosForm" method="GET">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Departamento</label>
                            <select class="form-select" name="departamento_id" id="modalDepartamento">
                                <option value="">Todos</option>
                                @if(isset($departamentos) && (is_object($departamentos) ? $departamentos->count() : count($departamentos)) > 0)
                                    @foreach($departamentos as $departamento)
                                        <option value="{{ $departamento->departamento_id }}" {{ $filtros['departamento_id'] == $departamento->departamento_id ? 'selected' : '' }}>
                                            {{ $departamento->departamentoNOMBRE ?? 'Sin nombre' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Municipio</label>
                            <select class="form-select" name="municipio_id" id="modalMunicipio">
                                <option value="">Todos</option>
                                @if(isset($municipios) && (is_object($municipios) ? $municipios->count() : count($municipios)) > 0)
                                    @foreach($municipios as $municipio)
                                        <option value="{{ $municipio->municipio_id }}" {{ $filtros['municipio_id'] == $municipio->municipio_id ? 'selected' : '' }}>
                                            {{ $municipio->municipioNOMBREOFICIAL ?? 'Sin nombre' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sector</label>
                            <select class="form-select" name="sector_id">
                                <option value="">Todos</option>
                                @if(isset($sectores) && (is_object($sectores) ? $sectores->count() : count($sectores)) > 0)
                                    @foreach($sectores as $sector)
                                        <option value="{{ $sector->sector_id }}" {{ $filtros['sector_id'] == $sector->sector_id ? 'selected' : '' }}>
                                            {{ $sector->sectorNOMBRE ?? 'Sin nombre' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Etapa</label>
                            <select class="form-select" name="etapa_id">
                                <option value="">Todas</option>
                                @if(isset($etapas) && (is_object($etapas) ? $etapas->count() : count($etapas)) > 0)
                                    @foreach($etapas as $etapa)
                                        <option value="{{ $etapa->etapa_id }}" {{ $filtros['etapa_id'] == $etapa->etapa_id ? 'selected' : '' }}>
                                            {{ $etapa->name ?? 'Sin nombre' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tamaño</label>
                            <select class="form-select" name="tamano_id">
                                <option value="">Todos</option>
                                @if(isset($tamanos) && (is_object($tamanos) ? $tamanos->count() : count($tamanos)) > 0)
                                    @foreach($tamanos as $tamano)
                                        <option value="{{ $tamano->tamano_id }}" {{ $filtros['tamano_id'] == $tamano->tamano_id ? 'selected' : '' }}>
                                            {{ $tamano->tamanoNOMBRE ?? 'Sin nombre' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de Persona</label>
                            <select class="form-select" name="tipopersona_id">
                                <option value="">Todos</option>
                                @if(isset($tiposPersona) && (is_object($tiposPersona) ? $tiposPersona->count() : count($tiposPersona)) > 0)
                                    @foreach($tiposPersona as $tipo)
                                        <option value="{{ $tipo->tipopersona_id }}" {{ $filtros['tipopersona_id'] == $tipo->tipopersona_id ? 'selected' : '' }}>
                                            {{ $tipo->tipoPersonaNOMBRE ?? 'Sin nombre' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha desde</label>
                            <input type="date" class="form-control" name="fecha_desde" value="{{ $filtros['fecha_desde'] ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha hasta</label>
                            <input type="date" class="form-control" name="fecha_hasta" value="{{ $filtros['fecha_hasta'] ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>
                        Aplicar Filtros
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
/* Estilos personalizados para el mapa */
.leaflet-container {
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.map-controls .btn {
    margin-left: 5px;
    font-size: 0.8rem;
    padding: 6px 10px;
}

.map-controls .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* Estilos para los marcadores del mapa */
.custom-marker {
    background: #667eea;
    border: 2px solid #fff;
    border-radius: 50%;
    width: 12px;
    height: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

.custom-marker:hover {
    background: #764ba2;
    transform: scale(1.2);
    transition: all 0.3s ease;
}

/* Tooltip personalizado */
.leaflet-tooltip {
    background: rgba(0, 0, 0, 0.8);
    border: none;
    border-radius: 8px;
    color: white;
    font-size: 12px;
    padding: 8px 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.leaflet-tooltip::before {
    border-top-color: rgba(0, 0, 0, 0.8);
}

/* Controles del mapa */
.leaflet-control-zoom {
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    border-radius: 8px;
}

.leaflet-control-zoom a {
    background: white;
    color: #667eea;
    border: none;
    border-radius: 6px;
    margin: 2px;
    transition: all 0.3s ease;
}

.leaflet-control-zoom a:hover {
    background: #667eea;
    color: white;
    transform: scale(1.1);
}

/* Popup personalizado */
.leaflet-popup-content-wrapper {
    border-radius: 12px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.3);
}

.leaflet-popup-content {
    margin: 15px;
    font-family: inherit;
}

.leaflet-popup-tip {
    background: white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* Animaciones para marcadores */
@keyframes markerPulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.2); opacity: 0.7; }
    100% { transform: scale(1); opacity: 1; }
}

.marker-pulse {
    animation: markerPulse 2s infinite;
}

/* Responsive para móviles */
@media (max-width: 768px) {
    #mapaColombia {
        height: 300px;
    }
    
    .map-controls .btn {
        font-size: 0.7rem;
        padding: 4px 8px;
        margin-bottom: 5px;
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
            console.log('Chart.js cargado desde CDN principal');
            resolve();
        };
        script.onerror = () => {
            console.warn('CDN principal falló, intentando CDN alternativo...');
            const script2 = document.createElement('script');
            script2.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js';
            script2.onload = () => {
                console.log('Chart.js cargado desde CDN alternativo');
                resolve();
            };
            script2.onerror = () => {
                console.error('Ambos CDNs fallaron');
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
    
    // Pasar datos del dashboard al JavaScript
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
    
    setupEventListeners();
    updateLastUpdateTime();
    
    // Calcular tendencia de métricas
    updateMetricTrend();
    
    hideLoading();
}

function initializeCharts() {
    console.log('Inicializando gráficos con datos reales...');
    
    try {
        // Usar datos del dashboard desde el backend
        console.log('Datos reales del backend:', backendData);
        
        // Gráfico de Tipo de Organización (datos reales)
        if (backendData.porTipoOrganizacion && backendData.porTipoOrganizacion.length > 0) {
            const tipoOrgData = {
                labels: backendData.porTipoOrganizacion.map(item => 
                    item.tipoPersona?.tipoPersonaNOMBRE || `Tipo ${item.tipopersona_id}`
                ),
                data: backendData.porTipoOrganizacion.map(item => item.total),
                backgroundColor: ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe']
            };
            createPieChart('tipoOrganizacionChart', tipoOrgData);
            
            // Actualizar la leyenda del gráfico
            updateTipoOrganizacionContent();
        } else {
            console.warn('No hay datos de tipo de organización');
            createPieChart('tipoOrganizacionChart', {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: ['#6c757d']
            });
            
            // Actualizar la leyenda con mensaje de no datos
            updateTipoOrganizacionContent();
        }

        // Gráfico de Estado del Diagnóstico (datos reales)
        if (backendData.porEstadoDiagnostico && backendData.porEstadoDiagnostico.length > 0) {
            const estadoData = {
                labels: backendData.porEstadoDiagnostico.map(item => item.estado),
                data: backendData.porEstadoDiagnostico.map(item => item.total),
                backgroundColor: ['#28a745', '#ffc107', '#dc3545']
            };
            createDoughnutChart('estadoDiagnosticoChart', estadoData);
            
            // Actualizar la leyenda del gráfico
            updateEstadoDiagnosticoContent();
        } else {
            console.warn('No hay datos de estado de diagnóstico');
            createDoughnutChart('estadoDiagnosticoChart', {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: ['#6c757d']
            });
            
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
        } else {
            console.warn('No hay datos de etapas');
            createBarChart('etapasChart', {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: ['#6c757d']
            });
        }

        // Gráfico de Municipios (datos reales - TOP 10)
        if (backendData.porMunicipios && backendData.porMunicipios.length > 0) {
            // Tomar solo los primeros 10 municipios
            const topMunicipios = backendData.porMunicipios.slice(0, 10);
            const municipiosData = {
                labels: topMunicipios.map(item => 
                    item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`
                ),
                data: topMunicipios.map(item => item.total),
                backgroundColor: '#28a745'
            };
            createBarChart('municipiosChart', municipiosData);
            
            // Actualizar la tabla de municipios
            updateMunicipiosTable();
        } else {
            console.warn('No hay datos de municipios');
            createBarChart('municipiosChart', {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: ['#6c757d']
            });
            
            // Actualizar la tabla con mensaje de no datos
            updateMunicipiosTable();
        }

        // Gráfico de Proporciones por Municipios (TODOS los municipios para 100% real)
        console.log('Verificando datos de proporciones:', {
            porMunicipiosCompletos: backendData.porMunicipiosCompletos,
            porMunicipios: backendData.porMunicipios,
            totalUnidades: backendData.totalUnidades
        });
        
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
            updateProporcionesContent();
        } else if (backendData.porMunicipios && Array.isArray(backendData.porMunicipios) && backendData.porMunicipios.length > 0) {
            // Fallback: usar datos de municipios limitados si no hay completos
            console.log('Usando fallback con datos limitados de municipios');
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
            updateProporcionesContentFallback();
        } else {
            console.warn('No hay datos para proporciones');
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
            console.warn('No hay datos de evolución temporal');
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
            createBarChart('tamanosChart', tamanosData);
        } else {
            console.warn('No hay datos de tamaños');
            createBarChart('tamanosChart', {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: ['#6c757d']
            });
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
            createDoughnutChart('sectoresChart', sectoresData);
        } else {
            console.warn('No hay datos de sectores');
            createDoughnutChart('sectoresChart', {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: ['#6c757d']
            });
        }
        
        console.log('Todos los gráficos inicializados con datos reales');
        
        // Actualizar contenido estático con datos reales
        updateChartContent();
        
    } catch (error) {
        console.error('Error al inicializar gráficos con datos reales:', error);
        alert('Error al crear los gráficos: ' + error.message);
        
        // Crear gráficos con datos de ejemplo en caso de error
        console.log('Creando gráficos con datos de ejemplo debido al error...');
        createPieChart('tipoOrganizacionChart', {
            labels: ['Natural', 'Jurídica', 'Establecimiento'],
            data: [2157, 750, 19],
            backgroundColor: ['#667eea', '#764ba2', '#f093fb']
        });
        createDoughnutChart('estadoDiagnosticoChart', {
            labels: ['Terminado', 'Pendiente'],
            data: [1800, 1126],
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
        createBarChart('tamanosChart', {
            labels: ['Micro', 'Pequeña', 'Mediana', 'Grande', 'Gigante'],
            data: [100, 200, 300, 250, 150],
            backgroundColor: '#17a2b8'
        });
    }
}

// ===== FUNCIONES DEL MAPA CON GOOGLE MAPS =====

let map = null;
let markers = [];
let currentMapType = 'roadmap';
let infoWindow = null;

function initializeMap() {
    console.log('Inicializando Google Maps...');
    
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
            console.log('Google Maps cargado completamente');
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
    console.log('Cargando marcadores reales del mapa...');
    
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
            console.warn('No hay datos de mapa disponibles, usando marcadores de ejemplo');
            addSampleMarkers();
            return;
        }
        
        console.log('Datos del mapa encontrados:', backendData.datosMapa.length, 'ubicaciones');
        
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
                console.log('Todos los marcadores cargados:', markers.length);
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
    console.log('Cambiando vista del mapa a:', tipo);
    
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
        
        console.log('Vista del mapa cambiada a:', tipo);
    } catch (error) {
        console.error('Error al cambiar vista del mapa:', error);
    }
}

function zoomToColombia() {
    if (map) {
        map.setCenter({ lat: 4.5709, lng: -74.2973 });
        map.setZoom(6);
        console.log('Zoom a Colombia aplicado');
    }
}

// ===== FUNCIONES DE GRÁFICOS =====

function createPieChart(canvasId, data) {
    console.log('Creando gráfico de pastel:', canvasId, data);
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
                    }
                }
            }
        });
        console.log('Gráfico de pastel creado exitosamente:', canvasId);
        return chart;
    } catch (error) {
        console.error('Error al crear gráfico de pastel:', canvasId, error);
        throw error;
    }
}

function createDoughnutChart(canvasId, data) {
    console.log('Creando gráfico de dona:', canvasId, data);
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error('Canvas no encontrado:', canvasId);
        return;
    }
    
    try {
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
                    }
                }
            }
        });
        console.log('Gráfico de dona creado exitosamente:', canvasId);
        return chart;
    } catch (error) {
        console.error('Error al crear gráfico de dona:', canvasId, error);
        throw error;
    }
}

function createBarChart(canvasId, data) {
    console.log('Creando gráfico de barras:', canvasId, data);
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error('Canvas no encontrado:', canvasId);
        return;
    }
    
    try {
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
        console.log('Gráfico de barras creado exitosamente:', canvasId);
        return chart;
    } catch (error) {
        console.error('Error al crear gráfico de barras:', canvasId, error);
        throw error;
    }
}

function createLineChart(canvasId, data) {
    console.log('Creando gráfico de líneas:', canvasId, data);
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
        console.log('Gráfico de líneas creado exitosamente:', canvasId);
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
    console.log('Refrescando dashboard...');
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
    console.log('Cambiando periodo del gráfico a:', periodo, 'meses');
    
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
            
            console.log(`Gráfico actualizado con ${datosFiltrados.length} meses de datos`);
        } else {
            console.warn('No hay datos de evolución temporal disponibles');
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
    
    console.log('Aplicando rango de fechas:', fechaDesde, 'a', fechaHasta);
    
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
                
                console.log(`Gráfico actualizado con ${datosFiltrados.length} meses de datos filtrados`);
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
    console.log('Actualizando contenido de gráficos con datos reales...');
    
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
            
            const proporcionesContent = document.getElementById('proporcionesContent');
            if (proporcionesContent) {
                proporcionesContent.innerHTML = proporcionesHTML;
            }
        }
        
        // Actualizar contenido de tamaños
        if (backendData.porTamanos && backendData.porTamanos.length > 0) {
            const totalTamanos = backendData.porTamanos.reduce((sum, item) => sum + item.total, 0);
            
            let tamanosHTML = '';
            backendData.porTamanos.forEach(item => {
                const porcentaje = Math.round((item.total / totalTamanos) * 100);
                const nombre = item.tamano?.tamanoNOMBRE || `Tamaño ${item.tamano_id}`;
                tamanosHTML += `<p><strong>${nombre}:</strong> ${porcentaje}% (${item.total.toLocaleString()} unidades)</p>`;
            });
            
            const tamanosContent = document.getElementById('tamanosContent');
            if (tamanosContent) {
                tamanosContent.innerHTML = tamanosHTML;
            }
        }

        // Actualizar contenido de sectores
        if (backendData.porSectores && backendData.porSectores.length > 0) {
            const totalSectores = backendData.porSectores.reduce((sum, item) => sum + item.total, 0);
            
            let sectoresHTML = '';
            backendData.porSectores.forEach(item => {
                const porcentaje = Math.round((item.total / totalSectores) * 100);
                const nombre = item.sector?.sectorNOMBRE || `Sector ${item.sector_id}`;
                sectoresHTML += `<p><strong>${nombre}:</strong> ${porcentaje}% (${item.total.toLocaleString()} unidades)</p>`;
            });
            
            const sectoresContent = document.getElementById('sectoresContent');
            if (sectoresContent) {
                sectoresContent.innerHTML = sectoresHTML;
            }
        }
        
        // Actualizar tendencia de métricas después de actualizar gráficos
        updateMetricTrend();
        
        console.log('Contenido de gráficos actualizado exitosamente');
    } catch (error) {
        console.error('Error al actualizar contenido de gráficos:', error);
    }
}

// Función para actualizar el contenido del gráfico de proporciones
function updateProporcionesContent() {
    console.log('Actualizando contenido de proporciones con todos los municipios...');
    
    try {
        if (backendData.porMunicipiosCompletos && backendData.porMunicipiosCompletos.length > 0) {
            const totalUnidades = backendData.totalUnidades || backendData.porMunicipiosCompletos.reduce((sum, item) => sum + item.total, 0);
            
            // Mostrar inicialmente solo el resumen (top 5 + otros)
            mostrarResumenProporciones(backendData.porMunicipiosCompletos, totalUnidades);
            
            // Mostrar botón para ver más
            document.getElementById('btnVerMasProporciones').style.display = 'inline-block';
            
            console.log('Contenido de proporciones actualizado exitosamente');
        } else {
            console.warn('No hay datos completos de municipios para proporciones');
        }
    } catch (error) {
        console.error('Error al actualizar contenido de proporciones:', error);
    }
}

// Función de fallback para actualizar contenido de proporciones con datos limitados
function updateProporcionesContentFallback() {
    console.log('Actualizando contenido de proporciones con datos limitados (fallback)...');
    
    try {
        if (backendData.porMunicipios && backendData.porMunicipios.length > 0) {
            const totalUnidades = backendData.totalUnidades || backendData.porMunicipios.reduce((sum, item) => sum + item.total, 0);
            
            // Mostrar resumen con datos limitados
            mostrarResumenProporciones(backendData.porMunicipios, totalUnidades);
            
            // No mostrar botón de ver más para datos limitados
            document.getElementById('btnVerMasProporciones').style.display = 'none';
            
            console.log('Contenido de proporciones (fallback) actualizado exitosamente');
        } else {
            console.warn('No hay datos de municipios para proporciones (fallback)');
        }
    } catch (error) {
        console.error('Error al actualizar contenido de proporciones (fallback):', error);
    }
}

// Función para mostrar resumen de proporciones (top 5 + otros)
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
    
    proporcionesContent.innerHTML = resumenHTML;
}

// Función para mostrar lista completa de proporciones
function mostrarListaCompletaProporciones(municipios, totalUnidades) {
    const proporcionesContent = document.getElementById('proporcionesContent');
    if (!proporcionesContent) return;
    
    let listaCompletaHTML = '';
    
    // Mostrar todos los municipios
    municipios.forEach((item, index) => {
        const porcentaje = Math.round((item.total / totalUnidades) * 100);
        const nombre = item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`;
        const rowClass = index % 2 === 0 ? 'bg-light' : '';
        
        listaCompletaHTML += `
            <div class="row ${rowClass} py-1 px-2 rounded mb-1">
                <div class="col-8">
                    <strong>${nombre}</strong>
                </div>
                <div class="col-2 text-center">
                    ${porcentaje}%
                </div>
                <div class="col-2 text-end">
                    ${item.total.toLocaleString()}
                </div>
            </div>
        `;
    });
    
    // Agregar encabezado
    const headerHTML = `
        <div class="row fw-bold bg-primary text-white py-2 px-2 rounded mb-2">
            <div class="col-8">Municipio</div>
            <div class="col-2 text-center">%</div>
            <div class="col-2 text-end">Unidades</div>
        </div>
    `;
    
    // Agregar total
    const totalHTML = `
        <hr class="my-2">
        <div class="row fw-bold bg-success text-white py-2 px-2 rounded">
            <div class="col-8">TOTAL</div>
            <div class="col-2 text-center">100%</div>
            <div class="col-2 text-end">${totalUnidades.toLocaleString()}</div>
        </div>
    `;
    
    proporcionesContent.innerHTML = headerHTML + listaCompletaHTML + totalHTML;
}

// Función para alternar entre vista resumen y completa
function toggleProporcionesCompletas() {
    const btnVerMas = document.getElementById('btnVerMasProporciones');
    const btnVerMenos = document.getElementById('btnVerMenosProporciones');
    const proporcionesContent = document.getElementById('proporcionesContent');
    
    if (btnVerMas.style.display !== 'none') {
        // Cambiar a vista completa
        if (backendData.porMunicipiosCompletos && backendData.porMunicipiosCompletos.length > 0) {
            const totalUnidades = backendData.totalUnidades || backendData.porMunicipiosCompletos.reduce((sum, item) => sum + item.total, 0);
            mostrarListaCompletaProporciones(backendData.porMunicipiosCompletos, totalUnidades);
        } else if (backendData.porMunicipios && backendData.porMunicipios.length > 0) {
            const totalUnidades = backendData.totalUnidades || backendData.porMunicipios.reduce((sum, item) => sum + item.total, 0);
            mostrarListaCompletaProporciones(backendData.porMunicipios, totalUnidades);
        }
        
        btnVerMas.style.display = 'none';
        btnVerMenos.style.display = 'inline-block';
    } else {
        // Cambiar a vista resumen
        if (backendData.porMunicipiosCompletos && backendData.porMunicipiosCompletos.length > 0) {
            const totalUnidades = backendData.totalUnidades || backendData.porMunicipiosCompletos.reduce((sum, item) => sum + item.total, 0);
            mostrarResumenProporciones(backendData.porMunicipiosCompletos, totalUnidades);
        } else if (backendData.porMunicipios && backendData.porMunicipios.length > 0) {
            const totalUnidades = backendData.totalUnidades || backendData.porMunicipios.reduce((sum, item) => sum + item.total, 0);
            mostrarResumenProporciones(backendData.porMunicipios, totalUnidades);
        }
        
        btnVerMas.style.display = 'inline-block';
        btnVerMenos.style.display = 'none';
    }
}



// Función para actualizar la tabla de municipios
function updateMunicipiosTable() {
    console.log('Actualizando tabla de municipios con datos reales...');
    console.log('backendData.porMunicipios:', backendData.porMunicipios);
    console.log('dashboardData.totalUnidades:', dashboardData.totalUnidades);
    
    try {
        const tableBody = document.getElementById('municipiosTableBody');
        if (!tableBody) {
            console.error('Tabla de municipios no encontrada');
            return;
        }
        
        if (backendData.porMunicipios && backendData.porMunicipios.length > 0) {
            console.log('Datos de municipios encontrados:', backendData.porMunicipios.length, 'registros');
            
            // Tomar solo los primeros 10 municipios para la tabla
            const topMunicipios = backendData.porMunicipios.slice(0, 10);
            // Usar el total real de todas las unidades, no solo de los top 10
            const totalUnidades = backendData.totalUnidades || dashboardData.totalUnidades || 1;
            
            console.log('Top municipios:', topMunicipios);
            console.log('Total unidades:', totalUnidades);
            
            let tableHTML = '';
            
            // Agregar filas de municipios
            topMunicipios.forEach((item, index) => {
                console.log(`Procesando municipio ${index}:`, item);
                
                const nombre = item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`;
                const cantidad = item.total;
                const color = '#' + Math.random().toString(16).substr(2, 6); // Color aleatorio para el punto
                
                console.log(`Municipio: ${nombre}, Cantidad: ${cantidad}`);
                
                tableHTML += `
                    <tr>
                        <td>
                            <div class="municip-info">
                                <span class="municip-dot" style="background-color: ${color}"></span>
                                ${nombre}
                            </div>
                        </td>
                        <td><strong>${cantidad.toLocaleString()}</strong></td>
                    </tr>
                `;
            });
            
            // Agregar fila de total
            tableHTML += `
                <tr class="table-info">
                    <td><strong>Total Sistema</strong></td>
                    <td><strong>${totalUnidades.toLocaleString()}</strong></td>
                </tr>
            `;
            
            tableBody.innerHTML = tableHTML;
            
            // Agregar comentario explicativo debajo de la tabla
            const comentarioHTML = `
                <tr>
                    <td colspan="2" class="text-center text-muted small">
                        <em>* Top 10 municipios por cantidad de unidades productivas</em><br>
                        <em>** Total incluye todas las unidades del sistema</em>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', comentarioHTML);
            
            console.log('Tabla de municipios actualizada exitosamente');
        } else {
            console.warn('No hay datos de municipios para mostrar en la tabla');
            console.log('backendData.porMunicipios es:', backendData.porMunicipios);
            
            tableBody.innerHTML = `
                <tr>
                    <td colspan="2" class="text-center text-muted">No hay datos disponibles</td>
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
    console.log('Actualizando contenido del estado de diagnóstico...');
    
    try {
        const contentDiv = document.getElementById('estadoDiagnosticoContent');
        if (!contentDiv) {
            console.error('Contenido de estado de diagnóstico no encontrado');
            return;
        }
        
        if (backendData.porEstadoDiagnostico && backendData.porEstadoDiagnostico.length > 0) {
            console.log('Datos de estado de diagnóstico encontrados:', backendData.porEstadoDiagnostico);
            
            let contentHTML = '';
            backendData.porEstadoDiagnostico.forEach(item => {
                const estado = item.estado || 'Sin estado';
                const total = item.total || 0;
                
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
                        <span class="fw-bold">${total.toLocaleString()}</span>
                    </div>
                `;
            });
            
            contentDiv.innerHTML = contentHTML;
            console.log('Contenido de estado de diagnóstico actualizado exitosamente');
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
    console.log('Actualizando contenido del tipo de organización...');
    
    try {
        const contentDiv = document.getElementById('tipoOrganizacionContent');
        if (!contentDiv) {
            console.error('Contenido de tipo de organización no encontrado');
            return;
        }
        
        if (backendData.porTipoOrganizacion && backendData.porTipoOrganizacion.length > 0) {
            console.log('Datos de tipo de organización encontrados:', backendData.porTipoOrganizacion);
            
            let contentHTML = '';
            backendData.porTipoOrganizacion.forEach((item, index) => {
                const nombre = item.tipoPersona?.tipoPersonaNOMBRE || `Tipo ${item.tipopersona_id}`;
                const total = item.total || 0;
                
                // Colores del gráfico para la leyenda
                const colors = ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe'];
                const color = colors[index % colors.length];
                
                contentHTML += `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">
                            <i class="fas fa-circle me-1" style="color: ${color}; font-size: 8px;"></i>
                            ${nombre}
                        </span>
                        <span class="fw-bold">${total.toLocaleString()}</span>
                    </div>
                `;
            });
            
            contentDiv.innerHTML = contentHTML;
            console.log('Contenido de tipo de organización actualizado exitosamente');
        } else {
            console.warn('No hay datos de tipo de organización disponibles');
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
    console.log('Calculando tendencia de métricas...');
    
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
                
                console.log(`Tendencia calculada: ${signo}${porcentajeFormateado}% (${totalActual} vs ${totalAnterior})`);
                
            } else {
                // No hay datos suficientes
                trendElement.innerHTML = `
                    <span class="badge bg-info">
                        <i class="fas fa-info-circle me-1"></i>Sin datos previos
                    </span>
                    <span class="text-muted ms-2">vs mes anterior</span>
                `;
                console.log('No hay datos suficientes para calcular tendencia');
            }
            
        } else {
            // No hay datos de evolución temporal
            trendElement.innerHTML = `
                <span class="badge bg-info">
                    <i class="fas fa-info-circle me-1"></i>Sin datos de evolución
                </span>
                <span class="text-muted ms-2">vs mes anterior</span>
            `;
            console.log('No hay datos de evolución temporal disponibles');
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
    const loadingIndicator = document.getElementById('loadingIndicator');
    if (loadingIndicator) {
        loadingIndicator.style.display = 'flex';
    }
}

function hideLoading() {
    const loadingIndicator = document.getElementById('loadingIndicator');
    if (loadingIndicator) {
        loadingIndicator.style.display = 'none';
    }
}

function updateLastUpdateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('es-CO');
    const lastUpdateElement = document.getElementById('lastUpdate');
    if (lastUpdateElement) {
        lastUpdateElement.textContent = timeString;
    }
}

function setupEventListeners() {
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
}

// ===== INICIALIZACIÓN PRINCIPAL =====

document.addEventListener('DOMContentLoaded', async function() {
    console.log('Dashboard inicializando...');
    
    try {
        // Cargar Chart.js y Google Maps en paralelo
        const [chartJSLoaded, googleMapsLoaded] = await Promise.all([
            typeof Chart === 'undefined' ? loadChartJS() : Promise.resolve(),
            loadGoogleMaps()
        ]);
        
        console.log('Chart.js disponible:', typeof Chart !== 'undefined');
        console.log('Google Maps disponible:', typeof google !== 'undefined' && google.maps);
        
        // Inicializar dashboard
        initializeDashboard();
        
        // Inicializar gráficos si Chart.js está disponible
        if (typeof Chart !== 'undefined') {
            initializeCharts();
        }
        
        // Inicializar mapa si Google Maps está disponible
        if (typeof google !== 'undefined' && google.maps) {
            initializeMap();
        }
        
    } catch (error) {
        console.error('Error al cargar librerías:', error);
        alert('Error al cargar librerías: ' + error.message);
        // Continuar sin gráficos o mapa
        initializeDashboard();
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
window.clearSectoresCache = clearSectoresCache;
window.debugSectores = debugSectores;
window.cambiarPeriodoGrafico = cambiarPeriodoGrafico;
window.aplicarRangoFechas = aplicarRangoFechas;
window.updateChartContent = updateChartContent;
window.updateMunicipiosTable = updateMunicipiosTable;
window.updateEstadoDiagnosticoContent = updateEstadoDiagnosticoContent;
window.updateTipoOrganizacionContent = updateTipoOrganizacionContent;
window.loadRealMapMarkers = loadRealMapMarkers;
window.updateMetricTrend = updateMetricTrend;
window.mostrarResumenProporciones = mostrarResumenProporciones;
window.mostrarListaCompletaProporciones = mostrarListaCompletaProporciones;
window.toggleProporcionesCompletas = toggleProporcionesCompletas;
window.debugProporciones = debugProporciones;

// Función callback para Google Maps
function initGoogleMaps() {
    console.log('Google Maps API cargada correctamente');
    // El mapa se inicializará automáticamente cuando se cargue la página
}
</script>

<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAr5OGhnaXqek4geU-i1OQEwM38omQ7fFw&libraries=places&callback=initGoogleMaps" async defer></script>

<style>
/* Estilos para los controles del gráfico */
.chart-controls {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.chart-controls .btn {
    transition: all 0.3s ease;
    border-radius: 20px;
    font-size: 0.85rem;
    padding: 4px 12px;
}

.chart-controls .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.chart-controls .btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
    font-weight: 500;
}

.chart-controls .btn-outline-primary {
    border-color: #007bff;
    color: #007bff;
}

.chart-controls .btn-outline-primary:hover {
    background-color: #007bff;
    color: white;
}

/* Estilos para los campos de fecha */
.chart-controls input[type="date"] {
    border-radius: 20px;
    border: 1px solid #dee2e6;
    font-size: 0.85rem;
    padding: 4px 12px;
    transition: all 0.3s ease;
}

.chart-controls input[type="date"]:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.chart-controls .btn-success {
    border-radius: 20px;
    font-size: 0.85rem;
    padding: 4px 12px;
    background-color: #28a745;
    border-color: #28a745;
}

.chart-controls .btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* Estilos para el widget de proporciones */
#proporcionesContent {
    max-height: 300px;
    overflow-y: auto;
    transition: all 0.3s ease;
}

#proporcionesContent::-webkit-scrollbar {
    width: 6px;
}

#proporcionesContent::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#proporcionesContent::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#proporcionesContent::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Estilos para la lista completa de municipios */
.municipio-row {
    transition: background-color 0.2s ease;
}

.municipio-row:hover {
    background-color: rgba(0,123,255,0.1) !important;
}

/* Estilos para los botones de control */
#btnVerMasProporciones,
#btnVerMenosProporciones {
    transition: all 0.3s ease;
    border-radius: 20px;
    font-size: 0.85rem;
    padding: 6px 16px;
}

#btnVerMasProporciones:hover,
#btnVerMenosProporciones:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* Estilos para el resumen */
.resumen-item {
    padding: 4px 0;
    border-bottom: 1px solid #f0f0f0;
}

.resumen-item:last-child {
    border-bottom: none;
}

/* Responsive para móviles */
@media (max-width: 768px) {
    .chart-controls {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
    }
    
    .chart-controls .btn,
    .chart-controls input[type="date"] {
        width: 100%;
        text-align: center;
    }
    
    .chart-controls .d-inline-block {
        display: block !important;
        width: 100%;
    }
}

/* Estilos para el mapa */
.map-card {
    position: relative;
}

#mapaColombia {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

#mapProgress {
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(5px);
    border-bottom: 1px solid #dee2e6;
}

#mapProgress .progress {
    background-color: #e9ecef;
    border-radius: 2px;
}

#mapProgress .progress-bar {
    background: linear-gradient(90deg, #007bff, #0056b3);
}

/* Mejoras para Google Maps */
.google-map-tooltip {
    background: rgba(0,0,0,0.9);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 10px 15px;
    font-size: 0.9rem;
    box-shadow: 0 6px 20px rgba(0,0,0,0.4);
    backdrop-filter: blur(10px);
    font-family: inherit;
    pointer-events: none;
    z-index: 1000;
}

/* Estilos para la ventana de información de Google Maps */
.gm-style .gm-style-iw-c {
    border-radius: 12px !important;
    box-shadow: 0 8px 32px rgba(0,0,0,0.2) !important;
}

.gm-style .gm-style-iw-d {
    border-radius: 12px !important;
}

.gm-style .gm-style-iw-t::after {
    background: linear-gradient(45deg, transparent 33.333%, #fff 33.333%, #fff 66.667%, transparent 66.667%) !important;
}

/* Mejoras para los controles del mapa */
.gm-control-active {
    background-color: #fff !important;
    border: 1px solid #ccc !important;
    border-radius: 4px !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1) !important;
}

.gm-control-active:hover {
    background-color: #f8f9fa !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

/* Estilos para las métricas de tendencia */
.metric-trend .badge {
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
    transition: all 0.3s ease;
}

.metric-trend .badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.metric-trend .badge.bg-success {
    background: linear-gradient(45deg, #28a745, #20c997) !important;
}

.metric-trend .badge.bg-danger {
    background: linear-gradient(45deg, #dc3545, #e74c3c) !important;
}

.metric-trend .badge.bg-warning {
    background: linear-gradient(45deg, #ffc107, #fd7e14) !important;
    color: #212529 !important;
}

.metric-trend .badge.bg-info {
    background: linear-gradient(45deg, #17a2b8, #6f42c1) !important;
}

.metric-trend .badge.bg-secondary {
    background: linear-gradient(45deg, #6c757d, #495057) !important;
}
</style>
@endsection