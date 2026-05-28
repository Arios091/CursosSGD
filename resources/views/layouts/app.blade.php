<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php
    $sidebarLogo = \App\Models\PageSetting::getValue('logo');
    $pageSettings = \App\Models\PageSetting::getAll();
    $primaryColor = $pageSettings['primary_color'] ?? '#0B5E2E';
    $secondaryColor = $pageSettings['secondary_color'] ?? '#C9A227';
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ $sidebarLogo ? asset('storage/' . $sidebarLogo) : asset('assets/unasicono.png') }}">
    
    <!-- Livewire Styles -->
    @livewireStyles

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistema de Gestión de Docencia') }}</title>

    <script src="{{ asset('js/app.js') }}" defer></script>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <link href="{{ asset('css/custom-layout.css') }}" rel="stylesheet">
    <style>
        :root {
            --verde-institucional: {{ $primaryColor }};
            --verde-hover: {{ $primaryColor }};
            --dorado: {{ $secondaryColor }};
            --dorado-hover: {{ $secondaryColor }};
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
        @include('layouts.partials.sidebar')

        <div class="main-content"        <div class="main-content" id="mainContent">
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
    
    <!-- Livewire Scripts -->
    @livewireScripts

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
