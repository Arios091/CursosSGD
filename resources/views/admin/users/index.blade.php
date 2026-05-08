@extends('layouts.app')

@section('page-title', 'Gestión de Usuarios')

@section('breadcrumbs')
    <span><i class="fas fa-chevron-right"></i></span>
    <span style="color: var(--gris-800);">Gestión de Usuarios</span>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: var(--radius-lg);">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-radius: var(--radius-lg) var(--radius-lg) 0 0;">
                    <h2 class="h4 mb-0" style="color: #0B5E2E;">
                        <i class="fas fa-users mr-2"></i>Gestión de Usuarios
                    </h2>
                    <span class="badge" style="background: #0B5E2E; color: white; padding: 6px 12px;">{{ $users->total() }} usuarios</span>
                </div>
                <div class="card-body">

                <div style="background: #f8f9fa; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-4">
                            <label style="font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Buscar</label>
                            <div class="search-input-wrapper mt-1">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" id="searchInput" class="form-control" placeholder="Nombre o email..." value="{{ request('search') }}" style="height: 44px; border-radius: 8px; padding-left: 40px;">
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <label style="font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Rol</label>
                            <select id="roleSelect" class="form-select mt-1" style="height: 44px; border-radius: 8px;">
                                <option value="">Todos</option>
                                <option value="admin_global" {{ request('role') == 'admin_global' ? 'selected' : '' }}>Admin Global</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="docente" {{ request('role') == 'docente' ? 'selected' : '' }}>Docente</option>
                                <option value="estudiante" {{ request('role') == 'estudiante' ? 'selected' : '' }}>Estudiante</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label style="font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Ordenar</label>
                            <select id="sortSelect" class="form-select mt-1" style="height: 44px; border-radius: 8px;">
                                <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Más recientes</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Más antiguos</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Por nombre</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <button type="button" id="clearBtn" class="btn btn-outline-secondary w-100 mt-1" style="height: 44px; border-radius: 8px;">
                                <i class="fas fa-times me-1"></i> Limpiar
                            </button>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="text-muted mt-1" id="resultsCount" style="font-size: 13px;">
                                <i class="fas fa-users me-1"></i> <span class="count-number">{{ $users->total() }}</span> usuarios
                            </div>
                        </div>
                    </div>
                </div>

                <div id="usersTable">
                    <div id="loadingState" style="display: none;">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Fecha</th>
                                        <th>Cursos</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i = 0; $i < 5; $i++)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="skeleton skeleton-avatar"></div>
                                                <div style="margin-left: 12px;">
                                                    <div class="skeleton skeleton-title"></div>
                                                    <div class="skeleton skeleton-text" style="width: 60%;"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><div class="skeleton skeleton-text" style="width: 150px;"></div></td>
                                        <td><div class="skeleton skeleton-text" style="width: 80px;"></div></td>
                                        <td><div class="skeleton skeleton-text" style="width: 70px;"></div></td>
                                        <td><div class="skeleton skeleton-text" style="width: 30px;"></div></td>
                                        <td><div class="skeleton skeleton-text" style="width: 60px;"></div></td>
                                    </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="usersContent">
                        <div class="table-responsive">
                            <table class="table table-hover" style="margin-bottom: 0;">
                                <thead style="background: #f8f9fa;">
                                    <tr>
                                        <th style="font-weight: 600; color: #374151;">Nombre</th>
                                        <th style="font-weight: 600; color: #374151;">Email</th>
                                        <th style="font-weight: 600; color: #374151;">Rol</th>
                                        <th style="font-weight: 600; color: #374151;">Fecha de Registro</th>
                                        <th style="font-weight: 600; color: #374151;">Cursos</th>
                                        <th style="font-weight: 600; color: #374151;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                    <tr class="user-row" style="transition: all 0.2s;">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; font-size: 14px; font-weight: 600; background: linear-gradient(135deg, #0B5E2E, #0d7a3f); color: white;">
                                                    {{ substr($user->primer_nombre ?? 'U', 0, 1) }}{{ substr($user->primer_apellido ?? '', 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong style="color: #1f2937;">{{ $user->primer_nombre }} {{ $user->primer_apellido }}</strong>
                                                    @if($user->segundo_nombre || $user->segundo_apellido)
                                                        <br><small class="text-muted">{{ $user->segundo_nombre }} {{ $user->segundo_apellido }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td style="color: #4b5563;">{{ $user->email }}</td>
                                        <td>
                                            @if($user->role === 'admin_global')
                                                <span class="badge" style="background: #fef3c7; color: #d97706; padding: 4px 10px;">Admin Global</span>
                                            @elseif($user->role === 'admin')
                                                <span class="badge" style="background: #fee2e2; color: #dc2626; padding: 4px 10px;">Administrador</span>
                                            @elseif($user->role === 'docente')
                                                <span class="badge" style="background: #dcfce7; color: #16a34a; padding: 4px 10px;">Docente</span>
                                            @else
                                                <span class="badge" style="background: #f3f4f6; color: #6b7280; padding: 4px 10px;">Estudiante</span>
                                            @endif
                                        </td>
                                        <td style="color: #6b7280; font-size: 13px;">{{ $user->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            @php
                                                $cursosCount = $user->progresos->count();
                                            @endphp
                                            <span class="badge" style="background: #dbeafe; color: #1d4ed8; padding: 4px 10px;">{{ $cursosCount }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-primary" title="Ver usuario" style="border-radius: 6px;">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($user->id !== auth()->id())
                                                    <button type="button" class="btn btn-outline-danger btn-delete" title="Eliminar usuario" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" style="border-radius: 6px;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div style="padding: 40px;">
                                                <div style="width: 80px; height: 80px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                                                    <i class="fas fa-users" style="font-size: 36px; color: #9ca3af;"></i>
                                                </div>
                                                <h5 style="color: #1f2937; margin-bottom: 8px;">No hay usuarios registrados</h5>
                                                <p style="color: #6b7280;">Los usuarios aparecerán aquí cuando se registren.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($users->hasPages())
                        <div class="d-flex justify-content-center mt-4" id="paginationLinks">
                            {{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--radius-lg); border: none;">
            <div class="modal-header" style="background: #fee2e2; border-bottom: none;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 48px; height: 48px; background: #dc2626; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-exclamation-triangle" style="color: white; font-size: 20px;"></i>
                    </div>
                    <h5 class="modal-title" style="color: #dc2626;">Confirmar eliminación</h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 24px;">&times;</button>
            </div>
            <div class="modal-body text-center py-4">
                <p style="font-size: 16px; color: #374151;">¿Estás seguro de eliminar al usuario <strong id="deleteUserName"></strong>?</p>
                <p style="color: #6b7280; font-size: 14px;">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer justify-content-center" style="border-top: 1px solid #e5e7eb; padding: 16px;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 10px 24px;">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="background: #dc2626; border-color: #dc2626; border-radius: 8px; padding: 10px 24px;">
                        <i class="fas fa-trash me-1"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .user-row:hover {
        background-color: #f9fafb !important;
    }

    .user-row td {
        vertical-align: middle;
    }

    .btn-outline-primary {
        color: #0B5E2E;
        border-color: #0B5E2E;
    }

    .btn-outline-primary:hover {
        background-color: #0B5E2E;
        border-color: #0B5E2E;
        color: white;
    }

    .btn-outline-danger:hover {
        background-color: #dc2626;
        border-color: #dc2626;
        color: white;
    }

    .btn-group-sm .btn {
        padding: 6px 10px;
    }

    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: skeleton-loading 1.5s infinite;
        border-radius: 4px;
    }

    .skeleton-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .skeleton-title {
        height: 16px;
        width: 120px;
        margin-bottom: 8px;
    }

    .skeleton-text {
        height: 12px;
        width: 80%;
    }

    @keyframes skeleton-loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .count-number {
        font-weight: 600;
        color: var(--verde-institucional);
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--verde-institucional);
        box-shadow: 0 0 0 3px rgba(11, 94, 46, 0.15);
    }

    .pagination {
        margin: 0;
    }

    .pagination .page-link {
        color: #0B5E2E;
        border-radius: 8px;
        margin: 0 2px;
    }

    .pagination .page-item.active .page-link {
        background-color: #0B5E2E;
        border-color: #0B5E2E;
    }

    .pagination .page-link:hover {
        background-color: #f0fdf4;
        border-color: #0B5E2E;
        color: #0B5E2E;
    }

    .search-input-wrapper {
        position: relative;
    }

    .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 14px;
        pointer-events: none;
        z-index: 1;
    }

    .search-input-wrapper .form-control:focus {
        border-color: var(--verde-institucional);
    }

    .search-input-wrapper .form-control:focus ~ .search-icon {
        color: var(--verde-institucional);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const roleSelect = document.getElementById('roleSelect');
    const sortSelect = document.getElementById('sortSelect');
    const clearBtn = document.getElementById('clearBtn');
    const usersTable = document.getElementById('usersTable');
    const loadingState = document.getElementById('loadingState');
    const usersContent = document.getElementById('usersContent');
    const resultsCount = document.getElementById('resultsCount');
    const paginationLinks = document.getElementById('paginationLinks');
    const deleteModal = document.getElementById('deleteModal');

    let timeout = null;

    function fetchUsers(page = 1) {
        if (timeout) clearTimeout(timeout);

        timeout = setTimeout(function() {
            const params = new URLSearchParams();
            if (searchInput.value) params.append('search', searchInput.value);
            if (roleSelect.value) params.append('role', roleSelect.value);
            if (sortSelect.value) params.append('sort', sortSelect.value);
            params.append('page', page);

            loadingState.style.display = 'block';
            usersContent.style.opacity = '0.5';

            fetch('{{ route('admin.users.index') }}?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newContent = doc.getElementById('usersContent');
                const newCount = doc.getElementById('resultsCount');

                if (newContent) usersContent.innerHTML = newContent.innerHTML;
                if (newCount) resultsCount.innerHTML = newCount.innerHTML;

                loadingState.style.display = 'none';
                usersContent.style.opacity = '1';

                history.pushState({}, '', '{{ route('admin.users.index') }}?' + params.toString());
                initDeleteButtons();
            })
            .catch(error => {
                loadingState.style.display = 'none';
                usersContent.style.opacity = '1';
                showToast('error', 'Error', 'No se pudieron cargar los usuarios');
            });
        }, 300);
    }

    function initDeleteButtons() {
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                document.getElementById('deleteUserName').textContent = userName;
                document.getElementById('deleteForm').action = '/admin/usuarios/' + userId;
                $('#deleteModal').modal('show');
            });
        });
    }

    // Init on page load
    initDeleteButtons();

    if (searchInput) searchInput.addEventListener('input', () => fetchUsers());
    if (roleSelect) roleSelect.addEventListener('change', () => fetchUsers());
    if (sortSelect) sortSelect.addEventListener('change', () => fetchUsers());

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            roleSelect.value = '';
            sortSelect.value = 'latest';
            fetchUsers();
        });
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('#paginationLinks a')) {
            e.preventDefault();
            const url = e.target.closest('a').href;
            const page = new URL(url).searchParams.get('page');
            fetchUsers(page);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    document.addEventListener('submit', function(e) {
        if (e.target.id === 'deleteForm') {
            $('#deleteModal').modal('hide');
        }
    });

    initDeleteButtons();
});
</script>
@endsection
