@extends('layouts.admin', ['titulo'=> 'Diagnóstico'])

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
                <button class="btn btn-success exportar px-2" type="button" data-tabla="respuestasIncripcion">
                    <i class="ri-file-excel-2-line"></i>
                </button>

                <h5 class="text-center mb-0 ms-2 pt-1">Respuestas</h5>
            </div> 
            <table id="respuestasIncripcion" class="table table-striped" >
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
    const TABLAS = [
        {
            id: 'respuestasIncripcion',
            setting: {
                toolbar: '#toolbarRespuestas',
                locale: 'es-ES',
                pagination: true,
                search: true,
                pageSize: 5,
                pageList: [5, 10, 20, 50, 100]
            }                
        }
    ];

    const options = {
        series: [{ data: {{$results}} }],
        chart: { height: 350, type: 'radar' },
        title: { text: 'Diagnóstico' },
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
