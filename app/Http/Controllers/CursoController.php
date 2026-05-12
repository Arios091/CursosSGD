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

    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = in_array($user->role, ['admin', 'admin_global']);
        
        if ($isAdmin) {
            $query = Curso::with(['docente', 'modulos', 'progresos']);
            
            // Búsqueda por título (case insensitive)
            if ($request->has('search') && $request->search) {
                $search = mb_strtolower($request->search);
                $query->where(function($q) use ($search) {
                    $q->whereRaw('LOWER(titulo) LIKE ?', ['%' . $search . '%']);
                });
            }
            
            // Filtro por estado
            if ($request->has('estado') && $request->estado) {
                if ($request->estado === 'con_contenido') {
                    $query->whereHas('modulos', function($q) {
                        $q->whereHas('materiales');
                    });
                } elseif ($request->estado === 'sin_contenido') {
                    $query->whereDoesntHave('modulos.materiales');
                }
            }
            
            // Ordenar por
            $sortBy = $request->get('sort', 'latest');
            if ($sortBy === 'oldest') {
                $query->oldest();
            } elseif ($sortBy === 'title') {
                $query->orderBy('titulo', 'asc');
            } else {
                $query->latest();
            }
            
            $cursos = $query->paginate(12)->withQueryString();
        } else {
            $cursos = collect();
        }

        return view('cursos.index', compact('cursos', 'isAdmin'));
    }
    
    public function home()
    {
        return view('home');
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

        // Legacy method - course editing is now handled by Livewire EditCurso component
        // This method is kept for backward compatibility but redirects to Livewire
        return redirect()->route('cursos.edit', $curso);
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
        if (!in_array($user->role, ['admin', 'admin_global'])) {
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
        $isJson = request()->ajax() || request()->wantsJson();

        // Verificar si el usuario tiene algún curso en progreso
        $cursoEnProgreso = ProgresoCurso::where('user_id', $user->id)
            ->where('estado', 'en_progreso')
            ->first();

        if ($cursoEnProgreso) {
            $cursoActual = Curso::find($cursoEnProgreso->curso_id);
            $mensaje = 'Solo puedes llevar un curso a la vez. Estás llevando "' . ($cursoActual ? $cursoActual->titulo : 'un curso') . '". Termínalo primero.';
            
            if ($isJson) {
                return response()->json(['error' => $mensaje]);
            }
            
            return redirect()->route('home')->with('error', $mensaje);
        }

        // Verificar si ya tiene progreso en este curso
        $progresoExistente = ProgresoCurso::where('user_id', $user->id)
            ->where('curso_id', $curso->id)
            ->first();

        if ($progresoExistente) {
            $url = in_array($progresoExistente->estado, ['completado', 'terminado'])
                ? route('cursos.completado', $curso)
                : route('cursos.ver', $curso);
            return $isJson ? response()->json(['redirect' => $url]) : redirect($url);
        }

        ProgresoCurso::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'estado' => 'en_progreso',
            'modulo_actual' => 1,
            'material_actual' => 1,
        ]);

        $user->update(['curso_en_progreso_id' => $curso->id]);

        $url = route('cursos.ver', $curso) . '?modulo=0&material=0';
        return $isJson ? response()->json(['redirect' => $url]) : redirect($url)->with('success', '¡Has comenzado el curso ' . $curso->titulo . '! Explora los materiales del primer módulo.');
    }

    public function verCurso(Curso $curso)
    {
        $user = auth()->user();
        $isAdmin = in_array($user->role, ['admin', 'admin_global']);

        if ($isAdmin) {
            $progreso = null;
        } else {
            $progreso = ProgresoCurso::where('user_id', $user->id)
                ->where('curso_id', $curso->id)
                ->first();

            if (!$progreso) {
                return redirect()->route('home')
                    ->with('error', 'No estás inscrito en este curso.');
            }
            
            if (in_array($progreso->estado, ['completado', 'terminado'])) {
                return redirect()->route('cursos.completado', $curso);
            }
        }

        $curso->load(['modulos.materiales', 'modulos.cuestionario.preguntas.opciones', 'evaluacionFinal.preguntas.opciones']);

        $modulos = $curso->modulos->sortBy('orden')->values();
        
        // Get requested module/material by ID from query params
        $requestedModuloId = request('modulo_id');
        $requestedMaterialId = request('material_id');
        
        // Find module index
        if ($requestedModuloId) {
            $moduloIndex = $modulos->search(fn($m) => $m->id == $requestedModuloId);
            if ($moduloIndex === false) $moduloIndex = 0;
        } elseif ($progreso && !request('modulo')) {
            $moduloIndex = max(0, min($progreso->modulo_actual - 1, $modulos->count() - 1));
        } else {
            $moduloIndex = max(0, (int) request('modulo', 0));
        }
        
        if ($moduloIndex >= $modulos->count()) $moduloIndex = 0;
        
        $moduloActual = $modulos->get($moduloIndex);
        if (!$moduloActual && $modulos->count() > 0) {
            $moduloActual = $modulos->first();
            $moduloIndex = 0;
        }
        
        $materiales = $moduloActual ? $moduloActual->materiales->sortBy('orden')->values() : collect();
        
        // Find material index
        if ($requestedMaterialId) {
            $materialIndex = $materiales->search(fn($m) => $m->id == $requestedMaterialId);
            if ($materialIndex === false) $materialIndex = 0;
        } elseif ($progreso && !request('material_id') && !request('material')) {
            $materialIndex = max(0, $progreso->material_actual - 1);
        } else {
            $materialIndex = max(0, (int) request('material', 0));
        }
        
        if ($materialIndex >= $materiales->count()) $materialIndex = 0;
        
        $materialSeleccionado = $materiales->get($materialIndex) ?? $materiales->first();
        if (!$materialSeleccionado && $materiales->count() > 0) {
            $materialSeleccionado = $materiales->first();
            $materialIndex = 0;
        }

        return view('cursos.ver-curso', compact('curso', 'user', 'progreso', 'moduloActual', 'materialSeleccionado', 'modulos', 'moduloIndex', 'materialIndex', 'materiales', 'isAdmin'));
    }

    public function marcarMaterial(Request $request, Curso $curso)
    {
        $materialId = $request->input('material_id');
        $moduloId = $request->input('modulo_id');
        
        if (!$materialId) {
            return back()->with('error', 'Error: No se recibió el ID del material');
        }
        
        $user = auth()->user();
        $material = \App\Models\Material::findOrFail($materialId);
        
        // Marcar como completado
        \App\Models\ProgresoMaterial::updateOrCreate(
            ['user_id' => $user->id, 'material_id' => $materialId],
            ['material_completado' => true, 'completado_at' => now()]
        );

        $modulo = $material->modulo;
        $todosMateriales = $modulo->materiales->sortBy('orden')->values();
        $materialesCompletados = \App\Models\ProgresoMaterial::where('user_id', $user->id)
            ->whereIn('material_id', $todosMateriales->pluck('id'))
            ->where('material_completado', true)
            ->count();
        
        // Find current material index in the sorted collection
        $currentMaterialIndex = -1;
        foreach ($todosMateriales as $i => $m) {
            if ($m->id == $materialId) {
                $currentMaterialIndex = $i;
                break;
            }
        }
        
        $moduloIndex = $curso->modulos->sortBy('orden')->values()->search(fn($m) => $m->id == $modulo->id);
        
        // If all materials in this module are done, go back to module start
        if ($materialesCompletados >= $todosMateriales->count()) {
            return redirect()->route('cursos.ver', [$curso, 'modulo' => $moduloIndex, 'material' => 0])
                ->with('success', '¡Has completado todos los materiales! Ahora puedes realizar el cuestionario del módulo.');
        }
        
        // Go to next material
        $nextMaterialIndex = $currentMaterialIndex + 1;
        
        return redirect()->route('cursos.ver', [$curso, 'modulo' => $moduloIndex, 'material' => $nextMaterialIndex])
            ->with('success', 'Material completado. Continúa con el siguiente.');
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
            ->where('material_completado', true)
            ->count();

        if ($materialesCompletados != $materiales->count()) {
            return redirect()->route('cursos.ver', $curso)
                ->with('error', 'Debes completar todos los materiales antes de tomar el cuestionario.');
        }

        // Obtener último resultado para mostrar feedback
        $resultado = \App\Models\ResultadoCuestionario::where('user_id', $user->id)
            ->where('modulo_id', $modulo->id)
            ->latest()
            ->first();

        // Calcular siguiente módulo
        $modulos = $curso->modulos->sortBy('orden');
        $moduloIndex = $modulos->search(fn($m) => $m->id == $modulo->id);
        $siguienteModuloIndex = $moduloIndex + 1;
        
        return view('cursos.cuestionario', compact('curso', 'modulo', 'cuestionario', 'resultado', 'siguienteModuloIndex'));
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
        
        // Detalle de cada respuesta para feedback
        $detalleRespuestas = [];

        foreach ($preguntas as $pregunta) {
            $opcionSeleccionada = $respuestas[$pregunta->id] ?? null;
            $opcionCorrecta = $pregunta->opciones->firstWhere('es_correcta', true);
            $esCorrecta = false;
            
            if ($opcionSeleccionada) {
                $opcion = $pregunta->opciones->firstWhere('id', $opcionSeleccionada);
                if ($opcion && $opcion->es_correcta) {
                    $correctas++;
                    $esCorrecta = true;
                }
            }
            
            $detalleRespuestas[] = [
                'pregunta' => $pregunta,
                'opcion_seleccionada_id' => $opcionSeleccionada,
                'opcion_correcta_id' => $opcionCorrecta ? $opcionCorrecta->id : null,
                'es_correcta' => $esCorrecta,
            ];
        }

        $porcentaje = $total > 0 ? round(($correctas / $total) * 100) : 0;
        $aprobado = $porcentaje == 100; // 100% para aprobar

        \App\Models\ResultadoCuestionario::updateOrCreate(
            ['user_id' => auth()->id(), 'modulo_id' => $modulo->id],
            ['cuestionario_id' => $cuestionario->id, 'nota' => $porcentaje, 'aprobado' => $aprobado, 'completado_at' => now()]
        );

        $progreso = ProgresoCurso::where('user_id', auth()->id())
            ->where('curso_id', $curso->id)
            ->first();

        if ($aprobado) {
            $modulosOrdenados = $curso->modulos->sortBy('orden');
            $moduloIndexActual = $modulosOrdenados->search(fn($m) => $m->id == $modulo->id);
            $siguienteModuloIndex = $moduloIndexActual + 1;
            
            if ($siguienteModuloIndex < $modulosOrdenados->count()) {
                $progreso->update([
                    'modulo_actual' => $siguienteModuloIndex + 1,
                    'material_actual' => 1,
                ]);
            } else {
                $progreso->update([
                    'modulo_actual' => $modulosOrdenados->count() + 1,
                    'material_actual' => 1,
                ]);
            }
            
            return redirect()->route('cursos.cuestionario.resultado', [$curso, $modulo->id])
                ->with('success', "¡Aprobaste con $porcentaje%!");
        }
        
        // Reprobado - mostrar feedback y permitir reintento
        return redirect()->route('cursos.cuestionario.resultado', [$curso, $modulo->id])
            ->with('error', "Obtuviste $porcentaje%. Necesitas 100% para aprobar. Intenta de nuevo.");
    }

    public function verResultadoCuestionario(Curso $curso, $moduloId)
    {
        $user = auth()->user();
        $modulo = $curso->modulos()->with('cuestionario.preguntas.opciones')->findOrFail($moduloId);
        $cuestionario = $modulo->cuestionario;
        
        if (!$cuestionario) {
            return redirect()->route('cursos.ver', $curso);
        }

        $resultado = \App\Models\ResultadoCuestionario::where('user_id', $user->id)
            ->where('modulo_id', $modulo->id)
            ->latest()
            ->first();

        if (!$resultado) {
            return redirect()->route('cursos.cuestionario.ver', [$curso, $modulo->id]);
        }

        $modulos = $curso->modulos->sortBy('orden');
        $moduloIndex = $modulos->search(fn($m) => $m->id == $modulo->id);
        $siguienteModuloIndex = $moduloIndex + 1;

        return view('cursos.cuestionario-resultado', compact('curso', 'modulo', 'cuestionario', 'resultado', 'siguienteModuloIndex'));
    }

    public function verEvaluacionFinal(Curso $curso)
    {
        $user = auth()->user();
        
        $progreso = ProgresoCurso::where('user_id', $user->id)
            ->where('curso_id', $curso->id)
            ->first();

        if (!$progreso) {
            return redirect()->route('home')->with('error', 'No estás inscrito en este curso.');
        }

        if (in_array($progreso->estado, ['completado', 'terminado'])) {
            return redirect()->route('cursos.completado', $curso);
        }

        $evaluacion = $curso->evaluacionFinal;
        
        if (!$evaluacion) {
            return redirect()->route('cursos.ver', $curso)->with('error', 'Este curso no tiene evaluación final.');
        }

        // Verificar que todos los módulos y cuestionarios estén completados
        $modulos = $curso->modulos->sortBy('orden');
        foreach ($modulos as $modulo) {
            $materialesCompletados = \App\Models\ProgresoMaterial::where('user_id', $user->id)
                ->whereIn('material_id', $modulo->materiales->pluck('id'))
                ->where('material_completado', true)
                ->count();
            
            if ($materialesCompletados < $modulo->materiales->count()) {
                return redirect()->route('cursos.ver', $curso)
                    ->with('error', 'Debes completar todos los materiales de todos los módulos.');
            }
            
            if ($modulo->cuestionario) {
                $quizAprobado = \App\Models\ResultadoCuestionario::where('user_id', $user->id)
                    ->where('modulo_id', $modulo->id)
                    ->where('aprobado', true)
                    ->exists();
                
                if (!$quizAprobado) {
                    return redirect()->route('cursos.ver', $curso)
                        ->with('error', 'Debes aprobar todos los cuestionarios de los módulos.');
                }
            }
        }

        // Obtener último resultado
        $resultado = \App\Models\ResultadoEvaluacion::where('user_id', $user->id)
            ->where('curso_id', $curso->id)
            ->latest()
            ->first();

        return view('cursos.evaluacion-final', compact('curso', 'evaluacion', 'resultado'));
    }

    public function enviarEvaluacionFinal(Request $request, Curso $curso)
    {
        $evaluacion = $curso->evaluacionFinal;

        if (!$evaluacion) {
            return back()->with('error', 'Este curso no tiene evaluación final.');
        }

        $respuestas = $request->input('respuestas', []);
        $preguntas = $evaluacion->preguntas;
        $total = $preguntas->count();
        $correctas = 0;
        
        $detalleRespuestas = [];

        foreach ($preguntas as $pregunta) {
            $opcionSeleccionada = $respuestas[$pregunta->id] ?? null;
            $opcionCorrecta = $pregunta->opciones->firstWhere('es_correcta', true);
            $esCorrecta = false;
            
            if ($opcionSeleccionada) {
                $opcion = $pregunta->opciones->firstWhere('id', $opcionSeleccionada);
                if ($opcion && $opcion->es_correcta) {
                    $correctas++;
                    $esCorrecta = true;
                }
            }
            
            $detalleRespuestas[] = [
                'pregunta' => $pregunta,
                'opcion_seleccionada_id' => $opcionSeleccionada,
                'opcion_correcta_id' => $opcionCorrecta ? $opcionCorrecta->id : null,
                'es_correcta' => $esCorrecta,
            ];
        }

        $porcentaje = $total > 0 ? round(($correctas / $total) * 100) : 0;
        $aprobado = $porcentaje >= 80; // 80% para aprobar

        \App\Models\ResultadoEvaluacion::updateOrCreate(
            ['user_id' => auth()->id(), 'curso_id' => $curso->id],
            ['nota' => $porcentaje, 'aprobado' => $aprobado, 'completado_at' => now()]
        );

        if ($aprobado) {
            ProgresoCurso::where('user_id', auth()->id())
                ->where('curso_id', $curso->id)
                ->update([
                    'estado' => 'completado',
                    'evaluacion_aprobada' => true,
                    'completado_at' => now(),
                    'fecha_fin' => now(),
                ]);

            auth()->user()->update(['curso_en_progreso_id' => null]);

            return redirect()->route('cursos.completado', $curso)
                ->with('success', "¡Felicidades! Completaste el curso con $porcentaje%!");
        }

        return redirect()->route('cursos.evaluacion-final.resultado', $curso)
            ->with('error', "Obtuviste $porcentaje%. Necesitas 80% para aprobar. Intenta de nuevo.");
    }

    public function verResultadoEvaluacionFinal(Curso $curso)
    {
        $user = auth()->user();
        $evaluacion = $curso->evaluacionFinal;
        
        if (!$evaluacion) {
            return redirect()->route('home');
        }

        $resultado = \App\Models\ResultadoEvaluacion::where('user_id', $user->id)
            ->where('curso_id', $curso->id)
            ->latest()
            ->first();

        if (!$resultado) {
            return redirect()->route('cursos.evaluacion-final', $curso);
        }

        return view('cursos.evaluacion-resultado', compact('curso', 'evaluacion', 'resultado'));
    }

    public function verCertificado(Curso $curso)
    {
        $user = auth()->user();
        
        $progreso = \App\Models\ProgresoCurso::where('user_id', $user->id)
            ->where('curso_id', $curso->id)
            ->whereIn('estado', ['completado', 'terminado'])
            ->first();
            
        if (!$progreso) {
            return redirect()->route('home')
                ->with('error', 'No puedes acceder a este certificado.');
        }
        
        return redirect()->route('cursos.completado', $curso);
    }

    public function verCertificadoView(Curso $curso)
    {
        $user = auth()->user();
        
        $progreso = \App\Models\ProgresoCurso::where('user_id', $user->id)
            ->where('curso_id', $curso->id)
            ->whereIn('estado', ['completado', 'terminado'])
            ->first();
            
        if (!$progreso) {
            return redirect()->route('home')
                ->with('error', 'No puedes acceder a este certificado.');
        }
        
        $fechaCompletado = $progreso->completado_at 
            ? $progreso->completado_at->format('d') . ' de ' . $progreso->completado_at->locale('es')->monthName . ' del ' . $progreso->completado_at->format('Y') 
            : now()->format('d/m/Y');
        $year = $progreso->completado_at ? $progreso->completado_at->format('Y') : now()->format('Y');
        $numeroCertificado = 'UNAS-CERT-' . strtoupper(substr($curso->titulo, 0, 4)) . '-' . str_pad($progreso->id, 6, '0', STR_PAD_LEFT) . '-' . $year;
        
        return view('certificado', compact('curso', 'user', 'progreso', 'fechaCompletado', 'numeroCertificado'));
    }

    public function descargarCertificado(Curso $curso)
    {
        $user = auth()->user();
        
        $progreso = \App\Models\ProgresoCurso::where('user_id', $user->id)
            ->where('curso_id', $curso->id)
            ->whereIn('estado', ['completado', 'terminado'])
            ->first();
            
        if (!$progreso) {
            return redirect()->route('home')
                ->with('error', 'No puedes descargar este certificado.');
        }
        
        $fechaCompletado = $progreso->completado_at 
            ? $progreso->completado_at->format('d') . ' de ' . $progreso->completado_at->locale('es')->monthName . ' del ' . $progreso->completado_at->format('Y') 
            : now()->format('d/m/Y');
        $year = $progreso->completado_at ? $progreso->completado_at->format('Y') : now()->format('Y');
        $numeroCertificado = 'UNAS-CERT-' . strtoupper(substr($curso->titulo, 0, 4)) . '-' . str_pad($progreso->id, 6, '0', STR_PAD_LEFT) . '-' . $year;
        
        $pdf = \PDF::loadView('certificado-pdf', compact('curso', 'user', 'progreso', 'fechaCompletado', 'numeroCertificado'));
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('Certificado_' . str_replace(' ', '_', $curso->titulo) . '_' . str_replace(' ', '_', $user->name) . '.pdf');
    }

    public function cursoCompletado(Curso $curso)
    {
        $user = auth()->user();

        $progreso = ProgresoCurso::where('user_id', $user->id)
            ->where('curso_id', $curso->id)
            ->whereIn('estado', ['completado', 'terminado'])
            ->first();

        if (!$progreso) {
            return redirect()->route('home')
                ->with('error', 'No puedes acceder a esta página porque el curso no está completado.');
        }

        return view('cursos.completado', compact('curso', 'progreso'));
    }

    public function verificarCertificado($codigo)
    {
        $progreso = ProgresoCurso::whereHas('curso', function($q) use ($codigo) {
            $q->whereRaw("CONCAT('UNAS-CERT-', UPPER(SUBSTRING(titulo, 1, 4)), '-', LPAD(progreso_cursos.id, 6, '0'), '-', EXTRACT(YEAR FROM progreso_cursos.completado_at)) = ?", [$codigo]);
        })->with(['user', 'curso'])->first();

        if (!$progreso) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Certificado no encontrado'
            ]);
        }

        return response()->json([
            'valido' => true,
            'mensaje' => 'Certificado autenticado',
            'datos' => [
                'nombre' => $progreso->user->name,
                'email' => $progreso->user->email,
                'curso' => $progreso->curso->titulo,
                'carga_horaria' => $progreso->curso->carga_horaria,
                'fecha_completado' => $progreso->completado_at ? $progreso->completado_at->format('d/m/Y') : null,
                'codigo' => $codigo
            ]
        ]);
    }
}