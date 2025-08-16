@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/email-template-builder.css') }}">
@endpush

@section('title', 'Crear Nueva Plantilla de Correo')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1 text-dark fw-bold">
                        <i class="ri-add-line text-primary me-2"></i>
                        Crear Nueva Plantilla
                    </h2>
                    <p class="text-muted mb-0">Define una nueva plantilla de correo para tu aplicación</p>
                </div>
                <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary">
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
                        <i class="ri-file-text-line me-2 text-primary"></i>
                        Información de la Plantilla
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.email-templates.store') }}" method="POST" id="templateForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Columna Izquierda -->
                            <div class="col-lg-8">
                                <!-- Información Básica -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label fw-semibold text-dark">
                                            <i class="ri-file-text-line me-2 text-primary"></i>
                                            Nombre de la Plantilla <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name') }}" 
                                               placeholder="Ej: Bienvenida, Recuperación de Contraseña"
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="subject" class="form-label fw-semibold text-dark">
                                            <i class="ri-subtitle me-2 text-primary"></i>
                                            Asunto del Correo <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('subject') is-invalid @enderror" 
                                               id="subject" 
                                               name="subject" 
                                               value="{{ old('subject') }}" 
                                               placeholder="Ej: ¡Bienvenido a Ruta C!"
                                               required>
                                        @error('subject')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Descripción -->
                                <div class="mb-4">
                                    <label for="description" class="form-label fw-semibold text-dark">
                                        <i class="ri-align-left me-2 text-primary"></i>
                                        Descripción
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="3" 
                                              placeholder="Describe el propósito de esta plantilla...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Variables Disponibles -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="ri-code-s-slash-line me-2 text-primary"></i>
                                        Variables Disponibles
                                    </label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="variables[]" value="business_name" id="var_business_name">
                                                <label class="form-check-label" for="var_business_name">
                                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1 rounded-pill">
                                                        <i class="ri-code-line me-1"></i>
                                                        business_name
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="variables[]" value="contact_person" id="var_contact_person">
                                                <label class="form-check-label" for="var_contact_person">
                                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1 rounded-pill">
                                                        <i class="ri-code-line me-1"></i>
                                                        contact_person
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="variables[]" value="project_name" id="var_project_name">
                                                <label class="form-check-label" for="var_project_name">
                                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1 rounded-pill">
                                                        <i class="ri-code-line me-1"></i>
                                                        project_name
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="variables[]" value="current_year" id="var_current_year">
                                                <label class="form-check-label" for="var_current_year">
                                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1 rounded-pill">
                                                        <i class="ri-code-line me-1"></i>
                                                        current_year
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <i class="ri-information-line me-1"></i>
                                        Usa estas variables en tu plantilla con la sintaxis: &#123;&#123; $variable_name &#125;&#125;
                                    </small>
                                </div>

                                <!-- Constructor Visual de Plantillas -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="ri-layout-grid-line me-2 text-primary"></i>
                                        Constructor de Plantilla <span class="text-danger">*</span>
                                    </label>
                                    
                                    <!-- Barra de Herramientas del Constructor -->
                                    <div class="card border-0 shadow-sm mb-3">
                                        <div class="card-body p-3">
                                            <div class="d-flex flex-wrap gap-2 mb-3">
                                                <button type="button" class="btn btn-outline-primary btn-sm" data-component="header">
                                                    <i class="ri-heading me-1"></i> Encabezado
                                                </button>
                                                <button type="button" class="btn btn-outline-primary btn-sm" data-component="text">
                                                    <i class="ri-text me-1"></i> Texto
                                                </button>
                                                <button type="button" class="btn btn-outline-primary btn-sm" data-component="image">
                                                    <i class="ri-image me-1"></i> Imagen
                                                </button>
                                                <button type="button" class="btn btn-outline-primary btn-sm" data-component="button">
                                                    <i class="ri-links-line me-1"></i> Botón
                                                </button>
                                                <button type="button" class="btn btn-outline-primary btn-sm" data-component="divider">
                                                    <i class="ri-separator me-1"></i> Separador
                                                </button>
                                                <button type="button" class="btn btn-outline-primary btn-sm" data-component="spacer">
                                                    <i class="ri-space me-1"></i> Espaciador
                                                </button>
                                                <button type="button" class="btn btn-outline-warning btn-sm" id="clearTemplate">
                                                    <i class="ri-delete-bin-line me-1"></i> Limpiar
                                                </button>
                                            </div>
                                            
                                            <!-- Información de la estructura -->
                                            <div class="border-top pt-3">
                                                <div class="alert alert-info mb-0">
                                                    <i class="ri-information-line me-2"></i>
                                                    <strong>Estructura Simple:</strong> Tu plantilla se construirá en una sola columna para mayor simplicidad y compatibilidad.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Área de Construcción -->
                                    <div class="card border-2 border-dashed border-light">
                                        <div class="card-body p-3" id="templateBuilder" style="min-height: 300px;">
                                            <div class="text-center text-muted py-5" id="emptyState">
                                                <i class="ri-layout-grid-line fs-1 mb-3"></i>
                                                <h6>Área de Construcción</h6>
                                                <p class="mb-0">Haz clic en los botones de arriba para agregar elementos a tu plantilla</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Campos ocultos para el HTML generado -->
                                    <input type="hidden" name="html_content" id="html_content" value="{{ old('html_content') }}" required>
                                    <input type="hidden" name="text_content" id="text_content" value="{{ old('text_content') }}">
                                    
                                    @error('html_content')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    
                                    <small class="text-muted">
                                        <i class="ri-information-line me-1"></i>
                                        <strong>Instrucciones:</strong> 
                                        • <strong>1.</strong> Haz clic en "Agregar Componente" para añadir elementos
                                        • <strong>2.</strong> Selecciona el tipo de componente que necesites
                                        • <strong>3.</strong> <strong>Doble clic</strong> en cualquier componente para editarlo
                                        • <strong>4.</strong> <strong>Arrastra y suelta</strong> componentes para reorganizarlos
                                    </small>
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
                                            <div id="previewSubject" class="bg-light p-2 rounded border"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-dark">Contenido:</label>
                                            <div id="previewContent" class="bg-light p-3 rounded border" style="max-height: 400px; overflow-y: auto;"></div>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary w-100" id="previewBtn">
                                            <i class="ri-eye-2-line me-2"></i>
                                            Actualizar Vista Previa
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-outline-info" id="testEmailBtn" disabled>
                                        <i class="ri-send-plane-line me-2"></i>
                                        Enviar Prueba
                                    </button>
                                    <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary">
                                        <i class="ri-close-line me-2"></i>
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line me-2"></i>
                                        Crear Plantilla
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Envío de Prueba -->
<div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="testEmailModalLabel">
                    <i class="ri-send-plane-line me-2 text-info"></i>
                    Enviar Email de Prueba
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="testEmail" class="form-label fw-semibold text-dark">
                        <i class="ri-mail-line me-2 text-primary"></i>
                        Email de Destino <span class="text-danger">*</span>
                    </label>
                    <input type="email" 
                           class="form-control" 
                           id="testEmail" 
                           placeholder="tu-email@ejemplo.com"
                           required>
                    <small class="text-muted">
                        <i class="ri-information-line me-1"></i>
                        El email se enviará a esta dirección para que puedas ver cómo se ve tu plantilla
                    </small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">
                        <i class="ri-eye-2-line me-2 text-primary"></i>
                        Vista Previa del Envío
                    </label>
                    <div class="border rounded p-3 bg-light">
                        <div class="mb-2">
                            <strong>Asunto:</strong> <span id="testSubject"></span>
                        </div>
                        <div class="mb-2">
                            <strong>Destinatario:</strong> <span id="testRecipient"></span>
                        </div>
                        <div>
                            <strong>Contenido:</strong> <span id="testContentPreview" class="text-muted">Se mostrará aquí...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-info" id="sendTestEmailBtn">
                    <i class="ri-send-plane-line me-2"></i>
                    Enviar Prueba
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Variables globales del constructor
let componentCounter = 0;
let selectedComponent = null;
let draggedElement = null;

