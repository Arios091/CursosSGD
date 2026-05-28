<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Curso;
use App\Models\Modulo;
use App\Models\Material;
use App\Models\Cuestionario;
use App\Models\Pregunta;
use App\Models\Opcion;
use App\Models\ProgresoCurso;
use App\Models\ProgresoMaterial;
use App\Models\ResultadoCuestionario;
use App\Models\EvaluacionFinal;
use App\Models\PreguntaEvaluacion;
use App\Models\OpcionEvaluacion;
use App\Models\ResultadoEvaluacion;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CursoRefactoringTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupCurso()
    {
        // 1. Crear un administrador (para simular el creador)
        $admin = User::create([
            'name' => 'Admin User',
            'primer_nombre' => 'Admin',
            'primer_apellido' => 'User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // 2. Crear un curso
        $curso = Curso::create([
            'titulo' => 'Curso de Prueba',
            'descripcion' => 'Descripción del curso',
            'carga_horaria' => 10,
            'user_id' => $admin->id,
            'categoria' => 'Pruebas',
        ]);

        // 3. Crear un módulo
        $modulo = Modulo::create([
            'curso_id' => $curso->id,
            'titulo' => 'Módulo 1',
            'descripcion' => 'Descripción Módulo 1',
            'orden' => 1,
        ]);

        // 4. Crear material para el módulo
        $material = Material::create([
            'modulo_id' => $modulo->id,
            'curso_id' => $curso->id,
            'titulo' => 'Material 1 (PDF)',
            'tipo' => 'pdf',
            'url' => 'http://drive.com/file',
            'orden' => 1,
        ]);

        // 5. Crear un cuestionario para el módulo
        $cuestionario = Cuestionario::create([
            'modulo_id' => $modulo->id,
            'titulo' => 'Cuestionario 1',
        ]);

        $pregunta = Pregunta::create([
            'cuestionario_id' => $cuestionario->id,
            'pregunta' => '¿La refactorización es buena?',
            'orden' => 1,
        ]);

        // Guardamos las opciones. Nota: en el estado actual es 'true'/'false' en la BD, pero con el trait.
        // Después del refactor será un boolean nativo real. El test se adaptará.
        // Pero para ser compatible con el trait actual en el test basal,
        // necesitamos insertar lo que espera el modelo.
        // El trait intercepta e inserta string 'true'/'false'.
        // Así que pasamos booleano y el trait se encargará (o la BD nativa tras el refactor).
        $opcionCorrecta = Opcion::create([
            'pregunta_id' => $pregunta->id,
            'opcion' => 'Sí',
            'es_correcta' => true,
            'orden' => 1,
        ]);

        $opcionIncorrecta = Opcion::create([
            'pregunta_id' => $pregunta->id,
            'opcion' => 'No',
            'es_correcta' => false,
            'orden' => 2,
        ]);

        // 6. Crear Evaluación Final del curso
        $evaluacionFinal = EvaluacionFinal::create([
            'curso_id' => $curso->id,
            'titulo' => 'Examen Final',
            'min_aprobacion' => 80,
        ]);

        $preguntaEval = PreguntaEvaluacion::create([
            'evaluacion_final_id' => $evaluacionFinal->id,
            'pregunta' => '¿El sistema está correcto?',
            'orden' => 1,
        ]);

        $opcionEvalCorrecta = OpcionEvaluacion::create([
            'pregunta_evaluacion_id' => $preguntaEval->id,
            'opcion' => 'Sí, ahora sí',
            'es_correcta' => true,
            'orden' => 1,
        ]);

        $opcionEvalIncorrecta = OpcionEvaluacion::create([
            'pregunta_evaluacion_id' => $preguntaEval->id,
            'opcion' => 'No',
            'es_correcta' => false,
            'orden' => 2,
        ]);

        return compact(
            'admin', 'curso', 'modulo', 'material', 'cuestionario', 
            'pregunta', 'opcionCorrecta', 'opcionIncorrecta',
            'evaluacionFinal', 'preguntaEval', 'opcionEvalCorrecta', 'opcionEvalIncorrecta'
        );
    }

    public function test_estudiante_puede_inscribirse_en_curso()
    {
        $data = $this->setupCurso();
        $curso = $data['curso'];

        $estudiante = User::create([
            'name' => 'Estudiante 1',
            'primer_nombre' => 'Estudiante',
            'primer_apellido' => 'Uno',
            'email' => 'estudiante@test.com',
            'password' => bcrypt('password'),
            'role' => 'estudiante',
        ]);

        $response = $this->actingAs($estudiante)
            ->post(route('cursos.comenzar', $curso));

        $response->assertRedirect();
        
        $this->assertDatabaseHas('progreso_cursos', [
            'user_id' => $estudiante->id,
            'curso_id' => $curso->id,
            'estado' => 'en_progreso',
            'modulo_actual' => 1,
        ]);

        $this->assertEquals($curso->id, $estudiante->fresh()->curso_en_progreso_id);
    }

    public function test_estudiante_no_puede_inscribirse_en_dos_cursos_a_la_vez()
    {
        $data = $this->setupCurso();
        $curso1 = $data['curso'];
        $admin = $data['admin'];

        $curso2 = Curso::create([
            'titulo' => 'Curso de Prueba 2',
            'descripcion' => 'Otro curso',
            'carga_horaria' => 5,
            'user_id' => $admin->id,
            'categoria' => 'Pruebas',
        ]);

        $estudiante = User::create([
            'name' => 'Estudiante 1',
            'primer_nombre' => 'Estudiante',
            'primer_apellido' => 'Uno',
            'email' => 'estudiante@test.com',
            'password' => bcrypt('password'),
            'role' => 'estudiante',
        ]);

        // Inscribirse en el primero
        $this->actingAs($estudiante)->post(route('cursos.comenzar', $curso1));

        // Intentar inscribirse en el segundo
        $response = $this->actingAs($estudiante)->post(route('cursos.comenzar', $curso2));

        // Debería redirigir con error a la página principal
        $response->assertSessionHas('error');
        
        $this->assertDatabaseMissing('progreso_cursos', [
            'user_id' => $estudiante->id,
            'curso_id' => $curso2->id,
        ]);
    }

    public function test_progreso_se_registra_correctamente()
    {
        $data = $this->setupCurso();
        $curso = $data['curso'];
        $material = $data['material'];

        $estudiante = User::create([
            'name' => 'Estudiante 1',
            'primer_nombre' => 'Estudiante',
            'primer_apellido' => 'Uno',
            'email' => 'estudiante@test.com',
            'password' => bcrypt('password'),
            'role' => 'estudiante',
        ]);

        // Inscribirse
        $this->actingAs($estudiante)->post(route('cursos.comenzar', $curso));

        // Marcar material como completado
        // Route::post('/mis-cursos/{curso}/material', [CursoController::class, 'marcarMaterial'])->name('cursos.material');
        $response = $this->actingAs($estudiante)
            ->post(route('cursos.material', $curso), [
                'material_id' => $material->id,
                'completado' => true,
            ]);

        echo "STATUS: " . $response->status() . "\n";
        echo "CONTENT: " . substr($response->content(), 0, 800) . "\n";
        $response->assertRedirect();

        // En el estado actual, el trait BoolToPgString convertirá esto a 'true'
        // pero mediante los helpers de Eloquent/PHPUnit,
        // assertDatabaseHas verificará la coincidencia.
        // Nota: para soportar tanto 'true' como true antes/después del refactor,
        // podemos simplemente consultar el modelo de forma explícita.
        $progreso = ProgresoMaterial::where('user_id', $estudiante->id)
            ->where('material_id', $material->id)
            ->first();

        $this->assertNotNull($progreso);
        // Si el valor se guarda como string 'true' o bool true, al convertirlo a booleano en PHP (por $casts) debe ser true.
        $this->assertTrue((bool)$progreso->material_completado);
    }

    public function test_aprobacion_de_cuestionario()
    {
        $data = $this->setupCurso();
        $curso = $data['curso'];
        $modulo = $data['modulo'];
        $pregunta = $data['pregunta'];
        $opcionCorrecta = $data['opcionCorrecta'];

        $estudiante = User::create([
            'name' => 'Estudiante 1',
            'primer_nombre' => 'Estudiante',
            'primer_apellido' => 'Uno',
            'email' => 'estudiante@test.com',
            'password' => bcrypt('password'),
            'role' => 'estudiante',
        ]);

        // Inscribirse
        $this->actingAs($estudiante)->post(route('cursos.comenzar', $curso));

        // Responder cuestionario correctamente
        // Route::post('/mis-cursos/{curso}/modulo/{modulo}/cuestionario', [CursoController::class, 'enviarCuestionario'])
        $response = $this->actingAs($estudiante)
            ->post(route('cursos.cuestionario', [$curso, $modulo->id]), [
                'respuestas' => [
                    $pregunta->id => $opcionCorrecta->id,
                ]
            ]);

        $response->assertRedirect(route('cursos.cuestionario.resultado', [$curso, $modulo->id]));
        $response->assertSessionHas('success');

        $resultado = ResultadoCuestionario::where('user_id', $estudiante->id)
            ->where('modulo_id', $modulo->id)
            ->first();

        $this->assertNotNull($resultado);
        $this->assertTrue((bool)$resultado->aprobado);
        $this->assertEquals(100, $resultado->nota);
    }
}
