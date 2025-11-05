$(document).ready(function () {

    // Silenciar logs si la vista lo solicita
    try {
        if (window.ENV_SILENCE_CONSOLE) {
            ['log','warn','error'].forEach(function(m){
                if (typeof console[m] === 'function') console[m] = function(){};
            });
        }
    } catch (_) {}

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

            // En transformar, la URL incluye el id y el método correcto es PUT
            if(/\/unidadesProductivas\/[0-9]+$/.test(window.URL_API)){
                formData.append('_method', 'PUT');
            }

            // Pre-chequeo: validar matrícula duplicada y marcar input en rojo
            const regInput = document.getElementById('registration_number');
            if (regInput) {
                const invalida = await validarMatriculaDuplicada();
                if (invalida) { $('.cargando').addClass('d-none'); return; }
            }

            const response = await axios.post(window.URL_API, formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
                validateStatus: () => true
            });

            $('.cargando').addClass('d-none');

            // Manejo según status sin disparar errores de consola
            if (response && response.status >= 200 && response.status < 300) {
                const id = document.getElementById('unidadproductiva_id')?.value || document.getElementById('id')?.value;
                if(id){
                    window.location.href = `/unidadesProductivas/${id}`;
                }
            } else if (response && response.status === 422) {
                const errs = response.data?.errors || {};
                const mensajes = Object.values(errs).flat().join('\n') || response.data?.message || 'Error de validación.';
                Swal.fire({ icon: 'error', title: 'No se pudo guardar', text: mensajes });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo guardar. Intenta nuevamente.' });
            }
        } 
        catch (error) {
            $('.cargando').addClass('d-none');
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo guardar. Intenta nuevamente.' });
        }
    });    
    // Validación en tiempo real de matrícula duplicada
    async function validarMatriculaDuplicada(){
        const input = document.getElementById('registration_number');
        if(!input) return false;
        const feedback = document.getElementById('registration_number_feedback');
        input.setCustomValidity('');
        input.classList.remove('is-invalid');
        if (!input.value) return false;
        try {
            const ignoreId = document.getElementById('unidadproductiva_id')?.value || '';
            const resp = await axios.get('/unidadesProductivas/check/matricula', {
                params: { registration_number: input.value.trim(), ignore_id: ignoreId },
                validateStatus: () => true
            });
            if (resp?.data?.exists) {
                input.setCustomValidity('duplicated');
                input.classList.add('is-invalid');
                if (feedback) feedback.textContent = 'Este número de matrícula ya existe en la base de datos.';
                input.reportValidity();
                return true;
            }
        } catch(_) { /* ignorar errores */ }
        return false;
    }

    const registrationInput = document.getElementById('registration_number');
    if (registrationInput) {
        registrationInput.addEventListener('blur', validarMatriculaDuplicada);
        registrationInput.addEventListener('input', function(){
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
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

    $("#department_id").on("change", function() {
        var departamentoId = $(this).val(); // departamento seleccionado

        // resetear municipios
        $("#municipality_id option").hide(); 
        $("#municipality_id option:first").show(); // mostrar "Seleccione una opción"
        $("#municipality_id").val(""); // reset valor seleccionado

        // mostrar solo los municipios que pertenezcan al departamento seleccionado
        $("#municipality_id option[data-departamento='" + departamentoId + "']").show();
    });

    $("#unidadtipo_id").on("change", function() {
        var text = $("#unidadtipo_id option:selected").text(); 
        $("#tipo_registro_rutac").val(text);
        
        // Obtener el ID seleccionado del tipo de registro
        var tipoId = $(this).val();
        
        // El campo Número de matrícula es obligatorio solo para tipos de registro formal (3 y 4)
        // ID 1: Idea de negocio
        // ID 2: Informal 
        // ID 3: Registrado fuera CCSM
        // ID 4: Registrado CCSM
        // NIT y Fecha de matrícula siempre son obligatorios
        var esFormal = tipoId && (tipoId === '3' || tipoId === '4');
        
        // Obtener el campo
        var registrationNumber = document.getElementById('registration_number');
        
        // Establecer o quitar el atributo required según el tipo solo para número de matrícula
        if (esFormal) {
            if (registrationNumber) registrationNumber.setAttribute('required', 'required');
        } else {
            if (registrationNumber) registrationNumber.removeAttribute('required');
        }
    });

    if (window.SELECTS) 
    {
        window.SELECTS.forEach(item => {
            $("#" + item).select2({ allowClear: true, placeholder: "Seleccione una opción" });
        });
    }

    if(window.DATA)
    {
        const form = document.getElementById('form');

        for (let nb in window.DATA) 
        {
            const input = form[nb];
            if (input && window.DATA[nb] != null) 
            {
               if (input.type === 'date') {
                    window.DATA[nb] = formatDateForInput(window.DATA[nb]); // YYYY-MM-DD
                } 
                else if (input.type === 'datetime-local') {
                    window.DATA[nb] = formatDateTimeForInput(window.DATA[nb]); // YYYY-MM-DDTHH:mm
                }
                
                $(input).val(window.DATA[nb]).trigger('change');       
            }
        }
        
        // Aplicar validación condicional después de cargar los datos
        var unidadtipoId = document.getElementById('unidadtipo_id');
        if (unidadtipoId && unidadtipoId.value) {
            $(unidadtipoId).trigger('change');
        }
    }

    // Enmascarar solo números en teléfonos (sin bloquear pegado; validar por patrón)
    ['telephone','mobile','contact_phone','nit'].forEach(function(id){
        const el = document.getElementById(id);
        if(!el) return;
        el.addEventListener('input', function(){
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });

    // Bloquear caracteres no numéricos mientras se escribe en NIT
    (function(){
        const nit = document.getElementById('nit');
        if(!nit) return;
        nit.addEventListener('keydown', function(e){
            const allowed = ['Backspace','Tab','ArrowLeft','ArrowRight','Delete','Home','End'];
            if (allowed.includes(e.key) || e.ctrlKey || e.metaKey) return;
            if(!/^[0-9]$/.test(e.key)){
                e.preventDefault();
            }
        });
        nit.addEventListener('paste', function(e){
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text');
            const digits = text.replace(/[^0-9]/g, '');
            const start = this.selectionStart;
            const end = this.selectionEnd;
            this.value = this.value.slice(0,start) + digits + this.value.slice(end);
            this.setSelectionRange(start + digits.length, start + digits.length);
        });
    })();

    // Validación en tiempo real de emails duplicados
    const email1 = document.getElementById('registration_email');
    const email2 = document.getElementById('contact_email');
    
    if (email1 && email2) {
        const revalidarEmails = function() {
            // Limpiar validación previa
            email1.setCustomValidity('');
            email2.setCustomValidity('');
            
            // Verificar si hay duplicación
            if(email1.value && email2.value && email1.value.trim().toLowerCase() === email2.value.trim().toLowerCase()){
                email2.setCustomValidity('El email de contacto no puede ser igual al email de registro.');
                email2.reportValidity();
            }
        };
        
        email1.addEventListener('input', revalidarEmails);
        email2.addEventListener('input', revalidarEmails);
        email1.addEventListener('blur', revalidarEmails);
        email2.addEventListener('blur', revalidarEmails);
        
        // Ejecutar validación inicial en caso de que haya datos precargados
        setTimeout(revalidarEmails, 100);
    }

    $('.cargando').addClass('d-none');
});