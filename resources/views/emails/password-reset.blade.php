@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 40px auto; padding: 20px; font-family: Arial, sans-serif;">
    <!-- Header -->
    <div style="text-align: center; padding: 30px 0; background: #0B5E2E; border-radius: 10px 10px 0 0;">
        <img src="{{ asset('assets/unasicono.png') }}" alt="UNAS" style="height: 60px; margin-bottom: 15px;">
        <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700;">Universidad Nacional Agraria de la Selva</h1>
        <p style="color: rgba(255,255,255,0.8); margin: 5px 0 0 0; font-size: 14px;">Sistema de Gestión de Docencia</p>
    </div>

    <!-- Body -->
    <div style="padding: 40px 30px; background: #ffffff; border: 1px solid #e5e7eb; border-top: none;">
        <h2 style="color: #1f2937; font-size: 22px; font-weight: 600; margin-bottom: 20px;">🔐 Restablecimiento de Contraseña</h2>
        
        <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 25px;">
            Hola <strong>{{ $user->name }}</strong>,
        </p>
        
        <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 30px;">
            Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en el 
            <strong>Sistema de Gestión de Docencia</strong> de la Universidad Nacional Agraria de la Selva.
        </p>

        <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 30px;">
            Si no realizaste esta solicitud, puedes ignorar este correo. Tu contraseña actual seguirá siendo válida.
        </p>

        <!-- Button -->
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}" style="display: inline-block; padding: 15px 30px; background: #0B5E2E; color: #ffffff; text-decoration: none; font-weight: 600; border-radius: 8px;">
                Restablecer mi Contraseña
            </a>
        </div>

        <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin-top: 30px;">
            <strong>Nota:</strong> Este enlace expirará en <strong>60 minutos</strong> por seguridad.
        </p>
        
        <p style="color: #9ca3af; font-size: 12px; margin-top: 20px;">
            Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
            <span style="color: #0B5E2E; word-break: break-all;">{{ $resetUrl }}</span>
        </p>
    </div>

    <!-- Footer -->
    <div style="background: #f9fafb; padding: 25px 30px; text-align: center; border-radius: 0 0 10px 10px;">
        <p style="color: #6b7280; font-size: 13px; margin: 0;">
            © {{ date('Y') }} Universidad Nacional Agraria de la Selva - UNAS<br>
            Tingo María, Huánuco, Perú
        </p>
        <p style="color: #9ca3af; font-size: 12px; margin-top: 10px;">
            Este correo fue enviado automáticamente. Por favor no responder a este mensaje.
        </p>
    </div>
</div>
@endsection