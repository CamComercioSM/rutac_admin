@foreach ($reporte['reporte_grupos'] as $encabezado => $items)
    <div class="card mb-4">
        <div class="card-header bg-primary py-3">
            <h5 class="text-white mb-0">
                <i class="bx bx-collection me-2"></i> {{ $encabezado }}
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Fase</th>
                        <th>Actividad</th>
                        <th>Modalidad / Tipo</th>
                        <th>Descripción y Conclusiones</th>
                        <th>Resultado</th>
                        <th>Evidencias</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $intervencion)
                        @php
                            $totalAsistentes =
                                ($intervencion->participantes ?? 0) + ($intervencion->participantes_otros ?? 0);
                        @endphp
                        <tr>
                            <td><span class="badge bg-label-info">{{ $intervencion->fase->nombre ?? 'N/A' }}</span></td>
                            <td>{{ $intervencion->categoria->nombre ?? 'N/A' }}</td>
                            <td>
                                <small class="d-block text-muted">{{ $intervencion->modalidad }}</small>
                                <strong>{{ $intervencion->tipo->nombre ?? 'N/A' }}</strong>
                            </td>
                            <td style="white-space: normal; min-width: 200px;">
                                <div class="text-wrap">
                                    {{ $intervencion->descripcion }}
                                    <hr class="my-1">
                                    <small class="text-success italic">Conclusiones:
                                        {{ $intervencion->conclusiones }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <small>Unidades: {{ $intervencion->cant_unidades }}</small>
                                    <small>Leads: {{ $intervencion->cant_leads }}</small>
                                    <span class="badge bg-label-secondary mt-1">Total: {{ $totalAsistentes }}</span>
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
