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
                <b>Detalles del programa</b>
            </h3>

            <div class="card-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <th class="w-25" >Nombre</th>
                            <td>{{$detalle->nombre ?? ' - '}}</td>
                        </tr>
                        <tr>
                            <th class="w-25" >Duración</th>
                            <td>{{$detalle->duracion ?? ' - '}}</td>
                        </tr>
                        <tr>
                            <th class="w-25" >Descripción</th>
                            <td>
                                <details> 
                                    <summary>Mostrar contenido</summary> 
                                    {!!$detalle->descripcion!!} 
                                </details>
                            </td>                                
                        </tr>
                        <tr>
                            <th class="w-25" >Beneficios</th>
                            <td>
                                <details> 
                                    <summary>Mostrar contenido</summary> 
                                    {!!$detalle->beneficios!!} 
                                </details>
                            </td>                                
                        </tr>
                        <tr>
                            <th class="w-25" >Requisitos</th>
                            <td>
                                <details> 
                                    <summary>Mostrar contenido</summary> 
                                    {!!$detalle->requisitos!!} 
                                </details>
                            </td>                                
                        </tr>
                        <tr>
                            <th class="w-25" >Dirigido A</th>
                            <td>
                                <details> 
                                    <summary>Mostrar contenido</summary> 
                                    {!!$detalle->dirigido_a!!} 
                                </details>
                            </td>                                
                        </tr>
                        <tr>
                            <th class="w-25" >Objetivo</th>
                            <td>
                                <details> 
                                    <summary>Mostrar contenido</summary> 
                                    {!!$detalle->objetivo!!} 
                                </details>
                            </td>                                
                        </tr>
                        <tr>
                            <th class="w-25" >Determinantes</th>
                            <td>
                                <details> 
                                    <summary>Mostrar contenido</summary> 
                                    {!!$detalle->determinantes!!} 
                                </details>
                            </td>                                
                        </tr>
                        <tr>
                            <th class="w-25" >Aporte</th>
                            <td>
                                <details> 
                                    <summary>Mostrar contenido</summary> 
                                    {!!$detalle->informacion_adicional!!} 
                                </details>
                            </td>                                
                        </tr>
                        <tr>
                            <th class="w-25" >Herramientas Requeridas</th>
                            <td>
                                <details> 
                                    <summary>Mostrar contenido</summary> 
                                    {!!$detalle->herramientas_requeridas!!} 
                                </details>
                            </td>                                
                        </tr>
                         <tr>
                            <th class="w-25" >Modalidad</th>
                            <td>{{$detalle->es_virtual_text[$detalle->es_virtual] ?? ' - '}}</td>
                        </tr>
                         <tr>
                            <th class="w-25" >Sitio Web</th>
                            <td>{{$detalle->sitio_web ?? ' - '}}</td>
                        </tr>
                       
                        <tr>
                            <th class="w-25" >Etapas</th>
                            <td>
                                @foreach ($detalle->etapas as $item)
                                    <span class="badge text-bg-info me-2">{{$item->name}}</span>
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
                <a class="btn btn-success px-2" href="/convocatoriasRequisitos/export?tipo=1&programa={{ $detalle->programa_id }}" target="_blanck" >
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
                <a class="btn btn-success px-2" href="/convocatoriasRequisitos/export?tipo=2&programa={{ $detalle->programa_id }}" target="_blanck" >
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