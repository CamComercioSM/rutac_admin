    // === IMPORTACIONES NECESARIAS ===
    import $ from 'jquery';

    window.$ = $;
    window.jQuery = $;

    // Select2 y Bootstrap Table después de jQuery
    import 'bootstrap-table';
    import 'tableexport.jquery.plugin';
    import 'bootstrap-table/dist/extensions/export/bootstrap-table-export.min.js';
    import 'bootstrap-table/dist/bootstrap-table.min.css';
    import 'bootstrap-table/dist/bootstrap-table.min.css';

    // === LOCALIZACIÓN DE BOOTSTRAP TABLE ===
    $.fn.bootstrapTable.locales['es-ES'] = $.fn.bootstrapTable.locales['es-ES'] || {};
    $.extend($.fn.bootstrapTable.locales['es-ES'], {
        formatShowingRows: function (from, to, total) { 
            return `Visualizando ${from}–${to} de ${total}.`; 
        },
        formatRecordsPerPage: function (pageNumber) {
            return `${pageNumber}`;
        }
    });

    // Establecer por defecto el idioma a español
    $.extend($.fn.bootstrapTable.defaults, {
        locale: 'es-ES'
    });
    
    if(TABLAS)
    {
        TABLAS.forEach(item => {
            $("#" + item.id).bootstrapTable(item.setting ?? {});
        });
    }

    
    $('.exportar').on('click', function (e) {
        e.preventDefault();
        let tabla = $(this).data('tabla');
        $('#'+tabla).bootstrapTable('refreshOptions', {exportDataType: 'all'}); 
        $('#'+tabla).tableExport({type: 'excel', fileName: tabla});
    });