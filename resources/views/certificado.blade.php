<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Certificado - {{ $curso->titulo }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            background-color: #0f172a;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Barra Superior de Acciones */
        .top-bar {
            width: 100%;
            background-color: #1e293b;
            border-bottom: 1px solid #334155;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .top-bar-title {
            color: #f8fafc;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
        }

        .top-bar-actions {
            display: flex;
            gap: 15px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-back {
            background-color: transparent;
            color: #cbd5e1;
            border: 1px solid #475569;
        }

        .btn-back:hover {
            background-color: #334155;
            color: #f8fafc;
        }

        .btn-download {
            background-color: #0B5E2E;
            color: #ffffff;
            border: 1px solid #0B5E2E;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-download:hover {
            background-color: #0d7a3f;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(11, 94, 46, 0.3);
        }

        /* Contenedor del Certificado con Escalado */
        .cert-viewport {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 90px 20px 40px; /* Margen para evitar solapamiento con la top-bar */
            overflow: hidden;
        }

        .cert-wrapper-outer {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: visible;
        }

        .cert-scaler {
            width: 1122px; /* A4 horizontal exacto en pixeles (96 DPI) */
            height: 793px;
            transform-origin: center center;
            flex-shrink: 0;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            background-color: #ffffff;
            border-radius: 8px;
            position: relative;
            padding: 38px;
        }

        /* Marco Exterior (Verde Institucional) */
        .border-outer {
            width: 1046px; /* 1122 - 76 de padding */
            height: 717px; /* 793 - 76 de padding */
            border: 6px solid #0B5E2E;
            border-radius: 10px;
            position: relative;
            background-color: #fdfdfb; /* Fondo sutil color crema/marfil */
        }

        /* Marco Interior (Dorado) */
        .border-inner {
            position: absolute;
            top: 8px;
            left: 8px;
            right: 8px;
            bottom: 8px;
            border: 2.5px solid #C9A227;
            border-radius: 6px;
        }

        /* Esquineros Ornamentales en Oro */
        .corner {
            position: absolute;
            width: 40px;
            height: 40px;
            border: 2.5px solid #C9A227;
        }
        .corner-tl { top: 4px; left: 4px; border-right: none; border-bottom: none; }
        .corner-tr { top: 4px; right: 4px; border-left: none; border-bottom: none; }
        .corner-bl { bottom: 4px; left: 4px; border-right: none; border-top: none; }
        .corner-br { bottom: 4px; right: 4px; border-left: none; border-top: none; }

        /* Contenedor del Contenido */
        .content-table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }

        .header-cell {
            text-align: center;
            padding-top: 30px;
            height: 120px;
        }

        .logo-img {
            width: 85px;
            height: 85px;
            vertical-align: middle;
            margin-bottom: 8px;
        }

        .univ-title {
            font-family: 'Times New Roman', Georgia, serif;
            font-size: 24px;
            font-weight: bold;
            color: #0B5E2E;
            letter-spacing: 5px;
            text-transform: uppercase;
        }

        .univ-subtitle {
            font-size: 11px;
            color: #7f8c8d;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-top: 3px;
        }

        .divider {
            width: 150px;
            height: 3px;
            background-color: #C9A227;
            margin: 15px auto;
        }

        .body-cell {
            text-align: center;
            vertical-align: middle;
            padding: 0 60px;
        }

        .cert-title {
            font-family: 'Times New Roman', Georgia, serif;
            font-size: 34px;
            font-weight: bold;
            color: #0B5E2E;
            letter-spacing: 8px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .cert-subtitle {
            font-size: 13px;
            color: #7f8c8d;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 25px;
        }

        .cert-label {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 12px;
            font-style: italic;
        }

        .student-name {
            font-family: 'Times New Roman', Georgia, serif;
            font-size: 32px;
            font-weight: bold;
            color: #1a252f;
            border-bottom: 3px solid #C9A227;
            display: inline-block;
            padding-bottom: 8px;
            margin-bottom: 20px;
            letter-spacing: 1.5px;
        }

        .cert-description {
            font-size: 14px;
            color: #555555;
            line-height: 1.7;
            margin: 0 auto 16px;
            max-width: 800px;
        }

        .course-title {
            font-size: 21px;
            font-weight: bold;
            color: #0B5E2E;
            margin-bottom: 8px;
        }

        .course-hours {
            font-size: 13px;
            color: #7f8c8d;
            margin-bottom: 16px;
        }

        .date-location {
            font-size: 13px;
            color: #555555;
            margin-bottom: 20px;
        }

        /* Tablas de Pie de Página (QR, Sello, Firmas) */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-cell {
            vertical-align: bottom;
        }

        .qr-container {
            width: 95px;
            text-align: center;
        }

        .qr-img {
            width: 90px;
            height: 90px;
            border: 2px solid #0B5E2E;
            border-radius: 4px;
            padding: 4px;
            background-color: #ffffff;
        }

        .qr-label {
            font-size: 8px;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 5px;
        }

        .signatures-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sign-cell {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 20px;
        }

        .sign-graphic {
            height: 55px;
            margin-bottom: -5px;
        }

        .sign-line {
            width: 180px;
            height: 1px;
            background-color: #bdc3c7;
            margin: 0 auto 6px;
        }

        .sign-name {
            font-size: 11px;
            font-weight: bold;
            color: #2c3e50;
        }

        .sign-title {
            font-size: 8px;
            color: #7f8c8d;
            margin-top: 1px;
        }

        .seal-container {
            text-align: right;
        }

        .seal-img {
            width: 90px;
            height: 90px;
            margin-bottom: 6px;
        }

        .serial-text {
            font-size: 8px;
            font-family: monospace;
            color: #95a5a6;
        }

        .serial-code {
            background-color: #f2f4f4;
            padding: 2px 6px;
            border-radius: 2px;
            border: 1px dashed #d5dbdb;
        }

        /* Estilos de Impresión */
        @media print {
            .top-bar {
                display: none !important;
            }
            body {
                background-color: #ffffff !important;
            }
            .cert-viewport {
                padding: 0 !important;
                margin: 0 !important;
                background-color: #ffffff !important;
            }
            .cert-wrapper-outer {
                height: 100vh !important;
            }
            .cert-scaler {
                transform: scale(1) !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
                padding: 10mm !important;
            }
        }
    </style>
</head>
<body>
    <!-- Barra Superior de Acciones -->
    <div class="top-bar">
        <div class="top-bar-title">
            <i class="fa-solid fa-graduation-cap" style="color: #C9A227; margin-right: 8px; font-size: 1.2rem;"></i>
            Vista Previa de Certificado
        </div>
        <div class="top-bar-actions">
            <a href="{{ route('cursos.completado', $curso) }}" class="btn-action btn-back">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
            <a href="{{ route('certificado.descargar', $curso) }}" class="btn-action btn-download">
                <i class="fa-solid fa-file-pdf"></i> Descargar PDF
            </a>
        </div>
    </div>

    <!-- Contenedor del Visor -->
    <div class="cert-viewport">
        <div class="cert-wrapper-outer">
            <div class="cert-scaler">
                <div class="border-outer">
                    <div class="border-inner">
                        <!-- Esquineros Ornamentales -->
                        <div class="corner corner-tl"></div>
                        <div class="corner corner-tr"></div>
                        <div class="corner corner-bl"></div>
                        <div class="corner corner-br"></div>
                        
                        <table class="content-table">
                            <!-- Fila de Cabecera (Logo e Institución) -->
                            <tr>
                                <td class="header-cell">
                                    <img class="logo-img" src="{{ asset('assets/unasicono.png') }}" alt="UNAS">
                                    <div class="univ-title">Universidad Nacional Agraria de la Selva</div>
                                    <div class="univ-subtitle">Tingo María - Perú</div>
                                    <div class="divider"></div>
                                </td>
                            </tr>
                            
                            <!-- Cuerpo Académico -->
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
                            
                            <!-- Fila de Firmas, QR y Sello Oficial -->
                            <tr>
                                <td style="padding: 0 35px 25px 35px; vertical-align: bottom;">
                                    <table class="footer-table">
                                        <tr>
                                            <!-- QR de Validación -->
                                            <td class="footer-cell" style="width: 25%;">
                                                <div class="qr-container">
                                                    <!-- PNG QR de Alta Calidad -->
                                                    <img class="qr-img" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('certificado.verificar', $numeroCertificado)) }}" alt="QR Code">
                                                    <div class="qr-label">Verificar Certificado</div>
                                                </div>
                                            </td>
                                            
                                            <!-- Firmas Académicas -->
                                            <td class="footer-cell" style="width: 50%;">
                                                <table class="signatures-table">
                                                    <tr>
                                                        <!-- Rector -->
                                                        <td class="sign-cell">
                                                            <!-- Firma Cursiva Vectorial de Ejemplo 1 -->
                                                            <svg class="sign-graphic" viewBox="0 0 100 40" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M10 25 C 20 5, 25 10, 30 18 C 35 25, 40 30, 45 20 C 50 10, 52 15, 55 25 C 60 30, 65 18, 70 12 C 75 8, 80 15, 85 28" fill="none" stroke="#2c3e50" stroke-width="1.8" stroke-linecap="round"/>
                                                            </svg>
                                                            <div class="sign-line"></div>
                                                            <div class="sign-name">Dr. Milthon Honorio Muñoz Berrocal</div>
                                                            <div class="sign-title">Rector de la UNAS</div>
                                                            <div class="sign-title" style="font-style: italic; color: #95a5a6; font-size: 7px; margin-top: 1px;">(Solo con propósitos de demostración)</div>
                                                        </td>
                                                        
                                                        <!-- OTI -->
                                                        <td class="sign-cell">
                                                            <!-- Firma Cursiva Vectorial de Ejemplo 2 -->
                                                            <svg class="sign-graphic" viewBox="0 0 100 40" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M15 28 C 22 22, 28 8, 32 15 C 36 22, 38 32, 42 25 C 46 18, 50 12, 54 18 C 58 24, 62 28, 66 18 C 70 8, 74 15, 80 22 C 84 28, 88 18, 92 12" fill="none" stroke="#2c3e50" stroke-width="1.5" stroke-linecap="round"/>
                                                            </svg>
                                                            <div class="sign-line"></div>
                                                            <div class="sign-name">Firma de Ejemplo</div>
                                                            <div class="sign-title">Director(a) de OTI</div>
                                                            <div class="sign-title" style="font-style: italic; color: #95a5a6; font-size: 7px; margin-top: 1px;">(Solo con propósitos de demostración)</div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            
                                            <!-- Sello Universitario y Serial -->
                                            <td class="footer-cell" style="width: 25%; text-align: right;">
                                                <div class="seal-container">
                                                    <img class="seal-img" src="{{ asset('assets/gold_seal.png') }}" alt="Sello Oficial">
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
        </div>
    </div>

    <!-- Script para escalado dinámico en pantalla -->
    <script>
        function adjustScale() {
            const container = document.querySelector('.cert-scaler');
            const wrapper = document.querySelector('.cert-wrapper-outer');
            if (!container || !wrapper) return;
            
            const baseWidth = 1122;
            const baseHeight = 793;
            
            // Espacio disponible en viewport
            const availableWidth = wrapper.clientWidth - 40; // padding lateral
            const availableHeight = window.innerHeight - 150; // considerando la top-bar y márgenes
            
            const scaleX = availableWidth / baseWidth;
            const scaleY = availableHeight / baseHeight;
            const scale = Math.min(scaleX, scaleY, 1); // Escalar hacia abajo si la pantalla es chica, no agrandar
            
            container.style.transform = `scale(${scale})`;
            wrapper.style.height = `${baseHeight * scale}px`;
        }
        
        window.addEventListener('resize', adjustScale);
        window.addEventListener('load', adjustScale);
        document.addEventListener('DOMContentLoaded', adjustScale);
    </script>
</body>
</html>
