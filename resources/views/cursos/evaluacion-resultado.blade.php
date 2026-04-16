@extends('layouts.app')

@section('content')
<style>
    .eval-result-container { max-width: 750px; margin: 40px auto; padding: 0 20px; }
    .eval-result-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .eval-result-header { padding: 30px; text-align: center; color: white; }
    .eval-result-header.aprobado { background: linear-gradient(135deg, #0B5E2E 0%, #0d7a3f 100%); }
    .eval-result-header.reprobado { background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); }
    .eval-result-icon { font-size: 48px; margin-bottom: 12px; }
    .eval-result-header h2 { margin: 0 0 8px; font-size: 24px; }
    .eval-result-header p { margin: 0; opacity: 0.9; }
    .eval-result-score { font-size: 36px; font-weight: 700; margin: 12px 0 0; }
    .eval-result-body { padding: 30px; }
    .eval-result-question { margin-bottom: 24px; padding: 20px; border-radius: 12px; border: 2px solid; }
    .eval-result-question.correct { border-color: #22c55e; background: #f0fdf4; }
    .eval-result-question.incorrect { border-color: #ef4444; background: #fef2f2; }
    .eval-q-header { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
    .eval-q-badge { padding: 2px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; color: white; }
    .eval-q-badge.correct { background: #22c55e; }
    .eval-q-badge.incorrect { background: #ef4444; }
    .eval-q-text { font-size: 15px; font-weight: 600; color: #111827; }
    .eval-result-options { margin-left: 10px; }
    .eval-result-option { padding: 8px 14px; margin-bottom: 6px; border-radius: 8px; font-size: 14px; }
    .eval-result-option.correct-answer { background: #dcfce7; color: #166534; border-left: 3px solid #22c55e; }
    .eval-result-option.neutral { background: #f9fafb; color: #6b7280; }
    .eval-result-footer { padding: 20px 30px; border-top: 1px solid #e5e7eb; text-align: center; display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
    .eval-result-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; }
    .eval-result-btn-primary { background: #0B5E2E; color: white; }
    .eval-result-btn-primary:hover { background: #0d7a3f; color: white; }
    .eval-result-btn-gold { background: #C9A227; color: white; }
    .eval-result-btn-gold:hover { background: #b8911f; color: white; }
    .eval-result-btn-outline { background: white; color: #374151; border: 2px solid #e5e7eb; }
    .eval-result-btn-outline:hover { border-color: #0B5E2E; color: #0B5E2E; }
</style>

<div class="eval-result-container">
    <a href="{{ route('cursos.ver', $curso) }}" style="display: inline-flex; align-items: center; gap: 6px; color: #6b7280; text-decoration: none; font-size: 14px; margin-bottom: 20px;">
        <i class="fas fa-arrow-left"></i> Volver al curso
    </a>

    <div class="eval-result-card">
        <div class="eval-result-header {{ $resultado->aprobado ? 'aprobado' : 'reprobado' }}">
            <div class="eval-result-icon">{{ $resultado->aprobado ? '🎓' : '😔' }}</div>
            <h2>{{ $resultado->aprobado ? '¡Evaluación Aprobada!' : 'Evaluación No Aprobada' }}</h2>
            <p>{{ $evaluacion->titulo }} — {{ $curso->titulo }}</p>
            <div class="eval-result-score">{{ $resultado->nota }}%</div>
            <p style="margin-top: 4px;">Necesitas 80% para aprobar</p>
        </div>

        <div class="eval-result-body">
            <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: #374151;">Revisión de respuestas:</h4>
            
            @foreach($evaluacion->preguntas as $pIdx => $pregunta)
                @php
                    $opcionCorrecta = $pregunta->opciones->firstWhere('es_correcta', true);
                @endphp
                <div class="eval-result-question">
                    <div class="eval-q-header">
                        <span class="eval-q-badge">Pregunta {{ $pIdx + 1 }}</span>
                        <span class="eval-q-text">{{ $pregunta->pregunta }}</span>
                    </div>
                    <div class="eval-result-options">
                        @foreach($pregunta->opciones as $opcion)
                            @if($opcion->es_correcta)
                                <div class="eval-result-option correct-answer">
                                    <i class="fas fa-check" style="color: #22c55e; margin-right: 8px;"></i>
                                    <strong>{{ $opcion->opcion }}</strong> <span style="color: #22c55e; font-size: 12px;">(Respuesta correcta)</span>
                                </div>
                            @else
                                <div class="eval-result-option neutral">{{ $opcion->opcion }}</div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="eval-result-footer">
            @if($resultado->aprobado)
                <a href="{{ route('cursos.completado', $curso) }}" class="eval-result-btn eval-result-btn-primary">
                    <i class="fas fa-trophy"></i> Ver Felicitaciones
                </a>
                <a href="{{ route('certificado', $curso) }}" class="eval-result-btn eval-result-btn-gold">
                    <i class="fas fa-certificate"></i> Descargar Certificado
                </a>
            @else
                <a href="{{ route('cursos.evaluacion-final', $curso) }}" class="eval-result-btn eval-result-btn-gold">
                    <i class="fas fa-redo"></i> Intentar de Nuevo
                </a>
                <a href="{{ route('cursos.ver', $curso) }}" class="eval-result-btn eval-result-btn-outline">
                    <i class="fas fa-arrow-left"></i> Volver al Curso
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
