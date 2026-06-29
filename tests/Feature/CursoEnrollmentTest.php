<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Curso;
use App\Models\Modulo;
use App\Models\Material;
use App\Models\ProgresoCurso;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CursoEnrollmentTest extends TestCase
{
    use DatabaseTransactions;

    protected function crearCursoCompleto()
    {
        $admin = User::create([
            'name' => 'Admin Curso',
            'primer_nombre' => 'Admin',
            'primer_apellido' => 'Curso',
            'email' => 'admin_curso_enroll@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $curso = Curso::create([
            'titulo' => 'Curso de PHP',
            'descripcion' => 'Aprende PHP desde cero',
            'carga_horaria' => 40,
            'user_id' => $admin->id,
            'categoria' => 'Programación',
        ]);

        $modulo = Modulo::create([
            'curso_id' => $curso->id,
            'titulo' => 'Introducción',
            'orden' => 1,
        ]);

        $material1 = Material::create([
            'modulo_id' => $modulo->id,
            'titulo' => 'Guía de PHP',
            'tipo' => 'pdf',
            'url' => 'guia-php.pdf',
            'orden' => 1,
        ]);

        $material2 = Material::create([
            'modulo_id' => $modulo->id,
            'titulo' => 'Video Intro',
            'tipo' => 'video',
            'url' => 'https://youtube.com/watch?v=abc123def45',
            'orden' => 2,
        ]);

        return compact('admin', 'curso', 'modulo', 'material1', 'material2');
    }

    public function test_estudiante_puede_inscribirse()
    {
        $data = $this->crearCursoCompleto();
        $curso = $data['curso'];

        $estudiante = User::create([
            'name' => 'Estudiante Nuevo',
            'primer_nombre' => 'Estudiante',
            'primer_apellido' => 'Nuevo',
            'email' => 'est_nuevo@test.com',
            'password' => bcrypt('password'),
            'role' => 'estudiante',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($estudiante)
            ->post(route('cursos.comenzar', $curso));

        $response->assertRedirect();

        $this->assertDatabaseHas('progreso_cursos', [
            'user_id' => $estudiante->id,
            'curso_id' => $curso->id,
            'estado' => 'en_progreso',
        ]);

        $this->assertEquals($curso->id, $estudiante->fresh()->curso_en_progreso_id);
    }

    public function test_estudiante_no_puede_inscribirse_en_dos_cursos()
    {
        $data = $this->crearCursoCompleto();
        $curso1 = $data['curso'];
        $admin = $data['admin'];

        $curso2 = Curso::create([
            'titulo' => 'Curso de JavaScript',
            'carga_horaria' => 30,
            'user_id' => $admin->id,
        ]);

        $estudiante = User::create([
            'name' => 'Est Doble',
            'primer_nombre' => 'Est',
            'primer_apellido' => 'Doble',
            'email' => 'est_doble@test.com',
            'password' => bcrypt('password'),
            'role' => 'estudiante',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($estudiante)->post(route('cursos.comenzar', $curso1));

        $response = $this->actingAs($estudiante)->post(route('cursos.comenzar', $curso2));

        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('progreso_cursos', [
            'user_id' => $estudiante->id,
            'curso_id' => $curso2->id,
        ]);
    }

    public function test_progreso_material_se_actualiza()
    {
        $data = $this->crearCursoCompleto();
        $curso = $data['curso'];
        $material1 = $data['material1'];

        $estudiante = User::create([
            'name' => 'Est Progreso',
            'primer_nombre' => 'Est',
            'primer_apellido' => 'Progreso',
            'email' => 'est_progreso@test.com',
            'password' => bcrypt('password'),
            'role' => 'estudiante',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($estudiante)->post(route('cursos.comenzar', $curso));

        $response = $this->actingAs($estudiante)
            ->post(route('cursos.material', $curso), [
                'material_id' => $material1->id,
                'completado' => true,
            ]);

        $response->assertRedirect();

        $progreso = \App\Models\ProgresoMaterial::where('user_id', $estudiante->id)
            ->where('material_id', $material1->id)
            ->first();

        $this->assertNotNull($progreso);
        $this->assertTrue((bool)$progreso->material_completado);
    }
}
