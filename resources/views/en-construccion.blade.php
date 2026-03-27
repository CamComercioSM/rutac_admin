@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Funcionalidad en Construcción')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-misc.scss'])
@endsection

@section('content')
<!-- Funcionalidad en construcción -->
<div class="misc-wrapper">
  <h4 class="mb-2 mx-2">🚧 Funcionalidad en construcción</h4>
  
  <p class="mb-2 mx-2 text-center">
    Estamos trabajando en esta sección para mejorar tu experiencia en la plataforma.
  </p>

  <p class="mb-10 mx-2 text-center text-muted">
    Esta funcionalidad se encuentra en proceso de desarrollo, ajuste o mejora.  
    Muy pronto estará disponible con nuevas capacidades.
  </p>

  <div class="d-flex justify-content-center mt-5">
    <img src="{{ asset('https://cdnsicam.net/img/logo-2026-activa-tu-crecimiento.png') }}" style="max-width: 200px" class="img-fluid misc-object d-none d-lg-inline-block authentication-image-object-left  renovacion-promo" />
    <img src="{{ asset('https://cdnsicam.net/img/rutac/rutac-logo-con-ccsm.png') }}" style="max-width: 200px" class="img-fluid misc-object-right d-none d-lg-inline-block" />
    
    <img src="{{ asset('assets/img/illustrations/misc-mask-' . $configData['theme'] . '.png') }}"
         class="scaleX-n1-rtl misc-bg d-none d-lg-inline-block"
         height="172"
         data-app-light-img="illustrations/misc-mask-light.png"
         data-app-dark-img="illustrations/misc-mask-dark.png" />

    <div class="d-flex flex-column align-items-center">
      <img src="{{ asset('https://cdnsicam.net/ia/marIAc/trabajando.png') }}"
           class="img-fluid z-1" style="height: 50vh;" />

      <div>
        <a href="{{ url('/') }}" class="btn btn-primary text-center my-12">
          Volver al inicio
        </a>
      </div>
    </div>
  </div>
</div>
<!-- /Funcionalidad en construcción -->
@endsection