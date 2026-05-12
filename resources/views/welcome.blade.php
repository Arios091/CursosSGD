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
    @php
        function darkenColor($hex, $percent) {
            $hex = ltrim($hex, '#');
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            $r = max(0, min(255, (int)round($r * (1 - $percent))));
            $g = max(0, min(255, (int)round($g * (1 - $percent))));
            $b = max(0, min(255, (int)round($b * (1 - $percent))));
            return sprintf("#%02x%02x%02x", $r, $g, $b);
        }
        $settings = \App\Models\PageSetting::getAll();
        $primaryColor = $settings['primary_color'] ?? '#0B5E2E';
        $secondaryColor = $settings['secondary_color'] ?? '#C9A227';
        $heroTitle = $settings['hero_title'] ?? 'Sistema de <span>Gestión de Docencia</span> UNAS';
        $heroSubtitle = $settings['hero_subtitle'] ?? 'Plataforma oficial de educación continua de la Universidad Nacional Agraria de la Selva. Accede a cursos especializados, gestiona tu progreso y obtén certificaciones con validez académica.';
        $logo = isset($settings['logo']) ? asset('storage/' . $settings['logo']) : asset('assets/unasicono.png');
        $contactPhone = $settings['contact_phone'] ?? '(062) 562341';
        $contactEmail = $settings['contact_email'] ?? 'mesadepartes@unas.edu.pe';
        $contactAddress = $settings['contact_address'] ?? 'Carretera Central Km. 1.21, Tingo María, Huánuco';
        $heroDark = darkenColor($primaryColor, 0.25);
    @endphp
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #fff; color: #1f2937; }

        .top-bar {
            background: {{ $primaryColor }};
            padding: 8px 0;
            font-size: 13px;
            color: rgba(255,255,255,0.85);
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
        .nav-logo img { height: 44px; object-fit: contain; }
        .nav-logo span {
            font-size: 18px; font-weight: 700; color: {{ $primaryColor }};
        }
        .nav-links { display: flex; align-items: center; gap: 12px; }
        .nav-links a {
            text-decoration: none; font-weight: 500; font-size: 14px;
            padding: 10px 20px; border-radius: 8px; transition: all 0.2s;
        }
        .nav-links .btn-outline {
            color: {{ $primaryColor }}; border: 1px solid {{ $primaryColor }};
        }
        .nav-links .btn-outline:hover { background: {{ $primaryColor }}11; }
        .nav-links .btn-solid {
            background: {{ $primaryColor }}; color: white;
        }
        .nav-links .btn-solid:hover { filter: brightness(0.9); }

        .hero {
            background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $heroDark }} 100%);
            min-height: 560px;
            display: flex; align-items: center;
            position: relative; overflow: hidden;
        }
        .hero::before {
            content: ''; position: absolute; top: -50%; right: -20%;
            width: 600px; height: 600px;
            background: radial-gradient(circle, {{ $secondaryColor }}08 0%, transparent 70%);
            border-radius: 50%;
        }
        .hero::after {
            content: ''; position: absolute; bottom: 0; left: 0; right: 0;
            height: 4px; background: linear-gradient(90deg, {{ $secondaryColor }}, {{ $primaryColor }}, {{ $secondaryColor }});
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
        .hero-content h1 span { color: {{ $secondaryColor }}; }
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
            background: {{ $secondaryColor }}; color: white;
        }
        .hero-buttons .btn-primary:hover { filter: brightness(0.9); transform: translateY(-1px); }
        .hero-buttons .btn-secondary {
            background: rgba(255,255,255,0.12); color: white; border: 1px solid rgba(255,255,255,0.25);
        }
        .hero-buttons .btn-secondary:hover { background: rgba(255,255,255,0.2); }

        .hero-carousel {
            flex: 0 0 600px;
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 12px 32px rgba(0,0,0,0.2);
            aspect-ratio: 16/9;
        }
        .hero-carousel img {
            width: 100%; height: 100%; object-fit: cover;
            position: absolute; top: 0; left: 0;
            opacity: 0;
            transition: opacity 0.8s ease, transform 0.8s ease;
            transform: scale(1.05);
        }
        .hero-carousel img.active {
            opacity: 1;
            transform: scale(1);
        }
        .carousel-dots {
            position: absolute; bottom: 12px; left: 50%;
            transform: translateX(-50%);
            display: flex; gap: 8px;
        }
        .carousel-dots span {
            width: 10px; height: 10px; border-radius: 50%;
            background: rgba(255,255,255,0.4);
            cursor: pointer; transition: all 0.3s;
        }
        .carousel-dots span.active {
            background: white; transform: scale(1.2);
        }

        section { padding: 80px 0; }
        section .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
        .section-title {
            text-align: center; margin-bottom: 56px;
        }
        .section-title h2 {
            font-size: 32px; font-weight: 700; color: {{ $primaryColor }}; margin-bottom: 12px;
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
            background: linear-gradient(135deg, {{ $primaryColor }}, {{ $heroDark }});
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px; font-size: 28px; color: white;
        }
        .feature-card h3 { font-size: 18px; font-weight: 600; margin-bottom: 8px; }
        .feature-card p { color: #6b7280; font-size: 14px; line-height: 1.6; }

        .stats { background: {{ $primaryColor }}08; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 32px; text-align: center; }
        .stat-number { font-size: 36px; font-weight: 800; color: {{ $primaryColor }}; }
        .stat-label { color: #6b7280; font-size: 14px; margin-top: 4px; }

        .footer {
            background: {{ $primaryColor }}; padding: 40px 0; color: rgba(255,255,255,0.7); font-size: 14px;
        }
        .footer .container {
            max-width: 1200px; margin: 0 auto; padding: 0 24px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .footer-logo { display: flex; align-items: center; gap: 10px; }
        .footer-logo img { height: 36px; object-fit: contain; }
        .footer-logo span { color: white; font-weight: 700; }

        @media (max-width: 768px) {
            .hero .container { flex-direction: column; text-align: center; padding: 40px 24px; }
            .hero-content h1 { font-size: 28px; }
            .hero-content p { margin: 0 auto 24px; }
            .hero-buttons { justify-content: center; flex-wrap: wrap; }
            .hero-carousel { flex: none; width: 100%; max-width: 100%; }
            .features-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .footer .container { flex-direction: column; gap: 16px; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="container">
            <span><i class="fas fa-phone"></i> {{ $contactPhone }}</span>
            <span><i class="fas fa-envelope"></i> {{ $contactEmail }}</span>
        </div>
    </div>

    <nav>
        <div class="container">
            <a href="/" class="nav-logo">
                <img src="{{ $logo }}" alt="UNAS">
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
                <h1>{!! $heroTitle !!}</h1>
                <p>{{ $heroSubtitle }}</p>
                <div class="hero-buttons">
                    <a href="{{ route('register') }}" class="btn-primary"><i class="fas fa-graduation-cap"></i> Comenzar Ahora</a>
                    <a href="{{ route('login') }}" class="btn-secondary"><i class="fas fa-sign-in-alt"></i> Ya tengo cuenta</a>
                </div>
            </div>
            <div class="hero-carousel" id="heroCarousel">
                @php
                    $hasCarousel = false;
                    for($i = 1; $i <= 4; $i++) {
                        if(!empty($settings['carousel_' . $i])) { $hasCarousel = true; break; }
                    }
                @endphp
                @if($hasCarousel)
                    @for($i = 1; $i <= 4; $i++)
                        @if(!empty($settings['carousel_' . $i]))
                            <img src="{{ asset('storage/' . $settings['carousel_' . $i]) }}" class="{{ $i === 1 ? 'active' : '' }}" data-index="{{ $i - 1 }}">
                        @endif
                    @endfor
                    <div class="carousel-dots">
                        @for($i = 1; $i <= 4; $i++)
                            @if(!empty($settings['carousel_' . $i]))
                                <span class="{{ $i === 1 ? 'active' : '' }}" data-index="{{ $i - 1 }}"></span>
                            @endif
                        @endfor
                    </div>
                @else
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.08);border-radius:16px;border:1px solid rgba(255,255,255,0.1);">
                        <i class="fas fa-university" style="font-size: 80px; color: rgba(255,255,255,0.2);"></i>
                    </div>
                @endif
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
                <img src="{{ $logo }}" alt="UNAS">
                <span>Universidad Nacional Agraria de la Selva</span>
            </div>
            <div>
                <i class="fas fa-copyright"></i> {{ date('Y') }} UNAS - Todos los derechos reservados<br>
                {{ $contactAddress }}
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var carousel = document.getElementById('heroCarousel');
        if (!carousel) return;
        var images = carousel.querySelectorAll('img');
        var dots = carousel.querySelectorAll('.carousel-dots span');
        if (images.length <= 1) return;
        
        var current = 0;
        var interval = setInterval(function() {
            images[current].classList.remove('active');
            if (dots[current]) dots[current].classList.remove('active');
            current = (current + 1) % images.length;
            images[current].classList.add('active');
            if (dots[current]) dots[current].classList.add('active');
        }, 4000);
        
        dots.forEach(function(dot) {
            dot.addEventListener('click', function() {
                clearInterval(interval);
                var idx = parseInt(this.getAttribute('data-index'));
                images[current].classList.remove('active');
                dots[current].classList.remove('active');
                current = idx;
                images[current].classList.add('active');
                dots[current].classList.add('active');
            });
        });
    });
    </script>
</body>
</html>