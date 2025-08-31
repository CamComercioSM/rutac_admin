@extends('layouts.admin')

@section('title', 'Gestor de Plantillas de Correo')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1 text-dark fw-bold">
                        <i class="ri-mail-send-fill text-primary me-2"></i>
                        Plantillas de Correo
                    </h2>
                    <p class="text-muted mb-0">Gestiona y personaliza las plantillas de correo de tu aplicación</p>
                </div>
                <a href="{{ route('admin.emailTemplates.create') }}" class="btn btn-primary btn-lg shadow-sm">
                    <i class="ri-add-line me-2"></i>
                    Nueva Plantilla
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="ri-mail-send-fill text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Plantillas</h6>
                            <h4 class="mb-0 fw-bold text-dark">{{ $templates->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="ri-check-line text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Activas</h6>
                            <h4 class="mb-0 fw-bold text-dark">{{ $templates->where('is_active', true)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="ri-time-line text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Inactivas</h6>
                            <h4 class="mb-0 fw-bold text-dark">{{ $templates->where('is_active', false)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="ri-code-s-slash-line text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Variables Totales</h6>
                            <h4 class="mb-0 fw-bold text-dark">{{ $templates->sum(function($t) { return count($t->variables ?? []); }) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-semibold text-dark">
                            <i class="ri-list-check me-2 text-primary"></i>
                            Lista de Plantillas
                        </h5>
                        <div class="d-flex gap-2">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="ri-search-line text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Buscar plantillas...">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            <i class="ri-check-line me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                            <i class="ri-error-warning-line me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="templatesTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 py-3 px-4 fw-semibold text-dark">
                                        <i class="ri-file-text-line me-2 text-primary"></i>
                                        Plantilla
                                    </th>
                                    <th class="border-0 py-3 px-4 fw-semibold text-dark">
                                        <i class="ri-subtitle me-2 text-primary"></i>
                                        Asunto
                                    </th>
                                    <th class="border-0 py-3 px-4 fw-semibold text-dark">
                                        <i class="ri-code-s-slash-line me-2 text-primary"></i>
                                        Variables
                                    </th>
                                    <th class="border-0 py-3 px-4 fw-semibold text-dark">
                                        <i class="ri-toggle-line me-2 text-primary"></i>
                                        Estado
                                    </th>
                                    <th class="border-0 py-3 px-4 fw-semibold text-dark text-center">
                                        <i class="ri-settings-3-line me-2 text-primary"></i>
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($templates as $template)
                                    <tr class="align-middle">
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                        <i class="ri-mail-line text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-semibold text-dark">{{ $template->name }}</h6>
                                                    @if($template->description)
                                                        <p class="text-muted small mb-0">{{ $template->description }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-dark fw-medium">{{ $template->subject }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($template->variables && count($template->variables) > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($template->variables as $variable)
                                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1 rounded-pill">
                                                            <i class="ri-code-line me-1"></i>
                                                            {{ $variable }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted small">
                                                    <i class="ri-information-line me-1"></i>
                                                    Sin variables
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($template->is_active)
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
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.emailTemplates.show', $template) }}" 
                                                   class="btn btn-sm btn-outline-info border-0" 
                                                   title="Ver detalles"
                                                   data-bs-toggle="tooltip">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                                <a href="{{ route('admin.emailTemplates.edit', $template) }}" 
                                                   class="btn btn-sm btn-outline-warning border-0" 
                                                   title="Editar plantilla"
                                                   data-bs-toggle="tooltip">
                                                    <i class="ri-edit-line"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-success border-0 preview-btn" 
                                                        data-template-id="{{ $template->id }}" 
                                                        title="Previsualizar"
                                                        data-bs-toggle="tooltip">
                                                    <i class="ri-eye-2-line"></i>
                                                </button>
                                                <form action="{{ route('admin.emailTemplates.toggle-status', $template) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-secondary border-0" 
                                                            title="{{ $template->is_active ? 'Desactivar' : 'Activar' }}"
                                                            data-bs-toggle="tooltip">
                                                        <i class="ri-toggle-{{ $template->is_active ? 'on' : 'off' }}-line"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.emailTemplates.destroy', $template) }}" 
                                                      method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger border-0" 
                                                            title="Eliminar plantilla"
                                                            data-bs-toggle="tooltip"
                                                            onclick="return confirm('¿Estás seguro de que quieres eliminar esta plantilla? Esta acción no se puede deshacer.')">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="ri-inbox-line fs-1 mb-3 d-block"></i>
                                                <h5 class="text-muted">No hay plantillas disponibles</h5>
                                                <p class="mb-3">Comienza creando tu primera plantilla de correo</p>
                                                <a href="{{ route('admin.emailTemplates.create') }}" class="btn btn-primary">
                                                    <i class="ri-add-line me-2"></i>
                                                    Crear Primera Plantilla
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Previsualización Mejorado -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="ri-eye-2-line me-2"></i>
                    Previsualización de Plantilla
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-light border-0">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="ri-information-line me-2 text-primary"></i>
                                    Información
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark">Asunto del correo:</label>
                                    <input type="text" class="form-control bg-light" id="previewSubject" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark">Variables disponibles:</label>
                                    <div id="previewVariables" class="bg-light p-3 rounded"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-light border-0">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="ri-mail-line me-2 text-primary"></i>
                                    Vista Previa del Correo
                                </h6>
                            </div>
                            <div class="card-body p-0">
                                <div id="previewContent" class="p-4" style="max-height: 600px; overflow-y: auto; background: #f8f9fa;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-2"></i>
                    Cerrar
                </button>
                <button type="button" class="btn btn-primary" id="sendTestEmail">
                    <i class="ri-send-plane-line me-2"></i>
                    Enviar Email de Prueba
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Búsqueda en tiempo real
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('templatesTable');
    const rows = table.querySelectorAll('tbody tr');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Botones de previsualización
    document.querySelectorAll('.preview-btn').forEach(button => {
        button.addEventListener('click', function() {
            const templateId = this.dataset.templateId;
            previewTemplate(templateId);
        });
    });

    function previewTemplate(templateId) {
        // Mostrar modal de carga
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();

        // Mostrar indicador de carga
        document.getElementById('previewContent').innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-3 text-muted">Cargando previsualización...</p>
            </div>
        `;

        // Cargar previsualización
        fetch(`/admin/email-templates/${templateId}/preview`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('previewSubject').value = data.subject;
            document.getElementById('previewContent').innerHTML = data.html;
            
            // Mostrar variables disponibles
            const variablesDiv = document.getElementById('previewVariables');
            variablesDiv.innerHTML = `
                <div class="d-flex flex-wrap gap-1">
                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1 rounded-pill">
                        <i class="ri-code-line me-1"></i>
                        business_name
                    </span>
                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1 rounded-pill">
                        <i class="ri-code-line me-1"></i>
                        contact_person
                    </span>
                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1 rounded-pill">
                        <i class="ri-code-line me-1"></i>
                        project_name
                    </span>
                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1 rounded-pill">
                        <i class="ri-code-line me-1"></i>
                        current_year
                    </span>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('previewContent').innerHTML = `
                <div class="text-center py-5 text-danger">
                    <i class="ri-error-warning-line fs-1 mb-3 d-block"></i>
                    <h5>Error al cargar la previsualización</h5>
                    <p class="text-muted">Inténtalo de nuevo más tarde</p>
                </div>
            `;
        });
    }

    // Botón de enviar email de prueba
    document.getElementById('sendTestEmail').addEventListener('click', function() {
        // Aquí podrías implementar la funcionalidad para enviar un email de prueba
        alert('Funcionalidad de email de prueba en desarrollo');
    });
});
</script>
@endpush
