<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Curso;
use App\Models\Modulo;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;

class CreateCurso extends Component
{
    use WithFileUploads;

    // Control del paso actual
    public $step = 1; // 1: datos básicos, 2: módulos, 3: resumen

    // Paso 1: Datos básicos del curso
    public $titulo = '';
    public $descripcion = '';
    public $imagen_referencial; // archivo subido
    public $carga_horaria = 0;

    // Paso 2: Módulos (array dinámico)
    public $modulos = []; // cada módulo tendrá: titulo, materiales (array), cuestionario

    // Paso 3: Confirmación + evaluación final
    public $confirmado = false;

    // Objetos de evaluación final
    public $evaluacion_final = [
        'titulo' => 'Evaluación Final',
        'min_aprobacion' => 80,
        'preguntas' => [],
    ];

    // Variables de validación
    protected $rules = [
        'titulo' => 'required|string|min:3|max:255',
        'descripcion' => 'nullable|string|max:1000',
        'carga_horaria' => 'required|numeric|min:1',
        'imagen_referencial' => 'nullable|image|max:2048',
        'modulos' => 'required|array|min:1',
        'modulos.*.titulo' => 'required|string|min:3|max:255',
        'modulos.*.materiales' => 'required|array|min:1',
        'modulos.*.materiales.*.titulo' => 'required|string|max:255',
        'modulos.*.materiales.*.tipo' => 'required|in:pdf,video,cuestionario',
        'modulos.*.materiales.*.url' => 'required|string',
                'evaluacion_final.titulo' => 'required|string|max:255',
        'evaluacion_final.min_aprobacion' => 'required|numeric|min:0|max:100',
        'evaluacion_final.preguntas' => 'required|array|min:1',
    ];

    protected $messages = [
        'titulo.required' => 'El título del curso es obligatorio',
        'titulo.min' => 'El título debe tener al menos 3 caracteres',
        'carga_horaria.required' => 'La carga horaria es obligatoria',
        'carga_horaria.numeric' => 'La carga horaria debe ser un número',
        'carga_horaria.min' => 'La carga horaria debe ser mayor a 0',
        'modulos.required' => 'Debe agregar al menos un módulo',
        'modulos.*.titulo.required' => 'El título del módulo es obligatorio',
        'modulos.*.materiales.required' => 'Cada módulo debe tener al menos un material',
        'modulos.*.materiales.*.titulo.required' => 'El título del material es obligatorio',
        'modulos.*.materiales.*.url.required' => 'La URL del material es obligatoria',
        'evaluacion_final.titulo.required' => 'El título de la evaluación final es obligatorio',
        'evaluacion_final.min_aprobacion.required' => 'El mínimo de aprobación es obligatorio',
        'evaluacion_final.preguntas.required' => 'Debes agregar al menos una pregunta a la evaluación final',
        'imagen_referencial.image' => 'El archivo debe ser una imagen',
    ];

    public function mount()
    {
        // Verificar autorización usando la Policy
        if (!Auth::check() || !Auth::user()->can('create', Curso::class)) {
            redirect()->route('home')->with('error', 'No tienes permiso para crear cursos');
        }

        // Iniciar con 1 módulo vacío
        $this->addModulo();
    }

    public function addModulo()
    {
        $this->modulos[] = [
            'titulo' => '',
            'orden' => count($this->modulos) + 1,
            'materiales' => [],
            'cuestionario' => ['preguntas' => []],
        ];
    }

    public function addMaterial($moduloIndex)
    {
        $this->modulos[$moduloIndex]['materiales'][] = [
            'titulo' => '',
            'tipo' => 'pdf',
            'url' => '',
        ];
    }

    public function removeMaterial($moduloIndex, $materialIndex)
    {
        unset($this->modulos[$moduloIndex]['materiales'][$materialIndex]);
        $this->modulos[$moduloIndex]['materiales'] = array_values($this->modulos[$moduloIndex]['materiales']);
    }

    public function addQuestion($moduloIndex)
    {
        $this->modulos[$moduloIndex]['cuestionario']['preguntas'][] = ['texto' => ''];
    }

