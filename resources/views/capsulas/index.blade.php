@extends('layouts.list', ['titulo'=> 'Capsulas', 'tituloModal'=> 'capsula'])

@section('form-fielsd')
    <div class="row">

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="nombre">Nombre</label>
            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre" required>
        </div>
        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="lastname" >Descripción</label>
            <textarea class="form-control" name="descripcion" id="descripcion" rows="5" placeholder="Descripción"></textarea>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="url_video">URL Video</label>
            <input type="url" class="form-control" name="url_video" id="url_video" placeholder="URL Video">
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
            urlApi: '/capsulas',
            sortName: 'nombre',
            accion_editar: true,
            columns: [
                { data: 'nombre', title: 'Nombre', orderable: true },
            ]
        };
    </script>
@endsection