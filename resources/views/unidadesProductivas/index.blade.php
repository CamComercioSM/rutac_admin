@extends('layouts.list', ['titulo'=> 'Inscripciones', 'tituloModal'=> 'Inscripción'])

@section('form-filters')

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="tipopersona">Tipo de persona</label>
        <select class="form-select" name="tipopersona" id="tipopersona">
            <option value="" >Seleccione una opción</option>
            @foreach ($tipoPersona as $item)
                <option value="{{$item->tipopersona_id}}" >{{$item->tipoPersonaNOMBRE}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="sector">Sector</label>
        <select class="form-select" name="sector" id="sector">
            <option value="" >Seleccione una opción</option>
            @foreach ($sectores as $item)
                <option value="{{$item->sector_id}}" >{{$item->sectorNOMBRE}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="tamano">Tamaño</label>
        <select class="form-select" name="tamano" id="tamano">
            <option value="" selected >Seleccione una opción</option>
            @foreach ($tamanos as $item)
                <option value="{{$item->tamano_id}}" >{{$item->tamanoNOMBRE}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="etapa">Etapa</label>
        <select class="form-select" name="etapa" id="etapa">
            <option value="" selected >Seleccione una opción</option>
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
        window.TABLA = {
            urlApi: '/unidadesProductivas',
            sortName: 'fecha_creacion',
            
            menu_row: ` <a class="dropdown-item" href="/unidadesProductivas/ROWID" >Ver detalles</a>
                        <a class="dropdown-item" href="/unidadesProductivas/ROWID/edit">Editar</a>
                        <a class="dropdown-item" href="/diagnosticosResultados/list?unidad=ROWID">Diagnósticos</a>
                        <a class="dropdown-item" href="/inscripciones/list?unidad=ROWID">Inscripciones</a>
                        <a class="dropdown-item" href="/intervenciones/list?unidad=ROWID">Intervenciones</a>
                    `,
            
            columns: [
                { data: 'fecha_creacion', title: 'Fecha de registro', orderable: true, render: v => window.formatearFecha(v) },
                { data: 'tipo_registro_rutac', title: 'Tipo registro', orderable: true },
                { data: 'nit', title: 'NIT', orderable: true },
                { data: 'business_name', title: 'Razón social', orderable: true },
                { data: 'name_legal_representative', title: 'Representante legal', orderable: true },
                { data: 'registration_email', title: 'Email', orderable: true },
                { data: 'tipo_persona', title: 'Tipo persona', orderable: true },
                { data: 'sector', title: 'Sector', orderable: true },
                { data: 'tamano', title: 'Tamaño', orderable: true },
                { data: 'etapa', title: 'Etapa', orderable: true },
                { data: 'departamento', title: 'Departamento', orderable: true },
                { data: 'municipio', title: 'Municipio', orderable: true }
            ]
        };
    </script>
@endsection