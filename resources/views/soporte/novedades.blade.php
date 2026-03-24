{{-- resources/views/soporte/novedades.blade.php --}}
@if (isset($novedadesSoporte) && $novedadesSoporte->count() > 0)
    <div class="row" id="contenedor-novedades">
        @foreach ($novedadesSoporte as $novedad)
            @php
                // Mapeo basado en tus ejemplos de Materio
                $iconoClase = match ($novedad->estilo_visual) {
                    'success' => 'ri-checkbox-circle-line',
                    'info' => 'ri-information-line',
                    'warning' => 'ri-alert-line',
                    'danger' => 'ri-error-warning-line',
                    default => 'ri-user-follow-line',
                };
            @endphp

            <div class="col-12 mb-4 novedad-soporte d-none" id="novedad-{{ $novedad->soporte_novedad_id }}"
                data-id="{{ $novedad->soporte_novedad_id }}">

                {{-- Estructura exacta de la plantilla Materio --}}
                <div class="alert alert-{{ $novedad->estilo_visual }} alert-dismissible shadow border-0" role="alert">
                    <h4 class="alert-heading d-flex align-items-center">
                        <span
                            class="alert-icon me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle shadow-sm"
                            style="width: 42px; height: 42px;">
                            <i class="icon-base ri {{ $iconoClase }} icon-md"></i>
                        </span>
                        {{ $novedad->titulo }}
                    </h4>

                    {{-- Opcional: una línea separadora si la descripción es larga --}}
                    <hr class="my-2">

                    <p class="mb-0 text-dark">
                        {{ $novedad->descripcion }}
                    </p>

                    {{-- Botón de cierre nativo con nuestra lógica JS --}}
                    <button type="button" class="btn-close"
                        onclick="ocultarNovedadLocal({{ $novedad->soporte_novedad_id }})" aria-label="Close">
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        (function() {
            // Verificación de LocalStorage al cargar
            document.querySelectorAll('.novedad-soporte').forEach(el => {
                const id = el.getAttribute('data-id');
                if (!localStorage.getItem('soporte_materio_hide_' + id)) {
                    el.classList.remove('d-none');
                }
            });
        })();

        function ocultarNovedadLocal(id) {
            // Persistencia en el navegador
            localStorage.setItem('soporte_materio_hide_' + id, 'true');

            const el = document.getElementById('novedad-' + id);
            if (el) {
                // Efecto de salida suave
                el.style.transition = 'all 0.3s ease';
                el.style.opacity = '0';
                el.style.transform = 'translateY(-10px)';
                setTimeout(() => el.remove(), 300);
            }
        }
    </script>
@endif
