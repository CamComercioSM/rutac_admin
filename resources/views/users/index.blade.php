@extends('layouts.list', ['titulo'=> 'Usuarios', 'tituloModal'=> 'usuario'])

@section('form-fiels')
    <div class="row">

        <div class="col-12 col-md-6 form-group">
            <label for="identification">N° documento</label>
            <input type="number" class="form-control" name="identification" id="identification" placeholder="N° documento" required>
        </div>
        <div class="col-12 col-md-6 form-group">
            <label for="position">Cargo</label>
            <input type="text" class="form-control" name="position" id="position" placeholder="Cargo" required>
        </div>

        <div class="col-12 col-md-6 form-group">
            <label for="name">Nombre (s)</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Nombre (s)" required>
        </div>
        <div class="col-12 col-md-6 form-group">
            <label for="lastname" >Apellido (s)</label>
            <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Apellido (s)" required>
        </div>

        <div class="col-12 col-md-4 form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
        </div>

        <div class="col-12 col-md-4 form-group">
            <label for="password">Contraseña</label>
            <input type="text" class="form-control" name="password" id="password" placeholder="**********">
        </div>

        <div class="col-12 col-md-4 form-group">
            <label for="rol_id">Rol</label>
            <select class="form-control" name="rol_id" id="rol_id">
                <option value="" disabled selected>Seleccione una opción</option>
                @foreach ($roles as $item)
                    <option value="{{$item->id}}" >{{$item->name}}</option>
                @endforeach
            </select>
        </div>

    </div>
@endsection

@section('script')
    <script> 
        const TABLA = {
            urlApi: '/users',
            sortName: 'name',
            acciones: "editar",
            columns: [
                { field: 'identification', title: 'N° documento', sortable: true },
                { field: 'name', title: 'Nombre (s)', sortable: true },
                { field: 'lastname', title: 'Apellido (s)', sortable: true },
                { field: 'position', title: 'Cargo', sortable: true },
                { field: 'action', title: 'Acciones', formatter: 'actionFormatter', events: 'actionEvents', class: 'td-acciones' }
            ]
        };
    </script>
@endsection