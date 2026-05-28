<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificado - {{ $curso->titulo }}</title>
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
            background-color: #ffffff;
        }
        
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #2c3e50;
        }
        
        .cert-container {
            width: 297mm;
            height: 210mm;
            padding: 10mm;
            position: relative;
            background-color: #ffffff;
        }
        
        /* Marco Exterior (Verde Institucional) */
        .border-outer {
            width: 277mm;
            height: 190mm;
            border: 5px solid #0B5E2E;
            border-radius: 8px;
            position: relative;
            background-color: #fdfdfb; /* Fondo sutil color crema/marfil */
        }
        
        /* Marco Interior (Dorado) */
        .border-inner {
            position: absolute;
            top: 6px;
            left: 6px;
            right: 6px;
            bottom: 6px;
            border: 2px solid #C9A227;
            border-radius: 5px;
        }
        
        /* Esquineros Ornamentales en Oro */
        .corner {
            position: absolute;
            width: 30px;
            height: 30px;
            border: 2px solid #C9A227;
        }
        .corner-tl { top: 3px; left: 3px; border-right: none; border-bottom: none; }
        .corner-tr { top: 3px; right: 3px; border-left: none; border-bottom: none; }
        .corner-bl { bottom: 3px; left: 3px; border-right: none; border-top: none; }
        .corner-br { bottom: 3px; right: 3px; border-left: none; border-top: none; }
        
        /* Contenedor de Contenido */
        .content-table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        
        .header-cell {
            text-align: center;
            padding-top: 15px;
            height: 85px;
        }
        
        .logo-img {
            width: 60px;
            height: 60px;
            vertical-align: middle;
            margin-bottom: 5px;
        }
        
        .univ-title {
            font-family: 'Times-Roman', Georgia, serif;
            font-size: 20px;
            font-weight: bold;
            color: #0B5E2E;
            letter-spacing: 4px;
            text-transform: uppercase;
        }
        
        .univ-subtitle {
            font-size: 9px;
            color: #7f8c8d;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 2px;
        }
        
        .divider {
            width: 120px;
            height: 2px;
            background-color: #C9A227;
            margin: 10px auto;
        }
        
        .body-cell {
            text-align: center;
            vertical-align: middle;
            padding: 0 40px;
        }
        
        .cert-title {
            font-family: 'Times-Roman', Georgia, serif;
            font-size: 28px;
            font-weight: bold;
            color: #0B5E2E;
            letter-spacing: 6px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        
        .cert-subtitle {
            font-size: 11px;
            color: #7f8c8d;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 15px;
        }
        
        .cert-label {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 8px;
            font-style: italic;
        }
        
        .student-name {
            font-family: 'Times-Roman', Georgia, serif;
            font-size: 26px;
            font-weight: bold;
            color: #1a252f;
            border-bottom: 2px solid #C9A227;
            display: inline-block;
            padding-bottom: 5px;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }
        
        .cert-description {
            font-size: 12px;
            color: #555555;
            line-height: 1.6;
            margin: 0 auto 12px;
            max-width: 700px;
        }
        
        .course-title {
            font-size: 17px;
            font-weight: bold;
            color: #0B5E2E;
            margin-bottom: 5px;
        }
        
        .course-hours {
            font-size: 11px;
            color: #7f8c8d;
            margin-bottom: 12px;
        }
        
        .date-location {
            font-size: 11px;
            color: #555555;
            margin-bottom: 15px;
        }
        
        /* Footer Table con QR, Firmas y Sello */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: auto;
        }
        
        .footer-cell {
            vertical-align: bottom;
        }
        
        /* QR Code Block */
        .qr-container {
            width: 75px;
            text-align: center;
        }
        
        .qr-img {
            width: 70px;
            height: 70px;
            border: 2px solid #0B5E2E;
            border-radius: 4px;
            padding: 3px;
            background-color: #ffffff;
        }
        
        .qr-label {
            font-size: 7px;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 3px;
        }
        
        /* Firmas Block */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .sign-cell {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 15px;
        }
        
        .sign-graphic {
            height: 40px;
            margin-bottom: -5px;
        }
        
        .sign-line {
            width: 150px;
            height: 1px;
            background-color: #bdc3c7;
            margin: 0 auto 5px;
        }
        
        .sign-name {
            font-size: 9px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .sign-title {
            font-size: 7px;
            color: #7f8c8d;
            margin-top: 1px;
        }
        
        /* Sello y Serial Block */
        .seal-container {
            text-align: right;
        }
        
        .seal-img {
            width: 70px;
            height: 70px;
            margin-bottom: 5px;
        }
        
        .serial-text {
            font-size: 7px;
            font-family: monospace;
            color: #95a5a6;
        }
        
        .serial-code {
            background-color: #f2f4f4;
            padding: 1px 4px;
            border-radius: 2px;
            border: 1px dashed #d5dbdb;
        }
    </style>
</head>
<body>
    <div class="cert-container">
        <div class="border-outer">
            <div class="border-inner">
                <!-- Esquineros de Decoración -->
                <div class="corner corner-tl"></div>
                <div class="corner corner-tr"></div>
                <div class="corner corner-bl"></div>
                <div class="corner corner-br"></div>
                
                <table class="content-table">
                    <!-- Fila de Cabecera (Logo e Institución) -->
                    <tr>
                        <td class="header-cell">
                            <img class="logo-img" src="{{ public_path('assets/unasicono.png') }}" alt="UNAS">
                            <div class="univ-title">Universidad Nacional Agraria de la Selva</div>
                            <div class="univ-subtitle">Tingo María - Perú</div>
                            <div class="divider"></div>
                        </td>
                    </tr>
                    
                    <!-- Fila del Cuerpo Principal -->
                    <tr>
                        <td class="body-cell">
                            <div class="cert-title">Certificado de Aprobación</div>
                            <div class="cert-subtitle">Otorgado a nivel institucional</div>
                            
                            <div class="cert-label">Se otorga el presente documento a:</div>
                            <div class="student-name">{{ strtoupper($user->name) }}</div>
                            
                            <div class="cert-description">
                                Por haber completado y aprobado satisfactoriamente todos los requisitos académicos exigidos en las actividades correspondientes al curso de capacitación especializada:
                            </div>
                            
                            <div class="course-title">{{ $curso->titulo }}</div>
                            <div class="course-hours">Habiendo cumplido con un total de <strong>{{ $curso->carga_horaria }} horas académicas</strong>.</div>
                            
                            <div class="date-location">
                                Expedido en la ciudad de Tingo María, Perú, el <strong>{{ $fechaCompletado }}</strong>.
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Fila de Pie de Página (QR, Firmas y Sello) -->
                    <tr>
                        <td style="padding: 0 25px 15px 25px; vertical-align: bottom;">
                            <table class="footer-table">
                                <tr>
                                    <!-- QR de Verificación -->
                                    <td class="footer-cell" style="width: 25%;">
                                        <div class="qr-container">
                                            <!-- QR Server API retornando PNG de alta calidad -->
                                            <img class="qr-img" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('certificado.verificar', $numeroCertificado)) }}" alt="QR Code">
                                            <div class="qr-label">Verificar Certificado</div>
                                        </div>
                                    </td>
                                    
                                    <!-- Firmas de Autoridades -->
                                    <td class="footer-cell" style="width: 50%;">
                                        <table class="signatures-table">
                                            <tr>
                                                <!-- Rector -->
                                                <td class="sign-cell">
                                                    <!-- Firma Cursiva en SVG Vectorial (Firma de Ejemplo 1) -->
                                                    <svg class="sign-graphic" viewBox="0 0 100 40" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M10 25 C 20 5, 25 10, 30 18 C 35 25, 40 30, 45 20 C 50 10, 52 15, 55 25 C 60 30, 65 18, 70 12 C 75 8, 80 15, 85 28" fill="none" stroke="#2c3e50" stroke-width="1.8" stroke-linecap="round"/>
                                                    </svg>
                                                    <div class="sign-line"></div>
                                                    <div class="sign-name">Dr. Milthon Honorio Muñoz Berrocal</div>
                                                    <div class="sign-title">Rector de la UNAS</div>
                                                    <div class="sign-title" style="font-style: italic; color: #95a5a6; font-size: 6px; margin-top: 1px;">(Solo con propósitos de demostración)</div>
                                                </td>
                                                
                                                <!-- OTI -->
                                                <td class="sign-cell">
                                                    <!-- Firma Cursiva en SVG Vectorial (Firma de Ejemplo 2) -->
                                                    <svg class="sign-graphic" viewBox="0 0 100 40" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M15 28 C 22 22, 28 8, 32 15 C 36 22, 38 32, 42 25 C 46 18, 50 12, 54 18 C 58 24, 62 28, 66 18 C 70 8, 74 15, 80 22 C 84 28, 88 18, 92 12" fill="none" stroke="#2c3e50" stroke-width="1.5" stroke-linecap="round"/>
                                                    </svg>
                                                    <div class="sign-line"></div>
                                                    <div class="sign-name">Firma de Ejemplo</div>
                                                    <div class="sign-title">Director(a) de OTI</div>
                                                    <div class="sign-title" style="font-style: italic; color: #95a5a6; font-size: 6px; margin-top: 1px;">(Solo con propósitos de demostración)</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    
                                    <!-- Sello Universitario y Serial -->
                                    <td class="footer-cell" style="width: 25%; text-align: right;">
                                        <div class="seal-container">
                                            <img class="seal-img" src="{{ public_path('assets/gold_seal.png') }}" alt="Sello Oficial">
                                            <div class="serial-text">
                                                N° Registro: <span class="serial-code">{{ $numeroCertificado }}</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
