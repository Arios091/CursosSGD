<?php

use App\Models\PageSetting;
use Illuminate\Database\Seeder;

class PageSettingsSeeder extends Seeder
{
    public function run()
    {
        $defaults = [
            ['key' => 'hero_title', 'value' => 'Sistema de <span>Gestión de Docencia</span> UNAS', 'type' => 'text'],
            ['key' => 'hero_subtitle', 'value' => 'Plataforma oficial de educación continua de la Universidad Nacional Agraria de la Selva. Accede a cursos especializados, gestiona tu progreso y obtén certificaciones con validez académica.', 'type' => 'text'],
            ['key' => 'primary_color', 'value' => '#0B5E2E', 'type' => 'color'],
            ['key' => 'secondary_color', 'value' => '#C9A227', 'type' => 'color'],
            ['key' => 'contact_phone', 'value' => '(062) 562341', 'type' => 'text'],
            ['key' => 'contact_email', 'value' => 'mesadepartes@unas.edu.pe', 'type' => 'text'],
            ['key' => 'contact_address', 'value' => 'Carretera Central Km. 1.21, Tingo María, Huánuco', 'type' => 'text'],
        ];

        foreach ($defaults as $item) {
            PageSetting::firstOrCreate(
                ['key' => $item['key']],
                ['value' => $item['value'], 'type' => $item['type']]
            );
        }
    }
}