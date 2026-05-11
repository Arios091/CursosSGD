@extends('layouts.app')

@section('content')
<style>
    .quiz-container { max-width: 750px; margin: 40px auto; padding: 0 20px; }
    .quiz-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .quiz-header { background: linear-gradient(135deg, var(--dorado) 0%, #d4af37 100%); padding: 30px; color: white; }
    .quiz-header h2 { margin: 0 0 8px; font-size: 22px; }
    .quiz-header p { margin: 0; opacity: 0.9; font-size: 14px; }
    .quiz-body { padding: 30px; }
    .quiz-question { margin-bottom: 28px; padding: 20px; background: #f9fafb; border-radius: 12px; }
    .quiz-question-num { display: inline-block; background: var(--dorado); color: white; padding: 2px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; margin-right: 8px; }
    .quiz-question-text { font-size: 15px; font-weight: 600; color: #111827; margin-bottom: 14px; }
    .quiz-option { display: flex; align-items: center; padding: 12px 16px; margin-bottom: 8px; background: white; border: 2px solid #e5e7eb; border-radius: 10px; cursor: pointer; transition: all 0.2s; }
    .quiz-option:hover { border-color: var(--dorado); background: #fffbeb; }
    .quiz-option input[type="radio"] { margin-right: 12px; accent-color: var(--dorado); width: 18px; height: 18px; }
    .quiz-option label { cursor: pointer; font-size: 14px; color: #374151; flex: 1; }
    .quiz-footer { padding: 20px 30px; border-top: 1px solid #e5e7eb; text-align: center; }
    .quiz-btn { display: inline-flex; align-items: center; gap: 8px; padding: 14px 32px; background: var(--dorado); color: white; border: none; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
    .quiz-btn:hover { background: #b8911f; transform: translateY(-1px); }
    .quiz-back { display: inline-flex; align-items: center; gap: 6px; color: #6b7280; text-decoration: none; font-size: 14px; margin-bottom: 20px; }
    .quiz-back:hover { color: var(--dorado); }
</style>

<div class="quiz-container">
    <a href="{{ route('cursos.ver', $curso) }}" class="quiz-back">
        <i class="fas fa-arrow-left"></i> Volver al curso
    </a>

    <div class="quiz-card">
        <div class="quiz-header">
            <h2><i class="fas fa-clipboard-list me-2"></i>{{ $cuestionario->titulo }}</h2>
            <p>Módulo: {{ $modulo->titulo }} — Necesitas 100% para aprobar</p>
        </div>

        <form action="{{ route('cursos.cuestionario', [$curso, $modulo->id]) }}" method="POST">
            @csrf
            <div class="quiz-body">
                @foreach($cuestionario->preguntas as $pIdx => $pregunta)
                <div class="quiz-question">
                    <div class="quiz-question-text">
                        <span class="quiz-question-num">{{ $pIdx + 1 }}</span>
                        {{ $pregunta->pregunta }}
                    </div>
                    @foreach($pregunta->opciones as $opcion)
                    <div class="quiz-option">
                        <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="{{ $opcion->id }}" id="q{{ $pregunta->id }}_{{ $opcion->id }}" required>
                        <label for="q{{ $pregunta->id }}_{{ $opcion->id }}">{{ $opcion->opcion }}</label>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>

            <div class="quiz-footer">
                <button type="submit" class="quiz-btn">
                    <i class="fas fa-paper-plane"></i> Enviar Respuestas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
