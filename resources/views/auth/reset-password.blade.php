@extends('layouts/blankLayout')

@section('title', 'Restablecer contraseña - Ruta C')

@section('page-style')
@vite([
  'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection

@section('content')
<div class="position-relative">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-6 mx-4">

      <!-- Reset Password -->
      <div class="card p-7">
        <!-- Logo -->
        <div class="app-brand justify-content-center mt-5">
          <a href="{{url('/')}}" class="app-brand-link gap-3">
            <span class="app-brand-logo demo">@include('_partials.macros',["height"=>20,"withbg"=>'fill: #fff;'])</span>
            <span class="app-brand-text demo text-heading fw-semibold">Ruta C</span>
          </a>
        </div>
        <!-- /Logo -->

        <div class="card-body mt-1">
          <h4 class="mb-1 text-center">Restablecer contraseña 🔐</h4>
          <p class="mb-5 text-center">Ingresa tu nueva contraseña</p>

          <form id="resetPasswordForm" class="mb-5" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            
            <div class="form-floating form-floating-outline mb-5">
              <input type="email" class="form-control" id="email" name="email" value="{{ $email }}" readonly>
              <label for="email">Correo electrónico</label>
            </div>
            
            <div class="mb-5">
              <div class="form-password-toggle">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline">
                    <input type="password" id="password" class="form-control" name="password" placeholder="Nueva contraseña" aria-describedby="password" />
                    <label for="password">Nueva contraseña</label>
                  </div>
                  <span class="input-group-text cursor-pointer"><i class="ri-eye-off-line ri-20px"></i></span>
                </div>
              </div>
            </div>
            
            <div class="mb-5">
              <div class="form-password-toggle">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline">
                    <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" placeholder="Confirmar contraseña" aria-describedby="password_confirmation" />
                    <label for="password_confirmation">Confirmar contraseña</label>
                  </div>
                  <span class="input-group-text cursor-pointer"><i class="ri-eye-off-line ri-20px"></i></span>
                </div>
              </div>
            </div>
            
            <div class="mb-3">
              <button class="btn btn-primary d-grid w-100" type="submit">Restablecer contraseña</button>
            </div>
            
            <div class="text-center">
              <a href="{{ route('login') }}" class="btn btn-link">
                <i class="ri-arrow-left-line me-1"></i>
                Volver al login
              </a>
            </div>
          </form>

        </div>
      </div>
      <!-- /Reset Password -->
      
      <img src="{{asset('assets/img/illustrations/tree-3.png')}}" alt="auth-tree" class="authentication-image-object-left d-none d-lg-block">
      <img src="{{asset('assets/img/illustrations/auth-basic-mask-light.png')}}" class="authentication-image d-none d-lg-block" height="172" alt="triangle-bg">
      <img src="{{asset('assets/img/illustrations/tree.png')}}" alt="auth-tree" class="authentication-image-object-right d-none d-lg-block">
    </div>
  </div>
</div>
@endsection

@section('page-script')
<!-- SweetAlert2 desde CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('resetPasswordForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
        
        fetch('{{ route("password.update") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Contraseña Actualizada!',
                    text: 'Tu contraseña ha sido restablecida correctamente. Serás redirigido al login para iniciar sesión con tu nueva contraseña.',
                    confirmButtonText: 'Ir al Login',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    timer: 5000,
                    timerProgressBar: true
                }).then((result) => {
                    // Redirigir al login
                    window.location.href = '{{ route("login") }}';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al Restablecer',
                    text: data.message || 'No se pudo restablecer la contraseña. Verifica que el enlace no haya expirado.',
                    confirmButtonText: 'Intentar de nuevo'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo conectar al servidor. Verifica tu conexión e inténtalo de nuevo.',
                confirmButtonText: 'Intentar de nuevo',
                footer: 'Error: ' + error.message
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
});
</script>
@endsection
