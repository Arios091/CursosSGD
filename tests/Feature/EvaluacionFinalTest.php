<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Curso;
use App\Models\Modulo;
use App\Models\Material;
use App\Models\Cuestionario;
use App\Models\Pregunta;
use App\Models\Opcion;
use App\Models\EvaluacionFinal;
use App\Models\PreguntaEvaluacion;
use App\Models\OpcionEvaluacion;
use App\Models\ProgresoCurso;
use App\Models\ProgresoMaterial;
use App\Models\ResultadoCuestionario;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EvaluacionFinalTest extends TestCase
{
    use DatabaseTransactions;

    public function test_evaluacion_final_con_nota_suficiente_completa_curso()
    {
        $admin = User::create([
            'name' => 'Admin Eval',
            'primer_nombre' => 'Admin',
            'primer_apellido' => 'Eval',
            'email' => 'admin_evalf@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $curso = Curso::create([
            'titulo' => 'Curso Eval Final',
            'carga_horaria' => 20,
            'user_id' => $admin->id,
        ]);

        $modulo = Modulo::create([
            'curso_id' => $curso->id,
            'titulo' => 'Módulo Único',
            'orden' => 1,
        ]);

        Material::create([
            'modulo_id' => $modulo->id,
            'titulo' => 'Material',
            'tipo' => 'pdf',
            'url' => 'test.pdf',
            'orden' => 1,
        ]);

        $cuestionario = Cuestionario::create([
            'modulo_id' => $modulo->id,
            'titulo' => 'Quiz',
            'min_aprobacion' => 50,
        ]);

        $preg = Pregunta::create([
            'cuestionario_id' => $cuestionario->id,
            'pregunta' => '¿Test?',
            'orden' => 1,
        ]);

        $opcCorrecta = Opcion::create([
            'pregunta_id' => $preg->id,
            'opcion' => 'Sí',
            'es_correcta' => true,
            'orden' => 1,
        ]);

        $evaluacion = EvaluacionFinal::create([
            'curso_id' => $curso->id,
            'titulo' => 'Examen Final',
            'min_aprobacion' => 60,
        ]);

        $pregEval = PreguntaEvaluacion::create([
            'evaluacion_final_id' => $evaluacion->id,
            'pregunta' => '¿Está todo correcto?',
            'orden' => 1,
        ]);

        $opcEvalCorrecta = OpcionEvaluacion::create([
            'pregunta_evaluacion_id' => $pregEval->id,
            'opcion' => 'Sí',
            'es_correcta' => true,
            'orden' => 1,
        ]);

        $estudiante = User::create([
            'name' => 'Est EvalFinal',
            'primer_nombre' => 'Est',
            'primer_apellido' => 'EvalFinal',
            'email' => 'est_evf@test.com',
            'password' => bcrypt('password'),
            'role' => 'estudiante',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($estudiante)->post(route('cursos.comenzar', $curso));

        ProgresoMaterial::create([
            'user_id' => $estudiante->id,
            'material_id' => Material::first()->id,
            'material_completado' => true,
            'completado_at' => now(),
        ]);

        // Aprobar quiz del módulo
        $this->actingAs($estudiante)->post(route('cursos.cuestionario', [$curso, $modulo->id]), [
            'respuestas' => [$preg->id => $opcCorrecta->id]
        ]);

        // Enviar evaluación final
        $response = $this->actingAs($estudiante)
            ->post(route('cursos.evaluacion-final.enviar', $curso), [
                'respuestas' => [$pregEval->id => $opcEvalCorrecta->id]
            ]);

        $response->assertRedirect(route('cursos.completado', $curso));
        $response->assertSessionHas('success');

        $progresoCurso = ProgresoCurso::where('user_id', $estudiante->id)
            ->where('curso_id', $curso->id)
            ->first();

        $this->assertEquals('completado', $progresoCurso->estado);
    }

    public function test_evaluacion_final_con_nota_insuficiente_no_completa_curso()
    {
        $admin = User::create([
            'name' => 'Admin Eval2',
            'primer_nombre' => 'Admin',
            'primer_apellido' => 'Eval2',
            'email' => 'admin_eval2@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $curso = Curso::create([
            'titulo' => 'Curso Eval Fail',
            'carga_horaria' => 20,
            'user_id' => $admin->id,
        ]);

        $modulo = Modulo::create([
            'curso_id' => $curso->id,
            'titulo' => 'Módulo Único',
            'orden' => 1,
        ]);

        $materialMat = Material::create([
            'modulo_id' => $modulo->id,
            'titulo' => 'Material',
            'tipo' => 'pdf',
            'url' => 'test.pdf',
            'orden' => 1,
        ]);

        $cuestionario = Cuestionario::create([
            'modulo_id' => $modulo->id,
            'titulo' => 'Quiz',
            'min_aprobacion' => 50,
        ]);

        $preg = Pregunta::create([
            'cuestionario_id' => $cuestionario->id,
            'pregunta' => '¿Test?',
            'orden' => 1,
        ]);

        $opcCorrecta = Opcion::create([
            'pregunta_id' => $preg->id,
            'opcion' => 'Sí',
            'es_correcta' => true,
            'orden' => 1,
        ]);

        $evaluacion = EvaluacionFinal::create([
            'curso_id' => $curso->id,
            'titulo' => 'Examen Final',
            'min_aprobacion' => 80,
        ]);

        $pregEval = PreguntaEvaluacion::create([
            'evaluacion_final_id' => $evaluacion->id,
            'pregunta' => '¿Está todo correcto?',
            'orden' => 1,
        ]);

        $opcEvalIncorrecta = OpcionEvaluacion::create([
            'pregunta_evaluacion_id' => $pregEval->id,
            'opcion' => 'No',
            'es_correcta' => false,
            'orden' => 1,
        ]);

        $estudiante = User::create([
            'name' => 'Est EvalFail',
            'primer_nombre' => 'Est',
            'primer_apellido' => 'EvalFail',
            'email' => 'est_evf2@test.com',
            'password' => bcrypt('password'),
            'role' => 'estudiante',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($estudiante)->post(route('cursos.comenzar', $curso));

        ProgresoMaterial::create([
            'user_id' => $estudiante->id,
            'material_id' => $materialMat->id,
            'material_completado' => true,
            'completado_at' => now(),
        ]);

        $this->actingAs($estudiante)->post(route('cursos.cuestionario', [$curso, $modulo->id]), [
            'respuestas' => [$preg->id => $opcCorrecta->id]
        ]);

        $response = $this->actingAs($estudiante)
            ->post(route('cursos.evaluacion-final.enviar', $curso), [
                'respuestas' => [$pregEval->id => $opcEvalIncorrecta->id]
            ]);

        $progresoCurso = ProgresoCurso::where('user_id', $estudiante->id)
            ->where('curso_id', $curso->id)
            ->first();

        $this->assertNotEquals('completado', $progresoCurso->estado);
    }
}
