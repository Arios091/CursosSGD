@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    @php
        $user = auth()->user();
    @endphp
    
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-header text-white d-flex justify-content-between align-items-center" 
                     style="background: linear-gradient(135deg, #C9A227 0%, #C9A227 100%);">
                    <h4 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>
                        {{ $cuestionario->titulo }}
                    </h4>
                    <a href="{{ route('cursos.ver', $curso) }}" class="btn btn-sm btn-light">
                        <i class="fas fa-arrow-left me-1"></i> Volver al curso
                    </a>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($resultado && $resultado->aprobado)
                        <div class="text-center py-5">
                            <i class="fas fa-trophy text-success fa-5x mb-4"></i>
                            <h2 class="text-dark">¡Aprobado!</h2>
                            <p class="text-muted">Tu nota: <strong>{{ $resultado->nota }}%</strong></p>
                            <p class="text-muted">Has aprobado el cuestionario del módulo "{{ $modulo->titulo }}"</p>
                            
                            @php
                                $esUltimoModulo = $siguienteModuloIndex >= $curso->modulos->count();
                                $tieneEvaluacionFinal = !empty($curso->evaluacionFinal);
                            @endphp
                            
                            @if($esUltimoModulo && $tieneEvaluacionFinal)
                                <a href="{{ route('cursos.ver', $curso) }}#evaluacion" class="btn btn-success btn-lg" style="background: #C9A227; border-color: #C9A227;">
                                    <i class="fas fa-graduation-cap me-2"></i> Realizar Evaluación Final
                                </a>
                            @elseif(isset($siguienteModuloIndex) && $siguienteModuloIndex < $curso->modulos->count())
                                <a href="{{ route('cursos.ver', $curso) }}?modulo={{ $siguienteModuloIndex }}&material=0" class="btn btn-success btn-lg">
                                    <i class="fas fa-arrow-right me-2"></i> Continuar al siguiente módulo
                                </a>
                            @else
                                <a href="{{ route('cursos.ver', $curso) }}" class="btn btn-success btn-lg">
                                    <i class="fas fa-home me-2"></i> Volver al curso
                                </a>
                            @endif
                        </div>
                    @elseif($resultado)
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong>Nota: {{ $resultado->nota }}%</strong> - Necesitas {{ $cuestionario->min_aprobacion }}% para aprobar
                        </div>
                        <form action="{{ route('cursos.cuestionario', [$curso, $modulo->id]) }}" method="POST">
                            @csrf
                            @foreach($cuestionario->preguntas as $pIndex => $pregunta)
                                <div class="mb-4 p-3 border rounded" style="background: #F9FAFB;">
                                    <h6 class="mb-3 text-dark">
                                        <span class="badge" style="background: #C9A227;">{{ $pIndex + 1 }}</span>
                                        {{ $pregunta->pregunta }}
                                    </h6>
                                    @foreach($pregunta->opciones as $opcion)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" 
                                                   name="respuestas[{{ $pregunta->id }}]"
                                                   id="preg{{ $pregunta->id }}_{{ $opcion->id }}"
                                                   value="{{ $opcion->id }}" required>
                                            <label class="form-check-label text-dark" for="preg{{ $pregunta->id }}_{{ $opcion->id }}">
                                                {{ $opcion->opcion }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                            <div class="text-center">
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar Respuestas
                                </button>
                            </div>
                        </form>
                    @else
                        <form action="{{ route('cursos.cuestionario', [$curso, $modulo->id]) }}" method="POST">
                            @csrf
                            @foreach($cuestionario->preguntas as $pIndex => $pregunta)
                                <div class="mb-4 p-3 border rounded" style="background: #F9FAFB;">
                                    <h6 class="mb-3 text-dark">
                                        <span class="badge" style="background: #C9A227;">{{ $pIndex + 1 }}</span>
                                        {{ $pregunta->pregunta }}
                                    </h6>
                                    @foreach($pregunta->opciones as $opcion)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" 
                                                   name="respuestas[{{ $pregunta->id }}]"
                                                   id="preg{{ $pregunta->id }}_{{ $opcion->id }}"
                                                   value="{{ $opcion->id }}" required>
                                            <label class="form-check-label text-dark" for="preg{{ $pregunta->id }}_{{ $opcion->id }}">
                                                {{ $opcion->opcion }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                            <div class="text-center">
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar Respuestas
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
