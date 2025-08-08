@extends('layouts.list', ['titulo'=> 'Administración de crons', 'tituloModal'=> 'cron'])

@section('form-fiels')
    <div class="row">

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="nombre">Nombre</label>
            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre" required>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="descripcion">Descripcion (opcional)</label>
            <textarea class="form-control" name="descripcion" id="descripcion" placeholder="Descripcion"></textarea>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="periodicidad">Periodicidad</label>
            <input type="text" class="form-control" name="periodicidad" id="periodicidad" placeholder="Periodicidad" required>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="ruta" >Ruta</label>
            <input type="text" class="form-control" name="ruta" id="ruta" placeholder="Ruta" required>
        </div>

    </div>
@endsection

@section('script')
    <script> 
        const TABLA = {
            urlApi: '/crons',
            sortName: 'nombre',
            acciones: "editar",
            columns: [
                { field: 'nombre', title: 'Nombre', sortable: true },
                { field: 'descripcion', title: 'Descripcion', sortable: true },
                { field: 'periodicidad', title: 'Periodicidad', sortable: true },
                { field: 'ruta', title: 'Ruta', sortable: true },
                { field: 'action', title: 'Acciones', formatter: 'actionFormatter', events: 'actionEvents', class: 'td-acciones' }
            ],
        };
    </script>
@endsection