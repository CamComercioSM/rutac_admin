@extends('layouts.list', ['titulo'=> 'Links', 'tituloModal'=> 'link'])

@section('form-fields')
    <div class="row">

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="name">Nombre</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Nombre" required>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="type">Tipo</label>
            <select id="type" class="form-control form-select">
                <option value="" selected >Seleccione una opción</option>
                <option value="0"> URL externa </option>
                <option value="1"> Archivo </option>
            </select>
        </div>

        <div class="col-12 col-md-12 form-group mb-3 d-none" id="contValue">
            <label class="form-label" for="value">URL</label>
            <input type="url" class="form-control" name="value" id="value" placeholder="URL">
        </div>

        <div class="col-12 col-md-12 form-group mb-3 d-none" id="contFormFile">
            <label for="formFile" class="form-label">Archivo</label>
            <input class="form-control" type="file" id="formFile" name="formFile">
        </div>

    </div>
@endsection

@section('script')
    <script> 
        window.TABLA = {
            urlApi: '/links',
            sortName: 'name',
            menu_row: ` <button class="dropdown-item" onClick="openEditar()" >Editar</button> `,
            columns: [
                { data: 'name', title: 'Nombre', orderable: true },
                { data: 'value', title: 'URL', orderable: false },
            ]
        };

        document.addEventListener('DOMContentLoaded', function () {
            $('#type').on('change', function () {
                
                const id = $("#type").val();

                if(id != null)
                {
                    $("#contValue").addClass('d-none');
                    $("#contFormFile").addClass('d-none');

                    if(id == 0){
                        $("#contValue").removeClass('d-none');
                    }
                    else if(id ==1){
                        $("#contFormFile").removeClass('d-none');
                    }
                }
            });
        });
    </script>
@endsection