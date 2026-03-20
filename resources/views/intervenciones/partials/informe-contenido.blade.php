<div class="preview-header">
    <img src="https://cdnsicam.net/img/rutac/rutac-logo-con-ccsm.png" alt="Logo RUTAC">
    <h1>Informe de Intervenciones</h1>
    <small>Desde {{ $inicio }} hasta {{ $fin }}</small>
    <p class="text-muted">
        Reporte generado automáticamente con base en intervenciones registradas en el sistema RUTAC
    </p>
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
