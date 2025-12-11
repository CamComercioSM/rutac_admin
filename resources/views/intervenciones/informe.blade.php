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
            top: -110px;
            left: 0;
            right: 0;
            height: 110px;
            text-align: center;
            padding-bottom: 10px;
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
           text-align: left;
        }

        h1, h2, h3 { margin: 0; padding: 0; color: #0e188a; }
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
            padding: 10px 5px;
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
    <table>
        <thead>
            <tr>
                <th style="padding: 5px;">
                    <img src="https://cdnsicam.net/img/rutac/rutac-logo-con-ccsm.png" width="auto" height="80">
                </th>
                <th style="padding: 5px;">
                    <h2 style="margin:0 !important; text-align: center;">Informe de Intervenciones</h2>
                    <small>Desde {{ $inicio }} hasta {{ $fin }}</small>
                </th>
            </tr>
        </thead>
    </table>
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
        <th>Categorías de Intervención</th>
        <th style="width: 100px" >Cantidad</th>
    </tr>
    </thead>
    <tbody>
    @foreach($porCategoria as $c)
        <tr>
            <td>
                <strong>{{ $c->categoria->nombre ?? 'Sin categoría' }}</strong>
            </td>
            <td style="text-align: right">{{ $c->total }}</td>
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
        <th style="width: 100px" >Cantidad</th>
    </tr>
    </thead>
    <tbody>
    @foreach($porTipo as $t)
        <tr>
            <td>
                <strong>{{ $t->tipo->nombre ?? 'Sin tipo' }}</strong>
            </td>
            <td style="text-align: right">{{ $t->total }}</td>
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
        <th style="width: 100px" >Cantidad</th>
    </tr>
    </thead>
    <tbody>
    @foreach($porUnidad as $u)
        <tr>
            <td>
                <strong>{{ $u->unidadProductiva->business_name }}</strong>
            </td>
            <td style="text-align: right">{{ $u->total }}</td>
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
        <th>Unidad Productiva / Asesor</th>
        <th>Categoría</th>
        <th>Descripción</th>
    </tr>
    </thead>
    <tbody>
    @foreach($intervenciones as $i)
        <tr>
            <td>
                {{ $i->fecha_inicio }}
            </td>
            <td>
                <strong>Unidad Productiva</strong><br>
                {{ $i->unidadProductiva->business_name ?? '' }}

                <br><br>
                <strong>Asesor</strong><br>
                {{ $i->asesor->name ?? '' }}
            </td>
            <td>
                <strong>Categoría</strong><br>
                {{ $i->categoria->nombre ?? '' }}

                <br><br>
                <strong>Tipo</strong><br>
                {{ $i->tipo->nombre ?? '' }}
            </td>
            <td>{!! $i->descripcion !!}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="page-break"></div>

<h2>Conclusiones</h2>
<p>{!! nl2br(e($conclusiones)) !!}</p>

</body>
</html>
