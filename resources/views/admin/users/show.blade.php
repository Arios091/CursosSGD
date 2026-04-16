@extends('layouts.app')

@section('page-title', 'Detalle de Usuario')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-4">
            <!-- Tarjeta de perfil -->
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center py-5">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 28px; font-weight: 600;">
                        {{ substr($user->primer_nombre ?? 'U', 0, 1) }}{{ substr($user->primer_apellido ?? '', 0, 1) }}
                    </div>
                    <h4>{{ $user->primer_nombre }} {{ $user->primer_apellido }}</h4>
                    @if($user->segundo_nombre || $user->segundo_apellido)
                        <p class="text-muted mb-1">{{ $user->segundo_nombre }} {{ $user->segundo_apellido }}</p>
                    @endif
                    <span class="badge badge-primary px-3 py-2" style="background: #0B5E2E;">
                        {{ $user->role === 'admin' ? 'Administrador' : ($user->role === 'docente' ? 'Docente' : 'Estudiante') }}
                    </span>
                    <p class="text-muted mt-2 mb-0"><small>Registrado {{ $user->created_at->diffForHumans() }}</small></p>
                </div>
            </div>

            <!-- Restablecer contraseña -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0" style="color: #0B5E2E;">
                        <i class="fas fa-key mr-2"></i>Restablecer Contraseña
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('success_password'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success_password') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="password">Nueva Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Mínimo 8 caracteres</small>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required minlength="8">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block" style="background: #0B5E2E; border-color: #0B5E2E;">
                            <i class="fas fa-save mr-2"></i>Guardar Nueva Contraseña
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Editar usuario -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0" style="color: #0B5E2E;">
                        <i class="fas fa-edit mr-2"></i>Editar Usuario
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="primer_nombre">Primer Nombre *</label>
                                    <input type="text" name="primer_nombre" id="primer_nombre" class="form-control @error('primer_nombre') is-invalid @enderror" value="{{ old('primer_nombre', $user->primer_nombre) }}" required>
                                    @error('primer_nombre')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="segundo_nombre">Segundo Nombre</label>
                                    <input type="text" name="segundo_nombre" id="segundo_nombre" class="form-control" value="{{ old('segundo_nombre', $user->segundo_nombre) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="primer_apellido">Primer Apellido *</label>
                                    <input type="text" name="primer_apellido" id="primer_apellido" class="form-control @error('primer_apellido') is-invalid @enderror" value="{{ old('primer_apellido', $user->primer_apellido) }}" required>
                                    @error('primer_apellido')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="segundo_apellido">Segundo Apellido *</label>
                                    <input type="text" name="segundo_apellido" id="segundo_apellido" class="form-control @error('segundo_apellido') is-invalid @enderror" value="{{ old('segundo_apellido', $user->segundo_apellido) }}" required>
                                    @error('segundo_apellido')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Correo Electrónico *</label>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role">Rol *</label>
                                    <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrador</option>
                                        <option value="docente" {{ old('role', $user->role) === 'docente' ? 'selected' : '' }}>Docente</option>
                                        <option value="estudiante" {{ old('role', $user->role) === 'estudiante' ? 'selected' : '' }}>Estudiante</option>
                                    </select>
                                    @error('role')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary" style="background: #0B5E2E; border-color: #0B5E2E;">
                                <i class="fas fa-save mr-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Cursos del usuario -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0" style="color: #0B5E2E;">
                        <i class="fas fa-book mr-2"></i>Cursos del Usuario
                    </h5>
                </div>
                <div class="card-body">
                    @if($user->progresos->count() > 0)
                        <div class="list-group">
                            @foreach($user->progresos as $progreso)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $progreso->curso->titulo ?? 'Curso eliminado' }}</strong>
                                    @if($progreso->curso)
                                        <br><small class="text-muted">{{ $progreso->curso->modulos->count() }} módulos</small>
                                    @endif
                                </div>
                                @if($progreso->estado === 'completado')
                                    <span class="badge badge-success">Completado</span>
                                @else
                                    <span class="badge badge-primary">En Progreso</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-muted mb-0 py-3">
                            <i class="fas fa-book-open fa-2x mb-2"></i>
                            <br>Este usuario no está inscrito en ningún curso
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
