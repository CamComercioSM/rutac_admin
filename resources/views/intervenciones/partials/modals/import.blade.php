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
