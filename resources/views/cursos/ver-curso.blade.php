@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $isAdmin = $user && $user->isAdmin();
    $esDocente = $user && $user->role === 'docente';
    $esEstudiante = !$isAdmin && !$esDocente;
    
    // Use controller variables (already reindexed)
    if (!isset($modulos)) { $modulos = $curso->modulos->sortBy('orden')->values(); }
    if (!isset($moduloIndex)) { $moduloIndex = request('modulo', 0); }
    if (!isset($moduloActual)) { $moduloActual = $modulos->get($moduloIndex); }
    if (!isset($materiales)) { $materiales = $moduloActual ? $moduloActual->materiales->sortBy('orden')->values() : collect(); }
    if (!isset($materialIndex)) { $materialIndex = request('material', 0); }
    if (!isset($materialSeleccionado)) { $materialSeleccionado = $materiales->get($materialIndex) ?? $materiales->first(); }
    
    // Calcular progreso
    $materialesCompletados = [];
    if (($esEstudiante || $esDocente) && $materiales->count() > 0) {
        $materialIds = $materiales->pluck('id')->toArray();
        $materialesCompletados = \App\Models\ProgresoMaterial::where('user_id', auth()->id())
            ->whereIn('material_id', $materialIds)
            ->where('material_completado', true)
            ->pluck('material_id')
            ->toArray();
    }
    
    $totalMateriales = $curso->modulos->flatMap(fn($m) => $m->materiales)->count();
    $materialesCompletadosTotal = \App\Models\ProgresoMaterial::where('user_id', auth()->id())
        ->whereIn('material_id', $curso->modulos->flatMap(fn($m) => $m->materiales->pluck('id')))
        ->where('material_completado', true)
        ->count();
    $progresoPorcentaje = $totalMateriales > 0 ? round(($materialesCompletadosTotal / $totalMateriales) * 100) : 0;
@endphp

@if($isAdmin)
{{-- VISTA ADMIN - Lista simple de cursos --}}
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('home') }}" style="color: var(--verde-institucional); text-decoration: none;">
            <i class="fas fa-arrow-left me-1"></i> Volver al Dashboard
        </a>
    </div>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">{{ $curso->titulo }}</h4>
            <div>
                <a href="{{ route('cursos.edit', $curso) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</a>
                <form action="{{ route('cursos.destroy', $curso) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este curso?')"><i class="fas fa-trash"></i> Eliminar</button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <p><strong>Descripción:</strong> {{ $curso->descripcion }}</p>
            <p><strong>Carga Horaria:</strong> {{ $curso->carga_horaria }} horas</p>
            <p><strong>Estado:</strong> {{ $curso->estado }}</p>
        </div>
    </div>
    <h5 class="mb-3">Módulos del Curso</h5>
    @foreach($modulos as $idx => $modulo)
    <div class="card mb-3">
        <div class="card-header bg-light"><h6 class="mb-0">Módulo {{ $idx + 1 }}: {{ $modulo->titulo }}</h6></div>
        <div class="card-body">
            @if($modulo->materiales->count() > 0)
                <ul class="list-group">
                    @foreach($modulo->materiales as $mIdx => $material)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><strong>{{ $mIdx + 1 }}.</strong> {{ $material->titulo }} <span class="badge bg-{{ $material->tipo == 'video' ? 'danger' : 'info' }}">{{ $material->tipo }}</span></span>
                            @if($material->url)<small class="text-muted">{{ $material->url }}</small>@endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted mb-0">Sin materiales</p>
            @endif
            @if($modulo->cuestionario)
                <div class="mt-2"><span class="badge bg-warning text-dark">📝 Cuestionario: {{ $modulo->cuestionario->titulo }}</span></div>
            @endif
        </div>
    </div>
    @endforeach
    @if($curso->evaluacionFinal)
    <div class="card mb-3">
        <div class="card-header bg-warning text-dark"><h6 class="mb-0">🎓 Evaluación Final: {{ $curso->evaluacionFinal->titulo }}</h6></div>
        <div class="card-body"><p>{{ $curso->evaluacionFinal->preguntas->count() }} preguntas</p></div>
    </div>
    @endif
</div>

