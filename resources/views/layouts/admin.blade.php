<!doctype html>
<html lang="es" class="h-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ $titulo ?? env('APP_NAME') }}</title>
    <meta name="description" content="Administrador de {{ $titulo ?? env('APP_NAME') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS -->
    <link rel="stylesheet" href="/libs/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="/css/admin.css">
    
    <!-- 🚀 Optimización de conexión anticipada para jsDelivr -->
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

    <!-- 🎯 Carga diferida de Bootstrap Icons con preload -->
    <link rel="preload" as="style" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.min.css" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.min.css"></noscript>

  </head>

  <body class="bg-light d-flex flex-column h-100">
    <!-- Loader -->
     <div class="cargando">
      <div class="w-100 h-100 d-flex justify-content-center align-items-center">
        <div class="spinner-border text-my-color" style="width: 3rem; height: 3rem;" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar fixed-top navbar-light bg-white shadow">
        <div class="container">
          <button class="navbar-toggler p-1" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <a class="navbar-brand d-flex align-items-center ml-3" href="#">
            {{-- <img src="" width="45" class="d-none d-sm-block mx-1" alt="Logo"> --}}
            <h5 class="titulo-pagina m-0">{{ $titulo ?? '' }}</h5>
          </a>

          <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-secondary ml-auto d-none d-sm-block">
            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
          </a>
          <a class="btn btn-sm ml-auto d-block d-sm-none" ></a>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container-fluid flex-shrink-0 mt-5" style="min-height: calc(100% - 90px);">
      <div class="row h-100">

        <!-- Sidebar -->
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 bg-white shadow collapse">
          <div class="position-sticky pt-4" style="top:75px">
            <div class="text-center text-my-color mb-4">
              <i class="bi bi-person-circle" style="font-size: 4rem;"></i><br>
              <strong>Usuario</strong>
              <a href="/cerrar_sesion.php" class="btn btn-sm btn-outline-danger d-block d-sm-none mt-2">
                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
              </a>
            </div>

            <ul class="nav flex-column">

              <li class="nav-item">
                  <a class="nav-link px-2 border-top text-my-color d-flex align-items-center menu-hover" href="/dashboard">
                      <i class="bi bi-speedometer2 text-primary mr-2"></i> Dashboard
                  </a>
              </li>
              
              @foreach(session('user_menu', []) as $item)
                  <li class="nav-item">
                      <a class="nav-link px-2 border-top text-my-color d-flex align-items-center menu-hover" href="{{ $item->url }}">
                          <i class="bi bi-{{ $item->icon }} text-primary mr-2"></i> {{ $item->label }}
                      </a>
                  </li>
              @endforeach

            </ul>
            
          </div>
        </nav>

        <!-- Contenido -->
        <main class="col-md col-lg px-md-4 my-4">
          
          @yield('content')
            
        </main>
      </div>
    </div>

    <!-- Footer -->
    <footer class="footer bg-white w-100 border-top mt-auto py-2 text-center z-1">
      <div class="container">
        <b class="text-muted">{{ config('app.name') }} © {{ now()->year }}</b>
      </div>
    </footer>

    <script>
      function ajustarSidebar() {
        var sidebar = document.getElementById('sidebarMenu');
        if (window.innerWidth < 768) {
          sidebar.classList.remove('show');
        } else {
          sidebar.classList.add('show');
        }
      }

      ajustarSidebar();
      window.addEventListener('resize', ajustarSidebar);
    </script>

    <!-- JS -->
    <script src="/libs/jquery.min.js"></script>
    <script src="/libs/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="/libs/axios.min.js"></script>
    <script src="/libs/sweetalert2@11.js"></script>

    <script>
      $(document).on('click', 'a[href]', function (e) {
        const href = $(this).attr('href');
        const target = $(this).attr('target');

        if (href && target != '_blank' &&
            !href.startsWith('#') && 
            !href.startsWith('data:') && 
            !href.startsWith('javascript:')) {
          $('.cargando').removeClass('d-none');
        }
      });      
    </script>

    @yield('scripts')
    @yield('scripts2')

    <script>
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
          navigator.serviceWorker.register('/service-worker.js')
            .then(reg => console.log('✅ Service Worker registrado', reg.scope))
            .catch(err => console.error('❌ Error en el Service Worker', err));
        });
      }
    </script>

  </body>
</html>
