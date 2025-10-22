@extends('layouts.list', ['titulo'=> 'Historias', 'tituloModal'=> 'historia'])

@section('form-fields')
    <div class="row">

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="name">Nombre</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Nombre" required>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="video_url">URL Video</label>
            <input type="url" class="form-control" name="video_url" id="video_url" placeholder="URL Video">
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
            urlApi: '/historias',
            sortName: 'name',
            accion_editar: true,
            columns: [
                { data: 'name', title: 'Nombre', orderable: true },
                { data: 'video_url', title: 'Video', orderable: false },
            ]
        };
    </script>
@endsection