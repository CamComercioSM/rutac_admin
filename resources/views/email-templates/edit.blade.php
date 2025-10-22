@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/email-template-builder.css') }}">
@endpush

@section('title', 'Editar Plantilla de Correo')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1 text-dark fw-bold">
                        <i class="ri-edit-line text-primary me-2"></i>
                        Editar Plantilla
                    </h2>
                    <p class="text-muted mb-0">Modifica la plantilla de correo "{{ $emailTemplate->name }}"</p>
                </div>
                <a href="{{ route('admin.emailTemplates.index') }}" class="btn btn-outline-secondary">
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
                    <form action="{{ route('admin.emailTemplates.update', $emailTemplate) }}" method="POST" id="templateForm">
                        @csrf
                        @method('PUT')
                        
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
                                               value="{{ old('name', $emailTemplate->name) }}" 
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
                                               value="{{ old('subject', $emailTemplate->subject) }}" 
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
                                              placeholder="Describe el propósito de esta plantilla...">{{ old('description', $emailTemplate->description) }}</textarea>
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
                                    @php
                                        $selectedVariables = $emailTemplate->variables ?? [];
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="variables[]" value="business_name" id="var_business_name"
                                                       {{ in_array('business_name', $selectedVariables) ? 'checked' : '' }}>
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
                                                <input class="form-check-input" type="checkbox" name="variables[]" value="contact_person" id="var_contact_person"
                                                       {{ in_array('contact_person', $selectedVariables) ? 'checked' : '' }}>
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
                                                <input class="form-check-input" type="checkbox" name="variables[]" value="project_name" id="var_project_name"
                                                       {{ in_array('project_name', $selectedVariables) ? 'checked' : '' }}>
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
                                                <input class="form-check-input" type="checkbox" name="variables[]" value="current_year" id="var_current_year"
                                                       {{ in_array('current_year', $selectedVariables) ? 'checked' : '' }}>
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
                                            <div class="d-flex flex-wrap gap-2">
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
                                    <input type="hidden" name="html_content" id="html_content" value="{{ old('html_content', $emailTemplate->html_content) }}" required>
                                    <input type="hidden" name="text_content" id="text_content" value="{{ old('html_content', $emailTemplate->text_content) }}">
                                    
                                    @error('html_content')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    
                                    <small class="text-muted">
                                        <i class="ri-information-line me-1"></i>
                                        Construye tu plantilla arrastrando y configurando elementos visualmente
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
                                    <a href="{{ route('admin.emailTemplates.index') }}" class="btn btn-outline-secondary">
                                        <i class="ri-close-line me-2"></i>
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line me-2"></i>
                                        Actualizar Plantilla
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos de ejemplo para la previsualización (usando los datos del servidor)
    const sampleData = @json($sampleData);
    
    // Variables globales del constructor
    let componentCounter = 0;
    let selectedComponent = null;
    
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
            htmlTemplate: (config) => `<div style="text-align: ${config.align || 'center'}; margin: 20px 0;">
                <img src="${config.src || 'https://via.placeholder.com/400x200'}" alt="${config.alt || 'Imagen'}" style="max-width: 100%; height: auto; border-radius: 8px;">
                ${config.caption ? `<p style="color: #666; font-size: 14px; margin-top: 10px; font-style: italic;">${config.caption}</p>` : ''}
            </div>`
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
    
    console.log('componentConfigs definido:', Object.keys(componentConfigs)); // Debug

    // Función para crear un componente
    function createComponent(type) {
        console.log('createComponent llamado con tipo:', type); // Debug
        const config = componentConfigs[type];
        if (!config) {
            console.log('No se encontró configuración para:', type); // Debug
            return;
        }

        componentCounter++;
        const componentId = `component_${componentCounter}`;
        
        const componentHtml = `
            <div class="template-component" id="${componentId}" data-type="${type}" style="border: 2px dashed #ddd; padding: 15px; margin: 10px 0; border-radius: 8px; position: relative;">
                <div class="component-header d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-primary">${config.label}</span>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary btn-sm edit-component" data-id="${componentId}">
                            <i class="ri-settings-3-line"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-component" data-id="${componentId}">
                            <i class="icon-base ri ri-delete-bin-line"></i>
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
        document.getElementById('emptyState').style.display = 'none';
        
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
                    <label class="form-label">URL de la imagen:</label>
                    <input type="url" class="form-control form-control-sm" value="https://via.placeholder.com/400x200" data-config="src">
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

    // Función para agregar event listeners a los componentes
    function addComponentEventListeners(componentId, type) {
        const component = document.getElementById(componentId);
        
        // Botón editar
        component.querySelector('.edit-component').addEventListener('click', function() {
            const configDiv = component.querySelector('.component-config');
            const previewDiv = component.querySelector('.component-preview');
            
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
                updateGeneratedHTML();
                
                // Mostrar estado vacío si no hay componentes
                if (document.querySelectorAll('.template-component').length === 0) {
                    document.getElementById('emptyState').style.display = 'block';
                }
            }
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
            componentConfig[input.getAttribute('data-config')] = input.value;
        });
        
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
            document.getElementById('previewContent').innerHTML = previewContent;
        } else {
            document.getElementById('previewContent').innerHTML = '<p class="text-muted text-center">Construye tu plantilla para ver la vista previa</p>';
        }
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
    }

    // Función para cargar plantilla existente
    function loadExistingTemplate() {
        const htmlContent = document.getElementById('html_content').value;
        if (htmlContent && htmlContent.trim()) {
            // Ocultar estado vacío
            document.getElementById('emptyState').style.display = 'none';
            
            // Crear un componente de texto con el contenido existente
            componentCounter++;
            const componentId = `component_${componentCounter}`;
            
            const componentHtml = `
                <div class="template-component" id="${componentId}" data-type="text" style="border: 2px dashed #ddd; padding: 15px; margin: 10px 0; border-radius: 8px; position: relative;">
                    <div class="component-header d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-primary">Contenido Existente</span>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary btn-sm edit-component" data-id="${componentId}">
                                <i class="ri-settings-3-line"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-component" data-id="${componentId}">
                                <i class="icon-base ri ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                    <div class="component-preview" id="preview_${componentId}">
                        ${htmlContent}
                    </div>
                    <div class="component-config" id="config_${componentId}" style="display: none;">
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label">Contenido HTML:</label>
                                <textarea class="form-control" rows="8" data-config="html">${htmlContent}</textarea>
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-success btn-sm apply-config" data-id="${componentId}">
                                    <i class="ri-check-line me-1"></i> Aplicar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('templateBuilder').insertAdjacentHTML('beforeend', componentHtml);
            addComponentEventListeners(componentId, 'text');
        }
    }

    // Event listeners para botones de componentes
    console.log('Buscando botones con data-component...'); // Debug
    const componentButtons = document.querySelectorAll('[data-component]');
    console.log('Botones encontrados:', componentButtons.length); // Debug
    
    componentButtons.forEach((button, index) => {
        console.log(`Botón ${index}:`, button.getAttribute('data-component')); // Debug
        button.addEventListener('click', function() {
            const componentType = this.getAttribute('data-component');
            console.log('Botón clickeado:', componentType); // Debug
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
            updateGeneratedHTML();
        }
    });

    // Botón de vista previa
    document.getElementById('previewBtn').addEventListener('click', updatePreview);

    // Actualizar vista previa en tiempo real
    document.getElementById('subject').addEventListener('input', updatePreview);

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

    // Cargar plantilla existente al inicializar
    loadExistingTemplate();
    
    // Inicializar vista previa
    updatePreview();
});
</script>
@endpush
