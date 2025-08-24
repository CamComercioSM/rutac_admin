/**
 * Dashboard JavaScript - rutaC
 * Funcionalidades avanzadas para el dashboard de Unidades Productivas
 */

// Variables globales
let dashboardData = null;
let map = null;
let markers = [];
let currentMapType = 'roadmap';
let infoWindow = null;

// ===== CONFIGURACIÓN GLOBAL =====
let backendData = window.backendData || {};

// Hacer backendData disponible globalmente
window.backendData = backendData;

// Configuración global del dashboard
window.DashboardConfig = {
    performanceMode: false,
    lazyLoading: false,
    cacheEnabled: true,
    autoRefresh: false,
    refreshInterval: 300000, // 5 minutos
    chartAnimationDuration: 1000,
    maxDataPoints: 20
};

// ===== FUNCIONES DE CARGA DE LIBRERÍAS =====

// Función para cargar Chart.js
function loadChartJS() {
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.js';
        script.onload = () => {
            console.log('Chart.js cargado desde CDN principal');
            resolve();
        };
        script.onerror = () => {
            console.warn('CDN principal falló, intentando CDN alternativo...');
            const script2 = document.createElement('script');
            script2.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js';
            script2.onload = () => {
                console.log('Chart.js cargado desde CDN alternativo');
                resolve();
            };
            script2.onerror = () => {
                console.error('Ambos CDNs fallaron');
                reject(new Error('No se pudo cargar Chart.js'));
            };
            document.head.appendChild(script2);
        };
        document.head.appendChild(script);
    });
}

// Función para cargar Google Maps (ya se carga via script tag)
function loadGoogleMaps() {
    return new Promise((resolve) => {
        if (typeof google !== 'undefined' && google.maps) {
            resolve();
        } else {
            // Esperar a que Google Maps se cargue
            const checkGoogleMaps = setInterval(() => {
                if (typeof google !== 'undefined' && google.maps) {
                    clearInterval(checkGoogleMaps);
                    resolve();
                }
            }, 100);
        }
    });
}

// ===== FUNCIONES PRINCIPALES DEL DASHBOARD =====

function initializeDashboard() {
    console.log('=== INICIALIZANDO DASHBOARD ===');
    
    // Pasar datos del dashboard al JavaScript
    dashboardData = {
        totalUnidades: window.totalUnidades || 0,
        datosMapa: backendData.datosMapa,
        porMunicipios: backendData.porMunicipios,
        porTipoOrganizacion: backendData.porTipoOrganizacion,
        porEstadoDiagnostico: backendData.porEstadoDiagnostico,
        porEtapas: backendData.porEtapas,
        evolucionTemporal: backendData.evolucionTemporal,
        porTamanos: backendData.porTamanos
    };
    
    console.log('Llamando a setupEventListeners...');
    setupEventListeners();
    updateLastUpdateTime();
    
    // Calcular tendencia de métricas
    updateMetricTrend();
    
    hideLoading();
    console.log('Dashboard inicializado completamente');
}

