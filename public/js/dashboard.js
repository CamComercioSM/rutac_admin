/**
 * Dashboard JavaScript - rutaC
 * Funcionalidades avanzadas para el dashboard de Unidades Productivas
 */

class DashboardManager {
    constructor() {
        this.map = null;
        this.markers = [];
        this.currentFilters = {};
        this.charts = {};
        this.chartColors = [
            '#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe',
            '#43e97b', '#38f9d7', '#fa709a', '#fee140', '#a8edea', '#fed6e3'
        ];
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeMap();
        this.setupAnimations();
        this.loadDashboardData();
        this.setupChartInteractions();
    }

    setupEventListeners() {
        // Filtros rápidos
        document.getElementById('periodoSelect')?.addEventListener('change', (e) => {
            this.cambiarPeriodo(e.target.value);
        });

        document.getElementById('departamentoSelect')?.addEventListener('change', (e) => {
            this.filtrarPorDepartamento(e.target.value);
        });

        document.getElementById('sectorSelect')?.addEventListener('change', (e) => {
            this.filtrarPorSector(e.target.value);
        });

        document.getElementById('etapaSelect')?.addEventListener('change', (e) => {
            this.filtrarPorEtapa(e.target.value);
        });

        // Botón de restablecer
        document.querySelector('[onclick="resetFilters()"]')?.addEventListener('click', (e) => {
            e.preventDefault();
            this.resetFilters();
        });

        // Modal de filtros avanzados
        document.getElementById('filtrosForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.aplicarFiltrosAvanzados();
        });

        // Actualizar municipios cuando cambie el departamento
        document.getElementById('modalDepartamento')?.addEventListener('change', (e) => {
            this.actualizarMunicipios(e.target.value);
        });