    public function removeQuestion($moduloIndex, $questionIndex)
    {
        unset($this->modulos[$moduloIndex]['cuestionario']['preguntas'][$questionIndex]);
        $this->modulos[$moduloIndex]['cuestionario']['preguntas'] = array_values($this->modulos[$moduloIndex]['cuestionario']['preguntas']);
    }

    public function removeModulo($index)
    {
        if (count($this->modulos) > 1) {
            unset($this->modulos[$index]);
            $this->modulos = array_values($this->modulos); // Reindexar
        } else {
            // informar al usuario que necesita al menos un módulo
            $this->addError('modulos', 'Debe haber al menos un módulo');
        }
    }

    public function nextStep()
    {
        // Validar datos del paso actual
        if ($this->step == 1) {
            // validar cada campo individualmente (no se permite un array)
            $this->validateOnly('titulo');
            $this->validateOnly('carga_horaria');
        } elseif ($this->step == 2) {
            $this->validateOnly('modulos');
            // check modules have at least one material to allow quiz creation
            // nothing else here; detailed validation runs on save
        }

        if ($this->step < 3) {
            // solamente avanzar a paso 3 si hay al menos un módulo completo
            if ($this->step == 2 && !$this->canProceedToEvaluation()) {
                $this->addError('modulos', 'Debes completar al menos un módulo con material y cuestionario antes de continuar');
                return;
            }
            $this->step++;
        }
    }

    public function canProceedToEvaluation()
    {
        foreach ($this->modulos as $modulo) {
            if (!empty($modulo['materiales']) && count($modulo['materiales']) >= 1 &&
                !empty($modulo['cuestionario']['preguntas']) &&
                count($modulo['cuestionario']['preguntas']) >= 1
            ) {
                return true;
            }
        }

        return false;
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function save()
    {
        // Validar todos los datos
        $this->validate();

        // Requisito adicional: al menos un módulo debe tener material y cuestionario
        if (! $this->canProceedToEvaluation()) {
            $this->addError('modulos', 'Debes completar al menos un módulo con material y cuestionario antes de guardar el curso');
            return;
        }

        try {
            // Crear el curso
            $curso = Curso::create([
                'titulo' => $this->titulo,
                'descripcion' => $this->descripcion,
                'carga_horaria' => $this->carga_horaria,
                'user_id' => Auth::id(),
            ]);

            // Guardar imagen si se subió
            if ($this->imagen_referencial) {
                $path = $this->imagen_referencial->store('cursos', 'public');
                $curso->update(['imagen_referencial' => $path]);
            }

            // Crear módulos, materiales y (opcional) cuestionarios
            foreach ($this->modulos as $index => $moduloData) {
                $modulo = $curso->modulos()->create([
                    'titulo' => $moduloData['titulo'],
                    'orden' => $index + 1,
                ]);

                // materiales
                if (!empty($moduloData['materiales'])) {
                    foreach ($moduloData['materiales'] as $mIndex => $material) {
                        $modulo->materiales()->create([
                            'titulo' => $material['titulo'] ?? 'Material ' . ($mIndex + 1),
                            'tipo' => $material['tipo'] ?? 'pdf',
                            'url' => $material['url'] ?? '',
                            'orden' => $mIndex + 1,
                        ]);
                    }
                }

                // TODO: guardar cuestionario en alguna tabla o procesar según la lógica de la aplicación
            }

            // Limpiar datos
            $this->reset();
            $this->step = 1;
            $this->addModulo();

            return redirect()->route('home')->with('success', '¡Curso creado correctamente! Ahora puedes agregar materiales y contenido a los módulos.');
        } catch (\Exception $e) {
            \Log::error('Error al crear curso: ' . $e->getMessage());
            // mostrar mensaje flash para que el usuario lo vea
            session()->flash('error', 'Error al crear el curso: ' . $e->getMessage());
        }
    }

    public function addEvalQuestion()
    {
        $this->evaluacion_final['preguntas'][] = '';
    }

    public function removeEvalQuestion($index)
    {
        unset($this->evaluacion_final['preguntas'][$index]);
        $this->evaluacion_final['preguntas'] = array_values($this->evaluacion_final['preguntas']);
    }

    public function render()
    {
        return view('livewire.create-curso');
    }
}