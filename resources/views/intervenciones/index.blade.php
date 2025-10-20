@extends('layouts.list', ['titulo'=> 'Intervenciones', 'tituloModal'=> 'interversión'])

@section('form-filters')

    <div class="col-12 col-md-6 form-group mb-3">
        <label class="form-label" for="unidad">Unidad productiva</label>
        <select class="form-select" name="unidad" id="unidad">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($unidades as $item)
                <option value="{{$item->unidadproductiva_id}}" >{{$item->business_name}}</option>
            @endforeach
        </select>
    </div>

    @if (!$esAsesor)
        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="asesor">Asesor</label>
            <select class="form-select" name="asesor" id="asesor">
                <option value="" disabled selected>Seleccione una opción</option>
                @foreach ($asesores as $item)
                    <option value="{{$item->id}}" >{{$item->name}} {{$item->lastname}}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="col-12 col-md-3 form-group mb-3">
        <label class="form-label" for="fecha_inicio">Fecha inicio</label>
        <input class="form-control" type="date" name="fecha_inicio" id="fecha_inicio">
    </div>

    <div class="col-12 col-md-3 form-group mb-3">
        <label class="form-label" for="fecha_inicio">Fecha fin</label>
        <input class="form-control" type="date" name="fecha_fin" id="fecha_fin">
    </div>

@endsection

@section('form-fields')
    <div class="row">

        <div class="col-12 col-md-12 form-group mb-4">
            <label class="form-label" for="unidadproductiva_id">Unidad productiva</label>
            <select class="form-select" name="unidadproductiva_id" id="unidadproductiva_id">
                <option value="" disabled selected>Seleccione una opción</option>
            </select>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="fecha_inicio">Fecha inicio</label>
            <input type="datetime-local" class="form-control" name="fecha_inicio" id="fecha_inicio" placeholder="Fecha inicio" required>
        </div>
        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="fecha_fin">Fecha fin</label>
            <input type="datetime-local" class="form-control" name="fecha_fin" id="fecha_fin" placeholder="Fecha fin" required>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="descripcion" >Descripción</label>
            <textarea class="form-control" name="descripcion" id="descripcion" rows="10" placeholder="Descripción"></textarea>
        </div>

        <div class="col-12 col-md-12 form-group mb-3" id="contFormFile">
            <label for="formFile" class="form-label">Soporte (opcional)</label>
            <input class="form-control" type="file" id="formFile" name="formFile">
        </div>

    </div>
@endsection

@section('script')
    <script> 
        window.TABLA = {
            urlApi: '/intervenciones',
            sortName: 'id',
            accion_editar: true,
            columns: [
                { data: 'fecha_inicio', title: 'F. inicio', orderable: true },
                { data: 'fecha_fin', title: 'F. fin', orderable: false },
                { data: 'unidad', title: 'Unidad productiva', orderable: false },
                { data: 'asesor', title: 'Asesor', orderable: false },
            ],
            initSelects: [ 
                { id:'unidadproductiva_id', setting: {
                        ajax: {
                            url: '/unidadProductiva/search',
                            dataType: 'json',
                            delay: 300,
                        },
                        minimumInputLength: 3,
                    }
                },
                { id:'unidad', setting: {
                        ajax: {
                            url: '/unidadProductiva/search',
                            dataType: 'json',
                            delay: 300,
                        },
                        minimumInputLength: 3,
                    }
                },
                { id:'asesor'}, 
            ],
            initFiltros: @json($filtros)
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