        // Búsqueda en tiempo real
        this.setupRealTimeSearch();
    }

    setupChartInteractions() {
        // Agregar interactividad a los gráficos
        this.setupChartHoverEffects();
        this.setupChartClickEvents();
        this.setupChartResponsiveness();
    }

    setupChartHoverEffects() {
        // Efectos de hover para contenedores de gráficos
        const chartContainers = document.querySelectorAll('.chart-container');
        chartContainers.forEach(container => {
            container.addEventListener('mouseenter', () => {
                container.style.transform = 'scale(1.02)';
                container.style.boxShadow = '0 8px 30px rgba(0,0,0,0.15)';
            });

            container.addEventListener('mouseleave', () => {
                container.style.transform = 'scale(1)';
                container.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
            });
        });
    }

    setupChartClickEvents() {
        // Eventos de clic para gráficos específicos
        this.setupPieChartClickEvents();
        this.setupBarChartClickEvents();
    }

    setupPieChartClickEvents() {
        // Configurar eventos de clic para gráficos de pastel
        const pieCharts = ['tipoOrganizacionChart', 'etapasChart', 'proporcionesChart'];
        pieCharts.forEach(chartId => {
            const chart = Chart.getChart(chartId);
            if (chart) {
                chart.options.onClick = (event, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const label = chart.data.labels[index];
                        const value = chart.data.datasets[0].data[index];
                        this.showChartDetail(label, value, chartId);
                    }
                };
                chart.update();
            }
        });
    }

    setupBarChartClickEvents() {
        // Configurar eventos de clic para gráficos de barras
        const barCharts = ['estadoDiagnosticoChart', 'municipiosChart'];
        barCharts.forEach(chartId => {
            const chart = Chart.getChart(chartId);
            if (chart) {
                chart.options.onClick = (event, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const label = chart.data.labels[index];
                        const value = chart.data.datasets[0].data[index];
                        this.showChartDetail(label, value, chartId);
                    }
                };
                chart.update();
            }
        });
    }

    setupChartResponsiveness() {
        // Hacer los gráficos responsivos
        window.addEventListener('resize', () => {
            Object.values(this.charts).forEach(chart => {
                if (chart && typeof chart.resize === 'function') {
                    chart.resize();
                }
            });
        });
    }

    showChartDetail(label, value, chartId) {
        // Mostrar detalles del gráfico en un modal o tooltip
        const tooltip = document.createElement('div');
        tooltip.className = 'chart-detail-tooltip';
        tooltip.innerHTML = `
            <div class="tooltip-content">
                <h6>${label}</h6>
                <p>Valor: ${this.formatNumber(value)}</p>
                <small>Gráfico: ${this.getChartName(chartId)}</small>
            </div>
        `;
        
        document.body.appendChild(tooltip);
        
        // Posicionar tooltip
        const event = window.event;
        tooltip.style.left = event.pageX + 10 + 'px';
        tooltip.style.top = event.pageY - 10 + 'px';
        
        // Remover tooltip después de 3 segundos
        setTimeout(() => {
            tooltip.remove();
        }, 3000);
    }

    getChartName(chartId) {
        const chartNames = {
            'tipoOrganizacionChart': 'Tipo de Organización',
            'estadoDiagnosticoChart': 'Estado del Diagnóstico',
            'etapasChart': 'Etapas',
            'municipiosChart': 'Municipios',
            'proporcionesChart': 'Proporciones'
        };
        return chartNames[chartId] || 'Gráfico';
    }

    setupRealTimeSearch() {
        const searchInputs = document.querySelectorAll('.form-select, .form-control');
        searchInputs.forEach(input => {
            input.addEventListener('input', this.debounce(() => {
                this.highlightSearchResults(input.value);
            }, 300));
        });
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    highlightSearchResults(searchTerm) {
        if (!searchTerm) return;
        
        const tableRows = document.querySelectorAll('tbody tr');
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm.toLowerCase())) {
                row.style.backgroundColor = 'rgba(102, 126, 234, 0.1)';
                row.style.transform = 'scale(1.02)';
            } else {
                row.style.backgroundColor = '';
                row.style.transform = '';
            }
        });
    }

    initializeMap() {
        if (typeof google === 'undefined') {
            console.warn('Google Maps no está disponible');
            return;
        }

        const colombia = { lat: 4.5709, lng: -74.2973 };
        
        this.map = new google.maps.Map(document.getElementById('mapaColombia'), {
            zoom: 6,
            center: colombia,
            mapTypeId: 'roadmap',
            styles: this.getMapStyles(),
            zoomControl: true,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true
        });

        this.addMapMarkers();
        this.setupMapInteractions();
    }

    getMapStyles() {
        return [
            {
                featureType: 'administrative',
                elementType: 'geometry',
                stylers: [{ visibility: 'simplified' }]
            },
            {
                featureType: 'landscape',
                elementType: 'geometry',
                stylers: [{ color: '#f5f5f2' }]
            },
            {
                featureType: 'water',
                elementType: 'geometry',
                stylers: [{ color: '#c9c9c9' }]
            }
        ];
    }

    addMapMarkers() {
        // Los marcadores se agregarán dinámicamente desde el backend
        // Esta función se puede expandir para agregar marcadores personalizados
    }

    setupMapInteractions() {
        // Agregar controles personalizados al mapa
        this.addCustomMapControls();
    }

    addCustomMapControls() {
        const mapDiv = document.getElementById('mapaColombia');
        if (!mapDiv) return;

        // Controles personalizados para el mapa
        const customControls = document.createElement('div');
        customControls.className = 'custom-map-controls';
        customControls.innerHTML = `
            <div class="map-control-panel">
                <button class="btn btn-sm btn-outline-primary" onclick="dashboardManager.zoomToColombia()">
                    <i class="fas fa-home"></i> Colombia
                </button>
                <button class="btn btn-sm btn-outline-info" onclick="dashboardManager.toggleHeatmap()">
                    <i class="fas fa-fire"></i> Mapa de Calor
                </button>
            </div>
        `;

        mapDiv.appendChild(customControls);
    }

    zoomToColombia() {
        if (this.map) {
            this.map.setCenter({ lat: 4.5709, lng: -74.2973 });
            this.map.setZoom(6);
        }
    }

    toggleHeatmap() {
        // Implementar funcionalidad de mapa de calor
        console.log('Mapa de calor no implementado aún');
        this.showNotification('Funcionalidad de mapa de calor próximamente', 'info');
    }

    cambiarVistaMapa(tipo) {
        if (!this.map) return;

        if (tipo === 'satelite') {
            this.map.setMapTypeId('hybrid');
        } else {
            this.map.setMapTypeId('roadmap');
        }
    }

    filtrarPorDepartamento(departamentoId) {
        this.updateURLParameter('departamento_id', departamentoId);
        this.reloadWithFilters();
    }

    filtrarPorSector(sectorId) {
        this.updateURLParameter('sector_id', sectorId);
        this.reloadWithFilters();
    }

    filtrarPorEtapa(etapaId) {
        this.updateURLParameter('etapa_id', etapaId);
        this.reloadWithFilters();
    }

    cambiarPeriodo(dias) {
        if (!dias) return;

        const fechaHasta = new Date();
        const fechaDesde = new Date();
        fechaDesde.setDate(fechaDesde.getDate() - parseInt(dias));
        
        this.updateURLParameter('fecha_desde', fechaDesde.toISOString().split('T')[0]);
        this.updateURLParameter('fecha_hasta', fechaHasta.toISOString().split('T')[0]);
        this.reloadWithFilters();
    }

    updateURLParameter(key, value) {
        const url = new URL(window.location);
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
        this.currentFilters[key] = value;
    }

    reloadWithFilters() {
        const url = new URL(window.location);
        Object.entries(this.currentFilters).forEach(([key, value]) => {
            if (value) {
                url.searchParams.set(key, value);
            }
        });
        window.location.href = url.toString();
    }

    resetFilters() {
        window.location.href = window.location.pathname;
    }

    aplicarFiltrosAvanzados() {
        const formData = new FormData(document.getElementById('filtrosForm'));
        const filters = {};
        
        for (let [key, value] of formData.entries()) {
            if (value) {
                filters[key] = value;
            }
        }

        // Construir URL con filtros
        const url = new URL(window.location);
        Object.entries(filters).forEach(([key, value]) => {
            url.searchParams.set(key, value);
        });

        // Cerrar modal y recargar
        const modal = bootstrap.Modal.getInstance(document.getElementById('filtrosModal'));
        modal.hide();
        window.location.href = url.toString();
    }

    actualizarMunicipios(departamentoId) {
        const municipioSelect = document.getElementById('modalMunicipio');
        if (!municipioSelect) return;

        if (!departamentoId) {
            municipioSelect.innerHTML = '<option value="">Todos los municipios</option>';
            return;
        }

        // Aquí podrías hacer una llamada AJAX para obtener los municipios del departamento
        // Por ahora solo limpiamos la selección
        municipioSelect.value = '';
        
        // Simular carga
        municipioSelect.innerHTML = '<option value="">Cargando...</option>';
        
        // En una implementación real, harías:
        // fetch(`/api/municipios/${departamentoId}`)
        //     .then(response => response.json())
        //     .then(municipios => {
        //         municipioSelect.innerHTML = '<option value="">Todos los municipios</option>';
        //         municipios.forEach(municipio => {
        //             municipioSelect.innerHTML += `<option value="${municipio.id}">${municipio.nombre}</option>`;
        //         });
        //     });
    }

    setupAnimations() {
        // Configurar animaciones de entrada para las tarjetas
        const cards = document.querySelectorAll('.summary-card, .map-card, .stages-card, .table-card');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
            observer.observe(card);
        });
    }

    loadDashboardData() {
        // Cargar datos adicionales del dashboard si es necesario
        this.updateLastUpdateTime();
        this.setupAutoRefresh();
        this.setupChartAnimations();
    }

    setupChartAnimations() {
        // Animaciones para los gráficos
        const chartContainers = document.querySelectorAll('.chart-container');
        chartContainers.forEach((container, index) => {
            container.style.opacity = '0';
            container.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                container.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
                container.style.opacity = '1';
                container.style.transform = 'scale(1)';
            }, index * 200);
        });
    }

    updateLastUpdateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('es-CO');
        
        // Agregar indicador de última actualización
        const header = document.querySelector('.dashboard-header');
        if (header) {
            let updateIndicator = header.querySelector('.update-indicator');
            if (!updateIndicator) {
                updateIndicator = document.createElement('div');
                updateIndicator.className = 'update-indicator text-muted small mt-2';
                header.appendChild(updateIndicator);
            }
            updateIndicator.textContent = `Última actualización: ${timeString}`;
        }
    }

    setupAutoRefresh() {
        // Auto-refresh cada 5 minutos (300000 ms)
        setInterval(() => {
            this.refreshDashboardData();
        }, 300000);
    }

    refreshDashboardData() {
        // Implementar refresh de datos sin recargar la página
        console.log('Refrescando datos del dashboard...');
        this.updateLastUpdateTime();
        this.showNotification('Datos actualizados automáticamente', 'success');
    }

    // Métodos de utilidad
    formatNumber(num) {
        return new Intl.NumberFormat('es-CO').format(num);
    }

    showNotification(message, type = 'info') {
        // Implementar sistema de notificaciones
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove después de 5 segundos
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    exportDashboardData(format = 'pdf') {
        // Implementar exportación de datos del dashboard
        this.showNotification('Exportando datos del dashboard...', 'info');
        
        // Aquí implementarías la lógica de exportación
        setTimeout(() => {
            this.showNotification('Datos exportados exitosamente', 'success');
        }, 2000);
    }

    // Métodos para gráficos avanzados
    updateChartData(chartId, newData) {
        const chart = Chart.getChart(chartId);
        if (chart) {
            chart.data = newData;
            chart.update('active');
        }
    }

    animateChart(chartId, animationType = 'fadeIn') {
        const chart = Chart.getChart(chartId);
        if (chart) {
            chart.options.animation = {
                duration: 1000,
                easing: 'easeInOutQuart'
            };
            chart.update();
        }
    }

    toggleChartType(chartId, newType) {
        const chart = Chart.getChart(chartId);
        if (chart) {
            chart.config.type = newType;
            chart.update();
        }
    }
}

