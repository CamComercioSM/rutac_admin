@extends('layouts.list', ['titulo'=> 'Intervenciones', 'tituloModal'=> 'intervención'])

@section('form-filters')

    <div class="col-12 col-md-6 form-group mb-3">
        <label class="form-label" for="unidad">Unidad productiva</label>
        <select class="form-select" name="unidad" id="unidad">
            <option value="" selected >Seleccione una opción</option>
            @foreach ($unidades as $item)
                <option value="{{$item->unidadproductiva_id}}" >{{$item->business_name}}</option>
            @endforeach
        </select>
    </div>

    @if (!$esAsesor)
        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="asesor">Asesor</label>
            <select class="form-select" name="asesor" id="asesor">
                <option value="" selected >Seleccione una opción</option>
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
            <label class="form-label" for="categoria_id">Categoría</label>
            <select class="form-select" name="categoria_id" id="categoria_id" required>
                <option value="" selected >Seleccione una opción</option>
                @foreach ($categorias as $item)
                    <option value="{{$item->id}}" >{{$item->nombre}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="tipo_id">Tipo</label>
            <select class="form-select" name="tipo_id" id="tipo_id" required>
                <option value="" selected >Seleccione una opción</option>
                @foreach ($tipos as $item)
                    <option value="{{$item->id}}" >{{$item->nombre}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-12 form-group mb-3 d-none" id="cont_referencia">
            <label for="referencia_id" class="form-label"></label>
            <select class="form-select w-75" name="referencia_id" id="referencia_id" ></select>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="modalidad">Modalidad</label>
            <select class="form-select" name="modalidad" id="modalidad" required>
                <option value="" selected >Seleccione una opción</option>
                @foreach ($modalidades as $index => $item)
                    <option value="{{$index}}" >{{$item}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="fecha_inicio">Fecha inicio</label>
            <input type="datetime-local" class="form-control" name="fecha_inicio" id="fecha_inicio" placeholder="Fecha inicio" required>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="fecha_fin">Fecha fin</label>
            <input type="datetime-local" class="form-control" name="fecha_fin" id="fecha_fin" placeholder="Fecha fin" required>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="descripcion" >Descripción</label>
            <textarea class="form-control" name="descripcion" id="descripcion" rows="7" placeholder="Descripción"></textarea>
        </div>

        <div class="col-12 col-md-12 form-group mb-4">

            <h4 class="mb-0"> Unidades productivas  intervenidas </h4>

            <div class="row">
                
                <div class="col-12 col-md-6 form-group mb-3" >
                    <label for="unidadAdd" class="form-label">Unidad productiva</label>
                    <select class="form-select w-75" name="unidadAdd" id="unidadAdd" >
                        <option value="" disabled selected>Seleccione una unidad para agregar</option>
                        @foreach ($unidades as $item)
                            <option value="{{$item->id}}" >{{$item->nombre}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3 form-group mb-3" >
                    <label for="participantes" class="form-label">Cantidad de participantes</label>
                    <input class="form-control" type="number" id="participantes" name="participantes" placeholder="Cantidad de participantes" >
                </div>

                <div class="col-12 col-md-3 form-group mb-3 pt-5">
                    <button type="button" class="btn btn-xl btn-primary py-1 mt-3" onclick="openAdd()" >Agregar</button>
                </div>
            </div>

            <table class="table table-sm table-border border">
                <thead>                    
                    <th> Nombre </th>
                    <th> # paricipantes </th>           
                    <th></th>  
                </thead>
                <tbody id="table_opciones"></tbody>
            </table>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="conclusiones" >Conclusiones</label>
            <textarea class="form-control" name="conclusiones" id="conclusiones" rows="7" placeholder="Conclusiones"></textarea>
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
            accion_editar: false,
            columns: [
                { data: 'categoria', title: 'Categoría', orderable: true },
                { data: 'tipo', title: 'Tipo', orderable: true },
                { data: 'modalidad', title: 'Modalidad', orderable: true },
                { data: 'fecha_inicio', title: 'F. inicio', orderable: true },
                { data: 'fecha_fin', title: 'F. fin', orderable: true },
                { data: 'unidad', title: 'Unidad productiva', orderable: true },
                { data: 'participantes', title: 'Participantes', orderable: true },
                { data: 'asesor', title: 'Asesor', orderable: true },
                { data: 'descripcion', title: 'Descripción', orderable: false },
                { data: 'conclusiones', title: 'Conclusiones', orderable: false },
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
            const participantes = $("#participantes").val();

            if( !(id && text && participantes) ) return;

            let existe = $("#table_opciones tr[data-id='" + id + "']").length > 0;
            if (existe) {
                Swal.fire({ title: "Elemento ya existe", icon: "info" });
                return;
            }

            window.itemOption({id: id, text: text, participantes: participantes});

            $("#unidadAdd").val(null).trigger('change');
            $("#participantess").val(null);
        }

        window.itemOption = function(row={}) 
        {
            const index = $("#table_opciones tr").length;

            const item = `
                <tr data-id="${row.id}" >
                    <td> ${row.text} </td>        
                    <td> ${row.participantes} </td>                    
                    <td style="width: 80px;" >
                        <input type="hidden" name="unidades[${index}][unidadproductiva_id]" value="${row.id}" />
                        <input type="hidden" name="unidades[${index}][participantes]" value="${row.participantes}" />
                        
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

            $("#categoria_id").on("change", function() {
                let categoria_id = $(this).val();
                $("#cont_referencia").addClass('d-none');

                if(categoria_id == 1)
                {
                    $("#cont_referencia label").text('Convocatoria (Seleccione una opción)');
                    
                    $("#cont_referencia select").select2({ ajax: { url: '/convocatorias/search', delay: 300 }, minimumInputLength: 3, });

                    $("#cont_referencia").removeClass('d-none');
                }
                 
            });

        });

    </script>
@endsection