@extends('layouts.list', ['titulo'=> 'Inscripciones', 'tituloModal'=> 'Inscripción'])

@section('form-filters')

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="tipopersona">Tipo de persona</label>
        <select class="form-control" name="tipopersona" id="tipopersona">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($tipoPersona as $item)
                <option value="{{$item->tipopersona_id}}" >{{$item->tipoPersonaNOMBRE}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="sector">Sector</label>
        <select class="form-control" name="sector" id="sector">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($sectores as $item)
                <option value="{{$item->sector_id}}" >{{$item->sectorNOMBRE}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="tamano">Tamaño</label>
        <select class="form-control" name="tamano" id="tamano">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($tamanos as $item)
                <option value="{{$item->tamano_id}}" >{{$item->tamanoNOMBRE}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="etapa">Etapa</label>
        <select class="form-control" name="etapa" id="etapa">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($etapas as $item)
                <option value="{{$item->etapa_id}}" >{{$item->name}}</option>
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
            urlApi: '/unidadesProductivas',
            sortName: 'fecha_creacion',
            accion_ver: true,
            columns: [
                { field: 'fecha_creacion', title: 'Fecha de registro', sortable: true, formatter: 'formatearFecha' },
                { field: 'tipo_registro_rutac', title: 'Tipo registro', sortable: true },
                { field: 'nit', title: 'NIT', sortable: true },
                { field: 'business_name', title: 'Razón social', sortable: true },                
                { field: 'name_legal_representative', title: 'Represnetante legal', sortable: true },
                { field: 'registration_email', title: 'Email', sortable: true },
                { field: 'tipo_persona', title: 'Tipo persona', sortable: true },
                { field: 'sector', title: 'Sector', sortable: true },
                { field: 'tamano', title: 'Tamaño', sortable: true },
                { field: 'etapa', title: 'Etapa', sortable: true },
                { field: 'departamento', title: 'Departamento', sortable: true },
                { field: 'municipio', title: 'Municipio', sortable: true }
            ]
        };
    </script>
@endsection