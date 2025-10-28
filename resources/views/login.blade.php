@extends('layouts/blankLayout')

@section('title', 'Iniciar sesión - Ruta C')

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
    <style>
        .btn-google {
            background: #fff;
            border: 1px solid #dadce0;
            color: #3c4043;
            font-weight: 500;
            border-radius: 0.375rem;
            padding: 12px 16px;
            transition: all 0.2s ease-in-out;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-google:hover {
            background: #f8f9fa;
            border-color: #dadce0;
            color: #3c4043;
            text-decoration: none;
            box-shadow: 0 1px 3px 0 rgba(60, 64, 67, 0.3);
            transform: translateY(-1px);
        }

        .btn-google:focus {
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(66, 133, 244, 0.25);
        }

        .btn-google:active {
            background: #f1f3f4;
            border-color: #dadce0;
            transform: translateY(0);
        }

        /* Asegurar que SweetAlert esté por encima de todo */
        .swal2-container {
            z-index: 9999 !important;
        }

        /* Asegurar que el backdrop del modal se oculte correctamente */
        .modal-backdrop {
            transition: opacity 0.3s ease;
        }

        /* Estilos personalizados para SweetAlert2 */
        .swal2-popup-custom {
            border-radius: 15px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
        }

        .swal2-title-custom {
            font-size: 24px !important;
            font-weight: 600 !important;
            color: #2c3e50 !important;
        }

        .swal2-html-container {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        }

        .swal2-confirm {
            border-radius: 8px !important;
            font-weight: 500 !important;
            padding: 12px 24px !important;
        }
    </style>
@endsection

@section('content')
    <div class="position-relative">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-6 mx-4">

                <!-- Login -->
                <div class="card p-7">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mt-5">
                        <a href="{{ url('/') }}" class="app-brand-link gap-3">
                            <span class="app-brand-logo demo">@include('_partials.macros', ['height' => 80, 'withbg' => 'fill: #fff;'])</span>
                        </a>
                    </div>
                    <!-- /Logo -->

                    <div class="card-body mt-1">
                        <h4 class="mb-1 text-center">¡Bienvenido!👋🏻</h4>
                        <p class="mb-5 text-center">Por favor inicia sesión para comenzar</p>

                        @if (isset($mensaje) && $mensaje)
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="ri-error-warning-line me-2"></i>
                                {{ $mensaje }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('mensaje'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="ri-error-warning-line me-2"></i>
                                {{ session('mensaje') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form id="formAuthentication" class="mb-5" action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="form-floating form-floating-outline mb-5">
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Correo electrónico o usuario" autofocus>
                                <label for="email">Correo electrónico o usuario</label>
                            </div>
                            <div class="mb-5">
                                <div class="form-password-toggle">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input type="password" id="password" class="form-control" name="password"
                                                placeholder="Contraseña" aria-describedby="password" />
                                            <label for="password">Contraseña</label>
                                        </div>
                                        <span class="input-group-text cursor-pointer"><i
                                                class="icon-base ri ri-eye-off-line"></i></span>
                                        <span class="form-floating-focused"></span>
                                    </div>
                                </div>

                            </div>
                            <div class="mb-5 pb-2 d-flex justify-content-between pt-2 align-items-center">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" id="remember-me" name="remember">
                                    <label class="form-check-label" for="remember-me">
                                        Recuérdame
                                    </label>
                                </div>
                                <a href="#" class="float-end mb-1" data-bs-toggle="modal"
                                    data-bs-target="#modalForgotPassword">
                                    <span>¿Olvidaste tu contraseña?</span>
                                </a>

                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit">Iniciar sesión</button>
                            </div>
                        </form>

                        <div class="text-center mb-4">
                            <span>O</span>
                        </div>
                        <div class="mb-5">
                            <a href="{{ route('google.login') }}" class="btn btn-google d-grid w-100">
                                <div class="d-flex align-items-center justify-content-center">
                                    <svg width="20" height="20" viewBox="0 0 24 24" class="me-2">
                                        <path fill="#4285F4"
                                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                        <path fill="#34A853"
                                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                        <path fill="#FBBC05"
                                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                        <path fill="#EA4335"
                                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                                    </svg>
                                    <span>Continuar con Google</span>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>
                <!-- /Login -->
                <img src="{{ asset('assets/img/illustrations/tree-3.png') }}" alt="auth-tree"
                    class="authentication-image-object-left d-none d-lg-block">
                <img src="{{ asset('img/img-fondo-sierra-m-min.png') }}"
                    class="authentication-image d-none d-lg-block" height="100%" alt="triangle-bg" style="height: auto;" >
                <img src="{{ asset('assets/img/illustrations/tree.png') }}" alt="auth-tree"
                    class="authentication-image-object-right d-none d-lg-block">
            </div>
        </div>
    </div>


    <!-- Modal Olvidé mi contraseña -->
    <div class="modal fade" id="modalForgotPassword" tabindex="-1" aria-labelledby="modalForgotPasswordLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="forgotPasswordForm" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalForgotPasswordLabel">Recuperar contraseña</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <p>Ingresa tu correo electrónico para recibir el enlace de recuperación.</p>
                        <div class="mb-3">
                            <label for="forgotEmail" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="forgotEmail" name="email"
                                placeholder="ejemplo@correo.com" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Enviar enlace</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('page-script')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Test SweetAlert
            console.log('SweetAlert cargado:', typeof Swal !== 'undefined');

            // Manejo del formulario de recuperación de contraseña
            const forgotPasswordForm = document.getElementById('forgotPasswordForm');

            if (forgotPasswordForm) {
                forgotPasswordForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;

                    submitBtn.disabled = true;
                    submitBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';

                    fetch('{{ route('password.email') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            }
                        })
                        .then(async response => {
                            console.log('Response status:', response.status);
                            
                            // Intentar parsear el JSON incluso si hay error
                            const contentType = response.headers.get("content-type");
                            let data = {};
                            
                            if (contentType && contentType.includes("application/json")) {
                                try {
                                    data = await response.json();
                                } catch (e) {
                                    console.error('Error parsing JSON:', e);
                                }
                            }
                            
                            // Si la respuesta no es ok, lanzar el error con los datos
                            if (!response.ok) {
                                const error = new Error(`HTTP error! status: ${response.status}`);
                                error.data = data;
                                error.status = response.status;
                                throw error;
                            }
                            
                            return data;
                        })
                        .then(data => {
                            console.log('Response data:', data);

                            // Cerrar el modal ANTES de mostrar el SweetAlert
                            const modal = bootstrap.Modal.getInstance(document.getElementById(
                                'modalForgotPassword'));
                            if (modal) {
                                modal.hide();
                            }

                            // Limpiar el formulario
                            forgotPasswordForm.reset();

                            // Mostrar SweetAlert después de cerrar el modal
                            setTimeout(() => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '¡Correo de Recuperación Enviado! 📧',
                                        html: `
                                <div style="text-align: center;">
                                    <p style="font-size: 16px; margin-bottom: 15px;">
                                        <strong>Se ha enviado un enlace de recuperación a tu correo electrónico.</strong>
                                    </p>
                                    <p style="font-size: 14px; color: #666; margin-bottom: 10px;">
                                        📬 Revisa tu bandeja de entrada
                                    </p>
                                    <p style="font-size: 14px; color: #666; margin-bottom: 15px;">
                                        📁 También revisa la carpeta de spam o correo no deseado
                                    </p>
                                    <p style="font-size: 12px; color: #999;">
                                        El enlace expirará en 60 minutos
                                    </p>
                                </div>
                            `,
                                        confirmButtonText: '¡Entendido!',
                                        confirmButtonColor: '#28a745',
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,
                                        showCloseButton: true,
                                        customClass: {
                                            popup: 'swal2-popup-custom',
                                            title: 'swal2-title-custom'
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error al Enviar Correo ❌',
                                        html: `
                                <div style="text-align: center;">
                                    <p style="font-size: 16px; margin-bottom: 15px;">
                                        <strong>No se pudo enviar el correo de recuperación</strong>
                                    </p>
                                    <p style="font-size: 14px; color: #666;">
                                        ${data.message || 'Error desconocido'}
                                    </p>
                                </div>
                            `,
                                        confirmButtonText: 'Intentar de Nuevo',
                                        confirmButtonColor: '#dc3545',
                                        showCloseButton: true
                                    });
                                }
                            }, 300); // Pequeño delay para que el modal se cierre completamente
                        })
                        .catch(error => {
                            console.error('Error completo:', error);

                            // Cerrar el modal también en caso de error
                            const modal = bootstrap.Modal.getInstance(document.getElementById(
                                'modalForgotPassword'));
                            if (modal) {
                                modal.hide();
                            }

                            // Limpiar el formulario
                            forgotPasswordForm.reset();

                            // Determinar el mensaje a mostrar
                            let errorTitle = 'Error de Conexión 🌐';
                            let errorMessage = 'Verifica tu conexión a internet e inténtalo de nuevo';
                            let errorDetails = error.message;

                            // Si el error tiene data con mensaje del backend, usarlo
                            if (error.data && error.data.message) {
                                errorTitle = 'Error ❌';
                                errorMessage = error.data.message;
                                errorDetails = '';
                            }

                            // Mostrar SweetAlert de error después de cerrar el modal
                            setTimeout(() => {
                                Swal.fire({
                                    icon: 'error',
                                    title: errorTitle,
                                    html: `
                            <div style="text-align: center;">
                                <p style="font-size: 16px; margin-bottom: 15px;">
                                    <strong>${errorMessage}</strong>
                                </p>
                                ${errorDetails ? `<p style="font-size: 12px; color: #999;">
                                    Error: ${errorDetails}
                                </p>` : ''}
                            </div>
                        `,
                                    confirmButtonText: 'Intentar de Nuevo',
                                    confirmButtonColor: '#dc3545',
                                    showCloseButton: true
                                });
                            }, 300);
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        });
                });
            }

            const loading = document.querySelectorAll('.cargando')[0];
            loading.classList.add('d-none');
        });
    </script>
@endsection
