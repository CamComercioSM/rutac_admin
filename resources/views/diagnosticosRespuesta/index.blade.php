@extends('layouts.list', ['titulo'=> 'Inscripciones', 'tituloModal'=> 'Inscripción'])

@section('form-filters')

    <div class="col-12 col-md-5 form-group mb-3">
        <label class="form-label" for="unidad">Unidad productiva</label>
        <select class="form-control" name="unidad" id="unidad">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($unidades as $item)
                <option value="{{$item->id}}" >{{$item->nombre}}</option>
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
    <script src="/libs/select2/select2.min.js"></script>
    <link rel="stylesheet" href="/libs/select2/select2.min.css">
    <script> 
        const TABLA = {
            urlApi: '/diagnosticosResultados',
            sortName: 'fecha_creacion',
            acciones: "ver",
            columns: [
                { field: 'fecha_creacion', title: 'Fecha de inscripcion', sortable: true, formatter: 'formatearFecha' },
                { field: 'nit', title: 'NIT', sortable: true },
                { field: 'business_name', title: 'Unidad productiva', sortable: true },                
                { field: 'resultado_puntaje', title: 'Puntaje', sortable: true },
                { field: 'etapa', title: 'Etapa', sortable: true },
                { field: 'action', title: 'Acciones', formatter: 'actionFormatter', events: 'actionEvents', class: 'td-acciones' }
            ]
        };

        $('#etapa').select2();
        $('#unidad').select2({
            ajax: {
                url: '/unidadProductiva/search',
                dataType: 'json',
                delay: 300,
            },
            minimumInputLength: 3,
        });
    </script>
@endsection