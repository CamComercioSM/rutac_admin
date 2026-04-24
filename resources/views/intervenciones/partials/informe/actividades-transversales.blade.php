<h2 class="mt-5">Actividades Transversales</h2>

<div class="card mb-4">
    {{-- Encabezado con el mismo estilo que Actividades Desarrolladas --}}
    <div class="card-header bg-warning py-3"> {{-- Usamos Warning para diferenciar gestión de programas --}}
        <h2 class="text-white mb-1">
            <i class="icon-base ri ri-refresh-line me-2"></i> Actividades Transversales y de Gestión
        </h2>
        <div class="d-flex gap-2">
            <span class="badge bg-warning">
                {{ $actividadesTransversales->count() }} Actividades
            </span>
            <span class="badge bg-white text-warning border">
                Inscritas (UND): {{ $actividadesTransversales->sum('cant_unidades') }}
                [{{ $actividadesTransversales->sum(fn($i) => $i->participantes ?? 0) }}]
            </span>
            <span class="badge bg-white text-warning border">
                Externas (EXT): {{ $actividadesTransversales->sum('cant_leads') }}
                [{{ $actividadesTransversales->sum(fn($i) => $i->participantes_otros ?? 0) }}]
            </span>
            <span class="badge bg-white text-warning border">
                Total Unidades:
                {{ $actividadesTransversales->sum('cant_unidades') + $actividadesTransversales->sum('cant_leads') }}
            </span>
            <span class="badge bg-white text-warning border">
                Total Asistentes:
                {{ $actividadesTransversales->sum(fn($i) => ($i->participantes ?? 0) + ($i->participantes_otros ?? 0)) }}
            </span>
        </div>
    </div>

    <div class="table-responsive">
        @forelse ($actividadesTransversales as $intervencion)
            @php
                $u_inscritas = $intervencion->cant_unidades ?? 0;
                $p_inscritas = $intervencion->participantes ?? 0;

                $u_externas = $intervencion->cant_leads ?? 0;
                $p_externos = $intervencion->participantes_otros ?? 0;

                $total_unidades = $u_inscritas + $u_externas;
                $total_asistentes = $p_inscritas + $p_externos;

                $inicio = $intervencion->fecha_inicio;
                $fin = $intervencion->fecha_fin;
                
                // Asegurar que sean objetos Carbon para evitar error format()
                if(is_string($inicio)) $inicio = \Carbon\Carbon::parse($inicio);
                if(is_string($fin)) $fin = \Carbon\Carbon::parse($fin);

                $diferencia = $inicio->diff($fin);
                $duracion = $diferencia->days > 0
                    ? $diferencia->format('%d d %h h')
                    : $diferencia->format('%h h %i min');
            @endphp

            <table class="table table-sm table-hover align-middle mt-0 mb-3">
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
                                    - {{ $fin->format('h:i A') }}
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
                                <span>
                                    <b>Modalidad:</b> 
                                    <i class="icon-base ri {{ $intervencion->modalidad == 'Virtual' ? 'ri-laptop-line' : 'ri-map-pin-line' }} me-1"></i>
                                    {{ $intervencion->modalidad }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-2 mb-3">
                                {{-- Inscritas --}}
                                <div class="p-2 border rounded bg-label-primary text-center" style="flex: 1;">
                                    <small class="d-block fw-bold" style="font-size: 0.6rem;">UNIDADES INSCRITAS</small>
                                    <div class="d-flex justify-content-around mt-1">
                                        <span><small>Und:</small> <strong>{{ $u_inscritas }}</strong></span>
                                        <span><small>Asist:</small> <strong>{{ $p_inscritas }}</strong></span>
                                    </div>
                                </div>
                                {{-- Externas --}}
                                <div class="p-2 border rounded bg-label-secondary text-center" style="flex: 1;">
                                    <small class="d-block fw-bold" style="font-size: 0.6rem;">UNIDADES EXTERNAS</small>
                                    <div class="d-flex justify-content-around mt-1">
                                        <span><small>Und:</small> <strong>{{ $u_externas }}</strong></span>
                                        <span><small>Asist:</small> <strong>{{ $p_externos }}</strong></span>
                                    </div>
                                </div>
                                {{-- Total Consolidado --}}
                                <div class="p-2 border rounded bg-dark text-white text-center" style="flex: 1;">
                                    <small class="d-block fw-bold" style="font-size: 0.6rem;">TOTAL CONSOLIDADO</small>
                                    <div class="d-flex justify-content-around mt-1">
                                        <span><small>Und:</small> <strong>{{ $total_unidades }}</strong></span>
                                        <span><small>Asist:</small> <strong>{{ $total_asistentes }}</strong></span>
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
                                    <div class="mt-2 text-muted small">
                                        <i class="ri-chat-check-line me-1"></i>
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
                            <span>Evidencia</span>
                        </th>
                        <td colspan="2" class="p-1 text-center">
                            @if ($intervencion->soporte)
                                <a href="{{ $intervencion->soporte }}" target="_blank" class="shadow-sm">
                                    <i class="icon-base ri ri-external-link-line"></i>
                                    {{ $intervencion->soporte }}
                                </a>
                            @else
                                <span class="text-muted small fst-italic">Sin adjunto</span>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        @empty
            <div class="text-center py-4">
                <p class="text-muted italic">No hay actividades transversales registradas.</p>
            </div>
        @endforelse
    </div>
</div>

<div class="page-break"></div>