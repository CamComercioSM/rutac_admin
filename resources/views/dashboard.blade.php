@extends('layouts.layoutMaster')
@section('title', 'Dashboard')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection
@section('page-style')
@endsection
@section('vendor-script')
  @vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection
@section('page-script')
    
    @vite(['resources/assets/js/dashboards-analytics.js'])

    <script>
        loading.classList.add('d-none');
    </script>

@endsection

@section('content')
  <div class="row gy-6">
    <!-- Congratulations card -->
    <div class="col-md-12 col-lg-4">
      <div class="card">
        <div class="card-body text-nowrap">
          <h5 class="card-title mb-0 flex-wrap text-nowrap">Unidades Productivas</h5>
          <p class="mb-2">Registradas</p>
          <h4 class="text-primary mb-0">{{ $cantidadUnidades }}</h4>
          <a href="/unidadesProductivas/list" class="btn btn-sm btn-primary">Ver listado</a>
        </div>
        <img src="{{ asset('assets/img/illustrations/trophy.png') }}" class="position-absolute bottom-0 end-0 me-5 mb-5"
          width="83" alt="view sales" />
      </div>
    </div>
    <!--/ Congratulations card -->

    <div class="col-lg-8">
      <div class="card h-100">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Registros por tipo</h5>
          </div>
        </div>
        <div class="card-body">

          <div class="row g-6">
            
            @foreach ($tiposRegistro as $item)
                <div class="col-md col-6">
                    <p class="mb-0"> {{ $item->nombre }}</p>
                    <div class="d-flex align-items-center">
                        <div class="avatar">
                            <div class="avatar-initial bg-white">
                                <img src="/img/registro/{{ $imgTipoRegistro[$item->unidadtipo_id] }}" alt="">
                            </div>
                        </div>
                        <div class="ms-3">
                            <h5 class="mb-0">{{ $item->cantidad }}</h5>
                        </div>
                    </div>
                </div>
            @endforeach
            
          </div>

        </div>
      </div>
    </div>
    
    <div class="col-lg-12">
      <div class="card h-100">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Registros por etapas</h5>
          </div>
        </div>
        <div class="card-body">

          <div class="row g-6">
            
            @foreach ($etapas as $item)

                <div class="col-md col-6">
                    <div class="d-flex align-items-center">
                        <div class="avatar">
                            <div class="avatar-initial rounded">
                                <img src="/img/content/{{ $item->image ?? 'advertencia.png' }}" alt="">
                            </div>
                        </div>
                        <div class="ms-3">
                            <p class="mb-0">{{ $item->nombre ?? 'No Registra' }}</p>
                            <h5 class="mb-0">{{ $item->cantidad }}</h5>
                        </div>
                    </div>
                </div>
                
            @endforeach
            
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">N° de registros de acuerdo al estado del diagnostico inicial</h5>
          </div>
        </div>
        <div class="card-body">

          <div class="row g-6">
            
            @foreach ($diagnosticos as $item)

                <div class="col-md col-6">
                    <div class="d-flex align-items-center">
                        <div class="avatar">
                            <div class="avatar-initial bg-white" >
                                <img src="/img/content/{{ $item->image}}" alt="">
                            </div>
                        </div>
                        <div class="ms-3">
                            <p class="mb-0">{{ $item->nombre ?? 'No Registra' }}</p>
                            <h5 class="mb-0">{{ $item->cantidad }}</h5>
                        </div>
                    </div>
                </div>
                
            @endforeach
            
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card h-100">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Registros  por tipo de organización</h5>
          </div>
        </div>
        <div class="card-body">

          <div class="row g-6">
            
            @foreach ($tiposOrganizacion as $item)
            
                <div class="col-md col-6">
                    <div class="d-flex align-items-center">
                        <div class="avatar">
                            <div class="avatar-initial bg-primary rounded shadow-xs">
                                <i class="icon-base ri ri-file-edit-line icon-24px"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <p class="mb-0">{{ $item->nombre ?? 'No Registra' }}</p>
                            <h5 class="mb-0">{{ $item->cantidad }}</h5>
                        </div>
                    </div>
                </div>
                
            @endforeach
            
          </div>
        </div>
      </div>
    </div>

    <!-- Sales by Countries -->
    <div class="col-xl-4 col-md-6">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Registros por municipios</h5>
        </div>
        <div class="card-body pb-0">

            @foreach ($municipios as $item)
            <div class="d-flex justify-content-between">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar me-4">
                        <div class="avatar-initial bg-label-success rounded-circle">
                            {{ collect(explode(' ', $item->nombre)) ->map(fn($p) => strtoupper(substr($p, 0, 1))) ->join('') }}
                        </div>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-1 mb-1">
                            <h6 class="mb-0">{{ $item->nombre }}</h6>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <h6 class="mb-1">{{ $item->cantidad }}</h6>
                </div>
            </div>
            @endforeach
          
        </div>
      </div>
    </div>
    <!--/ Sales by Countries -->
    <!-- Data Tables -->
    <div class="col-xl-8 col-md-6">
      <div class="card  h-100 overflow-hidden">

        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Registros por tamaño según su Macrosector</h5>
        </div>

        <div class="table-responsive">
          <table class="table table-sm">

            <thead>
                <tr>
                    <th>Tamaño</th>
                    @foreach($sectores as $sector)
                        <th class="text-end">{{ $sector ?? 'Sin sector' }}</th>
                    @endforeach
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pivot as $tamano => $fila)
                    <tr>
                        <td>{{ $tamano ? $tamano : 'Sin tamaño' }}</td>
                        @foreach($fila as $valor)
                            <td class="text-end">{{ $valor }}</td>
                        @endforeach
                        <td class="text-end fw-bold">{{ collect($fila)->sum() }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr class="fw-bold">
                    <td>Total</td>
                    @foreach($sectores as $sector)
                        <td class="text-end">
                            {{ collect($pivot)->sum(fn($fila) => $fila[$sector] ?? 0) }}
                        </td>
                    @endforeach
                    <td class="text-end">
                        {{ collect($pivot)->sum(fn($fila) => collect($fila)->sum()) }}
                    </td>
                </tr>
            </tfoot>

            
          </table>
        </div>
      </div>
    </div>
    <!--/ Data Tables -->
  </div>
@endsection
