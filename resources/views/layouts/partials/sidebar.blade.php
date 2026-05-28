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
            <small style="color: var(--dorado);">Admin Global</small>
        @elseif($esAdmin)
            <small style="color: var(--dorado);">Administrador</small>
        @elseif($esDocente)
            <small style="color: var(--dorado);">Docente</small>
        @else
            <small style="color: rgba(255,255,255,0.6);">Estudiante</small>
        @endif
    </div>

    <nav class="sidebar-menu">
        @if($esAdmin || $esAdminGlobal)
            <!-- Menú de Administradores -->
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
        @else
            <!-- Menú de Estudiantes y Docentes -->
            <a href="{{ route('home') }}" class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Inicio
            </a>
            <a href="{{ route('cursos.index') }}" class="menu-item {{ request()->routeIs('cursos.index') || request()->routeIs('cursos.ver') ? 'active' : '' }}">
                <i class="fas fa-book"></i> Mis Cursos
            </a>
            <a href="{{ route('perfil') }}" class="menu-item {{ request()->routeIs('perfil') ? 'active' : '' }}">
                <i class="fas fa-user"></i> Perfil
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
