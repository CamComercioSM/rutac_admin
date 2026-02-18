{{-- Navegación entre secciones de WhatsApp (mismo nivel) --}}
<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <a href="{{ route('admin.whatsapp.categories.index') }}" class="btn btn-sm {{ request()->routeIs('admin.whatsapp.categories.*') ? 'btn-primary' : 'btn-outline-secondary' }}">
        <i class="ri-folder-3-line me-1"></i> Categorías
    </a>
    <a href="{{ route('admin.whatsapp.templates.index') }}" class="btn btn-sm {{ request()->routeIs('admin.whatsapp.templates.*') ? 'btn-primary' : 'btn-outline-secondary' }}">
        <i class="ri-whatsapp-line me-1"></i> Plantillas
    </a>
    <a href="{{ route('admin.whatsapp.test-send.index') }}" class="btn btn-sm {{ request()->routeIs('admin.whatsapp.test-send.*') ? 'btn-primary' : 'btn-outline-secondary' }}">
        <i class="ri-send-plane-line me-1"></i> Probar envío
    </a>
    <a href="{{ route('admin.whatsapp.logs.index') }}" class="btn btn-sm {{ request()->routeIs('admin.whatsapp.logs.*') ? 'btn-primary' : 'btn-outline-secondary' }}">
        <i class="ri-file-list-3-line me-1"></i> Logs
    </a>
</div>
