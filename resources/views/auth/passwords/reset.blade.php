@extends('layouts.auth')

@section('page-title', 'Nueva Contraseña')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <img src="{{ asset('assets/unasicono.png') }}" alt="UNAS">
            <h4>Nueva Contraseña</h4>
            <p>Crea una contraseña segura</p>
        </div>
        
        <div class="auth-body">
            @if ($errors->any())
                <div class="alert alert-danger" style="background: #fef2f2; border-color: #fecaca; color: #991b1b;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">
                
                <label class="form-label">Correo Electrónico</label>
                <div class="input-group" style="opacity: 0.7;">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" value="{{ $email }}" readonly>
                </div>
                
                <label class="form-label">Nueva Contraseña *</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                           required placeholder="Mínimo 8 caracteres">
                </div>
                
                <label class="form-label">Confirmar Contraseña *</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password_confirmation" class="form-control" 
                           required placeholder="Repite tu contraseña">
                </div>
                
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Actualizar Contraseña
                </button>
            </form>
            
            <div class="auth-footer">
                <a href="{{ route('password.request') }}">
                    <i class="fas fa-redo"></i> Solicitar nuevo enlace
                </a>
            </div>
        </div>
    </div>
</div>
@endsection