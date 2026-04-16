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
            --blanco: #FFFFFF;
            --gris-50: #F9FAFB;
            --gris-100: #F3F4F6;
            --gris-200: #E5E7EB;
            --gris-600: #4B5563;
            --gris-800: #1F2937;
            --gris-900: #111827;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gris-50);
            color: var(--gris-900);
        }

        .sidebar-layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #0B5E2E 0%, #094525 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .logo-icon {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #0B5E2E;
        }

        .logo-text {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .user-info {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05);
        }

        .user-name {
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sidebar-menu {
            flex: 1;
            padding: 16px 0;
        }

        .menu-item {
            display: block;
            padding: 14px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            border-left: 4px solid transparent;
            transition: all 0.2s;
        }

        .menu-item:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            text-decoration: none;
        }

        .menu-item.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: #C9A227;
        }

        .menu-item i {
            width: 24px;
            margin-right: 12px;
            text-align: center;
        }

        .menu-item .badge {
            float: right;
            background: #C9A227;
            color: #0B5E2E;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 10px;
        }

        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 0;
            min-height: 100vh;
        }

        .top-bar {
            background: white;
            padding: 16px 30px;
            border-bottom: 1px solid var(--gris-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .top-bar-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--gris-800);
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: var(--verde-institucional);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .page-content {
            padding: 30px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                height: auto;
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar-layout {
                flex-direction: column;
            }
        }

        .btn-primary {
            background-color: var(--verde-institucional);
            border-color: var(--verde-institucional);
        }

        .btn-warning {
            background-color: var(--dorado);
            border-color: var(--dorado);
            color: white;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="sidebar-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="logo-container">
                    <div class="logo-icon">
                        <img src="{{ asset('assets/unasicono.png') }}" alt="UNAS" style="height: 60px;">
                    </div>
                    <span class="logo-text">UNAS</span>
                </div>
                <small style="color: rgba(255,255,255,0.7); font-size: 11px;">Universidad Nacional Agraria de la Selva</small>
            </div>
            
            <div class="user-info">
                <div class="user-name">
                    <i class="fas fa-user-circle"></i>
                    {{ Auth::user()->name }}
                </div>
                @if(Auth::user()->role === 'admin')
                    <small style="color: #C9A227;">Administrador</small>
                @else
                    <small style="color: rgba(255,255,255,0.6);">Estudiante</small>
                @endif
            </div>

            <nav class="sidebar-menu">
                <a href="{{ route('home') }}" class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Inicio
                </a>
                <a href="{{ route('home') }}" class="menu-item {{ request()->routeIs('cursos.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i> Mis Cursos
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-certificate"></i> Certificados
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-user"></i> Perfil
                </a>
            </nav>

            <div style="padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                <a href="{{ route('logout') }}" class="menu-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-bar">
                <div class="top-bar-title">
                    @yield('page-title', 'Panel de Control')
                </div>
                <div class="user-dropdown">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span style="font-weight: 500;">{{ Auth::user()->name }}</span>
                </div>
            </div>
            
            <div class="page-content">
                @yield('content')
            </div>
        </div>
    </div>
    @livewireScripts
</body>
</html>