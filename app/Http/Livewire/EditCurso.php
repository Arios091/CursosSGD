<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Curso;
use App\Models\Modulo;
use App\Models\Material;
use App\Models\Cuestionario;
use App\Models\Pregunta;
use App\Models\Opcion;
use App\Models\EvaluacionFinal;
use App\Models\PreguntaEvaluacion;
use App\Models\OpcionEvaluacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class EditCurso extends Component
{
    use WithFileUploads;

    public $curso;
    public $step = 1;

    public $titulo = '';
    public $descripcion = '';
    public $imagen_referencial;
    public $imagen_actual;
    public $carga_horaria = 1;

    public $modulos = [];
    public $modulosEliminados = [];

    public $evaluacion_final = [
        'id' => null,
        'titulo' => 'Evaluación Final',
        'min_aprobacion' => 80,
        'preguntas' => [],
    ];

    protected $rules = [
        'titulo' => 'required|string|min:3|max:255',
        'descripcion' => 'nullable|string|max:2000',
        'carga_horaria' => 'required|numeric|min:1|max:500',
        'imagen_referencial' => 'nullable|image|max:5120',
    ];

    public function mount($cursoId)
    {
        $this->curso = Curso::findOrFail($cursoId);
        
        Gate::authorize('update', $this->curso);

        $this->titulo = $this->curso->titulo;
        $this->descripcion = $this->curso->descripcion ?? '';
        $this->imagen_actual = $this->curso->imagen_referencial;
        $this->carga_horaria = $this->curso->carga_horaria ?? 1;

        $this->cargarModulos();
        $this->cargarEvaluacionFinal();
    }

    protected function cargarModulos()
    {
        $this->modulos = [];
        
        $modulos = $this->curso->modulos()
            ->with(['materiales', 'cuestionario', 'cuestionario.preguntas', 'cuestionario.preguntas.opciones'])
            ->get();
            
        foreach ($modulos as $modulo) {
            $preguntas = [];
            
            $cuestionario = Cuestionario::where('modulo_id', $modulo->id)
                ->with('preguntas.opciones')
                ->first();
            
            if ($cuestionario) {
                foreach ($cuestionario->preguntas as $pregunta) {
                    $opciones = [];
                    foreach ($pregunta->opciones as $opcion) {
                        $opciones[] = [
                            'id' => $opcion->id,
                            'texto' => $opcion->opcion,
                            'es_correcta' => (bool) $opcion->es_correcta,
                        ];
                    }
                    $preguntas[] = [
                        'id' => $pregunta->id,
                        'texto' => $pregunta->pregunta,
                        'opciones' => $opciones,
                    ];
                }
            }

            $materiales = [];
            foreach ($modulo->materiales as $material) {
                $materiales[] = [
                    'id' => $material->id,
                    'titulo' => $material->titulo,
                    'tipo' => $material->tipo,
                    'url' => $material->url ?? '',
                    'archivo' => null,
                ];
            }

            $this->modulos[] = [
                'id' => $modulo->id,
                'titulo' => $modulo->titulo,
                'orden' => $modulo->orden,
                'materiales' => $materiales,
                'cuestionario' => [
                    'id' => $cuestionario ? $cuestionario->id : null,
                    'titulo' => $cuestionario ? ($cuestionario->titulo ?: 'Cuestionario Módulo ' . $modulo->orden) : 'Cuestionario Módulo ' . $modulo->orden,
                    'min_aprobacion' => $cuestionario ? ($cuestionario->min_aprobacion ?? 80) : 80,
                    'preguntas' => $preguntas,
                ],
            ];
        }

        if (empty($this->modulos)) {
            $this->addModulo();
        }
    }

    protected function cargarEvaluacionFinal()
    {
        $evaluacion = $this->curso->evaluacionFinal;
        
        if ($evaluacion) {
            $this->evaluacion_final['id'] = $evaluacion->id;
            $this->evaluacion_final['titulo'] = $evaluacion->titulo;
            $this->evaluacion_final['min_aprobacion'] = $evaluacion->min_aprobacion;
            $this->evaluacion_final['preguntas'] = [];

            foreach ($evaluacion->preguntas as $pregunta) {
                $opciones = [];
                foreach ($pregunta->opciones as $opcion) {
                    $opciones[] = [
                        'id' => $opcion->id,
                        'texto' => $opcion->opcion,
                        'es_correcta' => (bool) $opcion->es_correcta,
                    ];
                }
                $this->evaluacion_final['preguntas'][] = [
                    'id' => $pregunta->id,
                    'texto' => $pregunta->pregunta,
                    'opciones' => $opciones,
                ];
            }
        }
    }

    public function addModulo()
    {
        $numero = count($this->modulos) + 1;
        $this->modulos[] = [
            'id' => null,
            'titulo' => '',
            'orden' => $numero,
            'materiales' => [],
            'cuestionario' => [
                'id' => null,
                'titulo' => 'Cuestionario Módulo ' . $numero,
                'min_aprobacion' => 80,
                'preguntas' => [],
            ],
        ];
        $this->addMaterial(count($this->modulos) - 1);
    }

    public function removeModulo($index)
    {
        if (isset($this->modulos[$index]['id'])) {
            $this->modulosEliminados[] = $this->modulos[$index]['id'];
        }
        
        if (count($this->modulos) > 1) {
            unset($this->modulos[$index]);
            $this->modulos = array_values($this->modulos);
            foreach ($this->modulos as $i => &$modulo) {
                $modulo['orden'] = $i + 1;
                if (!$modulo['cuestionario']['id']) {
                    $modulo['cuestionario']['titulo'] = 'Cuestionario Módulo ' . ($i + 1);
                }
            }
        }
    }

    public function addMaterial($moduloIndex)
    {
        $this->modulos[$moduloIndex]['materiales'][] = [
            'id' => null,
            'titulo' => '',
            'tipo' => 'pdf',
            'archivo' => null,
            'url' => '',
        ];
    }

    public function removeMaterial($moduloIndex, $materialIndex)
    {
        if (count($this->modulos[$moduloIndex]['materiales']) > 1) {
            unset($this->modulos[$moduloIndex]['materiales'][$materialIndex]);
            $this->modulos[$moduloIndex]['materiales'] = array_values($this->modulos[$moduloIndex]['materiales']);
        }
    }

    public function addQuestion($moduloIndex)
    {
        $this->modulos[$moduloIndex]['cuestionario']['preguntas'][] = [
            'id' => null,
            'texto' => '',
            'opciones' => [
                ['id' => null, 'texto' => '', 'es_correcta' => false],
                ['id' => null, 'texto' => '', 'es_correcta' => false],
            ],
        ];
    }

    public function removeQuestion($moduloIndex, $questionIndex)
    {
        unset($this->modulos[$moduloIndex]['cuestionario']['preguntas'][$questionIndex]);
        $this->modulos[$moduloIndex]['cuestionario']['preguntas'] = array_values($this->modulos[$moduloIndex]['cuestionario']['preguntas']);
    }

    public function addOption($moduloIndex, $questionIndex)
    {
        $this->modulos[$moduloIndex]['cuestionario']['preguntas'][$questionIndex]['opciones'][] = [
            'id' => null,
            'texto' => '',
            'es_correcta' => false,
        ];
    }

    public function removeOption($moduloIndex, $questionIndex, $optionIndex)
    {
        $opciones = &$this->modulos[$moduloIndex]['cuestionario']['preguntas'][$questionIndex]['opciones'];
        if (count($opciones) > 2) {
            unset($opciones[$optionIndex]);
            $this->modulos[$moduloIndex]['cuestionario']['preguntas'][$questionIndex]['opciones'] = array_values($opciones);
        }
    }

    public function setCorrectOption($moduloIndex, $questionIndex, $optionIndex)
    {
        foreach ($this->modulos[$moduloIndex]['cuestionario']['preguntas'][$questionIndex]['opciones'] as $i => &$opcion) {
            $opcion['es_correcta'] = ($i === $optionIndex);
        }
    }

    public function addEvalQuestion()
    {
        $this->evaluacion_final['preguntas'][] = [
            'id' => null,
            'texto' => '',
            'opciones' => [
                ['id' => null, 'texto' => '', 'es_correcta' => false],
                ['id' => null, 'texto' => '', 'es_correcta' => false],
            ],
        ];
    }

    public function removeEvalQuestion($index)
    {
        unset($this->evaluacion_final['preguntas'][$index]);
        $this->evaluacion_final['preguntas'] = array_values($this->evaluacion_final['preguntas']);
    }

    public function addEvalOption($questionIndex)
    {
        $this->evaluacion_final['preguntas'][$questionIndex]['opciones'][] = [
            'id' => null,
            'texto' => '',
            'es_correcta' => false,
        ];
    }

    public function removeEvalOption($questionIndex, $optionIndex)
    {
        $opciones = &$this->evaluacion_final['preguntas'][$questionIndex]['opciones'];
        if (count($opciones) > 2) {
            unset($opciones[$optionIndex]);
            $this->evaluacion_final['preguntas'][$questionIndex]['opciones'] = array_values($opciones);
        }
    }

    public function setEvalCorrectOption($questionIndex, $optionIndex)
    {
        foreach ($this->evaluacion_final['preguntas'][$questionIndex]['opciones'] as $i => &$opcion) {
            $opcion['es_correcta'] = ($i === $optionIndex);
        }
    }

    public function nextStep()
    {
        if ($this->step == 1) {
            $this->validateOnly('titulo');
            $this->validateOnly('carga_horaria');
        } elseif ($this->step == 2) {
            if (!$this->validateModulos()) {
                return;
            }
        }

        if ($this->step < 3) {
            $this->step++;
        }
    }

    public function validateModulos(): bool
    {
        $hasError = false;

        foreach ($this->modulos as $index => $modulo) {
            if (empty(trim($modulo['titulo']))) {
                $this->addError("modulos.{$index}.titulo", 'El título del módulo es obligatorio');
                $hasError = true;
            }

            $materialesValidos = 0;
            foreach ($modulo['materiales'] as $mIndex => $material) {
                if (!empty(trim($material['titulo']))) {
                    $materialesValidos++;
                    if ($material['tipo'] === 'video' && empty(trim($material['url'])) && !$material['archivo']) {
                        $this->addError("modulos.{$index}.materiales.{$mIndex}.url", 'La URL del video es obligatoria');
                        $hasError = true;
                    }
                }
            }

            if ($materialesValidos < 1) {
                $this->addError("modulos.{$index}.materiales", 'Cada módulo debe tener al menos un material con título');
                $hasError = true;
            }

            $preguntasValidas = 0;
            foreach ($modulo['cuestionario']['preguntas'] as $qIndex => $pregunta) {
                if (!empty(trim($pregunta['texto']))) {
                    $preguntasValidas++;
                    $opcionesValidas = 0;
                    $tieneCorrecta = false;
                    
                    foreach ($pregunta['opciones'] as $oIndex => $opcion) {
                        if (!empty(trim($opcion['texto']))) {
                            $opcionesValidas++;
                            if ($opcion['es_correcta']) {
                                $tieneCorrecta = true;
                            }
                        }
                    }

                    if ($opcionesValidas < 2) {
                        $this->addError("modulos.{$index}.cuestionario.preguntas.{$qIndex}", 'Cada pregunta debe tener al menos 2 opciones');
                        $hasError = true;
                    }
                    if (!$tieneCorrecta) {
                        $this->addError("modulos.{$index}.cuestionario.preguntas.{$qIndex}.correcta", 'Debes marcar una respuesta correcta');
                        $hasError = true;
                    }
                }
            }

            if ($preguntasValidas < 1) {
                $this->addError("modulos.{$index}.cuestionario", 'Cada módulo debe tener al menos una pregunta con opciones completas');
                $hasError = true;
            }
        }

        return !$hasError;
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function canCreate(): bool
    {
        if (empty(trim($this->titulo)) || $this->carga_horaria < 1) {
            return false;
        }

        if (count($this->modulos) < 1) {
            return false;
        }

        foreach ($this->modulos as $modulo) {
            if (empty(trim($modulo['titulo']))) {
                return false;
            }
            $hasMaterial = false;
            foreach ($modulo['materiales'] as $material) {
                if (!empty(trim($material['titulo']))) {
                    $hasMaterial = true;
                    break;
                }
            }
            if (!$hasMaterial) {
                return false;
            }

            $hasValidQuestion = false;
            foreach ($modulo['cuestionario']['preguntas'] as $pregunta) {
                if (!empty(trim($pregunta['texto']))) {
                    $opcionesValidas = 0;
                    $tieneCorrecta = false;
                    foreach ($pregunta['opciones'] as $opcion) {
                        if (!empty(trim($opcion['texto']))) {
                            $opcionesValidas++;
                            if ($opcion['es_correcta']) {
                                $tieneCorrecta = true;
                            }
                        }
                    }
                    if ($opcionesValidas >= 2 && $tieneCorrecta) {
                        $hasValidQuestion = true;
                        break;
                    }
                }
            }
            if (!$hasValidQuestion) {
                return false;
            }
        }

        if (count($this->evaluacion_final['preguntas']) < 1) {
            return false;
        }

        foreach ($this->evaluacion_final['preguntas'] as $pregunta) {
            if (empty(trim($pregunta['texto']))) {
                return false;
            }
            $opcionesValidas = 0;
            $tieneCorrecta = false;
            foreach ($pregunta['opciones'] as $opcion) {
                if (!empty(trim($opcion['texto']))) {
                    $opcionesValidas++;
                    if ($opcion['es_correcta']) {
                        $tieneCorrecta = true;
                    }
                }
            }
            if ($opcionesValidas < 2 || !$tieneCorrecta) {
                return false;
            }
        }

        return true;
    }

    public function save()
    {
        $this->validate();

        if (!$this->validateModulos()) {
            $this->step = 2;
            return;
        }

        if (!$this->canCreate()) {
            session()->flash('error', 'Completa todos los campos requeridos antes de guardar el curso');
            return;
        }

        try {
            $this->curso->update([
                'titulo' => $this->titulo,
                'descripcion' => $this->descripcion,
                'carga_horaria' => $this->carga_horaria,
            ]);

            if ($this->imagen_referencial) {
                $path = $this->imagen_referencial->store('cursos', 'public');
                $this->curso->update(['imagen_referencial' => $path]);
            }

            foreach ($this->modulosEliminados as $moduloId) {
                Modulo::destroy($moduloId);
            }

            foreach ($this->modulos as $index => $moduloData) {
                $moduloTitulo = trim($moduloData['titulo']) ?: 'Módulo ' . ($index + 1);

                $modulo = Modulo::updateOrCreate(
                    ['id' => $moduloData['id']],
                    [
                        'curso_id' => $this->curso->id,
                        'titulo' => $moduloTitulo,
                        'orden' => $index + 1,
                    ]
                );

                $materialesExistentes = $modulo->materiales()->pluck('id')->toArray();
                $materialesMantener = [];

                foreach ($moduloData['materiales'] as $mIndex => $material) {
                    if (empty(trim($material['titulo']))) {
                        continue;
                    }

                    $materialData = [
                        'titulo' => $material['titulo'],
                        'tipo' => $material['tipo'],
                        'orden' => $mIndex + 1,
                    ];

                    if ($material['tipo'] === 'video' && !empty($material['url'])) {
                        $rawUrl = trim($material['url']);
                        $youtubeId = $this->extractYouTubeId($rawUrl);
                        $materialData['url'] = $youtubeId ? 'https://www.youtube.com/embed/' . $youtubeId : $rawUrl;
                    }

                    if ($material['archivo']) {
                        $materialData['url'] = $material['archivo']->store('materiales', 'public');
                    }

                    $materialModel = Material::updateOrCreate(
                        ['id' => $material['id']],
                        array_merge($materialData, ['modulo_id' => $modulo->id])
                    );
                    $materialesMantener[] = $materialModel->id;
                }

                foreach (array_diff($materialesExistentes, $materialesMantener) as $deletedId) {
                    Material::destroy($deletedId);
                }

                $cuestionarioPreguntas = collect($moduloData['cuestionario']['preguntas'])
                    ->filter(fn($p) => !empty(trim($p['texto'])));

                if (!empty($moduloData['cuestionario']['id'])) {
                    $cuestionario = Cuestionario::find($moduloData['cuestionario']['id']);
                    if ($cuestionario) {
                        $cuestionario->update([
                            'titulo' => $moduloData['cuestionario']['titulo'],
                            'min_aprobacion' => $moduloData['cuestionario']['min_aprobacion'],
                        ]);
                    }
                } elseif ($cuestionarioPreguntas->isNotEmpty()) {
                    $cuestionario = $modulo->cuestionario()->create([
                        'titulo' => $moduloData['cuestionario']['titulo'],
                        'min_aprobacion' => $moduloData['cuestionario']['min_aprobacion'],
                    ]);
                }

                if (isset($cuestionario)) {
                    $preguntasExistentes = $cuestionario->preguntas()->pluck('id')->toArray();
                    $preguntasMantener = [];

                    foreach ($cuestionarioPreguntas as $qIndex => $pregunta) {
                        $preguntaModel = Pregunta::updateOrCreate(
                            ['id' => $pregunta['id']],
                            [
                                'cuestionario_id' => $cuestionario->id,
                                'pregunta' => $pregunta['texto'],
                                'orden' => $qIndex + 1,
                            ]
                        );
                        $preguntasMantener[] = $preguntaModel->id;

                        $opcionesExistentes = $preguntaModel->opciones()->pluck('id')->toArray();
                        $opcionesMantener = [];

                        foreach ($pregunta['opciones'] as $oIndex => $opcion) {
                            if (!empty(trim($opcion['texto']))) {
                                $opcionModel = Opcion::updateOrCreate(
                                    ['id' => $opcion['id']],
                                    [
                                        'pregunta_id' => $preguntaModel->id,
                                        'opcion' => $opcion['texto'],
                                        'es_correcta' => (bool) $opcion['es_correcta'],
                                        'orden' => $oIndex + 1,
                                    ]
                                );
                                $opcionesMantener[] = $opcionModel->id;
                            }
                        }

                        foreach (array_diff($opcionesExistentes, $opcionesMantener) as $deletedId) {
                            Opcion::destroy($deletedId);
                        }
                    }

                    foreach (array_diff($preguntasExistentes, $preguntasMantener) as $deletedId) {
                        Pregunta::destroy($deletedId);
                    }
                }
            }

            $evalPreguntas = collect($this->evaluacion_final['preguntas'])
                ->filter(fn($p) => !empty(trim($p['texto'])));

            if ($this->evaluacion_final['id']) {
                $evaluacion = EvaluacionFinal::find($this->evaluacion_final['id']);
                if ($evaluacion) {
                    $evaluacion->update([
                        'titulo' => $this->evaluacion_final['titulo'],
                        'min_aprobacion' => $this->evaluacion_final['min_aprobacion'],
                    ]);
                }
            } elseif ($evalPreguntas->isNotEmpty()) {
                $evaluacion = $this->curso->evaluacionFinal()->create([
                    'titulo' => $this->evaluacion_final['titulo'],
                    'min_aprobacion' => $this->evaluacion_final['min_aprobacion'],
                ]);
            }

            if (isset($evaluacion)) {
                $preguntasExistentes = $evaluacion->preguntas()->pluck('id')->toArray();
                $preguntasMantener = [];

                foreach ($evalPreguntas as $qIndex => $pregunta) {
                    $preguntaModel = PreguntaEvaluacion::updateOrCreate(
                        ['id' => $pregunta['id']],
                        [
                            'evaluacion_final_id' => $evaluacion->id,
                            'pregunta' => $pregunta['texto'],
                            'orden' => $qIndex + 1,
                        ]
                    );
                    $preguntasMantener[] = $preguntaModel->id;

                    $opcionesExistentes = $preguntaModel->opciones()->pluck('id')->toArray();
                    $opcionesMantener = [];

                    foreach ($pregunta['opciones'] as $oIndex => $opcion) {
                        if (!empty(trim($opcion['texto']))) {
                            $opcionModel = OpcionEvaluacion::updateOrCreate(
                                ['id' => $opcion['id']],
                                [
                                    'pregunta_evaluacion_id' => $preguntaModel->id,
                                    'opcion' => $opcion['texto'],
                                    'es_correcta' => (bool) $opcion['es_correcta'],
                                    'orden' => $oIndex + 1,
                                ]
                            );
                            $opcionesMantener[] = $opcionModel->id;
                        }
                    }

                    foreach (array_diff($opcionesExistentes, $opcionesMantener) as $deletedId) {
                        OpcionEvaluacion::destroy($deletedId);
                    }
                }

                foreach (array_diff($preguntasExistentes, $preguntasMantener) as $deletedId) {
                    PreguntaEvaluacion::destroy($deletedId);
                }
            }

            return redirect()->route('home')->with('success', '¡Curso actualizado correctamente!');
        } catch (\Exception $e) {
            \Log::error('Error al actualizar curso: ' . $e->getMessage());
            session()->flash('error', 'Error al actualizar el curso: ' . $e->getMessage());
        }
    }

    protected function extractYouTubeId($url)
    {
        $patterns = [
            '/youtu\.be\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/v\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/live\/([a-zA-Z0-9_-]{11})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    public function render()
    {
        return view('livewire.edit-curso');
    }
}
