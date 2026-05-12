@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('breadcrumbs')
    <span><i class="fas fa-chevron-right"></i></span>
    <span style="color: var(--gris-800);">Inicio</span>
@endsection

@section('content')
@php
    $user = auth()->user();
    $isAdmin = $user->isAdmin();
    $isAdminGlobal = $user->isAdminGlobal();
    $esDocente = $user->isDocente();

    if ($isAdmin || $isAdminGlobal) {
        $totalUsuarios = \App\Models\User::count();
        $totalDocentes = \App\Models\User::where('role', 'docente')->count();
        $totalEstudiantes = \App\Models\User::where('role', 'estudiante')->count();
        $totalAdminsGlobales = \App\Models\User::where('role', 'admin_global')->count();
        $totalAdmins = \App\Models\User::where('role', 'admin')->count();
        $totalCursos = \App\Models\Curso::count();
        
        $totalInscripciones = \App\Models\ProgresoCurso::count();
        $cursosEnProgreso = \App\Models\ProgresoCurso::where('estado', 'en_progreso')->count();
        $cursosCompletados = \App\Models\ProgresoCurso::whereIn('estado', ['completado', 'terminado'])->count();
        
        $cursosPopulares = \App\Models\Curso::withCount('progresos')->orderBy('progresos_count', 'desc')->take(5)->get();
        $cursosData = \App\Models\Curso::withCount('progresos')->get();
    } else {
        $cursosEnProgreso = \App\Models\ProgresoCurso::where('user_id', $user->id)
            ->where('estado', 'en_progreso')
            ->with('curso')
            ->get();

        $cursosCompletados = \App\Models\ProgresoCurso::where('user_id', $user->id)
            ->where('estado', 'completado')
            ->with('curso')
            ->get();

        $horasEstudiadas = $cursosCompletados->sum(function($progreso) {
            return $progreso->curso ? $progreso->curso->carga_horaria : 0;
        });

        $cursoIdsInscritos = \App\Models\ProgresoCurso::where('user_id', $user->id)
            ->pluck('curso_id')
            ->toArray();

        $cursosDisponibles = \App\Models\Curso::whereNotIn('id', $cursoIdsInscritos)->get();
        $cursoEnProgresoActual = $user->cursoEnProgreso;
    }
@endphp

@if($isAdmin)
<div style="margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 24px; font-weight: 700; color: #1f2937; margin-bottom: 4px;">Dashboard</h2>
            <p style="color: #6b7280; margin: 0;">Resumen del sistema de gestión de cursos</p>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--verde-institucional), #0d7a3f); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-book" style="color: white; font-size: 20px;"></i>
            </div>
            <div>
                <div class="stat-number" style="font-size: 24px; font-weight: 700; color: #1f2937;" data-target="{{ $totalCursos }}">0</div>
                <div style="font-size: 13px; color: #6b7280;">Cursos Totales</div>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #3b82f6, #60a5fa); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-users" style="color: white; font-size: 20px;"></i>
            </div>
            <div>
                <div class="stat-number" style="font-size: 24px; font-weight: 700; color: #1f2937;" data-target="{{ $totalUsuarios }}">0</div>
                <div style="font-size: 13px; color: #6b7280;">Usuarios</div>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b, #fbbf24); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-pen" style="color: white; font-size: 20px;"></i>
            </div>
            <div>
                <div class="stat-number" style="font-size: 24px; font-weight: 700; color: #1f2937;" data-target="{{ $totalDocentes }}">0</div>
                <div style="font-size: 13px; color: #6b7280;">Docentes</div>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #8b5cf6, #a78bfa); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user-graduate" style="color: white; font-size: 20px;"></i>
            </div>
            <div>
                <div class="stat-number" style="font-size: 24px; font-weight: 700; color: #1f2937;" data-target="{{ $totalEstudiantes }}">0</div>
                <div style="font-size: 13px; color: #6b7280;">Estudiantes</div>
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981, #34d399); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-clipboard-list" style="color: white; font-size: 20px;"></i>
            </div>
            <div>
                <div class="stat-number" style="font-size: 24px; font-weight: 700; color: #1f2937;" data-target="{{ $totalInscripciones }}">0</div>
                <div style="font-size: 13px; color: #6b7280;">Inscripciones</div>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #3b82f6, #60a5fa); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-spinner" style="color: white; font-size: 20px;"></i>
            </div>
            <div>
                <div class="stat-number" style="font-size: 24px; font-weight: 700; color: #1f2937;" data-target="{{ $cursosEnProgreso }}">0</div>
                <div style="font-size: 13px; color: #6b7280;">En Progreso</div>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #16a34a, #22c55e); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-check-circle" style="color: white; font-size: 20px;"></i>
            </div>
            <div>
                <div class="stat-number" style="font-size: 24px; font-weight: 700; color: #1f2937;" data-target="{{ $cursosCompletados }}">0</div>
                <div style="font-size: 13px; color: #6b7280;">Completados</div>
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 24px; margin-bottom: 24px;">
    <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <h3 style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-chart-pie" style="color: var(--verde-institucional);"></i>
            Progreso de Estudiantes
        </h3>
        <div style="position: relative; height: 250px;">
            <canvas id="progresoChart"></canvas>
        </div>
    </div>

    <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <h3 style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-trophy" style="color: var(--dorado);"></i>
            Cursos Más Populares
        </h3>
        <div style="position: relative; height: 250px;">
            <canvas id="cursosChart"></canvas>
        </div>
    </div>
