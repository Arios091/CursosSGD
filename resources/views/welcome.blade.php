<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/unasicono.png') }}">
    <title>Bienvenido - Sistema de Gestión de Docencia</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #fff; color: #1f2937; }

        .top-bar {
            background: #0B5E2E;
            padding: 8px 0;
            font-size: 13px;
            color: rgba(255,255,255,0.8);
        }
        .top-bar .container {
            max-width: 1200px; margin: 0 auto; padding: 0 24px;
            display: flex; justify-content: flex-end; gap: 24px;
        }
        .top-bar i { margin-right: 6px; }

        nav {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            position: sticky; top: 0; z-index: 100;
        }
        nav .container {
            max-width: 1200px; margin: 0 auto; padding: 0 24px;
            display: flex; align-items: center; justify-content: space-between;
            height: 72px;
        }
        .nav-logo {
            display: flex; align-items: center; gap: 12px;
            text-decoration: none;
        }
        .nav-logo img { height: 44px; }
        .nav-logo span {
            font-size: 18px; font-weight: 700; color: #0B5E2E;
        }
        .nav-links { display: flex; align-items: center; gap: 12px; }
        .nav-links a {
            text-decoration: none; font-weight: 500; font-size: 14px;
            padding: 10px 20px; border-radius: 8px; transition: all 0.2s;
        }
        .nav-links .btn-outline {
            color: #0B5E2E; border: 1px solid #0B5E2E;
        }
        .nav-links .btn-outline:hover { background: #f0fdf4; }
        .nav-links .btn-solid {
            background: #0B5E2E; color: white;
        }
        .nav-links .btn-solid:hover { background: #094525; }

        .hero {
            background: linear-gradient(135deg, #0B5E2E 0%, #06582a 50%, #0a4e28 100%);
            min-height: 520px;
            display: flex; align-items: center;
            position: relative; overflow: hidden;
        }
        .hero::before {
            content: ''; position: absolute; top: -50%; right: -20%;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(201,162,39,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .hero::after {
            content: ''; position: absolute; bottom: 0; left: 0; right: 0;
            height: 4px; background: linear-gradient(90deg, #C9A227, #0B5E2E, #C9A227);
        }
        .hero .container {
            max-width: 1200px; margin: 0 auto; padding: 60px 24px;
            display: flex; align-items: center; gap: 60px;
            position: relative; z-index: 1;
        }
        .hero-content { flex: 1; }
        .hero-content h1 {
            color: white; font-size: 42px; font-weight: 800; line-height: 1.2; margin-bottom: 16px;
        }
        .hero-content h1 span { color: #C9A227; }
        .hero-content p {
            color: rgba(255,255,255,0.8); font-size: 17px; line-height: 1.7; margin-bottom: 32px;
            max-width: 540px;
        }
        .hero-buttons { display: flex; gap: 12px; }
        .hero-buttons a {
            text-decoration: none; font-weight: 600; font-size: 15px;
            padding: 14px 28px; border-radius: 8px; transition: all 0.2s;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .hero-buttons .btn-primary {
            background: #C9A227; color: white;
        }
        .hero-buttons .btn-primary:hover { background: #b89120; transform: translateY(-1px); }
        .hero-buttons .btn-secondary {
            background: rgba(255,255,255,0.12); color: white; border: 1px solid rgba(255,255,255,0.25);
        }
        .hero-buttons .btn-secondary:hover { background: rgba(255,255,255,0.2); }
        .hero-image {
            flex: 0 0 380px; height: 320px;
            background: rgba(255,255,255,0.08);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .hero-image i { font-size: 100px; color: rgba(255,255,255,0.2); }

        section { padding: 80px 0; }
        section .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
        .section-title {
            text-align: center; margin-bottom: 56px;
        }
        .section-title h2 {
            font-size: 32px; font-weight: 700; color: #0B5E2E; margin-bottom: 12px;
        }
        .section-title p { color: #6b7280; font-size: 16px; max-width: 600px; margin: 0 auto; }

        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; }
        .feature-card {
            background: white; border-radius: 16px; padding: 40px 28px;
            text-align: center; border: 1px solid #e5e7eb;
            transition: all 0.3s;
        }
        .feature-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); }
        .feature-icon {
            width: 72px; height: 72px; border-radius: 50%;
            background: linear-gradient(135deg, #0B5E2E, #0d7a3f);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px; font-size: 28px; color: white;
        }
        .feature-card h3 { font-size: 18px; font-weight: 600; margin-bottom: 8px; }
        .feature-card p { color: #6b7280; font-size: 14px; line-height: 1.6; }

        .stats { background: #f9fafb; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 32px; text-align: center; }
        .stat-number { font-size: 36px; font-weight: 800; color: #0B5E2E; }
        .stat-label { color: #6b7280; font-size: 14px; margin-top: 4px; }

        .footer {
            background: #0B5E2E; padding: 40px 0; color: rgba(255,255,255,0.7); font-size: 14px;
        }
        .footer .container {
            max-width: 1200px; margin: 0 auto; padding: 0 24px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .footer-logo { display: flex; align-items: center; gap: 10px; }
        .footer-logo img { height: 36px; }
        .footer-logo span { color: white; font-weight: 700; }

        @media (max-width: 768px) {
            .hero .container { flex-direction: column; text-align: center; padding: 40px 24px; }
            .hero-content h1 { font-size: 28px; }
            .hero-content p { margin: 0 auto 24px; }
            .hero-buttons { justify-content: center; flex-wrap: wrap; }
            .hero-image { display: none; }
            .features-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .footer .container { flex-direction: column; gap: 16px; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="container">
            <span><i class="fas fa-phone"></i> (062) 562341</span>
            <span><i class="fas fa-envelope"></i> mesadepartes@unas.edu.pe</span>
        </div>
    </div>

    <nav>
        <div class="container">
            <a href="/" class="nav-logo">
                <img src="{{ asset('assets/unasicono.png') }}" alt="UNAS">
                <span>Cursos SGD</span>
            </a>
            <div class="nav-links">
                <a href="{{ route('login') }}" class="btn-outline"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
                <a href="{{ route('register') }}" class="btn-solid"><i class="fas fa-user-plus"></i> Crear Cuenta</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Sistema de <span>Gestión de Docencia</span> UNAS</h1>
                <p>Plataforma oficial de educación continua de la Universidad Nacional Agraria de la Selva. Accede a cursos especializados, gestiona tu progreso y obtén certificaciones con validez académica.</p>
                <div class="hero-buttons">
                    <a href="{{ route('register') }}" class="btn-primary"><i class="fas fa-graduation-cap"></i> Comenzar Ahora</a>
                    <a href="{{ route('login') }}" class="btn-secondary"><i class="fas fa-sign-in-alt"></i> Ya tengo cuenta</a>
                </div>
            </div>
            <div class="hero-image">
                <i class="fas fa-university"></i>
            </div>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="section-title">
                <h2>¿Por qué elegir Cursos SGD?</h2>
                <p>Diseñado para la comunidad universitaria con estándares de calidad académica</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-clock"></i></div>
                    <h3>Aprendizaje Asíncrono</h3>
                    <p>Estudia a tu propio ritmo, en cualquier momento y desde cualquier lugar del mundo.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-certificate"></i></div>
                    <h3>Certificación Oficial</h3>
                    <p>Al finalizar cada curso, obtén un certificado respaldado por la OTI y la UNAS.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-laptop-code"></i></div>
                    <h3>Recursos Digitales</h3>
                    <p>Material de alta calidad: videos, lecturas, cuestionarios y evaluaciones en línea.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div>
                    <div class="stat-number">100%</div>
                    <div class="stat-label">En línea</div>
                </div>
                <div>
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Disponibilidad</div>
                </div>
                <div>
                    <div class="stat-number">+10</div>
                    <div class="stat-label">Cursos Activos</div>
                </div>
                <div>
                    <div class="stat-number">+500</div>
                    <div class="stat-label">Estudiantes</div>
                </div>
            </div>
        </div>
    </section>

    <div class="footer">
        <div class="container">
            <div class="footer-logo">
                <img src="{{ asset('assets/unasicono.png') }}" alt="UNAS">
                <span>Universidad Nacional Agraria de la Selva</span>
            </div>
            <div>
                <i class="fas fa-copyright"></i> {{ date('Y') }} UNAS - Todos los derechos reservados<br>
                Carretera Central Km. 1.21, Tingo María, Huánuco
            </div>
        </div>
    </div>
</body>
</html>