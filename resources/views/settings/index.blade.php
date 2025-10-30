@extends('layouts.list', ['titulo'=> 'Links', 'tituloModal'=> 'link'])

@section('form-fields')
    <div class="row">

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="type_name">Tipo</label>
            <input type="text" class="form-control" name="type_name" id="type_name" placeholder="Nombre" readonly >
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="value" >Descripción</label>
            <textarea class="form-control" name="value" id="value" rows="5" placeholder="Descripción"></textarea>
        </div>

    </div>
@endsection

@section('script')
    <script> 
        window.TABLA = {
            urlApi: '/settings',
            sortName: 'id',
            menu_row: ` <button class="dropdown-item" onClick="openEditar()" >Editar</button> `,
            columns: [
                { data: 'type_name', title: 'Tipo', orderable: true },
                { data: 'value', title: 'Descripción', orderable: false },
            ]
        };

    </script>
@endsection