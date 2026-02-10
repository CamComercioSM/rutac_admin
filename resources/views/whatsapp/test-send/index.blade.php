@extends('layouts.admin')

@section('title', 'Probar envío WhatsApp')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container card my-3 shadow-sm">
    @include('whatsapp._nav')

    <div class="row mb-3">
        <div class="col-12">
            <h5 class="mb-1 fw-bold">Probar envío de plantilla WhatsApp</h5>
            <p class="text-muted small mb-0">Envía un mensaje de prueba a un número usando una plantilla</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card border shadow-none">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold">Formulario de prueba</h5>
                </div>
                <div class="card-body p-4">
                    <form id="formTestSend">
                        <div class="mb-3">
                            <label for="phone" class="form-label fw-semibold">Número destino (WhatsApp) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="573114158174" required maxlength="20">
                            <small class="text-muted">Ej: 573114158174 (código país + número)</small>
                            <div id="phoneError" class="invalid-feedback d-none"></div>
                        </div>
                        <div class="mb-3">
                            <label for="category_filter" class="form-label fw-semibold">Filtrar por categoría</label>
                            <select class="form-select" id="category_filter" name="category_filter">
                                <option value="">Todas las categorías</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="template_name" class="form-label fw-semibold">Plantilla <span class="text-danger">*</span></label>
                            <select class="form-select" id="template_name" name="template_name" required>
                                <option value="">Seleccione una plantilla</option>
                                @foreach($templates as $t)
                                    <option value="{{ $t->name }}" data-expected="{{ json_encode($t->expected_fields ?? []) }}" data-default="{{ json_encode($t->default_payload ?? []) }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="dynamicFields" class="mb-4">
                            <!-- Se rellenan por JS según expected_fields de la plantilla -->
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success" id="btnSend">
                                <i class="ri-send-plane-line me-2"></i>
                                Enviar prueba
                            </button>
                            <a href="{{ route('admin.whatsapp.logs.index') }}" class="btn btn-outline-secondary">Ver logs</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="resultMessage" class="alert d-none mt-3"></div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var form = document.getElementById('formTestSend');
    var templateSelect = document.getElementById('template_name');
    var categoryFilter = document.getElementById('category_filter');
    var dynamicFields = document.getElementById('dynamicFields');
    var resultMessage = document.getElementById('resultMessage');
    var btnSend = document.getElementById('btnSend');

    var allTemplates = @json($templates->map(fn($t) => ['name' => $t->name, 'category_id' => $t->category_id, 'expected_fields' => $t->expected_fields ?? [], 'default_payload' => $t->default_payload ?? []]));

    function filterTemplates() {
        var catId = categoryFilter.value;
        var opts = templateSelect.querySelectorAll('option');
        opts.forEach(function(opt) {
            if (opt.value === '') { opt.style.display = ''; return; }
            var t = allTemplates.find(function(x) { return x.name === opt.value; });
            if (!t) { opt.style.display = ''; return; }
            if (!catId) { opt.style.display = ''; return; }
            var templateEl = document.querySelector('#template_name option[value="' + opt.value + '"]');
            if (!templateEl) return;
            opt.style.display = '';
        });
        var firstOpt = templateSelect.querySelector('option[value=""]');
        if (firstOpt) firstOpt.selected = true;
        buildDynamicFields();
    }

    function buildDynamicFields() {
        var sel = templateSelect.options[templateSelect.selectedIndex];
        dynamicFields.innerHTML = '';
        if (!sel || !sel.value) return;
        var expected = [];
        try {
            expected = JSON.parse(sel.getAttribute('data-expected') || '[]');
        } catch (e) { expected = []; }
        var defaults = {};
        try {
            defaults = JSON.parse(sel.getAttribute('data-default') || '{}');
        } catch (e) { defaults = {}; }
        if (!Array.isArray(expected)) expected = [];
        expected.forEach(function(f) {
            var key = f.key || f.name || 'field';
            var required = f.required === true;
            var label = key;
            var val = defaults[key] !== undefined ? defaults[key] : '';
            var div = document.createElement('div');
            div.className = 'mb-3';
            div.innerHTML = '<label for="data_' + key + '" class="form-label">' + label + (required ? ' <span class="text-danger">*</span>' : '') + '</label>' +
                '<input type="text" class="form-control" id="data_' + key + '" name="data[' + key + ']" value="' + (val || '').replace(/"/g, '&quot;') + '" ' + (required ? 'required' : '') + '>';
            dynamicFields.appendChild(div);
        });
    }

    categoryFilter.addEventListener('change', function() {
        var catId = this.value;
        templateSelect.innerHTML = '<option value="">Seleccione una plantilla</option>';
        allTemplates.forEach(function(t) {
            if (catId && t.category_id != catId) return;
            var opt = document.createElement('option');
            opt.value = t.name;
            opt.setAttribute('data-expected', JSON.stringify(t.expected_fields));
            opt.setAttribute('data-default', JSON.stringify(t.default_payload));
            opt.textContent = t.name;
            templateSelect.appendChild(opt);
        });
        buildDynamicFields();
    });

    templateSelect.addEventListener('change', buildDynamicFields);

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var phone = document.getElementById('phone').value.trim();
        var templateName = document.getElementById('template_name').value;
        if (!phone || !templateName) {
            resultMessage.className = 'alert alert-warning mt-3';
            resultMessage.textContent = 'Complete número y plantilla.';
            resultMessage.classList.remove('d-none');
            return;
        }
        var data = {};
        dynamicFields.querySelectorAll('input[name^="data["]').forEach(function(input) {
            var m = input.name.match(/data\[([^\]]+)\]/);
            if (m) data[m[1]] = input.value;
        });

        btnSend.disabled = true;
        btnSend.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
        resultMessage.classList.add('d-none');

        fetch('{{ route("admin.whatsapp.send-test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ phone: phone, template_name: templateName, data: data })
        })
        .then(function(r) { return r.json().then(function(j) { return { ok: r.ok, status: r.status, json: j }; }); })
        .then(function(res) {
            if (res.ok && res.json.ok) {
                resultMessage.className = 'alert alert-success mt-3';
                resultMessage.innerHTML = '<i class="ri-check-line me-2"></i>' + (res.json.message || 'Enviado.');
            } else {
                resultMessage.className = 'alert alert-danger mt-3';
                resultMessage.innerHTML = '<i class="ri-error-warning-line me-2"></i>' + (res.json.message || 'Error al enviar.');
            }
            resultMessage.classList.remove('d-none');
        })
        .catch(function(err) {
            resultMessage.className = 'alert alert-danger mt-3';
            resultMessage.innerHTML = '<i class="ri-error-warning-line me-2"></i>Error de conexión.';
            resultMessage.classList.remove('d-none');
        })
        .finally(function() {
            btnSend.disabled = false;
            btnSend.innerHTML = '<i class="ri-send-plane-line me-2"></i>Enviar prueba';
        });
    });

    buildDynamicFields();
})();
</script>
@endpush
