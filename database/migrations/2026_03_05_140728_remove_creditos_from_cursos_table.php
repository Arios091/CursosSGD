<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCreditosFromCursosTable extends Migration
{
    /**
     * Migracion para eliminar la columna 'creditos' de la tabla 'cursos'
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropColumn('creditos');
        });
    }

    public function down(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->integer('creditos')->default(0);
        });
    }
}
