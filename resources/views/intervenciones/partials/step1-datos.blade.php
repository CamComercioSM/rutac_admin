@php
    $fechaInicio = now()->format('Y-m-d\TH:i');
    $fechaFin = now()->addMinutes(30)->format('Y-m-d\TH:i');
@endphp
<div class="row">
    <div class="col-12 col-md-7 form-group mb-3">
        <label class="form-label" for="programa_id">Programa</label>
        <select class="form-select" name="programa_id" id="programa_id" required>
            <option value="" selected>Seleccione una opción</option>
            @foreach ($programas as $item)
                <option value="{{ $item->programa_id }}">{{ $item->nombre }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-5 form-group mb-3">
        <label class="form-label" for="convocatoria_id">Ciclo</label>
        <select class="form-select" name="convocatoria_id" id="convocatoria_id" disabled>
            <option value="" selected>Seleccione primero un programa</option>
        </select>
    </div>

    <div class="col-12 col-md-3 form-group mb-3">
        <label class="form-label" for="fase_programa_id">Fase</label>
        <select class="form-select" name="fase_id" id="fase_programa_id" required>
            <option value="" selected>Seleccione una opción</option>
            @foreach ($fasesProgramas as $item)
                <option value="{{ $item->fase_id }}">
                    {{ $item->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="categoria_id">Actividad</label>
        <select class="form-select" name="categoria_id" id="categoria_id" required>
            <option value="" selected>Seleccione una opción</option>
            @foreach ($categorias as $item)
                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-5 form-group mb-3">
        <label class="form-label" for="tipo_id">Tarea</label>
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
        <select class="form-select" name="modalidad" id="modalidad">
            <option value="" selected>Seleccione una opción</option>
            @foreach ($modalidades as $index => $item)
                <option value="{{ $index }}">{{ $item }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="fecha_inicio">Fecha inicio</label>
        <input type="datetime-local" class="form-control" name="fecha_inicio" id="fecha_inicio"
            placeholder="Fecha inicio" required value="{{ old('fecha_inicio', $fechaInicio) }}">
    </div>

    <div class="col-12 col-md-4 form-group mb-3">
        <label class="form-label" for="fecha_fin">Fecha fin</label>
        <input type="datetime-local" class="form-control" name="fecha_fin" id="fecha_fin" placeholder="Fecha fin"
            required value="{{ old('fecha_fin', $fechaFin) }}">
    </div>
    
    <div class="col-12 col-md-12 form-group mb-3">
        <label class="form-label" for="descripcion">Descripción</label>
        <div id="descripcion"></div>
    </div>
</div>
