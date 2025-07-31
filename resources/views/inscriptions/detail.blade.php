@extends('layouts.admin', ['titulo'=> 'Inscripción'])

@section('content')

<div class="card my-3 shadow-sm">

    <h5 class="text-center py-3 bg-light border-bottom">
        Detalle de inscripción
    </h5>

    <div class="p-3">
        <table class="table">
            <tr>
                <th>Convocatoria</th> <td>{{$detalle->convocatoria->nombre_convocatoria ?? ' - '}}</td>
            </tr>
            <tr>
                <th>Programa</th> <td>{{$detalle->convocatoria->programa->nombre ?? ' - '}}</td>
            </tr>
            <tr>
                <th>NIT</th> <td>{{$detalle->unidadProductiva->nit ?? ' - '}}</td>
            </tr>
            <tr>
                <th>Unidad Productiva</th> <td>{{$detalle->unidadProductiva->business_name ?? ' - '}}</td>
            </tr>
            <tr>
                <th>Estado</th> <td>{{$detalle->estado->inscripcionEstadoNOMBRE ?? ' - '}}</td>
            </tr>
            <tr>
                <th>Fecha de inscripción</th> <td>{{$detalle->fecha_creacion ?? ' - '}}</td>
            </tr>
            <tr>
                <th>Comentarios</th> <td>{{$detalle->comentarios ?? ' - '}}</td>
            </tr>
            <tr>
                <th>Archivo</th> <td>{{$detalle->archivo ?? ' - '}}</td>
            </tr>
        </table>
    </div>

</div>

<div class="card my-3 shadow-sm">

    <h5 class="text-center py-3 bg-light border-bottom">
        Respuestas inscripción
    </h5>
    
    <div class="p-3">
        <div id="toolbarRespuestas">
            <button class="btn btn-info" type="button" onclick="exportarTabla('respuestasIncripcion')">
                <i class="bi bi-cloud-download"></i> Exportar
            </button>
        </div>    
        
        <table id="respuestasIncripcion" class="table table-striped">
            <thead>
                <th>Pregunta</th>
                <th>Respuesta</th>
            </thead>
            <tbody>
                @foreach ($detalle->respuestas as $item)
                    <tr>
                        <td>{{$item->requisito->requisito_titulo ?? ' - '}}</td>
                        <td>{{$item->value ?? ' - '}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

<div class="card my-3 shadow-sm">

    <h5 class="text-center py-3 bg-light border-bottom">
        Historial de cambios de estado
    </h5>
    
    <div class="p-3">
        <div id="toolbarHistorial">
            <button class="btn btn-info" type="button" onclick="exportarTabla('cambiosEstados')">
                <i class="bi bi-cloud-download"></i> Exportar
            </button>
        </div>
        <table id="cambiosEstados" class="table table-striped">
            <thead>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Comentarios</th>
            </thead>
            <tbody>
                @foreach ($detalle->historial as $item)
                    <tr>
                        <td>{{$item->fecha_creacion ?? ' - '}}</td>
                        <td>{{$item->estado->inscripcionEstadoNOMBRE ?? ' - '}}</td>
                        <td>{{$item->comentarios ?? ' - '}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
</div>

@endsection

@section('scripts')
    <link rel="stylesheet" href="/libs/bootstrap-table/bootstrap-table.min.css">
    <script src="/libs/bootstrap-table/bootstrap-table.min.js"></script>
    <script src="/libs/bootstrap-table/tableExport.min.js"></script>
    <script src="/libs/bootstrap-table/bootstrap-table-export.min.js"></script>
    <script src="/libs/bootstrap-table/bootstrap-table-es-ES.min.js"></script>
    <script>

        $.extend($.fn.bootstrapTable.locales['es-ES'], {
            formatShowingRows: function (from, to, total) { return `Visualizando ${from}–${to} de ${total}.`; },
        });

        $.extend($.fn.bootstrapTable.defaults, $.fn.bootstrapTable.locales['es-ES']);
        
        $("#respuestasIncripcion").bootstrapTable({
            toolbar: '#toolbarRespuestas',
            locale: 'es-ES',
            pagination: true,
            search: true,
            pageSize: 5,
            pageList: [5, 10, 20, 50, 100]
        });

        $("#cambiosEstados").bootstrapTable({
            toolbar: '#toolbarHistorial',
            locale: 'es-ES',
            pagination: true,
            search: true,
            pageSize: 5,
            pageList: [5, 10, 20, 50, 100]
        });

        function exportarTabla(tabla)
        {
            $('#'+tabla).bootstrapTable('refreshOptions', {exportDataType: 'all'}); 
            $('#'+tabla).tableExport({type: 'excel', fileName: tabla});
        }

        $('.cargando').addClass('d-none');
    </script>
@endsection
