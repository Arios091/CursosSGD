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
use App\Models\ResultadoEvaluacion;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    use DatabaseTransactions;

    protected function completarCurso()
    {
        $admin = User::create([
            'name' => 'Admin Cert',
            'primer_nombre' => 'Admin',
            'primer_apellido' => 'Cert',
            'email' => 'admin_cert@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $curso = Curso::create([
            'titulo' => 'Curso Certificado',
            'carga_horaria' => 20,
            'user_id' => $admin->id,
        ]);

        $modulo = Modulo::create([
            'curso_id' => $curso->id,
            'titulo' => 'Módulo Único',
            'orden' => 1,
        ]);

        $material = Material::create([
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
            'min_aprobacion' => 50,
        ]);

        $pregEval = PreguntaEvaluacion::create([
            'evaluacion_final_id' => $evaluacion->id,
            'pregunta' => '¿Todo bien?',
            'orden' => 1,
        ]);

        $opcEvalCorrecta = OpcionEvaluacion::create([
            'pregunta_evaluacion_id' => $pregEval->id,
            'opcion' => 'Sí',
            'es_correcta' => true,
            'orden' => 1,
        ]);

        $estudiante = User::create([
            'name' => 'Est Certificado',
            'primer_nombre' => 'Est',
            'primer_apellido' => 'Certificado',
            'email' => 'est_cert@test.com',
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

        $this->actingAs($estudiante)->post(route('cursos.cuestionario', [$curso, $modulo->id]), [
            'respuestas' => [$preg->id => $opcCorrecta->id]
        ]);

        $this->actingAs($estudiante)->post(route('cursos.evaluacion-final.enviar', $curso), [
            'respuestas' => [$pregEval->id => $opcEvalCorrecta->id]
        ]);

        return compact('curso', 'estudiante', 'admin');
    }

    public function test_certificado_view_se_muestra()
    {
        $data = $this->completarCurso();
        $curso = $data['curso'];
        $estudiante = $data['estudiante'];

        $response = $this->actingAs($estudiante)
            ->get(route('certificado.ver', $curso));

        $response->assertStatus(200);
        $response->assertSee('Certificado');
        $response->assertSee(strtoupper($estudiante->name));
        $response->assertSee($curso->titulo);
    }

    public function test_certificado_pdf_se_descarga()
    {
        $data = $this->completarCurso();
        $curso = $data['curso'];
        $estudiante = $data['estudiante'];

        $response = $this->actingAs($estudiante)
            ->get(route('certificado.descargar', $curso));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_certificado_no_disponible_sin_completar()
    {
        $admin = User::create([
            'name' => 'Admin NC',
            'primer_nombre' => 'Admin',
            'primer_apellido' => 'NC',
            'email' => 'admin_nc@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $curso = Curso::create([
            'titulo' => 'Curso No Completo',
            'carga_horaria' => 10,
            'user_id' => $admin->id,
        ]);

        $estudiante = User::create([
            'name' => 'Est NC',
            'primer_nombre' => 'Est',
            'primer_apellido' => 'NC',
            'email' => 'est_nc@test.com',
            'password' => bcrypt('password'),
            'role' => 'estudiante',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($estudiante)
            ->get(route('certificado.ver', $curso));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_verificacion_publica_de_certificado()
    {
        $data = $this->completarCurso();
        $curso = $data['curso'];
        $estudiante = $data['estudiante'];

        $progreso = ProgresoCurso::where('user_id', $estudiante->id)
            ->where('curso_id', $curso->id)
            ->first();

        $codigo = 'UNAS-CERT-' . strtoupper(substr($curso->titulo, 0, 4)) . '-' . str_pad($progreso->id, 6, '0', STR_PAD_LEFT) . '-' . $progreso->completado_at->format('Y');

        $response = $this->get(route('certificado.verificar.api', $codigo));

        $response->assertStatus(200);
        $response->assertJson([
            'valido' => true,
        ]);
        $response->assertJsonFragment([
            'nombre' => $estudiante->name,
        ]);
    }

    public function test_verificacion_de_certificado_invalido()
    {
        $response = $this->get(route('certificado.verificar.api', 'CODIGO-FALSO-000000-2026'));

        $response->assertStatus(200);
        $response->assertJson([
            'valido' => false,
        ]);
    }
}
