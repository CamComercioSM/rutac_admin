@extends('layouts.admin', ['titulo'=> 'Diagnóstico'])

@section('content')

<div class="card my-3 shadow-sm">

    <h5 class="text-center py-3 bg-light border-bottom">
        Detalle del diagnóstico
    </h5>

    <div class="p-3">
        <table class="table">
            <tr>
                <th>NIT</th> <td>{{$detalle->nit ?? ' - '}}</td>
            </tr>
            <tr>
                <th>Unidad Productiva</th> <td>{{$detalle->business_name ?? ' - '}}</td>
            </tr>
            <tr>
                <th>Etapa</th> <td>{{$detalle->etapa->name ?? ' - '}}</td>
            </tr>
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
