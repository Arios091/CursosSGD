@extends('layouts.app')

@section('page-title', 'Registrarse')

@section('content')
<div class="auth-wrapper d-flex align-items-center justify-content-center min-vh-100 py-4">
    <div class="col-lg-7 col-xl-6 px-3">
        <div class="card border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header text-white py-4 text-center" style="background: linear-gradient(135deg, #0B5E2E 0%, #094525 100%); border-radius: 16px 16px 0 0;">
                <div class="mb-2">
                    <img src="{{ asset('assets/unasicono.png') }}" alt="UNAS" style="height: 60px;">
                </div>
                <h3 class="mb-1" style="font-weight: 700;">Universidad Nacional Agraria de la Selva</h3>
                <p class="mb-0 opacity-75">Sistema de Gestión de Docencia</p>
            </div>

            <div class="card-body p-4 p-lg-5">
                <form method="POST" action="{{ route('register') }}" id="registerForm" novalidate>
                    @csrf

                    <h5 class="text-muted mb-4 pb-3 border-bottom d-flex align-items-center" style="color: #0B5E2E !important;">
                        <i class="fas fa-id-card-alt me-2"></i>Información Personal
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="primer_nombre" class="form-label fw-semibold">Primer Nombre <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-user text-muted"></i></span>
                                    <input id="primer_nombre" type="text" class="form-control py-2 @error('primer_nombre') is-invalid @enderror" name="primer_nombre" value="{{ old('primer_nombre') }}" required autocomplete="primer_nombre" autofocus placeholder="Ej: Juan" style="border-radius: 0 8px 8px 0;">
                                </div>
                                @error('primer_nombre')
                                    <span class="invalid-feedback d-block" style="animation: shake 0.5s ease;">
                                        <strong><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</strong>
                                    </span>
                                @else
                                    <div class="invalid-feedback" id="primer_nombre_error" style="display: none;"></div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="segundo_nombre" class="form-label fw-semibold">Segundo Nombre</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-user text-muted"></i></span>
                                    <input id="segundo_nombre" type="text" class="form-control py-2" name="segundo_nombre" value="{{ old('segundo_nombre') }}" autocomplete="segundo_nombre" placeholder="Segundo nombre" style="border-radius: 0 8px 8px 0;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="primer_apellido" class="form-label fw-semibold">Primer Apellido <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-user text-muted"></i></span>
                                    <input id="primer_apellido" type="text" class="form-control py-2 @error('primer_apellido') is-invalid @enderror" name="primer_apellido" value="{{ old('primer_apellido') }}" required autocomplete="primer_apellido" placeholder="Ej: Pérez" style="border-radius: 0 8px 8px 0;">
                                </div>
                                @error('primer_apellido')
                                    <span class="invalid-feedback d-block" style="animation: shake 0.5s ease;">
                                        <strong><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</strong>
                                    </span>
                                @else
                                    <div class="invalid-feedback" id="primer_apellido_error" style="display: none;"></div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="segundo_apellido" class="form-label fw-semibold">Segundo Apellido <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-user text-muted"></i></span>
                                    <input id="segundo_apellido" type="text" class="form-control py-2 @error('segundo_apellido') is-invalid @enderror" name="segundo_apellido" value="{{ old('segundo_apellido') }}" required autocomplete="segundo_apellido" placeholder="Ej: García" style="border-radius: 0 8px 8px 0;">
                                </div>
                                @error('segundo_apellido')
                                    <span class="invalid-feedback d-block" style="animation: shake 0.5s ease;">
                                        <strong><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</strong>
                                    </span>
                                @else
                                    <div class="invalid-feedback" id="segundo_apellido_error" style="display: none;"></div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <h5 class="text-muted mb-4 mt-4 pb-3 border-bottom d-flex align-items-center" style="color: #0B5E2E !important;">
                        <i class="fas fa-lock me-2"></i>Credenciales de Acceso
                    </h5>

                    <div class="form-group mb-4">
                        <label for="email" class="form-label fw-semibold">Correo Electrónico <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-envelope text-muted"></i></span>
                            <input id="email" type="email" class="form-control py-2 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Ej: juan.perez@unas.edu.pe" style="border-radius: 0 8px 8px 0;">
                        </div>
                        @error('email')
                            <span class="invalid-feedback d-block" style="animation: shake 0.5s ease;">
                                <strong><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</strong>
                            </span>
                        @else
                            <div class="invalid-feedback" id="email_error" style="display: none;"></div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password" class="form-label fw-semibold">Contraseña <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input id="password" type="password" class="form-control py-2 @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Mínimo 8 caracteres" style="border-radius: 8px 0 0 8px;">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" onclick="togglePassword('password', this)" style="border-radius: 0 8px 8px 0;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback d-block" style="animation: shake 0.5s ease;">
                                        <strong><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</strong>
                                    </span>
                                @else
                                    <div class="form-text text-muted small">Mínimo 8 caracteres, una mayúscula y un número</div>
                                    <div class="invalid-feedback" id="password_error" style="display: none;"></div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password-confirm" class="form-label fw-semibold">Confirmar Contraseña <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input id="password-confirm" type="password" class="form-control py-2" name="password_confirmation" required autocomplete="new-password" placeholder="Repite tu contraseña" style="border-radius: 8px 0 0 8px;">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" onclick="togglePassword('password-confirm', this)" style="border-radius: 0 8px 8px 0;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="password_confirm_error" style="display: none;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-5 mb-0">
                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 shadow-sm" id="submitBtn" disabled style="background: #0B5E2E; border-color: #0B5E2E; font-weight: 600; border-radius: 8px; position: relative;">
                            <span class="btn-text">
                                <i class="fas fa-user-plus me-2"></i>Crear Cuenta
                            </span>
                            <span class="btn-loading-text" style="display: none;">
                                <i class="fas fa-spinner fa-spin me-2"></i>Creando cuenta...
                            </span>
                        </button>
                    </div>

                    <div class="text-center mt-4 pt-3" style="border-top: 1px solid #e5e7eb;">
                        <p class="text-muted mb-0">¿Ya tienes una cuenta?
                            <a href="{{ route('login') }}" class="text-decoration-none fw-semibold" style="color: #0B5E2E;">
                                Iniciar Sesión <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-4" style="color: #9ca3af; font-size: 13px;">
            <p class="mb-1"><i class="fas fa-shield-alt me-1"></i> Tus datos están protegidos y son confidenciales</p>
            <p class="mb-0">UNAS © {{ date('Y') }} - Todos los derechos reservados</p>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    var input = document.getElementById(inputId);
    var icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('registerForm');
    var submitBtn = document.getElementById('submitBtn');

    var fields = {
        primer_nombre: {
            element: document.getElementById('primer_nombre'),
            validate: function(value) { return value.trim().length > 0; },
            errorElement: document.getElementById('primer_nombre_error')
        },
        primer_apellido: {
            element: document.getElementById('primer_apellido'),
            validate: function(value) { return value.trim().length > 0; },
            errorElement: document.getElementById('primer_apellido_error')
        },
        segundo_apellido: {
            element: document.getElementById('segundo_apellido'),
            validate: function(value) { return value.trim().length > 0; },
            errorElement: document.getElementById('segundo_apellido_error')
        },
        email: {
            element: document.getElementById('email'),
            validate: function(value) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value); },
            errorElement: document.getElementById('email_error')
        },
        password: {
            element: document.getElementById('password'),
            validate: function(value) { return value.length >= 8 && /[A-Z]/.test(value) && /[0-9]/.test(value); },
            errorElement: document.getElementById('password_error')
        },
        password_confirmation: {
            element: document.getElementById('password-confirm'),
            validate: function(value) { return value.length > 0 && value === document.getElementById('password').value; },
            errorElement: document.getElementById('password_confirm_error')
        }
    };

    function validateField(fieldName) {
        var field = fields[fieldName];
        var value = field.element.value;
        var isValid = field.validate(value);

        if (isValid) {
            field.element.classList.remove('is-invalid');
            field.element.classList.add('is-valid');
            field.errorElement.textContent = '';
            field.errorElement.style.display = 'none';
        } else {
            field.element.classList.remove('is-valid');
            field.element.classList.add('is-invalid');

            var errorMsg = '';
            if (fieldName === 'primer_nombre') {
                errorMsg = 'El primer nombre es obligatorio. Ejemplo: Juan';
            } else if (fieldName === 'primer_apellido') {
                errorMsg = 'El primer apellido es obligatorio. Ejemplo: Pérez';
            } else if (fieldName === 'segundo_apellido') {
                errorMsg = 'El segundo apellido es obligatorio. Ejemplo: García';
            } else if (fieldName === 'email') {
                errorMsg = 'Ingresa un correo electrónico válido. Ejemplo: juan.perez@unas.edu.pe';
            } else if (fieldName === 'password') {
                if (value.length < 8) {
                    errorMsg = 'La contraseña debe tener al menos 8 caracteres';
                } else if (!/[A-Z]/.test(value)) {
                    errorMsg = 'La contraseña debe contener al menos una letra mayúscula (A-Z)';
                } else if (!/[0-9]/.test(value)) {
                    errorMsg = 'La contraseña debe contener al menos un número (0-9)';
                } else {
                    errorMsg = 'La contraseña no cumple los requisitos de seguridad';
                }
            } else if (fieldName === 'password_confirmation') {
                errorMsg = 'Las contraseñas no coinciden. Asegúrate de escribir la misma contraseña en ambos campos';
            }
            field.errorElement.textContent = errorMsg;
            field.errorElement.style.display = 'block';
        }

        return isValid;
    }

    function checkFormValidity() {
        var allValid = true;
        for (var fieldName in fields) {
            if (!validateField(fieldName)) {
                allValid = false;
            }
        }
        submitBtn.disabled = !allValid;
    }

    for (var fieldName in fields) {
        fields[fieldName].element.addEventListener('input', function(fieldName) {
            return function() {
                validateField(fieldName);
                if (fieldName === 'password') {
                    validateField('password_confirmation');
                }
                checkFormValidity();
            };
        }(fieldName));

        fields[fieldName].element.addEventListener('blur', function(fieldName) {
            return function() {
                validateField(fieldName);
                checkFormValidity();
            };
        }(fieldName));
    }

    form.addEventListener('submit', function(e) {
        checkFormValidity();
        if (submitBtn.disabled) {
            e.preventDefault();
        } else {
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<span class="btn-loading-text"><i class="fas fa-spinner fa-spin me-2"></i>Creando cuenta...</span>';
        }
    });
});
</script>

