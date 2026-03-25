<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMaterialsTableAddModuloId extends Migration
{
    public function up()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropForeign(['curso_id']);
            $table->dropColumn('curso_id');
            $table->foreignId('modulo_id')->nullable()->constrained('modulos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropForeign(['modulo_id']);
            $table->dropColumn('modulo_id');
            $table->foreignId('curso_id')->nullable()->constrained('cursos')->onDelete('cascade');
        });
    }
}
