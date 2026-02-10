@extends('layouts.admin')

@section('title', 'Plantillas de: ' . $category->name)

@section('content')
<div class="container card my-3 shadow-sm">
    @include('whatsapp._nav')

    {{-- Nivel 1 → Nivel 2: estás dentro de una categoría --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 border-bottom pb-3">
        <div>
            <a href="{{ route('admin.whatsapp.categories.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                <i class="ri-arrow-left-line"></i>
            </a>
            <strong>Categoría:</strong> {{ $category->name }}
            <span class="text-muted ms-2">({{ $category->templates->count() }} plantilla(s))</span>
        </div>
        <div>
            <a href="{{ route('admin.whatsapp.categories.edit', $category) }}" class="btn btn-sm btn-warning me-2">Editar categoría</a>
            <a href="{{ route('admin.whatsapp.templates.create') }}?category_id={{ $category->id }}" class="btn btn-info btn-sm">
                <i class="icon-base ri ri-add-fill me-1"></i> Nueva plantilla en esta categoría
            </a>
        </div>
    </div>

    <div id="Data">
        <div class="table-responsive p-3 h-100">
            <table class="table table-hover mb-0" id="tablaWhatsapp">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 py-3 px-4 text-uppercase fw-semibold">Nombre</th>
                        <th class="border-0 py-3 px-4 text-uppercase fw-semibold">Grupo / Canal</th>
                        <th class="border-0 py-3 px-4 text-uppercase fw-semibold">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($category->templates as $t)
                        <tr class="align-middle" tabindex="0" style="cursor: pointer;"
                            data-id="{{ $t->id }}"
                            data-edit-url="{{ route('admin.whatsapp.templates.edit', $t) }}"
                            data-delete-url="{{ route('admin.whatsapp.templates.destroy', $t) }}">
                            <td class="px-4 py-3 fw-semibold text-dark">{{ $t->name }}</td>
                            <td class="px-4 py-3">{{ $t->group_code ?: '-' }} / {{ $t->channel ?: 'whatsapp' }}</td>
                            <td class="px-4 py-3">
                                @if($t->is_active)
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-secondary">Inactiva</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
                                No hay plantillas en esta categoría.
                                <a href="{{ route('admin.whatsapp.templates.create') }}?category_id={{ $category->id }}">Crear la primera</a>.
                            </td>
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
            const editUrl = this.dataset.editUrl;
            const deleteUrl = this.dataset.deleteUrl;
            dropdown.innerHTML = ''
                + '<a class="dropdown-item" href="' + editUrl + '"><i class="ri-edit-line me-2"></i>Editar</a>'
                + '<div class="dropdown-divider"></div>'
                + '<form method="POST" action="' + deleteUrl + '" onsubmit="return confirm(\'¿Eliminar esta plantilla?\');" class="dropdown-item p-0"><input type="hidden" name="_token" value="' + token + '"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="dropdown-item border-0 w-100 text-start text-danger"><i class="ri-delete-bin-line me-2"></i>Eliminar</button></form>';
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
