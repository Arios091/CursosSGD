@extends('layouts.app')

@section('page-title', 'Certificado - ' . $curso->titulo)

@section('content')
<style>
    .cert-preview-container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
    .cert-preview-header { text-align: center; margin-bottom: 30px; }
    .cert-preview-header h2 { font-size: 24px; font-weight: 700; color: #1f2937; margin-bottom: 4px; }
    .cert-preview-header p { color: #6b7280; margin: 0; }
    .cert-preview-actions { display: flex; justify-content: center; gap: 16px; margin-bottom: 30px; }
    .cert-btn { display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px; border-radius: 10px; font-size: 15px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; }
    .cert-btn-download { background: #0B5E2E; color: white; }
    .cert-btn-download:hover { background: #0d7a3f; color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(11,94,46,0.3); }
    .cert-btn-back { background: white; color: #374151; border: 2px solid #e5e7eb; }
    .cert-btn-back:hover { border-color: #0B5E2E; color: #0B5E2E; transform: translateY(-2px); }
    .cert-preview-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .cert-preview-inner { padding: 40px; position: relative; border: 6px solid #0B5E2E; margin: 20px; }
    .cert-preview-inner::before { content: ''; position: absolute; top: 8px; left: 8px; right: 8px; bottom: 8px; border: 2px solid #C9A227; pointer-events: none; }
    .cert-preview-logo { text-align: center; margin-bottom: 20px; }
    .cert-preview-logo h3 { font-size: 22px; font-weight: 700; color: #0B5E2E; letter-spacing: 4px; margin: 0; }
    .cert-preview-logo p { font-size: 11px; color: #6b7280; margin: 4px 0 0; letter-spacing: 2px; }
    .cert-preview-divider { width: 50px; height: 3px; background: #C9A227; margin: 16px auto; }
    .cert-preview-title { text-align: center; font-size: 26px; font-weight: 700; color: #0B5E2E; letter-spacing: 6px; margin-bottom: 4px; text-transform: uppercase; }
    .cert-preview-subtitle { text-align: center; font-size: 12px; color: #6b7280; letter-spacing: 2px; margin-bottom: 20px; }
    .cert-preview-name { text-align: center; font-size: 28px; font-weight: 700; color: #1f2937; margin: 16px 0; padding-bottom: 8px; border-bottom: 2px solid #C9A227; display: inline-block; width: 100%; }
    .cert-preview-course { text-align: center; font-size: 16px; font-weight: 600; color: #0B5E2E; margin: 12px 0 8px; }
    .cert-preview-hours { text-align: center; font-size: 12px; color: #6b7280; margin-bottom: 16px; }
    .cert-preview-meta { display: flex; justify-content: center; gap: 40px; margin-bottom: 20px; }
    .cert-preview-meta-item { text-align: center; }
    .cert-preview-meta-label { font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; }
    .cert-preview-meta-value { font-size: 13px; color: #374151; font-weight: 500; }
    .cert-preview-number { text-align: center; font-size: 9px; color: #9ca3af; letter-spacing: 1px; margin-bottom: 20px; }
    .cert-preview-signatures { display: flex; justify-content: center; gap: 80px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
    .cert-preview-sign { text-align: center; width: 150px; }
    .cert-preview-sign-line { width: 120px; height: 1px; background: #374151; margin: 0 auto 6px; }
    .cert-preview-sign-name { font-size: 11px; font-weight: 600; color: #1f2937; }
    .cert-preview-sign-title { font-size: 9px; color: #6b7280; }
</style>

<div class="cert-preview-container">
    <div class="cert-preview-header">
        <h2><i class="fas fa-certificate" style="color: #C9A227; margin-right: 8px;"></i>Tu Certificado</h2>
        <p>Curso: {{ $curso->titulo }}</p>
    </div>

    <div class="cert-preview-actions">
        <a href="{{ route('certificado.descargar', $curso) }}" class="cert-btn cert-btn-download">
            <i class="fas fa-download"></i> Descargar PDF
        </a>
        <a href="{{ route('cursos.completado', $curso) }}" class="cert-btn cert-btn-back">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="cert-preview-card">
        <div class="cert-preview-inner">
            <div class="cert-preview-logo">
                <h3>UNAS</h3>
                <p>UNIVERSIDAD NACIONAL AGRARIA DE LA SELVA</p>
            </div>
            <div class="cert-preview-divider"></div>
            <div class="cert-preview-title">Certificado</div>
            <div class="cert-preview-subtitle">DE APROBACIÓN DE CURSO</div>
            
            <p style="text-align: center; font-size: 12px; color: #4b5563; margin-bottom: 8px;">Se otorga el presente certificado a:</p>
            
            <div class="cert-preview-name">{{ strtoupper($user->name) }}</div>
            
            <p style="text-align: center; font-size: 11px; color: #4b5563; line-height: 1.5; max-width: 500px; margin: 0 auto;">
                Por haber completado y aprobado satisfactoriamente todas las actividades académicas, evaluaciones parciales y evaluación final del curso:
            </p>
            
            <div class="cert-preview-course">{{ $curso->titulo }}</div>
            <div class="cert-preview-hours"><strong>Carga Horaria:</strong> {{ $curso->carga_horaria }} horas académicas</div>
            
            <div class="cert-preview-meta">
                <div class="cert-preview-meta-item">
                    <div class="cert-preview-meta-label">Lugar</div>
                    <div class="cert-preview-meta-value">Tingo María</div>
                </div>
                <div class="cert-preview-meta-item">
                    <div class="cert-preview-meta-label">Fecha</div>
                    <div class="cert-preview-meta-value">{{ $fechaCompletado }}</div>
                </div>
            </div>
            
            <div class="cert-preview-number">N° {{ $numeroCertificado }}</div>
            
            <div class="cert-preview-signatures">
                <div class="cert-preview-sign">
                    <div class="cert-preview-sign-line"></div>
                    <div class="cert-preview-sign-name">Rector(a)</div>
                    <div class="cert-preview-sign-title">Universidad Nacional Agraria de la Selva</div>
                </div>
                <div class="cert-preview-sign">
                    <div class="cert-preview-sign-line"></div>
                    <div class="cert-preview-sign-name">Encargado(a) de LMS</div>
                    <div class="cert-preview-sign-title">Sistema de Gestión de Docencia</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