function initializeCharts() {
    console.log('Inicializando gráficos con datos reales...');
    
    try {
        // Usar datos del dashboard desde el backend
        console.log('Datos reales del backend:', backendData);
        
        // Gráfico de Tipo de Organización (datos reales)
        if (backendData.porTipoOrganizacion && backendData.porTipoOrganizacion.length > 0) {
            const tipoOrgData = {
                labels: backendData.porTipoOrganizacion.map(item => 
                    item.tipoPersona?.tipoPersonaNOMBRE || `Tipo ${item.tipopersona_id}`
                ),
                data: backendData.porTipoOrganizacion.map(item => item.total),
                backgroundColor: '#667eea'
            };
            createBarChart('tipoOrganizacionChart', tipoOrgData);
            
            // Actualizar la leyenda del gráfico
            updateTipoOrganizacionContent();
        } else {
            console.warn('No hay datos de tipo de organización');
            createBarChart('tipoOrganizacionChart', {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: '#6c757d'
            });
            
            // Actualizar la leyenda con mensaje de no datos
            updateTipoOrganizacionContent();
        }

        // Gráfico de Estado del Diagnóstico (datos reales) - MOSTRAR PORCENTAJES en la dona
        if (backendData.porEstadoDiagnostico && backendData.porEstadoDiagnostico.length > 0) {
            // Calcular total para porcentajes
            const totalDiagnosticos = backendData.porEstadoDiagnostico.reduce((sum, item) => sum + item.total, 0);
            
            const estadoData = {
                labels: backendData.porEstadoDiagnostico.map(item => item.estado),
                data: backendData.porEstadoDiagnostico.map(item => 
                    Math.round((item.total / totalDiagnosticos) * 100)
                ),
                backgroundColor: ['#28a745', '#ffc107', '#dc3545']
            };
            createDoughnutChart('estadoDiagnosticoChart', estadoData);
            
            // Actualizar la leyenda del gráfico
            updateEstadoDiagnosticoContent();
        } else {
            createDoughnutChart('estadoDiagnosticoChart', {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: ['#6c757d']
            });
            
            // Actualizar la leyenda con mensaje de no datos
            updateEstadoDiagnosticoContent();
        }

        // Gráfico de Etapas (datos reales)
        if (backendData.porEtapas && backendData.porEtapas.length > 0) {
            const etapasData = {
                labels: backendData.porEtapas.map(item => 
                    item.etapa?.name || `Etapa ${item.etapa_id}`
                ),
                data: backendData.porEtapas.map(item => item.total),
                backgroundColor: ['#ffc107', '#17a2b8', '#28a745', '#fd7e14', '#6f42c1']
            };
            createBarChart('etapasChart', etapasData);
        } else {
            console.warn('No hay datos de etapas');
            createBarChart('etapasChart', {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: ['#6c757d']
            });
        }

        // Gráfico de Municipios (datos reales - TOP 10)
        if (backendData.porMunicipios && backendData.porMunicipios.length > 0) {
            // Tomar solo los primeros 10 municipios
            const topMunicipios = backendData.porMunicipios.slice(0, 10);
            const municipiosData = {
                labels: topMunicipios.map(item => 
                    item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`
                ),
                data: topMunicipios.map(item => item.total),
                backgroundColor: '#28a745'
            };
            createBarChart('municipiosChart', municipiosData);
            
            // Actualizar la tabla de municipios
            updateMunicipiosTable();
        } else {
            console.warn('No hay datos de municipios');
            createBarChart('municipiosChart', {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: ['#6c757d']
            });
            
            // Actualizar la tabla con mensaje de no datos
            updateMunicipiosTable();
        }

        // Gráfico de Proporciones por Municipios
        if (backendData.porMunicipiosCompletos && Array.isArray(backendData.porMunicipiosCompletos) && backendData.porMunicipiosCompletos.length > 0) {
            const totalUnidades = backendData.totalUnidades || backendData.porMunicipiosCompletos.reduce((sum, item) => sum + item.total, 0);
            
            // Crear colores dinámicos para todos los municipios
            const generateColors = (count) => {
                const colors = [];
                for (let i = 0; i < count; i++) {
                    const hue = (i * 137.508) % 360; // Número áureo para distribución de colores
                    const saturation = 70 + (i % 20); // Variación en saturación
                    const lightness = 50 + (i % 15); // Variación en luminosidad
                    colors.push(`hsl(${hue}, ${saturation}%, ${lightness}%)`);
                }
                return colors;
            };
            
            const proporcionesData = {
                labels: backendData.porMunicipiosCompletos.map(item => 
                    item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`
                ),
                data: backendData.porMunicipiosCompletos.map(item => 
                    Math.round((item.total / totalUnidades) * 100)
                ),
                backgroundColor: generateColors(backendData.porMunicipiosCompletos.length)
            };
            createPieChart('proporcionesChart', proporcionesData);
            
            // Actualizar la tabla de proporciones directamente
            updateProporcionesTable();
        } else if (backendData.porMunicipios && Array.isArray(backendData.porMunicipios) && backendData.porMunicipios.length > 0) {
            // Fallback: usar datos de municipios limitados si no hay completos
            console.log('Usando fallback con datos limitados de municipios');
            const totalUnidades = backendData.totalUnidades || backendData.porMunicipios.reduce((sum, item) => sum + item.total, 0);
            
            const proporcionesData = {
                labels: backendData.porMunicipios.map(item => 
                    item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`
                ),
                data: backendData.porMunicipios.map(item => 
                    Math.round((item.total / totalUnidades) * 100)
                ),
                backgroundColor: ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#17a2b8', '#ffc107', '#28a745']
            };
            createPieChart('proporcionesChart', proporcionesData);
            
            // Actualizar la tabla de proporciones con datos limitados
            updateProporcionesTableFallback();
        } else {
            console.warn('No hay datos para proporciones');
            createPieChart('proporcionesChart', {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: ['#6c757d']
            });
        }

        // Gráfico de Evolución Temporal (datos reales - últimos 12 meses)
        if (backendData.evolucionTemporal && backendData.evolucionTemporal.length > 0) {
            const evolucionData = {
                labels: backendData.evolucionTemporal.map(item => item.label),
                data: backendData.evolucionTemporal.map(item => item.total),
                backgroundColor: '#ffc107'
            };
            window.evolucionTemporalChart = createLineChart('evolucionTemporalChart', evolucionData);
        } else {
            console.warn('No hay datos de evolución temporal');
            window.evolucionTemporalChart = createLineChart('evolucionTemporalChart', {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: '#6c757d'
            });
        }

        // Gráfico de Tamaños de Empresa (datos reales)
        if (backendData.porTamanos && backendData.porTamanos.length > 0) {
            const tamanosData = {
                labels: backendData.porTamanos.map(item => 
                    item.tamano?.tamanoNOMBRE || `Tamaño ${item.tamano_id}`
                ),
                data: backendData.porTamanos.map(item => item.total),
                backgroundColor: '#17a2b8'
            };
            // Inicializar con gráfico de barras por defecto (como está configurado en el HTML)
            tamanosChart = createBarChart('tamanosChart', tamanosData);
            updateTamanosContent(tamanosData, false); // false = mostrar conteos
        } else {
            console.warn('No hay datos de tamaños');
            const tamanosData = {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: '#6c757d'
            };
            tamanosChart = createBarChart('tamanosChart', tamanosData);
            updateTamanosContent(tamanosData, false);
        }

        // Gráfico de Sectores (datos reales)
        if (backendData.porSectores && backendData.porSectores.length > 0) {
            const sectoresData = {
                labels: backendData.porSectores.map(item => 
                    item.sector?.sectorNOMBRE || `Sector ${item.sector_id}`
                ),
                data: backendData.porSectores.map(item => item.total),
                backgroundColor: ['#e83e8c', '#fd7e14', '#20c997', '#6f42c1', '#dc3545', '#28a745', '#17a2b8', '#ffc107']
            };
            // Inicializar con gráfico de dona por defecto (como está configurado en el HTML)
            sectoresChart = createDoughnutChart('sectoresChart', sectoresData);
            updateSectoresContent(sectoresData, true); // true = mostrar porcentajes
        } else {
            console.warn('No hay datos de sectores');
            const sectoresData = {
                labels: ['Sin datos'],
                data: [1],
                backgroundColor: ['#6c757d']
            };
            sectoresChart = createDoughnutChart('sectoresChart', sectoresData);
            updateSectoresContent(sectoresData, true);
        }
        
        console.log('Todos los gráficos inicializados con datos reales');
        
        // Actualizar contenido estático con datos reales
        updateChartContent();
        
    } catch (error) {
        console.error('Error al inicializar gráficos con datos reales:', error);
        alert('Error al crear los gráficos: ' + error.message);
        
        // Crear gráficos con datos de ejemplo en caso de error
        console.log('Creando gráficos con datos de ejemplo debido al error...');
        createBarChart('tipoOrganizacionChart', {
            labels: ['Natural', 'Jurídica', 'Establecimiento'],
            data: [2157, 750, 19],
            backgroundColor: '#667eea'
        });
        createDoughnutChart('estadoDiagnosticoChart', {
            labels: ['Terminado', 'Pendiente'],
            data: [62, 38], // Porcentajes aproximados
            backgroundColor: ['#28a745', '#ffc107']
        });
        createBarChart('etapasChart', {
            labels: ['Despegue', 'Nacimiento', 'Crecimiento', 'Descubrimiento', 'Madurez'],
            data: [1145, 929, 322, 302, 30],
            backgroundColor: '#ffc107'
        });
        createBarChart('municipiosChart', {
            labels: ['Bogotá', 'Medellín', 'Cali', 'Barranquilla', 'Cartagena'],
            data: [450, 320, 280, 180, 150],
            backgroundColor: '#28a745'
        });
        createPieChart('proporcionesChart', {
            labels: ['Micro', 'Pequeña', 'Mediana', 'Gran Empresa'],
            data: [45, 30, 20, 5],
            backgroundColor: ['#667eea', '#764ba2', '#f093fb', '#f5576c']
        });
        window.evolucionTemporalChart = createLineChart('evolucionTemporalChart', {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            data: [100, 120, 110, 130, 140, 150, 160, 170, 180, 190, 200, 210],
            backgroundColor: '#ffc107'
        });
        const tamanosData = {
            labels: ['Micro', 'Pequeña', 'Mediana', 'Grande', 'Gigante'],
            data: [100, 200, 300, 250, 150],
            backgroundColor: '#17a2b8'
        };
        tamanosChart = createBarChart('tamanosChart', tamanosData);
        updateTamanosContent(tamanosData, false);
        
        const sectoresData = {
            labels: ['Servicios', 'Manufactura', 'Comercio'],
            data: [1118, 930, 794],
            backgroundColor: ['#e91e63', '#ff9800', '#00bcd4']
        };
        sectoresChart = createDoughnutChart('sectoresChart', sectoresData);
        updateSectoresContent(sectoresData, true);
    }
}

// ===== FUNCIONES DEL MAPA CON GOOGLE MAPS =====

function initializeMap() {
    console.log('Inicializando Google Maps...');
    
    try {
        // Mostrar indicador de carga del mapa
        const mapContainer = document.getElementById('mapaColombia');
        if (mapContainer) {
            mapContainer.innerHTML = `
                <div class="d-flex justify-content-center align-items-center h-100" style="background: #f8f9fa; border-radius: 10px;">
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-2" role="status">
                            <span class="visually-hidden">Cargando Google Maps...</span>
                        </div>
                        <p class="text-muted mb-0">Cargando Google Maps...</p>
                    </div>
                </div>
            `;
        }
        
        // Coordenadas de Colombia
        const colombiaCenter = { lat: 4.5709, lng: -74.2973 };
        
        // Crear el mapa de Google
        map = new google.maps.Map(document.getElementById('mapaColombia'), {
            center: colombiaCenter,
            zoom: 6,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapTypeControl: true,
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                position: google.maps.ControlPosition.TOP_RIGHT
            },
            zoomControl: true,
            zoomControlOptions: {
                position: google.maps.ControlPosition.RIGHT_CENTER
            },
            streetViewControl: false,
            fullscreenControl: true,
            styles: [
                {
                    featureType: 'poi',
                    elementType: 'labels',
                    stylers: [{ visibility: 'off' }]
                }
            ]
        });
        
        // Crear ventana de información
        infoWindow = new google.maps.InfoWindow();
        
        // Evento cuando el mapa está listo
        google.maps.event.addListenerOnce(map, 'idle', function() {
            console.log('Google Maps cargado completamente');
            // Cargar marcadores reales cuando el mapa esté listo
            loadRealMapMarkers();
        });
        
    } catch (error) {
        console.error('Error al inicializar Google Maps:', error);
        const mapContainer = document.getElementById('mapaColombia');
        if (mapContainer) {
            mapContainer.innerHTML = `
                <div class="d-flex justify-content-center align-items-center h-100" style="background: #f8f9fa; border-radius: 10px;">
                    <div class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p class="mb-0">Error al cargar Google Maps</p>
                        <small>${error.message}</small>
                    </div>
                </div>
            `;
        }
    }
}

function loadRealMapMarkers() {
    console.log('Cargando marcadores reales del mapa...');
    
    try {
        // Limpiar marcadores existentes
        if (markers.length > 0) {
            markers.forEach(marker => {
                marker.setMap(null);
            });
            markers = [];
        }
        
        // Verificar si hay datos del mapa disponibles
        if (!backendData.datosMapa || backendData.datosMapa.length === 0) {
            console.warn('No hay datos de mapa disponibles, usando marcadores de ejemplo');
            addSampleMarkers();
            return;
        }
        
        console.log('Datos del mapa encontrados:', backendData.datosMapa.length, 'ubicaciones');
        
        // Mostrar indicador de progreso
        const mapContainer = document.getElementById('mapaColombia');
        if (mapContainer) {
            const progressDiv = document.createElement('div');
            progressDiv.id = 'mapProgress';
            progressDiv.className = 'position-absolute top-0 start-0 w-100 p-2';
            progressDiv.style.zIndex = '1000';
            progressDiv.innerHTML = `
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                </div>
                <small class="text-muted">Cargando marcadores: 0/${backendData.datosMapa.length}</small>
            `;
            mapContainer.appendChild(progressDiv);
        }
        
        // Cargar marcadores en lotes para mejor rendimiento
        const batchSize = 15;
        let currentIndex = 0;
        
        function loadBatch() {
            const endIndex = Math.min(currentIndex + batchSize, backendData.datosMapa.length);
            
            for (let i = currentIndex; i < endIndex; i++) {
                const item = backendData.datosMapa[i];
                
                if (item.geolocation && item.municipio) {
                    try {
                        // Parsear coordenadas (asumiendo formato "lat,lng")
                        const coords = item.geolocation.split(',').map(coord => parseFloat(coord.trim()));
                        
                        if (coords.length === 2 && !isNaN(coords[0]) && !isNaN(coords[1])) {
                            const [lat, lng] = coords;
                            const count = item.total || 1;
                            
                            // Crear marcador de Google Maps
                            const marker = new google.maps.Marker({
                                position: { lat, lng },
                                map: map,
                                title: `${item.municipio.municipioNOMBREOFICIAL || 'Sin nombre'} - ${count.toLocaleString()} unidades`,
                                icon: {
                                    path: google.maps.SymbolPath.CIRCLE,
                                    scale: Math.max(8, Math.min(25, Math.sqrt(count) * 1.5)),
                                    fillColor: getColorByCount(count),
                                    fillOpacity: 0.8,
                                    strokeColor: '#ffffff',
                                    strokeWeight: 2
                                }
                            });
                            
                            // Agregar evento de clic
                            marker.addListener('click', function() {
                                const content = `
                                    <div style="min-width: 250px; padding: 10px;">
                                        <h6 style="color: #667eea; margin-bottom: 15px; border-bottom: 2px solid #667eea; padding-bottom: 8px;">
                                            <i class="fas fa-map-marker-alt me-2"></i>${item.municipio.municipioNOMBREOFICIAL || 'Sin nombre'}
                                        </h6>
                                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                                <i class="fas fa-building me-2" style="color: #667eea;"></i>
                                                <strong>Unidades Productivas:</strong>
                                            </div>
                                            <div style="font-size: 1.2em; color: #667eea; text-align: center;">
                                                ${count.toLocaleString()}
                                            </div>
                                        </div>
                                        <div style="font-size: 0.9em; color: #6c757d; text-align: center;">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Coordenadas: ${lat.toFixed(4)}, ${lng.toFixed(4)}
                                        </div>
                                    </div>
                                `;
                                
                                infoWindow.setContent(content);
                                infoWindow.open(map, marker);
                            });
                            
                            // Agregar tooltip al hacer hover
                            marker.addListener('mouseover', function() {
                                const tooltip = document.createElement('div');
                                tooltip.className = 'google-map-tooltip';
                                tooltip.innerHTML = `
                                    <div style="text-align: center; padding: 8px;">
                                        <strong>${item.municipio.municipioNOMBREOFICIAL || 'Sin nombre'}</strong><br>
                                        <span style="color: #667eea;">${count.toLocaleString()} unidades</span>
                                    </div>
                                `;
                                document.body.appendChild(tooltip);
                                
                                // Posicionar tooltip
                                const rect = marker.getIcon().path.getBoundingClientRect();
                                tooltip.style.position = 'absolute';
                                tooltip.style.left = rect.left + 'px';
                                tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
                                tooltip.style.zIndex = '1000';
                                
                                marker.tooltip = tooltip;
                            });
                            
                            marker.addListener('mouseout', function() {
                                if (marker.tooltip) {
                                    marker.tooltip.remove();
                                    marker.tooltip = null;
                                }
                            });
                            
                            markers.push(marker);
                        }
                    } catch (error) {
                        console.warn('Error al procesar marcador:', item, error);
                    }
                }
            }
            
            // Actualizar progreso
            currentIndex = endIndex;
            if (mapContainer && document.getElementById('mapProgress')) {
                const progressBar = document.querySelector('#mapProgress .progress-bar');
                const progressText = document.querySelector('#mapProgress small');
                if (progressBar && progressText) {
                    const percentage = (currentIndex / backendData.datosMapa.length) * 100;
                    progressBar.style.width = percentage + '%';
                    progressText.textContent = `Cargando marcadores: ${currentIndex}/${backendData.datosMapa.length}`;
                }
            }
            
            // Continuar con el siguiente lote si hay más
            if (currentIndex < backendData.datosMapa.length) {
                setTimeout(loadBatch, 30); // Pausa de 30ms entre lotes
            } else {
                // Completado
                console.log('Todos los marcadores cargados:', markers.length);
                if (mapContainer && document.getElementById('mapProgress')) {
                    document.getElementById('mapProgress').remove();
                }
                
                // Ajustar vista del mapa si hay marcadores
                if (markers.length > 0) {
                    const bounds = new google.maps.LatLngBounds();
                    markers.forEach(marker => {
                        bounds.extend(marker.getPosition());
                    });
                    map.fitBounds(bounds);
                    
                    // Ajustar zoom si es muy cercano
                    google.maps.event.addListenerOnce(map, 'bounds_changed', function() {
                        if (map.getZoom() > 15) {
                            map.setZoom(15);
                        }
                    });
                }
            }
        }
        
        // Iniciar carga por lotes
        loadBatch();
        
    } catch (error) {
        console.error('Error al cargar marcadores del mapa:', error);
        // Fallback a marcadores de ejemplo
        addSampleMarkers();
    }
}

function getColorByCount(count) {
    if (count > 1000) return '#e74c3c';
    if (count > 500) return '#f39c12';
    if (count > 200) return '#f1c40f';
    if (count > 100) return '#2ecc71';
    return '#3498db';
}

function cambiarVistaMapa(tipo) {
    console.log('Cambiando vista del mapa a:', tipo);
    
    if (!map) return;
    
    try {
        if (tipo === 'satelite') {
            map.setMapTypeId(google.maps.MapTypeId.SATELLITE);
            currentMapType = 'satellite';
        } else {
            map.setMapTypeId(google.maps.MapTypeId.ROADMAP);
            currentMapType = 'roadmap';
        }
        
        console.log('Vista del mapa cambiada a:', tipo);
    } catch (error) {
        console.error('Error al cambiar vista del mapa:', error);
    }
}

function zoomToColombia() {
    if (map) {
        map.setCenter({ lat: 4.5709, lng: -74.2973 });
        map.setZoom(6);
        console.log('Zoom a Colombia aplicado');
    }
}

// ===== FUNCIONES DE GRÁFICOS =====

function createPieChart(canvasId, data) {
    console.log('Creando gráfico de pastel:', canvasId, data);
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error('Canvas no encontrado:', canvasId);
        return;
    }
    
    try {
        const chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: data.backgroundColor,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
        console.log('Gráfico de pastel creado exitosamente:', canvasId);
        return chart;
    } catch (error) {
        console.error('Error al crear gráfico de pastel:', canvasId, error);
        throw error;
    }
}

function createDoughnutChart(canvasId, data) {
    console.log('Creando gráfico de dona:', canvasId, data);
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error('Canvas no encontrado:', canvasId);
        return;
    }
    
    try {
        // Calcular porcentajes
        const total = data.data.reduce((sum, value) => sum + value, 0);
        const percentages = data.data.map(value => ((value / total) * 100).toFixed(0));
        
        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: data.backgroundColor,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const percentage = percentages[context.dataIndex];
                                return `${label}: ${percentage}%`;
                            }
                        }
                    }
                }
            }
        });
        console.log('Gráfico de dona creado exitosamente:', canvasId);
        return chart;
    } catch (error) {
        console.error('Error al crear gráfico de dona:', canvasId, error);
        throw error;
    }
}

function createBarChart(canvasId, data) {
    console.log('Creando gráfico de barras:', canvasId, data);
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error('Canvas no encontrado:', canvasId);
        return;
    }
    
    try {
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Cantidad',
                    data: data.data,
                    backgroundColor: data.backgroundColor,
                    borderColor: data.backgroundColor,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        console.log('Gráfico de barras creado exitosamente:', canvasId);
        return chart;
    } catch (error) {
        console.error('Error al crear gráfico de barras:', canvasId, error);
        throw error;
    }
}

function createLineChart(canvasId, data) {
    console.log('Creando gráfico de líneas:', canvasId, data);
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error('Canvas no encontrado:', canvasId);
        return;
    }
    
    try {
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Registros',
                    data: data.data,
                    backgroundColor: data.backgroundColor + '20',
                    borderColor: data.backgroundColor,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        console.log('Gráfico de líneas creado exitosamente:', canvasId);
        return chart;
    } catch (error) {
        console.error('Error al crear gráfico de líneas:', canvasId, error);
        throw error;
    }
}

// ===== FUNCIONES DE FILTROS Y UTILIDADES =====

function filtrarPorDepartamento(departamentoId) {
    showLoading();
    const url = new URL(window.location);
    if (departamentoId) {
        url.searchParams.set('departamento_id', departamentoId);
    } else {
        url.searchParams.delete('departamento_id');
    }
    window.location.href = url.toString();
}

function filtrarPorSector(sectorId) {
    showLoading();
    const url = new URL(window.location);
    if (sectorId) {
        url.searchParams.set('sector_id', sectorId);
    } else {
        url.searchParams.delete('sector_id');
    }
    window.location.href = url.toString();
}

function filtrarPorEtapa(etapaId) {
    showLoading();
    const url = new URL(window.location);
    if (etapaId) {
        url.searchParams.set('etapa_id', etapaId);
    } else {
        url.searchParams.delete('etapa_id');
    }
    window.location.href = url.toString();
}

function cambiarPeriodo(dias) {
    if (dias) {
        showLoading();
        const fechaHasta = new Date();
        const fechaDesde = new Date();
        fechaDesde.setDate(fechaDesde.getDate() - parseInt(dias));
        
        const url = new URL(window.location);
        url.searchParams.set('fecha_desde', fechaDesde.toISOString().split('T')[0]);
        url.searchParams.set('fecha_hasta', fechaHasta.toISOString().split('T')[0]);
        window.location.href = url.toString();
    }
}

function resetFilters() {
    showLoading();
    window.location.href = window.location.pathname;
}

function refreshDashboard() {
    console.log('Refrescando dashboard...');
    showLoading();
    window.location.reload();
}

function togglePerformanceMode() {
    DashboardConfig.performanceMode = !DashboardConfig.performanceMode;
    const modeText = DashboardConfig.performanceMode ? 'Rápido' : 'Normal';
    const modeElement = document.getElementById('performanceMode');
    if (modeElement) {
        modeElement.textContent = modeText;
    }
    
    if (DashboardConfig.performanceMode) {
        DashboardConfig.chartAnimationDuration = 300;
        DashboardConfig.maxDataPoints = 10;
        document.body.classList.add('performance-mode');
    } else {
        DashboardConfig.chartAnimationDuration = 1000;
        DashboardConfig.maxDataPoints = 20;
        document.body.classList.remove('performance-mode');
    }
}

function cambiarPeriodoGrafico(periodo) {
    console.log('Cambiando periodo del gráfico a:', periodo, 'meses');
    
    try {
        // Actualizar estado visual de los botones
        document.querySelectorAll('.chart-controls .btn').forEach(btn => {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-primary');
        });
        
        // Marcar el botón seleccionado
        event.target.classList.remove('btn-outline-primary');
        event.target.classList.add('btn-primary');
        
        // Filtrar datos según el período seleccionado
        if (backendData.evolucionTemporal && backendData.evolucionTemporal.length > 0) {
            let datosFiltrados = [];
            const meses = parseInt(periodo);
            
            if (meses === 12) {
                datosFiltrados = backendData.evolucionTemporal;
            } else {
                // Tomar solo los últimos N meses
                datosFiltrados = backendData.evolucionTemporal.slice(-meses);
            }
            
            // Actualizar el gráfico con los datos filtrados
            const evolucionData = {
                labels: datosFiltrados.map(item => item.label),
                data: datosFiltrados.map(item => item.total),
                backgroundColor: '#ffc107'
            };
            
            // Destruir el gráfico existente si existe
            if (window.evolucionTemporalChart) {
                window.evolucionTemporalChart.destroy();
            }
            
            // Crear nuevo gráfico
            window.evolucionTemporalChart = createLineChart('evolucionTemporalChart', evolucionData);
            
            console.log(`Gráfico actualizado con ${datosFiltrados.length} meses de datos`);
        } else {
            console.warn('No hay datos de evolución temporal disponibles');
        }
        
        // Limpiar campos de fecha
        document.getElementById('fechaDesde').value = '';
        document.getElementById('fechaHasta').value = '';
        
    } catch (error) {
        console.error('Error al cambiar período del gráfico:', error);
    }
}

function aplicarRangoFechas() {
    const fechaDesde = document.getElementById('fechaDesde').value;
    const fechaHasta = document.getElementById('fechaHasta').value;
    
    if (!fechaDesde || !fechaHasta) {
        alert('Por favor selecciona ambas fechas');
        return;
    }
    
    if (fechaDesde > fechaHasta) {
        alert('La fecha de inicio debe ser anterior a la fecha final');
        return;
    }
    
    console.log('Aplicando rango de fechas:', fechaDesde, 'a', fechaHasta);
    
    try {
        // Resetear estado de botones de período
        document.querySelectorAll('.chart-controls .btn').forEach(btn => {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-primary');
        });
        
        // Filtrar datos por rango de fechas
        if (backendData.evolucionTemporal && backendData.evolucionTemporal.length > 0) {
            const datosFiltrados = backendData.evolucionTemporal.filter(item => {
                // Asumiendo que item.label contiene la fecha en formato 'MMM YYYY'
                const itemDate = new Date(item.label + ' 01');
                const desde = new Date(fechaDesde);
                const hasta = new Date(fechaHasta);
                
                return itemDate >= desde && itemDate <= hasta;
            });
            
            if (datosFiltrados.length > 0) {
                // Actualizar el gráfico con los datos filtrados
                const evolucionData = {
                    labels: datosFiltrados.map(item => item.label),
                    data: datosFiltrados.map(item => item.total),
                    backgroundColor: '#ffc107'
                };
                
                // Destruir el gráfico existente si existe
                if (window.evolucionTemporalChart) {
                    window.evolucionTemporalChart.destroy();
                }
                
                // Crear nuevo gráfico
                window.evolucionTemporalChart = createLineChart('evolucionTemporalChart', evolucionData);
                
                console.log(`Gráfico actualizado con ${datosFiltrados.length} meses de datos filtrados`);
            } else {
                alert('No hay datos disponibles para el rango de fechas seleccionado');
            }
        } else {
            console.warn('No hay datos de evolución temporal disponibles');
        }
        
    } catch (error) {
        console.error('Error al aplicar rango de fechas:', error);
        alert('Error al filtrar por fechas: ' + error.message);
    }
}

// ===== FUNCIONES DE UTILIDAD =====

// Función para mostrar loading
function showLoading() {
    const loadingIndicator = document.getElementById('loadingIndicator');
    if (loadingIndicator) {
        loadingIndicator.style.display = 'flex';
    }
}

// Función para ocultar loading
function hideLoading() {
    const loadingIndicator = document.getElementById('loadingIndicator');
    if (loadingIndicator) {
        loadingIndicator.style.display = 'none';
    }
}

// Función para actualizar última actualización
function updateLastUpdateTime() {
    const lastUpdateElement = document.getElementById('lastUpdate');
    if (lastUpdateElement) {
        const now = new Date();
        lastUpdateElement.textContent = now.toLocaleTimeString('es-ES');
    }
}

// Función para configurar event listeners
function setupEventListeners() {
    console.log('=== CONFIGURANDO EVENT LISTENERS ===');
    
    // Actualizar municipios cuando cambie el departamento
    const modalDepartamento = document.getElementById('modalDepartamento');
    if (modalDepartamento) {
        modalDepartamento.addEventListener('change', function() {
            const departamentoId = this.value;
            const municipioSelect = document.getElementById('modalMunicipio');
            if (municipioSelect) {
                municipioSelect.value = '';
            }
        });
    }

    // Configurar filtros avanzados
    const filtrosForm = document.getElementById('filtrosForm');
    if (filtrosForm) {
        filtrosForm.addEventListener('submit', function(e) {
            e.preventDefault();
            showLoading();
            this.submit();
        });
    }
    
    console.log('Llamando a setupChartToggleListeners...');
    // Configurar event listeners para alternancia de gráficos
    setupChartToggleListeners();
    console.log('Event listeners configurados completamente');
}

// Función para configurar event listeners de alternancia de gráficos
function setupChartToggleListeners() {
    console.log('Configurando event listeners para alternancia de gráficos...');
    
    // Event listeners para gráfico de sectores
    const sectoresDona = document.getElementById('sectoresDona');
    const sectoresBarra = document.getElementById('sectoresBarra');
    
    console.log('Elementos de sectores encontrados:', { sectoresDona, sectoresBarra });
    
    if (sectoresDona) {
        sectoresDona.addEventListener('change', function() {
            console.log('Botón Dona de sectores clickeado');
            toggleSectoresChart();
        });
        console.log('Event listener agregado a sectoresDona');
    } else {
        console.warn('Elemento sectoresDona no encontrado');
    }
    
    if (sectoresBarra) {
        sectoresBarra.addEventListener('change', function() {
            console.log('Botón Barra de sectores clickeado');
            toggleSectoresChart();
        });
        console.log('Event listener agregado a sectoresBarra');
    } else {
        console.warn('Elemento sectoresBarra no encontrado');
    }
    
    // Event listeners para gráfico de tamaños
    const tamanosDona = document.getElementById('tamanosDona');
    const tamanosBarra = document.getElementById('tamanosBarra');
    
    console.log('Elementos de tamaños encontrados:', { tamanosDona, tamanosBarra });
    
    if (tamanosDona) {
        tamanosDona.addEventListener('change', function() {
            console.log('Botón Dona de tamaños clickeado');
            toggleTamanosChart();
        });
        console.log('Event listener agregado a tamanosDona');
    } else {
        console.warn('Elemento tamanosDona no encontrado');
    }
    
    if (tamanosBarra) {
        tamanosBarra.addEventListener('change', function() {
            console.log('Botón Barra de tamaños clickeado');
            toggleTamanosChart();
        });
        console.log('Event listener agregado a tamanosBarra');
    } else {
        console.warn('Elemento tamanosBarra no encontrado');
    }
    
    console.log('Event listeners de alternancia configurados completamente');
}

// ===== INICIALIZACIÓN PRINCIPAL =====

document.addEventListener('DOMContentLoaded', async function() {
    console.log('=== DOM CONTENT LOADED ===');
    
    try {
        // Cargar Chart.js y Google Maps en paralelo
        const [chartJSLoaded, googleMapsLoaded] = await Promise.all([
            typeof Chart === 'undefined' ? loadChartJS() : Promise.resolve(),
            loadGoogleMaps()
        ]);
        
        console.log('Chart.js disponible:', typeof Chart !== 'undefined');
        console.log('Google Maps disponible:', typeof google !== 'undefined' && google.maps);
        
        // Inicializar dashboard
        initializeDashboard();
        
        // Inicializar gráficos si Chart.js está disponible
        if (typeof Chart !== 'undefined') {
            initializeCharts();
        }
        
        // Inicializar mapa si Google Maps está disponible
        if (typeof google !== 'undefined' && google.maps) {
            initializeMap();
        }
        
        // Verificación adicional de event listeners después de un breve retraso
        setTimeout(() => {
            console.log('=== VERIFICACIÓN ADICIONAL DE EVENT LISTENERS ===');
            console.log('Verificando elementos de alternancia...');
            
            const sectoresDona = document.getElementById('sectoresDona');
            const sectoresBarra = document.getElementById('sectoresBarra');
            const tamanosDona = document.getElementById('tamanosDona');
            const tamanosBarra = document.getElementById('tamanosBarra');
            
            console.log('Elementos encontrados:', {
                sectoresDona: !!sectoresDona,
                sectoresBarra: !!sectoresBarra,
                tamanosDona: !!tamanosDona,
                tamanosBarra: !!tamanosBarra
            });
            
            // Si los elementos no tienen event listeners, configurarlos manualmente
            if (sectoresDona && !sectoresDona.onchange) {
                console.log('Configurando event listener manualmente para sectoresDona');
                sectoresDona.addEventListener('change', toggleSectoresChart);
            }
            
            if (sectoresBarra && !sectoresBarra.onchange) {
                console.log('Configurando event listener manualmente para sectoresBarra');
                sectoresBarra.addEventListener('change', toggleSectoresChart);
            }
            
            if (tamanosDona && !tamanosDona.onchange) {
                console.log('Configurando event listener manualmente para tamanosDona');
                tamanosDona.addEventListener('change', toggleTamanosChart);
            }
            
            if (tamanosBarra && !tamanosBarra.onchange) {
                console.log('Configurando event listener manualmente para tamanosBarra');
                tamanosBarra.addEventListener('change', toggleTamanosChart);
            }
            
            console.log('Verificación de event listeners completada');
        }, 1000);
        
    } catch (error) {
        console.error('Error al cargar librerías:', error);
        alert('Error al cargar librerías: ' + error.message);
        // Continuar sin gráficos o mapa
        initializeDashboard();
    }
});

// Función callback para Google Maps
function initGoogleMaps() {
    console.log('Google Maps API cargada correctamente');
    // El mapa se inicializará automáticamente cuando se cargue la página
}

// ===== FUNCIONES ADICIONALES NECESARIAS =====

function updateChartContent() {
    console.log('Actualizando contenido de gráficos con datos reales...');
    
    try {
        // Actualizar contenido de proporciones (TODOS los municipios para 100% real)
        if (backendData.porMunicipiosCompletos && backendData.porMunicipiosCompletos.length > 0) {
            const totalUnidades = backendData.totalUnidades || backendData.porMunicipiosCompletos.reduce((sum, item) => sum + item.total, 0);
            
            // Ya no necesitamos actualizar proporcionesContent, ahora usamos la tabla
            // La tabla se actualiza a través de updateProporcionesTable()
            console.log('Datos de proporciones disponibles, tabla se actualizará automáticamente');
        }
        
        // Actualizar contenido de tamaños
        if (backendData.porTamanos && backendData.porTamanos.length > 0) {
            const totalTamanos = backendData.porTamanos.reduce((sum, item) => sum + item.total, 0);
            
            let tamanosHTML = '';
            backendData.porTamanos.forEach(item => {
                const porcentaje = Math.round((item.total / totalTamanos) * 100);
                const nombre = item.tamano?.tamanoNOMBRE || `Tamaño ${item.tamano_id}`;
                tamanosHTML += `<p><strong>${nombre}:</strong> ${porcentaje}% (${item.total.toLocaleString()} unidades)</p>`;
            });
            
            const tamanosContent = document.getElementById('tamanosContent');
            if (tamanosContent) {
                tamanosContent.innerHTML = tamanosHTML;
            }
        }

        // Actualizar contenido de sectores
        if (backendData.porSectores && backendData.porSectores.length > 0) {
            const totalSectores = backendData.porSectores.reduce((sum, item) => sum + item.total, 0);
            
            let sectoresHTML = '';
            backendData.porSectores.forEach(item => {
                const porcentaje = Math.round((item.total / totalSectores) * 100);
                const nombre = item.sector?.sectorNOMBRE || `Sector ${item.sector_id}`;
                sectoresHTML += `<p><strong>${nombre}:</strong> ${porcentaje}% (${item.total.toLocaleString()} unidades)</p>`;
            });
            
            const sectoresContent = document.getElementById('sectoresContent');
            if (sectoresContent) {
                sectoresContent.innerHTML = sectoresHTML;
            }
        }
        
        // Actualizar tendencia de métricas después de actualizar gráficos
        updateMetricTrend();
        
        console.log('Contenido de gráficos actualizado exitosamente');
    } catch (error) {
        console.error('Error al actualizar contenido de gráficos:', error);
    }
}

// Función para actualizar el contenido del gráfico de proporciones
function updateProporcionesContent() {
    console.log('Actualizando contenido de proporciones con todos los municipios...');
    
    try {
        if (backendData.porMunicipiosCompletos && backendData.porMunicipiosCompletos.length > 0) {
            const totalUnidades = backendData.totalUnidades || backendData.porMunicipiosCompletos.reduce((sum, item) => sum + item.total, 0);
            
            // Actualizar la tabla de proporciones
            updateProporcionesTable();
            
            // Mostrar botón para ver más
            const btnVerMas = document.getElementById('btnVerMasProporciones');
            if (btnVerMas) {
                btnVerMas.style.display = 'inline-block';
            }
            
            console.log('Contenido de proporciones actualizado exitosamente');
        } else {
            console.warn('No hay datos completos de municipios para proporciones');
        }
    } catch (error) {
        console.error('Error al actualizar contenido de proporciones:', error);
    }
}

// Función de fallback para actualizar contenido de proporciones con datos limitados
function updateProporcionesContentFallback() {
    console.log('Actualizando contenido de proporciones con datos limitados (fallback)...');
    
    try {
        if (backendData.porMunicipios && backendData.porMunicipios.length > 0) {
            const totalUnidades = backendData.totalUnidades || backendData.porMunicipios.reduce((sum, item) => sum + item.total, 0);
            
            // Actualizar la tabla de proporciones con datos limitados
            updateProporcionesTableFallback();
            
            // No mostrar botón de ver más para datos limitados
            const btnVerMas = document.getElementById('btnVerMasProporciones');
            if (btnVerMas) {
                btnVerMas.style.display = 'none';
            }
            
            console.log('Contenido de proporciones (fallback) actualizado exitosamente');
        } else {
            console.warn('No hay datos de municipios para proporciones (fallback)');
        }
    } catch (error) {
        console.error('Error al actualizar contenido de proporciones (fallback):', error);
    }
}

// Función para mostrar resumen de proporciones (top 5 + otros)
function mostrarResumenProporciones(municipios, totalUnidades) {
    // Esta función ahora actualiza la tabla de proporciones
    console.log('Actualizando tabla de proporciones con resumen...');
    updateProporcionesTable();
}

// Función para actualizar la tabla de proporciones con datos limitados (fallback)
function updateProporcionesTableFallback() {
    console.log('Actualizando tabla de proporciones con datos limitados (fallback)...');
    
    try {
        const tableBody = document.getElementById('proporcionesTableBody');
        if (!tableBody) {
            console.error('Tabla de proporciones no encontrada');
            return;
        }
        
        if (backendData.porMunicipios && backendData.porMunicipios.length > 0) {
            console.log('Datos de proporciones limitados encontrados:', backendData.porMunicipios.length, 'registros');
            
            // Tomar solo los primeros 8 municipios para la tabla
            const topMunicipios = backendData.porMunicipios.slice(0, 8);
            const totalUnidades = backendData.totalUnidades || backendData.porMunicipios.reduce((sum, item) => sum + item.total, 0);
            
            console.log('Top municipios proporciones (fallback):', topMunicipios);
            console.log('Total unidades:', totalUnidades);
            
            let tableHTML = '';
            
            // Agregar filas de municipios
            topMunicipios.forEach((item, index) => {
                console.log(`Procesando municipio proporción ${index} (fallback):`, item);
                
                const nombre = item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`;
                const cantidad = item.total;
                const porcentaje = Math.round((cantidad / totalUnidades) * 100);
                const color = '#' + Math.random().toString(16).substr(2, 6); // Color aleatorio para el punto
                
                console.log(`Municipio: ${nombre}, Cantidad: ${cantidad}, Porcentaje: ${porcentaje}%`);
                
                tableHTML += `
                    <tr>
                        <td>
                            <div class="municip-info">
                                <span class="municip-dot" style="background-color: ${color}"></span>
                                ${nombre}
                            </div>
                        </td>
                        <td><strong>${porcentaje}%</strong></td>
                        <td><strong>${cantidad.toLocaleString()}</strong></td>
                    </tr>
                `;
            });
            
            // Agregar fila de total
            tableHTML += `
                <tr class="table-info">
                    <td><strong>Total del Sistema</strong></td>
                    <td><strong>100%</strong></td>
                    <td><strong>${totalUnidades.toLocaleString()}</strong></td>
                </tr>
            `;
            
            tableBody.innerHTML = tableHTML;
            
            // Agregar comentario explicativo debajo de la tabla
            const comentarioHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted small">
                        <em>* Top 8 municipios por porcentaje de unidades productivas</em><br>
                        <em>** Total incluye todas las unidades del sistema</em>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', comentarioHTML);
            
            console.log('Tabla de proporciones (fallback) actualizada exitosamente');
        } else {
            console.warn('No hay datos de proporciones para mostrar en la tabla (fallback)');
            console.log('backendData.porMunicipios es:', backendData.porMunicipios);
            
            tableBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted">No hay datos disponibles</td>
                </tr>
            `;
        }
    } catch (error) {
        console.error('Error al actualizar tabla de proporciones (fallback):', error);
        console.error('Error completo:', error.stack);
        
        const tableBody = document.getElementById('proporcionesTableBody');
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-danger">Error al cargar datos: ${error.message}</td>
                </tr>
            `;
        }
    }
}

