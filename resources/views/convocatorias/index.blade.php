@extends('layouts.list', ['titulo'=> 'Convocatorias', 'tituloModal'=> 'convocatoria', 'exportar'=> $puedeExportar])

@section('form-filters')

    <div class="col-12 col-md-3 form-group mb-3">
        <label class="form-label" for="programa">Programa</label>
        <select class="form-select" name="programa" id="programa">
            <option value="" selected >Seleccione una opción</option>
            @foreach ($programas as $item)
                <option value="{{$item->programa_id}}" >{{$item->nombre}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-3 form-group mb-3">
        <label class="form-label" for="sector">Sector</label>
        <select class="form-select" name="sector" id="sector">
            <option value="" selected >Seleccione una opción</option>
            @foreach ($sectores as $item)
                    <option value="{{$item->sector_id}}" >{{$item->sectorNOMBRE}}</option>
            @endforeach
        </select>
    </div>

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
    <div class="row">

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="programa_id">Programa</label>
            <select class="form-select" name="programa_id" id="programa_id">
                <option value="" selected >Seleccione una opción</option>
                @foreach ($programas as $item)
                    <option value="{{$item->programa_id}}" >{{$item->nombre}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="nombre_convocatoria">Nombre <span class="text-muted">(Máximo 200 caracteres)</span></label>
            <input type="text" class="form-control" name="nombre_convocatoria" id="nombre_convocatoria" placeholder="Nombre " maxlength="200" required>
            <div class="invalid-feedback">El nombre debe tener máximo 200 caracteres.</div>
            <small class="text-muted caracteres-restantes" id="nombre_convocatoria_counter">200 caracteres restantes</small>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="persona_encargada">Persona a cargo <span class="text-muted">(Máximo 200 caracteres)</span></label>
            <input type="text" class="form-control" name="persona_encargada" id="persona_encargada" placeholder="Persona a cargo" maxlength="200" required>
            <div class="invalid-feedback">El nombre debe tener máximo 200 caracteres.</div>
            <small class="text-muted caracteres-restantes" id="persona_encargada_counter">200 caracteres restantes</small>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="correo_contacto" >Correo de contacto</label>
            <input type="email" class="form-control" name="correo_contacto" id="correo_contacto" placeholder="Correo de contacto" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" required>
            <div class="invalid-feedback">Ingrese un correo electrónico válido (no se permiten símbolos extraños).</div>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="telefono">Teléfono de contacto <span class="text-muted">(Solo números)</span></label>
            <input type="text" class="form-control" name="telefono" id="telefono" placeholder="Teléfono de contacto" pattern="[0-9]+" required>
            <div class="invalid-feedback">El teléfono solo debe contener números.</div>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="fecha_apertura_convocatoria">Fecha de inicio</label>
            <input type="date" class="form-control" name="fecha_apertura_convocatoria" id="fecha_apertura_convocatoria" placeholder="Fecha de inicio" required>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="fecha_cierre_convocatoria">Fecha de finalización</label>
            <input type="date" class="form-control" name="fecha_cierre_convocatoria" id="fecha_cierre_convocatoria" placeholder="Fecha de finalización" required>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="sector_id">Sector</label>
            <select class="form-select" name="sector_id" id="sector_id">
                <option value="" selected >Seleccione una opción</option>
                @foreach ($sectores as $item)
                    <option value="{{$item->sector_id}}" >{{$item->sectorNOMBRE}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="con_matricula">Con matricula</label>
            <select class="form-select" name="con_matricula" id="con_matricula">
                <option value="" selected >Seleccione una opción</option>
                <option value="0">No</option>
                <option value="1">Si</option>
            </select>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="asesores">Asesores</label>
            <select class="form-select" name="asesores[]" id="asesores" multiple >
                @foreach ($asesores as $item)
                    <option value="{{$item->id}}" >{{$item->name}} {{$item->lastname}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-12 form-group mb-3 mt-4">
            <h3 class="mb-0" for="pregunta_porcentaje">
                Preguntas  <button type="button" class="btn btn-sm btn-primary py-1" onclick="openAdd()" >Agregar</button>
            </h3>
            <div class="mb-2">
                <select class="form-select w-75" name="pregunta" id="pregunta" >
                    <option value="" disabled selected>Seleccione una opción para agregar</option>
                    @foreach ($preguntas as $item)
                        <option value="{{$item->requisito_id}}" >{{$item->requisito_titulo}}</option>
                    @endforeach
                </select>
            </div>
            <table class="table table-sm table-border border">
                <thead>                    
                    <th colspan="2" > Nombre </th>             
                </thead>
                <tbody id="table_opciones"></tbody>
            </table>
        </div>

    </div>

@endsection

@section('script')
    <script> 
        const btn_edit =  '{!! $esAsesor == 1 ? '' : '<button class="dropdown-item" onClick="openEditar()" >Editar</button>' !!}';
        window.TABLA = {
            urlApi: '/convocatorias',
            sortName: 'convocatoria_id',
            
            menu_row: ` ${btn_edit}
                        <a class="dropdown-item" href="/convocatorias/ROWID" >Ver detalles</a>
                        <a class="dropdown-item" href="/inscripciones/list?convocatoria=ROWID">Inscripciones</a>`,

            columns: [
                { data: 'nombre_programa', title: 'Programa', orderable: true },
                { data: 'nombre_convocatoria', title: 'Nombre', orderable: true },
                { data: 'persona_encargada', title: 'Persona a cargo', orderable: true },
                { data: 'telefono', title: 'Teléfono', orderable: true },
                { data: 'fecha_apertura_convocatoria', title: 'Fecha inicio', orderable: true, render: v => window.formatearFecha(v) },
                { data: 'fecha_cierre_convocatoria', title: 'Fecha finalización', orderable: true, render: v => window.formatearFecha(v) },
                { data: 'sector', title: 'Sector', orderable: true },
            ],

            initSelects: [ 
                { id:'programa'}, { id:'pregunta'}, 
                { id:'asesores', setting:{ placeholder: 'Selección multiple'}  },                
            ],

            initFiltros: @json($filtros),

            loadOptions: function(opciones) 
            {
                $("#table_opciones").html('');

                for(let i = 0; i< opciones.length; i++){
                    window.itemOption(opciones[i]);
                }
            }
        };

        window.openAdd = function() 
        {
            const id = $("#pregunta").val();
            const text = $("#pregunta option:selected").text();

            if( !(id && text) ) return;


            let existe = $("#table_opciones tr[data-id='" + id + "']").length > 0;
            if (existe) {
                Swal.fire({ title: "Elemento ya existe", icon: "info" });
                return;
            }


            window.itemOption({ requisito_id: id, requisito_titulo: text });

            $("#pregunta").val(null).trigger('change');

        }

        window.itemOption = function(row = {}) {
            const index = document.querySelectorAll("#table_opciones tr").length;

            const item = `
                <tr data-id="${row.requisito_id}" draggable="true">
                    <td>${row.requisito_titulo}</td>
                    <td style="width: 80px;">
                        <input type="hidden" name="requisitosTodos[${index}]" value="${row.requisito_id}" />
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeOption(this)">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>`;
            
            document.querySelector("#table_opciones").insertAdjacentHTML("beforeend", item);

            window.reindexInputs();
        };

        window.removeOption = function(btn) {
            btn.closest("tr").remove();
            window.reindexInputs();
        };

        window.enableDrag = function() {
            const tbody = document.getElementById("table_opciones");

            let draggingRow = null;

            // Solo se ejecuta UNA VEZ
            tbody.addEventListener("dragstart", (e) => {
                if (e.target.tagName === "TR") {
                    draggingRow = e.target;
                    draggingRow.classList.add("dragging");
                }
            });

            tbody.addEventListener("dragend", (e) => {
                if (draggingRow) {
                    draggingRow.classList.remove("dragging");
                    draggingRow = null;
                    reindexInputs();
                }
            });

            tbody.addEventListener("dragover", (e) => {
                e.preventDefault();
                const rows = Array.from(tbody.querySelectorAll("tr:not(.dragging)"));

                let nextRow = rows.find(r => e.clientY <= r.getBoundingClientRect().top + r.offsetHeight / 2);

                if (draggingRow) {
                    if (nextRow) {
                        tbody.insertBefore(draggingRow, nextRow);
                    } else {
                        tbody.appendChild(draggingRow);
                    }
                }
            });
        };

        window.reindexInputs = function() {
            document.querySelectorAll("#table_opciones tr").forEach((row, i) => {
                let hidden = row.querySelector("input[type=hidden]");
                if (hidden) hidden.name = `requisitosTodos${i}]`;
            });
        };

        // Inicializa una sola vez al cargar
        window.enableDrag();

        // Validaciones del lado del cliente
        document.addEventListener('DOMContentLoaded', function() {
            
            // Validación de máximo 200 caracteres para campos de nombre
            const nombreFields = ['nombre_convocatoria', 'persona_encargada'];
            nombreFields.forEach(function(fieldId) {
                const field = document.getElementById(fieldId);
                const counter = document.getElementById(fieldId + '_counter');
                
                if (field && counter) {
                    field.addEventListener('input', function() {
                        const length = this.value.length;
                        const remaining = 200 - length;
                        counter.textContent = remaining + ' caracteres restantes';
                        
                        if (length > 200) {
                            this.value = this.value.substring(0, 200);
                            counter.textContent = '0 caracteres restantes';
                        }
                        
                        // Validación visual
                        if (length > 200) {
                            this.classList.add('is-invalid');
                        } else {
                            this.classList.remove('is-invalid');
                        }
                    });
                }
            });

            // Validación de teléfono: solo números
            const telefonoField = document.getElementById('telefono');
            if (telefonoField) {
                // Prevenir entrada de caracteres no numéricos
                telefonoField.addEventListener('keypress', function(e) {
                    const char = String.fromCharCode(e.which);
                    if (!/[0-9]/.test(char)) {
                        e.preventDefault();
                        return false;
                    }
                });

                // Validar al pegar texto
                telefonoField.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                    const numbersOnly = pastedText.replace(/[^0-9]/g, '');
                    this.value = numbersOnly;
                    
                    if (numbersOnly !== pastedText) {
                        Swal.fire({
                            title: 'Caracteres no válidos eliminados',
                            text: 'Solo se permiten números en el teléfono.',
                            icon: 'warning',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });

                // Validar al escribir
                telefonoField.addEventListener('input', function() {
                    const originalValue = this.value;
                    const numbersOnly = originalValue.replace(/[^0-9]/g, '');
                    
                    if (originalValue !== numbersOnly) {
                        this.value = numbersOnly;
                        this.classList.add('is-invalid');
                        
                        setTimeout(() => {
                            this.classList.remove('is-invalid');
                        }, 2000);
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });
            }

            // Validación mejorada de correo electrónico
            const correoField = document.getElementById('correo_contacto');
            if (correoField) {
                // Expresión regular para validar email válido (sin símbolos extraños)
                const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                
                correoField.addEventListener('blur', function() {
                    const email = this.value.trim();
                    
                    if (email && !emailPattern.test(email)) {
                        this.classList.add('is-invalid');
                        Swal.fire({
                            title: 'Correo inválido',
                            text: 'Por favor ingrese un correo electrónico válido. No se permiten símbolos extraños.',
                            icon: 'error',
                            timer: 3000
                        });
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });

                correoField.addEventListener('input', function() {
                    // Remover caracteres no permitidos en tiempo real (opcional)
                    // Solo permitir: letras, números, puntos, guiones, guiones bajos, @, y %
                    const value = this.value;
                    const cleaned = value.replace(/[^a-zA-Z0-9._%+-@]/g, '');
                    
                    if (value !== cleaned) {
                        this.value = cleaned;
                    }
                });
            }

            // Validación antes de enviar el formulario
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    let isValid = true;

                    // Validar campos de nombre
                    nombreFields.forEach(function(fieldId) {
                        const field = document.getElementById(fieldId);
                        if (field && field.value.length > 200) {
                            field.classList.add('is-invalid');
                            isValid = false;
                        }
                    });

                    // Validar teléfono
                    if (telefonoField && !/^[0-9]+$/.test(telefonoField.value)) {
                        telefonoField.classList.add('is-invalid');
                        isValid = false;
                    }

                    // Validar correo
                    if (correoField) {
                        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                        if (correoField.value && !emailPattern.test(correoField.value.trim())) {
                            correoField.classList.add('is-invalid');
                            isValid = false;
                        }
                    }

                    if (!isValid) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Error de validación',
                            text: 'Por favor corrija los errores en el formulario antes de continuar.',
                            icon: 'error'
                        });
                        return false;
                    }
                });
            }
        });

    </script>

    <style>
        #table_opciones tr {
            cursor: grab;
            background: white;
        }

        #table_opciones tr.dragging {
            opacity: 0.5;
            background: #f8f9fa;
            cursor: grabbing;
        }

        .caracteres-restantes {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.875rem;
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .is-invalid ~ .invalid-feedback {
            display: block;
        }
    </style>
@endsection