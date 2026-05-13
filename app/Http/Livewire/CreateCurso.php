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
use Illuminate\Support\Facades\Storage;

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

    // Track temp file paths
    protected $tempFiles = [];

    protected $rules = [
        'titulo' => 'required|string|min:3|max:255',
        'carga_horaria' => 'required|numeric|min:1|max:500',
    ];

    public function mount()
    {
        if (!in_array(auth()->user()->role, ['admin', 'admin_global'])) {
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
        // Handle file uploads immediately - store temp file
        if (str_contains($property, '.archivo') && $value instanceof \Livewire\TemporaryUploadedFile) {
            $this->handleFileUpload($property, $value);
        }
        
        if ($property !== 'imagen_referencial' && !str_contains($property, '.archivo')) {
            $this->saveToSession();
        }
    }

    protected function handleFileUpload($property, $file)
    {
        // Parse property to find module and material index
        // Format: modulos.0.materiales.1.archivo
        preg_match('/modulos\.(\d+)\.materiales\.(\d+)\.archivo/', $property, $matches);
        
        if (count($matches) === 3) {
            $moduloIndex = (int) $matches[1];
            $materialIndex = (int) $matches[2];
            
            // Store file temporarily
            $tempName = 'curso_temp_' . uniqid() . '_' . $file->getClientOriginalName();
            $tempPath = $file->storeAs('temp', $tempName, 'public');
            
            // Update the module data with temp path
            $this->modulos[$moduloIndex]['materiales'][$materialIndex]['archivo'] = null;
            $this->modulos[$moduloIndex]['materiales'][$materialIndex]['temp_path'] = $tempPath;
            $this->modulos[$moduloIndex]['materiales'][$materialIndex]['original_name'] = $file->getClientOriginalName();
            
            $this->saveToSession();
        }
    }

    protected function saveToSession()
    {
        $modulosData = array_map(function($modulo) {
            $modulo['materiales'] = array_map(function($material) {
                // Remove Livewire uploaded file object, keep temp_path
                if (isset($material['archivo']) && $material['archivo'] instanceof \Livewire\TemporaryUploadedFile) {
                    unset($material['archivo']);
                }
                return $material;
            }, $modulo['materiales'] ?? []);
            return $modulo;
        }, $this->modulos);

        Session::put('curso_creating', [
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'carga_horaria' => $this->carga_horaria,
            'modulos' => $modulosData,
            'evaluacion_final_titulo' => $this->evaluacion_final_titulo,
            'evaluacion_final_preguntas' => $this->evaluacion_final_preguntas,
            'step' => $this->step,
        ]);
    }

    public function clearSession()
    {
        Session::forget('curso_creating');
        // Clean temp files
        $files = Storage::disk('public')->files('temp');
        foreach ($files as $file) {
            if (str_starts_with(basename($file), 'curso_temp_')) {
                Storage::disk('public')->delete($file);
            }
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

    public function agregarModulo()
    {
        $numero = count($this->modulos) + 1;
        $this->modulos[] = [
            'titulo' => '',
            'materiales' => [
                ['titulo' => '', 'tipo' => 'pdf', 'url' => '', 'temp_path' => null, 'original_name' => null]
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
            // Clean temp file if exists
            $tempPath = $this->modulos[$index]['materiales'][0]['temp_path'] ?? null;
            if ($tempPath) {
                Storage::disk('public')->delete($tempPath);
            }
            unset($this->modulos[$index]);
            $this->modulos = array_values($this->modulos);
        }
    }

    public function agregarMaterial($moduloIndex)
    {
        $this->modulos[$moduloIndex]['materiales'][] = ['titulo' => '', 'tipo' => 'pdf', 'url' => '', 'temp_path' => null, 'original_name' => null];
    }

    public function eliminarMaterial($moduloIndex, $materialIndex)
    {
        if (count($this->modulos[$moduloIndex]['materiales']) > 1) {
            // Clean temp file if exists
            $tempPath = $this->modulos[$moduloIndex]['materiales'][$materialIndex]['temp_path'] ?? null;
            if ($tempPath) {
                Storage::disk('public')->delete($tempPath);
            }
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
            return false;
        }

        foreach ($this->modulos as $idx => $modulo) {
            if (empty(trim($modulo['titulo']))) {
                return false;
            }
            $tieneMaterial = false;
            foreach ($modulo['materiales'] as $mIdx => $material) {
                $titulo = $material['titulo'] ?? '';
                $tipo = $material['tipo'] ?? 'pdf';
                $url = $material['url'] ?? '';
                $tempPath = $material['temp_path'] ?? null;
                
                if (!empty(trim($titulo))) {
                    if ($tipo === 'video' && !empty(trim($url))) {
                        $tieneMaterial = true;
                    }
                    if ($tipo === 'pdf' && !empty($tempPath)) {
                        $tieneMaterial = true;
                    }
                }
            }
            if (!$tieneMaterial) {
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

        return $evalValida;
    }

    public function guardar()
    {
        if (!$this->puedeCrear()) {
            session()->flash('error', 'Completa todos los campos requeridos.');
            return;
        }

        try {
            DB::beginTransaction();

            $curso = Curso::create([
                'titulo' => $this->titulo,
                'descripcion' => $this->descripcion,
                'carga_horaria' => $this->carga_horaria,
                'user_id' => Auth::id(),
            ]);

            // Handle course image
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
                    if (empty(trim($material['titulo'] ?? ''))) {
                        continue;
                    }

                    $materialUrl = null;
                    $tipo = $material['tipo'] ?? 'pdf';
                    
                    // Para videos - SOLO YouTube permitido
                    if ($tipo === 'video' && !empty(trim($material['url'] ?? ''))) {
                        $rawUrl = trim($material['url']);
                        
                        // Extraer ID de YouTube y guardar en formato embed
                        $youtubeId = $this->extractYouTubeId($rawUrl);
                        if ($youtubeId) {
                            $materialUrl = 'https://www.youtube.com/embed/' . $youtubeId;
                        } else {
                            // No es YouTube - rechazar
                            session()->flash('error', 'Solo se permiten videos de YouTube. URL inválida: ' . $rawUrl);
                            DB::rollBack();
                            return;
                        }
                    }
                    // Para PDFs - verificar que el archivo temporal exista antes de mover
                    elseif ($tipo === 'pdf' && !empty($material['temp_path'] ?? '')) {
                        $tempPath = $material['temp_path'];
                        
                        // Verificar que el archivo temporal existe
                        if (Storage::disk('public')->exists($tempPath)) {
                            $originalName = $material['original_name'] ?? 'material.pdf';
                            // Sanitize filename - solo caracteres seguros
                            $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
                            $filename = time() . '_' . $curso->id . '_m' . ($idx + 1) . '_' . $mIdx . '_' . $safeName;
                            
                            Storage::disk('public')->move($tempPath, 'materiales/' . $filename);
                            $materialUrl = 'materiales/' . $filename;
                        } else {
                            // Log de error para debugging
                            \Illuminate\Support\Facades\Log::warning('Archivo PDF no encontrado en temp: ' . $tempPath);
                        }
                    }

                    // Solo crear material si tenemos una URL válida
                    if ($materialUrl) {
                        $modulo->materiales()->create([
                            'titulo' => $material['titulo'],
                            'tipo' => $tipo,
                            'url' => $materialUrl,
                            'orden' => $mIdx + 1,
                        ]);
                    } else {
                        // Crear material con indicador de que no tiene archivo
                        $modulo->materiales()->create([
                            'titulo' => $material['titulo'],
                            'tipo' => $tipo,
                            'url' => null,
                            'orden' => $mIdx + 1,
                        ]);
                    }
                }

                // Module quiz
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
                                    'es_correcta' => $opcion['es_correcta'] ? 'true' : 'false',
                                    'orden' => $oIdx + 1,
                                ]);
                            }
                        }
                    }
                }
            }

            // Final evaluation
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
                                'es_correcta' => $opcion['es_correcta'] ? 'true' : 'false',
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
            Log::error('Error creating course: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.create-curso');
    }
}
