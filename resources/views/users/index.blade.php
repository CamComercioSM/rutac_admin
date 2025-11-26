@extends('layouts.list', ['titulo'=> 'Usuarios', 'tituloModal'=> 'usuario'])

@section('form-fields')
    <div class="row">

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="identification">N° documento <span class="text-muted">(Alfanumérico)</span></label>
            <input type="text" class="form-control" name="identification" id="identification" placeholder="N° documento" required maxlength="20" pattern="[A-Za-z0-9]+">
            <div class="invalid-feedback">El documento debe contener solo letras y números (permite pasaportes extranjeros).</div>
        </div>
        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="position">Cargo <span class="text-muted">(Máximo 200 caracteres)</span></label>
            <input type="text" class="form-control" name="position" id="position" placeholder="Cargo" maxlength="200" required>
            <div class="invalid-feedback">El cargo debe tener máximo 200 caracteres.</div>
            <small class="text-muted caracteres-restantes" id="position_counter">200 caracteres restantes</small>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="active">Activo</label>
            <select class="form-select" name="active" id="active" required>
                <option value="" selected >Seleccione una opción</option>
                <option value="0">No</option>
                <option value="1">Si</option>
            </select>
        </div>

        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="name">Nombre (s) <span class="text-muted">(Máximo 200 caracteres)</span></label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Nombre (s)" required maxlength="200">
            <div class="invalid-feedback">El nombre debe tener máximo 200 caracteres.</div>
            <small class="text-muted caracteres-restantes" id="name_counter">200 caracteres restantes</small>
        </div>
        <div class="col-12 col-md-6 form-group mb-3">
            <label class="form-label" for="lastname" >Apellido (s) <span class="text-muted">(Máximo 200 caracteres)</span></label>
            <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Apellido (s)" required maxlength="200">
            <div class="invalid-feedback">El apellido debe tener máximo 200 caracteres.</div>
            <small class="text-muted caracteres-restantes" id="lastname_counter">200 caracteres restantes</small>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="email">Email</label>
            <input type="email" class="form-control" name="email" id="email" placeholder="Email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" required>
            <div class="invalid-feedback">Ingrese un correo electrónico válido (no se permiten símbolos extraños).</div>
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="password">Contraseña</label>
            <input type="text" class="form-control" name="password" id="password" placeholder="**********">
        </div>

        <div class="col-12 col-md-4 form-group mb-3">
            <label class="form-label" for="rol_id">Rol <span class="text-danger">*</span></label>
            <select class="form-select" name="rol_id" id="rol_id" required>
                <option value="" selected >Seleccione una opción</option>
                @foreach ($roles as $item)
                    <option value="{{$item->id}}" >{{$item->name}}</option>
                @endforeach
            </select>
            <div class="invalid-feedback">Debe seleccionar un rol válido.</div>
        </div>

    </div>
@endsection

