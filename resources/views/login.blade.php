<!doctype html>
<html lang="es" class="h-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ $titulo ?? env('APP_NAME') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS -->
    <link rel="stylesheet" href="/libs/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/css/admin.css">
  </head>

  <body class="bg-light d-flex flex-column h-100">
 
    <div class="container mt-5">
        <div class="d-flex justify-content-center">
            <div class="card shadow" style="width: 100%; max-width: 400px;">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4">Iniciar Sesión</h4>

                    @if($mensaje)
                        <div class="alert alert-danger" role="alert">
                            {{ $mensaje }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group">
                            <label for="email">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" required autofocus>
                        </div>

                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>