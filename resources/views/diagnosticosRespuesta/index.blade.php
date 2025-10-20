@extends('layouts.list', ['titulo'=> 'Inscripciones', 'tituloModal'=> 'Inscripción'])

@section('form-filters')

    <div class="col-12 col-md-5 form-group mb-3">
        <label class="form-label" for="unidad">Unidad productiva</label>
        <select class="form-control" name="unidad" id="unidad">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($unidades as $item)
                <option value="{{$item->unidadproductiva_id}}" >{{$item->business_name}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-3 form-group mb-3">
        <label class="form-label" for="estado">Etapa</label>
        <select class="form-control" name="etapa" id="etapa">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($etapas as $item)
                <option value="{{$item->etapa_id}}" >{{$item->name}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-2 form-group mb-3">
        <label class="form-label" for="fecha_inicio">Fecha inicio</label>
        <input class="form-control" type="date" name="fecha_inicio" id="fecha_inicio">
    </div>

    <div class="col-12 col-md-2 form-group mb-3">
        <label class="form-label" for="fecha_inicio">Fecha fin</label>
        <input class="form-control" type="date" name="fecha_fin" id="fecha_fin">
    </div>

@endsection

@section('script')
    <script> 
        window.TABLA = {
            urlApi: '/diagnosticosResultados',
            sortName: 'fecha_creacion',
            accion_ver: true,
            columns: [
                { data: 'fecha_creacion', title: 'Fecha de inscripcion', orderable: true, render: v => window.formatearFecha(v) },
                { data: 'nit', title: 'NIT', orderable: true },
                { data: 'business_name', title: 'Unidad productiva', orderable: true },                
                { data: 'resultado_puntaje', title: 'Puntaje', orderable: true },
                { data: 'etapa', title: 'Etapa', orderable: true }
            ],
            initSelects: [ 
                { id:'etapa'}, 
                { 
                    id:'unidad', setting: {
                        ajax: {
                            url: '/unidadProductiva/search',
                            dataType: 'json',
                            delay: 300,
                        },
                        minimumInputLength: 3,
                    }
                } 
            ],
            initFiltros: @json($filtros)
        };
    </script>
@endsection