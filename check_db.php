<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== MATERIALES ===\n";
foreach(\App\Models\Material::all() as $m) {
    echo "ID: $m->id | Titulo: $m->titulo | Tipo: $m->tipo | URL: $m->url | Modulo: $m->modulo_id\n";
}

echo "\n=== MODULOS ===\n";
foreach(\App\Models\Modulo::all() as $m) {
    echo "ID: $m->id | Titulo: $m->titulo | Curso: $m->curso_id | Materiales: " . $m->materiales->count() . "\n";
}

echo "\n=== CURSOS ===\n";
foreach(\App\Models\Curso::all() as $c) {
    echo "ID: $c->id | Titulo: $c->titulo | Imagen: $c->imagen_referencial\n";
}
