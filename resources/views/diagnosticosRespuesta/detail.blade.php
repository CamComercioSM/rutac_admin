@extends('layouts.layoutMaster')

<!-- Vendor Styles -->
@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/apex-charts/apex-charts.scss'
])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('content')

<div class="row">
    <div class="col-12 col-md-5 mb-3">
        @include('_partials.unidad', ["unidad"=>$detalle->unidadProductiva])
    </div>
    <div class="col-12 col-md-7 mb-3">
        
        <div class="card mb-6 border border-2 border-primary rounded">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="badge bg-label-primary rounded-pill">{{$detalle->etapa->name ?? ' - '}}</span>
                    <div class="d-flex justify-content-center">
                        <sub class="h6 pricing-duration mt-auto mb-3 fw-normal">{{$detalle->fecha_creacion ?? ' - '}}</sub>
                    </div>
                </div>
                <ul class="list-unstyled g-2 mt-2">
                    <li class="mb-2 d-flex align-items-center">
                        <i class="icon-base ri ri-circle-fill icon-10px text-body me-2"></i>
                        <span>Puntaje: {{$detalle->resultado_puntaje ?? ' - '}}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card pt-2">
            <div id="chart"></div>
        </div>

    </div>
    <div class="col-12 col-md-12">

        <div class="card mb-6 p-3">

            <div class="d-flex" id="toolbarRespuestas">
                <a class="btn btn-success px-2" href="exportRespuestas?id={{ $detalle->resultado_id }}" target="_blanck" >
                    <i class="icon-base ri ri-file-excel-2-line"></i>
                </a>

                <h5 class="text-center mb-0 ms-2 pt-1">Listado de respuestas</h5>
            </div> 
            <table id="respuestasIncripcion" class="table" >
                <thead>
                    <th>Pregunta</th>
                    <th>Respuesta</th>
                    <th>Porcentaje</th>
                </thead>
                <tbody>
                    @foreach ($detalle->respuestas as $item)
                        <tr>
                            <td>{{$item->pregunta->pregunta_titulo ?? ' - '}}</td>
                            <td>{{$item->diagnosticorespuesta_valor ?? ' - '}}</td>
                            <td>{{$item->pregunta->pregunta_porcentaje ?? ' - '}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

    </div>
</div>

@endsection

@section('page-script')
<script>
    window.TABLAS = [
        {
            id: 'respuestasIncripcion',
            setting: {
                pagination: true,
                search: true,
                pageLength: 5,
                lengthMenu: [5, 10, 20, 50, 100]
            }                
        }
    ];

    // Normalizar datos del gráfico para evitar null/undefined
    const _results = (() => { try { return {!! $results !!} } catch(e){ return [] } })();
    const _dimensions = (() => { try { return {!! $dimensions !!} } catch(e){ return [] } })();

    const seriesData = Array.isArray(_results) ? _results.filter(v => v != null).map(Number) : [];
    const categories = Array.isArray(_dimensions) ? _dimensions.filter(v => v != null) : [];

    const options = {
        series: [{ data: seriesData, name: 'Puntaje' }],
        chart: { height: 380, type: 'radar', toolbar: { show: true } },
        title: { text: 'Diagnóstico' },
        stroke: { width: 2 },
        fill: { opacity: 0.3 },
        markers: { size: 4 },
        dataLabels: { enabled: false },
        yaxis: { tickAmount: 5, decimalsInFloat: 0 },
        xaxis: { 
            categories,
            labels: { show: true, style: { colors: Array(categories.length).fill('#6c757d'), fontSize: '12px' } }
        },
        plotOptions: { radar: { polygons: { strokeColors: '#e9ecef', fill: { colors: ['#f8f9fa', '#fff'] } } } }
    };

    const CHARTS = [ { id:'chart', options: options } ];

    document.querySelectorAll('.cargando').forEach(function(element) {
        element.classList.add('d-none');
    });


</script>

@vite(['resources/assets/js/admin-table.js', 'resources/assets/js/admin-chart.js'])

@endsection
