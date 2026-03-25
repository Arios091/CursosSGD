@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0">
                        <i class="fas fa-user-circle me-2"></i>
                        Bienvenido, {{ Auth::user()->name }}!
                        <span class="badge bg-light text-primary ms-2">{{ ucfirst(Auth::user()->role) }}</span>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <p class="lead mb-2">Sistema de Gestión de Cursos</p>
                            <p class="text-muted mb-0">
                                Explora los cursos disponibles, inscríbete y aprende a tu propio ritmo.
                                @if(Auth::user()->role === 'admin')
                                    También puedes crear y gestionar cursos.
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-book-open me-2"></i>Ver Cursos
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if(Auth::user()->role === 'admin')
                <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                    <div class="card-body py-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="text-success mb-1">
                                    <i class="fas fa-plus-circle me-2"></i>¿Tienes conocimiento para compartir?
                                </h5>
                                <p class="text-muted mb-0">
                                    Crea un nuevo curso y ayuda a otros a aprender.
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="{{ route('crear.curso') }}" class="btn btn-success btn-lg">
                                    <i class="fas fa-plus me-2"></i>Crear Curso
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @php
                $misProgresos = \App\Models\ProgresoCurso::where('user_id', Auth::id())
                    ->with('curso')
                    ->orderBy('created_at', 'desc')
                    ->limit(3)
                    ->get();
            @endphp

            @if($misProgresos->count() > 0)
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-graduation-cap me-2 text-primary"></i>Mis Cursos Recientes
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($misProgresos as $progreso)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $progreso->curso->titulo }}</h6>
                                            <small class="text-muted">
                                                @if($progreso->estado === 'completado')
                                                    <span class="badge bg-success">Completado</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">En progreso</span>
                                                @endif
                                            </small>
                                        </div>
                                        @if($progreso->estado === 'en_progreso')
                                            <a href="{{ route('cursos.ver', $progreso->curso) }}" class="btn btn-sm btn-primary">
                                                Continuar
                                            </a>
                                        @else
                                            <span class="text-success">
                                                <i class="fas fa-trophy"></i>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
