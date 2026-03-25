<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\ProgresoCurso;
use Illuminate\Http\Request;

class CursoController extends Controller
{
        /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cursos = Curso::with('docente')->get();
        
        $user = auth()->user();
        \Log::info('User role: ' . ($user ? $user->role : 'null'));

        return view('cursos.index', compact('cursos'));
    }
    public function create()
    {
        return view('cursos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
                'modulo_titulo' => 'nullable|string|max:255',
        ]);

        // Crear el curso
        $curso = Curso::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'user_id' => auth()->id(),
        ]);

        // Crear el primer módulo (si se proporcionó título o autogenerado)
        $moduloTitulo = $request->modulo_titulo ?: 'Módulo 1';

        $curso->modulos()->create([
            'titulo' => $moduloTitulo,
            'orden' => 1,
        ]);

        return redirect()->route('home')
                        ->with('success', '¡Curso y módulo inicial creados correctamente!');
    }
    /**
     * Muestra el formulario para editar un curso (solo admin)
     */
    public function edit(Curso $curso)
    {
        // Verificar permiso con la Policy (ya lo tienes registrado)
        $this->authorize('update', $curso);

        // Cargar el curso con sus módulos
        $curso->load('modulos');

        return view('cursos.edit', compact('curso'));
    }

    public function editLivewire(Curso $curso)
    {
        $this->authorize('update', $curso);

        return view('cursos.edit-livewire', compact('curso'));
    }


    public function update(Request $request, Curso $curso)
    {
        $this->authorize('update', $curso);

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'carga_horaria' => 'nullable|numeric|min:0',
            // Validaciones del material (solo si se llenó algo)
            'material_titulo' => 'nullable|string|max:255',
            'material_tipo' => 'nullable|in:pdf,video,cuestionario',
            'material_url' => 'nullable|url',
        ]);

        // Actualizar los campos del curso (solo los permitidos)
        $curso->update([
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'] ?? null,
            'fecha_inicio' => $validated['fecha_inicio'] ?? null,
            'fecha_fin' => $validated['fecha_fin'] ?? null,
            'carga_horaria' => $validated['carga_horaria'] ?? $curso->carga_horaria,
        ]);

        // Si se llenó información de material → agregarlo como nuevo
        if ($request->filled('material_titulo') && $request->filled('material_url') && $request->filled('material_tipo')) {
            $curso->materiales()->create([
                'titulo' => $request->material_titulo,
                'tipo' => $request->material_tipo,
                'url' => $request->material_url,
                'orden' => $curso->materiales()->count() + 1,
            ]);
        }

        return redirect()->route('home')
                        ->with('success', '¡Curso actualizado correctamente!' . ($request->filled('material_titulo') ? ' + material agregado.' : ''));
    }

    public function destroy(Curso $curso)
    {
        $this->authorize('delete', $curso);

        $curso->delete();

        return redirect()->route('home')
                        ->with('success', 'Curso eliminado correctamente!');
    }

    public function reiniciarProgreso(Curso $curso)
    {
        $user = auth()->user();
        
        // Solo admin puede reiniciar
        if ($user->role !== 'admin') {
            abort(403, 'No tienes permiso para esta acción');
        }
        
        // Eliminar progresos de materiales
        $materialIds = $curso->modulos->flatMap(function($modulo) {
            return $modulo->materiales->pluck('id');
        });
        
        \App\Models\ProgresoMaterial::where('user_id', $user->id)
            ->whereIn('material_id', $materialIds)
            ->delete();
        
        // Eliminar resultados de cuestionarios
        $moduloIds = $curso->modulos->pluck('id');
        \App\Models\ResultadoCuestionario::where('user_id', $user->id)
            ->whereIn('modulo_id', $moduloIds)
            ->delete();
        
        // Eliminar resultado de evaluación
        \App\Models\ResultadoEvaluacion::where('user_id', $user->id)
            ->where('curso_id', $curso->id)
            ->delete();
        
        // Reiniciar progreso del curso
        ProgresoCurso::where('user_id', $user->id)
            ->where('curso_id', $curso->id)
            ->update(['modulo_actual' => 1, 'material_actual' => 1, 'estado' => 'en_progreso']);
        
        return redirect()->route('cursos.ver', $curso)
            ->with('success', 'Progreso reiniciado correctamente');
    }

    public function comenzar(Curso $curso)
    {
        $user = auth()->user();

        if ($user->cursoEnProgreso !== null) {
            return redirect()->route('home')
                            ->with('error', 'Solo puedes llevar un curso a la vez. Termina el actual primero.');
        }

        if (ProgresoCurso::where('user_id', $user->id)
                        ->where('curso_id', $curso->id)
                        ->exists()) {
            return redirect()->route('cursos.ver', $curso);
        }

        ProgresoCurso::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'estado' => 'en_progreso',
            'modulo_actual' => 1,
            'material_actual' => 1,
        ]);

        $user->update(['curso_en_progreso_id' => $curso->id]);

        return redirect(route('cursos.ver', $curso) . '?modulo=0&material=0')
                        ->with('success', '¡Has comenzado el curso ' . $curso->titulo . '! Explora los materiales del primer módulo.');
    }

    public function verCurso(Curso $curso)
    {
        $user = auth()->user();

        $progreso = ProgresoCurso::where('user_id', $user->id)
            ->where('curso_id', $curso->id)
            ->first();

        if (!$progreso) {
            return redirect()->route('home')
                ->with('error', 'No estás inscrito en este curso.');
        }

        $curso->load(['modulos.materiales', 'modulos.cuestionario.preguntas.opciones', 'evaluacionFinal.preguntas.opciones']);

        $modulos = $curso->modulos->sortBy('orden');
        
        $moduloIndex = request('modulo', $progreso->modulo_actual - 1);
        $materialIndex = request('material', $progreso->material_actual - 1);
        
        $moduloActual = $modulos->values()->get($moduloIndex);
        $materiales = $moduloActual ? $moduloActual->materiales->sortBy('orden') : collect();
        $materialSeleccionado = $materiales->values()->get($materialIndex) ?? $materiales->first();

        $materialIndex = $materiales->values()->search(fn($m) => $m->id == $materialSeleccionado->id);
        if ($materialIndex === false) {
            $materialIndex = 0;
        }

        return view('cursos.ver-curso', compact('curso', 'user', 'progreso', 'moduloActual', 'materialSeleccionado', 'modulos', 'moduloIndex', 'materialIndex'));
    }

    public function marcarMaterial(Request $request, Curso $curso)
    {
        $materialId = $request->input('material_id');
        $moduloId = $request->input('modulo_id');
        
        \Log::info("marcarMaterial llamado - material_id: $materialId, modulo_id: $moduloId");
        
        if (!$materialId) {
            \Log::error("Error: No se recibió material_id");
            return back()->with('error', 'Error: No se recibió el ID del material');
        }
        
        $user = auth()->user();
        
        try {
            $material = \App\Models\Material::findOrFail($materialId);
            \Log::info("Material encontrado: " . $material->titulo);
        } catch (\Exception $e) {
            \Log::error("Error al buscar material: " . $e->getMessage());
            return back()->with('error', 'Error: Material no encontrado');
        }
        
        // Marcar como completado
        $progreso = \App\Models\ProgresoMaterial::updateOrCreate(
            ['user_id' => $user->id, 'material_id' => $materialId],
            ['completado' => true, 'completado_at' => now()]
        );
        
        \Log::info("Progreso guardado para usuario $user->id, material $materialId");

        $modulo = $material->modulo;
        $todosMateriales = $modulo->materiales->sortBy('orden');
        $materialesCompletados = \App\Models\ProgresoMaterial::where('user_id', $user->id)
            ->whereIn('material_id', $todosMateriales->pluck('id'))
            ->where('completado', true)
            ->count();
        
        $progresoCurso = ProgresoCurso::where('user_id', $user->id)
            ->where('curso_id', $curso->id)
            ->first();

        $moduloIndex = $curso->modulos->sortBy('orden')->search(fn($m) => $m->id == $modulo->id);
        
        $mensaje = "Material marcado como completado. $materialesCompletados de {$todosMateriales->count()} completados.";
        
        if ($materialesCompletados == $todosMateriales->count()) {
            return redirect(route('cursos.ver', $curso) . '?modulo=' . $moduloIndex . '&material=0')
                ->with('success', $mensaje . ' ¡Ahora puedes realizar el cuestionario!');
        } else {
            $currentMaterialIndex = array_search($materialId, $todosMateriales->pluck('id')->toArray());
            
            return redirect(route('cursos.ver', $curso) . '?modulo=' . $moduloIndex . '&material=' . $currentMaterialIndex)
                ->with('success', $mensaje);
        }
    }

    public function verCuestionario(Curso $curso, $moduloId)
    {
        $user = auth()->user();
        $modulo = $curso->modulos()->with('cuestionario.preguntas.opciones')->findOrFail($moduloId);
        $cuestionario = $modulo->cuestionario;

        if (!$cuestionario) {
            return redirect()->route('cursos.ver', $curso)
                ->with('error', 'Este módulo no tiene cuestionario.');
        }

        // Verificar que todos los materiales estén completados
        $materiales = $modulo->materiales;
        $materialesCompletados = \App\Models\ProgresoMaterial::where('user_id', $user->id)
            ->whereIn('material_id', $materiales->pluck('id'))
            ->where('completado', true)
            ->count();

        if ($materialesCompletados != $materiales->count()) {
            return redirect()->route('cursos.ver', $curso)
                ->with('error', 'Debes completar todos los materiales antes de tomar el cuestionario.');
        }

        // Siempre mostrar el cuestionario para permitir reintentos
        // Calcular siguiente módulo
        $modulos = $curso->modulos->sortBy('orden');
        $moduloIndex = $modulos->search(fn($m) => $m->id == $modulo->id);
        $siguienteModuloIndex = $moduloIndex + 1;
        
        $resultado = \App\Models\ResultadoCuestionario::where('user_id', $user->id)
            ->where('modulo_id', $modulo->id)
            ->first();
        
        return view('cursos.ver-cuestionario', compact('curso', 'modulo', 'cuestionario', 'resultado', 'siguienteModuloIndex'));
    }

    public function enviarCuestionario(Request $request, Curso $curso, $moduloId)
    {
        $modulo = $curso->modulos()->findOrFail($moduloId);
        $cuestionario = $modulo->cuestionario;

        if (!$cuestionario) {
            return back()->with('error', 'Este módulo no tiene cuestionario.');
        }

        $respuestas = $request->input('respuestas', []);
        $preguntas = $cuestionario->preguntas;
        $total = $preguntas->count();
        $correctas = 0;

        foreach ($preguntas as $pregunta) {
            $opcionSeleccionada = $respuestas[$pregunta->id] ?? null;
            if ($opcionSeleccionada) {
                $opcion = $pregunta->opciones->firstWhere('id', $opcionSeleccionada);
                if ($opcion && $opcion->es_correcta) {
                    $correctas++;
                }
            }
        }

        $porcentaje = $total > 0 ? round(($correctas / $total) * 100) : 0;
        $aprobado = $porcentaje >= $cuestionario->min_aprobacion;

        \App\Models\ResultadoCuestionario::updateOrCreate(
            ['user_id' => auth()->id(), 'modulo_id' => $modulo->id],
            ['cuestionario_id' => $cuestionario->id, 'nota' => $porcentaje, 'aprobado' => $aprobado, 'completado_at' => now()]
        );

        $progreso = ProgresoCurso::where('user_id', auth()->id())
            ->where('curso_id', $curso->id)
            ->first();

        if ($aprobado) {
            // Actualizar progreso al siguiente módulo
            $modulosOrdenados = $curso->modulos->sortBy('orden');
            $moduloIndexActual = $modulosOrdenados->search(fn($m) => $m->id == $modulo->id);
            $siguienteModuloIndex = $moduloIndexActual + 1;
            
            if ($siguienteModuloIndex < $modulosOrdenados->count()) {
                // Hay siguiente módulo - desbloquearlo
                $progreso->update([
                    'modulo_actual' => $siguienteModuloIndex + 1,
                    'material_actual' => 1,
                ]);
            } else {
                // Es el último módulo - ir a evaluación final si existe
                $progreso->update([
                    'modulo_actual' => $modulosOrdenados->count() + 1,
                    'material_actual' => 1,
                ]);
            }
            
            return redirect(route('cursos.ver', $curso) . '?modulo=' . $siguienteModuloIndex)
                ->with('success', "Aprobaste con $porcentaje%!");
        } else {
            return back()->with('error', "Obtuviste $porcentaje%. Necesitas {$cuestionario->min_aprobacion}% para aprobar. Intenta de nuevo.");
        }
    }

    public function enviarEvaluacion(Request $request, Curso $curso)
    {
        $evaluacion = $curso->evaluacionFinal;

        if (!$evaluacion) {
            return back()->with('error', 'Este curso no tiene evaluación final.');
        }

        $respuestas = $request->input('respuestas', []);
        $preguntas = $evaluacion->preguntas;
        $total = $preguntas->count();
        $correctas = 0;

        foreach ($preguntas as $pregunta) {
            $opcionSeleccionada = $respuestas[$pregunta->id] ?? null;
            if ($opcionSeleccionada) {
                $opcion = $pregunta->opciones->firstWhere('id', $opcionSeleccionada);
                if ($opcion && $opcion->es_correcta) {
                    $correctas++;
                }
            }
        }

        $porcentaje = $total > 0 ? round(($correctas / $total) * 100) : 0;
        $aprobado = $porcentaje >= $evaluacion->min_aprobacion;

        \App\Models\ResultadoEvaluacion::updateOrCreate(
            ['user_id' => auth()->id(), 'curso_id' => $curso->id],
            ['nota' => $porcentaje, 'aprobado' => $aprobado, 'completado_at' => now()]
        );

        if ($aprobado) {
            ProgresoCurso::where('user_id', auth()->id())
                ->where('curso_id', $curso->id)
                ->update(['estado' => 'completado', 'completado_at' => now()]);

            auth()->user()->update(['curso_en_progreso_id' => null]);

            return redirect()->route('home')->with('success', "¡Felicidades! Completaste el curso con $porcentaje%!");
        } else {
            return back()->with('error', "Obtuviste $porcentaje%. Necesitas {$evaluacion->min_aprobacion}% para aprobar.");
        }
    }

}