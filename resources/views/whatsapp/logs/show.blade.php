@extends('layouts.admin')

@section('title', 'Log #' . $log->id)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1 text-dark fw-bold">
                        <i class="ri-file-list-3-line text-primary me-2"></i>
                        Detalle del envío
                    </h2>
                    <p class="text-muted mb-0">{{ $log->created_at->format('d/m/Y H:i:s') }} · {{ $log->phone }} · {{ $log->template_name }}</p>
                </div>
                <a href="{{ route('admin.whatsapp.logs.index') }}" class="btn btn-outline-secondary"><i class="ri-arrow-left-line me-2"></i>Volver a logs</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-light border-0"><strong>Resumen</strong></div>
                <div class="card-body">
                    <p><strong>Estado:</strong>
                        @if($log->status === 'sent')
                            <span class="badge bg-success">Enviado</span>
                        @elseif($log->status === 'failed')
                            <span class="badge bg-danger">Fallido</span>
                        @else
                            <span class="badge bg-secondary">{{ $log->status }}</span>
                        @endif
                    </p>
                    <p><strong>Teléfono:</strong> {{ $log->phone }}</p>
                    <p><strong>Plantilla:</strong> {{ $log->template_name }}</p>
                    <p><strong>Grupo plantilla:</strong> {{ $log->template_group ?: '-' }}</p>
                    <p><strong>Usuario:</strong> {{ $log->user ? $log->user->name . ' (' . $log->user->email . ')' : '-' }}</p>
                    @if($log->provider_message_id)
                        <p><strong>ID mensaje proveedor:</strong> <code>{{ $log->provider_message_id }}</code></p>
                    @endif
                    @if($log->error_message)
                        <p class="text-danger"><strong>Error:</strong> {{ $log->error_message }}</p>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-light border-0"><strong>Payload (JSON)</strong></div>
                <div class="card-body p-0">
                    <pre class="mb-0 p-3 bg-light rounded-0" style="max-height: 400px; overflow: auto;">{{ $log->payload ? json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}' }}</pre>
                </div>
            </div>

            @if($log->provider_response && count($log->provider_response) > 0)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-light border-0"><strong>Respuesta del proveedor (JSON)</strong></div>
                <div class="card-body p-0">
                    <pre class="mb-0 p-3 bg-light rounded-0" style="max-height: 300px; overflow: auto;">{{ json_encode($log->provider_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
