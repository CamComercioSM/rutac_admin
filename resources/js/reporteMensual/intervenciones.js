'use strict';


// Users List suggestion
const tagifyUserListEl = document.querySelector('#TagifyUserList');
const tagifyOtrosParticipantesEl = document.getElementById('TagifyOtrosParticipantes');
const wizardModernVertical = document.querySelector('.wizard-modern-vertical');

window.tagifyUserList = null;
//Dropzone.autoDiscover = false;
window.dropzoneFile = null;
window.myDropzone = null;

const previewTemplate = `
        <div class="dz-preview dz-file-preview">
        <div class="dz-details">
         <div class="dz-thumbnail">
            <img data-dz-thumbnail>
            <span class="dz-nopreview">No preview</span>
            <div class="dz-success-mark"></div>
            <div class="dz-error-mark"></div>
            <div class="dz-error-message"><span data-dz-errormessage></span></div>
            <div class="progress">
                <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
                </div>
            </div>
            <div class="dz-filename" data-dz-name></div>
            <div class="dz-size" data-dz-size></div>
                </div>
            </div>`
    ;

const dropzoneBasic = document.querySelector('#dropzone-basic');


(function () {

    function tagTemplate(tagData) {
        return `
        <tag title="${tagData.title || tagData.participantes}"
        contenteditable='false'
        spellcheck='false'
        tabIndex="-1"
        class="${this.settings.classNames.tag} ${tagData.class || ''}"
        ${this.getAttributes(tagData)}
        >
        <x class='tagify__tag__removeBtn'></x>
        <div>
            <div class='tagify__tag__avatar-wrap'>
                <img onerror="this.style.visibility='hidden'" src="${tagData.avatar}">
            </div>

            <!-- 🔥 FIX: mostrar participantes -->
            <span class='tagify__tag-text'>
                ${tagData.name} 
                <small style="opacity:0.7;font-size:150%;">(${tagData.participantes})</small>
            </span>

        </div>
        </tag>
    `;
    }

    function suggestionItemTemplate(tagData) {
        return `
                    <div ${this.getAttributes(tagData)}
                    class='tagify__dropdown__item align-items-center ${tagData.class || ''}'
                    tabindex="0"
                    role="option"
                    >
                    ${tagData.avatar
                ? `<div class='tagify__dropdown__item__avatar-wrap'>
                            <img onerror="this.style.visibility='hidden'" src="${tagData.avatar}">
                        </div>`
                : ''
            }
                    <div class="fw-medium">${tagData.name}</div>
                    <span>${tagData.participantes}</span>
                    </div>
                `;
    }

    function dropdownHeaderTemplate(suggestions) {
        return `
                    <div class="${this.settings.classNames.dropdownItem} ${this.settings.classNames.dropdownItem}__addAll">
                        <strong>${this.value.length ? 'Add remaining' : 'Add All'}</strong>
                        <span>${suggestions.length} members</span>
                    </div>
                `;
    }


    // Función para limitar texto y agregar botón "ver más"
    window.limitarTexto = function (texto, maxLength = 150, titulo = '') {
        if (!texto) return '';

        // Remover etiquetas HTML para contar caracteres
        const textoLimpio = texto.replace(/<[^>]*>/g, '');

        if (textoLimpio.length <= maxLength) {
            return texto;
        }

        // Crear un elemento temporal para trabajar con el HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = texto;

        // Obtener solo el texto sin HTML
        const textoPlano = tempDiv.textContent || tempDiv.innerText || '';

        // Truncar el texto plano
        const textoTruncado = textoPlano.substring(0, maxLength);

        const idUnico = 'texto_' + Math.random().toString(36).substr(2, 9);

        // Almacenar el texto completo y el título en un objeto global
        if (!window.textosCompletos) {
            window.textosCompletos = {};
        }
        window.textosCompletos[idUnico] = {
            texto: texto,
            titulo: titulo
        };

        return `${textoTruncado}... <a href="#" class="text-primary ver-mas-link" data-id="${idUnico}" style="cursor: pointer; text-decoration: underline;">ver más</a>`;
    };

    // Función para renderizar soporte como hipervínculo
    window.renderSoporte = function (url) {
        if (!url) return '';
        // Escapar la URL para evitar problemas de seguridad
        const urlEscapada = url.replace(/"/g, '&quot;').replace(/'/g, '&#x27;');
        return `<a href="${urlEscapada}" target="_blank" class="text-primary" style="text-decoration: underline;">Ver adjunto</a>`;
    };

    window.openAdd = function () {
        const id = $("#unidadAdd").val();
        const text = $("#unidadAdd option:selected").text();
        const participantes = $("#participantes").val();


        // 🔥 FORZAR mínimo 1
        if (isNaN(participantes) || participantes < 1) {
            participantes = 1;
            $("#participantes").val(1);
        }

        if (!(id && text && participantes)) return;

        let existe = $("#table_opciones tr[data-id='" + id + "']").length > 0;
        if (existe) {
            Swal.fire({
                title: "Elemento ya existe",
                icon: "info"
            });
            return;
        }

        window.itemOption({
            id: id,
            text: text,
            participantes: participantes
        });
        window.addUnidadToTagify({
            id: id,
            text: text,
            participantes: participantes
        });

        $("#unidadAdd").val(null).trigger('change');
    }

    window.openAddOtroParticipante = function () {
        const id = $("#otroParticipanteAdd").val();
        const text = $("#otroParticipanteAdd option:selected").text();
        const participantes = $("#participantes_otros").val();

        // 🔥 FORZAR mínimo 1
        if (isNaN(participantes) || participantes < 1) {
            participantes = 1;
            $("#participantes_otros").val(1);
        }

        if (!(id && text && participantes)) return;

        let existe = $("#table_otros_participantes tr[data-id='" + id + "']").length > 0;
        if (existe) {
            Swal.fire({
                title: "Elemento ya existe",
                icon: "info"
            });
            return;
        }

        window.itemOtroParticipante({
            id: id,
            text: text,
            participantes: participantes
        });

        window.addOtroParticipanteToTagify({
            id: id,
            text: text,
            participantes: participantes
        });

        $("#otroParticipanteAdd").val(null).trigger('change');
    }

    window.itemOption = function (row = {}) {
        const index = $("#table_opciones tr").length;

        const item = `
                <tr data-id="${row.id}" >
                    <td> ${row.text} </td>        
                    <td> ${row.participantes} </td>                    
                    <td style="width: 80px;" >
                        <input type="hidden" name="unidades[${index}][unidadproductiva_id]" value="${row.id}" />
                        <input type="hidden" name="unidades[${index}][participantes]" value="${row.participantes}" />
                        
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeOption(this)" >
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>`;

        $("#table_opciones").append(item);
    }

    window.addUnidadToTagify = function (row = {}) {
        if (!window.tagifyUserList) return;

        const yaExiste = window.tagifyUserList.value.some(tag => String(tag.value) === String(row.id));
        if (yaExiste) return;

        const tagData = {
            value: row.id,
            name: row.text,
            participantes: `${row.participantes}`,
            avatar: 'https://placehold.co/40x40?text=' + row.text
        };

        // agregar al whitelist para que también quede disponible en búsquedas
        window.tagifyUserList.settings.whitelist.push(tagData);

        // agregar como tag visible
        window.tagifyUserList.addTags([tagData]);
    }

    window.itemOtroParticipante = function (row = {}) {
        const index = $("#table_otros_participantes tr").length;
        const item = `
            <tr data-id="${row.id}">
            <td>${row.text}</td>
            <td>${row.participantes}</td>
            <td style="width: 80px;">
                <input type="hidden" name="otros_participantes[${index}][lead_id]" value="${row.id}" />
                <input type="hidden" name="otros_participantes[${index}][participantes]" value="${row.participantes}" />

                <button type="button" class="btn btn-danger btn-sm" onclick="removeOtroParticipante(this)">
                    <i class="icon-base ri ri-delete-bin-line"></i>
                </button>
            </td>
            </tr>`;

        $("#table_otros_participantes").append(item);
    }

    window.addOtroParticipanteToTagify = function (row = {}) {
        if (!window.tagifyOtrosParticipantes) return;
        const yaExiste = window.tagifyOtrosParticipantes.value.some(tag => String(tag.value) === String(row.id));
        if (yaExiste) return;

        const tagData = {
            value: row.id,
            name: row.text,
            participantes: `${row.participantes}`,
            avatar: 'https://placehold.co/40x40?text=' + row.text
        };

        // agregar al whitelist para que también quede disponible en búsquedas
        window.tagifyOtrosParticipantes.settings.whitelist.push(tagData);

        // agregar como tag visible
        window.tagifyOtrosParticipantes.addTags([tagData]);
    };

    window.removeOption = function (btn) {
        $(btn).closest("tr").remove();
    }

    window.removeOtroParticipante = function (btn) {
        $(btn).closest("tr").remove();
    }

    window.validarExtraForm = function () {

        return true;
    }

    // ===================== SOPORTE - JS =====================

    /**
     * Carga el soporte en el formulario cuando se edita
     */
    window.cargarSoporteEnFormulario = function (soporteUrl) {

        const container = document.getElementById('soporteActualContainer');
        const link = document.getElementById('linkSoporteActual');
        const inputHidden = document.getElementById('soporteActual');
        const textoRuta = document.getElementById('textoRutaSoporte');

        document.getElementById('eliminarSoporte').value = '0';

        if (soporteUrl && soporteUrl !== '') {
            container.classList.remove('d-none');

            link.href = soporteUrl;
            inputHidden.value = soporteUrl;
            textoRuta.textContent = soporteUrl;

        } else {
            container.classList.add('d-none');
            inputHidden.value = '';
            textoRuta.textContent = '';
        }
    };

    //copiar ruta del soporte
    window.copiarRutaSoporte = function () {

        const ruta = document.getElementById('soporteActual').value;

        if (!ruta) return;

        navigator.clipboard.writeText(ruta).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Ruta copiada',
                timer: 1200,
                showConfirmButton: false
            });
        });
    };

    /**
     * Elimina el soporte actual (solo en UI)
     */
    window.eliminarSoporteActual = function () {
        document.getElementById('soporteActualContainer').classList.add('d-none');
        document.getElementById('soporteActual').value = '';
        document.getElementById('eliminarSoporte').value = '1';
    };


    /**
     * LIMPIAR FORMULARIO (IMPORTANTE cuando es nuevo registro)
     */
    window.resetSoporteFormulario = function () {
        document.getElementById('soporteActualContainer').classList.add('d-none');
        document.getElementById('soporteActual').value = '';
        document.getElementById('formFile').value = '';
    };










    if (dropzoneBasic) {
        window.myDropzone = new Dropzone(dropzoneBasic, {
            url: "/upload",
            autoProcessQueue: true,
            previewTemplate: previewTemplate,
            previewsContainer: dropzoneBasic,
            parallelUploads: 1,
            maxFilesize: 5,
            acceptedFiles: '.jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx',
            addRemoveLinks: true,
            maxFiles: 1,
            init: function () {
                this.on('addedfile', function (file) {
                    window.dropzoneFile = file;
                });

                this.on('removedfile', function () {
                    window.dropzoneFile = null;
                });

                this.on('maxfilesexceeded', function (file) {
                    this.removeAllFiles();
                    this.addFile(file);
                    window.dropzoneFile = file;
                });
            }
        });
    }

    if (wizardModernVertical) {
        const wizard = new Stepper(wizardModernVertical, {
            linear: false
        });

        wizardModernVertical.querySelectorAll('.btn-next').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                wizard.next();
            });
        });

        wizardModernVertical.querySelectorAll('.btn-prev').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                wizard.previous();
            });
        });
    }

    if (tagifyUserListEl) {
        window.tagifyUserList = new Tagify(tagifyUserListEl, {
            tagTextProp: 'name', // very important since a custom template is used with this property as text. allows typing a "value" or a "name" to match input with whitelist
            enforceWhitelist: true,
            skipInvalid: true, // do not remporarily add invalid tags
            dropdown: {
                closeOnSelect: false,
                enabled: 1,
                classname: 'users-list',
                searchKeys: ['name',
                    'email'
                ] // very important to set by which keys to search for suggesttions when typing
            },
            templates: {
                tag: tagTemplate,
                dropdownItem: suggestionItemTemplate,
                dropdownHeader: dropdownHeaderTemplate
            },
            whitelist: []
        });
        window.tagifyUserList.DOM.input.addEventListener('keydown', e => e.preventDefault());
        window.tagifyUserList.on('dropdown:select', onSelectSuggestion).on('edit:start',
            onEditStart); // show custom text in the tag while in edit-mode

        function onSelectSuggestion(e) {
            // custom class from "dropdownHeaderTemplate"
            if (e.detail.elm.classList.contains(
                `${window.tagifyUserList.settings.classNames.dropdownItem}__addAll`)) {
                window.tagifyUserList.dropdown.selectAll();
            }
        }

        function onEditStart({
            detail: {
                tag,
                data
            }
        }) {
            window.tagifyUserList.setTagTextNode(tag, `${data.name} <${data.email}>`);
        }
    }
    if (tagifyOtrosParticipantesEl) {
        window.tagifyOtrosParticipantes = new Tagify(tagifyOtrosParticipantesEl, {
            tagTextProp: 'name',
            enforceWhitelist: true,
            skipInvalid: true,
            dropdown: {
                closeOnSelect: false,
                enabled: 1,
                classname: 'users-list',
                searchKeys: ['name',
                    'participantes'
                ]
            },
            templates: {
                tag: tagTemplate,
                dropdownItem: suggestionItemTemplate,
                dropdownHeader: dropdownHeaderTemplate
            },
            whitelist: []
        });
        window.tagifyOtrosParticipantes.DOM.input.addEventListener('keydown', e => e.preventDefault());
        window.tagifyOtrosParticipantes.on('dropdown:select', onSelectSuggestion).on('edit:start',
            onEditStart);

        function onSelectSuggestion(e) {
            if (e.detail.elm.classList.contains(
                `${window.tagifyOtrosParticipantes.settings.classNames.dropdownItem}__addAll`)) {
                window.tagifyOtrosParticipantes.dropdown.selectAll();
            }
        }
        function onEditStart({
            detail: {
                tag,
                data
            }
        }) {
            window.tagifyOtrosParticipantes.setTagTextNode(tag, `${data.name} <${data.participantes}>`);
        }
    }


})();