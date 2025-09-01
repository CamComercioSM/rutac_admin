    // === IMPORTACIONES NECESARIAS ===
    import $ from 'jquery';
    import axios from 'axios';

    window.$ = $;
    window.jQuery = $;
    window.axios = axios;

    // Select2 y Bootstrap Table después de jQuery
    import 'bootstrap-table'
    import select2 from 'select2';
    select2($);

    // Si necesitas estilos también:
    import 'select2/dist/css/select2.min.css'
    import 'bootstrap-table/dist/bootstrap-table.min.css'
    import 'bootstrap-table/dist/locale/bootstrap-table-es-ES.min.js'

    $.extend($.fn.bootstrapTable.defaults, $.fn.bootstrapTable.locales['es-ES']);    
    
    const preview = document.getElementById('preview');
    const file = document.getElementById('file');
    const dropdown = $('#MenurowTable');
    let itemSelect = null;

    window.formatearFecha = function(value) {
        if (!value) return '';
        const fecha = new Date(value);
        
        return fecha.toLocaleString('es-CO', 
            { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' }
        );
    }

    window.ajaxCargarData = params => {

        let data = { ...params.data, ...TABLA.paramsExtra };

        if(document.getElementById('filters')){
            const formData = new FormData(document.getElementById('filters'));
            for (const [key, value] of formData.entries()) {
                if(value) data[key] = value;
            }
        }

        const queryString = 'export?' + new URLSearchParams(data).toString();
        $('#btnExport').attr('href', queryString);

        axios.get(TABLA.urlApi, { params: data })
        .then((res) => {
            params.success(res.data);
        })
        .catch((error) => {
            console.error("Error al cargar integrantes:", error);
            params.error && params.error(error);
        })
        .finally(() => {
            $('.cargando').addClass('d-none');
        });
    }

    $('#btnCrear').on('click', function () { 
        itemSelect = null;
        CrearRegistro(); 
    });

    window.openEditar = function() {
        CrearRegistro(itemSelect);
    };

    window.CrearRegistro = function (data = null) 
    {
        const form = document.getElementById('form');
        form.reset();

        form.querySelectorAll('input, textarea, select')
        .forEach(input => {
            if ($(input).hasClass('trumbowyg-textarea')) {
                $(input).trumbowyg('html', '');
            } else {
                $(input).val(null).trigger('change');
            }
        });

        if(data == null) data = {};

        if(preview) preview.innerHTML = '<i class="bi bi-cloud-upload"></i> <div>Seleccionar archivo</div> <small>Click para seleccionar</small>';

        for (let nb in data) {
            const input = form[nb];
            if (input && input.type !== 'file' && data[nb] != null) 
            {
                if ($(input).hasClass('trumbowyg-textarea')) {
                    $('#' + nb).trumbowyg('html', data[nb]);
                } else 
                {
                    if (input.type === 'date') {
                        data[nb] = formatDateForInput(data[nb]); // YYYY-MM-DD
                    } 
                    else if (input.type === 'datetime-local') {
                        data[nb] = formatDateTimeForInput(data[nb]); // YYYY-MM-DDTHH:mm
                    }
                    
                    $(input).val(data[nb]).trigger('change');
                }       
            }
            else if((nb == 'opciones' || nb == 'requisitos') && data[nb] != null && typeof TABLA.loadOptions === "function" ){
                TABLA.loadOptions(data[nb]);
            }
        }

        if ((data.img || data.url) && preview) {
            preview.innerHTML = `<img src="${data.img || data.url}" >`;
        }

        $("#accionModal").text( (data.id ? 'Editar' : 'Crear') );

        AbrirModal();
    }

    file?.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file && preview) {
            const url = URL.createObjectURL(file);
            const type = file.type;
            const name = file.name;

            if (type.startsWith('image/')) {
                preview.innerHTML = `<img src="${url}" >`;
            } 
            else if (type.startsWith('video/')) {
                preview.innerHTML = `<i class="bi bi-camera-reels"></i> <div>${name}</div>`;
            } 
            else {
                preview.innerHTML = `<i class="bi bi-file-earmark-medical"></i> <div>${name}</div>`;
            }
        }
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

        try {
            $('.cargando').removeClass('d-none');

            await axios.post(TABLA.urlApi, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });

            CerrarModal();
            $('#tabla').bootstrapTable('refresh');
            $('.cargando').addClass('d-none');
        } 
        catch (error) {
            console.error('Error al guardar:', error);
        }
    });

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

    $('#btnFiltrar').on('click', function (e) {
        e.preventDefault();
        $('#tabla').bootstrapTable('refresh');
    });

    $('#btnLimpiarFiltrar').on('click', function (e) {
        const form = document.getElementById('filters');
        form.reset();

        form.querySelectorAll('input, textarea, select')
        .forEach(input => {
            $(input).val(null).trigger('change');
        });
        
        $('#tabla').bootstrapTable('refresh');
    });


    if(TABLA.initFiltros)
    {
        $.each(TABLA.initFiltros, function(key, value) {
            const input = $('#'+key);

            if(input.length)
                input.val(value).trigger('change');
        });
    }


    if (TABLA.initSelects) 
    {
        TABLA.initSelects.forEach(item => {
            $("#" + item.id).select2(item.setting ?? {});
        });
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

    $('#tabla').bootstrapTable({
        toolbar: '#toolbar',
        columns: TABLA.columns,
        locale: 'es-ES',
        pagination: true,
        sidePagination: 'server',
        search: true,
        sortName: TABLA.sortName,
        sortOrder: 'asc',
        pageList: [15, 25, 50, 100],
        ajax: ajaxCargarData,
        queryParamsType: '',
        onClickCell: function (field, value, row, $element) { menuTabla(row, $element) },
        onDblClickCell: function (field, value, row, $element) { menuTabla(row, $element) },
    });


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
