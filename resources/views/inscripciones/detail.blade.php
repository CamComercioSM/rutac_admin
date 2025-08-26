@extends('layouts.admin', ['titulo'=> 'Inscripción'])

@section('content')

<div class="row">
    <div class="col-12 col-md-5">
        
        @include('_partials.unidad', ["unidad"=>$detalle->unidadProductiva])

        <div class="card mb-6 border border-2 border-primary rounded">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="badge bg-label-primary rounded-pill">{{$detalle->estado->inscripcionEstadoNOMBRE ?? ' - '}}</span>
                    <div class="d-flex justify-content-center">
                        <sub class="h6 pricing-duration mt-auto mb-3 fw-normal">{{$detalle->fecha_creacion ?? ' - '}}</sub>
                    </div>
                </div>
                <ul class="list-unstyled g-2 my-6">
                    <li class="mb-2 d-flex align-items-center">
                        <i class="icon-base ri ri-circle-fill icon-10px text-body me-2"></i>                        
                        <span> 
                            <b>Programa: </b>
                            <a href="/programas/{{$detalle->convocatoria->programa_id}}"> {{$detalle->convocatoria->programa->nombre ?? ' - '}} </a>                            
                        </span>
                    </li>
                    <li class="mb-2 d-flex align-items-center">
                        <i class="icon-base ri ri-circle-fill icon-10px text-body me-2"></i>
                        <span>
                            <b>Convocatoria: </b>
                            <a href="/convocatorias/{{$detalle->convocatoria->convocatoria_id}}"> {{$detalle->convocatoria->nombre_convocatoria ?? ' - '}} </a>
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
             
            <div class="d-flex" id="toolbarRespuestas">
                <button class="btn btn-success exportar px-2" type="button" data-tabla="respuestasIncripcion">
                    <i class="ri-file-excel-2-line"></i>
                </button>

                <h5 class="text-center mb-0 ms-2 pt-1">Respuestas</h5>
            </div> 
            <table id="respuestasIncripcion" class="table table-striped" >
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
                                    <i class="ri-loop-left-line"></i>
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
                                <small class="text-body-secondary">{{$item->fecha_creacion ?? ' - '}}</small>
                            </div>
                            <p class="mb-2">{{$item->comentarios ?? ' - '}}</p>
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
        <form id="cambioEstadoForm" class="row g-5 d-flex align-items-center" action="/inscripciones/{{$detalle->inscripcion_id}}" method="PATCH" >
          
            <div class="col-sm-12 mb-3">
                <label class="form-label" for="inscripcionestado_id">Estado</label>
                <select id="inscripcionestado_id" name="inscripcionestado_id" class="form-select form-select-sm">
                    <option value="" disabled selected>Seleccione una opción</option>
                    @foreach ($estados as $item)
                        <option value="{{$item->inscripcionestado_id}}" >{{$item->inscripcionEstadoNOMBRE}}</option>
                    @endforeach
                </select>
            </div>
          
            <div class="col-sm-12 mb-3">
                <label class="form-label" for="comentarios">Comentarios </label>
                <textarea class="form-control" name="comentarios" id="comentarios" rows="4" placeholder="Ingrese los comentarios"></textarea>
            </div>

            <div class="col-sm-12 mb-3">
                <label class="form-label" for="activarPreguntas">¿Activar preguntas nuevamente?</label>
                <select class="form-select form-select-sm" name="activarPreguntas" id="activarPreguntas">
                    <option value="" disabled selected>Seleccione una opción</option>
                    <option value="0">No</option>
                    <option value="1">Si</option>
                </select>
            </div>

            <input type="hidden" name="inscripcion_id" id="inscripcion_id" value="{{$detalle->inscripcion_id}}" >
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

<div class="modal fade" id="editarPreguntaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-6">
      <div class="modal-body pt-md-0 px-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-6">
          <h4 class="mb-2">Editar pregunta</h4>
        </div>
        <form id="editarPreguntaForm" class="row g-5 d-flex align-items-center" action="/inscripciones" method="POST" >
          
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

<div class="position-fixed top-0 end-0 p-5 w-100 d-flex justify-content-center" style="z-index: 1055;">
    <div id="estadoToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                ✅ Cambio guardado exitosamente
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

@endsection

@section('page-script')
    <script>
        const TABLAS = [
            {
                id: 'respuestasIncripcion',
                setting: {
                    toolbar: '#toolbarRespuestas',
                    locale: 'es-ES',
                    pagination: true,
                    search: true,
                    pageSize: 5,
                    pageList: [5, 10, 20, 50, 100]
                }                
            }
        ];

        const cargando = document.querySelectorAll('.cargando')[0];

        document.addEventListener('DOMContentLoaded', function () {

            $('#cambioEstadoForm, #editarPreguntaForm').on('submit', function (e) {
                e.preventDefault();

                cargando.classList.remove('d-none');

                let form = $(this);
                let method = form.attr('method');
                let actionUrl = form.attr('action');

                $.ajax({
                    type: method,
                    url: actionUrl,
                    data: form.serialize(),
                    success: function (response) {

                        $(".modal").modal('hide');
                        cargando.classList.add('d-none');
                        
                        let toastEl = document.getElementById('estadoToast');
                        let toast = new bootstrap.Toast(toastEl, { delay: 2000 }); // 3s
                        toast.show();

                        // Recargar la página después de que el toast se oculte
                        toastEl.addEventListener('hidden.bs.toast', () => {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        alert('Ocurrió un error al guardar');
                       cargando.classList.add('d-none');
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
    @vite([ 'resources/js/admin-table.js' ])
@endsection
