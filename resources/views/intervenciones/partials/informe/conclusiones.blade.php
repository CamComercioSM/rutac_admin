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


<div class="page-break"></div>
