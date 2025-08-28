@extends('layouts.admin', ['titulo'=> 'Programa'])

@section('content')
<div class="row">
    <div class="col-12 col-md-12">

        <div class="card mb-6 border border-2 border-primary rounded">
            
            <h3 class="text-center my-3">Detalles del programa</h3>

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
                                    <span class="badge bg-secondary me-2">{{$item->name}}</span>
                                @endforeach
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>


</div>

@endsection

@section('page-script')
    <script>
        const cargando = document.querySelectorAll('.cargando')[0];
        cargando.classList.add('d-none');
    </script>
@endsection