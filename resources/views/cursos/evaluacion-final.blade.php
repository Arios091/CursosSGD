@extends('layouts.app')

@section('content')
<style>
    .eval-container { max-width: 750px; margin: 40px auto; padding: 0 20px; }
    .eval-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .eval-header { background: linear-gradient(135deg, var(--verde-institucional) 0%, #0d7a3f 100%); padding: 30px; color: white; text-align: center; }
    .eval-header h2 { margin: 0 0 8px; font-size: 22px; }
    .eval-header p { margin: 0; opacity: 0.9; font-size: 14px; }
    .eval-body { padding: 30px; }
    .eval-question { margin-bottom: 28px; padding: 20px; background: #f9fafb; border-radius: 12px; }
    .eval-question-num { display: inline-block; background: var(--verde-institucional); color: white; padding: 2px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; margin-right: 8px; }
    .eval-question-text { font-size: 15px; font-weight: 600; color: #111827; margin-bottom: 14px; }
    .eval-option { display: flex; align-items: center; padding: 12px 16px; margin-bottom: 8px; background: white; border: 2px solid #e5e7eb; border-radius: 10px; cursor: pointer; transition: all 0.2s; }
    .eval-option:hover { border-color: var(--verde-institucional); background: #f0fdf4; }
    .eval-option input[type="radio"] { margin-right: 12px; accent-color: var(--verde-institucional); width: 18px; height: 18px; }
    .eval-option label { cursor: pointer; font-size: 14px; color: #374151; flex: 1; }
    .eval-footer { padding: 20px 30px; border-top: 1px solid #e5e7eb; text-align: center; }
    .eval-btn { display: inline-flex; align-items: center; gap: 8px; padding: 14px 32px; background: var(--verde-institucional); color: white; border: none; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
    .eval-btn:hover { background: #0d7a3f; transform: translateY(-1px); }
    .eval-back { display: inline-flex; align-items: center; gap: 6px; color: #6b7280; text-decoration: none; font-size: 14px; margin-bottom: 20px; }
    .eval-back:hover { color: var(--verde-institucional); }
    .eval-warning { background: #fef3c7; border: 1px solid #f59e0b; border-radius: 10px; padding: 16px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; }
    .eval-warning i { color: #f59e0b; font-size: 20px; }
    .eval-warning p { margin: 0; font-size: 14px; color: #92400e; }
</style>

<div class="eval-container">
    <a href="{{ route('cursos.ver', $curso) }}" class="eval-back">
        <i class="fas fa-arrow-left"></i> Volver al curso
    </a>

    <div class="eval-card">
        <div class="eval-header">
            <h2><i class="fas fa-graduation-cap me-2"></i>{{ $evaluacion->titulo }}</h2>
            <p>Evaluación Final del Curso — Necesitas 80% para aprobar</p>
        </div>

        <form action="{{ route('cursos.evaluacion-final.enviar', $curso) }}" method="POST">
            @csrf
            <div class="eval-body">
                <div class="eval-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Esta es la evaluación final del curso. Si apruebas con al menos 80%, recibirás tu certificado.</p>
                </div>

                @foreach($evaluacion->preguntas as $pIdx => $pregunta)
                <div class="eval-question">
                    <div class="eval-question-text">
                        <span class="eval-question-num">{{ $pIdx + 1 }}</span>
                        {{ $pregunta->pregunta }}
                    </div>
                    @foreach($pregunta->opciones as $opcion)
                    <div class="eval-option">
                        <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="{{ $opcion->id }}" id="ef{{ $pregunta->id }}_{{ $opcion->id }}" required>
                        <label for="ef{{ $pregunta->id }}_{{ $opcion->id }}">{{ $opcion->opcion }}</label>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>

            <div class="eval-footer">
                <button type="submit" class="eval-btn">
                    <i class="fas fa-paper-plane"></i> Enviar Evaluación
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
