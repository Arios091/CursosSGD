@extends('layouts.app')

@section('page-title', 'Perfil')

@section('content')
@php
    $user = auth()->user();
    $isAdmin = $user->isAdmin();
    $isAdminGlobal = $user->isAdminGlobal();
    
    // Obtener cursos completados para mostrar certificados
    $cursosCompletados = \App\Models\ProgresoCurso::where('user_id', $user->id)
        ->where('estado', 'completado')
        ->with('curso')
        ->get();
@endphp

<div style="max-width: 800px; margin: 0 auto;">
    <!-- Información del Usuario -->
    <div style="background: white; border-radius: 16px; padding: 30px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <div style="display: flex; align-items: center; gap: 24px; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #e5e7eb;">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--verde-institucional) 0%, #0d7a3f 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 32px; font-weight: 600;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h2 style="font-size: 24px; font-weight: 600; color: #1f2937; margin-bottom: 4px;">{{ $user->name }}</h2>
                <span style="background: {{ $isAdmin ? 'var(--dorado)' : 'var(--verde-institucional)' }}; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                    {{ $isAdminGlobal ? 'Admin Global' : ($isAdmin ? 'Administrador' : 'Estudiante') }}
                </span>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
            <div>
                <label style="font-size: 12px; color: #6b7280; font-weight: 500; display: block; margin-bottom: 4px;">Correo Electrónico</label>
                <div style="font-size: 14px; color: #1f2937; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-envelope" style="color: var(--verde-institucional);"></i>
                    {{ $user->email }}
                </div>
            </div>
            <div>
                <label style="font-size: 12px; color: #6b7280; font-weight: 500; display: block; margin-bottom: 4px;">Usuario desde</label>
                <div style="font-size: 14px; color: #1f2937; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-calendar" style="color: var(--verde-institucional);"></i>
                    {{ $user->created_at->format('d/m/Y') }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Estadísticas -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 30px;">
        <div style="background: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="font-size: 28px; font-weight: 700; color: #16a34a;">{{ $cursosCompletados->count() }}</div>
            <div style="font-size: 13px; color: #6b7280;">Cursos Completados</div>
        </div>
        <div style="background: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="font-size: 28px; font-weight: 700; color: #f59e0b;">
                {{ $cursosCompletados->sum(function($p) { return $p->curso ? $p->curso->carga_horaria : 0; }) }}
            </div>
            <div style="font-size: 13px; color: #6b7280;">Horas de Estudio</div>
        </div>
        <div style="background: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="font-size: 28px; font-weight: 700; color: #3b82f6;">{{ $cursosCompletados->count() }}</div>
            <div style="font-size: 13px; color: #6b7280;">Certificados</div>
        </div>
    </div>
    
    <!-- Certificados -->
    <div style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <h3 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-certificate" style="color: var(--dorado);"></i>
            Mis Certificados
        </h3>
        
        @if($cursosCompletados->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px;">
                @foreach($cursosCompletados as $progreso)
                    @if($progreso->curso)
                    <div style="background: linear-gradient(135deg, var(--verde-institucional) 0%, #0d7a3f 100%); border-radius: 12px; padding: 20px; color: white; text-align: center;">
                        <i class="fas fa-award" style="font-size: 32px; margin-bottom: 12px; color: var(--dorado);"></i>
                        <div style="font-size: 14px; font-weight: 600; margin-bottom: 4px;">{{ $progreso->curso->titulo }}</div>
                        <div style="font-size: 11px; opacity: 0.8;">Completado el {{ $progreso->completado_at ? $progreso->completado_at->format('d/m/Y') : 'recientemente' }}</div>
                        <a href="{{ route('certificado', $progreso->curso) }}" target="_blank" style="margin-top: 12px; background: var(--dorado); color: var(--verde-institucional); border: none; padding: 8px 16px; border-radius: 6px; font-size: 12px; font-weight: 500; cursor: pointer; text-decoration: none; display: inline-block;">
                            <i class="fas fa-download"></i> Ver Certificado
                        </a>
                    </div>
                    @endif
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 40px; background: #f9fafb; border-radius: 12px;">
                <i class="fas fa-certificate" style="color: #d1d5db; font-size: 48px; margin-bottom: 16px;"></i>
                <p style="color: #6b7280; margin-bottom: 8px;">Aún no tienes certificados</p>
                <p style="color: #9ca3af; font-size: 13px;">Completa un curso para obtener tu primer certificado</p>
            </div>
        @endif
    </div>
</div>
@endsection