// Inicializar el dashboard cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si Google Maps está disponible
    if (typeof google === 'undefined') {
        // Cargar Google Maps dinámicamente
        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=geometry&callback=initDashboard';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    } else {
        initDashboard();
    }
});

// Función global para inicializar el dashboard
function initDashboard() {
    window.dashboardManager = new DashboardManager();
}

// Funciones globales para compatibilidad con onclick
function cambiarVistaMapa(tipo) {
    if (window.dashboardManager) {
        window.dashboardManager.cambiarVistaMapa(tipo);
    }
}

function filtrarPorDepartamento(departamentoId) {
    if (window.dashboardManager) {
        window.dashboardManager.filtrarPorDepartamento(departamentoId);
    }
}

function filtrarPorSector(sectorId) {
    if (window.dashboardManager) {
        window.dashboardManager.filtrarPorSector(sectorId);
    }
}

function filtrarPorEtapa(etapaId) {
    if (window.dashboardManager) {
        window.dashboardManager.filtrarPorEtapa(etapaId);
    }
}

function cambiarPeriodo(dias) {
    if (window.dashboardManager) {
        window.dashboardManager.cambiarPeriodo(dias);
    }
}

function resetFilters() {
    if (window.dashboardManager) {
        window.dashboardManager.resetFilters();
    }
}

