@extends('layouts/layoutMaster')

@section('title', 'User View - Pages')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('content')
<div class="row">
  <!-- User Sidebar -->
  <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
    <!-- User Card -->
    <div class="card mb-6">
      <div class="card-body pt-12">
        <div class="user-avatar-section">
          <div class=" d-flex align-items-center flex-column">
            <img class="img-fluid rounded mb-4" src="{{ asset('assets/img/avatars/10.png') }}" height="120" width="120" alt="User avatar" />
            <div class="user-info text-center">
              <h5>{{ $user->name }} {{ $user->lastname }}</h5>
              <span class="badge bg-label-danger rounded-pill">
                {{ $user->role->name ?? '-' }}
              </span>
            </div>
          </div>
        </div>
        
        <h5 class="pb-4 border-bottom mb-4">Details</h5>
        <div class="info-container">
          <ul class="list-unstyled mb-6">
            <li class="mb-2">
              <span class="h6">Nombres:</span>
              <span>{{ $user->name }}</span>
            </li>
            <li class="mb-2">
              <span class="h6">Apellidos:</span>
              <span>{{ $user->lastname }}</span>
            </li>
            <li class="mb-2">
              <span class="h6">N° documento:</span>
              <span>{{ $user->identification }}</span>
            </li>
            <li class="mb-2">
              <span class="h6">Email:</span>
              <span>{{ $user->email }}</span>
            </li>
            <li class="mb-2">
              <span class="h6">Estado:</span>
              <span class="badge bg-label-success rounded-pill"> Activo </span>
            </li>
            <li class="mb-2">
              <span class="h6">Rol:</span>
              <span>{{ $user->role->name ?? '-' }}</span>
            </li>
            <li class="mb-2">
              <span class="h6">Cargo:</span>
              <span>{{ $user->position }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!-- /User Card -->
    
  </div>
  <!--/ User Sidebar -->

  <!-- User Content -->
  <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">

    <!-- Change Password -->
    <div class="card mb-6">
      <h5 class="card-header">Cambiar contraseña</h5>
      <div class="card-body">
        <form id="formChangePassword" method="POST" action="/editarPassword" >
          <div class="alert alert-warning alert-dismissible" role="alert">
            <h5 class="alert-heading mb-1">Asegúrese de que se cumplan estos requisitos.</h5>
            <span>Mínimo 8 caracteres, mayúsculas y símbolos</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <div class="row gx-5">
            <div class="mb-4 col-12 col-sm-6 form-password-toggle form-control-validation">
              <div class="input-group input-group-merge">
                <div class="form-floating form-floating-outline">
                  <input class="form-control" type="password" id="newPassword" name="newPassword" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                  <label for="newPassword">Nueva contraseña</label>
                </div>
                <span class="input-group-text cursor-pointer"><i class="icon-base ri ri-eye-off-line icon-20px"></i></span>
              </div>
            </div>
            <div class="mb-4 col-12 col-sm-6 form-password-toggle form-control-validation">
              <div class="input-group input-group-merge">
                <div class="form-floating form-floating-outline">
                  <input class="form-control" type="password" name="confirmPassword" id="confirmPassword" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                  <label for="confirmPassword">Confirmar nueva contraseña</label>
                </div>
                <span class="input-group-text cursor-pointer"><i class="icon-base ri ri-eye-off-line icon-20px"></i></span>
              </div>
            </div>
            <div>
              @csrf
              <button type="submit" class="btn btn-primary me-2">Cambiar contraseña</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <!--/ Change Password -->

  </div>
  <!--/ User Content -->
</div>

@endsection

@section('page-script')
    <script>
       
        const cargando = document.querySelectorAll('.cargando')[0];

        document.addEventListener('DOMContentLoaded', function () {

            $('#formChangePassword').on('submit', function (e) {
                e.preventDefault();

                cargando.classList.remove('d-none');

                let form = $(this); 
                let formEl = this; 

                let formData = new FormData(formEl);

                $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {

                      cargando.classList.add('d-none');

                      if (response.success) {
                          Swal.fire({ title: "Cambio guardado exitosamente", icon: "success" });
                          formEl.reset();
                      }

                    },
                    error: function (xhr) {
                        cargando.classList.add('d-none');

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let mensajes = Object.values(errors).map(e => e.join(' ')).join(' ');
                            Swal.fire('Error', mensajes, 'error');
                        } else {
                            Swal.fire('Error', 'Ocurrió un error al guardar', 'error');
                        }
                    }
                });
            });
        });

        cargando.classList.add('d-none');

    </script>
    @vite(['resources/assets/js/admin-table.js'])
@endsection