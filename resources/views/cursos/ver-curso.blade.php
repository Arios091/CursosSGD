@extends('layouts.app')

@section('content')
@if(session('error'))
    <div style="background: #fee2e2; color: #991b1b; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div style="background: #dcfce7; color: #166534; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

@php
    $user = auth()->user();
    $isAdmin = $user && $user->role === 'admin';
    $esEstudiante = !$isAdmin;
    
    $modulos = $curso->modulos->sortBy('orden');
    $moduloIndex = request('modulo', 0);
    $moduloActual = $modulos->values()->get($moduloIndex);
    $materiales = $moduloActual ? $moduloActual->materiales->sortBy('orden') : collect();
    
    $materialIndex = request('material', 0);
    $materialSeleccionado = $materiales->values()->get($materialIndex) ?? $materiales->first();
    
    $materialesCompletados = [];
    if ($esEstudiante && $materiales->count() > 0) {
        $materialIds = $materiales->pluck('id')->toArray();
        $materialesCompletados = \App\Models\ProgresoMaterial::where('user_id', auth()->id())
            ->whereIn('material_id', $materialIds)
            ->where('completado', true)
            ->pluck('material_id')
            ->toArray();
    }
    
    $totalMateriales = $curso->modulos->flatMap(function($m) { return $m->materiales; })->count();
    $materialesCompletadosTotal = \App\Models\ProgresoMaterial::where('user_id', auth()->id())
        ->whereIn('material_id', $curso->modulos->flatMap(function($m) { return $m->materiales->pluck('id'); }))
        ->where('completado', true)
        ->count();
    $progresoPorcentaje = $totalMateriales > 0 ? round(($materialesCompletadosTotal / $totalMateriales) * 100) : 0;
@endphp