</div>

<div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <h3 style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-list" style="color: var(--verde-institucional);"></i>
        Detalle de Cursos
    </h3>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead>
                <tr style="background: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                    <th style="text-align: left; padding: 12px 16px; color: #374151; font-weight: 600;">Curso</th>
                    <th style="text-align: center; padding: 12px 16px; color: #374151; font-weight: 600;">Inscritos</th>
                    <th style="text-align: center; padding: 12px 16px; color: #374151; font-weight: 600;">En Progreso</th>
                    <th style="text-align: center; padding: 12px 16px; color: #374151; font-weight: 600;">Completados</th>
                    <th style="text-align: center; padding: 12px 16px; color: #374151; font-weight: 600;">% Tasa Final</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cursosData as $curso)
                @php
                    $inscritos = $curso->progresos_count;
                    $enProgreso = $curso->progresos()->where('estado', 'en_progreso')->count();
                    $completados = $curso->progresos()->whereIn('estado', ['completado', 'terminado'])->count();
                    $tasa = $inscritos > 0 ? round(($completados / $inscritos) * 100) : 0;
                @endphp
                <tr style="border-bottom: 1px solid #e5e7eb; transition: background 0.2s;">
                    <td style="padding: 12px 16px; color: #1f2937;">{{ $curso->titulo }}</td>
                    <td style="padding: 12px 16px; text-align: center;">
                        <span class="badge" style="background: #dbeafe; color: #1d4ed8; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500;">{{ $inscritos }}</span>
                    </td>
                    <td style="padding: 12px 16px; text-align: center;">
                        <span class="badge" style="background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500;">{{ $enProgreso }}</span>
                    </td>
                    <td style="padding: 12px 16px; text-align: center;">
                        <span class="badge" style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500;">{{ $completados }}</span>
                    </td>
                    <td style="padding: 12px 16px; text-align: center;">
                        <div style="display: inline-flex; align-items: center; gap: 8px;">
                            <div style="width: 60px; height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden;">
                                <div style="width: {{ $tasa }}%; height: 100%; background: {{ $tasa >= 70 ? '#16a34a' : ($tasa >= 40 ? '#f59e0b' : '#ef4444') }}; border-radius: 3px;"></div>
                            </div>
                            <span style="font-size: 12px; color: #6b7280; min-width: 35px;">{{ $tasa }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 24px; text-align: center; color: #6b7280;">No hay cursos registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    animateCounters();

    const progresoCtx = document.getElementById('progresoChart').getContext('2d');
    new Chart(progresoCtx, {
        type: 'doughnut',
                data: {
                    labels: ['En Progreso', 'Completados'],
                    datasets: [{
                        data: [{{ $cursosEnProgreso }}, {{ $cursosCompletados }}],
                        backgroundColor: ['#3b82f6', '#16a34a'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 12,
                            cornerRadius: 8
                        }
                    }
                }
    });

    const cursosLabels = {!! json_encode($cursosPopulares->pluck('titulo')->map(function($t) { return Str::limit($t, 20); })->toArray()) !!};
    const cursosInscriptos = {!! json_encode($cursosPopulares->pluck('progresos_count')->toArray()) !!};
    
    const cursosCtx = document.getElementById('cursosChart').getContext('2d');
    new Chart(cursosCtx, {
        type: 'bar',
        data: {
            labels: cursosLabels.length > 0 ? cursosLabels : ['Sin datos'],
            datasets: [{
                label: 'Estudiantes',
                data: cursosInscriptos.length > 0 ? cursosInscriptos : [0],
                backgroundColor: [
                    'rgba(11, 94, 46, 0.8)',
                    'rgba(201, 162, 39, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(236, 72, 153, 0.8)'
                ],
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    padding: 12,
                    cornerRadius: 8
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { display: false },
                    ticks: { 
                        stepSize: 1,
                        color: '#6b7280'
                    }
                },
                y: {
                    grid: { display: false },
                    ticks: { 
                        color: '#374151'
                    }
                }
            }
        }
    });
});
</script>

