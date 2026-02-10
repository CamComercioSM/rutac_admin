@extends('layouts.admin')

@section('title', 'Editar plantilla WhatsApp')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1 text-dark fw-bold">
                        <i class="icon-base ri ri-edit-line text-primary me-2"></i>
                        Editar plantilla
                    </h2>
                    <p class="text-muted mb-0">{{ $template->name }}</p>
                </div>
                <a href="{{ route('admin.whatsapp.templates.index') }}" class="btn btn-outline-secondary"><i class="icon-base ri ri-arrow-left-line me-2"></i>Volver</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('admin.whatsapp.templates.update', $template) }}" method="POST" id="templateForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}" {{ old('category_id', $template->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-semibold">Nombre (único) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $template->name) }}" required maxlength="255">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="group_code" class="form-label fw-semibold">Código de grupo</label>
                                <input type="text" class="form-control @error('group_code') is-invalid @enderror" id="group_code" name="group_code" value="{{ old('group_code', $template->group_code) }}" maxlength="100">
                                @error('group_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="channel" class="form-label fw-semibold">Canal</label>
                                <input type="text" class="form-control @error('channel') is-invalid @enderror" id="channel" name="channel" value="{{ old('channel', $template->channel ?? 'whatsapp') }}" maxlength="50">
                                @error('channel')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Activa</label>
                                </div>
                            </div>
                            <input type="hidden" name="default_payload" value="{{ old('default_payload', $template->default_payload ? json_encode($template->default_payload) : '{}') }}">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Expected fields (plantillaDatos)</label>
                                <small class="text-muted d-block mb-2">Campos que se obtienen de la BD al enviar (ej: usuarioNOMBRE, empresaRAZONSOCIAL). Marque "Requerido" si es obligatorio.</small>
                                <div id="expectedFieldsContainer" class="mb-2"></div>
                                <button type="button" id="addExpectedField" class="btn btn-outline-primary btn-sm"><i class="icon-base ri ri-add-line me-1"></i>Agregar campo</button>
                                <input type="hidden" name="expected_fields" id="expected_fields" value="{{ old('expected_fields', $template->expected_fields ? json_encode($template->expected_fields) : '[]') }}" />
                                @error('expected_fields')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="icon-base ri ri-save-line me-2"></i>Guardar</button>
                            <a href="{{ route('admin.whatsapp.templates.index') }}" class="btn btn-outline-secondary">Cancelar</a>
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
(function() {
    var expectedFieldsContainer = document.getElementById('expectedFieldsContainer');
    var expectedFieldsHidden = document.getElementById('expected_fields');

    function escapeAttr(s) {
        if (s == null) return '';
        return String(s).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function addExpectedFieldRow(key, required) {
        var row = document.createElement('div');
        row.className = 'expected-field-row row g-2 mb-2 align-items-center';
        row.innerHTML = '<div class="col-md-5"><input type="text" class="form-control key-input" placeholder="Clave (ej: usuarioNOMBRE)" value="' + escapeAttr(key) + '" /></div>' +
            '<div class="col-auto"><label class="form-check-label me-2"><input type="checkbox" class="form-check-input required-input" ' + (required ? 'checked' : '') + ' /> Requerido</label></div>' +
            '<div class="col-auto"><button type="button" class="btn btn-outline-danger btn-sm remove-field"><i class="icon-base ri ri-delete-bin-line"></i></button></div>';
        expectedFieldsContainer.appendChild(row);
    }

    function buildExpectedFieldsJson() {
        var rows = expectedFieldsContainer.querySelectorAll('.expected-field-row');
        var arr = [];
        rows.forEach(function(row) {
            var key = row.querySelector('.key-input').value.trim();
            if (key) {
                arr.push({ key: key, required: row.querySelector('.required-input').checked });
            }
        });
        return JSON.stringify(arr);
    }

    try {
        var efData = JSON.parse(expectedFieldsHidden.value || '[]');
        expectedFieldsContainer.innerHTML = '';
        if (Array.isArray(efData) && efData.length > 0) {
            efData.forEach(function(item) {
                addExpectedFieldRow(item.key, item.required === true);
            });
        } else {
            addExpectedFieldRow('', false);
        }
    } catch (_) {
        expectedFieldsContainer.innerHTML = '';
        addExpectedFieldRow('', false);
    }

    document.getElementById('addExpectedField').addEventListener('click', function() {
        addExpectedFieldRow('', false);
    });

    expectedFieldsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-field')) {
            var row = e.target.closest('.expected-field-row');
            if (expectedFieldsContainer.querySelectorAll('.expected-field-row').length > 1) row.remove();
        }
    });

    document.getElementById('templateForm').addEventListener('submit', function(e) {
        expectedFieldsHidden.value = buildExpectedFieldsJson();
    });
})();
</script>
@endpush
