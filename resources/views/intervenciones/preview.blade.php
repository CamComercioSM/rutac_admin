<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
        
        th, td {
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

        #analisis-ia-loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        #analisis-ia-error {
            color: #dc3545;
            padding: 10px;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
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
            <form id="formGenerarPDF" method="POST" action="/intervenciones/informe" style="display: inline;" target="_blank">
                @csrf
                <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio') ?? request()->input('fecha_inicio') }}">
                <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin') ?? request()->input('fecha_fin') }}">
                @if(request('asesor') || request()->input('asesor'))
                    <input type="hidden" name="asesor" value="{{ request('asesor') ?? request()->input('asesor') }}">
                @endif
                @if(request('unidad') || request()->input('unidad'))
                    <input type="hidden" name="unidad" value="{{ request('unidad') ?? request()->input('unidad') }}">
                @endif
                <input type="hidden" name="conclusiones" value="{{ $conclusiones }}">
                <button type="submit" class="btn btn-primary">Generar PDF</button>
            </form>
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
                        <td colspan="4" style="text-align: center; color: #666;">No hay intervenciones en el rango de fechas seleccionado</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="page-break"></div>

        <div class="conclusiones-section">
            <h2>Conclusiones</h2>
            <p>{!! nl2br(e($conclusiones ?: 'No se han ingresado conclusiones.')) !!}</p>
        </div>

        <div id="analisis-ia-container" class="conclusiones-section" style="margin-top: 20px; display: none;">
            <h2>Análisis complementario (IA)</h2>
            <div id="analisis-ia-loading" style="text-align: center; padding: 20px; color: #666;">
                <p>Cargando análisis de IA...</p>
            </div>
            <div id="analisis-ia-content" style="display: none;"></div>
            <div id="analisis-ia-error" style="display: none; color: #dc3545; padding: 10px;"></div>
        </div>

        <div class="footer-info">
            Intervenciones - Generado el {{ date('d/m/Y H:i') }}
        </div>
    </div>

    <script>
        (function() {
            // Obtener los datos del formulario para construir el payload
            const formData = new FormData();
            formData.append('fecha_inicio', '{{ request('fecha_inicio') ?? request()->input('fecha_inicio') }}');
            formData.append('fecha_fin', '{{ request('fecha_fin') ?? request()->input('fecha_fin') }}');
            formData.append('_token', '{{ csrf_token() }}');
            @if(request('asesor') || request()->input('asesor'))
            formData.append('asesor', '{{ request('asesor') ?? request()->input('asesor') }}');
            @endif
            @if(request('unidad') || request()->input('unidad'))
            formData.append('unidad', '{{ request('unidad') ?? request()->input('unidad') }}');
            @endif
            formData.append('conclusiones', '{{ addslashes($conclusiones ?? '') }}');

            // Obtener el payload desde el backend
            fetch('/intervenciones/informe/payload-ia', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('analisis-ia-container');
                const loading = document.getElementById('analisis-ia-loading');
                const content = document.getElementById('analisis-ia-content');
                const errorDiv = document.getElementById('analisis-ia-error');

                container.style.display = 'block';

                if (!data.payload || !data.api_url) {
                    loading.style.display = 'none';
                    errorDiv.style.display = 'block';
                    errorDiv.textContent = 'No se pudo obtener el payload para el análisis.';
                    return;
                }

                // Mostrar el payload en consola para debugging
                console.log('Payload que se enviará a la API:', data.payload);
                console.log('URL de la API:', data.api_url);

                // Llamar a la API externa (esto aparecerá en Network)
                fetch(data.api_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data.payload)
                })
                .then(response => {
                    console.log('Respuesta de la API:', response);
                    return response.json();
                })
                .then(apiResponse => {
                    loading.style.display = 'none';
                    
                    if (apiResponse.RESPUESTA === 'EXITO' && apiResponse.MENSAJE) {
                        // El mensaje viene en Markdown, se renderizará como HTML
                        content.innerHTML = apiResponse.MENSAJE;
                        content.style.display = 'block';
                        
                        // Convertir Markdown básico a HTML (negritas, listas, etc.)
                        convertMarkdownToHtml(content);
                    } else {
                        errorDiv.style.display = 'block';
                        errorDiv.textContent = 'La API no devolvió un mensaje válido.';
                        console.error('Respuesta de API:', apiResponse);
                    }
                })
                .catch(error => {
                    loading.style.display = 'none';
                    errorDiv.style.display = 'block';
                    errorDiv.textContent = 'Error al llamar a la API: ' + error.message;
                    console.error('Error:', error);
                });
            })
            .catch(error => {
                const container = document.getElementById('analisis-ia-container');
                const loading = document.getElementById('analisis-ia-loading');
                const errorDiv = document.getElementById('analisis-ia-error');
                
                container.style.display = 'block';
                loading.style.display = 'none';
                errorDiv.style.display = 'block';
                errorDiv.textContent = 'Error al obtener el payload: ' + error.message;
                console.error('Error:', error);
            });

            // Función para convertir Markdown básico a HTML
            function convertMarkdownToHtml(element) {
                let html = element.innerHTML;
                
                // Primero proteger los bloques de código si los hay
                const codeBlocks = [];
                html = html.replace(/```[\s\S]*?```/g, (match) => {
                    const id = 'CODE_BLOCK_' + codeBlocks.length;
                    codeBlocks.push(match);
                    return id;
                });
                
                // Convertir **texto** a <strong>texto</strong> (negritas)
                html = html.replace(/\*\*([^*]+?)\*\*/g, '<strong>$1</strong>');
                
                // Convertir *texto* a <em>texto</em> (cursiva, solo si no está dentro de **)
                html = html.replace(/(?<!\*)\*([^*\n]+?)\*(?!\*)/g, '<em>$1</em>');
                
                // Convertir títulos ### a <h3>
                html = html.replace(/^###\s+(.+)$/gm, '<h3>$1</h3>');
                html = html.replace(/^##\s+(.+)$/gm, '<h3>$1</h3>');
                
                // Convertir listas con - o * al inicio de línea
                const lines = html.split('\n');
                let inList = false;
                let listHtml = '';
                
                lines.forEach((line, index) => {
                    const listMatch = line.match(/^[\-\*]\s+(.+)$/);
                    const numberedMatch = line.match(/^\d+\.\s+(.+)$/);
                    
                    if (listMatch || numberedMatch) {
                        if (!inList) {
                            listHtml += '<ul>';
                            inList = true;
                        }
                        listHtml += '<li>' + (listMatch ? listMatch[1] : numberedMatch[1]) + '</li>';
                    } else {
                        if (inList) {
                            listHtml += '</ul>';
                            inList = false;
                        }
                        if (line.trim()) {
                            listHtml += line + '\n';
                        }
                    }
                });
                
                if (inList) {
                    listHtml += '</ul>';
                }
                
                html = listHtml || html;
                
                // Convertir saltos de línea dobles a párrafos
                html = html.split(/\n\n+/).map(para => {
                    para = para.trim();
                    if (!para) return '';
                    if (para.startsWith('<')) return para; // Ya es HTML
                    return '<p>' + para + '</p>';
                }).join('');
                
                // Convertir saltos de línea simples a <br>
                html = html.replace(/\n/g, '<br>');
                
                // Restaurar bloques de código
                codeBlocks.forEach((block, index) => {
                    html = html.replace('CODE_BLOCK_' + index, block);
                });
                
                element.innerHTML = html;
            }
        })();
    </script>
</body>
</html>