// Configuración de componentes
const componentConfigs = {
    header: {
        icon: 'ri-heading',
        label: 'Encabezado',
        defaultText: 'Título Principal',
        defaultStyle: 'h1',
        htmlTemplate: (config) => `<h${config.style || 1} style="color: ${config.color || '#333'}; font-size: ${config.fontSize || '24px'}; text-align: ${config.align || 'left'}; margin: 10px 0;">${config.text}</h${config.style || 1}>`
    },
    text: {
        icon: 'ri-text',
        label: 'Texto',
        defaultText: 'Escribe tu texto aquí...',
        htmlTemplate: (config) => `<p style="color: ${config.color || '#666'}; font-size: ${config.fontSize || '16px'}; text-align: ${config.align || 'left'}; margin: 10px 0; line-height: 1.6;">${config.text}</p>`
    },
    image: {
        icon: 'ri-image',
        label: 'Imagen',
        defaultText: 'Descripción de la imagen',
        htmlTemplate: (config) => {
            // Usar imagen cargada si está disponible, sino usar URL
            const imageSrc = config.file || config.src || 'https://via.placeholder.com/400x200';
            return `<div style="text-align: ${config.align || 'center'}; margin: 20px 0;">
                <img src="${imageSrc}" alt="${config.alt || 'Imagen'}" style="max-width: 100%; height: auto; border-radius: 8px;">
                ${config.caption ? `<p style="color: #666; font-size: 14px; margin-top: 10px; font-style: italic;">${config.caption}</p>` : ''}
            </div>`;
        }
    },
    button: {
        icon: 'ri-links-line',
        label: 'Botón',
        defaultText: 'Haz clic aquí',
        htmlTemplate: (config) => `<div style="text-align: ${config.align || 'center'}; margin: 20px 0;">
            <a href="${config.url || '#'}" style="display: inline-block; background-color: ${config.bgColor || '#007bff'}; color: ${config.textColor || 'white'}; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">${config.text}</a>
        </div>`
    },
    divider: {
        icon: 'ri-separator',
        label: 'Separador',
        htmlTemplate: (config) => `<hr style="border: none; height: 1px; background-color: ${config.color || '#ddd'}; margin: 20px 0;">`
    },
    spacer: {
        icon: 'ri-space',
        label: 'Espaciador',
        htmlTemplate: (config) => `<div style="height: ${config.height || '20'}px;"></div>`
    }
};

