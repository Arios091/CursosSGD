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
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CuestionarioTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupCompleto($minAprobacion = 75)
    {
        $admin = User::create([
            'name' => 'Admin Quiz',
            'primer_nombre' => 'Admin',
            'primer_apellido' => 'Quiz',
            'email' => 'admin_quiz@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $curso = Curso::create([
            'titulo' => 'Curso Quiz',
            'carga_horaria' => 20,
            'user_id' => $admin->id,
        ]);

        $modulo = Modulo::create([
            'curso_id' => $curso->id,
            'titulo' => 'Módulo Quiz',
            'orden' => 1,
        ]);

        $material = Material::create([
            'modulo_id' => $modulo->id,
            'titulo' => 'Material Obligatorio',
            'tipo' => 'pdf',
            'url' => 'test.pdf',
            'orden' => 1,
        ]);

        $cuestionario = Cuestionario::create([
            'modulo_id' => $modulo->id,
            'titulo' => 'Quiz del Módulo',
            'min_aprobacion' => $minAprobacion,
        ]);

        $p1 = Pregunta::create([
            'cuestionario_id' => $cuestionario->id,
            'pregunta' => '¿2+2 es 4?',
            'orden' => 1,
        ]);

        $p1Correcta = Opcion::create([
            'pregunta_id' => $p1->id,
            'opcion' => 'Sí',
            'es_correcta' => true,
            'orden' => 1,
        ]);

        $p1Incorrecta = Opcion::create([
            'pregunta_id' => $p1->id,
            'opcion' => 'No',
            'es_correcta' => false,
            'orden' => 2,
        ]);

        $p2 = Pregunta::create([
            'cuestionario_id' => $cuestionario->id,
            'pregunta' => '¿El cielo es azul?',
            'orden' => 2,
        ]);

        $p2Correcta = Opcion::create([
            'pregunta_id' => $p2->id,
            'opcion' => 'Sí',
            'es_correcta' => true,
            'orden' => 1,
        ]);

        $p2Incorrecta = Opcion::create([
            'pregunta_id' => $p2->id,
            'opcion' => 'No',
            'es_correcta' => false,
            'orden' => 2,
        ]);

        $p3 = Pregunta::create([
            'cuestionario_id' => $cuestionario->id,
            'pregunta' => '¿PHP es un lenguaje?',
            'orden' => 3,
        ]);

        $p3Correcta = Opcion::create([
            'pregunta_id' => $p3->id,
            'opcion' => 'Sí',
            'es_correcta' => true,
            'orden' => 1,
        ]);

        $p3Incorrecta = Opcion::create([
            'pregunta_id' => $p3->id,
            'opcion' => 'No',
            'es_correcta' => false,
            'orden' => 2,
        ]);

        $estudiante = User::create([
            'name' => 'Est Quiz',
            'primer_nombre' => 'Est',
            'primer_apellido' => 'Quiz',
            'email' => 'est_quiz@test.com',
            'password' => bcrypt('password'),
            'role' => 'estudiante',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($estudiante)->post(route('cursos.comenzar', $curso));

        ProgresoMaterial::create([
            'user_id' => $estudiante->id,
            'material_id' => $material->id,
            'material_completado' => true,
            'completado_at' => now(),
        ]);

        return compact('curso', 'modulo', 'cuestionario', 'estudiante', 'p1', 'p2', 'p3', 'p1Correcta', 'p2Correcta', 'p3Correcta');
    }

    public function test_responder_cuestionario_correctamente_aprueba()
    {
        $data = $this->setupCompleto(75);
        $curso = $data['curso'];
        $modulo = $data['modulo'];
        $estudiante = $data['estudiante'];

        $response = $this->actingAs($estudiante)
            ->post(route('cursos.cuestionario', [$curso, $modulo->id]), [
                'respuestas' => [
                    $data['p1']->id => $data['p1Correcta']->id,
                    $data['p2']->id => $data['p2Correcta']->id,
                    $data['p3']->id => $data['p3Correcta']->id,
                ]
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $resultado = ResultadoCuestionario::where('user_id', $estudiante->id)
            ->where('modulo_id', $modulo->id)
            ->first();

        $this->assertNotNull($resultado);
        $this->assertTrue((bool)$resultado->aprobado);
        $this->assertEquals(100, $resultado->nota);
    }

    public function test_responder_con_50_porciento_con_min_75_no_aprueba()
    {
        $data = $this->setupCompleto(75);
        $curso = $data['curso'];
        $modulo = $data['modulo'];
        $estudiante = $data['estudiante'];
        $p1Incorrecta = Opcion::where('pregunta_id', $data['p1']->id)->where('es_correcta', false)->first();
        $p2Incorrecta = Opcion::where('pregunta_id', $data['p2']->id)->where('es_correcta', false)->first();

        $response = $this->actingAs($estudiante)
            ->post(route('cursos.cuestionario', [$curso, $modulo->id]), [
                'respuestas' => [
                    $data['p1']->id => $p1Incorrecta->id,
                    $data['p2']->id => $p2Incorrecta->id,
                    $data['p3']->id => $data['p3Correcta']->id,
                ]
            ]);

        $resultado = ResultadoCuestionario::where('user_id', $estudiante->id)
            ->where('modulo_id', $modulo->id)
            ->first();

        $this->assertNotNull($resultado);
        $this->assertFalse((bool)$resultado->aprobado);
        $this->assertEquals(33, $resultado->nota);
    }

    public function test_responder_con_67_porciento_con_min_60_aprueba()
    {
        $data = $this->setupCompleto(60);
        $curso = $data['curso'];
        $modulo = $data['modulo'];
        $estudiante = $data['estudiante'];
        $p1Incorrecta = Opcion::where('pregunta_id', $data['p1']->id)->where('es_correcta', false)->first();

        $response = $this->actingAs($estudiante)
            ->post(route('cursos.cuestionario', [$curso, $modulo->id]), [
                'respuestas' => [
                    $data['p1']->id => $p1Incorrecta->id,
                    $data['p2']->id => $data['p2Correcta']->id,
                    $data['p3']->id => $data['p3Correcta']->id,
                ]
            ]);

        $resultado = ResultadoCuestionario::where('user_id', $estudiante->id)
            ->where('modulo_id', $modulo->id)
            ->first();

        $this->assertNotNull($resultado);
        $this->assertTrue((bool)$resultado->aprobado);
        $this->assertEquals(67, $resultado->nota);
    }

    public function test_no_puede_tomar_cuestionario_sin_completar_materiales()
    {
        $admin = User::create([
            'name' => 'Admin',
            'primer_nombre' => 'Admin',
            'primer_apellido' => 'User',
            'email' => 'admin_sinmat@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $curso = Curso::create([
            'titulo' => 'Curso Sin Materiales',
            'carga_horaria' => 10,
            'user_id' => $admin->id,
        ]);

        $modulo = Modulo::create([
            'curso_id' => $curso->id,
            'titulo' => 'Módulo Único',
            'orden' => 1,
        ]);

        $material = Material::create([
            'modulo_id' => $modulo->id,
            'titulo' => 'PDF Obligatorio',
            'tipo' => 'pdf',
            'url' => 'test.pdf',
            'orden' => 1,
        ]);

        Cuestionario::create([
            'modulo_id' => $modulo->id,
            'titulo' => 'Quiz',
            'min_aprobacion' => 80,
        ]);

        $estudiante = User::create([
            'name' => 'Est SinMat',
            'primer_nombre' => 'Est',
            'primer_apellido' => 'SinMat',
            'email' => 'est_sinmat@test.com',
            'password' => bcrypt('password'),
            'role' => 'estudiante',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($estudiante)->post(route('cursos.comenzar', $curso));

        $this->assertEquals(1, $modulo->materiales()->count());

        $response = $this->actingAs($estudiante)
            ->post(route('cursos.cuestionario', [$curso, $modulo->id]), [
                'respuestas' => []
            ]);

        $this->assertEquals(0, \App\Models\ProgresoMaterial::where('user_id', $estudiante->id)
            ->where('material_id', $material->id)
            ->where('material_completado', true)
            ->count());

        $response->assertSessionHas('error');
        $response->assertRedirect();
    }
}
