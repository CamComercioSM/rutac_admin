@include('intervenciones.partials.informe.portada')

<div class="page-break"></div>

<div class="resumen-ejecutivo">
    <h2 style="border:none; margin-top:0;">1. Resumen Ejecutivo</h2>
    <p>
        Durante el periodo comprendido entre el <strong>{{ $inicio }}</strong> y el
        <strong>{{ $fin }}</strong>,
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

<div class="page-break"></div>
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
        <div class="card shadow-sm border-0" style="background: #0e188a; color: white;">
            <div class="card-body">
                <small>Total Actividades</small>
                <h3>{{ $totalGeneral }}</h3>
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



@include('intervenciones.partials.informe.tabla-por-programa')


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

{{-- 6. CONCLUSIONES INSTITUCIONALES --}}
<div class="conclusiones-section">
    <h2>Análisis de la Gestión y Resultados</h2>
    <div class="contenido-html">
        @if ($conclusiones)
            {!! $conclusiones !!}
        @else
            <p>Se recomienda mantener el seguimiento constante a las unidades intervenidas para asegurar la
                sostenibilidad de las acciones implementadas durante este periodo.</p>
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

<div class="footer-info">
    Intervenciones - Generado el {{ date('d/m/Y H:i') }}
</div>
