<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; size: A4 landscape; }
        body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            width: 297mm;
            height: 210mm;
            position: relative;
            background: #fff;
        }
        .cert-border-outer {
            position: absolute;
            top: 8mm; left: 8mm; right: 8mm; bottom: 8mm;
            border: 3px solid #0B5E2E;
        }
        .cert-border-inner {
            position: absolute;
            top: 12mm; left: 12mm; right: 12mm; bottom: 12mm;
            border: 1.5px solid #C9A227;
        }
        .cert-corner-tl { position: absolute; top: 10mm; left: 10mm; width: 20mm; height: 20mm; border-top: 2px solid #C9A227; border-left: 2px solid #C9A227; }
        .cert-corner-tr { position: absolute; top: 10mm; right: 10mm; width: 20mm; height: 20mm; border-top: 2px solid #C9A227; border-right: 2px solid #C9A227; }
        .cert-corner-bl { position: absolute; bottom: 10mm; left: 10mm; width: 20mm; height: 20mm; border-bottom: 2px solid #C9A227; border-left: 2px solid #C9A227; }
        .cert-corner-br { position: absolute; bottom: 10mm; right: 10mm; width: 20mm; height: 20mm; border-bottom: 2px solid #C9A227; border-right: 2px solid #C9A227; }
        
        .cert-content {
            position: absolute;
            top: 22mm; left: 22mm; right: 22mm; bottom: 22mm;
            text-align: center;
        }
        .cert-logo { font-size: 26px; font-weight: bold; color: #0B5E2E; margin-bottom: 2px; letter-spacing: 6px; }
        .cert-university { font-size: 10px; color: #6b7280; margin-bottom: 12px; letter-spacing: 2px; }
        .cert-divider { width: 50mm; height: 1.5px; background: #C9A227; margin: 0 auto 14px; }
        .cert-title { font-size: 28px; font-weight: bold; color: #0B5E2E; letter-spacing: 8px; margin-bottom: 4px; text-transform: uppercase; }
        .cert-subtitle { font-size: 11px; color: #6b7280; margin-bottom: 18px; letter-spacing: 3px; }
        .cert-otorgado { font-size: 12px; color: #4b5563; margin-bottom: 6px; }
        .cert-name { font-size: 24px; font-weight: bold; color: #1f2937; margin-bottom: 10px; border-bottom: 1.5px solid #C9A227; display: inline-block; padding-bottom: 3px; min-width: 100mm; }
        .cert-description { font-size: 11px; color: #4b5563; line-height: 1.5; margin: 0 auto 12px; max-width: 160mm; }
        .cert-course { font-size: 16px; font-weight: bold; color: #0B5E2E; margin-bottom: 6px; }
        .cert-hours { font-size: 11px; color: #6b7280; margin-bottom: 16px; }
        .cert-date { font-size: 10px; color: #6b7280; margin-bottom: 20px; }
        .cert-number { font-size: 8px; color: #9ca3af; margin-bottom: 20px; letter-spacing: 1px; }
        .cert-signatures { text-align: center; margin-top: 8mm; }
        .cert-signature { display: inline-block; text-align: center; width: 55mm; margin: 0 15mm; }
        .cert-sign-line { width: 45mm; height: 1px; background: #374151; margin: 0 auto 4px; }
        .cert-sign-name { font-size: 10px; font-weight: bold; color: #1f2937; margin-bottom: 1px; }
        .cert-sign-title { font-size: 8px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="cert-border-outer"></div>
    <div class="cert-border-inner"></div>
    <div class="cert-corner-tl"></div>
    <div class="cert-corner-tr"></div>
    <div class="cert-corner-bl"></div>
    <div class="cert-corner-br"></div>
    
    <div class="cert-content">
        <div class="cert-logo">UNAS</div>
        <div class="cert-university">UNIVERSIDAD NACIONAL AGRARIA DE LA SELVA</div>
        
        <div class="cert-divider"></div>
        
        <div class="cert-title">Certificado</div>
        <div class="cert-subtitle">DE APROBACIÓN DE CURSO</div>
        
        <div class="cert-otorgado">Se otorga el presente certificado a:</div>
        
        <div class="cert-name">{{ strtoupper($user->name) }}</div>
        
        <div class="cert-description">
            Por haber completado y aprobado satisfactoriamente todas las actividades académicas, 
            evaluaciones parciales y evaluación final del curso:
        </div>
        
        <div class="cert-course">{{ $curso->titulo }}</div>
        
        <div class="cert-hours">
            <strong>Carga Horaria:</strong> {{ $curso->carga_horaria }} horas académicas
        </div>
        
        <div class="cert-date">
            Tingo María, {{ $fechaCompletado }}
        </div>
        
        <div class="cert-number">
            N° de Certificado: {{ $numeroCertificado }}
        </div>
        
        <div class="cert-signatures">
            <div class="cert-signature">
                <div class="cert-sign-line"></div>
                <div class="cert-sign-name">Rector(a)</div>
                <div class="cert-sign-title">Universidad Nacional Agraria de la Selva</div>
            </div>
            <div class="cert-signature">
                <div class="cert-sign-line"></div>
                <div class="cert-sign-name">Encargado(a) de LMS</div>
                <div class="cert-sign-title">Sistema de Gestión de Docencia</div>
            </div>
        </div>
    </div>
</body>
</html>
