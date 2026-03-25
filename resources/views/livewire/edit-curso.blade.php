<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Editar Curso</h2>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>

            <div class="progress mb-4" style="height: 25px; border-radius: 15px;">
                @php
                    $progressWidth = $step == 1 ? '33%' : ($step == 2 ? '66%' : '100%');
                @endphp
                <div class="progress-bar bg-primary" role="progressbar" 
                     style="width: {{ $progressWidth }};" 
                     aria-valuenow="{{ $step }}" aria-valuemin="1" aria-valuemax="3">
                    Paso {{ $step }} de 3
                </div>
            </div>

            <ul class="nav nav-pills mb-4 justify-content-center">
                <li class="nav-item mx-2">
                    <span class="nav-link {{ $step == 1 ? 'active bg-primary' : 'text-secondary' }}">
                        <i class="fas fa-book me-1"></i> Datos Básicos
                    </span>
                </li>
                <li class="nav-item mx-2">
                    <span class="nav-link {{ $step == 2 ? 'active bg-info' : 'text-secondary' }}">
                        <i class="fas fa-layer-group me-1"></i> Módulos
                    </span>
                </li>
                <li class="nav-item mx-2">
                    <span class="nav-link {{ $step == 3 ? 'active bg-success' : 'text-secondary' }}">
                        <i class="fas fa-clipboard-check me-1"></i> Evaluación Final
                    </span>
                </li>
            </ul>

            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-dismiss="alert"></button>
                </div>
            @endif

            @if ($step == 1)
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Paso 1: Datos Básicos del Curso</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label fw-bold">Título del Curso <span class="text-danger">*</span></label>
                                <input type="text" wire:model="titulo" class="form-control @error('titulo') is-invalid @enderror" 
                                       placeholder="Ej: Introducción a la Programación en Python" />
                                @error('titulo') 
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Descripción del Curso</label>
                                <textarea wire:model="descripcion" class="form-control" rows="4" 
                                          placeholder="Describe el contenido, objetivos y a quién está dirigido el curso..."></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Carga Horaria (horas) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" wire:model="carga_horaria" class="form-control @error('carga_horaria') is-invalid @enderror" 
                                           min="1" max="500" placeholder="Ej: 40" />
                                    <span class="input-group-text">horas</span>
                                </div>
                                @error('carga_horaria') 
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Imagen Referencial</label>
                                <input type="file" wire:model="imagen_referencial" class="form-control @error('imagen_referencial') is-invalid @enderror" 
                                       accept="image/*" />
                                <small class="text-muted">Formato: JPG, PNG. Máximo: 5MB</small>
                                @error('imagen_referencial') 
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                @if ($imagen_referencial)
                                    <div class="mt-2">
                                        <img src="{{ $imagen_referencial->temporaryUrl() }}" alt="Preview" class="img-thumbnail" style="max-height: 150px;">
                                    </div>
                                @elseif($imagen_actual)
                                    <div class="mt-2">
                                        <small class="text-muted">Imagen actual:</small>
                                        <img src="{{ asset('storage/' . $imagen_actual) }}" alt="Imagen actual" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Cancelar
                            </a>
                            <button wire:click="nextStep" class="btn btn-primary px-4">
                                Siguiente <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

            @elseif ($step == 2)
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Paso 2: Módulos del Curso</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">
                            Define los módulos del curso. Cada módulo debe tener al menos un material (PDF o video) y un cuestionario de preguntas de opción múltiple.
                        </p>

                        @foreach ($modulos as $index => $modulo)
                            <div class="card mb-4 border-{{ $index % 2 == 0 ? 'primary' : 'info' }} border-opacity-25">
                                <div class="card-header bg-{{ $index % 2 == 0 ? 'primary' : 'info' }} bg-opacity-10">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            Módulo {{ $index + 1 }}
                                            <small class="text-muted">
                                                @if(!empty($modulo['titulo']))
                                                    : {{ $modulo['titulo'] }}
                                                @else
                                                    (Sin título)
                                                @endif
                                            </small>
                                        </h5>
                                        @if (count($modulos) > 1)
                                            <button type="button" wire:click="removeModulo({{ $index }})" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('¿Eliminar este módulo? Se eliminarán todos sus materiales y cuestionarios.')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Título del Módulo</label>
                                        <input type="text" wire:model="modulos.{{ $index }}.titulo" class="form-control" 
                                               placeholder="Ej: Introducción (dejar vacío para 'Módulo {{ $index + 1 }}')" />
                                        @error("modulos.{$index}.titulo")
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="fw-bold text-uppercase text-muted mb-3">
                                            <i class="fas fa-file-alt me-1"></i> Materiales del Módulo
                                        </h6>
                                        <small class="text-muted d-block mb-2">Cada módulo debe tener al menos un material (PDF o video)</small>
                                        
                                        @foreach ($modulo['materiales'] as $mIndex => $material)
                                            <div class="row g-2 mb-2 align-items-end">
                                                <div class="col-md-4">
                                                    <input type="text" wire:model="modulos.{{ $index }}.materiales.{{ $mIndex }}.titulo" 
                                                           class="form-control form-control-sm" placeholder="Título del material" />
                                                </div>
                                                <div class="col-md-2">
                                                    <select wire:model="modulos.{{ $index }}.materiales.{{ $mIndex }}.tipo" class="form-select form-select-sm">
                                                        <option value="pdf">PDF</option>
                                                        <option value="video">Video</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    @if($material['tipo'] === 'video')
                                                        <input type="url" wire:model="modulos.{{ $index }}.materiales.{{ $mIndex }}.url" 
                                                               class="form-control form-control-sm" placeholder="URL del video" />
                                                    @else
                                                        <input type="file" wire:model="modulos.{{ $index }}.materiales.{{ $mIndex }}.archivo" 
                                                               class="form-control form-control-sm" accept=".pdf" />
                                                    @endif
                                                </div>
                                                <div class="col-md-2">
                                                    @if (count($modulo['materiales']) > 1)
                                                        <button type="button" wire:click="removeMaterial({{ $index }}, {{ $mIndex }})" 
                                                                class="btn btn-sm btn-outline-danger w-100">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                            @error("modulos.{$index}.materiales.{$mIndex}.url")
                                                <div class="text-danger small mb-2">{{ $message }}</div>
                                            @enderror
                                        @endforeach

                                        <button type="button" wire:click="addMaterial({{ $index }})" class="btn btn-sm btn-success mt-2">
                                            <i class="fas fa-plus me-1"></i> Agregar Material
                                        </button>
                                        @error("modulos.{$index}.materiales")
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <h6 class="fw-bold text-uppercase text-muted mb-3">
                                            <i class="fas fa-question-circle me-1"></i> Cuestionario del Módulo
                                        </h6>
                                        <small class="text-muted d-block mb-3">
                                            Crea preguntas de opción múltiple. Cada pregunta debe tener al menos 2 opciones y una respuesta correcta marcada.
                                        </small>

                                        @foreach ($modulo['cuestionario']['preguntas'] as $qIndex => $pregunta)
                                            <div class="card bg-light mb-3">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <span class="badge bg-secondary">Pregunta {{ $qIndex + 1 }}</span>
                                                        <button type="button" wire:click="removeQuestion({{ $index }}, {{ $qIndex }})" 
                                                                class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <input type="text" wire:model="modulos.{{ $index }}.cuestionario.preguntas.{{ $qIndex }}.texto" 
                                                           class="form-control mb-3" 
                                                           placeholder="Escribe la pregunta..." />

                                                    <small class="text-muted d-block mb-2">Opciones de respuesta (marca la correcta):</small>
                                                    @foreach ($pregunta['opciones'] as $oIndex => $opcion)
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-text">
                                                                <input type="radio" 
                                                                       wire:click="setCorrectOption({{ $index }}, {{ $qIndex }}, {{ $oIndex }})"
                                                                       {{ $opcion['es_correcta'] ? 'checked' : '' }}
                                                                       title="Marcar como correcta">
                                                            </div>
                                                            <input type="text" wire:model="modulos.{{ $index }}.cuestionario.preguntas.{{ $qIndex }}.opciones.{{ $oIndex }}.texto" 
                                                                   class="form-control" placeholder="Opción {{ $oIndex + 1 }}">
                                                            @if (count($pregunta['opciones']) > 2)
                                                                <button type="button" wire:click="removeOption({{ $index }}, {{ $qIndex }}, {{ $oIndex }})" 
                                                                        class="btn btn-outline-danger">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                    
                                                    <button type="button" wire:click="addOption({{ $index }}, {{ $qIndex }})" 
                                                            class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-plus me-1"></i> Agregar opción
                                                    </button>
                                                    
                                                    @error("modulos.{$index}.cuestionario.preguntas.{$qIndex}")
                                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                                    @enderror
                                                    @error("modulos.{$index}.cuestionario.preguntas.{$qIndex}.correcta")
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endforeach

                                        <button type="button" wire:click="addQuestion({{ $index }})" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i> Agregar Pregunta
                                        </button>
                                        @error("modulos.{$index}.cuestionario")
                                            <div class="text-danger small mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <button type="button" wire:click="addModulo" class="btn btn-success mb-4">
                            <i class="fas fa-plus-circle me-1"></i> Agregar Nuevo Módulo
                        </button>

                        <div class="d-flex justify-content-between">
                            <button wire:click="previousStep" class="btn btn-secondary px-4">
                                <i class="fas fa-arrow-left me-1"></i> Atrás
                            </button>
                            <button wire:click="nextStep" class="btn btn-info px-4">
                                Siguiente <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

            @elseif ($step == 3)
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Paso 3: Evaluación Final y Confirmación</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Evaluación Final:</strong> Esta evaluación se realiza al completar todos los módulos. 
                            El estudiante debe obtener mínimo <strong>80%</strong> de respuestas correctas para aprobar el curso.
                        </div>

                        <div class="mb-4">
                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold">Título de la Evaluación</label>
                                    <input type="text" wire:model="evaluacion_final.titulo" class="form-control" 
                                           placeholder="Evaluación Final del Curso" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Mínimo para Aprobar</label>
                                    <div class="input-group">
                                        <input type="number" wire:model="evaluacion_final.min_aprobacion" class="form-control" 
                                               min="0" max="100" readonly />
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <small class="text-muted">Por defecto: 80%</small>
                                </div>
                            </div>

                            <h6 class="fw-bold text-uppercase text-muted mb-3">
                                <i class="fas fa-list-ol me-1"></i> Preguntas de la Evaluación Final
                            </h6>
                            <small class="text-muted d-block mb-3">
                                Crea preguntas de opción múltiple para la evaluación final del curso.
                            </small>

                            @foreach ($evaluacion_final['preguntas'] as $qIndex => $pregunta)
                                <div class="card bg-success bg-opacity-10 mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-success">Pregunta {{ $qIndex + 1 }}</span>
                                            <button type="button" wire:click="removeEvalQuestion({{ $qIndex }})" 
                                                    class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <input type="text" wire:model="evaluacion_final.preguntas.{{ $qIndex }}.texto" 
                                               class="form-control mb-3" 
                                               placeholder="Escribe la pregunta de la evaluación final..." />

                                        <small class="text-muted d-block mb-2">Opciones de respuesta (marca la correcta):</small>
                                        @foreach ($pregunta['opciones'] as $oIndex => $opcion)
                                            <div class="input-group mb-2">
                                                <div class="input-group-text">
                                                    <input type="radio" 
                                                           wire:click="setEvalCorrectOption({{ $qIndex }}, {{ $oIndex }})"
                                                           {{ $opcion['es_correcta'] ? 'checked' : '' }}
                                                           title="Marcar como correcta">
                                                </div>
                                                <input type="text" wire:model="evaluacion_final.preguntas.{{ $qIndex }}.opciones.{{ $oIndex }}.texto" 
                                                       class="form-control" placeholder="Opción {{ $oIndex + 1 }}">
                                                @if (count($pregunta['opciones']) > 2)
                                                    <button type="button" wire:click="removeEvalOption({{ $qIndex }}, {{ $oIndex }})" 
                                                            class="btn btn-outline-danger">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                        
                                        <button type="button" wire:click="addEvalOption({{ $qIndex }})" 
                                                class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-plus me-1"></i> Agregar opción
                                        </button>
                                    </div>
                                </div>
                            @endforeach

                            <button type="button" wire:click="addEvalQuestion" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i> Agregar Pregunta a la Evaluación
                            </button>
                            @error('evaluacion_final.preguntas')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-list-check me-2"></i>Resumen del Curso</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li><strong>Título:</strong> {{ $titulo }}</li>
                                            <li><strong>Carga horaria:</strong> {{ $carga_horaria }} horas</li>
                                            <li><strong>Descripción:</strong> {{ Str::limit($descripcion, 80) ?: 'No proporcionada' }}</li>
                                            <li><strong>Imagen:</strong> {{ $imagen_referencial ? 'Nueva imagen seleccionada' : ($imagen_actual ? 'Mantener actual' : 'Sin imagen') }}</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li><strong>Módulos:</strong> {{ count($modulos) }}</li>
                                            <li><strong>Total materiales:</strong> 
                                                {{ collect($modulos)->sum(fn($m) => count(array_filter($m['materiales'], fn($mat) => !empty(trim($mat['titulo']))))) }}
                                            </li>
                                            <li><strong>Total preguntas módulos:</strong> 
                                                {{ collect($modulos)->sum(fn($m) => count(array_filter($m['cuestionario']['preguntas'], fn($p) => !empty(trim($p['texto']))))) }}
                                            </li>
                                            <li><strong>Preguntas evaluación final:</strong> {{ count($evaluacion_final['preguntas']) }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Importante:</strong> Los materiales (PDF/video) deben ser leídos/vistos en su totalidad para marcar como completado.
                            Los cuestionarios de módulo y la evaluación final requieren mínimo <strong>80%</strong> de respuestas correctas para aprobar.
                        </div>

                        <div class="card bg-gradient bg-primary text-white mb-3">
                            <div class="card-body py-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <i class="fas fa-save me-2"></i>
                                        <strong>¿Listo para guardar los cambios?</strong>
                                    </div>
                                    @if($this->canCreate())
                                        <span class="badge bg-light text-primary">
                                            <i class="fas fa-check-circle me-1"></i> Todo listo
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-exclamation-circle me-1"></i> Completa los campos
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button wire:click="previousStep" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i> Atrás
                            </button>
                            
                            @if($this->canCreate())
                                <button wire:click="save" class="btn btn-primary btn-lg px-5 shadow">
                                    <i class="fas fa-save me-2"></i> Guardar Cambios
                                </button>
                            @else
                                <button class="btn btn-secondary btn-lg px-5" disabled>
                                    <i class="fas fa-lock me-2"></i> Completa todos los campos
                                </button>
                            @endif
                        </div>

                        @if (!$this->canCreate())
                            <div class="alert alert-secondary mt-4">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-list-check me-3 mt-1 text-muted"></i>
                                    <div>
                                        <strong class="d-block mb-2">Pendiente por completar:</strong>
                                        <ul class="mb-0 small">
                                            @if(empty(trim($titulo)))
                                                <li><i class="fas fa-circle text-danger me-2" style="font-size:6px;vertical-align:middle;"></i> El título del curso</li>
                                            @endif
                                            @if($carga_horaria < 1)
                                                <li><i class="fas fa-circle text-danger me-2" style="font-size:6px;vertical-align:middle;"></i> La carga horaria del curso</li>
                                            @endif
                                            @foreach($modulos as $index => $modulo)
                                                @if(empty(trim($modulo['titulo'])))
                                                    <li><i class="fas fa-circle text-danger me-2" style="font-size:6px;vertical-align:middle;"></i> Módulo {{ $index + 1 }}: Agregar título</li>
                                                @endif
                                                @php
                                                    $hasMaterial = false;
                                                    foreach($modulo['materiales'] as $mat) {
                                                        if(!empty(trim($mat['titulo']))) { $hasMaterial = true; break; }
                                                    }
                                                @endphp
                                                @if(!$hasMaterial)
                                                    <li><i class="fas fa-circle text-danger me-2" style="font-size:6px;vertical-align:middle;"></i> Módulo {{ $index + 1 }}: Agregar al menos un material</li>
                                                @endif
                                                @php
                                                    $hasQuestion = false;
                                                    foreach($modulo['cuestionario']['preguntas'] as $preg) {
                                                        if(!empty(trim($preg['texto'])) && count(array_filter($preg['opciones'], fn($o) => !empty(trim($o['texto'])) && $o['es_correcta'])) >= 1) {
                                                            $hasQuestion = true; break;
                                                        }
                                                    }
                                                @endphp
                                                @if(!$hasQuestion)
                                                    <li><i class="fas fa-circle text-danger me-2" style="font-size:6px;vertical-align:middle;"></i> Módulo {{ $index + 1 }}: Agregar al menos una pregunta</li>
                                                @endif
                                            @endforeach
                                            @if(count($evaluacion_final['preguntas']) < 1)
                                                <li><i class="fas fa-circle text-danger me-2" style="font-size:6px;vertical-align:middle;"></i> Agregar al menos una pregunta a la evaluación final</li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
