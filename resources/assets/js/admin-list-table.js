$(document).ready(function () {

    const dropdown = $('#MenurowTable');
    let itemSelect = null;
    let tabla = null;

    const fullToolbar = [
        [ { size: [] } ],
        ['bold', 'italic', 'underline', 'strike'],
        [ { color: [] }, {  background: [] } ],
        [ { header: '1' }, { header: '2' }, 'blockquote', 'code-block' ],
        [ { list: 'ordered' }, { list: 'bullet' }, { indent: '-1' }, { indent: '+1' } ],
        [ 'direction', { align: [] } ]
    ];

    window.ajaxCargarData = function(data, callback, settings) {

        // Mostrar loader de DataTables
        $('#tabla').closest('.dataTables_wrapper').find('.dataTables_processing').show();
        $('.cargando').removeClass('d-none');

        let params = { ...window.TABLA.paramsExtra };

        // DataTables envía start y length
        params.page = Math.floor(data.start / data.length) + 1;
        params.pageSize = data.length;

        // Orden
        if (data.order && data.order.length > 0) {
            const dir = data.order[0].dir;
            params.sortName = window.TABLA.sortName ?? 'id';
            params.sortOrder = dir;
        }

        // Búsqueda
        if (data.search && data.search.value) {
            params.search = data.search.value;
        }

        // Filtros del formulario
        if (document.getElementById('filters')) {
            const formData = new FormData(document.getElementById('filters'));
            for (const [key, value] of formData.entries()) {
                if (value) params[key] = value;
            }
        }

        // Export link
        const queryString = 'export?' + new URLSearchParams(params).toString();
        $('#btnExport').attr('href', queryString);

        window.TABLA.filtrosCampos = params;

        // Llamada con axios
        axios.get(window.TABLA.urlApi, { params })
            .then(res => {
                window.TABLA.totalRegistros = res.data.recordsTotal;
                callback(res.data);
            })
            .catch(error => {
                console.error("Error al cargar integrantes:", error);
                callback({ data: [] });
            })
            .finally(() => {
                $('#tabla').closest('.dataTables_wrapper').find('.dataTables_processing').hide();
                $('.cargando').addClass('d-none');
            });
    };

    if(window.TABLA && window.TABLA.initFiltros)
    {
        $.each(window.TABLA.initFiltros, function(key, value) {
            const input = $('#'+key);

            if(input.length)
                input.val(value).trigger('change');
        });
    }

    if (window.TABLA && window.TABLA.initSelects) 
    {
        window.TABLA.initSelects.forEach(item => {
            item.setting = item.setting ?? {};
            item.setting.allowClear = true;
            item.setting.placeholder = item.setting.placeholder ?? "Seleccione una opción";
            $("#" + item.id).select2(item.setting);
            if(typeof item.change === "function"){
                $("#" + item.id).on("change", function (e) { item.change(e); });
            }
        });
    }

    if(window.TABLA && window.TABLA.columns)
    {
        window.TABLA.seleccionados = new Map();
        window.TABLA.seleccionarTodo = false;
        let columnDefs = [];
        let select = null;

        if(window.TABLA.checkboxes)
        {
            window.TABLA.columns.unshift({ data: 'id', orderable: false, className: 'check', render: DataTable.render.select() });
            columnDefs = [
                {
                    targets: 0,
                    searchable: false,
                    orderable: false,
                    className: 'check',
                    render: function () {
                        return '<input type="checkbox" class="dt-checkboxes form-check-input">';
                    },
                    checkboxes: {
                        selectRow: true,
                        selectAllRender: '<input type="checkbox" class="form-check-input">'
                    }
                }
            ];
            select = { style: 'multi' };
        }

        tabla = $('#tabla').DataTable({
            serverSide: true,
            processing: true,
            ajax: ajaxCargarData,
            columns: window.TABLA.columns,
            columnDefs: columnDefs,
            pageLength: 10,
            lengthMenu: [10, 15, 25, 50, 100],
            language: {
                paginate: {
                    next: '<i class="icon-base ri ri-arrow-right-s-line scaleX-n1-rtl icon-22px"></i>',
                    previous: '<i class="icon-base ri ri-arrow-left-s-line scaleX-n1-rtl icon-22px"></i>',
                    first: '<i class="icon-base ri ri-skip-back-mini-line scaleX-n1-rtl icon-22px"></i>',
                    last: '<i class="icon-base ri ri-skip-forward-mini-line scaleX-n1-rtl icon-22px"></i>'
                }
            },
            layout: {
                topStart: {
                    rowClass: 'row mx-2 justify-content-between',
                    features: [
                        { pageLength: { menu: [10, 25, 50, 100], text: 'Show_MENU_entries' } }
                    ]
                },
                topEnd: { search: { placeholder: 'Busqueda...' } },
                bottomStart: {
                    rowClass: 'row mx-2 justify-content-between',
                    features: ['info']
                },
                bottomEnd: 'paging'
            },
            select: select
        });

        if(window.TABLA.checkboxes)
        {
            tabla.on('select', function(e, dt, type, indexes) {
                const data = tabla.rows(indexes).data().toArray();
                data.forEach(row => {
                    window.TABLA.seleccionados.set(row.id, row.business_name);
                });
            });

            tabla.on('deselect', function(e, dt, type, indexes) {
                const data = tabla.rows(indexes).data().toArray();
                data.forEach(row => {
                    window.TABLA.seleccionados.delete(row.id);
                });
            });

            tabla.on('draw', function() {
                tabla.rows().every(function () {
                    const data = this.data();
                    if (window.TABLA.seleccionados.has(data.id)) {
                        this.select();
                    }
                });
            });
        }
    }

    // Eventos click y dblclick
    $('#tabla').on('click', 'td:not(.check)', function () {
        const row = $('#tabla').DataTable().row(this.closest('tr')).data();
        menuTabla(row, $(this));
    });

    $('#tabla').on('dblclick', 'td:not(.check)', function () {
        const row = $('#tabla').DataTable().row(this.closest('tr')).data();
        menuTabla(row, $(this));
    });

    $('#btnFiltrar').on('click', function (e) {
        e.preventDefault();
        tabla.ajax.reload();
    });

    $('#btnLimpiarFiltrar').on('click', function (e) {
        const form = document.getElementById('filters');
        form.reset();

        form.querySelectorAll('input, textarea, select')
        .forEach(input => {
            $(input).val(null).trigger('change');
        });
        
        tabla.ajax.reload();
    });
    

    document.getElementById('form').addEventListener('submit', async function (e) {
    
        e.preventDefault();

        const form = e.target;

        if (!form.checkValidity()) 
        {
            e.stopPropagation();
            form.classList.add('was-validated');
            return;
        }
    
        form.classList.add('was-validated');

        if(typeof window.validarExtraForm === "function"){
            if(!window.validarExtraForm()) return;
        }
    
        const formData = new FormData(form);

        if (window.TABLA && window.TABLA.initEditors) 
        {
            window.TABLA.initEditors.forEach(item => {
                formData.append(item.id, item.quill.root.innerHTML);
            });
        }

        try {
            $('.cargando').removeClass('d-none');

            await axios.post(TABLA.urlApi, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });

            CerrarModal();

            tabla.ajax.reload();
            $('.cargando').addClass('d-none');
        } 
        catch (error) {
            console.error('Error al guardar:', error);
        }
    });

    if (window.TABLA && window.TABLA.initEditors) 
    {
        window.TABLA.initEditors.forEach(item => {
            item.quill = new Quill("#" + item.id, {
                bounds: "#" + item.id,
                placeholder: 'Escriba aqui...',
                modules: { toolbar: fullToolbar },
                theme: 'snow'
            });
        });
    }

    $('#btnCrear').on('click', function () { 
        itemSelect = null;
        CrearRegistro(); 
    });

    window.openEditar = function() {
        dropdown.hide();
        CrearRegistro(itemSelect);
    };

    window.CrearRegistro = function (data = null) 
    {
        const form = document.getElementById('form');
        form.reset();

        form.querySelectorAll('input, textarea, select')
        .forEach(input => { $(input).val(null).trigger('change'); });

        if(typeof TABLA.loadOptions === "function" ){  TABLA.loadOptions([]); }

        if(data == null) data = {};

        for (let nb in data) {
            const input = form[nb];
            if (input && input.type !== 'file' && data[nb] != null) 
            {
               if (input.type === 'date') {
                    data[nb] = formatDateForInput(data[nb]); // YYYY-MM-DD
                } 
                else if (input.type === 'datetime-local') {
                    data[nb] = formatDateTimeForInput(data[nb]); // YYYY-MM-DDTHH:mm
                }
                
                $(input).val(data[nb]).trigger('change');       
            }
            else if((nb == 'opciones' || nb == 'requisitos_todos') && data[nb] != null && typeof TABLA.loadOptions === "function" ){
                TABLA.loadOptions(data[nb]);
            }
        }

        if (window.TABLA && window.TABLA.initEditors) 
        {
            window.TABLA.initEditors.forEach(item => {
                item.quill.clipboard.dangerouslyPasteHTML( data[item.id] ?? '' );
            });
        }

        $("#accionModal").text( (data.id ? 'Editar' : 'Crear') );

        AbrirModal();
    }

    $('#Modal .cancelar').on('click', function () { CerrarModal(); });

    function CerrarModal() {
        $('#Modal').slideUp(200, function () {
            $('#Data').slideDown(200).removeClass('d-none');
        });
    }

    function AbrirModal() 
    {
        if(typeof window.initAlAbrirModal === "function"){
            window.initAlAbrirModal();
        }

        $('#Data').slideUp(200, function () {
            $('#Modal').slideDown(200).removeClass('d-none');
        });
    }


    window.menuTabla = function(row, $element)
    {
        if(TABLA.menu_row)
        {
            itemSelect = row;
            let menu = TABLA.menu_row.replace(/ROWID/g, row.id);
            const rect = $element[0].getBoundingClientRect();
            
            dropdown.html(menu);
            dropdown.css({
                top: rect.bottom + window.scrollY - 10,
                left: rect.left + window.scrollX,
                display: 'block',
                position: 'absolute',
                zIndex: 9999
            });

            $(document).off('mousedown.menuContext');
            $(document).on('mousedown.menuContext', function(e) {
                // Si el clic NO fue dentro del dropdown
                if (!$(e.target).closest('#MenurowTable').length) {
                    dropdown.hide();
                    $(document).off('mousedown.menuContext'); // remover handler
                }
            });
            
        }
        else
        {
            if(TABLA.accion_editar)
            {
                CrearRegistro(row);
            }
            else if(TABLA.accion_ver)
            {
                $('.cargando').removeClass('d-none');
                window.location.href = TABLA.urlApi +'/'+ row.id;
            }
        }
    }

    window.formatearFecha = function(value) {
        if (!value) return '';
        const fecha = new Date(value);
        
        return fecha.toLocaleString('es-CO', 
            { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' }
        );
    }

    function formatDateForInput(dateStr) {
        const date = new Date(dateStr);
        if (isNaN(date)) return "";
        return date.toISOString().split('T')[0];
    }

    function formatDateTimeForInput(dateStr) {
        const date = new Date(dateStr);
        if (isNaN(date)) return "";
        return date.toISOString().slice(0, 16);
    }

});