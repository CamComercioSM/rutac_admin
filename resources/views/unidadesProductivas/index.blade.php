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

        // Función para abrir el modal de WhatsApp
        window.abrirModalWhatsApp = function(unidadId) {
            // Obtener los datos de la unidad productiva
            $.ajax({
                url: '/unidadesProductivas/' + unidadId,
                type: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    const nombre = response.business_name || 'Unidad Productiva';
                    const telefono = response.mobile || response.telephone || response.contact_phone || '';
                    
                    // Llenar el modal
                    $('#whatsappUnidadNombre').text(nombre);
                    $('#whatsappUnidadId').val(unidadId);
                    
                    if (telefono) {
                        $('#whatsappTelefono').val(telefono).removeClass('text-muted');
                    } else {
                        $('#whatsappTelefono').val('No hay número registrado').addClass('text-muted');
                    }
                    
                    $('#whatsappMensaje').val('');
                    
                    // Mostrar el modal
                    const modal = new bootstrap.Modal(document.getElementById('whatsappModal'));
                    modal.show();
                },
                error: function() {
                    // Si falla, abrir el modal de todas formas
                    $('#whatsappUnidadNombre').text('Unidad Productiva #' + unidadId);
                    $('#whatsappUnidadId').val(unidadId);
                    $('#whatsappTelefono').val('No hay número registrado').addClass('text-muted');
                    $('#whatsappMensaje').val('');
                    
                    const modal = new bootstrap.Modal(document.getElementById('whatsappModal'));
                    modal.show();
                }
            });
        };

        // Enviar mensaje de WhatsApp
        $('#formWhatsApp').on('submit', function(e) {
            e.preventDefault();
            
            const unidadId = $('#whatsappUnidadId').val();
            const telefono = $('#whatsappTelefono').val();
            const mensaje = $('#whatsappMensaje').val();
            
            if (!telefono) {
                Swal.fire({
                    title: 'Número no disponible',
                    text: 'Esta unidad productiva no tiene un número de teléfono registrado',
                    icon: 'warning'
                });
                return;
            }
            
            if (!mensaje) {
                Swal.fire({
                    title: 'Mensaje requerido',
                    text: 'Por favor, ingrese el mensaje a enviar',
                    icon: 'warning'
                });
                return;
            }
            
            const btnEnviar = $('#btnEnviarWhatsApp');
            const textoOriginal = btnEnviar.html();
            btnEnviar.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Enviando...');
            
            $.ajax({
                url: '/unidadesProductivas/' + unidadId + '/enviar-whatsapp',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    telefono: telefono,
                    mensaje: mensaje
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: response.message || 'Mensaje enviado correctamente',
                            icon: 'success'
                        });
                        $('#whatsappModal').modal('hide');
                        $('#formWhatsApp')[0].reset();
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message || 'No se pudo enviar el mensaje',
                            icon: 'error'
                        });
                    }
                    btnEnviar.prop('disabled', false).html(textoOriginal);
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.message || 'Error al enviar el mensaje';
                    Swal.fire({
                        title: 'Error',
                        text: error,
                        icon: 'error'
                    });
                    btnEnviar.prop('disabled', false).html(textoOriginal);
                }
            });
        });
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
            
            <form id="formWhatsApp">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Unidad Productiva</label>
                        <p class="form-control-plaintext fw-bold" id="whatsappUnidadNombre">-</p>
                        <input type="hidden" id="whatsappUnidadId" name="unidad_id">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="whatsappTelefono">Número de teléfono</label>
                        <input type="text" class="form-control" id="whatsappTelefono" name="telefono" 
                               placeholder="No hay número registrado" readonly>
                        <small class="form-text text-muted">Número registrado en la unidad productiva</small>
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