<h2>Actividades Desarrolladas</h2>

@foreach ($reporte_grupos as $encabezado => $fases)
    <div class="card mb-4">
        <div class="card-header bg-primary py-3">
            <h5 class="text-white mb-0">
                <i class="bx bx-collection me-2"></i> {{ $encabezado }}
            </h5>
            <div class="d-flex gap-2">
                <span class="badge bg-primary">
                    {{-- Usamos flatten() para contar todas las intervenciones de todas las fases --}}
                    {{ $fases->flatten()->count() }} Actividades Reportadas
                </span>
                <span class="badge bg-white text-primary border">
                    Unidades: {{ $fases->flatten()->sum('cant_unidades') }}
                </span>
                <span class="badge bg-white text-primary border">
                    Leads: {{ $fases->flatten()->sum('cant_leads') }}
                </span>
                <span class="badge bg-white text-primary border">
                    {{-- Aquí estaba el error. Al usar flatten(), $i vuelve a ser una Intervención individual --}}
                    Asistentes:
                    {{ $fases->flatten()->sum(fn($i) => ($i->participantes ?? 0) + ($i->participantes_otros ?? 0)) }}
                </span>
            </div>
        </div>


        @foreach ($fases as $faseNombre => $items)
            {{-- 🟦 Sub-encabezado de Fase (Estilo inspirado en la imagen) --}}
            <div class="bg-light">
                <div colspan="3" class="py-2 px-4">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-right-arrow-alt text-primary me-2"></i>
                        <span class="fw-bold text-dark uppercase small">FASE: {{ $faseNombre }}</span>
                        <span class="badge badge-dot bg-primary ms-2"></span>
                        <small class="text-muted ms-auto">{{ $items->count() }} registros en esta
                            fase</small>
                    </div>
                </div>
            </div>

            <div class="table-responsive">


                @foreach ($items as $intervencion)
                    @php
                        $totalAsistentes =
                            ($intervencion->participantes ?? 0) + ($intervencion->participantes_otros ?? 0);

                        // Cálculo de Duración
                        $inicio = $intervencion->fecha_inicio;
                        $fin = $intervencion->fecha_fin;
                        $diferencia = $inicio->diff($fin);
                        $duracion = $diferencia->format('%h h %i min');
                        if ($diferencia->days > 0) {
                            $duracion = $diferencia->format('%d d %h h');
                        }
                    @endphp
                    <table class="table table-hover" style="margin: 0px;">
                        <thead>
                            <tr>
                                <th width="12%" class="text-center">Inicio</th>
                                <th width="12%" class="text-center">Fin</th>
                                <th width="15%">Actividad / Tipo</th>
                                <th width="10%">Modalidad</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr>
                                {{-- Celda Inicio --}}
                                <td class="ps-4">
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-dark">{{ $inicio->format('d/m/Y') }}</span>
                                        <small class="text-primary fw-bold">{{ $inicio->format('h:i A') }}</small>
                                    </div>
                                </td>
                                {{-- Celda Fin --}}
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-dark">{{ $fin->format('d/m/Y') }}</span>
                                        <small class="text-muted">{{ $fin->format('h:i A') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold">{{ $intervencion->categoria->nombre ?? 'N/A' }}</span>
                                        <small class="text-muted">{{ $intervencion->tipo->nombre ?? 'N/A' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <small class="d-block text-muted">{{ $intervencion->modalidad }}</small>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <th width="35%" colspan="4">Descripción y Conclusiones</th>
                            </tr>
                            <tr class="border-bottom">
                                <td style="white-space: normal;" colspan="4">
                                    <div class="text-wrap" style="font-size: 0.85rem;">
                                        <p class="mb-1">{!! $intervencion->descripcion ?? '<span style="color:#666;">Sin descripción</span>' !!}</p>
                                        @if ($intervencion->conclusiones)
                                            <div class="mt-1 p-2 bg-label-success rounded">
                                                <i class="bx bx-comment-check me-1"></i>
                                                <small class="fst-italic">{!! $intervencion->conclusiones ?? '<span style="color:#666;">Sin conclusiones</span>' !!}</small>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <th width="40%">Resultados (U / L / T)</th>
                                <th width="60%" class="text-center" colspan="3">Evidencias</th>
                            </tr>
                            <tr class="border-bottom">
                                <td>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="badge bg-label-warning fw-bold">
                                            <i class="bi bi-time-five me-1"></i> Duración: {{ $duracion }}
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <div class="px-2 py-1 bg-light rounded shadow-xs" style="font-size: 0.75rem;">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Unidades:</span>
                                                <span class="fw-bold">{{ $intervencion->cant_unidades }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Leads:</span>
                                                <span class="fw-bold">{{ $intervencion->cant_leads }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between border-top mt-1 pt-1">
                                                <span class="text-dark fw-bold">Asistentes:</span>
                                                <span class="text-primary fw-bold">{{ $totalAsistentes }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center" colspan="3">
                                    @if ($intervencion->evidencia_url)
                                        <a href="{{ $intervencion->evidencia_url }}" target="_blank"
                                            class="btn btn-icon btn-sm btn-outline-primary">
                                            <i class="bx bx-file"></i> {{ $intervencion->evidencia_url }}
                                        </a>
                                    @else
                                        <span class="text-muted small">Sin archivo</span>
                                    @endif
                                </td>
                            </tr>

                        </tbody>
                    </table>
                    <hr />
                @endforeach
        @endforeach
    </div>
    </div>
@endforeach

<div class="page-break"></div>

<style>
    .font-xsmall {
        font-size: 0.7rem;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(105, 108, 255, 0.04);
    }

    .shadow-xs {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
    }
</style>
