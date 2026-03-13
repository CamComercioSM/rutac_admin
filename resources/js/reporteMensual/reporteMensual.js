'use strict';
// Hour pie chart

document.addEventListener('DOMContentLoaded', function (e) {
    let cardColor,
        headingColor,
        labelColor,
        fontFamily,
        borderColor,
        legendColor,
        heatMap1,
        heatMap2,
        heatMap3,
        heatMap4,
        bodyColor,
        currentTheme,
        chartBgColor;

    if (isDarkStyle) {
        heatMap1 = '#333457';
        heatMap2 = '#3c3e75';
        heatMap3 = '#484b9b';
        heatMap4 = '#696cff';
        chartBgColor = '#474360';
        currentTheme = 'dark';
    } else {
        heatMap1 = '#ededff';
        heatMap2 = '#d5d6ff';
        heatMap3 = '#b7b9ff';
        heatMap4 = '#696cff';
        chartBgColor = '#F0F2F8';
        currentTheme = 'light';
    }
    cardColor = config.colors.cardColor;
    headingColor = config.colors.headingColor;
    labelColor = config.colors.textMuted;
    borderColor = config.colors.borderColor;
    legendColor = config.colors.bodyColor;
    fontFamily = config.fontFamily;

    // Chart Colors
    const chartColors = {
        donut: {
            series1: config.colors.primary,
            series2: '#9055fdb3',
            series3: '#9055fd80'
        },
        donut2: {
            series1: '#49AC00',
            series2: '#4DB600',
            series3: config.colors.success,
            series4: '#78D533',
            series5: '#9ADF66',
            series6: '#BBEA99'
        },
        line: {
            series1: config.colors.warning,
            series2: config.colors.primary,
            series3: '#7367f029'
        }
    };

    // Time Spendings Chart
    const donutData = window.intervencionesDonut || [];
    const donutLabels = donutData.map(d => d.mes);
    const donutSeries = donutData.map(d => d.total);
    const leadsReportChartEl = document.querySelector('#leadsReportChart'),
        leadsReportChartConfig = {
            chart: {
                height: 139,
                width: 130,
                parentHeightOffset: 0,
                type: 'donut',
                opacity: 1
            },
            labels: donutLabels,
            series: donutSeries,
            colors: [
                chartColors.donut2.series1,
                chartColors.donut2.series2,
                chartColors.donut2.series3,
                chartColors.donut2.series4,
                chartColors.donut2.series5,
                chartColors.donut2.series6
            ],
            stroke: {
                width: 0
            },
            dataLabels: {
                enabled: false,
                formatter: function (val, opt) {
                    return parseInt(val) + '%';
                }
            },
            legend: {
                show: false
            },
            tooltip: {
                theme: currentTheme
            },
            grid: {
                padding: {
                    top: 0
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            value: {
                                fontSize: '1.125rem',
                                fontFamily: fontFamily,
                                color: headingColor,
                                fontWeight: 500,
                                offsetY: -15,
                                formatter: function (val) {
                                    return parseInt(val) + '%';
                                }
                            },
                            name: {
                                offsetY: 20,
                                fontFamily: fontFamily
                            },
                            total: {
                                show: true,
                                fontSize: '.9375rem',
                                label: 'Total',
                                color: labelColor,
                                formatter: function (w) {
                                    return '231h';
                                }
                            }
                        }
                    }
                }
            }
        };
    if (typeof leadsReportChartEl !== undefined && leadsReportChartEl !== null) {
        const leadsReportChart = new ApexCharts(leadsReportChartEl, leadsReportChartConfig);
        leadsReportChart.render();
    }

    // datatbale bar chart
    const mesesData = window.intervencionesPorMes || [];
    const labelsMeses = mesesData.map(m => m.nombre);
    const valoresMeses = mesesData.map(m => m.total);

    const horizontalBarChartEl = document.querySelector('#horizontalBarChart'),
        horizontalBarChartConfig = {
            chart: {
                height: 275,
                type: 'bar',
                toolbar: {
                    show: false
                }
            },
            fill: {
                opacity: 1
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '60%',
                    distributed: true,
                    startingShape: 'rounded',
                    borderRadiusApplication: 'end',
                    borderRadius: 7
                }
            },
            grid: {
                strokeDashArray: 10,
                borderColor: borderColor,
                xaxis: {
                    lines: {
                        show: true
                    }
                },
                yaxis: {
                    lines: {
                        show: false
                    }
                },
                padding: {
                    top: -35,
                    bottom: -12
                }
            },
            colors: [
                config.colors.primary,
                config.colors.info,
                config.colors.success,
                config.colors.secondary,
                config.colors.danger,
                config.colors.warning
            ],
            fill: {
                opacity: [1, 1, 1, 1, 1, 1]
            },
            dataLabels: {
                enabled: true,
                style: {
                    colors: [config.colors.white],
                    fontWeight: 400,
                    fontSize: '13px',
                    fontFamily: fontFamily
                },
                formatter: function (val, opts) {
                    return horizontalBarChartConfig.labels[opts.dataPointIndex];
                },
                offsetX: 0,
                dropShadow: {
                    enabled: false
                }
            },

            labels: labelsMeses,
            series: [
                {
                    data: valoresMeses
                }
            ],

            xaxis: {
                categories: ['6', '5', '4', '3', '2', '1'],
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    style: {
                        colors: labelColor,
                        fontFamily: fontFamily,
                        fontSize: '13px'
                    },
                    formatter: function (val) {
                        return `${val}%`;
                    }
                }
            },
            yaxis: {
                max: Math.max(...valoresMeses) + 5,
                labels: {
                    style: {
                        colors: [labelColor],
                        fontFamily: fontFamily,
                        fontSize: '13px'
                    }
                }
            },
            tooltip: {
                enabled: true,
                style: {
                    fontFamily: 'Inter',
                    fontSize: '13px'
                },
                onDatasetHover: {
                    highlightDataSeries: false
                },
                custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                    return '<div class="px-3 py-2">' + '<span>' + series[seriesIndex][dataPointIndex] + '%</span>' + '</div>';
                }
            },
            legend: {
                show: false
            }
        };
    if (typeof horizontalBarChartEl !== undefined && horizontalBarChartEl !== null) {
        const horizontalBarChart = new ApexCharts(horizontalBarChartEl, horizontalBarChartConfig);
        horizontalBarChart.render();
    }

    //radial Barchart

    function radialBarChart(color, value, show) {
        const radialBarChartOpt = {
            chart: {
                height: show == 'true' ? 58 : 55,
                width: show == 'true' ? 58 : 45,
                type: 'radialBar'
            },
            plotOptions: {
                radialBar: {
                    hollow: {
                        size: show == 'true' ? '45%' : '25%'
                    },
                    dataLabels: {
                        show: show == 'true' ? true : false,
                        value: {
                            offsetY: -10,
                            fontSize: '15px',
                            fontWeight: 500,
                            fontFamily: fontFamily,
                            color: headingColor
                        }
                    },
                    track: {
                        background: config.colors_label.secondary
                    }
                }
            },
            stroke: {
                lineCap: 'round'
            },
            colors: [color],
            grid: {
                padding: {
                    top: show == 'true' ? -12 : -15,
                    bottom: show == 'true' ? -17 : -15,
                    left: show == 'true' ? -17 : -5,
                    right: -15
                }
            },
            series: [value],
            labels: show == 'true' ? [''] : ['Progress']
        };
        return radialBarChartOpt;
    }

    const chartProgressList = document.querySelectorAll('.chart-progress');
    if (chartProgressList) {
        chartProgressList.forEach(function (chartProgressEl) {
            const color = config.colors[chartProgressEl.dataset.color],
                series = chartProgressEl.dataset.series;
            const progress_variant = chartProgressEl.dataset.progress_variant;
            const optionsBundle = radialBarChart(color, series, progress_variant);
            const chart = new ApexCharts(chartProgressEl, optionsBundle);
            chart.render();
        });
    }

    // datatable

    // Variable declaration for table
    const dt_reporte_mensual = document.querySelector('.datatables-reporte-mensual'),
        reportesUrl = baseUrl + 'reportes/reportesMensuales';
    let logoObj = {
        angular:
            '<span class="badge bg-label-danger rounded p-1_5"><i class="icon-base ri ri-angularjs-line icon-28px"></i></span>',
        figma:
            '<span class="badge bg-label-warning rounded p-1_5"><i class="icon-base ri ri-pencil-line icon-28px"></i></span>',
        react:
            '<span class="badge bg-label-info rounded p-1_5"><i class="icon-base ri ri-reactjs-line icon-28px"></i></span>',
        art: '<span class="badge bg-label-success rounded p-1_5"><i class="icon-base ri ri-palette-line icon-28px"></i></span>',
        fundamentals:
            '<span class="badge bg-label-primary rounded p-1_5"><i class="icon-base ri ri-star-smile-line icon-28px"></i></span>'
    };

    if (dt_reporte_mensual) {
        let tableTitle = document.createElement('h5');
        tableTitle.classList.add('card-title', 'mb-0', 'text-nowrap', 'text-md-start', 'text-center');
        tableTitle.innerHTML = 'Course you are taking';

        let dt_reporte = new DataTable(dt_reporte_mensual, {
            processing: true,
            serverSide: true,
            ajax: reportesUrl,
            columns: [
                { data: 'id' }, // control (responsive)
                { data: 'id', orderable: false }, // checkbox
                { data: null, orderable: true },
                { data: null },
                { data: null },
                { data: 'supervisor.name' },
                { data: null },
                { data: null }, // Meta (lo renderizamos)
                { data: 'estado' }, // Estado (render)
                { data: null }
            ],
            columnDefs: [
                {
                    // For Responsive
                    className: 'control',
                    searchable: false,
                    orderable: false,
                    responsivePriority: 2,
                    targets: 0,
                    render: function (data, type, full, meta) {
                        return '';
                    }
                },
                {
                    // For Checkboxes
                    targets: 1,
                    orderable: false,
                    searchable: false,
                    responsivePriority: 3,
                    checkboxes: true,
                    render: function () {
                        return '<input type="checkbox" class="dt-checkboxes form-check-input">';
                    },
                    checkboxes: {
                        selectAllRender: '<input type="checkbox" class="form-check-input">'
                    }
                },
                {
                    targets: 2, // creado
                    responsivePriority: 3,
                    render: (data, type, full) => {
                        let fecha = new Date(full.fecha_generacion);
                        return `<span class="text-nowrap">${fecha.toLocaleDateString()}</span>`;
                    }
                },
                {
                    data: null,
                    targets: 3,
                    render: (data, type, full) => {

                        if (!full?.anio || !full?.mes) {
                            return '-';
                        }

                        const mes = String(full.mes).padStart(2, '0');
                        const periodo = `${full.anio}-${mes}`;

                        // Para filtros y orden
                        if (type === 'filter' || type === 'sort') {
                            return periodo;
                        }

                        // Para mostrar en la tabla
                        return `<span class="text-nowrap">${periodo}</span>`;
                    }
                },
                {
                    targets: 4, // Gestor
                    responsivePriority: 2,
                    render: (data, type, full) => {
                        if (full?.estado?.toUpperCase().includes('BORRADOR')) {
                            return `<span class="fw-medium text-heading">${full.asesor.name}</span>`;
                        }
                        return `<a href="${baseUrl}reportes/supervision/${full.id}" class="fw-medium text-heading">${full.asesor.name}</a>`;
                    }
                },
                {
                    targets: 5, // supervisor
                    responsivePriority: 3,
                    render: (data, type, full) => {

                        let supervisor = full?.supervisor?.name ?? '-';

                        return `<span class="fw-medium text-heading">${supervisor}</span>`;
                    }
                },
                {
                    targets: 6, // revision
                    responsivePriority: 3,
                    render: (data, type, full) => {

                        if (!full?.fecha_revision) {
                            return `<span class="text-nowrap">-</span>`;
                        }

                        let fecha = new Date(full.fecha_revision);
                        return `<span class="text-nowrap">${fecha.toLocaleDateString()}</span>`;
                    }
                },
                {
                    targets: 7,
                    render: (data, type, full) => {
                        // genera un porcentaje simple SIN depender de campos de la plantilla
                        const total = (full.total_intervenciones ?? 0);
                        const unidades = (full.total_unidades ?? 0);

                        // porcentaje "safe" (0..100). Ejemplo: si hay 0 unidades => 0%
                        const pct = unidades > 0 ? Math.min(100, Math.round((total / unidades) * 100)) : 0;

                        // estos 2 reemplazan status/number sin romper el markup
                        const statusNumber = `${pct}%`;
                        const averageNumber = `${total}/${unidades}`;

                        return `
                            <div class="d-flex align-items-center gap-3">
                                <p class="fw-medium mb-0 text-heading">${statusNumber}</p>
                                <div class="progress bg-label-primary w-100" style="height: 8px;">
                                <div
                                    class="progress-bar"
                                    style="width: ${statusNumber}"
                                    aria-valuenow="${pct}"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                                </div>
                                <small>${averageNumber}</small>
                            </div>
                            `;
                    }
                },
                {
                    targets: 8, // Estado
                    render: (data, type, full) => {
                        const estado = (full.estado || '').toUpperCase();

                        // mapea a badge bonito
                        let badge = 'bg-label-secondary';
                        let label = estado;

                        if (estado.includes('BORRADOR')) { badge = 'bg-label-warning'; label = 'Borrador'; }
                        else if (estado.includes('PENDIENTE_REVISION')) { badge = 'bg-label-info'; label = 'Por revisar'; }
                        else if (estado.includes('APROBADO')) { badge = 'bg-label-success'; label = 'Aprobado'; }
                        else if (estado.includes('RECHAZADO')) { badge = 'bg-label-danger'; label = 'Rechazado'; }
                        return `
                        <div class="d-flex align-items-center">
                            <span class="badge ${badge}">${label}</span>
                        </div>
                        `;
                    }
                },
                {
                    targets: 9,
                    render: function (data, type, full, meta) {
                        return full.informe_url
                            ? `<a class="btn btn-sm btn-outline-primary" target="_blank" href="${full.informe_url}">Informe</a>`
                            : `<span class="text-muted">Sin informe</span>`;
                    }
                }
            ],
            select: {
                style: 'multi',
                selector: 'td:nth-child(2)'
            },
            order: [[2, 'desc']],
            layout: {
                topStart: {
                    rowClass: 'row m-3 my-0 justify-content-between',
                    features: [
                        {
                            pageLength: {
                                menu: [10, 20, 50, 70, 100],
                                text: '_MENU_'
                            }
                        }
                    ]
                },
                topEnd: {
                    search: {
                        placeholder: 'Buscar reportes...',
                        text: '_INPUT_'
                    }
                },
                bottomStart: {
                    rowClass: 'row mx-5 justify-content-between',
                    features: ['info']
                },
                bottomEnd: 'paging'
            },
            lengthMenu: [10, 25, 50, 100],
            language: {
                paginate: {
                    next: '<i class="icon-base ri ri-arrow-right-s-line scaleX-n1-rtl icon-22px"></i>',
                    previous: '<i class="icon-base ri ri-arrow-left-s-line scaleX-n1-rtl icon-22px"></i>',
                    first: '<i class="icon-base ri ri-skip-back-mini-line scaleX-n1-rtl icon-22px"></i>',
                    last: '<i class="icon-base ri ri-skip-forward-mini-line scaleX-n1-rtl icon-22px"></i>'
                }
            },
            // For responsive popup
            responsive: {
                details: {
                    display: DataTable.Responsive.display.modal({
                        header: function (row) {
                            const data = row.data();
                            return 'Detalles de ' + data['id'];
                        }
                    }),
                    type: 'column',
                    renderer: function (api, rowIdx, columns) {
                        const data = columns
                            .map(function (col) {
                                return col.title !== '' // Do not show row in modal popup if title is blank (for check box)
                                    ? `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                      <td>${col.title}:</td>
                      <td>${col.data}</td>
                    </tr>`
                                    : '';
                            })
                            .join('');

                        if (data) {
                            const div = document.createElement('div');
                            div.classList.add('table-responsive');
                            const table = document.createElement('table');
                            div.appendChild(table);
                            table.classList.add('table');
                            table.classList.add('datatables-basic');
                            const tbody = document.createElement('tbody');
                            tbody.innerHTML = data;
                            table.appendChild(tbody);
                            return div;
                        }
                        return false;
                    }
                }
            },
            initComplete: function () {
                const api = this.api();

                const filterRefs = {};

                const createFilter = (columnIndex, containerClass, selectId, defaultOptionText) => {
                    const column = api.column(columnIndex);
                    const container = document.querySelector(containerClass);

                    if (!container) return;

                    container.innerHTML = '';

                    const label = document.createElement('label');
                    label.className = 'form-label';
                    label.setAttribute('for', selectId);
                    label.textContent = defaultOptionText;

                    const select = document.createElement('select');
                    select.id = selectId;
                    select.className = 'form-select text-capitalize';
                    select.innerHTML = `<option value="">Todos</option>`;

                    container.appendChild(label);
                    container.appendChild(select);

                    const uniqueData = Array.from(
                        new Set(
                            api
                                .cells(null, columnIndex)
                                .render('filter')
                                .toArray()
                                .filter(d => d !== null && d !== undefined && d !== '-')
                        )
                    ).sort();

                    uniqueData.forEach(d => {
                        const option = document.createElement('option');
                        option.value = d;
                        option.textContent = d;
                        select.appendChild(option);
                    });

                    // guardamos referencia, pero NO filtramos aquí
                    filterRefs[columnIndex] = {
                        column,
                        select
                    };
                };

                createFilter(8, '.reporte_estado', 'ReporteEstado', 'Estados');
                createFilter(3, '.reporte_periodo', 'ReportePeriodo', 'Periodos');
                createFilter(5, '.reporte_gestor', 'ReporteSupervisor', 'Gestores');

                const btnAplicar = document.getElementById('btnAplicarFiltros');
                const btnLimpiar = document.getElementById('btnLimpiarFiltros');

                if (btnAplicar) {
                    btnAplicar.addEventListener('click', () => {
                        Object.values(filterRefs).forEach(({ column, select }) => {
                            column.search(select.value || '');
                        });

                        api.draw();
                    });
                }

                if (btnLimpiar) {
                    btnLimpiar.addEventListener('click', () => {
                        Object.values(filterRefs).forEach(({ column, select }) => {
                            select.value = '';
                            column.search('');
                        });

                        api.search(''); // limpia búsqueda global también

                        const searchInput = document.querySelector('.dt-search input');
                        if (searchInput) {
                            searchInput.value = '';
                        }

                        api.draw();
                    });
                }
            }
        });
    }

    // Filter form control to default size
    // ? setTimeout used for data-table initialization
    setTimeout(() => {
        const elementsToModify = [
            { selector: '.dt-layout-table', classToRemove: 'row mt-2' },
            { selector: '.dt-layout-start', classToAdd: 'px-0' },
            { selector: '.dt-layout-end', classToAdd: 'px-0' },
            { selector: '.dt-layout-full', classToRemove: 'col-md col-12', classToAdd: 'table-responsive' }
        ];

        // Delete record
        elementsToModify.forEach(({ selector, classToRemove, classToAdd }) => {
            document.querySelectorAll(selector).forEach(element => {
                if (classToRemove) {
                    classToRemove.split(' ').forEach(className => element.classList.remove(className));
                }
                if (classToAdd) {
                    classToAdd.split(' ').forEach(className => element.classList.add(className));
                }
            });
        });
    }, 100);

    const btnExportarExcel = document.getElementById('btnExportarReporte');

    if (btnExportarExcel) {
        btnExportarExcel.addEventListener('click', exportTableToExcel);
    }
});

function exportTableToExcel() {
    Swal.fire({
        title: '¿Deseas exportar los reportes mensuales?',
        text: "Se descargará un archivo Excel con los datos de los reportes mensuales.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, exportar',
        cancelButtonText: 'No, cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = baseUrl + 'reportes/export';
        }
    });
}
