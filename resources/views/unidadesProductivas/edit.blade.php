@extends('layouts.layoutMaster')


@section('content')
<div class="container card my-3 shadow-sm">

    <h2 class="text-center text-primary my-4">
        <b> {{ $accion }} unidad productiva </b>
    </h2>

    <form id="form" novalidate>
        
        <input type="hidden" id="id" name="id">

        <div class="row">

            <div class="col-12 col-md-12 form-group mb-3">
                <label class="form-label" for="business_name">Razon social </label>
                <input type="text" class="form-control" name="business_name" id="business_name" placeholder="Razon social" required>
            </div>

            <div class="col-12 col-md-12 form-group mb-3">
                <label class="form-label" for="description">Descripción </label>
                <textarea class="form-control" name="description" id="description" placeholder="Descripción"></textarea>
            </div>

            <div class="col-12 col-md-4 form-group mb-3">
                <label class="form-label" for="nit">NIT </label>
                <input type="text" class="form-control" name="nit" id="nit" placeholder="NIT" pattern="^\d{5,12}(-\d{1})?$" required>
            </div>

            <div class="col-12 col-md-4 form-group mb-3">
                <label class="form-label" for="registration_number">Número de matrícula </label>
                <input type="text" class="form-control" name="registration_number" id="registration_number" placeholder="Número de matrícula" pattern="^\d{4,10}$" required>
            </div>

            <div class="col-12 col-md-4 form-group mb-3">
                <label class="form-label" for="registration_date">Fecha de matrícula </label>
                <input type="date" class="form-control" name="registration_date" id="registration_date" required>
            </div>

            <div class="col-12 col-md-12 form-group mb-3">
                <label class="form-label" for="name_legal_representative">Nombre del representante legal </label>
                <input type="text" class="form-control" name="name_legal_representative" id="name_legal_representative" placeholder="Razon social" required>
            </div>

            <div class="col-12 col-md-4 form-group mb-3">
                <label class="form-label" for="unidadtipo_id">Tipo de Registro</label>
                <select class="form-select" name="unidadtipo_id" id="unidadtipo_id">
                    <option value="" selected >Seleccione una opción</option>
                    @foreach ($tipoUnidad as $item)
                        <option value="{{$item->unidadtipo_id}}" >{{$item->unidadtipo_nombre}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-4 form-group mb-3">
                <label class="form-label" for="tipopersona_id">Tipo de persona</label>
                <select class="form-select" name="tipopersona_id" id="tipopersona_id">
                    <option value="" selected >Seleccione una opción</option>
                    @foreach ($tipoPersona as $item)
                        <option value="{{$item->tipopersona_id}}" >{{$item->tipoPersonaNOMBRE}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-4 form-group mb-3">
                <label class="form-label" for="tipopersona_id">Sector</label>
                <select class="form-select" name="tipopersona_id" id="tipopersona_id">
                    <option value="" selected >Seleccione una opción</option>
                    @foreach ($sectores as $item)
                        <option value="{{$item->sector_id}}" >{{$item->sectorNOMBRE}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-4 form-group mb-3">
                <label class="form-label" for="tamano_id">Tamaño</label>
                <select class="form-select" name="tamano_id" id="tamano_id">
                    <option value="" selected >Seleccione una opción</option>
                    @foreach ($tamanos as $item)
                        <option value="{{$item->tamano_id}}" >{{$item->tamanoNOMBRE}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-4 form-group mb-3">
                <label class="form-label" for="affiliated">Afiliado</label>
                <select class="form-select" name="affiliated" id="affiliated">
                    <option value="" selected >Seleccione una opción</option>
                    <option value="1" >SI</option>
                    <option value="0" >NO</option>
                </select>
            </div>

            <div class="col-12 col-md-6 form-group mb-3">
                <label class="form-label" for="ciiuactividad_id">Actividad económica</label>
                <select class="form-select" name="ciiuactividad_id" id="ciiuactividad_id">
                    <option value="" selected >Seleccione una opción</option>
                    @foreach ($SectorSecciones as $seccion)
                        <optgroup label="{{ $seccion->ciiuSeccionTITULO }}" >
                             @foreach ($seccion->actividades as $item)
                                    <option value="{{$item->ciiuactividad_id}}">{{$item->ciiuActividadTITULO}}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-12"> <hr> </div>

            <div class="col-12 col-md-4 form-group mb-3">
                <label class="form-label" for="registration_email">Email </label>
                <input type="email" class="form-control" name="registration_email" id="registration_email" placeholder="Email" required>
            </div>
            <div class="col-12 col-md-4 form-group mb-3">
                <label class="form-label" for="telephone">Teléfono </label>
                <input type="text" class="form-control" name="telephone" id="telephone" placeholder="Teléfono" >
            </div>
            <div class="col-12 col-md-4 form-group mb-3">
                <label class="form-label" for="mobile">Celular </label>
                <input type="text" class="form-control" name="mobile" id="mobile" placeholder="Celular" >
            </div>

            <div class="col-12 col-md-12 form-group mb-3">
                <label class="form-label" for="address">Dirección </label>
                <input type="text" class="form-control" name="address" id="address" placeholder="Dirección" >
            </div>

            <div class="col-12 col-md-6 form-group mb-3">
                <label class="form-label" for="department_id">Departamento</label>
                <select class="form-select" name="department_id" id="department_id">
                    <option value="" selected >Seleccione una opción</option>
                    @foreach ($departamentos as $item)
                        <option value="{{$item->departamento_id}}" >{{$item->departamentoNOMBRE}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-6 form-group mb-3">
                <label class="form-label" for="municipality_id">Municipio </label>
                <select class="form-select" name="municipality_id" id="municipality_id">
                    <option value="" selected >Seleccione una opción</option>
                    @foreach ($municipios as $item)
                        <option value="{{$item->municipio_id}}" data-departamento="{{ $item->departamentoID }}" >{{$item->municipioNOMBREOFICIAL}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-12"> <hr> </div>

            <div class="col-12 col-md-8 form-group mb-3">
                <label class="form-label" for="contact_person">Persona de contacto </label>
                <input type="text" class="form-control" name="contact_person" id="contact_person" placeholder="Persona de contacto" required>
            </div>
            <div class="col-12 col-md-4 form-group mb-3">
                <label class="form-label" for="contact_sexo">Sexo de contacto</label>
                <select class="form-select" name="contact_sexo" id="contact_sexo" required>
                    <option value="">Seleccione una opción</option>
                    <option value="MASCULINO" >MASCULINO</option>
                    <option value="FEMENINO" >FEMENINO</option>
                </select>
            </div>
            <div class="col-12 col-md-4 form-group mb-3">
                <label class="form-label" for="contact_position">Cargo de contacto</label>
                <select class="form-select" name="contact_position" id="contact_position">
                    <option value="" selected >Seleccione una opción</option>
                    @foreach ($cargos as $item)
                        <option value="{{$item->vinculoCargoTITULO}}">{{$item->vinculoCargoTITULO}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4 form-group mb-3">
                <label class="form-label" for="contact_email">Email de contacto</label>
                <input type="email" class="form-control" name="contact_email" id="contact_email" placeholder="Email de contacto" required>
            </div>
            <div class="col-12 col-md-4 form-group mb-3">
                <label class="form-label" for="contact_phone">Celular de contacto</label>
                <input type="text" class="form-control" name="contact_phone" id="contact_phone" placeholder="Celular de contacto" required>
            </div>

            <div class="col-12 col-md-12"> <hr> </div>

            <div class="col-12 col-md-12 form-group mb-3">
                <label class="form-label" for="website">Sitio web </label>
                <input type="url" class="form-control" name="website" id="website" placeholder="Sitio web ">
            </div>

            <div class="col-12 col-md-4 form-group mb-3">
                <label class="form-label" for="social_instagram">Instagram </label>
                <input type="url" class="form-control" name="social_instagram" id="social_instagram" placeholder="Instagram" >
            </div>
            <div class="col-12 col-md-4 form-group mb-3">
                <label class="form-label" for="social_facebook">Facebook </label>
                <input type="url" class="form-control" name="social_facebook" id="social_facebook" placeholder="Facebook" >
            </div>
            <div class="col-12 col-md-4 form-group mb-3">
                <label class="form-label" for="social_linkedin">Linkedin </label>
                <input type="url" class="form-control" name="social_linkedin" id="social_linkedin" placeholder="Linkedin" >
            </div>

        </div>

        <input type="hidden" name="unidadproductiva_id" id="unidadproductiva_id" >
        <input type="hidden" name="tipo_registro_rutac" id="tipo_registro_rutac" >

        <div class="text-center my-4">
            <a type="button" class="btn btn-secondary" href="/unidadesProductivas/list" >
                <i class="icon-base ri ri-arrow-go-back-line me-2"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-success">
                <i class="icon-base ri ri-send-plane-line me-2"></i> Guardar
            </button>
        </div>
    </form>
</div>
@endsection

@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('page-script')
<script>
    window.URL_API = "{{ $api }}";
    window.SELECTS = ['ciiuactividad_id', 'department_id', 'municipality_id'];
    window.DATA = @json($elemento);
</script>
@vite(['resources/assets/js/admin-edit.js'])
@endsection
