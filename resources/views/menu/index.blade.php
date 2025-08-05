@extends('layouts.list', ['titulo'=> 'Administrar menu', 'tituloModal'=> 'opción de menu'])

@section('form-fiels')
    <div class="row">

        <div class="col-12 col-md-6 form-group mb-3">
            <label for="label">Label</label>
            <input type="text" class="form-control" name="label" id="label" placeholder="Label" required>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label for="icon">Icono</label>
            <input type="text" class="form-control" name="icon" id="icon" placeholder="Icono" required>
        </div>
        <div class="col-12 col-md-12 form-group mb-3">
            <label for="url" >URL</label>
            <input type="text" class="form-control" name="url" id="url" placeholder="URL" required>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label for="roles">Roles</label>
            <select class="form-control" name="roles[]" id="roles" multiple >
                @foreach ($roles as $item)
                    <option value="{{$item->id}}" >{{$item->name}}</option>
                @endforeach
            </select>
        </div>

    </div>
@endsection

@section('script')
    <script src="/libs/select2/select2.min.js"></script>
    <link rel="stylesheet" href="/libs/select2/select2.min.css">
    <script> 
        const TABLA = {
            urlApi: '/menu',
            sortName: 'label',
            acciones: "editar",
            columns: [
                { field: 'label', title: 'Label', sortable: true },
                { field: 'icon', title: 'Icon', sortable: true },
                { field: 'url', title: 'URL', sortable: true },
                { field: 'action', title: 'Acciones', formatter: 'actionFormatter', events: 'actionEvents', class: 'td-acciones' }
            ]
        };

        $('#roles').select2({ placeholder: 'Seleccione las opciones' });
    </script>
@endsection