<style>
.auth-wrapper {
    background: linear-gradient(135deg, #FDF8F3 0%, #FEFBF7 50%, #FDF8F3 100%);
    min-height: 100vh;
}

.auth-wrapper .card {
    border-radius: 16px;
    overflow: hidden;
    animation: slideUp 0.5s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.auth-wrapper .card-header {
    background: linear-gradient(135deg, #0B5E2E 0%, #094525 100%) !important;
    position: relative;
    overflow: hidden;
}

.auth-wrapper .card-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #C9A227, #0B5E2E, #C9A227);
}

.auth-wrapper .form-control {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
    padding: 12px 16px;
}

.auth-wrapper .form-control:focus {
    border-color: #0B5E2E;
    box-shadow: 0 0 0 3px rgba(11, 94, 46, 0.15);
    transform: translateY(-1px);
}

.auth-wrapper .input-group:focus-within {
    box-shadow: 0 0 0 3px rgba(11, 94, 46, 0.15);
    border-radius: 8px;
}

.auth-wrapper .input-group-text {
    border-radius: 8px 0 0 8px;
    border: 1px solid #dee2e6;
    border-right: none;
    background: #fff;
    transition: all 0.3s ease;
}

.auth-wrapper .input-group:focus-within .input-group-text {
    border-color: #0B5E2E;
    background: #f0fdf4;
}

.auth-wrapper .input-group:focus-within .input-group-text .fa-user,
.auth-wrapper .input-group:focus-within .input-group-text .fa-envelope {
    color: #0B5E2E !important;
}

.auth-wrapper .toggle-password {
    border-radius: 0 8px 8px 0;
    border: 1px solid #dee2e6;
    border-left: none;
    background: #fff;
    color: #6c757d;
    transition: all 0.3s ease;
}

.auth-wrapper .toggle-password:hover {
    background: #f8f9fa;
    color: #0B5E2E;
    border-color: #0B5E2E;
}

.auth-wrapper .btn-primary {
    background: #0B5E2E;
    border-color: #0B5E2E;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    overflow: hidden;
}

.auth-wrapper .btn-primary:not(:disabled):hover {
    background: #094D25;
    border-color: #094D25;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(11, 94, 46, 0.4);
}

.auth-wrapper .btn-primary:active {
    transform: translateY(0);
}

.auth-wrapper .btn-primary:disabled {
    opacity: 0.65;
    cursor: not-allowed;
    transform: none !important;
}

.auth-wrapper .btn-primary.loading {
    pointer-events: none;
}

.auth-wrapper .form-label {
        color: #495057;
        margin-bottom: 6px;
    }

    .auth-wrapper .form-text {
        font-size: 12px;
        color: #6b7280;
    }

    .auth-wrapper .is-valid {
        border-color: #28a745 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        padding-right: calc(1.5em + 0.75rem);
    }

    .auth-wrapper .is-invalid {
        border-color: #dc3545 !important;
        animation: shake 0.5s ease;
    }

    .auth-wrapper .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
        animation: fadeIn 0.3s ease;
    }

.auth-wrapper .is-valid {
    border-color: #28a745 !important;
}

.auth-wrapper .is-invalid {
    border-color: #dc3545 !important;
    animation: shake 0.5s ease;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

.auth-wrapper .invalid-feedback {
    display: none;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

.auth-wrapper h5 {
    font-size: 0.9rem;
    font-weight: 600;
}

.auth-wrapper a:hover {
    color: #094D25 !important;
}

@media (max-width: 768px) {
    .auth-wrapper .card-body {
        padding: 1.5rem;
    }

    .auth-wrapper .card {
        border-radius: 12px;
        margin: 0 8px;
    }

    .auth-wrapper .card-header {
        border-radius: 12px 12px 0 0;
        padding: 20px;
    }

    .auth-wrapper .card-header h3 {
        font-size: 16px;
    }
}
</style>
@endsection
