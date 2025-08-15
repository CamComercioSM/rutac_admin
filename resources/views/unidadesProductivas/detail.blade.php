@extends('layouts.admin', ['titulo'=> 'Diagnóstico'])

@section('content')

<div class="row">
    <div class="col-12 col-md-5">
        
        @include('_partials.unidad', ["unidad"=>$detalle])

        <div class="card pt-2">
            <div id="chart"></div>
        </div>

    </div>
    <div class="col-12 col-md-7">

        <div class="card mb-6 p-3">

            <h5 class="text-center">Diagnósticos</h5>

            <div id="toolbarDiagnosticos">
                <button class="btn btn-info exportar" type="button" data-tabla="diagnosticos" >
                    <i class="bi bi-cloud-download"></i> Exportar
                </button>
            </div> 
            <table id="diagnosticos" class="table table-striped" >
                <thead>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Etapa</th>
                    <th>Puntaje</th>
                </thead>
                <tbody>
                    @foreach ($detalle->diagnosticos as $item)
                        <tr>
                            <td>{{$item->resultado_id ?? ' - '}}</td>
                            <td>{{$item->fecha_creacion ?? ' - '}}</td>
                            <td>{{$item->etapa->name ?? ' - '}}</td>
                            <td>{{$item->resultado_puntaje ?? ' - '}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

        <div class="card mb-6 p-3">

            <h5 class="text-center">Inscripciones</h5>

            <div id="toolbarInscripciones">
                <button class="btn btn-info exportar" type="button" data-tabla="inscripciones" >
                    <i class="bi bi-cloud-download"></i> Exportar
                </button>
            </div> 
            <table id="inscripciones" class="table table-striped" >
                <thead>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Convocatoria</th>
                    <th>Estado</th>
                </thead>
                <tbody>
                    @foreach ($detalle->inscripciones as $item)
                        <tr>
                            <td>{{$item->inscripcion_id ?? ' - '}}</td>
                            <td>{{$item->fecha_creacion ?? ' - '}}</td>
                            <td>{{$item->convocatoria->nombre_convocatoria ?? ' - '}}</td>
                            <td>{{$item->estado->inscripcionEstadoNOMBRE ?? ' - '}}</td>
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
    const TABLAS = [
        {
            id: 'diagnosticos',
            setting: {
                toolbar: '#toolbarDiagnosticos',
                locale: 'es-ES',
                pagination: true,
                search: true,
                pageSize: 5,
                pageList: [5, 10, 20, 50, 100],
                onDblClickRow: function (row, $element, field) {
                    window.location.href = '/diagnosticosResultados/'+ row[0];
                }
            }                
        },
        {
            id: 'inscripciones',
            setting: {
                toolbar: '#toolbarInscripciones',
                locale: 'es-ES',
                pagination: true,
                search: true,
                pageSize: 5,
                pageList: [5, 10, 20, 50, 100],
                onDblClickRow: function (row, $element, field) {
                    window.location.href = '/inscriptions/'+ row[0];
                }
            }                
        }
    ];

    const options = {
        series: [{ data: {{$results}} }],
        chart: { height: 350, type: 'radar' },
        title: { text: 'Último diagnóstico' },
        yaxis: { stepSize: 20 },
        xaxis: { categories: {!! $dimensions !!} }
    };

    const CHARTS = [ { id:'chart', options: options } ];

    document.querySelectorAll('.cargando').forEach(function(element) {
        element.classList.add('d-none');
    });

</script>
@vite([ 
    'resources/assets/vendor/libs/apex-charts/apexcharts.js',
    'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
    'resources/js/admin-table.js', 
    'resources/js/admin-chart.js' 
])
@endsection
