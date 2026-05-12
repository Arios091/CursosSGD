@extends('layouts.app')

@section('page-title', $isAdmin ? 'Gestión de Cursos' : 'Mis Cursos')

@section('breadcrumbs')
    <span><i class="fas fa-chevron-right"></i></span>
    <span style="color: var(--gris-800);">{{ $isAdmin ? 'Gestión de Cursos' : 'Mis Cursos' }}</span>
@endsection

@section('content')
@php
    $user = auth()->user();
    $isAdmin = $user->isAdmin();
@endphp

@if($isAdmin)
<div style="margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 24px; font-weight: 700; color: #1f2937; margin-bottom: 4px;">Gestión de Cursos</h2>
            <p style="color: #6b7280; margin: 0;">Administra todos los cursos del sistema</p>
        </div>
        <a href="{{ route('crear.curso') }}" class="btn" style="background: var(--verde-institucional); color: white; padding: 12px 24px; border-radius: 8px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Crear Curso
        </a>
    </div>
</div>

<div style="background: white; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: var(--shadow-sm);">
    <div class="row g-3 align-items-end">
        <div class="col-12 col-md-4">
            <label style="font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Buscar</label>
            <div class="search-input-wrapper mt-1">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" class="form-control" placeholder="Nombre del curso..." value="{{ request('search') }}" style="height: 44px; border-radius: 8px; padding-left: 40px;">
            </div>
        </div>
        <div class="col-6 col-md-2">
            <label style="font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Estado</label>
            <select id="estadoSelect" class="form-select mt-1" style="height: 44px; border-radius: 8px;">
                <option value="">Todos</option>
                <option value="con_contenido" {{ request('estado') == 'con_contenido' ? 'selected' : '' }}>Con contenido</option>
                <option value="sin_contenido" {{ request('estado') == 'sin_contenido' ? 'selected' : '' }}>Sin contenido</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label style="font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Ordenar</label>
            <select id="sortSelect" class="form-select mt-1" style="height: 44px; border-radius: 8px;">
                <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Más recientes</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Más antiguos</option>
                <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Por título (A-Z)</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <button type="button" id="clearBtn" class="btn btn-outline-secondary w-100 mt-1" style="height: 44px; border-radius: 8px;">
                <i class="fas fa-times me-1"></i> Limpiar
            </button>
        </div>
        <div class="col-6 col-md-2">
            <div class="text-muted mt-1" id="resultsCount" style="font-size: 13px;">
                <i class="fas fa-book me-1"></i> <span class="count-number">{{ count($cursos) }}</span> cursos
            </div>
        </div>
    </div>
</div>

