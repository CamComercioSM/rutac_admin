@extends('layouts.layoutMaster')

<!-- Vendor Styles -->
@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss'
])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection

@section('content')
<div class="row">
    <div class="col-12 col-md-12">

        <div class="card mb-6">
            
            <h3 class="text-center text-primary my-3">
                <b>Detalles de la convocatoria</b>
            </h3>

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
                                    <span class="badge text-bg-info me-2">{{$item->name}} {{$item->lastname}}</span>
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
             
            <div class="d-flex">
                <a class="btn btn-success px-2" href="/convocatoriasRequisitos/export?tipo=1&convocatoria={{ $detalle->convocatoria_id }}" target="_blanck" >
                    <i class="icon-base ri ri-file-excel-2-line me-2"></i> Exportar
                </a>

                <h5 class="text-center mb-0 ms-2 pt-1">Requisitos - indicadores</h5>
            </div> 
            <table id="RequisitosIndicadores" class="table" >
                <thead>
                    <th>Nombre</th>
                    <th>Indicador</th>
                </thead>
                <tbody>
                    @foreach ($detalle->requisitosTodos->whereNotNull('indicador') as $item)
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
             
            <div class="d-flex">
                <a class="btn btn-success px-2" href="/convocatoriasRequisitos/export?tipo=2&convocatoria={{ $detalle->convocatoria_id }}" target="_blanck" >
                    <i class="icon-base ri ri-file-excel-2-line me-2"></i> Exportar
                </a>

                <h5 class="text-center mb-0 ms-2 pt-1">Requisitos</h5>
            </div> 
            <table id="Requisitos" class="table" >
                <thead>
                    <th>Nombre</th>
                </thead>
                <tbody>
                    @foreach ($detalle->requisitosTodos->whereNull('indicador') as $item)
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
        window.TABLAS = [
            {
                id: 'RequisitosIndicadores',
                setting: {
                    pagination: true,
                    search: true,
                    pageLength: 5,
                    lengthMenu: [5, 10, 20, 50, 100]
                }                
            },
            {
                id: 'Requisitos',
                setting: {
                    pagination: true,
                    search: true,
                    pageLength: 5,
                    lengthMenu: [5, 10, 20, 50, 100]
                }                
            }
        ];

        const cargando = document.querySelectorAll('.cargando')[0];
        cargando.classList.add('d-none');

    </script>
    @vite([ 'resources/assets/js/admin-table.js' ])
@endsection