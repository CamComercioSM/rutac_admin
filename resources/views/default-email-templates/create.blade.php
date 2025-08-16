@extends('layouts.admin')

@section('title', 'Crear Plantilla por Defecto')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1 text-dark fw-bold">
                        <i class="ri-add-line text-primary me-2"></i>
                        Crear Plantilla por Defecto
                    </h2>
                    <p class="text-muted mb-0">Configura una nueva plantilla por defecto para un tipo de proceso</p>
                </div>
                <a href="{{ route('admin.default-email-templates.index') }}" class="btn btn-outline-secondary">
                    <i class="ri-arrow-left-line me-2"></i>
                    Volver al Listado
                </a>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold text-dark">
                        <i class="ri-settings-3-line me-2 text-primary"></i>
                        Configuración de Plantilla por Defecto
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.default-email-templates.store') }}" method="POST" id="createForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Tipo de Proceso -->
                            <div class="col-md-6 mb-3">
                                <label for="process_type" class="form-label fw-semibold text-dark">
                                    <i class="ri-settings-3-line me-2 text-primary"></i>
                                    Tipo de Proceso <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('process_type') is-invalid @enderror" 
                                        id="process_type" 
                                        name="process_type" 
                                        required>
                                    <option value="">Selecciona un tipo de proceso</option>
                                    @foreach($availableProcessTypes as $processType => $processName)
                                        @php
                                            $isUsed = \App\Models\DefaultEmailTemplate::where('process_type', $processType)->exists();
                                        @endphp
                                        <option value="{{ $processType }}" 
                                                {{ old('process_type') == $processType ? 'selected' : '' }}
                                                {{ $isUsed ? 'disabled' : '' }}>
                                            {{ $processName }}
                                            @if($isUsed)
                                                (Ya configurado)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('process_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    <i class="ri-information-line me-1"></i>
                                    Solo se muestran los tipos de proceso que aún no tienen plantilla configurada
                                </small>
                            </div>

                            <!-- Nombre -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-semibold text-dark">
                                    <i class="ri-file-text-line me-2 text-primary"></i>
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Ej: Plantilla de Recuperación de Contraseña"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    <i class="ri-information-line me-1"></i>
                                    Nombre descriptivo para identificar esta configuración
                                </small>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold text-dark">
                                <i class="ri-align-left me-2 text-primary"></i>
                                Descripción
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Describe el propósito de esta plantilla por defecto...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="ri-information-line me-1"></i>
                                Descripción opcional para entender mejor cuándo se usa esta plantilla
                            </small>
                        </div>

                        <!-- Plantilla de Email -->
                        <div class="mb-3">
                            <label for="email_template_id" class="form-label fw-semibold text-dark">
                                <i class="ri-mail-line me-2 text-primary"></i>
                                Plantilla de Email <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('email_template_id') is-invalid @enderror" 
                                    id="email_template_id" 
                                    name="email_template_id" 
                                    required>
                                <option value="">Selecciona una plantilla de email</option>
                                @foreach($emailTemplates as $template)
                                    <option value="{{ $template->id }}" 
                                            {{ old('email_template_id') == $template->id ? 'selected' : '' }}>
                                        {{ $template->name }}
                                        <small class="text-muted">({{ $template->subject }})</small>
                                    </option>
                                @endforeach
                            </select>
                            @error('email_template_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="ri-information-line me-1"></i>
                                Selecciona la plantilla de email que se usará para este tipo de proceso
                            </small>
                        </div>

                        <!-- Estado -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input @error('is_active') is-invalid @enderror" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-dark" for="is_active">
                                    <i class="ri-check-line me-2 text-success"></i>
                                    Activar esta plantilla por defecto
                                </label>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">
                                    <i class="ri-information-line me-1"></i>
                                    Si se activa, esta plantilla será la que use el sistema para este tipo de proceso. 
                                    Solo una plantilla puede estar activa por tipo de proceso.
                                </small>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.default-email-templates.index') }}" class="btn btn-outline-secondary">
                                <i class="ri-close-line me-2"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-2"></i>
                                Crear Plantilla por Defecto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Información Adicional -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 py-3">
                    <h6 class="mb-0 fw-semibold text-dark">
                        <i class="ri-information-line me-2 text-primary"></i>
                        Tipos de Procesos Disponibles
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($availableProcessTypes as $processType => $processName)
                            @php
                                $isUsed = \App\Models\DefaultEmailTemplate::where('process_type', $processType)->exists();
                                $description = $this->getProcessDescription($processType);
                            @endphp
                            
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="border rounded p-3 {{ $isUsed ? 'border-success bg-success bg-opacity-5' : 'border-light' }}">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="ri-{{ $isUsed ? 'check-circle-fill text-success' : 'information-line text-muted' }} me-2"></i>
                                        <h6 class="mb-0">{{ $processName }}</h6>
                                    </div>
                                    
                                    <small class="text-muted d-block mb-2">
                                        {{ $description }}
                                    </small>
                                    
                                    @if($isUsed)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                            <i class="ri-check-line me-1"></i>
                                            Configurado
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                                            <i class="ri-close-line me-1"></i>
                                            Disponible
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-completar nombre basado en el tipo de proceso seleccionado
    const processTypeSelect = document.getElementById('process_type');
    const nameInput = document.getElementById('name');
    
    processTypeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value && !nameInput.value) {
            nameInput.value = selectedOption.text + ' - Plantilla por Defecto';
        }
    });

    // Validación del formulario
    document.getElementById('createForm').addEventListener('submit', function(e) {
        const processType = document.getElementById('process_type').value;
        const emailTemplateId = document.getElementById('email_template_id').value;
        
        if (!processType || !emailTemplateId) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Campos Requeridos',
                text: 'Por favor, completa todos los campos obligatorios.',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
    });
});
</script>
@endpush
