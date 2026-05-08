<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php $sidebarLogo = \App\Models\PageSetting::getValue('logo'); @endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ $sidebarLogo ? asset('storage/' . $sidebarLogo) : asset('assets/unasicono.png') }}">
    
    <!-- Livewire Styles - Use CDN -->
    @if(app()->environment('production'))
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/livewire@2.12.1/dist/livewire.css">
    @else
    @livewireStyles
    @endif

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistema de Gestión de Docencia') }}</title>

    <script src="{{ asset('js/app.js') }}" defer></script>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

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
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gris-50);
            color: var(--gris-900);
            margin: 0;
            padding: 0;
        }

        .page-transition {
            opacity: 1;
            transition: opacity 0.2s ease;
        }

        .page-loading {
            opacity: 0.5;
            pointer-events: none;
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
            z-index: 1000;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            border-left: 4px solid transparent;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .menu-item:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            text-decoration: none;
            padding-left: 24px;
        }

        .menu-item.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: #C9A227;
            padding-left: 24px;
        }

        .menu-item i {
            width: 20px;
            margin-right: 12px;
            text-align: center;
            transition: transform 0.2s;
        }

        .menu-item:hover i {
            transform: scale(1.1);
        }

        .menu-item .badge {
            margin-left: auto;
            background: #C9A227;
            color: #0B5E2E;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 600;
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .main-content {
            flex: 1;
            margin-left: 280px;
            min-height: 100vh;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
            z-index: 99;
            box-shadow: var(--shadow-sm);
        }

        .top-bar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .mobile-menu-btn {
            display: none;
            width: 40px;
            height: 40px;
            border: none;
            background: var(--gris-100);
            border-radius: var(--radius-md);
            cursor: pointer;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .mobile-menu-btn:hover {
            background: var(--gris-200);
        }

        .mobile-menu-btn i {
            color: var(--gris-800);
            font-size: 18px;
        }

        .breadcrumbs {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--gris-600);
        }

        .breadcrumbs a {
            color: var(--verde-institucional);
            text-decoration: none;
            transition: color 0.2s;
        }

        .breadcrumbs a:hover {
            color: var(--verde-hover);
            text-decoration: underline;
        }

        .breadcrumbs i {
            font-size: 10px;
            color: var(--gris-400);
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
            padding: 8px 12px;
            border-radius: var(--radius-md);
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
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(11, 94, 46, 0.3);
        }

        .page-content {
            padding: 30px;
        }

        .btn-primary {
            background-color: var(--verde-institucional);
            border-color: var(--verde-institucional);
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background-color: var(--verde-hover);
            border-color: var(--verde-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-warning {
            background-color: var(--dorado);
            border-color: var(--dorado);
            color: white;
            transition: all 0.2s;
        }

        .btn-warning:hover {
            background-color: var(--dorado-hover);
            border-color: var(--dorado-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .card {
            border: none;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
        }

        .btn {
            border-radius: var(--radius-md);
            font-weight: 500;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn:active {
            transform: scale(0.98);
        }

        .btn.btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: flex;
            }

            .top-bar {
                padding: 12px 16px;
            }

            .page-content {
                padding: 20px 16px;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
                opacity: 0;
                transition: opacity 0.3s;
            }

            .sidebar-overlay.show {
                display: block;
                opacity: 1;
            }
        }

        .btn-loading {
            position: relative;
            pointer-events: none;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-top: -8px;
            margin-left: -8px;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 300px;
            max-width: 450px;
            animation: slideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .toast.toast-exit {
            animation: slideOut 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .toast-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .toast-success .toast-icon {
            background: #dcfce7;
            color: #16a34a;
        }

        .toast-error .toast-icon {
            background: #fee2e2;
            color: #dc2626;
        }

        .toast-warning .toast-icon {
            background: #fef3c7;
            color: #f59e0b;
        }

        .toast-info .toast-icon {
            background: #dbeafe;
            color: #3b82f6;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 600;
            font-size: 14px;
            color: var(--gris-900);
            margin-bottom: 2px;
        }

        .toast-message {
            font-size: 13px;
            color: var(--gris-600);
        }

        .toast-close {
            background: none;
            border: none;
            color: var(--gris-400);
            cursor: pointer;
            padding: 4px;
            transition: color 0.2s;
        }

        .toast-close:hover {
            color: var(--gris-600);
        }

        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gris-200);
            z-index: 10001;
            overflow: hidden;
        }

        .page-loader-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--verde-institucional), var(--dorado));
            width: 0;
            transition: width 0.3s;
        }

        .page-loader.active .page-loader-bar {
            animation: loading 1.5s ease-in-out infinite;
        }

        @keyframes loading {
            0% { width: 0; margin-left: 0; }
            50% { width: 70%; }
            100% { width: 0; margin-left: 100%; }
        }

        .skeleton {
            background: linear-gradient(90deg, var(--gris-200) 25%, var(--gris-100) 50%, var(--gris-200) 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
            border-radius: var(--radius-md);
        }

        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .skeleton-text {
            height: 16px;
            margin-bottom: 8px;
        }

        .skeleton-title {
            height: 24px;
            width: 60%;
            margin-bottom: 12px;
        }

        .skeleton-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        @media (max-width: 768px) {
            .toast-container {
                top: 10px;
                right: 10px;
                left: 10px;
            }

            .toast {
                min-width: auto;
                max-width: 100%;
            }
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--verde-institucional);
            box-shadow: 0 0 0 3px rgba(11, 94, 46, 0.15);
        }

        .table-hover tbody tr {
            transition: background-color 0.15s;
        }

        .table-hover tbody tr:hover {
            background-color: var(--gris-50) !important;
        }

        a {
            transition: color 0.2s;
        }
    </style>
</head>
@php
    $user = Auth::user();
    $userRole = $user->role ?? null;
    $esAdminGlobal = $user && $user->isAdminGlobal();
    $esAdmin = $user && $user->isAdmin();
    $esDocente = $user && $user->isDocente();
@endphp

<body>
    @auth
    <div class="page-loader" id="pageLoader">
        <div class="page-loader-bar"></div>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="sidebar-layout">
        @if($esAdmin || $esAdminGlobal)
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                    <div class="logo-container">
                        <img src="{{ $sidebarLogo ? asset('storage/' . $sidebarLogo) : asset('assets/unasicono.png') }}" alt="UNAS" style="height: 44px; width: 44px; object-fit: contain; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        <span class="logo-text">UNAS</span>
                    </div>
                    <small style="color: rgba(255,255,255,0.7); font-size: 11px;">Universidad Nacional Agraria de la Selva</small>
                </div>

                <div class="user-info">
                    <div class="user-name">
                        <i class="fas fa-user-circle"></i>
                        {{ Auth::user()->name }}
                    </div>
                    @if($esAdminGlobal)
                        <small style="color: #C9A227;">Admin Global</small>
                    @else
                        <small style="color: #C9A227;">Administrador</small>
                    @endif
                </div>

                <nav class="sidebar-menu">
                <a href="{{ route('home') }}" class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="{{ route('cursos.index') }}" class="menu-item {{ request()->routeIs('cursos.index') || request()->routeIs('crear.curso') || request()->routeIs('cursos.edit') ? 'active' : '' }}">
                    <i class="fas fa-book"></i> Gestión de Cursos
                </a>
                @if($esAdminGlobal)
                <a href="{{ route('admin.users.index') }}" class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i> Gestión de Usuarios
                </a>
                <a href="{{ route('admin.page-settings') }}" class="menu-item {{ request()->routeIs('admin.page-settings') ? 'active' : '' }}">
                    <i class="fas fa-paint-brush"></i> Personalizar Página
                </a>
                @endif
            </nav>

            <div class="sidebar-footer">
                <a href="{{ route('logout') }}" class="menu-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
        @else
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                    <div class="logo-container">
                        <img src="{{ $sidebarLogo ? asset('storage/' . $sidebarLogo) : asset('assets/unasicono.png') }}" alt="UNAS" style="height: 44px; width: 44px; object-fit: contain; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        <span class="logo-text">UNAS</span>
                    </div>
                <small style="color: rgba(255,255,255,0.7); font-size: 11px;">Universidad Nacional Agraria de la Selva</small>
            </div>

            <div class="user-info">
                <div class="user-name">
                    <i class="fas fa-user-circle"></i>
                    {{ Auth::user()->name }}
                </div>
                @if($esDocente)
                    <small style="color: #C9A227;">Docente</small>
                @else
                    <small style="color: rgba(255,255,255,0.6);">Estudiante</small>
                @endif
            </div>

            <nav class="sidebar-menu">
                <a href="{{ route('home') }}" class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Inicio
                </a>
                <a href="{{ route('cursos.index') }}" class="menu-item {{ request()->routeIs('cursos.index') ? 'active' : '' }}">
                    <i class="fas fa-book"></i> Mis Cursos
                </a>
                <a href="{{ route('perfil') }}" class="menu-item {{ request()->routeIs('perfil') ? 'active' : '' }}">
                    <i class="fas fa-user"></i> Perfil
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="{{ route('logout') }}" class="menu-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
        @endif

        <div class="main-content" id="mainContent">
            <div class="top-bar">
                <div class="top-bar-left">
                    <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Abrir menú">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="breadcrumbs">
                        <a href="{{ route('home') }}"><i class="fas fa-home"></i></a>
                        @hasSection('breadcrumbs')
                            @yield('breadcrumbs')
                        @else
                            <span><i class="fas fa-chevron-right"></i></span>
                            <span style="color: var(--gris-800);">@yield('page-title', 'Panel de Control')</span>
                        @endif
                    </div>
                </div>
                <div class="user-dropdown">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span style="font-weight: 500;">{{ Auth::user()->name }}</span>
                </div>
            </div>

            <div class="page-content" id="pageContent">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mainContent = document.getElementById('mainContent');

        function toggleSidebar() {
            sidebar.classList.toggle('open');
            sidebarOverlay.classList.toggle('show');
            document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
        }

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', toggleSidebar);
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', toggleSidebar);
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('open');
                sidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        });

        document.addEventListener('showToast', function(e) {
            const { type = 'info', title = '', message = '', duration = 5000 } = e.detail || {};
            showToast(type, title, message, duration);
        });

        function showToast(type, title, message, duration = 5000) {
            const container = document.getElementById('toastContainer');
            const icons = {
                success: 'fa-check',
                error: 'fa-times',
                warning: 'fa-exclamation',
                info: 'fa-info'
            };
            const titles = {
                success: 'Éxito',
                error: 'Error',
                warning: 'Advertencia',
                info: 'Información'
            };

            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="fas ${icons[type] || icons.info}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">${title || titles[type] || 'Notificación'}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="dismissToast(this.parentElement)">
                    <i class="fas fa-times"></i>
                </button>
            `;

            container.appendChild(toast);

            if (duration > 0) {
                setTimeout(() => dismissToast(toast), duration);
            }

            toast.style.animation = 'fadeIn 0.3s ease';
        }

        function dismissToast(toast) {
            if (!toast || toast.classList.contains('toast-exit')) return;
            toast.classList.add('toast-exit');
            setTimeout(() => toast.remove(), 300);
        }

        document.addEventListener('click', function(e) {
            if (e.target.closest('a[href]') && !e.target.closest('a[href^="#"]')) {
                const pageLoader = document.getElementById('pageLoader');
                if (pageLoader) {
                    pageLoader.classList.add('active');
                }
            }
        });

        window.addEventListener('load', function() {
            const pageLoader = document.getElementById('pageLoader');
            if (pageLoader) {
                pageLoader.classList.remove('active');
            }
        });

        document.addEventListener('livewire:load', function() {
            const pageLoader = document.getElementById('pageLoader');
            if (pageLoader) {
                pageLoader.classList.remove('active');
            }
        });

        Livewire.hook('message.sent', () => {
            const pageLoader = document.getElementById('pageLoader');
            if (pageLoader) {
                pageLoader.classList.add('active');
            }
        });

        Livewire.hook('message.processed', () => {
            const pageLoader = document.getElementById('pageLoader');
            if (pageLoader) {
                pageLoader.classList.remove('active');
            }
        });
    </script>
    @else
        @yield('content')
    @endauth
    
    <!-- Livewire Scripts - Use CDN in production -->
    @if(app()->environment('production'))
        <script src="https://cdn.jsdelivr.net/npm/livewire@2.12.1/dist/livewire.js"></script>
    @else
        @livewireScripts
    @endif

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('success', 'Operación exitosa', '{{ session('success') }}');
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('error', 'Error', '{{ session('error') }}');
        });
    </script>
    @endif
</body>
</html>
