@extends('layouts.admin')

@section('title', 'Plantillas WhatsApp')

@section('content')
<div class="container card my-3 shadow-sm">
    <div id="Data">
        <form method="GET" action="{{ route('admin.whatsapp.templates.index') }}" class="border p-3 mt-3">
            <div class="row justify-content-center align-items-end">
                <div class="col-12 col-md-3 mb-2">
                    <label class="form-label small">Categoría</label>
                    <select name="category_id" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 mb-2">
                    <label class="form-label small">Estado</label>
                    <select name="is_active" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Activas</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactivas</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 mb-2 text-center">
                    <button type="submit" class="btn btn-sm btn-warning"><i class="ri-filter-line me-1"></i> Filtrar</button>
                </div>
            </div>
        </form>

        <div class="table-responsive p-3 h-100">
            <div id="toolbar" class="d-flex mb-3">
                <a href="{{ route('admin.whatsapp.templates.create') }}" class="btn btn-info me-3">
                    <i class="icon-base ri ri-add-fill me-2"></i> Crear
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ri-check-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <table class="table table-hover mb-0" id="tablaWhatsapp">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 py-3 px-4 text-uppercase fw-semibold">Nombre</th>
                        <th class="border-0 py-3 px-4 text-uppercase fw-semibold">Categoría</th>
                        <th class="border-0 py-3 px-4 text-uppercase fw-semibold">Grupo / Canal</th>
                        <th class="border-0 py-3 px-4 text-uppercase fw-semibold">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                        <tr class="align-middle" tabindex="0" style="cursor: pointer;"
                            data-id="{{ $template->id }}"
                            data-edit-url="{{ route('admin.whatsapp.templates.edit', $template) }}"
                            data-delete-url="{{ route('admin.whatsapp.templates.destroy', $template) }}">
                            <td class="px-4 py-3 fw-semibold text-dark">{{ $template->name }}</td>
                            <td class="px-4 py-3">{{ $template->category?->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $template->group_code ?: '-' }} / {{ $template->channel ?: 'whatsapp' }}</td>
                            <td class="px-4 py-3">
                                @if($template->is_active)
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-secondary">Inactiva</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No hay plantillas. <a href="{{ route('admin.whatsapp.templates.create') }}">Crear la primera</a>.</td>
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
