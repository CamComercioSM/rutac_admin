@extends('layouts.list', ['titulo'=> 'Inscripciones', 'tituloModal'=> 'Inscripción'])

@section('btns-actions')
    <button class="btn btn-primary" id="btnCambioEstado">
        Cambio de estado
    </button>
@endsection

@section('form-filters')

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="programa">Programa</label>
        <select class="form-select" name="programa" id="programa" required >
            <option value="" selected >Seleccione una opción</option>
            @foreach ($programas as $item)
                <option value="{{$item->programa_id}}" >{{$item->nombre}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="convocatoria">Convocatoria</label>
        <select class="form-select" name="convocatoria" id="convocatoria" required >
            <option value="" selected >Seleccione una opción</option>
            @foreach ($convocatorias as $item)
                <option value="{{$item->convocatoria_id}}" data-programa="{{$item->programa_id}}" >{{$item->nombre_convocatoria}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="estado">Estado inscripción</label>
        <select class="form-select" name="estado" id="estado">
            <option value="" selected >Seleccione una opción</option>
            @foreach ($estados as $item)
                <option value="{{$item->inscripcionestado_id}}" >{{$item->inscripcionEstadoNOMBRE}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-6 form-group mb-3">
        <label class="form-label" for="unidad">Unidad productiva</label>
        <select class="form-select" name="unidad" id="unidad">
            <option value="" selected >Seleccione una opción</option>
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
                <option value="" selected >Seleccione una opción</option>
                @foreach ($programas as $item)
                    <option value="{{$item->programa_id}}" >{{$item->nombre}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-12 form-group mb-5">
            <label class="form-label" for="convocatoriaAdd">Convocatoria</label>
            <select class="form-select" name="convocatoriaAdd" id="convocatoriaAdd" required >
                <option value="" selected >Seleccione una opción</option>
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
                        <label class="form-label">Unidades productivas seleccionadas (<span id="cantidad"></span>) </label>
                        <ul id="listaUnidades" class="list-group mb-3"></ul>
                    </div>
                
                    <div class="col-sm-12 mb-3">
                        <label class="form-label" for="inscripcionestado_id">Estado</label>
                        <select id="inscripcionestado_id" name="inscripcionestado_id" class="form-select form-select-sm">
                            <option value="" selected >Seleccione una opción</option>
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
                            <option value="">Seleccione una opción</option>
                            <option value="0" selected>No</option>
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
@endsection

@section('script')
    <script> 
        window.TABLA = {
            urlApi: '/inscripciones',
            sortName: 'fecha_creacion',
            menu_row: `<a class="dropdown-item" href="/inscripciones/ROWID" >Ver detalles</a>`,
            checkboxes: true,
            columns: [
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
                { 
                    id:'programa',
                    change: function(e) {
                        // Evitar actualización si viene desde convocatoria
                        if (window.updatingFromConvocatoria) {
                            return;
                        }
                        
                        // Cuando cambia el programa, actualizar las convocatorias
                        const programaId = $("#programa").val();
                        
                        if (programaId) {
                            // Cargar convocatorias del programa seleccionado
                            $.ajax({
                                url: '/api/convocatorias/by-programa/' + programaId,
                                type: 'GET',
                                dataType: 'json',
                                success: function(response) {
                                    if (response.success) {
                                        // Guardar convocatoria seleccionada actualmente si existe y pertenece al programa
                                        const currentConvocatoriaId = $("#convocatoria").val();
                                        let shouldKeepCurrent = false;
                                        
                                        // Limpiar y actualizar opciones de convocatorias
                                        const convocatoriaSelect = $("#convocatoria");
                                        convocatoriaSelect.empty();
                                        convocatoriaSelect.append('<option value="">Seleccione una opción</option>');
                                        
                                        response.data.forEach(function(conv) {
                                            convocatoriaSelect.append(
                                                '<option value="' + conv.convocatoria_id + '" data-programa="' + conv.programa_id + '">' + conv.nombre_convocatoria + '</option>'
                                            );
                                            
                                            // Si la convocatoria actual pertenece a este programa, mantenerla seleccionada
                                            if (currentConvocatoriaId == conv.convocatoria_id) {
                                                shouldKeepCurrent = true;
                                            }
                                        });
                                        
                                        // Reinicializar select2 para convocatorias
                                        if (shouldKeepCurrent && currentConvocatoriaId) {
                                            convocatoriaSelect.val(currentConvocatoriaId).trigger('change');
                                        } else {
                                            convocatoriaSelect.val(null).trigger('change');
                                        }
                                        convocatoriaSelect.select2();
                                    }
                                },
                                error: function(xhr) {
                                    console.error('Error al cargar convocatorias:', xhr);
                                }
                            });
                        } else {
                            // Si no hay programa seleccionado, mostrar todas las convocatorias
                            const convocatoriaSelect = $("#convocatoria");
                            const currentConvocatoriaId = convocatoriaSelect.val();
                            
                            convocatoriaSelect.empty();
                            convocatoriaSelect.append('<option value="">Seleccione una opción</option>');
                            
                            // Restaurar todas las convocatorias originales
                            if (window.allConvocatorias) {
                                window.allConvocatorias.forEach(function(conv) {
                                    convocatoriaSelect.append(
                                        '<option value="' + conv.convocatoria_id + '" data-programa="' + conv.programa_id + '">' + conv.nombre_convocatoria + '</option>'
                                    );
                                });
                                
                                // Mantener la convocatoria seleccionada si existe
                                if (currentConvocatoriaId) {
                                    convocatoriaSelect.val(currentConvocatoriaId);
                                }
                            }
                            
                            convocatoriaSelect.trigger('change');
                            convocatoriaSelect.select2();
                        }
                    }
                }, 
                { 
                    id:'convocatoria',
                    change: function(e) {
                        // Cuando cambia la convocatoria, actualizar el programa
                        const convocatoriaId = $("#convocatoria").val();
                        
                        if (convocatoriaId && !window.updatingFromConvocatoria) {
                            const selectedOption = $("#convocatoria option:selected");
                            const programaId = selectedOption.data('programa');
                            
                            if (programaId) {
                                // Bandera para evitar bucles infinitos
                                window.updatingFromConvocatoria = true;
                                
                                // Actualizar el select de programa sin disparar su evento change
                                const programaSelect = $("#programa");
                                const currentProgramaValue = programaSelect.val();
                                
                                // Solo actualizar si es diferente
                                if (currentProgramaValue !== programaId) {
                                    programaSelect.val(programaId).trigger('change.select2');
                                }
                                
                                // Resetear la bandera después de un momento
                                setTimeout(function() {
                                    window.updatingFromConvocatoria = false;
                                }, 300);
                            }
                        }
                    }
                }, 
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
                Swal.fire({ title: "El registro ya existe en la tabla.", icon: "info" });
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
            if( $("#table_opciones tr").length  == 0)
            {
                Swal.fire({ title: "Agregar por lo menos una unidad productiva", icon: "info" });
                return false;
            }

            return true;
        }

        document.addEventListener('DOMContentLoaded', function () {
            
            // Guardar todas las convocatorias originales para restaurar cuando se deseleccione el programa
            window.allConvocatorias = [];
            $("#convocatoria option").each(function() {
                if ($(this).val()) {
                    window.allConvocatorias.push({
                        convocatoria_id: $(this).val(),
                        nombre_convocatoria: $(this).text(),
                        programa_id: $(this).data('programa') || ''
                    });
                }
            });

            const cargando = document.querySelectorAll('.cargando')[0];

            $('#cambioEstadoForm').on('submit', function (e) {
                e.preventDefault();

                if (window.TABLA.seleccionados.size === 0) {
                    return alert('Debe seleccionar al menos un registro.');
                }

                cargando.classList.remove('d-none');

                let form = $(this); 
                let formEl = this; 

                let method = form.attr('method'); 
                let actionUrl = form.attr('action');

                let formData = new FormData(formEl);

                for(let nb in window.TABLA.filtrosCampos)
                {
                    formData.append('filtros['+nb+']', window.TABLA.filtrosCampos[nb])
                }

                $.ajax({
                    type: method,
                    url: actionUrl,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {

                        $(".modal").modal('hide');
                        cargando.classList.add('d-none');
                        
                        Swal.fire({ title: "Cambio de estado guardado exitosamente", icon: "success" })
                        .then((result) => {
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

            $('#btnCambioEstado').on('click', function () {

                if (window.TABLA.seleccionados.size === 0) {
                    return alert('Debe seleccionar al menos un registro.');
                }

                pintarSeleccionados();

                const modal = new bootstrap.Modal(document.getElementById('cambioEstadoModal'));
                modal.show();
            });

            window.pintarSeleccionados = function ()
            {
                $('#listaUnidades').empty();
               
                $('#cantidad').text(window.TABLA.seleccionados.size);
                window.TABLA.seleccionados
                .forEach((nombre, id) => {
                    $('#listaUnidades').append(`
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-warning">
                            <input type="hidden" name="inscripciones[]" value="${id}"> 
                            ${nombre}
                            <input type="button" class="btn btn-xs btn-danger" value="Eliminar" onClick="eliminarSeleccionado(${id}); pintarSeleccionados();" > 
                        </li>`);
                });
            }

        });

    </script>

    <style>
        .select2-container--default .select2-results__option--disabled
        {
            display: none !important;
        }
    </style>
@endsection