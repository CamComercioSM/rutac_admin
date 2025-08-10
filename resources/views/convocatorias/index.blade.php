@extends('layouts.list', ['titulo'=> 'Convocatorias', 'tituloModal'=> 'convocatoria', 'exportar'=> $puedeExportar])

@section('form-filters')

    <div class="col-12 col-md-3 form-group mb-3">
        <label class="form-label" for="programa">Programa</label>
        <select class="form-select" name="programa" id="programa">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($programas as $item)
                <option value="{{$item->programa_id}}" >{{$item->nombre}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-3 form-group mb-3">
        <label class="form-label" for="sector">Sector</label>
        <select class="form-select" name="sector" id="sector">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($sectores as $item)
                    <option value="{{$item->sector_id}}" >{{$item->sectorNOMBRE}}</option>
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

@section('form-fiels')
    <div class="row">

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="programa_id">Programa</label>
            <select class="form-select" name="programa_id" id="programa_id">
                <option value="" disabled selected>Seleccione una opción</option>
                @foreach ($programas as $item)
                    <option value="{{$item->programa_id}}" >{{$item->nombre}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="nombre_convocatoria">Nombre </label>
            <input type="text" class="form-control" name="nombre_convocatoria" id="nombre_convocatoria" placeholder="Nombre " required>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="persona_encargada">Persona a cargo</label>
            <input type="text" class="form-control" name="persona_encargada" id="persona_encargada" placeholder="Persona a cargo" required>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="correo_contacto" >Correo de contacto</label>
            <input type="email" class="form-control" name="correo_contacto" id="correo_contacto" placeholder="Correo de contacto" required>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="telefono">Teléfono de contacto</label>
            <input type="text" class="form-control" name="telefono" id="telefono" placeholder="Teléfono de contacto" required>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="fecha_apertura_convocatoria">Fecha de inicio</label>
            <input type="date" class="form-control" name="fecha_apertura_convocatoria" id="fecha_apertura_convocatoria" placeholder="Fecha de inicio" required>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="fecha_cierre_convocatoria">Fecha de finalización</label>
            <input type="date" class="form-control" name="fecha_cierre_convocatoria" id="fecha_cierre_convocatoria" placeholder="Fecha de finalización" required>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="sector_id">Sector</label>
            <select class="form-select" name="sector_id" id="sector_id">
                <option value="" disabled selected>Seleccione una opción</option>
                @foreach ($sectores as $item)
                    <option value="{{$item->sector_id}}" >{{$item->sectorNOMBRE}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="con_matricula">Con matricula</label>
            <select class="form-select" name="con_matricula" id="con_matricula">
                <option value="" disabled selected>Seleccione una opción</option>
                <option value="0">No</option>
                <option value="1">Si</option>
            </select>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="asesores">Asesores</label>
            <select class="form-select" name="asesores[]" id="asesores" multiple >
                @foreach ($asesores as $item)
                    <option value="{{$item->id}}" >{{$item->name}} {{$item->lastname}}</option>
                @endforeach
            </select>
        </div>

    </div>
@endsection

@section('script')
    <script> 
        const TABLA = {
            urlApi: '/convocatorias',
            sortName: 'convocatoria_id',
            accion_editar: true,
            columns: [
                { field: 'nombre_programa', title: 'Programa', sortable: true },
                { field: 'nombre_convocatoria', title: 'Nombre', sortable: true },
                { field: 'persona_encargada', title: 'Persona a cargo', sortable: true },
                { field: 'telefono', title: 'Teléfono', sortable: true },
                { field: 'fecha_apertura_convocatoria', title: 'Fecha inicio', sortable: true, formatter: 'formatearFecha' },
                { field: 'fecha_cierre_convocatoria', title: 'Fecha finalización', sortable: true, formatter: 'formatearFecha' }
            ],
            initSelects: [ { id:'programa'}, { id:'asesores', setting:{ placeholder: 'Selección multiple'}  } ]
        };
    </script>
@endsection