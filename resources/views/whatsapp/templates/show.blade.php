@extends('layouts.admin')

@section('title', 'Plantilla: ' . $template->name)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1 text-dark fw-bold">{{ $template->name }}</h2>
                    <p class="text-muted mb-0">Categoría: {{ $template->category?->name ?? '-' }}</p>
                </div>
                <a href="{{ route('admin.whatsapp.templates.edit', $template) }}" class="btn btn-warning">Editar</a>
                <a href="{{ route('admin.whatsapp.templates.index') }}" class="btn btn-outline-secondary">Volver</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <p><strong>Canal:</strong> {{ $template->channel ?? 'whatsapp' }}</p>
                    <p><strong>Grupo:</strong> {{ $template->group_code ?: '-' }}</p>
                    <p><strong>Estado:</strong> {{ $template->is_active ? 'Activa' : 'Inactiva' }}</p>
                </div>
            </div>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-light"><strong>Expected fields</strong></div>
                <div class="card-body">
                    <pre class="mb-0 bg-light p-3 rounded">{{ $template->expected_fields ? json_encode($template->expected_fields, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '[]' }}</pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
