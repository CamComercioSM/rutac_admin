@extends('layouts.layoutMaster')

@section('content')
<div class="container card my-3 py-4 shadow-sm">

    <h2 class="text-center text-primary mb-4">
        <b> Pagina principal </b>
    </h2>

    <form id="form" action="/secciones" method="POST" novalidate>
        <div class="row">

            <div class="col-12 col-md-12 form-group mb-3">
                <label class="form-label" for="tag">Etiqueta</label>
                <input type="text" class="form-control" name="tag" id="tag" placeholder="Etiqueta" value="{{ $data->tag }}" required>
            </div>

            <div class="col-12 col-md-12 form-group mb-3">
                <label class="form-label" for="h1">Título principal (H1)</label>
                <input type="text" class="form-control" name="h1" id="h1" placeholder="Título principal (H1)" value="{{ $data->h1 }}" required>
            </div>

            <div class="col-12 col-md-12 form-group mb-3">
                <label class="form-label" for="video_url">Video URL</label>
                <input type="url" class="form-control" name="video_url" id="video_url" placeholder="Video URL" value="{{ $data->video_url }}" >
            </div>

            <div class="col-12"> <hr> </div>

            <div class="col-12 col-md-12 form-group mb-3">
                <label class="form-label" for="seo_title">Título SEO</label>
                <input type="text" class="form-control" name="seo_title" id="seo_title" placeholder="Título SEO" value="{{ $data->seo_title }}" >
            </div>
            <div class="col-12 col-md-12 form-group mb-3">
                <label class="form-label" for="seo_description">Descripción SEO</label>
                <textarea class="form-control" name="seo_description" id="seo_description" placeholder="Descripción SEO" >{{ $data->seo_description }}</textarea>
            </div>
            
            <div class="col-12"> 
                <hr> 
                <h5 class="text-primary fw-bold">Historias</h5> 
            </div>

            <div class="col-12 col-md-12 form-group mb-3">
                <label for="histories_title" class="form-label">Título</label>
                <input type="text" class="form-control" id="histories_title" name="histories_title" value="{{ $data->historia['histories_title']  ?? '' }}" placeholder="Ingrese el título">
            </div>

            <div class="col-12 col-md-12 form-group mb-3">
                <label for="histories_description" class="form-label">Descripción</label>
                <textarea class="form-control" id="histories_description" name="histories_description" rows="3" placeholder="Ingrese la descripción">{{ $data->historia['histories_description']  ?? '' }}</textarea>
            </div>

            <div class="col-12 col-md-12 form-group mb-3">
                <label for="discover_title" class="form-label">Título (Descubre)</label>
                <input type="text" class="form-control" id="discover_title" name="discover_title" value="{{ $data->historia['discover_title']  ?? '' }}" placeholder="Ingrese el título de descubre">
            </div>

            <div class="col-12 col-md-12 form-group mb-3">
                <label for="discover_bg_image" class="form-label">Imagen de fondo (Tamaño recomendado: 1100 x 355 píxeles)</label>
                <input type="file" class="form-control" id="discover_bg_image" name="discover_bg_image" accept="image/*">
            </div>

            <div class="col-12 col-md-6 form-group mb-3">
                <label for="discover_button_1_label" class="form-label">Etiqueta del botón 1</label>
                <input type="text" class="form-control" id="discover_button_1_label" value="{{ $data->historia['discover_button_1_label']  ?? '' }}" name="discover_button_1_label" placeholder="Texto del botón 1">
            </div>

            <div class="col-12 col-md-6 form-group mb-3">
                <label for="discover_button_1_url" class="form-label">URL del botón 1</label>
                <input type="url" class="form-control" id="discover_button_1_url" value="{{ $data->historia['discover_button_1_url']  ?? '' }}" name="discover_button_1_url" placeholder="https://...">
            </div>

            <div class="col-12 col-md-6 form-group mb-3">
                <label for="discover_button_2_label" class="form-label">Etiqueta del botón 2</label>
                <input type="text" class="form-control" id="discover_button_2_label" value="{{ $data->historia['discover_button_2_label']  ?? '' }}" name="discover_button_2_label" placeholder="Texto del botón 2">
            </div>

            <div class="col-12 col-md-6 form-group mb-3">
                <label for="discover_button_2_url" class="form-label">URL del botón 2</label>
                <input type="url" class="form-control" id="discover_button_2_url" value="{{ $data->historia['discover_button_2_url'] ?? '' }}" name="discover_button_2_url" placeholder="https://...">
            </div>


            <div class="col-12">
                <hr>
                <h5 class="text-primary fw-bold">Pié de página</h5>
            </div>

            <div class="col-12 col-md-6 form-group mb-3">
                <label for="footer_logo_rutac" class="form-label">Logo Ruta C (Tamaño recomendado: 400 x 88 píxeles)</label>
                <input type="file" class="form-control" id="footer_logo_rutac" name="footer_logo_rutac" accept="image/*">
            </div>

            <div class="col-12 col-md-6 form-group mb-3">
                <label for="footer_logo_ally" class="form-label">Logo Aliado (Tamaño recomendado: 115 x 110 píxeles)</label>
                <input type="file" class="form-control" id="footer_logo_ally" name="footer_logo_ally" accept="image/*">
            </div>

            <div class="col-12 col-md-6 form-group mb-3">
                <label for="footer_number_contact" class="form-label">Número de contacto</label>
                <input type="text" class="form-control" id="footer_number_contact" name="footer_number_contact" value="{{ $data->footer['footer_number_contact'] ?? '' }}" placeholder="+57 605 420 9909">
            </div>

            <div class="col-12 col-md-6 form-group mb-3">
                <label for="footer_ally_page" class="form-label">Página del aliado</label>
                <input type="url" class="form-control" id="footer_ally_page" name="footer_ally_page" value="{{ $data->footer['footer_ally_page'] ?? '' }}" placeholder="https://www.ccsm.org.co/">
            </div>

            <div class="col-12 col-md-6 form-group mb-3">
                <label for="footer_email_contact" class="form-label">Email de contacto</label>
                <input type="email" class="form-control" id="footer_email_contact" name="footer_email_contact" value="{{ $data->footer['footer_email_contact'] ?? '' }}" placeholder="info@rutadecrecimiento.com">
            </div>

            <div class="col-12 col-md-6 form-group mb-3">
                <label for="footer_address" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="footer_address" name="footer_address" value="{{ $data->footer['footer_address'] ?? '' }}" placeholder="Calle 24 #2-66. Edificio Cámara de Comercio.">
            </div>

            <div class="text-center my-4">
                <button type="submit" class="btn btn-success">
                    <i class="icon-base ri ri-send-plane-line me-2"></i> Guardar
                </button>
            </div>

        </div>
    </form>

    <div class="position-fixed top-0 end-0 w-100 d-flex justify-content-center" style="z-index: 1111;">        
        <div id="estadoToast" class="toast align-items-center text-bg-success border-0 m-5" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"> ✅ Cambio guardado exitosamente </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form');
    const cargando = document.querySelectorAll('.cargando')[0];

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        // Mostrar animación de carga
        cargando?.classList.remove('d-none');

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });

            const result = await response.json();

            cargando?.classList.add('d-none');

            let toastEl = document.getElementById('estadoToast');
            let toast = new bootstrap.Toast(toastEl, { delay: 2000 }); // 3s
            toast.show();

        } catch (error) {
            cargando?.classList.add('d-none');
            console.error('Error en la petición:', error);
        }
    });

    cargando.classList.add('d-none');
});
</script>
@endsection
