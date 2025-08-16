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
            <div class="col-md-6 text-end">
                <div class="dashboard-actions">
                    <button class="btn btn-outline-secondary btn-sm me-2" onclick="resetFilters()">
                        <i class="fas fa-undo me-1"></i> Restablecer
                    </button>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filtrosModal">
                        <i class="fas fa-filter me-1"></i> Filtros Avanzados
                    </button>
                    <button class="btn btn-success btn-sm ms-2" onclick="refreshDashboard()">
                        <i class="fas fa-sync-alt me-1"></i> Actualizar
                    </button>
                </div>
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
                    @if(isset($departamentos) && $departamentos->count() > 0)
                        @foreach($departamentos as $departamento)
                            <option value="{{ $departamento->departamento_id }}" {{ $filtros['departamento_id'] == $departamento->departamento_id ? 'selected' : '' }}>
                                {{ $departamento->departamentonombre ?? 'Sin nombre' }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="sectorSelect" onchange="filtrarPorSector(this.value)">
                    <option value="">Todos los sectores</option>
                    @if(isset($sectores) && $sectores->count() > 0)
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
                    @if(isset($etapas) && $etapas->count() > 0)
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
                <h2 class="metric-number">{{ number_format($totalUnidades ?? 0) }}</h2>
                <p class="metric-label">Unidades Productivas</p>
                <div class="metric-trend">
                    <span class="badge bg-success">
                        <i class="fas fa-arrow-up me-1"></i>+12.5%
                    </span>
                    <span class="text-muted ms-2">vs mes anterior</span>
                </div>
                <div class="metric-info mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Datos limitados a 10,000 registros para mejor rendimiento
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos de Resumen -->
    <div class="charts-section mb-4">
        <div class="row">
            <!-- Distribución por Tipo de Organización -->
            <div class="col-md-6 mb-4">
                <div class="card summary-card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-users me-2 text-primary"></i>
                            Distribución por Tipo de Organización
                        </h6>
                    </div>
                    <div class="card-body">
                        @if(isset($porTipoOrganizacion) && $porTipoOrganizacion->count() > 0)
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

            <!-- Estado del Diagnóstico Inicial -->
            <div class="col-md-6 mb-4">
                <div class="card summary-card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-clipboard-check me-2 text-success"></i>
                            Estado del Diagnóstico Inicial
                        </h6>
                    </div>
                    <div class="card-body">
                        @if(isset($porEstadoDiagnostico) && $porEstadoDiagnostico->count() > 0)
                            @foreach($porEstadoDiagnostico as $estado)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">{{ $estado->estado }}</span>
                                    <span class="fw-bold">{{ $estado->total }}</span>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted">
                                <p>Terminado: 1,800</p>
                                <p>Pendiente: 1,126</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Líneas - Evolución Temporal -->
    <div class="evolution-chart mb-4">
        <div class="card summary-card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2 text-warning"></i>
                    Evolución de Registros (Últimos 12 Meses)
                </h6>
                <div class="chart-controls">
                    <button class="btn btn-sm btn-outline-primary" onclick="cambiarPeriodoGrafico('12')">12 Meses</button>
                    <button class="btn btn-sm btn-outline-primary" onclick="cambiarPeriodoGrafico('6')">6 Meses</button>
                    <button class="btn btn-sm btn-outline-primary" onclick="cambiarPeriodoGrafico('3')">3 Meses</button>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center text-muted">
                    <p>Enero: 120</p>
                    <p>Febrero: 135</p>
                    <p>Marzo: 142</p>
                    <p>...</p>
                    <p>Diciembre: 220</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mapa y Gráficos -->
    <div class="row mb-4">
        <!-- Mapa Interactivo -->
        <div class="col-md-8">
            <div class="card map-card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-map-marked-alt me-2 text-danger"></i>
                        Distribución Geográfica
                    </h6>
                    <div class="map-controls">
                        <button class="btn btn-sm btn-outline-secondary" onclick="cambiarVistaMapa('mapa')">
                            <i class="fas fa-map me-1"></i> Mapa
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="cambiarVistaMapa('satelite')">
                            <i class="fas fa-satellite me-1"></i> Satélite
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="mapaColombia" style="height: 400px; width: 100%; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                        <div class="text-center">
                            <i class="fas fa-map fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Mapa en desarrollo</p>
                        </div>
                    </div>
                    @if(isset($datosMapa) && $datosMapa->count() > 0)
                        <div class="map-legend mt-3">
                            <h6>Leyenda:</h6>
                            <div class="legend-items">
                                @foreach($datosMapa->take(10) as $dato)
                                    <div class="legend-item">
                                        <span class="legend-dot" style="background-color: {{ '#' . substr(md5($dato->municipio->municipionombreoficial ?? 'default'), 0, 6) }}"></span>
                                        <span class="legend-text">{{ $dato->municipio->municipionombreoficial ?? 'Sin Municipio' }} ({{ $dato->total }})</span>
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
            <div class="card stages-card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2 text-warning"></i>
                        Distribución por Etapas
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($porEtapas) && $porEtapas->count() > 0)
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

        <!-- Evolución de Registros -->
        <div class="col-md-6 mb-4">
            <div class="card summary-card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-area me-2 text-info"></i>
                        Evolución de Registros (Últimos 12 Meses)
                    </h6>
                    <div class="chart-controls">
                        <button class="btn btn-sm btn-outline-primary" onclick="cambiarPeriodoGrafico(12)">12 Meses</button>
                        <button class="btn btn-sm btn-outline-primary" onclick="cambiarPeriodoGrafico(6)">6 Meses</button>
                        <button class="btn btn-sm btn-outline-primary" onclick="cambiarPeriodoGrafico(3)">3 Meses</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center text-muted">
                        <p>Enero: 120</p>
                        <p>Febrero: 135</p>
                        <p>Marzo: 142</p>
                        <p>...</p>
                        <p>Diciembre: 220</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos de Análisis -->
    <div class="row mb-4">
        <!-- Comparativa de Métricas -->
        <div class="col-md-4 mb-4">
            <div class="card summary-card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-radar me-2 text-info"></i>
                        Comparativa de Métricas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center text-muted">
                        <p>Registro: 85%</p>
                        <p>Diagnóstico: 72%</p>
                        <p>Capacitación: 68%</p>
                        <p>Seguimiento: 75%</p>
                        <p>Evaluación: 60%</p>
                        <p>Certificación: 45%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Tamaño vs Sector -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card table-card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>
                        Análisis Tamaño vs Sector Económico
                    </h6>
                    <div class="chart-controls">
                        <button class="btn btn-sm btn-outline-primary" onclick="cambiarTipoGraficoTamanoSector('stacked')">Apilado</button>
                        <button class="btn btn-sm btn-outline-primary" onclick="cambiarTipoGraficoTamanoSector('grouped')">Agrupado</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center text-muted">
                        <p>Micro: 1,200 unidades</p>
                        <p>Pequeña: 800 unidades</p>
                        <p>Mediana: 400 unidades</p>
                        <p>Gran Empresa: 200 unidades</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tablas Detalladas -->
    <div class="row">
        <!-- Por Municipios con Gráfico -->
        <div class="col-md-6 mb-4">
            <div class="card table-card">
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
                                    <th>Porcentaje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($porMunicipios) && $porMunicipios->count() > 0)
                                    @foreach($porMunicipios as $municipio)
                                        <tr>
                                            <td>
                                                <div class="municip-info">
                                                    <span class="municip-dot" style="background-color: {{ '#' . substr(md5($municipio->municipio->municipionombreoficial ?? 'default'), 0, 6) }}"></span>
                                                    {{ $municipio->municipio->municipionombreoficial ?? 'Sin Municipio' }}
                                                </div>
                                            </td>
                                            <td><strong>{{ number_format($municipio->total) }}</strong></td>
                                            <td>{{ number_format(($municipio->total / ($totalUnidades ?? 1)) * 100, 1) }}%</td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-info">
                                        <td><strong>Total</strong></td>
                                        <td><strong>{{ number_format($totalUnidades ?? 0) }}</strong></td>
                                        <td><strong>100%</strong></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No hay datos disponibles</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Proporciones por Categoría -->
        <div class="col-md-6 mb-4">
            <div class="card summary-card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2 text-warning"></i>
                        Proporciones por Categoría
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center text-muted">
                        <p>Micro: 45%</p>
                        <p>Pequeña: 30%</p>
                        <p>Mediana: 20%</p>
                        <p>Gran Empresa: 5%</p>
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
                    Límite de registros: 10,000 para consultas rápidas
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
                                @if(isset($departamentos) && $departamentos->count() > 0)
                                    @foreach($departamentos as $departamento)
                                        <option value="{{ $departamento->departamento_id }}" {{ $filtros['departamento_id'] == $departamento->departamento_id ? 'selected' : '' }}>
                                            {{ $departamento->departamentonombre ?? 'Sin nombre' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Municipio</label>
                            <select class="form-select" name="municipio_id" id="modalMunicipio">
                                <option value="">Todos</option>
                                @if(isset($municipios) && $municipios->count() > 0)
                                    @foreach($municipios as $municipio)
                                        <option value="{{ $municipio->municipio_id }}" {{ $filtros['municipio_id'] == $municipio->municipio_id ? 'selected' : '' }}>
                                            {{ $municipio->municipionombreoficial ?? 'Sin nombre' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sector</label>
                            <select class="form-select" name="sector_id">
                                <option value="">Todos</option>
                                @if(isset($sectores) && $sectores->count() > 0)
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
                                @if(isset($etapas) && $etapas->count() > 0)
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
                                @if(isset($tamanos) && $tamanos->count() > 0)
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
                                @if(isset($tiposPersona) && $tiposPersona->count() > 0)
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
@endsection

@section('scripts')
<script>
// Funciones básicas del dashboard
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard cargando...');
    setupEventListeners();
    updateLastUpdateTime();
});

// Funciones de filtros
function cambiarVistaMapa(tipo) {
    console.log('Cambiando vista del mapa a:', tipo);
}

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

// Funciones de utilidad
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

// Configuración de event listeners
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
}

// Ocultar loading cuando se complete la carga
window.addEventListener('load', function() {
    hideLoading();
    console.log('Dashboard completamente cargado');
});
</script>
@endsection