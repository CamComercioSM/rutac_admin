<div class="modal fade" id="informeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formPreviewInforme" action="{{ url('/intervenciones/informe/preview') }}" method="POST" target="_blank">
                @csrf

                <input type="hidden" name="asesor" id="preview_asesor">
                <input type="hidden" name="unidad" id="preview_unidad">

                <div class="modal-header">
                    <h5 class="modal-title">Informe Intervenciones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio_informe" name="fecha_inicio" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" id="fecha_fin_informe" name="fecha_fin" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Conclusiones</label>
                            <div id="conclusionesI"></div>
                            <input type="hidden" name="conclusiones" id="conclusionesI_input">
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnExportInforme">Generar Informe</button>
                </div>

            </form>
        </div>
    </div>
</div>
