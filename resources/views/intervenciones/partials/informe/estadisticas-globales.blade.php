<div class="row text-center mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0" style="background: #0e188a; color: white;">
            <div class="card-body">
                <small>Total Actividades</small>
                <h3>{{ $totalGeneral }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Con intervenido</h6>
                <h3 class="text-success">{{ $intervencionesDetalladas->count() }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Transversales</h6>
                <h3 class="text-dark">{{ $actividadesTransversales->count() }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Unidades</h6>
                <h3 class="text-info">{{ count($porUnidad) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row text-center mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Intervenciones</h6>
                <h3 class="text-primary">{{ $totalGeneral }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Unidades</h6>
                <h3 class="text-success">{{ count($porUnidad) }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Categorías</h6>
                <h3 class="text-dark">{{ count($porCategoria) }}</h3>
            </div>
        </div>
    </div>
</div>
