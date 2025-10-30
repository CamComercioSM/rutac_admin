@extends('layouts.list', ['titulo'=> 'Programas', 'tituloModal'=> 'programa'])

@section('form-filters')

    <div class="col-12 col-md-5 form-group mb-3">
        <label class="form-label" for="etapa">Etapa</label>
        <select class="form-select" name="etapa" id="etapa" >
            <option value="" selected >Seleccione una opción</option>
            @foreach ($etapas as $item)
                <option value="{{$item->etapa_id}}" >{{$item->name}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-5 form-group mb-3">
        <label class="form-label" for="modalidad">Modalidad</label>
        <select class="form-select" name="modalidad" id="modalidad" >
            <option value="" selected >Seleccione una opción</option>
            @foreach ($modalidades as $index => $item)
                <option value="{{$index}}" >{{$item}}</option>
            @endforeach
        </select>
    </div>

@endsection

@section('form-fields')
    <div class="row">

        <div class="col-12 col-md-9 form-group mb-3">
            <label class="form-label" for="nombre">Nombre </label>
            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre " required>
        </div>

        <div class="col-12 col-md-3 form-group mb-3">
            <label class="form-label" for="codigo_pac">Código PAC </label>
            <input type="text" class="form-control" name="codigo_pac" id="codigo_pac" placeholder="Código PAC " required>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label">Descripción</label>
            <div id="descripcion"></div>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label">Beneficios</label>
            <div id="beneficios"></div>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label">Requisitos</label>
            <div id="requisitos"></div>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" >Dirigido A </label>
            <div id="dirigido_a"></div>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label">Objetivo </label>
            <div id="objetivo"></div>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label">Determinantes </label>
            <div id="determinantes"></div>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label">Herramientas Requeridas </label>
            <div id="herramientas_requeridas"></div>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label">Aporte </label>
            <div id="informacion_adicional"></div>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="sitio_web">Sitio Web  </label>
            <input type="text" class="form-control" name="sitio_web" id="sitio_web" placeholder="Sitio Web  " >
        </div>

        <div class="col-sm-12 mb-3">
            <label class="form-label" for="logo_archivo">Logo</label>
            <input class="form-control" type="file" name="logo_archivo" id="logo_archivo" accept=".jpg,.png">
        </div>

        <div class="col-sm-12 mb-3">
            <label class="form-label" for="procedimiento_imagen_archivo">Imagen del Procedimiento </label>
            <input class="form-control" type="file" name="procedimiento_imagen_archivo" id="procedimiento_imagen_archivo" accept=".jpg,.png">
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="duracion">Duración </label>
            <input type="text" class="form-control" name="duracion" id="duracion" placeholder="Duración " >
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="es_virtual">Modalidad</label>
            <select class="form-select" name="es_virtual" id="es_virtual" required >
                @foreach ($modalidades as $index => $item)
                    <option value="" selected >Seleccione una opción</option>
                    <option value="{{$index}}" >{{$item}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-12 form-group mb-3">
            <label class="form-label" for="etapas">Etapas</label>
            <select class="form-select" name="etapas[]" id="etapas" multiple >
                @foreach ($etapas as $item)
                    <option value="{{$item->etapa_id}}" >{{$item->name}}</option>
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

    @vite([
        'resources/assets/vendor/libs/quill/typography.scss', 
        'resources/assets/vendor/libs/quill/editor.scss',
    ])

    @vite([
        'resources/assets/vendor/libs/quill/katex.js', 
        'resources/assets/vendor/libs/quill/quill.js'
    ])

    <script> 
        window.TABLA = {
            urlApi: '/programas',
            sortName: 'programa_id',

            menu_row: ` <button class="dropdown-item" onClick="openEditar()" >Editar</button>
                        <a class="dropdown-item" href="/programas/ROWID" >Ver detalles</a>
                        <a class="dropdown-item" href="/convocatorias/list?programa=ROWID">Convocatorias</a>`,

            columns: [
                { data: 'codigo_pac', title: 'Código PAC', orderable: true },
                { data: 'nombre', title: 'Nombre', orderable: true },
                { data: 'duracion', title: 'Duración', orderable: true },
                { data: 'modalidad', title: 'Modalidad', orderable: true },
                { data: 'etapas_str', title: 'Etapas', orderable: true }
            ],

            initSelects: [ 
                { id:'pregunta'}, 
                { id:'etapas', setting:{ placeholder: 'Selección multiple'}  },                
            ],

            initEditors: [ 
                { id:'descripcion' },
                { id:'beneficios' },
                { id:'requisitos' },
                { id:'dirigido_a' },
                { id:'objetivo' },
                { id:'determinantes' },
                { id:'herramientas_requeridas' },
                { id:'informacion_adicional' },               
            ],

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
                if (hidden) hidden.name = `requisitosTodos[${i}]`;
            });
        };

        // Inicializa una sola vez al cargar
        window.enableDrag();

    </script>
@endsection