<div id="cursosContainer">
    <div id="loadingState" style="display: none;">
        <div style="display: grid; gap: 16px;">
            @for($i = 0; $i < 4; $i++)
            <div class="skeleton-card" style="display: flex; justify-content: space-between; align-items: center; padding: 20px; background: white; border-radius: 12px; box-shadow: var(--shadow-md);">
                <div style="display: flex; align-items: center; gap: 16px; flex: 1;">
                    <div class="skeleton" style="width: 80px; height: 60px;"></div>
                    <div style="flex: 1;">
                        <div class="skeleton skeleton-title" style="height: 20px; width: 40%;"></div>
                        <div class="skeleton skeleton-text" style="width: 80%;"></div>
                        <div class="skeleton skeleton-text" style="width: 60%; height: 12px;"></div>
                    </div>
                </div>
                <div style="display: flex; gap: 8px;">
                    <div class="skeleton" style="width: 80px; height: 32px; border-radius: 6px;"></div>
                    <div class="skeleton" style="width: 80px; height: 32px; border-radius: 6px;"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    <div id="cursosContent">
        @if($cursos->count() > 0)
        <p style="color: #6b7280; margin-bottom: 16px;">{{ count($cursos) }} cursos encontrados</p>
        <div style="display: grid; gap: 16px;">
            @foreach($cursos as $curso)
            <div class="course-item" style="display: flex; justify-content: space-between; align-items: center; padding: 20px; background: white; border-radius: 12px; box-shadow: var(--shadow-md); border: 1px solid #e5e7eb; transition: all 0.3s;">
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        @if($curso->imagen_referencial)
                            <img src="{{ asset('storage/'.$curso->imagen_referencial) }}" style="width: 80px; height: 60px; object-fit: cover; border-radius: 8px;">
                        @else
                            <div style="width: 80px; height: 60px; background: linear-gradient(135deg, var(--verde-institucional) 0%, #0d7a3f 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-book" style="color: white; font-size: 24px;"></i>
                            </div>
                        @endif
                        <div>
                            <div style="font-weight: 600; font-size: 16px; color: #1f2937;">{{ $curso->titulo }}</div>
                            <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">{{ $curso->descripcion ? Str::limit($curso->descripcion, 80) : 'Sin descripción' }}</div>
                            <div style="margin-top: 8px; display: flex; gap: 8px; flex-wrap: wrap;">
                                <span style="background: #dbeafe; color: #1d4ed8; padding: 2px 8px; border-radius: 4px; font-size: 12px;">{{ $curso->carga_horaria }} horas</span>
                                <span style="background: #f3f4f6; color: #6b7280; padding: 2px 8px; border-radius: 4px; font-size: 12px;">{{ $curso->modulos->count() }} módulos</span>
                                <span style="background: #fce7f3; color: #be185d; padding: 2px 8px; border-radius: 4px; font-size: 12px;">
                                    <i class="fas fa-users me-1"></i>{{ $curso->progresos->count() }} inscritos
                                </span>
                                <span style="background: {{ $curso->estado === 'publicado' ? '#dcfce7' : '#fef3c7' }}; color: {{ $curso->estado === 'publicado' ? '#166534' : '#92400e' }}; padding: 2px 8px; border-radius: 4px; font-size: 12px;">{{ $curso->estado }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="display: flex; gap: 8px; flex-shrink: 0; margin-left: 16px;">
                    <a href="{{ route('cursos.edit', $curso) }}" class="btn btn-sm" style="background: var(--dorado); color: white;">
                        <i class="fas fa-edit"></i> <span class="d-none d-md-inline">Editar</span>
                    </a>
                    <button type="button" class="btn btn-sm" style="background: #dc2626; color: white;" data-toggle="modal" data-target="#deleteModal" data-curso-id="{{ $curso->id }}" data-curso-titulo="{{ $curso->titulo }}">
                        <i class="fas fa-trash"></i> <span class="d-none d-md-inline">Eliminar</span>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div style="background: white; border-radius: 12px; padding: 60px; text-align: center; box-shadow: var(--shadow-md);">
            <div style="width: 80px; height: 80px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i class="fas fa-book" style="font-size: 36px; color: #9ca3af;"></i>
            </div>
            <h4 style="color: #1f2937; margin-bottom: 8px;">No hay cursos creados</h4>
            <p style="color: #6b7280; margin-bottom: 20px;">Crea el primer curso para comenzar.</p>
            <a href="{{ route('crear.curso') }}" class="btn" style="background: var(--verde-institucional); color: white;">
                <i class="fas fa-plus me-2"></i>Crear curso
            </a>
        </div>
        @endif
    </div>
</div>

@else
@php
    $cursosInscritos = \App\Models\ProgresoCurso::where('user_id', $user->id)
        ->with('curso')
        ->get();

    $cursosCompletados = $cursosInscritos->where('estado', 'completado');
    $cursosEnProgreso = $cursosInscritos->where('estado', 'en_progreso');
@endphp

@if($cursosEnProgreso->count() > 0)
<div style="margin-bottom: 40px;">
    <h3 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-spinner" style="color: #3b82f6;"></i> Cursos en Progreso
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
        @foreach($cursosEnProgreso as $progreso)
            @if($progreso->curso)
            <div class="course-card" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-md); transition: all 0.3s;">
                @if($progreso->curso->imagen_referencial)
                    <img src="{{ asset('storage/'.$progreso->curso->imagen_referencial) }}" style="width: 100%; height: 140px; object-fit: cover;">
                @else
                    <div style="width: 100%; height: 140px; background: linear-gradient(135deg, var(--verde-institucional) 0%, #0d7a3f 100%); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-book" style="color: white; font-size: 40px;"></i>
                    </div>
                @endif
                <div style="padding: 16px;">
                    <h4 style="font-size: 15px; font-weight: 600; color: #1f2937; margin-bottom: 8px;">{{ $progreso->curso->titulo }}</h4>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #6b7280;">
                        <span><i class="fas fa-clock"></i> {{ $progreso->curso->carga_horaria }} horas</span>
                        <span style="color: #3b82f6;"><i class="fas fa-spinner"></i> En progreso</span>
                    </div>
                    <a href="{{ route('cursos.ver', $progreso->curso) }}" style="display: block; margin-top: 12px; background: var(--verde-institucional); color: white; text-align: center; padding: 10px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s;" class="btn-continue">
                        <i class="fas fa-play"></i> Continuar
                    </a>
                </div>
            </div>
            @endif
        @endforeach
    </div>
