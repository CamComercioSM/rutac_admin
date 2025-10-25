@extends('layouts.layoutMaster')

<!-- Vendor Styles -->
@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss'])
@endsection

<!-- Page Styles -->
@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-profile.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection

<!-- Page Scripts -->
@section('page-script')
@vite(['resources/assets/js/pages-profile-user.js'])
<script>
    loading.classList.add('d-none');
</script>
@endsection

@section('content')

<!-- Header -->
<div class="row">
  <div class="col-12">
    <div class="card mb-6">
      <div class="user-profile-header-banner">
        <img src="{{ asset('assets/img/pages/profile-banner.png') }}" alt="Banner image" class="rounded-top" />
      </div>
      <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-4">
        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
          <img src="{{ asset('assets/img/avatars/1.png') }}" alt="user image" class="d-block h-auto ms-0 ms-sm-5 rounded user-profile-img" />
        </div>
        <div class="flex-grow-1 mt-3 mt-lg-5">
          <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
            <div class="user-profile-info">
              <h4 class="mb-2 mt-lg-6">{{ $detalle->name }} {{$detalle->lastname}}</h4>
              <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4">
                <li class="list-inline-item">
                    <i class="icon-base ri ri-palette-line me-2 icon-24px"></i>
                    <span class="fw-medium">{{ $detalle->email }}</span>
                </li>
                <li class="list-inline-item">
                    <i class="icon-base ri ri-map-pin-line me-2 icon-24px"></i>
                    <span class="fw-medium">{{ $detalle->identification }}</span>
                </li>
              </ul>
            </div>
            <a href="javascript:void(0)" class="btn btn-primary"> <i class="icon-base ri ri-user-follow-line icon-16px me-1_5"></i>Conectado </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Header -->

<div class="row justify-content-center">
    @foreach($detalle->unidades as $item)
        <div class="col-12 col-md-5">
            @include('_partials.unidad', ["unidad"=>$item])
        </div>
    @endforeach
</div>


@endsection