<div class="preview-header">
    <img src="https://cdnsicam.net/img/rutac/rutac-logo-con-ccsm.png" alt="Logo RUTAC">
    <h1>Informe de Intervenciones</h1>
    <small>Desde {{ $inicio }} hasta {{ $fin }}</small>
    <p class="text-muted">
        Reporte generado automáticamente con base en intervenciones registradas en el sistema RUTAC
    </p>
</div>

{{-- 1. PORTADA --}}
<div class="portada">
    <img src="https://cdnsicam.net/img/rutac/rutac-logo-con-ccsm.png" style="max-height: 120px;">
    <h1>Informe de Gestión de Intervenciones</h1>
    <h3 class="text-muted">Ruta de Crecimiento (Ruta C)</h3>
    <div style="margin-top: 50px;">
        <p><strong>Periodo:</strong> {{ $inicio }} - {{ $fin }}</p>
        <p><strong>Generado por:</strong> Sistema Administrativo Ruta C</p>
        <p><strong>Fecha de emisión:</strong> {{ date('d/m/Y') }}</p>
    </div>
</div>

<div class="page-break"></div>

{{-- 2. RESUMEN EJECUTIVO Y ANÁLISIS AUTOMÁTICO --}}
<div class="resumen-ejecutivo">
    <h2 style="border:none; margin-top:0;">1. Resumen Ejecutivo</h2>
    <p>
        Durante el periodo comprendido entre el <strong>{{ $inicio }}</strong> y el <strong>{{ $fin }}</strong>, 
        se han ejecutado un total de <strong>{{ $totalGeneral }}</strong> intervenciones técnicas. 
        Este esfuerzo se ha distribuido en <strong>{{ count($porUnidad) }}</strong> unidades productivas únicas, 
        impactando significativamente en el fortalecimiento del ecosistema empresarial regional.
    </p>
    
    <div class="alert alert-info mt-3" style="background: #eef1f7; border-left: 5px solid #0e188a; padding: 15px;">
        <strong>Análisis de Cobertura:</strong> 
        @php
            $promedioIntervenciones = count($porUnidad) > 0 ? round($totalGeneral / count($porUnidad), 1) : 0;
        @endphp
        Se registra un promedio de <strong>{{ $promedioIntervenciones }}</strong> intervenciones por unidad productiva. 
        La categoría con mayor recurrencia es <strong>{{ $porCategoria->first()->categoria->nombre ?? 'N/A' }}</strong>, 
        lo que sugiere una tendencia de necesidad técnica en dicha área.
    </div>
</div>

{{-- 3. INDICADORES CONSOLIDADOS (Kpis) --}}
<div class="row text-center mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0" style="background: #0e188a; color: white;">
            <div class="card-body">
                <small>Total Actividades</small>
                <h3>{{ $totalGeneral }}</h3>
            </div>
        </div>
    </div>
    {{-- Repetir estructura para Unidades, Categorías y Transversales con diferentes colores --}}
</div>

{{-- 4. TABLAS DE DISTRIBUCIÓN (Categorías y Tipos) --}}
<div class="row">
    <div class="col-6">
        <h2>2. Distribución por Categoría</h2>
        {{-- Tabla de categorías aquí --}}
    </div>
</div>

<div class="page-break"></div>

{{-- 5. DETALLE TÉCNICO --}}
<h2>3. Listado Detallado de Gestión</h2>
<p class="text-muted small">Cronología de actividades y evidencias de soporte registradas.</p>
{{-- Mantener la tabla de intervencionesDetalladas pero con fuentes optimizadas --}}

{{-- 6. CONCLUSIONES INSTITUCIONALES --}}
<div class="conclusiones-section">
    <h2>4. Conclusiones y Recomendaciones</h2>
    <div class="contenido-html">
        @if($conclusiones)
            {!! $conclusiones !!}
        @else
            <p>Se recomienda mantener el seguimiento constante a las unidades intervenidas para asegurar la sostenibilidad de las acciones implementadas durante este periodo.</p>
        @endif
    </div>
</div>

{{-- 7. ANÁLISIS IA (Si aplica) --}}
@if (!empty($analisis_ia) && ($mostrarIA ?? false))
    <div class="conclusiones-section" style="border-left-color: #28a745;">
        <h2>5. Hallazgos Estratégicos (IA)</h2>
        <div>{!! $analisis_ia !!}</div>
    </div>
@endif

{{-- 8. FIRMAS --}}
<div class="firma-section">
    <div class="firma-box">
        <strong>Asesor Responsable</strong><br>
        Ruta de Crecimiento
    </div>
    <div class="firma-box">
        <strong>Coordinación</strong><br>
        CCSM / Ruta C
    </div>
</div>

