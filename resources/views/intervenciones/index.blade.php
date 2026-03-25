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
@endsection

@section('btns-actions')
    <button id="btnImport" class="btn btn-success me-3">
        <i class="icon-base ri ri-file-excel-2-line me-2"></i> Importar
    </button>
    <button id="btnInforme" class="btn btn-info me-3">
        <i class="icon-base ri ri-file-pdf-2-line me-2"></i> Informe
    </button>
@endsection

@section('modals')
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Importar Intervenciones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="formImport" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">
                        <p class="mb-2">Seleccione archivo Excel (.xlsx)</p>
                        <input type="file" name="archivo" class="form-control" accept=".xlsx" required>

                        <div class="alert alert-danger d-none mt-3" id="importErrors"></div>

                        <div class="mt-3">
                            <a href="/plantilla_intervenciones.xlsx" class="btn btn-outline-primary btn-sm"> Descargar
                                plantilla
                            </a>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" id="btnUpload">Importar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="informeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formPreviewInforme" action="{{ url('/intervenciones/informe/preview') }}" method="POST"
                    target="_blank">
                    @csrf
                    <input type="hidden" name="fecha_inicio" id="preview_fecha_inicio" value="">
                    <input type="hidden" name="fecha_fin" id="preview_fecha_fin" value="">
                    <input type="hidden" name="asesor" id="preview_asesor" value="">
                    <input type="hidden" name="unidad" id="preview_unidad" value="">

                    <div class="modal-header">
                        <h5 class="modal-title">Informe Intervenciones</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="col-12 mb-3">
                            <label class="form-label" for="conclusionesI">Conclusiones</label>
                            {{-- <textarea class="form-control" name="conclusiones" id="conclusionesI" rows="6" placeholder="Ingrese las conclusiones"></textarea> --}}

                            <div id="conclusionesI"></div>
                            <input type="hidden" name="conclusiones" id="conclusionesI_input">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" id="btnExportInforme">Generar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="textoCompletoModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="textoCompletoModalTitle">Texto completo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="textoCompletoModalContent" style="max-height: 70vh; overflow-y: auto;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="modalNuevoParticipante" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Nuevo participante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <!-- Tipo documento -->
                        <div class="col-md-4">
                            <label class="form-label">Tipo documento *</label>
                            <select class="form-select" id="lead_type">
                                <option value="1">Cédula</option>
                                <option value="2">NIT</option>
                                <option value="3">Cédula extranjería</option>
                                <option value="4">Tarjeta de Identidad</option>
                                <option value="5">Pasaporte</option>
                            </select>
                        </div>

                        <!-- Documento -->
                        <div class="col-md-4">
                            <label class="form-label">Documento *</label>
                            <input type="text" class="form-control" id="lead_document">
                        </div>

                        <!-- Teléfono -->
                        <div class="col-md-4">
                            <label class="form-label">Teléfono *</label>
                            <input type="text" class="form-control" id="lead_phone">
                        </div>

                        <!-- Nombre -->
                        <div class="col-md-12">
                            <label class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="lead_name">
                        </div>

                        <!-- Correo -->
                        <div class="col-md-6">
                            <label class="form-label">Correo</label>
                            <input type="email" class="form-control" id="lead_email">
                        </div>

                        <!-- Observaciones -->
                        <div class="col-md-6">
                            <label class="form-label">Observaciones</label>
                            <input type="text" class="form-control" id="lead_description">
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button class="btn btn-primary" onclick="guardarLead()">
                        Guardar
                    </button>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    @vite(['resources/assets/vendor/libs/quill/typography.scss', 'resources/assets/vendor/libs/quill/editor.scss', 'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss'])

    @vite(['resources/assets/vendor/libs/quill/katex.js', 'resources/assets/vendor/libs/quill/quill.js', 'resources/assets/js/form-wizard-validation.js', 'resources/js/reporteMensual/intervenciones.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stepperEl = document.querySelector('.bs-stepper');
            const btnNext = document.getElementById('wizard-next-btn');
            const btnPrev = document.querySelector('.btn-prev');

            // Referencias a los botones GLOBALES del layout
            const btnGuardarFirme = document.querySelector('#Modal button[type="submit"]'); // El verde del layout
            const btnResetLimpiar = document.querySelector('#Modal button[type="reset"]'); // El verde del layout
            const btnCancelarLayout = document.querySelector('#form .btn-cancelar');
            const footerModal = btnGuardarFirme.parentNode; // El div text-center my-4

            // 1. Inicializar el botón de "Borrador" dinámicamente al lado del de Guardar
            const btnBorrador = document.createElement('button');
            btnBorrador.type = 'button';
            btnBorrador.className = 'btn btn-info me-2 d-none'; // Oculto al inicio
            btnBorrador.innerHTML = '<i class="ri-save-line me-2"></i> Guardar Borrador';
            btnBorrador.onclick = () => submitForm('borrador');
            footerModal.insertBefore(btnBorrador, btnGuardarFirme);

            // 2. Ajustar el botón de Guardar Firme original
            btnGuardarFirme.classList.add('d-none'); // Lo ocultamos al inicio
            btnGuardarFirme.innerHTML = '<i class="ri-check-double-line me-2"></i> Guardar en Firme';
            btnResetLimpiar.classList.add('d-none'); // Lo ocultamos al inicio
            btnCancelarLayout.classList.add('d-none'); // Lo ocultamos al inicio

            const stepper = new Stepper(stepperEl, {
                linear: false
            });

            // Escuchar el cambio de paso
            stepperEl.addEventListener('show.bs-stepper', function(event) {
                const index = event.detail.indexStep; // 0, 1, 2, 3
                const totalSteps = 3; // El índice del último paso (paso 4)

                if (index === totalSteps) {
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

            if (tipo === 'firme') {
                Swal.fire({
                    title: '¿Guardar en Firme?',
                    text: "No podrás editar la intervención después.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, finalizar'
                }).then((result) => {
                    if (result.isConfirmed) form.requestSubmit();
                });
            } else {
                form.requestSubmit();
            }
        }
    </script>

    <script>
        const CONVOCATORIAS = @json($convocatorias);

        // Función para limitar texto y agregar botón "ver más"
        window.limitarTexto = function(texto, maxLength = 150, titulo = '') {
            if (!texto) return '';

            // Remover etiquetas HTML para contar caracteres
            const textoLimpio = texto.replace(/<[^>]*>/g, '');

            if (textoLimpio.length <= maxLength) {
                return texto;
            }

            // Crear un elemento temporal para trabajar con el HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = texto;

            // Obtener solo el texto sin HTML
            const textoPlano = tempDiv.textContent || tempDiv.innerText || '';

            // Truncar el texto plano
            const textoTruncado = textoPlano.substring(0, maxLength);

            const idUnico = 'texto_' + Math.random().toString(36).substr(2, 9);

            // Almacenar el texto completo y el título en un objeto global
            if (!window.textosCompletos) {
                window.textosCompletos = {};
            }
            window.textosCompletos[idUnico] = {
                texto: texto,
                titulo: titulo
            };

            return `${textoTruncado}... <a href="#" class="text-primary ver-mas-link" data-id="${idUnico}" style="cursor: pointer; text-decoration: underline;">ver más</a>`;
        };

        // Función para renderizar soporte como hipervínculo
        window.renderSoporte = function(url) {
            if (!url) return '';
            // Escapar la URL para evitar problemas de seguridad
            const urlEscapada = url.replace(/"/g, '&quot;').replace(/'/g, '&#x27;');
            return `<a href="${urlEscapada}" target="_blank" class="text-primary" style="text-decoration: underline;">Ver adjunto</a>`;
        };

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
                <a class="dropdown-item" href="javascript:void(0);" onclick="Helpers.notificarEnConstruccion('La edición de registros');">
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
                    data: 'participantes',
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

        window.openAdd = function() {
            const id = $("#unidadAdd").val();
            const text = $("#unidadAdd option:selected").text();
            const participantes = $("#participantes").val();

            if (!(id && text && participantes)) return;

            let existe = $("#table_opciones tr[data-id='" + id + "']").length > 0;
            if (existe) {
                Swal.fire({
                    title: "Elemento ya existe",
                    icon: "info"
                });
                return;
            }

            window.itemOption({
                id: id,
                text: text,
                participantes: participantes
            });
            window.addUnidadToTagify({
                id: id,
                text: text,
                participantes: participantes
            });

            $("#unidadAdd").val(null).trigger('change');
        }
        window.openAddOtroParticipante = function() {
            const id = $("#otroParticipanteAdd").val();
            const text = $("#otroParticipanteAdd option:selected").text();
            const participantes = $("#participantes_otros").val();
            if (!(id && text && participantes)) return;

            let existe = $("#table_otros_participantes tr[data-id='" + id + "']").length > 0;
            if (existe) {
                Swal.fire({
                    title: "Elemento ya existe",
                    icon: "info"
                });
                return;
            }

            window.itemOtroParticipante({
                id: id,
                text: text,
                participantes: participantes
            });

            window.addOtroParticipanteToTagify({
                id: id,
                text: text,
                participantes: participantes
            });

            $("#otroParticipanteAdd").val(null).trigger('change');
        }

        window.itemOption = function(row = {}) {
            const index = $("#table_opciones tr").length;

            const item = `
                <tr data-id="${row.id}" >
                    <td> ${row.text} </td>        
                    <td> ${row.participantes} </td>                    
                    <td style="width: 80px;" >
                        <input type="hidden" name="unidades[${index}][unidadproductiva_id]" value="${row.id}" />
                        <input type="hidden" name="unidades[${index}][participantes]" value="${row.participantes}" />
                        
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeOption(this)" >
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>`;

            $("#table_opciones").append(item);
        }

        window.addUnidadToTagify = function(row = {}) {
            if (!window.tagifyUserList) return;

            const yaExiste = window.tagifyUserList.value.some(tag => String(tag.value) === String(row.id));
            if (yaExiste) return;

            const tagData = {
                value: row.id,
                name: row.text,
                participantes: `${row.participantes}`,
                avatar: 'https://placehold.co/40x40?text=' + row.text
            };

            // agregar al whitelist para que también quede disponible en búsquedas
            window.tagifyUserList.settings.whitelist.push(tagData);

            // agregar como tag visible
            window.tagifyUserList.addTags([tagData]);
        }

        window.itemOtroParticipante = function(row = {}) {
            const index = $("#table_otros_participantes tr").length;
            const item = `
            <tr data-id="${row.id}">
            <td>${row.text}</td>
            <td>${row.participantes}</td>
            <td style="width: 80px;">
                <input type="hidden" name="otros_participantes[${index}][lead_id]" value="${row.id}" />
                <input type="hidden" name="otros_participantes[${index}][participantes]" value="${row.participantes}" />

                <button type="button" class="btn btn-danger btn-sm" onclick="removeOtroParticipante(this)">
                    <i class="icon-base ri ri-delete-bin-line"></i>
                </button>
            </td>
            </tr>`;

            $("#table_otros_participantes").append(item);
        }

        window.addOtroParticipanteToTagify = function(row = {}) {
            if (!window.tagifyOtrosParticipantes) return;
            const yaExiste = window.tagifyOtrosParticipantes.value.some(tag => String(tag.value) === String(row.id));
            if (yaExiste) return;

            const tagData = {
                value: row.id,
                name: row.text,
                participantes: `${row.participantes}`,
                avatar: 'https://placehold.co/40x40?text=' + row.text
            };

            // agregar al whitelist para que también quede disponible en búsquedas
            window.tagifyOtrosParticipantes.settings.whitelist.push(tagData);

            // agregar como tag visible
            window.tagifyOtrosParticipantes.addTags([tagData]);
        };

        window.removeOption = function(btn) {
            $(btn).closest("tr").remove();
        }

        window.removeOtroParticipante = function(btn) {
            $(btn).closest("tr").remove();
        }

        window.validarExtraForm = function() {
            // if ($("#table_opciones tr").length == 0) {
            //     Swal.fire({
            //         title: "Agregar por lo menos una unidad productiva",
            //         icon: "info"
            //     });
            //     return false;
            // }
            return true;
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

            // $("#categoria_id").on("change", function() {
            //     let categoria_id = $(this).val();
            //     $("#cont_referencia").addClass('d-none');

            //     if (categoria_id == 1) {
            //         $("#cont_referencia label").text('Convocatoria (Seleccione una opción)');

            //         $("#cont_referencia select").select2({
            //             ajax: {
            //                 url: '/convocatorias/search',
            //                 delay: 300
            //             },
            //             minimumInputLength: 3,
            //         });

            //         $("#cont_referencia").removeClass('d-none');
            //     }

            // });

            $('#btnImport').on('click', function() {
                let modal = new bootstrap.Modal(document.getElementById('importModal'));
                modal.show();
            });

            $('#btnInforme').on('click', function() {

                if (!($("#fecha_inicio_filtros").val() && $("#fecha_fin_filtros").val())) {
                    Swal.fire({
                        title: "Seleccione un rango de fechas para el informe",
                        icon: "info"
                    });
                    return;
                }

                let modal = new bootstrap.Modal(document.getElementById('informeModal'));
                modal.show();
            });
            // Al enviar el formulario de previsualización, copiar filtros al formulario y abrir en ruta real del servidor
            $('#formPreviewInforme').on('submit', function() {

                const editor = window.TABLA.initEditors.find(item => item.id === 'conclusionesI');

                if (editor && editor.quill) {
                    let html = editor.quill.root.innerHTML;

                    if (html === '<p><br></p>') {
                        html = '';
                    }

                    $('#conclusionesI_input').val(html);
                    console.log('conclusiones a enviar:', html);
                }

                var $filters = $('#filters');
                $('#preview_fecha_inicio').val($filters.find('input[id="fecha_inicio_filtros"]').val() ||
                    '');
                $('#preview_fecha_fin').val($filters.find('input[id="fecha_fin_filtros"]').val() || '');
                $('#preview_asesor').val($filters.find('select[name="asesor"]').val() || '');
                $('#preview_unidad').val($filters.find('select[name="unidad"]').val() || '');
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

        });
    </script>

    <script>
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
    </script>
@endsection
