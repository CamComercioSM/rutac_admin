@extends('layouts.admin')

@section('title', 'Logs de mensajes WhatsApp')

@section('content')
<div class="container card my-3 shadow-sm">
    <div id="Data">
        <form method="GET" action="{{ route('admin.whatsapp.logs.index') }}" class="border p-3 mt-3">
            <div class="row justify-content-center align-items-end flex-wrap">
                <div class="col-12 col-md-2 mb-2">
                    <label class="form-label small">Desde</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-12 col-md-2 mb-2">
                    <label class="form-label small">Hasta</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-12 col-md-2 mb-2">
                    <label class="form-label small">Estado</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="queued" {{ request('status') === 'queued' ? 'selected' : '' }}>En cola</option>
                        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Enviado</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Fallido</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 mb-2">
                    <label class="form-label small">Teléfono</label>
                    <input type="text" name="phone" class="form-control form-control-sm" value="{{ request('phone') }}" placeholder="573...">
                </div>
                <div class="col-12 col-md-2 mb-2">
                    <label class="form-label small">Plantilla</label>
                    <input type="text" name="template_name" class="form-control form-control-sm" value="{{ request('template_name') }}">
                </div>
                <div class="col-12 col-md-2 mb-2 text-center">
                    <button type="submit" class="btn btn-sm btn-warning me-1"><i class="ri-filter-line me-1"></i> Filtrar</button>
                    <a href="{{ route('admin.whatsapp.logs.index') }}" class="btn btn-sm btn-danger"><i class="ri-filter-off-line me-1"></i> Limpiar</a>
                </div>
            </div>
        </form>

        <div class="table-responsive p-3 h-100">
            <div id="toolbar" class="d-flex mb-3">
                <a href="{{ route('admin.whatsapp.test-send.index') }}" class="btn btn-success me-3">
                    <i class="ri-send-plane-line me-2"></i> Probar envío
                </a>
            </div>

            <table class="table table-hover mb-0" id="tablaWhatsapp">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 py-3 px-4 text-uppercase fw-semibold">Fecha</th>
                        <th class="border-0 py-3 px-4 text-uppercase fw-semibold">Teléfono</th>
                        <th class="border-0 py-3 px-4 text-uppercase fw-semibold">Plantilla</th>
                        <th class="border-0 py-3 px-4 text-uppercase fw-semibold">Estado</th>
                        <th class="border-0 py-3 px-4 text-uppercase fw-semibold">ID mensaje</th>
                        <th class="border-0 py-3 px-4 text-uppercase fw-semibold">Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="align-middle" tabindex="0" style="cursor: pointer;"
                            data-id="{{ $log->id }}"
                            data-show-url="{{ route('admin.whatsapp.logs.show', $log) }}">
                            <td class="px-4 py-3">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $log->phone }}</td>
                            <td class="px-4 py-3">{{ $log->template_name }}</td>
                            <td class="px-4 py-3">
                                @if($log->status === 'sent')
                                    <span class="badge bg-success">Enviado</span>
                                @elseif($log->status === 'failed')
                                    <span class="badge bg-danger">Fallido</span>
                                @else
                                    <span class="badge bg-secondary">{{ $log->status }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 small font-monospace">{{ \Illuminate\Support\Str::limit($log->provider_message_id, 12) ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $log->user ? $log->user->name : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No hay registros.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($logs->hasPages())
                <div class="d-flex justify-content-center py-3">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<div id="MenurowTable" class="dropdown-menu shadow" style="position:fixed; display:none; z-index:1050;"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.getElementById('MenurowTable');

    document.querySelectorAll('#tablaWhatsapp tbody tr[data-id]').forEach(function(tr) {
        tr.addEventListener('click', function(e) {
            e.preventDefault();
            const showUrl = this.dataset.showUrl;
            dropdown.innerHTML = '<a class="dropdown-item" href="' + showUrl + '"><i class="ri-eye-line me-2"></i>Ver detalles</a>';
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
