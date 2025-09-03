@extends('layouts.list', ['titulo'=> 'Inscripciones', 'tituloModal'=> 'Inscripción'])

@section('form-filters')

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="programa">Programa</label>
        <select class="form-select" name="programa" id="programa">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($programas as $item)
                <option value="{{$item->programa_id}}" >{{$item->nombre}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="convocatoria">Convocatoria</label>
        <select class="form-select" name="convocatoria" id="convocatoria">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($convocatorias as $item)
                <option value="{{$item->convocatoria_id}}" >{{$item->nombre_convocatoria}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="estado">Estado inscripción</label>
        <select class="form-select" name="estado" id="estado">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($estados as $item)
                <option value="{{$item->inscripcionestado_id}}" >{{$item->inscripcionEstadoNOMBRE}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-6 form-group mb-3">
        <label class="form-label" for="unidad">Unidad productiva</label>
        <select class="form-select" name="unidad" id="unidad">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($unidades as $item)
                <option value="{{$item->id}}" >{{$item->nombre}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-3 form-group mb-3">
        <label class="form-label" for="fecha_inicio">Fecha inicio</label>
        <input class="form-control" type="date" name="fecha_inicio" id="fecha_inicio">
    </div>

    <div class="col-12 col-md-3 form-group mb-3">
        <label class="form-label" for="fecha_inicio">Fecha fin</label>
        <input class="form-control" type="date" name="fecha_fin" id="fecha_fin">
    </div>

@endsection


@section('form-fiels')

    <div class="row">

        <div class="col-12 col-md-12 form-group mb-4">
            <label class="form-label" for="programaAdd">Programa</label>
            <select class="form-select" name="programaAdd" id="programaAdd">
                <option value="" disabled selected>Seleccione una opción</option>
                @foreach ($programas as $item)
                    <option value="{{$item->programa_id}}" >{{$item->nombre}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-12 form-group mb-5">
            <label class="form-label" for="convocatoriaAdd">Convocatoria</label>
            <select class="form-select" name="convocatoriaAdd" id="convocatoriaAdd" required >
                <option value="" disabled selected>Seleccione una opción</option>
                @foreach ($convocatorias as $item)
                    <option value="{{$item->convocatoria_id}}" data-programa="{{$item->programa_id}}" >{{$item->nombre_convocatoria}}</option>
                @endforeach
            </select>
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

    <div class="position-fixed top-0 end-0 p-5 w-100 d-flex justify-content-center" style="z-index: 1111;">
        <div id="warningToast" class="toast bg-warning text-dark" role="alert">
            <div class="toast-body">
            ⚠️ El registro ya existe en la tabla.
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script> 
        const TABLA = {
            urlApi: '/inscripciones',
            sortName: 'fecha_creacion',
            accion_ver: true,
            columns: [
                @if ($esAsesor != 1)
                    { field: 'nombre_convocatoria', title: 'Convocatoria', sortable: true },
                    { field: 'nombre_programa', title: 'Programa', sortable: true },
                 @endif  
                { field: 'nit', title: 'NIT', sortable: true },
                { field: 'business_name', title: 'Unidad productiva', sortable: true },
                { field: 'sector', title: 'Sector', sortable: true },
                { field: 'ventas', title: 'Ventas', sortable: true },
                { field: 'fecha_creacion', title: 'Fecha de inscripcion', sortable: true, formatter: 'formatearFecha' },
                { field: 'estado', title: 'Estado', sortable: true }
            ],
            initSelects: [ 
                { id:'programa'}, 
                { id:'convocatoria'}, 
                { id:'estado'}, 
                { id:'unidad', setting: {
                        ajax: {
                            url: '/unidadProductiva/search',
                            dataType: 'json',
                            delay: 300,
                        },
                        minimumInputLength: 3,
                    }
                },
                { id:'unidadAdd', setting: {
                        ajax: {
                            url: '/unidadProductiva/search',
                            dataType: 'json',
                            delay: 300,
                        },
                        minimumInputLength: 3,
                    }
                },
                { id:'programaAdd', 
                    change: function(e)
                    {
                        let id = $("#programaAdd").val();

                        $("#convocatoriaAdd option").prop("disabled", true);
                        $("#convocatoriaAdd option[data-programa='" + id + "']").prop("disabled", false);

                        $("#convocatoriaAdd").val(null).trigger('change');
                        $("#convocatoriaAdd").select2();
                    } 
                }, 
                { id:'convocatoriaAdd'}, 
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
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>`;

            $("#table_opciones").append(item);
        }
        
        window.removeOption = function(btn) {
            $(btn).closest("tr").remove();
        };

        window.initAlAbrirModal = function()
        {
            $("#programaAdd").val($("#programa").val()).trigger('change');
            $("#convocatoriaAdd").val($("#convocatoria").val()).trigger('change');
        }

        window.validarExtraForm = function()
        {
            return $("#table_opciones tr").length > 0;
        }

    </script>

    <style>
        .select2-container--default .select2-results__option--disabled
        {
            display: none !important;
        }
    </style>
@endsection