<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToCursosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cursos', function (Blueprint $table) {
            // Agregar carga_horaria si no existe
            if (!Schema::hasColumn('cursos', 'carga_horaria')) {
                $table->integer('carga_horaria')->default(0)->after('creditos');
            }
            // Agregar imagen_referencial si no existe
            if (!Schema::hasColumn('cursos', 'imagen_referencial')) {
                $table->string('imagen_referencial')->nullable()->after('descripcion');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cursos', function (Blueprint $table) {
            if (Schema::hasColumn('cursos', 'carga_horaria')) {
                $table->dropColumn('carga_horaria');
            }
            if (Schema::hasColumn('cursos', 'imagen_referencial')) {
                $table->dropColumn('imagen_referencial');
            }
        });
    }
}
