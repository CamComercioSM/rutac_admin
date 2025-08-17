<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dashboard Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración del dashboard de Unidades Productivas para optimizar
    | el rendimiento y evitar timeouts
    |
    */

    // Límites de consultas para evitar timeouts
    'query_limits' => [
        'max_records' => env('DASHBOARD_MAX_RECORDS', 10000),
        'max_departments' => env('DASHBOARD_MAX_DEPARTMENTS', 50),
        'max_municipalities' => env('DASHBOARD_MAX_MUNICIPALITIES', 200),
        'max_sectors' => env('DASHBOARD_MAX_SECTORS', 30),
        'max_etapas' => env('DASHBOARD_MAX_ETAPAS', 20),
        'max_tamanos' => env('DASHBOARD_MAX_TAMANOS', 10),
        'max_tipos_persona' => env('DASHBOARD_MAX_TIPOS_PERSONA', 15),
        'max_tamano_sector_combinations' => env('DASHBOARD_MAX_TAMANO_SECTOR', 50),
        'max_map_locations' => env('DASHBOARD_MAX_MAP_LOCATIONS', 100),
    ],

    // Configuración de cache
    'cache' => [
        'enabled' => env('DASHBOARD_CACHE_ENABLED', true),
        'ttl' => [
            'stats' => env('DASHBOARD_CACHE_STATS_TTL', 300), // 5 minutos
            'filters' => env('DASHBOARD_CACHE_FILTERS_TTL', 600), // 10 minutos
            'general' => env('DASHBOARD_CACHE_GENERAL_TTL', 1800), // 30 minutos
        ],
        'prefix' => env('DASHBOARD_CACHE_PREFIX', 'dashboard_'),
    ],

    // Configuración de paginación
    'pagination' => [
        'default_per_page' => env('DASHBOARD_DEFAULT_PER_PAGE', 50),
        'max_per_page' => env('DASHBOARD_MAX_PER_PAGE', 200),
        'load_more_increment' => env('DASHBOARD_LOAD_MORE_INCREMENT', 25),
    ],

    // Configuración de gráficos
    'charts' => [
        'max_data_points' => env('DASHBOARD_MAX_CHART_POINTS', 20),
        'animation_duration' => env('DASHBOARD_CHART_ANIMATION', 1000),
        'responsive_breakpoint' => env('DASHBOARD_RESPONSIVE_BREAKPOINT', 768),
    ],

    // Configuración de filtros
    'filters' => [
        'max_date_range_days' => env('DASHBOARD_MAX_DATE_RANGE', 365),
        'default_date_range' => env('DASHBOARD_DEFAULT_DATE_RANGE', 30),
        'enable_real_time_search' => env('DASHBOARD_REALTIME_SEARCH', true),
        'search_debounce_ms' => env('DASHBOARD_SEARCH_DEBOUNCE', 300),
    ],

    // Configuración de rendimiento
    'performance' => [
        'enable_lazy_loading' => env('DASHBOARD_LAZY_LOADING', true),
        'enable_infinite_scroll' => env('DASHBOARD_INFINITE_SCROLL', false),
        'auto_refresh_interval' => env('DASHBOARD_AUTO_REFRESH', 300), // 5 minutos
        'max_concurrent_requests' => env('DASHBOARD_MAX_CONCURRENT', 3),
    ],

    // Configuración de exportación
    'export' => [
        'enabled' => env('DASHBOARD_EXPORT_ENABLED', true),
        'max_records_export' => env('DASHBOARD_MAX_EXPORT_RECORDS', 5000),
        'formats' => ['csv', 'xlsx', 'pdf'],
        'chunk_size' => env('DASHBOARD_EXPORT_CHUNK_SIZE', 1000),
    ],

    // Configuración de notificaciones
    'notifications' => [
        'enabled' => env('DASHBOARD_NOTIFICATIONS_ENABLED', true),
        'show_loading' => env('DASHBOARD_SHOW_LOADING', true),
        'show_errors' => env('DASHBOARD_SHOW_ERRORS', true),
        'auto_hide_delay' => env('DASHBOARD_NOTIFICATION_DELAY', 5000),
    ],

    // Configuración de mapas
    'maps' => [
        'enabled' => env('DASHBOARD_MAPS_ENABLED', true),
        'max_markers' => env('DASHBOARD_MAX_MAP_MARKERS', 100),
        'cluster_markers' => env('DASHBOARD_CLUSTER_MARKERS', true),
        'default_zoom' => env('DASHBOARD_DEFAULT_ZOOM', 6),
        'default_center' => [
            'lat' => env('DASHBOARD_DEFAULT_LAT', 4.5709),
            'lng' => env('DASHBOARD_DEFAULT_LNG', -74.2973),
        ],
        'google_maps_key' => env('GOOGLE_MAPS_KEY'),
    ],

    // Configuración de monitoreo
    'monitoring' => [
        'enabled' => env('DASHBOARD_MONITORING_ENABLED', true),
        'log_slow_queries' => env('DASHBOARD_LOG_SLOW_QUERIES', true),
        'slow_query_threshold' => env('DASHBOARD_SLOW_QUERY_THRESHOLD', 1000), // ms
        'log_performance_metrics' => env('DASHBOARD_LOG_PERFORMANCE', true),
    ],

    // Configuración de desarrollo
    'development' => [
        'debug_mode' => env('DASHBOARD_DEBUG_MODE', false),
        'show_query_log' => env('DASHBOARD_SHOW_QUERY_LOG', false),
        'mock_data' => env('DASHBOARD_MOCK_DATA', false),
        'performance_profiling' => env('DASHBOARD_PERFORMANCE_PROFILING', false),
    ],

    // Configuración de seguridad
    'security' => [
        'rate_limiting' => env('DASHBOARD_RATE_LIMITING', true),
        'max_requests_per_minute' => env('DASHBOARD_MAX_REQUESTS_PER_MINUTE', 60),
        'session_timeout' => env('DASHBOARD_SESSION_TIMEOUT', 3600), // 1 hora
        'csrf_protection' => env('DASHBOARD_CSRF_PROTECTION', true),
    ],

    // Configuración de idioma
    'locale' => [
        'default' => env('DASHBOARD_DEFAULT_LOCALE', 'es'),
        'fallback' => env('DASHBOARD_FALLBACK_LOCALE', 'en'),
        'available' => ['es', 'en'],
    ],

    // Configuración de temas
    'themes' => [
        'default' => env('DASHBOARD_DEFAULT_THEME', 'light'),
        'available' => ['light', 'dark', 'auto'],
        'custom_colors' => [
            'primary' => env('DASHBOARD_PRIMARY_COLOR', '#667eea'),
            'secondary' => env('DASHBOARD_SECONDARY_COLOR', '#764ba2'),
            'success' => env('DASHBOARD_SUCCESS_COLOR', '#28a745'),
            'warning' => env('DASHBOARD_WARNING_COLOR', '#ffc107'),
            'danger' => env('DASHBOARD_DANGER_COLOR', '#dc3545'),
        ],
    ],

    // Configuración de accesibilidad
    'accessibility' => [
        'high_contrast' => env('DASHBOARD_HIGH_CONTRAST', false),
        'large_text' => env('DASHBOARD_LARGE_TEXT', false),
        'screen_reader_support' => env('DASHBOARD_SCREEN_READER', true),
        'keyboard_navigation' => env('DASHBOARD_KEYBOARD_NAV', true),
    ],

    // Configuración de backup y recuperación
    'backup' => [
        'enabled' => env('DASHBOARD_BACKUP_ENABLED', false),
        'auto_backup' => env('DASHBOARD_AUTO_BACKUP', false),
        'backup_interval' => env('DASHBOARD_BACKUP_INTERVAL', 'daily'),
        'retention_days' => env('DASHBOARD_BACKUP_RETENTION', 30),
    ],

    // Configuración de logs
    'logging' => [
        'enabled' => env('DASHBOARD_LOGGING_ENABLED', true),
        'log_level' => env('DASHBOARD_LOG_LEVEL', 'info'),
        'log_dashboard_actions' => env('DASHBOARD_LOG_ACTIONS', true),
        'log_user_interactions' => env('DASHBOARD_LOG_INTERACTIONS', false),
    ],

    // Configuración de métricas
    'metrics' => [
        'enabled' => env('DASHBOARD_METRICS_ENABLED', true),
        'track_page_load_time' => env('DASHBOARD_TRACK_LOAD_TIME', true),
        'track_query_performance' => env('DASHBOARD_TRACK_QUERY_PERFORMANCE', true),
        'track_user_engagement' => env('DASHBOARD_TRACK_ENGAGEMENT', false),
    ],
];
