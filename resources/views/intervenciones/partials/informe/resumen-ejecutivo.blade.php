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
