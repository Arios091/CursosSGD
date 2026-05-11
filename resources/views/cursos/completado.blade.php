@extends('layouts.app')

@section('page-title', '¡Curso Completado!')

@section('content')
<style>
    .completado-container {
        max-width: 800px;
        margin: 40px auto;
        padding: 0 20px;
    }
    .completado-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }
    .completado-header {
        background: linear-gradient(135deg, var(--verde-institucional) 0%, #0d7a3f 100%);
        padding: 40px;
        text-align: center;
        color: white;
        position: relative;
    }
    .completado-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .completado-icon {
        width: 80px;
        height: 80px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 40px;
        position: relative;
        z-index: 1;
    }
    .completado-header h1 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 8px;
        position: relative;
        z-index: 1;
    }
    .completado-header p {
        font-size: 16px;
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }
    .completado-body {
        padding: 32px;
    }
    .completado-info {
        background: #f9fafb;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }
    .completado-info h3 {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 16px;
    }
    .completado-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .completado-stat {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 16px;
        text-align: center;
    }
    .completado-stat-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--verde-institucional);
    }
    .completado-stat-label {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }
    .completado-actions {
        display: flex;
        gap: 16px;
        justify-content: center;
        flex-wrap: wrap;
    }
    .btn-certificado {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 28px;
        background: var(--verde-institucional);
        color: white;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-certificado:hover {
        background: #0d7a3f;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(11,94,46,0.3);
    }
    .btn-home {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 28px;
        background: white;
        color: #374151;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-home:hover {
        border-color: var(--verde-institucional);
        color: var(--verde-institucional);
        transform: translateY(-2px);
    }
    .completado-footer {
        text-align: center;
        padding: 20px 32px;
        border-top: 1px solid #e5e7eb;
        color: #6b7280;
        font-size: 14px;
    }
    .completado-footer a {
        color: var(--verde-institucional);
        text-decoration: none;
        font-weight: 500;
    }
    .completado-footer a:hover {
        text-decoration: underline;
    }
    @media (max-width: 640px) {
        .completado-stats { grid-template-columns: 1fr; }
        .completado-actions { flex-direction: column; }
        .completado-header h1 { font-size: 24px; }
    }
</style>

<div class="completado-container">
    <div class="completado-card">
        <div class="completado-header">
            <div class="completado-icon">🎓</div>
            <h1>¡Felicidades!</h1>
            <p>Has completado exitosamente el curso</p>
        </div>

        <div class="completado-body">
            <div class="completado-info">
                <h3>{{ $curso->titulo }}</h3>
                <p style="color: #6b7280; margin: 0;">{{ $curso->descripcion }}</p>
            </div>

            <div class="completado-stats">
                <div class="completado-stat">
                    <div class="completado-stat-value">{{ $curso->modulos->count() }}</div>
                    <div class="completado-stat-label">Módulos</div>
                </div>
                <div class="completado-stat">
                    <div class="completado-stat-value">{{ $curso->carga_horaria }}</div>
                    <div class="completado-stat-label">Horas</div>
                </div>
                <div class="completado-stat">
                    <div class="completado-stat-value">{{ $progreso->completado_at ? $progreso->completado_at->format('d/m/Y') : '-' }}</div>
                    <div class="completado-stat-label">Completado</div>
                </div>
            </div>

            <div class="completado-actions">
                <a href="{{ route('certificado.descargar', $curso) }}" class="btn-certificado">
                    <i class="fas fa-download"></i> Descargar Certificado
                </a>
                <a href="{{ route('certificado.ver', $curso) }}" target="_blank" class="btn-certificado" style="background: var(--dorado);">
                    <i class="fas fa-eye"></i> Ver Certificado
                </a>
                <a href="{{ route('home') }}" class="btn-home">
                    <i class="fas fa-arrow-left"></i> Volver al Inicio
                </a>
            </div>
        </div>

        <div class="completado-footer">
            <p style="margin: 0;">Tu certificado está listo para ser descargado. También puedes verlo en <a href="{{ route('certificado', $curso) }}">esta página</a>.</p>
        </div>
    </div>
</div>
@endsection