// Datos de ejemplo para la previsualización
const sampleData = {
    business_name: 'Mi Empresa',
    contact_person: 'Juan Pérez',
    project_name: 'Ruta C',
    current_year: new Date().getFullYear().toString()
};

// Función para crear un componente
function createComponent(type) {
    console.log('Creando componente:', type);
    
    const config = componentConfigs[type];
    if (!config) {
        console.error('No se encontró configuración para:', type);
        return;
    }

    componentCounter++;
    const componentId = `component_${componentCounter}`;
    
    const componentHtml = `
        <div class="template-component" id="${componentId}" data-type="${type}" draggable="true">
            <div class="component-header d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-primary">${config.label}</span>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-component" data-id="${componentId}">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </div>
            <div class="component-preview" id="preview_${componentId}">
                ${config.htmlTemplate(config)}
            </div>
            <div class="component-config" id="config_${componentId}" style="display: none;">
                ${generateConfigForm(type, componentId, config)}
            </div>
        </div>
    `;
    
    // Ocultar estado vacío
    const emptyState = document.getElementById('emptyState');
    if (emptyState) {
        emptyState.style.display = 'none';
    }
    
    // Agregar componente al constructor
    document.getElementById('templateBuilder').insertAdjacentHTML('beforeend', componentHtml);
    
    // Agregar event listeners
    addComponentEventListeners(componentId, type);
    
    // Actualizar HTML generado
    updateGeneratedHTML();
}

