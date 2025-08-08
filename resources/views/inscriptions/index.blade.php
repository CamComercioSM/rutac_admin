@extends('layouts.list', ['titulo'=> 'Inscripciones', 'tituloModal'=> 'Inscripción'])

@section('form-filters')

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="programa">Programa</label>
        <select class="form-select" name="programa" id="programa">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($programas as $item)
                <option value="{{$item->programa_id}}" >{{$item->nombre}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="convocatoria">Convocatoria</label>
        <select class="form-select" name="convocatoria" id="convocatoria">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($convocatorias as $item)
                <option value="{{$item->convocatoria_id}}" >{{$item->nombre_convocatoria}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="estado">Estado inscripción</label>
        <select class="form-select" name="estado" id="estado">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($estados as $item)
                <option value="{{$item->inscripcionestado_id}}" >{{$item->inscripcionEstadoNOMBRE}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-6 form-group mb-3">
        <label class="form-label" for="unidad">Unidad productiva</label>
        <select class="form-select" name="unidad" id="unidad">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($unidades as $item)
                <option value="{{$item->id}}" >{{$item->nombre}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-3 form-group mb-3">
        <label class="form-label" for="fecha_inicio">Fecha inicio</label>
        <input class="form-control" type="date" name="fecha_inicio" id="fecha_inicio">
    </div>

    <div class="col-12 col-md-3 form-group mb-3">
        <label class="form-label" for="fecha_inicio">Fecha fin</label>
        <input class="form-control" type="date" name="fecha_fin" id="fecha_fin">
    </div>

@endsection

@section('script')
    <script> 
        const TABLA = {
            urlApi: '/inscriptions',
            sortName: 'fecha_creacion',
            acciones: "ver",
            columns: [
                { field: 'nombre_convocatoria', title: 'Convocatoria', sortable: true },
                { field: 'nombre_programa', title: 'Programa', sortable: true },
                { field: 'nit', title: 'NIT', sortable: true },
                { field: 'business_name', title: 'Unidad productiva', sortable: true },
                { field: 'fecha_creacion', title: 'Fecha de inscripcion', sortable: true, formatter: 'formatearFecha' },
                { field: 'estado', title: 'Estado', sortable: true },
                { field: 'action', title: 'Acciones', formatter: 'actionFormatter', events: 'actionEvents', class: 'td-acciones' }
            ],
            initSelects: [ 
                { id:'programa'}, 
                { id:'convocatoria'}, 
                { id:'estado'}, 
                { id:'unidad', setting: {
                        ajax: {
                            url: '/unidadProductiva/search',
                            dataType: 'json',
                            delay: 300,
                        },
                        minimumInputLength: 3,
                    }
                } 
            ]
        };

    </script>
@endsection