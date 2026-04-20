<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar Certificado - UNAS</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f0fdf4 0%, #f9fafb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .verify-container {
            max-width: 500px;
            width: 100%;
        }
        .verify-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .verify-header {
            background: linear-gradient(135deg, #0B5E2E 0%, #0d7a3f 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .verify-logo {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
        }
        .verify-logo i { font-size: 28px; color: #0B5E2E; }
        .verify-title { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .verify-subtitle { font-size: 12px; opacity: 0.9; }
        .verify-body { padding: 30px; }
        
        .status-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px;
        }
        .status-icon.success { background: #dcfce7; color: #16a34a; }
        .status-icon.error { background: #fee2e2; color: #dc2626; }
        
        .verify-status { text-align: center; margin-bottom: 24px; }
        .verify-status h2 { font-size: 22px; color: #1f2937; margin-bottom: 8px; }
        .verify-status p { color: #6b7280; font-size: 14px; }
        
        .cert-details {
            background: #f9fafb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .cert-detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .cert-detail-row:last-child { border-bottom: none; }
        .cert-detail-label { color: #6b7280; font-size: 13px; }
        .cert-detail-value { color: #1f2937; font-weight: 600; font-size: 13px; text-align: right; }
        
        .verify-footer {
            text-align: center;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }
        .verify-code {
            font-family: monospace;
            background: #f3f4f6;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            color: #374151;
        }
        
        .loading { text-align: center; padding: 40px; }
        .loading i { font-size: 32px; color: #0B5E2E; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="verify-card">
            <div class="verify-header">
                <div class="verify-logo">
                    <i class="fas fa-university"></i>
                </div>
                <h1 class="verify-title">UNAS</h1>
                <p class="verify-subtitle">Universidad Nacional Agraria de la Selva</p>
            </div>
            
            <div class="verify-body" id="verifyContent">
                <div class="loading">
                    <i class="fas fa-spinner"></i>
                </div>
            </div>
        </div>
    </div>

    <script>
        const codigo = window.location.pathname.split('/').pop();
        
        fetch('/verificar/' + codigo)
            .then(r => r.json())
            .then(data => {
                const content = document.getElementById('verifyContent');
                
                if (data.valido) {
                    const d = data.datos;
                    content.innerHTML = `
                        <div class="verify-status">
                            <div class="status-icon success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h2>Certificado Válido</h2>
                            <p>Este certificado ha sido verificado exitosamente</p>
                        </div>
                        
                        <div class="cert-details">
                            <div class="cert-detail-row">
                                <span class="cert-detail-label">Estudiante</span>
                                <span class="cert-detail-value">${d.nombre}</span>
                            </div>
                            <div class="cert-detail-row">
                                <span class="cert-detail-label">Curso</span>
                                <span class="cert-detail-value">${d.curso}</span>
                            </div>
                            <div class="cert-detail-row">
                                <span class="cert-detail-label">Carga Horaria</span>
                                <span class="cert-detail-value">${d.carga_horaria} horas</span>
                            </div>
                            <div class="cert-detail-row">
                                <span class="cert-detail-label">Fecha de Completado</span>
                                <span class="cert-detail-value">${d.fecha_completado || 'N/A'}</span>
                            </div>
                        </div>
                        
                        <div class="verify-footer">
                            <p class="verify-code">${d.codigo}</p>
                        </div>
                    `;
                } else {
                    content.innerHTML = `
                        <div class="verify-status">
                            <div class="status-icon error">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <h2>Certificado No Válido</h2>
                            <p>${data.mensaje}</p>
                        </div>
                    `;
                }
            })
            .catch(() => {
                document.getElementById('verifyContent').innerHTML = `
                    <div class="verify-status">
                        <div class="status-icon error">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h2>Error de Verificación</h2>
                        <p>No se pudo verificar el certificado. Intente más tarde.</p>
                    </div>
                `;
            });
    </script>
</body>
</html>
