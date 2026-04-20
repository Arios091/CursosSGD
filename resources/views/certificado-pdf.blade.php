<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { 
            margin: 0; 
            size: A4 landscape; 
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        html, body {
            width: 297mm;
            height: 210mm;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            background: #fff;
        }
        
        .cert-page {
            width: 297mm;
            height: 210mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .cert-wrapper {
            width: 277mm;
            height: 190mm;
            border: 6px solid #0B5E2E;
            border-radius: 10px;
            position: relative;
        }
        
        .cert-inner {
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            border: 3px solid #C9A227;
            border-radius: 6px;
        }
        
        .cert-corner {
            position: absolute;
            width: 40px;
            height: 40px;
            border: 3px solid #C9A227;
        }
        
        .cert-corner-tl { top: 3px; left: 3px; border-right: none; border-bottom: none; }
        .cert-corner-tr { top: 3px; right: 3px; border-left: none; border-bottom: none; }
        .cert-corner-bl { bottom: 3px; left: 3px; border-right: none; border-top: none; }
        .cert-corner-br { bottom: 3px; right: 3px; border-left: none; border-top: none; }
        
        .cert-content {
            position: absolute;
            top: 20px;
            left: 30px;
            right: 30px;
            bottom: 20px;
            display: flex;
            flex-direction: column;
            text-align: center;
            align-items: center;
        }
        
        .cert-header {
            margin-bottom: 10px;
        }
        
        .cert-logo {
            width: 55px;
            height: 55px;
            margin: 0 auto 6px;
        }
        
        .cert-university {
            font-size: 22px;
            font-weight: bold;
            color: #0B5E2E;
            letter-spacing: 4px;
            margin-bottom: 2px;
        }
        
        .cert-university-sub {
            font-size: 9px;
            color: #6b7280;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        .cert-divider {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #0B5E2E, #C9A227);
            margin: 10px auto;
        }
        
        .cert-title {
            font-size: 30px;
            font-weight: bold;
            color: #0B5E2E;
            letter-spacing: 8px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        
        .cert-subtitle {
            font-size: 11px;
            color: #6b7280;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }
        
        .cert-label {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 4px;
        }
        
        .cert-name {
            font-size: 26px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 3px solid #C9A227;
            display: inline-block;
            min-width: 240px;
        }
        
        .cert-description {
            font-size: 11px;
            color: #4b5563;
            line-height: 1.6;
            text-align: center;
            margin-bottom: 12px;
            max-width: 260mm;
        }
        
        .cert-course {
            font-size: 16px;
            font-weight: bold;
            color: #0B5E2E;
            margin-bottom: 3px;
        }
        
        .cert-hours {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 12px;
        }
        
        .cert-meta {
            display: flex;
            justify-content: center;
            gap: 70mm;
            margin-bottom: 14px;
        }
        
        .cert-meta-item {
            text-align: center;
        }
        
        .cert-meta-label {
            font-size: 8px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        
        .cert-meta-value {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
        }
        
        .cert-footer {
            display: flex;
            align-items: center;
            margin-top: auto;
            padding-top: 10px;
            border-top: 1.5px solid #e5e7eb;
        }
        
        .cert-qr {
            text-align: center;
            flex-shrink: 0;
            margin-right: auto;
        }
        
        .cert-qr-box {
            width: 50px;
            height: 50px;
            border: 2px solid #0B5E2E;
            border-radius: 4px;
            padding: 3px;
            background: white;
        }
        
        .cert-label {
            font-size: 6px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 3px;
        }
        
        .cert-signatures {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }
        
        .cert-signatures-row {
            display: flex;
            gap: 90px;
            justify-content: center;
        }
        
        .cert-sign {
            text-align: center;
            min-width: 80px;
        }
        
        .cert-sign-line {
            width: 70px;
            height: 1.5px;
            background: #374151;
            margin: 0 auto 4px;
        }
        
        .cert-sign-name {
            font-size: 10px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 2px;
        }
        
        .cert-sign-title {
            font-size: 7px;
            color: #6b7280;
        }
        
        .cert-number {
            font-size: 8px;
            color: #9ca3af;
            letter-spacing: 0.5px;
            margin-top: 10px;
        }
        
        .cert-number code {
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 2px;
            font-family: monospace;
        }
        
        .cert-qr {
            text-align: center;
            flex-shrink: 0;
            margin-right: auto;
        }
        
        .cert-qr-box {
            width: 50px;
            height: 50px;
            border: 2px solid #0B5E2E;
            border-radius: 4px;
            padding: 3px;
            background: white;
        }
        
        .cert-qr-label {
            font-size: 6px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 3px;
        }
        
        .cert-signatures {
            display: flex;
            gap: 90px;
            justify-content: center;
            flex: 1;
        }
        
        .cert-sign {
            text-align: center;
            min-width: 80px;
        }
        
        .cert-sign-line {
            width: 70px;
            height: 1.5px;
            background: #374151;
            margin: 0 auto 4px;
        }
        
        .cert-sign-name {
            font-size: 10px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 2px;
        }
        
        .cert-sign-title {
            font-size: 7px;
            color: #6b7280;
        }
        
        .cert-number {
            font-size: 8px;
            color: #9ca3af;
            letter-spacing: 0.5px;
            flex-shrink: 0;
            margin-left: auto;
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px dashed #e5e7eb;
        }
        
        .cert-number code {
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 2px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="cert-page">
        <div class="cert-wrapper">
            <div class="cert-inner">
                <div class="cert-corner cert-corner-tl"></div>
                <div class="cert-corner cert-corner-tr"></div>
                <div class="cert-corner cert-corner-bl"></div>
                <div class="cert-corner cert-corner-br"></div>
                
                <div class="cert-content">
                    <div class="cert-header">
                        <div class="cert-logo">
                            <img src="{{ public_path('assets/unasicono.png') }}" alt="UNAS">
                        </div>
                        <div class="cert-university">UNAS</div>
                        <div class="cert-university-sub">Universidad Nacional Agraria de la Selva</div>
                    </div>
                    
                    <div class="cert-divider"></div>
                    
                    <div class="cert-title">Certificado</div>
                    <div class="cert-subtitle">De Aprobación de Curso</div>
                    
                    <div class="cert-label">Se otorga el presente certificado a:</div>
                    <div class="cert-name">{{ strtoupper($user->name) }}</div>
                    
                    <div class="cert-description">
                        Por haber completado y aprobado satisfactoriamente todas las actividades académicas, 
                        evaluaciones parciales y evaluación final del curso:
                    </div>
                    
                    <div class="cert-course">{{ $curso->titulo }}</div>
                    <div class="cert-hours"><strong>Carga Horaria:</strong> {{ $curso->carga_horaria }} horas académicas</div>
                    
                    <div class="cert-meta">
                        <div class="cert-meta-item">
                            <div class="cert-meta-label">Lugar</div>
                            <div class="cert-meta-value">Tingo María, Perú</div>
                        </div>
                        <div class="cert-meta-item">
                            <div class="cert-meta-label">Fecha de Emisión</div>
                            <div class="cert-meta-value">{{ $fechaCompletado }}</div>
                        </div>
                    </div>
                    
                    <div class="cert-footer">
                        <div class="cert-qr">
                            <div class="cert-qr-box">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=44x44&data={{ urlencode(route('certificado.verificar', $numeroCertificado)) }}&format=svg" alt="QR" style="width: 100%;">
                            </div>
                            <div class="cert-qr-label">Verificar</div>
                        </div>
                        
                        <div class="cert-signatures">
                            <div class="cert-signatures-row">
                                <div class="cert-sign">
                                    <div class="cert-sign-line"></div>
                                    <div class="cert-sign-name">Rector(a) UNAS</div>
                                    <div class="cert-sign-title">Universidad Nacional Agraria de la Selva</div>
                                </div>
                                <div class="cert-sign">
                                    <div class="cert-sign-line"></div>
                                    <div class="cert-sign-name">Director(a) de OTI</div>
                                    <div class="cert-sign-title">Oficina de Tecnología e Información</div>
                                </div>
                            </div>
                            <div class="cert-number">
                                <code>{{ $numeroCertificado }}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
