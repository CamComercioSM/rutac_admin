@extends('layouts.list', ['titulo'=> 'Usuarios', 'tituloModal'=> 'usuario'])

@section('form-fields')
    <div class="row">

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="identification">N° documento</label>
            <input type="text" class="form-control" name="identification" id="identification" placeholder="N° documento" required maxlength="20" pattern="^[A-Za-z0-9]{5,20}$" title="Alfanumérico sin espacios ni símbolos. 5 a 20 caracteres (permite pasaporte).">
            <div class="invalid-feedback">Alfanumérico sin espacios ni símbolos. 5 a 20 caracteres.</div>
        </div>
        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="position">Cargo</label>
            <input type="text" class="form-control" name="position" id="position" placeholder="Cargo" required>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="active">Activo</label>
            <select class="form-select" name="active" id="active" required>
                <option value="" selected >Seleccione una opción</option>
                <option value="0">No</option>
                <option value="1">Si</option>
            </select>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="name">Nombre (s)</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Nombre (s)" required maxlength="200" pattern="^[^0-9]{2,200}$" title="No se permiten números. 2 a 200 caracteres.">
            <div class="invalid-feedback">No se permiten números. 2 a 200 caracteres.</div>
        </div>
        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="lastname" >Apellido (s)</label>
            <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Apellido (s)" required maxlength="200" pattern="^[^0-9]{2,200}$" title="No se permiten números. 2 a 200 caracteres.">
            <div class="invalid-feedback">No se permiten números. 2 a 200 caracteres.</div>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="email">Email</label>
            <input type="email" class="form-control" name="email" id="email" placeholder="Email" required maxlength="120" pattern="^[^<>\s]{5,120}$" title="Correo válido, sin espacios ni < >. Máximo 120 caracteres.">
            <div class="invalid-feedback">Correo válido, sin espacios ni < >. Máximo 120 caracteres.</div>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="password">Contraseña</label>
            <input type="text" class="form-control" name="password" id="password" placeholder="**********">
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="rol_id">Rol</label>
            <select class="form-select" name="rol_id" id="rol_id">
                <option value="" selected >Seleccione una opción</option>
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
            menu_row: ` <button class="dropdown-item" onClick="openEditar()" >Editar</button> `,
            columns: [
                { data: 'identification', title: 'N° documento', orderable: true },
                { data: 'name', title: 'Nombre (s)', orderable: true },
                { data: 'lastname', title: 'Apellido (s)', orderable: true },
                { data: 'position', title: 'Cargo', orderable: true }
            ]
        };
    </script>
@endsection