@extends('layouts.list', ['titulo'=> 'Convocatorias', 'tituloModal'=> 'convocatoria', 'exportar'=> $puedeExportar])

@section('form-filters')

    <div class="col-12 col-md-3 form-group mb-3">
        <label class="form-label" for="programa">Programa</label>
        <select class="form-select" name="programa" id="programa">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($programas as $item)
                <option value="{{$item->programa_id}}" >{{$item->nombre}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-3 form-group mb-3">
        <label class="form-label" for="sector">Sector</label>
        <select class="form-select" name="sector" id="sector">
            <option value="" disabled selected>Seleccione una opción</option>
            @foreach ($sectores as $item)
                    <option value="{{$item->sector_id}}" >{{$item->sectorNOMBRE}}</option>
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

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="programa_id">Programa</label>
            <select class="form-select" name="programa_id" id="programa_id">
                <option value="" disabled selected>Seleccione una opción</option>
                @foreach ($programas as $item)
                    <option value="{{$item->programa_id}}" >{{$item->nombre}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="nombre_convocatoria">Nombre </label>
            <input type="text" class="form-control" name="nombre_convocatoria" id="nombre_convocatoria" placeholder="Nombre " required>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="persona_encargada">Persona a cargo</label>
            <input type="text" class="form-control" name="persona_encargada" id="persona_encargada" placeholder="Persona a cargo" required>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="correo_contacto" >Correo de contacto</label>
            <input type="email" class="form-control" name="correo_contacto" id="correo_contacto" placeholder="Correo de contacto" required>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="telefono">Teléfono de contacto</label>
            <input type="text" class="form-control" name="telefono" id="telefono" placeholder="Teléfono de contacto" required>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="fecha_apertura_convocatoria">Fecha de inicio</label>
            <input type="date" class="form-control" name="fecha_apertura_convocatoria" id="fecha_apertura_convocatoria" placeholder="Fecha de inicio" required>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="fecha_cierre_convocatoria">Fecha de finalización</label>
            <input type="date" class="form-control" name="fecha_cierre_convocatoria" id="fecha_cierre_convocatoria" placeholder="Fecha de finalización" required>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="sector_id">Sector</label>
            <select class="form-select" name="sector_id" id="sector_id">
                <option value="" disabled selected>Seleccione una opción</option>
                @foreach ($sectores as $item)
                    <option value="{{$item->sector_id}}" >{{$item->sectorNOMBRE}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="con_matricula">Con matricula</label>
            <select class="form-select" name="con_matricula" id="con_matricula">
                <option value="" disabled selected>Seleccione una opción</option>
                <option value="0">No</option>
                <option value="1">Si</option>
            </select>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="asesores">Asesores</label>
            <select class="form-select" name="asesores[]" id="asesores" multiple >
                @foreach ($asesores as $item)
                    <option value="{{$item->id}}" >{{$item->name}} {{$item->lastname}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-12 form-group mb-3 mt-4">
            <h3 class="mb-0" for="pregunta_porcentaje">
                Preguntas  <button type="button" class="btn btn-sm btn-primary py-1" onclick="openAdd()" >Agregar</button>
            </h3>
            <div class="mb-2">
                <select class="form-select w-75" name="pregunta" id="pregunta" >
                    <option value="" disabled selected>Seleccione una opción para agregar</option>
                    @foreach ($preguntas as $item)
                        <option value="{{$item->requisito_id}}" >{{$item->requisito_titulo}}</option>
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
            urlApi: '/convocatorias',
            sortName: 'convocatoria_id',
            
            menu_row: ` <a class="dropdown-item" onClick="openEditar()" >Editar</a>
                        <a class="dropdown-item" href="/convocatorias/ROWID" >Ver detalles</a>
                        <a class="dropdown-item" href="/inscripciones/list?convocatoria=ROWID">Inscripciones</a>`,

            columns: [
                { field: 'nombre_programa', title: 'Programa', sortable: true },
                { field: 'nombre_convocatoria', title: 'Nombre', sortable: true },
                { field: 'persona_encargada', title: 'Persona a cargo', sortable: true },
                { field: 'telefono', title: 'Teléfono', sortable: true },
                { field: 'fecha_apertura_convocatoria', title: 'Fecha inicio', sortable: true, formatter: 'formatearFecha' },
                { field: 'fecha_cierre_convocatoria', title: 'Fecha finalización', sortable: true, formatter: 'formatearFecha' }
            ],

            initSelects: [ 
                { id:'programa'}, { id:'pregunta'}, 
                { id:'asesores', setting:{ placeholder: 'Selección multiple'}  },                
            ],

            initFiltros: @json($filtros),

            loadOptions: function(opciones) 
            {
                $("#table_opciones").html('');

                for(let i = 0; i< opciones.length; i++){
                    window.itemOption(opciones[i]);
                }
            }
        };

        window.openAdd = function() 
        {
            const id = $("#pregunta").val();
            const text = $("#pregunta option:selected").text();

            if( !(id && text) ) return;


            let existe = $("#table_opciones tr[data-id='" + id + "']").length > 0;
            if (existe) {
                let toastEl = document.getElementById('warningToast');
                let toast = new bootstrap.Toast(toastEl, { delay: 2000 });
                toast.show();
                return;
            }


            window.itemOption({ requisito_id: id, requisito_titulo: text });

            $("#pregunta").val(null).trigger('change');

        }

        window.itemOption = function(row={}) 
        {
            const index = $("#table_opciones tr").length;

            const item = `
                <tr data-id="${row.requisito_id}" >
                    <td> ${row.requisito_titulo} </td>
                    <td style="width: 80px;" >
                        <input type="hidden" name="requisitos[${index}]" value="${row.requisito_id}" />

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

    </script>
@endsection