<div class="row text-center mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Intervenciones</h6>
                <h3 class="text-primary">{{ $totalGeneral }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Unidades</h6>
                <h3 class="text-success">{{ count($porUnidad) }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Categorías</h6>
                <h3 class="text-dark">{{ count($porCategoria) }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- SECCIÓN 1: CATEGORÍAS -->
<h2>Categorías de Intervención</h2>
<table>
    <thead>
        <tr>
            <th>Categorías de Intervención</th>
            <th style="width: 100px">Cantidad</th>
        </tr>
    </thead>
    <tbody>
        @forelse($porCategoria as $c)
            <tr>
                <td>
                    <strong>{{ $c->categoria->nombre ?? 'Sin categoría' }}</strong>
                </td>
                <td class="text-right">{{ $c->total }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" style="text-align: center; color: #666;">No hay datos disponibles</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- SECCIÓN 2: TIPOS -->
<h2>Tipos de Intervención</h2>
<table>
    <thead>
        <tr>
            <th>Tipo</th>
            <th style="width: 100px">Cantidad</th>
        </tr>
    </thead>
    <tbody>
        @forelse($porTipo as $t)
            <tr>
                <td>
                    <strong>{{ $t->tipo->nombre ?? 'Sin tipo' }}</strong>
                </td>
                <td class="text-right">{{ $t->total }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" style="text-align: center; color: #666;">No hay datos disponibles</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- SECCIÓN 3: UNIDADES PRODUCTIVAS -->
<h2>Unidades Productivas</h2>
<table>
    <thead>
        <tr>
            <th>Unidad Productiva</th>
            <th style="width: 100px">Cantidad</th>
        </tr>
    </thead>
    <tbody>
        @forelse($porUnidad as $u)
            <tr>
                <td>
                    <strong>{{ $u->unidadProductiva?->business_name ?? 'Sin unidad productiva' }}</strong>
                </td>
                <td class="text-right">{{ $u->total }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" style="text-align: center; color: #666;">No hay datos disponibles</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="page-break" ></div> 


<!-- LISTADO DETALLADO -->
<h2>Listado Detallado de Intervenciones</h2>
@php
    $intervencionesDetalladas = collect($intervenciones)->filter(function ($i) {
        return !empty($i->unidadproductiva_id) || !empty($i->lead_id);
    });

    $actividadesTransversales = collect($intervenciones)->filter(function ($i) {
        return empty($i->unidadproductiva_id) && empty($i->lead_id);
    });
@endphp

<div class="row text-center mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Actividades</h6>
                <h3 class="text-primary">{{ $totalGeneral }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Con intervenido</h6>
                <h3 class="text-success">{{ $intervencionesDetalladas->count() }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Transversales</h6>
                <h3 class="text-dark">{{ $actividadesTransversales->count() }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Unidades</h6>
                <h3 class="text-info">{{ count($porUnidad) }}</h3>
            </div>
        </div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 100px;">Fecha</th>
            <th style="width: 260px;">Intervenido / Asesor</th>
            <th style="width: 120px;">Categoría / Tipo</th>
            <th>Descripción</th>
            <th style="width: 180px;">Evidencia</th>
        </tr>
    </thead>
    <tbody>
        @forelse($intervencionesDetalladas as $i)
            <tr>
                <td>{{ $i->fecha_inicio }}</td>
                <td>
                    <div>
                        <strong>
                            {{ $i->unidadProductiva?->business_name ?? ($i->lead?->name ?? 'N/A') }}
                        </strong>
                    </div>
                    <small style="color: #666;">
                        {{ $i->unidadProductiva?->business_name ? 'Unidad productiva' : 'Otro participante' }}
                    </small>

                    <div style="margin-top: 8px;">
                        <small style="color: #666;">
                            <strong>Asesor:</strong> {{ $i->asesor?->name ?? 'N/A' }}
                        </small>
                    </div>
                </td>
                <td>
                    <div><strong>{{ $i->categoria?->nombre ?? 'N/A' }}</strong></div>
                    <small style="color: #666;">{{ $i->tipo?->nombre ?? 'N/A' }}</small>
                </td>
                <td>{!! $i->descripcion ?? '<span style="color:#666;">Sin descripción</span>' !!}</td>
                <td class="text-break">
                    @if (!empty($i->soporte))
                        <a href="{{ $i->soporte }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            Ver soporte - <span style="font-size: 50%;">{{ $i->soporte }}</span>
                        </a>
                    @else
                        <span class="text-muted">Sin soporte</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #666;">
                    No hay intervenciones con unidad productiva u otro participante en el rango seleccionado
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<h2>Actividades Transversales</h2>
<table>
    <thead>
        <tr>
            <th style="width: 100px;">Fecha</th>
            <th style="width: 220px;">Categoría / Tipo</th>
            <th>Descripción</th>
            <th style="width: 160px;">Soporte</th>
        </tr>
    </thead>
    <tbody>
        @forelse($actividadesTransversales as $i)
            <tr>
                <td>
                    {{ $i->fecha_inicio }}
                </td>
                <td>
                    <div><strong>{{ $i->categoria?->nombre ?? 'N/A' }}</strong></div>
                    <small style="color: #666;">{{ $i->tipo?->nombre ?? 'N/A' }}</small>
                </td>
                <td>{!! $i->descripcion ?? '<span style="color:#666;">Sin descripción</span>' !!}</td>
                <td class="text-break">
                    @if (!empty($i->soporte))
                        <a href="{{ $i->soporte }}" target="_blank" title="Ver soporte">
                            <i class="fas fa-paperclip"></i> <span style="font-size: 50%;">{{ $i->soporte }}</span>
                        </a>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align: center; color: #666;">
                    No hay actividades transversales en el rango seleccionado
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="page-break"></div>

<div class="conclusiones-section">
    <h2>Conclusiones</h2>
    <div class="contenido-html">
        {!! $conclusiones ?: '<p>No se han ingresado conclusiones.</p>' !!}
    </div>
</div>

@if (!empty($analisis_ia) && ($mostrarIA ?? false))
    <div class="conclusiones-section" style="margin-top: 20px;">
        <h2>Análisis complementario (IA)</h2>
        <div>{!! $analisis_ia !!}</div>
    </div>
@endif

<div class="footer-info">
    Intervenciones - Generado el {{ date('d/m/Y H:i') }}
</div>
