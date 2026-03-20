<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Informe de Actividades e intervenciones</title>
    @include('intervenciones.partials.informe-styles')

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: #ffffff;
        }

        .row {
            display: table !important;
            width: 100% !important;
            table-layout: fixed;
        }

        .col-md-4 {
            display: table-cell !important;
            width: 33.33% !important;
            vertical-align: top;
        }

        .col-md-3 {
            display: table-cell !important;
            width: 25% !important;
        }

        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }

        .card-body {
            padding: 10px !important;
            text-align: center;
        }

        .card-body h6 {
            margin-bottom: 5px;
            font-size: 12px;
        }

        .card-body h3 {
            margin: 0;
            font-size: 18px;
            line-height: 1.2;
        }


        table {
            width: 100% !important;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead {
            display: table-header-group;
        }

        th,
        td {
            word-wrap: break-word;
            overflow-wrap: break-word;
            font-size: 11px;
            padding: 6px;
        }

        a {
            word-break: break-all;
            font-size: 10px;
        }

        tr {
            page-break-inside: avoid;
        }

        .page-break {
            display: block;
            width: 100%;
            height: 1px;
            page-break-before: always;
        }
    </style>
</head>

<body>

    {{-- contenido reutilizable --}}
    @include('intervenciones.partials.informe-contenido', [
        'mostrarIA' => false,
    ])



</body>

</html>
