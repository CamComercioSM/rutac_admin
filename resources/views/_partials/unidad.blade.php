<div class="card mb-6">
    <div class="card-body pt-12">
        <div class="user-avatar-section">
            <div class=" d-flex align-items-center flex-column">
                <img class="img-fluid rounded mb-4" src="{{$unidad->logo}}" height="120" width="120" alt="User avatar">
                <div class="user-info text-center">
                    <h5>{{$unidad->business_name ?? ' - '}}</h5>
                    <span class="badge bg-label-danger rounded-pill">{{$unidad->etapa->name ?? '-'}}</span>
                </div>
            </div>
        </div>
        
        <h5 class="pb-4 border-bottom mb-4">Detalles</h5>
        <div class="info-container">
            <ul class="list-unstyled mb-6">
                <li class="mb-2">
                    <span class="h6">NIT:</span>
                    <span>{{$unidad->nit ?? ' - '}}</span>
                </li>
                <li class="mb-2">
                    <span class="h6">Matrícula:</span>
                    <span>{{$unidad->registration_number ?? ' - '}}</span>
                </li>
                <li class="mb-2">
                    <span class="h6">Sector:</span>
                    <span>{{$unidad->sectorUnidad->sectorNOMBRE ?? ' - '}}</span>
                </li>
                <li class="mb-2">
                    <span class="h6">Ventas anuales:</span>
                    <span>{{$unidad->ventaAnual->ventasAnualesNOMBRE ?? ' - '}}</span>
                </li>
                <li class="mb-2">
                    <span class="h6">Contacto:</span>
                    <span>{{$unidad->contact_person ?? ' - '}}</span>
                </li>
                <li class="mb-2">
                    <span class="h6">Contacto teléfono:</span>
                    <span>{{$unidad->contact_phone ?? ' - '}}</span>
                </li>                        
            </ul>

            @if (!isset($verMasDetalles) || $verMasDetalles == true)
                <div class="d-flex justify-content-center">
                    <a href="/unidadesProductivas/{{$unidad->unidadproductiva_id}}" class="btn btn-primary me-4 waves-effect waves-light" >
                        Ver más detalles
                    </a>
                </div>
            @endif

        </div>
    </div>
</div>