<style>
    .course-container { display: flex; min-height: calc(100vh - 56px); }
    .sidebar { width: 300px; background: white; border-right: 1px solid #e1e5eb; flex-shrink: 0; }
    .sidebar-header { background: linear-gradient(135deg, #0B5E2E 0%, #0d7a3f 100%); padding: 20px; color: white; }
    .sidebar-header h4 { font-size: 16px; font-weight: 600; margin-bottom: 8px; }
    .module-link { display: block; padding: 14px 16px; text-decoration: none; color: #333; border-left: 4px solid transparent; }
    .module-link:hover { background: #f8f9fa; }
    .module-link.active { background: #f0f7f2; border-left-color: #0B5E2E; }
    .module-title { font-size: 13px; font-weight: 500; }
    .module-status { font-size: 11px; color: #666; }
    .main-content { flex: 1; padding: 24px; overflow-y: auto; }
    .content-header { background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
    .content-header h2 { font-size: 20px; margin-bottom: 4px; }
    .materials-section { background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
    .section-title { font-size: 16px; font-weight: 600; margin-bottom: 12px; }
    .materials-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 10px; }
    .material-card { display: flex; align-items: center; gap: 10px; padding: 12px; background: #f8f9fa; border-radius: 6px; text-decoration: none; color: #333; }
    .material-card:hover { background: #f0f7f2; }
    .material-card.active { background: #0B5E2E; color: white; }
    .material-card.completed { background: #f0fdf4; }
    .material-icon { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 6px; font-size: 14px; }
    .material-card:not(.active):not(.completed) .material-icon { background: #e1e5eb; }
    .material-card.active .material-icon { background: rgba(255,255,255,0.2); }
    .material-card.completed .material-icon { background: #22c55e; color: white; }
    .material-title { font-size: 13px; }
    .viewer-container { background: white; border-radius: 8px; overflow: hidden; margin-bottom: 20px; }
    .viewer-header { padding: 16px 20px; border-bottom: 1px solid #e1e5eb; display: flex; align-items: center; gap: 12px; }
    .viewer-icon { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; }
    .viewer-icon.video { background: #fef3c7; color: #f59e0b; }
    .viewer-icon.pdf { background: #dbeafe; color: #3b82f6; }
    .viewer-title { font-size: 16px; font-weight: 600; }
    .viewer-content iframe, .viewer-content video { width: 100%; height: 450px; border: none; }
    .actions-section { background: white; border-radius: 8px; padding: 20px; }
    .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 20px; font-size: 13px; font-weight: 500; margin-bottom: 12px; }
    .status-badge.pending { background: #fef3c7; color: #92400e; }
    .status-badge.completed { background: #dcfce7; color: #166534; }
    .btn-action { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 6px; font-size: 14px; font-weight: 500; text-decoration: none; border: none; cursor: pointer; }
    .btn-complete { background: #0B5E2E; color: white; }
    .btn-complete:hover { background: #0d7a3f; }
    .btn-next { background: white; color: #0B5E2E; border: 2px solid #0B5E2E; }
    .btn-next:hover { background: #0B5E2E; color: white; }
    .empty-state { text-align: center; padding: 60px 20px; color: #666; }
    .empty-icon { font-size: 48px; color: #d1d5db; margin-bottom: 12px; }
    @media (max-width: 768px) { .course-container { flex-direction: column; } .sidebar { width: 100%; } }
</style>

<div class="course-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h4>{{ Str::limit($curso->titulo, 30) }}</h4>
            <div style="margin-top: 10px;">
                <div style="height: 6px; background: rgba(255,255,255,0.3); border-radius: 3px; overflow: hidden;">
                    <div style="height: 100%; background: #C9A227; width: {{ $progresoPorcentaje }}%;"></div>
                </div>
                <div style="font-size: 11px; color: rgba(255,255,255,0.8); margin-top: 4px;">
                    {{ $progresoPorcentaje }}% - {{ $materialesCompletadosTotal }}/{{ $totalMateriales }}
                </div>
            </div>
        </div>
        
        <div>
            @foreach($modulos as $index => $modulo)
                @php
                    $moduloMateriales = $modulo->materiales;
                    $moduloCompletados = 0;
                    if ($esEstudiante && $moduloMateriales->count() > 0) {
                        $moduloCompletados = \App\Models\ProgresoMaterial::where('user_id', auth()->id())
                            ->whereIn('material_id', $moduloMateriales->pluck('id'))
                            ->where('completado', true)
                            ->count();
                    }
                    $moduloCompletado = $moduloCompletados == $moduloMateriales->count() && $moduloMateriales->count() > 0;
                    $esActivo = $moduloActual && $moduloActual->id == $modulo->id;
                @endphp
                
                <a href="{{ route('cursos.ver', $curso) }}?modulo={{ $index }}&material=0" class="module-link {{ $esActivo ? 'active' : '' }}">
                    <div class="module-title">
                        {{ $index + 1 }}. {{ $modulo->titulo ?: 'Módulo ' . ($index + 1) }}
                        @if($moduloCompletado)
                            <span style="color: #22c55e;">✓</span>
                        @endif
                    </div>
                    <div class="module-status">
                        {{ $moduloCompletados }}/{{ $moduloMateriales->count() }} materiales
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <div class="main-content">
        @if($moduloActual)
            <div class="content-header">
                <div style="color: #666; font-size: 14px; margin-bottom: 4px;">
                    <a href="{{ route('home') }}" style="color: #0B5E2E; text-decoration: none;">Mis Cursos</a> / {{ $curso->titulo }}
                </div>
                <h2>{{ $moduloActual->titulo ?: 'Módulo ' . $moduloActual->orden }}</h2>
                <div style="color: #666; font-size: 14px;">
                    {{ $moduloActual->materiales->count() }} materiales en este módulo
                </div>
            </div>

            @if($moduloActual->materiales->count() > 0)
            <div class="materials-section">
                <h3 class="section-title">Materiales del Módulo</h3>
                <div class="materials-grid">
                    @foreach($materiales as $mIndex => $material)
                        @php
                            $completado = in_array($material->id, $materialesCompletados);
                            $esActivo = $materialSeleccionado && $materialSeleccionado->id == $material->id;
                        @endphp
                        <a href="{{ route('cursos.ver', $curso) }}?modulo={{ $moduloIndex }}&material={{ $mIndex }}" 
                           class="material-card {{ $esActivo ? 'active' : '' }} {{ $completado ? 'completed' : '' }}">
                            <div class="material-icon">
                                {{ $material->tipo == 'video' ? '▶' : '📄' }}
                            </div>
                            <div class="material-title">{{ $material->titulo }}</div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($materialSeleccionado)
            <div class="viewer-container">
                <div class="viewer-header">
                    <div class="viewer-icon {{ $materialSeleccionado->tipo == 'video' ? 'video' : 'pdf' }}">
                        {{ $materialSeleccionado->tipo == 'video' ? '▶' : '📄' }}
                    </div>
                    <div class="viewer-title">{{ $materialSeleccionado->titulo }}</div>
                </div>
                <div class="viewer-content">
                    @if($materialSeleccionado->tipo == 'video')
                        @if($materialSeleccionado->url)
                            @php
                                $url = $materialSeleccionado->url;
                                $isYoutube = str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be') || str_contains($url, 'vimeo.com');
                            @endphp
                            @if($isYoutube)
                                @if(str_contains($url, 'youtu.be'))
                                    <iframe src="{{ str_replace('youtu.be', 'www.youtube.com/embed', $url) }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                @elseif(str_contains($url, 'vimeo.com'))
                                    <iframe src="{{ str_replace('vimeo.com/', 'player.vimeo.com/video/', $url) }}" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                                @else
                                    <iframe src="{{ str_replace('watch?v=', 'embed/', $url) }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                @endif
                            @else
                                <video controls>
                                    <source src="{{ asset('storage/'.$url) }}" type="video/mp4">
                                    Tu navegador no soporta el video.
                                </video>
                            @endif
                        @endif
                    @else
                        <iframe src="{{ asset('storage/'.$materialSeleccionado->url) }}" style="width:100%; height:500px; border:none;"></iframe>
                    @endif
                </div>
            </div>
            @endif

            @if($esEstudiante && $materialSeleccionado)
            <div class="actions-section">
                @php
                    $materialCompletado = in_array($materialSeleccionado->id, $materialesCompletados);
                    $currentMaterialIndex = $materiales->search(fn($m) => $m->id == $materialSeleccionado->id);
                    $nextMaterial = $materiales->values()->get($currentMaterialIndex + 1);
                    $todosCompletados = count($materialesCompletados) == $materiales->count();
                @endphp
                
                @if($materialCompletado)
                    <div class="status-badge completed">
                        ✓ Material completado
                    </div>
                    
                    @if($nextMaterial)
                        <a href="{{ route('cursos.ver', $curso) }}?modulo={{ $moduloIndex }}&material={{ $currentMaterialIndex + 1 }}" class="btn-action btn-next">
                            Siguiente material →
                        </a>
                    @else
                        <div style="background: #dcfce7; padding: 16px; border-radius: 8px; color: #166534; margin-bottom: 16px;">
                            ✓ Has completado todos los materiales de este módulo
                        </div>
                        
                        @if($moduloActual->cuestionario)
                            <a href="/mis-cursos/{{ $curso->id }}/modulo/{{ $moduloActual->id }}/cuestionario" 
                               class="btn-action" style="background: #C9A227; color: white; font-size: 16px; padding: 16px 32px;">
                                📝 Realizar Cuestionario del Módulo →
                            </a>
                        @else
                            @php
                                // Verificar si es el último módulo y tiene evaluación final
                                $esUltimoModulo = ($moduloIndex == ($modulos->count() - 1));
                            @endphp
                            @if($esUltimoModulo && $curso->evaluacionFinal)
                                <a href="#" onclick="document.getElementById('modalEvaluacion').style.display='block'; document.getElementById('modalEvaluacion').className='modal show'; return false;" 
                                   class="btn-action" style="background: #0B5E2E; color: white; font-size: 16px; padding: 16px 32px;">
                                    🎓 Realizar Evaluación Final →
                                </a>
                            @endif
                        @endif
                    @endif
                @else
                    <div class="status-badge pending">
                        ⏱ Material no completado
                    </div>
                    
                    <form action="{{ route('cursos.material', $curso) }}" method="POST">
                        @csrf
                        <input type="hidden" name="material_id" value="{{ $materialSeleccionado->id }}">
                        <input type="hidden" name="modulo_id" value="{{ $moduloActual->id }}">
                        <button type="submit" class="btn-action btn-complete">
                            ✓ Marcar como Completado
                        </button>
                    </form>
                @endif
            </div>
            @endif

            <!-- Evaluación Final Modal -->
            @if($curso->evaluacionFinal)
            <div id="modalEvaluacion" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
                <div style="background: white; max-width: 600px; margin: 50px auto; padding: 24px; border-radius: 12px; max-height: 80vh; overflow-y: auto;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="margin: 0; color: #0B5E2E;">{{ $curso->evaluacionFinal->titulo }}</h3>
                        <button onclick="document.getElementById('modalEvaluacion').style.display='none'" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                    </div>
                    <form action="{{ route('cursos.evaluacion', $curso) }}" method="POST">
                        @csrf
                        @foreach($curso->evaluacionFinal->preguntas as $pIndex => $pregunta)
                            <div style="margin-bottom: 16px; padding: 12px; background: #f9fafb; border-radius: 8px;">
                                <div style="font-weight: 500; margin-bottom: 8px;">
                                    <span style="background: #0B5E2E; color: white; padding: 2px 8px; border-radius: 4px; margin-right: 8px;">{{ $pIndex + 1 }}</span>
                                    {{ $pregunta->pregunta }}
                                </div>
                                @foreach($pregunta->opciones as $opcion)
                                    <div style="margin-left: 20px;">
                                        <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="{{ $opcion->id }}" id="eval{{ $pregunta->id }}_{{ $opcion->id }}">
                                        <label for="eval{{ $pregunta->id }}_{{ $opcion->id }}">{{ $opcion->opcion }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                        <button type="submit" class="btn-action btn-complete" style="width: 100%; justify-content: center;">
                            Enviar Evaluación
                        </button>
                    </form>
                </div>
            </div>
            @endif

        @else
            <div class="empty-state">
                <div class="empty-icon">📚</div>
                <div style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 8px;">Selecciona un módulo</div>
                <p>Elige un módulo del menú lateral para comenzar a estudiar</p>
            </div>
        @endif
    </div>
</div>
@endsection