@else
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 50px; height: 50px; background: #dcfce7; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-check-circle" style="color: #16a34a; font-size: 24px;"></i>
            </div>
            <div>
                <div class="stat-number" style="font-size: 28px; font-weight: 700; color: #1f2937;" data-target="{{ $cursosCompletados->count() }}">0</div>
                <div style="font-size: 14px; color: #6b7280;">Cursos Completados</div>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 50px; height: 50px; background: #fef3c7; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-clock" style="color: #f59e0b; font-size: 24px;"></i>
            </div>
            <div>
                <div class="stat-number" style="font-size: 28px; font-weight: 700; color: #1f2937;" data-target="{{ $horasEstudiadas }}">0</div>
                <div style="font-size: 14px; color: #6b7280;">Horas de Estudio</div>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 50px; height: 50px; background: #dbeafe; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-spinner" style="color: #3b82f6; font-size: 24px;"></i>
            </div>
            <div>
                <div class="stat-number" style="font-size: 28px; font-weight: 700; color: #1f2937;" data-target="{{ $cursosEnProgreso->count() }}">0</div>
                <div style="font-size: 14px; color: #6b7280;">Cursos en Progreso</div>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 50px; height: 50px; background: #f3e8ff; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-book" style="color: #9333ea; font-size: 24px;"></i>
            </div>
            <div>
                <div class="stat-number" style="font-size: 28px; font-weight: 700; color: #1f2937;" data-target="{{ $cursosDisponibles->count() }}">0</div>
                <div style="font-size: 14px; color: #6b7280;">Cursos Disponibles</div>
            </div>
        </div>
    </div>
</div>

@if($cursoEnProgresoActual)
@php
    $progresoActual = \App\Models\ProgresoCurso::where('user_id', $user->id)
        ->where('curso_id', $cursoEnProgresoActual->id)
        ->first();

    $totalMateriales = $cursoEnProgresoActual->modulos->flatMap(function($m) { return $m->materiales; })->count();
    $materialesCompletados = \App\Models\ProgresoMaterial::where('user_id', $user->id)
        ->whereIn('material_id', $cursoEnProgresoActual->modulos->flatMap(function($m) { return $m->materiales->pluck('id'); }))
        ->where('material_completado', true)
        ->count();
    $porcentajeProgreso = $totalMateriales > 0 ? round(($materialesCompletados / $totalMateriales) * 100) : 0;
@endphp
<div style="background: linear-gradient(135deg, var(--verde-institucional) 0%, #0d7a3f 100%); border-radius: 16px; padding: 24px; margin-bottom: 30px; color: white;" class="continue-course-card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="font-size: 14px; opacity: 0.8; margin-bottom: 4px;">CONTINUAR CURSO</div>
            <div style="font-size: 20px; font-weight: 600;">{{ $cursoEnProgresoActual->titulo }}</div>
            <div style="font-size: 14px; opacity: 0.8; margin-top: 4px;">{{ $porcentajeProgreso }}% completado</div>
        </div>
        <a href="{{ route('cursos.ver', $cursoEnProgresoActual) }}" style="background: var(--dorado); color: var(--verde-institucional); padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
            <i class="fas fa-play"></i> Continuar
        </a>
    </div>
    <div style="margin-top: 16px; background: rgba(255,255,255,0.2); border-radius: 8px; height: 8px; overflow: hidden;">
        <div style="background: var(--dorado); height: 100%; width: {{ $porcentajeProgreso }}%; transition: width 0.5s ease;"></div>
    </div>
</div>
@endif

