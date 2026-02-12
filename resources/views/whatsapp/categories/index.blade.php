@extends('layouts.admin')

@section('title', 'Categorías de plantillas WhatsApp')

@section('content')
<div class="container card my-3 shadow-sm">
    <div id="Data">
        <div class="table-responsive p-3 h-100">
            <div id="toolbar" class="d-flex mb-3">
                <a href="{{ route('admin.whatsapp.categories.create') }}" class="btn btn-info me-3">
                    <i class="icon-base ri ri-add-fill me-2"></i> Crear
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ri-check-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <table class="table table-hover mb-0" id="tablaWhatsapp">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 py-3 px-4 text-uppercase fw-semibold">Nombre</th>
                        <th class="border-0 py-3 px-4 text-uppercase fw-semibold">Código</th>
                        <th class="border-0 py-3 px-4 text-uppercase fw-semibold">Descripción</th>
                        <th class="border-0 py-3 px-4 text-uppercase fw-semibold">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr class="align-middle" tabindex="0" style="cursor: pointer;"
                            data-id="{{ $category->id }}"
                            data-show-url="{{ route('admin.whatsapp.categories.show', $category) }}"
                            data-edit-url="{{ route('admin.whatsapp.categories.edit', $category) }}"
                            data-toggle-url="{{ route('admin.whatsapp.categories.toggle-status', $category) }}"
                            data-delete-url="{{ route('admin.whatsapp.categories.destroy', $category) }}"
                            data-is-active="{{ $category->is_active ? '1' : '0' }}">
                            <td class="px-4 py-3">
                                <span class="fw-semibold text-dark">{{ $category->name }}</span>
                                <small class="d-block text-muted">Clic en la fila para acciones</small>
                            </td>
                            <td class="px-4 py-3"><code>{{ $category->code }}</code></td>
                            <td class="px-4 py-3 text-muted">{{ \Illuminate\Support\Str::limit($category->description, 50) }}</td>
                            <td class="px-4 py-3">
                                @if($category->is_active)
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-secondary">Inactiva</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No hay categorías. <a href="{{ route('admin.whatsapp.categories.create') }}">Crear la primera</a>.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="MenurowTable" class="dropdown-menu shadow" style="position:fixed; display:none; z-index:1050;"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.getElementById('MenurowTable');
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    document.querySelectorAll('#tablaWhatsapp tbody tr[data-id]').forEach(function(tr) {
        tr.addEventListener('click', function(e) {
            e.preventDefault();
            if (e.target.tagName === 'A' && e.target.getAttribute('href') && !e.target.getAttribute('href').startsWith('#')) return;
            const id = this.dataset.id;
            const showUrl = this.dataset.showUrl;
            const editUrl = this.dataset.editUrl;
            const toggleUrl = this.dataset.toggleUrl;
            const deleteUrl = this.dataset.deleteUrl;
            const isActive = this.dataset.isActive === '1';
            const toggleLabel = isActive ? 'Desactivar' : 'Activar';

            dropdown.innerHTML = ''
                + '<a class="dropdown-item" href="' + showUrl + '"><i class="ri-file-list-line me-2"></i>Ver plantillas</a>'
                + '<a class="dropdown-item" href="' + editUrl + '"><i class="ri-edit-line me-2"></i>Editar</a>'
                + '<form method="POST" action="' + toggleUrl + '" class="dropdown-item p-0"><input type="hidden" name="_token" value="' + token + '"><button type="submit" class="dropdown-item border-0 w-100 text-start bg-transparent"><i class="ri-toggle-' + (isActive ? 'on' : 'off') + '-line me-2"></i>' + toggleLabel + '</button></form>'
                + '<div class="dropdown-divider"></div>'
                + '<form method="POST" action="' + deleteUrl + '" onsubmit="return confirm(\'¿Eliminar esta categoría?\');" class="dropdown-item p-0"><input type="hidden" name="_token" value="' + token + '"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="dropdown-item border-0 w-100 text-start text-danger"><i class="ri-delete-bin-line me-2"></i>Eliminar</button></form>';
            const rect = tr.getBoundingClientRect();
            dropdown.style.display = 'block';
            dropdown.style.top = (rect.bottom + 2) + 'px';
            dropdown.style.left = rect.left + 'px';
        });
    });

    document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target) && !e.target.closest('#tablaWhatsapp tbody tr[data-id]')) dropdown.style.display = 'none';
    });
    dropdown.addEventListener('click', function() { dropdown.style.display = 'none'; });
});
</script>
@endpush
