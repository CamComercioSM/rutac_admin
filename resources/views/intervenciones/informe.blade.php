<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; line-height: 1.4; }

        @page {
            margin-top: 130px;
            margin-bottom: 50px;
        }

        header {
            position: fixed;
            top: -130px;
            left: 0;
            right: 0;
            height: 120px;
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 2px solid #555;
        }

        footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        h2{
           margin-top: 20px !important;
        }

        h1, h2, h3 { text-align: center; margin: 0; padding: 0; }
        .section-title { background: #eee; padding: 6px; font-weight: bold; margin-top: 20px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #444;
            padding: 5px;
        }

        th {
            background: #f5f5f5;
            font-weight: bold;
        }

        .page-break {
            page-break-after: always;
        }

        .totals-box {
            background: #fafafa;
            padding: 10px;
            border: 1px solid #ccc;
            margin-top: 20px !important;
        }
    </style>
</head>

<body>

<header>
    <img src="https://cdnsicam.net/img/rutac/rutac-logo-con-ccsm.png" width="auto" height="80">
    <h2 style="margin:0 !important;">Informe de Intervenciones</h2>
    <small>Desde {{ $inicio }} hasta {{ $fin }}</small>
</header>

<footer>
    Intervenciones - Generado el {{ date('d/m/Y H:i') }}
</footer>


<div class="totals-box">
    <strong>Total de intervenciones:</strong> {{ $totalGeneral }}
</div>

<!-- SECCIÓN 1: CATEGORÍAS -->
<h2>Categorías de Intervención</h2>
<table>
    <thead>
    <tr>
        <th>Categoría</th>
        <th>Cantidad</th>
    </tr>
    </thead>
    <tbody>
    @foreach($porCategoria as $c)
        <tr>
            <td>{{ $c->categoria->nombre ?? 'Sin categoría' }}</td>
            <td>{{ $c->total }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<!-- SECCIÓN 2: TIPOS -->
<h2>Tipos de Intervención</h2>
<table>
    <thead>
    <tr>
        <th>Tipo</th>
        <th>Cantidad</th>
    </tr>
    </thead>
    <tbody>
    @foreach($porTipo as $t)
        <tr>
            <td>{{ $t->tipo->nombre ?? 'Sin tipo' }}</td>
            <td>{{ $t->total }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<!-- SECCIÓN 3: UNIDADES PRODUCTIVAS -->
<h2>Unidades Productivas</h2>
<table>
    <thead>
    <tr>
        <th>Unidad Productiva</th>
        <th>Cantidad</th>
    </tr>
    </thead>
    <tbody>
    @foreach($porUnidad as $u)
        <tr>
            <td>{{ $u->unidadProductiva->business_name }}</td>
            <td>{{ $u->total }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="page-break"></div>

<!-- LISTADO DETALLADO -->
<h2>Listado Detallado de Intervenciones</h2>

<table>
    <thead>
    <tr>
        <th>Fecha</th>
        <th>Unidad Productiva</th>
        <th>Asesor</th>
        <th>Categoría</th>
        <th>Tipo</th>
        <th>Descripción</th>
    </tr>
    </thead>
    <tbody>
    @foreach($intervenciones as $i)
        <tr>
            <td>{{ $i->fecha_inicio }}</td>
            <td>{{ $i->unidadProductiva->business_name ?? '' }}</td>
            <td>{{ $i->asesor->name ?? '' }}</td>
            <td>{{ $i->categoria->nombre ?? '' }}</td>
            <td>{{ $i->tipo->nombre ?? '' }}</td>
            <td>{{ $i->descripcion }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
