@extends('layouts.list', ['titulo'=> 'Requisitos convocatoria', 'tituloModal'=> 'requisito'])

@section('form-fiels')
    <div class="row">

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="requisito_titulo">Nombre</label>
            <input type="text" class="form-control" name="requisito_titulo" id="requisito_titulo" placeholder="Nombre" required>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="indicador_id">Indicador</label>
            <select class="form-select" name="indicador_id" id="indicador_id">
                <option value="" disabled selected>Seleccione una opción</option>
                @foreach ($indicadores as $item)
                    <option value="{{$item->indicador_id}}" >{{$item->indicador_nombre}}</option>
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

        <div class="col-12 col-md-12 form-group mb-3 mt-4">
            <label class="form-label" for="pregunta_porcentaje">
                Opciones
                <button type="button" class="btn btn-sm btn-primary py-1" onclick="itemOption()" >Agregar</button>
            </label>
            <table class="table table-sm table-border border">
                <thead>
                    <th>Nombre</th>
                    <th></th>     
                </thead>
                <tbody id="table_opciones"></tbody>
            </table>
        </div>

    </div>
@endsection

@section('script')
    <script> 
        const TABLA = {
            urlApi: '/convocatoriasRequisitos',
            sortName: 'requisito_titulo',
            accion_editar: true,
            columns: [
                { field: 'requisito_titulo', title: 'Nombre', sortable: true },
            ],            
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
                        <input type="hidden"
                            name="opciones[${index}][opcionrequisito_id]"
                            value="${row.opcionrequisito_id ?? ''}" />

                        <button type="button" class="btn btn-danger btn-sm" onclick="removeOption(this)" >Eliminar</button>
                    </td>
                </tr>`;
            $("#table_opciones").append(item);
        }
        
        window.removeOption = function(btn) {
            $(btn).closest("tr").remove();
        };

    </script>
@endsection