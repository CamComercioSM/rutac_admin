@extends('layouts.admin')

@section('title', 'Ver Plantilla de Correo')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1 text-dark fw-bold">
                        <i class="ri-eye-line text-primary me-2"></i>
                        Ver Plantilla
                    </h2>
                    <p class="text-muted mb-0">Detalles de la plantilla "{{ $emailTemplate->name }}"</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.email-templates.edit', $emailTemplate) }}" class="btn btn-warning">
                        <i class="ri-edit-line me-2"></i>
                        Editar
                    </a>
                    <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary">
                        <i class="ri-arrow-left-line me-2"></i>
                        Volver al Listado
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Template Information -->
    <div class="row">
        <!-- Columna Izquierda - Información -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold text-dark">
                        <i class="ri-information-line me-2 text-primary"></i>
                        Información General
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-dark">
                                <i class="ri-file-text-line me-2 text-primary"></i>
                                Nombre
                            </label>
                            <div class="form-control-plaintext">{{ $emailTemplate->name }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-dark">
                                <i class="ri-subtitle me-2 text-primary"></i>
                                Asunto
                            </label>
                            <div class="form-control-plaintext">{{ $emailTemplate->subject }}</div>
                        </div>
                    </div>
                    
                    @if($emailTemplate->description)
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">
                                <i class="ri-align-left me-2 text-primary"></i>
                                Descripción
                            </label>
                            <div class="form-control-plaintext">{{ $emailTemplate->description }}</div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-dark">
                                <i class="ri-toggle-line me-2 text-primary"></i>
                                Estado
                            </label>
                            <div>
                                @if($emailTemplate->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                                        <i class="ri-check-line me-1"></i>
                                        Activa
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill">
                                        <i class="ri-close-line me-1"></i>
                                        Inactiva
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-dark">
                                <i class="ri-time-line me-2 text-primary"></i>
                                Creada
                            </label>
                            <div class="form-control-plaintext">{{ $emailTemplate->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Variables -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold text-dark">
                        <i class="ri-code-s-slash-line me-2 text-primary"></i>
                        Variables Disponibles
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($emailTemplate->variables && count($emailTemplate->variables) > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($emailTemplate->variables as $variable)
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2 rounded-pill">
                                    <i class="ri-code-line me-2"></i>
                                    {{ $variable }}
                                </span>
                            @endforeach
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="ri-information-line me-1"></i>
                            Estas variables se pueden usar en la plantilla con la sintaxis: &#123;&#123; $variable_name &#125;&#125;
                        </small>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="ri-information-line fs-1 mb-2 d-block"></i>
                            <p class="mb-0">Esta plantilla no utiliza variables dinámicas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna Derecha - Previsualización -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0 fw-semibold">
                        <i class="ri-eye-2-line me-2 text-primary"></i>
                        Vista Previa
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Asunto:</label>
                        <div id="previewSubject" class="bg-light p-2 rounded border">{{ $emailTemplate->subject }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Contenido:</label>
                        <div id="previewContent" class="bg-light p-3 rounded border" style="max-height: 400px; overflow-y: auto;"></div>
                    </div>
                    <button type="button" class="btn btn-outline-primary w-100" id="previewBtn">
                        <i class="ri-eye-2-line me-2"></i>
                        Generar Vista Previa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Preview -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold text-dark">
                        <i class="ri-mail-line me-2 text-primary"></i>
                        Contenido de la Plantilla
                    </h5>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-tabs nav-tabs-line" id="contentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="html-tab" data-bs-toggle="tab" data-bs-target="#html-content" type="button" role="tab">
                                <i class="ri-code-s-slash-line me-2"></i>
                                HTML
                            </button>
                        </li>
                        @if($emailTemplate->text_content)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="text-tab" data-bs-toggle="tab" data-bs-target="#text-content" type="button" role="tab">
                                    <i class="ri-text me-2"></i>
                                    Texto Plano
                                </button>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content" id="contentTabsContent">
                        <div class="tab-pane fade show active" id="html-content" role="tabpanel">
                            <div class="p-4">
                                <pre class="bg-light p-3 rounded border" style="max-height: 400px; overflow-y: auto; white-space: pre-wrap;">{{ $emailTemplate->html_content }}</pre>
                            </div>
                        </div>
                        @if($emailTemplate->text_content)
                            <div class="tab-pane fade" id="text-content" role="tabpanel">
                                <div class="p-4">
                                    <pre class="bg-light p-3 rounded border" style="max-height: 400px; overflow-y: auto; white-space: pre-wrap;">{{ $emailTemplate->text_content }}</pre>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos de ejemplo para la previsualización
    const sampleData = {
        'business_name': 'Mi Empresa S.A.S.',
        'contact_person': 'Juan Pérez',
        'project_name': 'Ruta C',
        'current_year': new Date().getFullYear()
    };

    // Función para generar la vista previa
    function generatePreview() {
        const htmlContent = `{!! $emailTemplate->html_content !!}`;
        
        if (htmlContent) {
            let previewContent = htmlContent;
            // Reemplazar variables con datos de ejemplo
            Object.keys(sampleData).forEach(key => {
                const regex = new RegExp('\\{\\{\\s*\\$' + key + '\\s*\\}\\}', 'g');
                previewContent = previewContent.replace(regex, sampleData[key]);
            });
            document.getElementById('previewContent').innerHTML = previewContent;
        } else {
            document.getElementById('previewContent').innerHTML = '<p class="text-muted text-center">No hay contenido HTML para mostrar</p>';
        }
    }

    // Botón de vista previa
    document.getElementById('previewBtn').addEventListener('click', generatePreview);

    // Generar vista previa inicial
    generatePreview();
});
</script>
@endpush
