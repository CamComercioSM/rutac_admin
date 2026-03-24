<div class="col-12">

    <h4 class="mb-4">Unidades productivas intervenidas</h4>

    <div class="row g-4">

        <!-- ================= UNIDADES PRODUCTIVAS ================= -->
        <div class="col-md-6">
            <div class="card p-3">

                <div class="mb-3">
                    <label for="unidadAdd" class="form-label">Unidad productiva</label>

                    <div class="d-flex align-items-center gap-2">

                        <!-- BOTÓN IZQUIERDA -->
                        <a href="/unidadesProductivas/list" target="_blank" class="btn btn-outline-primary btn-icon">
                            <i class="ri ri-search-line"></i>
                        </a>

                        <select class="form-select" name="unidadAdd" id="unidadAdd">
                            <option value="" disabled selected>Seleccione una unidad
                            </option>
                            @foreach ($unidades as $item)
                                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label"># Participantes</label>
                        <input class="form-control" type="number" id="participantes">
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <button type="button" class="btn btn-primary w-100" onclick="openAdd()">
                            Agregar
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- LISTADO -->
        <div class="col-md-6">
            <div class="form-floating">
                <input id="TagifyUserList" name="unidades" class="form-control h-auto">
                <label>Unidades productivas</label>
            </div>
        </div>

        <!-- ================= OTROS PARTICIPANTES ================= -->
        <div class="col-md-6">
            <div class="card p-3">

                <div class="mb-3">
                    <label for="otroParticipanteAdd" class="form-label">
                        Otros participantes
                    </label>

                    <div class="d-flex align-items-center gap-2">

                        <!-- BOTÓN + (CREAR NUEVO) -->
                        <button type="button" class="btn btn-outline-success btn-icon" data-bs-toggle="modal"
                            data-bs-target="#modalNuevoParticipante">
                            <i class="ri ri-add-line"></i>
                        </button>

                        <select class="form-select" id="otroParticipanteAdd">
                            <option value="" disabled selected>Selecciona participante
                            </option>
                            @foreach ($leads as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label"># Participantes</label>
                        <input class="form-control" type="number" id="participantes_otros">
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <button type="button" class="btn btn-primary w-100" onclick="openAddOtroParticipante()">
                            Agregar
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- LISTADO -->
        <div class="col-md-6">
            <div class="form-floating">
                <input id="TagifyOtrosParticipantes" name="otrosParticipantes" class="form-control h-auto">
                <label>Otros Participantes</label>
            </div>
        </div>

    </div>
</div>
