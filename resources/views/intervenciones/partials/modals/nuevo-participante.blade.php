<div class="modal fade" id="modalNuevoParticipante" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Nuevo participante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">

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

                    <div class="col-md-4">
                        <label class="form-label">Documento *</label>
                        <input type="text" class="form-control" id="lead_document">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Teléfono *</label>
                        <input type="text" class="form-control" id="lead_phone">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="lead_name">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Correo</label>
                        <input type="email" class="form-control" id="lead_email">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Observaciones</label>
                        <input type="text" class="form-control" id="lead_description">
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary" onclick="guardarLead()">Guardar</button>
            </div>

        </div>
    </div>
</div>
