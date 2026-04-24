{{-- 1. PORTADA --}}
<div class="portada">
    <div class="contenido-portada">

        {{-- Logo --}}
        <img src="https://cdnsicam.net/img/rutac/rutac-logo-con-ccsm.png" class="logo-portada">

        {{-- Título --}}
        <h1>Informe Mensual de Gestión</h1>
        <h3 class="text-muted">Ruta de Crecimiento (Ruta C)</h3>

        {{-- Información general --}}
        <div class="seccion-info">
            <p>
                <strong>Periodo:</strong> {{ $periodo }}<br />
                <strong>Fecha de generación:</strong> {{ $fecha_generacion }}<br />
                <strong>Estado del informe:</strong> {{ $estado_legible }}
            </p>
        </div>

        {{-- Gestor --}}
        <div class="seccion-info">
            <h4>Gestor / Asesor</h4>
            <p>
                <strong>Nombre:</strong> {{ $asesor->name ?? '' }} {{ $asesor->lastname ?? '' }}<br />
                <strong>Cédula:</strong> {{ $asesor->identificacion ?? 'N/A' }}<br />
                <strong>Correo:</strong> {{ $asesor->email_institucional ?? ($asesor->email ?? 'N/A') }}<br />
                <strong>Rol:</strong> {{ $asesor->rol->nombre ?? 'Asesor' }}
            </p>
        </div>

        {{-- Supervisor --}}
        @if (!empty($supervisor))
            <div class="seccion-info">
                <h4>Supervisor</h4>
                <p>
                    <strong>Nombre:</strong> {{ $supervisor->name ?? '' }} {{ $supervisor->lastname ?? '' }}<br />
                    <strong>Cédula:</strong> {{ $supervisor->identificacion ?? 'N/A' }}<br />
                    <strong>Rol:</strong> {{ $supervisor->rol->nombre ?? 'Supervisor' }}
                </p>
            </div>
        @endif

        {{-- Resumen y Evaluación en dos columnas (Opcional, usando tablas para PDF) --}}
        <div class="seccion-info">
            <table style="width: 100%;" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="width: 50%; vertical-align: top;">
                        <h4>Resumen del periodo</h4>
                        <p>
                            Intervenciones: {{ $total_intervenciones }}<br />
                            Unidades impactadas: {{ $total_unidades }}
                        </p>
                    </td>
                    <td style="width: 50%; vertical-align: top;">
                        <h4>Evaluación</h4>
                        <p>
                            <strong>Meta:</strong> {{ $meta_texto }}<br />
                            <strong>Avance:</strong> {{ $avance_texto }}
                        </p>
                    </td>
                </tr>
            </table>
        </div>

    </div>
</div>

<div class="page-break"></div>
