@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    @php
        $user = auth()->user();
        $isAdmin = $user && $user->role === 'admin';
    @endphp
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-verde">
                <i class="fas fa-book-open me-2"></i>Catálogo de Cursos
            </h2>
            <p class="text-muted mb-0">
                @if($isAdmin)
                    Gestiona y publica cursos para tus estudiantes
                @else
                    Explora e inscríbete en los cursos disponibles
                @endif
            </p>
        </div>

        @if($isAdmin)
            <a href="{{ route('crear.curso') }}" class="btn btn-primary btn-lg shadow-sm">
                <i class="fas fa-plus-circle me-2"></i>Crear Curso
            </a>
        @endif
    </div>

    @if ($cursos->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-folder-open fa-4x text-muted mb-4" style="color: #9CA3AF;"></i>
                <h4 class="text-dark mb-3">Aún no hay cursos disponibles</h4>
                <p class="text-muted mb-4">
                    @if($isAdmin || $isDocente)
                        ¡Crea tu primer curso y comienza a enseñar!
                    @else
                        ¡Sé el primero en matricularte cuando haya cursos disponibles!
                    @endif
                </p>
                @if($isAdmin || $isDocente)
                    <a href="{{ route('crear.curso') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Crear Primer Curso
                    </a>
                @endif
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach ($cursos as $curso)
                @php
                    $progreso = \App\Models\ProgresoCurso::where('user_id', auth()->id())
                        ->where('curso_id', $curso->id)
                        ->first();
                    $esCreador = auth()->user()->id === $curso->user_id;
                @endphp
                
                <div class="col-sm-6 col-lg-4 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm" style="transition: all 0.3s ease;">
                        @if($curso->imagen_referencial)
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $curso->imagen_referencial) }}" 
                                     class="card-img-top" 
                                     alt="{{ $curso->titulo }}"
                                     style="height: 160px; object-fit: cover;">
                                @if($progreso && $progreso->estado === 'completado')
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Completado
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="card-img-top d-flex align-items-center justify-content-center position-relative" 
                                 style="height: 160px; background: linear-gradient(135deg, #0B5E2E 0%, #3B82F6 100%);">
                                <i class="fas fa-book fa-3x text-white" style="opacity: 0.7;"></i>
                                @if($progreso && $progreso->estado === 'completado')
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Completado
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        <div class="card-body pb-2">
                            <h5 class="card-title fw-bold text-dark mb-2" style="font-size: 1rem; line-height: 1.4;">
                                {{ Str::limit($curso->titulo, 45) }}
                            </h5>
                            
                            <p class="text-muted small mb-3" style="font-size: 0.85rem; line-height: 1.5;">
                                {{ Str::limit($curso->descripcion ?? 'Sin descripción disponible', 75) }}
                            </p>
                            
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge" style="background-color: var(--gris-100); color: var(--gris-700); border: 1px solid var(--gris-200);">
                                    <i class="fas fa-clock me-1"></i>{{ $curso->carga_horaria ?? 0 }}h
                                </span>
                                <span class="badge" style="background-color: var(--gris-100); color: var(--gris-700); border: 1px solid var(--gris-200);">
                                    <i class="fas fa-layer-group me-1"></i>{{ $curso->modulos->count() ?? 0 }} módulos
                                </span>
                                @if($progreso && $progreso->estado === 'en_progreso')
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-spinner me-1"></i>En progreso
                                    </span>
                                @endif
                            </div>
                            
                            <div class="d-flex align-items-center text-muted small mb-3 pb-3 border-bottom">
                                <i class="fas fa-user-tie me-2"></i>
                                <span>{{ $curso->docente->name ?? 'Sin docente' }}</span>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-transparent border-0 pt-0">
                            @if ($progreso && $progreso->estado === 'completado')
                                <div class="alert alert-success py-2 mb-2 text-center">
                                    <i class="fas fa-trophy me-1"></i>¡Curso Completado!
                                </div>
                            @elseif ($progreso && $progreso->estado === 'en_progreso')
                                <a href="{{ route('cursos.ver', $curso) }}?modulo=0&material=0" class="btn btn-success w-100 mb-2">
                                    <i class="fas fa-play me-2"></i>Continuar Curso
                                </a>
                            @elseif (!$esCreador && !$user->cursoEnProgreso)
                                <form action="{{ route('cursos.comenzar', $curso) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100 mb-2">
                                        <i class="fas fa-user-plus me-2"></i>Inscribirme
                                    </button>
                                </form>
                            @elseif ($esCreador)
                                <div class="alert alert-secondary py-2 mb-2 text-center small">
                                    <i class="fas fa-chalkboard-teacher me-1"></i>Tú creaste este curso
                                </div>
                            @else
                                <button class="btn btn-outline-secondary w-100 mb-2" disabled>
                                    <i class="fas fa-lock me-2"></i>Ya tienes un curso en progreso
                                </button>
                            @endif
                            
                            @if($isAdmin)
                                <div class="btn-group w-100" role="group">
                                    <a href="{{ route('cursos.edit', $curso) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit me-1"></i>Editar
                                    </a>
                                    <form action="{{ route('cursos.destroy', $curso) }}" method="POST" class="d-inline flex-grow-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100" 
                                                onclick="return confirm('¿Seguro que quieres eliminar este curso? Esta acción no se puede deshacer.')">
                                            <i class="fas fa-trash me-1"></i>Eliminar
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-4 text-center text-muted">
            <small>
                <i class="fas fa-info-circle me-1"></i>
                Mostrando {{ $cursos->count() }} {{ $cursos->count() == 1 ? 'curso' : 'cursos' }} disponibles
            </small>
        </div>
    @endif
</div>
@endsection
