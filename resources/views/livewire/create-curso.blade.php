<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h2 class="mb-4 text-center text-verde">
                <i class="fas fa-plus-circle me-2"></i>Crear Nuevo Curso
            </h2>

            <div class="progress mb-4" style="height: 30px; border-radius: 15px; background: #E5E7EB;">
                <div class="progress-bar" role="progressbar" 
                     style="width: {{ $step == 1 ? '33%' : ($step == 2 ? '66%' : '100'); }}; background: linear-gradient(135deg, #0B5E2E 0%, #0B5E2E 100%);" 
                     aria-valuenow="{{ $step }}" aria-valuemin="1" aria-valuemax="3">
                    <strong class="text-white">Paso {{ $step }} de 3</strong>
                </div>
            </div>

            <ul class="nav nav-pills mb-4 justify-content-center">
                <li class="nav-item mx-2">
                    <span class="nav-link {{ $step == 1 ? 'active' : '' }}" 
                          style="background: {{ $step == 1 ? '#0B5E2E' : '#E5E7EB' }}; color: {{ $step == 1 ? 'white' : '#4B5563' }}; border-radius: 20px;">
                        <i class="fas fa-info-circle me-1"></i> Datos Básicos
                    </span>
                </li>
                <li class="nav-item mx-2">
                    <span class="nav-link {{ $step == 2 ? 'active' : '' }}" 
                          style="background: {{ $step == 2 ? '#0B5E2E' : '#E5E7EB' }}; color: {{ $step == 2 ? 'white' : '#4B5563' }}; border-radius: 20px;">
                        <i class="fas fa-layer-group me-1"></i> Módulos
                    </span>
                </li>
                <li class="nav-item mx-2">
                    <span class="nav-link {{ $step == 3 ? 'active' : '' }}" 
                          style="background: {{ $step == 3 ? '#C9A227' : '#E5E7EB' }}; color: {{ $step == 3 ? 'white' : '#4B5563' }}; border-radius: 20px;">
                        <i class="fas fa-clipboard-check me-1"></i> Evaluación
                    </span>
                </li>
            </ul>

            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-dismiss="alert"></button>
                </div>
            @endif

            @if ($step == 1)
                <div class="card shadow-sm border-0">
                    <div class="card-header text-white" style="background: linear-gradient(135deg, #0B5E2E 0%, #0B5E2E 100%);">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Paso 1: Datos Básicos del Curso</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark">Título del Curso <span class="text-danger">*</span></label>
                                <input type="text" wire:model="titulo" class="form-control" 
                                       placeholder="Ej: Introducción a la Programación en Python" />
                                @error('titulo') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-dark">Descripción del Curso</label>
                                <textarea wire:model="descripcion" class="form-control" rows="4" 
                                          placeholder="Describe el contenido, objetivos y a quién está dirigido el curso..."></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Carga Horaria (horas) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" wire:model="carga_horaria" class="form-control" 
                                           min="1" max="500" placeholder="Ej: 40" />
                                    <span class="input-group-text">horas</span>
                                </div>
                                @error('carga_horaria') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Imagen Referencial</label>
                                <input type="file" wire:model="imagen_referencial" class="form-control" accept="image/*" />
                                <small class="text-muted">Formato: JPG, PNG. Máximo: 10MB</small>
                                @if ($imagen_referencial)
                                    <div class="mt-2">
                                        <img src="{{ $imagen_referencial->temporaryUrl() }}" alt="Preview" class="img-thumbnail" style="max-height: 150px;">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Cancelar
                            </a>
                            <button wire:click="siguiente" class="btn btn-primary px-4">
                                Siguiente <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

            @elseif ($step == 2)
                <div class="card shadow-sm border-0">
                    <div class="card-header text-white" style="background: linear-gradient(135deg, #0B5E2E 0%, #0B5E2E 100%);">
                        <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Paso 2: Módulos del Curso</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">
                            Define los módulos del curso. Cada módulo debe tener al menos un material (PDF o video).
                        </p>

                        @foreach ($modulos as $modIndex => $modulo)
                            <div class="card mb-4 border" style="border: 1px solid #E5E7EB;">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 text-dark">Módulo {{ $modIndex + 1 }}</h5>
                                        @if (count($modulos) > 1)
                                            <button type="button" wire:click="eliminarModulo({{ $modIndex }})" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-dark">Título del Módulo</label>
                                        <input type="text" wire:model="modulos.{{ $modIndex }}.titulo" class="form-control" 
                                               placeholder="Ej: Introducción" />
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-dark">Materiales</label>
                                        <small class="text-muted d-block mb-2">
                                            <i class="fas fa-info-circle me-1"></i> 
                                            Para PDF: selecciona un archivo. Para Video: pega una URL (YouTube, Vimeo, etc.)
                                        </small>
                                        @foreach ($modulo['materiales'] as $matIndex => $material)
                                            <div class="row g-2 mb-2 align-items-end">
                                                <div class="col-md-4">
                                                    <input type="text" wire:model="modulos.{{ $modIndex }}.materiales.{{ $matIndex }}.titulo" 
                                                           class="form-control form-control-sm" placeholder="Título del material" />
                                                </div>
                                                <div class="col-md-2">
                                                    <select wire:model="modulos.{{ $modIndex }}.materiales.{{ $matIndex }}.tipo" 
                                                            wire:change="actualizarTipoMaterial({{ $modIndex }}, {{ $matIndex }}, $event.target.value)"
                                                            class="form-select form-select-sm">
                                                        <option value="pdf">PDF (archivo)</option>
                                                        <option value="video">Video (URL)</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" wire:model="modulos.{{ $modIndex }}.materiales.{{ $matIndex }}.url" 
                                                           class="form-control form-control-sm {{ $material['tipo'] !== 'video' ? 'd-none' : '' }}" 
                                                           placeholder="https://youtube.com/watch?v=..." 
                                                           style="{{ $material['tipo'] !== 'video' ? 'display:none;' : '' }}" />
                                                    <input type="file" wire:model="modulos.{{ $modIndex }}.materiales.{{ $matIndex }}.archivo" 
                                                           class="form-control form-control-sm {{ $material['tipo'] === 'video' ? 'd-none' : '' }}"
                                                           accept=".pdf"
                                                           style="{{ $material['tipo'] === 'video' ? 'display:none;' : '' }}" />
                                                </div>
                                                <div class="col-md-2">
                                                    @if (count($modulo['materiales']) > 1)
                                                        <button type="button" wire:click="eliminarMaterial({{ $modIndex }}, {{ $matIndex }})" 
                                                                class="btn btn-sm btn-outline-danger w-100">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                        <button type="button" wire:click="agregarMaterial({{ $modIndex }})" class="btn btn-sm btn-success">
                                            <i class="fas fa-plus me-1"></i> Agregar Material
                                        </button>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-dark">Cuestionario del Módulo</label>
                                        @foreach ($modulo['cuestionario']['preguntas'] as $pIdx => $pregunta)
                                            <div class="card bg-light mb-3">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <span class="badge" style="background: #0B5E2E;">Pregunta {{ $pIdx + 1 }}</span>
                                                        <button type="button" wire:click="eliminarPreguntaCuestionario({{ $modIndex }}, {{ $pIdx }})" 
                                                                class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    <input type="text" wire:model="modulos.{{ $modIndex }}.cuestionario.preguntas.{{ $pIdx }}.texto" 
                                                           class="form-control mb-3" placeholder="Escribe la pregunta..." />
                                                    @foreach ($pregunta['opciones'] as $oIdx => $opcion)
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-text">
                                                                <input type="radio" 
                                                                       wire:click="setCorrectaCuestionario({{ $modIndex }}, {{ $pIdx }}, {{ $oIdx }})"
                                                                       {{ $opcion['es_correcta'] ? 'checked' : '' }}>
                                                            </div>
                                                            <input type="text" wire:model="modulos.{{ $modIndex }}.cuestionario.preguntas.{{ $pIdx }}.opciones.{{ $oIdx }}.texto" 
                                                                   class="form-control" placeholder="Opción {{ $oIdx + 1 }}">
                                                            @if (count($pregunta['opciones']) > 2)
                                                                <button type="button" wire:click="eliminarOpcionCuestionario({{ $modIndex }}, {{ $pIdx }}, {{ $oIdx }})" 
                                                                        class="btn btn-outline-danger">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                    <button type="button" wire:click="agregarOpcionCuestionario({{ $modIndex }}, {{ $pIdx }})" 
                                                            class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-plus me-1"></i> Opción
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                        <button type="button" wire:click="agregarPreguntaCuestionario({{ $modIndex }})" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i> Agregar Pregunta
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <button type="button" wire:click="agregarModulo" class="btn btn-success mb-4">
                            <i class="fas fa-plus-circle me-1"></i> Agregar Nuevo Módulo
                        </button>

                        <div class="d-flex justify-content-between">
                            <button wire:click="anterior" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-arrow-left me-1"></i> Atrás
                            </button>
                            <button wire:click="siguiente" class="btn btn-primary px-4">
                                Siguiente <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

            @elseif ($step == 3)
                <div class="card shadow-sm border-0">
                    <div class="card-header text-white" style="background: linear-gradient(135deg, #C9A227 0%, #C9A227 100%);">
                        <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Paso 3: Evaluación Final</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Título de la Evaluación</label>
                            <input type="text" wire:model="evaluacion_final_titulo" class="form-control" />
                        </div>

                        <label class="form-label fw-bold text-dark">Preguntas de la Evaluación Final</label>
                        @foreach ($evaluacion_final_preguntas as $pIdx => $pregunta)
                            <div class="card mb-3" style="background: #F0FDF4; border: 1px solid #BBF7D0;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-success">Pregunta {{ $pIdx + 1 }}</span>
                                        @if (count($evaluacion_final_preguntas) > 1)
                                            <button type="button" wire:click="eliminarPreguntaEvaluacion({{ $pIdx }})" 
                                                    class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <input type="text" wire:model="evaluacion_final_preguntas.{{ $pIdx }}.texto" 
                                           class="form-control mb-3" placeholder="Escribe la pregunta..." />
                                    @foreach ($pregunta['opciones'] as $oIdx => $opcion)
                                        <div class="input-group mb-2">
                                            <div class="input-group-text">
                                                <input type="radio" 
                                                       wire:click="setCorrectaEvaluacion({{ $pIdx }}, {{ $oIdx }})"
                                                       {{ $opcion['es_correcta'] ? 'checked' : '' }}>
                                            </div>
                                            <input type="text" wire:model="evaluacion_final_preguntas.{{ $pIdx }}.opciones.{{ $oIdx }}.texto" 
                                                   class="form-control" placeholder="Opción {{ $oIdx + 1 }}">
                                            @if (count($pregunta['opciones']) > 2)
                                                <button type="button" wire:click="eliminarOpcionEvaluacion({{ $pIdx }}, {{ $oIdx }})" 
                                                        class="btn btn-outline-danger">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                    <button type="button" wire:click="agregarOpcionEvaluacion({{ $pIdx }})" 
                                            class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-plus me-1"></i> Opción
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        <button type="button" wire:click="agregarPreguntaEvaluacion" class="btn btn-success">
                            <i class="fas fa-plus me-1"></i> Agregar Pregunta
                        </button>

                        <hr class="my-4">

                        <div class="card mb-4" style="background: #F9FAFB; border: 1px solid #E5E7EB;">
                            <div class="card-body">
                                <h5><i class="fas fa-list-check me-2 text-verde"></i>Resumen</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li><strong class="text-dark">Título:</strong> <span class="text-muted">{{ $titulo ?: 'Sin título' }}</span></li>
                                            <li><strong class="text-dark">Carga horaria:</strong> <span class="text-muted">{{ $carga_horaria }} horas</span></li>
                                            <li><strong class="text-dark">Módulos:</strong> <span class="text-muted">{{ count($modulos) }}</span></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li><strong class="text-dark">Preguntas evaluación:</strong> <span class="text-muted">{{ count($evaluacion_final_preguntas) }}</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button wire:click="anterior" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i> Atrás
                            </button>
                            
                            @if(!$this->puedeCrear())
                                <button type="button" class="btn btn-secondary btn-lg px-5 shadow" disabled>
                                    <i class="fas fa-graduation-cap me-2"></i> Complete los requisitos
                                </button>
                                <div class="text-center mt-2">
                                    <small class="text-danger">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        Requiere: título, módulo con material (PDF o video con URL), y al menos 1 pregunta en evaluación final
                                    </small>
                                </div>
                            @else
                                <button wire:click="guardar" class="btn btn-success btn-lg px-5 shadow">
                                    <i class="fas fa-graduation-cap me-2"></i> Crear Curso
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
