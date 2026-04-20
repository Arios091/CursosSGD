@extends('layouts.app')

@section('page-title', 'Restablecer Contraseña')

@section('content')
<div class="auth-wrapper d-flex align-items-center justify-content-center min-vh-100 py-4">
    <div class="col-lg-6 col-xl-5 px-3">
        <div class="card border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header text-white py-4 text-center" style="background: linear-gradient(135deg, #0B5E2E 0%, #094525 100%); border-radius: 16px 16px 0 0;">
                <div class="mb-2">
                    <img src="{{ asset('assets/unasicono.png') }}" alt="UNAS" style="height: 50px;">
                </div>
                <h4 class="mb-1" style="font-weight: 700;">Restablecer Contraseña</h4>
                <p class="mb-0 opacity-75" style="font-size: 14px;">Ingresa tu correo para recibir el enlace</p>
            </div>

            <div class="card-body p-4 p-lg-5">
                @if (session('status'))
                    <div class="alert alert-success d-flex align-items-center mb-4" role="alert" style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px;">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
                    @csrf

                    <div class="form-group mb-4">
                        <label for="email" class="form-label fw-semibold">Correo Electrónico <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-envelope text-muted"></i></span>
                            <input id="email" type="email" class="form-control py-2 @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required 
                                   autocomplete="email" autofocus placeholder="correo@unas.edu.pe" 
                                   style="border-radius: 0 8px 8px 0;">
                        </div>
                        @error('email')
                            <span class="invalid-feedback d-block" role="alert" style="animation: shake 0.5s ease;">
                                <strong><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-lg w-100 py-2 text-white fw-semibold" 
                            style="background: linear-gradient(135deg, #0B5E2E 0%, #094525 100%); border-radius: 10px; border: none;">
                        <i class="fas fa-paper-plane me-2"></i> Enviar Enlace de Restablecimiento
                    </button>
                </form>

                <hr class="my-4" style="border-color: #e5e7eb;">

                <div class="text-center">
                    <p class="text-muted mb-2" style="font-size: 14px;">¿Recordaste tu contraseña?</p>
                    <a href="{{ route('login') }}" class="text-decoration-none fw-semibold" style="color: #0B5E2E;">
                        <i class="fas fa-arrow-left me-1"></i> Volver a Iniciar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

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
    border-radius: 8px 0 0 8px;
}

.form-control {
    border-left: none;
    border-radius: 0 8px 8px 0;
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

.btn:active {
    transform: translateY(0);
}

a:hover {
    color: #094525 !important;
}
</style>
@endsection