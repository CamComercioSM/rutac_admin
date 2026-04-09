@extends('layouts.list', ['titulo' => 'Actividades y Tareas', 'tituloModal' => 'Actividad'])

@section('form-filters')

    <!-- Unidad -->
    <div>
        <label class="form-label mb-1">Unidad</label>
        <select class="form-select form-select-sm" name="unidad" id="unidad">
            <option value="">Todas</option>
            @foreach ($unidades as $item)
                <option value="{{ $item->unidadproductiva_id }}">{{ $item->business_name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Usuario -->
    @if (!$esAsesor)
        <div>
            <label class="form-label mb-1">Usuario</label>
            <select class="form-select form-select-sm" name="asesor" id="asesor">
                <option value="">Todos</option>
                @foreach ($asesores as $item)
                    <option value="{{ $item->id }}">{{ $item->name }} {{ $item->lastname }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <!-- Programa -->
    <div>
        <label class="form-label mb-1">Programa</label>
        <select class="form-select form-select-sm" name="programa" id="filtro_programa">
            <option value="">Todos</option>
            @foreach ($programas as $item)
                <option value="{{ $item->programa_id }}">{{ $item->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Fase -->
    <div>
        <label class="form-label mb-1">Fase</label>
        <select class="form-select form-select-sm" name="fase" id="filtro_fase">
            <option value="">Todas</option>
            @foreach ($fasesProgramas as $item)
                <option value="{{ $item->fase_id }}">{{ $item->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Categoría -->
    <div>
        <label class="form-label mb-1">Categoría</label>
        <select class="form-select form-select-sm" name="categoria" id="filtro_categoria">
            <option value="">Todas</option>
            @foreach ($categorias as $item)
                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Fechas -->
    <div>
        <label class="form-label mb-1" for="fecha_inicio_filtros">Desde</label>
        <input type="date" class="form-control form-control-sm" name="fecha_inicio" id="fecha_inicio_filtros">
    </div>

    <div>
        <label class="form-label mb-1" for="fecha_fin_filtros">Hasta</label>
        <input type="date" class="form-control form-control-sm" name="fecha_fin" id="fecha_fin_filtros">
    </div>

@endsection

@section('form-fields')
    <!-- Modern Vertical Wizard -->
    <div class="row">
        <div class="col-12">
            <small class="fw-medium">Actividad</small>
            <div class="bs-stepper vertical wizard-modern wizard-modern-vertical mt-2">
                <div class="bs-stepper-header gap-lg-2">
                    @include('intervenciones.partials.stepper-header')
                </div>
                <div class="bs-stepper-content">

                    {{-- BOTONES DE NAVEGACIÓN PROPIOS (Siguiente/Anterior) --}}
                    <div class="d-flex justify-content-between mb-4 pb-3 border-bottom">
                        <button type="button" class="btn btn-outline-secondary btn-prev">
                            <i class="ri-arrow-left-line"></i> Anterior
                        </button>
                        <button type="button" class="btn btn-primary btn-next" id="wizard-next-btn">
                            Siguiente <i class="ri-arrow-right-line"></i>
                        </button>
                    </div>

                    <!-- informacion de intervencion -->
                    <div id="account-details-modern-vertical" class="content">
                        @include('intervenciones.partials.step1-datos')
                    </div>
                    <!-- Avances -->
                    <div id="avances-info-modern-vertical" class="content">
                        @include('intervenciones.partials.step2-avances')
                    </div>
                    <!-- Unidades productivas -->
                    <div id="personal-info-modern-vertical" class="content">
                        @include('intervenciones.partials.step3-unidades')
                    </div>
                    <!-- Soporte -->
                    <div id="social-links-modern-vertical" class="content">
                        @include('intervenciones.partials.step4-soportes')
                    </div>

                    {{-- Input oculto para el estado --}}
                    <input type="hidden" name="estado_guardado" id="estado_guardado" value="firme">

                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="soporteActual" id="soporteActual">
    <input type="hidden" name="eliminarSoporte" id="eliminarSoporte" value="0">
@endsection

@section('btns-actions')
    <button id="btnImport" class="btn btn-success me-3">
        <i class="icon-base ri ri-file-excel-2-line me-2"></i> Importar
    </button>
    <button id="btnInforme" class="btn btn-primary me-3">
        <i class="icon-base ri ri-file-pdf-2-line me-2"></i> Informe
    </button>
@endsection

@section('modals')
    @include('intervenciones.partials.modals.import')
    @include('intervenciones.partials.modals.informe')
    @include('intervenciones.partials.modals.texto-completo')
    @include('intervenciones.partials.modals.nuevo-participante')
@endsection


@section('script')
    @vite(['resources/assets/vendor/libs/quill/typography.scss', 'resources/assets/vendor/libs/quill/editor.scss', 'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss'])

    @vite(['resources/assets/vendor/libs/quill/katex.js', 'resources/assets/vendor/libs/quill/quill.js', 'resources/assets/js/form-wizard-validation.js', 'resources/js/reporteMensual/intervenciones.js'])

    <script>
        const CONVOCATORIAS = @json($convocatorias);

        window.TABLA = {
            urlApi: '/intervenciones',
            sortName: 'fecha_creacion',
            sortOrder: 'desc',
            order: [
                [0, 'desc']
            ],
            accion_editar: false,
            mensajeEliminar: '¿Estás seguro de que deseas eliminar esta intervención? Esta acción no se puede deshacer.',
            menu_row: `
                <a class="dropdown-item  btn-editar" href="javascript:void(0);" >
                    <i class="ri-pencil-line me-1"></i> Editar
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="window.eliminarRegistro()">
                    <i class="ri-delete-bin-line me-1"></i> Eliminar
                </a>
            `,

            columns: [{
                    data: 'fecha_creacion',
                    title: 'Reportado',
                    orderable: true
                },

                {
                    data: 'estado',
                    title: 'Estado',
                    orderable: true,
                    render: function(data) {
                        let color = 'secondary';
                        let text = data || 'N/A';

                        if (data === 'BORRADOR') {
                            color = 'warning';
                            text = 'Borrador';
                        } else if (data === 'REPORTADO') {
                            color = 'info';
                            text = 'Reportado';
                        } else if (data === 'VALIDADO') {
                            color = 'success';
                            text = 'Validado';
                        }

                        return `<span class="badge bg-${color}">${text}</span>`;
                    }
                },

                {
                    data: 'fecha_inicio',
                    title: 'inicio',
                    orderable: true
                },
                {
                    data: 'fecha_fin',
                    title: 'fin',
                    orderable: true
                },
                {
                    data: 'programa',
                    title: 'Programa',
                    orderable: true
                },
                {
                    data: 'convocatoria',
                    title: 'Ciclo',
                    orderable: true
                },
                {
                    data: 'fase',
                    title: 'Etapa',
                    orderable: true
                },
                {
                    data: 'categoria',
                    title: 'Actividad',
                    orderable: true
                },
                {
                    data: 'tipo',
                    title: 'Tarea',
                    orderable: true
                },
                {
                    data: 'unidad',
                    title: 'Intervenidos',
                    orderable: true
                },
                {
                    data: 'participantes_total',
                    title: 'Participantes',
                    orderable: true
                },
                {
                    data: 'asesor',
                    title: 'Asesor',
                    orderable: true
                },
                {
                    data: 'soporte',
                    title: 'Soporte',
                    orderable: false,
                    render: function(data, type, row) {
                        return window.renderSoporte(data);
                    }
                },
            ],

            initEditors: [{
                    id: 'descripcion'
                },
                {
                    id: 'conclusiones'
                },
                {
                    id: 'conclusionesI'
                }
            ],

            initSelects: [{
                    id: 'unidadAdd',
                    setting: {
                        ajax: {
                            url: '/unidadProductiva/search',
                            dataType: 'json',
                            delay: 300,
                        },
                        minimumInputLength: 3,
                    }
                },
                {
                    id: 'unidad',
                    setting: {
                        ajax: {
                            url: '/unidadProductiva/search',
                            dataType: 'json',
                            delay: 300,
                        },
                        minimumInputLength: 3,
                    }
                },
                {
                    id: 'otroParticipanteAdd',
                    setting: {
                        ajax: {
                            url: '/lead/search',
                            dataType: 'json',
                            delay: 300,
                        },
                        minimumInputLength: 3,
                    }
                },
                {
                    id: 'asesor'
                },
            ],

            initFiltros: @json($filtros)
        };

        // --- 3. VALIDACIÓN GLOBAL DE PASOS ---
        window.obtenerCamposFaltantes = function() {
            let faltantes = [];
            const fInicio = document.getElementById('fecha_inicio');
            const fFin = document.getElementById('fecha_fin');

            // Paso 1: Datos Básicos
            if (!document.getElementById('programa_id').value) faltantes.push("Programa (Paso 1)");
            if (!document.getElementById('fase_id')?.value && !document.getElementById('fase_programa_id')
                ?.value) faltantes.push("Fase (Paso 1)");
            if (!document.getElementById('categoria_id').value) faltantes.push(
                "Actividad/Categoría (Paso 1)");
            if (!fInicio.value) faltantes.push("Fecha Inicio (Paso 1)");
            if (!fFin.value) faltantes.push("Fecha Fin (Paso 1)");

            // Paso 2: Avances (Validar Quill editor si existe)
            // Asumiendo que el contenido de Quill se sincroniza a un input o div
            const conclusionesHTML = document.querySelector('#conclusiones .ql-editor')?.innerHTML || "";
            if (conclusionesHTML === "<p><br></p>" || conclusionesHTML === "") {
                faltantes.push("Conclusiones/Resultados (Paso 2)");
            }

            // Paso 3: Unidades o Leads (OBLIGATORIO al menos uno)
            // const totalUnidades = document.querySelectorAll('#table_opciones tr').length;
            // const totalLeads = document.querySelectorAll('#table_otros_participantes tr').length;
            // if (totalUnidades === 0 && totalLeads === 0) {
            //     Swal.fire({
            //         icon: 'info',
            //         title: 'Sin participantes',
            //         text: 'No has agregado ninguna unidad productiva ni participante. Puedes continuar, pero se recomienda agregar al menos uno.',
            //         confirmButtonText: 'Entendido'
            //     });
            // }
            return faltantes;
        };


        document.addEventListener('DOMContentLoaded', function() {

            const fInicio = document.getElementById('fecha_inicio');
            const fFin = document.getElementById('fecha_fin');
            // ---VALIDACIÓN DE FECHA FIN > INICIO ---
            const validarFechas = () => {
                if (fInicio.value && fFin.value) {
                    if (new Date(fFin.value) <= new Date(fInicio.value)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Rango de tiempo inválido',
                            text: 'La fecha de fin debe ser posterior a la de inicio.'
                        });
                        fFin.value = ''; // Limpiar campo inválido
                    }
                }
            };
            fInicio.addEventListener('change', validarFechas);
            fFin.addEventListener('change', validarFechas);


            const stepperEl = document.querySelector('.bs-stepper');
            const btnNext = document.getElementById('wizard-next-btn');
            const btnPrev = document.querySelector('.btn-prev');

            // Referencias a los botones GLOBALES del layout
            const btnResetLimpiar = document.querySelector('#Modal button[type="reset"]'); // El verde del layout
            btnResetLimpiar.classList.add('d-none'); // Lo ocultamos al inicio
            const btnCancelarLayout = document.querySelector('#form .btn-cancelar');
            btnCancelarLayout.classList.add('d-none'); // Lo ocultamos al inicio            
            const btnGuardarLayout = document.querySelector('#Modal button[type="submit"]'); // El verde del layout
            btnGuardarLayout.classList.add('d-none'); // Lo ocultamos al inicio

            // 1. Inicializar el botón de "Borrador" dinámicamente al lado del de Guardar
            const btnBorrador = document.createElement('button');
            btnBorrador.type = 'button';
            btnBorrador.className = 'btn btn-info me-2 d-none'; // Oculto al inicio
            btnBorrador.innerHTML = '<i class="ri-save-line me-2"></i> Guardar Borrador';
            btnBorrador.onclick = () => submitForm('BORRADOR');

            // 2. Ajustar el botón de Guardar Firme original
            const btnGuardarFirme = document.createElement('button');
            btnGuardarFirme.type = 'button';
            btnGuardarFirme.className = 'btn btn-success me-2  d-none'; // Oculto al inicio
            btnGuardarFirme.innerHTML = '<i class="ri-check-double-line me-2"></i> Guardar en Firme';
            btnGuardarFirme.onclick = () => submitForm('FIRME');

            const footerModal = document.querySelector('#Modal #form-botones');; // El div text-center my-4
            footerModal.appendChild(btnGuardarFirme);
            footerModal.insertBefore(btnBorrador, btnGuardarFirme);

            const stepper = new Stepper(stepperEl, {
                linear: false
            });

            // Escuchar el cambio de paso
            stepperEl.addEventListener('show.bs-stepper', function(event) {
                const index = event.detail.indexStep; // 0, 1, 2, 3
                const totalSteps = 3; // El índice del último paso (paso 4)

                if (index === totalSteps) {
                    const faltantes = obtenerCamposFaltantes();
                    if (faltantes.length > 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atención: Datos incompletos',
                            html: `<p>Aún faltan datos importantes en pasos anteriores:</p>
                                    <ul class="text-start">
                                        ${faltantes.map(f => `<li>${f}</li>`).join('')}
                                    </ul>
                                   <p>Recuerda completarlos antes de Guardar en Firme.</p>`,
                            confirmButtonText: 'Entendido'
                        });
                    }
                    // ESTAMOS AL FINAL: Mostrar botones de guardado, ocultar "Siguiente"
                    btnCancelarLayout.classList.remove('d-none');
                    btnGuardarFirme.classList.remove('d-none');
                    btnBorrador.classList.remove('d-none');
                    // BLOQUEAR botón Siguiente
                    btnNext.disabled = true;
                } else {
                    // NO ES EL FINAL: Ocultar botones de guardado, mostrar "Siguiente"
                    btnCancelarLayout.classList.add('d-none');
                    btnGuardarFirme.classList.add('d-none');
                    btnBorrador.classList.add('d-none');
                    // ACTIVAR botón Siguiente
                    btnNext.disabled = false;
                }

                // Control de botón "Anterior"
                btnPrev.disabled = (index === 0);
            });

            // Eventos de los botones de navegación
            btnNext.addEventListener('click', () => stepper.next());
            btnPrev.addEventListener('click', () => stepper.previous());
        });


        function submitForm(tipo) {
            document.getElementById('estado_guardado').value = tipo;
            const form = document.getElementById('form');

            if (tipo === 'FIRME') {
                const faltantes = obtenerCamposFaltantes();

                if (faltantes.length > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'No se puede guardar',
                        html: `<p>Para guardar en FIRME, debes completar:</p>
                       <ul class="text-start">
                         ${faltantes.map(f => `<li>${f}</li>`).join('')}
                       </ul>`,
                        confirmButtonText: 'Ir a completar'
                    });
                    return; // Bloquear envío
                }

                Swal.fire({
                    title: '¿Guardar en FIRME?',
                    text: "No podrás editar la intervención después de esta acción.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, finalizar',
                    cancelButtonText: 'Revisar'
                }).then((result) => {
                    if (result.isConfirmed)
                        form.requestSubmit();
                });
            } else {
                form.requestSubmit();
            }
        }

        function cargarEditar(id) {

            Helpers.bloquearPantalla();

            $.get(`/intervenciones/${id}`, function(res) {

                // 🔹 limpiar
                window.tagifyUserList?.removeAllTags();
                window.tagifyOtrosParticipantes?.removeAllTags();

                $("#table_opciones").empty(); // 🔥 limpiar tabla
                $("#table_otros_participantes").empty(); // 🔥 limpiar tabla

                // =========================
                // 🔹 UNIDADES (USANDO TU FLUJO REAL)
                // =========================
                if (res.unidades) {
                    res.unidades.forEach(item => {

                        const row = {
                            id: item.unidadproductiva_id,
                            text: item.unidad_productiva?.nombre ?? item.unidad_productiva
                                ?.business_name,
                            participantes: item.participantes
                        };

                        // 🔥 MISMAS funciones que usas al agregar manual
                        window.itemOption(row);
                        window.addUnidadToTagify(row);
                    });
                }

                // =========================
                // 🔹 LEADS (USANDO TU FLUJO REAL)
                // =========================
                if (res.leads) {
                    res.leads.forEach(item => {

                        const row = {
                            id: item.lead_id,
                            text: item.lead?.name,
                            participantes: item.participantes
                        };

                        // 🔥 MISMAS funciones que usas al agregar manual
                        window.itemOtroParticipante(row);
                        window.addOtroParticipanteToTagify(row);
                    });
                }

                window.cargarSoporteEnFormulario(res.soporte);
                openEditar();
                Helpers.desbloquearPantalla();
            });

        }

        function guardarLead() {

            const data = {
                type: document.getElementById('lead_type').value,
                document: document.getElementById('lead_document').value,
                name: document.getElementById('lead_name').value,
                phone: document.getElementById('lead_phone').value,
                email: document.getElementById('lead_email').value,
                description: document.getElementById('lead_description').value,
            };

            if (!data.name || !data.phone) {
                alert('Nombre y teléfono son obligatorios');
                return;
            }

            fetch("{{ route('leads.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(response => {

                    if (response.success) {

                        const select = document.getElementById('otroParticipanteAdd');

                        // Crear opción nueva
                        const option = new Option(response.data.text, response.data.id, true, true);
                        select.appendChild(option);

                        // Seleccionarlo
                        select.value = response.data.id;

                        // Cerrar modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoParticipante'));
                        modal.hide();

                        // Limpiar campos
                        document.getElementById('lead_name').value = '';
                        document.getElementById('lead_document').value = '';
                        document.getElementById('lead_phone').value = '';
                        document.getElementById('lead_email').value = '';
                        document.getElementById('lead_description').value = '';

                    } else {
                        alert('Error al guardar');
                    }

                })
                .catch(() => {
                    alert('Error en la petición');
                });
        }

        function obtenerPeriodoActual() {
            const hoy = new Date();

            const inicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
            const fin = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);

            const format = (fecha) => fecha.toISOString().split('T')[0];

            return {
                inicio: format(inicio),
                fin: format(fin)
            };
        }

        // Helper: sincroniza filtros → modal
        function sincronizarFechasModal() {
            const fInicioFiltro = document.getElementById('fecha_inicio_filtros')?.value;
            const fFinFiltro = document.getElementById('fecha_fin_filtros')?.value;

            const {
                inicio,
                fin
            } = obtenerPeriodoActual();

            // Si no hay filtros, usar periodo actual
            document.getElementById('fecha_inicio_informe').value = fInicioFiltro || inicio;
            document.getElementById('fecha_fin_informe').value = fFinFiltro || fin;
        }


        document.addEventListener('DOMContentLoaded', function() {
            function cargarConvocatorias(programaId) {
                let $convocatoria = $('#convocatoria_id');

                $convocatoria.empty();

                if (!programaId) {
                    $convocatoria
                        .append('<option value="" selected>Seleccione primero un programa</option>')
                        .prop('disabled', true);
                    return;
                }

                let filtradas = CONVOCATORIAS.filter(function(item) {
                    return String(item.programa_id) === String(programaId);
                });

                if (filtradas.length === 0) {
                    $convocatoria
                        .append('<option value="" selected>No hay convocatorias para este programa</option>')
                        .prop('disabled', true);
                    return;
                }

                $convocatoria.append('<option value="" selected>Seleccione una opción</option>');

                $.each(filtradas, function(index, item) {
                    $convocatoria.append(
                        `<option value="${item.convocatoria_id}">${item.nombre_convocatoria}</option>`
                    );
                });

                $convocatoria.prop('disabled', false);
            }

            $('#programa_id').on('change', function() {
                let programaId = $(this).val();
                cargarConvocatorias(programaId);
            });

            if ($('#programa_id').val()) {
                cargarConvocatorias($('#programa_id').val());
            }

            $('#btnImport').on('click', function() {
                
                return Helpers.notificarEnConstruccion();        
                
                let modal = new bootstrap.Modal(document.getElementById('importModal'));
                modal.show();
            });


            ['fecha_inicio_filtros', 'fecha_fin_filtros'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', sincronizarFechasModal);
                }
            });

            $('#btnInforme').on('click', function() {          
                sincronizarFechasModal();

                // Otros filtros
                document.getElementById('preview_asesor').value =
                    document.getElementById('asesor')?.value || '';

                document.getElementById('preview_unidad').value =
                    document.getElementById('unidad')?.value || '';

                // Mostrar modal
                const modal = new bootstrap.Modal(document.getElementById('informeModal'));
                modal.show();
            });
            // Al enviar el formulario de previsualización, copiar filtros al formulario y abrir en ruta real del servidor
            $('#formPreviewInforme').on('submit', function() {
                const fInicio = document.getElementById('fecha_inicio_informe').value;
                const fFin = document.getElementById('fecha_fin_informe').value;

                if (!fInicio || !fFin) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Fechas requeridas',
                        text: 'Debe seleccionar el rango de fechas para generar el informe.'
                    });
                    return;
                }

                if (new Date(fFin) < new Date(fInicio)) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Rango inválido',
                        text: 'La fecha fin debe ser mayor o igual a la fecha inicio.'
                    });
                    return;
                }

                const editor = window.TABLA.initEditors.find(item => item.id === 'conclusionesI');
                if (editor && editor.quill) {
                    let html = editor.quill.root.innerHTML;

                    if (html === '<p><br></p>') {
                        html = '';
                    }

                    $('#conclusionesI_input').val(html);
                    console.log('conclusiones a enviar:', html);
                }

                $('#informeModal').modal('hide');
            });

            $('#formImport').on('submit', function(e) {
                e.preventDefault();

                $(".cargando").removeClass("d-none");
                let formData = new FormData(this);

                $("#btnUpload").prop("disabled", true).text("Importando...");

                $.ajax({
                    url: "/intervenciones/import",
                    type: "POST",
                    data: formData,
                    processData: false, // Necesario para FormData
                    contentType: false, // Necesario para FormData
                    success: function(response) {

                        if (response.ok) {
                            // Éxito
                            alert("Importación completada: " + response.importados +
                                " registros");
                            $("#importModal").modal("hide");
                        } else {
                            // Errores de validación del import
                            mostrarErrores(response.errores);
                        }

                        $("#btnUpload").prop("disabled", false).text("Importar");
                        $(".cargando").addClass("d-none");
                    },
                    error: function(xhr) {
                        mostrarErrores(["Error interno, verifique el archivo"]);
                        $("#btnUpload").prop("disabled", false).text("Importar");
                        $(".cargando").addClass("d-none");
                    }
                });
            });

            function mostrarErrores(errores) {
                let div = $("#importErrors");
                div.removeClass("d-none").empty();

                errores.forEach(err => {
                    div.append("<div>• " + err + "</div>");
                });
            }



            // Manejar clics en "ver más" para abrir modal
            $(document).on('click', '.ver-mas-link', function(e) {
                e.preventDefault();
                const id = $(this).data('id');

                // Obtener el texto completo y el título del objeto global
                const datos = window.textosCompletos && window.textosCompletos[id] ? window.textosCompletos[
                    id] : null;

                if (datos) {
                    // Establecer el título del modal
                    $('#textoCompletoModalTitle').text(datos.titulo || 'Texto completo');

                    // Establecer el contenido del modal
                    $('#textoCompletoModalContent').html(datos.texto || '');

                    // Mostrar el modal
                    const modal = new bootstrap.Modal(document.getElementById('textoCompletoModal'));
                    modal.show();
                }
            });

            $(document).on('change', '#switchBorrador', function() {

                if ($(this).is(':checked')) {
                    $('#switchLabel').text('Crear');
                } else {
                    $('#switchLabel').text('Borrador');
                }

            });

            $(document).on('click', '.btn-editar', function() {

                const row = window.itemSelect;
                if (!row) return;
                if (row.estado !== 'BORRADOR') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No permitido',
                        text: 'Solo se pueden editar intervenciones en estado BORRADOR'
                    });
                    return;
                }


                cargarEditar(row.id);

            });



            const {
                inicio,
                fin
            } = obtenerPeriodoActual();

            const fInicioFiltro = document.getElementById('fecha_inicio_filtros');
            const fFinFiltro = document.getElementById('fecha_fin_filtros');

            if (fInicioFiltro && !fInicioFiltro.value) {
                fInicioFiltro.value = inicio;
            }

            if (fFinFiltro && !fFinFiltro.value) {
                fFinFiltro.value = fin;
            }

        });
    </script>
@endsection
