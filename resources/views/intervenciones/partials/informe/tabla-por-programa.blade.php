<h2>Actividades Desarrolladas</h2>

@foreach ($reporte_grupos as $encabezado => $fases)
    <div class="card mb-4">
        <div class="card-header bg-primary py-3">

            @php
                // Obtenemos el primer registro del grupo para extraer la información de la convocatoria
                $primerRegistro = $fases->first()->first();
                $convocatoria = $primerRegistro?->convocatoria;
            @endphp

            <h2 class="text-white mb-1">
                <i class="icon-base ri ri-collection me-2"></i> {{ $encabezado }} 
                <small>[
                <i class="icon-base ri ri-calendar-event-line me-1"></i>
                @if ($convocatoria && $convocatoria->fecha_apertura_convocatoria)
                    {{ $convocatoria->fecha_apertura_convocatoria->format('d/m/Y') }}
                @else
                    <span class="text-muted">Sin fecha apertura</span>
                @endif

                <span class="mx-2 text-muted">-</span>

                @if ($convocatoria && $convocatoria->fecha_cierre_convocatoria)
                    {{ $convocatoria->fecha_cierre_convocatoria->format('d/m/Y') }}
                @else
                    <span class="text-muted">Sin fecha cierre</span>
                @endif
                ]</small>
            </h2>
            <div class="d-flex gap-2">
                <span class="badge bg-primary">
                    {{ $fases->flatten()->count() }} Actividades
                </span>
                <span class="badge bg-white text-primary border">
                    Inscritas (UND): {{ $fases->flatten()->sum('cant_unidades') }}
                    [{{ $fases->flatten()->sum(fn($i) => $i->participantes ?? 0) }}]
                </span>
                <span class="badge bg-white text-primary border">
                    Externas (EXT): {{ $fases->flatten()->sum('cant_leads') }}
                    [{{ $fases->flatten()->sum(fn($i) => $i->participantes_otros ?? 0) }}]
                </span>
                <span class="badge bg-white text-primary border">
                    Total Unidades:
                    {{ $fases->flatten()->sum('cant_unidades') + $fases->flatten()->sum('cant_leads') }}
                </span>
                <span class="badge bg-white text-primary border">
                    Total Asistentes:
                    {{ $fases->flatten()->sum(fn($i) => ($i->participantes ?? 0) + ($i->participantes_otros ?? 0)) }}
                </span>
            </div>
        </div>
        @foreach ($fases as $faseNombre => $items)
            <div class="bg-lighter ">
                <div colspan="3" class="py-2 px-4">
                    <div class="d-flex align-items-center">
                        <i class="icon-base ri ri-arrow-right-s-fill text-primary me-2"></i>
                        <span class="fw-bold text-dark uppercase small">FASE: {{ $faseNombre }}</span>
                        <span class="badge badge-dot bg-primary ms-2"></span>
                        <small class="text-muted ms-auto">{{ $items->count() }} registros en esta fase</small>
                    </div>
                </div>
            </div>
            <div class="table-responsive">

                @foreach ($items as $intervencion)
                    @php
                        $u_inscritas = $intervencion->cant_unidades ?? 0;
                        $p_inscritas = $intervencion->participantes ?? 0;

                        $u_externas = $intervencion->cant_leads ?? 0;
                        $p_externos = $intervencion->participantes_otros ?? 0;

                        $total_unidades = $u_inscritas + $u_externas;
                        $total_asistentes = $p_inscritas + $p_externos;

                        $inicio = $intervencion->fecha_inicio;
                        $fin = $intervencion->fecha_fin;
                        $diferencia = $inicio->diff($fin);
                        $duracion =
                            $diferencia->days > 0
                                ? $diferencia->format('%d d %h h')
                                : $diferencia->format('%h h %i min');
                    @endphp
                    <table class="table table-sm table-hover align-middle mt-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 15%;">Fecha y Duración</th>
                                <th style="width: 25%;">Actividad / Tarea</th>
                                <th style="width: 60%;">Impacto</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr class="border-top">
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">{{ $inicio->format('d/m/Y') }}</span>
                                        <small class="text-muted">
                                            <i class="bx bx-time-five size-xs"></i> {{ $inicio->format('h:i A') }}
                                            -
                                            {{ $fin->format('h:i A') }}
                                        </small>
                                        <span class="mt-1" style="width: fit-content;">
                                            {{ $duracion }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-uppercase fw-semibold">
                                            {{ $intervencion->categoria->nombre ?? 'N/A' }}
                                        </span>
                                        <small class="text-uppercase" style="font-size: 0.6rem;">
                                            {{ $intervencion->tipo->nombre ?? 'N/A' }}
                                        </small>
                                        <span class="">
                                            <b>Modalidad:</b> <i
                                                class="icon-base ri  ri-{{ $intervencion->modalidad == 'Virtual' ? 'ri-laptop' : 'ri-map-pin' }} me-1"></i>
                                            {{ $intervencion->modalidad }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    {{-- Desglose de Impacto Solicitado --}}
                                    <div class="d-flex gap-2 mb-3">
                                        {{-- Inscritas --}}
                                        <div class="p-2 border rounded bg-label-primary text-center" style="flex: 1;">
                                            <small class="d-block fw-bold" style="font-size: 0.6rem;">UNIDADES
                                                INSCRITAS</small>
                                            <div class="d-flex justify-content-around mt-1">
                                                <span><small>Und:</small>
                                                    <strong>{{ $u_inscritas }}</strong></span>
                                                <span><small>Asist:</small>
                                                    <strong>{{ $p_inscritas }}</strong></span>
                                            </div>
                                        </div>
                                        {{-- Externas --}}
                                        <div class="p-2 border rounded bg-label-secondary text-center" style="flex: 1;">
                                            <small class="d-block fw-bold" style="font-size: 0.6rem;">UNIDADES
                                                EXTERNAS</small>
                                            <div class="d-flex justify-content-around mt-1">
                                                <span><small>Und:</small>
                                                    <strong>{{ $u_externas }}</strong></span>
                                                <span><small>Asist:</small>
                                                    <strong>{{ $p_externos }}</strong></span>
                                            </div>
                                        </div>
                                        {{-- Total Consolidado --}}
                                        <div class="p-2 border rounded bg-dark text-white text-center" style="flex: 1;">
                                            <small class="d-block fw-bold" style="font-size: 0.6rem;">TOTAL
                                                CONSOLIDADO</small>
                                            <div class="d-flex justify-content-around mt-1">
                                                <span><small>Und:</small>
                                                    <strong>{{ $total_unidades }}</strong></span>
                                                <span><small>Asist:</small>
                                                    <strong>{{ $total_asistentes }}</strong></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3" class="pt-0 border-bottom-1">
                                    <div class="p-1">
                                        <div class="text-wrap" style="color: #566a7f;">
                                            <strong>Descripción:</strong> {!! $intervencion->descripcion ?? 'N/A' !!}
                                        </div>
                                        @if ($intervencion->conclusiones)
                                            <div class="mt-2 text-" style="">
                                                <i class="bx bx-check-double me-1"></i>
                                                <strong>Conclusión:</strong> {!! $intervencion->conclusiones !!}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th style="background: #eeeeee">
                                    <span class="">Evidencia</span>
                                </th>
                                <td colspan="2" class="p-1 text-center">
                                    @if ($intervencion->evidencia_url)
                                        <a href="{{ $intervencion->evidencia_url }}" target="_blank" class="shadow-sm"
                                            title="Ver Evidencia">
                                            <span class=" icon-base ri ri-external-link-line"></span>
                                            {{ $intervencion->evidencia_url }}
                                        </a>
                                    @else
                                        <span class="text-muted small fst-italic">Sin adjunto</span>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                @endforeach
            </div>
        @endforeach

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
