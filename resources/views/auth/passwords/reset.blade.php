@extends('layouts.app')

@section('page-title', 'Nueva Contraseña')

@section('content')
<div class="auth-wrapper d-flex align-items-center justify-content-center min-vh-100 py-4">
    <div class="col-lg-6 col-xl-5 px-3">
        <div class="card border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header text-white py-4 text-center" style="background: linear-gradient(135deg, #0B5E2E 0%, #094525 100%); border-radius: 16px 16px 0 0;">
                <div class="mb-2">
                    <img src="{{ asset('assets/unasicono.png') }}" alt="UNAS" style="height: 50px;">
                </div>
                <h4 class="mb-1" style="font-weight: 700;">Nueva Contraseña</h4>
                <p class="mb-0 opacity-75" style="font-size: 14px;">Crea una contraseña segura</p>
            </div>

            <div class="card-body p-4 p-lg-5">
                <form method="POST" action="{{ route('password.update') }}" id="resetForm">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="form-group mb-4">
                        <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                            <input id="email" type="email" class="form-control py-2 bg-light" 
                                   value="{{ $email }}" readonly 
                                   style="border-radius: 0 8px 8px 0; color: #6b7280;">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="password" class="form-label fw-semibold">Nueva Contraseña <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-lock text-muted"></i></span>
                            <input id="password" type="password" class="form-control py-2 @error('password') is-invalid @enderror" 
                                   name="password" required autocomplete="new-password" placeholder="Mínimo 8 caracteres"
                                   style="border-radius: 0 8px 8px 0;">
                            <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword('password')" style="border-radius: 0 8px 8px 0;">
                                <i class="fas fa-eye" id="toggle-password-icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="invalid-feedback d-block" role="alert" style="animation: shake 0.5s ease;">
                                <strong><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</strong>
                            </span>
                        @else
                            <small class="text-muted">Mínimo 8 caracteres. Combina letras, números y símbolos.</small>
                        @endif
                    </div>

                    <div class="form-group mb-4">
                        <label for="password-confirm" class="form-label fw-semibold">Confirmar Contraseña <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-lock text-muted"></i></span>
                            <input id="password-confirm" type="password" class="form-control py-2" 
                                   name="password_confirmation" required autocomplete="new-password" 
                                   placeholder="Repite tu contraseña"
                                   style="border-radius: 0 8px 8px 0;">
                            <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword('password-confirm')" style="border-radius: 0 8px 8px 0;">
                                <i class="fas fa-eye" id="toggle-password-confirm-icon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-lg w-100 py-2 text-white fw-semibold mb-3" 
                            style="background: linear-gradient(135deg, #0B5E2E 0%, #094525 100%); border-radius: 10px; border: none;">
                        <i class="fas fa-save me-2"></i> Actualizar Contraseña
                    </button>
                </form>

                <hr class="my-4" style="border-color: #e5e7eb;">

                <div class="text-center">
                    <p class="text-muted mb-2" style="font-size: 14px;">¿Necesitas otro enlace?</p>
                    <a href="{{ route('password.request') }}" class="text-decoration-none fw-semibold" style="color: #0B5E2E;">
                        <i class="fas fa-redo me-1"></i> Solicitar nuevo enlace
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById('toggle-' + inputId + '-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<style>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

.auth-wrapper {
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    min-height: 100vh;
}

.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.input-group-text {
    border-right: none;
}

.form-control {
    border-left: none;
}

.input-group:focus-within {
    box-shadow: 0 0 0 3px rgba(11, 94, 46, 0.1);
}

.form-control:focus {
    border-color: #0B5E2E;
    box-shadow: 0 0 0 3px rgba(11, 94, 46, 0.1);
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(11, 94, 46, 0.3);
}

a:hover {
    color: #094525 !important;
}
</style>
@endsection