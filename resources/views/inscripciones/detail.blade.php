@extends('layouts.layoutMaster')

<!-- Vendor Styles -->
@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
])
@endsection

@section('content')

<div class="row">
    <div class="col-12 col-md-5">
        
        @include('_partials.unidad', ["unidad"=>$detalle->unidadProductiva])

        <div class="card mb-6 border border-2 border-primary rounded">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="badge bg-label-primary rounded-pill">{{$detalle->estado->inscripcionEstadoNOMBRE ?? ' - '}}</span>
                    <div class="d-flex justify-content-center">
                        <sub class="h6 pricing-duration mt-auto mb-3 fw-normal">
                            @if($detalle->fecha_creacion)
                                {{ \Carbon\Carbon::parse($detalle->fecha_creacion)->setTimezone('America/Bogota')->format('Y-m-d H:i:s') }}
                            @else
                                -
                            @endif
                        </sub>
                    </div>
                </div>
                <ul class="list-unstyled g-2 my-6">
                    <li class="mb-2 d-flex align-items-center">
                        <i class="icon-base ri ri-circle-fill icon-10px text-body me-2"></i>                        
                        <span> 
                            <b>Programa: </b>
                            <a href="/programas/{{$detalle->convocatoria->programa_id ?? '#'}}"> {{$detalle->convocatoria->programa->nombre ?? ' - '}} </a>                            
                        </span>
                    </li>
                    <li class="mb-2 d-flex align-items-center">
                        <i class="icon-base ri ri-circle-fill icon-10px text-body me-2"></i>
                        <span>
                            <b>Convocatoria: </b>
                            <a href="/convocatorias/{{$detalle->convocatoria->convocatoria_id ?? '#'}}"> {{$detalle->convocatoria->nombre_convocatoria ?? ' - '}} </a>
                        </span>
                    </li>
                    <li class="mb-2 d-flex align-items-center">
                        <i class="icon-base ri ri-circle-fill icon-10px text-body me-2"></i>
                        <span>¿Activar preguntas nuevamente? {{$detalle->activarPreguntas ? 'SI' : 'NO'}}  </span> 
                    </li>
                </ul>
                <p>{{$detalle->comentarios ?? ' - '}}</p>
                <div class="d-grid w-100 mt-6">
                    <button class="btn btn-primary waves-effect waves-light" data-bs-target="#cambioEstadoModal" data-bs-toggle="modal">
                        Cambio de estado
                    </button>
                </div>
            </div>
        </div>

    </div>
    <div class="col-12 col-md-7">

        <div class="card mb-6 p-3">
             
            <div class="d-flex">
                <a class="btn btn-success px-2" href="exportRespuestas?id={{ $detalle->inscripcion_id }}" target="_blanck" >
                    <i class="icon-base ri ri-file-excel-2-line me-2"></i> Exportar
                </a>

                <h5 class="text-center mb-0 ms-2 pt-1">Listado de respuestas</h5>
            </div> 
            <table id="respuestasIncripcion" class="table" >
                <thead>
                    <th>Pregunta</th>
                    <th>Respuesta</th>
                    <th>Editar</th>
                </thead>
                <tbody>
                    @foreach ($detalle->respuestas as $item)
                        <tr>
                            <td>{{$item->requisito->requisito_titulo ?? ' - '}}</td>
                            <td>{{$item->value ?? ' - '}}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editarRequisito({{$item->convocatoriarespuesta_id}}, {{$item->requisito_id}}, '{{$item->value}}')" >
                                    <i class="icon-base ri ri-loop-left-line"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

        <div class="card mb-6">
            <h5 class="card-header">Historial de cambios de estado</h5>
            <div class="card-body pt-0">
                <ul class="timeline mb-0">
                    @foreach ($detalle->historial as $item)                
                    <li class="timeline-item timeline-item-transparent">
                        <span class="timeline-point timeline-point-primary"></span>
                        <div class="timeline-event">
                            <div class="timeline-header mb-3">
                                <h6 class="mb-0">{{$item->estado->inscripcionEstadoNOMBRE ?? ' - '}}</h6>
                                <small class="text-body-secondary">
                                    @if($item->fecha_creacion)
                                        {{ \Carbon\Carbon::parse($item->fecha_creacion)->setTimezone('America/Bogota')->format('Y-m-d H:i:s') }}
                                    @else
                                        -
                                    @endif
                                </small>
                            </div>
                            <p class="mb-2">{{$item->comentarios ?? ' - '}}</p>
                            @if ($item->archivo)                            
                                <div class="d-flex align-items-center mb-1">
                                    <a class="badge bg-lightest" href="{{$item->archivo}}" target="_blank" >
                                        <img src="{{ asset('assets/img/icons/misc/pdf.png') }}" alt="img" width="20" class="me-2" />
                                        <span class="h6 mb-0">Archivo</span>
                                    </a>
                                </div>
                            @endif

                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="cambioEstadoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-6">
      <div class="modal-body pt-md-0 px-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-6">
          <h4 class="mb-2">Cambio de estado</h4>
        </div>
        <form id="cambioEstadoForm" class="row g-5 d-flex align-items-center" 
            action="/inscripciones/{{$detalle->inscripcion_id}}" 
            enctype="multipart/form-data"
            method="POST" >
          
            <div class="col-sm-12 mb-3">
                <label class="form-label" for="inscripcionestado_id">Estado <span class="text-danger">*</span></label>
                <select id="inscripcionestado_id" name="inscripcionestado_id" class="form-select form-select-sm" required>
                    @if(empty($detalle->inscripcionestado_id))
                        <option value="">Seleccione una opción</option>
                    @endif
                    @foreach ($estados as $item)
                        <option value="{{$item->inscripcionestado_id}}" {{ ($detalle->inscripcionestado_id ?? null) == $item->inscripcionestado_id ? 'selected' : '' }} >{{$item->inscripcionEstadoNOMBRE}}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback">
                    Debe seleccionar un estado válido.
                </div>
            </div>
          
            <div class="col-sm-12 mb-3">
                <label class="form-label" for="comentarios">Comentarios </label>
                <textarea class="form-control" name="comentarios" id="comentarios" rows="4" placeholder="Ingrese los comentarios"></textarea>
            </div>

            <div class="col-sm-12 mb-3">
                <label class="form-label" for="activarPreguntas">¿Activar preguntas nuevamente?</label>
                <select class="form-select form-select-sm" name="activarPreguntas" id="activarPreguntas" required>
                    <option value="0" {{ !($detalle->activarPreguntas ?? false) ? 'selected' : '' }}>No</option>
                    <option value="1" {{ ($detalle->activarPreguntas ?? false) ? 'selected' : '' }}>Si</option>
                </select>
            </div>

            <div class="col-sm-12 mb-3">
                <label class="form-label" for="archivo">Archivo adjunto</label>
                <input class="form-control" type="file" name="archivo" id="archivo" accept=".pdf,.jpg,.png,.doc,.docx">
            </div>

            <input type="hidden" name="inscripciones[]" id="inscripciones" value="{{$detalle->inscripcion_id}}" >
            
            @csrf
            @method('PATCH')

            <div class="col-sm-12 text-center">
                <hr class="mx-md-n5 mx-n3" />

                <button class="btn btn-success mt-4" type="submit">
                    Guardar
                </button>
            </div>

        </form>
      </div>
      
    </div>
  </div>
