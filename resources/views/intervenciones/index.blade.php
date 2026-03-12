@extends('layouts.list', ['titulo' => 'Intervenciones', 'tituloModal' => 'intervención'])

@section('form-filters')

    <div class="col-12 col-md-6 form-group mb-3">
        <label class="form-label" for="unidad">Unidad productiva</label>
        <select class="form-select" name="unidad" id="unidad">
            <option value="" selected>Seleccione una opción</option>
            @foreach ($unidades as $item)
                <option value="{{ $item->unidadproductiva_id }}">{{ $item->business_name }}</option>
            @endforeach
        </select>
    </div>

    @if (!$esAsesor)
        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="asesor">Usuario</label>
            <select class="form-select" name="asesor" id="asesor">
                <option value="" selected>Seleccione una opción</option>
                @foreach ($asesores as $item)
                    <option value="{{ $item->id }}">{{ $item->name }} {{ $item->lastname }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="col-12 col-md-3 form-group mb-3">
        <label class="form-label" for="fecha_inicio">Fecha inicio</label>
        <input class="form-control" type="date" name="fecha_inicio" id="fecha_inicio">
    </div>

    <div class="col-12 col-md-3 form-group mb-3">
        <label class="form-label" for="fecha_inicio">Fecha fin</label>
        <input class="form-control" type="date" name="fecha_fin" id="fecha_fin">
    </div>

@endsection

@section('form-fields')
    <!-- Modern Vertical Wizard -->
    <div class="row">
        <div class="col-12">
            <small class="fw-medium">Intervencion</small>
            <div class="bs-stepper vertical wizard-modern wizard-modern-vertical mt-2">
                <div class="bs-stepper-header gap-lg-2">
                    <div class="step" data-target="#account-details-modern-vertical">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle"><i class="icon-base ri ri-check-line"></i></span>
                            <span class="bs-stepper-label">
                                <span class="bs-stepper-number">01</span>
                                <span class="d-flex flex-column gap-1 ms-2">
                                    <span class="bs-stepper-title">Datos de la intervencion</span>
                                    <span class="bs-stepper-subtitle">Programa, convocatoria ...</span>
                                </span>
                            </span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step" data-target="#avances-info-modern-vertical">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle"><i class="icon-base ri ri-check-line"></i></span>
                            <span class="bs-stepper-label">
                                <span class="bs-stepper-number">02</span>
                                <span class="d-flex flex-column gap-1 ms-2">
                                    <span class="bs-stepper-title">Avances y resultados</span>
                                    <span class="bs-stepper-subtitle">Resultados obtenidos</span>
                                </span>
                            </span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step" data-target="#personal-info-modern-vertical">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle"><i class="icon-base ri ri-check-line"></i></span>
                            <span class="bs-stepper-label">
                                <span class="bs-stepper-number">03</span>
                                <span class="d-flex flex-column gap-1 ms-2">
                                    <span class="bs-stepper-title">Unidades intervenidas</span>
                                    <span class="bs-stepper-subtitle">Información de las unidades</span>
                                </span>
                            </span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step" data-target="#social-links-modern-vertical">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle"><i class="icon-base ri ri-check-line"></i></span>
                            <span class="bs-stepper-label">
                                <span class="bs-stepper-number">04</span>
                                <span class="d-flex flex-column gap-1 ms-2">
                                    <span class="bs-stepper-title">Soportes</span>
                                    <span class="bs-stepper-subtitle">Evidencias y documentos</span>
                                </span>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="bs-stepper-content">
                    <!-- informacion de intervencion -->
                    <div id="account-details-modern-vertical" class="content">
                        <div class="row">
                            <div class="col-12 col-md-6 form-group mb-3">
                                <label class="form-label" for="programa_id">Programa</label>
                                <select class="form-select" name="programa_id" id="programa_id" required>
                                    <option value="" selected>Seleccione una opción</option>
                                    @foreach ($programas as $item)
                                        <option value="{{ $item->programa_id }}">{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-6 form-group mb-3">
                                <label class="form-label" for="convocatoria_id">Convocatoria</label>
                                <select class="form-select" name="convocatoria_id" id="convocatoria_id" required disabled>
                                    <option value="" selected>Seleccione primero un programa</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-6 form-group mb-3">
                                <label class="form-label" for="categoria_id">Categoría</label>
                                <select class="form-select" name="categoria_id" id="categoria_id" required>
                                    <option value="" selected>Seleccione una opción</option>
                                    @foreach ($categorias as $item)
                                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-6 form-group mb-3">
                                <label class="form-label" for="tipo_id">Tipo</label>
                                <select class="form-select" name="tipo_id" id="tipo_id" required>
                                    <option value="" selected>Seleccione una opción</option>
                                    @foreach ($tipos as $item)
                                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-12 form-group mb-3 d-none" id="cont_referencia">
                                <label for="referencia_id" class="form-label"></label>
                                <select class="form-select w-75" name="referencia_id" id="referencia_id"></select>
                            </div>

                            <div class="col-12 col-md-4 form-group mb-3">
                                <label class="form-label" for="modalidad">Modalidad</label>
                                <select class="form-select" name="modalidad" id="modalidad" required>
                                    <option value="" selected>Seleccione una opción</option>
                                    @foreach ($modalidades as $index => $item)
                                        <option value="{{ $index }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-4 form-group mb-3">
                                <label class="form-label" for="fecha_inicio">Fecha inicio</label>
                                <input type="datetime-local" class="form-control" name="fecha_inicio" id="fecha_inicio"
                                    placeholder="Fecha inicio" required>
                            </div>

                            <div class="col-12 col-md-4 form-group mb-3">
                                <label class="form-label" for="fecha_fin">Fecha fin</label>
                                <input type="datetime-local" class="form-control" name="fecha_fin" id="fecha_fin"
                                    placeholder="Fecha fin" required>
                            </div>
                            <div class="col-12 col-md-12 form-group mb-3">
                                <label class="form-label" for="descripcion">Descripción</label>
                                <div id="descripcion"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Avances -->
                    <div id="avances-info-modern-vertical" class="content">
                        <div class="col-12 col-md-12 form-group mb-3">
                            <label class="form-label" for="conclusiones">Conclusiones</label>
                            <div id="conclusiones"></div>
                        </div>
                        {{-- <div class="col-12">
                                <div class="card-body pb-12">
                                    <small class="fw-medium">Porcentaje de avance</small>
                                    <div class="noUi-info mt-6 mb-12" id="slider-info"></div>
                                </div>
                            </div> --}}
                    </div>
                    <!-- Unidades productivas -->
                    <div id="personal-info-modern-vertical" class="content">
                        <div class="col-12 col-md-12 form-group mb-4">
                            <h4 class="mb-0"> Unidades productivas intervenidas </h4>
                            <div class="row">
                                <div class="col-6">
                                    <div class="row">
                                        <div class="row">
                                            <div class="col-12 col-md-9 form-group mb-3">
                                                <label for="unidadAdd" class="form-label">Unidad productiva</label>
                                                <div class="d-flex align-items-center gap-2">
                                                    <select class="form-select w-75" name="unidadAdd" id="unidadAdd">
                                                        <option value="" disabled selected>Seleccione una unidad
                                                            para agregar
                                                        </option>
                                                        @foreach ($unidades as $item)
                                                            <option value="{{ $item->id }}">{{ $item->nombre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <a href="/unidadesProductivas/list"
                                                        class="btn btn-outline-primary btn-icon">
                                                        <i class="icon-base ri ri-search-line"></i>
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-3 form-group mb-3">
                                                <label for="participantes" class="form-label"># Participantes</label>
                                                <input class="form-control" type="number" id="participantes"
                                                    name="participantes" placeholder="Cantidad de participantes">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3 form-group mb-3 pt-5">
                                            <button type="button" class="btn btn-xl btn-primary py-1 mt-3"
                                                onclick="openAdd()">Agregar</button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Users List -->
                                <div class="col-md-6 mb-3 mt-3">
                                    <div class="form-floating form-floating-outline">
                                        <input id="TagifyUserList" name="unidades" class="form-control h-auto"
                                            value="" />
                                        <label for="TagifyUserList">Unidades productivas</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="row">
                                        <div class="row">
                                            <div class="col-12 col-md-9 form-group mb-3">
                                                <label for="otroParticipanteAdd" class="form-label">Otros
                                                    participantes</label>
                                                <div class="d-flex align-items-center gap-2">
                                                    <select class="form-select w-75" name="otroParticipanteAdd"
                                                        id="otroParticipanteAdd">
                                                        <option value="" disabled selected>Selecciona otro
                                                            Participante
                                                        </option>
                                                        @foreach ($leads as $item)
                                                            <option value="{{ $item->id }}">{{ $item->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="">
                                                        <button class="btn btn-outline-primary btn-icon">
                                                            <i class="icon-base ri ri-add-line"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-3 form-group mb-3">
                                                <label for="participantes_otros" class="form-label">#
                                                    Participantes</label>
                                                <input class="form-control" type="number" id="participantes_otros"
                                                    name="participantesOtros" placeholder="Cantidad de participantes">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3 form-group mb-3 pt-5">
                                            <button type="button" class="btn btn-xl btn-primary py-1 mt-3"
                                                onclick="openAddOtroParticipante()">Agregar</button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Users List -->
                                <div class="col-md-6 mb-3 mt-3">
                                    <div class="form-floating form-floating-outline">
                                        <input id="TagifyOtrosParticipantes" name="otrosParticipantes"
                                            class="form-control h-auto" value="" />
                                        <label for="TagifyOtrosParticipantes">Otros Participantes</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Soporte -->
                    <div id="social-links-modern-vertical" class="content">
                        {{-- <div class="col-12 col-md-12 form-group mb-3" id="contFormFile">
                                <label for="formFile" class="form-label">Soporte (opcional)</label>
                                <input class="form-control" type="file" id="formFile" name="formFile">
                            </div> --}}
                        <!-- Media -->
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
                                        <input name="formFile" id="formFile" type="file"
                                            accept=".jpg,.jpeg,.png,.gif" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Media -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Modern Vertical Wizard -->
    {{-- <div class="row">

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="programa_id">Programa</label>
            <select class="form-select" name="programa_id" id="programa_id" required>
                <option value="" selected>Seleccione una opción</option>
                @foreach ($programas as $item)
                    <option value="{{ $item->programa_id }}">{{ $item->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="convocatoria_id">Convocatoria</label>
            <select class="form-select" name="convocatoria_id" id="convocatoria_id" required disabled>
                <option value="" selected>Seleccione primero un programa</option>
            </select>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="categoria_id">Categoría</label>
            <select class="form-select" name="categoria_id" id="categoria_id" required>
                <option value="" selected>Seleccione una opción</option>
                @foreach ($categorias as $item)
                    <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="tipo_id">Tipo</label>
            <select class="form-select" name="tipo_id" id="tipo_id" required>
                <option value="" selected>Seleccione una opción</option>
                @foreach ($tipos as $item)
                    <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-12 form-group mb-3 d-none" id="cont_referencia">
            <label for="referencia_id" class="form-label"></label>
            <select class="form-select w-75" name="referencia_id" id="referencia_id"></select>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="modalidad">Modalidad</label>
            <select class="form-select" name="modalidad" id="modalidad" required>
                <option value="" selected>Seleccione una opción</option>
                @foreach ($modalidades as $index => $item)
                    <option value="{{ $index }}">{{ $item }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="fecha_inicio">Fecha inicio</label>
            <input type="datetime-local" class="form-control" name="fecha_inicio" id="fecha_inicio"
                placeholder="Fecha inicio" required>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="fecha_fin">Fecha fin</label>
            <input type="datetime-local" class="form-control" name="fecha_fin" id="fecha_fin" placeholder="Fecha fin"
                required>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="descripcion">Descripción</label>
            <div id="descripcion"></div>
        </div>

        <div class="col-12 col-md-12 form-group mb-4">

            <h4 class="mb-0"> Unidades productivas intervenidas </h4>
            <div class="row">

                <div class="col-12 col-md-6 form-group mb-3">
                    <label for="unidadAdd" class="form-label">Unidad productiva</label>
                    <div class="d-flex align-items-center gap-2">
                        <select class="form-select w-75" name="unidadAdd" id="unidadAdd">
                            <option value="" disabled selected>Seleccione una unidad para agregar</option>
                            @foreach ($unidades as $item)
                                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                            @endforeach
                        </select>
                        <a href="/unidadesProductivas/list" class="btn btn-outline-primary btn-icon">
                            <i class="icon-base ri ri-search-line"></i>
                        </a>
                    </div>
                </div>

                <div class="col-12 col-md-3 form-group mb-3">
                    <label for="participantes" class="form-label">Cantidad de participantes</label>
                    <input class="form-control" type="number" id="participantes" name="participantes"
                        placeholder="Cantidad de participantes">
                </div>

                <div class="col-12 col-md-3 form-group mb-3 pt-5">
                    <button type="button" class="btn btn-xl btn-primary py-1 mt-3" onclick="openAdd()">Agregar</button>
                </div>
            </div>
            <table class="table table-sm table-border border">
                <thead>
                    <th> Nombre </th>
                    <th> # paricipantes </th>
                    <th></th>
                </thead>
                <tbody id="table_opciones"></tbody>
            </table>

            <div class="row">

                <div class="col-12 col-md-6 form-group mb-3">
                    <label for="otroParticipanteAdd" class="form-label">Otros participantes</label>
                    <div class="d-flex align-items-center gap-2">
                        <select class="form-select w-75" name="otroParticipanteAdd" id="otroParticipanteAdd">
                            <option value="" disabled selected>Selecciona otro Participante</option>
                            @foreach ($leads as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                        <div class="">
                            <button class="btn btn-outline-primary btn-icon">
                                <i class="icon-base ri ri-add-line"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-3 form-group mb-3">
                    <label for="participantes_otros" class="form-label">Cantidad de participantes</label>
                    <input class="form-control" type="number" id="participantes_otros" name="participantesOtros"
                        placeholder="Cantidad de participantes">
                </div>

                <div class="col-12 col-md-3 form-group mb-3 pt-5">
                    <button type="button" class="btn btn-xl btn-primary py-1 mt-3"
                        onclick="openAddOtroParticipante()">Agregar</button>
                </div>
            </div>

            <table class="table table-sm table-border border">
                <thead>
                    <th> Nombre </th>
                    <th> # paricipantes </th>
                    <th></th>
                </thead>
                <tbody id="table_otros_participantes"></tbody>
            </table>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="conclusiones">Conclusiones</label>
            <div id="conclusiones"></div>
        </div>

        <div class="col-12 col-md-12 form-group mb-3" id="contFormFile">
            <label for="formFile" class="form-label">Soporte (opcional)</label>
            <input class="form-control" type="file" id="formFile" name="formFile">
        </div>

        <label class="switch switch-lg mt-2">
            <input type="checkbox" class="switch-input" id="switchBorrador" />

            <span class="switch-toggle-slider">
                <span class="switch-off">
                    <i class="icon-base ri ri-draft-line"></i>
                </span>
                <span class="switch-on">
                    <i class="icon-base ri ri-check-line"></i>
                </span>
            </span>

            <span class="switch-label" id="switchLabel">Borrador</span>
        </label>
    </div> --}}
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
                            <textarea class="form-control" name="conclusiones" id="conclusionesI" rows="6"
                                placeholder="Ingrese las conclusiones"></textarea>
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
@endsection

@section('script')
    @vite(['resources/assets/vendor/libs/quill/typography.scss', 'resources/assets/vendor/libs/quill/editor.scss', 'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss'])

    @vite(['resources/assets/vendor/libs/quill/katex.js', 'resources/assets/vendor/libs/quill/quill.js', 'resources/assets/js/form-wizard-validation.js', 'resources/js/reporteMensual/intervenciones.js'])

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
            sortName: 'id',
            accion_editar: false,
            columns: [{
                    data: 'categoria',
                    title: 'Categoría',
                    orderable: true
                },
                {
                    data: 'tipo',
                    title: 'Tipo',
                    orderable: true
                },
                {
                    data: 'modalidad',
                    title: 'Modalidad',
                    orderable: true
                },
                {
                    data: 'fecha_inicio',
                    title: 'F. inicio',
                    orderable: true
                },
                {
                    data: 'fecha_fin',
                    title: 'F. fin',
                    orderable: true
                },
                {
                    data: 'unidad',
                    title: 'Unidad productiva',
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
                    data: 'descripcion',
                    title: 'Descripción',
                    orderable: false,
                    render: function(data, type, row) {
                        return window.limitarTexto(data || '', 150, 'Descripción');
                    }
                },
                {
                    data: 'conclusiones',
                    title: 'Conclusiones',
                    orderable: false,
                    render: function(data, type, row) {
                        return window.limitarTexto(data || '', 150, 'Conclusiones');
                    }
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
            }, {
                id: 'conclusiones'
            }],
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
            $("#participantes").val(null);
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
            $("#participantes_otro").val(null);
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
                email: `Participantes: ${row.participantes}`,
                avatar: 'https://via.placeholder.com/40?text=UP'
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
                email: `Participantes: ${row.participantes}`,
                avatar: 'https://via.placeholder.com/40?text=UP'
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

                if (!($("#fecha_inicio").val() && $("#fecha_fin").val())) {
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
                var $filters = $('#filters');
                $('#preview_fecha_inicio').val($filters.find('input[name="fecha_inicio"]').val() || '');
                $('#preview_fecha_fin').val($filters.find('input[name="fecha_fin"]').val() || '');
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
@endsection
