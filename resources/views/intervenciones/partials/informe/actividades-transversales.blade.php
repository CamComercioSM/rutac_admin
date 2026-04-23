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
