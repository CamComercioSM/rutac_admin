@extends('layouts/layoutMaster')

@section('title', 'Reporte mensual | Supervisión')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/nouislider/nouislider.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/nouislider/nouislider.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
@endsection

@section('page-script')
    @vite(['resources/assets/js/modal-edit-user.js', 'resources/assets/js/app-ecommerce-customer-detail.js', 'resources/assets/js/app-ecommerce-customer-detail-overview.js', 'resources/js/reporteMensual/intervenciones.js'])
    @vite(['resources/assets/js/admin-list-table.js'])
@endsection

@section('content')
    <div
        class="d-flex flex-column flex-sm-row align-items-center justify-content-sm-between mb-6 text-center text-sm-start gap-2">
        <div class="mb-2 mb-sm-0">
            <h4 class="mb-1">Reporte {{ $reporte->id }}</h4>
            <p class="mb-0">Periodo: {{ $reporte->anio }} - {{ $reporte->mes }}</p>
        </div>
        <div>
            @if ($reporte->estado !== 'APROBADO' && $reporte->estado !== 'RECHAZADO')
                <button id="approve-report" class="btn btn-success me-2"><i
                        class="icon-base ri ri-check-line me-1"></i>Aprobar</button>
                <button id="delete-report" class="btn btn-danger"><i
                        class="icon-base ri ri-close-line me-1"></i>Rechazar</button>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Customer-detail Sidebar -->
        <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
            <!-- Customer-detail Card -->
            <div class="card mb-6">
                <div class="card-body pt-12">
                    <div class="customer-avatar-section">
                        <div class="d-flex align-items-center flex-column">
                            @if (!empty($reporte->asesor->logo_url))
                                <img class="img-fluid rounded mb-4" src="{{ $reporte->asesor->logo_url }}" height="120"
                                    width="120" alt="User avatar" />
                            @else
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-4"
                                    style="width:120px;height:120px;background:#e9ecef;">
                                    <span class="fw-bold text-primary" style="font-size:48px;">
                                        {{ strtoupper(substr($reporte->asesor->name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            <div class="customer-info text-center mb-6">
                                <h5 class="mb-0">{{ $reporte->asesor->name }}</h5>
                                <span>{{ $reporte->asesor->identification }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-around flex-wrap mb-6 gap-0 gap-md-3 gap-lg-4">
                        <div class="d-flex align-items-center gap-4 me-5">
                            <div class="avatar">
                                <div class="avatar-initial rounded bg-label-primary"><i
                                        class="icon-base ri ri-building-4-line icon-24px"></i></div>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $reporte->total_unidades }}</h5>
                                <span>Unidades</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-4">
                            <div class="avatar">
                                <div class="avatar-initial rounded bg-label-primary"><i
                                        class="icon-base ri ri-file-edit-line icon-24px"></i></div>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $reporte->total_intervenciones }}</h5>
                                <span>Intervenciones</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-container">
                        <h5 class="border-bottom text-capitalize pb-4 mt-6 mb-4">Detalles de Reporte</h5>
                        <ul class="list-unstyled mb-6">
                            <li class="mb-2">
                                <span class="h6 me-1">Fecha de generacion: </span>
                                <span>{{ $reporte->fecha_generacion }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6 me-1">Fecha de revision: </span>
                                <span>{{ $reporte->fecha_revision ?? 'No revisado' }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6 me-1">Estado: </span>
                                <span
                                    class="badge bg-label-success rounded-pill">{{ $reporte->estado ?? 'Sin estado' }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6 me-1"></span>
                                <span></span>
                            </li>

                            <li class="mb-2">
                                <span class="h6 me-1">Periodo: </span>
                                <span>{{ $reporte->anio . '-' . $reporte->mes ?? 'Sin periodo' }}</span>
                            </li>
                        </ul>
                        {{-- <div class="d-flex justify-content-center">
                            <a href="javascript:;" class="btn btn-primary w-100" data-bs-target="#editUser"
                                data-bs-toggle="modal">Edit
                                Details</a>
                        </div> --}}
                    </div>
                </div>
            </div>
            <!-- /Customer-detail Card -->
            <div class="card h-10">
                <div class="card-body">
                    <div class="card-info">
                        <div class="col-12">
                            <div class="card-body pb-12">
                                <small class="fw-medium">Porcentaje de avance</small>
                                <div class="noUi-info mt-6 mb-12" id="slider-info"></div>
                            </div>
                        </div>
                        <h5 class="card-title mb-2">Observaciones</h5>

                        @if ($reporte->observaciones_supervisor)
                            <div class="form-floating form-floating-outline mb-6">
                                <textarea class="form-control h-px-100" id="observacionesSupervisor" name="observacionesSupervisor" disabled
                                    placeholder="Comments here...">{{ $reporte->observaciones_supervisor }}</textarea>
                                <label for="observacionesSupervisor">Escriba las observaciones</label>
                            </div>
                        @else
                            <div class="form-floating form-floating-outline mb-6">
                                <textarea class="form-control h-px-100" id="observacionesSupervisor" name="observacionesSupervisor"
                                    placeholder="Comments here..."></textarea>
                                <label for="observacionesSupervisor">Escriba las observaciones</label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!--/ Customer Sidebar -->

        <!-- Customer Content -->
        <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informe PDF</h5>
                </div>
                <div class="card-body p-2 p-md-3">
                    <div id="pdf-viewer" class="pdf-viewer"></div>
                </div>
                {{-- <!-- Customer Pills -->
            <div class="nav-align-top">
                <ul class="nav nav-pills flex-column flex-md-row mb-6 row-gap-2 flex-wrap">
                    <li class="nav-item">
                        <a class="nav-link active" href="javascript:void(0);"><i
                                class="icon-base ri ri-group-line icon-sm me-1_5"></i>Overview</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('app/ecommerce/customer/details/security') }}"><i
                                class="icon-base ri ri-lock-2-line icon-sm me-1_5"></i>Security</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('app/ecommerce/customer/details/billing') }}"><i
                                class="icon-base ri ri-map-pin-line icon-sm me-1_5"></i>Address & Billing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('app/ecommerce/customer/details/notifications') }}"><i
                                class="icon-base ri ri-notification-4-line icon-sm me-1_5"></i>Notifications</a>
                    </li>
                </ul>
            </div>
            <!--/ Customer Pills -->

            <!--  Customer cards -->
            <div class="row text-nowrap">
                <div class="col-md-6 mb-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="card-icon mb-2">
                                <div class="avatar">
                                    <div class="avatar-initial rounded bg-label-primary"><i
                                            class="icon-base ri ri-money-dollar-circle-line icon-24px"></i></div>
                                </div>
                            </div>
                            <div class="card-info">
                                <h5 class="card-title mb-2">Account Balance</h5>
                                <div class="d-flex align-items-baseline gap-1">
                                    <h5 class="text-primary mb-0">$2345</h5>
                                    <p class="mb-0">Credit Left</p>
                                </div>
                                <p class="mb-0 text-truncate">Account balance for next purchase</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-icon mb-2">
                                <div class="avatar">
                                    <div class="avatar-initial rounded bg-label-success"><i
                                            class="icon-base ri ri-gift-line icon-24px"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-info">
                                <h5 class="card-title mb-2">Loyalty Program</h5>
                                <span class="badge bg-label-success mb-2 rounded-pill">Platinum member</span>
                                <p class="mb-0">3000 points to next tier</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-icon mb-2">
                                <div class="avatar">
                                    <div class="avatar-initial rounded bg-label-warning"><i
                                            class="icon-base ri ri-star-smile-line icon-24px"></i></div>
                                </div>
                            </div>
                            <div class="card-info">
                                <h5 class="card-title mb-2">Wishlist</h5>
                                <div class="d-flex align-items-baseline gap-1">
                                    <h5 class="text-warning mb-0">15</h5>
                                    <p class="mb-0">Items in wishlist</p>
                                </div>
                                <p class="mb-0 text-truncate">Receive notification when items go on sale</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-icon mb-2">
                                <div class="avatar">
                                    <div class="avatar-initial rounded bg-label-info"><i
                                            class="icon-base ri ri-vip-crown-line icon-24px"></i></div>
                                </div>
                            </div>
                            <div class="card-info">
                                <h5 class="card-title mb-2">Coupons</h5>
                                <div class="d-flex align-items-baseline gap-1">
                                    <h5 class="text-info mb-0">21</h5>
                                    <p class="mb-0">Coupons you win</p>
                                </div>

                                <p class="mb-0 text-truncate">Use coupon on next purchase</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ customer cards -->

            <!-- Invoice table -->
            <div class="card mb-6">
                <div class="table-responsive mb-4">
                    <table class="table datatables-customer-order">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Spent</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <!-- /Invoice table --> --}}
            </div>
            <!--/ Customer Content -->
        </div>

        <!-- Modal -->
        @include('_partials/_modals/modal-edit-user')
        @include('_partials/_modals/modal-upgrade-plan')
        <!-- /Modal -->
    @endsection

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            const reporte = @json($reporte);
            const url = baseUrl + reporte.informe_url;

            pdfjsLib.GlobalWorkerOptions.workerSrc =
                'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            const viewer = document.getElementById('pdf-viewer');

            function getScale(containerWidth, pageViewport) {
                const horizontalPadding = 24; // margen interno aproximado
                const availableWidth = Math.max(containerWidth - horizontalPadding, 320);
                return availableWidth / pageViewport.width;
            }

            try {
                const pdf = await pdfjsLib.getDocument(url).promise;

                viewer.innerHTML = '';

                for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                    const page = await pdf.getPage(pageNum);

                    const unscaledViewport = page.getViewport({
                        scale: 1
                    });
                    const scale = getScale(viewer.clientWidth, unscaledViewport);
                    const viewport = page.getViewport({
                        scale
                    });

                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');

                    canvas.classList.add('pdf-page');
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;

                    viewer.appendChild(canvas);

                    await page.render({
                        canvasContext: context,
                        viewport: viewport
                    }).promise;
                }
            } catch (error) {
                console.error('Error cargando PDF:', error);
                viewer.innerHTML = `
                    <div class="alert alert-danger mb-0">
                    No fue posible cargar el PDF.
                    </div>
                `;
            }

            // boton de aprobación
            document.getElementById('approve-report').addEventListener('click', function() {
                if (reporte.estado === 'APROBADO') {
                    Swal.fire('Reporte ya aprobado', 'Este reporte ya ha sido aprobado previamente.',
                        'info');
                    return;
                } else if (reporte.estado === 'RECHAZADO') {
                    Swal.fire('Reporte rechazado', 'Este reporte ha sido rechazado previamente.',
                        'info');
                    return;
                }
                if (!document.getElementById('observacionesSupervisor').value.trim()) {
                    Swal.fire('Observaciones requeridas',
                        'Por favor, ingresa tus observaciones antes de aprobar o rechazar el reporte.',
                        'warning');
                    return;
                }

                Swal.fire({
                    title: '¿Aprobar reporte?',
                    text: '¿Estás seguro de que deseas aprobar este reporte mensual?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, aprobar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData();
                        formData.append('reporte_id', reporte.id);
                        formData.append('observacionesSupervisor', document.getElementById(
                            'observacionesSupervisor').value);
                        formData.append('estado', 'APROBADO');

                        peticionesAJAX(baseUrl + 'reportes/supervision', 'POST', formData).then(
                            response => {

                                if (response.success) {
                                    Swal.fire(response.message, '', 'success');
                                    window.location.reload();
                                } else {
                                    Swal.fire('Error',
                                        'No se pudo aprobar el reporte. Inténtalo de nuevo.',
                                        'error');
                                }
                            }).catch(() => {
                            Swal.fire('Error',
                                'No se pudo aprobar el reporte. Inténtalo de nuevo.',
                                'error');
                        });
                    }
                });
            });
            // boton de rechazo
            document.getElementById('delete-report').addEventListener('click', function() {
                if (reporte.estado === 'APROBADO') {
                    Swal.fire('Reporte ya aprobado', 'Este reporte ya ha sido aprobado previamente.',
                        'info');
                    return;
                } else if (reporte.estado === 'RECHAZADO') {
                    Swal.fire('Reporte rechazado', 'Este reporte ha sido rechazado previamente.',
                        'info');
                    return;
                }
                if (!document.getElementById('observacionesSupervisor').value.trim()) {
                    Swal.fire('Observaciones requeridas',
                        'Por favor, ingresa tus observaciones antes de aprobar o rechazar el reporte.',
                        'warning');
                    return;
                }
                Swal.fire({
                    title: '¿Rechazar reporte?',
                    text: '¿Estás seguro de que deseas rechazar este reporte mensual?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, rechazar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData();
                        formData.append('reporte_id', reporte.id);
                        formData.append('observacionesSupervisor', document.getElementById(
                            'observacionesSupervisor').value);
                        formData.append('estado', 'RECHAZADO');
                        peticionesAJAX(baseUrl + 'reportes/supervision', 'POST', formData).then(
                            response => {
                                if (response.success) {
                                    Swal.fire(response.message, '', 'success');
                                    window.location.reload();
                                } else {
                                    Swal.fire('Error',
                                        'No se pudo rechazar el reporte. Inténtalo de nuevo.',
                                        'error');
                                }
                            }).catch(() => {
                            Swal.fire('Error',
                                'No se pudo rechazar el reporte. Inténtalo de nuevo.',
                                'error');
                        });
                    }
                });
            });

            function peticionesAJAX(url, method, data) {
                return fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: data
                }).then(response => response.json());
            }

            function validacionesOperaciones() {
                if (reporte.estado === 'APROBADO') {
                    Swal.fire('Reporte ya aprobado', 'Este reporte ya ha sido aprobado previamente.', 'info');
                    return;
                } else if (reporte.estado === 'RECHAZADO') {
                    Swal.fire('Reporte rechazado', 'Este reporte ha sido rechazado previamente.', 'info');
                    return;
                }
                if (!document.getElementById('observacionesSupervisor').value.trim()) {
                    Swal.fire('Observaciones requeridas',
                        'Por favor, ingresa tus observaciones antes de aprobar o rechazar el reporte.',
                        'warning');
                    return;
                }
            }
        });
    </script>
