{{-- 1. PORTADA --}}
<div class="portada text-center">

    {{-- Logo --}}
    <img src="https://cdnsicam.net/img/rutac/rutac-logo-con-ccsm.png" style="max-height: 120px;">

    {{-- Título --}}
    <h1 style="margin-bottom: 10px;">Informe Mensual de Gestión</h1>
    <h3 class="text-muted">Ruta de Crecimiento (Ruta C)</h3>

    {{-- Información general --}}
    <div style="margin-top: 40px; text-align: left; width: 80%; margin-left: auto; margin-right: auto;">
        <p><strong>Periodo:</strong> {{ $periodo }}<br />
            <strong>Fecha de generación:</strong> {{ $fecha_generacion }}<br />
            <strong>Estado del informe:</strong> {{ $estado_legible }}
        </p>
    </div>

    {{-- Gestor --}}
    <div style="margin-bottom: 10px; text-align: left; width: 80%; margin-left: auto; margin-right: auto;">
        <h4>Gestor / Asesor</h4>
        <p><strong>Nombre:</strong> {{ $asesor->name ?? '' }} {{ $asesor->lastname ?? '' }}<br />
            <strong>Cédula:</strong> {{ $asesor->identificacion ?? 'N/A' }}<br />
            <strong>Correo:</strong> {{ $asesor->email ?? 'N/A' }}<br />
            <strong>Correo institucional:</strong> {{ $asesor->email_institucional ?? 'N/A' }}<br />
            <strong>Rol:</strong> {{ $asesor->rol->nombre ?? 'Asesor' }}
        </p>
    </div>

    {{-- Supervisor --}}
    @if (!empty($supervisor))
        <div style="margin-bottom: 10px; text-align: left; width: 80%; margin-left: auto; margin-right: auto;">
            <h4>Supervisor</h4>
            <p><strong>Nombre:</strong> {{ $supervisor->name ?? '' }} {{ $supervisor->lastname ?? '' }}</p>
            <p><strong>Cédula:</strong> {{ $supervisor->identificacion ?? 'N/A' }}</p>
            <p><strong>Correo:</strong> {{ $supervisor->email ?? 'N/A' }}</p>
            <p><strong>Correo institucional:</strong> {{ $supervisor->email_institucional ?? 'N/A' }}</p>
            <p><strong>Rol:</strong> {{ $supervisor->rol->nombre ?? 'Supervisor' }}</p>
        </div>
    @endif

    {{-- Resumen --}}
    <div style="margin-bottom: 10px; text-align: left; width: 80%; margin-left: auto; margin-right: auto;">
        <h4>Resumen del periodo</h4>
        <p>
            <strong>Intervenciones realizadas:</strong> {{ $total_intervenciones }}<br />
            <strong>Unidades impactadas:</strong> {{ $total_unidades }}
        </p>
    </div>

    {{-- Evaluación --}}
    <div style="margin-bottom: 10px; text-align: left; width: 80%; margin-left: auto; margin-right: auto;">
        <h4>Evaluación del periodo</h4>
        <p><strong>Meta del periodo:</strong> {{ $meta_texto }}</p>
        <p><strong>Avance:</strong> {{ $avance_texto }}</p>
    </div>

</div>
