@extends('layouts.list', ['titulo'=> 'Usuarios', 'tituloModal'=> 'usuario'])

@section('form-fields')
    <div class="row">

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="identification">N° documento</label>
            <input type="number" class="form-control" name="identification" id="identification" placeholder="N° documento" required>
        </div>
        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="position">Cargo</label>
            <input type="text" class="form-control" name="position" id="position" placeholder="Cargo" required>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="active">Activo</label>
            <select class="form-select" name="active" id="active" required>
                <option value="" disabled selected>Seleccione una opción</option>
                <option value="0">No</option>
                <option value="1">Si</option>
            </select>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="name">Nombre (s)</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Nombre (s)" required>
        </div>
        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="lastname" >Apellido (s)</label>
            <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Apellido (s)" required>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="email">Email</label>
            <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="password">Contraseña</label>
            <input type="text" class="form-control" name="password" id="password" placeholder="**********">
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="rol_id">Rol</label>
            <select class="form-select" name="rol_id" id="rol_id">
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
        window.TABLA = {
            urlApi: '/users',
            sortName: 'name',
            accion_editar: true,
            columns: [
                { data: 'identification', title: 'N° documento', orderable: true },
                { data: 'name', title: 'Nombre (s)', orderable: true },
                { data: 'lastname', title: 'Apellido (s)', orderable: true },
                { data: 'position', title: 'Cargo', orderable: true }
            ]
        };
    </script>
@endsection