@extends('layouts.admin', ['titulo'=> 'Diagnóstico'])

@section('content')


<div class="row">
    <div class="col-12 col-md-5">
        
        @include('_partials.unidad', ["unidad"=>$detalle->unidadProductiva])

        <div class="card mb-6 border border-2 border-primary rounded">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="badge bg-label-primary rounded-pill">{{$detalle->etapa->name ?? ' - '}}</span>
                    <div class="d-flex justify-content-center">
                        <sub class="h6 pricing-duration mt-auto mb-3 fw-normal">{{$detalle->fecha_creacion ?? ' - '}}</sub>
                    </div>
                </div>
                <ul class="list-unstyled g-2 my-6">
                    <li class="mb-2 d-flex align-items-center">
                        <i class="icon-base ri ri-circle-fill icon-10px text-body me-2"></i>
                        <span>Puntaje: {{$detalle->resultado_puntaje ?? ' - '}}</span>
                    </li>
                </ul>
            </div>
        </div>

    </div>
    <div class="col-12 col-md-7">

        <div class="card mb-6 p-3">

            <div id="toolbarRespuestas">
                <button class="btn btn-info exportar" type="button" data-tabla="respuestasIncripcion" >
                    <i class="bi bi-cloud-download"></i> Exportar
                </button>
            </div> 

            <table id="respuestasIncripcion" class="table table-striped" >
                <thead>
                    <th>Pregunta</th>
                    <th>Respuesta</th>
                </thead>
                <tbody>
                    @foreach ($detalle->respuestas as $item)
                        <tr>
                            <td>{{$item->pregunta->pregunta_titulo ?? ' - '}}</td>
                            <td>{{$item->diagnosticorespuesta_valor ?? ' - '}}</td>
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
    </script>
    @vite([ 'resources/js/admin-table.js' ])
@endsection
