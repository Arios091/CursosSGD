<div>
<style>
    .cc-container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
    .cc-title { font-size: 28px; font-weight: 700; color: #111827; margin-bottom: 8px; text-align: center; }
    .cc-subtitle { font-size: 14px; color: #6b7280; text-align: center; margin-bottom: 30px; }
    .cc-steps { display: flex; justify-content: center; gap: 0; margin-bottom: 30px; position: relative; }
    .cc-step { display: flex; align-items: center; gap: 10px; padding: 12px 20px; position: relative; z-index: 1; }
    .cc-step-num { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0; }
    .cc-step-num.active { background: #0B5E2E; color: white; }
    .cc-step-num.completed { background: #22c55e; color: white; }
    .cc-step-num.pending { background: #e5e7eb; color: #9ca3af; }
    .cc-step-label { font-size: 13px; font-weight: 500; }
    .cc-step-label.active { color: #0B5E2E; }
    .cc-step-label.completed { color: #22c55e; }
    .cc-step-label.pending { color: #9ca3af; }
    .cc-step-line { width: 60px; height: 2px; background: #e5e7eb; align-self: center; }
    .cc-step-line.completed { background: #22c55e; }
    .cc-card { background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); overflow: hidden; }
    .cc-card-header { padding: 24px 30px; border-bottom: 1px solid #f3f4f6; }
    .cc-card-header h3 { font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 4px; }
    .cc-card-header p { font-size: 13px; color: #6b7280; margin: 0; }
    .cc-card-body { padding: 30px; }
    .cc-field { margin-bottom: 24px; }
    .cc-label { display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px; }
    .cc-label .req { color: #ef4444; }
    .cc-input { width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px; color: #111827; transition: all 0.2s; background: #fff; }
    .cc-input:focus { border-color: #0B5E2E; outline: none; box-shadow: 0 0 0 3px rgba(11,94,46,0.1); }
    .cc-input::placeholder { color: #9ca3af; }
    .cc-textarea { resize: vertical; min-height: 100px; }
    .cc-error { font-size: 12px; color: #ef4444; margin-top: 6px; }
    .cc-hint { font-size: 12px; color: #9ca3af; margin-top: 6px; }
    .cc-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .cc-module-card { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-bottom: 16px; }
    .cc-module-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .cc-module-title { font-size: 15px; font-weight: 600; color: #111827; }
    .cc-module-num { background: #0B5E2E; color: white; padding: 2px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; margin-right: 8px; }
    .cc-material-row { display: flex; gap: 10px; align-items: flex-end; margin-bottom: 10px; padding: 12px; background: white; border-radius: 8px; border: 1px solid #e5e7eb; }
    .cc-material-row .cc-field { margin-bottom: 0; flex: 1; }
    .cc-material-row select { padding: 10px 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 13px; background: white; }
    .cc-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .cc-btn-primary { background: #0B5E2E; color: white; }
    .cc-btn-primary:hover { background: #0d7a3f; color: white; }
    .cc-btn-secondary { background: white; color: #374151; border: 2px solid #e5e7eb; }
    .cc-btn-secondary:hover { border-color: #0B5E2E; color: #0B5E2E; }
    .cc-btn-success { background: #22c55e; color: white; }
    .cc-btn-success:hover { background: #16a34a; color: white; }
    .cc-btn-danger { background: white; color: #ef4444; border: 1px solid #fecaca; padding: 8px 12px; font-size: 12px; }
    .cc-btn-danger:hover { background: #fef2f2; }
    .cc-btn-gold { background: #C9A227; color: white; }
    .cc-btn-gold:hover { background: #b8911f; color: white; }
    .cc-btn-sm { padding: 8px 16px; font-size: 13px; }
    .cc-btn-add { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: #f0fdf4; color: #0B5E2E; border: 1px dashed #86efac; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s; }
    .cc-btn-add:hover { background: #dcfce7; border-color: #22c55e; }
    .cc-question-card { background: white; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; margin-bottom: 12px; }
    .cc-question-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
    .cc-option-row { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
    .cc-option-radio { width: 18px; height: 18px; accent-color: #0B5E2E; cursor: pointer; }
    .cc-option-input { flex: 1; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 13px; }
    .cc-option-input:focus { border-color: #0B5E2E; outline: none; }
    .cc-summary { background: #f9fafb; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
    .cc-summary h4 { font-size: 15px; font-weight: 600; color: #111827; margin-bottom: 12px; }
    .cc-summary-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .cc-summary-item { font-size: 13px; color: #4b5563; }
    .cc-summary-item strong { color: #111827; }
    .cc-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 20px; border-top: 1px solid #f3f4f6; }
    .cc-file-preview { margin-top: 8px; }
    .cc-file-preview img { max-width: 200px; border-radius: 8px; border: 2px solid #e5e7eb; }
    .cc-file-preview .preview-img { max-width: 200px; border-radius: 8px; border: 2px solid #e5e7eb; }
    @media (max-width: 640px) {
        .cc-row { grid-template-columns: 1fr; }
        .cc-steps { flex-wrap: wrap; }
        .cc-step-line { display: none; }
        .cc-material-row { flex-direction: column; }
        .cc-footer { flex-direction: column; gap: 12px; }
    }
</style>

<div class="cc-container">
    <div class="cc-title"><i class="fas fa-plus-circle" style="color: #0B5E2E; margin-right: 8px;"></i>Crear Nuevo Curso</div>
    <div class="cc-subtitle">Completa los 3 pasos para crear un curso completo con módulos, materiales y evaluación</div>

    {{-- Step Indicator --}}
    <div class="cc-steps">
        <div class="cc-step">
            <div class="cc-step-num {{ $step >= 1 ? ($step > 1 ? 'completed' : 'active') : 'pending' }}">
                @if($step > 1) <i class="fas fa-check"></i> @else 1 @endif
            </div>
            <span class="cc-step-label {{ $step >= 1 ? ($step > 1 ? 'completed' : 'active') : 'pending' }}">Datos Básicos</span>
        </div>
        <div class="cc-step-line {{ $step > 1 ? 'completed' : '' }}"></div>
        <div class="cc-step">
            <div class="cc-step-num {{ $step >= 2 ? ($step > 2 ? 'completed' : 'active') : 'pending' }}">
                @if($step > 2) <i class="fas fa-check"></i> @else 2 @endif
            </div>
            <span class="cc-step-label {{ $step >= 2 ? ($step > 2 ? 'completed' : 'active') : 'pending' }}">Módulos</span>
        </div>
        <div class="cc-step-line {{ $step > 2 ? 'completed' : '' }}"></div>
        <div class="cc-step">
            <div class="cc-step-num {{ $step >= 3 ? 'active' : 'pending' }}">3</div>
            <span class="cc-step-label {{ $step >= 3 ? 'active' : 'pending' }}">Evaluación Final</span>
        </div>
    </div>

    @if (session()->has('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #fecaca;">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    {{-- STEP 1: Datos Básicos --}}
    @if ($step == 1)
    <div class="cc-card">
        <div class="cc-card-header">
            <h3><i class="fas fa-info-circle" style="color: #0B5E2E; margin-right: 8px;"></i>Información del Curso</h3>
            <p>Ingresa los datos generales del curso</p>
        </div>
        <div class="cc-card-body">
            <div class="cc-field">
                <label class="cc-label">Título del Curso <span class="req">*</span></label>
                <input type="text" wire:model="titulo" class="cc-input" placeholder="Ej: Introducción a la Programación en Python" />
                @error('titulo') <div class="cc-error">{{ $message }}</div> @enderror
            </div>

            <div class="cc-field">
                <label class="cc-label">Descripción</label>
                <textarea wire:model="descripcion" class="cc-input cc-textarea" rows="4" placeholder="Describe el contenido, objetivos y a quién está dirigido el curso..."></textarea>
            </div>

            <div class="cc-row">
                <div class="cc-field">
                    <label class="cc-label">Carga Horaria <span class="req">*</span></label>
                    <input type="number" wire:model="carga_horaria" class="cc-input" min="1" max="500" placeholder="Ej: 40" />
                    @error('carga_horaria') <div class="cc-error">{{ $message }}</div> @enderror
                </div>

                <div class="cc-field">
                    <label class="cc-label">Imagen Referencial</label>
                    <input type="file" wire:model="imagen_referencial" class="cc-input" accept="image/*" style="padding: 10px;" id="imagenInput" onchange="previewImage(this)" />
                    <div class="cc-hint">Formato: JPG, PNG. Máximo: 10MB</div>
                    @if ($imagen_referencial)
                        <div class="cc-file-preview" id="imagePreview">
                            @if(is_object($imagen_referencial) && method_exists($imagen_referencial, 'getClientOriginalName'))
                                <div style="text-align: center;">
                                    <i class="fas fa-image" style="font-size: 48px; color: #0B5E2E; margin-bottom: 8px;"></i>
                                    <p style="font-size: 13px; color: #0B5E2E; margin: 0;">{{ $imagen_referencial->getClientOriginalName() }}</p>
                                    <p style="font-size: 11px; color: #9ca3af; margin: 4px 0 0;">{{ number_format($imagen_referencial->getSize() / 1024, 1) }} KB</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="cc-footer">
                <a href="{{ route('home') }}" class="cc-btn cc-btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button wire:click="siguiente" class="cc-btn cc-btn-primary">
                    Siguiente <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- STEP 2: Módulos --}}
    @elseif ($step == 2)
    <div class="cc-card">
        <div class="cc-card-header">
            <h3><i class="fas fa-layer-group" style="color: #0B5E2E; margin-right: 8px;"></i>Módulos y Materiales</h3>
            <p>Define los módulos del curso. Cada módulo debe tener al menos un material.</p>
        </div>
        <div class="cc-card-body">
            @foreach ($modulos as $modIndex => $modulo)
            <div class="cc-module-card">
                <div class="cc-module-header">
                    <div class="cc-module-title"><span class="cc-module-num">{{ $modIndex + 1 }}</span> Módulo</div>
                    @if (count($modulos) > 1)
                        <button type="button" wire:click="eliminarModulo({{ $modIndex }})" class="cc-btn cc-btn-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    @endif
                </div>

                <div class="cc-field">
                    <label class="cc-label">Título del Módulo</label>
                    <input type="text" wire:model="modulos.{{ $modIndex }}.titulo" class="cc-input" placeholder="Ej: Introducción" />
                </div>

                <div class="cc-field">
                    <label class="cc-label">Materiales</label>
                    <div class="cc-hint" style="margin-bottom: 12px;">PDF: sube un archivo | Video: pega una URL de YouTube, Vimeo, etc.</div>
                    
                    @foreach ($modulos[$modIndex]['materiales'] as $matIndex => $material)
                    <div class="cc-material-row" x-data="{ tipo: '{{ $material['tipo'] }}' }">
                        <div class="cc-field" style="flex: 2;">
                            <input type="text" wire:model="modulos.{{ $modIndex }}.materiales.{{ $matIndex }}.titulo" class="cc-input" placeholder="Título del material" style="padding: 8px 12px; font-size: 13px;" />
                        </div>
                        <div style="flex: 1;">
                            <select wire:model="modulos.{{ $modIndex }}.materiales.{{ $matIndex }}.tipo" x-model="tipo" class="cc-input" style="padding: 8px 12px; font-size: 13px;">
                                <option value="pdf">📄 PDF</option>
                                <option value="video">▶ Video</option>
                            </select>
                        </div>
                        <div class="cc-field" style="flex: 3;" x-show="tipo === 'video'" x-cloak>
                            <input type="text" wire:model="modulos.{{ $modIndex }}.materiales.{{ $matIndex }}.url" class="cc-input" placeholder="https://youtube.com/watch?v=..." style="padding: 8px 12px; font-size: 13px;" />
                        </div>
                        <div class="cc-field" style="flex: 3;" x-show="tipo === 'pdf'" x-cloak>
                            <input type="file" wire:model="modulos.{{ $modIndex }}.materiales.{{ $matIndex }}.archivo" class="cc-input" accept=".pdf" style="padding: 8px 12px; font-size: 13px;" />
                            @if(!empty($material['original_name']))
                                <div style="font-size: 11px; color: #0B5E2E; margin-top: 4px;">
                                    <i class="fas fa-check-circle"></i> {{ $material['original_name'] }}
                                </div>
                            @endif
                        </div>
                        @if (count($modulo['materiales']) > 1)
                            <button type="button" wire:click="eliminarMaterial({{ $modIndex }}, {{ $matIndex }})" class="cc-btn cc-btn-danger" style="flex-shrink: 0;">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                    @endforeach
                    
                    <button type="button" wire:click="agregarMaterial({{ $modIndex }})" class="cc-btn-add">
                        <i class="fas fa-plus"></i> Agregar Material
                    </button>
                </div>

                {{-- Cuestionario del Módulo --}}
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                    <div class="cc-module-header">
                        <div class="cc-module-title"><i class="fas fa-clipboard-list" style="color: #C9A227; margin-right: 8px;"></i>Cuestionario del Módulo</div>
                    </div>
                    
                    @foreach ($modulo['cuestionario']['preguntas'] as $pIdx => $pregunta)
                    <div class="cc-question-card">
                        <div class="cc-question-header">
                            <span class="cc-module-num">{{ $pIdx + 1 }}</span>
                            <button type="button" wire:click="eliminarPreguntaCuestionario({{ $modIndex }}, {{ $pIdx }})" class="cc-btn cc-btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <input type="text" wire:model="modulos.{{ $modIndex }}.cuestionario.preguntas.{{ $pIdx }}.texto" class="cc-input" placeholder="Escribe la pregunta..." style="margin-bottom: 12px;" />
                        
                        @foreach ($pregunta['opciones'] as $oIdx => $opcion)
                        <div class="cc-option-row">
                            <input type="radio" class="cc-option-radio" wire:click="setCorrectaCuestionario({{ $modIndex }}, {{ $pIdx }}, {{ $oIdx }})" {{ $opcion['es_correcta'] ? 'checked' : '' }} title="Marcar como correcta">
                            <input type="text" wire:model="modulos.{{ $modIndex }}.cuestionario.preguntas.{{ $pIdx }}.opciones.{{ $oIdx }}.texto" class="cc-option-input" placeholder="Opción {{ $oIdx + 1 }}" />
                            @if (count($pregunta['opciones']) > 2)
                                <button type="button" wire:click="eliminarOpcionCuestionario({{ $modIndex }}, {{ $pIdx }}, {{ $oIdx }})" class="cc-btn cc-btn-danger" style="padding: 6px 10px;">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                        @endforeach
                        <button type="button" wire:click="agregarOpcionCuestionario({{ $modIndex }}, {{ $pIdx }})" class="cc-btn-add" style="margin-top: 8px;">
                            <i class="fas fa-plus"></i> Opción
                        </button>
                    </div>
                    @endforeach
                    <button type="button" wire:click="agregarPreguntaCuestionario({{ $modIndex }})" class="cc-btn-add">
                        <i class="fas fa-plus"></i> Agregar Pregunta al Cuestionario
                    </button>
                </div>
            </div>
            @endforeach

            <button type="button" wire:click="agregarModulo" class="cc-btn-add" style="margin-bottom: 24px; width: 100%; justify-content: center; padding: 14px;">
                <i class="fas fa-plus-circle"></i> Agregar Nuevo Módulo
            </button>

            <div class="cc-footer">
                <button wire:click="anterior" class="cc-btn cc-btn-secondary">
                    <i class="fas fa-arrow-left"></i> Atrás
                </button>
                <button wire:click="siguiente" class="cc-btn cc-btn-primary">
                    Siguiente <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- STEP 3: Evaluación Final --}}
    @elseif ($step == 3)
    <div class="cc-card">
        <div class="cc-card-header">
            <h3><i class="fas fa-graduation-cap" style="color: #C9A227; margin-right: 8px;"></i>Evaluación Final y Resumen</h3>
            <p>Configura la evaluación final y revisa el resumen del curso</p>
        </div>
        <div class="cc-card-body">
            <div class="cc-field">
                <label class="cc-label">Título de la Evaluación Final</label>
                <input type="text" wire:model="evaluacion_final_titulo" class="cc-input" placeholder="Ej: Evaluación Final del Curso" />
            </div>

            <label class="cc-label">Preguntas de la Evaluación Final</label>
            <div class="cc-hint" style="margin-bottom: 12px;">Selecciona la respuesta correcta marcando el radio button</div>
            
            @foreach ($evaluacion_final_preguntas as $pIdx => $pregunta)
            <div class="cc-question-card" style="border-left: 3px solid #0B5E2E;">
                <div class="cc-question-header">
                    <span class="cc-module-num">{{ $pIdx + 1 }}</span>
                    @if (count($evaluacion_final_preguntas) > 1)
                        <button type="button" wire:click="eliminarPreguntaEvaluacion({{ $pIdx }})" class="cc-btn cc-btn-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    @endif
                </div>
                <input type="text" wire:model="evaluacion_final_preguntas.{{ $pIdx }}.texto" class="cc-input" placeholder="Escribe la pregunta..." style="margin-bottom: 12px;" />
                
                @foreach ($pregunta['opciones'] as $oIdx => $opcion)
                <div class="cc-option-row">
                    <input type="radio" class="cc-option-radio" wire:click="setCorrectaEvaluacion({{ $pIdx }}, {{ $oIdx }})" {{ $opcion['es_correcta'] ? 'checked' : '' }} title="Marcar como correcta">
                    <input type="text" wire:model="evaluacion_final_preguntas.{{ $pIdx }}.opciones.{{ $oIdx }}.texto" class="cc-option-input" placeholder="Opción {{ $oIdx + 1 }}" />
                    @if (count($pregunta['opciones']) > 2)
                        <button type="button" wire:click="eliminarOpcionEvaluacion({{ $pIdx }}, {{ $oIdx }})" class="cc-btn cc-btn-danger" style="padding: 6px 10px;">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>
                @endforeach
                <button type="button" wire:click="agregarOpcionEvaluacion({{ $pIdx }})" class="cc-btn-add" style="margin-top: 8px;">
                    <i class="fas fa-plus"></i> Opción
                </button>
            </div>
            @endforeach

            <button type="button" wire:click="agregarPreguntaEvaluacion" class="cc-btn-add" style="margin-bottom: 24px;">
                <i class="fas fa-plus"></i> Agregar Pregunta
            </button>

            {{-- Resumen --}}
            <div class="cc-summary">
                <h4><i class="fas fa-list-check" style="color: #0B5E2E; margin-right: 8px;"></i>Resumen del Curso</h4>
                <div class="cc-summary-grid">
                    <div class="cc-summary-item"><strong>Título:</strong> {{ $titulo ?: 'Sin título' }}</div>
                    <div class="cc-summary-item"><strong>Carga Horaria:</strong> {{ $carga_horaria }} horas</div>
                    <div class="cc-summary-item"><strong>Módulos:</strong> {{ count($modulos) }}</div>
                    <div class="cc-summary-item"><strong>Preguntas Evaluación:</strong> {{ count($evaluacion_final_preguntas) }}</div>
                </div>
                @foreach($modulos as $idx => $mod)
                    <div class="cc-summary-item" style="margin-top: 8px; padding-left: 12px; border-left: 2px solid #e5e7eb;">
                        <strong>Módulo {{ $idx + 1 }}:</strong> {{ $mod['titulo'] ?: 'Sin título' }} — {{ count($mod['materiales']) }} material(es)
                        @if(count($mod['cuestionario']['preguntas']) > 0)
                            — {{ count($mod['cuestionario']['preguntas']) }} pregunta(s) en cuestionario
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="cc-footer">
                <button wire:click="anterior" class="cc-btn cc-btn-secondary">
                    <i class="fas fa-arrow-left"></i> Atrás
                </button>
                
                @if(!$this->puedeCrear())
                    <div style="text-align: right;">
                        <button type="button" class="cc-btn" style="background: #e5e7eb; color: #9ca3af; cursor: not-allowed;" disabled>
                            <i class="fas fa-graduation-cap"></i> Complete los requisitos
                        </button>
                        <div class="cc-error" style="margin-top: 8px; text-align: right;">
                            Requiere: título, al menos 1 módulo con material, y 1 pregunta en evaluación final
                        </div>
                    </div>
                @else
                    <button wire:click="guardar" class="cc-btn cc-btn-gold" style="padding: 14px 32px; font-size: 16px;">
                        <i class="fas fa-graduation-cap"></i> Crear Curso
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById('imagePreview');
            if (preview) {
                preview.innerHTML = '<img src="' + e.target.result + '" class="preview-img" alt="Preview" />';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</div>
