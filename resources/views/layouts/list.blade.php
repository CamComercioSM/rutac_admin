@extends('layouts.admin')

@section('content')
<div class="container card my-3 shadow-sm">

    <div id="Data" >
        @if (View::hasSection('form-filters'))
            <form class="border p-3 mt-3" id="filters" novalidate >
                <div class="row justify-content-center">

                    @yield('form-filters')

                    <div class="col-12 col-md-12 my-3 text-center">
                        <button class="btn btn-sm btn-danger mx-1" id="btnLimpiarFiltrar" type="button" >
                            <i class="ri-filter-off-line"></i> Limpiar
                        </button>
                        <button class="btn btn-sm btn-warning mx-1" id="btnFiltrar" type="button" >
                            <i class="ri-filter-line"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        @endif

        <div class="table-responsive p-3 h-100" >
            <div id="toolbar" class="d-flex">
                @if (View::hasSection('form-fiels'))
                    <button class="btn btn-success me-3" onclick="CrearRegistro()">
                        <i class="ri-add-line"></i> Crear
                    </button>
                @endif

                <a id="btnExport" class="btn btn-info" href="export" target="_blank" >
                    <i class="ri-download-cloud-2-line me-1"></i> Exportar
                </a>

            </div>

            <table id="tabla" class="table"></table>
        </div>
    </div>


    <div class="py-3 d-none" id="Modal" >

        <h2 class="text-center text-my-color mb-4">
           <span id="accionModal"></span> {{$tituloModal}}
        </h2>

        <form id="form" novalidate >
                                    
            <input type="hidden" id="id" name="id">
            
            @yield('form-fiels')

            <div class="text-center my-4">                    
                <button type="button" class="btn btn-secondary cancelar">
                    <i class="bi bi-sign-turn-left"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-send-check"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
  
  <link rel="stylesheet" href="/libs/bootstrap-table/bootstrap-table.min.css">

  <script src="/libs/axios.min.js"></script>
  <script src="/libs/jquery.min.js"></script>
  <script src="/libs/bootstrap-table/bootstrap-table.min.js"></script>
  <script src="/libs/bootstrap-table/bootstrap-table-es-ES.min.js"></script>

  <script>
        $.extend($.fn.bootstrapTable.locales['es-ES'], {
            formatShowingRows: function (from, to, total) { return `Visualizando ${from}–${to} de ${total}.`; },
        });

        $.extend($.fn.bootstrapTable.defaults, $.fn.bootstrapTable.locales['es-ES']);
  </script>
  @yield('script')
  <script src="/js/admin-base.js"></script>
@endsection