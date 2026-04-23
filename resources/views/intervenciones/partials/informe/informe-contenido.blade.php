@php
    $intervencionesDetalladas = collect($intervenciones)->filter(function ($i) {
        return !empty($i->unidadproductiva_id) || !empty($i->lead_id);
    });

    $actividadesTransversales = collect($intervenciones)->filter(function ($i) {
        return empty($i->unidadproductiva_id) && empty($i->lead_id);
    });
@endphp


@include('intervenciones.partials.informe.portada')

@include('intervenciones.partials.informe.resumen-ejecutivo')

@include('intervenciones.partials.informe.tabla-por-programa')

@include('intervenciones.partials.informe.actividades-transversales')

@include('intervenciones.partials.informe.conclusiones')

@include('intervenciones.partials.informe.trazabilidad')

@include('intervenciones.partials.informe.estadisticas-globales')
