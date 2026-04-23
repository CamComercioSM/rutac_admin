<h2>Actividades Desarrolladas</h2>

@foreach ($reporte_grupos as $encabezado => $items)
    <div class="card mb-4">
        <div class="card-header bg-primary py-3">
            <h5 class="text-white mb-0">
                <i class="bx bx-collection me-2"></i> {{ $encabezado }}
            </h5>


            <div class="d-flex gap-2">
                <span class="badge bg-primary">
                    {{ $items->count() }} Actividades Reportadas
                </span>
                <span class="badge bg-white text-primary border">
                    Unidades: {{ $items->sum('cant_unidades') }}
                </span>
                <span class="badge bg-white text-primary border">
                    Leads: {{ $items->sum('cant_leads') }}
                </span>
                <span class="badge bg-white text-primary border">
                    Asistentes: {{ $items->sum(fn($i) => ($i->participantes ?? 0) + ($i->participantes_otros ?? 0)) }}
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" style="margin: 0px;">
                <thead>
                    <tr>
                        <th width="10%" class="text-center">Fecha / Hora</th>
                        <th width="10%">Fase</th>
                        <th width="10%">Actividad</th>
                        <th width="10%">Modalidad</th>
                        <th width="10%">Tipo</th>
                        <th width="30%">Descripción y Conclusiones</th>
                        <th width="10%">Resultado</th>
                        <th width="10%">Evidencias</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $intervencion)
                        @php
                            $totalAsistentes =
                                ($intervencion->participantes ?? 0) + ($intervencion->participantes_otros ?? 0);
                        @endphp
                        <tr>
                            <td class="text-center">
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold text-primary">
                                        {{ $intervencion->fecha_inicio->format('d/m/Y') }}
                                    </span>
                                    <small class="text-muted">
                                        {{ $intervencion->fecha_inicio->format('h:i A') }}
                                    </small>
                                </div>
                            </td>
                            <td><span class="">{{ $intervencion->fase->nombre ?? 'N/A' }}</span></td>
                            <td>{{ $intervencion->categoria->nombre ?? 'N/A' }}</td>
                            <td>
                                <small class="d-block text-muted">{{ $intervencion->modalidad }}</small>
                            </td>
                            <td>
                                <strong>{{ $intervencion->tipo->nombre ?? 'N/A' }}</strong>
                            </td>
                            <td style="white-space: normal; min-width: 200px;">
                                <div class="text-wrap">
                                    {{ $intervencion->descripcion }}
                                    <hr class="my-1">
                                    <small class="text-success italic">{{ $intervencion->conclusiones }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <small>Unidades: {{ $intervencion->cant_unidades }}</small>
                                    <small>Leads: {{ $intervencion->cant_leads }}</small>
                                    <span class="">Total: {{ $totalAsistentes }}</span>
                                </div>
                            </td>
                            <td>
                                @if ($intervencion->evidencia_url)
                                    <a href="{{ $intervencion->evidencia_url }}" target="_blank"
                                        class="btn btn-icon btn-sm btn-outline-primary">
                                        <i class="bx bx-file"></i>
                                    </a>
                                @else
                                    <span class="text-muted small">Sin archivo</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach

<div class="page-break"></div>