@if($cursosDisponibles->count() > 0)
<div style="margin-bottom: 30px;">
    <h3 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-book" style="color: var(--verde-institucional);"></i> Cursos Disponibles
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        @foreach($cursosDisponibles as $curso)
        <div class="course-card" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s; cursor: pointer;">
            @if($curso->imagen_referencial)
            <img src="{{ asset('storage/'.$curso->imagen_referencial) }}" style="width: 100%; height: 160px; object-fit: cover;" alt="{{ $curso->titulo }}">
            @else
            <div style="width: 100%; height: 160px; background: linear-gradient(135deg, var(--verde-institucional) 0%, #0d7a3f 100%); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-book" style="color: white; font-size: 48px;"></i>
            </div>
            @endif
            <div style="padding: 20px;">
                <h4 style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 8px;">{{ $curso->titulo }}</h4>
                <p style="font-size: 13px; color: #6b7280; margin-bottom: 12px; line-height: 1.5;">{{ Str::limit($curso->descripcion, 100) }}</p>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="font-size: 13px; color: var(--verde-institucional); font-weight: 500;">
                        <i class="fas fa-clock" style="margin-right: 4px;"></i>{{ $curso->carga_horaria }} horas
                    </span>
                    <span style="font-size: 13px; color: #6b7280;">
                        <i class="fas fa-layer-group" style="margin-right: 4px;"></i>{{ $curso->modulos->count() }} módulos
                    </span>
                </div>
                <button type="button" onclick="inscribirse(this)" data-curso-id="{{ $curso->id }}" data-curso-titulo="{{ $curso->titulo }}" class="btn-enroll" style="width: 100%; background: var(--verde-institucional); color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s;">
                    <i class="fas fa-plus"></i> Inscribirse
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>
@else
<div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 12px; padding: 40px; text-align: center;">
    <div style="width: 80px; height: 80px; background: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
        <i class="fas fa-check-circle" style="color: white; font-size: 40px;"></i>
    </div>
    <h4 style="color: #1f2937; margin-bottom: 8px;">¡Estás al día!</h4>
    <p style="color: #6b7280;">No hay cursos disponibles nuevos por ahora.</p>
</div>
@endif

@if($cursosEnProgreso->count() > 0 && !$cursoEnProgresoActual)
<div style="margin-bottom: 30px;">
    <h3 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-spinner" style="color: #3b82f6;"></i> Cursos en Progreso
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        @foreach($cursosEnProgreso as $progreso)
        @if($progreso->curso)
        <a href="{{ route('cursos.ver', $progreso->curso) }}" class="course-card" style="display: block; background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-decoration: none; transition: all 0.3s;">
            <h4 style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 8px;">{{ $progreso->curso->titulo }}</h4>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 13px; color: #6b7280;">
                    <i class="fas fa-layer-group" style="margin-right: 4px;"></i>{{ $progreso->curso->modulos->count() }} módulos
                </span>
                <span style="background: #dbeafe; color: #3b82f6; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                    En Progreso
                </span>
            </div>
        </a>
        @endif
        @endforeach
    </div>
</div>
@endif
@endif

<style>
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.course-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.12);
}

.continue-course-card:hover {
    transform: scale(1.01);
}

.btn-enroll:hover {
    background: #094D25 !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(11, 94, 46, 0.3);
}

.btn-enroll:active {
    transform: translateY(0);
}

.btn-enroll.loading {
    pointer-events: none;
    opacity: 0.7;
}

.btn-enroll.loading::after {
    content: '';
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid transparent;
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin-left: 8px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

@keyframes countUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.stat-number {
    animation: countUp 0.5s ease forwards;
}

tr:hover {
    background-color: #f9fafb !important;
}
</style>

<script>
function animateCounters() {
    const counters = document.querySelectorAll('.stat-number');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target')) || 0;
        const duration = 1500;
        const step = target / (duration / 16);
        let current = 0;

        const updateCounter = () => {
            current += step;
            if (current < target) {
                counter.textContent = Math.floor(current);
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target;
            }
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCounter();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        observer.observe(counter);
    });
}

function inscribirse(btn) {
    var cursoId = btn.getAttribute('data-curso-id');
    var url = '/cursos/' + cursoId + '/comenzar';

    btn.classList.add('loading');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inscribiendo...';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(function(response) {
        return response.json().then(function(data) {
            if (!response.ok) {
                throw data;
            }
            return data;
        });
    })
    .then(function(data) {
        if (data.redirect) {
            window.location.href = data.redirect;
        } else if (data.success) {
            showToast('success', '¡Inscripción exitosa!', 'Has sido inscrito en el curso.');
            setTimeout(() => window.location.reload(), 1500);
        }
    })
    .catch(function(error) {
        btn.classList.remove('loading');
        btn.innerHTML = '<i class="fas fa-plus"></i> Inscribirse';

        if (error.error) {
            showToast('warning', 'No puedes inscribirte', error.error);
        } else {
            showToast('error', 'Error', 'Ocurrió un error. Por favor intenta nuevamente.');
        }
    });
}
</script>
@endsection
