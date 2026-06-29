<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Curso;
use App\Models\Modulo;
use App\Models\Material;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MaterialModelTest extends TestCase
{
    use DatabaseTransactions;

    protected $material;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::create([
            'name' => 'Admin',
            'primer_nombre' => 'Admin',
            'primer_apellido' => 'User',
            'email' => 'admin_matmodel@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $curso = Curso::create([
            'titulo' => 'Curso Videos',
            'carga_horaria' => 10,
            'user_id' => $admin->id,
        ]);

        $modulo = Modulo::create([
            'curso_id' => $curso->id,
            'titulo' => 'Módulo Videos',
            'orden' => 1,
        ]);

        $this->material = new Material();
        $this->material->modulo_id = $modulo->id;
        $this->material->titulo = 'Test Material';
        $this->material->tipo = 'video';
        $this->material->url = 'https://youtube.com/watch?v=dQw4w9WgXcQ';
        $this->material->orden = 1;
    }

    public function test_youtube_url_se_normaliza_a_embed()
    {
        $this->material->url = 'https://youtube.com/watch?v=dQw4w9WgXcQ';
        $this->material->save();

        $embedUrl = $this->material->video_embed_url;
        $this->assertStringContainsString('youtube.com/embed/dQw4w9WgXcQ', $embedUrl);
    }

    public function test_youtube_embed_incluye_enablejsapi()
    {
        $this->material->url = 'https://youtube.com/watch?v=dQw4w9WgXcQ';
        $this->material->save();

        $embedUrl = $this->material->video_embed_url;
        $this->assertStringContainsString('enablejsapi=1', $embedUrl);
    }

    public function test_youtube_shorts_url()
    {
        $this->material->url = 'https://youtube.com/shorts/dQw4w9WgXcQ';
        $this->material->save();

        $embedUrl = $this->material->video_embed_url;
        $this->assertStringContainsString('youtube.com/embed/dQw4w9WgXcQ', $embedUrl);
    }

    public function test_youtube_live_url()
    {
        $this->material->url = 'https://youtube.com/live/dQw4w9WgXcQ';
        $this->material->save();

        $embedUrl = $this->material->video_embed_url;
        $this->assertStringContainsString('youtube.com/embed/dQw4w9WgXcQ', $embedUrl);
    }

    public function test_youtu_be_url()
    {
        $this->material->url = 'https://youtu.be/dQw4w9WgXcQ';
        $this->material->save();

        $embedUrl = $this->material->video_embed_url;
        $this->assertStringContainsString('youtube.com/embed/dQw4w9WgXcQ', $embedUrl);
    }

    public function test_vimeo_url_se_convierte_a_player()
    {
        $this->material->url = 'https://vimeo.com/123456789';
        $this->material->save();

        $embedUrl = $this->material->video_embed_url;
        $this->assertStringContainsString('player.vimeo.com/video/123456789', $embedUrl);
    }

    public function test_google_drive_url_se_convierte_a_preview()
    {
        $this->material->url = 'https://drive.google.com/file/d/abc123/view';
        $this->material->save();

        $embedUrl = $this->material->video_embed_url;
        $this->assertStringContainsString('drive.google.com/file/d/abc123/preview', $embedUrl);
    }

    public function test_material_con_url_vacia_retorna_null()
    {
        $this->material->url = '';
        $this->material->save();

        $embedUrl = $this->material->video_embed_url;
        $this->assertNull($embedUrl);
    }
}