// Función para actualizar la tabla de proporciones
function updateProporcionesTable() {
    console.log('Actualizando tabla de proporciones con datos reales...');
    
    try {
        const tableBody = document.getElementById('proporcionesTableBody');
        if (!tableBody) {
            console.error('Tabla de proporciones no encontrada');
            return;
        }
        
        if (backendData.porMunicipiosCompletos && backendData.porMunicipiosCompletos.length > 0) {
            console.log('Datos de proporciones encontrados:', backendData.porMunicipiosCompletos.length, 'registros');
            
            // Tomar solo los primeros 8 municipios para la tabla (como en la imagen)
            const topMunicipios = backendData.porMunicipiosCompletos.slice(0, 8);
            const totalUnidades = backendData.totalUnidades || backendData.porMunicipiosCompletos.reduce((sum, item) => sum + item.total, 0);
            
            console.log('Top municipios proporciones:', topMunicipios);
            console.log('Total unidades:', totalUnidades);
            
            let tableHTML = '';
            
            // Agregar filas de municipios
            topMunicipios.forEach((item, index) => {
                console.log(`Procesando municipio proporción ${index}:`, item);
                
                const nombre = item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`;
                const cantidad = item.total;
                const porcentaje = Math.round((cantidad / totalUnidades) * 100);
                const color = '#' + Math.random().toString(16).substr(2, 6); // Color aleatorio para el punto
                
                console.log(`Municipio: ${nombre}, Cantidad: ${cantidad}, Porcentaje: ${porcentaje}%`);
                
                tableHTML += `
                    <tr>
                        <td>
                            <div class="municip-info">
                                <span class="municip-dot" style="background-color: ${color}"></span>
                                ${nombre}
                            </div>
                        </td>
                        <td><strong>${porcentaje}%</strong></td>
                        <td><strong>${cantidad.toLocaleString()}</strong></td>
                    </tr>
                `;
            });
            
            // Agregar fila de total
            tableHTML += `
                <tr class="table-info">
                    <td><strong>Total del Sistema</strong></td>
                    <td><strong>100%</strong></td>
                    <td><strong>${totalUnidades.toLocaleString()}</strong></td>
                </tr>
            `;
            
            tableBody.innerHTML = tableHTML;
            
            // Agregar comentario explicativo debajo de la tabla
            const comentarioHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted small">
                        <em>* Top 8 municipios por porcentaje de unidades productivas</em><br>
                        <em>** Total incluye todas las unidades del sistema</em>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', comentarioHTML);
            
            console.log('Tabla de proporciones actualizada exitosamente');
        } else {
            console.warn('No hay datos de proporciones para mostrar en la tabla');
            console.log('backendData.porMunicipiosCompletos es:', backendData.porMunicipiosCompletos);
            
            tableBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted">No hay datos disponibles</td>
                </tr>
            `;
        }
    } catch (error) {
        console.error('Error al actualizar tabla de proporciones:', error);
        console.error('Error completo:', error.stack);
        
        const tableBody = document.getElementById('proporcionesTableBody');
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-danger">Error al cargar datos: ${error.message}</td>
                </tr>
            `;
        }
    }
}

// Función para actualizar la tabla de municipios
function updateMunicipiosTable() {
    console.log('Actualizando tabla de municipios con datos reales...');
    
    try {
        const tableBody = document.getElementById('municipiosTableBody');
        if (!tableBody) {
            console.error('Tabla de municipios no encontrada');
            return;
        }
        
        if (backendData.porMunicipios && backendData.porMunicipios.length > 0) {
            console.log('Datos de municipios encontrados:', backendData.porMunicipios.length, 'registros');
            
            // Tomar solo los primeros 10 municipios para la tabla
            const topMunicipios = backendData.porMunicipios.slice(0, 10);
            // Usar el total real de todas las unidades, no solo de los top 10
            const totalUnidades = backendData.totalUnidades || dashboardData.totalUnidades || 1;
            
            console.log('Top municipios:', topMunicipios);
            console.log('Total unidades:', totalUnidades);
            
            let tableHTML = '';
            
            // Agregar filas de municipios
            topMunicipios.forEach((item, index) => {
                console.log(`Procesando municipio ${index}:`, item);
                
                const nombre = item.municipio?.municipioNOMBREOFICIAL || `Municipio ${item.municipality_id}`;
                const cantidad = item.total;
                const color = '#' + Math.random().toString(16).substr(2, 6); // Color aleatorio para el punto
                
                console.log(`Municipio: ${nombre}, Cantidad: ${cantidad}`);
                
                tableHTML += `
                    <tr>
                        <td>
                            <div class="municip-info">
                                <span class="municip-dot" style="background-color: ${color}"></span>
                                ${nombre}
                            </div>
                        </td>
                        <td><strong>${cantidad.toLocaleString()}</strong></td>
                    </tr>
                `;
            });
            
            // Agregar fila de total
            tableHTML += `
                <tr class="table-info">
                    <td><strong>Total Sistema</strong></td>
                    <td><strong>${totalUnidades.toLocaleString()}</strong></td>
                </tr>
            `;
            
            tableBody.innerHTML = tableHTML;
            
            // Agregar comentario explicativo debajo de la tabla
            const comentarioHTML = `
                <tr>
                    <td colspan="2" class="text-center text-muted small">
                        <em>* Top 10 municipios por cantidad de unidades productivas</em><br>
                        <em>** Total incluye todas las unidades del sistema</em>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', comentarioHTML);
            
            console.log('Tabla de municipios actualizada exitosamente');
        } else {
            console.warn('No hay datos de municipios para mostrar en la tabla');
            console.log('backendData.porMunicipios es:', backendData.porMunicipios);
            
            tableBody.innerHTML = `
                <tr>
                    <td colspan="2" class="text-center text-muted">No hay datos disponibles</td>
                </tr>
            `;
        }
    } catch (error) {
        console.error('Error al actualizar tabla de municipios:', error);
        console.error('Error completo:', error.stack);
        
        const tableBody = document.getElementById('municipiosTableBody');
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-danger">Error al cargar datos: ${error.message}</td>
                </tr>
            `;
        }
    }
}

// Función para actualizar la leyenda del gráfico de estado de diagnóstico
function updateEstadoDiagnosticoContent() {
    console.log('Actualizando contenido del estado de diagnóstico...');
    
    try {
        const contentDiv = document.getElementById('estadoDiagnosticoContent');
        if (!contentDiv) {
            console.error('Contenido de estado de diagnóstico no encontrado');
            return;
        }
        
        if (backendData.porEstadoDiagnostico && backendData.porEstadoDiagnostico.length > 0) {
            console.log('Datos de estado de diagnóstico encontrados:', backendData.porEstadoDiagnostico);
            
            // Calcular total para porcentajes
            const totalDiagnosticos = backendData.porEstadoDiagnostico.reduce((sum, item) => sum + item.total, 0);
            
            let contentHTML = '';
            backendData.porEstadoDiagnostico.forEach(item => {
                const estado = item.estado || 'Sin estado';
                const total = item.total || 0;
                const porcentaje = Math.round((total / totalDiagnosticos) * 100);
                
                // Determinar color del indicador según el estado
                let colorClass = 'text-muted';
                if (estado.toLowerCase().includes('terminado') || estado.toLowerCase().includes('completado')) {
                    colorClass = 'text-success';
                } else if (estado.toLowerCase().includes('pendiente') || estado.toLowerCase().includes('en proceso')) {
                    colorClass = 'text-warning';
                } else if (estado.toLowerCase().includes('cancelado') || estado.toLowerCase().includes('rechazado')) {
                    colorClass = 'text-danger';
                }
                
                contentHTML += `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="${colorClass}">
                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                            ${estado}
                        </span>
                        <span class="fw-bold">
                            ${total.toLocaleString()} 
                            <small class="text-muted">(${porcentaje}%)</small>
                        </span>
                    </div>
                `;
            });
            
            contentDiv.innerHTML = contentHTML;
            console.log('Contenido de estado de diagnóstico actualizado exitosamente');
        } else {
            console.warn('No hay datos de estado de diagnóstico disponibles');
            contentDiv.innerHTML = `
                <div class="text-center text-muted">
                    <p>No hay datos disponibles</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error al actualizar contenido de estado de diagnóstico:', error);
        const contentDiv = document.getElementById('estadoDiagnosticoContent');
        if (contentDiv) {
            contentDiv.innerHTML = `
                <div class="text-center text-danger">
                    <p>Error al cargar datos</p>
                </div>
            `;
        }
    }
}

// Función para actualizar la leyenda del gráfico de tipo de organización
function updateTipoOrganizacionContent() {
    console.log('Actualizando contenido del tipo de organización...');
    
    try {
        const contentDiv = document.getElementById('tipoOrganizacionContent');
        if (!contentDiv) {
            console.error('Contenido de tipo de organización no encontrado');
            return;
        }
        
        if (backendData.porTipoOrganizacion && backendData.porTipoOrganizacion.length > 0) {
            console.log('Datos de tipo de organización encontrados:', backendData.porTipoOrganizacion);
            
            let contentHTML = '';
            backendData.porTipoOrganizacion.forEach((item, index) => {
                const nombre = item.tipoPersona?.tipoPersonaNOMBRE || `Tipo ${item.tipopersona_id}`;
                const total = item.total || 0;
                
                contentHTML += `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">
                            <i class="fas fa-square me-1" style="color: #667eea; font-size: 8px;"></i>
                            ${nombre}
                        </span>
                        <span class="fw-bold">${total.toLocaleString()}</span>
                    </div>
                `;
            });
            
            contentDiv.innerHTML = contentHTML;
            console.log('Contenido de tipo de organización actualizado exitosamente');
        } else {
            console.warn('No hay datos de tipo de organización disponibles');
            contentDiv.innerHTML = `
                <div class="text-center text-muted">
                    <p>No hay datos disponibles</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error al actualizar contenido de tipo de organización:', error);
        const contentDiv = document.getElementById('tipoOrganizacionContent');
        if (contentDiv) {
            contentDiv.innerHTML = `
                <div class="text-center text-danger">
                    <p>Error al cargar datos</p>
                </div>
            `;
        }
    }
}

// Función para calcular y mostrar el porcentaje de crecimiento
function updateMetricTrend() {
    console.log('Calculando tendencia de métricas...');
    
    try {
        const trendElement = document.getElementById('metricTrend');
        if (!trendElement) {
            console.error('Elemento de tendencia no encontrado');
            return;
        }
        
        if (backendData.evolucionTemporal && backendData.evolucionTemporal.length >= 2) {
            // Obtener los últimos dos meses de datos
            const ultimosDatos = backendData.evolucionTemporal.slice(-2);
            const mesActual = ultimosDatos[ultimosDatos.length - 1];
            const mesAnterior = ultimosDatos[ultimosDatos.length - 2];
            
            if (mesActual && mesAnterior && mesActual.total && mesAnterior.total) {
                const totalActual = parseInt(mesActual.total);
                const totalAnterior = parseInt(mesAnterior.total);
                
                // Calcular porcentaje de cambio
                let porcentajeCambio = 0;
                let esIncremento = true;
                let badgeClass = 'bg-success';
                let iconClass = 'fa-arrow-up';
                
                if (totalAnterior > 0) {
                    porcentajeCambio = ((totalActual - totalAnterior) / totalAnterior) * 100;
                    esIncremento = porcentajeCambio >= 0;
                }
                
                // Determinar clase y icono según el cambio
                if (porcentajeCambio === 0) {
                    badgeClass = 'bg-secondary';
                    iconClass = 'fa-minus';
                } else if (porcentajeCambio < 0) {
                    badgeClass = 'bg-danger';
                    iconClass = 'fa-arrow-down';
                } else if (porcentajeCambio < 5) {
                    badgeClass = 'bg-warning';
                    iconClass = 'fa-arrow-up';
                } else {
                    badgeClass = 'bg-success';
                    iconClass = 'fa-arrow-up';
                }
                
                // Formatear el porcentaje
                const porcentajeFormateado = Math.abs(porcentajeCambio).toFixed(1);
                const signo = esIncremento ? '+' : '-';
                
                // Actualizar el HTML
                trendElement.innerHTML = `
                    <span class="badge ${badgeClass}">
                        <i class="fas ${iconClass} me-1"></i>${signo}${porcentajeFormateado}%
                    </span>
                    <span class="text-muted ms-2">vs mes anterior</span>
                `;
                
                console.log(`Tendencia calculada: ${signo}${porcentajeFormateado}% (${totalActual} vs ${totalAnterior})`);
                
            } else {
                // No hay datos suficientes
                trendElement.innerHTML = `
                    <span class="badge bg-info">
                        <i class="fas fa-info-circle me-1"></i>Sin datos previos
                    </span>
                    <span class="text-muted ms-2">vs mes anterior</span>
                `;
                console.log('No hay datos suficientes para calcular tendencia');
            }
            
        } else {
            // No hay datos de evolución temporal
            trendElement.innerHTML = `
                <span class="badge bg-info">
                    <i class="fas fa-info-circle me-1"></i>Sin datos de evolución
                </span>
                <span class="text-muted ms-2">vs mes anterior</span>
            `;
            console.log('No hay datos de evolución temporal disponibles');
        }
        
    } catch (error) {
        console.error('Error al calcular tendencia de métricas:', error);
        const trendElement = document.getElementById('metricTrend');
        if (trendElement) {
            trendElement.innerHTML = `
                <span class="badge bg-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>Error en cálculo
                </span>
                <span class="text-muted ms-2">vs mes anterior</span>
            `;
        }
    }
}

// Función para agregar marcadores de ejemplo al mapa
function addSampleMarkers() {
    console.log('Agregando marcadores de ejemplo al mapa...');
    
    try {
        // Coordenadas de ejemplo para Colombia
        const sampleData = [
            { lat: 4.7109, lng: -74.0721, nombre: 'Bogotá', count: 450 },
            { lat: 6.2442, lng: -75.5812, nombre: 'Medellín', count: 320 },
            { lat: 3.4516, lng: -76.5320, nombre: 'Cali', count: 280 },
            { lat: 10.9685, lng: -74.7813, nombre: 'Barranquilla', count: 180 },
            { lat: 10.3932, lng: -75.4792, nombre: 'Cartagena', count: 150 }
        ];
        
        sampleData.forEach(item => {
            const marker = new google.maps.Marker({
                position: { lat: item.lat, lng: item.lng },
                map: map,
                title: `${item.nombre} - ${item.count.toLocaleString()} unidades`,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: Math.max(8, Math.min(25, Math.sqrt(item.count) * 1.5)),
                    fillColor: getColorByCount(item.count),
                    fillOpacity: 0.8,
                    strokeColor: '#ffffff',
                    strokeWeight: 2
                }
            });
            
            markers.push(marker);
        });
        
        console.log('Marcadores de ejemplo agregados exitosamente');
    } catch (error) {
        console.error('Error al agregar marcadores de ejemplo:', error);
    }
}

// ===== FUNCIONES ADICIONALES QUE FALTABAN =====

// Función para limpiar caché de sectores
function clearSectoresCache() {
    console.log('Limpiando caché de sectores...');
    try {
        // Limpiar cualquier caché local si existe
        if (window.sectoresCache) {
            delete window.sectoresCache;
            console.log('Caché de sectores limpiado localmente');
        }
        
        // Llamar al backend para limpiar el cache
        fetch('/clear-sectores-cache')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Cache de sectores limpiado en el backend:', data.message);
                    // Recargar la página para obtener datos frescos
                    window.location.reload();
                } else {
                    console.error('Error al limpiar cache en el backend:', data.message);
                }
            })
            .catch(error => {
                console.error('Error al comunicarse con el backend:', error);
                // Intentar recargar la página de todos modos
                window.location.reload();
            });
        
        return true;
    } catch (error) {
        console.error('Error al limpiar caché de sectores:', error);
        return false;
    }
}

// Función para debug de sectores
function debugSectores() {
    console.log('=== DEBUG SECTORES ===');
    console.log('backendData.porSectores:', backendData.porSectores);
    console.log('sectoresCache:', window.sectoresCache);
    console.log('Total sectores:', backendData.porSectores ? backendData.porSectores.length : 'No disponible');
    
    if (backendData.porSectores && backendData.porSectores.length > 0) {
        backendData.porSectores.forEach((sector, index) => {
            console.log(`Sector ${index}:`, sector);
        });
    }
    console.log('=== FIN DEBUG SECTORES ===');
}

// Función para mostrar lista completa de proporciones
// COMENTADA: Esta función está duplicada en dashboard.blade.php
/*
function mostrarListaCompletaProporciones() {
    console.log('Mostrando lista completa de proporciones...');
    
    try {
        if (backendData.porMunicipiosCompletos && backendData.porMunicipiosCompletos.length > 0) {
            const totalUnidades = backendData.totalUnidades || backendData.porMunicipiosCompletos.reduce((sum, item) => sum + item.total, 0);
                    // Ya no necesitamos actualizar proporcionesContent, ahora usamos la tabla
        // La tabla ya muestra los datos principales, esta función se mantiene por compatibilidad
        console.log('Función mostrarListaCompletaProporciones llamada pero ya no se usa (reemplazada por tabla)');
        
        // Cambiar botón a "Ver menos" (aunque no se use)
        const btnVerMas = document.getElementById('btnVerMasProporciones');
        if (btnVerMas) {
            btnVerMas.textContent = 'Ver menos';
            btnVerMas.onclick = mostrarResumenProporciones;
        }
        
        console.log('Lista completa de proporciones mostrada (compatibilidad)');
        } else {
            console.warn('No hay datos completos de municipios para mostrar lista completa');
        }
    } catch (error) {
        console.error('Error al mostrar lista completa de proporciones:', error);
    }
}
*/

// Función para alternar entre vista resumida y completa de proporciones
// COMENTADA: Esta función está duplicada en dashboard.blade.php
/*
function toggleProporcionesCompletas() {
    console.log('Alternando vista de proporciones...');
    
    try {
        const btnVerMas = document.getElementById('btnVerMasProporciones');
        
        if (!btnVerMas) {
            console.error('Botón de proporciones no encontrado');
            return;
        }
        
        // Verificar si está mostrando la vista completa
        const isShowingComplete = btnVerMas.textContent.includes('menos');
        
        if (isShowingComplete) {
            // Cambiar a vista resumida (aunque ya no se use)
            btnVerMas.textContent = 'Ver Lista Completa';
            btnVerMas.onclick = mostrarListaCompletaProporciones;
        } else {
            // Cambiar a vista completa (aunque ya no se use)
            mostrarListaCompletaProporciones();
        }
        
        console.log('Vista de proporciones alternada exitosamente (compatibilidad)');
    } catch (error) {
        console.error('Error al alternar vista de proporciones:', error);
    }
}
*/

// Función para debug de proporciones
function debugProporciones() {
    console.log('=== DEBUG PROPORCIONES ===');
    console.log('backendData.porMunicipiosCompletos:', backendData.porMunicipiosCompletos);
    console.log('backendData.porMunicipios:', backendData.porMunicipios);
    console.log('Total unidades:', backendData.totalUnidades);
    
    if (backendData.porMunicipiosCompletos && backendData.porMunicipiosCompletos.length > 0) {
        console.log('Municipios completos encontrados:', backendData.porMunicipiosCompletos.length);
        backendData.porMunicipiosCompletos.forEach((municipio, index) => {
            console.log(`Municipio ${index}:`, municipio);
        });
    }
    
    if (backendData.porMunicipios && backendData.porMunicipios.length > 0) {
        console.log('Municipios limitados encontrados:', backendData.porMunicipios.length);
        backendData.porMunicipios.forEach((municipio, index) => {
            console.log(`Municipio limitado ${index}:`, municipio);
        });
    }
    console.log('=== FIN DEBUG PROPORCIONES ===');
}

// ===== FUNCIONES DE ALTERNANCIA DE GRÁFICOS =====

// Variables globales para los gráficos
let sectoresChart = null;
let tamanosChart = null;

// Función para alternar gráfico de sectores
function toggleSectoresChart() {
    console.log('=== FUNCIÓN toggleSectoresChart EJECUTADA ===');
    
    try {
        const checkedRadio = document.querySelector('input[name="sectoresChartType"]:checked');
        console.log('Radio button seleccionado:', checkedRadio);
        
        if (!checkedRadio) {
            console.error('No hay radio button seleccionado para sectores');
            return;
        }
        
        const chartType = checkedRadio.value;
        console.log('Tipo de gráfico seleccionado:', chartType);
        
        const canvas = document.getElementById('sectoresChart');
        console.log('Canvas de sectores encontrado:', canvas);
        
        if (!canvas) {
            console.error('Canvas de sectores no encontrado');
            return;
        }
        
        // Destruir gráfico existente si existe
        if (sectoresChart) {
            console.log('Destruyendo gráfico existente de sectores');
            sectoresChart.destroy();
        }
        
        // Obtener datos de sectores
        const sectoresData = backendData.porSectores || {
            labels: ['Servicios', 'Manufactura', 'Comercio'],
            data: [1118, 930, 794],
            backgroundColor: ['#e91e63', '#ff9800', '#00bcd4']
        };
        
        console.log('Datos de sectores a usar:', sectoresData);
        
        if (chartType === 'dona') {
            console.log('Creando gráfico de dona para sectores');
            // Crear gráfico de dona
            sectoresChart = createDoughnutChart('sectoresChart', sectoresData);
            updateSectoresContent(sectoresData, true); // true = mostrar porcentajes
        } else {
            console.log('Creando gráfico de barras para sectores');
            // Crear gráfico de barras
            sectoresChart = createBarChart('sectoresChart', sectoresData);
            updateSectoresContent(sectoresData, false); // false = mostrar conteos
        }
        
        console.log('Gráfico de sectores alternado exitosamente a:', chartType);
    } catch (error) {
        console.error('Error al alternar gráfico de sectores:', error);
        console.error('Stack trace:', error.stack);
    }
}

// Función para alternar gráfico de tamaños
function toggleTamanosChart() {
    console.log('=== FUNCIÓN toggleTamanosChart EJECUTADA ===');
    
    try {
        const checkedRadio = document.querySelector('input[name="tamanosChartType"]:checked');
        console.log('Radio button seleccionado:', checkedRadio);
        
        if (!checkedRadio) {
            console.error('No hay radio button seleccionado para tamaños');
            return;
        }
        
        const chartType = checkedRadio.value;
        console.log('Tipo de gráfico seleccionado:', chartType);
        
        const canvas = document.getElementById('tamanosChart');
        console.log('Canvas de tamaños encontrado:', canvas);
        
        if (!canvas) {
            console.error('Canvas de tamaños no encontrado');
            return;
        }
        
        // Destruir gráfico existente si existe
        if (tamanosChart) {
            console.log('Destruyendo gráfico existente de tamaños');
            tamanosChart.destroy();
        }
        
        // Obtener datos de tamaños
        const tamanosData = backendData.porTamanos || {
            labels: ['Micro', 'Pequeña', 'Mediana', 'Gran empresa'],
            data: [2517, 156, 66, 40],
            backgroundColor: '#17a2b8'
        };
        
        console.log('Datos de tamaños a usar:', tamanosData);
        
        if (chartType === 'dona') {
            console.log('Creando gráfico de dona para tamaños');
            // Crear gráfico de dona
            tamanosChart = createDoughnutChart('tamanosChart', tamanosData);
            updateTamanosContent(tamanosData, true); // true = mostrar porcentajes
        } else {
            console.log('Creando gráfico de barras para tamaños');
            // Crear gráfico de barras
            tamanosChart = createBarChart('tamanosChart', tamanosData);
            updateTamanosContent(tamanosData, false); // false = mostrar conteos
        }
        
        console.log('Gráfico de tamaños alternado exitosamente a:', chartType);
    } catch (error) {
        console.error('Error al alternar gráfico de tamaños:', error);
        console.error('Stack trace:', error.stack);
    }
}

// Función para actualizar contenido de sectores
function updateSectoresContent(data, showPercentages = true) {
    try {
        const contentDiv = document.getElementById('sectoresContent');
        if (!contentDiv) return;
        
        const total = data.data.reduce((sum, value) => sum + value, 0);
        
        let html = '<div class="chart-legend">';
        data.labels.forEach((label, index) => {
            const value = data.data[index];
            const percentage = ((value / total) * 100).toFixed(0);
            
            if (showPercentages) {
                html += `
                    <div class="legend-item d-flex justify-content-between align-items-center mb-2">
                        <span class="d-flex align-items-center">
                            <span class="legend-color me-2" style="width: 12px; height: 12px; background-color: ${data.backgroundColor[index] || data.backgroundColor}; border-radius: 50%;"></span>
                            <strong>${label}:</strong>
                        </span>
                        <span class="text-muted">${percentage}% (${value} unidades)</span>
                    </div>
                `;
            } else {
                html += `
                    <div class="legend-item d-flex justify-content-between align-items-center mb-2">
                        <span class="d-flex align-items-center">
                            <span class="legend-color me-2" style="width: 12px; height: 12px; background-color: ${data.backgroundColor[index] || data.backgroundColor}; border-radius: 50%;"></span>
                            <strong>${label}:</strong>
                        </span>
                        <span class="text-muted">${value} unidades</span>
                    </div>
                `;
            }
        });
        html += '</div>';
        
        contentDiv.innerHTML = html;
    } catch (error) {
        console.error('Error al actualizar contenido de sectores:', error);
    }
}

// Función para actualizar contenido de tamaños
function updateTamanosContent(data, showPercentages = true) {
    try {
        const contentDiv = document.getElementById('tamanosContent');
        if (!contentDiv) return;
        
        const total = data.data.reduce((sum, value) => sum + value, 0);
        
        let html = '<div class="chart-legend">';
        data.labels.forEach((label, index) => {
            const value = data.data[index];
            const percentage = ((value / total) * 100).toFixed(0);
            
            if (showPercentages) {
                html += `
                    <div class="legend-item d-flex justify-content-between align-items-center mb-2">
                        <span class="d-flex align-items-center">
                            <span class="legend-color me-2" style="width: 12px; height: 12px; background-color: ${data.backgroundColor[index] || data.backgroundColor}; border-radius: 50%;"></span>
                            <strong>${label}:</strong>
                        </span>
                        <span class="text-muted">${percentage}% (${value} unidades)</span>
                    </div>
                `;
            } else {
                html += `
                    <div class="legend-item d-flex justify-content-between align-items-center mb-2">
                        <span class="d-flex align-items-center">
                            <span class="legend-color me-2" style="width: 12px; height: 12px; background-color: ${data.backgroundColor[index] || data.backgroundColor}; border-radius: 50%;"></span>
                            <strong>${label}:</strong>
                        </span>
                        <span class="text-muted">${value} unidades</span>
                    </div>
                `;
            }
        });
        html += '</div>';
        
        contentDiv.innerHTML = html;
    } catch (error) {
        console.error('Error al actualizar contenido de tamaños:', error);
    }
}

// Hacer todas las funciones disponibles globalmente
window.cambiarVistaMapa = cambiarVistaMapa;
window.zoomToColombia = zoomToColombia;
window.filtrarPorDepartamento = filtrarPorDepartamento;
window.filtrarPorSector = filtrarPorSector;
window.filtrarPorEtapa = filtrarPorEtapa;
window.cambiarPeriodo = cambiarPeriodo;
window.resetFilters = resetFilters;
window.refreshDashboard = refreshDashboard;
window.togglePerformanceMode = togglePerformanceMode;
window.cambiarPeriodoGrafico = cambiarPeriodoGrafico;
window.aplicarRangoFechas = aplicarRangoFechas;
window.updateChartContent = updateChartContent;
window.updateMunicipiosTable = updateMunicipiosTable;
window.updateEstadoDiagnosticoContent = updateEstadoDiagnosticoContent;
window.updateTipoOrganizacionContent = updateTipoOrganizacionContent;
window.loadRealMapMarkers = loadRealMapMarkers;
window.updateMetricTrend = updateMetricTrend;
window.mostrarResumenProporciones = mostrarResumenProporciones;
// COMENTADAS: Estas funciones están duplicadas en dashboard.blade.php
// window.mostrarListaCompletaProporciones = mostrarListaCompletaProporciones;
// window.toggleProporcionesCompletas = toggleProporcionesCompletas;
window.debugProporciones = debugProporciones;
window.clearSectoresCache = clearSectoresCache;
window.debugSectores = debugSectores;
window.loadChartJS = loadChartJS;
window.loadGoogleMaps = loadGoogleMaps;
window.initializeDashboard = initializeDashboard;
window.initializeCharts = initializeCharts;
window.initializeMap = initializeMap;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.updateLastUpdateTime = updateLastUpdateTime;
window.setupEventListeners = setupEventListeners;
window.initGoogleMaps = initGoogleMaps;
window.toggleSectoresChart = toggleSectoresChart;
window.toggleTamanosChart = toggleTamanosChart;
window.setupChartToggleListeners = setupChartToggleListeners;

// Función de prueba para debug
function testSectoresToggle() {
    console.log('=== PRUEBA MANUAL DE ALTERNANCIA ===');
    console.log('Elementos encontrados:');
    console.log('sectoresDona:', document.getElementById('sectoresDona'));
    console.log('sectoresBarra:', document.getElementById('sectoresBarra'));
    console.log('Radio seleccionado:', document.querySelector('input[name="sectoresChartType"]:checked'));
    
    // Verificar si los event listeners están configurados
    const sectoresDona = document.getElementById('sectoresDona');
    if (sectoresDona) {
        console.log('Event listeners en sectoresDona:', sectoresDona.onchange);
    }
    
    // Llamar manualmente a la función
    console.log('Llamando manualmente a toggleSectoresChart...');
    toggleSectoresChart();
}
window.testSectoresToggle = testSectoresToggle;
