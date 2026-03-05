<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Previsualización - Informe de Intervenciones</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
            padding: 20px;
        }

        .preview-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .preview-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #0e188a;
        }

        .preview-header img {
            max-height: 80px;
            margin-bottom: 10px;
        }

        .preview-header h1 {
            color: #0e188a;
            margin: 10px 0;
            font-size: 24px;
        }

        .preview-header small {
            color: #666;
            font-size: 14px;
        }

        .preview-actions {
            position: sticky;
            top: 0;
            background: white;
            padding: 15px;
            margin: -30px -30px 30px -30px;
            border-bottom: 2px solid #0e188a;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #0e188a;
            color: white;
        }

        .btn-primary:hover {
            background: #0a1266;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .totals-box {
            background: #f8f9fa;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-left: 4px solid #0e188a;
            margin: 20px 0;
            border-radius: 4px;
        }

        .totals-box strong {
            color: #0e188a;
            font-size: 16px;
        }

        h2 {
            color: #0e188a;
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: white;
        }

        th,
        td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }

        th {
            background: #0e188a;
            color: white;
            font-weight: bold;
            text-align: left;
        }

        tr:nth-child(even) {
            background: #f8f9fa;
        }

        tr:hover {
            background: #e9ecef;
        }

        .text-right {
            text-align: right;
        }

        .page-break {
            margin: 40px 0;
            border-top: 2px dashed #dee2e6;
        }

        .conclusiones-section {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-left: 4px solid #0e188a;
            border-radius: 4px;
        }

        .conclusiones-section h2 {
            margin-top: 0;
        }

        .conclusiones-section p {
            white-space: pre-wrap;
            line-height: 1.8;
        }

        .conclusiones-section strong {
            font-weight: bold;
            color: #0e188a;
        }

        .conclusiones-section em {
            font-style: italic;
        }

        .conclusiones-section ul,
        .conclusiones-section ol {
            margin: 10px 0;
            padding-left: 30px;
            line-height: 1.8;
        }

        .conclusiones-section li {
            margin: 5px 0;
        }

        .conclusiones-section h3,
        .conclusiones-section h4 {
            color: #0e188a;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .conclusiones-section p {
            margin: 10px 0;
            line-height: 1.8;
        }

        .footer-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #666;
            font-size: 12px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .preview-container {
                box-shadow: none;
                padding: 20px;
            }

            .preview-actions {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="preview-container">
        <div class="preview-actions">
            <button type="button" class="btn btn-secondary" onclick="window.close()">Cerrar</button>
            <form id="formGenerarPDF" method="POST" action="{{ url('/intervenciones/informe') }}"
                style="display: inline;" target="_blank">
                @csrf
                <input type="hidden" name="fecha_inicio"
                    value="{{ request('fecha_inicio') ?? request()->input('fecha_inicio') }}">
                <input type="hidden" name="fecha_fin"
                    value="{{ request('fecha_fin') ?? request()->input('fecha_fin') }}">
                @if (request('asesor') || request()->input('asesor'))
                    <input type="hidden" name="asesor" value="{{ request('asesor') ?? request()->input('asesor') }}">
                @endif
                @if (request('unidad') || request()->input('unidad'))
                    <input type="hidden" name="unidad" value="{{ request('unidad') ?? request()->input('unidad') }}">
                @endif
                <input type="hidden" name="conclusiones" value="{{ $conclusiones }}">
                <button type="submit" class="btn btn-primary">Generar PDF</button>
            </form>
            <button type="button" id="btnGuardarInforme" class="btn btn-success">Guardar Reporte</button>
        </div>

        <div class="preview-header">
            <img src="https://cdnsicam.net/img/rutac/rutac-logo-con-ccsm.png" alt="Logo RUTAC">
            <h1>Informe de Intervenciones</h1>
            <small>Desde {{ $inicio }} hasta {{ $fin }}</small>
        </div>

        <div class="totals-box">
            <strong>Total de intervenciones:</strong> {{ $totalGeneral }}
        </div>

        <!-- SECCIÓN 1: CATEGORÍAS -->
        <h2>Categorías de Intervención</h2>
        <table>
            <thead>
                <tr>
                    <th>Categorías de Intervención</th>
                    <th style="width: 100px">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @forelse($porCategoria as $c)
                    <tr>
                        <td>
                            <strong>{{ $c->categoria->nombre ?? 'Sin categoría' }}</strong>
                        </td>
                        <td class="text-right">{{ $c->total }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center; color: #666;">No hay datos disponibles</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- SECCIÓN 2: TIPOS -->
        <h2>Tipos de Intervención</h2>
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th style="width: 100px">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @forelse($porTipo as $t)
                    <tr>
                        <td>
                            <strong>{{ $t->tipo->nombre ?? 'Sin tipo' }}</strong>
                        </td>
                        <td class="text-right">{{ $t->total }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center; color: #666;">No hay datos disponibles</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- SECCIÓN 3: UNIDADES PRODUCTIVAS -->
        <h2>Unidades Productivas</h2>
        <table>
            <thead>
                <tr>
                    <th>Unidad Productiva</th>
                    <th style="width: 100px">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @forelse($porUnidad as $u)
                    <tr>
                        <td>
                            <strong>{{ $u->unidadProductiva?->business_name ?? 'Sin unidad productiva' }}</strong>
                        </td>
                        <td class="text-right">{{ $u->total }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center; color: #666;">No hay datos disponibles</td>
                    </tr>
                @endforelse
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
                @forelse($intervenciones as $i)
                    <tr>
                        <td>
                            {{ $i->fecha_inicio }}
                        </td>
                        <td>
                            <strong>Unidad Productiva</strong><br>
                            {{ $i->unidadProductiva?->business_name ?? 'N/A' }}

                            <br><br>
                            <strong>Asesor</strong><br>
                            {{ $i->asesor?->name ?? 'N/A' }}
                        </td>
                        <td>
                            <strong>Categoría</strong><br>
                            {{ $i->categoria?->nombre ?? 'N/A' }}

                            <br><br>
                            <strong>Tipo</strong><br>
                            {{ $i->tipo?->nombre ?? 'N/A' }}
                        </td>
                        <td>{!! $i->descripcion ?? 'Sin descripción' !!}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #666;">No hay intervenciones en el rango de
                            fechas seleccionado</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="page-break"></div>

        <div class="conclusiones-section">
            <h2>Conclusiones</h2>
            <p>{!! nl2br(e($conclusiones ?: 'No se han ingresado conclusiones.')) !!}</p>
        </div>

        @if (!empty($analisis_ia))
            <div class="conclusiones-section" style="margin-top: 20px;">
                <h2>Análisis complementario (IA)</h2>
                <div>{!! $analisis_ia !!}</div>
            </div>
        @endif

        <div class="footer-info">
            Intervenciones - Generado el {{ date('d/m/Y H:i') }}
        </div>
    </div>
    <div class="modal fade" id="guardarInformeModal" tabindex="-1">

        <div class="modal-dialog">
            <div class="modal-content">

                <form id="formSaveInforme" action="{{ url('/intervenciones/informe') }}" method="POST">
                    @csrf

                    <input type="hidden" name="fecha_inicio"
                        value="{{ request('fecha_inicio') ?? request()->input('fecha_inicio') }}">
                    <input type="hidden" name="fecha_fin"
                        value="{{ request('fecha_fin') ?? request()->input('fecha_fin') }}">
                    @if (request('asesor') || request()->input('asesor'))
                        <input type="hidden" name="asesor"
                            value="{{ request('asesor') ?? request()->input('asesor') }}">
                    @endif
                    <input type="hidden" name="conclusiones" value="{{ $conclusiones }}">
                    <input type="hidden" name="reporte_id" id="reporte_id" value="{{ $reporte_id }}">

                    <div class="modal-header">
                        <h5 class="modal-title">Guardar informe de intervencion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="col-12 mb-3">
                            <label class="form-label">Año</label>
                            <select id="selectAnioInforme" name="anio" class="form-control" required>
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 mb-3">
                            <label class="form-label">Mes</label>
                            <select id="selectMesInforme" name="mes" class="form-control" required>
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" id="btnEnviarInforme">Guardar informe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const anioSelect = document.getElementById('selectAnioInforme');
            const mesSelect = document.getElementById('selectMesInforme');

            const fechaActual = new Date();
            const currentYear = fechaActual.getFullYear();
            const mesActual = fechaActual.getMonth();

            for (let i = currentYear; i >= currentYear - 5; i--) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = i;
                anioSelect.appendChild(option);
            }

            function llenarMeses(anioSeleccionado) {
                mesSelect.innerHTML = "<option value=''>Seleccione</option>";
                let limiteMes = 11;
                if (parseInt(anioSeleccionado) === currentYear) {
                    limiteMes = mesActual;
                }
                for (let i = 0; i <= limiteMes; i++) {
                    const fecha = new Date(anioSeleccionado, i, 1);
                    const option = document.createElement("option");
                    option.value = i + 1;
                    option.textContent = fecha
                        .toLocaleString("es-ES", {
                            month: "long"
                        })
                        .replace(/^./, c => c.toUpperCase());
                    mesSelect.appendChild(option);
                }
            }
            llenarMeses(currentYear);
            // 🔹 Detectar cambio de año
            anioSelect.addEventListener('change', function() {
                llenarMeses(this.value);
            });

        });

        $(document).ready(function() {
            $('#btnGuardarInforme').on('click', function() {
                let modal = new bootstrap.Modal(document.getElementById('guardarInformeModal'));
                modal.show();
            });
        });
    </script>
</body>

</html>
