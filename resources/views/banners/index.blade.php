@extends('layouts.list', ['titulo'=> 'Capsulas', 'tituloModal'=> 'capsula'])

@section('form-fields')
    <div class="row">

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="name">Nombre</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Nombre" required>
        </div>
        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="title">Titulo</label>
            <input type="text" class="form-control" name="title" id="title" placeholder="Titulo" required>
        </div>
        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="description" >Descripción</label>
            <textarea class="form-control" name="description" id="description" rows="5" placeholder="Descripción"></textarea>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="text_button">Botón</label>
            <input type="text" class="form-control" name="text_button" id="text_button" placeholder="Botón">
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="url">URL</label>
            <input type="url" class="form-control" name="url" id="url" placeholder="URL">
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label for="formFile" class="form-label">Imagen</label>
            <input class="form-control" type="file" id="formFile" name="formFile">
        </div>

    </div>
@endsection

@section('script')
    <script> 
        window.TABLA = {
            urlApi: '/banners',
            sortName: 'name',
            accion_editar: true,
            columns: [
                { data: 'name', title: 'Nombre', orderable: true },
                { data: 'title', title: 'Titulo', orderable: true },
            ]
        };
    </script>
@endsection