// Función para generar formulario de configuración
function generateConfigForm(type, componentId, config) {
    let formHtml = '<div class="row g-2">';
    
    if (type === 'header') {
        formHtml += `
            <div class="col-md-6">
                <label class="form-label">Texto:</label>
                <input type="text" class="form-control form-control-sm" value="${config.defaultText}" data-config="text">
            </div>
            <div class="col-md-3">
                <label class="form-label">Nivel:</label>
                <select class="form-select form-select-sm" data-config="style">
                    <option value="1">H1</option>
                    <option value="2">H2</option>
                    <option value="3">H3</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Alineación:</label>
                <select class="form-select form-select-sm" data-config="align">
                    <option value="left">Izquierda</option>
                    <option value="center">Centro</option>
                    <option value="right">Derecha</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Color:</label>
                <input type="color" class="form-control form-control-sm" value="#333" data-config="color">
            </div>
            <div class="col-md-6">
                <label class="form-label">Tamaño de fuente:</label>
                <input type="text" class="form-control form-control-sm" value="24px" data-config="fontSize">
            </div>
        `;
    } else if (type === 'text') {
        formHtml += `
            <div class="col-md-6">
                <label class="form-label">Texto:</label>
                <textarea class="form-control form-control-sm" rows="3" data-config="text">${config.defaultText}</textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label">Alineación:</label>
                <select class="form-select form-select-sm" data-config="align">
                    <option value="left">Izquierda</option>
                    <option value="center">Centro</option>
                    <option value="right">Derecha</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Color:</label>
                <input type="color" class="form-control form-control-sm" value="#666" data-config="color">
            </div>
            <div class="col-md-6">
                <label class="form-label">Tamaño de fuente:</label>
                <input type="text" class="form-control form-control-sm" value="16px" data-config="fontSize">
            </div>
        `;
    } else if (type === 'image') {
        formHtml += `
            <div class="col-md-6">
                <label class="form-label">Cargar imagen:</label>
                <input type="file" class="form-control form-control-sm" accept="image/*" data-config="file" onchange="handleImageUpload(this, '${componentId}')">
                <small class="text-muted">Formatos: JPG, PNG, GIF (Max: 2MB)</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">URL de la imagen (opcional):</label>
                <input type="url" class="form-control form-control-sm" value="https://via.placeholder.com/400x200" data-config="src" placeholder="https://ejemplo.com/imagen.jpg">
                <small class="text-muted">O usa el campo de arriba para subir</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Texto alternativo:</label>
                <input type="text" class="form-control form-control-sm" value="Imagen" data-config="alt">
            </div>
            <div class="col-md-6">
                <label class="form-label">Pie de foto:</label>
                <input type="text" class="form-control form-control-sm" value="" data-config="caption">
            </div>
            <div class="col-md-6">
                <label class="form-label">Alineación:</label>
                <select class="form-select form-select-sm" data-config="align">
                    <option value="left">Izquierda</option>
                    <option value="center">Centro</option>
                    <option value="right">Derecha</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Vista previa:</label>
                <div class="border rounded p-2 bg-light" style="min-height: 60px;">
                    <img id="preview_${componentId}_img" src="https://via.placeholder.com/200x100?text=Vista+Previa" alt="Vista previa" style="max-width: 100%; height: auto; border-radius: 4px;">
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearImageUpload('${componentId}')">
                        <i class="ri-delete-bin-line me-1"></i> Limpiar Imagen
                    </button>
                </div>
            </div>
        `;
    } else if (type === 'button') {
        formHtml += `
            <div class="col-md-6">
                <label class="form-label">Texto del botón:</label>
                <input type="text" class="form-control form-control-sm" value="${config.defaultText}" data-config="text">
            </div>
            <div class="col-md-6">
                <label class="form-label">URL del enlace:</label>
                <input type="url" class="form-control form-control-sm" value="#" data-config="url">
            </div>
            <div class="col-md-4">
                <label class="form-label">Color de fondo:</label>
                <input type="color" class="form-control form-control-sm" value="#007bff" data-config="bgColor">
            </div>
            <div class="col-md-4">
                <label class="form-label">Color del texto:</label>
                <input type="color" class="form-control form-control-sm" value="#ffffff" data-config="textColor">
            </div>
            <div class="col-md-4">
                <label class="form-label">Alineación:</label>
                <select class="form-select form-select-sm" data-config="align">
                    <option value="left">Izquierda</option>
                    <option value="center">Centro</option>
                    <option value="right">Derecha</option>
                </select>
            </div>
        `;
    } else if (type === 'divider') {
        formHtml += `
            <div class="col-md-6">
                <label class="form-label">Color:</label>
                <input type="color" class="form-control form-control-sm" value="#ddd" data-config="color">
            </div>
        `;
    } else if (type === 'spacer') {
        formHtml += `
            <div class="col-md-6">
                <label class="form-label">Altura (px):</label>
                <input type="number" class="form-control form-control-sm" value="20" min="10" max="100" data-config="height">
            </div>
        `;
    }
    
    formHtml += `
        <div class="col-12">
            <button type="button" class="btn btn-success btn-sm apply-config" data-id="${componentId}">
                <i class="ri-check-line me-1"></i> Aplicar
            </button>
        </div>
    </div>`;
    
    return formHtml;
}

