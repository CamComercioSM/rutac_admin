@extends('layouts.layoutMaster')

@section('title', 'DataTables - Advanced Tables')

<!-- Vendor Styles -->
@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/select2/select2.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/select2/select2.js'])
@endsection

<!-- Page Scripts -->
@section('page-script')
@yield('script')
@vite(['resources/assets/js/admin-list-table.js'])
@endsection

@section('content')
<div class="container card my-3 shadow-sm">

    @yield('info-header')

    <div id="Data">
        @hasSection('form-filters')
            <form class="border p-3 mt-3" id="filters" novalidate>
                <div class="row justify-content-center">

                    @yield('form-filters')

                    <div class="col-12 col-md-12 my-3 text-center">
                        <button class="btn btn-sm btn-danger mx-1" id="btnLimpiarFiltrar" type="button">
                            <i class="ri-filter-off-line"></i> Limpiar
                        </button>
                        <button class="btn btn-sm btn-warning mx-1" id="btnFiltrar" type="button">
                            <i class="ri-filter-line"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        @endif

        <div class="table-responsive p-3 h-100">
            <div id="toolbar" class="d-flex">
                @hasSection('form-fields')
                    <button class="btn btn-info me-3" id="btnCrear">
                        <i class="icon-base ri ri-add-fill me-2"></i> Crear
                    </button>
                @endif

                @if (!isset($exportar) || $exportar === true)
                    <a id="btnExport" class="btn btn-success" href="export" target="_blank">
                        <i class="icon-base ri ri-file-excel-2-line  me-2"></i> Exportar
                    </a>
                @endif
            </div>

            <table id="tabla" class="table"></table>
        </div>
    </div>

    <div class="py-3 d-none" id="Modal">

        <h2 class="text-center text-primary mb-4">
            <b> <span id="accionModal"></span> {{$tituloModal}} </b>
        </h2>

        <form id="form" novalidate>
            <input type="hidden" id="id" name="id">

            @yield('form-fields')

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

<div id="MenurowTable" class="dropdown-menu shadow" style="position:absolute; display:none;"></div>
@endsection