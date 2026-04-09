<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Previsualización - Informe de Intervenciones</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('intervenciones.partials.informe.informe-styles')
</head>

<body>
    <div class="preview-container">
        <div class="preview-actions">
            <button type="button" class="btn btn-secondary" onclick="window.close()">Cerrar</button>
            <form id="formGenerarPDF" method="POST" action="{{ url('/intervenciones/informe/generar') }}"
                style="display: inline;" target="_blank">
                @csrf
                <input type="hidden" name="fecha_inicio"
                    value="{{ request('fecha_inicio') ?? request()->input('fecha_inicio') }}">
                <input type="hidden" name="fecha_fin"
                    value="{{ request('fecha_fin') ?? request()->input('fecha_fin') }}">
                @if (request('asesor') || request()->input('asesor'))
                    <input type="hidden" name="asesor" value="{{ request('asesor') ?? request()->input('asesor') }}">
                @endif
                @if (request('unidad') || request()->input('unidad'))
                    <input type="hidden" name="unidad" value="{{ request('unidad') ?? request()->input('unidad') }}">
                @endif
                <input type="hidden" name="conclusiones" value="{{ $conclusiones }}">
                <button type="submit" class="btn btn-primary">Generar PDF</button>
            </form>
            <button type="button" id="btnGuardarInforme" class="btn btn-success">Guardar Reporte</button>
        </div>

        {{-- contenido reutilizable --}}
        @include('intervenciones.partials.informe.informe-contenido', [
            'mostrarIA' => true,
        ])

    </div>
    <div class="modal fade" id="guardarInformeModal" tabindex="-1">

        <div class="modal-dialog">
            <div class="modal-content">

                <form id="formSaveInforme" action="{{ url('/intervenciones/informe') }}" method="POST">
                    @csrf

                    <input type="hidden" name="fecha_inicio"
                        value="{{ request('fecha_inicio') ?? request()->input('fecha_inicio') }}">
                    <input type="hidden" name="fecha_fin"
                        value="{{ request('fecha_fin') ?? request()->input('fecha_fin') }}">
                    @if (request('asesor') || request()->input('asesor'))
                        <input type="hidden" name="asesor"
                            value="{{ request('asesor') ?? request()->input('asesor') }}">
                    @endif
                    <input type="hidden" name="conclusiones" value="{{ $conclusiones }}">
                    <input type="hidden" name="reporte_id" id="reporte_id" value="{{ $reporte_id }}">

                    <div class="modal-header">
                        <h5 class="modal-title">Guardar informe de intervencion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="col-12 mb-3">
                            <label class="form-label">Año</label>
                            <select id="selectAnioInforme" name="anio" class="form-control" required>
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 mb-3">
                            <label class="form-label">Mes</label>
                            <select id="selectMesInforme" name="mes" class="form-control" required>
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" id="btnEnviarInforme">Guardar informe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const anioSelect = document.getElementById('selectAnioInforme');
            const mesSelect = document.getElementById('selectMesInforme');

            const fechaActual = new Date();
            const currentYear = fechaActual.getFullYear();
            const mesActual = fechaActual.getMonth();

            for (let i = currentYear; i >= currentYear - 5; i--) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = i;
                anioSelect.appendChild(option);
            }

            function llenarMeses(anioSeleccionado) {
                mesSelect.innerHTML = "<option value=''>Seleccione</option>";
                let limiteMes = 11;
                if (parseInt(anioSeleccionado) === currentYear) {
                    limiteMes = mesActual;
                }
                for (let i = 0; i <= limiteMes; i++) {
                    const fecha = new Date(anioSeleccionado, i, 1);
                    const option = document.createElement("option");
                    option.value = i + 1;
                    option.textContent = fecha
                        .toLocaleString("es-ES", {
                            month: "long"
                        })
                        .replace(/^./, c => c.toUpperCase());
                    mesSelect.appendChild(option);
                }
            }
            llenarMeses(currentYear);
            // 🔹 Detectar cambio de año
            anioSelect.addEventListener('change', function() {
                llenarMeses(this.value);
            });

        });

        $(document).ready(function() {
            $('#btnGuardarInforme').on('click', function() {
                let modal = new bootstrap.Modal(document.getElementById('guardarInformeModal'));
                modal.show();
            });
        });

        $(document).on('submit', '#formSaveInforme', function(e) {

            e.preventDefault();

            const $form = $(this);
            const $btn = $('#btnEnviarInforme');

            if (!$form[0].checkValidity()) {
                $form[0].reportValidity();
                return;
            }

            $btn.prop('disabled', true).text('Guardando...');

            Swal.fire({
                title: 'Guardando informe',
                text: 'Por favor espere...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'El informe se ha guardado correctamente.',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        let modalElement = document.getElementById('guardarInformeModal');
                        let modalInstance = bootstrap.Modal.getInstance(modalElement);
                        modalInstance.hide();
                    });
                },
                error: function(xhr) {
                    let mensaje =
                        'El periodo ya fue registrado anteriormente. Por favor, elija otro año/mes.';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        mensaje = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: mensaje,
                        confirmButtonText: 'Aceptar'
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false).text('Guardar informe');
                }
            });

        });
    </script>
</body>

</html>
