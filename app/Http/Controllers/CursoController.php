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
        $cursos = Curso::with('docente')->get(); // Trae todos los cursos + el nombre del docente

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

    public function comenzar(Curso $curso)
    {
        \Log::info('Intentando comenzar curso ID: ' . $curso->id . ' por usuario ID: ' . auth()->id());    

        $user = auth()->user();

        // 1. Verificar si ya tiene cualquier curso en progreso
        if ($user->cursoEnProgreso !== null) {
            return redirect()->route('home')
                            ->with('error', 'Solo puedes llevar un curso a la vez. Termina el actual primero.');
        }

        // 2. Verificar si YA ESTÁ INSCRITO en ESTE curso
        if (ProgresoCurso::where('user_id', $user->id)
                        ->where('curso_id', $curso->id)
                        ->exists()) {
            return redirect()->route('home')
                            ->with('info', 'Ya estás inscrito en este curso. Continúa desde tu progreso.');
        }

        // 3. Crear el registro de progreso
        ProgresoCurso::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'estado' => 'en_progreso',
        ]);

        // 4. Marcar como curso en progreso
        $user->update(['curso_en_progreso_id' => $curso->id]);

        \Log::info('Curso comenzado exitosamente');

        return redirect()->route('home')
                        ->with('success', '¡Has comenzado el curso ' . $curso->titulo . '!');
    }

}