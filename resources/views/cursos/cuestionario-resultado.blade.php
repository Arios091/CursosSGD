@extends('layouts.app')

@section('content')
<style>
    .result-container { max-width: 750px; margin: 40px auto; padding: 0 20px; }
    .result-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .result-header { padding: 30px; text-align: center; color: white; }
    .result-header.aprobado { background: linear-gradient(135deg, var(--verde-institucional) 0%, #0d7a3f 100%); }
    .result-header.reprobado { background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); }
    .result-icon { font-size: 48px; margin-bottom: 12px; }
    .result-header h2 { margin: 0 0 8px; font-size: 24px; }
    .result-header p { margin: 0; opacity: 0.9; }
    .result-score { font-size: 36px; font-weight: 700; margin: 12px 0 0; }
    .result-body { padding: 30px; }
    .result-question { margin-bottom: 24px; padding: 20px; border-radius: 12px; border: 2px solid; }
    .result-question.correct { border-color: #22c55e; background: #f0fdf4; }
    .result-question.incorrect { border-color: #ef4444; background: #fef2f2; }
    .result-q-header { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
    .result-q-badge { padding: 2px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; color: white; }
    .result-q-badge.correct { background: #22c55e; }
    .result-q-badge.incorrect { background: #ef4444; }
    .result-q-text { font-size: 15px; font-weight: 600; color: #111827; }
    .result-options { margin-left: 10px; }
    .result-option { padding: 8px 14px; margin-bottom: 6px; border-radius: 8px; font-size: 14px; }
    .result-option.selected-correct { background: #dcfce7; color: #166534; border-left: 3px solid #22c55e; }
    .result-option.selected-wrong { background: #fee2e2; color: #991b1b; border-left: 3px solid #ef4444; text-decoration: line-through; }
    .result-option.correct-answer { background: #dcfce7; color: #166534; border-left: 3px solid #22c55e; }
    .result-option.neutral { background: #f9fafb; color: #6b7280; }
    .result-footer { padding: 20px 30px; border-top: 1px solid #e5e7eb; text-align: center; display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
    .result-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; }
    .result-btn-primary { background: var(--verde-institucional); color: white; }
    .result-btn-primary:hover { background: #0d7a3f; color: white; }
    .result-btn-gold { background: var(--dorado); color: white; }
    .result-btn-gold:hover { background: #b8911f; color: white; }
    .result-btn-outline { background: white; color: #374151; border: 2px solid #e5e7eb; }
    .result-btn-outline:hover { border-color: var(--verde-institucional); color: var(--verde-institucional); }
</style>

<div class="result-container">
    <a href="{{ route('cursos.ver', $curso) }}" style="display: inline-flex; align-items: center; gap: 6px; color: #6b7280; text-decoration: none; font-size: 14px; margin-bottom: 20px;">
        <i class="fas fa-arrow-left"></i> Volver al curso
    </a>

    <div class="result-card">
        <div class="result-header {{ $resultado->aprobado ? 'aprobado' : 'reprobado' }}">
            <div class="result-icon">{{ $resultado->aprobado ? '🎉' : '😔' }}</div>
            <h2>{{ $resultado->aprobado ? '¡Aprobado!' : 'No Aprobado' }}</h2>
            <p>{{ $cuestionario->titulo }} — Módulo: {{ $modulo->titulo }}</p>
            <div class="result-score">{{ $resultado->nota }}%</div>
            <p style="margin-top: 4px;">Necesitas 100% para aprobar</p>
        </div>

        <div class="result-body">
            <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: #374151;">Revisión de respuestas:</h4>
            
            @foreach($cuestionario->preguntas as $pIdx => $pregunta)
                @php
                    $opcionCorrecta = $pregunta->opciones->firstWhere('es_correcta', true);
                    // Find user's last attempt answer (if any stored)
                    $userAnswerId = null;
                    $isCorrect = false;
                @endphp
                <div class="result-question {{ $opcionCorrecta && $opcionCorrecta->id == $opcionCorrecta->id ? '' : '' }}">
                    @php
                        // Check if we have the answer from the request session or we show all options
                        $isCorrect = false;
                    @endphp
                    <div class="result-q-header">
                        <span class="result-q-badge {{ $isCorrect ? 'correct' : 'incorrect' }}">
                            {{ $isCorrect ? '✓ Correcta' : '? Pendiente' }}
                        </span>
                        <span class="result-q-text">{{ $pIdx + 1 }}. {{ $pregunta->pregunta }}</span>
                    </div>
                    <div class="result-options">
                        @foreach($pregunta->opciones as $opcion)
                            @if($opcion->es_correcta)
                                <div class="result-option correct-answer">
                                    <i class="fas fa-check" style="color: #22c55e; margin-right: 8px;"></i>
                                    <strong>{{ $opcion->opcion }}</strong> <span style="color: #22c55e; font-size: 12px;">(Respuesta correcta)</span>
                                </div>
                            @else
                                <div class="result-option neutral">{{ $opcion->opcion }}</div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="result-footer">
            @if($resultado->aprobado)
                @if($siguienteModuloIndex < $curso->modulos->count())
                    <a href="{{ route('cursos.ver', $curso) }}?modulo={{ $siguienteModuloIndex }}&material=0" class="result-btn result-btn-primary">
                        <i class="fas fa-arrow-right"></i> Siguiente Módulo
                    </a>
                @elseif($curso->evaluacionFinal)
                    <a href="{{ route('cursos.evaluacion-final', $curso) }}" class="result-btn result-btn-gold">
                        <i class="fas fa-graduation-cap"></i> Evaluación Final
                    </a>
                @else
                    <a href="{{ route('cursos.ver', $curso) }}" class="result-btn result-btn-primary">
                        <i class="fas fa-home"></i> Volver al Curso
                    </a>
                @endif
            @else
                <a href="{{ route('cursos.cuestionario.ver', [$curso, $modulo->id]) }}" class="result-btn result-btn-gold">
                    <i class="fas fa-redo"></i> Intentar de Nuevo
                </a>
                <a href="{{ route('cursos.ver', $curso) }}" class="result-btn result-btn-outline">
                    <i class="fas fa-arrow-left"></i> Volver al Curso
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