// Función para manejar la carga de imágenes
function handleImageUpload(input, componentId) {
    const file = input.files[0];
    if (!file) {
        return;
    }

    // Validar tipo de archivo
    if (!file.type.startsWith('image/')) {
        Swal.fire({
            icon: 'error',
            title: 'Tipo de archivo inválido',
            text: 'Por favor, selecciona un archivo de imagen válido (JPG, PNG, GIF).',
            confirmButtonText: 'Entendido'
        });
        input.value = '';
        return;
    }

    // Validar tamaño (2MB máximo)
    if (file.size > 2 * 1024 * 1024) {
        Swal.fire({
            icon: 'warning',
            title: 'Archivo demasiado grande',
            text: 'La imagen es demasiado grande. El tamaño máximo es 2MB.',
            confirmButtonText: 'Entendido'
        });
        input.value = '';
        return;
    }

    // Mostrar indicador de carga
    const previewImg = document.getElementById(`preview_${componentId}_img`);
    if (previewImg) {
        previewImg.style.opacity = '0.5';
        previewImg.style.filter = 'grayscale(50%)';
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        // Actualizar vista previa
        if (previewImg) {
            previewImg.src = e.target.result;
            previewImg.style.opacity = '1';
            previewImg.style.filter = 'none';
        }
        
        // Actualizar el campo de URL con la imagen en base64
        const urlInput = input.closest('.row').querySelector('[data-config="src"]');
        if (urlInput) {
            urlInput.value = e.target.result;
        }

        // Mostrar mensaje de éxito
        Swal.fire({
            icon: 'success',
            title: 'Imagen cargada exitosamente',
            text: `La imagen "${file.name}" se ha cargado correctamente.`,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    };

    reader.onerror = function() {
        Swal.fire({
            icon: 'error',
            title: 'Error al cargar la imagen',
            text: 'Ocurrió un error al procesar la imagen. Inténtalo de nuevo.',
            confirmButtonText: 'Entendido'
        });
        input.value = '';
        
        // Restaurar vista previa
        if (previewImg) {
            previewImg.style.opacity = '1';
            previewImg.style.filter = 'none';
        }
    };

    reader.readAsDataURL(file);
}

// Función para limpiar la imagen cargada en un componente específico
function clearImageUpload(componentId) {
    const previewImg = document.getElementById(`preview_${componentId}_img`);
    if (previewImg) {
        previewImg.src = 'https://via.placeholder.com/200x100?text=Vista+Previa';
        previewImg.style.opacity = '1';
        previewImg.style.filter = 'none';
    }
    
    const fileInput = document.querySelector(`.template-component[id="${componentId}"] input[type="file"]`);
    if (fileInput) {
        fileInput.value = '';
    }
    
    const urlInput = document.querySelector(`.template-component[id="${componentId}"] input[type="url"]`);
    if (urlInput) {
        urlInput.value = '';
    }
    
    Swal.fire({
        icon: 'info',
        title: 'Imagen Limpiada',
        text: 'La imagen cargada en este componente ha sido eliminada.',
        timer: 2000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

// Función para agregar event listeners a los componentes
function addComponentEventListeners(componentId, type) {
    const component = document.getElementById(componentId);
    
    // Doble clic para editar
    component.addEventListener('dblclick', function() {
        const previewDiv = component.querySelector('.component-preview');
        const configDiv = component.querySelector('.component-config');
        
        if (configDiv.style.display === 'none') {
            configDiv.style.display = 'block';
            previewDiv.style.display = 'none';
        } else {
            configDiv.style.display = 'none';
            previewDiv.style.display = 'block';
        }
    });
    
    // Botón eliminar
    component.querySelector('.remove-component').addEventListener('click', function() {
        if (confirm('¿Estás seguro de que quieres eliminar este componente?')) {
            component.remove();
            
            // Mostrar estado vacío si no hay componentes
            if (document.querySelectorAll('.template-component').length === 0) {
                document.getElementById('emptyState').style.display = 'block';
            }
            
            updateGeneratedHTML();
        }
    });
    
    // Drag & Drop para componentes
    component.addEventListener('dragstart', function(e) {
        draggedElement = this;
        this.classList.add('dragging');
    });
    
    component.addEventListener('dragend', function(e) {
        this.classList.remove('dragging');
    });
    
    // Aplicar configuración
    component.addEventListener('click', function(e) {
        if (e.target.classList.contains('apply-config')) {
            applyComponentConfig(componentId, type);
        }
    });
}

// Función para aplicar configuración del componente
function applyComponentConfig(componentId, type) {
    const component = document.getElementById(componentId);
    const configDiv = component.querySelector('.component-config');
    const previewDiv = component.querySelector('.component-preview');
    const config = componentConfigs[type];
    
    // Recopilar configuración
    const componentConfig = {};
    configDiv.querySelectorAll('[data-config]').forEach(input => {
        const configKey = input.getAttribute('data-config');
        if (configKey === 'file' && input.files && input.files[0]) {
            // Para archivos, convertir a base64
            const reader = new FileReader();
            reader.onload = function(e) {
                componentConfig[configKey] = e.target.result;
                // Continuar con la aplicación de la configuración
                finishApplyingConfig(component, configDiv, previewDiv, config, componentConfig, type);
            };
            reader.readAsDataURL(input.files[0]);
            return; // Salir temprano para archivos
        } else {
            componentConfig[configKey] = input.value;
        }
    });
    
    // Para componentes que no son archivos, aplicar inmediatamente
    finishApplyingConfig(component, configDiv, previewDiv, config, componentConfig, type);
}

// Función auxiliar para terminar de aplicar la configuración
function finishApplyingConfig(component, configDiv, previewDiv, config, componentConfig, type) {
    // Actualizar vista previa
    previewDiv.innerHTML = config.htmlTemplate(componentConfig);
    
    // Ocultar configuración y mostrar vista previa
    configDiv.style.display = 'none';
    previewDiv.style.display = 'block';
    
    // Actualizar HTML generado
    updateGeneratedHTML();
}

// Función para actualizar la vista previa
function updatePreview() {
    const subject = document.getElementById('subject').value;
    const htmlContent = document.getElementById('html_content').value;
    
    // Actualizar asunto
    document.getElementById('previewSubject').textContent = subject || 'Asunto del correo';
    
    // Actualizar contenido
    let previewContent = htmlContent;
    if (previewContent) {
        // Reemplazar variables con datos de ejemplo
        Object.keys(sampleData).forEach(key => {
            const regex = new RegExp('\\{\\{\\s*\\$' + key + '\\s*\\}\\}', 'g');
            previewContent = previewContent.replace(regex, sampleData[key]);
        });
        
        // Agregar header y footer estáticos
        const fullEmail = generateFullEmail(previewContent);
        document.getElementById('previewContent').innerHTML = fullEmail;
    } else {
        document.getElementById('previewContent').innerHTML = '<p class="text-muted text-center">Construye tu plantilla para ver la vista previa</p>';
    }
}

// Función para generar el email completo con header y footer
function generateFullEmail(content) {
    return `
        <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
            <!-- Header -->
            <div style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); padding: 40px 20px 30px; text-align: center; position: relative;">
                <div style="display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 30px; margin-bottom: 25px;">
                    <div style="width: 180px; height: 100px; background-color: transparent; display: flex; align-items: center; justify-content: center;">
                        <img src="https://cdnsicam.net/img/rutac/rutac_blanco.png" alt="Ruta C Logo" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <p style="color: #ffffff; text-align: left; font-size: 20px; font-weight: 600; margin: 0; opacity: 0.9;">Haz crecer tu negocio</p>
                </div>
                <h2 style="color: #ffffff; font-size: 32px; font-weight: 600; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">Tu Plantilla</h2>
            </div>
            
            <!-- Content -->
            <div style="padding: 40px 30px; background-color: #ffffff;">
                ${content}
            </div>
            
            <!-- Footer -->
            <div style="background-color: #1e3a8a; color: #ffffff; text-align: center; padding: 25px 30px; font-size: 14px;">
                <p style="margin: 5px 0; color: #cbd5e1;">Este es un correo automático, por favor no respondas a este mensaje.</p>
                <p style="color: #94a3b8; font-size: 12px;">&copy; ${sampleData.current_year} ${sampleData.project_name}. Todos los derechos reservados.</p>
            </div>
        </div>
    `;
}

// Función para actualizar HTML generado
function updateGeneratedHTML() {
    const components = document.querySelectorAll('.template-component');
    let htmlContent = '';
    let textContent = '';
    
    components.forEach(component => {
        const previewDiv = component.querySelector('.component-preview');
        htmlContent += previewDiv.innerHTML + '\n';
        
        // Generar texto plano
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = previewDiv.innerHTML;
        textContent += tempDiv.textContent + '\n\n';
    });
    
    // Actualizar campos ocultos
    document.getElementById('html_content').value = htmlContent.trim();
    document.getElementById('text_content').value = textContent.trim();
    
    // Actualizar vista previa
    updatePreview();
    
    // Habilitar botón de prueba si hay contenido
    const testEmailBtn = document.getElementById('testEmailBtn');
    if (htmlContent.trim()) {
        testEmailBtn.disabled = false;
        testEmailBtn.classList.remove('btn-outline-info');
        testEmailBtn.classList.add('btn-info');
    } else {
        testEmailBtn.disabled = true;
        testEmailBtn.classList.remove('btn-info');
        testEmailBtn.classList.add('btn-outline-info');
    }
}

// Función para abrir modal de envío de prueba
function openTestEmailModal() {
    const subject = document.getElementById('subject').value;
    const htmlContent = document.getElementById('html_content').value;
    
    if (!subject.trim()) {
        alert('Por favor, ingresa un asunto para el email antes de enviar la prueba.');
        return;
    }
    
    if (!htmlContent.trim()) {
        alert('Por favor, construye tu plantilla antes de enviar la prueba.');
        return;
    }
    
    // Actualizar vista previa del modal
    document.getElementById('testSubject').textContent = subject;
    document.getElementById('testRecipient').textContent = 'Se mostrará cuando ingreses el email';
    document.getElementById('testContentPreview').textContent = 'Plantilla construida con ' + document.querySelectorAll('.template-component').length + ' componente(s)';
    
    // Limpiar campo de email
    document.getElementById('testEmail').value = '';
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('testEmailModal'));
    modal.show();
}

// Función para enviar email de prueba
function sendTestEmail() {
    const testEmail = document.getElementById('testEmail').value.trim();
    const subject = document.getElementById('subject').value.trim();
    const htmlContent = document.getElementById('html_content').value.trim();
    const textContent = document.getElementById('text_content').value.trim();
    
    if (!testEmail) {
        alert('Por favor, ingresa un email de destino.');
        return;
    }
    
    if (!subject) {
        alert('Por favor, ingresa un asunto para el email.');
        return;
    }
    
    if (!htmlContent) {
        alert('Por favor, construye tu plantilla antes de enviar la prueba.');
        return;
    }
    
    // Deshabilitar botón durante el envío
    const sendBtn = document.getElementById('sendTestEmailBtn');
    const originalText = sendBtn.innerHTML;
    sendBtn.disabled = true;
    sendBtn.innerHTML = '<i class="ri-loader-4-line me-2"></i> Enviando...';
    
    // Preparar datos para el envío
    const formData = new FormData();
    formData.append('test_email', testEmail);
    formData.append('subject', subject);
    formData.append('html_content', htmlContent);
    formData.append('text_content', textContent);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Enviar solicitud
    fetch('{{ route("admin.email-templates.send-test") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensaje de éxito
            Swal.fire({
                icon: 'success',
                title: '¡Email Enviado!',
                text: data.message,
                confirmButtonText: 'Perfecto',
                confirmButtonColor: '#28a745'
            });
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('testEmailModal'));
            modal.hide();
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Mostrar mensaje de error
        Swal.fire({
            icon: 'error',
            title: 'Error al Enviar',
            text: error.message || 'Ocurrió un error al enviar el email de prueba.',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#dc3545'
        });
    })
    .finally(() => {
        // Restaurar botón
        sendBtn.disabled = false;
        sendBtn.innerHTML = originalText;
    });
}

// Función para actualizar vista previa del modal en tiempo real
function updateTestModalPreview() {
    const testEmail = document.getElementById('testEmail').value.trim();
    const testRecipient = document.getElementById('testRecipient');
    
    if (testEmail) {
        testRecipient.textContent = testEmail;
    } else {
        testRecipient.textContent = 'Se mostrará cuando ingreses el email';
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('Constructor de plantillas inicializado');
    
    // Event listeners para botones de componentes
    const componentButtons = document.querySelectorAll('[data-component]');
    componentButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const componentType = this.getAttribute('data-component');
            createComponent(componentType);
        });
    });
    
    // Botón limpiar plantilla
    document.getElementById('clearTemplate').addEventListener('click', function() {
        if (confirm('¿Estás seguro de que quieres limpiar toda la plantilla?')) {
            document.getElementById('templateBuilder').innerHTML = `
                <div class="text-center text-muted py-5" id="emptyState">
                    <i class="ri-layout-grid-line fs-1 mb-3"></i>
                    <h6>Área de Construcción</h6>
                    <p class="mb-0">Haz clic en los botones de arriba para agregar elementos a tu plantilla</p>
                </div>
            `;
            componentCounter = 0;
            updateGeneratedHTML();
        }
    });

    // Botón de vista previa
    document.getElementById('previewBtn').addEventListener('click', updatePreview);

    // Botón de envío de prueba
    document.getElementById('testEmailBtn').addEventListener('click', openTestEmailModal);

    // Botón de envío de prueba en el modal
    document.getElementById('sendTestEmailBtn').addEventListener('click', sendTestEmail);

    // Actualizar vista previa del modal en tiempo real
    document.getElementById('testEmail').addEventListener('input', updateTestModalPreview);

    // Validación del formulario
    document.getElementById('templateForm').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const subject = document.getElementById('subject').value.trim();
        const htmlContent = document.getElementById('html_content').value.trim();

        if (!name || !subject || !htmlContent) {
            e.preventDefault();
            alert('Por favor, completa todos los campos obligatorios.');
            return false;
        }
    });

    // Inicializar vista previa
    updatePreview();
});
</script>
@endpush
