<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Certificado - {{ $curso->titulo }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        html, body {
            width: 297mm;
            height: 210mm;
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            background: #f5f5f5;
        }
        
        .cert-page {
            width: 297mm;
            height: 210mm;
            background: white;
            position: relative;
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
            margin-bottom: 12px;
        }
        
        .cert-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 8px;
        }
        
        .cert-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .cert-university {
            font-size: 26px;
            font-weight: bold;
            color: #0B5E2E;
            letter-spacing: 6px;
            margin-bottom: 4px;
        }
        
        .cert-university-sub {
            font-size: 12px;
            color: #6b7280;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        
        .cert-divider {
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #0B5E2E, #C9A227);
            margin: 12px auto;
            border-radius: 2px;
        }
        
        .cert-title {
            font-size: 38px;
            font-weight: bold;
            color: #0B5E2E;
            letter-spacing: 10px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        
        .cert-subtitle {
            font-size: 14px;
            color: #6b7280;
            letter-spacing: 5px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        
        .cert-label {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 6px;
        }
        
        .cert-name {
            font-size: 32px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 4px solid #C9A227;
            display: inline-block;
            min-width: 300px;
        }
        
        .cert-description {
            font-size: 14px;
            color: #4b5563;
            line-height: 1.8;
            text-align: center;
            margin-bottom: 16px;
            max-width: 320px;
        }
        
        .cert-course {
            font-size: 20px;
            font-weight: bold;
            color: #0B5E2E;
            margin-bottom: 4px;
        }
        
        .cert-hours {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 16px;
        }
        
        .cert-meta {
            display: flex;
            justify-content: center;
            gap: 80px;
            margin-bottom: 20px;
        }
        
        .cert-meta-item {
            text-align: center;
        }
        
        .cert-meta-label {
            font-size: 11px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }
        
        .cert-meta-value {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
        }
        
        .cert-footer {
            display: flex;
            align-items: flex-end;
            width: 100%;
            margin-top: auto;
            padding-top: 12px;
            border-top: 2px solid #e5e7eb;
        }
        
        .cert-qr {
            text-align: center;
            flex-shrink: 0;
            margin-right: auto;
        }
        
        .cert-qr-box {
            width: 70px;
            height: 70px;
            border: 3px solid #0B5E2E;
            border-radius: 6px;
            padding: 4px;
            background: white;
        }
        
        .cert-qr-box img {
            width: 100%;
            height: 100%;
        }
        
        .cert-qr-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 6px;
        }
        
        .cert-signatures {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }
        
        .cert-signatures-row {
            display: flex;
            gap: 120px;
            justify-content: center;
        }
        
        .cert-sign {
            text-align: center;
            min-width: 100px;
        }
        
        .cert-sign-line {
            width: 100px;
            height: 2px;
            background: #374151;
            margin: 0 auto 6px;
        }
        
        .cert-sign-name {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 3px;
        }
        
        .cert-sign-title {
            font-size: 10px;
            color: #6b7280;
        }
        
        .cert-number {
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            letter-spacing: 1px;
            margin-top: 16px;
        }
        
        .cert-number code {
            background: #f3f4f6;
            padding: 3px 10px;
            border-radius: 4px;
            font-family: monospace;
        }
        
        @media print {
            body {
                background: white !important;
            }
            .cert-page {
                width: 100%;
                height: 100%;
            }
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
                            <img src="{{ asset('assets/unasicono.png') }}" alt="UNAS">
                        </div>
                        <div class="cert-university">UNAS</div>
                        <div class="cert-university-sub">Universidad Nacional Agraria de la Selva</div>
                    </div>
                    
                    <div class="cert-divider"></div>
                    
                    <div class="cert-title">Certificado</div>
                    <div class="cert-subtitle">De Aprobación de Curso</div>
                    
                    <div class="cert-label">Se otorga el presente certificado a:</div>
                    <div class="certName">{{ strtoupper($user->name) }}</div>
                    
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
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=62x62&data={{ urlencode(route('certificado.verificar', $numeroCertificado)) }}&format=svg" alt="QR">
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
