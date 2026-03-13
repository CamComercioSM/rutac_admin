'use strict';
(function () {

    // Users List suggestion
    const tagifyUserListEl = document.querySelector('#TagifyUserList');
    const tagifyOtrosParticipantesEl = document.getElementById('TagifyOtrosParticipantes');
    const wizardModernVertical = document.querySelector('.wizard-modern-vertical');
    const sliderInfo = document.getElementById('slider-info');
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

    //   if (accountUserImage) {
    //     const resetImage = accountUserImage.src;
    //     fileInput.onchange = () => {
    //       if (fileInput.files[0]) {
    //         accountUserImage.src = window.URL.createObjectURL(fileInput.files[0]);
    //       }
    //     };
    //     resetFileInput.onclick = () => {
    //       fileInput.value = '';
    //       accountUserImage.src = resetImage;
    //     };
    //   }


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

    function tagTemplate(tagData) {
        return `
                <tag title="${tagData.title || tagData.participantes}"
                contenteditable='false'
                spellcheck='false'
                tabIndex="-1"
                class="${this.settings.classNames.tag} ${tagData.class || ''}"
                ${this.getAttributes(tagData)}
                >
                <x title='' class='tagify__tag__removeBtn' role='button' aria-label='remove tag'></x>
                <div>
                    <div class='tagify__tag__avatar-wrap'>
                    <img onerror="this.style.visibility='hidden'" src="${tagData.avatar}">
                    </div>
                    <span class='tagify__tag-text'>${tagData.name}</span>
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

    const colorOptions = {
        start: [50],
        connect: [true, false],
        behaviour: 'tap-drag',
        step: 10,
        tooltips: true,
        range: {
            min: 0,
            max: 100
        },
        pips: {
            mode: 'steps',
            stepped: true,
            density: 5
        },
        direction: isRtl ? 'rtl' : 'ltr'
    };
    if (sliderInfo) {
        console.log('sliderInfo', sliderInfo);
        noUiSlider.create(sliderInfo, colorOptions);
    }


})();