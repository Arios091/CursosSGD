<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Curso;
use App\Models\Modulo;
use App\Models\Material;
use App\Models\Cuestionario;
use App\Models\EvaluacionFinal;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CursoModelTest extends TestCase
{
    use DatabaseTransactions;

    public function test_curso_tiene_modulos()
    {
        $admin = User::create([
            'name' => 'Admin',
            'primer_nombre' => 'Admin',
            'primer_apellido' => 'User',
            'email' => 'admin_curso@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $curso = Curso::create([
            'titulo' => 'Curso con Módulos',
            'carga_horaria' => 20,
            'user_id' => $admin->id,
        ]);

        $modulo1 = Modulo::create([
            'curso_id' => $curso->id,
            'titulo' => 'Módulo 1',
            'orden' => 1,
        ]);

        $modulo2 = Modulo::create([
            'curso_id' => $curso->id,
            'titulo' => 'Módulo 2',
            'orden' => 2,
        ]);

        $curso->load('modulos');
        $this->assertCount(2, $curso->modulos);
        $this->assertEquals(1, $curso->modulos[0]->orden);
        $this->assertEquals(2, $curso->modulos[1]->orden);
    }

    public function test_modulo_tiene_materiales()
    {
        $admin = User::create([
            'name' => 'Admin',
            'primer_nombre' => 'Admin',
            'primer_apellido' => 'User',
            'email' => 'admin_mat@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $curso = Curso::create([
            'titulo' => 'Curso Materiales',
            'carga_horaria' => 10,
            'user_id' => $admin->id,
        ]);

        $modulo = Modulo::create([
            'curso_id' => $curso->id,
            'titulo' => 'Módulo 1',
            'orden' => 1,
        ]);

        $material1 = Material::create([
            'modulo_id' => $modulo->id,
            'titulo' => 'PDF Material',
            'tipo' => 'pdf',
            'url' => 'test.pdf',
            'orden' => 1,
        ]);

        $material2 = Material::create([
            'modulo_id' => $modulo->id,
            'titulo' => 'Video Material',
            'tipo' => 'video',
            'url' => 'https://youtube.com/watch?v=abc123def45',
            'orden' => 2,
        ]);

        $modulo->load('materiales');
        $this->assertCount(2, $modulo->materiales);
        $this->assertEquals('pdf', $modulo->materiales[0]->tipo);
        $this->assertEquals('video', $modulo->materiales[1]->tipo);
    }

    public function test_modulo_tiene_cuestionario()
    {
        $admin = User::create([
            'name' => 'Admin',
            'primer_nombre' => 'Admin',
            'primer_apellido' => 'User',
            'email' => 'admin_cuest@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $curso = Curso::create([
            'titulo' => 'Curso Cuestionario',
            'carga_horaria' => 10,
            'user_id' => $admin->id,
        ]);

        $modulo = Modulo::create([
            'curso_id' => $curso->id,
            'titulo' => 'Módulo 1',
            'orden' => 1,
        ]);

        $cuestionario = Cuestionario::create([
            'modulo_id' => $modulo->id,
            'titulo' => 'Test Quiz',
            'min_aprobacion' => 80,
        ]);

        $modulo->load('cuestionario');
        $this->assertNotNull($modulo->cuestionario);
        $this->assertEquals('Test Quiz', $modulo->cuestionario->titulo);
        $this->assertEquals(80, $modulo->cuestionario->min_aprobacion);
    }

    public function test_curso_tiene_evaluacion_final()
    {
        $admin = User::create([
            'name' => 'Admin',
            'primer_nombre' => 'Admin',
            'primer_apellido' => 'User',
            'email' => 'admin_eval@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $curso = Curso::create([
            'titulo' => 'Curso Evaluación',
            'carga_horaria' => 15,
            'user_id' => $admin->id,
        ]);

        $evaluacion = EvaluacionFinal::create([
            'curso_id' => $curso->id,
            'titulo' => 'Examen Final',
            'min_aprobacion' => 70,
        ]);

        $curso->load('evaluacionFinal');
        $this->assertNotNull($curso->evaluacionFinal);
        $this->assertEquals('Examen Final', $curso->evaluacionFinal->titulo);
        $this->assertEquals(70, $curso->evaluacionFinal->min_aprobacion);
    }

    public function test_curso_tiene_columna_estado()
    {
        $admin = User::create([
            'name' => 'Admin',
            'primer_nombre' => 'Admin',
            'primer_apellido' => 'User',
            'email' => 'admin_est@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $curso = Curso::create([
            'titulo' => 'Curso con Estado',
            'carga_horaria' => 10,
            'user_id' => $admin->id,
        ]);

        $this->assertNotNull($curso->fresh()->estado);
        $this->assertEquals('publicado', $curso->fresh()->estado);
    }
}
