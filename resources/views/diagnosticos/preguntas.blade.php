@extends('layouts.list', ['titulo'=> 'Preguntas diagnóstico', 'tituloModal'=> 'pregunta'])

@section('info-header')
    @if ($diagnostico)
    <table class="table table-sm table-border border mt-3">
        <tr>
            <td colspan="2"> <b>Nombre: </b> {{$diagnostico->diagnostico_nombre}} </td>
        </tr>
        <tr>
            <td> <b>Etapa: </b> {{$diagnostico->etapa->name ?? ' - '}} </td>
            <td> <b>Ventas: </b> {{$diagnostico->diagnostico_conventas ? 'SI' : 'NO'}} </td>
        </tr>
    </table>
    @endif
@endsection

@section('form-fields')
    <div class="row">

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="pregunta_titulo">Nombre</label>
            <input type="text" class="form-control" name="pregunta_titulo" id="pregunta_titulo" placeholder="Nombre" required>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="preguntagrupo_id">Grupo</label>
            <select class="form-select" name="preguntagrupo_id" id="preguntagrupo_id">
                <option value="" disabled selected>Seleccione una opción</option>
                @foreach ($grupos as $item)
                    <option value="{{$item->preguntagrupo_id}}" >{{$item->preguntagrupo_nombre}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="preguntadimension_id">Dimensión</label>
            <select class="form-select" name="preguntadimension_id" id="preguntadimension_id">
                <option value="" disabled selected>Seleccione una opción</option>
                @foreach ($dimensiones as $item)
                    <option value="{{$item->preguntadimension_id}}" >{{$item->preguntadimension_nombre}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="preguntatipo_id">Tipo</label>
            <select class="form-select" name="preguntatipo_id" id="preguntatipo_id">
                <option value="" disabled selected>Seleccione una opción</option>
                @foreach ($tipos as $item)
                    <option value="{{$item->preguntatipo_id}}" >{{$item->preguntatipo_nombre}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="pregunta_porcentaje">
                Nivel de porcentaje
                <small>(Recuerde que la sumataria de los niveles de todas preguntas de la misma dimension debe ser 100%)</small>
            </label>
            <input type="number" class="form-control" name="pregunta_porcentaje" id="pregunta_porcentaje" placeholder="porcentaje" required>
        </div>

        <div class="col-12 col-md-12 form-group mb-3 mt-4">
            <label class="form-label" for="pregunta_porcentaje">
                Opciones
                <button type="button" class="btn btn-sm btn-primary py-1" onclick="itemOption()" >Agregar</button>
            </label>
            <table class="table table-sm table-border border">
                <thead>
                    <th>Nombre</th>
                    <th style="width: 200px;" >Porcentaje</th>
                    <th style="width: 80px;" ></th>     
                </thead>
                <tbody id="table_opciones"></tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script> 
        window.TABLA = {
            urlApi: '/diagnosticosPreguntas',
            sortName: 'pregunta_titulo',
            accion_editar: true,
            columns: [
                { data: 'pregunta_titulo', title: 'Nombre', sortable: true },
                { data: 'pregunta_porcentaje', title: 'Porcentaje ', sortable: true }
            ],
            paramsExtra: { diagnostico: '{{$diagnostico->diagnostico_id ?? ''}}' },
            
            loadOptions: function(opciones) 
            {
                $("#table_opciones").html('');

                for(let i = 0; i< opciones.length; i++){
                    window.itemOption(opciones[i]);
                }
            }
        };

        window.itemOption = function(row={}) 
        {
            const index = $("#table_opciones tr").length;

            const item = `
                <tr>
                    <td>
                        <input type="text" class="form-control"
                            name="opciones[${index}][opcion_variable_response]"
                            value="${row.opcion_variable_response ?? ''}" placeholder="Nombre" />
                    </td>
                    <td>
                        <input type="number" class="form-control"
                            name="opciones[${index}][opcion_percentage]"
                            value="${row.opcion_percentage ?? ''}" placeholder="Porcentaje" />
                    </td>
                    <td>
                        <input type="hidden"
                            name="opciones[${index}][opcion_id]"
                            value="${row.opcion_id ?? ''}" />

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