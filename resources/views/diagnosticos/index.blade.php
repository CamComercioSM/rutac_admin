@extends('layouts.list', ['titulo'=> 'Diagnósticos', 'tituloModal'=> 'diagnóstico'])

@section('form-fiels')
    <div class="row">

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="diagnostico_nombre">Nombre</label>
            <input type="text" class="form-control" name="diagnostico_nombre" id="diagnostico_nombre" placeholder="Nombre" required>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="diagnostico_etapa_id">Etapa</label>
            <select class="form-select" name="diagnostico_etapa_id" id="diagnostico_etapa_id">
                <option value="" disabled selected>Seleccione una opción</option>
                @foreach ($etapas as $item)
                    <option value="{{$item->etapa_id}}" >{{$item->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="diagnostico_conventas">Con ventas</label>
            <select class="form-select" name="diagnostico_conventas" id="diagnostico_conventas">
                <option value="" disabled selected>Seleccione una opción</option>
                <option value="0">No</option>
                <option value="1">Si</option>
            </select>
        </div>

    </div>
@endsection

@section('script')
    <script> 
        const TABLA = {
            urlApi: '/diagnosticos',
            sortName: 'diagnostico_nombre',
            accion_editar: true,
            columns: [
                { field: 'diagnostico_nombre', title: 'Nombre', sortable: true },
                { field: 'etapa', title: 'Etapa', sortable: true },
                { 
                    field: '', 
                    title: 'Preguntas', 
                    formatter: (value, row) => {
                        return `<a href="/diagnosticosPreguntas/list/${row.id}"><i class="ri-eye-line"></i></a>`
                    }
                }
            ]
        };
    </script>
@endsection