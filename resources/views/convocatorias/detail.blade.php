@extends('layouts.admin', ['titulo'=> 'Convocatoria'])

@section('content')
<div class="row">
    <div class="col-12 col-md-12">

        <div class="card mb-6 border border-2 border-primary rounded">
            
            <h3 class="text-center my-3">Detalles de la convocatoria</h3>

            <div class="card-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <th>Programa</th>
                            <td>
                                <a href="/programas/{{$detalle->programa_id}}"> {{$detalle->programa->nombre ?? ' - '}} </a> 
                            </td>
                        </tr>
                        <tr>
                            <th>Convocatoria</th>
                            <td>{{$detalle->nombre_convocatoria ?? ' - '}}</td>
                        </tr>
                        <tr>
                            <th>Persona a cargo</th>
                            <td>{{$detalle->persona_encargada ?? ' - '}}</td>
                        </tr>
                        <tr>
                            <th>Correo de contacto</th>
                            <td>{{$detalle->correo_contacto ?? ' - '}}</td>
                        </tr>
                        <tr>
                            <th>Teléfono de contacto</th>
                            <td>{{$detalle->telefono ?? ' - '}}</td>
                        </tr>
                        <tr>
                            <th>Fecha de inicio</th>
                            <td>{{$detalle->fecha_apertura_convocatoria ?? ' - '}}</td>
                        </tr>
                        <tr>
                            <th>Fecha de finalización</th>
                            <td>{{$detalle->fecha_cierre_convocatoria ?? ' - '}}</td>
                        </tr>
                        <tr>
                            <th>Sector</th>
                            <td>{{$detalle->sector->sectorNOMBRE ?? ' - '}}</td>
                        </tr>
                        <tr>
                            <th>Con matricula</th>
                            <td>{{$detalle->con_matricula ? ' SI ' : ' NO '}}</td>
                        </tr>
                        <tr>
                            <th>Asesores</th>
                            <td>
                                @foreach ($detalle->asesores as $item)
                                    <span class="badge bg-secondary me-2">{{$item->name}} {{$item->lastname}}</span>
                                @endforeach
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>

    <div class="col-12 col-md-12">
         <div class="card mb-6 p-3">
             
            <div class="d-flex" id="toolbarRequisitosIndicadores">
                <button class="btn btn-success exportar px-2" type="button" data-tabla="RequisitosIndicadores">
                    <i class="ri-file-excel-2-line"></i>
                </button>

                <h5 class="text-center mb-0 ms-2 pt-1">Requisitos - indicadores</h5>
            </div> 
            <table id="RequisitosIndicadores" class="table table-striped" >
                <thead>
                    <th>Nombre</th>
                    <th>Indicador</th>
                </thead>
                <tbody>
                    @foreach ($detalle->requisitosIndicadores as $item)
                        <tr>
                            <td>{{$item->requisito_titulo ?? ' - '}}</td>
                            <td>{{$item->indicador->indicador_nombre ?? ' - '}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

    <div class="col-12 col-md-12">
         <div class="card mb-6 p-3">
             
            <div class="d-flex" id="toolbarRequisitos">
                <button class="btn btn-success exportar px-2" type="button" data-tabla="Requisitos">
                    <i class="ri-file-excel-2-line"></i>
                </button>

                <h5 class="text-center mb-0 ms-2 pt-1">Requisitos</h5>
            </div> 
            <table id="Requisitos" class="table table-striped" >
                <thead>
                    <th>Nombre</th>
                </thead>
                <tbody>
                    @foreach ($detalle->requisitos as $item)
                        <tr>
                            <td>{{$item->requisito_titulo ?? ' - '}}</td>
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
                id: 'RequisitosIndicadores',
                setting: {
                    toolbar: '#toolbarRequisitosIndicadores',
                    locale: 'es-ES',
                    pagination: true,
                    search: true,
                    pageSize: 5,
                    pageList: [5, 10, 20, 50, 100]
                }                
            },
            {
                id: 'Requisitos',
                setting: {
                    toolbar: '#toolbarRequisitos',
                    locale: 'es-ES',
                    pagination: true,
                    search: true,
                    pageSize: 5,
                    pageList: [5, 10, 20, 50, 100]
                }                
            }
        ];

        const cargando = document.querySelectorAll('.cargando')[0];
        cargando.classList.add('d-none');

    </script>
    @vite([ 'resources/js/admin-table.js' ])
@endsection