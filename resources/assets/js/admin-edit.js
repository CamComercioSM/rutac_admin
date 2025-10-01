$(document).ready(function () {

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

            await axios.post(window.URL_API, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });

            $('.cargando').addClass('d-none');
        } 
        catch (error) {
            console.error('Error al guardar:', error);
        }
    });    

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
    });


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
    }

    $('.cargando').addClass('d-none');
});