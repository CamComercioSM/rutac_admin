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
    
    function sendPasswordReset(id) {
        const btn = document.getElementById('btnPasswordReset');
        const originalText = btn.innerHTML;
        
        // Deshabilitar botón y mostrar loading
        btn.disabled = true;
        btn.innerHTML = '<i class="icon-base ri ri-loader-4-line icon-16px me-1_5"></i>Enviando...';
        
        // Enviar petición
        fetch(`/empresarios/${id}/send-password-reset`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message,
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'No se pudo enviar el correo de recuperación',
                    timer: 3000
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al enviar el correo de recuperación',
                timer: 3000
            });
        })
        .finally(() => {
            // Restaurar botón
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
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
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-warning" onclick="sendPasswordReset({{ $detalle->id }})" id="btnPasswordReset">
                    <i class="icon-base ri ri-mail-send-line icon-16px me-1_5"></i>Enviar recuperación de contraseña
                </button>
                <a href="javascript:void(0)" class="btn btn-primary"> <i class="icon-base ri ri-user-follow-line icon-16px me-1_5"></i>Conectado </a>
            </div>
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