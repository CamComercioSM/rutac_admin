@extends('layouts.list', ['titulo'=> 'Inscripciones', 'tituloModal'=> 'Inscripción'])

@section('form-filters')

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="tipopersona">Tipo de persona</label>
        <select class="form-select" name="tipopersona" id="tipopersona">
            <option value="" >Seleccione una opción</option>
            @foreach ($tipoPersona as $item)
                <option value="{{$item->tipopersona_id}}" >{{$item->tipoPersonaNOMBRE}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="sector">Sector</label>
        <select class="form-select" name="sector" id="sector">
            <option value="" >Seleccione una opción</option>
            @foreach ($sectores as $item)
                <option value="{{$item->sector_id}}" >{{$item->sectorNOMBRE}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="tamano">Tamaño</label>
        <select class="form-select" name="tamano" id="tamano">
            <option value="" selected >Seleccione una opción</option>
            @foreach ($tamanos as $item)
                <option value="{{$item->tamano_id}}" >{{$item->tamanoNOMBRE}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="etapa">Etapa</label>
        <select class="form-select" name="etapa" id="etapa">
            <option value="" selected >Seleccione una opción</option>
            @foreach ($etapas as $item)
                <option value="{{$item->etapa_id}}" >{{$item->name}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-3 form-group mb-3">
        <label class="form-label" for="fecha_inicio">Fecha inicio</label>
        <input class="form-control" type="date" name="fecha_inicio" id="fecha_inicio">
    </div>

    <div class="col-12 col-md-3 form-group mb-3">
        <label class="form-label" for="fecha_inicio">Fecha fin</label>
        <input class="form-control" type="date" name="fecha_fin" id="fecha_fin">
    </div>

@endsection

@section('script')
    <script> 

        const editar = '{!! $esAsesor ? '' : '<a class="dropdown-item" href="/unidadesProductivas/ROWID/edit">Editar</a>' !!}';
        const transformar = '{!! $esAsesor ? '' : '<a class="dropdown-item" href="/unidadesProductivas/ROWID/transformar">Transformar</a>' !!}';

        window.TABLA = {
            urlApi: '/unidadesProductivas',
            sortName: 'fecha_creacion',
            
            menu_row: ` <a class="dropdown-item" href="/unidadesProductivas/ROWID" >Ver detalles</a>
                        ${editar} ${transformar}
                        <a class="dropdown-item" href="/diagnosticosResultados/list?unidad=ROWID">Diagnósticos</a>
                        <a class="dropdown-item" href="/inscripciones/list?unidad=ROWID">Inscripciones</a>
                        <a class="dropdown-item" href="/intervenciones/list?unidad=ROWID">Intervenciones</a>
                        <a class="dropdown-item" href="javascript:void(0);" onclick="abrirModalWhatsApp(ROWID)">Enviar mensaje vía WhatsApp</a>
                    `,
            
            columns: [
                { data: 'fecha_creacion', title: 'Fecha de registro', orderable: true, render: v => window.formatearFecha(v) },
                { data: 'tipo_registro_rutac', title: 'Tipo registro', orderable: true },
                { data: 'nit', title: 'NIT', orderable: true },
                { data: 'business_name', title: 'Razón social', orderable: true },
                { data: 'name_legal_representative', title: 'Representante legal', orderable: true },
                { data: 'registration_email', title: 'Email', orderable: true },
                { data: 'tipo_persona', title: 'Tipo persona', orderable: true },
                { data: 'sector', title: 'Sector', orderable: true },
                { data: 'tamano', title: 'Tamaño', orderable: true },
                { data: 'etapa', title: 'Etapa', orderable: true },
                { data: 'departamento', title: 'Departamento', orderable: true },
                { data: 'municipio', title: 'Municipio', orderable: true }
            ]
        };

        // Función para abrir el modal de WhatsApp (vanilla JS, sin jQuery)
        window.abrirModalWhatsApp = function(unidadId) {
            fetch('/unidadesProductivas/' + unidadId, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(function(response) {
                const nombre = response.business_name || 'Unidad Productiva';
                const numeros = [];
                if (response.mobile) numeros.push({ value: response.mobile, label: 'Móvil', type: 'mobile' });
                if (response.telephone) numeros.push({ value: response.telephone, label: 'Teléfono', type: 'telephone' });
                if (response.contact_phone) numeros.push({ value: response.contact_phone, label: 'Tel. contacto', type: 'contact_phone' });
                const seen = new Set();
                const numerosUnicos = numeros.filter(n => {
                    const v = n.value.replace(/\D/g, '');
                    if (seen.has(v)) return false;
                    seen.add(v);
                    return true;
                });
                document.getElementById('whatsappUnidadNombre').textContent = nombre;
                document.getElementById('whatsappUnidadId').value = unidadId;
                const select = document.getElementById('whatsappTelefonoSelect');
                select.innerHTML = '<option value="">Seleccione un número...</option>';
                numerosUnicos.forEach(n => {
                    const opt = document.createElement('option');
                    opt.value = n.value;
                    opt.dataset.type = n.type;
                    opt.textContent = n.label + ': ' + n.value;
                    select.appendChild(opt);
                });
                document.getElementById('whatsappTelefonoGroup').style.display = numerosUnicos.length > 0 ? '' : 'none';
                const sinNumeros = document.getElementById('whatsappSinNumeros');
                sinNumeros.classList.toggle('d-none', numerosUnicos.length > 0);
                sinNumeros.classList.toggle('d-block', numerosUnicos.length === 0);
                const btn = document.getElementById('btnEnviarWhatsApp');
                btn.disabled = numerosUnicos.length === 0;
                btn.style.display = '';
                document.getElementById('whatsappMensaje').value = '';
                new bootstrap.Modal(document.getElementById('whatsappModal')).show();
            })
            .catch(function() {
                document.getElementById('whatsappUnidadNombre').textContent = 'Unidad Productiva #' + unidadId;
                document.getElementById('whatsappUnidadId').value = unidadId;
                document.getElementById('whatsappTelefonoSelect').innerHTML = '<option value="">Seleccione un número...</option>';
                document.getElementById('whatsappTelefonoGroup').style.display = 'none';
                document.getElementById('whatsappSinNumeros').classList.remove('d-none').classList.add('d-block');
                document.getElementById('btnEnviarWhatsApp').disabled = true;
                document.getElementById('whatsappMensaje').value = '';
                new bootstrap.Modal(document.getElementById('whatsappModal')).show();
            });
        };

        // Enviar mensaje de WhatsApp
        document.getElementById('formWhatsApp')?.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const unidadId = document.getElementById('whatsappUnidadId').value;
            const opt = document.getElementById('whatsappTelefonoSelect').selectedOptions[0];
            const telefono = opt ? opt.value : '';
            const phoneType = opt ? (opt.dataset.type || '') : '';
            const mensaje = document.getElementById('whatsappMensaje').value;
            const btnEnviar = document.getElementById('btnEnviarWhatsApp');
            const textoOriginal = btnEnviar.innerHTML;

            if (!telefono) {
                (window.Swal && window.Swal.fire) ? window.Swal.fire({ title: 'Seleccione un número', text: 'Esta unidad productiva no tiene números registrados o debe elegir uno', icon: 'warning' }) : alert('Seleccione un número');
                return false;
            }
            if (!mensaje) {
                (window.Swal && window.Swal.fire) ? window.Swal.fire({ title: 'Mensaje requerido', text: 'Por favor, ingrese el mensaje a enviar', icon: 'warning' }) : alert('Ingrese el mensaje');
                return false;
            }

            btnEnviar.disabled = true;
            btnEnviar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('telefono', telefono);
            formData.append('phone_type', phoneType);
            formData.append('mensaje', mensaje);

            fetch('/unidadesProductivas/' + unidadId + '/enviar-whatsapp', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(function(r) {
                return r.json().then(function(data) { return { ok: r.ok, data: data }; });
            })
            .then(function(result) {
                const response = result.data;
                if (result.ok && response.success) {
                    (window.Swal && window.Swal.fire) ? window.Swal.fire({ title: '¡Éxito!', text: response.message || 'Mensaje enviado correctamente', icon: 'success' }) : alert('Mensaje enviado');
                    bootstrap.Modal.getInstance(document.getElementById('whatsappModal')).hide();
                    document.getElementById('formWhatsApp').reset();
                    if (window.history && window.history.replaceState) window.history.replaceState({}, document.title, window.location.pathname);
                } else {
                    const msg = (response && response.message) ? response.message : 'No se pudo enviar el mensaje';
                    (window.Swal && window.Swal.fire) ? window.Swal.fire({ title: 'Error', text: msg, icon: 'error' }) : alert(msg);
                }
                btnEnviar.disabled = false;
                btnEnviar.innerHTML = textoOriginal;
            })
            .catch(function() {
                (window.Swal && window.Swal.fire) ? window.Swal.fire({ title: 'Error', text: 'Error al enviar el mensaje', icon: 'error' }) : alert('Error al enviar el mensaje');
                btnEnviar.disabled = false;
                btnEnviar.innerHTML = textoOriginal;
                if (window.history && window.history.replaceState && window.location.search) window.history.replaceState({}, document.title, window.location.pathname);
            });

            return false;
        });

        document.getElementById('whatsappModal')?.addEventListener('hidden.bs.modal', function() {
            if (window.history && window.history.replaceState && window.location.search) window.history.replaceState({}, document.title, window.location.pathname);
        });

        (function() {
            var params = new URLSearchParams(window.location.search);
            if (params.has('unidad_id') || params.has('telefono') || params.has('mensaje')) {
                if (window.history && window.history.replaceState) window.history.replaceState({}, document.title, window.location.pathname);
            }
        })();
    </script>
@endsection

@section('modals')
<div class="modal fade" id="whatsappModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="icon-base ri ri-whatsapp-line me-2" style="color: #25D366;"></i>
                    Enviar mensaje vía WhatsApp
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form id="formWhatsApp" method="post" action="javascript:void(0);" onsubmit="return false;">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Unidad Productiva</label>
                        <p class="form-control-plaintext fw-bold" id="whatsappUnidadNombre">-</p>
                        <input type="hidden" id="whatsappUnidadId" name="unidad_id">
                    </div>
                    
                    <div class="mb-3" id="whatsappTelefonoGroup">
                        <label class="form-label" for="whatsappTelefonoSelect">Número de teléfono</label>
                        <select class="form-select" id="whatsappTelefonoSelect" name="telefono">
                            <option value="">Seleccione un número...</option>
                        </select>
                        <small class="form-text text-muted">Seleccione uno de los números asociados a la unidad productiva</small>
                    </div>
                    <div class="mb-3 alert alert-warning py-2 d-none" id="whatsappSinNumeros">
                        <small>Esta unidad productiva no tiene números de teléfono registrados (móvil, teléfono o contacto).</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="whatsappMensaje">Mensaje <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="whatsappMensaje" name="mensaje" rows="5" 
                                  placeholder="Escriba el mensaje a enviar..." required></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btnEnviarWhatsApp">
                        <i class="icon-base ri ri-send-plane-line me-2"></i>
                        Enviar mensaje
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection