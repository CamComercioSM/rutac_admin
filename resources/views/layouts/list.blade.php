@extends('layouts.admin')

@section('content')
<div class="container card my-3 shadow-sm">

    @if (View::hasSection('form-filters'))
        <form class="row justify-content-center bg-light pt-3 mb-4" id="filters" novalidate >
            @yield('form-filters')

            <div class="col-12 col-md-12 my-3 text-center">
                <button class="btn btn-sm btn-danger mx-1" id="btnLimpiarFiltrar" type="button" >
                    <i class="bi bi-x-lg"></i> Limpiar
                </button>
                <button class="btn btn-sm btn-warning mx-1" id="btnFiltrar" type="button" >
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
            </div>

        </form>
    @endif

    <div class="table-responsive p-3 h-100" id="Data" >
        <div id="toolbar" class="d-flex">
            @if (View::hasSection('form-fiels'))
                <button class="btn btn-success mr-3" onclick="CrearRegistro()">
                    <i class="bi bi-plus-lg"></i> Crear
                </button>
            @endif

            <a id="btnExport" class="btn btn-info" href="export" target="_blank" >
                <i class="bi bi-cloud-download"></i> Exportar
            </a>

        </div>

        <table id="tabla" class="table table-sm table-striped custom-header-style"></table>
    </div>

    <div class="d-none" id="Modal" >

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

@section('scripts')
  <link rel="stylesheet" href="/libs/bootstrap-table/bootstrap-table.min.css">
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