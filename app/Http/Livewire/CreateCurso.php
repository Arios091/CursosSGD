<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Curso;
use App\Models\Modulo;
use App\Models\Material;
use App\Models\Cuestionario;
use App\Models\EvaluacionFinal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CreateCurso extends Component
{
    use WithFileUploads;

    public $step = 1;
    public $titulo = '';
    public $descripcion = '';
    public $imagen_referencial;
    public $carga_horaria = 1;
    
    public $modulos = [];
    public $evaluacion_final_titulo = 'Evaluación Final';
    public $evaluacion_final_preguntas = [];

    protected $rules = [
        'titulo' => 'required|string|min:3|max:255',
        'carga_horaria' => 'required|numeric|min:1|max:500',
        'modulos.*.titulo' => 'required|string|min:1',
        'modulos.*.materiales.*.titulo' => 'required|string|min:1',
        'modulos.*.materiales.*.tipo' => 'required|in:pdf,video',
        'modulos.*.materiales.*.url' => 'nullable|string',
        'modulos.*.cuestionario.preguntas.*.texto' => 'nullable|string',
        'evaluacion_final_preguntas.*.texto' => 'nullable|string',
    ];

    public function mount()
    {
        // Solo admin puede crear cursos
        if (auth()->user()->role !== 'admin') {
            abort(403, 'No tienes permiso para crear cursos.');
        }
        
        $savedData = Session::get('curso_creating', null);
        
        if ($savedData) {
            $this->titulo = $savedData['titulo'] ?? '';
            $this->descripcion = $savedData['descripcion'] ?? '';
            $this->carga_horaria = $savedData['carga_horaria'] ?? 1;
            $this->modulos = $savedData['modulos'] ?? [];
            $this->evaluacion_final_titulo = $savedData['evaluacion_final_titulo'] ?? 'Evaluación Final';
            $this->evaluacion_final_preguntas = $savedData['evaluacion_final_preguntas'] ?? [];
            $this->step = $savedData['step'] ?? 1;
        } else {
            $this->agregarModulo();
            $this->agregarPreguntaEvaluacion();
        }
    }

    public function updated($property, $value)
    {
        Log::info("Updated property: $property, value: " . (is_array($value) ? 'array' : $value));
        
        if ($property !== 'imagen_referencial' && !str_contains($property, '.archivo')) {
            $this->saveToSession();
        }
    }

    public function updatedModulosMaterialesTipo($value)
    {
        Log::info("Tipo cambiado a: $value");
        $this->saveToSession();
    }

    public function actualizarTipoMaterial($moduloIndex, $materialIndex, $tipoNuevo)
    {
        Log::info("actualizarTipoMaterial: modulo=$moduloIndex, material=$materialIndex, tipo=$tipoNuevo");
        $this->saveToSession();
    }

    protected function saveToSession()
    {
        $modulosSinArchivos = array_map(function($modulo) {
            $modulo['materiales'] = array_map(function($material) {
                unset($material['archivo']);
                return $material;
            }, $modulo['materiales'] ?? []);
            return $modulo;
        }, $this->modulos);

        Session::put('curso_creating', [
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'carga_horaria' => $this->carga_horaria,
            'modulos' => $modulosSinArchivos,
            'evaluacion_final_titulo' => $this->evaluacion_final_titulo,
            'evaluacion_final_preguntas' => $this->evaluacion_final_preguntas,
            'step' => $this->step,
        ]);
    }

    public function clearSession()
    {
        Session::forget('curso_creating');
    }

    public function agregarModulo()
    {
        $numero = count($this->modulos) + 1;
        $this->modulos[] = [
            'titulo' => '',
            'materiales' => [
                ['titulo' => '', 'tipo' => 'pdf', 'url' => '', 'archivo' => null]
            ],
            'cuestionario' => [
                'titulo' => 'Cuestionario Módulo ' . $numero,
                'min_aprobacion' => 80,
                'preguntas' => [
                    ['texto' => '', 'opciones' => [['texto' => '', 'es_correcta' => true], ['texto' => '', 'es_correcta' => false]]]
                ]
            ]
        ];
    }

    public function eliminarModulo($index)
    {
        if (count($this->modulos) > 1) {
            unset($this->modulos[$index]);
            $this->modulos = array_values($this->modulos);
        }
    }

    public function agregarMaterial($moduloIndex)
    {
        $this->modulos[$moduloIndex]['materiales'][] = ['titulo' => '', 'tipo' => 'pdf', 'url' => '', 'archivo' => null];
    }

    public function eliminarMaterial($moduloIndex, $materialIndex)
    {
        if (count($this->modulos[$moduloIndex]['materiales']) > 1) {
            unset($this->modulos[$moduloIndex]['materiales'][$materialIndex]);
            $this->modulos[$moduloIndex]['materiales'] = array_values($this->modulos[$moduloIndex]['materiales']);
        }
    }

    public function agregarPreguntaCuestionario($moduloIndex)
    {
        $this->modulos[$moduloIndex]['cuestionario']['preguntas'][] = [
            'texto' => '',
            'opciones' => [
                ['texto' => '', 'es_correcta' => true],
                ['texto' => '', 'es_correcta' => false]
            ]
        ];
    }

    public function eliminarPreguntaCuestionario($moduloIndex, $preguntaIndex)
    {
        unset($this->modulos[$moduloIndex]['cuestionario']['preguntas'][$preguntaIndex]);
        $this->modulos[$moduloIndex]['cuestionario']['preguntas'] = array_values($this->modulos[$moduloIndex]['cuestionario']['preguntas']);
    }

    public function agregarOpcionCuestionario($moduloIndex, $preguntaIndex)
    {
        $this->modulos[$moduloIndex]['cuestionario']['preguntas'][$preguntaIndex]['opciones'][] = ['texto' => '', 'es_correcta' => false];
    }

    public function eliminarOpcionCuestionario($moduloIndex, $preguntaIndex, $opcionIndex)
    {
        $opciones = $this->modulos[$moduloIndex]['cuestionario']['preguntas'][$preguntaIndex]['opciones'];
        if (count($opciones) > 2) {
            unset($opciones[$opcionIndex]);
            $this->modulos[$moduloIndex]['cuestionario']['preguntas'][$preguntaIndex]['opciones'] = array_values($opciones);
        }
    }

    public function setCorrectaCuestionario($moduloIndex, $preguntaIndex, $opcionIndex)
    {
        foreach ($this->modulos[$moduloIndex]['cuestionario']['preguntas'][$preguntaIndex]['opciones'] as $i => $opcion) {
            $this->modulos[$moduloIndex]['cuestionario']['preguntas'][$preguntaIndex]['opciones'][$i]['es_correcta'] = ($i === $opcionIndex);
        }
    }

    public function agregarPreguntaEvaluacion()
    {
        $this->evaluacion_final_preguntas[] = [
            'texto' => '',
            'opciones' => [
                ['texto' => '', 'es_correcta' => true],
                ['texto' => '', 'es_correcta' => false]
            ]
        ];
    }

    public function eliminarPreguntaEvaluacion($index)
    {
        if (count($this->evaluacion_final_preguntas) > 1) {
            unset($this->evaluacion_final_preguntas[$index]);
            $this->evaluacion_final_preguntas = array_values($this->evaluacion_final_preguntas);
        }
    }

    public function agregarOpcionEvaluacion($preguntaIndex)
    {
        $this->evaluacion_final_preguntas[$preguntaIndex]['opciones'][] = ['texto' => '', 'es_correcta' => false];
    }

    public function eliminarOpcionEvaluacion($preguntaIndex, $opcionIndex)
    {
        $opciones = $this->evaluacion_final_preguntas[$preguntaIndex]['opciones'];
        if (count($opciones) > 2) {
            unset($opciones[$opcionIndex]);
            $this->evaluacion_final_preguntas[$preguntaIndex]['opciones'] = array_values($opciones);
        }
    }

    public function setCorrectaEvaluacion($preguntaIndex, $opcionIndex)
    {
        foreach ($this->evaluacion_final_preguntas[$preguntaIndex]['opciones'] as $i => $opcion) {
            $this->evaluacion_final_preguntas[$preguntaIndex]['opciones'][$i]['es_correcta'] = ($i === $opcionIndex);
        }
    }

    public function siguiente()
    {
        if ($this->step < 3) {
            $this->step++;
        }
    }

    public function anterior()
    {
        if ($this->step > 1) {
            $this->step--;
            $this->dispatchBrowserEvent('stepChanged', ['step' => $this->step]);
        }
    }

    public function puedeCrear(): bool
    {
        if (empty(trim($this->titulo)) || $this->carga_horaria < 1) {
            Log::info('puedeCrear false: titulo vacio');
            return false;
        }

        foreach ($this->modulos as $idx => $modulo) {
            if (empty(trim($modulo['titulo']))) {
                Log::info('puedeCrear false: modulo sin titulo ' . $idx);
                return false;
            }
            $tieneMaterial = false;
            foreach ($modulo['materiales'] as $mIdx => $material) {
                $titulo = $material['titulo'] ?? '';
                $tipo = $material['tipo'] ?? 'pdf';
                $url = $material['url'] ?? '';
                
                Log::info("Material $idx.$mIdx: tipo=$tipo, titulo=$titulo, url=$url");
                
                if (!empty(trim($titulo))) {
                    if ($tipo === 'video' && !empty(trim($url))) {
                        $tieneMaterial = true;
                    }
                    if ($tipo === 'pdf' && !empty($material['archivo'])) {
                        $tieneMaterial = true;
                    }
                }
            }
            if (!$tieneMaterial) {
                Log::info('puedeCrear false: modulo sin material ' . $idx);
                return false;
            }
        }

        $evalValida = false;
        foreach ($this->evaluacion_final_preguntas as $preg) {
            if (!empty(trim($preg['texto']))) {
                $evalValida = true;
                break;
            }
        }

        if (!$evalValida) {
            Log::info('puedeCrear false: evaluacion sin preguntas');
        }

        return $evalValida;
    }

    public function guardar()
    {
        Log::info('Intentando guardar curso. puedeCrear: ' . ($this->puedeCrear() ? 'true' : 'false'));
        
        if (!$this->puedeCrear()) {
            Log::info('Error: No puede crear curso');
            session()->flash('error', 'Completa todos los campos requeridos: título, módulo con material (PDF o video con URL), y al menos una pregunta en la evaluación final.');
            return;
        }
        
        $this->validate();

        try {
            DB::beginTransaction();

            $curso = Curso::create([
                'titulo' => $this->titulo,
                'descripcion' => $this->descripcion,
                'carga_horaria' => $this->carga_horaria,
                'user_id' => Auth::id(),
            ]);

            if ($this->imagen_referencial) {
                $path = $this->imagen_referencial->store('cursos', 'public');
                $curso->update(['imagen_referencial' => $path]);
            }

            foreach ($this->modulos as $idx => $moduloData) {
                $modulo = $curso->modulos()->create([
                    'titulo' => $moduloData['titulo'] ?: 'Módulo ' . ($idx + 1),
                    'orden' => $idx + 1,
                ]);

                foreach ($moduloData['materiales'] as $mIdx => $material) {
                    if (empty(trim($material['titulo']))) {
                        continue;
                    }

                    $materialUrl = null;
                    $tipo = $material['tipo'] ?? 'pdf';
                    
                    if ($tipo === 'video' && !empty(trim($material['url'] ?? ''))) {
                        $materialUrl = trim($material['url']);
                    } elseif ($tipo === 'pdf' && !empty($material['archivo'])) {
                        $archivo = $material['archivo'];
                        $filename = time() . '_' . $curso->id . '_m' . ($idx + 1) . '_' . $mIdx . '.' . $archivo->getClientOriginalExtension();
                        $materialUrl = $archivo->storeAs('materiales', $filename, 'public');
                    }

                    $modulo->materiales()->create([
                        'titulo' => $material['titulo'],
                        'tipo' => $tipo,
                        'url' => $materialUrl,
                        'orden' => $mIdx + 1,
                    ]);
                }

                $preguntasValidas = array_filter($moduloData['cuestionario']['preguntas'], fn($p) => !empty(trim($p['texto'])));
                if (!empty($preguntasValidas)) {
                    $cuestionario = $modulo->cuestionario()->create([
                        'titulo' => $moduloData['cuestionario']['titulo'],
                        'min_aprobacion' => 80,
                    ]);

                    foreach ($preguntasValidas as $pIdx => $pregunta) {
                        $pregModel = $cuestionario->preguntas()->create([
                            'pregunta' => $pregunta['texto'],
                            'orden' => $pIdx + 1,
                        ]);

                        foreach ($pregunta['opciones'] as $oIdx => $opcion) {
                            if (!empty(trim($opcion['texto']))) {
                                $pregModel->opciones()->create([
                                    'opcion' => $opcion['texto'],
                                    'es_correcta' => $opcion['es_correcta'],
                                    'orden' => $oIdx + 1,
                                ]);
                            }
                        }
                    }
                }
            }

            $evalPreguntasValidas = array_filter($this->evaluacion_final_preguntas, fn($p) => !empty(trim($p['texto'])));
            if (!empty($evalPreguntasValidas)) {
                $evaluacion = $curso->evaluacionFinal()->create([
                    'titulo' => $this->evaluacion_final_titulo,
                    'min_aprobacion' => 80,
                ]);

                foreach ($evalPreguntasValidas as $pIdx => $pregunta) {
                    $pregModel = $evaluacion->preguntas()->create([
                        'pregunta' => $pregunta['texto'],
                        'orden' => $pIdx + 1,
                    ]);

                    foreach ($pregunta['opciones'] as $oIdx => $opcion) {
                        if (!empty(trim($opcion['texto']))) {
                            $pregModel->opciones()->create([
                                'opcion' => $opcion['texto'],
                                'es_correcta' => $opcion['es_correcta'],
                                'orden' => $oIdx + 1,
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            $this->clearSession();
            session()->flash('success', '¡Curso creado correctamente!');
            return redirect()->route('home');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.create-curso');
    }
}