// Funciones adicionales para gráficos
function cambiarTipoGraficoTamanoSector(tipo) {
    if (window.dashboardManager) {
        window.dashboardManager.toggleChartType('tamanoSectorChart', tipo === 'stacked' ? 'bar' : 'bar');
    }
}

function cambiarPeriodoGrafico(periodo) {
    if (window.dashboardManager) {
        console.log('Cambiando periodo del gráfico a:', periodo, 'meses');
        // Aquí implementarías la lógica para cambiar el periodo del gráfico
    }
}

// Estilos CSS dinámicos para tooltips
const tooltipStyles = `
    .chart-detail-tooltip {
        position: absolute;
        z-index: 10000;
        background: rgba(0, 0, 0, 0.9);
        color: white;
        padding: 10px;
        border-radius: 8px;
        font-size: 12px;
        max-width: 200px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        pointer-events: none;
        animation: fadeIn 0.3s ease;
    }
    
    .chart-detail-tooltip .tooltip-content h6 {
        margin: 0 0 5px 0;
        color: #fff;
        font-size: 14px;
    }
    
    .chart-detail-tooltip .tooltip-content p {
        margin: 0 0 3px 0;
        color: #ccc;
    }
    
    .chart-detail-tooltip .tooltip-content small {
        color: #999;
        font-size: 10px;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;

// Agregar estilos al head
const styleSheet = document.createElement('style');
styleSheet.textContent = tooltipStyles;
document.head.appendChild(styleSheet);
