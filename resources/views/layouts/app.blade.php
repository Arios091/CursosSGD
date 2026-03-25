<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @livewireStyles

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistema de Gestión de Docencia') }}</title>

    <script src="{{ asset('js/app.js') }}" defer></script>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --verde-institucional: #0B5E2E;
            --verde-hover: #094D25;
            --dorado: #C9A227;
            --dorado-hover: #B89120;
            --dorado-claro: #D4B13A;
            --blanco: #FFFFFF;
            --gris-50: #F9FAFB;
            --gris-100: #F3F4F6;
            --gris-200: #E5E7EB;
            --gris-300: #D1D5DB;
            --gris-400: #9CA3AF;
            --gris-600: #4B5563;
            --gris-700: #374151;
            --gris-800: #1F2937;
            --gris-900: #111827;
            --verde-success-50: #F0FDF4;
            --verde-success-600: #16A34A;
            --verde-success-700: #15803D;
            --verde-success-800: #166534;
            --verde-success-900: #14532D;
            --rojo-error-50: #FEF2F2;
            --rojo-error-600: #DC2626;
            --azul-info-50: #EFF6FF;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gris-50);
            color: var(--gris-900);
        }

        .navbar {
            background: linear-gradient(135deg, var(--verde-institucional) 0%, #0B5E2E 100%) !important;
        }

        .navbar-brand, .nav-link {
            color: white !important;
            font-weight: 500;
        }

        .nav-link:hover {
            color: rgba(255,255,255,0.8) !important;
        }

        .btn-primary {
            background-color: var(--verde-institucional);
            border-color: var(--verde-institucional);
        }

        .btn-primary:hover {
            background-color: var(--verde-hover);
            border-color: var(--verde-hover);
        }

        .btn-warning {
            background-color: var(--dorado);
            border-color: var(--dorado);
            color: white;
        }

        .btn-warning:hover {
            background-color: var(--dorado-hover);
            border-color: var(--dorado-hover);
            color: white;
        }

        .btn-outline-primary {
            color: var(--verde-institucional);
            border-color: var(--verde-institucional);
        }

        .btn-outline-primary:hover {
            background-color: var(--verde-institucional);
            border-color: var(--verde-institucional);
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .text-primary-custom {
            color: var(--verde-institucional) !important;
        }

        .badge-dorado {
            background-color: var(--dorado);
            color: white;
        }

        .bg-verde {
            background-color: var(--verde-institucional) !important;
        }

        .text-verde {
            color: var(--verde-institucional) !important;
        }

        .border-dorado {
            border-color: var(--dorado) !important;
        }

        .bg-dorado {
            background-color: var(--dorado) !important;
        }

        .text-dorado {
            color: var(--dorado) !important;
        }

        .success-bg {
            background-color: var(--verde-success-50);
        }

        .success-border {
            border-color: var(--verde-success-600) !important;
        }

        .error-bg {
            background-color: var(--rojo-error-50);
        }

        .progress-bar {
            background-color: var(--verde-institucional);
        }

        .nav-pills .nav-link.active {
            background-color: var(--verde-institucional);
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 8px;
        }

        .dropdown-item:hover {
            background-color: var(--gris-100);
        }

        .page-header {
            background: white;
            border-bottom: 1px solid var(--gris-200);
            padding: 1rem 0;
            margin-bottom: 1.5rem;
        }

        footer {
            background-color: var(--gris-900);
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark shadow-lg">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <i class="fas fa-graduation-cap me-2"></i>
                    <strong>SGD</strong> - Cursos
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto">
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-1"></i> Iniciar Sesión
                                </a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-user-circle me-2"></i>
                                    {{ Auth::user()->name }}
                                    @if(Auth::user()->role === 'admin')
                                        <span class="badge bg-dorado text-dark ms-2">Admin</span>
                                    @endif
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <div class="dropdown-header bg-light">
                                        <strong>{{ Auth::user()->name }}</strong><br>
                                        <small class="text-muted">{{ Auth::user()->email }}</small>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('home') }}">
                                        <i class="fas fa-home me-2"></i> Mi Panel
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>

        <footer class="text-white py-4 mt-5">
            <div class="container text-center">
                <p class="mb-0">
                    <small>&copy; {{ date('Y') }} Sistema de Gestión de Docencia - Todos los derechos reservados</small>
                </p>
            </div>
        </footer>
    </div>
    @livewireScripts
</body>
</html>
