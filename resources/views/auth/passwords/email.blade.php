@extends('layouts.auth')

@section('page-title', 'Restablecer Contraseña')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <img src="{{ asset('assets/unasicono.png') }}" alt="UNAS">
            <h4>Restablecer Contraseña</h4>
            <p>Ingresa tu correo para recibir el enlace</p>
        </div>
        
        <div class="auth-body">
            @if (session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif
            
            @if ($errors->any())
                <div class="alert alert-danger" style="background: #fef2f2; border-color: #fecaca; color: #991b1b;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                
                <label class="form-label">Correo Electrónico *</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" required autofocus
                           placeholder="correo@correo.com">
                </div>
                
                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane"></i> Enviar Enlace
                </button>
            </form>
            
            <div class="auth-footer">
                <a href="{{ route('login') }}">
                    <i class="fas fa-arrow-left"></i> Volver a Iniciar Sesión
                </a>
            </div>
        </div>
    </div>
</div>
@endsection