</div>
@endif

@if($cursosCompletados->count() > 0)
<div style="margin-bottom: 40px;">
    <h3 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-check-circle" style="color: #16a34a;"></i> Cursos Completados
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
        @foreach($cursosCompletados as $progreso)
            @if($progreso->curso)
            <div class="course-card" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-md); transition: all 0.3s;">
                @if($progreso->curso->imagen_referencial)
                    <img src="{{ asset('storage/'.$progreso->curso->imagen_referencial) }}" style="width: 100%; height: 140px; object-fit: cover;">
                @else
                    <div style="width: 100%; height: 140px; background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-trophy" style="color: white; font-size: 40px;"></i>
                    </div>
                @endif
                <div style="padding: 16px;">
                    <h4 style="font-size: 15px; font-weight: 600; color: #1f2937; margin-bottom: 8px;">{{ $progreso->curso->titulo }}</h4>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #6b7280;">
                        <span><i class="fas fa-clock"></i> {{ $progreso->curso->carga_horaria }} horas</span>
                        <span><i class="fas fa-check-circle" style="color: #16a34a;"></i> Completado</span>
                    </div>
                    <a href="{{ route('certificado', $progreso->curso) }}" style="display: block; margin-top: 12px; background: var(--dorado); color: white; text-align: center; padding: 10px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s;">
                        <i class="fas fa-certificate"></i> Ver Certificado
                    </a>
                </div>
            </div>
            @endif
        @endforeach
    </div>
</div>
@endif

@if($cursosCompletados->count() == 0 && $cursosEnProgreso->count() == 0)
<div style="background: white; border-radius: 12px; padding: 60px; text-align: center; box-shadow: var(--shadow-md);">
    <div style="width: 80px; height: 80px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
        <i class="fas fa-book-open" style="color: #9ca3af; font-size: 36px;"></i>
    </div>
    <h4 style="color: #1f2937; margin-bottom: 8px;">No tienes cursos inscritos</h4>
    <p style="color: #6b7280;">Explora los cursos disponibles en la página de inicio para inscribirte.</p>
    <a href="{{ route('home') }}" class="btn" style="background: var(--verde-institucional); color: white; margin-top: 16px;">
        <i class="fas fa-home me-2"></i>Ir al inicio
    </a>
</div>
@endif
@endif

