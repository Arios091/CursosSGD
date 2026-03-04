<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modulos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade');
                $table->string('titulo')->nullable();           // puede ser null → se autogenera "Módulo 1", "Módulo 2"...
                $table->integer('orden')->default(1);           // para ordenar los módulos
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modulos');
    }
}
