<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4">Crear Curso Completo</h1>

            <!-- Barra de progreso -->
            <div class="progress mb-4" style="height: 30px;">
                <div class="progress-bar" role="progressbar" style="width: {{ ($step / 3) * 100 }}%;" aria-valuenow="{{ $step }}" aria-valuemin="1" aria-valuemax="3">
                    Paso {{ $step }} de 3
                </div>
            </div>

            @if ($step == 1)
                <!-- Paso 1: Datos básicos -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Paso 1 de 3: Datos Básicos del Curso</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Título del Curso <span class="text-danger">*</span></label>
                            <input type="text" wire:model="titulo" class="form-control @error('titulo') is-invalid @enderror" placeholder="Ej: Introducción a la Programación" />
                            @error('titulo') 
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea wire:model="descripcion" class="form-control" rows="4" placeholder="Describe el contenido y objetivos del curso..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Imagen Referencial</label>
                            @if ($imagen_referencial)
                                <div class="mb-2">
                                    <img src="{{ $imagen_referencial->temporaryUrl() }}" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            @endif
                            <input type="file" wire:model="imagen_referencial" class="form-control @error('imagen_referencial') is-invalid @enderror" accept="image/*" />
                            @error('imagen_referencial') 
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Carga Horaria (horas) <span class="text-danger">*</span></label>
                            <input type="number" wire:model="carga_horaria" class="form-control @error('carga_horaria') is-invalid @enderror" min="1" placeholder="Ej: 40" />
                            @error('carga_horaria') 
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('home') }}" class="btn btn-secondary">Cancelar</a>
                            <button wire:click="nextStep" class="btn btn-primary">Siguiente → Módulos</button>
                        </div>
                    </div>
                </div>
            @elseif ($step == 2)
                <!-- Paso 2: Módulos -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Paso 2 de 3: Módulos del Curso</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Define los módulos o unidades temáticas del curso</p>

                        @forelse ($modulos as $index => $modulo)
                            <div class="card mb-3 border-secondary">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Módulo {{ $index + 1 }}</h6>
                                        @if (count($modulos) > 1)
                                            <button type="button" wire:click="removeModulo({{ $index }})" class="btn btn-sm btn-danger">
                                                Eliminar
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Título del Módulo <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="modulos.{{ $index }}.titulo" class="form-control @error('modulos.' . $index . '.titulo') is-invalid @enderror" placeholder="Ej: Introducción" />
                                        @error('modulos.' . $index . '.titulo')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Materiales del módulo --}}
                                    <div class="mb-3">
                                        <h6>Materiales</h6>
                                        @foreach ($modulos[$index]['materiales'] as $mIndex => $material)
                                            <div class="row g-2 align-items-end mb-2">
                                                <div class="col">
                                                    <input type="text" wire:model="modulos.{{ $index }}.materiales.{{ $mIndex }}.titulo" class="form-control" placeholder="Título del material" />
                                                </div>
                                                <div class="col-3">
                                                    <select wire:model="modulos.{{ $index }}.materiales.{{ $mIndex }}.tipo" class="form-control">
                                                        <option value="pdf">PDF</option>
                                                        <option value="video">Video</option>
                                                        <option value="cuestionario">Cuestionario</option>
                                                    </select>
                                                </div>
                                                <div class="col-4">
                                                    <input type="text" wire:model="modulos.{{ $index }}.materiales.{{ $mIndex }}.url" class="form-control" placeholder="URL o ruta" />
                                                </div>
                                                <div class="col-auto">
                                                    <button type="button" wire:click="removeMaterial({{ $index }}, {{ $mIndex }})" class="btn btn-sm btn-danger">×</button>
                                                </div>
                                            </div>
                                        @endforeach

                                        <button type="button" wire:click="addMaterial({{ $index }})" class="btn btn-sm btn-success">
                                            + Agregar Material
                                        </button>
                                    </div>

                                    {{-- Cuestionario del módulo --}}
                                    <div class="mb-3">
                                        <h6>Cuestionario</h6>
                                        @if(count($modulos[$index]['materiales']) > 0)
                                            @foreach ($modulos[$index]['cuestionario']['preguntas'] as $qIndex => $pregunta)
                                                <div class="input-group mb-2">
                                                    <input type="text" wire:model="modulos.{{ $index }}.cuestionario.preguntas.{{ $qIndex }}.texto" class="form-control" placeholder="Texto de la pregunta" />
                                                    <button type="button" wire:click="removeQuestion({{ $index }}, {{ $qIndex }})" class="btn btn-sm btn-danger">×</button>
                                                </div>
                                            @endforeach
                                            <button type="button" wire:click="addQuestion({{ $index }})" class="btn btn-sm btn-primary">
                                                + Agregar Pregunta
                                            </button>
                                        @else
                                            <div class="text-muted">Agrega al menos un material para habilitar el cuestionario.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info">No hay módulos. Haz clic en "Agregar Módulo"</div>
                        @endforelse

                        @error('modulos')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <button type="button" wire:click="addModulo" class="btn btn-success mb-3">
                            + Agregar Módulo
                        </button>

                        <div class="d-flex justify-content-between">
                            <button wire:click="previousStep" class="btn btn-secondary">← Atrás</button>
                            <button wire:click="nextStep" class="btn btn-primary">Siguiente → Resumen</button>
                        </div>
                    </div>
                </div>
            @elseif ($step == 3)
                <!-- Paso 3: Evaluación final y confirmación -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Paso 3 de 3: Evaluación Final y Resumen</h5>
                    </div>
                    <div class="card-body">
                        @unless($this->canProceedToEvaluation())
                            <div class="alert alert-danger">
                                Debes tener al menos un módulo con al menos un material y un cuestionario para crear la evaluación final.
                            </div>
                        @endunless

                        {{-- Formulario evaluación final --}}
                        <div class="mb-4">
                            <h6 class="fw-bold">Datos de la Evaluación Final</h6>

                            <div class="mb-3">
                                <label class="form-label">Título evaluación <span class="text-danger">*</span></label>
                                <input type="text" wire:model="evaluacion_final.titulo" class="form-control @error('evaluacion_final.titulo') is-invalid @enderror" />
                                @error('evaluacion_final.titulo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mínimo aprobación (%) <span class="text-danger">*</span></label>
                                <input type="number" wire:model="evaluacion_final.min_aprobacion" class="form-control @error('evaluacion_final.min_aprobacion') is-invalid @enderror" min="0" max="100" />
                                @error('evaluacion_final.min_aprobacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <h6>Preguntas</h6>
                                @foreach ($evaluacion_final['preguntas'] as $qIndex => $pregunta)
                                    <div class="input-group mb-2">
                                        <input type="text" wire:model="evaluacion_final.preguntas.{{ $qIndex }}" class="form-control" placeholder="Pregunta {{ $qIndex + 1 }}" />
                                        <button type="button" wire:click="removeEvalQuestion({{ $qIndex }})" class="btn btn-sm btn-danger">×</button>
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addEvalQuestion" class="btn btn-sm btn-primary">+ Agregar pregunta</button>
                                @error('evaluacion_final.preguntas')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold">Datos del Curso</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Título:</strong> {{ $titulo }}</li>
                                    <li><strong>Carga horaria:</strong> {{ $carga_horaria }} horas</li>
                                    <li><strong>Descripción:</strong> {{ Str::limit($descripcion, 100) ?? 'No proporcionada' }}</li>
                                    <li><strong>Imagen:</strong> {{ $imagen_referencial ? 'Sí' : 'No' }}</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                @if ($imagen_referencial)
                                    <img src="{{ $imagen_referencial->temporaryUrl() }}" alt="Imagen del curso" class="img-thumbnail w-100">
                                @else
                                    <div class="bg-light p-5 text-center text-muted">
                                        Sin imagen de referencia
                                    </div>
                                @endif
                            </div>
                        </div>

                        <hr>

                        <h6 class="fw-bold mb-3">Módulos ({{ count($modulos) }})</h6>
                        <div class="list-group">
                            @foreach ($modulos as $index => $modulo)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Módulo {{ $index + 1 }}</h6>
                                        <small>Orden: {{ $index + 1 }}</small>
                                    </div>
                                    <p class="mb-1"><strong>{{ $modulo['titulo'] }}</strong></p>
                                    <p class="mb-1 text-muted">Materiales: {{ count($modulo['materiales']) }}</p>
                                    <p class="mb-1 text-muted">Preguntas: {{ count($modulo['cuestionario']['preguntas'] ?? []) }}</p>
                                </div>
                            @endforeach
                        </div>

                        <hr>
                        <h6 class="fw-bold mb-3">Evaluación Final</h6>
                        <ul class="list-unstyled">
                            <li><strong>Título:</strong> {{ $evaluacion_final['titulo'] }}</li>
                            <li><strong>Mín. aprobación:</strong> {{ $evaluacion_final['min_aprobacion'] }}%</li>
                            <li><strong>Preguntas:</strong> {{ count($evaluacion_final['preguntas']) }}</li>
                        </ul>

                        <hr>

                        <div class="form-check mb-3">
                            <input type="checkbox" wire:model="confirmado" class="form-check-input" id="confirmar">
                            <label class="form-check-label" for="confirmar">
                                Confirmo que los datos son correctos y autorizo la creación del curso
                            </label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button wire:click="previousStep" class="btn btn-secondary">← Atrás</button>
                            <button wire:click="save" @disabled(!$confirmado || !$this->canProceedToEvaluation() || empty($evaluacion_final['preguntas'])) class="btn btn-success">
                                @if(!$confirmado)
                                    Debes confirmar para continuar
                                @elseif(!$this->canProceedToEvaluation())
                                    Completa al menos un módulo válido antes
                                @elseif(empty($evaluacion_final['preguntas']))
                                    Añade preguntas a la evaluación
                                @else
                                    Crear Curso
                                @endif
                            </button>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger mt-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>