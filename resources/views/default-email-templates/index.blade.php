@extends('layouts.admin')

@section('title', 'Plantillas por Defecto')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1 text-dark fw-bold">
                        <i class="ri-settings-3-line text-primary me-2"></i>
                        Plantillas por Defecto
                    </h2>
                    <p class="text-muted mb-0">Configura qué plantillas usar para cada tipo de proceso del sistema</p>
                </div>
                <a href="{{ route('admin.default-email-templates.create') }}" class="btn btn-primary">
                    <i class="ri-add-line me-2"></i>
                    Nueva Plantilla por Defecto
                </a>
            </div>
        </div>
    </div>

    <!-- Info Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info border-0">
                <div class="d-flex">
                    <i class="ri-information-line fs-4 me-3"></i>
                    <div>
                        <h6 class="alert-heading">¿Cómo funciona?</h6>
                        <p class="mb-0">
                            Las <strong>Plantillas por Defecto</strong> permiten al sistema saber qué plantilla usar automáticamente 
                            para cada tipo de proceso (recuperación de contraseña, bienvenida, etc.). 
                            Solo una plantilla puede estar activa por tipo de proceso.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Plantillas por Defecto -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold text-dark">
                        <i class="ri-list-check me-2 text-primary"></i>
                        Configuración de Plantillas por Defecto
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($defaultTemplates->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">Tipo de Proceso</th>
                                        <th class="border-0">Nombre</th>
                                        <th class="border-0">Plantilla Asignada</th>
                                        <th class="border-0">Estado</th>
                                        <th class="border-0">Descripción</th>
                                        <th class="border-0 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($defaultTemplates as $defaultTemplate)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill">
                                                        <i class="ri-settings-3-line me-1"></i>
                                                        {{ $availableProcessTypes[$defaultTemplate->process_type] ?? $defaultTemplate->process_type }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{{ $defaultTemplate->name }}</strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="ri-file-text-line text-muted me-2"></i>
                                                    <span class="text-truncate" style="max-width: 200px;" title="{{ $defaultTemplate->emailTemplate->name }}">
                                                        {{ $defaultTemplate->emailTemplate->name }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                @if($defaultTemplate->is_active)
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                                        <i class="ri-check-line me-1"></i>
                                                        Activa
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                                                        <i class="ri-close-line me-1"></i>
                                                        Inactiva
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ Str::limit($defaultTemplate->description, 60) }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('admin.default-email-templates.preview', $defaultTemplate) }}" 
                                                       class="btn btn-outline-info btn-sm" 
                                                       title="Vista Previa">
                                                        <i class="ri-eye-2-line"></i>
                                                    </a>
                                                    
                                                    <a href="{{ route('admin.default-email-templates.edit', $defaultTemplate) }}" 
                                                       class="btn btn-outline-primary btn-sm" 
                                                       title="Editar">
                                                        <i class="ri-edit-line"></i>
                                                    </a>
                                                    
                                                    <button type="button" 
                                                            class="btn btn-outline-{{ $defaultTemplate->is_active ? 'warning' : 'success' }} btn-sm toggle-active"
                                                            data-id="{{ $defaultTemplate->id }}"
                                                            data-current-state="{{ $defaultTemplate->is_active }}"
                                                            title="{{ $defaultTemplate->is_active ? 'Desactivar' : 'Activar' }}">
                                                        <i class="ri-{{ $defaultTemplate->is_active ? 'close-line' : 'check-line' }}"></i>
                                                    </button>
                                                    
                                                    <button type="button" 
                                                            class="btn btn-outline-danger btn-sm delete-default-template"
                                                            data-id="{{ $defaultTemplate->id }}"
                                                            data-name="{{ $defaultTemplate->name }}"
                                                            title="Eliminar">
                                                        <i class="icon-base ri ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ri-settings-3-line fs-1 text-muted mb-3"></i>
                            <h6 class="text-muted">No hay plantillas por defecto configuradas</h6>
                            <p class="text-muted mb-3">
                                Comienza configurando las plantillas por defecto para los procesos del sistema
                            </p>
                            <a href="{{ route('admin.default-email-templates.create') }}" class="btn btn-primary">
                                <i class="ri-add-line me-2"></i>
                                Crear Primera Plantilla por Defecto
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tipos de Procesos Disponibles -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold text-dark">
                        <i class="ri-information-line me-2 text-primary"></i>
                        Tipos de Procesos Disponibles
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($availableProcessTypes as $processType => $processName)
                            @php
                                $hasTemplate = $defaultTemplates->where('process_type', $processType)->count() > 0;
                                $hasActiveTemplate = $defaultTemplates->where('process_type', $processType)->where('is_active', true)->count() > 0;
                            @endphp
                            
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="border rounded p-3 {{ $hasTemplate ? 'border-success' : 'border-light' }}">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="ri-{{ $hasActiveTemplate ? 'check-circle-fill text-success' : ($hasTemplate ? 'information-line text-info' : 'close-circle-line text-muted') }} me-2"></i>
                                        <h6 class="mb-0">{{ $processName }}</h6>
                                    </div>
                                    
                                    @if($hasTemplate)
                                        @php
                                            $template = $defaultTemplates->where('process_type', $processType)->first();
                                        @endphp
                                        
                                        <small class="text-muted d-block mb-2">
                                            <strong>Plantilla:</strong> {{ $template->emailTemplate->name }}
                                        </small>
                                        
                                        @if($hasActiveTemplate)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                                <i class="ri-check-line me-1"></i>
                                                Activa
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">
                                                <i class="ri-alert-line me-1"></i>
                                                Inactiva
                                            </span>
                                        @endif
                                    @else
                                        <small class="text-muted d-block mb-2">
                                            No configurada
                                        </small>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                                            <i class="ri-close-line me-1"></i>
                                            Sin plantilla
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="ri-delete-bin-line me-2 text-danger"></i>
                    Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres eliminar la plantilla por defecto <strong id="templateName"></strong>?</p>
                <p class="text-muted mb-0">
                    <small>
                        <i class="ri-information-line me-1"></i>
                        Esta acción no elimina la plantilla de email, solo la configuración por defecto.
                    </small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="ri-delete-bin-line me-2"></i>
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle activo/inactivo
    document.querySelectorAll('.toggle-active').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const currentState = this.dataset.currentState === '1';
            
            fetch(`/admin/default-email-templates/${id}/toggle-active`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                    
                    // Recargar la página para actualizar el estado
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Error al cambiar el estado');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Ocurrió un error al cambiar el estado',
                    confirmButtonText: 'Entendido'
                });
            });
        });
    });

    // Eliminar plantilla por defecto
    document.querySelectorAll('.delete-default-template').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            
            // Configurar modal
            document.getElementById('templateName').textContent = name;
            document.getElementById('deleteForm').action = `/admin/default-email-templates/${id}`;
            
            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        });
    });
});
</script>
@endpush