@else
{{-- VISTA ESTUDIANTE / DOCENTE - Interfaz de curso moderna --}}
<style>
    .cs-container { display: flex; min-height: calc(100vh - 56px); }
    .cs-sidebar { width: 300px; background: #fff; border-right: 1px solid #e5e7eb; flex-shrink: 0; overflow-y: auto; }
    .cs-sidebar-header { background: linear-gradient(135deg, var(--verde-institucional) 0%, #0d7a3f 100%); padding: 20px; color: #fff; }
    .cs-sidebar-header h4 { font-size: 15px; font-weight: 600; margin: 0 0 10px; }
    .cs-progress { height: 6px; background: rgba(255,255,255,0.3); border-radius: 3px; overflow: hidden; }
    .cs-progress-bar { height: 100%; background: var(--dorado); transition: width 0.3s; }
    .cs-progress-text { font-size: 11px; color: rgba(255,255,255,0.8); margin-top: 4px; }
    .cs-module-link { display: block; padding: 12px 16px; text-decoration: none; color: #374151; border-left: 3px solid transparent; transition: all 0.2s; }
    .cs-module-link:hover { background: #f9fafb; }
    .cs-module-link.active { background: #f0fdf4; border-left-color: var(--verde-institucional); }
    .cs-module-link.completed { background: #f0fdf4; }
    .cs-module-title { font-size: 13px; font-weight: 500; }
    .cs-module-meta { font-size: 11px; color: #6b7280; margin-top: 2px; }
    .cs-module-check { color: #22c55e; font-weight: bold; }
    .cs-main { flex: 1; background: #f3f4f6; padding: 24px; overflow-y: auto; }
    .cs-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 20px; overflow: hidden; }
    .cs-card-header { padding: 20px 24px; border-bottom: 1px solid #e5e7eb; }
    .cs-card-body { padding: 24px; }
    .cs-breadcrumb { font-size: 13px; color: #6b7280; margin-bottom: 4px; }
    .cs-breadcrumb a { color: var(--verde-institucional); text-decoration: none; }
    .cs-breadcrumb a:hover { text-decoration: underline; }
    .cs-module-name { font-size: 20px; font-weight: 700; color: #111827; margin: 0; }
    .cs-materials-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; }
    .cs-material-item { display: flex; align-items: center; gap: 12px; padding: 14px; background: #f9fafb; border-radius: 10px; text-decoration: none; color: #374151; border: 2px solid transparent; transition: all 0.2s; cursor: pointer; }
    .cs-material-item:hover { border-color: var(--verde-institucional); background: #f0fdf4; }
    .cs-material-item.active { background: var(--verde-institucional); color: #fff; border-color: var(--verde-institucional); }
    .cs-material-item.completed { background: #f0fdf4; border-color: #22c55e; }
    .cs-material-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
    .cs-material-icon.video { background: #fef3c7; color: #f59e0b; }
    .cs-material-icon.pdf { background: #dbeafe; color: #3b82f6; }
    .cs-material-item.active .cs-material-icon { background: rgba(255,255,255,0.2); color: #fff; }
    .cs-material-item.completed .cs-material-icon { background: #22c55e; color: #fff; }
    .cs-material-name { font-size: 13px; font-weight: 500; }
    .cs-viewer { background: #000; border-radius: 8px; overflow: hidden; }
    .cs-viewer iframe, .cs-viewer video { width: 100%; height: 480px; border: none; }
    .cs-viewer iframe[src*="drive.google.com"] { height: 600px; }
    .cs-actions { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .cs-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; }
    .cs-btn-primary { background: var(--verde-institucional); color: #fff; }
    .cs-btn-primary:hover { background: #0d7a3f; color: #fff; }
    .cs-btn-outline { background: #fff; color: var(--verde-institucional); border: 2px solid var(--verde-institucional); }
    .cs-btn-outline:hover { background: var(--verde-institucional); color: #fff; }
    .cs-btn-gold { background: var(--dorado); color: #fff; }
    .cs-btn-gold:hover { background: #b8911f; color: #fff; }
    .cs-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 500; }
    .cs-badge-success { background: #dcfce7; color: #166534; }
    .cs-badge-warning { background: #fef3c7; color: #92400e; }
    .cs-empty { text-align: center; padding: 80px 20px; }
    .cs-empty-icon { font-size: 64px; margin-bottom: 16px; }
    .cs-empty-title { font-size: 20px; font-weight: 600; color: #374151; margin-bottom: 8px; }
    .cs-empty-text { color: #6b7280; }
    .cs-quiz-banner { background: linear-gradient(135deg, var(--dorado) 0%, #d4af37 100%); color: #fff; padding: 20px 24px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; }
    .cs-quiz-banner h4 { margin: 0; font-size: 16px; }
    .cs-quiz-banner p { margin: 4px 0 0; font-size: 13px; opacity: 0.9; }
    .cs-eval-banner { background: linear-gradient(135deg, var(--verde-institucional) 0%, #0d7a3f 100%); color: #fff; padding: 20px 24px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; }
    .cs-eval-banner h4 { margin: 0; font-size: 16px; }
    .cs-eval-banner p { margin: 4px 0 0; font-size: 13px; opacity: 0.9; }
    @media (max-width: 768px) { .cs-container { flex-direction: column; } .cs-sidebar { width: 100%; } }
</style>

@if(session('success'))
<div style="background: #dcfce7; color: #166534; padding: 12px 20px; border-radius: 8px; margin-bottom: 16px; border: 1px solid #22c55e;">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background: #fee2e2; color: #991b1b; padding: 12px 20px; border-radius: 8px; margin-bottom: 16px; border: 1px solid #ef4444;">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
</div>
@endif

<div class="cs-container">
    {{-- Sidebar de módulos --}}
    <div class="cs-sidebar">
        <div class="cs-sidebar-header">
            <h4><i class="fas fa-book me-2"></i>{{ Str::limit($curso->titulo, 35) }}</h4>
            <div class="cs-progress">
                <div class="cs-progress-bar" style="width: {{ $progresoPorcentaje }}%;"></div>
            </div>
            <div class="cs-progress-text">{{ $progresoPorcentaje }}% completado ({{ $materialesCompletadosTotal }}/{{ $totalMateriales }})</div>
        </div>
        <div style="padding: 8px 0;">
            @php
                // Calculate unlocked modules: all completed modules + the first incomplete one
                $unlockedUpTo = 0;
                if ($esEstudiante || $esDocente) {
                    foreach($modulos as $mIdx => $mod) {
                        $mMats = $mod->materiales;
                        if ($mMats->count() == 0) { $unlockedUpTo = $mIdx + 1; continue; }
                        $mCompleted = \App\Models\ProgresoMaterial::where('user_id', auth()->id())->whereIn('material_id', $mMats->pluck('id'))->where('material_completado', true)->count();
                        if ($mCompleted == $mMats->count()) {
                            // Check quiz
                            if ($mod->cuestionario) {
                                $quizDone = \App\Models\ResultadoCuestionario::where('user_id', auth()->id())->where('modulo_id', $mod->id)->where('aprobado', true)->exists();
                                if ($quizDone) { $unlockedUpTo = $mIdx + 1; continue; }
                            } else {
                                $unlockedUpTo = $mIdx + 1; continue;
                            }
                        }
                        break;
                    }
                } else {
                    $unlockedUpTo = $modulos->count(); // Admin sees all
                }
            @endphp
            @foreach($modulos as $idx => $modulo)
                @php
                    $modMats = $modulo->materiales;
                    $modCompleted = ($esEstudiante || $esDocente) ? \App\Models\ProgresoMaterial::where('user_id', auth()->id())->whereIn('material_id', $modMats->pluck('id'))->where('material_completado', true)->count() : 0;
                    $modDone = $modCompleted == $modMats->count() && $modMats->count() > 0;
                    $modActive = $moduloActual && $moduloActual->id == $modulo->id;
                    $isLocked = $idx > $unlockedUpTo;
                @endphp
                @if($isLocked)
                    <div class="cs-module-link" style="opacity: 0.5; cursor: not-allowed; pointer-events: none;">
                        <div class="cs-module-title">
                            <i class="fas fa-lock" style="color: #9ca3af; margin-right: 6px; font-size: 11px;"></i>
                            {{ $idx + 1 }}. {{ $modulo->titulo ?: 'Módulo ' . ($idx + 1) }}
                        </div>
                        <div class="cs-module-meta">Bloqueado</div>
                    </div>
                @else
                    <a href="{{ route('cursos.ver', $curso) }}?modulo={{ $idx }}&material=0" class="cs-module-link {{ $modActive ? 'active' : '' }} {{ $modDone ? 'completed' : '' }}">
                        <div class="cs-module-title">
                            {{ $idx + 1 }}. {{ $modulo->titulo ?: 'Módulo ' . ($idx + 1) }}
                            @if($modDone) <span class="cs-module-check">✓</span> @endif
                        </div>
                        <div class="cs-module-meta">{{ $modCompleted }}/{{ $modMats->count() }} materiales</div>
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Contenido principal --}}
    <div class="cs-main">
        @if($moduloActual)
            {{-- Header del módulo --}}
            <div class="cs-card">
                <div class="cs-card-header">
                    <div class="cs-breadcrumb">
                        <a href="{{ route('home') }}">Inicio</a> / {{ $curso->titulo }}
                    </div>
                    <h1 class="cs-module-name">{{ $moduloActual->titulo ?: 'Módulo ' . $moduloActual->orden }}</h1>
                </div>
            </div>

            {{-- Grid de materiales --}}
            @if($materiales->count() > 0)
            <div class="cs-card">
                <div class="cs-card-header">
                    <h3 style="font-size: 16px; font-weight: 600; margin: 0; color: #374151;">📚 Materiales del Módulo</h3>
                </div>
                <div class="cs-card-body">
                    <div class="cs-materials-grid">
                        @foreach($materiales as $mIdx => $material)
                            @php
                                $matDone = in_array($material->id, $materialesCompletados);
                                $matActive = $materialSeleccionado && $materialSeleccionado->id == $material->id;
                            @endphp
                            <a href="{{ route('cursos.ver', [$curso, 'modulo' => $moduloIndex, 'material' => $mIdx]) }}" 
                               class="cs-material-item {{ $matActive ? 'active' : '' }} {{ $matDone ? 'completed' : '' }}">
                                <div class="cs-material-icon {{ $material->tipo }}">
                                    {{ $material->tipo == 'video' ? '▶' : '📄' }}
                                </div>
                                <div class="cs-material-name">{{ $material->titulo }}</div>
                                @if($matDone) <span style="margin-left: auto; color: #22c55e;">✓</span> @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Visor de material --}}
            @if($materialSeleccionado)
            <div class="cs-card">
                <div class="cs-card-header" style="display: flex; align-items: center; gap: 12px;">
                    <div class="cs-material-icon {{ $materialSeleccionado->tipo }}" style="width: 40px; height: 40px; font-size: 18px;">
                        {{ $materialSeleccionado->tipo == 'video' ? '▶' : '📄' }}
                    </div>
                    <div>
                        <div style="font-size: 16px; font-weight: 600;">{{ $materialSeleccionado->titulo }}</div>
                        <div style="font-size: 12px; color: #6b7280;">{{ $materialSeleccionado->tipo == 'video' ? 'Video' : 'Documento PDF' }}</div>
                    </div>
                </div>
                <div class="cs-card-body" style="padding: 0;">
                    <div class="cs-viewer">
                        @if($materialSeleccionado->tipo == 'video' && $materialSeleccionado->es_video_valido)
                            @php
                                $platform = $materialSeleccionado->video_platform;
                            @endphp
                            
                            @if($platform === 'google-drive')
                                {{-- Google Drive --}}
                                <iframe 
                                    src="{{ $materialSeleccionado->video_embed_url }}" 
                                    style="border:0;" 
                                    allow="autoplay" 
                                    referrerpolicy="strict-origin-when-cross-origin"
                                    title="{{ $materialSeleccionado->titulo }}">
                                </iframe>
                            @else
                                {{-- YouTube / Vimeo / otros --}}
                                <iframe 
                                    src="{{ $materialSeleccionado->video_embed_url }}" 
                                    allowfullscreen 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    referrerpolicy="strict-origin-when-cross-origin"
                                    title="{{ $materialSeleccionado->titulo }}">
                                </iframe>
                            @endif
                        @elseif($materialSeleccionado->tipo == 'video')
                            <div style="padding: 40px; text-align: center; color: #dc2626; background: #fef2f2; border-radius: 8px; margin: 20px;">
                                <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 12px;"></i>
                                <p style="font-weight: 600;">Formato de video no soportado</p>
                                <p style="font-size: 13px; color: #6b7280;">La URL ingresada no es válida o no se pudo procesar.</p>
                                @if($materialSeleccionado->url)
                                    <p style="font-size: 11px; color: #9ca3af; margin-top: 8px;">URL: {{ $materialSeleccionado->url }}</p>
                                @endif
                            </div>
                        @elseif($materialSeleccionado->tipo == 'pdf')
                            @php
                                $url = $materialSeleccionado->url;
                                // Verificar si es una URL válida o un valor inválido
                                $isValidUrl = !empty($url) && $url !== '1' && strpos($url, 'materiales/') === 0;
                            @endphp
                            
                            @if($isValidUrl)
                                @php
                                    $filename = basename($url);
                                    $pdfUrl = route('archivo.pdf', $filename);
                                    $fileExists = \Illuminate\Support\Facades\Storage::disk('public')->exists('materiales/' . $filename);
                                @endphp
                                @if($fileExists)
                                <div style="width: 100%; height: 600px; overflow: hidden;">
                                    <embed src="{{ $pdfUrl }}" type="application/pdf" style="width: 100%; height: 100%;">
                                </div>
                                <div style="padding: 12px; text-align: center; background: #f9fafb; border-top: 1px solid #e5e7eb;">
                                    <a href="{{ route('archivo.pdf.descargar', $filename) }}" target="_blank" style="color: var(--verde-institucional); font-size: 13px; text-decoration: none;">
                                        <i class="fas fa-download"></i> Descargar PDF
                                    </a>
                                </div>
                                @else
                                <div style="padding: 40px; text-align: center; color: #dc2626; background: #fef2f2; border-radius: 8px; margin: 20px 0;">
                                    <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 12px;"></i>
                                    <p style="font-weight: 600;">Archivo PDF no encontrado</p>
                                    <p style="font-size: 13px; color: #6b7280;">El archivo no se encontró en el servidor. Por favor contacta al administrador.</p>
                                </div>
                                @endif
                            @else
                            <div style="padding: 40px; text-align: center; color: #dc2626; background: #fef2f2; border-radius: 8px; margin: 20px 0;">
                                <i class="fas fa-file-pdf" style="font-size: 48px; margin-bottom: 12px;"></i>
                                <p style="font-weight: 600;">Archivo PDF no disponible</p>
                                <p style="font-size: 13px; color: #6b7280;">El archivo no fue subido correctamente durante la creación del curso.</p>
                                <p style="font-size: 12px; color: #9ca3af; margin-top: 8px;">Por favor, contacta al administrador para solucionar este problema.</p>
                            </div>
                            @endif
                        @else
                            <div style="padding: 40px; text-align: center; color: #6b7280;">
                                <i class="fas fa-file-alt" style="font-size: 48px; color: #d1d5db; margin-bottom: 12px;"></i>
                                <p>Material no disponible</p>
                                @if($materialSeleccionado->url)
                                    <small style="color: #9ca3af;">URL: {{ $materialSeleccionado->url }}</small>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Acciones del material --}}
            @if(($esEstudiante || $esDocente) && $materialSeleccionado)
            <div class="cs-card">
                <div class="cs-card-body">
                    @php
                        $matDone = in_array($materialSeleccionado->id, $materialesCompletados);
                        $curMatIdx = $materialIndex;
                        $nextMat = $materiales->get($curMatIdx + 1);
                        $nextModule = $modulos->get($moduloIndex + 1);
                        $hasQuiz = $moduloActual && $moduloActual->cuestionario;
                        $videoPlatform = $materialSeleccionado->tipo == 'video' ? $materialSeleccionado->video_platform : null;
                        
                        // Check evaluation final prerequisites
                        $allModsComplete = true;
                        $allQuizzesDone = true;
                        if ($curso->evaluacionFinal) {
                            foreach($modulos as $mod) {
                                $modMats = $mod->materiales;
                                if ($modMats->count() > 0) {
                                    $modCompleted = \App\Models\ProgresoMaterial::where('user_id', auth()->id())
                                        ->whereIn('material_id', $modMats->pluck('id'))
                                        ->where('material_completado', true)->count();
                                    if ($modCompleted < $modMats->count()) { $allModsComplete = false; }
                                }
                                if ($mod->cuestionario) {
                                    $quizDone = \App\Models\ResultadoCuestionario::where('user_id', auth()->id())
                                        ->where('modulo_id', $mod->id)->where('aprobado', true)->exists();
                                    if (!$quizDone) { $allQuizzesDone = false; }
                                }
                            }
                        }
                        $isLastModule = $moduloIndex == ($modulos->count() - 1);
                    @endphp
                    
                    {{-- PENDING state --}}
                    <div id="pending-state-{{ $materialSeleccionado->id }}" class="cs-actions" @if($matDone) style="display:none;" @endif>
                        <span class="cs-badge cs-badge-warning"><i class="fas fa-clock"></i> Material pendiente</span>
                        
                        @if($materialSeleccionado->tipo == 'video' && $videoPlatform === 'youtube')
                            <span style="color: #6b7280; font-size: 13px;">
                                <i class="fas fa-spinner fa-spin"></i> Se marcará automáticamente al finalizar el video
                            </span>
                        @elseif($materialSeleccionado->tipo == 'video')
                            <button onclick="completarManual({{ $materialSeleccionado->id }})" class="cs-btn cs-btn-primary">
                                <i class="fas fa-check"></i> Continuar
                            </button>
                            <span style="color: #6b7280; font-size: 13px;">
                                <i class="fas fa-info-circle"></i> Sin seguimiento automático
                            </span>
                        @elseif($materialSeleccionado->tipo == 'pdf')
                            <button onclick="completarManual({{ $materialSeleccionado->id }})" class="cs-btn cs-btn-primary">
                                <i class="fas fa-check"></i> Continuar
                            </button>
                            <span style="color: #6b7280; font-size: 13px;">
                                <i class="fas fa-info-circle"></i> Marca como completado al terminar de leer
                            </span>
                        @endif
                    </div>
                    
                    {{-- COMPLETED state --}}
                    <div id="completed-state-{{ $materialSeleccionado->id }}" class="cs-actions" @if(!$matDone) style="display:none;" @endif>
                        <span class="cs-badge cs-badge-success"><i class="fas fa-check"></i> Material completado</span>
                        
                        @if($nextMat)
                            <a href="{{ route('cursos.ver', [$curso, 'modulo' => $moduloIndex, 'material' => $curMatIdx + 1]) }}" class="cs-btn cs-btn-outline">
                                Siguiente: {{ $nextMat->titulo }} <i class="fas fa-arrow-right"></i>
                            </a>
                        @elseif($hasQuiz)
                            <a href="{{ route('cursos.cuestionario.ver', [$curso, $moduloActual->id]) }}" class="cs-btn cs-btn-gold">
                                <i class="fas fa-clipboard-list"></i> Realizar Cuestionario
                            </a>
                        @elseif($nextModule)
                            <a href="{{ route('cursos.ver', [$curso, 'modulo' => $moduloIndex + 1, 'material' => 0]) }}" class="cs-btn cs-btn-primary">
                                Siguiente módulo: {{ $nextModule->titulo ?: 'Módulo ' . ($moduloIndex + 2) }} <i class="fas fa-arrow-right"></i>
                            </a>
                        @elseif($isLastModule && $curso->evaluacionFinal && $allModsComplete && $allQuizzesDone)
                            <a href="{{ route('cursos.evaluacion-final', $curso) }}" class="cs-btn" style="background: var(--verde-institucional); color: #fff;">
                                <i class="fas fa-graduation-cap"></i> Realizar Evaluación Final
                            </a>
                        @else
                            <div style="background: #dcfce7; padding: 16px 20px; border-radius: 10px; color: #166534; width: 100%;">
                                <i class="fas fa-check-circle me-2"></i> ¡Has completado todos los materiales!
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Banner informativo de cuestionario (sin botón, la acción está arriba) --}}
            @if(($esEstudiante || $esDocente) && $moduloActual->cuestionario && count($materialesCompletados) == $materiales->count() && $materiales->count() > 0 && !$nextMat)
            <div class="cs-quiz-banner">
                <div>
                    <h4><i class="fas fa-clipboard-list me-2"></i>Cuestionario del Módulo</h4>
                    <p>Has completado todos los materiales. ¡Es hora de evaluar tus conocimientos!</p>
                </div>
            </div>
            @endif

            {{-- Banner informativo de evaluación final (sin botón, la acción está arriba) --}}
            @if(($esEstudiante || $esDocente) && $moduloIndex == ($modulos->count() - 1) && $curso->evaluacionFinal && !$nextMat && !$hasQuiz)
                @php
                    $allModulesDone = true;
                    foreach($modulos as $mod) {
                        $modMats = $mod->materiales;
                        if ($modMats->count() > 0) {
                            $modCompleted = \App\Models\ProgresoMaterial::where('user_id', auth()->id())->whereIn('material_id', $modMats->pluck('id'))->where('material_completado', true)->count();
                            if ($modCompleted < $modMats->count()) { $allModulesDone = false; break; }
                        }
                        if ($mod->cuestionario) {
                            $modQuizDone = \App\Models\ResultadoCuestionario::where('user_id', auth()->id())->where('modulo_id', $mod->id)->where('aprobado', true)->exists();
                            if (!$modQuizDone) { $allModulesDone = false; break; }
                        }
                    }
                @endphp
                @if($allModulesDone)
                <div class="cs-eval-banner">
                    <div>
                        <h4><i class="fas fa-graduation-cap me-2"></i>Evaluación Final</h4>
                        <p>¡Has completado todo el curso! Realiza la evaluación final para obtener tu certificado.</p>
                    </div>
                </div>
                @endif
            @endif

        @else
            <div class="cs-empty">
                <div class="cs-empty-icon">📚</div>
                <div class="cs-empty-title">Selecciona un módulo</div>
                <div class="cs-empty-text">Elige un módulo del menú lateral para comenzar a estudiar</div>
            </div>
        @endif
    </div>
</div>

<script>
let videoInterval = null;
let tiempoVisto = 0;
let videoCompletado = false;
let videoDuration = 0;
let requiredTime = 0;
let materialActualId = {{ $materialSeleccionado ? $materialSeleccionado->id : 0 }};

// Toggle UI entre pendiente y completado
function toggleMaterialCompletado(materialId) {
    console.log('[UI] toggleMaterialCompletado called for materialId:', materialId);
    var pending = document.getElementById('pending-state-' + materialId);
    var completed = document.getElementById('completed-state-' + materialId);
    console.log('[UI] pending element found:', !!pending, '| completed element found:', !!completed);
    if (pending) pending.style.display = 'none';
    if (completed) completed.style.display = 'flex';
}

// Detectar cuando se carga un video
function initVideoTracking() {
    console.log('[VIDEO] initVideoTracking started');
    
    function createPlayer() {
        console.log('[VIDEO] createPlayer called');
        var iframe = document.querySelector('iframe');
        if (!iframe) { console.log('[VIDEO] ERROR: no iframe found in DOM'); return; }
        console.log('[VIDEO] iframe src:', iframe.src);
        if (iframe.src.indexOf('youtube') === -1) { console.log('[VIDEO] ERROR: iframe src is not youtube'); return; }
        if (videoCompletado) { console.log('[VIDEO] already completed, skipping'); return; }
        
        try {
            var player = new YT.Player(iframe, {
                events: {
                    'onStateChange': onPlayerStateChange,
                    'onReady': onPlayerReady
                }
            });
            console.log('[VIDEO] YT.Player created successfully');
        } catch(e) {
            console.log('[VIDEO] ERROR creating YT.Player:', e.message);
            return;
        }
        
        function onPlayerReady(event) {
            videoDuration = player.getDuration();
            requiredTime = videoDuration * 0.9;
            console.log('[VIDEO] Player ready, duration:', videoDuration, 'requiredTime:', requiredTime);
        }
        
        function onPlayerStateChange(event) {
            console.log('[VIDEO] State change:', event.data, '(PLAYING=1, PAUSED=2, ENDED=0)');
            if (event.data === YT.PlayerState.ENDED && !videoCompletado) {
                console.log('[VIDEO] Video ENDED, marking complete');
                videoCompletado = true;
                if (videoInterval) clearInterval(videoInterval);
                marcarVideoCompletado();
                return;
            }
            if (event.data === YT.PlayerState.PLAYING && !videoCompletado) {
                console.log('[VIDEO] Video PLAYING, starting interval');
                videoInterval = setInterval(function() {
                    tiempoVisto++;
                    
                    if (tiempoVisto % 10 === 0) {
                        updateVideoProgress();
                    }
                    
                    if (requiredTime > 0 && tiempoVisto >= requiredTime) {
                        console.log('[VIDEO] 90% reached at tiempoVisto=' + tiempoVisto + ', requiredTime=' + requiredTime);
                        videoCompletado = true;
                        clearInterval(videoInterval);
                        marcarVideoCompletado();
                    }
                }, 1000);
            } else if (event.data === YT.PlayerState.PAUSED) {
                console.log('[VIDEO] Video PAUSED, clearing interval');
                if (videoInterval) {
                    clearInterval(videoInterval);
                }
            }
        }
    }
    
    function marcarVideoCompletado() {
        console.log('[VIDEO] marcarVideoCompletado called, materialId:', materialActualId);
        updateVideoProgressFinal();
        toggleMaterialCompletado(materialActualId);
    }
    
    // Si API ya cargada, crear player directamente
    if (window.YT && typeof YT.Player === 'function') {
        console.log('[VIDEO] YT API already loaded, creating player directly');
        createPlayer();
        return;
    }
    
    console.log('[VIDEO] YT API not loaded, will wait for onYouTubeIframeAPIReady');
    var prevCallback = window.onYouTubeIframeAPIReady;
    window.onYouTubeIframeAPIReady = function() {
        console.log('[VIDEO] onYouTubeIframeAPIReady fired');
        if (typeof prevCallback === 'function') prevCallback();
        createPlayer();
    };
    
    if (!window.YT) {
        console.log('[VIDEO] Loading YouTube IFrame API script');
        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    }
}

// Actualizar progreso de video en servidor
function updateVideoProgress() {
    if (!materialActualId) return;
    fetch('/material/' + materialActualId + '/video-progress', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            tiempo_visto: tiempoVisto,
            duracion_total: Math.round(videoDuration)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.video_completado) {
            videoCompletado = true;
            toggleMaterialCompletado(materialActualId);
        }
    });
}

// Marcar video como completado final
function updateVideoProgressFinal() {
    if (!materialActualId) return;
    fetch('/material/' + materialActualId + '/video-progress', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            tiempo_visto: tiempoVisto,
            duracion_total: Math.round(videoDuration),
            completado: true
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.video_completado) {
            toggleMaterialCompletado(materialActualId);
        }
    });
}

// Detectar scroll en PDF
function initPdfScrollTracking() {
    const embed = document.querySelector('embed[type="application/pdf"]');
    if (!embed) return;
    
    let scrollTimeout = null;
    let scrollCompletado = false;
    
    const container = embed.parentElement;
    if (!container) return;
    
    container.addEventListener('scroll', function() {
        if (scrollCompletado) return;
        
        const scrollTop = container.scrollTop;
        const scrollHeight = container.scrollHeight - container.clientHeight;
        
        if (scrollHeight > 0 && (scrollTop / scrollHeight) >= 0.9) {
            scrollCompletado = true;
            
            if (scrollTimeout) clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(function() {
                marcarPdfCompletado();
            }, 1000);
        }
    });
}

function marcarPdfCompletado() {
    if (!materialActualId) return;
    fetch('/material/' + materialActualId + '/pdf-scroll', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.scroll_completado) {
            toggleMaterialCompletado(materialActualId);
        }
    });
}

// Botón "Continuar" para videos sin auto-seguimiento (Google Drive, Vimeo)
function completarManual(materialId) {
    if (!materialId) return;
    fetch('/material/' + materialId + '/pdf-scroll', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.scroll_completado) {
            toggleMaterialCompletado(materialId);
        }
    });
}

// Inicializar cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    @if($materialSeleccionado && $materialSeleccionado->tipo == 'video' && ($materialSeleccionado->video_platform ?? '') === 'youtube')
        setTimeout(initVideoTracking, 1000);
    @elseif($materialSeleccionado && $materialSeleccionado->tipo == 'pdf')
        initPdfScrollTracking();
    @endif
});
</script>

@endif
@endsection