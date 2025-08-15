@extends('layouts.admin', [ 'titulo'=> 'Dashboard' ])

@section('content')

<div class="container mt-3">
  <div class="row justify-content-center">

    <a href="/users/list" class="col-md-3 mb-3 text-decoration-none">
        <div class="card h-100 shadow-sm card-hover">
            <div class="card-body text-center">
                <i class="bi bi-people display-4 text-primary"></i>
                <h5 class="mt-2 mb-0">Usuarios</h5>
            </div>
        </div>
    </a>

    {{-- <a href="/inscripciones/list" class="col-md-3 mb-3 text-decoration-none">
        <div class="card h-100 shadow-sm card-hover">
            <div class="card-body text-center">
                <i class="bi bi-person-lines-fill display-4 text-danger"></i>
                <h5 class="mt-2 mb-0">Inscripciones</h5>
            </div>
        </div>
    </a> --}}


  </div>
</div>



@endsection

@section('page-script')
<script>
    document.querySelectorAll('.cargando').forEach(function(element) {
        element.classList.add('d-none');
    });
</script>
@endsection