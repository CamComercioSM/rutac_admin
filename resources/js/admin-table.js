    // === IMPORTACIONES NECESARIAS ===
    import $ from 'jquery';

    window.$ = $;
    window.jQuery = $;

    // Select2 y Bootstrap Table después de jQuery
    import 'bootstrap-table';
    import 'tableexport.jquery.plugin';
    import 'bootstrap-table/dist/extensions/export/bootstrap-table-export.min.js';
    import 'bootstrap-table/dist/bootstrap-table.min.css';
    import 'bootstrap-table/dist/locale/bootstrap-table-es-ES.min.js'

    $.extend($.fn.bootstrapTable.defaults, $.fn.bootstrapTable.locales['es-ES']);    
    
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