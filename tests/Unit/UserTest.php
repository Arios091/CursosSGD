<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Curso;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function test_crear_usuario_con_role_estudiante()
    {
        $user = User::create([
            'name' => 'Test Estudiante',
            'primer_nombre' => 'Test',
            'primer_apellido' => 'Estudiante',
            'email' => 'estudiante@test.com',
            'password' => bcrypt('password'),
            'role' => 'estudiante',
        ]);

        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isAdminGlobal());
        $this->assertFalse($user->isDocente());
        $this->assertTrue($user->isEstudiante());
        $this->assertFalse($user->puedeGestionarCursos());
        $this->assertFalse($user->puedeGestionarUsuarios());
    }

    public function test_crear_usuario_con_role_docente()
    {
        $user = User::create([
            'name' => 'Test Docente',
            'primer_nombre' => 'Test',
            'primer_apellido' => 'Docente',
            'email' => 'docente@test.com',
            'password' => bcrypt('password'),
            'role' => 'docente',
        ]);

        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isAdminGlobal());
        $this->assertTrue($user->isDocente());
        $this->assertFalse($user->isEstudiante());
        $this->assertFalse($user->puedeGestionarCursos());
        $this->assertFalse($user->puedeGestionarUsuarios());
    }

    public function test_crear_usuario_con_role_admin()
    {
        $user = User::create([
            'name' => 'Test Admin',
            'primer_nombre' => 'Test',
            'primer_apellido' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isAdminGlobal());
        $this->assertFalse($user->isDocente());
        $this->assertTrue($user->puedeGestionarCursos());
    }

    public function test_crear_usuario_con_role_admin_global()
    {
        $user = User::create([
            'name' => 'Test Admin Global',
            'primer_nombre' => 'Test',
            'primer_apellido' => 'AdminGlobal',
            'email' => 'adminglobal@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin_global',
        ]);

        $this->assertTrue($user->isAdmin());
        $this->assertTrue($user->isAdminGlobal());
        $this->assertTrue($user->puedeGestionarCursos());
        $this->assertTrue($user->puedeGestionarUsuarios());
    }

    public function test_usuario_sin_curso_en_progreso_tiene_null()
    {
        $user = User::create([
            'name' => 'Test User',
            'primer_nombre' => 'Test',
            'primer_apellido' => 'User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'role' => 'estudiante',
        ]);

        $this->assertNull($user->curso_en_progreso_id);
        $this->assertNull($user->cursoEnProgreso);
    }

    public function test_usuario_puede_tener_un_solo_curso_en_progreso()
    {
        $admin = User::create([
            'name' => 'Admin',
            'primer_nombre' => 'Admin',
            'primer_apellido' => 'User',
            'email' => 'admin2@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $curso = Curso::create([
            'titulo' => 'Curso Test',
            'carga_horaria' => 10,
            'user_id' => $admin->id,
        ]);

        $user = User::create([
            'name' => 'Test',
            'primer_nombre' => 'Test',
            'primer_apellido' => 'User',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'role' => 'estudiante',
        ]);

        $user->curso_en_progreso_id = $curso->id;
        $user->save();

        $this->assertEquals($curso->id, $user->fresh()->curso_en_progreso_id);
        $this->assertNotNull($user->fresh()->cursoEnProgreso);
        $this->assertEquals($curso->titulo, $user->fresh()->cursoEnProgreso->titulo);
    }
}
