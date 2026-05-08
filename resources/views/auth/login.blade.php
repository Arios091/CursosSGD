@extends('layouts.app')

@section('page-title', 'Iniciar Sesión')

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
                <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
                    @csrf

                    <div class="form-group mb-4">
                        <label for="email" class="form-label fw-semibold">Correo Electrónico <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-envelope text-muted"></i></span>
                            <input id="email" type="email" class="form-control py-2 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="correo@unas.edu.pe" style="border-radius: 0 8px 8px 0;">
                        </div>
                        @error('email')
                            <span class="invalid-feedback d-block" role="alert" style="animation: shake 0.5s ease;">
                                <strong><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="password" class="form-label fw-semibold">Contraseña <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input id="password" type="password" class="form-control py-2 @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="********" style="border-radius: 8px 0 0 8px;">
                            <button class="btn btn-outline-secondary toggle-password" type="button" onclick="togglePassword('password', this)" style="border-radius: 0 8px 8px 0;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="invalid-feedback d-block" role="alert" style="animation: shake 0.5s ease;">
                                <strong><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Recordarme
                            </label>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 shadow-sm" id="loginBtn" style="background: #0B5E2E; border-color: #0B5E2E; font-weight: 600; border-radius: 8px; position: relative;">
                            <span class="btn-text">
                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                            </span>
                            <span class="btn-loading-text" style="display: none;">
                                <i class="fas fa-spinner fa-spin me-2"></i>Iniciando sesión...
                            </span>
                        </button>
                    </div>

                    @if (Route::has('password.request'))
                        <div class="text-center">
                            <a href="{{ route('password.request') }}" class="text-decoration-none" style="color: #0B5E2E;">
                                <i class="fas fa-key me-1"></i> ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    @endif
                </form>

                <div class="text-center mt-4 pt-3" style="border-top: 1px solid #e5e7eb;">
                    <p class="text-muted mb-0">¿No tienes cuenta?
                        <a href="{{ route('register') }}" class="text-decoration-none fw-semibold" style="color: #0B5E2E;">
                            Regístrate <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <div class="text-center mt-4" style="color: #9ca3af; font-size: 13px;">
            <p class="mb-1"><i class="fas fa-shield-alt me-1"></i> Sistema seguro con conexión encriptada</p>
            <p class="mb-0">UNAS © {{ date('Y') }} - Todos los derechos reservados</p>
        </div>
    </div>
</div>

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

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
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

    .auth-wrapper .input-group .form-control:focus ~ .input-group-text,
    .auth-wrapper .input-group:focus-within .input-group-text {
        border-color: #0B5E2E;
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

    .auth-wrapper .btn-primary:hover {
        background: #094D25;
        border-color: #094D25;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(11, 94, 46, 0.4);
    }

    .auth-wrapper .btn-primary:active {
        transform: translateY(0);
    }

    .auth-wrapper .btn-primary.loading {
        pointer-events: none;
        background: #0B5E2E;
    }

    .auth-wrapper .btn-primary.loading .btn-text {
        opacity: 0;
    }

    .auth-wrapper .btn-primary.loading .btn-loading-text {
        display: inline-flex !important;
        align-items: center;
    }

    .auth-wrapper .form-label {
        color: #495057;
        margin-bottom: 6px;
        transition: color 0.3s;
    }

    .auth-wrapper .form-control:focus ~ .input-group-text .fa-envelope,
    .auth-wrapper .input-group:focus-within .input-group-text .fa-envelope {
        color: #0B5E2E !important;
        transition: color 0.3s;
    }

    .auth-wrapper .form-check-input {
        width: 18px;
        height: 18px;
        margin-top: 2px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .auth-wrapper .form-check-input:checked {
        background-color: #0B5E2E;
        border-color: #0B5E2E;
    }

    .auth-wrapper .form-check-input:focus {
        box-shadow: 0 0 0 3px rgba(11, 94, 46, 0.15);
    }

    .auth-wrapper .form-check-label {
        cursor: pointer;
        color: #6b7280;
        transition: color 0.2s;
    }

    .auth-wrapper .form-check-label:hover {
        color: #0B5E2E;
    }

    .auth-wrapper a:hover {
        color: #094D25 !important;
    }

    .auth-wrapper .invalid-feedback {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .auth-wrapper .card-header {
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

    .input-group:focus-within {
        box-shadow: 0 0 0 3px rgba(11, 94, 46, 0.15);
        border-radius: 8px;
    }
</style>

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

    document.getElementById('loginForm')?.addEventListener('submit', function(e) {
        const btn = document.getElementById('loginBtn');
        btn.classList.add('loading');

        setTimeout(() => {
            if (!btn.classList.contains('loading')) return;
        }, 2000);
    });

    const inputs = document.querySelectorAll('.auth-wrapper .form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.closest('.form-group')?.classList.add('focused');
        });
        input.addEventListener('blur', () => {
            input.closest('.form-group')?.classList.remove('focused');
        });
    });
</script>
@endsection
