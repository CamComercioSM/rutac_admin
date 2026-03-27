<!-- ===================== SOPORTE ===================== -->
<div class="col-12 col-md-12 form-group mb-3" id="contFormFile">

    <label class="form-label">Soporte</label>

    <!-- SOPORTE EXISTENTE -->
    <div id="soporteActualContainer" class="mb-2 d-none">
        <div class="alert alert-info">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <!-- INFO -->
                <div class="w-100">
                    <i class="ri-attachment-line"></i>
                    <strong>Soporte cargado:</strong>

                    <!-- 🔥 RUTA VISIBLE -->
                    <div class="mt-1">
                        <small id="textoRutaSoporte" class="text-break"></small>
                    </div>
                </div>

                <!-- ACCIONES -->
                <div class="d-flex gap-2">

                    <button type="button" class="btn btn-sm btn-secondary" onclick="copiarRutaSoporte()">
                        <i class="ri-file-copy-line"></i> Copiar
                    </button>

                    <a href="#" target="_blank" id="linkSoporteActual" class="btn btn-sm btn-primary">
                        <i class="ri-eye-line"></i> Ver
                    </a>

                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarSoporteActual()">
                        <i class="ri-delete-bin-line"></i> Quitar
                    </button>

                </div>

            </div>

        </div>
    </div>

    <!-- INPUT FILE -->
    <input class="form-control" type="file" id="formFile" name="formFile">

    <!-- INPUT OCULTO -->
    <input type="hidden" id="soporteActual" name="soporteActual">

    <small class="text-muted">
        Si no cargas un nuevo archivo, se mantendrá el actual.
    </small>

</div>
<!-- Media -->

{{-- 
<div class="card mb-6">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 card-title">Cargar soporte</h5>
        <a href="javascript:void(0);" class="fw-medium">Agregar url de la evidencia</a>
    </div>
    <div class="card-body">
        <!-- Dropzone container -->
        <div class="dropzone needsclick" id="dropzone-basic" data-url="/upload">
            <div class="dz-message needsclick">
                <div class="d-flex justify-content-center">
                    <div class="avatar avatar-md">
                        <span class="avatar-initial rounded bg-label-secondary">
                            <i class="icon-base ri ri-upload-2-line icon-24px"></i>
                        </span>
                    </div>
                </div>

                <p class="h4 needsclick my-2">Arrastre los archivos aquí</p>
                <button type="button" class="needsclick btn btn-sm btn-outline-primary d-inline"
                    id="btnBrowse">
                    Explorar
                </button>
            </div>

            <!-- Aquí se renderiza el preview -->
            <div class="dz-preview-container mt-3"></div>

            <!-- Fallback opcional (si quieres mantenerlo) -->
            <div class="fallback mt-2">
                <input name="" id="" type="file"
                    accept=".jpg,.jpeg,.png,.gif" />
            </div>
        </div>
    </div>
</div> 
--}}
<!-- /Media -->
