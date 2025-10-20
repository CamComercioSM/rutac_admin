@extends('layouts.list', ['titulo'=> 'Inscripciones', 'tituloModal'=> 'Inscripción'])

@section('btns-actions')
    <button class="btn btn-primary" id="btnCambioEstado">
        Cambio de estado
    </button>
@endsection

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
                <option value="{{$item->unidadproductiva_id}}" >{{$item->business_name}}</option>
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

@section('form-fields')

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

@endsection

@section('modals')
    <div class="modal fade" id="cambioEstadoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-6">
            <div class="modal-body pt-md-0 px-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h4 class="mb-2">Cambio de estado</h4>
                </div>
                <form id="cambioEstadoForm" class="row g-5 d-flex align-items-center" 
                    action="/inscripciones/0" 
                    enctype="multipart/form-data"
                    method="POST" >

                    <div class="col-sm-12 mb-3">
                        <label class="form-label">Unidades productivas seleccionadas</label>
                        <ul id="listaUnidades" class="list-group mb-3"></ul>
                    </div>
                
                    <div class="col-sm-12 mb-3">
                        <label class="form-label" for="inscripcionestado_id">Estado</label>
                        <select id="inscripcionestado_id" name="inscripcionestado_id" class="form-select form-select-sm">
                            <option value="" disabled selected>Seleccione una opción</option>
                            @foreach ($estados as $item)
                                <option value="{{$item->inscripcionestado_id}}" >{{$item->inscripcionEstadoNOMBRE}}</option>
                            @endforeach
                        </select>
                    </div>
                
                    <div class="col-sm-12 mb-3">
                        <label class="form-label" for="comentarios">Comentarios </label>
                        <textarea class="form-control" name="comentarios" id="comentarios" rows="4" placeholder="Ingrese los comentarios"></textarea>
                    </div>

                    <div class="col-sm-12 mb-3">
                        <label class="form-label" for="activarPreguntas">¿Activar preguntas nuevamente?</label>
                        <select class="form-select form-select-sm" name="activarPreguntas" id="activarPreguntas">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="0">No</option>
                            <option value="1">Si</option>
                        </select>
                    </div>

                    <div class="col-sm-12 mb-3">
                        <label class="form-label" for="archivo">Archivo adjunto</label>
                        <input class="form-control" type="file" name="archivo" id="archivo" accept=".pdf,.jpg,.png,.doc,.docx">
                    </div>

                    @csrf
                    @method('PATCH')

                    <div class="col-sm-12 text-center">
                        <hr class="mx-md-n5 mx-n3" />

                        <button class="btn btn-success mt-4" type="submit">
                            Guardar
                        </button>
                    </div>

                </form>
            </div>
            
            </div>
        </div>
    </div>

    <div class="position-fixed top-0 end-0 w-100 d-flex justify-content-center" style="z-index: 1111;">
        <div id="warningToast" class="toast bg-warning text-dark m-5" role="alert">
            <div class="toast-body"> ⚠️ El registro ya existe en la tabla. </div>
        </div>
        
        <div id="estadoToast" class="toast align-items-center text-bg-success border-0 m-5" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"> ✅ Cambio guardado exitosamente </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script> 
        window.TABLA = {
            urlApi: '/inscripciones',
            sortName: 'fecha_creacion',
            menu_row: `<a class="dropdown-item" href="/inscripciones/ROWID" >Ver detalles</a>`,
            seleccionMultiple: true,
            columns: [
                { data: 'id', title: '', orderable: false, render: v => `<input type="checkbox" class="fila-check" data-id="${v}">`, class: 'check' },
                @if ($esAsesor != 1)
                    { data: 'nombre_convocatoria', title: 'Convocatoria', orderable: true },
                    { data: 'nombre_programa', title: 'Programa', orderable: true },
                @endif  
                { data: 'nit', title: 'NIT', orderable: true },
                { data: 'business_name', title: 'Unidad productiva', orderable: true },
                { data: 'sector', title: 'Sector', orderable: true },
                { data: 'ventas', title: 'Ventas', orderable: true },
                { data: 'fecha_creacion', title: 'Fecha de inscripcion', orderable: true, render: v => window.formatearFecha(v) },
                { data: 'estado', title: 'Estado', orderable: true }
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
                { 
                    id:'unidadAdd', setting: {
                        ajax: {
                            url: '/unidadProductiva/search',
                            dataType: 'json',
                            delay: 300,
                        },
                        minimumInputLength: 3,
                    }
                },
                { 
                    id:'programaAdd', 
                    change: function(e)
                    {
                        let id = $("#programaAdd").val();

                        $("#convocatoriaAdd option").prop("disabled", true);
                        $("#convocatoriaAdd option[data-programa='" + id + "']").prop("disabled", false);

                        $("#convocatoriaAdd").val(null).trigger('change');
                        $("#convocatoriaAdd").select2();
                    } 
                }, 
                { id:'convocatoriaAdd' }, 
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

        window.initAlAbrirModal = function()
        {
            $("#programaAdd").val($("#programa").val()).trigger('change');
            $("#convocatoriaAdd").val($("#convocatoria").val()).trigger('change');
        }

        window.validarExtraForm = function()
        {
            return $("#table_opciones tr").length > 0;
        }

        document.addEventListener('DOMContentLoaded', function () {

            const cargando = document.querySelectorAll('.cargando')[0];

            $('#cambioEstadoForm').on('submit', function (e) {
                e.preventDefault();

                cargando.classList.remove('d-none');

                let form = $(this); 
                let formEl = this; 

                let method = form.attr('method'); 
                let actionUrl = form.attr('action');

                let formData = new FormData(formEl);

                $.ajax({
                    type: method,
                    url: actionUrl,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {

                        $(".modal").modal('hide');
                        cargando.classList.add('d-none');
                        
                        let toastEl = document.getElementById('estadoToast');
                        let toast = new bootstrap.Toast(toastEl, { delay: 2000 }); // 3s
                        toast.show();

                        // Recargar la página después de que el toast se oculte
                        toastEl.addEventListener('hidden.bs.toast', () => {
                            cargando.classList.remove('d-none');
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        alert('Ocurrió un error al guardar');
                       cargando.classList.add('d-none');
                    }
                });
            });

            let seleccionados = new Map();

            $('#tabla').on('change', '.fila-check', function () {
                const rowData = $('#tabla').DataTable().row($(this).closest('tr')).data();
                const id = $(this).data('id');
                
                if (this.checked) {
                    seleccionados.set(id, rowData.business_name);
                } else {
                    seleccionados.delete(id);
                }
            });

            $('#btnCambioEstado').on('click', function () {
                if (seleccionados.size === 0) {
                    alert('Debe seleccionar al menos un registro.');
                    return;
                }

                $('#listaUnidades').empty();

                seleccionados.forEach((nombre, id) => {
                    $('#listaUnidades').append(`
                        <li class="list-group-item">
                            <input type="hidden" name="inscripciones[]" value="${id}"> ${nombre}
                        </li>`);
                });

                const modal = new bootstrap.Modal(document.getElementById('cambioEstadoModal'));
                modal.show();
            });

        });

    </script>

    <style>
        .select2-container--default .select2-results__option--disabled
        {
            display: none !important;
        }
    </style>
@endsection