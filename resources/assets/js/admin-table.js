import languageEs from '../../assets/es-ES.json';

$(document).ready(function () {      
    if(window.TABLAS)
    {
        window.TABLAS.forEach(item => {
            item.setting.language = languageEs;
            $("#" + item.id).DataTable(item.setting);
        });
    }
});