@section('script')
    <script> 
        window.TABLA = {
            urlApi: '/users',
            sortName: 'name',
            menu_row: ` <button class="dropdown-item" onClick="openEditar()" >Editar</button> `,
            columns: [
                { data: 'identification', title: 'N° documento', orderable: true },
                { data: 'name', title: 'Nombre (s)', orderable: true },
                { data: 'lastname', title: 'Apellido (s)', orderable: true },
                { data: 'email', title: 'Usuario', orderable: true },
                { data: 'email_cargo', title: 'Correo', orderable: true },
                { data: 'position', title: 'Cargo', orderable: true }
            ]
        };
    </script>

    <script>
        // Validaciones del lado del cliente
        document.addEventListener('DOMContentLoaded', function() {
            
            // Validación de máximo 200 caracteres para campos de nombre, apellido y cargo
            const nombreFields = ['name', 'lastname', 'position'];
            nombreFields.forEach(function(fieldId) {
                const field = document.getElementById(fieldId);
                const counter = document.getElementById(fieldId + '_counter');
                
                if (field && counter) {
                    field.addEventListener('input', function() {
                        const length = this.value.length;
                        const remaining = 200 - length;
                        counter.textContent = remaining + ' caracteres restantes';
                        
                        if (length > 200) {
                            this.value = this.value.substring(0, 200);
                            counter.textContent = '0 caracteres restantes';
                        }
                        
                        // Validación visual
                        if (length > 200) {
                            this.classList.add('is-invalid');
                        } else {
                            this.classList.remove('is-invalid');
                        }
                    });
                }
            });

            // Validación de documento: alfanumérico (permite pasaportes)
            const documentoField = document.getElementById('identification');
            if (documentoField) {
                // Prevenir entrada de caracteres no alfanuméricos
                documentoField.addEventListener('keypress', function(e) {
                    const char = String.fromCharCode(e.which);
                    // Permitir letras, números
                    if (!/[A-Za-z0-9]/.test(char) && char !== ' ') {
                        e.preventDefault();
                        return false;
                    }
                });

                // Validar al pegar texto
                documentoField.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                    // Mantener solo letras y números, convertir a mayúsculas
                    const alphanumericOnly = pastedText.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
                    this.value = alphanumericOnly;
                    
                    if (alphanumericOnly !== pastedText.replace(/[^A-Za-z0-9]/g, '').toUpperCase()) {
                        Swal.fire({
                            title: 'Caracteres no válidos eliminados',
                            text: 'El documento solo permite letras y números (se permite pasaportes extranjeros).',
                            icon: 'warning',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });

                // Validar y convertir a mayúsculas al escribir
                documentoField.addEventListener('input', function() {
                    const originalValue = this.value;
                    // Eliminar caracteres no alfanuméricos y convertir a mayúsculas
                    const alphanumericOnly = originalValue.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
                    
                    if (originalValue !== alphanumericOnly) {
                        this.value = alphanumericOnly;
                        this.classList.add('is-invalid');
                        
                        setTimeout(() => {
                            this.classList.remove('is-invalid');
                        }, 1000);
                    } else {
                        // Convertir a mayúsculas si hay letras minúsculas
                        if (originalValue !== originalValue.toUpperCase() && /[a-z]/.test(originalValue)) {
                            this.value = originalValue.toUpperCase();
                        }
                        this.classList.remove('is-invalid');
                    }
                });
            }

            // Validación mejorada de correo electrónico
            const correoField = document.getElementById('email');
            if (correoField) {
                // Expresión regular para validar email válido (sin símbolos extraños)
                const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                
                correoField.addEventListener('blur', function() {
                    const email = this.value.trim();
                    
                    if (email && !emailPattern.test(email)) {
                        this.classList.add('is-invalid');
                        Swal.fire({
                            title: 'Correo inválido',
                            text: 'Por favor ingrese un correo electrónico válido. No se permiten símbolos extraños.',
                            icon: 'error',
                            timer: 3000
                        });
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });

                correoField.addEventListener('input', function() {
                    // Remover caracteres no permitidos en tiempo real
                    // Solo permitir: letras, números, puntos, guiones, guiones bajos, @, y %
                    const value = this.value;
                    const cleaned = value.replace(/[^a-zA-Z0-9._%+-@]/g, '');
                    
                    if (value !== cleaned) {
                        this.value = cleaned;
                    }
                });
            }

            // Validación de dropdowns (Activo y Rol)
            const activoField = document.getElementById('active');
            const rolField = document.getElementById('rol_id');
            
            [activoField, rolField].forEach(function(field) {
                if (field) {
                    field.addEventListener('change', function() {
                        if (this.value === '') {
                            this.classList.add('is-invalid');
                        } else {
                            this.classList.remove('is-invalid');
                        }
                    });
                }
            });

            // Validación antes de enviar el formulario
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    let isValid = true;

                    // Validar campos de nombre
                    nombreFields.forEach(function(fieldId) {
                        const field = document.getElementById(fieldId);
                        if (field && field.value.length > 200) {
                            field.classList.add('is-invalid');
                            isValid = false;
                        }
                    });

                    // Validar documento (alfanumérico para permitir pasaportes)
                    if (documentoField && !/^[A-Za-z0-9]+$/.test(documentoField.value)) {
                        documentoField.classList.add('is-invalid');
                        isValid = false;
                    }

                    // Validar correo
                    if (correoField) {
                        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                        if (correoField.value && !emailPattern.test(correoField.value.trim())) {
                            correoField.classList.add('is-invalid');
                            isValid = false;
                        }
                    }

                    // Validar dropdowns
                    if (activoField && activoField.value === '') {
                        activoField.classList.add('is-invalid');
                        isValid = false;
                    }

                    if (rolField && rolField.value === '') {
                        rolField.classList.add('is-invalid');
                        isValid = false;
                    }

                    if (!isValid) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Error de validación',
                            text: 'Por favor corrija los errores en el formulario antes de continuar.',
                            icon: 'error'
                        });
                        return false;
                    }
                });
            }
        });
    </script>

    <style>
        .caracteres-restantes {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.875rem;
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .is-invalid ~ .invalid-feedback {
            display: block;
        }
    </style>
@endsection