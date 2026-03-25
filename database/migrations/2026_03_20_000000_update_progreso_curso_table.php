<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProgresoCursoTable extends Migration
{
    public function up()
    {
        Schema::table('progreso_cursos', function (Blueprint $table) {
            $table->unsignedInteger('modulo_actual')->default(1)->after('estado');
            $table->unsignedInteger('material_actual')->default(1)->after('modulo_actual');
            $table->timestamp('completado_at')->nullable()->after('evaluacion_aprobada');
        });
    }

    public function down()
    {
        Schema::table('progreso_cursos', function (Blueprint $table) {
            $table->dropColumn(['modulo_actual', 'material_actual', 'completado_at']);
        });
    }
}