</div>

<div class="modal fade" id="editarPreguntaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-6">
      <div class="modal-body pt-md-0 px-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-6">
          <h4 class="mb-2">Editar pregunta</h4>
        </div>
        <form id="editarPreguntaForm" class="row g-5 d-flex align-items-center" action="/inscripciones/updateRespuesta" method="POST" >
          
            <h5 id="textPregunta"></h5>
            
            <div class="my-3" id="campoPregunta"></div>

            <input type="hidden" name="respuestaId" id="respuestaId">
            @csrf

            <div class="col-sm-12 text-center">
                <hr class="mx-md-n5 mx-n3" />

                <button class="btn btn-success mt-4" type="submit">
                    Guardar
                </button>
            </div>

        </form>
      </div>
      
    </div>
  </div>
</div>

@endsection

@section('page-script')
    <script>
        window.TABLAS = [
            {
                id: 'respuestasIncripcion',
                setting: {
                    pagination: true,
                    search: true,
                    pageLength: 5,
                    lengthMenu: [5, 10, 20, 50, 100]
                }                
            }
        ];

        const cargando = document.querySelectorAll('.cargando')[0];

        document.addEventListener('DOMContentLoaded', function () {

            $('#cambioEstadoForm, #editarPreguntaForm').on('submit', function (e) {
                e.preventDefault();

                let form = $(this); 
                let formEl = this;
                
                // Validar que se haya seleccionado un estado válido
                if (formEl.id === 'cambioEstadoForm') {
                    let estadoSelect = $('#inscripcionestado_id');
                    let estadoValue = estadoSelect.val();
                    
                    // Remover clase de error previa
                    estadoSelect.removeClass('is-invalid');
                    
                    if (!estadoValue || estadoValue === '') {
                        estadoSelect.addClass('is-invalid');
                        Swal.fire({
                            title: 'Error de validación',
                            text: 'El campo estado es obligatorio. Debe seleccionar un estado válido.',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                        return false;
                    }
                }

                // Validar formulario HTML5
                if (!formEl.checkValidity()) {
                    formEl.reportValidity();
                    return false;
                }

                cargando.classList.remove('d-none');

                let method = form.attr('method'); 
                let actionUrl = form.attr('action');

                let formData = new FormData(formEl);

                $.ajax({
                    type: method,
                    url: actionUrl,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {

                        $(".modal").modal('hide');
                        cargando.classList.add('d-none');
                        
                        Swal.fire({ title: "Cambio de estado guardado exitosamente", icon: "success" })
                        .then((result) => {
                            cargando.classList.remove('d-none');
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        cargando.classList.add('d-none');
                        
                        // Mostrar errores de validación
                        let errorMessage = 'Ocurrió un error al guardar';
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            let errorText = '';
                            
                            // Recopilar todos los mensajes de error
                            for (let field in errors) {
                                if (errors.hasOwnProperty(field)) {
                                    errorText += errors[field].join('<br>') + '<br>';
                                }
                            }
                            
                            if (errorText) {
                                errorMessage = errorText;
                            }
                            
                            // Mostrar error específico del campo estado si existe
                            if (errors.inscripcionestado_id) {
                                $('#inscripcionestado_id').addClass('is-invalid');
                                $('#inscripcionestado_id').on('change', function() {
                                    $(this).removeClass('is-invalid');
                                });
                            }
                        }
                        
                        Swal.fire({
                            title: 'Error de validación',
                            html: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            });
        });

        window.editarRequisito = function(respuesta_id, requisito_id, value){
            
            cargando.classList.remove('d-none');
            $('#respuestaId').val(respuesta_id); 

            $.ajax({
                url: `/convocatoriasRequisitos/${requisito_id}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    
                    $('#textPregunta').text(response.requisito_titulo); 

                    let htmlCampo = '';

                    if (response.preguntatipo_id === 1) 
                    {
                        htmlCampo = '<div>';
                        response.opciones.forEach(function(op, index) {
                            
                            let checked = (String(value).trim() === String(op.opcion_variable_response).trim()) ? 'checked' : '';
                            htmlCampo += `
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="radio" 
                                        name="valorPregunta" 
                                        id="opcion_${index}" 
                                        value="${op.opcion_variable_response}" 
                                        ${checked}>
                                    <label class="form-check-label" for="opcion_${index}">
                                        ${op.opcion_variable_response}
                                    </label>
                                </div>
                            `;
                        });
                        htmlCampo += '</div>';
                    }
                    else if (response.preguntatipo_id === 2) 
                    {
                        htmlCampo = `<input type="number" class="form-control" 
                                        name="valorPregunta" id="valorPregunta" 
                                        value="${value ?? ''}" />`;
                    }

                    $('#campoPregunta').html(htmlCampo);
                    $('#editarPreguntaModal').modal('show');

                    cargando.classList.add('d-none');
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('Error al obtener la información del requisito.');
                    cargando.classList.add('d-none');
                }
            });
        }

        cargando.classList.add('d-none');

    </script>
    @vite(['resources/assets/js/admin-table.js'])
@endsection
