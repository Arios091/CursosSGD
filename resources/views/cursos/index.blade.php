@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Lista de Cursos</h1>

            <!-- Botón "Crear Nuevo Curso" SOLO para admin -->
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('cursos.create') }}" class="btn btn-success btn-lg px-5">
                    Crear Nuevo Curso
                </a>
            @endif
        </div>

        @if ($cursos->isEmpty())
            <div class="alert alert-info text-center py-4">
                <h4>Aún no hay cursos disponibles</h4>
                <p>¡Sé el primero en crear uno!</p>
            </div>
        @else
            <div class="row g-4">
                @foreach ($cursos as $curso)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow border-0">
                            <div class="card-body">
                                <h5 class="card-title text-primary fw-bold">
                                    {{ $curso->titulo }}
                                </h5>
                                <p class="card-text text-muted small">
                                    {{ Str::limit($curso->descripcion ?? 'Sin descripción', 100) }}
                                </p>
                                <div class="d-flex justify-content-between mt-3">
                                    <span class="badge bg-primary rounded-pill">
                                        Créditos: {{ $curso->creditos }}
                                    </span>
                                    <small class="text-muted">
                                        Por: {{ $curso->docente->name ?? 'Sin asignar' }}
                                    </small>
                                </div>

                                <!-- Botones Editar y Eliminar SOLO para admin -->
                                @if (auth()->user()->role === 'admin')
                                    <div class="mt-3 d-flex gap-2">
                                        <a href="{{ route('cursos.edit', $curso) }}" class="btn btn-sm btn-warning">
                                            Editar
                                        </a>

                                        <form action="{{ route('cursos.destroy', $curso) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que quieres eliminar este curso?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Botón Comenzar - solo si no es el docente del curso y no tiene otro en progreso -->
                        @if (auth()->user()->id !== $curso->user_id)
                            @if (!auth()->user()->cursoEnProgreso)
                                <form action="{{ route('cursos.comenzar', $curso) }}" method="POST" class="mt-3">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        Comenzar
                                    </button>
                                </form>
                            @elseif (auth()->user()->cursoEnProgreso->id === $curso->id)
                                <div class="mt-3 alert alert-success small text-center">
                                    Este es tu curso en progreso
                                </div>
                            @endif
                        @endif
                @endforeach
            </div>
        @endif
    </div>
@endsection