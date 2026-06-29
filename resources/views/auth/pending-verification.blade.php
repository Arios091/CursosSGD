@extends('layouts.app')

@section('page-title', 'Verifica tu Correo')

@section('content')
<div class="auth-wrapper d-flex align-items-center justify-content-center min-vh-100 py-4">
    <div class="col-lg-6 col-xl-5 px-3">
        <div class="card border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header text-white py-4 text-center" style="background: var(--verde-institucional); border-radius: 16px 16px 0 0;">
                <div class="mb-2">
                    <img src="{{ asset('assets/unasicono.png') }}" alt="UNAS" style="height: 60px;">
                </div>
                <h4 class="mb-1" style="font-weight: 700;">¡Casi listo!</h4>
                <p class="mb-0 opacity-75">Revisa tu correo institucional</p>
            </div>

            <div class="card-body p-4 p-lg-5 text-center">
                <div class="mb-4">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-envelope-open-text text-success" style="font-size: 32px;"></i>
                    </div>
                </div>

                <h5 class="fw-bold mb-3">Te hemos enviado un correo de verificación</h5>
                <p class="text-muted mb-2">
                    Hemos enviado un enlace de verificación a <strong>{{ session('email', 'tu correo') }}</strong>.
                </p>
                <p class="text-muted mb-4">
                    Haz clic en el botón <strong>"Verificar Correo"</strong> dentro del correo para activar tu cuenta.
                </p>

                <div class="alert alert-info text-start" role="alert">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>¿No encuentras el correo?</strong>
                    <ul class="mb-0 mt-1">
                        <li>Revisa tu bandeja de <strong>spam</strong> o correos no deseados.</li>
                        <li>Si pasaron más de 24 horas, <a href="{{ route('register') }}" class="alert-link reg-link">regístrate nuevamente</a>.</li>
                    </ul>
                </div>

                <p class="text-muted small mt-3 mb-0">
                    <i class="fas fa-shield-alt me-1"></i>
                    Tu cuenta se creará solo después de verificar tu correo.
                </p>
            </div>
        </div>

        <div class="text-center mt-4" style="color: #9ca3af; font-size: 13px;">
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
        animation: slideUp 0.5s ease;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .auth-wrapper .reg-link {
        color: var(--verde-institucional);
        font-weight: 600;
    }

    .auth-wrapper .alert {
        border-radius: 12px;
        border-left: 4px solid var(--verde-institucional);
    }
</style>
@endsection
