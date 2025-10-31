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
    <div class="col-12 col-md-5 mb-6">
        
        @include('_partials.unidad', [ 
            "unidad"=> $detalle, 
            "verMasDetalles"=> false, 
            "editar"=> !$esAsesor, 
            "transformar"=> !$esAsesor 
        ])

        <div class="card mb-6 p-3">

            <div class="mb-4">
                <h5 class="mb-0 ms-2 pt-1"> 
                    <b>Datos del transformación</b> 
                </h5>
            </div> 
            <table class="table table-sm mb-3" >
                <tbody>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Etapa</th>
                        <td> {{ $detalle->etapa_intervencion ?? ' - ' }} </td>                            
                    </tr>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Fecha</th>
                        <td> {{ $detalle->transformada_fecha ?? ' - ' }} </td>                            
                    </tr>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Desde</th>
                        <td> 
                            @if ($detalle->transformadaDesde)
                                <a class="btn btn-xs btn-outline-info" href="{{ $detalle->transformadaDesde->unidadproductiva_id }}">
                                    {{ $detalle->transformadaDesde->business_name }}
                                </a>
                            @else
                                -
                            @endif
                        </td>                            
                    </tr>
                    <tr>
                        <th class="text-primary" style="width: 50px;">En</th>
                        <td> 
                            @if ($detalle->transformadaEn)
                                <a class="btn btn-xs btn-outline-info" href="{{ $detalle->transformadaEn->unidadproductiva_id }}">
                                    {{ $detalle->transformadaEn->business_name }}
                                </a>
                            @else
                                -
                            @endif
                        </td>                            
                    </tr>
                    
                </tbody>
            </table>

        </div>

        <div class="card pt-2">
            <div id="chart"></div>
        </div>

    </div>
    <div class="col-12 col-md-7 mb-6">

        <div class="card mb-6 p-3">

            <div class="d-flex mb-4">
                <h5 class="text-center mb-0 ms-2 pt-1"> <b>Información complementaria</b> </h5>
            </div> 
            <table class="table table-sm mb-3" >
                <tbody>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Representante legal</th>
                        <td> {{ $detalle->name_legal_representative ?? ' - ' }} </td>                            
                    </tr>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Ubicación</th>
                        <td> {{$detalle->municipio->municipioNOMBREOFICIAL ?? ' - '}}, {{$detalle->departamento->departamentoNOMBRE ?? ' - '}} </td>                            
                    </tr>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Dirección</th>
                        <td> {{ $detalle->address ?? ' - ' }} </td>                            
                    </tr>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Email</th>
                        <td> {{ $detalle->registration_email ?? ' - ' }} </td>                         
                    </tr>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Teléfono</th>
                        <td> {{ $detalle->telephone ?? ' - ' }} </td>                         
                    </tr>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Celular</th>
                        <td> {{ $detalle->mobile ?? ' - ' }} </td>                         
                    </tr>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Sitio web</th>
                        <td>
                            @if ($detalle->website )
                                @php
                                    $websiteUrl = $detalle->website;
                                    if (!preg_match('#^https?://#', $websiteUrl)) {
                                        $websiteUrl = 'https://' . $websiteUrl;
                                    }
                                @endphp
                                <a class="btn btn-xs btn-outline-info" href="{{ $websiteUrl }}" target="_blank" >
                                    {{ $detalle->website }}
                                </a>
                            @else
                                -
                            @endif 
                            
                        </td>                         
                    </tr>
                </tbody>
            </table>

        </div>

        <div class="card mb-6 p-3">

            <div class="mb-4">
                <h5 class="mb-0 ms-2 pt-1"> 
                    <b>Datos del empresario</b> 
                    <a class="btn btn-xs btn-outline-info float-end" href="/empresarios/{{ $detalle->usuario->id ?? '#' }}" >
                        <i class="icon-base ri ri-eye-fill"></i> Más detalles
                    </a>
                </h5>
            </div> 
            <table class="table table-sm mb-3" >
                <tbody>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Identificación</th>
                        <td> {{ $detalle->usuario->identification ?? ' - ' }} </td>                            
                    </tr>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Nombre</th>
                        <td> {{ $detalle->usuario->name ?? '' }}  {{ $detalle->usuario->lastname ?? '' }} </td>                            
                    </tr>

                    <tr>
                        <th class="text-primary" style="width: 50px;">Email</th>
                        <td> {{ $detalle->usuario->email ?? '' }} </td>                            
                    </tr>
                    
                </tbody>
            </table>

        </div>

        <div class="card mb-6 p-3">

            <div class="d-flex mb-4">
                <h5 class="text-center mb-0 ms-2 pt-1"> <b>Datos de contacto</b> </h5>
            </div> 
            <table class="table table-sm mb-3" >
                <tbody>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Nombre</th>
                        <td> {{ $detalle->contact_person ?? ' - ' }} </td>                            
                    </tr>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Cargo</th>
                        <td> {{ $detalle->contact_position ?? ' - ' }} </td>                         
                    </tr>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Email</th>
                        <td> {{ $detalle->contact_email ?? ' - ' }} </td>                         
                    </tr>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Celular</th>
                        <td> {{ $detalle->contact_phone ?? ' - ' }} </td>                         
                    </tr>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Sexo</th>
                        <td> {{ $detalle->sexo ?? ' - ' }} </td>                         
                    </tr>
                </tbody>
            </table>

        </div>

        <div class="card mb-6 p-3">

            <div class="d-flex mb-4">
                <h5 class="text-center mb-0 ms-2 pt-1"> <b>Redes sociales</b> </h5>
            </div> 
            <table class="table table-sm mb-3" >
                <tbody>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Instagram</th>
                        <td>
                            @if ($detalle->social_instagram )
                                @php
                                    $instagramUrl = $detalle->social_instagram;
                                    if (!preg_match('#^https?://#', $instagramUrl)) {
                                        $instagramUrl = 'https://' . $instagramUrl;
                                    }
                                @endphp
                                <a class="btn btn-xs btn-outline-info" href="{{ $instagramUrl }}" target="_blank" >
                                    {{ $detalle->social_instagram }}
                                </a>
                            @else
                                -
                            @endif
                        </td>                            
                    </tr>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Facebook</th>
                        <td>
                            @if ($detalle->social_facebook )
                                @php
                                    $facebookUrl = $detalle->social_facebook;
                                    if (!preg_match('#^https?://#', $facebookUrl)) {
                                        $facebookUrl = 'https://' . $facebookUrl;
                                    }
                                @endphp
                                <a class="btn btn-xs btn-outline-info" href="{{ $facebookUrl }}" target="_blank" >
                                    {{ $detalle->social_facebook }}
                                </a>
                            @else
                                -
                            @endif
                        </td>                            
                    </tr>
                    <tr>
                        <th class="text-primary" style="width: 50px;">Linkedin</th>
                        <td>
                            @if ($detalle->social_linkedin )
                                @php
                                    $linkedinUrl = $detalle->social_linkedin;
                                    if (!preg_match('#^https?://#', $linkedinUrl)) {
                                        $linkedinUrl = 'https://' . $linkedinUrl;
                                    }
                                @endphp
                                <a class="btn btn-xs btn-outline-info" href="{{ $linkedinUrl }}" target="_blank" >
                                    {{ $detalle->social_linkedin }}
                                </a>
                            @else
                                -
                            @endif
                        </td>                            
                    </tr>
                </tbody>
            </table>

        </div>

    </div>

    <div class="col-12 col-md-12 mb-4">

        <div class="card mb-6 p-3">

            <div class="d-flex">
                <a class="btn btn-success px-2" href="/diagnosticosResultados/export?unidad={{ $detalle->unidadproductiva_id }}" target="_blanck" >
                    <i class="icon-base ri ri-file-excel-2-line me-2"></i> Exportar
                </a>

                <h5 class="text-center mb-0 ms-2 pt-1">Listado de diagnósticos</h5>
            </div> 
            <table id="diagnosticos" class="table" >
                <thead>
                    <th>Fecha</th>
                    <th>Etapa</th>
                    <th>Puntaje</th>
                    <th></th>
                </thead>
                <tbody>
                    @foreach ($detalle->diagnosticos as $item)
                        <tr>
                            <td>{{$item->fecha_creacion ?? ' - '}}</td>
                            <td>{{$item->etapa->name ?? ' - '}}</td>
                            <td>{{$item->resultado_puntaje ?? ' - '}}</td>
                            <td>
                                <a class="btn btn-xs btn-outline-info" href="/diagnosticosResultados/{{ $item->resultado_id }}" >
                                    <i class="icon-base ri ri-eye-fill"></i>
                                </a>
                            </td>                            
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

        <div class="card mb-6 p-3">

            <div class="d-flex" id="toolbarInscripciones">
                <a class="btn btn-success px-2" href="/inscripciones/export?unidad={{ $detalle->unidadproductiva_id }}" target="_blanck" >
                    <i class="icon-base ri ri-file-excel-2-line me-2"></i> Exportar
                </a>

                <h5 class="text-center mb-0 ms-2 pt-1">Listado de inscripciones</h5>
            </div> 
            <table id="inscripciones" class="table" >
                <thead>
                    <th>Fecha</th>
                    <th>Programa</th>
                    <th>Convocatoria</th>
                    <th>Estado</th>
                    <th></th>
                </thead>
                <tbody>
                    @foreach ($detalle->inscripciones as $item)
                        <tr>
                            <td>{{$item->fecha_creacion ?? ' - '}}</td>
                            <td>{{$item->convocatoria->programa->nombre ?? ' - '}}</td>
                            <td>{{$item->convocatoria->nombre_convocatoria ?? ' - '}}</td>
                            <td>{{$item->estado->inscripcionEstadoNOMBRE ?? ' - '}}</td>
                            <td>
                                <a class="btn btn-xs btn-outline-info" href="/inscripciones/{{ $item->inscripcion_id }}" >
                                    <i class="icon-base ri ri-eye-fill"></i>
                                </a>
                            </td>
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
            id: 'diagnosticos',
            setting: {
                pagination: true,
                search: true,
                pageLength: 5,
                lengthMenu: [5, 10, 20, 50, 100],
            }                
        },
        {
            id: 'inscripciones',
            setting: {
                pagination: true,
                search: true,
                pageLength: 5,
                lengthMenu: [5, 10, 20, 50, 100],
            }                
        }
    ];

    // Pasar arrays desde PHP sin doble encoding
    const categories = @json($dimensions ?? []);
    const seriesData = @json($results ?? []);
    
    console.log('Chart data:', { categories, seriesData });

    const options = {
        series: [{ data: seriesData, name: 'Puntaje' }],
        chart: { height: 380, type: 'radar', toolbar: { show: true } },
        title: { text: 'Último diagnóstico' },
        stroke: { width: 2 },
        fill: { opacity: 0.3 },
        markers: { size: 4 },
        dataLabels: { enabled: false },
        yaxis: { tickAmount: 5, decimalsInFloat: 0 },
        xaxis: { 
            categories,
            labels: { 
                show: true,
                style: { colors: Array(categories.length).fill('#6c757d'), fontSize: '12px' }
            }
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
