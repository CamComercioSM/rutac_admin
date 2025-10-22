@extends('layouts.list', ['titulo'=> 'Intervenciones', 'tituloModal'=> 'intervención'])

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

        <div class="col-12 col-md-12 form-group mb-4">
            <h4 class="mb-0">
                Unidades productivas  <button type="button" class="btn btn-sm btn-primary py-1" onclick="openAdd()" >Agregar</button>
            </h4>
            <div class="mb-2">
                <select class="form-select w-75" name="unidadAdd" id="unidadAdd" >
                    <option value="" disabled selected>Seleccione una unidad para agregar</option>
                    @foreach ($unidades as $item)
                        <option value="{{$item->id}}" >{{$item->nombre}}</option>
                    @endforeach
                </select>
            </div>

            <table class="table table-sm table-border border">
                <thead>                    
                    <th colspan="2" > Nombre </th>             
                </thead>
                <tbody id="table_opciones"></tbody>
            </table>
        </div>


    </div>
@endsection

@section('modals')

    <div class="position-fixed top-0 end-0 w-100 d-flex justify-content-center" style="z-index: 1111;">
        <div id="warningToast" class="toast bg-warning text-dark m-5" role="alert">
            <div class="toast-body"> ⚠️ El registro ya existe en la tabla. </div>
        </div>
    </div>

@endsection

@section('script')
    <script> 
        window.TABLA = {
            urlApi: '/intervenciones',
            sortName: 'id',
            accion_editar: false,
            columns: [
                { data: 'fecha_inicio', title: 'F. inicio', orderable: true },
                { data: 'fecha_fin', title: 'F. fin', orderable: true },
                { data: 'unidad', title: 'Unidad productiva', orderable: true },
                { data: 'asesor', title: 'Asesor', orderable: true },
                { data: 'descripcion', title: 'Descripción', orderable: false },
                { data: 'soporte', title: 'Soporte', orderable: false },
            ],
            initSelects: [ 
                { id:'unidadAdd', setting: {
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

        window.openAdd = function() 
        {
            const id = $("#unidadAdd").val();
            const text = $("#unidadAdd option:selected").text();

            if( !(id && text) ) return;

            let existe = $("#table_opciones tr[data-id='" + id + "']").length > 0;
            if (existe) {
                let toastEl = document.getElementById('warningToast');
                let toast = new bootstrap.Toast(toastEl, { delay: 2000 });
                toast.show();
                return;
            }

            window.itemOption({id: id, text: text });

            $("#unidadAdd").val(null).trigger('change');

        }

        window.itemOption = function(row={}) 
        {
            const index = $("#table_opciones tr").length;

            const item = `
                <tr data-id="${row.id}" >
                    <td> ${row.text} </td>                    
                    <td style="width: 80px;" >
                        <input type="hidden" name="unidades[${index}]" value="${row.id}" />

                        <button type="button" class="btn btn-danger btn-sm" onclick="removeOption(this)" >
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>`;

            $("#table_opciones").append(item);
        }
        
        window.removeOption = function(btn) {
            $(btn).closest("tr").remove();
        };

    </script>
@endsection