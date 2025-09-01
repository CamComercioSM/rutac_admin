<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio de Estado - Inscripción</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #6f42c1;
        }
        .header h1 {
            color: #6f42c1;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 10px 0 0 0;
        }
        .content {
            margin-bottom: 30px;
        }
        .info-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #6f42c1;
        }
        .info-section h3 {
            color: #6f42c1;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }
        .info-label {
            font-weight: bold;
            min-width: 150px;
            color: #555;
        }
        .info-value {
            flex: 1;
            color: #333;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }
        .status-admitido {
            background-color: #d4edda;
            color: #155724;
        }
        .status-no-admitido {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-en-proceso {
            background-color: #fff3cd;
            color: #856404;
        }
        .comments-section {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .comments-section h4 {
            margin-top: 0;
            color: #495057;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        .attachment-info {
            background-color: #d1ecf1;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 3px solid #17a2b8;
        }
        .attachment-info strong {
            color: #0c5460;
        }
        @media (max-width: 600px) {
            .info-row {
                flex-direction: column;
            }
            .info-label {
                min-width: auto;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Cambio de Estado - Inscripción</h1>
            <p>Sistema de Gestión RUTAC</p>
        </div>

        <div class="content">
            <p>Estimado/a <strong>{{ $unidadProductiva->contact_person ?? $unidadProductiva->name_legal_representative ?? 'Representante' }}</strong>,</p>
            
            <p>Le informamos que se ha realizado un cambio en el estado de su inscripción al programa. A continuación, encontrará los detalles:</p>

            <div class="info-section">
                <h3>📋 Información de la Inscripción</h3>
                <div class="info-row">
                    <span class="info-label">Unidad Productiva:</span>
                    <span class="info-value">{{ $unidadProductiva->business_name ?? 'No especificado' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">NIT:</span>
                    <span class="info-value">{{ $unidadProductiva->nit ?? 'No especificado' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Programa:</span>
                    <span class="info-value">{{ $inscripcion->convocatoria->programa->nombre ?? 'No especificado' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Convocatoria:</span>
                    <span class="info-value">{{ $inscripcion->convocatoria->nombre_convocatoria ?? 'No especificado' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha de Inscripción:</span>
                    <span class="info-value">{{ $inscripcion->fecha_creacion ? \Carbon\Carbon::parse($inscripcion->fecha_creacion)->format('d/m/Y H:i') : 'No especificada' }}</span>
                </div>
            </div>

            <div class="info-section">
                <h3>🔄 Cambio de Estado</h3>
                <div class="info-row">
                    <span class="info-label">Nuevo Estado:</span>
                    <span class="info-value">
                        <span class="status-badge 
                            @if(str_contains(strtolower($inscripcion->estado->inscripcionEstadoNOMBRE ?? ''), 'admitido'))
                                status-admitido
                            @elseif(str_contains(strtolower($inscripcion->estado->inscripcionEstadoNOMBRE ?? ''), 'no admitido') || str_contains(strtolower($inscripcion->estado->inscripcionEstadoNOMBRE ?? ''), 'rechazado'))
                                status-no-admitido
                            @else
                                status-en-proceso
                            @endif
                        ">
                            {{ $inscripcion->estado->inscripcionEstadoNOMBRE ?? 'No especificado' }}
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">¿Activar preguntas nuevamente?:</span>
                    <span class="info-value">{{ $inscripcion->activarPreguntas ? 'Sí' : 'No' }}</span>
                </div>
            </div>

            @if($inscripcion->comentarios)
            <div class="comments-section">
                <h4>💬 Comentarios Adicionales</h4>
                <p>{{ $inscripcion->comentarios }}</p>
            </div>
            @endif

            @if($inscripcion->archivo)
            <div class="attachment-info">
                <strong>📎 Archivo Adjunto:</strong> Se ha adjuntado un archivo relacionado con este cambio de estado.
            </div>
            @endif

            <div class="info-section">
                <h3>📞 Información de Contacto</h3>
                <div class="info-row">
                    <span class="info-label">Persona de Contacto:</span>
                    <span class="info-value">{{ $unidadProductiva->contact_person ?? 'No especificado' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Teléfono:</span>
                    <span class="info-value">{{ $unidadProductiva->contact_phone ?? $unidadProductiva->telephone ?? 'No especificado' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $unidadProductiva->contact_email ?? $unidadProductiva->registration_email ?? 'No especificado' }}</span>
                </div>
            </div>

            <p>Si tiene alguna pregunta o necesita aclaraciones sobre este cambio, no dude en contactarnos.</p>
        </div>

        <div class="footer">
            <p><strong>Sistema de Gestión RUTAC</strong></p>
            <p>Este es un correo automático, por favor no responda a este mensaje.</p>
            <p>© {{ date('Y') }} - Todos los derechos reservados</p>
        </div>
    </div>
</body>
</html>