<style>
    .course-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        border-color: var(--verde-institucional);
    }

    .course-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.12);
    }

    .btn-continue:hover {
        background: #094D25 !important;
        transform: scale(1.02);
    }

    .skeleton-card {
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--verde-institucional);
        box-shadow: 0 0 0 3px rgba(11, 94, 46, 0.15);
    }

    #loadingState .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: skeleton-loading 1.5s infinite;
    }

    @keyframes skeleton-loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .count-number {
        font-weight: 600;
        color: var(--verde-institucional);
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

    .search-input-wrapper .form-control:focus + .search-icon,
    .search-input-wrapper .form-control:focus ~ .search-icon {
        color: var(--verde-institucional);
    }
</style>

{{-- Modal de confirmación para eliminar curso --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom: 1px solid #e5e7eb; padding: 20px 24px;">
                <h5 class="modal-title" id="deleteModalLabel" style="font-weight: 600; color: #1f2937;">
                    <i class="fas fa-exclamation-triangle" style="color: #dc2626; margin-right: 8px;"></i>
                    Confirmar eliminación
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <p style="color: #374151; font-size: 15px; margin-bottom: 8px;">
                    ¿Estás seguro de eliminar el curso <strong id="deleteCursoTitulo"></strong>?
                </p>
                <p style="color: #6b7280; font-size: 13px;">
                    Esta acción eliminará todos los módulos, materiales, cuestionarios y progresos asociados. No se puede deshacer.
                </p>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 16px 24px;">
                <button type="button" class="btn" style="background: #f3f4f6; color: #374151; padding: 8px 20px; border-radius: 8px;" data-dismiss="modal">Cancelar</button>
                <form id="deleteForm" action="" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn" style="background: #dc2626; color: white; padding: 8px 20px; border-radius: 8px;">
                        <i class="fas fa-trash"></i> Eliminar curso
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');

    // Modal de confirmación de eliminación
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var cursoId = button.data('curso-id');
        var cursoTitulo = button.data('curso-titulo');
        $(this).find('#deleteCursoTitulo').text(cursoTitulo);
        $(this).find('#deleteForm').attr('action', '/cursos/' + cursoId);
    });
    const estadoSelect = document.getElementById('estadoSelect');
    const sortSelect = document.getElementById('sortSelect');
    const clearBtn = document.getElementById('clearBtn');
    const cursosContainer = document.getElementById('cursosContainer');
    const loadingState = document.getElementById('loadingState');
    const cursosContent = document.getElementById('cursosContent');
    const resultsCount = document.getElementById('resultsCount');

    let timeout = null;

    function fetchCursos() {
        if (timeout) clearTimeout(timeout);

        timeout = setTimeout(function() {
            const params = new URLSearchParams();
            if (searchInput.value) params.append('search', searchInput.value);
            if (estadoSelect.value) params.append('estado', estadoSelect.value);
            if (sortSelect.value) params.append('sort', sortSelect.value);

            loadingState.style.display = 'block';
            cursosContent.style.opacity = '0.5';

            fetch('{{ route('cursos.index') }}?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newContent = doc.getElementById('cursosContent');
                const newCount = doc.getElementById('resultsCount');

                if (newContent) {
                    cursosContent.innerHTML = newContent.innerHTML;
                    initCardEffects();
                }
                if (newCount) resultsCount.innerHTML = newCount.innerHTML;

                loadingState.style.display = 'none';
                cursosContent.style.opacity = '1';

                history.pushState({}, '', '{{ route('cursos.index') }}?' + params.toString());
            })
            .catch(error => {
                loadingState.style.display = 'none';
                cursosContent.style.opacity = '1';
                showToast('error', 'Error', 'No se pudieron cargar los cursos');
            });
        }, 300);
    }

    function initCardEffects() {
        document.querySelectorAll('.course-item').forEach(item => {
            item.addEventListener('mouseenter', () => item.style.transform = 'translateY(-2px)');
            item.addEventListener('mouseleave', () => item.style.transform = 'translateY(0)');
        });
    }

    if (searchInput) searchInput.addEventListener('input', () => fetchCursos());
    if (estadoSelect) estadoSelect.addEventListener('change', () => fetchCursos());
    if (sortSelect) sortSelect.addEventListener('change', () => fetchCursos());

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            estadoSelect.value = '';
            sortSelect.value = 'latest';
            fetchCursos();
        });
    }

    initCardEffects();
});
</script>
@endsection
